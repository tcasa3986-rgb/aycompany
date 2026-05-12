const router = require('express').Router();
const ctrl   = require('../controllers/agenteController');
const auth   = require('../middlewares/auth');

// Configuracion
router.get('/config',    auth, ctrl.getConfig);
router.put('/config',    auth, ctrl.updateConfig);

// Actividad y control
router.get('/actividad', auth, ctrl.actividadReciente);
router.post('/ejecutar', auth, ctrl.ejecutarAhora);

// Webhook de WhatsApp (sin auth — Meta llama directo)
router.get('/webhook/whatsapp',  ctrl.webhookVerificar);
router.post('/webhook/whatsapp', ctrl.webhookMensajes);

module.exports = router;
