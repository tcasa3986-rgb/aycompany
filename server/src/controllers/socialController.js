const { MensajeSocial } = require('../models');
const { Op } = require('sequelize');
const telegramService = require('../services/telegramService');
const autoResponder = require('../services/autoResponder');

const VERIFY_TOKEN = process.env.META_VERIFY_TOKEN || 'aicompany_webhook_2024';

// ── Helpers de audio ────────────────────────────────────────────────────────
async function descargarMediaWA(mediaId) {
    const token = process.env.WHATSAPP_TOKEN;
    if (!token) throw new Error('WHATSAPP_TOKEN no configurado');
    const info = await (await fetch(`https://graph.facebook.com/v21.0/${mediaId}`, {
        headers: { 'Authorization': `Bearer ${token}` }
    })).json();
    if (!info.url) throw new Error('Sin URL para media: ' + JSON.stringify(info));
    const buf = await (await fetch(info.url, {
        headers: { 'Authorization': `Bearer ${token}` }
    })).arrayBuffer();
    return { buffer: Buffer.from(buf), mimeType: info.mime_type || 'audio/ogg' };
}

async function transcribirAudio(buffer, mimeType) {
    if (!process.env.OPENAI_API_KEY) {
        console.warn('🔇 OPENAI_API_KEY no configurado — audio sin transcripción');
        return null;
    }
    const cleanMime = mimeType.split(';')[0].trim();
    const ext = cleanMime.includes('ogg') ? 'ogg'
        : cleanMime.includes('webm') ? 'webm'
        : cleanMime.includes('mp4') ? 'mp4'
        : cleanMime.includes('mpeg') ? 'mp3'
        : 'ogg';
    const blob = new Blob([buffer], { type: cleanMime });
    const form = new FormData();
    form.append('file', blob, `audio.${ext}`);
    form.append('model', 'whisper-1');
    form.append('language', 'es');
    const res = await fetch('https://api.openai.com/v1/audio/transcriptions', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${process.env.OPENAI_API_KEY}` },
        body: form
    });
    const data = await res.json();
    if (data.error) { console.error('Whisper error:', data.error.message); return null; }
    return data.text?.trim() || null;
}

async function extraerContenidoMensaje(msg, red) {
    if (msg.type === 'text') return msg.text?.body || '';

    if (msg.type === 'audio' || msg.type === 'voice') {
        const audioId = msg.audio?.id || msg.voice?.id;
        console.log(`🎤 Audio WA recibido — ID: ${audioId}, tipo: ${msg.type}`);
        if (audioId && red === 'whatsapp') {
            try {
                const { buffer, mimeType } = await descargarMediaWA(audioId);
                console.log(`🎤 Audio descargado: ${buffer.length} bytes, mime: ${mimeType}`);
                const tx = await transcribirAudio(buffer, mimeType);
                if (tx) { console.log(`🎤 Audio transcrito (${red}): "${tx.slice(0, 80)}"`); return `[Audio]: ${tx}`; }
                console.warn('🎤 Transcripción vacía o nula');
            } catch (e) { console.error('Error descargando audio WA:', e.message); }
        }
        return '[Audio de voz]';
    }

    const tipos = { sticker: '🎭 Sticker', image: '🖼️ Imagen', video: '🎥 Video', document: '📎 Documento', location: '📍 Ubicación', contacts: '👤 Contacto' };
    return tipos[msg.type] || `[${msg.type || 'adjunto'}]`;
}

async function extraerContenidoEvento(event) {
    if (event.message.text) return event.message.text;
    const att = event.message.attachments?.[0];
    if (!att) return '[adjunto]';
    if (att.type === 'audio') {
        try {
            const audioRes = await fetch(att.payload.url);
            if (audioRes.ok) {
                const buffer = Buffer.from(await audioRes.arrayBuffer());
                const tx = await transcribirAudio(buffer, 'audio/mp4');
                if (tx) { console.log(`🎤 Audio transcrito (fb/ig): "${tx.slice(0, 60)}"`); return `[Audio]: ${tx}`; }
            }
        } catch (e) { console.error('Error audio FB/IG:', e.message); }
        return '[Audio de voz]';
    }
    return `[${att.type}]`;
}

// ── Verificación del webhook (GET) ──────────────────────────────────────────
exports.verificarWebhook = (req, res) => {
    const mode      = req.query['hub.mode'];
    const token     = req.query['hub.verify_token'];
    const challenge = req.query['hub.challenge'];
    if (mode === 'subscribe' && token === VERIFY_TOKEN) {
        console.log('✅ Webhook Meta verificado');
        return res.status(200).send(challenge);
    }
    res.sendStatus(403);
};

// ── Recepción de eventos (POST) ─────────────────────────────────────────────
exports.recibirWebhook = async (req, res) => {
    const body = req.body;
    res.sendStatus(200); // responder rápido a Meta

    try {
        if (body.object === 'page') {
            for (const entry of body.entry || []) {
                for (const event of entry.messaging || []) {
                    if (event.message) await guardar({
                        red: 'facebook', tipo: 'mensaje',
                        remitente: event.sender?.id,
                        remitente_id: event.sender?.id,
                        contenido: await extraerContenidoEvento(event),
                        mensaje_id: event.message.mid,
                        fecha_red: new Date(event.timestamp),
                        raw: JSON.stringify(event)
                    });
                }
                for (const change of entry.changes || []) {
                    const val = change.value;
                    if (change.field === 'feed' && val.item === 'comment') {
                        await guardar({
                            red: 'facebook', tipo: 'comentario',
                            remitente: val.from?.name,
                            remitente_id: val.from?.id,
                            contenido: val.message,
                            post_id: val.post_id,
                            mensaje_id: val.comment_id,
                            fecha_red: new Date(val.created_time * 1000),
                            raw: JSON.stringify(val)
                        });
                    }
                }
            }
        }

        if (body.object === 'instagram') {
            for (const entry of body.entry || []) {
                for (const event of entry.messaging || []) {
                    if (event.message) await guardar({
                        red: 'instagram', tipo: 'mensaje',
                        remitente: event.sender?.id,
                        remitente_id: event.sender?.id,
                        contenido: await extraerContenidoEvento(event),
                        mensaje_id: event.message.mid,
                        fecha_red: new Date(event.timestamp),
                        raw: JSON.stringify(event)
                    });
                }
                for (const change of entry.changes || []) {
                    if (change.field === 'comments') {
                        const val = change.value;
                        await guardar({
                            red: 'instagram', tipo: 'comentario',
                            remitente: val.from?.username,
                            remitente_id: val.from?.id,
                            contenido: val.text,
                            post_id: val.media?.id,
                            mensaje_id: val.id,
                            fecha_red: new Date(),
                            raw: JSON.stringify(val)
                        });
                    }
                }
            }
        }

        if (body.object === 'whatsapp_business_account') {
            for (const entry of body.entry || []) {
                for (const change of entry.changes || []) {
                    const val = change.value;
                    for (const msg of val.messages || []) {
                        const contacto = val.contacts?.find(c => c.wa_id === msg.from);
                        await guardar({
                            red: 'whatsapp', tipo: 'mensaje',
                            remitente: contacto?.profile?.name || msg.from,
                            remitente_id: msg.from,
                            contenido: await extraerContenidoMensaje(msg, 'whatsapp'),
                            mensaje_id: msg.id,
                            fecha_red: new Date(msg.timestamp * 1000),
                            raw: JSON.stringify(msg)
                        });
                    }
                }
            }
        }
    } catch (err) {
        console.error('Error procesando webhook Meta:', err.message);
    }
};

async function guardar(datos) {
    const existe = datos.mensaje_id
        ? await MensajeSocial.findOne({ where: { mensaje_id: datos.mensaje_id } })
        : null;
    if (existe) return;

    const m = await MensajeSocial.create(datos);

    // Auto-responder con IA
    autoResponder.responder(m).catch(err => console.error('Auto-responder catch:', err.message));
}

// ── Endpoint síncrono para Make: recibe mensaje, devuelve respuesta de Claude ─
exports.responderMake = async (req, res) => {
    const secret = req.headers['x-make-secret'] || req.body.secret;
    if (process.env.MAKE_SECRET && secret !== process.env.MAKE_SECRET) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    try {
        const ev = req.body;
        const datos = {
            red:          ev.red || ev.plataforma || 'instagram',
            tipo:         ev.tipo || 'mensaje',
            remitente:    ev.remitente || ev.nombre || ev.username || 'Desconocido',
            remitente_id: ev.remitente_id || ev.from_id || ev.user_id || '',
            contenido:    ev.contenido || ev.mensaje || ev.text || ev.message || '',
            post_id:      ev.post_id || ev.media_id || '',
            mensaje_id:   ev.mensaje_id || ev.id || String(Date.now()),
            fecha_red:    new Date(),
            raw:          JSON.stringify(ev)
        };
        const existe = await MensajeSocial.findOne({ where: { mensaje_id: datos.mensaje_id } });
        if (existe) return res.json({ respuesta: null, duplicado: true });

        const m = await MensajeSocial.create(datos);
        const respuesta = await autoResponder.generarRespuesta(m);

        if (respuesta) {
            await m.update({ respondido: true, respuesta });
            console.log(`🤖 Make síncrono (${datos.red}) → ${datos.remitente}: "${respuesta.slice(0, 60)}"`);
        }

        res.json({ respuesta: respuesta || null });
    } catch (err) {
        console.error('Error responderMake:', err.message);
        res.status(500).json({ error: err.message });
    }
};

// ── Webhook Make.com (POST genérico) ────────────────────────────────────────
exports.recibirMake = async (req, res) => {
    const secret = req.headers['x-make-secret'] || req.body.secret;
    if (process.env.MAKE_SECRET && secret !== process.env.MAKE_SECRET) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    res.sendStatus(200);
    try {
        // Make puede enviar un array o un objeto
        const eventos = Array.isArray(req.body) ? req.body : [req.body];
        for (const ev of eventos) {
            await guardar({
                red:          ev.red || ev.plataforma || 'desconocida',
                tipo:         ev.tipo || 'mensaje',
                remitente:    ev.remitente || ev.nombre || ev.from_name || ev.username || 'Desconocido',
                remitente_id: ev.remitente_id || ev.from_id || ev.user_id || '',
                contenido:    ev.contenido || ev.mensaje || ev.text || ev.message || ev.comment || '',
                post_id:      ev.post_id || ev.media_id || '',
                mensaje_id:   ev.mensaje_id || ev.id || String(Date.now()),
                fecha_red:    ev.fecha || ev.timestamp ? new Date(ev.fecha || ev.timestamp) : new Date(),
                raw:          JSON.stringify(ev)
            });
        }
    } catch (err) {
        console.error('Error webhook Make:', err.message);
    }
};

// ── API para la plataforma ──────────────────────────────────────────────────
exports.listar = async (req, res) => {
    const { red, tipo, leido, page = 1 } = req.query;
    const where = {};
    if (red)  where.red  = red;
    if (tipo) where.tipo = tipo;
    if (leido !== undefined) where.leido = leido === 'true';
    const limit = 50;
    const offset = (page - 1) * limit;
    const { rows, count } = await MensajeSocial.findAndCountAll({
        where, order: [['createdAt', 'DESC']], limit, offset
    });
    res.json({ items: rows, total: count, pagina: Number(page) });
};

exports.conversacion = async (req, res) => {
    const { remitente_id, red } = req.query;
    if (!remitente_id) return res.status(400).json({ error: 'remitente_id requerido' });
    const where = { remitente_id };
    if (red) where.red = red;
    const mensajes = await MensajeSocial.findAll({
        where, order: [['createdAt', 'ASC']]
    });
    res.json(mensajes);
};

exports.marcarLeido = async (req, res) => {
    await MensajeSocial.update({ leido: true }, { where: { id: req.params.id } });
    res.json({ ok: true });
};

exports.marcarRespondido = async (req, res) => {
    await MensajeSocial.update({ respondido: true }, { where: { id: req.params.id } });
    res.json({ ok: true });
};

exports.eliminar = async (req, res) => {
    await MensajeSocial.destroy({ where: { id: req.params.id } });
    res.json({ ok: true });
};

exports.responder = async (req, res) => {
    const { texto } = req.body;
    if (!texto?.trim()) return res.status(400).json({ error: 'Texto requerido' });

    const msg = await MensajeSocial.findByPk(req.params.id);
    if (!msg) return res.status(404).json({ error: 'Mensaje no encontrado' });

    try {
        if (msg.red === 'facebook' || msg.red === 'instagram') {
            if (!process.env.META_PAGE_TOKEN)
                return res.status(500).json({ error: 'META_PAGE_TOKEN no configurado. Genera el token en el panel de Meta y agrégalo a Railway.' });

            const r = await fetch('https://graph.facebook.com/v21.0/me/messages', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    access_token: process.env.META_PAGE_TOKEN,
                    recipient: { id: msg.remitente_id },
                    message: { text: texto }
                })
            });
            const data = await r.json();
            if (data.error) return res.status(400).json({ error: data.error.message });
        } else if (msg.red === 'whatsapp') {
            if (!process.env.WHATSAPP_TOKEN || !process.env.WHATSAPP_PHONE_ID)
                return res.status(500).json({ error: 'WHATSAPP_TOKEN o WHATSAPP_PHONE_ID no configurados.' });

            // Verificar si la ventana de 24h está abierta
            const ultimoMensaje = await MensajeSocial.findOne({
                where: { remitente_id: msg.remitente_id, red: 'whatsapp', respuesta: null },
                order: [['createdAt', 'DESC']]
            });
            const hace24h = new Date(Date.now() - 24 * 60 * 60 * 1000);
            const ventanaAbierta = ultimoMensaje && new Date(ultimoMensaje.createdAt) > hace24h;

            let bodyWA;
            if (ventanaAbierta) {
                // Dentro de 24h — mensaje libre
                bodyWA = {
                    messaging_product: 'whatsapp',
                    to: msg.remitente_id,
                    type: 'text',
                    text: { body: texto }
                };
            } else {
                // Fuera de 24h — usar plantilla aprobada de seguimiento
                // Usa la plantilla de seguimiento (debe estar aprobada)
                bodyWA = {
                    messaging_product: 'whatsapp',
                    to: msg.remitente_id,
                    type: 'template',
                    template: {
                        name: 'prospecto_whatsapp_automatico',
                        language: { code: 'es' },
                        components: [{
                            type: 'body',
                            parameters: [{ type: 'text', text: msg.remitente || 'equipo' }]
                        }]
                    }
                };
            }

            const r = await fetch(`https://graph.facebook.com/v21.0/${process.env.WHATSAPP_PHONE_ID}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${process.env.WHATSAPP_TOKEN}`
                },
                body: JSON.stringify(bodyWA)
            });
            const data = await r.json();
            if (data.error) {
                // Si la plantilla no está aprobada aún, informar claramente
                if (data.error.code === 132001 || data.error.message?.includes('template')) {
                    return res.status(400).json({
                        error: 'ventana_cerrada',
                        mensaje: `Han pasado más de 24h desde el último mensaje de ${msg.remitente}. Las plantillas de WhatsApp están en revisión por Meta (24-48h). Por ahora escríbele directamente desde tu celular.`
                    });
                }
                return res.status(400).json({ error: data.error.message });
            }
            if (!ventanaAbierta) {
                return res.json({ ok: true, plantilla: true, aviso: 'Ventana de 24h cerrada — se envió plantilla de seguimiento' });
            }
        } else {
            return res.status(400).json({ error: `Red ${msg.red} no soportada para respuesta directa` });
        }

        await MensajeSocial.update({ respondido: true, respuesta: texto }, { where: { id: msg.id } });
        res.json({ ok: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
};

exports.stats = async (req, res) => {
    const total     = await MensajeSocial.count();
    const noLeidos  = await MensajeSocial.count({ where: { leido: false } });
    const facebook  = await MensajeSocial.count({ where: { red: 'facebook' } });
    const instagram = await MensajeSocial.count({ where: { red: 'instagram' } });
    const whatsapp  = await MensajeSocial.count({ where: { red: 'whatsapp' } });
    const tiktok    = await MensajeSocial.count({ where: { red: 'tiktok' } });
    res.json({ total, noLeidos, facebook, instagram, whatsapp, tiktok });
};
