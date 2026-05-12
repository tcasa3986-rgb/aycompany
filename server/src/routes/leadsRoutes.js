const router = require('express').Router();
const ctrl   = require('../controllers/leadsController');
const auth   = require('../middlewares/auth');

router.get('/',          auth, ctrl.listar);
router.post('/',         auth, ctrl.crear);
router.put('/:id',       auth, ctrl.actualizar);
router.delete('/:id',    auth, ctrl.eliminar);
router.get('/stats',     auth, ctrl.stats);
router.get('/:id/actividad',  auth, ctrl.actividad);
router.post('/:id/procesar',  auth, ctrl.procesarManual);

module.exports = router;
