const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/carteraController');

router.use(auth);
router.use(auth.requireRol(['admin']));
router.get('/',              ctrl.resumen);
router.post('/recordatorio', ctrl.enviarRecordatorio);
router.post('/masivo',       ctrl.enviarMasivo);

module.exports = router;
