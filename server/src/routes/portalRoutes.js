const router = require('express').Router();
const ctrl   = require('../controllers/portalController');

// Rutas públicas — no requieren token de admin
router.get('/:token',                    ctrl.obtener);
router.get('/:token/facturas',           ctrl.facturas);
router.get('/:token/facturas/:id/pdf',   ctrl.facturaPDF);
router.put('/:token/datos',              ctrl.actualizarDatos);
router.get('/:token/tickets',            ctrl.ticketsCliente);
router.post('/:token/tickets',           ctrl.crearTicket);

module.exports = router;