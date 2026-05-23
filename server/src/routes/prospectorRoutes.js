const router = require('express').Router();
const ctrl   = require('../controllers/prospectorController');
const auth   = require('../middlewares/auth');

router.use(auth);

router.post('/investigar',        ctrl.investigar);
router.post('/buscar-categoria',  ctrl.buscarCategoria);
router.get('/historial',          ctrl.historial);
router.get('/:key/html',     ctrl.getHtml);
router.post('/:key/pdf',     ctrl.generarPdf);
router.post('/:key/email',   ctrl.enviarEmail);

// Stubs para compatibilidad
router.post('/buscar',       ctrl.buscar);
router.get('/config',        ctrl.getConfig);
router.put('/config',        ctrl.updateConfig);
router.post('/ejecutar',     ctrl.ejecutarAhora);
router.get('/keys',          ctrl.estadoKeys);

module.exports = router;
