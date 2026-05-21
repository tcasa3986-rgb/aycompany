const express = require('express');
const router = express.Router();
const ctrl = require('../controllers/unidades.controller');
const { verifyToken, requireRol } = require('../middlewares/auth.middleware');

router.use(verifyToken);
router.get('/', ctrl.getAll);
router.get('/:id', ctrl.getById);
router.post('/', requireRol('super_admin','administrador'), ctrl.create);
router.put('/:id', requireRol('super_admin','administrador'), ctrl.update);
router.delete('/:id', requireRol('super_admin','administrador'), ctrl.remove);

module.exports = router;
