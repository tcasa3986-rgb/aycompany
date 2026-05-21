const { Cliente, Licencia, Pago, Producto, Ticket } = require('../models');
const { Op } = require('sequelize');
const sequelize = require('../config/db');

exports.stats = async (req, res) => {
  try {
    const hoy  = new Date();
    const en7  = new Date(); en7.setDate(en7.getDate() + 7);
    const en30 = new Date(); en30.setDate(en30.getDate() + 30);
    const ini  = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    const fin  = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);

    // Últimos 6 meses para la gráfica
    const mesesChart = [];
    for (let i = 5; i >= 0; i--) {
        const d = new Date(hoy.getFullYear(), hoy.getMonth() - i, 1);
        mesesChart.push({
            label: d.toLocaleDateString('es-CO', { month: 'short', year: '2-digit' }),
            ini:   new Date(d.getFullYear(), d.getMonth(), 1).toISOString().split('T')[0],
            fin:   new Date(d.getFullYear(), d.getMonth() + 1, 0).toISOString().split('T')[0]
        });
    }

    const [
        totalClientes, licenciasActivas, porVencer, licenciasVencidas,
        ingresosRes, ticketsAbiertos, ultimosPagos, proximosVencer,
        licenciasConPrecio
    ] = await Promise.all([
        Cliente.count({ where: { activo: true } }),
        Licencia.count({ where: { activo: true, fecha_vencimiento: { [Op.gte]: hoy } } }),
        Licencia.count({ where: { activo: true, fecha_vencimiento: { [Op.between]: [hoy, en7] } } }),
        Licencia.count({ where: { activo: false } }),
        Pago.findOne({
            attributes: [[sequelize.fn('SUM', sequelize.col('monto')), 'total']],
            where: { fecha_pago: { [Op.between]: [ini.toISOString().split('T')[0], fin.toISOString().split('T')[0]] } },
            raw: true
        }),
        Ticket.count({ where: { estado: { [Op.in]: ['abierto', 'en_proceso'] } } }).catch(() => 0),
        Pago.findAll({
            include: [
                { model: Cliente,  as: 'cliente',  attributes: ['nombre'] },
                { model: Licencia, as: 'licencia', attributes: ['license_key'],
                  include: [{ model: Producto, as: 'producto', attributes: ['nombre'] }] }
            ],
            order: [['fecha_pago', 'DESC']], limit: 5
        }),
        Licencia.findAll({
            where: { activo: true, fecha_vencimiento: { [Op.between]: [hoy, en30] } },
            include: [
                { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'telefono', 'email'] },
                { model: Producto, as: 'producto', attributes: ['nombre'] }
            ],
            order: [['fecha_vencimiento', 'ASC']]
        }),
        // Para calcular MRR: licencias activas con su precio mensual
        Licencia.findAll({
            where: { activo: true, fecha_vencimiento: { [Op.gte]: hoy } },
            include: [{ model: Producto, as: 'producto', attributes: ['precio_mensual'] }]
        })
    ]);

    // MRR y ARR
    const mrr = licenciasConPrecio.reduce((sum, l) => sum + Number(l.producto?.precio_mensual || 0), 0);
    const arr = mrr * 12;

    // Gráfica de ingresos últimos 6 meses
    const chartData = await Promise.all(
        mesesChart.map(async m => {
            const res = await Pago.findOne({
                attributes: [[sequelize.fn('SUM', sequelize.col('monto')), 'total']],
                where: { fecha_pago: { [Op.between]: [m.ini, m.fin] } },
                raw: true
            });
            return { label: m.label, total: parseFloat(res?.total || 0) };
        })
    );

    // Tasa de renovación (pagos en últimos 30 días / licencias que vencían)
    const ini30 = new Date(); ini30.setDate(ini30.getDate() - 30);
    const pagosUltimos30 = await Pago.count({ where: { fecha_pago: { [Op.gte]: ini30.toISOString().split('T')[0] } } });

    res.json({
        ok: true,
        data: {
            totalClientes, licenciasActivas, licenciasVencidas,
            porVencer, ingresosMes: parseFloat(ingresosRes?.total || 0),
            mrr, arr, ticketsAbiertos,
            tasaRenovacion: licenciasActivas > 0 ? Math.round((pagosUltimos30 / licenciasActivas) * 100) : 0,
            ultimosPagos, proximosVencer, chartData
        }
    });
  } catch (err) {
    console.error('Error dashboard:', err.message);
    res.status(500).json({ ok: false, msg: 'Error al obtener estadísticas' });
  }
};
