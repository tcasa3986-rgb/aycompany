const express = require('express');
const router  = express.Router();
const { autenticar } = require('../middlewares/auth');
const { listar, kanban, crear, actualizar, cambiarEtapa } = require('../controllers/oportunidadesController');

router.get   ('/',          autenticar, listar);
router.get   ('/kanban',    autenticar, kanban);
router.post  ('/',          autenticar, crear);
router.put   ('/:id',       autenticar, actualizar);
router.patch ('/:id/etapa', autenticar, cambiarEtapa);

module.exports = router;
