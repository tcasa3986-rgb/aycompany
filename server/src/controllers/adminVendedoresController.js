const { Usuario, Lead, Cliente } = require('../models');
const sequelize = require('../config/db');

// GET /api/admin/vendedores
exports.listar = async (req, res) => {
    try {
        const vendedores = await Usuario.findAll({
            where: { rol: 'vendedor' },
            attributes: [
                'id', 'nombre', 'email', 'telefono', 'ciudad',
                'codigo_referido', 'activo', 'created_at',
                [sequelize.literal(`(SELECT COUNT(*) FROM leads WHERE leads.vendedor_id = Usuario.id)`), 'leads'],
                [sequelize.literal(`(SELECT COUNT(*) FROM clientes WHERE clientes.vendedor_id = Usuario.id)`), 'clientes'],
            ],
            include: [
                {
                    model: Usuario, as: 'equipo',
                    attributes: ['id', 'nombre', 'email', 'ciudad', 'activo', 'created_at'],
                    required: false
                }
            ],
            order: [['created_at', 'DESC']]
        });

        res.json({ ok: true, data: vendedores });
    } catch (err) {
        console.error('Error listando vendedores:', err.message);
        res.status(500).json({ ok: false, msg: 'Error al obtener vendedores' });
    }
};

// PATCH /api/admin/vendedores/:id/activo
exports.toggleActivo = async (req, res) => {
    try {
        const v = await Usuario.findOne({ where: { id: req.params.id, rol: 'vendedor' } });
        if (!v) return res.status(404).json({ ok: false, msg: 'Vendedor no encontrado' });
        await v.update({ activo: !v.activo });
        res.json({ ok: true, activo: v.activo });
    } catch (err) {
        console.error('Error toggle vendedor:', err.message);
        res.status(500).json({ ok: false, msg: 'Error al actualizar vendedor' });
    }
};

// DELETE /api/admin/vendedores/:id
exports.eliminar = async (req, res) => {
    try {
        const v = await Usuario.findOne({ where: { id: req.params.id, rol: 'vendedor' } });
        if (!v) return res.status(404).json({ ok: false, msg: 'Vendedor no encontrado' });
        await v.destroy();
        res.json({ ok: true });
    } catch (err) {
        console.error('Error eliminando vendedor:', err.message);
        res.status(500).json({ ok: false, msg: 'Error al eliminar vendedor' });
    }
};
