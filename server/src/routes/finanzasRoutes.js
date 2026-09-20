const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/finanzasController');

// Finanzas incluye la plata personal de Cristian: solo admin.
router.use(auth);
router.use(auth.requireRol(['admin']));

router.get('/resumen',            ctrl.resumen);

router.get('/movimientos',        ctrl.listarMovimientos);
router.post('/movimientos',       ctrl.crearMovimiento);
router.put('/movimientos/:id',    ctrl.actualizarMovimiento);
router.delete('/movimientos/:id', ctrl.eliminarMovimiento);

router.get('/deudas',             ctrl.listarDeudas);
router.post('/deudas',            ctrl.crearDeuda);
router.put('/deudas/:id',         ctrl.actualizarDeuda);
router.post('/deudas/:id/abonar', ctrl.abonarDeuda);
router.delete('/deudas/:id',      ctrl.eliminarDeuda);

router.get('/metas',              ctrl.listarMetas);
router.post('/metas',             ctrl.crearMeta);
router.put('/metas/:id',          ctrl.actualizarMeta);
router.post('/metas/:id/aportar', ctrl.aportarMeta);
router.delete('/metas/:id',       ctrl.eliminarMeta);

module.exports = router;
