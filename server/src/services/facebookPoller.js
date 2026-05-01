const { MensajeSocial } = require('../models');
const autoResponder = require('./autoResponder');

async function guardar(datos) {
    if (!datos.mensaje_id) return;
    const existe = await MensajeSocial.findOne({ where: { mensaje_id: datos.mensaje_id } });
    if (existe) return;
    const m = await MensajeSocial.create(datos);
    autoResponder.responder(m).catch(err => console.error('Auto-responder poller catch:', err.message));
}

async function getPageId(token) {
    // Usar variable de entorno si está disponible
    if (process.env.META_PAGE_ID) return process.env.META_PAGE_ID;
    // Obtener desde la API como fallback
    try {
        const r = await fetch(`https://graph.facebook.com/v21.0/me?fields=id&access_token=${token}`);
        const data = await r.json();
        if (data.id) return data.id;
    } catch (_) {}
    // Extraer del primer participante "AI Company" en conversaciones
    const r2 = await fetch(`https://graph.facebook.com/v21.0/me/conversations?fields=participants&access_token=${token}&limit=1`);
    const conv = await r2.json();
    const participants = conv.data?.[0]?.participants?.data || [];
    const page = participants.find(p => p.email?.includes('@facebook.com') && p.name !== 'Cristian');
    return page?.id || null;
}

async function pollConversations(token, pid, platform) {
    const red = platform === 'instagram' ? 'instagram' : 'facebook';
    // Solo traer conversaciones actualizadas en los últimos 3 minutos
    const since = Math.floor((Date.now() - 3 * 60 * 1000) / 1000);
    const url = platform === 'instagram'
        ? `https://graph.facebook.com/v21.0/${pid}/conversations?platform=instagram&fields=id,participants,updated_time&access_token=${token}&limit=25&since=${since}`
        : `https://graph.facebook.com/v21.0/${pid}/conversations?fields=id,participants,updated_time&access_token=${token}&limit=25&since=${since}`;

    const r = await fetch(url);
    const data = await r.json();

    if (data.error) {
        console.error(`${red} polling error [${data.error.code}]:`, data.error.message);
        return;
    }

    for (const conv of data.data || []) {
        const mr = await fetch(
            `https://graph.facebook.com/v21.0/${conv.id}/messages?fields=id,message,from,created_time&access_token=${token}&limit=5`
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
    console.log('🔄 Social poller iniciado (Facebook + Instagram, cada 1 min)');
    pollAll();
    setInterval(pollAll, 60 * 1000);
}

module.exports = { startPoller };
