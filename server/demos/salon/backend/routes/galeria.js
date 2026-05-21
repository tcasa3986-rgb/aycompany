const express = require('express');
const router = express.Router();
const pool = require('../db');
const multer = require('multer');
const path = require('path');
const fs = require('fs');

// Configuración de Multer para guardar las imágenes localmente
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        const dir = path.join(__dirname, '..', 'uploads', 'evidencias');
        // Asegurarse de que el directorio exista
        if (!fs.existsSync(dir)) {
            fs.mkdirSync(dir, { recursive: true });
        }
        cb(null, dir);
    },
    filename: (req, file, cb) => {
        // Generar un nombre único: timestamp + número aleatorio + extensión original
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        const ext = path.extname(file.originalname);
        cb(null, 'evidence-' + uniqueSuffix + ext);
    }
});

// Filtro de archivos para aceptar solo imágenes
const fileFilter = (req, file, cb) => {
    if (file.mimetype.startsWith('image/')) {
        cb(null, true);
    } else {
        cb(new Error('El archivo no es una imagen válida.'), false);
    }
};

const upload = multer({ 
    storage: storage,
    limits: { fileSize: 5 * 1024 * 1024 }, // Limite de 5MB
    fileFilter: fileFilter
});

// SUBIR UNA FOTO AL HISTORIAL DEL CLIENTE
router.post('/upload', upload.single('foto'), async (req, res) => {
    try {
        if (!req.file) {
            return res.status(400).json({ error: 'No se incluyó ninguna foto en la petición.' });
        }

        const { cliente_id, cita_id, tipo, descripcion } = req.body;

        if (!cliente_id) {
            // Si hay error en validación, borramos el archivo que multer ya guardó
            fs.unlinkSync(req.file.path);
            return res.status(400).json({ error: 'El ID del cliente es obligatorio.' });
        }

        // Construir la URL relativa para servir el archivo estáticamente en el frontend
        const urlFoto = `/uploads/evidencias/${req.file.filename}`;
        
        // Manejar el caso donde cita_id viene vacío del form ("" string)
        const parsedCitaId = cita_id && cita_id !== "null" && cita_id !== "" ? parseInt(cita_id) : null;

        const [result] = await pool.query(
            `INSERT INTO galeria_clientes (cliente_id, cita_id, url_foto, tipo, descripcion) 
             VALUES (?, ?, ?, ?, ?)`,
            [cliente_id, parsedCitaId, urlFoto, tipo || 'general', descripcion || null]
        );

        res.status(201).json({
            id: result.insertId,
            cliente_id: cliente_id,
            cita_id: parsedCitaId,
            url_foto: urlFoto,
            tipo: tipo || 'general',
            descripcion: descripcion || null,
            mensaje: 'Foto subida y guardada exitosamente.'
        });
    } catch (err) {
        console.error('Error uploading photo:', err);
        // Intentar borrar la foto si hubo un error en la base de datos
        if (req.file && fs.existsSync(req.file.path)) {
            try { fs.unlinkSync(req.file.path); } catch (e) { console.error('Error cleanup file:', e); }
        }
        res.status(500).json({ error: err.message });
    }
});

// OBTENER LA GALERÍA DE UN CLIENTE ESPECÍFICO
router.get('/cliente/:clienteId', async (req, res) => {
    const { clienteId } = req.params;
    try {
        const [rows] = await pool.query(`
            SELECT g.*, c.fecha_hora as fecha_cita, s.nombre as servicio_nombre
            FROM galeria_clientes g
            LEFT JOIN citas c ON g.cita_id = c.id
            LEFT JOIN servicios s ON c.servicio_id = s.id
            WHERE g.cliente_id = ?
            ORDER BY g.fecha_subida DESC
        `, [clienteId]);
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// ELIMINAR UNA FOTO DEL HISTORIAL
router.delete('/:id', async (req, res) => {
    const { id } = req.params;
    try {
        // Primero, encontrar la foto para borrar el archivo físico
        const [rows] = await pool.query('SELECT url_foto FROM galeria_clientes WHERE id = ?', [id]);
        
        if (rows.length === 0) {
            return res.status(404).json({ error: 'Foto no encontrada.' });
        }

        const urlFoto = rows[0].url_foto;
        // urlFoto es algo tipo '/uploads/evidencias/evidence-1234.jpg'
        // Necesitamos construir la ruta absoluta eliminando el primer '/' si lo hay
        const filePath = path.join(__dirname, '..', urlFoto.startsWith('/') ? urlFoto.slice(1) : urlFoto);
        
        // Borrar archivo físico si existe
        if (fs.existsSync(filePath)) {
            fs.unlinkSync(filePath);
        }

        // Borrar registro de la BD
        await pool.query('DELETE FROM galeria_clientes WHERE id = ?', [id]);

        res.json({ message: 'Foto eliminada correctamente de la galería.' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

module.exports = router;
