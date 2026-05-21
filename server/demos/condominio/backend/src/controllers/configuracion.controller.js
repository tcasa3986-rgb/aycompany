const pool = require('../config/db');

// Datos del condominio
exports.getDatosCondominio = async (req, res, next) => {
  try {
    const [condominio] = await pool.query('SELECT * FROM condominios LIMIT 1');
    const [config] = await pool.query('SELECT clave, valor FROM configuracion');
    
    // Convert array of config {clave: 'x', valor: 'y'} to object
    const confObj = config.reduce((acc, c) => ({ ...acc, [c.clave]: c.valor }), {});
    
    res.json({ success: true, data: { condominio: condominio[0] || {}, configuracion: confObj } });
  } catch (error) { next(error); }
};

exports.updateDatosCondominio = async (req, res, next) => {
  try {
    const { condominio, configuracion } = req.body;
    
    if (condominio && condominio.id) {
      await pool.query(
        'UPDATE condominios SET nombre=?, direccion=?, rfc=?, telefono=?, email=?, sitio_web=? WHERE id=?',
        [condominio.nombre, condominio.direccion, condominio.rfc, condominio.telefono, condominio.email, condominio.sitio_web, condominio.id]
      );
    } else if (condominio) {
      await pool.query(
        'INSERT INTO condominios (nombre, direccion, rfc, telefono, email, sitio_web) VALUES (?,?,?,?,?,?)',
        [condominio.nombre, condominio.direccion, condominio.rfc, condominio.telefono, condominio.email, condominio.sitio_web]
      );
    }

    if (configuracion) {
      const dbConfig = Object.entries(configuracion).map(([k, v]) => [k, v]);
      // Use INSERT ON DUPLICATE KEY UPDATE
      for(const [clave, valor] of dbConfig) {
        await pool.query(
          'INSERT INTO configuracion (clave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = ?',
          [clave, valor, valor]
        );
      }
    }

    res.json({ success: true, message: 'Configuración actualizada' });
  } catch (error) { next(error); }
};

// Tipos de cuota
exports.getTiposCuota = async (req, res, next) => {
  try {
    const [rows] = await pool.query('SELECT * FROM tipos_cuota');
    res.json({ success: true, data: rows });
  } catch (error) { next(error); }
};

exports.createTipoCuota = async (req, res, next) => {
  try {
    const { nombre, descripcion, monto_base, periodicidad } = req.body;
    await pool.query(
      'INSERT INTO tipos_cuota (nombre, descripcion, monto_base, periodicidad) VALUES (?,?,?,?)',
      [nombre, descripcion, monto_base, periodicidad]
    );
    res.json({ success: true, message: 'Tipo de cuota creado' });
  } catch (error) { next(error); }
};

// Usuarios y Roles
// Usuarios y Roles
exports.getUsuarios = async (req, res, next) => {
  try {
    const [usuarios] = await pool.query(`
      SELECT u.id, u.nombre, u.apellidos, u.email, u.telefono, u.activo, r.id as rol_id, r.nombre as rol
      FROM usuarios u JOIN roles r ON u.rol_id = r.id
      ORDER BY u.id DESC
    `);
    const [roles] = await pool.query('SELECT * FROM roles');
    res.json({ success: true, data: { usuarios, roles } });
  } catch (error) { next(error); }
};

const bcrypt = require('bcryptjs');

exports.createUsuario = async (req, res, next) => {
  try {
    const { nombre, apellidos, email, telefono, rol_id, password } = req.body;
    const hash = await bcrypt.hash(password || '123456', 10);
    await pool.query(
      'INSERT INTO usuarios (nombre, apellidos, email, telefono, rol_id, password_hash) VALUES (?, ?, ?, ?, ?, ?)',
      [nombre, apellidos, email, telefono, rol_id, hash]
    );
    res.json({ success: true, message: 'Usuario creado exitosamente' });
  } catch (error) { 
    if(error.code === 'ER_DUP_ENTRY') return res.status(400).json({ success: false, message: 'El email ya está registrado' });
    next(error); 
  }
};

exports.updateUsuario = async (req, res, next) => {
  try {
    const userId = req.params.id;
    const { nombre, apellidos, email, telefono, rol_id, password } = req.body;
    
    if (password) {
      const hash = await bcrypt.hash(password, 10);
      await pool.query(
        'UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=?, rol_id=?, password_hash=? WHERE id=?',
        [nombre, apellidos, email, telefono, rol_id, hash, userId]
      );
    } else {
      await pool.query(
        'UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=?, rol_id=? WHERE id=?',
        [nombre, apellidos, email, telefono, rol_id, userId]
      );
    }
    
    res.json({ success: true, message: 'Usuario actualizado exitosamente' });
  } catch (error) { 
    if(error.code === 'ER_DUP_ENTRY') return res.status(400).json({ success: false, message: 'El email ya está registrado por otro usuario' });
    next(error); 
  }
};

exports.toggleUsuario = async (req, res, next) => {
  try {
    const userId = req.params.id;
    const { activo } = req.body;
    await pool.query('UPDATE usuarios SET activo=? WHERE id=?', [activo ? 1 : 0, userId]);
    res.json({ success: true, message: 'Estado del usuario actualizado' });
  } catch (error) { next(error); }
};

// Auditoría
exports.getAuditoria = async (req, res, next) => {
  try {
    const [logs] = await pool.query(`
      SELECT l.*, u.nombre, u.email 
      FROM log_actividad l 
      LEFT JOIN usuarios u ON l.usuario_id = u.id 
      ORDER BY l.fecha DESC LIMIT 100
    `);
    res.json({ success: true, data: logs });
  } catch (error) { next(error); }
};
