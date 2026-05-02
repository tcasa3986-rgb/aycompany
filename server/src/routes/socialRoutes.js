const router = require('express').Router();
const ctrl   = require('../controllers/socialController');
const auth   = require('../middlewares/auth');

// Webhooks (sin auth — llamados externamente)
router.get( '/webhook/meta',  ctrl.verificarWebhook);
router.post('/webhook/meta',  ctrl.recibirWebhook);
router.post('/webhook/make',       ctrl.recibirMake);
router.post('/webhook/make/reply', ctrl.responderMake);

// API protegida para la plataforma
router.use(auth);
router.get('/social',                ctrl.listar);
router.get('/social/stats',          ctrl.stats);
router.get('/social/conversacion',   ctrl.conversacion);
router.put('/social/:id/leido',      ctrl.marcarLeido);
router.put('/social/:id/respondido', ctrl.marcarRespondido);
router.post('/social/:id/responder', ctrl.responder);
router.delete('/social/:id',         ctrl.eliminar);

module.exports = router;
