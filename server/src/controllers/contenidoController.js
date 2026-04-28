const { IdeaContenido } = require('../models');

exports.listar = async (req, res) => {
    try {
        const ideas = await IdeaContenido.findAll({ order: [['createdAt', 'DESC']] });
        res.json(ideas);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.crear = async (req, res) => {
    try {
        const idea = await IdeaContenido.create(req.body);
        res.json(idea);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const idea = await IdeaContenido.findByPk(req.params.id);
        if (!idea) return res.status(404).json({ error: 'No encontrada' });
        await idea.update(req.body);
        res.json(idea);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.eliminar = async (req, res) => {
    try {
        await IdeaContenido.destroy({ where: { id: req.params.id } });
        res.json({ ok: true });
    } catch (e) { res.status(500).json({ error: e.message }); }
};
