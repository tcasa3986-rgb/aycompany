const axios = require('axios');
const pool = require('../db');

/**
 * Sends a WhatsApp message using the CallMeBot API.
 * The client must have sent the opt-in message to the bot to get their apikey.
 * 
 * @param {string} phone - The recipient's phone number exactly as registered (with country code, usually starting with +).
 * @param {string} apikey - The CallMeBot apikey provided to the client.
 * @param {string} text - The message to send.
 */
async function sendWhatsAppMessage(phone, apikey, text) {
    try {
        if (!phone || !apikey || !text) {
            console.log('Skipping WhatsApp message: Missing phone, apikey, or text.');
            return false;
        }

        // URL encode the text
        const encodedText = encodeURIComponent(text);
        
        // Remove +, spaces, dashes from phone for the API format if needed, 
        // CallMeBot usually prefers international formatted number directly (e.g., +34123123123)
        // Let's pass it as is but URL encoded just in case the + causes issues
        const encodedPhone = encodeURIComponent(phone);

        const url = `https://api.callmebot.com/whatsapp.php?phone=${encodedPhone}&text=${encodedText}&apikey=${apikey}`;

        const response = await axios.get(url);
        
        if (response.status === 200) {
            console.log(`WhatsApp message sent successfully to ${phone}`);
            return true;
        } else {
            console.error(`WhatsApp API returned status: ${response.status}`);
            return false;
        }
    } catch (error) {
        console.error('Error sending WhatsApp message via CallMeBot:', error.message);
        return false;
    }
}

/**
 * Checks system preferences and sends an appointment confirmation.
 */
async function triggerNewAppointmentAlert(citaId) {
    try {
        // 1. Check if notifications are enabled globally and get the template
        const [configRows] = await pool.query('SELECT notificar_nueva_cita, plantilla_nueva_cita FROM configuracion_notificaciones LIMIT 1');
        if (configRows.length > 0 && !configRows[0].notificar_nueva_cita) {
            return; // Globally disabled
        }
        const plantilla = configRows[0].plantilla_nueva_cita || 'Hola [CLIENTE], tu cita para *[SERVICIO]* ha sido confirmada para el *[FECHA]*. ¡Te esperamos!';

        // 2. Fetch appointment details and client details
        const query = `
            SELECT c.*, cl.nombre as cliente_nombre, cl.telefono, cl.whatsapp_apikey, s.nombre as servicio_nombre
            FROM citas c
            JOIN clientes cl ON c.cliente_id = cl.id
            JOIN servicios s ON c.servicio_id = s.id
            WHERE c.id = ?
        `;
        const [citas] = await pool.query(query, [citaId]);
        
        if (citas.length === 0) return;
        const cita = citas[0];

        // 3. Check if client has apikey and phone
        if (!cita.telefono || !cita.whatsapp_apikey) {
            return; // Client cannot receive messages via CallMeBot
        }

        // 4. Format Date
        const fecha = new Date(cita.fecha_hora).toLocaleString('es-ES', { 
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' 
        });

        // 5. Compose Message using Template
        let message = plantilla;
        message = message.replace(/\[CLIENTE\]/g, cita.cliente_nombre);
        message = message.replace(/\[SERVICIO\]/g, cita.servicio_nombre);
        message = message.replace(/\[FECHA\]/g, fecha);

        // 6. Send
        await sendWhatsAppMessage(cita.telefono, cita.whatsapp_apikey, message);

    } catch (err) {
        console.error('Error in triggerNewAppointmentAlert:', err);
    }
}

module.exports = {
    sendWhatsAppMessage,
    triggerNewAppointmentAlert
};
