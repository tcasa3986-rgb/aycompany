const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/hoyController');

router.use(auth);
router.use(auth.requireRol(['admin']));
router.get('/', ctrl.resumen);

// Pendientes que Cristian se programa, y posponer los avisos de fondo.
router.get('/pendientes',           ctrl.listarPendientes);
router.post('/pendientes',          ctrl.crearPendiente);
router.post('/pendientes/:id/hecho',ctrl.completarPendiente);
router.delete('/pendientes/:id',    ctrl.eliminarPendiente);
router.post('/posponer',            ctrl.posponer);

module.exports = router;
