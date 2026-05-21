const express  = require('express');
const cors     = require('cors');
const helmet   = require('helmet');
const morgan   = require('morgan');
const path     = require('path');
const fs       = require('fs');
require('dotenv').config();

const sequelize = require('./config/database');
require('./models'); // Cargar todos los modelos y asociaciones

const app = express();

// ─── Middlewares globales ──────────────────────────────────────
app.use(helmet({ crossOriginResourcePolicy: { policy: 'cross-origin' }, contentSecurityPolicy: false }));
const allowedOrigins = [
  process.env.FRONTEND_URL,
  process.env.RAILWAY_PUBLIC_DOMAIN ? `https://${process.env.RAILWAY_PUBLIC_DOMAIN}` : null,
  'http://localhost:5173',
  'http://localhost:5174',
  'http://localhost:5175',
].filter(Boolean);

app.use(cors({ origin: (origin, cb) => { if (!origin || allowedOrigins.includes(origin)) return cb(null, true); cb(null, true); }, credentials: true }));
app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ extended: true }));
app.use(morgan('dev'));

// ─── Archivos estáticos ────────────────────────────────────────
app.use('/uploads', express.static(path.join(__dirname, '../uploads')));

// Servir frontend compilado en producción
const clientDist = path.join(__dirname, '../../frontend/dist');
if (fs.existsSync(clientDist)) {
  app.use(express.static(clientDist));
}

// ─── Rutas ────────────────────────────────────────────────────
app.use('/api/auth',          require('./routes/auth'));
app.use('/api/dashboard',     require('./routes/dashboard'));
app.use('/api/clientes',      require('./routes/clientes'));
app.use('/api/paquetes',      require('./routes/paquetes'));
app.use('/api/oportunidades', require('./routes/oportunidades'));
app.use('/api/reservas',      require('./routes/reservas'));
app.use('/api/pagos',         require('./routes/pagos'));
app.use('/api/mantenimiento', require('./routes/mantenimiento'));

// Rutas de datos estáticos
const { autenticar } = require('./middlewares/auth');
const { Pais, Destino, CategoriaPaquete, FuenteOrigen, Etiqueta, EtapaPipeline, MetodoPago, Usuario, Rol, Tarea, Proveedor, Campana, ConfiguracionGeneral } = require('./models');
const { Op } = require('sequelize');

app.get('/api/paises',    autenticar, async (req, res) => {
  const data = await Pais.findAll({ order: [['nombre','ASC']] });
  res.json({ ok: true, data });
});
app.get('/api/destinos',  autenticar, async (req, res) => {
  const data = await Destino.findAll({ where: { activo: 1 }, include: ['pais'], order: [['nombre','ASC']] });
  res.json({ ok: true, data });
});
app.get('/api/categorias-paquete', autenticar, async (req, res) => {
  const data = await CategoriaPaquete.findAll({ order: [['nombre','ASC']] });
  res.json({ ok: true, data });
});
app.get('/api/fuentes',   autenticar, async (req, res) => {
  const data = await FuenteOrigen.findAll({ order: [['nombre','ASC']] });
  res.json({ ok: true, data });
});
app.get('/api/etiquetas', autenticar, async (req, res) => {
  const data = await Etiqueta.findAll();
  res.json({ ok: true, data });
});
app.get('/api/etapas',    autenticar, async (req, res) => {
  const data = await EtapaPipeline.findAll({ order: [['orden','ASC']] });
  res.json({ ok: true, data });
});
app.get('/api/metodos-pago', autenticar, async (req, res) => {
  const data = await MetodoPago.findAll();
  res.json({ ok: true, data });
});
app.get('/api/agentes',   autenticar, async (req, res) => {
  const data = await Usuario.findAll({
    where: { activo: 1 },
    attributes: ['id','nombre','apellido','email','avatar_url','rol_id'],
    include: ['rol'],
  });
  res.json({ ok: true, data });
});

// Roles
app.get('/api/roles', autenticar, async (req, res) => {
  try {
    const data = await Rol.findAll({ where: { activo: 1 }, order: [['nombre','ASC']] });
    res.json({ ok: true, data });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});

// CRUD Usuarios
app.get('/api/usuarios', autenticar, async (req, res) => {
  const data = await Usuario.findAll({ attributes: { exclude: ['password_hash'] }, include: ['rol'] });
  res.json({ ok: true, data });
});
app.post('/api/usuarios', autenticar, async (req, res) => {
  try {
    const bcrypt = require('bcryptjs');
    const hash = await bcrypt.hash(req.body.password || 'Viaje360@', 10);
    const u = await Usuario.create({ ...req.body, password_hash: hash });
    res.status(201).json({ ok: true, data: u });
  } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
});
app.put('/api/usuarios/:id', autenticar, async (req, res) => {
  try {
    const bcrypt = require('bcryptjs');
    const u = await Usuario.findByPk(req.params.id);
    if (!u) return res.status(404).json({ ok: false, msg: 'No encontrado' });
    const updateData = { ...req.body };
    if (req.body.password) {
      updateData.password_hash = await bcrypt.hash(req.body.password, 10);
    }
    delete updateData.password;
    await u.update(updateData);
    res.json({ ok: true, data: u });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});
app.delete('/api/usuarios/:id', autenticar, async (req, res) => {
  try {
    const u = await Usuario.findByPk(req.params.id);
    if (!u) return res.status(404).json({ ok: false, msg: 'No encontrado' });
    await u.update({ activo: u.activo ? 0 : 1 });
    res.json({ ok: true, data: u });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});

// CRUD Destinos (completo)
app.get('/api/destinos/crud', autenticar, async (req, res) => {
  try {
    const { buscar } = req.query;
    const where = {};
    if (buscar) where.nombre = { [Op.like]: `%${buscar}%` };
    const data = await Destino.findAll({ where, include: ['pais'], order: [['nombre','ASC']] });
    res.json({ ok: true, data });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});
app.post('/api/destinos', autenticar, async (req, res) => {
  try {
    const d = await Destino.create(req.body);
    const resultado = await Destino.findByPk(d.id, { include: ['pais'] });
    res.status(201).json({ ok: true, data: resultado });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});
app.put('/api/destinos/:id', autenticar, async (req, res) => {
  try {
    const d = await Destino.findByPk(req.params.id);
    if (!d) return res.status(404).json({ ok: false, msg: 'No encontrado' });
    await d.update(req.body);
    const resultado = await Destino.findByPk(d.id, { include: ['pais'] });
    res.json({ ok: true, data: resultado });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});
app.delete('/api/destinos/:id', autenticar, async (req, res) => {
  try {
    const d = await Destino.findByPk(req.params.id);
    if (!d) return res.status(404).json({ ok: false, msg: 'No encontrado' });
    await d.update({ activo: d.activo ? 0 : 1 });
    res.json({ ok: true });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});

// CRUD Proveedores
app.get('/api/proveedores', autenticar, async (req, res) => {
  try {
    const { buscar, tipo } = req.query;
    const where = {};
    if (buscar) where.nombre = { [Op.like]: `%${buscar}%` };
    if (tipo) where.tipo = tipo;
    const data = await Proveedor.findAll({ where, order: [['nombre','ASC']] });
    res.json({ ok: true, data });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});
app.post('/api/proveedores', autenticar, async (req, res) => {
  try {
    const p = await Proveedor.create(req.body);
    res.status(201).json({ ok: true, data: p });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});
app.put('/api/proveedores/:id', autenticar, async (req, res) => {
  try {
    const p = await Proveedor.findByPk(req.params.id);
    if (!p) return res.status(404).json({ ok: false, msg: 'No encontrado' });
    await p.update(req.body);
    res.json({ ok: true, data: p });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});
app.delete('/api/proveedores/:id', autenticar, async (req, res) => {
  try {
    const p = await Proveedor.findByPk(req.params.id);
    if (!p) return res.status(404).json({ ok: false, msg: 'No encontrado' });
    await p.update({ activo: p.activo ? 0 : 1 });
    res.json({ ok: true });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});

// CRUD Tareas
app.get('/api/tareas', autenticar, async (req, res) => {
  const { asignado_a, estado, cliente_id } = req.query;
  const where = {};
  if (asignado_a) where.asignado_a = asignado_a;
  if (estado)     where.estado = estado;
  if (cliente_id) where.cliente_id = cliente_id;
  const data = await Tarea.findAll({
    where,
    include: [
      { association: 'asignado', attributes: ['id','nombre','apellido'] },
      { association: 'cliente', attributes: ['id','nombre','apellido'] },
    ],
    order: [['fecha_vence','ASC']],
  });
  res.json({ ok: true, data });
});
app.post('/api/tareas', autenticar, async (req, res) => {
  const t = await Tarea.create({ ...req.body, creado_por: req.usuario.id });
  res.status(201).json({ ok: true, data: t });
});
app.put('/api/tareas/:id', autenticar, async (req, res) => {
  const t = await Tarea.findByPk(req.params.id);
  if (!t) return res.status(404).json({ ok: false, msg: 'No encontrada' });
  if (req.body.estado === 'Completada') req.body.completada_en = new Date();
  await t.update(req.body);
  res.json({ ok: true, data: t });
});
app.delete('/api/tareas/:id', autenticar, async (req, res) => {
  const t = await Tarea.findByPk(req.params.id);
  if (!t) return res.status(404).json({ ok: false, msg: 'No encontrada' });
  await t.update({ estado: 'Cancelada' });
  res.json({ ok: true });
});

// CRUD Campañas
app.get('/api/campanas', autenticar, async (req, res) => {
  const data = await Campana.findAll({ order: [['creado_en','DESC']] });
  res.json({ ok: true, data });
});
app.post('/api/campanas', autenticar, async (req, res) => {
  const c = await Campana.create({ ...req.body, creado_por: req.usuario.id });
  res.status(201).json({ ok: true, data: c });
});
app.put('/api/campanas/:id', autenticar, async (req, res) => {
  const c = await Campana.findByPk(req.params.id);
  if (!c) return res.status(404).json({ ok: false, msg: 'No encontrada' });
  await c.update(req.body);
  res.json({ ok: true, data: c });
});
app.post('/api/campanas/:id/enviar', autenticar, async (req, res) => {
  try {
    const c = await Campana.findByPk(req.params.id);
    if (!c) return res.status(404).json({ ok: false, msg: 'No encontrada' });
    await c.update({ estado: 'Activa', fecha_inicio: new Date() });
    res.json({ ok: true, msg: 'Campaña enviada exitosamente', data: c });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});

// Reportes básicos
app.get('/api/reportes/ventas', autenticar, async (req, res) => {
  const [rows] = await sequelize.query(`
    SELECT
      DATE_FORMAT(r.creado_en,'%Y-%m') AS mes,
      COUNT(r.id) AS reservas,
      SUM(r.total_final) AS total_ventas,
      AVG(r.total_final) AS ticket_promedio
    FROM reservas r
    WHERE r.estado != 'Cancelada'
    GROUP BY mes ORDER BY mes ASC LIMIT 12
  `);
  res.json({ ok: true, data: rows });
});

app.get('/api/reportes/agentes', autenticar, async (req, res) => {
  const [rows] = await sequelize.query(`
    SELECT
      u.nombre, u.apellido,
      COUNT(r.id) AS reservas,
      IFNULL(SUM(r.total_final),0) AS total_ventas,
      COUNT(CASE WHEN o.estado='Ganada' THEN 1 END) AS ganadas,
      COUNT(o.id) AS oportunidades
    FROM usuarios u
    LEFT JOIN reservas r ON r.agente_id = u.id
    LEFT JOIN oportunidades o ON o.agente_id = u.id
    GROUP BY u.id ORDER BY total_ventas DESC
  `);
  res.json({ ok: true, data: rows });
});

app.get('/api/reportes/destinos', autenticar, async (req, res) => {
  try {
    const [rows] = await sequelize.query(`
      SELECT
        d.nombre AS destino,
        d.imagen_url,
        COUNT(r.id) AS reservas,
        IFNULL(SUM(r.total_final), 0) AS total_ingresos,
        IFNULL(AVG(r.total_final), 0) AS promedio
      FROM destinos d
      JOIN paquetes p ON p.destino_id = d.id
      JOIN reservas r ON r.paquete_id = p.id
      WHERE r.estado != 'Cancelada'
      GROUP BY d.id, d.nombre, d.imagen_url
      ORDER BY total_ingresos DESC
      LIMIT 10
    `);
    res.json({ ok: true, data: rows });
  } catch(e) {
    res.status(500).json({ ok: false, msg: e.message });
  }
});

// ─── Configuración General ────────────────────────────────────
app.get('/api/configuracion_general', autenticar, async (req, res) => {
  try {
    let conf = await ConfiguracionGeneral.findByPk(1);
    if (!conf) conf = await ConfiguracionGeneral.create({ id: 1, empresa_nombre: 'Viaje 360 CRM' });
    res.json({ ok: true, data: conf });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});

app.put('/api/configuracion_general', autenticar, async (req, res) => {
  try {
    let conf = await ConfiguracionGeneral.findByPk(1);
    if (!conf) conf = await ConfiguracionGeneral.create({ id: 1, empresa_nombre: 'Viaje 360 CRM' });
    await conf.update(req.body);
    res.json({ ok: true, data: conf, msg: 'Configuración guardada' });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});


// ─── Resumen 360° de Cliente ──────────────────────────────────
const { Reserva, Oportunidad, Interaccion: InteraccionM, Tarea: TareaM } = require('./models');
app.get('/api/clientes/:id/resumen', autenticar, async (req, res) => {
  try {
    const cid = req.params.id;
    const [reservas, oportunidades, ultimaInteraccion, tareasPendientes] = await Promise.all([
      Reserva.findAll({ where: { cliente_id: cid }, attributes: ['total_final','costo_neto','estado'] }),
      Oportunidad.findAll({ where: { cliente_id: cid }, attributes: ['valor_estimado','estado','probabilidad'] }),
      InteraccionM.findOne({ where: { cliente_id: cid }, order: [['fecha','DESC']], attributes: ['fecha','tipo'] }),
      TareaM.findAll({ where: { cliente_id: cid, estado: ['Pendiente','En Progreso'] } }),
    ]);
    const ltv = reservas.filter(r => r.estado !== 'Cancelada').reduce((s,r) => s + +r.total_final, 0);
    const utilidadCliente = reservas.filter(r => r.estado !== 'Cancelada').reduce((s,r) => s + (+r.total_final - +r.costo_neto), 0);
    const totalReservas = reservas.length;
    const reservasCompletadas = reservas.filter(r => r.estado === 'Completada').length;
    const opActivas = oportunidades.filter(o => o.estado === 'Activa').length;
    const valorPipeline = oportunidades.filter(o => o.estado === 'Activa').reduce((s,o) => s + +o.valor_estimado, 0);
    res.json({ ok: true, data: { ltv, utilidadCliente, totalReservas, reservasCompletadas, opActivas, valorPipeline, ultimaInteraccion, tareasPendientes: tareasPendientes.length } });
  } catch(e) { res.status(500).json({ ok: false, msg: e.message }); }
});

// ─── SPA fallback ─────────────────────────────────────────────
if (fs.existsSync(clientDist)) {
  app.get('*', (req, res) => {
    const index = path.join(clientDist, 'index.html');
    if (fs.existsSync(index)) res.sendFile(index);
    else res.status(404).send('Frontend no compilado');
  });
}

// ─── 404 ──────────────────────────────────────────────────────
app.use((req, res) => res.status(404).json({ ok: false, msg: 'Ruta no encontrada' }));

// ─── Error handler ────────────────────────────────────────────
app.use((err, req, res, next) => {
  console.error(err);
  res.status(500).json({ ok: false, msg: err.message || 'Error interno del servidor' });
});

// ─── Auto-seed (BD vacía en primer deploy) ────────────────────
async function autoSeed() {
  try {
    const { Rol } = require('./models');
    const count = await Rol.count().catch(() => 0);
    if (count > 0) return;

    const sqlFiles = [
      path.join(__dirname, '../../schema.sql'),
      path.join(__dirname, '../../seed.sql'),
      path.join(__dirname, '../../seed_costos.sql'),
    ];

    for (const file of sqlFiles) {
      if (!fs.existsSync(file)) continue;
      const sql = fs.readFileSync(file, 'utf8');
      const stmts = sql.split(/;\s*(\r?\n|$)/)
        .map(s => s.trim())
        .filter(s => s && !s.startsWith('--') && !/^use\s/i.test(s));
      for (const stmt of stmts) {
        await sequelize.query(stmt).catch(() => {});
      }
      console.log(`✅  Seed ejecutado: ${path.basename(file)}`);
    }
  } catch (e) {
    console.warn('⚠️  autoSeed falló (no crítico):', e.message);
  }
}

// ─── Iniciar con reintentos ───────────────────────────────────
const PORT = process.env.PORT || 3001;

async function iniciar(intentos = 10) {
  for (let i = 1; i <= intentos; i++) {
    try {
      await sequelize.authenticate();
      console.log('✅  MySQL conectado correctamente');
      await sequelize.sync({ alter: false });
      await autoSeed();
      app.listen(PORT, () => console.log(`🚀  Servidor corriendo en puerto ${PORT}`));
      return;
    } catch (err) {
      console.error(`❌  Intento ${i}/${intentos} fallido: ${err.message}`);
      if (i < intentos) await new Promise(r => setTimeout(r, 3000));
    }
  }
  process.exit(1);
  });
