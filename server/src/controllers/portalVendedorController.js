const { Lead, Cliente, Producto, Reunion, Licencia, Usuario } = require('../models');
const { Op }      = require('sequelize');
const sequelize   = require('../config/db');
const telegramService = require('../services/telegramService');

// ── Catálogo de sistemas de AI Company ───────────────────────────────────────
const CATALOGO = [
    // ── CRM ──────────────────────────────────────────────────────────────────
    { nombre: 'CRM Delivery',       categoria: 'CRM', descripcion_venta: 'Gestión de pedidos a domicilio, rutas de entrega, clientes frecuentes y domiciliarios para restaurantes y dark kitchens.',
      demo_url: '/demos/delivery/',  demo_usuario: 'admin@crm.com',         demo_password: 'password' },
    { nombre: 'CRM Tienda Celulares', categoria: 'CRM', descripcion_venta: 'Control de equipos nuevos y usados, IMEI, reparaciones, garantías y ventas para tiendas de tecnología.',
      demo_url: '/demos/celulares/', demo_usuario: 'admin@tienda.com',      demo_password: 'password' },
    { nombre: 'CRM Agencia de Viajes', categoria: 'CRM', descripcion_venta: 'Gestión de paquetes turísticos, reservas, cotizaciones, clientes y comisiones para agencias de viajes.',
      demo_url: '/demos/viaje360/', demo_usuario: 'admin@viaje360.com',    demo_password: 'Viaje360@' },
    { nombre: 'CRM Colegio',        categoria: 'CRM', descripcion_venta: 'Matrículas, pensiones, notas, asistencia, comunicación con padres y agenda académica para colegios.',
      demo_url: '/demos/colegio/',  demo_usuario: 'admin@colegio.edu.pe',  demo_password: 'admin123' },
    { nombre: 'CRM Condominio',     categoria: 'CRM', descripcion_venta: 'Administración de cuotas, reservas de zonas comunes, PQR, visitantes y comunicados para conjuntos residenciales.',
      demo_url: '/demos/condominio/', demo_usuario: 'admin@laspalmas.com', demo_password: 'Admin123!' },
    { nombre: 'CRM Odontología',    categoria: 'CRM', descripcion_venta: 'Historia clínica digital, citas, tratamientos, pagos y recordatorios para consultorios odontológicos.',
      demo_url: '/demos/odontologia/', demo_usuario: 'admin@clinica.com',  demo_password: 'admin123' },
    { nombre: 'CRM Ventas',         categoria: 'CRM', descripcion_venta: 'Pipeline de ventas, seguimiento de clientes, cotizaciones y reportes para equipos comerciales de cualquier sector.',
      demo_url: '/demos/ventas/',   demo_usuario: 'admin@crm.com',         demo_password: 'admin123' },
    // ── ERP ──────────────────────────────────────────────────────────────────
    { nombre: 'Sistema Ferretería',                  categoria: 'ERP',    descripcion_venta: 'Control de inventario, ventas, compras y caja para ferreterías y distribuidoras de materiales de construcción.',
      demo_url: '/demos/ferreteria/', demo_usuario: 'admin@ferreteria.com', demo_password: 'admin123' },
    { nombre: 'ERP Educativo',                       categoria: 'ERP',    descripcion_venta: 'Sistema integral para instituciones educativas: estudiantes, docentes, finanzas, notas y comunicación.' },
    { nombre: 'ERP Farmacia',                        categoria: 'ERP',    descripcion_venta: 'Inventario de medicamentos, ventas, recetas, vencimientos y caja para droguerías y farmacias.',
      demo_url: '/demos/farmacia/', demo_usuario: 'admin@farmacia.com', demo_password: 'password' },
    { nombre: 'ERP Taller Automotriz',               categoria: 'ERP',    descripcion_venta: 'Órdenes de trabajo, repuestos, mecánicos, historial de vehículos y facturación para talleres mecánicos.' },
    // ── Salud ─────────────────────────────────────────────────────────────────
    { nombre: 'Sistema Citas Médicas',               categoria: 'Salud',  descripcion_venta: 'Agenda médica, historia clínica, recordatorios automáticos y facturación para consultorios médicos.' },
    { nombre: 'Sistema Citas Médicas V2',            categoria: 'Salud',  descripcion_venta: 'Versión mejorada con módulo de telemedicina, pagos en línea y portal del paciente.',
      demo_url: '/demos/citas/', demo_usuario: 'admin@clinica.com', demo_password: 'password' },
    { nombre: 'Sistema Laboratorio Clínico',         categoria: 'Salud',  descripcion_venta: 'Registro de muestras, resultados, impresión de informes y gestión de pacientes para laboratorios clínicos.',
      demo_url: '/demos/laboratorio/', demo_usuario: 'admin@lab.com', demo_password: 'password' },
    { nombre: 'Sistema Botica',                      categoria: 'Salud',  descripcion_venta: 'Ventas, inventario de medicamentos, control de vencimientos y caja para boticas y droguerías pequeñas.',
      demo_url: '/demos/botica/', demo_usuario: 'admin', demo_password: 'admin' },
    // ── Comercio ─────────────────────────────────────────────────────────────
    { nombre: 'Sistema de Moda',                     categoria: 'Comercio',descripcion_venta: 'Inventario por tallas y colores, ventas, apartados y caja para tiendas de ropa y accesorios.' },
    { nombre: 'Sistema Boutique',                    categoria: 'Comercio',descripcion_venta: 'POS completo con catálogo visual, apartados, fidelización de clientes y reportes para boutiques.' },
    { nombre: 'Sistema Restaurante',                 categoria: 'Comercio',descripcion_venta: 'Mesas, comandas, cocina, caja y reportes de ventas para restaurantes y cafeterías.',
      demo_url: '/demos/restaurante/', demo_usuario: 'admin@admin.com', demo_password: 'Xvito2013$' },
    { nombre: 'Sistema Pollería',                    categoria: 'Comercio',descripcion_venta: 'Gestión de ventas, combos, caja y control de inventario especializado para pollerías y asaderos.',
      demo_url: '/demos/polleria/', demo_usuario: 'admin@polleria.com', demo_password: 'admin123' },
    { nombre: 'Sistema Salón de Belleza',            categoria: 'Comercio',descripcion_venta: 'Agenda de citas, servicios, comisiones de estilistas, ventas de productos y caja para salones de belleza.',
      demo_url: '/demos/salon/', demo_usuario: 'admin@salon.com', demo_password: 'admin123' },
    // ── Servicios ─────────────────────────────────────────────────────────────
    { nombre: 'Sistema Panadería y Pastelería',      categoria: 'Servicios',descripcion_venta: 'Producción, ventas, caja, insumos y control de recetas para panaderías y negocios de repostería.',
      demo_url: '/demos/panaderia/', demo_usuario: 'admin@panaderia.com', demo_password: 'Xvito2013$' },
    { nombre: 'Sistema Hospedaje',                   categoria: 'Servicios',descripcion_venta: 'Check-in/out, disponibilidad de habitaciones, facturación y reporte de ocupación para hoteles y hostales.',
      demo_url: '/demos/hospedaje/', demo_usuario: 'admin@hospedaje.com', demo_password: 'password' },
    { nombre: 'Sistema Parqueo y Estacionamiento',   categoria: 'Servicios',descripcion_venta: 'Control de entrada/salida de vehículos, tarifas por hora, facturación y reportes para parqueaderos.',
      demo_url: '/demos/parqueo/', demo_usuario: 'admin@parksmart.com', demo_password: 'password' },
    { nombre: 'Sistema Control de Acceso',           categoria: 'Servicios',descripcion_venta: 'Registro de entrada/salida de empleados o visitantes con QR o carnet para empresas y edificios.' },
    { nombre: 'Sistema Cotización',                  categoria: 'Servicios',descripcion_venta: 'Generación rápida de cotizaciones profesionales en PDF, seguimiento y conversión a factura.',
      demo_url: '/demos/cotizacion/', demo_usuario: 'admin@test.com', demo_password: 'password123' },
    // ── Finanzas ──────────────────────────────────────────────────────────────
    { nombre: 'Sistema Préstamos y Cobranza',        categoria: 'Finanzas',descripcion_venta: 'Gestión de créditos, cuotas, cartera vencida, recordatorios y reportes para empresas financieras y prestamistas.',
      demo_url: '/demos/prestamos/', demo_usuario: 'admin@sistema.com', demo_password: 'Xvito2013$' },
    { nombre: 'Sistema Inventario Equipos Tecnológicos', categoria: 'Servicios', descripcion_venta: 'Control de equipos, asignaciones, mantenimientos y depreciación para empresas de tecnología.',
      demo_url: '/demos/inventario/', demo_usuario: 'admin@inventario.com', demo_password: 'password' },
    // ── IA y Automatización ───────────────────────────────────────────────────
    { nombre: 'Bot de Ventas por WhatsApp',          categoria: 'IA',     descripcion_venta: 'Agente de IA para WhatsApp que vende productos digitales automáticamente, 24/7, sin intervención humana.' },
    { nombre: 'ASOERC',                              categoria: 'Sistema',descripcion_venta: 'Sistema de gestión para asociaciones y cooperativas: socios, aportes, préstamos y asambleas.' },
];

async function seedProductos() {
    for (const p of CATALOGO) {
        const existe = await Producto.findOne({ where: { nombre: p.nombre } });
        if (!existe) {
            await Producto.create({ ...p, precio_mensual: 250000, visible_vendedor: true, activo: true });
        } else if (p.demo_url) {
            // Siempre sincroniza credenciales para que el portal muestre datos correctos
            await existe.update({ demo_url: p.demo_url, demo_usuario: p.demo_usuario, demo_password: p.demo_password });
        }
    }
}
exports.seedProductos = seedProductos;

const e500 = (res, e) => { console.error(e.message); res.status(500).json({ ok: false, msg: 'Error del servidor' }); };

// GET /api/vendedor/catalogo
exports.catalogo = async (req, res) => {
    try {
        const productos = await Producto.findAll({
            where: { activo: true, visible_vendedor: true },
            order: [['categoria', 'ASC'], ['nombre', 'ASC']]
        });
        res.json({ ok: true, data: productos });
    } catch (e) { e500(res, e); }
};

// GET /api/vendedor/stats
exports.stats = async (req, res) => {
    try {
        const vendedorId = req.user.id;
        const ahora      = new Date();
        const inicioMes  = new Date(ahora.getFullYear(), ahora.getMonth(), 1);

        const [leadsActivos, clientesMes, clientesTotal] = await Promise.all([
            Lead.count({ where: { vendedor_id: vendedorId, estado: { [Op.notIn]: ['cliente', 'descartado', 'sin_respuesta'] } } }),
            Cliente.count({ where: { vendedor_id: vendedorId, created_at: { [Op.gte]: inicioMes } } }),
            Cliente.count({ where: { vendedor_id: vendedorId } })
        ]);

        const reuniones = await Reunion.findAll({
            where: { fecha: { [Op.gte]: ahora }, estado: 'pendiente', descripcion: { [Op.like]: `%${req.user.nombre}%` } },
            order: [['fecha', 'ASC']],
            limit: 10
        });

        res.json({ ok: true, data: { leadsActivos, reunionesPendientes: reuniones.length, clientesMes, clientesTotal, reuniones } });
    } catch (e) { e500(res, e); }
};

// GET /api/vendedor/leads
exports.leads = async (req, res) => {
    try {
        const leads = await Lead.findAll({
            where: { vendedor_id: req.user.id },
            order: [['created_at', 'DESC']]
        });
        res.json({ ok: true, data: leads });
    } catch (e) { e500(res, e); }
};

// POST /api/vendedor/leads
exports.crearLead = async (req, res) => {
    try {
        const lead = await Lead.create({ ...req.body, vendedor_id: req.user.id, fuente: 'vendedor' });
        telegramService.enviar(
            `👤 *Nuevo lead registrado*\n\n` +
            `🏷 *Vendedor:* ${req.user.nombre}\n` +
            `👤 *Prospecto:* ${lead.nombre}\n` +
            `🏢 *Empresa:* ${lead.empresa || '—'}\n` +
            `📱 *Teléfono:* ${lead.telefono || '—'}\n` +
            `🖥 *Sistema de interés:* ${lead.sistema_interes || '—'}`
        ).catch(() => {});
        res.status(201).json({ ok: true, data: lead });
    } catch (e) { e500(res, e); }
};

// PUT /api/vendedor/leads/:id
exports.actualizarLead = async (req, res) => {
    try {
        const lead = await Lead.findOne({ where: { id: req.params.id, vendedor_id: req.user.id } });
        if (!lead) return res.status(404).json({ ok: false, msg: 'Lead no encontrado' });
        await lead.update(req.body);
        res.json({ ok: true, data: lead });
    } catch (e) { e500(res, e); }
};

// POST /api/vendedor/reuniones
exports.agendarReunion = async (req, res) => {
  try {
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
  } catch (e) { e500(res, e); }
};

// GET /api/vendedor/mi-equipo
exports.miEquipo = async (req, res) => {
    try {
        const yo = await Usuario.findByPk(req.user.id, { attributes: ['id', 'nombre', 'codigo_referido'] });
        const equipo = await Usuario.findAll({
            where: { referido_por: req.user.id, rol: 'vendedor' },
            attributes: [
                'id', 'nombre', 'email', 'ciudad', 'activo', 'created_at',
                [sequelize.literal(`(SELECT COUNT(*) FROM leads WHERE leads.vendedor_id = Usuario.id)`), 'leads'],
                [sequelize.literal(`(SELECT COUNT(*) FROM clientes WHERE clientes.vendedor_id = Usuario.id)`), 'clientes'],
            ]
        });
        res.json({ ok: true, data: { codigo_referido: yo?.codigo_referido, equipo } });
    } catch (e) { e500(res, e); }
};

// GET /api/vendedor/clientes
exports.clientes = async (req, res) => {
    try {
        const clientes = await Cliente.findAll({
            where: { vendedor_id: req.user.id },
            include: [{ model: Licencia, as: 'licencias', attributes: ['id', 'activo', 'fecha_vencimiento'] }],
            order: [['created_at', 'DESC']]
        });
        res.json({ ok: true, data: clientes });
    } catch (e) { e500(res, e); }
};
