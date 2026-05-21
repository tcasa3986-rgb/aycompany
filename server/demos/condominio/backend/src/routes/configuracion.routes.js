const express = require('express');
const { getDatosCondominio, updateDatosCondominio, getTiposCuota, createTipoCuota, getUsuarios, createUsuario, updateUsuario, toggleUsuario, getAuditoria } = require('../controllers/configuracion.controller');
const { verifyToken, requireRol } = require('../middlewares/auth.middleware');

const router = express.Router();

router.use(verifyToken);
// Solo admin y super admin
router.use(requireRol('super_admin', 'administrador'));

router.get('/datos', getDatosCondominio);
router.put('/datos', updateDatosCondominio);

router.get('/cuotas', getTiposCuota);
router.post('/cuotas', createTipoCuota);

router.get('/usuarios', getUsuarios);
router.post('/usuarios', createUsuario);
router.put('/usuarios/:id', updateUsuario);
router.patch('/usuarios/:id/toggle', toggleUsuario);
router.get('/auditoria', getAuditoria);

module.exports = router;
