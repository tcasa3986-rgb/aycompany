const pool = require('./db');

async function migrateSuscripciones() {
    try {
        console.log('Creando tabla de suscripcion_planes...');
        
        await pool.query(`
            CREATE TABLE IF NOT EXISTS suscripcion_planes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                descripcion TEXT,
                precio DECIMAL(10, 2) NOT NULL,
                duracion_dias INT NOT NULL,
                servicios_incluidos INT DEFAULT 0,
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        `);

        console.log('Tabla suscripcion_planes creada correctamente.');

        console.log('Creando tabla de cliente_suscripciones...');
        await pool.query(`
            CREATE TABLE IF NOT EXISTS cliente_suscripciones (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cliente_id INT NOT NULL,
                plan_id INT NOT NULL,
                fecha_inicio DATE NOT NULL,
                fecha_fin DATE NOT NULL,
                estado ENUM('activa', 'vencida', 'cancelada') DEFAULT 'activa',
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
                FOREIGN KEY (plan_id) REFERENCES suscripcion_planes(id) ON DELETE CASCADE
            )
        `);

        console.log('Tabla cliente_suscripciones creada correctamente.');
        
        process.exit(0);
    } catch (error) {
        console.error('Error al crear las tablas de suscripciones:', error);
        process.exit(1);
    }
}

migrateSuscripciones();
