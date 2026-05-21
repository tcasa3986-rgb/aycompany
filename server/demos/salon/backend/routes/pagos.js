const express = require('express');
const router = express.Router();
const pool = require('../db');

// Obtener pagos de una cita específica
router.get('/cita/:citaId', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM pagos WHERE cita_id = ? ORDER BY fecha ASC', [req.params.citaId]);
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Registrar un nuevo pago (abono) a una cita
router.post('/', async (req, res) => {
    const { cita_id, monto, metodo_pago } = req.body;
    try {
        // 1. Registrar el abono
        const [result] = await pool.query(
            'INSERT INTO pagos (cita_id, monto, metodo_pago) VALUES (?, ?, ?)',
            [cita_id, monto, metodo_pago]
        );
        
        // 2. Verificar cuánto se ha pagado en total
        const [sumaRows] = await pool.query('SELECT COALESCE(SUM(monto), 0) as total_abonado FROM pagos WHERE cita_id = ?', [cita_id]);
        const totalAbonado = parseFloat(sumaRows[0].total_abonado);
        
        // 3. Obtener el precio total del servicio
        const [citaRows] = await pool.query(`
            SELECT c.estado, s.precio 
            FROM citas c
            JOIN servicios s ON c.servicio_id = s.id
            WHERE c.id = ?
        `, [cita_id]);
        
        if (citaRows.length === 0) {
            return res.status(404).json({ error: 'Cita no encontrada.' });
        }
        
        const precioTotal = parseFloat(citaRows[0].precio);
        let nuevoEstado = citaRows[0].estado;
        
        // 4. Si el total abonado cubre o supera el precio, y la cita no estaba completada ni cancelada
        if (totalAbonado >= precioTotal && nuevoEstado !== 'completada' && nuevoEstado !== 'cancelada') {
            await pool.query('UPDATE citas SET estado = "completada" WHERE id = ?', [cita_id]);
            nuevoEstado = 'completada';
            
            // Generar la venta por el total del servicio
            await pool.query('INSERT INTO ventas (cita_id, total, metodo_pago) VALUES (?, ?, ?)', [cita_id, precioTotal, metodo_pago]);
        }
        
        res.status(201).json({ 
            id: result.insertId, 
            cita_id, 
            monto, 
            metodo_pago,
            total_abonado: totalAbonado,
            precio_total: precioTotal,
            nuevo_estado: nuevoEstado
        });
        
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Eliminar un pago
router.delete('/:id', async (req, res) => {
    const { id } = req.params;
    try {
        const [result] = await pool.query('DELETE FROM pagos WHERE id = ?', [id]);
        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'Pago no encontrado' });
        }
        res.json({ message: 'Pago eliminado correctamente' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

module.exports = router;
