const express  = require('express');
const router   = express.Router();
const { autenticar } = require('../middlewares/auth');
const {
  listar, obtener, crear, actualizar, eliminar, agregarInteraccion
} = require('../controllers/clientesController');

router.get   ('/',              autenticar, listar);
router.post  ('/',              autenticar, crear);
router.get   ('/:id',           autenticar, obtener);
router.put   ('/:id',           autenticar, actualizar);
router.delete('/:id',           autenticar, eliminar);
router.post  ('/:id/interacciones', autenticar, agregarInteraccion);

module.exports = router;
