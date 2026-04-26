const { Pago, Cliente, Licencia, Producto } = require('../models');
const { Op } = require('sequelize');

const include = [
    { model: Cliente,  as: 'cliente',  attributes: ['id', 'nombre'] },
    { model: Licencia, as: 'licencia', attributes: ['id', 'license_key'],
      include: [{ model: Producto, as: 'producto', attributes: ['nombre'] }] }
];

exports.listar = async (req, res) => {
    const pagos = await Pago.findAll({ include, order: [['fecha_pago', 'DESC']] });
    res.json({ ok: true, data: pagos });
};

exports.crear = async (req, res) => {
    const pago = await Pago.create(req.body);

    // Renovar licencia automáticamente al registrar el pago
    const { licencia_id, meses = 1 } = req.body;
    const lic = await Licencia.findByPk(licencia_id);
    if (lic) {
        const base = new Date(lic.fecha_vencimiento) > new Date() ? new Date(lic.fecha_vencimiento) : new Date();
        base.setMonth(base.getMonth() + parseInt(meses));
        await lic.update({ fecha_vencimiento: base.toISOString().split('T')[0], activo: true });
    }

    res.json({ ok: true, data: pago, msg: 'Pago registrado y licencia renovada' });
};

exports.eliminar = async (req, res) => {
    await Pago.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Pago eliminado' });
};
