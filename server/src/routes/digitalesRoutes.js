const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/digitalesController');

// Es la caja del negocio de productos digitales: solo admin.
router.use(auth);
router.use(auth.requireRol(['admin']));

router.get('/resumen',          ctrl.resumen);

router.get('/productos',        ctrl.listarProductos);
router.post('/productos',       ctrl.crearProducto);
router.put('/productos/:id',    ctrl.actualizarProducto);
router.delete('/productos/:id', ctrl.eliminarProducto);

router.get('/ventas',           ctrl.listarVentas);
router.post('/ventas',          ctrl.crearVenta);
router.put('/ventas/:id',       ctrl.actualizarVenta);
router.delete('/ventas/:id',    ctrl.eliminarVenta);
router.post('/ventas/:id/confirmar', ctrl.confirmarPago);
router.post('/ventas/:id/rechazar',  ctrl.rechazarPago);
router.post('/ventas/:id/despachar', ctrl.despachar);

module.exports = router;
