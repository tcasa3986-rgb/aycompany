const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/configuracionController');

router.use(auth);
router.get('/',  ctrl.listar);
router.put('/',  ctrl.actualizar);

module.exports = router;
