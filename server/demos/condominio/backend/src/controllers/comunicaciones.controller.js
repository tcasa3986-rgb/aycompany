const pool = require('../config/db');

const getAnuncios = async (req, res, next) => {
  try {
    const [rows] = await pool.query(
      `SELECT a.*, u.nombre AS autor FROM anuncios a LEFT JOIN usuarios u ON a.publicado_por = u.id 
       WHERE a.activo = 1 ORDER BY a.fecha_publicacion DESC`
    );
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const createAnuncio = async (req, res, next) => {
  try {
    const { titulo, contenido, tipo, fecha_expiracion, enviar_email } = req.body;
    const publicado_por = req.usuario.id;
    const [result] = await pool.query(
      `INSERT INTO anuncios (titulo, contenido, tipo, publicado_por, fecha_expiracion, enviar_email) VALUES (?, ?, ?, ?, ?, ?)`,
      [titulo, contenido, tipo || 'informativo', publicado_por, fecha_expiracion, enviar_email ? 1 : 0]
    );
    res.status(201).json({ success: true, message: 'Anuncio publicado', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

const updateAnuncio = async (req, res, next) => {
  try {
    const { titulo, contenido, tipo, fecha_expiracion, activo } = req.body;
    await pool.query(
      `UPDATE anuncios SET titulo=?, contenido=?, tipo=?, fecha_expiracion=?, activo=? WHERE id=?`,
      [titulo, contenido, tipo, fecha_expiracion, activo ?? 1, req.params.id]
    );
    res.json({ success: true, message: 'Anuncio actualizado' });
  } catch (err) { next(err); }
};

// MENSAJES
const getMensajes = async (req, res, next) => {
  try {
    const userId = req.usuario.id;
    const [rows] = await pool.query(
      `SELECT m.*, u.nombre AS de_nombre FROM mensajes m JOIN usuarios u ON m.de_usuario_id = u.id 
       WHERE m.para_usuario_id = ? ORDER BY m.creado_en DESC`,
      [userId]
    );
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const sendMensaje = async (req, res, next) => {
  try {
    const { para_usuario_id, asunto, contenido, unidad_id } = req.body;
    const de_usuario_id = req.usuario.id;
    const [result] = await pool.query(
      `INSERT INTO mensajes (de_usuario_id, para_usuario_id, unidad_id, asunto, contenido) VALUES (?, ?, ?, ?, ?)`,
      [de_usuario_id, para_usuario_id, unidad_id, asunto, contenido]
    );
    res.status(201).json({ success: true, message: 'Mensaje enviado', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

// ASAMBLEAS
const getAsambleas = async (req, res, next) => {
  try {
    const [rows] = await pool.query(`SELECT * FROM asambleas ORDER BY fecha DESC`);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const createAsamblea = async (req, res, next) => {
  try {
    const { titulo, tipo, fecha, lugar, orden_dia } = req.body;
    const creado_por = req.usuario.id;
    const [result] = await pool.query(
      `INSERT INTO asambleas (titulo, tipo, fecha, lugar, orden_dia, creado_por) VALUES (?, ?, ?, ?, ?, ?)`,
      [titulo, tipo || 'ordinaria', fecha, lugar, orden_dia, creado_por]
    );
    res.status(201).json({ success: true, message: 'Asamblea creada', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

module.exports = { getAnuncios, createAnuncio, updateAnuncio, getMensajes, sendMensaje, getAsambleas, createAsamblea };
