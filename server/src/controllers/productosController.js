const { Producto } = require('../models');

exports.listar = async (req, res) => {
    const productos = await Producto.findAll({ order: [['nombre', 'ASC']] });
    res.json({ ok: true, data: productos });
};

exports.crear = async (req, res) => {
    const producto = await Producto.create(req.body);
    res.json({ ok: true, data: producto });
};

exports.actualizar = async (req, res) => {
    await Producto.update(req.body, { where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Producto actualizado' });
};

exports.eliminar = async (req, res) => {
    await Producto.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Producto eliminado' });
};
