const { MensajeSocial } = require('../models');
const { Op } = require('sequelize');
const telegramService = require('../services/telegramService');

const VERIFY_TOKEN = process.env.META_VERIFY_TOKEN || 'aicompany_webhook_2024';

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
                        contenido: event.message.text || '[adjunto]',
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
                        contenido: event.message.text || '[adjunto]',
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
                            contenido: msg.text?.body || msg.type || '[adjunto]',
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

    // Notificar por Telegram si está configurado
    if (process.env.PLATAFORMA_TELEGRAM_TOKEN && process.env.PLATAFORMA_TELEGRAM_CHAT_ID) {
        const iconos = { facebook: '🔵', instagram: '📸', whatsapp: '🟢' };
        const ico = iconos[datos.red] || '💬';
        const msg = `${ico} *Nuevo ${datos.tipo} en ${datos.red}*\n👤 ${datos.remitente || 'Desconocido'}\n💬 ${(datos.contenido || '').slice(0, 200)}`;
        telegramService.enviar(msg).catch(() => {});
    }
}

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

exports.stats = async (req, res) => {
    const total     = await MensajeSocial.count();
    const noLeidos  = await MensajeSocial.count({ where: { leido: false } });
    const facebook  = await MensajeSocial.count({ where: { red: 'facebook' } });
    const instagram = await MensajeSocial.count({ where: { red: 'instagram' } });
    const whatsapp  = await MensajeSocial.count({ where: { red: 'whatsapp' } });
    res.json({ total, noLeidos, facebook, instagram, whatsapp });
};
