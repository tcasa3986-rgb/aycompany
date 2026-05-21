const express = require('express');
const router = express.Router();
const ctrl = require('../controllers/comunicaciones.controller');
const { verifyToken, requireRol } = require('../middlewares/auth.middleware');

router.use(verifyToken);
router.get('/anuncios', ctrl.getAnuncios);
router.post('/anuncios', requireRol('super_admin','administrador'), ctrl.createAnuncio);
router.put('/anuncios/:id', requireRol('super_admin','administrador'), ctrl.updateAnuncio);
router.get('/mensajes', ctrl.getMensajes);
router.post('/mensajes', ctrl.sendMensaje);
router.get('/asambleas', ctrl.getAsambleas);
router.post('/asambleas', requireRol('super_admin','administrador'), ctrl.createAsamblea);

module.exports = router;
