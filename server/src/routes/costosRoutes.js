const router = require('express').Router();
const auth   = require('../middlewares/auth');
const ctrl   = require('../controllers/costosController');
const { enviarResumen, armarResumen } = require('../services/alertasCobroScheduler');

router.use(auth);
router.use(auth.requireRol(['admin']));
router.get('/',               ctrl.listar);
router.post('/',              ctrl.crear);
router.put('/:id',            ctrl.actualizar);
router.post('/:id/pagado',    ctrl.marcarPagado);
router.delete('/:id',         ctrl.eliminar);

// Vista previa del aviso diario y envío manual por Telegram
router.get('/alertas/preview', async (req, res) => res.json({ ok: true, msg: (await armarResumen()) || 'Sin novedades' }));
router.post('/alertas/enviar', async (req, res) => res.json({ ok: true, ...(await enviarResumen(true)) }));

module.exports = router;
