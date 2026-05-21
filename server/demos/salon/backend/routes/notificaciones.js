const express = require('express');
const router = express.Router();
const pool = require('../db');

// GET: Obtener la configuración actual de notificaciones y plantillas
router.get('/config', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM configuracion_notificaciones LIMIT 1');
        if (rows.length === 0) {
            return res.status(404).json({ error: 'Configuración no encontrada' });
        }
        res.json(rows[0]);
    } catch (error) {
        console.error('Error al obtener configuración de notificaciones:', error);
        res.status(500).json({ error: 'Error del servidor' });
    }
});

// PUT: Actualizar configuración de notificaciones (solo admin)
// Se asume que authMiddleware y adminMiddleware validan el acceso antes en server.js o mediante roles
router.put('/config', async (req, res) => {
    try {
        const { notificar_nueva_cita, notificar_cancelacion, plantilla_nueva_cita, plantilla_cancelacion } = req.body;
        
        const query = `
            UPDATE configuracion_notificaciones 
            SET notificar_nueva_cita = ?, 
                notificar_cancelacion = ?, 
                plantilla_nueva_cita = ?, 
                plantilla_cancelacion = ? 
            WHERE id = 1
        `;
        
        await pool.query(query, [
            notificar_nueva_cita, 
            notificar_cancelacion, 
            plantilla_nueva_cita, 
            plantilla_cancelacion
        ]);

        const [updated] = await pool.query('SELECT * FROM configuracion_notificaciones LIMIT 1');
        res.json(updated[0]);
    } catch (error) {
        console.error('Error al actualizar configuración de notificaciones:', error);
        res.status(500).json({ error: 'Error del servidor' });
    }
});

module.exports = router;
