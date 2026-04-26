const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const { Usuario } = require('../models');

exports.login = async (req, res) => {
    const { email, password } = req.body;
    const user = await Usuario.findOne({ where: { email } });
    if (!user) return res.status(401).json({ ok: false, msg: 'Credenciales incorrectas' });
    const ok = await bcrypt.compare(password, user.password);
    if (!ok) return res.status(401).json({ ok: false, msg: 'Credenciales incorrectas' });
    const token = jwt.sign({ id: user.id, nombre: user.nombre, rol: user.rol }, process.env.JWT_SECRET, { expiresIn: process.env.JWT_EXPIRES_IN });
    res.json({ ok: true, token, user: { id: user.id, nombre: user.nombre, email: user.email, rol: user.rol } });
};
