const pool = require('../config/db');

// VISITANTES
const getVisitantes = async (req, res, next) => {
  try {
    const { fecha, unidad_id, estado } = req.query;
    let sql = `SELECT v.*, u.numero AS unidad_numero 
               FROM visitantes v LEFT JOIN unidades u ON v.unidad_id = u.id WHERE 1=1`;
    const params = [];
    if (fecha) { sql += ' AND DATE(v.entrada) = ?'; params.push(fecha); }
    if (unidad_id) { sql += ' AND v.unidad_id = ?'; params.push(unidad_id); }
    if (estado === 'dentro') { sql += ' AND v.salida IS NULL'; }
    sql += ' ORDER BY v.entrada DESC LIMIT 100';
    const [rows] = await pool.query(sql, params);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const registrarVisitante = async (req, res, next) => {
  try {
    const { nombre, documento_id, unidad_id, motivo, tipo, vehiculo_placas } = req.body;
    const guardia_id = req.usuario.id;
    const [result] = await pool.query(
      `INSERT INTO visitantes (nombre, documento_id, unidad_id, motivo, tipo, vehiculo_placas, guardia_id) VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [nombre, documento_id, unidad_id, motivo, tipo || 'visita', vehiculo_placas, guardia_id]
    );
    res.status(201).json({ success: true, message: 'Visitante registrado', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

const registrarSalida = async (req, res, next) => {
  try {
    await pool.query(`UPDATE visitantes SET salida = NOW() WHERE id = ?`, [req.params.id]);
    res.json({ success: true, message: 'Salida registrada' });
  } catch (err) { next(err); }
};

// PAQUETES
const getPaquetes = async (req, res, next) => {
  try {
    const [rows] = await pool.query(
      `SELECT p.*, u.numero AS unidad_numero FROM paquetes p JOIN unidades u ON p.unidad_id = u.id ORDER BY p.fecha_recepcion DESC`
    );
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const registrarPaquete = async (req, res, next) => {
  try {
    const { unidad_id, descripcion, remitente, empresa_mensajeria, numero_guia } = req.body;
    const recibido_por = req.usuario.id;
    const [result] = await pool.query(
      `INSERT INTO paquetes (unidad_id, descripcion, remitente, empresa_mensajeria, numero_guia, recibido_por) VALUES (?, ?, ?, ?, ?, ?)`,
      [unidad_id, descripcion, remitente, empresa_mensajeria, numero_guia, recibido_por]
    );
    res.status(201).json({ success: true, message: 'Paquete registrado', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

// INCIDENTES
const getIncidentes = async (req, res, next) => {
  try {
    const [rows] = await pool.query(
      `SELECT i.*, u.nombre AS reportado_nombre FROM incidentes i LEFT JOIN usuarios u ON i.reportado_por = u.id ORDER BY i.fecha DESC`
    );
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const createIncidente = async (req, res, next) => {
  try {
    const { tipo, descripcion, ubicacion, nivel } = req.body;
    const reportado_por = req.usuario.id;
    const [result] = await pool.query(
      `INSERT INTO incidentes (tipo, descripcion, ubicacion, nivel, reportado_por) VALUES (?, ?, ?, ?, ?)`,
      [tipo, descripcion, ubicacion, nivel || 'medio', reportado_por]
    );
    res.status(201).json({ success: true, message: 'Incidente registrado', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

module.exports = { getVisitantes, registrarVisitante, registrarSalida, getPaquetes, registrarPaquete, getIncidentes, createIncidente };
