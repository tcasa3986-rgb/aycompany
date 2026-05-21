const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/adminVendedoresController');

router.use(auth);
router.use(auth.requireRol(['admin']));
router.get('/',             ctrl.listar);
router.patch('/:id/activo', ctrl.toggleActivo);
router.delete('/:id',       ctrl.eliminar);

module.exports = router;
