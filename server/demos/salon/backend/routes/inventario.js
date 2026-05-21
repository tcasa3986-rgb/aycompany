const express = require('express');
const router = express.Router();
const pool = require('../db');

// Obtener todos los productos
router.get('/', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM productos ORDER BY nombre ASC');
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Crear producto
router.post('/', async (req, res) => {
    const { nombre, descripcion, precio, stock } = req.body;
    try {
        const [result] = await pool.query(
            'INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)',
            [nombre, descripcion, precio, stock || 0]
        );
        res.status(201).json({ id: result.insertId, nombre, descripcion, precio, stock });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Actualizar producto
router.put('/:id', async (req, res) => {
    const { id } = req.params;
    const { nombre, descripcion, precio, stock } = req.body;
    try {
        const [result] = await pool.query(
            'UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?',
            [nombre, descripcion, precio, stock, id]
        );
        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'Producto no encontrado' });
        }
        res.json({ message: 'Producto actualizado correctamente' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Eliminar producto
router.delete('/:id', async (req, res) => {
    const { id } = req.params;
    try {
        const [result] = await pool.query('DELETE FROM productos WHERE id = ?', [id]);
        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'Producto no encontrado' });
        }
        res.json({ message: 'Producto eliminado correctamente' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

module.exports = router;
