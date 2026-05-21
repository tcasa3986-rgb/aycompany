const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/ticketsController');

router.use(auth);
router.use(auth.requireRol(['admin', 'soporte']));
router.get('/',       ctrl.listar);
router.put('/:id',    ctrl.responder);
router.delete('/:id', ctrl.eliminar);

module.exports = router;
