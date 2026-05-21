const mysql = require('mysql2/promise');

async function runMigration() {
    try {
        const connection = await mysql.createConnection({
            host: 'localhost',
            user: 'root',
            password: '',
            database: 'salon_belleza_db'
        });

        const createProductosTable = `
      CREATE TABLE IF NOT EXISTS productos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(150) NOT NULL,
        descripcion TEXT,
        precio DECIMAL(10, 2) NOT NULL,
        stock INT DEFAULT 0,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    `;

        await connection.query(createProductosTable);
        console.log('Tabla Productos (Inventario) creada exitosamente.');

        await connection.end();
        process.exit(0);
    } catch (error) {
        console.error('Error al realizar la migración:', error);
        process.exit(1);
    }
}

runMigration();
