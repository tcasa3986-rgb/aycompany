require('dotenv').config();
const http = require('http');
const path = require('path');
const fs = require('fs');
const express = require('express');
const cors = require('cors');
const morgan = require('morgan');
const routes = require('./src/routes');
const { initSocket } = require('./src/config/socket');
const swaggerUi = require('swagger-ui-express');
const swaggerSpec = require('./src/config/swagger');
const pool = require('./src/config/db');

const app = express();
const server = http.createServer(app);

app.use(cors({ origin: '*', credentials: false }));
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));
app.use(morgan('dev'));

app.use('/uploads', express.static(path.join(__dirname, 'public/uploads')));

app.use('/api', routes);

app.use('/api/docs', swaggerUi.serve, swaggerUi.setup(swaggerSpec, {
  customSiteTitle: 'CRM Ventas API',
  customCss: '.swagger-ui .topbar { background-color: #0f766e; }',
}));

app.get('/health', (_req, res) => res.json({ status: 'ok', time: new Date() }));

app.use((err, _req, res, _next) => {
  console.error(err.stack);
  res.status(500).json({ message: 'Error interno del servidor' });
});

// Serve React frontend
const FRONTEND_DIST = path.join(__dirname, '../frontend/dist');
if (fs.existsSync(FRONTEND_DIST)) {
  app.use(express.static(FRONTEND_DIST));
  app.get('*', (_req, res) => res.sendFile(path.join(FRONTEND_DIST, 'index.html')));
}

async function autoSeed() {
  try {
    const [[row]] = await pool.query('SELECT COUNT(*) AS n FROM usuarios').catch(() => [[{ n: 0 }]]);
    if (row.n > 0) return;
    const sqlFiles = [
      path.join(__dirname, '../database/schema.sql'),
      path.join(__dirname, '../database/update_v1_1.sql'),
      path.join(__dirname, '../database/update_v1_2.sql'),
      path.join(__dirname, '../database/update_v1_3.sql'),
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
      console.log(`✅ Seed: ${path.basename(file)}`);
    }
  } catch (e) {
    console.warn('⚠️  autoSeed falló:', e.message);
  }
}

const { startRunner } = require('./src/services/workflow_runner');

initSocket(server);
startRunner();

const PORT = process.env.PORT || 5000;
server.listen(PORT, async () => {
  console.log(`CRM Ventas corriendo en puerto ${PORT}`);
  await autoSeed();
});
