const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/pagosController');

// Públicas — Mercado Pago (sin autenticación)
router.get( '/mp/info/:license_key',            ctrl.mpInfoLicencia);
router.post('/mp/crear/:license_key',           ctrl.mpCrearPago);
router.post('/mp/webhook',                      ctrl.mpWebhook);
router.post('/mp/suscripcion/:license_key',     ctrl.mpCrearSuscripcion);
router.post('/mp/cancelar/:license_key',        ctrl.mpCancelarSuscripcion);
router.get( '/mp/validar/:license_key',         ctrl.validarLicencia);

// Protegidas — solo admin
router.use(auth);
router.use(auth.requireRol(['admin']));
router.get('/',       ctrl.listar);
router.post('/',      ctrl.crear);
router.delete('/:id', ctrl.eliminar);

module.exports = router;
