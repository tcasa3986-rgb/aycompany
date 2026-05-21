const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/contratosController');

router.use(auth);
router.get('/',          ctrl.listar);
router.get('/:id',       ctrl.obtener);
router.get('/:id/pdf',   ctrl.descargarPDF);
router.post('/',         ctrl.crear);
router.put('/:id',       ctrl.actualizar);
router.delete('/:id',    ctrl.eliminar);

module.exports = router;
