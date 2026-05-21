const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/portalVendedorController');

router.get('/catalogo',        auth, ctrl.catalogo);
router.get('/stats',           auth, ctrl.stats);
router.get('/leads',           auth, ctrl.leads);
router.post('/leads',          auth, ctrl.crearLead);
router.put('/leads/:id',       auth, ctrl.actualizarLead);
router.post('/reuniones',      auth, ctrl.agendarReunion);
router.get('/clientes',        auth, ctrl.clientes);
router.get('/mi-equipo',       auth, ctrl.miEquipo);

module.exports = router;
