const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/habitosController');

// Los hábitos son de Cristian, no de la empresa: solo admin, igual que Finanzas.
router.use(auth);
router.use(auth.requireRol(['admin']));

router.get('/',              ctrl.listar);
router.post('/',             ctrl.crear);
router.put('/:id',           ctrl.actualizar);
router.delete('/:id',        ctrl.eliminar);
router.post('/:id/marcar',   ctrl.marcar);
router.get('/:id/historial', ctrl.historial);

module.exports = router;
