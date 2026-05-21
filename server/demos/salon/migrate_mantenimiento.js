const pool = require('./db');

async function migrateMantenimiento() {
    try {
        console.log('Creando tabla de mantenimiento_fisico...');

        await pool.query(`
            CREATE TABLE IF NOT EXISTS mantenimiento_fisico (
                id INT AUTO_INCREMENT PRIMARY KEY,
                equipo VARCHAR(255) NOT NULL,
                descripcion TEXT,
                fecha_mantenimiento DATE NOT NULL,
                proxima_fecha DATE,
                costo DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
                estado ENUM('Pendiente', 'En Proceso', 'Completado') DEFAULT 'Pendiente',
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        `);

        console.log('Tabla de mantenimiento_fisico creada exitosamente.');

        process.exit(0);
    } catch (error) {
        console.error('Error al crear la tabla de mantenimiento_fisico:', error);
        process.exit(1);
    }
}

migrateMantenimiento();
