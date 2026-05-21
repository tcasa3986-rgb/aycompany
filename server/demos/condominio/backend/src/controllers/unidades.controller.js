const pool = require('../config/db');

const getAll = async (req, res, next) => {
  try {
    const { torre_id, estado, tipo } = req.query;
    let sql = `SELECT u.*, t.nombre AS torre_nombre FROM unidades u LEFT JOIN torres t ON u.torre_id = t.id WHERE u.activo = 1`;
    const params = [];
    if (torre_id) { sql += ' AND u.torre_id = ?'; params.push(torre_id); }
    if (estado) { sql += ' AND u.estado = ?'; params.push(estado); }
    if (tipo) { sql += ' AND u.tipo = ?'; params.push(tipo); }
    sql += ' ORDER BY t.nombre, u.piso, u.numero';
    const [rows] = await pool.query(sql, params);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const getById = async (req, res, next) => {
  try {
    const [unidad] = await pool.query(
      `SELECT u.*, t.nombre AS torre_nombre FROM unidades u LEFT JOIN torres t ON u.torre_id = t.id WHERE u.id = ?`,
      [req.params.id]
    );
    if (!unidad.length) return res.status(404).json({ success: false, message: 'Unidad no encontrada' });

    const [residentes] = await pool.query(`SELECT * FROM residentes WHERE unidad_id = ? AND activo = 1`, [req.params.id]);
    const [vehiculos] = await pool.query(`SELECT * FROM vehiculos WHERE unidad_id = ? AND activo = 1`, [req.params.id]);
    const [mascotas] = await pool.query(`SELECT * FROM mascotas WHERE unidad_id = ? AND activo = 1`, [req.params.id]);
    const [cuotas] = await pool.query(`SELECT * FROM cuotas WHERE unidad_id = ? ORDER BY fecha_emision DESC LIMIT 12`, [req.params.id]);

    res.json({ success: true, data: { ...unidad[0], residentes, vehiculos, mascotas, cuotas } });
  } catch (err) { next(err); }
};

const create = async (req, res, next) => {
  try {
    const { torre_id, numero, piso, tipo, metros_cuadrados, estado, descripcion } = req.body;
    const [result] = await pool.query(
      `INSERT INTO unidades (torre_id, numero, piso, tipo, metros_cuadrados, estado, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [torre_id, numero, piso || 1, tipo || 'departamento', metros_cuadrados, estado || 'vacía', descripcion]
    );
    res.status(201).json({ success: true, message: 'Unidad creada', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

const update = async (req, res, next) => {
  try {
    const { torre_id, numero, piso, tipo, metros_cuadrados, estado, descripcion } = req.body;
    await pool.query(
      `UPDATE unidades SET torre_id=?, numero=?, piso=?, tipo=?, metros_cuadrados=?, estado=?, descripcion=? WHERE id=?`,
      [torre_id, numero, piso, tipo, metros_cuadrados, estado, descripcion, req.params.id]
    );
    res.json({ success: true, message: 'Unidad actualizada' });
  } catch (err) { next(err); }
};

const remove = async (req, res, next) => {
  try {
    await pool.query(`UPDATE unidades SET activo = 0 WHERE id = ?`, [req.params.id]);
    res.json({ success: true, message: 'Unidad eliminada' });
  } catch (err) { next(err); }
};

module.exports = { getAll, getById, create, update, remove };
