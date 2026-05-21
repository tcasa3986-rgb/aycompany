const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const { Usuario } = require('../models');
require('dotenv').config();

// POST /api/auth/login
const login = async (req, res) => {
  try {
    const { email, password } = req.body;
    if (!email || !password)
      return res.status(400).json({ ok: false, msg: 'Email y contraseña requeridos' });

    const usuario = await Usuario.findOne({
      where: { email, activo: 1 },
      include: [{ association: 'rol' }],
    });

    if (!usuario)
      return res.status(401).json({ ok: false, msg: 'Credenciales inválidas' });

    const valido = await bcrypt.compare(password, usuario.password_hash);
    if (!valido)
      return res.status(401).json({ ok: false, msg: 'Credenciales inválidas' });

    // Actualizar último login
    await usuario.update({ ultimo_login: new Date() });

    const payload = {
      id:      usuario.id,
      email:   usuario.email,
      nombre:  usuario.nombre,
      apellido:usuario.apellido,
      rol:     usuario.rol?.nombre,
      permisos:usuario.rol?.permisos,
    };

    const token = jwt.sign(payload, process.env.JWT_SECRET, {
      expiresIn: process.env.JWT_EXPIRES_IN || '8h',
    });

    return res.json({
      ok: true,
      token,
      usuario: { ...payload, avatar_url: usuario.avatar_url },
    });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: 'Error del servidor' });
  }
};

// GET /api/auth/me
const me = async (req, res) => {
  try {
    const usuario = await Usuario.findByPk(req.usuario.id, {
      attributes: { exclude: ['password_hash'] },
      include: [{ association: 'rol' }],
    });
    return res.json({ ok: true, usuario });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: 'Error del servidor' });
  }
};

// POST /api/auth/logout
const logout = (req, res) => {
  return res.json({ ok: true, msg: 'Sesión cerrada' });
};

module.exports = { login, me, logout };
