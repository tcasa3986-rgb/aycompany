const { Lead, AgentActividad } = require('../models');
const { procesarLead } = require('../services/agentService');

exports.listar = async (req, res) => {
    try {
        const leads = await Lead.findAll({ order: [['created_at', 'DESC']] });
        res.json(leads);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.crear = async (req, res) => {
    try {
        const lead = await Lead.create(req.body);
        res.status(201).json(lead);
    } catch (e) { res.status(400).json({ error: e.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        await Lead.update(req.body, { where: { id: req.params.id } });
        const lead = await Lead.findByPk(req.params.id);
        res.json(lead);
    } catch (e) { res.status(400).json({ error: e.message }); }
};

exports.eliminar = async (req, res) => {
    try {
        await Lead.destroy({ where: { id: req.params.id } });
        res.json({ ok: true });
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.actividad = async (req, res) => {
    try {
        const actividad = await AgentActividad.findAll({
            where: { lead_id: req.params.id },
            order: [['created_at', 'DESC']]
        });
        res.json(actividad);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

// Disparar el agente manualmente sobre un lead específico
exports.procesarManual = async (req, res) => {
    try {
        const lead = await Lead.findByPk(req.params.id);
        if (!lead) return res.status(404).json({ error: 'Lead no encontrado' });
        await procesarLead(lead, req.body.evento || 'Acción manual del administrador');
        res.json({ ok: true });
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.stats = async (req, res) => {
    try {
        const total      = await Lead.count();
        const nuevos     = await Lead.count({ where: { estado: 'nuevo' } });
        const contactados = await Lead.count({ where: { estado: 'contactado' } });
        const respondieron = await Lead.count({ where: { estado: ['respondio','interesado','reunion_agendada','reunion_realizada'] } });
        const reuniones  = await Lead.count({ where: { estado: ['reunion_agendada','reunion_realizada'] } });
        const clientes   = await Lead.count({ where: { estado: 'cliente' } });
        const descartados = await Lead.count({ where: { estado: ['descartado','sin_respuesta'] } });

        const tasaRespuesta = contactados > 0 ? Math.round((respondieron / (contactados + respondieron)) * 100) : 0;
        const tasaReunion   = respondieron > 0 ? Math.round((reuniones / respondieron) * 100) : 0;

        res.json({ total, nuevos, contactados, respondieron, reuniones, clientes, descartados, tasaRespuesta, tasaReunion });
    } catch (e) { res.status(500).json({ error: e.message }); }
};
