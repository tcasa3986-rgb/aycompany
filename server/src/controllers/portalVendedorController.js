const { Lead, Cliente, Producto, Reunion, Licencia, Usuario } = require('../models');
const { Op } = require('sequelize');
const telegramService = require('../services/telegramService');

// ── Catálogo de sistemas de AI Company ───────────────────────────────────────
const CATALOGO = [
    { nombre: 'Sistema Ferretería',         categoria: 'ERP',    descripcion_venta: 'Control de inventario, ventas, compras y caja para ferreterías y distribuidoras de materiales de construcción.' },
    { nombre: 'CRM Delivery',               categoria: 'CRM',    descripcion_venta: 'Gestión de pedidos a domicilio, rutas de entrega, clientes frecuentes y domiciliarios para restaurantes y dark kitchens.' },
    { nombre: 'CRM Tienda Celulares',        categoria: 'CRM',    descripcion_venta: 'Control de equipos nuevos y usados, IMEI, reparaciones, garantías y ventas para tiendas de tecnología.' },
    { nombre: 'CRM Agencia de Viajes',       categoria: 'CRM',    descripcion_venta: 'Gestión de paquetes turísticos, reservas, cotizaciones, clientes y comisiones para agencias de viajes.' },
    { nombre: 'CRM Colegio',                 categoria: 'CRM',    descripcion_venta: 'Matrículas, pensiones, notas, asistencia, comunicación con padres y agenda académica para colegios.' },
    { nombre: 'CRM Condominio',              categoria: 'CRM',    descripcion_venta: 'Administración de cuotas, reservas de zonas comunes, PQR, visitantes y comunicados para conjuntos residenciales.' },
    { nombre: 'CRM Odontología',             categoria: 'CRM',    descripcion_venta: 'Historia clínica digital, citas, tratamientos, pagos y recordatorios para consultorios odontológicos.' },
    { nombre: 'CRM Ventas',                  categoria: 'CRM',    descripcion_venta: 'Pipeline de ventas, seguimiento de clientes, cotizaciones y reportes para equipos comerciales de cualquier sector.' },
    { nombre: 'ERP Educativo',               categoria: 'ERP',    descripcion_venta: 'Sistema integral para instituciones educativas: estudiantes, docentes, finanzas, notas y comunicación.' },
    { nombre: 'ERP Farmacia',                categoria: 'ERP',    descripcion_venta: 'Inventario de medicamentos, ventas, recetas, vencimientos y caja para droguerías y farmacias.' },
    { nombre: 'ERP Taller Automotriz',       categoria: 'ERP',    descripcion_venta: 'Órdenes de trabajo, repuestos, mecánicos, historial de vehículos y facturación para talleres mecánicos.' },
    { nombre: 'Sistema Citas Médicas',       categoria: 'Sistema',descripcion_venta: 'Agenda médica, historia clínica, recordatorios automáticos y facturación para consultorios médicos.' },
    { nombre: 'Sistema Control de Acceso',   categoria: 'Sistema',descripcion_venta: 'Registro de entrada/salida de empleados o visitantes con huella, QR o carnet para empresas y edificios.' },
    { nombre: 'Sistema de Moda',             categoria: 'Sistema',descripcion_venta: 'Inventario por tallas y colores, ventas, apartados y caja para tiendas de ropa y accesorios.' },
    { nombre: 'Sistema Panadería',           categoria: 'Sistema',descripcion_venta: 'Producción, ventas, caja, insumos y control de recetas para panaderías y negocios de repostería.' },
    { nombre: 'Sistema Préstamos y Cobranza',categoria: 'Sistema',descripcion_venta: 'Gestión de créditos, cuotas, cartera vencida, recordatorios y reportes para empresas financieras.' },
    { nombre: 'ASOERC',                      categoria: 'Sistema',descripcion_venta: 'Sistema de gestión para asociaciones y cooperativas: socios, aportes, préstamos y asambleas.' },
];

async function seedProductos() {
    for (const p of CATALOGO) {
        const existe = await Producto.findOne({ where: { nombre: p.nombre } });
        if (!existe) {
            await Producto.create({ ...p, precio_mensual: 250000, visible_vendedor: true, activo: true });
        }
    }
}

// GET /api/vendedor/catalogo
exports.catalogo = async (req, res) => {
    await seedProductos();
    const productos = await Producto.findAll({
        where: { activo: true, visible_vendedor: true },
        order: [['categoria', 'ASC'], ['nombre', 'ASC']]
    });
    res.json({ ok: true, data: productos });
};

// GET /api/vendedor/stats
exports.stats = async (req, res) => {
    const vendedorId = req.user.id;
    const ahora      = new Date();
    const inicioMes  = new Date(ahora.getFullYear(), ahora.getMonth(), 1);

    const [leadsActivos, reunionesPendientes, clientesMes, clientesTotal] = await Promise.all([
        Lead.count({ where: { vendedor_id: vendedorId, estado: { [Op.notIn]: ['cliente', 'descartado', 'sin_respuesta'] } } }),
        Reunion.count({ where: { fecha: { [Op.gte]: ahora }, estado: 'pendiente', descripcion: { [Op.like]: `%${req.user.nombre}%` } } }),
        Cliente.count({ where: { vendedor_id: vendedorId, created_at: { [Op.gte]: inicioMes } } }),
        Cliente.count({ where: { vendedor_id: vendedorId } })
    ]);

    // Reuniones del vendedor (por participantes que contenga su nombre)
    const reuniones = await Reunion.findAll({
        where: { fecha: { [Op.gte]: ahora }, estado: 'pendiente' },
        order: [['fecha', 'ASC']],
        limit: 10
    });

    res.json({ ok: true, data: { leadsActivos, reunionesPendientes: reuniones.length, clientesMes, clientesTotal, reuniones } });
};

// GET /api/vendedor/leads
exports.leads = async (req, res) => {
    const leads = await Lead.findAll({
        where: { vendedor_id: req.user.id },
        order: [['created_at', 'DESC']]
    });
    res.json({ ok: true, data: leads });
};

// POST /api/vendedor/leads
exports.crearLead = async (req, res) => {
    const lead = await Lead.create({
        ...req.body,
        vendedor_id: req.user.id,
        fuente: 'vendedor'
    });

    // Notificar al admin
    telegramService.enviar(
        `👤 *Nuevo lead registrado*\n\n` +
        `🏷 *Vendedor:* ${req.user.nombre}\n` +
        `👤 *Prospecto:* ${lead.nombre}\n` +
        `🏢 *Empresa:* ${lead.empresa || '—'}\n` +
        `📱 *Teléfono:* ${lead.telefono || '—'}\n` +
        `🖥 *Sistema de interés:* ${lead.sistema_interes || '—'}`
    ).catch(() => {});

    res.status(201).json({ ok: true, data: lead });
};

// PUT /api/vendedor/leads/:id
exports.actualizarLead = async (req, res) => {
    const lead = await Lead.findOne({ where: { id: req.params.id, vendedor_id: req.user.id } });
    if (!lead) return res.status(404).json({ ok: false, msg: 'Lead no encontrado' });
    await lead.update(req.body);
    res.json({ ok: true, data: lead });
};

// POST /api/vendedor/reuniones
exports.agendarReunion = async (req, res) => {
    const { prospecto, telefono, sistema, fecha, duracion = 60, notas } = req.body;
    if (!prospecto || !fecha) return res.status(400).json({ ok: false, msg: 'Prospecto y fecha son requeridos' });

    const { Evento } = require('../models');
    const fechaInicio = new Date(fecha);
    const fechaFin    = new Date(fechaInicio.getTime() + duracion * 60000);
    const titulo      = `Reunión con ${prospecto} — ${sistema || 'Sistema AI Company'}`;

    const reunion = await Reunion.create({
        titulo,
        descripcion: `Vendedor: ${req.user.nombre}${notas ? ` | ${notas}` : ''}`,
        fecha:       fechaInicio,
        duracion,
        participantes: prospecto,
        estado:      'pendiente'
    });

    // Agregar al calendario del admin
    Evento.create({
        titulo, descripcion: `Prospecto: ${prospecto} | Tel: ${telefono || '—'} | Sistema: ${sistema || '—'} | Vendedor: ${req.user.nombre}`,
        fecha_inicio: fechaInicio, fecha_fin: fechaFin,
        color: '#6366f1', participantes: prospecto, recordatorio: true
    }).catch(() => {});

    // Telegram al admin
    const fechaTxt = fechaInicio.toLocaleDateString('es-CO', {
        weekday: 'long', day: 'numeric', month: 'long',
        hour: '2-digit', minute: '2-digit', timeZone: 'America/Bogota'
    });
    telegramService.enviar(
        `📅 *Reunión agendada por ${req.user.nombre}*\n\n` +
        `🤝 *Prospecto:* ${prospecto}\n` +
        `📱 *Teléfono:* ${telefono || '—'}\n` +
        `🖥 *Sistema:* ${sistema || '—'}\n` +
        `📅 *Fecha:* ${fechaTxt}\n` +
        `⏱ *Duración:* ${duracion} min\n` +
        `${notas ? `📝 *Notas:* ${notas}` : ''}\n\n` +
        `_Esta reunión ya quedó en tu calendario._`
    ).catch(() => {});

    res.status(201).json({ ok: true, data: reunion, msg: 'Reunión agendada y notificada al admin' });
};

// GET /api/vendedor/clientes
exports.clientes = async (req, res) => {
    const clientes = await Cliente.findAll({
        where: { vendedor_id: req.user.id },
        include: [{ model: Licencia, as: 'licencias', attributes: ['id', 'activo', 'fecha_vencimiento'] }],
        order: [['created_at', 'DESC']]
    });
    res.json({ ok: true, data: clientes });
};
