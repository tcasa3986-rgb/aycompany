const mysql = require('mysql2/promise');

async function migrateConfig() {
    try {
        const connection = await mysql.createConnection({
            host: 'localhost',
            user: 'root',
            password: '',
            database: 'salon_belleza_db'
        });

        console.log('Creando tabla de configuracion...');

        await connection.query(`
            CREATE TABLE IF NOT EXISTS configuracion (
                id INT PRIMARY KEY DEFAULT 1,
                nombre_empresa VARCHAR(150) NOT NULL DEFAULT 'Belleza Admin',
                logo_url VARCHAR(255),
                simbolo_moneda VARCHAR(10) NOT NULL DEFAULT '$',
                telefono VARCHAR(50) DEFAULT '',
                direccion TEXT,
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CHECK (id = 1) -- Asegura que solo haya un único registro (Singleton)
            )
        `);

        console.log('Tabla de configuracion creada exitosamente.');

        // Insert default record if not exists
        const [rows] = await connection.query('SELECT COUNT(*) as count FROM configuracion WHERE id = 1');
        if (rows[0].count === 0) {
            await connection.query(`
                INSERT INTO configuracion (id, nombre_empresa, simbolo_moneda, telefono, direccion) 
                VALUES (1, 'Salón de Belleza Elegance', '$', '+1 234 567 8900', '123 Beauty St, Fashion City')
            `);
            console.log('Registro de configuración por defecto insertado.');
        } else {
            console.log('El registro de configuración ya existe.');
        }

        await connection.end();
        process.exit(0);
    } catch (error) {
        console.error('Error al realizar la migración de configuración:', error);
        process.exit(1);
    }
}

migrateConfig();
