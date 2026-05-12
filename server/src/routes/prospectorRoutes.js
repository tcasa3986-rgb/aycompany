const router = require('express').Router();
const ctrl   = require('../controllers/prospectorController');
const auth   = require('../middlewares/auth');

router.use(auth);
router.post('/buscar',       ctrl.buscar);
router.get('/config',        ctrl.getConfig);
router.put('/config',        ctrl.updateConfig);
router.post('/ejecutar',     ctrl.ejecutarAhora);
router.get('/keys',          ctrl.estadoKeys);

module.exports = router;
