const { AgenteConfig, AgentActividad, Lead } = require('../models');
const { ejecutarCiclo } = require('../services/agentScheduler');
const { procesarRespuestaWhatsApp } = require('../services/agentService');

exports.getConfig = async (req, res) => {
    try {
        let config = await AgenteConfig.findOne();
        if (!config) config = await AgenteConfig.create({});
        res.json(config);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.updateConfig = async (req, res) => {
    try {
        let config = await AgenteConfig.findOne();
        if (!config) config = await AgenteConfig.create({});
        await config.update(req.body);
        res.json(config);
    } catch (e) { res.status(400).json({ error: e.message }); }
};

// Actividad reciente del agente (todos los leads)
exports.actividadReciente = async (req, res) => {
    try {
        const actividad = await AgentActividad.findAll({
            order: [['created_at', 'DESC']],
            limit: 50,
            include: [{ model: Lead, as: 'lead', attributes: ['nombre', 'empresa', 'telefono'] }]
        });
        res.json(actividad);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

// Disparar ciclo manualmente
exports.ejecutarAhora = async (req, res) => {
    try {
        ejecutarCiclo(); // no awaiteamos para responder rápido
        res.json({ ok: true, mensaje: 'Ciclo del agente iniciado' });
    } catch (e) { res.status(500).json({ error: e.message }); }
};

// Webhook de WhatsApp (verificación GET + mensajes POST)
exports.webhookVerificar = (req, res) => {
    const modo    = req.query['hub.mode'];
    const token   = req.query['hub.verify_token'];
    const desafio = req.query['hub.challenge'];
    if (modo === 'subscribe' && token === process.env.WHATSAPP_VERIFY_TOKEN) {
        res.status(200).send(desafio);
    } else {
        res.status(403).end();
    }
};

exports.webhookMensajes = async (req, res) => {
    res.status(200).end(); // responder rápido a Meta
    try {
        const entry = req.body?.entry?.[0];
        const cambio = entry?.changes?.[0];
        const valor  = cambio?.value;
        const mensajes = valor?.messages;
        if (!mensajes || mensajes.length === 0) return;

        for (const msg of mensajes) {
            if (msg.type !== 'text') continue;
            const telefono = msg.from;
            const texto    = msg.text?.body;
            if (telefono && texto) {
                await procesarRespuestaWhatsApp(telefono, texto);
            }
        }
    } catch (e) {
        console.error('[Webhook WhatsApp]', e.message);
    }
};
