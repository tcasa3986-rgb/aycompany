require('dotenv').config();
const express = require('express');
const cors = require('cors');

const app = express();
const PORT = process.env.PORT || 5000;

app.use(cors());
app.use(express.json());

// Main Entry Point Route
app.get('/', (req, res) => {
    res.send('API Salón de Belleza Funcionando!');
});

// Middleware de autenticación
const authMiddleware = require('./middleware/authMiddleware');

const path = require('path');
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

const clientesRoutes = require('./routes/clientes');
const serviciosRoutes = require('./routes/servicios');
const citasRoutes = require('./routes/citas');
const dashboardRoutes = require('./routes/dashboard');
const authRoutes = require('./routes/auth');
const usuariosRoutes = require('./routes/usuarios');
const ventasRoutes = require('./routes/ventas');
const inventarioRoutes = require('./routes/inventario');
const reportesRoutes = require('./routes/reportes');
const gastosRoutes = require('./routes/gastos');
const configuracionRoutes = require('./routes/configuracion');
const suscripcionesRoutes = require('./routes/suscripciones');
const pagosRoutes = require('./routes/pagos');
const galeriaRoutes = require('./routes/galeria');
const mantenimientoRoutes = require('./routes/mantenimiento');
const notificacionesRoutes = require('./routes/notificaciones');
const databaseRoutes = require('./routes/database');

app.use('/api/auth', authRoutes); // Público
app.use('/api/clientes', authMiddleware, clientesRoutes); // Protegido
app.use('/api/servicios', authMiddleware, serviciosRoutes); // Protegido
app.use('/api/citas', authMiddleware, citasRoutes); // Protegido
app.use('/api/dashboard', authMiddleware, dashboardRoutes); // Protegido
app.use('/api/usuarios', authMiddleware, usuariosRoutes); // Protegido
app.use('/api/ventas', authMiddleware, ventasRoutes); // Protegido
app.use('/api/inventario', authMiddleware, inventarioRoutes); // Protegido
app.use('/api/reportes', authMiddleware, reportesRoutes); // Protegido
app.use('/api/gastos', authMiddleware, gastosRoutes); // Protegido
app.use('/api/configuracion', configuracionRoutes); // La protección es por ruta dentro del controlador
app.use('/api/suscripciones', authMiddleware, suscripcionesRoutes); // Protegido
app.use('/api/pagos', authMiddleware, pagosRoutes); // Protegido
app.use('/api/galeria', authMiddleware, galeriaRoutes); // Protegido
app.use('/api/mantenimiento', authMiddleware, mantenimientoRoutes); // Protegido
app.use('/api/notificaciones', authMiddleware, notificacionesRoutes); // Protegido
app.use('/api/database', authMiddleware, databaseRoutes); // Protegido

app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
