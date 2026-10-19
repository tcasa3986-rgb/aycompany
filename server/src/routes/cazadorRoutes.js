const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/cazadorController');

router.use(auth);
router.use(auth.requireRol(['admin']));

router.post('/buscar',        ctrl.buscar);
router.get('/',               ctrl.listar);
router.get('/:id/preview', ctrl.preview);
router.put('/:id',            ctrl.actualizar);
router.post('/:id/generar',   ctrl.generarDemo);
router.post('/:id/demo',      ctrl.guardarDemo);

module.exports = router;
