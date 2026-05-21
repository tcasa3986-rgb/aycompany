const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/empresaController');

router.get('/',     auth, auth.requireRol(['admin']), ctrl.listar);
router.get('/:id',  auth, auth.requireRol(['admin']), ctrl.obtener);
router.post('/',    auth, auth.requireRol(['admin']), ctrl.crear);
router.put('/:id',  auth, auth.requireRol(['admin']), ctrl.actualizar);
router.delete('/:id', auth, auth.requireRol(['admin']), ctrl.eliminar);

module.exports = router;
