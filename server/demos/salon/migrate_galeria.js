const pool = require('./db');
const fs = require('fs');
const path = require('path');

async function migrateGaleria() {
    try {
        console.log('Creando tabla galeria_clientes...');
        await pool.query(`
            CREATE TABLE IF NOT EXISTS galeria_clientes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cliente_id INT NOT NULL,
                cita_id INT NULL,
                url_foto VARCHAR(255) NOT NULL,
                tipo ENUM('antes', 'despues', 'general') DEFAULT 'general',
                descripcion TEXT NULL,
                fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
                FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE SET NULL
            )
        `);
        console.log('Tabla galeria_clientes creada correctamente.');

        // Ensure the upload directory exists
        const uploadDir = path.join(__dirname, 'uploads', 'evidencias');
        if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir, { recursive: true });
            console.log('Directorio de subidas (uploads/evidencias) credo.');
        } else {
            console.log('Directorio de subidas ya existe.');
        }

        console.log('Migración de Galería completada con éxito.');
        process.exit(0);
    } catch (error) {
        console.error('Error en migración de galeria:', error);
        process.exit(1);
    }
}

migrateGaleria();
