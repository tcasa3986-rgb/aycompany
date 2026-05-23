'use strict';
const { spawn } = require('child_process');
const http      = require('http');
const path      = require('path');
const fs        = require('fs');

const MOCK     = path.join(__dirname, '../demos/universal-mock.js');
const SPA_DIST = path.join(__dirname, '../demos/universal-spa/dist');

// ── Todos los demos usan el mock universal ─────────────────────────────────────
const NODE_DEMOS = [
    { name:'viaje360',    port:5200, dist: path.join(__dirname,'../demos/viaje360/frontend/dist')    },
    { name:'condominio',  port:5201, dist: path.join(__dirname,'../demos/condominio/frontend/dist')  },
    { name:'odontologia', port:5202, dist: path.join(__dirname,'../demos/odontologia/client/dist')   },
    { name:'ventas',      port:5203, dist: path.join(__dirname,'../demos/ventas/frontend/dist')      },
    { name:'ferreteria',  port:5204, dist: path.join(__dirname,'../demos/ferreteria/frontend/dist')  },
    { name:'polleria',    port:5205, dist: path.join(__dirname,'../demos/polleria/frontend/dist')    },
    { name:'salon',       port:5206, dist: path.join(__dirname,'../demos/salon/frontend/dist')       },
    { name:'parqueo',     port:5207, dist: path.join(__dirname,'../demos/parqueo/frontend/dist')     },
    // Antes EJS/dist:null — ahora usa la SPA universal
    { name:'prestamos',   port:5208, dist: SPA_DIST },
    // Ex-PHP demos — convertidos a React SPA universal
    { name:'delivery',    port:5210, dist: SPA_DIST },
    { name:'celulares',   port:5211, dist: SPA_DIST },
    { name:'colegio',     port:5212, dist: SPA_DIST },
    { name:'farmacia',    port:5213, dist: SPA_DIST },
    { name:'panaderia',   port:5214, dist: SPA_DIST },
    { name:'restaurante', port:5215, dist: SPA_DIST },
    { name:'citas',       port:5216, dist: SPA_DIST },
    { name:'hospedaje',   port:5217, dist: SPA_DIST },
    { name:'inventario',  port:5218, dist: SPA_DIST },
    { name:'laboratorio', port:5219, dist: SPA_DIST },
    { name:'cotizacion',  port:5220, dist: SPA_DIST },
];

const PHP_DEMOS = []; // Todos migrados a Node.js + SPA universal
const DEMOS     = NODE_DEMOS;

// ── Spawn demo con mock universal ──────────────────────────────────────────────
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

// ── Proxy: /demos/[name]/api → puerto interno ──────────────────────────────────
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

// phpProxyMiddleware se mantiene para compatibilidad pero PHP_DEMOS está vacío
function phpProxyMiddleware(demo) {
    return nodeProxyMiddleware(demo);
}

// ── Inicializar todos los demos ────────────────────────────────────────────────
async function initDemos() {
    console.log('=== Iniciando demos (mock universal) ===');
    for (const demo of NODE_DEMOS) spawnNodeDemo(demo);
    console.log(`=== ${NODE_DEMOS.length} demos iniciados ===`);
}

module.exports = { DEMOS, NODE_DEMOS, PHP_DEMOS, initDemos, nodeProxyMiddleware, phpProxyMiddleware };
