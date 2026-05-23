const path     = require('path');
const fs       = require('fs');
const { investigarNegocio, buscarNegociosCategoria } = require('../services/propuestasScraper');
const { generarPropuesta }     = require('../services/propuestasEngine');
const { actualizarConfig, getConfig, ejecutarProspeccionDiaria, setCacheRef } = require('../services/prospectorScheduler');

// Cache en memoria: key → { info, html, demos, analisis, pdfPath?, auto? }
const cache = {};

// Conectar el cache con el scheduler para que pueda escribir propuestas auto
setCacheRef(cache);

// POST /api/prospector/investigar
exports.investigar = async (req, res) => {
    const { nombre, ciudad, tipo, urlDirecta } = req.body;
    if (!nombre || !ciudad) return res.status(400).json({ error: 'Nombre y ciudad son requeridos' });

    try {
        const info = await investigarNegocio({ nombre, ciudad, tipo: tipo || '', urlDirecta: urlDirecta || null });
        const { html, demos, analisis } = await generarPropuesta(info);

        const key = `${Date.now()}`;
        cache[key] = { info, html, demos, analisis };

        res.json({
            ok:       true,
            key,
            nombre:   info.nombre,
            ciudad:   info.ciudad,
            sitioUrl: info.sitioUrl,
            telefono: info.telefono,
            rating:   info.rating,
            direccion: info.direccion,
            tieneMaps: info.tieneMaps,
            analisis,
            demos: demos.map(d => ({ id: d.id, label: d.label, url: d.url }))
        });
    } catch (e) {
        console.error('[Propuestas]', e.message);
        res.status(500).json({ error: e.message });
    }
};

// POST /api/prospector/buscar-categoria — búsqueda automática por categoría
exports.buscarCategoria = async (req, res) => {
    const { categoria, ciudad, maxResultados = 5 } = req.body;
    if (!categoria || !ciudad) return res.status(400).json({ error: 'Categoría y ciudad son requeridas' });
    if (maxResultados > 20) return res.status(400).json({ error: 'Máximo 20 resultados por búsqueda' });

    try {
        const negocios = await buscarNegociosCategoria({ categoria, ciudad, maxResultados });

        const resultados = [];
        for (const info of negocios) {
            const { html, demos, analisis } = await generarPropuesta(info);
            const key = `auto_${Date.now()}_${resultados.length}`;
            cache[key] = { info, html, demos, analisis, auto: true };
            resultados.push({
                key,
                nombre:   info.nombre,
                ciudad:   info.ciudad,
                sitioUrl: info.sitioUrl,
                telefono: info.telefono,
                rating:   info.rating,
                tieneMaps: info.tieneMaps,
                analisis,
                demos: demos.map(d => ({ id: d.id, label: d.label, url: d.url }))
            });
        }

        res.json({ ok: true, total: resultados.length, categoria, ciudad, data: resultados });
    } catch (e) {
        console.error('[BuscarCategoría]', e.message);
        res.status(500).json({ error: e.message });
    }
};

// GET /api/prospector/:key/html
exports.getHtml = (req, res) => {
    const entry = cache[req.params.key];
    if (!entry) return res.status(404).send('Propuesta no encontrada');
    res.setHeader('Content-Type', 'text/html');
    res.send(entry.html);
};

// POST /api/prospector/:key/pdf
exports.generarPdf = async (req, res) => {
    const entry = cache[req.params.key];
    if (!entry) return res.status(404).json({ error: 'Propuesta no encontrada' });

    try {
        const { chromium } = require('playwright');
        const outputDir = path.join(__dirname, '../../../output');
        if (!fs.existsSync(outputDir)) fs.mkdirSync(outputDir, { recursive: true });

        const nombre   = entry.info.nombre.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '');
        const fileName = `propuesta_${nombre}_${req.params.key}.pdf`;
        const filePath = path.join(outputDir, fileName);

        const browser = await chromium.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'] });
        const page    = await browser.newPage();
        await page.setContent(entry.html, { waitUntil: 'domcontentloaded' });
        await page.pdf({ path: filePath, format: 'A4', printBackground: true });
        await browser.close();

        entry.pdfPath = filePath;
        res.json({ ok: true, fileName });
    } catch (e) {
        res.status(500).json({ error: e.message });
    }
};

// POST /api/prospector/:key/email
exports.enviarEmail = async (req, res) => {
    const entry = cache[req.params.key];
    if (!entry) return res.status(404).json({ error: 'Propuesta no encontrada' });

    const { destinatario, asunto } = req.body;
    if (!destinatario) return res.status(400).json({ error: 'Email del destinatario requerido' });

    try {
        const nodemailer = require('nodemailer');
        const transporter = nodemailer.createTransport({
            service: 'gmail',
            auth: { user: process.env.GMAIL_USER, pass: process.env.GMAIL_PASS }
        });

        await transporter.sendMail({
            from:    `AI Company CO <${process.env.GMAIL_USER}>`,
            to:      destinatario,
            subject: asunto || `Propuesta digital para ${entry.info.nombre}`,
            html:    entry.html,
            attachments: entry.pdfPath && fs.existsSync(entry.pdfPath) ? [{
                filename: `propuesta_${entry.info.nombre}.pdf`,
                path:     entry.pdfPath
            }] : []
        });
        res.json({ ok: true, msg: `Email enviado a ${destinatario}` });
    } catch (e) {
        res.status(500).json({ error: e.message });
    }
};

// GET /api/prospector/historial
exports.historial = (req, res) => {
    const lista = Object.entries(cache).map(([key, entry]) => ({
        key,
        nombre:   entry.info.nombre,
        ciudad:   entry.info.ciudad,
        sitioUrl: entry.info.sitioUrl,
        telefono: entry.info.telefono,
        rating:   entry.info.rating,
        tienePDF: !!entry.pdfPath,
        auto:     !!entry.auto
    })).reverse();
    res.json({ ok: true, data: lista });
};

// GET /api/prospector/config
exports.getConfig = (req, res) => res.json(getConfig());

// PUT /api/prospector/config
exports.updateConfig = (req, res) => {
    actualizarConfig(req.body);
    res.json({ ok: true, config: getConfig() });
};

// POST /api/prospector/ejecutar — dispara una ronda manual ahora
exports.ejecutarAhora = (req, res) => {
    res.json({ ok: true, mensaje: 'Búsqueda automática iniciada en segundo plano' });
    ejecutarProspeccionDiaria().catch(e => console.error('[Auto manual]', e.message));
};

// GET /api/prospector/keys
exports.estadoKeys = (req, res) => res.json({
    gemini:     !!process.env.GEMINI_API_KEY,
    playwright: true
});

// Stub legacy
exports.buscar = (req, res) => res.status(410).json({ error: 'Usa POST /api/prospector/investigar o /buscar-categoria' });
