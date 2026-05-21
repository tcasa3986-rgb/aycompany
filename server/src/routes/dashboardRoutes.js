const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/dashboardController');
router.use(auth);
router.use(auth.requireRol(['admin']));
router.get('/stats', ctrl.stats);
module.exports = router;
