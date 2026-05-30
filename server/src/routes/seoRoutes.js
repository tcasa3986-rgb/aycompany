const router = require('express').Router();
const auth   = require('../middlewares/auth');
const { generarReporte } = require('../services/seoReportScheduler');

router.post('/reporte-telegram', auth, async (req, res) => {
    try {
        const result = await generarReporte();
        if (result.ok) res.json({ ok: true, msg: 'Reporte enviado a Telegram', ...result });
        else res.status(500).json({ ok: false, msg: result.error });
    } catch (e) {
        res.status(500).json({ ok: false, msg: e.message });
    }
});

module.exports = router;
