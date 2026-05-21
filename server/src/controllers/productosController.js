const { Producto } = require('../models');

const err500 = (res, e) => { console.error(e.message); res.status(500).json({ ok: false, msg: 'Error del servidor' }); };

exports.listar = async (req, res) => {
    try {
        const productos = await Producto.findAll({ order: [['nombre', 'ASC']] });
        res.json({ ok: true, data: productos });
    } catch (e) { err500(res, e); }
};

exports.crear = async (req, res) => {
    try {
        const producto = await Producto.create(req.body);
        res.json({ ok: true, data: producto });
    } catch (e) { err500(res, e); }
};

exports.actualizar = async (req, res) => {
    try {
        await Producto.update(req.body, { where: { id: req.params.id } });
        res.json({ ok: true, msg: 'Producto actualizado' });
    } catch (e) { err500(res, e); }
};

exports.eliminar = async (req, res) => {
    try {
        await Producto.destroy({ where: { id: req.params.id } });
        res.json({ ok: true, msg: 'Producto eliminado' });
    } catch (e) { err500(res, e); }
};
