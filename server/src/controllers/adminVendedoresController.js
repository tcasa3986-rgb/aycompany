const { Usuario, Lead, Cliente } = require('../models');
const { Op } = require('sequelize');

// GET /api/admin/vendedores
exports.listar = async (req, res) => {
    const vendedores = await Usuario.findAll({
        where: { rol: 'vendedor' },
        attributes: ['id', 'nombre', 'email', 'telefono', 'ciudad', 'codigo_referido', 'activo', 'created_at'],
        include: [
            {
                model: Usuario, as: 'equipo',
                attributes: ['id', 'nombre', 'email', 'ciudad', 'activo', 'created_at']
            }
        ],
        order: [['created_at', 'DESC']]
    });

    const data = await Promise.all(vendedores.map(async v => {
        const [leads, clientes] = await Promise.all([
            Lead.count({ where: { vendedor_id: v.id } }),
            Cliente.count({ where: { vendedor_id: v.id } })
        ]);
        return { ...v.toJSON(), leads, clientes };
    }));

    res.json({ ok: true, data });
};

// PATCH /api/admin/vendedores/:id/activo
exports.toggleActivo = async (req, res) => {
    const v = await Usuario.findOne({ where: { id: req.params.id, rol: 'vendedor' } });
    if (!v) return res.status(404).json({ ok: false, msg: 'Vendedor no encontrado' });
    await v.update({ activo: !v.activo });
    res.json({ ok: true, activo: v.activo });
};

// DELETE /api/admin/vendedores/:id
exports.eliminar = async (req, res) => {
    const v = await Usuario.findOne({ where: { id: req.params.id, rol: 'vendedor' } });
    if (!v) return res.status(404).json({ ok: false, msg: 'Vendedor no encontrado' });
    await v.destroy();
    res.json({ ok: true });
};
