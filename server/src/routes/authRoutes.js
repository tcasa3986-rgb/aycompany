const router = require('express').Router();
const ctrl   = require('../controllers/authController');
router.post('/login',              ctrl.login);
router.post('/registro-vendedor',  ctrl.registroVendedor);
module.exports = router;
