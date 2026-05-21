'use strict';
const { spawn } = require('child_process');
const http     = require('http');
const path     = require('path');
const mysql2   = require('mysql2/promise');
const fs       = require('fs');

// Extrae host/port/user/password de MYSQL_URL, MYSQLHOST, o DB_HOST (en ese orden)
function getDbConfig() {
  const url = process.env.MYSQL_URL || process.env.DATABASE_URL;
  if (url) {
    try {
      const u = new URL(url);
      return { host: u.hostname, port: u.port || '3306', user: decodeURIComponent(u.username), password: decodeURIComponent(u.password) };
    } catch (_) {}
  }
  return {
    host:     process.env.DB_HOST     || process.env.MYSQLHOST     || '127.0.0.1',
    port:     process.env.DB_PORT     || process.env.MYSQLPORT     || '3306',
    user:     process.env.DB_USER     || process.env.MYSQLUSER     || 'root',
    password: process.env.DB_PASSWORD || process.env.MYSQLPASSWORD || '',
  };
}

// ── Node.js demos ─────────────────────────────────────────────────
const NODE_DEMOS = [
  {
    name: 'viaje360',
    port: 5200,
    db:   'demo_viaje360',
    cwd:  path.join(__dirname, '../demos/viaje360/backend'),
    entry: 'src/app.js',
    dist: path.join(__dirname, '../demos/viaje360/frontend/dist'),
    sqlFiles: [
      path.join(__dirname, '../demos/viaje360/schema.sql'),
      path.join(__dirname, '../demos/viaje360/seed.sql'),
      path.join(__dirname, '../demos/viaje360/seed_extra.sql'),
      path.join(__dirname, '../demos/viaje360/seed_costos.sql'),
    ],
    checkTable: 'usuarios',
  },
  {
    name: 'condominio',
    port: 5201,
    db:   'demo_condominio',
    cwd:  path.join(__dirname, '../demos/condominio/backend'),
    entry: 'server.js',
    dist: path.join(__dirname, '../demos/condominio/frontend/dist'),
    sqlFiles: [
      path.join(__dirname, '../demos/condominio/database/schema.sql'),
      path.join(__dirname, '../demos/condominio/database/seed.sql'),
    ],
    checkTable: 'usuarios',
  },
  {
    name: 'odontologia',
    port: 5202,
    db:   'demo_odontologia',
    cwd:  path.join(__dirname, '../demos/odontologia/server'),
    entry: 'src/index.js',
    dist: path.join(__dirname, '../demos/odontologia/client/dist'),
    sqlFiles: [],
    checkTable: null,
  },
  {
    name: 'ventas',
    port: 5203,
    db:   'demo_ventas',
    cwd:  path.join(__dirname, '../demos/ventas/backend'),
    entry: 'server.js',
    dist: path.join(__dirname, '../demos/ventas/frontend/dist'),
    sqlFiles: [
      path.join(__dirname, '../demos/ventas/database/schema.sql'),
      path.join(__dirname, '../demos/ventas/database/update_v1_1.sql'),
      path.join(__dirname, '../demos/ventas/database/update_v1_2.sql'),
      path.join(__dirname, '../demos/ventas/database/update_v1_3.sql'),
    ],
    checkTable: 'usuarios',
  },
];

// ── PHP/Laravel demos ─────────────────────────────────────────────
const PHP_DEMOS = [
  { name: 'delivery',  port: 5210, db: 'demo_delivery',  cwd: path.join(__dirname, '../demos/php/delivery') },
  { name: 'celulares', port: 5211, db: 'demo_celulares', cwd: path.join(__dirname, '../demos/php/celulares') },
  { name: 'colegio',   port: 5212, db: 'demo_colegio',   cwd: path.join(__dirname, '../demos/php/colegio') },
  { name: 'farmacia',  port: 5213, db: 'demo_farmacia',  cwd: path.join(__dirname, '../demos/php/farmacia') },
];

const DEMOS = [...NODE_DEMOS, ...PHP_DEMOS];

// ── Seed a single Node.js demo database ──────────────────────────
async function seedDemo(demo) {
  const { host, port, user, password } = getDbConfig();

  let conn;
  try {
    conn = await mysql2.createConnection({ host, port: parseInt(port), user, password });
    await conn.execute(
      `CREATE DATABASE IF NOT EXISTS \`${demo.db}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`
    );
    await conn.changeUser({ database: demo.db });

    if (demo.checkTable) {
      try {
        const [rows] = await conn.execute(`SELECT COUNT(*) AS cnt FROM \`${demo.checkTable}\``);
        if (rows[0].cnt > 0) {
          console.log(`[${demo.name}] DB ya poblada (${rows[0].cnt} filas), omitiendo seed.`);
          return;
        }
      } catch (_) { /* tabla aún no existe */ }
    }

    for (const sqlFile of demo.sqlFiles) {
      if (!fs.existsSync(sqlFile)) { console.warn(`[${demo.name}] SQL no encontrado: ${sqlFile}`); continue; }
      let sql = fs.readFileSync(sqlFile, 'utf8');
      sql = sql.replace(/^CREATE\s+DATABASE\b.*?;\s*$/gim, '');
      sql = sql.replace(/^USE\s+`?\w+`?;\s*$/gim, '');
      const stmts = sql.split(/;\s*(?:\r?\n|$)/);
      for (const stmt of stmts) {
        const s = stmt.trim();
        if (!s || s.startsWith('--') || s.startsWith('/*')) continue;
        try { await conn.execute(s); } catch (_) { }
      }
      console.log(`[${demo.name}] SQL ejecutado: ${path.basename(sqlFile)}`);
    }
  } catch (err) {
    console.error(`[${demo.name}] seedDemo error: ${err.message}`);
  } finally {
    if (conn) conn.end().catch(() => {});
  }
}

// ── Spawn a Node.js demo ─────────────────────────────────────────
function spawnNodeDemo(demo) {
  if (!fs.existsSync(path.join(demo.cwd, demo.entry))) {
    console.warn(`[${demo.name}] entry no encontrado, omitiendo.`);
    return;
  }
  const { host, port, user, password } = getDbConfig();
  const env = {
    ...process.env,
    PORT:        String(demo.port),
    DB_NAME:     demo.db,
    DB_DATABASE: demo.db,
    DB_HOST:     host,
    DB_PORT:     String(port),
    DB_USER:     user,
    DB_PASSWORD: password,
    NODE_ENV:    'production',
  };
  const child = spawn('node', [demo.entry], { cwd: demo.cwd, env, stdio: 'pipe' });
  child.stdout.on('data', d => process.stdout.write(`[${demo.name}] ${d}`));
  child.stderr.on('data', d => process.stderr.write(`[${demo.name}] ${d}`));
  child.on('exit', code => {
    console.log(`[${demo.name}] proceso terminó (code=${code}), reiniciando en 5s...`);
    setTimeout(() => spawnNodeDemo(demo), 5000);
  });
}

// ── Spawn a PHP/Laravel demo ─────────────────────────────────────
function spawnPhpDemo(demo) {
  if (!fs.existsSync(path.join(demo.cwd, 'artisan'))) {
    console.warn(`[${demo.name}] artisan no encontrado, omitiendo.`);
    return;
  }
  const { host, port: dbPort, user, password } = getDbConfig();
  const env = {
    ...process.env,
    DEMO_PORT:   String(demo.port),
    DB_DATABASE: demo.db,
    DB_NAME:     demo.db,
    DB_HOST:     host,
    DB_PORT:     String(dbPort),
    DB_USER:     user,
    DB_USERNAME: user,
    DB_PASSWORD: password,
    NODE_ENV:    'production',
  };
  const child = spawn('bash', ['start.sh'], { cwd: demo.cwd, env, stdio: 'pipe' });
  child.stdout.on('data', d => process.stdout.write(`[${demo.name}] ${d}`));
  child.stderr.on('data', d => process.stderr.write(`[${demo.name}] ${d}`));
  child.on('exit', code => {
    console.log(`[${demo.name}] PHP proceso terminó (code=${code}), reiniciando en 10s...`);
    setTimeout(() => spawnPhpDemo(demo), 10000);
  });
}

// ── HTTP proxy for Node.js demos (/demos/[name]/api → /api) ──────
// Express strips the mount prefix from req.url, so prepend '/api'
function nodeProxyMiddleware(demo) {
  return (req, res) => {
    const targetPath = '/api' + req.url;
    const options = {
      hostname: '127.0.0.1',
      port:     demo.port,
      path:     targetPath,
      method:   req.method,
      headers:  { ...req.headers, host: `127.0.0.1:${demo.port}` },
    };
    const proxy = http.request(options, proxyRes => {
      res.writeHead(proxyRes.statusCode, proxyRes.headers);
      proxyRes.pipe(res, { end: true });
    });
    proxy.on('error', err => {
      console.error(`[${demo.name}] proxy error: ${err.message}`);
      if (!res.headersSent) res.status(502).json({ ok: false, msg: 'Demo temporalmente no disponible' });
    });
    req.pipe(proxy, { end: true });
  };
}

// ── HTTP proxy for PHP demos (/demos/[name]/* → /*) ──────────────
// Passes all requests to PHP artisan serve (HTML, CSS, JS, POST, etc.)
function phpProxyMiddleware(demo) {
  return (req, res) => {
    const options = {
      hostname: '127.0.0.1',
      port:     demo.port,
      path:     req.url || '/',
      method:   req.method,
      headers:  { ...req.headers, host: `127.0.0.1:${demo.port}` },
    };
    const proxy = http.request(options, proxyRes => {
      res.writeHead(proxyRes.statusCode, proxyRes.headers);
      proxyRes.pipe(res, { end: true });
    });
    proxy.on('error', err => {
      console.error(`[${demo.name}] PHP proxy error: ${err.message}`);
      if (!res.headersSent) res.status(502).send('<h2>Demo temporalmente no disponible</h2>');
    });
    req.pipe(proxy, { end: true });
  };
}

// ── Bootstrap all demos ───────────────────────────────────────────
async function initDemos() {
  console.log('=== Inicializando demos ===');
  for (const demo of NODE_DEMOS) {
    try { await seedDemo(demo); } catch (e) { console.error(`[${demo.name}] seed failed: ${e.message}`); }
    spawnNodeDemo(demo);
  }
  for (const demo of PHP_DEMOS) {
    spawnPhpDemo(demo);
  }
  console.log('=== Demos iniciados ===');
}

module.exports = { DEMOS, NODE_DEMOS, PHP_DEMOS, initDemos, nodeProxyMiddleware, phpProxyMiddleware };
