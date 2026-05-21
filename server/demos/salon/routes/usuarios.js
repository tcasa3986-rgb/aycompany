const express = require('express');
const router = express.Router();
const pool = require('../db');
const bcrypt = require('bcryptjs');

// Obtener todos los usuarios
router.get('/', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT id, nombre, email, rol, creado_en FROM usuarios ORDER BY creado_en DESC');
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Crear usuario
router.post('/', async (req, res) => {
    const { nombre, email, password, rol } = req.body;
    try {
        // Hashear la contraseña antes de guardarla
        const salt = await bcrypt.genSalt(10);
        const hashedPassword = await bcrypt.hash(password, salt);

        const [result] = await pool.query(
            'INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)',
            [nombre, email, hashedPassword, rol || 'recepcionista']
        );
        res.status(201).json({ id: result.insertId, nombre, email, rol });
    } catch (err) {
        // Error de email duplicado u otros
        if (err.code === 'ER_DUP_ENTRY') {
            return res.status(400).json({ error: 'El email ya está registrado' });
        }
        res.status(500).json({ error: err.message });
    }
});

// Actualizar usuario
router.put('/:id', async (req, res) => {
    const { id } = req.params;
    const { nombre, email, password, rol } = req.body;
    try {
        let query;
        let params;

        // Si se provee una nueva contraseña, actualizarla (encriptada). Si no, mantener la actual.
        if (password && password.trim() !== '') {
            const salt = await bcrypt.genSalt(10);
            const hashedPassword = await bcrypt.hash(password, salt);
            query = 'UPDATE usuarios SET nombre = ?, email = ?, password = ?, rol = ? WHERE id = ?';
            params = [nombre, email, hashedPassword, rol, id];
        } else {
            query = 'UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?';
            params = [nombre, email, rol, id];
        }

        const [result] = await pool.query(query, params);

        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'Usuario no encontrado' });
        }
        res.json({ message: 'Usuario actualizado correctamente' });
    } catch (err) {
        if (err.code === 'ER_DUP_ENTRY') {
            return res.status(400).json({ error: 'El email ya está en uso por otro usuario' });
        }
        res.status(500).json({ error: err.message });
    }
});

// Eliminar usuario
router.delete('/:id', async (req, res) => {
    const { id } = req.params;
    try {
        // Evitar que el usuario se elimine a sí mismo
        if (req.user && parseInt(req.user.id) === parseInt(id)) {
            return res.status(403).json({ error: 'No puedes eliminar tu propia cuenta' });
        }

        const [result] = await pool.query('DELETE FROM usuarios WHERE id = ?', [id]);
        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'Usuario no encontrado' });
        }
        res.json({ message: 'Usuario eliminado correctamente' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

module.exports = router;
