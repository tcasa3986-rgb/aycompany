require('dotenv').config();
const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const path = require('path');
const fs = require('fs');
const http = require('http');
const { Server } = require('socket.io');

const { errorHandler, notFound } = require('./src/middlewares/error.middleware');

// Rutas
const authRoutes = require('./src/routes/auth.routes');
const dashboardRoutes = require('./src/routes/dashboard.routes');
const unidadesRoutes = require('./src/routes/unidades.routes');
const residentesRoutes = require('./src/routes/residentes.routes');
const cobranzaRoutes = require('./src/routes/cobranza.routes');
const mantenimientoRoutes = require('./src/routes/mantenimiento.routes');
const amenidadesRoutes = require('./src/routes/amenidades.routes');
const accesoRoutes = require('./src/routes/acceso.routes');
const comunicacionesRoutes = require('./src/routes/comunicaciones.routes');
const configuracionRoutes = require('./src/routes/configuracion.routes');
const proveedoresRoutes = require('./src/routes/proveedores.routes');
const reportesRoutes = require('./src/routes/reportes.routes');
const contabilidadRoutes = require('./src/routes/contabilidad.routes');
const sistemaRoutes = require('./src/routes/sistema.routes');

const pool = require('./src/config/db');

const app = express();
const server = http.createServer(app);

// Auto-seed: run schema + seed SQL files on fresh DB
async function autoSeed() {
  try {
    const [[row]] = await pool.query('SELECT COUNT(*) AS n FROM usuarios').catch(() => [[{ n: 0 }]]);
    if (row.n > 0) return;
    const sqlFiles = [
      path.join(__dirname, '../database/schema.sql'),
      path.join(__dirname, '../database/seed.sql'),
    ];
    for (const file of sqlFiles) {
      if (!fs.existsSync(file)) continue;
      const sql = fs.readFileSync(file, 'utf8');
      const stmts = sql.split(/;\s*(\r?\n|$)/)
        .map(s => s.trim())
        .filter(s => s && !s.startsWith('--') && !/^use\s/i.test(s) && !/^create database/i.test(s));
      for (const stmt of stmts) {
        await pool.query(stmt).catch(() => {});
      }
      console.log(`✅ Seed ejecutado: ${path.basename(file)}`);
    }
  } catch (e) {
    console.warn('⚠️  autoSeed falló (no crítico):', e.message);
  }
}

// Socket.io
const io = new Server(server, {
  cors: { origin: '*', methods: ['GET', 'POST'] },
});

io.on('connection', (socket) => {
  socket.on('disconnect', () => {});
  socket.on('join-room', (room) => socket.join(room));
});

app.set('io', io);

// Middlewares
app.use(helmet({ crossOriginResourcePolicy: false }));
app.use(cors({ origin: '*', credentials: false }));
app.use(morgan('dev'));
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true }));

app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// API Routes
app.use('/api/auth', authRoutes);
app.use('/api/dashboard', dashboardRoutes);
app.use('/api/unidades', unidadesRoutes);
app.use('/api/residentes', residentesRoutes);
app.use('/api/cobranza', cobranzaRoutes);
app.use('/api/mantenimiento', mantenimientoRoutes);
app.use('/api/amenidades', amenidadesRoutes);
app.use('/api/acceso', accesoRoutes);
app.use('/api/comunicaciones', comunicacionesRoutes);
app.use('/api/configuracion', configuracionRoutes);
app.use('/api/proveedores', proveedoresRoutes);
app.use('/api/reportes', reportesRoutes);
app.use('/api/contabilidad', contabilidadRoutes);
app.use('/api/sistema', sistemaRoutes);

app.get('/api/health', (req, res) => {
  res.json({ success: true, message: 'CRM Condominio API funcionando', timestamp: new Date() });
});

// Serve React frontend
const FRONTEND_DIST = path.join(__dirname, '../frontend/dist');
if (fs.existsSync(FRONTEND_DIST)) {
  app.use(express.static(FRONTEND_DIST));
  app.get('*', (req, res) => res.sendFile(path.join(FRONTEND_DIST, 'index.html')));
} else {
  app.use(notFound);
  app.use(errorHandler);
}

const PORT = process.env.PORT || 5000;
server.listen(PORT, async () => {
  console.log(`\n🚀 CRM Condominio corriendo en http://localhost:${PORT}`);
  await autoSeed();
});

module.exports = { app, io };
