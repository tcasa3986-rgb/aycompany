'use strict';
const { spawn }  = require('child_process');
const http       = require('http');
const path       = require('path');
const fs         = require('fs');
const crypto     = require('crypto');

const MOCK     = path.join(__dirname, '../demos/universal-mock.js');
const SPA_DIST = path.join(__dirname, '../demos/universal-spa/dist');

// ── Node.js demos (React frontend + mock universal) ────────────────────────────
const NODE_DEMOS = [
    { name:'viaje360',    port:5200, dist: path.join(__dirname,'../demos/viaje360/frontend/dist')    },
    { name:'condominio',  port:5201, dist: path.join(__dirname,'../demos/condominio/frontend/dist')  },
    { name:'odontologia', port:5202, dist: path.join(__dirname,'../demos/odontologia/client/dist')   },
    { name:'ventas',      port:5203, dist: path.join(__dirname,'../demos/ventas/frontend/dist')      },
    { name:'ferreteria',  port:5204, dist: path.join(__dirname,'../demos/ferreteria/frontend/dist')  },
    { name:'polleria',    port:5205, dist: path.join(__dirname,'../demos/polleria/frontend/dist')    },
    { name:'salon',       port:5206, dist: path.join(__dirname,'../demos/salon/frontend/dist')       },
    { name:'parqueo',     port:5207, dist: path.join(__dirname,'../demos/parqueo/frontend/dist')     },
    // prestamos: original requiere MySQL — usa SPA universal + mock API
    { name:'prestamos',   port:5208, dist: SPA_DIST },
];

// ── PHP/Laravel demos — ahora sirven HTML estático desde demos/static ──────────
const STATIC_DIR = path.join(__dirname, '../demos/static');
const PHP_DEMOS = [
    { name:'delivery',    port:5210, cwd: path.join(__dirname,'../demos/php/delivery'),    dist: path.join(STATIC_DIR,'delivery')    },
    { name:'celulares',   port:5211, cwd: path.join(__dirname,'../demos/php/celulares'),   dist: path.join(STATIC_DIR,'celulares')   },
    { name:'colegio',     port:5212, cwd: path.join(__dirname,'../demos/php/colegio'),     dist: path.join(STATIC_DIR,'colegio')     },
    { name:'farmacia',    port:5213, cwd: path.join(__dirname,'../demos/php/farmacia'),    dist: path.join(STATIC_DIR,'farmacia')    },
    { name:'panaderia',   port:5214, cwd: path.join(__dirname,'../demos/php/panaderia'),   dist: path.join(STATIC_DIR,'panaderia')   },
    { name:'restaurante', port:5215, cwd: path.join(__dirname,'../demos/php/restaurante'), dist: path.join(STATIC_DIR,'restaurante') },
    { name:'citas',       port:5216, cwd: path.join(__dirname,'../demos/php/citas'),       dist: path.join(STATIC_DIR,'citas')       },
    { name:'hospedaje',   port:5217, cwd: path.join(__dirname,'../demos/php/hospedaje'),   dist: path.join(STATIC_DIR,'hospedaje')   },
    { name:'inventario',  port:5218, cwd: path.join(__dirname,'../demos/php/inventario'),  dist: path.join(STATIC_DIR,'inventario')  },
    { name:'laboratorio', port:5219, cwd: path.join(__dirname,'../demos/php/laboratorio'), dist: path.join(STATIC_DIR,'laboratorio') },
    { name:'cotizacion',  port:5220, cwd: path.join(__dirname,'../demos/php/cotizacion'),  dist: path.join(STATIC_DIR,'cotizacion')  },
    { name:'botica',      port:5221, cwd: path.join(__dirname,'../demos/php/botica'),      dist: path.join(STATIC_DIR,'botica')      },
];

const DEMOS = [...NODE_DEMOS, ...PHP_DEMOS];

// ── Spawn Node.js demo con mock universal ──────────────────────────────────────
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

// ── Spawn PHP demo con SQLite (sin MySQL) ──────────────────────────────────────
function spawnPhpDemo(demo) {
    const startSh = path.join(demo.cwd, 'start.sh');
    if (!fs.existsSync(startSh)) {
        console.warn(`[${demo.name}] start.sh no encontrado, omitiendo.`);
        return;
    }
    const sqliteFile = `/tmp/demo_${demo.name}.sqlite`;
    try { fs.unlinkSync(sqliteFile); } catch (_) {}

    // Escribir .env con SQLite directamente — más confiable que depender de
    // que las env vars del proceso ganen sobre el .env generado por start.sh
    const envFile = path.join(demo.cwd, '.env');
    const appKey  = `base64:${crypto.randomBytes(32).toString('base64')}`;
    fs.writeFileSync(envFile, [
        `APP_NAME="${demo.name}"`,
        `APP_ENV=production`,
        `APP_KEY=${appKey}`,
        `APP_DEBUG=false`,
        `APP_URL=https://mi-plataforma-production.up.railway.app/demos/${demo.name}`,
        `LOG_CHANNEL=stderr`,
        `DB_CONNECTION=sqlite`,
        `DB_DATABASE=${sqliteFile}`,
        `CACHE_DRIVER=file`,
        `SESSION_DRIVER=file`,
        `SESSION_COOKIE=${demo.name}_session`,
        `SESSION_SECURE_COOKIE=false`,
        `SESSION_DOMAIN=`,
        `QUEUE_CONNECTION=sync`,
        `FILESYSTEM_DISK=local`,
        `BROADCAST_CONNECTION=log`,
    ].join('\n') + '\n');

    // Borrar cache de Laravel para que lea el .env recién escrito
    for (const cache of ['bootstrap/cache/config.php', 'bootstrap/cache/routes-v7.php', 'bootstrap/cache/services.php']) {
        try { fs.unlinkSync(path.join(demo.cwd, cache)); } catch (_) {}
    }

    const env = {
        ...process.env,
        DEMO_PORT: String(demo.port),
    };
    const child = spawn('bash', ['start.sh'], { cwd: demo.cwd, env, stdio: 'pipe' });
    child.stdout.on('data', d => process.stdout.write(`[${demo.name}] ${d}`));
    child.stderr.on('data', d => process.stderr.write(`[${demo.name}] ${d}`));
    child.on('exit', code => {
        console.log(`[${demo.name}] PHP reiniciando en 10s... (exit ${code})`);
        setTimeout(() => spawnPhpDemo(demo), 10000);
    });
}

// ── Proxy Node.js: /demos/[name]/api → puerto interno ─────────────────────────
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

// ── Proxy PHP: /demos/[name]/* → php artisan serve ────────────────────────────
function phpProxyMiddleware(demo) {
    const internalBase = `http://127.0.0.1:${demo.port}`;
    const publicBase   = `/demos/${demo.name}`;

    function rewriteUrl(str) {
        // Reemplaza http://127.0.0.1:PORT/ruta → /demos/nombre/ruta
        str = str.replace(
            new RegExp(`https?://127\\.0\\.0\\.1:${demo.port}(/[^'"\\s>]*)?`, 'g'),
            (_, p) => `${publicBase}${p || '/'}`
        );
        return str;
    }

    return (req, res) => {
        const opts = {
            hostname: '127.0.0.1',
            port:     demo.port,
            path:     req.url || '/',
            method:   req.method,
            headers:  {
                ...req.headers,
                host:                `127.0.0.1:${demo.port}`,
                'x-forwarded-proto': 'https',
                'x-forwarded-host':  req.headers['host'] || 'mi-plataforma-production.up.railway.app',
            },
        };
        const proxy = http.request(opts, r => {
            const headers = { ...r.headers };

            // Reescribir header Location
            if (headers['location']) {
                let loc = rewriteUrl(headers['location']);
                if (loc.startsWith('/') && !loc.startsWith(publicBase)) {
                    loc = `${publicBase}${loc}`;
                }
                headers['location'] = loc;
            }

            const contentType = (headers['content-type'] || '').toLowerCase();
            const isHtml = contentType.includes('text/html') || contentType.includes('text/plain');

            if (isHtml) {
                // Bufferizar y reescribir todas las URLs internas en el body HTML
                delete headers['content-length'];
                const chunks = [];
                r.on('data', chunk => chunks.push(chunk));
                r.on('end', () => {
                    let body = Buffer.concat(chunks).toString('utf8');
                    body = rewriteUrl(body);
                    // Rutas relativas en atributos action/href/src que empiecen en /
                    // Excluye //cdn (protocol-relative) y rutas ya prefijadas
                    body = body.replace(
                        /((?:action|href|src)=["'])\/(?!\/|demos\/)(.*?)(["'])/g,
                        (_, pre, path, post) => `${pre}${publicBase}/${path}${post}`
                    );
                    headers['content-length'] = Buffer.byteLength(body, 'utf8').toString();
                    res.writeHead(r.statusCode, headers);
                    res.end(body);
                });
            } else {
                res.writeHead(r.statusCode, headers);
                r.pipe(res, { end:true });
            }
        });
        proxy.on('error', err => {
            console.error(`[${demo.name}] PHP proxy error: ${err.message}`);
            if (!res.headersSent) {
                res.status(502).send(`
                    <html><body style="font-family:sans-serif;padding:2rem;background:#0f172a;color:#e2e8f0">
                    <h2>Demo cargando...</h2>
                    <p>El sistema está iniciando. Espera 10 segundos y recarga la página.</p>
                    <button onclick="location.reload()" style="padding:.5rem 1rem;background:#6366f1;color:#fff;border:none;border-radius:6px;cursor:pointer">
                        Recargar
                    </button></body></html>`);
            }
        });
        req.pipe(proxy, { end:true });
    };
}

// ── Inicializar todos los demos ────────────────────────────────────────────────
async function initDemos() {
    console.log('=== Iniciando demos ===');
    for (const demo of NODE_DEMOS) spawnNodeDemo(demo);
    // Solo lanzar PHP para demos sin HTML estático
    for (const demo of PHP_DEMOS) { if (!demo.dist) spawnPhpDemo(demo); }
    console.log('=== Demos en marcha ===');
}

module.exports = { DEMOS, NODE_DEMOS, PHP_DEMOS, initDemos, nodeProxyMiddleware, phpProxyMiddleware };
