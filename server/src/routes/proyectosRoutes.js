const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/proyectosController');

router.use(auth);
router.use(auth.requireRol(['admin', 'soporte']));
router.get('/',                        ctrl.listar);
router.get('/:id',                     ctrl.obtener);
router.post('/',                       ctrl.crear);
router.put('/:id',                     ctrl.actualizar);
router.delete('/:id',                  ctrl.eliminar);
router.post('/:id/tareas',             ctrl.crearTarea);
router.put('/:id/tareas/:tarea_id',    ctrl.actualizarTarea);
router.delete('/:id/tareas/:tarea_id', ctrl.eliminarTarea);

module.exports = router;
