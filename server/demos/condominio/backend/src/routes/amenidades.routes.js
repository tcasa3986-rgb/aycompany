const express = require('express');
const router = express.Router();
const ctrl = require('../controllers/amenidades.controller');
const { verifyToken } = require('../middlewares/auth.middleware');

router.use(verifyToken);
router.get('/', ctrl.getAmenidades);
router.get('/reservaciones', ctrl.getReservaciones);
router.post('/reservaciones', ctrl.createReservacion);
router.put('/reservaciones/:id', ctrl.updateReservacion);

module.exports = router;
