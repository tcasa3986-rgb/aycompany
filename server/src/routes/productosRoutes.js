const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/productosController');

router.use(auth);
router.use(auth.requireRol(['admin']));
router.get('/',       ctrl.listar);
router.post('/',      ctrl.crear);
router.put('/:id',    ctrl.actualizar);
router.delete('/:id', ctrl.eliminar);
module.exports = router;
