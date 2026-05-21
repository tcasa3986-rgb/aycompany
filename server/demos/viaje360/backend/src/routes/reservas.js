const express = require('express');
const router  = express.Router();
const { autenticar } = require('../middlewares/auth');
const { listar, obtener, crear, actualizar, registrarPago, generarPDF } = require('../controllers/reservasController');

router.get   ('/',          autenticar, listar);
router.post  ('/',          autenticar, crear);
router.get   ('/:id',       autenticar, obtener);
router.put   ('/:id',       autenticar, actualizar);
router.post  ('/:id/pagos', autenticar, registrarPago);
router.get   ('/:id/pdf',   autenticar, generarPDF);

module.exports = router;
