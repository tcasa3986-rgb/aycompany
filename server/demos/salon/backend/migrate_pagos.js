const pool = require('./db');

async function migratePagos() {
    try {
        console.log('Creando tabla pagos...');
        await pool.query(`
            CREATE TABLE IF NOT EXISTS pagos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cita_id INT NOT NULL,
                monto DECIMAL(10, 2) NOT NULL,
                metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia', 'suscripcion') DEFAULT 'efectivo',
                fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE CASCADE
            )
        `);
        console.log('Tabla pagos creada correctamente.');
        
        console.log('Migración de Abonos (Pagos) completada con éxito.');
        process.exit(0);
    } catch (error) {
        console.error('Error en migración de pagos:', error);
        process.exit(1);
    }
}

migratePagos();
