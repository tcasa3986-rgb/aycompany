const pool = require('../config/db');

const getAmenidades = async (req, res, next) => {
  try {
    const [rows] = await pool.query(`SELECT * FROM amenidades WHERE activo = 1`);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const getReservaciones = async (req, res, next) => {
  try {
    const { amenidad_id, fecha, unidad_id, estado } = req.query;
    let sql = `SELECT rv.*, am.nombre AS amenidad_nombre, u.numero AS unidad_numero, r.nombre AS residente_nombre
               FROM reservaciones rv
               JOIN amenidades am ON rv.amenidad_id = am.id
               JOIN unidades u ON rv.unidad_id = u.id
               LEFT JOIN residentes r ON rv.residente_id = r.id
               WHERE 1=1`;
    const params = [];
    if (amenidad_id) { sql += ' AND rv.amenidad_id = ?'; params.push(amenidad_id); }
    if (fecha) { sql += ' AND rv.fecha = ?'; params.push(fecha); }
    if (unidad_id) { sql += ' AND rv.unidad_id = ?'; params.push(unidad_id); }
    if (estado) { sql += ' AND rv.estado = ?'; params.push(estado); }
    sql += ' ORDER BY rv.fecha DESC, rv.hora_inicio';
    const [rows] = await pool.query(sql, params);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const createReservacion = async (req, res, next) => {
  try {
    const { amenidad_id, unidad_id, residente_id, fecha, hora_inicio, hora_fin, num_personas, notas } = req.body;

    // Verificar disponibilidad
    const [conflicto] = await pool.query(
      `SELECT id FROM reservaciones 
       WHERE amenidad_id = ? AND fecha = ? AND estado IN ('pendiente','confirmada')
       AND ((hora_inicio <= ? AND hora_fin > ?) OR (hora_inicio < ? AND hora_fin >= ?) OR (hora_inicio >= ? AND hora_fin <= ?))`,
      [amenidad_id, fecha, hora_inicio, hora_inicio, hora_fin, hora_fin, hora_inicio, hora_fin]
    );
    if (conflicto.length) {
      return res.status(409).json({ success: false, message: 'La amenidad ya está reservada en ese horario' });
    }

    const [amenidad] = await pool.query(`SELECT * FROM amenidades WHERE id = ?`, [amenidad_id]);
    const costo = amenidad[0]?.tiene_costo ? amenidad[0].costo : 0;

    const [result] = await pool.query(
      `INSERT INTO reservaciones (amenidad_id, unidad_id, residente_id, fecha, hora_inicio, hora_fin, num_personas, estado, costo_cobrado, notas) VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmada', ?, ?)`,
      [amenidad_id, unidad_id, residente_id, fecha, hora_inicio, hora_fin, num_personas || 1, costo, notas]
    );
    res.status(201).json({ success: true, message: 'Reservación creada', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

const updateReservacion = async (req, res, next) => {
  try {
    const { estado, notas } = req.body;
    await pool.query(`UPDATE reservaciones SET estado = ?, notas = ? WHERE id = ?`, [estado, notas, req.params.id]);
    res.json({ success: true, message: 'Reservación actualizada' });
  } catch (err) { next(err); }
};

module.exports = { getAmenidades, getReservaciones, createReservacion, updateReservacion };
