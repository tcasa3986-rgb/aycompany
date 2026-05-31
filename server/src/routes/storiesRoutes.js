const express = require('express');
const router  = express.Router();
const { generarYPublicarStory, servirImagenTemp } = require('../services/storiesService');
const { ejecutarHistoriasDiarias } = require('../services/storiesScheduler');
const authMiddleware = require('../middleware/auth');

// Servir imágenes temporales (pública — Instagram las descarga desde aquí)
router.get('/temp/:nombre', servirImagenTemp);

// Generar historias manualmente
router.post('/generar', authMiddleware, async (req, res) => {
    try {
        const { tipo = 'curiosidad', contexto = '' } = req.body;
        const tipos = ['curiosidad', 'educativo', 'ventas'];
        if (!tipos.includes(tipo)) {
            return res.status(400).json({ ok: false, error: `Tipo debe ser: ${tipos.join(', ')}` });
        }
        const resultado = await generarYPublicarStory(tipo, contexto);
        res.json({ ok: true, frames: resultado.frames, publicados: resultado.publicados });
    } catch (e) {
        res.status(500).json({ ok: false, error: e.message });
    }
});

// Ejecutar ciclo del día (misma función que el scheduler)
router.post('/ejecutar-hoy', authMiddleware, async (req, res) => {
    try {
        await ejecutarHistoriasDiarias();
        res.json({ ok: true, mensaje: 'Historias del día generadas y enviadas a Telegram' });
    } catch (e) {
        res.status(500).json({ ok: false, error: e.message });
    }
});

module.exports = router;
