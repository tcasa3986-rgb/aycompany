const { MensajeSocial } = require('../models');

let pageId = null;

async function guardar(datos) {
    if (!datos.mensaje_id) return;
    const existe = await MensajeSocial.findOne({ where: { mensaje_id: datos.mensaje_id } });
    if (existe) return;
    await MensajeSocial.create(datos);
}

async function getPageId(token) {
    if (pageId) return pageId;
    const r = await fetch(`https://graph.facebook.com/v21.0/me?fields=id&access_token=${token}`);
    const data = await r.json();
    if (data.id) pageId = data.id;
    return pageId;
}

async function pollConversations(token, pid, platform) {
    const red = platform === 'instagram' ? 'instagram' : 'facebook';
    const url = platform === 'instagram'
        ? `https://graph.facebook.com/v21.0/me/conversations?platform=instagram&fields=id,participants&access_token=${token}&limit=25`
        : `https://graph.facebook.com/v21.0/me/conversations?fields=id,participants&access_token=${token}&limit=25`;

    const r = await fetch(url);
    const data = await r.json();

    if (data.error) {
        // instagram puede no estar conectado aún — loguear solo si no es error de permiso
        if (data.error.code !== 190 && data.error.code !== 10) {
            console.error(`${red} polling error:`, data.error.message);
        }
        return;
    }

    for (const conv of data.data || []) {
        const mr = await fetch(
            `https://graph.facebook.com/v21.0/${conv.id}/messages?fields=id,message,from,created_time&access_token=${token}&limit=10`
        );
        const msgs = await mr.json();
        if (msgs.error) continue;

        for (const msg of msgs.data || []) {
            if (!msg.message) continue;
            if (msg.from?.id === pid) continue;

            const remitente = conv.participants?.data?.find(p => p.id !== pid);
            await guardar({
                red,
                tipo: 'mensaje',
                remitente: remitente?.name || remitente?.username || msg.from?.name || msg.from?.id,
                remitente_id: msg.from?.id,
                contenido: msg.message,
                mensaje_id: msg.id,
                fecha_red: new Date(msg.created_time),
                raw: JSON.stringify(msg)
            });
        }
    }
}

async function pollWhatsApp() {
    const token = process.env.WHATSAPP_TOKEN;
    const phoneId = process.env.WHATSAPP_PHONE_ID;
    if (!token || !phoneId) return;

    // WhatsApp Cloud API no soporta polling de mensajes — usa webhooks
    // Este espacio queda reservado para implementación futura vía webhook
}

async function pollAll() {
    const token = process.env.META_PAGE_TOKEN;
    if (!token) return;

    try {
        const pid = await getPageId(token);
        if (!pid) return;

        await pollConversations(token, pid, 'facebook');
        await pollConversations(token, pid, 'instagram');
    } catch (err) {
        console.error('Social poller error:', err.message);
    }
}

function startPoller() {
    if (!process.env.META_PAGE_TOKEN) {
        console.log('⚠️  META_PAGE_TOKEN no configurado — social poller desactivado');
        return;
    }
    console.log('🔄 Social poller iniciado (Facebook + Instagram, cada 5 min)');
    pollAll();
    setInterval(pollAll, 5 * 60 * 1000);
}

module.exports = { startPoller };
