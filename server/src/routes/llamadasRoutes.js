const router = require('express').Router();
const auth   = require('../middlewares/auth');
const { llamar, procesarWebhook } = require('../services/llamadasService');

// Webhook de Vapi (sin auth — llamado por Vapi)
router.post('/webhook', async (req, res) => {
    res.sendStatus(200);
    await procesarWebhook(req.body).catch(e => console.error('webhook error:', e.message));
});

// Lanzar llamada manual desde el panel
router.post('/llamar', auth, async (req, res) => {
    try {
        const { telefono, nombre, ciudad } = req.body;
        if (!telefono) return res.status(400).json({ error: 'Teléfono requerido' });

        const resultado = await llamar({
            telefono,
            infoNegocio: { nombre: nombre || 'Negocio', ciudad: ciudad || '' }
        });
        res.json({ ok: true, ...resultado });
    } catch (err) {
        res.status(500).json({ ok: false, error: err.message });
    }
});

// Estado del sistema de llamadas
router.get('/estado', auth, (req, res) => {
    res.json({
        ok: true,
        configurado: !!(process.env.VAPI_API_KEY && process.env.VAPI_PHONE_ID),
        tieneApiKey:  !!process.env.VAPI_API_KEY,
        tienePhoneId: !!process.env.VAPI_PHONE_ID,
    });
});

module.exports = router;
