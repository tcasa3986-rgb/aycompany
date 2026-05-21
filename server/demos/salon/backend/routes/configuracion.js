const express = require('express');
const router = express.Router();
const pool = require('../db');
const authMiddleware = require('../middleware/authMiddleware');
const multer = require('multer');
const path = require('path');
const fs = require('fs');

const adminMiddleware = (req, res, next) => {
    if (req.user && req.user.rol === 'admin') {
        next();
    } else {
        res.status(403).json({ error: 'Acceso denegado. Se requieren permisos de administrador.' });
    }
};

// Configuración de Multer para subir la imagen del logo
const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        const uploadDir = path.join(__dirname, '../uploads');
        if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir, { recursive: true });
        }
        cb(null, uploadDir);
    },
    filename: function (req, file, cb) {
        // Renombrar a logo_empresa + extensión
        const ext = path.extname(file.originalname);
        cb(null, 'logo_empresa_' + Date.now() + ext);
    }
});

const upload = multer({
    storage: storage,
    limits: { fileSize: 5 * 1024 * 1024 }, // 5MB max
    fileFilter: (req, file, cb) => {
        if (file.mimetype.startsWith('image/')) {
            cb(null, true);
        } else {
            cb(new Error('Solo se permiten imágenes.'));
        }
    }
});

// GET: Obtener la configuración actual (Pública o Protegida, sugerido proteger para usuarios)
// Usamos solo id=1 porque es Singleton
router.get('/', authMiddleware, async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM configuracion WHERE id = 1');
        if (rows.length === 0) {
            return res.status(404).json({ error: 'Configuración no encontrada' });
        }
        res.json(rows[0]);
    } catch (error) {
        console.error('Error al obtener configuración:', error);
        res.status(500).json({ error: 'Error del servidor' });
    }
});

// GET PUBLIC (Opcional): Para el login si se requiere logo o nombre de la empresa sin auth
router.get('/public', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT nombre_empresa, logo_url, simbolo_moneda FROM configuracion WHERE id = 1');
        if (rows.length === 0) {
            return res.status(404).json({ error: 'Configuración no encontrada' });
        }
        res.json(rows[0]);
    } catch (error) {
        console.error('Error al obtener configuración:', error);
        res.status(500).json({ error: 'Error del servidor' });
    }
});


// PUT: Actualizar configuración (Solo admins)
router.put('/', authMiddleware, adminMiddleware, upload.single('logo'), async (req, res) => {
    try {
        const { nombre_empresa, simbolo_moneda, telefono, direccion } = req.body;
        let updateQuery = 'UPDATE configuracion SET nombre_empresa = ?, simbolo_moneda = ?, telefono = ?, direccion = ?';
        let queryParams = [nombre_empresa, simbolo_moneda, telefono, direccion];

        // Verificar si se subió un nuevo logo
        if (req.file) {
            // Guardar la URL relativa para que el frontend pueda consumirla
            const logo_url = '/uploads/' + req.file.filename;
            updateQuery += ', logo_url = ?';
            queryParams.push(logo_url);
        }

        updateQuery += ' WHERE id = 1';

        await pool.query(updateQuery, queryParams);

        // Devolver la nueva configuración
        const [updated] = await pool.query('SELECT * FROM configuracion WHERE id = 1');
        res.json(updated[0]);

    } catch (error) {
        console.error('Error al actualizar configuración:', error);
        if (error instanceof multer.MulterError) {
            return res.status(400).json({ error: 'Error subiendo la imagen: ' + error.message });
        }
        res.status(500).json({ error: 'Error del servidor' });
    }
});

module.exports = router;
