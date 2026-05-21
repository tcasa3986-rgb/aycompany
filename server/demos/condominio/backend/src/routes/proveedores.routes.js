const express = require('express');
const ctrl = require('../controllers/proveedores.controller');
const { verifyToken, requireRol } = require('../middlewares/auth.middleware');

const router = express.Router();

router.use(verifyToken);
// Acceso permitido a administradores
router.use(requireRol('super_admin', 'administrador'));

// Rutas de Proveedores
router.get('/', ctrl.getAllProveedores);
router.post('/', ctrl.createProveedor);
router.get('/:id', ctrl.getProveedorById);
router.put('/:id', ctrl.updateProveedor);
router.delete('/:id', ctrl.deleteProveedor);

// Rutas de Contratos
router.get('/todos/contratos', ctrl.getContratos);
router.post('/contratos/nuevo', ctrl.createContrato);

module.exports = router;
