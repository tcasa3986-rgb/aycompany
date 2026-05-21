const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/analiticaController');

router.get('/predicciones', auth, ctrl.predicciones);
router.post('/insights-ia', auth, ctrl.insightsIA);

module.exports = router;
