const router = require('express').Router();
const auth   = require('../middlewares/auth');
const { generarReporte } = require('../services/seoReportScheduler');
const telegramService = require('../services/telegramService');

router.post('/reporte-telegram', auth, async (req, res) => {
    try {
        const result = await generarReporte();
        if (result.ok) res.json({ ok: true, msg: 'Reporte enviado a Telegram', ...result });
        else res.status(500).json({ ok: false, msg: result.error });
    } catch (e) {
        res.status(500).json({ ok: false, msg: e.message });
    }
});

// Llamado por GitHub Actions cuando publica un artículo nuevo
// Envía Telegram con botón para indexar en Google Search Console
router.post('/notificar-articulo', async (req, res) => {
    const secret = req.headers['x-seo-secret'];
    if (!secret || secret !== process.env.SEO_NOTIFY_SECRET) {
        return res.status(401).json({ ok: false, msg: 'No autorizado' });
    }

    const { titulo, slug, tipo = 'blog' } = req.body;
    if (!titulo || !slug) {
        return res.status(400).json({ ok: false, msg: 'titulo y slug requeridos' });
    }

    const url = `https://aicompanyco.com/blog/${slug}/`;
    const gscUrl = `https://search.google.com/search-console/inspect?resource_id=sc-domain:aicompanyco.com&id=${encodeURIComponent(url)}`;
    const emoji = tipo === 'noticia' ? '📰' : '✍️';

    const msg =
`${emoji} *Nuevo artículo publicado*

📌 *${titulo}*

🔗 \`${url}\`

👆 Presiona *Indexar en Google* para solicitar indexación inmediata en Search Console.`;

    const botones = [
        [
            { text: '🔍 Indexar en Google', url: gscUrl },
            { text: '🌐 Ver artículo', url }
        ]
    ];

    try {
        await telegramService.enviarConBotones(msg, botones);
        res.json({ ok: true, msg: 'Notificación enviada a Telegram' });
    } catch (e) {
        res.status(500).json({ ok: false, msg: e.message });
    }
});

module.exports = router;
