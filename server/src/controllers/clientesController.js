const { Cliente, Licencia, Producto } = require('../models');

exports.listar = async (req, res) => {
    const clientes = await Cliente.findAll({ order: [['created_at', 'DESC']] });
    res.json({ ok: true, data: clientes });
};

exports.obtener = async (req, res) => {
    const cliente = await Cliente.findByPk(req.params.id, {
        include: [{ model: Licencia, as: 'licencias', include: [{ model: Producto, as: 'producto' }] }]
    });
    if (!cliente) return res.status(404).json({ ok: false, msg: 'No encontrado' });
    res.json({ ok: true, data: cliente });
};

exports.crear = async (req, res) => {
    const cliente = await Cliente.create(req.body);
    res.json({ ok: true, data: cliente });
};

exports.actualizar = async (req, res) => {
    await Cliente.update(req.body, { where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Cliente actualizado' });
};

exports.eliminar = async (req, res) => {
    await Cliente.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Cliente eliminado' });
};
