const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/analiticaController');

router.use(auth);
router.use(auth.requireRol(['admin']));
router.get('/predicciones', ctrl.predicciones);
router.post('/insights-ia', ctrl.insightsIA);

module.exports = router;
