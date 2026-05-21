const express = require('express');
const router = express.Router();
const { getKpis, getIngresosEgresos, getDistribucionGastos, getMorosos, getActividadReciente } = require('../controllers/dashboard.controller');
const { verifyToken } = require('../middlewares/auth.middleware');

router.use(verifyToken);
router.get('/kpis', getKpis);
router.get('/ingresos-egresos', getIngresosEgresos);
router.get('/distribucion-gastos', getDistribucionGastos);
router.get('/morosos', getMorosos);
router.get('/actividad-reciente', getActividadReciente);

module.exports = router;
