const pool = require('../config/db');

// GET /api/dashboard/kpis
const getKpis = async (req, res, next) => {
  try {
    const mesActual = new Date().getMonth() + 1;
    const anioActual = new Date().getFullYear();

    // Cuotas cobradas este mes
    const [[cobrado]] = await pool.query(
      `SELECT COALESCE(SUM(monto_pagado), 0) AS total FROM pagos 
       WHERE MONTH(fecha_pago) = ? AND YEAR(fecha_pago) = ?`,
      [mesActual, anioActual]
    );

    // Cuotas pendientes / vencidas
    const [[pendientes]] = await pool.query(
      `SELECT COUNT(*) AS total, COALESCE(SUM(monto), 0) AS monto 
       FROM cuotas WHERE estado IN ('pendiente', 'vencido')`
    );

    // Gastos del mes
    const [[gastos]] = await pool.query(
      `SELECT COALESCE(SUM(monto), 0) AS total FROM transacciones 
       WHERE tipo = 'egreso' AND MONTH(fecha) = ? AND YEAR(fecha) = ?`,
      [mesActual, anioActual]
    );

    // Fondo de reserva actual
    const [[fondo]] = await pool.query(
      `SELECT saldo_resultante FROM fondo_reserva ORDER BY id DESC LIMIT 1`
    );

    // Unidades habitadas vs total
    const [[unidades]] = await pool.query(
      `SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN estado = 'habitada' THEN 1 ELSE 0 END) AS habitadas,
        SUM(CASE WHEN estado = 'vacía' THEN 1 ELSE 0 END) AS vacias,
        SUM(CASE WHEN estado = 'en_venta' THEN 1 ELSE 0 END) AS en_venta
       FROM unidades WHERE activo = 1`
    );

    // Órdenes de mantenimiento abiertas
    const [[ordenes]] = await pool.query(
      `SELECT COUNT(*) AS total FROM ordenes_trabajo WHERE estado IN ('abierto', 'asignado', 'en_progreso')`
    );

    // Visitantes hoy
    const [[visitantes]] = await pool.query(
      `SELECT COUNT(*) AS total FROM visitantes WHERE DATE(entrada) = CURDATE()`
    );

    // Reservaciones hoy
    const [[reservaciones]] = await pool.query(
      `SELECT COUNT(*) AS total FROM reservaciones WHERE fecha = CURDATE() AND estado = 'confirmada'`
    );

    res.json({
      success: true,
      data: {
        cobradoMes: cobrado.total,
        cuotasPendientes: { cantidad: pendientes.total, monto: pendientes.monto },
        gastosMes: gastos.total,
        fondoReserva: fondo?.saldo_resultante || 0,
        unidades: { total: unidades.total, habitadas: unidades.habitadas, vacias: unidades.vacias, en_venta: unidades.en_venta },
        ordenesAbiertas: ordenes.total,
        visitantesHoy: visitantes.total,
        reservacionesHoy: reservaciones.total,
      },
    });
  } catch (err) {
    next(err);
  }
};

// GET /api/dashboard/ingresos-egresos
const getIngresosEgresos = async (req, res, next) => {
  try {
    const anio = req.query.anio || new Date().getFullYear();
    const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    const [ingresos] = await pool.query(
      `SELECT MONTH(fecha) AS mes, SUM(monto) AS total 
       FROM transacciones WHERE tipo = 'ingreso' AND YEAR(fecha) = ? 
       GROUP BY MONTH(fecha)`,
      [anio]
    );

    const [egresos] = await pool.query(
      `SELECT MONTH(fecha) AS mes, SUM(monto) AS total 
       FROM transacciones WHERE tipo = 'egreso' AND YEAR(fecha) = ? 
       GROUP BY MONTH(fecha)`,
      [anio]
    );

    const data = meses.map((nombre, i) => {
      const mes = i + 1;
      const ing = ingresos.find(r => r.mes === mes);
      const egr = egresos.find(r => r.mes === mes);
      return { mes: nombre, ingresos: ing ? parseFloat(ing.total) : 0, egresos: egr ? parseFloat(egr.total) : 0 };
    });

    res.json({ success: true, data });
  } catch (err) {
    next(err);
  }
};

// GET /api/dashboard/distribucion-gastos
const getDistribucionGastos = async (req, res, next) => {
  try {
    const [rows] = await pool.query(
      `SELECT categoria, SUM(monto) AS total FROM transacciones 
       WHERE tipo = 'egreso' AND YEAR(fecha) = YEAR(NOW())
       GROUP BY categoria ORDER BY total DESC`
    );
    res.json({ success: true, data: rows });
  } catch (err) {
    next(err);
  }
};

// GET /api/dashboard/morosos
const getMorosos = async (req, res, next) => {
  try {
    const [rows] = await pool.query(
      `SELECT u.numero AS unidad, t.nombre AS torre, 
        COUNT(c.id) AS cuotas_vencidas, SUM(c.monto) AS deuda_total,
        MIN(c.fecha_vencimiento) AS vencida_desde
       FROM cuotas c
       JOIN unidades u ON c.unidad_id = u.id
       JOIN torres t ON u.torre_id = t.id
       WHERE c.estado = 'vencido'
       GROUP BY c.unidad_id ORDER BY deuda_total DESC LIMIT 10`
    );
    res.json({ success: true, data: rows });
  } catch (err) {
    next(err);
  }
};

// GET /api/dashboard/actividad-reciente
const getActividadReciente = async (req, res, next) => {
  try {
    const [pagosRecientes] = await pool.query(
      `SELECT 'pago' AS tipo, p.fecha_pago AS fecha, 
        CONCAT('Pago recibido — Unidad ', u.numero) AS descripcion,
        p.monto_pagado AS monto
       FROM pagos p JOIN unidades u ON p.unidad_id = u.id
       ORDER BY p.fecha_pago DESC LIMIT 5`
    );

    const [ordenesRecientes] = await pool.query(
      `SELECT 'orden' AS tipo, ot.fecha_reporte AS fecha, 
        CONCAT('Orden: ', ot.titulo) AS descripcion, NULL AS monto
       FROM ordenes_trabajo ot ORDER BY ot.fecha_reporte DESC LIMIT 3`
    );

    const actividad = [...pagosRecientes, ...ordenesRecientes]
      .sort((a, b) => new Date(b.fecha) - new Date(a.fecha))
      .slice(0, 10);

    res.json({ success: true, data: actividad });
  } catch (err) {
    next(err);
  }
};

module.exports = { getKpis, getIngresosEgresos, getDistribucionGastos, getMorosos, getActividadReciente };
