const express = require('express');
const router = express.Router();
const ctrl = require('../controllers/mantenimiento.controller');
const { verifyToken, requireRol } = require('../middlewares/auth.middleware');

router.use(verifyToken);
router.get('/ordenes', ctrl.getOrdenes);
router.post('/ordenes', ctrl.createOrden);
router.put('/ordenes/:id', ctrl.updateOrden);
router.get('/areas', ctrl.getAreas);
router.get('/proveedores', ctrl.getProveedores);
router.post('/proveedores', requireRol('super_admin','administrador'), ctrl.createProveedor);

module.exports = router;
