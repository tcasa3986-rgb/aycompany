const { Reunion } = require('../models');
const { Op } = require('sequelize');

exports.listar = async (req, res) => {
    try {
        const reuniones = await Reunion.findAll({ order: [['fecha', 'ASC']] });
        res.json(reuniones);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.crear = async (req, res) => {
    try {
        const reunion = await Reunion.create(req.body);
        res.json(reunion);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const reunion = await Reunion.findByPk(req.params.id);
        if (!reunion) return res.status(404).json({ error: 'No encontrada' });
        await reunion.update(req.body);
        res.json(reunion);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.eliminar = async (req, res) => {
    try {
        await Reunion.destroy({ where: { id: req.params.id } });
        res.json({ ok: true });
    } catch (e) { res.status(500).json({ error: e.message }); }
};
