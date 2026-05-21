const pool = require('../config/db');

const getAll = async (req, res, next) => {
  try {
    const { unidad_id, tipo, activo } = req.query;
    let sql = `SELECT r.*, u.numero AS unidad_numero, t.nombre AS torre_nombre 
               FROM residentes r 
               JOIN unidades u ON r.unidad_id = u.id 
               LEFT JOIN torres t ON u.torre_id = t.id 
               WHERE 1=1`;
    const params = [];
    if (unidad_id) { sql += ' AND r.unidad_id = ?'; params.push(unidad_id); }
    if (tipo) { sql += ' AND r.tipo = ?'; params.push(tipo); }
    if (activo !== undefined) { sql += ' AND r.activo = ?'; params.push(activo); }
    else { sql += ' AND r.activo = 1'; }
    sql += ' ORDER BY r.nombre';
    const [rows] = await pool.query(sql, params);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const getById = async (req, res, next) => {
  try {
    const [rows] = await pool.query(
      `SELECT r.*, u.numero AS unidad_numero FROM residentes r JOIN unidades u ON r.unidad_id = u.id WHERE r.id = ?`,
      [req.params.id]
    );
    if (!rows.length) return res.status(404).json({ success: false, message: 'Residente no encontrado' });
    const [contactos] = await pool.query(`SELECT * FROM contactos_emergencia WHERE residente_id = ?`, [req.params.id]);
    res.json({ success: true, data: { ...rows[0], contactos_emergencia: contactos } });
  } catch (err) { next(err); }
};

const create = async (req, res, next) => {
  try {
    const { unidad_id, nombre, apellidos, tipo, documento_id, tipo_documento, fecha_nacimiento, genero, email, telefono, telefono_alt, fecha_ingreso, notas } = req.body;
    const [result] = await pool.query(
      `INSERT INTO residentes (unidad_id, nombre, apellidos, tipo, documento_id, tipo_documento, fecha_nacimiento, genero, email, telefono, telefono_alt, fecha_ingreso, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [unidad_id, nombre, apellidos, tipo, documento_id, tipo_documento, fecha_nacimiento, genero, email, telefono, telefono_alt, fecha_ingreso, notas]
    );
    // Actualizar estado de unidad a 'habitada'
    await pool.query(`UPDATE unidades SET estado = 'habitada' WHERE id = ?`, [unidad_id]);
    res.status(201).json({ success: true, message: 'Residente creado', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

const update = async (req, res, next) => {
  try {
    const { unidad_id, nombre, apellidos, tipo, documento_id, tipo_documento, fecha_nacimiento, genero, email, telefono, telefono_alt, fecha_ingreso, fecha_salida, notas, activo } = req.body;
    await pool.query(
      `UPDATE residentes SET unidad_id=?, nombre=?, apellidos=?, tipo=?, documento_id=?, tipo_documento=?, fecha_nacimiento=?, genero=?, email=?, telefono=?, telefono_alt=?, fecha_ingreso=?, fecha_salida=?, notas=?, activo=? WHERE id=?`,
      [unidad_id, nombre, apellidos, tipo, documento_id, tipo_documento, fecha_nacimiento, genero, email, telefono, telefono_alt, fecha_ingreso, fecha_salida, notas, activo ?? 1, req.params.id]
    );
    res.json({ success: true, message: 'Residente actualizado' });
  } catch (err) { next(err); }
};

const remove = async (req, res, next) => {
  try {
    await pool.query(`UPDATE residentes SET activo = 0 WHERE id = ?`, [req.params.id]);
    res.json({ success: true, message: 'Residente dado de baja' });
  } catch (err) { next(err); }
};

module.exports = { getAll, getById, create, update, remove };
