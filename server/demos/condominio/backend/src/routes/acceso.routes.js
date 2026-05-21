const express = require('express');
const router = express.Router();
const ctrl = require('../controllers/acceso.controller');
const { verifyToken } = require('../middlewares/auth.middleware');

router.use(verifyToken);
router.get('/visitantes', ctrl.getVisitantes);
router.post('/visitantes', ctrl.registrarVisitante);
router.put('/visitantes/:id/salida', ctrl.registrarSalida);
router.get('/paquetes', ctrl.getPaquetes);
router.post('/paquetes', ctrl.registrarPaquete);
router.get('/incidentes', ctrl.getIncidentes);
router.post('/incidentes', ctrl.createIncidente);

module.exports = router;
