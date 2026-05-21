const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const pool = require('../config/db');
require('dotenv').config();

const generarTokens = (usuario) => {
  const payload = { id: usuario.id, email: usuario.email, rol: usuario.rol_nombre, unidad_id: usuario.unidad_id };
  const accessToken = jwt.sign(payload, process.env.JWT_SECRET, { expiresIn: process.env.JWT_EXPIRES_IN });
  const refreshToken = jwt.sign({ id: usuario.id }, process.env.JWT_REFRESH_SECRET, { expiresIn: process.env.JWT_REFRESH_EXPIRES_IN });
  return { accessToken, refreshToken };
};

// POST /api/auth/login
const login = async (req, res, next) => {
  try {
    const { email, password } = req.body;

    if (!email || !password) {
      return res.status(400).json({ success: false, message: 'Email y contraseña son requeridos' });
    }

    const [rows] = await pool.query(
      `SELECT u.*, r.nombre AS rol_nombre FROM usuarios u 
       JOIN roles r ON u.rol_id = r.id 
       WHERE u.email = ? AND u.activo = 1`,
      [email]
    );

    if (rows.length === 0) {
      return res.status(401).json({ success: false, message: 'Credenciales inválidas' });
    }

    const usuario = rows[0];
    const passwordOk = await bcrypt.compare(password, usuario.password_hash);

    if (!passwordOk) {
      return res.status(401).json({ success: false, message: 'Credenciales inválidas' });
    }

    const { accessToken, refreshToken } = generarTokens(usuario);

    // Guardar refresh token en BD
    const expira = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000);
    await pool.query(
      `INSERT INTO sesiones (usuario_id, refresh_token, ip_address, user_agent, expira_en) VALUES (?, ?, ?, ?, ?)`,
      [usuario.id, refreshToken, req.ip, req.get('User-Agent'), expira]
    );

    // Actualizar último acceso
    await pool.query(`UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?`, [usuario.id]);

    res.json({
      success: true,
      message: 'Login exitoso',
      data: {
        accessToken,
        refreshToken,
        usuario: {
          id: usuario.id,
          nombre: usuario.nombre,
          apellidos: usuario.apellidos,
          email: usuario.email,
          rol: usuario.rol_nombre,
          foto_url: usuario.foto_url,
          unidad_id: usuario.unidad_id,
        },
      },
    });
  } catch (err) {
    next(err);
  }
};

// POST /api/auth/refresh
const refresh = async (req, res, next) => {
  try {
    const { refreshToken } = req.body;
    if (!refreshToken) return res.status(401).json({ success: false, message: 'Refresh token requerido' });

    // Verificar en BD
    const [sesiones] = await pool.query(
      `SELECT s.*, u.*, r.nombre AS rol_nombre FROM sesiones s 
       JOIN usuarios u ON s.usuario_id = u.id 
       JOIN roles r ON u.rol_id = r.id
       WHERE s.refresh_token = ? AND s.expira_en > NOW()`,
      [refreshToken]
    );

    if (sesiones.length === 0) {
      return res.status(401).json({ success: false, message: 'Refresh token inválido o expirado' });
    }

    jwt.verify(refreshToken, process.env.JWT_REFRESH_SECRET);
    const usuario = sesiones[0];
    const { accessToken, refreshToken: newRefreshToken } = generarTokens(usuario);

    // Renovar sesión
    const expira = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000);
    await pool.query(
      `UPDATE sesiones SET refresh_token = ?, expira_en = ? WHERE refresh_token = ?`,
      [newRefreshToken, expira, refreshToken]
    );

    res.json({ success: true, data: { accessToken, refreshToken: newRefreshToken } });
  } catch (err) {
    next(err);
  }
};

// POST /api/auth/logout
const logout = async (req, res, next) => {
  try {
    const { refreshToken } = req.body;
    if (refreshToken) {
      await pool.query(`DELETE FROM sesiones WHERE refresh_token = ?`, [refreshToken]);
    }
    res.json({ success: true, message: 'Sesión cerrada correctamente' });
  } catch (err) {
    next(err);
  }
};

// GET /api/auth/me
const me = async (req, res, next) => {
  try {
    const [rows] = await pool.query(
      `SELECT u.id, u.nombre, u.apellidos, u.email, u.telefono, u.foto_url, u.unidad_id, r.nombre AS rol
       FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE u.id = ?`,
      [req.usuario.id]
    );
    if (rows.length === 0) return res.status(404).json({ success: false, message: 'Usuario no encontrado' });
    res.json({ success: true, data: rows[0] });
  } catch (err) {
    next(err);
  }
};

// PUT /api/auth/cambiar-password
const cambiarPassword = async (req, res, next) => {
  try {
    const { passwordActual, passwordNuevo } = req.body;
    const [rows] = await pool.query(`SELECT password_hash FROM usuarios WHERE id = ?`, [req.usuario.id]);
    if (!rows.length) return res.status(404).json({ success: false, message: 'Usuario no encontrado' });

    const ok = await bcrypt.compare(passwordActual, rows[0].password_hash);
    if (!ok) return res.status(401).json({ success: false, message: 'Contraseña actual incorrecta' });

    const nuevoHash = await bcrypt.hash(passwordNuevo, 10);
    await pool.query(`UPDATE usuarios SET password_hash = ? WHERE id = ?`, [nuevoHash, req.usuario.id]);

    res.json({ success: true, message: 'Contraseña actualizada correctamente' });
  } catch (err) {
    next(err);
  }
};

module.exports = { login, refresh, logout, me, cambiarPassword };
