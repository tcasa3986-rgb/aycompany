const express = require('express');
const router  = express.Router();
const { autenticar } = require('../middlewares/auth');
const { login, me, logout } = require('../controllers/authController');

router.post('/login',  login);
router.get ('/me',     autenticar, me);
router.post('/logout', autenticar, logout);

module.exports = router;
