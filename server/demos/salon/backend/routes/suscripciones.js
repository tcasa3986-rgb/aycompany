const express = require('express');
const router = express.Router();
const pool = require('../db');

// --- PLANES DE SUSCRIPCIÓN ---

// Obtener todos los planes
router.get('/planes', async (req, res) => {
    try {
        const query = 'SELECT * FROM suscripcion_planes ORDER BY id DESC';
        const [planes] = await pool.query(query);
        res.json(planes);
    } catch (error) {
        console.error('Error al obtener planes:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Crear un nuevo plan
router.post('/planes', async (req, res) => {
    const { nombre, descripcion, precio, duracion_dias, servicios_incluidos } = req.body;

    if (!nombre || precio === undefined || !duracion_dias) {
        return res.status(400).json({ error: 'Faltan campos obligatorios' });
    }

    try {
        const query = `
            INSERT INTO suscripcion_planes (nombre, descripcion, precio, duracion_dias, servicios_incluidos) 
            VALUES (?, ?, ?, ?, ?)
        `;
        const values = [nombre, descripcion, precio, duracion_dias, servicios_incluidos || 0];

        const [result] = await pool.query(query, values);
        res.status(201).json({ id: result.insertId, message: 'Plan creado correctamente' });
    } catch (error) {
        console.error('Error al crear plan:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Eliminar un plan
router.delete('/planes/:id', async (req, res) => {
    const { id } = req.params;
    try {
        // Only allow deleting if it's not being heavily used, or let CASCADE handle it?
        // Let's just delete it directly (will cascade to cliente_suscripciones)
        await pool.query('DELETE FROM suscripcion_planes WHERE id = ?', [id]);
        res.json({ message: 'Plan eliminado correctamente' });
    } catch (error) {
        console.error('Error al eliminar plan:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});


// --- SUSCRIPCIONES DE CLIENTES ---

// Obtener todas las suscripciones de clientes
router.get('/clientes', async (req, res) => {
    try {
        const query = `
            SELECT cs.*, c.nombre as cliente_nombre, p.nombre as plan_nombre, p.duracion_dias
            FROM cliente_suscripciones cs
            JOIN clientes c ON cs.cliente_id = c.id
            JOIN suscripcion_planes p ON cs.plan_id = p.id
            ORDER BY cs.id DESC
        `;
        const [suscripciones] = await pool.query(query);
        res.json(suscripciones);
    } catch (error) {
        console.error('Error al obtener suscripciones:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Asignar un plan a un cliente
router.post('/clientes', async (req, res) => {
    const { cliente_id, plan_id, fecha_inicio } = req.body;

    if (!cliente_id || !plan_id || !fecha_inicio) {
        return res.status(400).json({ error: 'Faltan campos obligatorios' });
    }

    try {
        // Get plan duration
        const [planes] = await pool.query('SELECT duracion_dias FROM suscripcion_planes WHERE id = ?', [plan_id]);
        if (planes.length === 0) {
            return res.status(404).json({ error: 'Plan no encontrado' });
        }
        const duracionDias = planes[0].duracion_dias;

        // Calculate end date
        const fechaInicioDate = new Date(fecha_inicio);
        const fechaFinDate = new Date(fechaInicioDate);
        fechaFinDate.setDate(fechaFinDate.getDate() + duracionDias);
        const fechaFinStr = fechaFinDate.toISOString().split('T')[0];

        const query = `
            INSERT INTO cliente_suscripciones (cliente_id, plan_id, fecha_inicio, fecha_fin, estado) 
            VALUES (?, ?, ?, ?, 'activa')
        `;
        const values = [cliente_id, plan_id, fecha_inicio, fechaFinStr];

        const [result] = await pool.query(query, values);
        res.status(201).json({ id: result.insertId, message: 'Suscripción asignada correctamente', fecha_fin: fechaFinStr });
    } catch (error) {
        console.error('Error al asignar suscripción:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Cancelar una suscripción de cliente
router.put('/clientes/:id/estado', async (req, res) => {
    const { id } = req.params;
    const { estado } = req.body;

    if (!estado || !['activa', 'vencida', 'cancelada'].includes(estado)) {
        return res.status(400).json({ error: 'Estado inválido' });
    }

    try {
        await pool.query('UPDATE cliente_suscripciones SET estado = ? WHERE id = ?', [estado, id]);
        res.json({ message: 'Estado actualizado correctamente' });
    } catch (error) {
        console.error('Error al actualizar estado:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

module.exports = router;
