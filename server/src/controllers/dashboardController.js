const { Cliente, Licencia, Pago, Producto } = require('../models');
const { Op } = require('sequelize');
const sequelize = require('../config/db');

exports.stats = async (req, res) => {
    const hoy   = new Date();
    const en7   = new Date(); en7.setDate(en7.getDate() + 7);
    const ini   = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    const fin   = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);

    const [totalClientes, licenciasActivas, porVencer, ingresosRes, ultimosPagos, proximosVencer] = await Promise.all([
        Cliente.count({ where: { activo: true } }),
        Licencia.count({ where: { activo: true, fecha_vencimiento: { [Op.gte]: hoy } } }),
        Licencia.count({ where: { activo: true, fecha_vencimiento: { [Op.between]: [hoy, en7] } } }),
        Pago.findOne({
            attributes: [[sequelize.fn('SUM', sequelize.col('monto')), 'total']],
            where: { fecha_pago: { [Op.between]: [ini.toISOString().split('T')[0], fin.toISOString().split('T')[0]] } },
            raw: true
        }),
        Pago.findAll({
            include: [
                { model: Cliente,  as: 'cliente',  attributes: ['nombre'] },
                { model: Licencia, as: 'licencia', attributes: ['license_key'],
                  include: [{ model: Producto, as: 'producto', attributes: ['nombre'] }] }
            ],
            order: [['fecha_pago', 'DESC']],
            limit: 5
        }),
        Licencia.findAll({
            where: { activo: true, fecha_vencimiento: { [Op.between]: [hoy, en7] } },
            include: [
                { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'telefono'] },
                { model: Producto, as: 'producto', attributes: ['nombre'] }
            ],
            order: [['fecha_vencimiento', 'ASC']]
        })
    ]);

    res.json({
        ok: true,
        data: {
            totalClientes,
            licenciasActivas,
            porVencer,
            ingresosMes: parseFloat(ingresosRes?.total || 0),
            ultimosPagos,
            proximosVencer
        }
    });
};
