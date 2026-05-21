const { MetricaMarketing, MetaMarketing } = require('../models');
const { Op } = require('sequelize');

const err500 = (res, e) => { console.error(e.message); res.status(500).json({ ok: false, msg: 'Error del servidor' }); };

// Métricas
exports.listarMetricas = async (req, res) => {
    try {
        const { plataforma, desde, hasta } = req.query;
        const where = {};
        if (plataforma) where.plataforma = plataforma;
        if (desde || hasta) {
            where.fecha = {};
            if (desde) where.fecha[Op.gte] = desde;
            if (hasta) where.fecha[Op.lte] = hasta;
        }
        const data = await MetricaMarketing.findAll({ where, order: [['fecha', 'ASC']] });
        res.json(data);
    } catch (e) { err500(res, e); }
};

exports.crearMetrica = async (req, res) => {
    try {
        const m = await MetricaMarketing.create(req.body);
        res.json(m);
    } catch (e) { err500(res, e); }
};

exports.actualizarMetrica = async (req, res) => {
    try {
        const m = await MetricaMarketing.findByPk(req.params.id);
        if (!m) return res.status(404).json({ error: 'No encontrado' });
        await m.update(req.body);
        res.json(m);
    } catch (e) { err500(res, e); }
};

exports.eliminarMetrica = async (req, res) => {
    try {
        await MetricaMarketing.destroy({ where: { id: req.params.id } });
        res.json({ ok: true });
    } catch (e) { err500(res, e); }
};

// Metas
exports.listarMetas = async (req, res) => {
    try {
        const data = await MetaMarketing.findAll({ order: [['createdAt', 'DESC']] });
        res.json(data);
    } catch (e) { err500(res, e); }
};

exports.crearMeta = async (req, res) => {
    try {
        const m = await MetaMarketing.create(req.body);
        res.json(m);
    } catch (e) { err500(res, e); }
};

exports.actualizarMeta = async (req, res) => {
    try {
        const m = await MetaMarketing.findByPk(req.params.id);
        if (!m) return res.status(404).json({ error: 'No encontrado' });
        await m.update(req.body);
        res.json(m);
    } catch (e) { err500(res, e); }
};

exports.eliminarMeta = async (req, res) => {
    try {
        await MetaMarketing.destroy({ where: { id: req.params.id } });
        res.json({ ok: true });
    } catch (e) { err500(res, e); }
};
