const { EstrategiaMarketing } = require('../models');

exports.listar = async (req, res) => {
    try {
        const items = await EstrategiaMarketing.findAll({ order: [['createdAt', 'DESC']] });
        res.json(items);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.crear = async (req, res) => {
    try {
        const item = await EstrategiaMarketing.create(req.body);
        res.json(item);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const item = await EstrategiaMarketing.findByPk(req.params.id);
        if (!item) return res.status(404).json({ error: 'No encontrado' });
        await item.update(req.body);
        res.json(item);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.eliminar = async (req, res) => {
    try {
        await EstrategiaMarketing.destroy({ where: { id: req.params.id } });
        res.json({ ok: true });
    } catch (e) { res.status(500).json({ error: e.message }); }
};
