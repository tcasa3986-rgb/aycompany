const { Cliente, Licencia, Pago, Factura, Ticket, Proyecto, Contrato, Producto } = require('../models');
const { Op } = require('sequelize');
const sequelize = require('../config/db');

exports.ficha = async (req, res) => {
    try {
        const { id } = req.params;

        const [cliente, licencias, pagos, facturas, tickets, proyectos, contratos, totalPagado] = await Promise.all([
            Cliente.findByPk(id),
            Licencia.findAll({
                where: { cliente_id: id },
                include: [{ model: Producto, as: 'producto', attributes: ['nombre','precio_mensual'] }],
                order: [['id','DESC']]
            }),
            Pago.findAll({
                where: { cliente_id: id },
                include: [{ model: Licencia, as: 'licencia', include: [{ model: Producto, as: 'producto', attributes: ['nombre'] }] }],
                order: [['fecha_pago','DESC']]
            }),
            Factura.findAll({ where: { cliente_id: id }, order: [['id','DESC']] }),
            Ticket.findAll({ where: { cliente_id: id }, order: [['created_at','DESC']] }),
            Proyecto.findAll({ where: { cliente_id: id }, order: [['created_at','DESC']] }),
            Contrato.findAll({ where: { cliente_id: id }, order: [['created_at','DESC']] }),
            Pago.findOne({
                attributes: [[sequelize.fn('SUM', sequelize.col('monto')), 'total']],
                where: { cliente_id: id }, raw: true
            })
        ]);

        if (!cliente) return res.status(404).json({ ok: false, msg: 'Cliente no encontrado' });

        const ahora = new Date();
        const licConEstado = licencias.map(l => {
            const vence = new Date(l.fecha_vencimiento + 'T23:59:59');
            return {
                ...l.toJSON(),
                valida: l.activo && vence >= ahora,
                dias_restantes: Math.ceil((vence - ahora) / 86400000)
            };
        });

        res.json({
            ok: true,
            data: {
                cliente,
                licencias: licConEstado,
                pagos,
                facturas,
                tickets,
                proyectos,
                contratos,
                stats: {
                    totalPagado:      Number(totalPagado?.total || 0),
                    licenciasActivas: licConEstado.filter(l => l.valida).length,
                    ticketsAbiertos:  tickets.filter(t => t.estado !== 'cerrado').length,
                    proyectosActivos: proyectos.filter(p => p.estado === 'en_curso').length,
                    totalPagos:       pagos.length,
                    totalContratos:   contratos.length
                }
            }
        });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};
