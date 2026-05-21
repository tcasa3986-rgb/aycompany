const express = require('express');
const router  = express.Router();
const { autenticar } = require('../middlewares/auth');
const ctrl = require('../controllers/paquetesController');

// Paquetes
router.get   ('/',     autenticar, ctrl.listar);
router.post  ('/',     autenticar, ctrl.crear);
router.get   ('/:id',  autenticar, ctrl.obtener);
router.put   ('/:id',  autenticar, ctrl.actualizar);
router.delete('/:id',  autenticar, ctrl.eliminar);

module.exports = router;
