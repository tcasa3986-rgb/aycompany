const express = require('express');
const router = express.Router();
const pool = require('../db');

// Obtener servicios
router.get('/', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM servicios ORDER BY nombre ASC');
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Crear servicio
router.post('/', async (req, res) => {
    const { nombre, descripcion, precio, duracion_minutos } = req.body;
    try {
        const [result] = await pool.query('INSERT INTO servicios (nombre, descripcion, precio, duracion_minutos) VALUES (?, ?, ?, ?)', [nombre, descripcion, precio, duracion_minutos]);
        res.status(201).json({ id: result.insertId, nombre, descripcion, precio, duracion_minutos });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Actualizar servicio
router.put('/:id', async (req, res) => {
    const { id } = req.params;
    const { nombre, descripcion, precio, duracion_minutos } = req.body;
    try {
        const [result] = await pool.query(
            'UPDATE servicios SET nombre = ?, descripcion = ?, precio = ?, duracion_minutos = ? WHERE id = ?',
            [nombre, descripcion, precio, duracion_minutos, id]
        );
        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'Servicio no encontrado' });
        }
        res.json({ message: 'Servicio actualizado correctamente' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Eliminar servicio
router.delete('/:id', async (req, res) => {
    const { id } = req.params;
    try {
        const [result] = await pool.query('DELETE FROM servicios WHERE id = ?', [id]);
        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'Servicio no encontrado' });
        }
        res.json({ message: 'Servicio eliminado correctamente' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

module.exports = router;
