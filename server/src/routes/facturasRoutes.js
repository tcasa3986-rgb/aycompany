const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/facturasController');

router.use(auth);
router.get('/',                  ctrl.listar);
router.get('/exportar/excel',    ctrl.exportarExcel);
router.get('/:id/pdf',           ctrl.descargarPDF);
router.post('/:id/enviar-email', ctrl.enviarPorEmail);
router.delete('/:id',            ctrl.eliminar);

module.exports = router;
