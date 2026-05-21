const express = require('express');
const router = express.Router();
const { login, refresh, logout, me, cambiarPassword } = require('../controllers/auth.controller');
const { verifyToken } = require('../middlewares/auth.middleware');

router.post('/login', login);
router.post('/refresh', refresh);
router.post('/logout', logout);
router.get('/me', verifyToken, me);
router.put('/cambiar-password', verifyToken, cambiarPassword);

module.exports = router;
