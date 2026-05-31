require('dotenv').config();
const express   = require('express');
const cors      = require('cors');
const helmet    = require('helmet');
const rateLimit = require('express-rate-limit');
const bcrypt    = require('bcryptjs');
const path      = require('path');
const fs        = require('fs');
const sequelize = require('./config/db');
const { Usuario } = require('./models');
const { seedProductos } = require('./controllers/portalVendedorController');
const { initBot } = require('./services/plataformaBot');
const { startPoller }   = require('./services/facebookPoller');
const { startReminder }  = require('./services/meetingReminder');
const { startFollowUp }  = require('./services/followUpService');
const { iniciarScheduler } = require('./services/agentScheduler');
const { iniciarProspectorScheduler } = require('./services/prospectorScheduler');
const { iniciarLicenciaExpirationScheduler } = require('./services/licenciaExpirationScheduler');
const { iniciarSeoReportScheduler } = require('./services/seoReportScheduler');
const { iniciarBalanceMonitor } = require('./services/balanceMonitor');
const { iniciarVentasReportScheduler } = require('./services/ventasReportScheduler');
const { iniciarCarouselScheduler } = require('./services/carouselScheduler');

const app = express();
const isProd = process.env.NODE_ENV === 'production';

// ── Seguridad: cabeceras HTTP ────────────────────────────────────────────────
app.use(helmet({
    contentSecurityPolicy: false, // desactivado para no romper el frontend React
    crossOriginEmbedderPolicy: false
}));

// ── CORS ─────────────────────────────────────────────────────────────────────
const allowedOrigins = isProd
    ? [process.env.ALLOWED_ORIGIN || 'https://mi-plataforma-production.up.railway.app']
    : ['http://localhost:4000', 'http://localhost:5173'];

app.use(cors({
    origin: (origin, cb) => {
        // Permitir sin origin (mobile apps, Postman, webhooks server-to-server)
        if (!origin) return cb(null, true);
        if (allowedOrigins.includes(origin)) return cb(null, true);
        cb(new Error('CORS: origen no permitido'));
    },
    credentials: true
}));

// ── Demo proxies (deben ir ANTES de express.json para no consumir el body) ────
const { DEMOS, NODE_DEMOS, PHP_DEMOS, initDemos, nodeProxyMiddleware, phpProxyMiddleware } = require('./demoManager');
for (const demo of NODE_DEMOS) {
    if (demo.dist === null) {
        // Server-rendered Node.js app (EJS/views) — proxy all requests like PHP
        app.use(`/demos/${demo.name}`, phpProxyMiddleware(demo));
    } else {
        app.use(`/demos/${demo.name}/api`, nodeProxyMiddleware(demo));
    }
}
for (const demo of PHP_DEMOS) {
    // Demos con HTML estático no usan proxy PHP
    if (!demo.dist) app.use(`/demos/${demo.name}`, phpProxyMiddleware(demo));
}

// ── Límite de tamaño de peticiones ───────────────────────────────────────────
app.use(express.json({ limit: '2mb' }));

// ── Rate limiting global ──────────────────────────────────────────────────────
const globalLimiter = rateLimit({
    windowMs: 15 * 60 * 1000,
    max: 300,
    standardHeaders: true,
    legacyHeaders: false,
    message: { ok: false, msg: 'Demasiadas peticiones, intenta en 15 minutos' }
});
app.use('/api', globalLimiter);

// ── Rate limiting estricto para autenticación ─────────────────────────────────
const authLimiter = rateLimit({
    windowMs: 15 * 60 * 1000,
    max: 10,
    standardHeaders: true,
    legacyHeaders: false,
    message: { ok: false, msg: 'Demasiados intentos de inicio de sesión, espera 15 minutos' },
    skipSuccessfulRequests: true
});
app.use('/api/auth/login',             authLimiter);
app.use('/api/auth/registro-vendedor', authLimiter);

// ── Rate limiting para webhooks externos (más permisivo) ─────────────────────
const webhookLimiter = rateLimit({
    windowMs: 1 * 60 * 1000,
    max: 60,
    standardHeaders: true,
    legacyHeaders: false,
    message: { ok: false, msg: 'Límite de webhook alcanzado' }
});
app.use('/api/webhook', webhookLimiter);

// En producción servir el frontend compilado
if (isProd) {
    const clientDist = path.join(__dirname, '../../client/dist');
    if (fs.existsSync(clientDist)) {
        app.use(express.static(clientDist));
    }
}

app.use('/api/portal',    require('./routes/portalRoutes'));
app.use('/api/auth',      require('./routes/authRoutes'));
app.use('/api/clientes',  require('./routes/clientesRoutes'));
app.use('/api/productos', require('./routes/productosRoutes'));
app.use('/api/licencias', require('./routes/licenciasRoutes'));
app.use('/api/pagos',     require('./routes/pagosRoutes'));
app.use('/api/dashboard', require('./routes/dashboardRoutes'));
app.use('/api/facturas',   require('./routes/facturasRoutes'));
app.use('/api/tickets',        require('./routes/ticketsRoutes'));
app.use('/api/cartera',        require('./routes/carteraRoutes'));
app.use('/api/configuracion',  require('./routes/configuracionRoutes'));
app.use('/api/proyectos',      require('./routes/proyectosRoutes'));
app.use('/api/contratos',      require('./routes/contratosRoutes'));
app.use('/api/usuarios',       require('./routes/usuariosRoutes'));
app.use('/api/reportes',       require('./routes/reportesRoutes'));
app.use('/api/analitica',      require('./routes/analiticaRoutes'));
app.use('/api/vendedor',       require('./routes/portalVendedorRoutes'));
app.use('/api/admin/vendedores', require('./routes/adminVendedoresRoutes'));
app.use('/api/empresas',       require('./routes/empresaRoutes'));
app.use('/api/marketing',  require('./routes/marketingRoutes'));
app.use('/api/reuniones',  require('./routes/reunionesRoutes'));
app.use('/api/contenido',  require('./routes/contenidoRoutes'));
app.use('/api/metricas',   require('./routes/metricasRoutes'));
app.use('/api/eventos',    require('./routes/eventosRoutes'));
app.use('/api/asistente',  require('./routes/asistenteRoutes'));
app.use('/api/leads',       require('./routes/leadsRoutes'));
app.use('/api/agente',      require('./routes/agenteRoutes'));
app.use('/api/prospector',  require('./routes/prospectorRoutes'));
app.use('/api/llamadas',    require('./routes/llamadasRoutes'));
app.use('/api/seo',         require('./routes/seoRoutes'));
app.use('/api/carousel',   require('./routes/carouselRoutes'));
app.get('/api/health', (_, res) => res.json({ ok: true }));
app.use('/api',            require('./routes/socialRoutes'));

// ── Manejador global de errores (no exponer stack en producción) ──────────────
app.use((err, req, res, next) => {
    if (err.message?.includes('CORS')) return res.status(403).json({ ok: false, msg: 'Origen no permitido' });
    console.error('Error no manejado:', err.message);
    res.status(500).json({ ok: false, msg: isProd ? 'Error interno del servidor' : err.message });
});

// ── Demo frontends estáticos ─────────────────────────────────────────────────
if (isProd) {
    for (const demo of DEMOS) {
        if (fs.existsSync(demo.dist)) {
            app.use(`/demos/${demo.name}`, express.static(demo.dist));
            app.get(`/demos/${demo.name}`, (_, res) => res.sendFile(path.join(demo.dist, 'index.html')));
            app.get(`/demos/${demo.name}/*`, (_, res) => res.sendFile(path.join(demo.dist, 'index.html')));
        }
    }
}

// En producción redirigir todo lo demás al index.html del React
if (isProd) {
    app.get('*', (req, res) => {
        const index = path.join(__dirname, '../../client/dist/index.html');
        if (fs.existsSync(index)) res.sendFile(index);
        else res.status(404).send('Frontend no compilado');
    });
}

async function seedAdmin() {
    const existe = await Usuario.findOne({ where: { email: process.env.ADMIN_EMAIL } });
    if (!existe) {
        const hash = await bcrypt.hash(process.env.ADMIN_PASSWORD, 10);
        await Usuario.create({ nombre: 'Administrador', email: process.env.ADMIN_EMAIL, password: hash, rol: 'admin' });
        console.log(`✅ Usuario admin creado: ${process.env.ADMIN_EMAIL}`);
    }
}

const PORT = process.env.PORT || 5000;

// Agrega una columna si no existe; ignora el error si ya existe
async function addCol(table, col, opts) {
    try {
        await sequelize.getQueryInterface().addColumn(table, col, opts);
        console.log(`  + columna agregada: ${table}.${col}`);
    } catch (_) { /* ya existe — ok */ }
}

async function syncSchema() {
    try {
        await sequelize.sync({ alter: true });
        console.log('✅ Schema sincronizado (alter)');
    } catch (alterErr) {
        console.warn('⚠️  sync alter falló, aplicando migraciones manuales:', alterErr.message);
        try { await sequelize.sync(); } catch (_) {}

        const { DataTypes } = require('sequelize');
        // usuarios — columnas del sistema de vendedores
        await addCol('usuarios', 'telefono',        { type: DataTypes.STRING(20),  allowNull: true });
        await addCol('usuarios', 'ciudad',          { type: DataTypes.STRING(100), allowNull: true });
        await addCol('usuarios', 'activo',          { type: DataTypes.BOOLEAN, defaultValue: true });
        await addCol('usuarios', 'referido_por',    { type: DataTypes.INTEGER,     allowNull: true });
        await addCol('usuarios', 'codigo_referido', { type: DataTypes.STRING(20),  allowNull: true });
        await addCol('usuarios', 'empresa_id',      { type: DataTypes.INTEGER,     allowNull: true });
        // productos — columnas de demo
        await addCol('productos', 'demo_url',       { type: DataTypes.STRING(300), allowNull: true });
        await addCol('productos', 'demo_usuario',   { type: DataTypes.STRING(100), allowNull: true });
        await addCol('productos', 'demo_password',  { type: DataTypes.STRING(100), allowNull: true });
        await addCol('productos', 'descripcion_venta', { type: DataTypes.TEXT,     allowNull: true });
        await addCol('productos', 'categoria',      { type: DataTypes.STRING(60),  defaultValue: 'Sistema' });
        await addCol('productos', 'visible_vendedor', { type: DataTypes.BOOLEAN,   defaultValue: true });
        await addCol('productos', 'imagen_url',     { type: DataTypes.STRING(300), allowNull: true });
        console.log('✅ Migraciones manuales completadas');
    }
}

async function iniciar(intentos = 5) {
    for (let i = 1; i <= intentos; i++) {
        try {
            await sequelize.authenticate();
            console.log(`✅ BD conectada (intento ${i})`);
            await syncSchema();
            await seedAdmin();
            app.listen(PORT, () => console.log(`🚀 Plataforma corriendo en puerto ${PORT}`));
            initDemos().catch(e => console.error('⚠️  initDemos falló (no crítico):', e.message));
            seedProductos().catch(e => console.error('⚠️  seedProductos falló (no crítico):', e.message));
            try { initBot(); } catch(e) { console.warn('⚠️ initBot:', e.message); }
            try { startPoller(); } catch(e) { console.warn('⚠️ startPoller:', e.message); }
            try { startReminder(); } catch(e) { console.warn('⚠️ startReminder:', e.message); }
            try { startFollowUp(); } catch(e) { console.warn('⚠️ startFollowUp:', e.message); }
            try { iniciarScheduler(); } catch(e) { console.warn('⚠️ agentScheduler:', e.message); }
            try { iniciarProspectorScheduler(); } catch(e) { console.warn('⚠️ prospectorScheduler:', e.message); }
            try { iniciarLicenciaExpirationScheduler(); } catch(e) { console.warn('⚠️ licenciaScheduler:', e.message); }
            try { iniciarSeoReportScheduler(); } catch(e) { console.warn('⚠️ seoReportScheduler:', e.message); }
            try { iniciarBalanceMonitor(); } catch(e) { console.warn('⚠️ balanceMonitor:', e.message); }
            try { iniciarVentasReportScheduler(); } catch(e) { console.warn('⚠️ ventasReport:', e.message); }
            try { iniciarCarouselScheduler(); } catch(e) { console.warn('⚠️ carousel:', e.message); }
            return;
        } catch (err) {
            console.error(`Intento ${i}/${intentos} fallido: ${err.message}`);
            if (i === intentos) { console.error('No se pudo conectar a la BD'); process.exit(1); }
            await new Promise(r => setTimeout(r, 5000));
        }
    }
}

iniciar();
