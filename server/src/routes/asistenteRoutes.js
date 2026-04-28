const router = require('express').Router();
const ctrl = require('../controllers/asistenteController');
const auth = require('../middlewares/auth');
router.use(auth);
router.post('/chat', ctrl.chat);
module.exports = router;
