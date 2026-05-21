const pool = require('../config/db');

// Reporte de Pagos
exports.getPagos = async (req, res, next) => {
  try {
    const [rows] = await pool.query(`
      SELECT p.id, u.numero as unidad, t.nombre as torre, c.descripcion as cuota, 
             p.monto_pagado, p.fecha_pago, p.metodo, us.nombre as registrado_por
      FROM pagos p
      JOIN unidades u ON p.unidad_id = u.id
      LEFT JOIN torres t ON u.torre_id = t.id
      LEFT JOIN cuotas c ON p.cuota_id = c.id
      LEFT JOIN usuarios us ON p.registrado_por = us.id
      ORDER BY p.fecha_pago DESC
    `);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

// Reporte de Morosidad
exports.getMorosos = async (req, res, next) => {
  try {
    const [rows] = await pool.query(`
      SELECT c.id, u.numero as unidad, t.nombre as torre, c.descripcion, 
             c.monto, c.fecha_vencimiento, DATEDIFF(CURDATE(), c.fecha_vencimiento) as dias_retraso
      FROM cuotas c
      JOIN unidades u ON c.unidad_id = u.id
      LEFT JOIN torres t ON u.torre_id = t.id
      WHERE c.estado = 'vencido'
      ORDER BY dias_retraso DESC
    `);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

// Reporte de Accesos
exports.getAccesos = async (req, res, next) => {
  try {
    const [rows] = await pool.query(`
      SELECT v.id, v.nombre as visitante, v.motivo, v.tipo, v.entrada, v.salida, 
             u.numero as unidad_visitada, g.nombre as guardia
      FROM visitantes v
      LEFT JOIN unidades u ON v.unidad_id = u.id
      LEFT JOIN usuarios g ON v.guardia_id = g.id
      ORDER BY v.entrada DESC
    `);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

// Reporte de Mantenimiento
exports.getMantenimiento = async (req, res, next) => {
  try {
    const [rows] = await pool.query(`
      SELECT o.id, o.titulo, o.estado, o.prioridad, o.fecha_reporte, o.fecha_fin, 
             u.numero as unidad, a.nombre as area_comun, p.nombre as proveedor, o.costo_real
      FROM ordenes_trabajo o
      LEFT JOIN unidades u ON o.unidad_id = u.id
      LEFT JOIN areas_comunes a ON o.area_id = a.id
      LEFT JOIN proveedores p ON o.proveedor_id = p.id
      ORDER BY o.fecha_reporte DESC
    `);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};
