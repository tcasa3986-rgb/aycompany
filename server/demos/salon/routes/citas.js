const express = require('express');
const router = express.Router();
const pool = require('../db');
const whatsappService = require('../services/whatsappService');

// Obtener todas las citas con datos relacionados
router.get('/', async (req, res) => {
    try {
        const query = `
      SELECT c.id, c.fecha_hora, c.estado, 
             cl.nombre AS cliente_nombre, 
             s.nombre AS servicio_nombre, s.precio, s.duracion_minutos,
             u.nombre AS estilista_nombre,
             COALESCE(SUM(p.monto), 0) AS total_abonado
      FROM citas c
      JOIN clientes cl ON c.cliente_id = cl.id
      JOIN servicios s ON c.servicio_id = s.id
      JOIN usuarios u ON c.usuario_id = u.id
      LEFT JOIN pagos p ON c.id = p.cita_id
      GROUP BY c.id, cl.nombre, s.nombre, s.precio, s.duracion_minutos, u.nombre
      ORDER BY c.fecha_hora DESC
    `;
        const [rows] = await pool.query(query);
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Crear cita
router.post('/', async (req, res) => {
    const { cliente_id, servicio_id, usuario_id, fecha_hora } = req.body;
    try {
        // --- INICIO VALIDACIÓN DE CRUCE DE HORARIOS ---
        const [servicioInfo] = await pool.query('SELECT duracion_minutos FROM servicios WHERE id = ?', [servicio_id]);
        if (servicioInfo.length === 0) return res.status(400).json({ error: 'Servicio no encontrado.' });

        const duracionMinutos = servicioInfo[0].duracion_minutos;
        const fechaInicioReq = new Date(fecha_hora);
        const fechaFinReq = new Date(fechaInicioReq.getTime() + duracionMinutos * 60000);

        // Buscar las citas del estilista en el mismo día (que no estén canceladas/completadas)
        const [citasExistentes] = await pool.query(`
            SELECT c.id, c.fecha_hora, s.duracion_minutos
            FROM citas c
            JOIN servicios s ON c.servicio_id = s.id
            WHERE c.usuario_id = ? AND c.estado IN ('pendiente', 'confirmada')
            AND DATE(c.fecha_hora) = DATE(?)
        `, [usuario_id, fecha_hora]);

        for (let cita of citasExistentes) {
            const inicioExistente = new Date(cita.fecha_hora);
            const finExistente = new Date(inicioExistente.getTime() + cita.duracion_minutos * 60000);

            // Lógica de solapamiento de rangos de tiempo
            if (fechaInicioReq < finExistente && fechaFinReq > inicioExistente) {
                return res.status(400).json({ error: 'El estilista seleccionado ya tiene una cita que se cruza con este horario.' });
            }
        }
        // --- FIN VALIDACIÓN ---

        const [result] = await pool.query(
            'INSERT INTO citas (cliente_id, servicio_id, usuario_id, fecha_hora) VALUES (?, ?, ?, ?)',
            [cliente_id, servicio_id, usuario_id, fecha_hora]
        );
        
        // --- WHATSAPP NOTIFICATION ---
        // Fire asynchronously to not block the response
        whatsappService.triggerNewAppointmentAlert(result.insertId).catch(e => console.error('Fallo disparando alerta WhatsApp:', e));

        res.status(201).json({ id: result.insertId, cliente_id, servicio_id, usuario_id, fecha_hora, estado: 'pendiente' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Actualizar estado de cita
router.patch('/:id/estado', async (req, res) => {
    const { id } = req.params;
    const { estado } = req.body;
    try {
        await pool.query('UPDATE citas SET estado = ? WHERE id = ?', [estado, id]);

        // Si la cita se completa, crear una venta automáticamente
        if (estado === 'completada') {
            const [cita] = await pool.query(`
        SELECT c.id, s.precio 
        FROM citas c 
        JOIN servicios s ON c.servicio_id = s.id 
        WHERE c.id = ?`,
                [id]);

            if (cita.length > 0) {
                await pool.query('INSERT INTO ventas (cita_id, total) VALUES (?, ?)', [id, cita[0].precio]);
            }
        }

        res.json({ message: 'Estado actualizado correctamente' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Actualizar cita
router.put('/:id', async (req, res) => {
    const { id } = req.params;
    const { cliente_id, servicio_id, usuario_id, fecha_hora, estado } = req.body;
    try {
        // --- INICIO VALIDACIÓN DE CRUCE DE HORARIOS ---
        // Excluimos las citas completadas/canceladas y actualizaciones de solo estado (cuando cambia a cancelada)
        if (estado === 'pendiente' || estado === 'confirmada') {
            const [servicioInfo] = await pool.query('SELECT duracion_minutos FROM servicios WHERE id = ?', [servicio_id]);
            if (servicioInfo.length === 0) return res.status(400).json({ error: 'Servicio no encontrado.' });

            const duracionMinutos = servicioInfo[0].duracion_minutos;
            const fechaInicioReq = new Date(fecha_hora);
            const fechaFinReq = new Date(fechaInicioReq.getTime() + duracionMinutos * 60000);

            const [citasExistentes] = await pool.query(`
                SELECT c.id, c.fecha_hora, s.duracion_minutos
                FROM citas c
                JOIN servicios s ON c.servicio_id = s.id
                WHERE c.usuario_id = ? AND c.estado IN ('pendiente', 'confirmada')
                AND DATE(c.fecha_hora) = DATE(?)
            `, [usuario_id, fecha_hora]);

            for (let cita of citasExistentes) {
                if (parseInt(cita.id) === parseInt(id)) continue; // Si es si mismo, ignorar

                const inicioExistente = new Date(cita.fecha_hora);
                const finExistente = new Date(inicioExistente.getTime() + cita.duracion_minutos * 60000);

                if (fechaInicioReq < finExistente && fechaFinReq > inicioExistente) {
                    return res.status(400).json({ error: 'El estilista seleccionado ya tiene una cita que se cruza con este horario modificado.' });
                }
            }
        }
        // --- FIN VALIDACIÓN ---

        const [result] = await pool.query(
            'UPDATE citas SET cliente_id = ?, servicio_id = ?, usuario_id = ?, fecha_hora = ?, estado = ? WHERE id = ?',
            [cliente_id, servicio_id, usuario_id, fecha_hora, estado || 'pendiente', id]
        );
        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'Cita no encontrada' });
        }
        res.json({ message: 'Cita actualizada correctamente' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Eliminar cita
router.delete('/:id', async (req, res) => {
    const { id } = req.params;
    try {
        const [result] = await pool.query('DELETE FROM citas WHERE id = ?', [id]);
        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'Cita no encontrada' });
        }
        res.json({ message: 'Cita eliminada correctamente' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

module.exports = router;
