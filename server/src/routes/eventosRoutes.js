const router = require('express').Router();
const ctrl   = require('../controllers/eventosController');
const auth   = require('../middlewares/auth');

router.use(auth);
router.use(auth.requireRol(['admin', 'soporte']));
router.get('/',       ctrl.listar);
router.post('/',      ctrl.crear);
router.put('/:id',    ctrl.actualizar);
router.delete('/:id', ctrl.eliminar);
module.exports = router;
