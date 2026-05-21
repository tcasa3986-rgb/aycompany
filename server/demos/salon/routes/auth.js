const express = require('express');
const router = express.Router();
const pool = require('../db');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');

// Usar una clave secreta por defecto si no está en el .env
const JWT_SECRET = process.env.JWT_SECRET || 'salonbelleza_secreto_super_seguro_123';

// Login route
router.post('/login', async (req, res) => {
    const { email, password } = req.body;

    try {
        // Buscar el usuario por email
        const [users] = await pool.query('SELECT * FROM usuarios WHERE email = ?', [email]);

        if (users.length === 0) {
            return res.status(401).json({ error: 'Credenciales inválidas' });
        }

        const user = users[0];

        // Usar bcryptjs para comparar la contraseña proporcionada con el hash de la BD
        const isMatch = await bcrypt.compare(password, user.password);

        // Si no hay match (Y para mantener la compatibilidad hacia atrás en caso de que la BD aún
        // tenga admin123 en texto plano por el setup_db, validemos si es 'admin123' texto plano temporalmente)
        if (!isMatch && password !== user.password) {
            return res.status(401).json({ error: 'Credenciales inválidas' });
        }

        // Crear el token JWT
        const token = jwt.sign(
            { id: user.id, rol: user.rol },
            JWT_SECRET,
            { expiresIn: '8h' }
        );

        // Devolver token y datos seguros del usuario
        res.json({
            message: 'Login exitoso',
            token,
            user: {
                id: user.id,
                nombre: user.nombre,
                email: user.email,
                rol: user.rol
            }
        });

    } catch (error) {
        console.error('Error en el login:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

module.exports = router;
