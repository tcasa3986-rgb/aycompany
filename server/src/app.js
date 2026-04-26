require('dotenv').config();
const express   = require('express');
const cors      = require('cors');
const bcrypt    = require('bcryptjs');
const path      = require('path');
const fs        = require('fs');
const sequelize = require('./config/db');
const { Usuario } = require('./models');

const app = express();
const isProd = process.env.NODE_ENV === 'production';

app.use(cors({
    origin: isProd ? '*' : ['http://localhost:4000'],
    credentials: true
}));
app.use(express.json());

// En producción servir el frontend compilado
if (isProd) {
    const clientDist = path.join(__dirname, '../../client/dist');
    if (fs.existsSync(clientDist)) {
        app.use(express.static(clientDist));
    }
}

app.use('/api/auth',      require('./routes/authRoutes'));
app.use('/api/clientes',  require('./routes/clientesRoutes'));
app.use('/api/productos', require('./routes/productosRoutes'));
app.use('/api/licencias', require('./routes/licenciasRoutes'));
app.use('/api/pagos',     require('./routes/pagosRoutes'));
app.use('/api/dashboard', require('./routes/dashboardRoutes'));

app.get('/api/health', (_, res) => res.json({ ok: true }));

// En producción redirigir todo lo demás al index.html del React
if (isProd) {
    app.get('*', (req, res) => {
        const index = path.join(__dirname, '../../client/dist/index.html');
        if (fs.existsSync(index)) res.sendFile(index);
        else res.status(404).send('Frontend no compilado');
    });
}

async function seedAdmin() {
    const existe = await Usuario.findOne({ where: { email: process.env.ADMIN_EMAIL } });
    if (!existe) {
        const hash = await bcrypt.hash(process.env.ADMIN_PASSWORD, 10);
        await Usuario.create({ nombre: 'Administrador', email: process.env.ADMIN_EMAIL, password: hash, rol: 'admin' });
        console.log(`✅ Usuario admin creado: ${process.env.ADMIN_EMAIL}`);
    }
}

const PORT = process.env.PORT || 5000;

sequelize.sync()
    .then(seedAdmin)
    .then(() => app.listen(PORT, () => console.log(`🚀 Plataforma corriendo en http://localhost:${PORT}`)))
    .catch(err => { console.error('Error al iniciar:', err.message); process.exit(1); });
