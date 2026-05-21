const express = require('express');
const router = express.Router();
const db = require('../db');
const auth = require('../middleware/auth');

// GET /api/reportes/dashboard - stats para el dashboard
router.get('/dashboard', auth(), async (req, res) => {
  try {
    const [ingresoHoy] = await db.query(
      "SELECT COALESCE(SUM(monto),0) as total FROM pagos WHERE DATE(fecha_pago)=CURDATE()"
    );
    const [ingresoMes] = await db.query(
      "SELECT COALESCE(SUM(monto),0) as total FROM pagos WHERE MONTH(fecha_pago)=MONTH(CURDATE()) AND YEAR(fecha_pago)=YEAR(CURDATE())"
    );
    const [vehiculosHoy] = await db.query(
      "SELECT COUNT(*) as total FROM tickets WHERE DATE(hora_entrada)=CURDATE()"
    );
    const [vehiculosActivos] = await db.query(
      "SELECT COUNT(*) as total FROM tickets WHERE estado='activo'"
    );
    const [espaciosStats] = await db.query(
      "SELECT COUNT(*) as total, SUM(estado='libre') as libres, SUM(estado='ocupado') as ocupados FROM espacios"
    );
    const [ultimas5] = await db.query(
      "SELECT t.placa, t.tipo_vehiculo, t.hora_entrada, e.numero as espacio, t.estado FROM tickets t LEFT JOIN espacios e ON t.espacio_id=e.id ORDER BY t.hora_entrada DESC LIMIT 5"
    );
    // Ingresos últimos 7 días
    const [ingresos7] = await db.query(
      `SELECT DATE(fecha_pago) as fecha, SUM(monto) as total
       FROM pagos
       WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
       GROUP BY DATE(fecha_pago)
       ORDER BY fecha`
    );
    // Vehículos últimos 7 días
    const [vehiculos7] = await db.query(
      `SELECT DATE(hora_entrada) as fecha, COUNT(*) as total
       FROM tickets
       WHERE hora_entrada >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
       GROUP BY DATE(hora_entrada)
       ORDER BY fecha`
    );
    // Distribución por tipo de vehículo hoy
    const [tiposHoy] = await db.query(
      `SELECT tipo_vehiculo as name, COUNT(*) as value
       FROM tickets
       WHERE DATE(hora_entrada)=CURDATE()
       GROUP BY tipo_vehiculo`
    );

    res.json({
      ingreso_hoy: parseFloat(ingresoHoy[0].total),
      ingreso_mes: parseFloat(ingresoMes[0].total),
      vehiculos_hoy: vehiculosHoy[0].total,
      vehiculos_activos: vehiculosActivos[0].total,
      espacios: espaciosStats[0],
      ultimas_entradas: ultimas5,
      ingresos_7dias: ingresos7,
      vehiculos_7dias: vehiculos7,
      vehiculos_tipos: tiposHoy
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// GET /api/reportes/ingresos?desde=&hasta=&agrupar=dia|semana|mes
router.get('/ingresos', auth(), async (req, res) => {
  const { desde, hasta, agrupar = 'dia' } = req.query;
  const formatMap = { dia: '%Y-%m-%d', semana: '%Y-%u', mes: '%Y-%m' };
  const fmt = formatMap[agrupar] || '%Y-%m-%d';
  try {
    let q = `SELECT DATE_FORMAT(fecha_pago,'${fmt}') as periodo, SUM(monto) as total, COUNT(*) as transacciones FROM pagos WHERE 1=1`;
    const params = [];
    if (desde) { q += ' AND fecha_pago >= ?'; params.push(desde); }
    if (hasta) { q += ' AND fecha_pago <= ?'; params.push(hasta + ' 23:59:59'); }
    q += ` GROUP BY periodo ORDER BY periodo`;
    const [rows] = await db.query(q, params);
    res.json(rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// GET /api/reportes/ocupacion - porcentaje de ocupación por día
router.get('/ocupacion', auth(), async (req, res) => {
  try {
    const [rows] = await db.query(`
      SELECT DATE(hora_entrada) as fecha, COUNT(*) as vehiculos
      FROM tickets
      WHERE hora_entrada >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
      GROUP BY fecha ORDER BY fecha
    `);
    res.json(rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
