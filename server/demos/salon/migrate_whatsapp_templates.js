const pool = require('./db');

async function migrateWhatsappTemplates() {
    try {
        console.log('Ampliando tabla configuracion_notificaciones con plantillas...');

        // Añadir columna de plantilla_nueva_cita
        try {
            await pool.query('ALTER TABLE configuracion_notificaciones ADD COLUMN plantilla_nueva_cita TEXT NULL');
            console.log('Columna plantilla_nueva_cita agregada.');
        } catch (alterErr) {
            if (alterErr.code === 'ER_DUP_FIELDNAME') {
                console.log('La columna plantilla_nueva_cita ya existe.');
            } else {
                throw alterErr;
            }
        }

        // Añadir columna de plantilla_cancelacion
        try {
            await pool.query('ALTER TABLE configuracion_notificaciones ADD COLUMN plantilla_cancelacion TEXT NULL');
            console.log('Columna plantilla_cancelacion agregada.');
        } catch (alterErr) {
            if (alterErr.code === 'ER_DUP_FIELDNAME') {
                console.log('La columna plantilla_cancelacion ya existe.');
            } else {
                throw alterErr;
            }
        }

        // Actualizar la primera fila con plantillas predeterminadas si están nulas
        const defaultNuevaCita = 'Hola [CLIENTE], tu cita para *[SERVICIO]* ha sido confirmada para el *[FECHA]*. ¡Te esperamos!';
        const defaultCancelacion = 'Hola [CLIENTE], te informamos que tu cita para *[SERVICIO]* del *[FECHA]* ha sido cancelada. Contáctanos para reprogramar.';

        await pool.query(`
            UPDATE configuracion_notificaciones 
            SET 
                plantilla_nueva_cita = COALESCE(plantilla_nueva_cita, ?),
                plantilla_cancelacion = COALESCE(plantilla_cancelacion, ?)
            WHERE id = 1
        `, [defaultNuevaCita, defaultCancelacion]);

        console.log('Plantillas predeterminadas configuradas.');
        console.log('Migración completada con éxito.');
        process.exit(0);
    } catch (error) {
        console.error('Error en migración de plantillas WhatsApp:', error);
        process.exit(1);
    }
}

migrateWhatsappTemplates();
