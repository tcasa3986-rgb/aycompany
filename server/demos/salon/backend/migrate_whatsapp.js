const pool = require('./db');

async function migrateWhatsapp() {
    try {
        console.log('Agregando campo whatsapp_apikey a tabla clientes...');
        
        // Use ALTER TABLE, but wrap in try-catch in case it already exists
        try {
            await pool.query('ALTER TABLE clientes ADD COLUMN whatsapp_apikey VARCHAR(100) NULL');
            console.log('Campo whatsapp_apikey agregado a clientes.');
        } catch (alterErr) {
            // Error 1060: Duplicate column name
            if (alterErr.code === 'ER_DUP_FIELDNAME') {
                console.log('La columna whatsapp_apikey ya existe en clientes, omitiendo.');
            } else {
                throw alterErr;
            }
        }

        console.log('Creando tabla de configuracion_notificaciones...');
        await pool.query(`
            CREATE TABLE IF NOT EXISTS configuracion_notificaciones (
                id INT AUTO_INCREMENT PRIMARY KEY,
                notificar_nueva_cita BOOLEAN DEFAULT TRUE,
                notificar_cancelacion BOOLEAN DEFAULT TRUE,
                actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        `);

        // Insert a default row if none exists
        const [rows] = await pool.query('SELECT COUNT(*) as count FROM configuracion_notificaciones');
        if (rows[0].count === 0) {
            await pool.query('INSERT INTO configuracion_notificaciones (notificar_nueva_cita, notificar_cancelacion) VALUES (TRUE, TRUE)');
            console.log('Configuración predeterminada de notificaciones insertada.');
        }

        console.log('Migración de Notificaciones WhatsApp completada con éxito.');
        process.exit(0);
    } catch (error) {
        console.error('Error en migración de WhatsApp:', error);
        process.exit(1);
    }
}

migrateWhatsapp();
