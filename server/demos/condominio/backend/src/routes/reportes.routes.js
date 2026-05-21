const express = require('express');
const ctrl = require('../controllers/reportes.controller');
const { verifyToken, requireRol } = require('../middlewares/auth.middleware');

const router = express.Router();

router.use(verifyToken);
router.use(requireRol('super_admin', 'administrador', 'contador'));

router.get('/pagos', ctrl.getPagos);
router.get('/morosos', ctrl.getMorosos);
router.get('/accesos', ctrl.getAccesos);
router.get('/mantenimiento', ctrl.getMantenimiento);

module.exports = router;
