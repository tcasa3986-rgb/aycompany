const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/pagosController');
router.use(auth);
router.get('/',       ctrl.listar);
router.post('/',      ctrl.crear);
router.delete('/:id', ctrl.eliminar);
module.exports = router;
