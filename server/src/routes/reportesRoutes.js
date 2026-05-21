const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/reportesController');

router.use(auth);
router.use(auth.requireRol(['admin']));
router.get('/clientes',  ctrl.clientes);
router.get('/licencias', ctrl.licencias);
router.get('/pagos',     ctrl.pagos);
router.get('/cartera',   ctrl.cartera);
router.get('/proyectos', ctrl.proyectos);
router.get('/tickets',   ctrl.tickets);

module.exports = router;
