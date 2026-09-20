const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/hoyController');

router.use(auth);
router.use(auth.requireRol(['admin']));
router.get('/', ctrl.resumen);

module.exports = router;
