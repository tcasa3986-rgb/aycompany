const express = require('express');
const router = express.Router();
const pool = require('../db');

// GET: Obtener todos los mantenimientos
router.get('/', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM mantenimiento_fisico ORDER BY fecha_mantenimiento DESC, id DESC');
        res.json(rows);
    } catch (error) {
        console.error('Error fetching mantenimientos:', error);
        res.status(500).json({ error: 'Error al obtener los mantenimientos' });
    }
});

// POST: Crear un nuevo mantenimiento
router.post('/', async (req, res) => {
    try {
        const { equipo, descripcion, fecha_mantenimiento, proxima_fecha, costo, estado } = req.body;
        
        if (!equipo || !fecha_mantenimiento) {
            return res.status(400).json({ error: 'El equipo y la fecha_mantenimiento son obligatorios.' });
        }

        const query = `
            INSERT INTO mantenimiento_fisico 
            (equipo, descripcion, fecha_mantenimiento, proxima_fecha, costo, estado) 
            VALUES (?, ?, ?, ?, ?, ?)
        `;
        const values = [
            equipo, 
            descripcion || null, 
            fecha_mantenimiento, 
            proxima_fecha || null, 
            costo || 0.00,
            estado || 'Pendiente'
        ];

        const [result] = await pool.query(query, values);
        
        res.status(201).json({ id: result.insertId, message: 'Mantenimiento registrado con éxito' });
    } catch (error) {
        console.error('Error creating mantenimiento:', error);
        res.status(500).json({ error: 'Error al registrar el mantenimiento' });
    }
});

// PUT: Actualizar un mantenimiento existente
router.put('/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { equipo, descripcion, fecha_mantenimiento, proxima_fecha, costo, estado } = req.body;

        const query = `
            UPDATE mantenimiento_fisico 
            SET equipo = ?, descripcion = ?, fecha_mantenimiento = ?, proxima_fecha = ?, costo = ?, estado = ?
            WHERE id = ?
        `;
        const values = [
            equipo, 
            descripcion || null, 
            fecha_mantenimiento, 
            proxima_fecha || null, 
            costo || 0.00,
            estado || 'Pendiente',
            id
        ];

        await pool.query(query, values);
        res.json({ message: 'Mantenimiento actualizado con éxito' });
    } catch (error) {
        console.error('Error updating mantenimiento:', error);
        res.status(500).json({ error: 'Error al actualizar el mantenimiento' });
    }
});

// DELETE: Eliminar un mantenimiento
router.delete('/:id', async (req, res) => {
    try {
        const { id } = req.params;
        await pool.query('DELETE FROM mantenimiento_fisico WHERE id = ?', [id]);
        res.json({ message: 'Mantenimiento eliminado con éxito' });
    } catch (error) {
        console.error('Error deleting mantenimiento:', error);
        res.status(500).json({ error: 'Error al eliminar el mantenimiento' });
    }
});

module.exports = router;
