const express  = require('express');
const router   = express.Router();
const auth     = require('../middlewares/auth');
const { generarYPublicar, servirImagenTemp, generarContenidoClaude, renderizarCarrusel } = require('../services/carouselService');
const path = require('path');
const fs   = require('fs');

// Servir imagen temporal (pública — Instagram la descarga desde aquí)
router.get('/temp/:nombre', servirImagenTemp);

// Generar y publicar carrusel manualmente desde el panel
router.post('/generar', auth, async (req, res) => {
    try {
        const { tipo = 'educativo', contexto = '' } = req.body;
        if (!['educativo', 'ventas'].includes(tipo)) {
            return res.status(400).json({ ok: false, msg: 'tipo debe ser educativo o ventas' });
        }
        const resultado = await generarYPublicar(tipo, contexto);
        res.json({ ok: true, slides: resultado.slides, postId: resultado.postId });
    } catch (e) {
        console.error('[CarouselRoute] Error:', e.message);
        res.status(500).json({ ok: false, msg: e.message });
    }
});

// Solo generar preview (sin publicar) — para ver cómo quedaría
router.post('/preview', auth, async (req, res) => {
    try {
        const { tipo = 'educativo', contexto = '' } = req.body;
        const contenido = await generarContenidoClaude(tipo, contexto);
        res.json({ ok: true, contenido });
    } catch (e) {
        res.status(500).json({ ok: false, msg: e.message });
    }
});

module.exports = router;
