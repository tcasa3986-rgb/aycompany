const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/reportesController');

router.get('/clientes',  auth, ctrl.clientes);
router.get('/licencias', auth, ctrl.licencias);
router.get('/pagos',     auth, ctrl.pagos);
router.get('/cartera',   auth, ctrl.cartera);
router.get('/proyectos', auth, ctrl.proyectos);
router.get('/tickets',   auth, ctrl.tickets);

module.exports = router;
