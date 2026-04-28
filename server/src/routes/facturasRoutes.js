const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/facturasController');

router.use(auth);
router.get('/',       ctrl.listar);
router.delete('/:id', ctrl.eliminar);

module.exports = router;
