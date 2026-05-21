const pool = require('../config/db');

// ÓRDENES DE TRABAJO
const getOrdenes = async (req, res, next) => {
  try {
    const { estado, tipo, prioridad } = req.query;
    let sql = `SELECT ot.*, u.nombre AS asignado_nombre, p.nombre AS proveedor_nombre, 
                un.numero AS unidad_numero, ac.nombre AS area_nombre
               FROM ordenes_trabajo ot 
               LEFT JOIN usuarios u ON ot.asignado_a = u.id
               LEFT JOIN proveedores p ON ot.proveedor_id = p.id
               LEFT JOIN unidades un ON ot.unidad_id = un.id
               LEFT JOIN areas_comunes ac ON ot.area_id = ac.id
               WHERE 1=1`;
    const params = [];
    if (estado) { sql += ' AND ot.estado = ?'; params.push(estado); }
    if (tipo) { sql += ' AND ot.tipo = ?'; params.push(tipo); }
    if (prioridad) { sql += ' AND ot.prioridad = ?'; params.push(prioridad); }
    sql += ' ORDER BY FIELD(ot.prioridad, "urgente","alta","media","baja"), ot.fecha_reporte DESC';
    const [rows] = await pool.query(sql, params);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const createOrden = async (req, res, next) => {
  try {
    const { tipo, titulo, descripcion, unidad_id, area_id, prioridad } = req.body;
    const reportado_por = req.usuario.id;
    const [result] = await pool.query(
      `INSERT INTO ordenes_trabajo (tipo, titulo, descripcion, unidad_id, area_id, prioridad, reportado_por) VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [tipo || 'correctivo', titulo, descripcion, unidad_id, area_id, prioridad || 'media', reportado_por]
    );
    res.status(201).json({ success: true, message: 'Orden de trabajo creada', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

const updateOrden = async (req, res, next) => {
  try {
    const { estado, proveedor_id, asignado_a, costo_real, notas, calificacion } = req.body;
    const fields = [];
    const params = [];
    if (estado) { 
      fields.push('estado = ?'); params.push(estado);
      if (estado === 'asignado') { fields.push('fecha_asignacion = NOW()'); }
      if (estado === 'en_progreso') { fields.push('fecha_inicio = NOW()'); }
      if (['completado','cerrado'].includes(estado)) { fields.push('fecha_fin = NOW()'); }
    }
    if (proveedor_id !== undefined) { fields.push('proveedor_id = ?'); params.push(proveedor_id); }
    if (asignado_a !== undefined) { fields.push('asignado_a = ?'); params.push(asignado_a); }
    if (costo_real !== undefined) { fields.push('costo_real = ?'); params.push(costo_real); }
    if (notas) { fields.push('notas = ?'); params.push(notas); }
    if (calificacion) { fields.push('calificacion = ?'); params.push(calificacion); }
    if (!fields.length) return res.status(400).json({ success: false, message: 'Nada que actualizar' });
    params.push(req.params.id);
    await pool.query(`UPDATE ordenes_trabajo SET ${fields.join(', ')} WHERE id = ?`, params);
    res.json({ success: true, message: 'Orden actualizada' });
  } catch (err) { next(err); }
};

// ÁREAS COMUNES
const getAreas = async (req, res, next) => {
  try {
    const [rows] = await pool.query(`SELECT * FROM areas_comunes WHERE activo = 1`);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

// PROVEEDORES
const getProveedores = async (req, res, next) => {
  try {
    const [rows] = await pool.query(`SELECT * FROM proveedores WHERE activo = 1 ORDER BY nombre`);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const createProveedor = async (req, res, next) => {
  try {
    const { nombre, rfc, tipo_servicio, contacto_nombre, contacto_telefono, contacto_email, direccion } = req.body;
    const [result] = await pool.query(
      `INSERT INTO proveedores (nombre, rfc, tipo_servicio, contacto_nombre, contacto_telefono, contacto_email, direccion) VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [nombre, rfc, tipo_servicio, contacto_nombre, contacto_telefono, contacto_email, direccion]
    );
    res.status(201).json({ success: true, message: 'Proveedor creado', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

module.exports = { getOrdenes, createOrden, updateOrden, getAreas, getProveedores, createProveedor };
