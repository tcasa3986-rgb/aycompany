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
const { initBot } = require('./services/plataformaBot');
const { startPoller }   = require('./services/facebookPoller');
const { startReminder }  = require('./services/meetingReminder');
const { startFollowUp }  = require('./services/followUpService');
const { iniciarScheduler } = require('./services/agentScheduler');
const { iniciarProspectorScheduler } = require('./services/prospectorScheduler');
const { iniciarLicenciaExpirationScheduler } = require('./services/licenciaExpirationScheduler');

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
app.use('/api/auth/login', authLimiter);

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
app.get('/api/health', (_, res) => res.json({ ok: true }));
app.use('/api',            require('./routes/socialRoutes'));

// ── Manejador global de errores (no exponer stack en producción) ──────────────
app.use((err, req, res, next) => {
    if (err.message?.includes('CORS')) return res.status(403).json({ ok: false, msg: 'Origen no permitido' });
    console.error('Error no manejado:', err.message);
    res.status(500).json({ ok: false, msg: isProd ? 'Error interno del servidor' : err.message });
});

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

async function iniciar(intentos = 5) {
    for (let i = 1; i <= intentos; i++) {
        try {
            await sequelize.authenticate();
            await sequelize.sync({ alter: true });
            await seedAdmin();
            app.listen(PORT, () => console.log(`🚀 Plataforma corriendo en puerto ${PORT}`));
            initBot();
            startPoller();
            startReminder();
            startFollowUp();
            iniciarScheduler();
            iniciarProspectorScheduler();
            iniciarLicenciaExpirationScheduler();
            return;
        } catch (err) {
            console.error(`Intento ${i}/${intentos} fallido: ${err.message}`);
            if (i === intentos) { console.error('No se pudo conectar a la BD'); process.exit(1); }
            await new Promise(r => setTimeout(r, 5000));
        }
    }
}

iniciar();
