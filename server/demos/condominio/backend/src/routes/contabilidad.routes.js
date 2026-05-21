const express = require('express');
const ctrl = require('../controllers/contabilidad.controller');
const { verifyToken, requireRol } = require('../middlewares/auth.middleware');

const router = express.Router();

router.use(verifyToken);
router.use(requireRol('super_admin', 'administrador', 'contador'));

router.get('/cuentas', ctrl.getCuentas);
router.get('/movimientos', ctrl.getMovimientos);
router.post('/movimientos', ctrl.createMovimiento);
router.get('/resumen', ctrl.getResumen);

module.exports = router;
