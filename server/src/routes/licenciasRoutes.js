const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/licenciasController');

// Público — lo llaman los sistemas de los clientes
router.post('/validar', ctrl.validar);

router.use(auth);
router.get('/',              ctrl.listar);
router.post('/',             ctrl.crear);
router.put('/:id/toggle',   ctrl.toggle);
router.put('/:id/renovar',  ctrl.renovar);
router.delete('/:id',       ctrl.eliminar);
module.exports = router;
