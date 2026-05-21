const express = require('express');
const router = express.Router();
const ctrl = require('../controllers/cobranza.controller');
const { verifyToken, requireRol } = require('../middlewares/auth.middleware');

router.use(verifyToken);
router.get('/tipos-cuota', ctrl.getTiposCuota);
router.get('/cuotas', ctrl.getCuotas);
router.post('/cuotas', requireRol('super_admin','administrador','contador'), ctrl.createCuota);
router.post('/cuotas/generar-masivo', requireRol('super_admin','administrador'), ctrl.generarCuotasMasivas);
router.get('/pagos', ctrl.getPagos);
router.post('/pagos', requireRol('super_admin','administrador','contador'), ctrl.registrarPago);
router.get('/morosos', ctrl.getMorosos);

module.exports = router;
