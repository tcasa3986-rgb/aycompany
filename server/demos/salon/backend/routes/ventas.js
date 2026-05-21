const express = require('express');
const router = express.Router();
const pool = require('../db');

// Obtener todas las ventas con detalles vinculados
router.get('/', async (req, res) => {
    try {
        const query = `
            SELECT v.id, v.total, v.metodo_pago, v.fecha,
                   c.fecha_hora AS cita_fecha, c.estado AS cita_estado,
                   cl.nombre AS cliente_nombre,
                   s.nombre AS servicio_nombre
            FROM ventas v
            LEFT JOIN citas c ON v.cita_id = c.id
            LEFT JOIN clientes cl ON c.cliente_id = cl.id
            LEFT JOIN servicios s ON c.servicio_id = s.id
            ORDER BY v.fecha DESC
        `;
        const [rows] = await pool.query(query);
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Aquí se podrían agregar POST o DELETE si el administrador necesita crear ventas manuales 
// no asociadas a citas o anular pagos, pero para este requerimiento nos enfocamos en reportar.

module.exports = router;
