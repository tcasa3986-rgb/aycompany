const router = require('express').Router();
const auth   = require('../middleware/authMiddleware');
const ctrl   = require('../controllers/ticketsController');

router.use(auth);
router.get('/',     ctrl.listar);
router.put('/:id',  ctrl.responder);
router.delete('/:id', ctrl.eliminar);

module.exports = router;
