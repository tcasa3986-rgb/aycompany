const pool = require('./db');

async function migrateGastos() {
    try {
        console.log('Creando tabla de gastos...');

        await pool.query(`
            CREATE TABLE IF NOT EXISTS gastos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                concepto VARCHAR(255) NOT NULL,
                descripcion TEXT,
                monto DECIMAL(10, 2) NOT NULL,
                fecha DATE NOT NULL,
                categoria ENUM('servicios', 'insumos', 'nomina', 'mantenimiento', 'otros') DEFAULT 'otros',
                usuario_id INT,
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
            )
        `);

        console.log('Tabla de gastos creada exitosamente.');

        process.exit(0);
    } catch (error) {
        console.error('Error al crear la tabla de gastos:', error);
        process.exit(1);
    }
}

migrateGastos();
