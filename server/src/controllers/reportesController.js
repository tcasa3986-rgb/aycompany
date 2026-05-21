const { Cliente, Licencia, Pago, Ticket, Proyecto, Tarea, Producto } = require('../models');
const { Op } = require('sequelize');

function toCSV(rows, headers) {
    const escape = v => {
        if (v === null || v === undefined) return '';
        const s = String(v).replace(/"/g, '""');
        return s.includes(',') || s.includes('"') || s.includes('\n') ? `"${s}"` : s;
    };
    const lines = [headers.join(',')];
    for (const row of rows) lines.push(row.map(escape).join(','));
    return lines.join('\n');
}

function sendCSV(res, filename, csv) {
    res.setHeader('Content-Type', 'text/csv; charset=utf-8');
    res.setHeader('Content-Disposition', `attachment; filename="${filename}"`);
    res.send('﻿' + csv); // BOM for Excel UTF-8
}

exports.clientes = async (req, res) => {
    const data = await Cliente.findAll({ order: [['created_at', 'DESC']] });
    const csv = toCSV(
        data.map(c => [c.nombre, c.empresa, c.email, c.telefono, c.ciudad, c.pais, c.activo ? 'Activo' : 'Inactivo', c.created_at]),
        ['Nombre', 'Empresa', 'Email', 'Teléfono', 'Ciudad', 'País', 'Estado', 'Registro']
    );
    sendCSV(res, 'clientes.csv', csv);
};

exports.licencias = async (req, res) => {
    const data = await Licencia.findAll({
        include: [
            { model: Cliente, as: 'cliente', attributes: ['nombre', 'email'] },
            { model: Producto, as: 'producto', attributes: ['nombre'] }
        ],
        order: [['created_at', 'DESC']]
    });
    const ahora = new Date();
    const csv = toCSV(
        data.map(l => {
            const vence = l.fecha_vencimiento ? new Date(l.fecha_vencimiento) : null;
            const dias = vence ? Math.ceil((vence - ahora) / 86400000) : '';
            return [
                l.license_key,
                l.cliente?.nombre,
                l.cliente?.email,
                l.producto?.nombre,
                l.activa ? 'Activa' : 'Inactiva',
                l.fecha_vencimiento,
                dias,
                l.created_at
            ];
        }),
        ['Clave', 'Cliente', 'Email', 'Producto', 'Estado', 'Vencimiento', 'Días restantes', 'Creada']
    );
    sendCSV(res, 'licencias.csv', csv);
};

exports.pagos = async (req, res) => {
    const data = await Pago.findAll({
        include: [
            { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'email'] },
            { model: Licencia, as: 'licencia', attributes: ['license_key'],
              include: [{ model: Producto, as: 'producto', attributes: ['nombre'] }] }
        ],
        order: [['fecha_pago', 'DESC']]
    });
    const csv = toCSV(
        data.map(p => [
            p.fecha_pago,
            p.cliente?.nombre,
            p.cliente?.email,
            p.licencia?.producto?.nombre,
            p.licencia?.license_key,
            p.meses || 1,
            p.metodo_pago,
            p.monto,
            p.descuento || 0,
            p.estado
        ]),
        ['Fecha', 'Cliente', 'Email', 'Producto', 'Licencia', 'Meses', 'Método', 'Monto', 'Descuento%', 'Estado']
    );
    sendCSV(res, 'pagos.csv', csv);
};

exports.cartera = async (req, res) => {
    const ahora = new Date();
    const en30  = new Date(ahora.getTime() + 30 * 86400000);
    const data  = await Licencia.findAll({
        where: { fecha_vencimiento: { [Op.lte]: en30 } },
        include: [
            { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'email', 'telefono'] },
            { model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }
        ],
        order: [['fecha_vencimiento', 'ASC']]
    });
    const csv = toCSV(
        data.map(l => {
            const vence = new Date(l.fecha_vencimiento);
            const dias  = Math.ceil((vence - ahora) / 86400000);
            return [
                l.cliente?.nombre,
                l.cliente?.email,
                l.cliente?.telefono,
                l.producto?.nombre,
                l.license_key,
                l.fecha_vencimiento,
                dias,
                dias < 0 ? 'Vencida' : 'Por vencer',
                l.producto?.precio_mensual
            ];
        }),
        ['Cliente', 'Email', 'Teléfono', 'Producto', 'Licencia', 'Vencimiento', 'Días', 'Estado', 'Valor mensual']
    );
    sendCSV(res, 'cartera.csv', csv);
};

exports.proyectos = async (req, res) => {
    const data = await Proyecto.findAll({
        include: [
            { model: Cliente, as: 'cliente', attributes: ['nombre'] },
            { model: Tarea,   as: 'tareas',  attributes: ['estado'] }
        ],
        order: [['created_at', 'DESC']]
    });
    const csv = toCSV(
        data.map(p => {
            const total     = p.tareas?.length || 0;
            const completas = p.tareas?.filter(t => t.estado === 'completado').length || 0;
            return [
                p.nombre,
                p.cliente?.nombre,
                p.estado,
                p.fecha_inicio,
                p.fecha_fin,
                p.presupuesto,
                p.facturado,
                total,
                completas,
                total > 0 ? Math.round(completas / total * 100) + '%' : '0%',
                p.created_at
            ];
        }),
        ['Proyecto', 'Cliente', 'Estado', 'Inicio', 'Fin', 'Presupuesto', 'Facturado', 'Tareas', 'Completadas', 'Avance', 'Creado']
    );
    sendCSV(res, 'proyectos.csv', csv);
};

exports.tickets = async (req, res) => {
    const data = await Ticket.findAll({
        include: [{ model: Cliente, as: 'cliente', attributes: ['nombre', 'email'] }],
        order: [['created_at', 'DESC']]
    });
    const csv = toCSV(
        data.map(t => [
            t.id,
            t.cliente?.nombre,
            t.cliente?.email,
            t.asunto,
            t.estado,
            t.created_at,
            t.respondido_at,
            t.respuesta ? 'Sí' : 'No'
        ]),
        ['ID', 'Cliente', 'Email', 'Asunto', 'Estado', 'Abierto', 'Respondido', 'Tiene respuesta']
    );
    sendCSV(res, 'tickets.csv', csv);
};
