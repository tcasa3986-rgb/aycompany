'use strict';
const { spawn } = require('child_process');
const http      = require('http');
const path      = require('path');
const fs        = require('fs');

const MOCK = path.join(__dirname, '../../demos/universal-mock.js');

// ── Node.js demos ─────────────────────────────────────────────
const NODE_DEMOS = [
    { name:'viaje360',   port:5200, dist: path.join(__dirname,'../demos/viaje360/frontend/dist')    },
    { name:'condominio', port:5201, dist: path.join(__dirname,'../demos/condominio/frontend/dist')  },
    { name:'odontologia',port:5202, dist: path.join(__dirname,'../demos/odontologia/client/dist')   },
    { name:'ventas',     port:5203, dist: path.join(__dirname,'../demos/ventas/frontend/dist')      },
    { name:'ferreteria', port:5204, dist: path.join(__dirname,'../demos/ferreteria/frontend/dist')  },
    { name:'polleria',   port:5205, dist: path.join(__dirname,'../demos/polleria/frontend/dist')    },
    { name:'salon',      port:5206, dist: path.join(__dirname,'../demos/salon/frontend/dist')       },
    { name:'parqueo',    port:5207, dist: path.join(__dirname,'../demos/parqueo/frontend/dist')     },
    { name:'prestamos',  port:5208, dist: null },
];

// ── PHP demos ─────────────────────────────────────────────────
const PHP_DEMOS = [
    { name:'delivery',    port:5210, cwd: path.join(__dirname,'../demos/php/delivery')    },
    { name:'celulares',   port:5211, cwd: path.join(__dirname,'../demos/php/celulares')   },
    { name:'colegio',     port:5212, cwd: path.join(__dirname,'../demos/php/colegio')     },
    { name:'farmacia',    port:5213, cwd: path.join(__dirname,'../demos/php/farmacia')    },
    { name:'panaderia',   port:5214, cwd: path.join(__dirname,'../demos/php/panaderia')   },
    { name:'restaurante', port:5215, cwd: path.join(__dirname,'../demos/php/restaurante') },
    { name:'citas',       port:5216, cwd: path.join(__dirname,'../demos/php/citas')       },
    { name:'hospedaje',   port:5217, cwd: path.join(__dirname,'../demos/php/hospedaje')   },
    { name:'inventario',  port:5218, cwd: path.join(__dirname,'../demos/php/inventario')  },
    { name:'laboratorio', port:5219, cwd: path.join(__dirname,'../demos/php/laboratorio') },
    { name:'cotizacion',  port:5220, cwd: path.join(__dirname,'../demos/php/cotizacion')  },
];

const DEMOS = [...NODE_DEMOS, ...PHP_DEMOS];

// ── Spawn Node.js demo con mock universal ──────────────────────
function spawnNodeDemo(demo) {
    if (!fs.existsSync(MOCK)) {
        console.warn(`[${demo.name}] universal-mock.js no encontrado, omitiendo.`);
        return;
    }
    const child = spawn('node', [MOCK], {
        env: { ...process.env, PORT: String(demo.port), DEMO_NAME: demo.name, NODE_ENV: 'production' },
        stdio: 'pipe'
    });
    child.stdout.on('data', d => process.stdout.write(`[${demo.name}] ${d}`));
    child.stderr.on('data', d => process.stderr.write(`[${demo.name}] ${d}`));
    child.on('exit', code => {
        console.log(`[${demo.name}] reiniciando en 5s... (exit ${code})`);
        setTimeout(() => spawnNodeDemo(demo), 5000);
    });
}

// ── Spawn PHP demo con SQLite (sin MySQL) ──────────────────────
function spawnPhpDemo(demo) {
    const startSh = path.join(demo.cwd, 'start.sh');
    if (!fs.existsSync(startSh)) {
        console.warn(`[${demo.name}] start.sh no encontrado, omitiendo.`);
        return;
    }
    const sqliteFile = `/tmp/demo_${demo.name}.sqlite`;
    // Borrar BD anterior para que siempre empiece limpia
    try { fs.unlinkSync(sqliteFile); } catch (_) {}

    const env = {
        ...process.env,
        DEMO_PORT:      String(demo.port),
        APP_ENV:        'production',
        APP_DEBUG:      'false',
        APP_URL:        `https://mi-plataforma-production.up.railway.app/demos/${demo.name}`,
        DB_CONNECTION:  'sqlite',
        DB_DATABASE:    sqliteFile,
        CACHE_DRIVER:   'array',
        SESSION_DRIVER: 'array',
        QUEUE_CONNECTION:'sync',
    };
    const child = spawn('bash', ['start.sh'], { cwd: demo.cwd, env, stdio: 'pipe' });
    child.stdout.on('data', d => process.stdout.write(`[${demo.name}] ${d}`));
    child.stderr.on('data', d => process.stderr.write(`[${demo.name}] ${d}`));
    child.on('exit', code => {
        console.log(`[${demo.name}] PHP reiniciando en 10s... (exit ${code})`);
        setTimeout(() => spawnPhpDemo(demo), 10000);
    });
}

// ── Proxy: /demos/[name]/api → puerto interno ──────────────────
function nodeProxyMiddleware(demo) {
    return (req, res) => {
        const opts = {
            hostname: '127.0.0.1',
            port:     demo.port,
            path:     '/api' + req.url,
            method:   req.method,
            headers:  { ...req.headers, host: `127.0.0.1:${demo.port}` },
        };
        delete opts.headers['origin'];
        delete opts.headers['referer'];
        const proxy = http.request(opts, r => { res.writeHead(r.statusCode, r.headers); r.pipe(res, { end:true }); });
        proxy.on('error', err => {
            console.error(`[${demo.name}] proxy error: ${err.message}`);
            if (!res.headersSent) res.status(502).json({ ok:false, msg:'Demo temporalmente no disponible' });
        });
        req.pipe(proxy, { end:true });
    };
}

// ── Proxy: /demos/[name]/* → PHP artisan serve ─────────────────
function phpProxyMiddleware(demo) {
    return (req, res) => {
        const opts = {
            hostname: '127.0.0.1',
            port:     demo.port,
            path:     req.url || '/',
            method:   req.method,
            headers:  { ...req.headers, host: `127.0.0.1:${demo.port}` },
        };
        const proxy = http.request(opts, r => {
            const headers = { ...r.headers };
            if (headers['location']) {
                headers['location'] = headers['location']
                    .replace(/^https?:\/\/127\.0\.0\.1:\d+(\/.*)?$/, `/demos/${demo.name}$1`);
            }
            res.writeHead(r.statusCode, headers);
            r.pipe(res, { end:true });
        });
        proxy.on('error', err => {
            console.error(`[${demo.name}] PHP proxy error: ${err.message}`);
            if (!res.headersSent) res.status(502).send('<h2>Demo temporalmente no disponible</h2>');
        });
        req.pipe(proxy, { end:true });
    };
}

// ── Inicializar todos los demos ────────────────────────────────
async function initDemos() {
    console.log('=== Iniciando demos (modo mock) ===');
    for (const demo of NODE_DEMOS) spawnNodeDemo(demo);
    for (const demo of PHP_DEMOS)  spawnPhpDemo(demo);
    console.log('=== Demos en marcha ===');
}

module.exports = { DEMOS, NODE_DEMOS, PHP_DEMOS, initDemos, nodeProxyMiddleware, phpProxyMiddleware };
