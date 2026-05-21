const { Proyecto, Tarea, Cliente } = require('../models');

const includeCliente = [{ model: Cliente, as: 'cliente', attributes: ['id','nombre'] }];
const includeTareas  = [{ model: Tarea, as: 'tareas', order: [['orden','ASC'],['created_at','ASC']] }];

// ── Proyectos ────────────────────────────────────────────────────────────────

exports.listar = async (req, res) => {
    try {
        const where = {};
        if (req.query.estado)     where.estado     = req.query.estado;
        if (req.query.cliente_id) where.cliente_id = req.query.cliente_id;
        const proyectos = await Proyecto.findAll({ where, include: [...includeCliente, ...includeTareas], order: [['created_at','DESC']] });
        res.json({ ok: true, data: proyectos });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.obtener = async (req, res) => {
    try {
        const p = await Proyecto.findByPk(req.params.id, { include: [...includeCliente, ...includeTareas] });
        if (!p) return res.status(404).json({ ok: false, msg: 'Proyecto no encontrado' });
        res.json({ ok: true, data: p });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.crear = async (req, res) => {
    try {
        const p = await Proyecto.create(req.body);
        res.status(201).json({ ok: true, msg: 'Proyecto creado', data: p });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const p = await Proyecto.findByPk(req.params.id);
        if (!p) return res.status(404).json({ ok: false, msg: 'Proyecto no encontrado' });
        await p.update(req.body);
        res.json({ ok: true, msg: 'Proyecto actualizado' });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.eliminar = async (req, res) => {
    try {
        const p = await Proyecto.findByPk(req.params.id);
        if (!p) return res.status(404).json({ ok: false, msg: 'Proyecto no encontrado' });
        await Tarea.destroy({ where: { proyecto_id: p.id } });
        await p.destroy();
        res.json({ ok: true, msg: 'Proyecto eliminado' });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

// ── Tareas ────────────────────────────────────────────────────────────────────

exports.crearTarea = async (req, res) => {
    try {
        const t = await Tarea.create({ ...req.body, proyecto_id: req.params.id });
        res.status(201).json({ ok: true, msg: 'Tarea creada', data: t });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.actualizarTarea = async (req, res) => {
    try {
        const t = await Tarea.findByPk(req.params.tarea_id);
        if (!t) return res.status(404).json({ ok: false, msg: 'Tarea no encontrada' });
        await t.update(req.body);
        res.json({ ok: true, msg: 'Tarea actualizada' });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.eliminarTarea = async (req, res) => {
    try {
        const t = await Tarea.findByPk(req.params.tarea_id);
        if (!t) return res.status(404).json({ ok: false, msg: 'Tarea no encontrada' });
        await t.destroy();
        res.json({ ok: true, msg: 'Tarea eliminada' });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};
