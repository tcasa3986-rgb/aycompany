const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/fichaClienteController');

router.use(auth);
router.get('/:id/ficha', ctrl.ficha);

module.exports = router;
