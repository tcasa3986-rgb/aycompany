const pool = require('../config/db');

// CUOTAS
const getCuotas = async (req, res, next) => {
  try {
    const { unidad_id, estado, mes, anio } = req.query;
    let sql = `SELECT c.*, u.numero AS unidad_numero, tc.nombre AS tipo_nombre, t.nombre AS torre
               FROM cuotas c 
               JOIN unidades u ON c.unidad_id = u.id 
               JOIN tipos_cuota tc ON c.tipo_cuota_id = tc.id
               LEFT JOIN torres t ON u.torre_id = t.id
               WHERE 1=1`;
    const params = [];
    if (unidad_id) { sql += ' AND c.unidad_id = ?'; params.push(unidad_id); }
    if (estado) { sql += ' AND c.estado = ?'; params.push(estado); }
    if (mes) { sql += ' AND MONTH(c.fecha_emision) = ?'; params.push(mes); }
    if (anio) { sql += ' AND YEAR(c.fecha_emision) = ?'; params.push(anio); }
    sql += ' ORDER BY c.fecha_emision DESC';
    const [rows] = await pool.query(sql, params);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const createCuota = async (req, res, next) => {
  try {
    const { unidad_id, tipo_cuota_id, monto, fecha_emision, fecha_vencimiento, descripcion } = req.body;
    const [result] = await pool.query(
      `INSERT INTO cuotas (unidad_id, tipo_cuota_id, monto, fecha_emision, fecha_vencimiento, descripcion) VALUES (?, ?, ?, ?, ?, ?)`,
      [unidad_id, tipo_cuota_id, monto, fecha_emision, fecha_vencimiento, descripcion]
    );
    res.status(201).json({ success: true, message: 'Cuota creada', data: { id: result.insertId } });
  } catch (err) { next(err); }
};

// GENERACIÓN MASIVA DE CUOTAS
const generarCuotasMasivas = async (req, res, next) => {
  try {
    const { tipo_cuota_id, mes, anio, fecha_vencimiento } = req.body;
    const [tipoCuota] = await pool.query(`SELECT * FROM tipos_cuota WHERE id = ?`, [tipo_cuota_id]);
    if (!tipoCuota.length) return res.status(404).json({ success: false, message: 'Tipo de cuota no encontrado' });

    const [unidades] = await pool.query(`SELECT id FROM unidades WHERE activo = 1 AND estado = 'habitada'`);
    const fechaEmision = `${anio}-${String(mes).padStart(2, '0')}-01`;
    
    let creadas = 0;
    for (const u of unidades) {
      // Evitar duplicados
      const [existe] = await pool.query(
        `SELECT id FROM cuotas WHERE unidad_id = ? AND tipo_cuota_id = ? AND MONTH(fecha_emision) = ? AND YEAR(fecha_emision) = ?`,
        [u.id, tipo_cuota_id, mes, anio]
      );
      if (!existe.length) {
        await pool.query(
          `INSERT INTO cuotas (unidad_id, tipo_cuota_id, monto, fecha_emision, fecha_vencimiento) VALUES (?, ?, ?, ?, ?)`,
          [u.id, tipo_cuota_id, tipoCuota[0].monto_base, fechaEmision, fecha_vencimiento]
        );
        creadas++;
      }
    }
    res.json({ success: true, message: `${creadas} cuotas generadas para ${unidades.length} unidades` });
  } catch (err) { next(err); }
};

// PAGOS
const registrarPago = async (req, res, next) => {
  try {
    const { cuota_id, unidad_id, monto_pagado, fecha_pago, metodo, referencia_pago, notas } = req.body;
    const registrado_por = req.usuario.id;

    const [result] = await pool.query(
      `INSERT INTO pagos (cuota_id, unidad_id, monto_pagado, fecha_pago, metodo, referencia_pago, registrado_por, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [cuota_id, unidad_id, monto_pagado, fecha_pago || new Date(), metodo, referencia_pago, registrado_por, notas]
    );

    // Actualizar estado de cuota
    if (cuota_id) {
      await pool.query(`UPDATE cuotas SET estado = 'pagado' WHERE id = ?`, [cuota_id]);
    }

    // Generar folio de recibo
    const folio = `REC-${anio(new Date())}-${String(result.insertId).padStart(6, '0')}`;
    await pool.query(`INSERT INTO recibos (pago_id, folio) VALUES (?, ?)`, [result.insertId, folio]);

    // Registrar transacción contable
    await pool.query(
      `INSERT INTO transacciones (cuenta_id, tipo, monto, fecha, descripcion, categoria, registrado_por) VALUES (1, 'ingreso', ?, ?, 'Pago de cuota', 'Cuotas', ?)`,
      [monto_pagado, fecha_pago || new Date(), registrado_por]
    );

    res.status(201).json({ success: true, message: 'Pago registrado', data: { id: result.insertId, folio } });
  } catch (err) { next(err); }
};

const anio = (d) => d.getFullYear();

const getPagos = async (req, res, next) => {
  try {
    const { unidad_id, mes, anio } = req.query;
    let sql = `SELECT p.*, u.numero AS unidad_numero, r.folio, usr.nombre AS registrado_por_nombre
               FROM pagos p JOIN unidades u ON p.unidad_id = u.id 
               LEFT JOIN recibos r ON r.pago_id = p.id
               LEFT JOIN usuarios usr ON p.registrado_por = usr.id
               WHERE 1=1`;
    const params = [];
    if (unidad_id) { sql += ' AND p.unidad_id = ?'; params.push(unidad_id); }
    if (mes) { sql += ' AND MONTH(p.fecha_pago) = ?'; params.push(mes); }
    if (anio) { sql += ' AND YEAR(p.fecha_pago) = ?'; params.push(anio); }
    sql += ' ORDER BY p.fecha_pago DESC';
    const [rows] = await pool.query(sql, params);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

const getMorosos = async (req, res, next) => {
  try {
    const [rows] = await pool.query(
      `SELECT u.id AS unidad_id, u.numero AS unidad, t.nombre AS torre,
        COUNT(c.id) AS cuotas_vencidas,
        SUM(c.monto + c.mora_aplicada) AS deuda_total,
        MIN(c.fecha_vencimiento) AS vencida_desde,
        DATEDIFF(NOW(), MIN(c.fecha_vencimiento)) AS dias_mora
       FROM cuotas c
       JOIN unidades u ON c.unidad_id = u.id
       JOIN torres t ON u.torre_id = t.id
       WHERE c.estado = 'vencido'
       GROUP BY c.unidad_id ORDER BY deuda_total DESC`
    );
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

// TIPOS DE CUOTA
const getTiposCuota = async (req, res, next) => {
  try {
    const [rows] = await pool.query(`SELECT * FROM tipos_cuota WHERE activo = 1`);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

module.exports = { getCuotas, createCuota, generarCuotasMasivas, registrarPago, getPagos, getMorosos, getTiposCuota };
