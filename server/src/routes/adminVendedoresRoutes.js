const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/adminVendedoresController');

router.get('/',           auth, ctrl.listar);
router.patch('/:id/activo', auth, ctrl.toggleActivo);
router.delete('/:id',     auth, ctrl.eliminar);

module.exports = router;
