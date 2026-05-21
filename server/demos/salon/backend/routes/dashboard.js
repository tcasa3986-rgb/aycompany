const express = require('express');
const router = express.Router();
const pool = require('../db');

// Obtener estadísticas para el Dashboard
router.get('/stats', async (req, res) => {
    try {
        // Total Ingresos (ejemplo simplificado: sumar todas ventas)
        const [ingresosResult] = await pool.query("SELECT COALESCE(SUM(total), 0) AS total_ingresos FROM ventas");
        const totalIngresos = ingresosResult[0].total_ingresos;

        // Citas completadas vs canceladas (KPI circulares)
        const [citasCompletadas] = await pool.query("SELECT COUNT(*) AS count FROM citas WHERE estado = 'completada'");
        const [citasCanceladas] = await pool.query("SELECT COUNT(*) AS count FROM citas WHERE estado = 'cancelada'");
        const [totalCitas] = await pool.query("SELECT COUNT(*) AS count FROM citas");

        // Clientes y Total de citas del día/mes (simplificado)
        const [totalClientes] = await pool.query("SELECT COUNT(*) AS count FROM clientes");

        // Ingresos por mes para gráfica
        // MySQL MONTH(fecha) simplificado (no agrupa por año aquí para brevedad)
        const [ingresosPorMes] = await pool.query(`
      SELECT MONTH(fecha) AS mes, SUM(total) AS total 
      FROM ventas 
      GROUP BY MONTH(fecha)
      ORDER BY mes
    `);

        res.json({
            ingresos: totalIngresos,
            citas: {
                total: totalCitas[0].count,
                completadas: citasCompletadas[0].count,
                canceladas: citasCanceladas[0].count,
            },
            clientes: totalClientes[0].count,
            ingresosMensuales: ingresosPorMes
        });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

module.exports = router;
