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
const { iniciarLicenciaExpirationScheduler } = require('./services/licenciaExpirationScheduler');
const { iniciarSeoReportScheduler } = require('./services/seoReportScheduler');
const { iniciarBalanceMonitor } = require('./services/balanceMonitor');
const { iniciarVentasReportScheduler } = require('./services/ventasReportScheduler');
const { iniciarCarouselScheduler } = require('./services/carouselScheduler');
const { iniciarStoriesScheduler }  = require('./services/storiesScheduler');
const { iniciarAlertasCobro }      = require('./services/alertasCobroScheduler');

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
app.use('/api/auth/login',             authLimiter);

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
app.use('/api/costos',    require('./routes/costosRoutes'));
app.use('/api/finanzas',  require('./routes/finanzasRoutes'));
app.use('/api/hoy',       require('./routes/hoyRoutes'));
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
app.use('/api/marketing',  require('./routes/marketingRoutes'));
app.use('/api/reuniones',  require('./routes/reunionesRoutes'));
app.use('/api/contenido',  require('./routes/contenidoRoutes'));
app.use('/api/metricas',   require('./routes/metricasRoutes'));
app.use('/api/eventos',    require('./routes/eventosRoutes'));
app.use('/api/asistente',  require('./routes/asistenteRoutes'));
app.use('/api/leads',       require('./routes/leadsRoutes'));
app.use('/api/seo',         require('./routes/seoRoutes'));
app.use('/api/carousel',   require('./routes/carouselRoutes'));
app.use('/api/stories',    require('./routes/storiesRoutes'));
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
    // Limpieza: el antiguo usuario de recuperación con clave fija en el código
    // era una puerta trasera. Se elimina si todavía existe en la BD.
    // Nunca debe tumbar el arranque: si no se puede borrar (FK), se deja inutilizable.
    try {
        const borrados = await Usuario.destroy({ where: { email: 'recover@aicompany.co' } });
        if (borrados) console.log('🧹 Usuario de recuperación eliminado');
    } catch (e) {
        const u = await Usuario.findOne({ where: { email: 'recover@aicompany.co' } });
        if (u) await u.update({ activo: false, rol: 'vendedor', password: await bcrypt.hash(require('crypto').randomBytes(32).toString('hex'), 10) });
        console.warn('⚠️  Usuario de recuperación no se pudo borrar; quedó desactivado:', e.message);
    }

    // Admin normal
    if (process.env.ADMIN_EMAIL && process.env.ADMIN_PASSWORD) {
        const hash = await bcrypt.hash(process.env.ADMIN_PASSWORD, 10);
        const existe = await Usuario.findOne({ where: { email: process.env.ADMIN_EMAIL } });
        if (!existe) {
            await Usuario.create({ nombre: 'Administrador', email: process.env.ADMIN_EMAIL, password: hash, rol: 'admin' });
        } else {
            await existe.update({ password: hash });
        }
        console.log(`✅ Admin actualizado: ${process.env.ADMIN_EMAIL}`);
    }
}

const PORT = process.env.PORT || 5000;

// Agrega una columna si no existe; ignora el error si ya existe
let migracionesFallidas = [];

async function addCol(table, col, opts) {
    try {
        await sequelize.getQueryInterface().addColumn(table, col, opts);
        console.log(`  + columna agregada: ${table}.${col}`);
    } catch (e) {
        // "ya existe" es lo esperado; cualquier otro error hay que verlo, porque
        // una columna faltante rompe la validación de licencias en silencio.
        if (/duplicate column|already exists/i.test(e.message)) return;
        console.error(`MIGRACION FALLIDA ${table}.${col}: ${e.message}`);
        migracionesFallidas.push(`${table}.${col}: ${e.message}`);
    }
}

// Ajustes que deben correr SIEMPRE, haya funcionado o no sequelize.sync.
async function migracionesLicencias() {
    const { DataTypes } = require('sequelize');
    // Se crean aqui (antes de sync) para que sequelize.sync({alter}) no tenga
    // que hacer DDL pesado con el servicio aun sin escuchar.
    await addCol('licencias', 'dia_corte',      { type: DataTypes.INTEGER,        allowNull: true });
    await addCol('licencias', 'dias_gracia',    { type: DataTypes.INTEGER,        defaultValue: 0 });
    await addCol('licencias', 'bloquear',       { type: DataTypes.BOOLEAN,        defaultValue: true });
    await addCol('licencias', 'precio_mensual', { type: DataTypes.DECIMAL(10, 2), allowNull: true });
    await addCol('licencias', 'notas',          { type: DataTypes.TEXT,           allowNull: true });
    await addCol('pagos', 'referencia_externa', { type: DataTypes.STRING(120),    allowNull: true });

    // contratos — estructura de pago (anticipo + saldo financiado)
    await addCol('contratos', 'anticipo',      { type: DataTypes.DECIMAL(12, 2), defaultValue: 0 });
    await addCol('contratos', 'cuota_mensual', { type: DataTypes.DECIMAL(12, 2), defaultValue: 0 });
    await addCol('contratos', 'dia_cobro',     { type: DataTypes.INTEGER,        allowNull: true });
    await addCol('contratos', 'primera_cuota', { type: DataTypes.DATEONLY,       allowNull: true });
    await addCol('contratos', 'mensualidad',   { type: DataTypes.DECIMAL(12, 2), defaultValue: 0 });
    await addCol('contratos', 'tipo_servicio', { type: DataTypes.ENUM('sistema','marketing','pagina_web','mixto','otro'), defaultValue: 'sistema' });

    // leads — economía del trato (tratos por cerrar)
    await addCol('leads', 'valor_unico',   { type: DataTypes.DECIMAL(12, 2), defaultValue: 0 });
    await addCol('leads', 'valor_mensual', { type: DataTypes.DECIMAL(12, 2), defaultValue: 0 });
    await addCol('leads', 'probabilidad',  { type: DataTypes.INTEGER,        defaultValue: 50 });
    await addCol('leads', 'fecha_estimada_cierre', { type: DataTypes.DATEONLY, allowNull: true });

    // costos_servidor deja de ser solo infraestructura: ahora lleva también los
    // pagos personales con fecha (arriendo, servicios, gimnasio...)
    await addCol('costos_servidor', 'ambito',    { type: DataTypes.ENUM('empresa', 'personal'), defaultValue: 'empresa' });
    await addCol('costos_servidor', 'categoria', { type: DataTypes.STRING(40), defaultValue: 'hosting' });

    // ENUM de metodo_pago: solo se reescribe si de verdad le faltan valores
    // (el ALTER copia la tabla y demora el arranque).
    try {
        const [filas] = await sequelize.query("SHOW COLUMNS FROM pagos LIKE 'metodo_pago'");
        const col = filas && filas[0];
        if (col && !/mercadopago/i.test(col.Type)) {
            console.log('  ~ ampliando ENUM pagos.metodo_pago...');
            await sequelize.query("ALTER TABLE pagos MODIFY metodo_pago ENUM('efectivo','transferencia','tarjeta','nequi','mercadopago','otro') NOT NULL DEFAULT 'efectivo'");
        }
    } catch (e) {
        console.error('ENUM metodo_pago:', e.message);
        migracionesFallidas.push('pagos.metodo_pago: ' + e.message);
    }

    // UNIQUE que hace idempotente el webhook de MercadoPago.
    try {
        const [idx] = await sequelize.query("SHOW INDEX FROM pagos WHERE Key_name = 'pagos_referencia_externa_unique'");
        if (!idx.length) await sequelize.query('ALTER TABLE pagos ADD UNIQUE INDEX pagos_referencia_externa_unique (referencia_externa)');
    } catch (e) {
        console.error('indice referencia_externa:', e.message);
        migracionesFallidas.push('pagos.referencia_externa UNIQUE: ' + e.message);
    }

    // Pagos viejos de MercadoPago: se les rellena la referencia desde las notas
    // para que una reentrega de un webhook antiguo no los duplique.
    try {
        await sequelize.query("UPDATE pagos SET referencia_externa = CONCAT('mp_pay_', TRIM(SUBSTRING_INDEX(notas, '#', -1))) WHERE referencia_externa IS NULL AND notas LIKE 'MP pago%#%'");
        await sequelize.query("UPDATE pagos SET referencia_externa = CONCAT('mp_sub_', TRIM(SUBSTRING_INDEX(notas, '#', -1))) WHERE referencia_externa IS NULL AND notas LIKE 'MP suscrip%#%'");
    } catch (e) { console.error('backfill referencia_externa:', e.message); }

    // Filas viejas: NULL en dias_gracia/bloquear se interpretaria como "bloquea
    // sin tolerancia". Se dejan explicitas para que nadie dependa del default.
    try {
        await sequelize.query('UPDATE licencias SET dias_gracia = 0 WHERE dias_gracia IS NULL');
        await sequelize.query('UPDATE licencias SET bloquear = 1 WHERE bloquear IS NULL');
    } catch (e) {
        console.error('backfill licencias:', e.message);
        migracionesFallidas.push('backfill licencias: ' + e.message);
    }
}

// Verifica que las columnas criticas existan de verdad antes de servir trafico.
async function verificarEsquema() {
    const requeridas = {
        licencias: ['dia_corte', 'dias_gracia', 'bloquear', 'precio_mensual'],
        pagos:     ['referencia_externa']
    };
    const faltantes = [];
    for (const [tabla, cols] of Object.entries(requeridas)) {
        const desc = await sequelize.getQueryInterface().describeTable(tabla).catch(() => ({}));
        for (const c of cols) if (!(c in desc)) faltantes.push(`${tabla}.${c}`);
    }
    try {
        const [idx] = await sequelize.query("SHOW INDEX FROM pagos WHERE Key_name = 'pagos_referencia_externa_unique'");
        if (!idx.length) faltantes.push('pagos.referencia_externa UNIQUE (webhook podria duplicar pagos)');
    } catch (e) { faltantes.push('no se pudo verificar el indice UNIQUE: ' + e.message); }

    if (faltantes.length) console.error(`FALTA EN EL ESQUEMA: ${faltantes.join(', ')}`);
    else console.log('Esquema de licencias verificado');
    return faltantes;
}

async function syncSchema() {
    // sync() SIN alter: solo crea las tablas que falten. Nunca reescribe columnas.
    //
    // Antes era sync({ alter: true }) y eso costó caro dos veces:
    //   1. Agregaba un índice nuevo por cada campo `unique` EN CADA ARRANQUE. La
    //      base de producción llegó a 247 índices duplicados y a 63 de los 64
    //      que MySQL permite por tabla; el arranque empezó a fallar.
    //   2. Reescribía columnas que ya estaban bien (ALTER TABLE reuniones CHANGE
    //      recordatorio_enviado ...) y una de esas se quedó colgada esperando un
    //      commit. Un ALTER lento al arrancar es un despliegue que Railway da
    //      por caído.
    //
    // Las columnas nuevas se agregan de forma explícita en migracionesLicencias()
    // y en el bloque de abajo, con addCol(), que es idempotente y no toca nada
    // que ya exista.
    await sequelize.sync();
    console.log('✅ Tablas verificadas (sin alter)');

    const { DataTypes } = require('sequelize');
    // usuarios — columnas del sistema de vendedores (ya retirado; las columnas quedan)
    await addCol('usuarios', 'telefono',        { type: DataTypes.STRING(20),  allowNull: true });
    await addCol('usuarios', 'ciudad',          { type: DataTypes.STRING(100), allowNull: true });
    await addCol('usuarios', 'activo',          { type: DataTypes.BOOLEAN, defaultValue: true });
    await addCol('usuarios', 'referido_por',    { type: DataTypes.INTEGER,     allowNull: true });
    await addCol('usuarios', 'codigo_referido', { type: DataTypes.STRING(20),  allowNull: true });
    await addCol('usuarios', 'empresa_id',      { type: DataTypes.INTEGER,     allowNull: true });
    // productos
    await addCol('productos', 'descripcion_venta', { type: DataTypes.TEXT,     allowNull: true });
    await addCol('productos', 'categoria',      { type: DataTypes.STRING(60),  defaultValue: 'Sistema' });
    await addCol('productos', 'visible_vendedor', { type: DataTypes.BOOLEAN,   defaultValue: true });
    await addCol('productos', 'imagen_url',     { type: DataTypes.STRING(300), allowNull: true });
    console.log('✅ Migraciones explícitas completadas');
}

async function iniciar(intentos = 5) {
    for (let i = 1; i <= intentos; i++) {
        try {
            await sequelize.authenticate();
            console.log(`✅ BD conectada (intento ${i})`);
            migracionesFallidas = [];
            await migracionesLicencias();   // primero: deja el esquema listo
            await syncSchema();             // despues: sync ya no tiene DDL pesado que hacer
            const faltantes = await verificarEsquema();
            if (migracionesFallidas.length || faltantes.length) {
                const problemas = migracionesFallidas.concat(faltantes.map(f => 'falta ' + f));
                require('./services/telegramService')
                    .enviar('*mi-plataforma arranco con el esquema incompleto*\n' + problemas.join('\n'))
                    .catch(() => {});
            }
            await seedAdmin();
            app.listen(PORT, () => console.log(`🚀 Plataforma corriendo en puerto ${PORT}`));
            try { initBot(); } catch(e) { console.warn('⚠️ initBot:', e.message); }
            try { startPoller(); } catch(e) { console.warn('⚠️ startPoller:', e.message); }
            try { startReminder(); } catch(e) { console.warn('⚠️ startReminder:', e.message); }
            try { startFollowUp(); } catch(e) { console.warn('⚠️ startFollowUp:', e.message); }
            try { iniciarLicenciaExpirationScheduler(); } catch(e) { console.warn('⚠️ licenciaScheduler:', e.message); }
            try { iniciarSeoReportScheduler(); } catch(e) { console.warn('⚠️ seoReportScheduler:', e.message); }
            try { iniciarBalanceMonitor(); } catch(e) { console.warn('⚠️ balanceMonitor:', e.message); }
            try { iniciarVentasReportScheduler(); } catch(e) { console.warn('⚠️ ventasReport:', e.message); }
            try { iniciarCarouselScheduler(); } catch(e) { console.warn('⚠️ carousel:', e.message); }
            try { iniciarStoriesScheduler();  } catch(e) { console.warn('⚠️ stories:', e.message); }
            try { iniciarAlertasCobro();      } catch(e) { console.warn('⚠️ alertasCobro:', e.message); }
            return;
        } catch (err) {
            console.error(`Intento ${i}/${intentos} fallido: ${err.message}`);
            if (i === intentos) { console.error('No se pudo conectar a la BD'); process.exit(1); }
            await new Promise(r => setTimeout(r, 5000));
        }
    }
}

iniciar();
