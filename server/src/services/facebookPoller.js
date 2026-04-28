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

async function pollFacebookMessages() {
    const token = process.env.META_PAGE_TOKEN;
    if (!token) return;

    try {
        const pid = await getPageId(token);
        if (!pid) return;

        const r = await fetch(
            `https://graph.facebook.com/v21.0/me/conversations?fields=id,participants&access_token=${token}&limit=25`
        );
        const data = await r.json();

        if (data.error) {
            console.error('Facebook polling error:', data.error.message);
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
                if (msg.from?.id === pid) continue; // ignorar mensajes enviados por la página

                const remitente = conv.participants?.data?.find(p => p.id !== pid);
                await guardar({
                    red: 'facebook',
                    tipo: 'mensaje',
                    remitente: remitente?.name || msg.from?.name || msg.from?.id,
                    remitente_id: msg.from?.id,
                    contenido: msg.message,
                    mensaje_id: msg.id,
                    fecha_red: new Date(msg.created_time),
                    raw: JSON.stringify(msg)
                });
            }
        }
    } catch (err) {
        console.error('Facebook poller error:', err.message);
    }
}

function startPoller() {
    if (!process.env.META_PAGE_TOKEN) {
        console.log('⚠️  META_PAGE_TOKEN no configurado — Facebook poller desactivado');
        return;
    }
    console.log('🔄 Facebook message poller iniciado (cada 5 min)');
    pollFacebookMessages(); // ejecutar de inmediato al arrancar
    setInterval(pollFacebookMessages, 5 * 60 * 1000);
}

module.exports = { startPoller };
