const { MetricaMarketing, MetaMarketing } = require('../models');
const { Op } = require('sequelize');

// Métricas
exports.listarMetricas = async (req, res) => {
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
};

exports.crearMetrica = async (req, res) => {
    const m = await MetricaMarketing.create(req.body);
    res.json(m);
};

exports.actualizarMetrica = async (req, res) => {
    const m = await MetricaMarketing.findByPk(req.params.id);
    if (!m) return res.status(404).json({ error: 'No encontrado' });
    await m.update(req.body);
    res.json(m);
};

exports.eliminarMetrica = async (req, res) => {
    await MetricaMarketing.destroy({ where: { id: req.params.id } });
    res.json({ ok: true });
};

// Metas
exports.listarMetas = async (req, res) => {
    const data = await MetaMarketing.findAll({ order: [['createdAt', 'DESC']] });
    res.json(data);
};

exports.crearMeta = async (req, res) => {
    const m = await MetaMarketing.create(req.body);
    res.json(m);
};

exports.actualizarMeta = async (req, res) => {
    const m = await MetaMarketing.findByPk(req.params.id);
    if (!m) return res.status(404).json({ error: 'No encontrado' });
    await m.update(req.body);
    res.json(m);
};

exports.eliminarMeta = async (req, res) => {
    await MetaMarketing.destroy({ where: { id: req.params.id } });
    res.json({ ok: true });
};
