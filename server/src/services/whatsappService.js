const axios = require('axios');

const BASE_URL = 'https://graph.facebook.com/v19.0';

async function enviarMensaje(telefono, mensaje) {
    const phoneId = process.env.WHATSAPP_PHONE_NUMBER_ID;
    const token   = process.env.WHATSAPP_TOKEN;

    if (!phoneId || !token) throw new Error('WhatsApp no configurado (WHATSAPP_PHONE_NUMBER_ID / WHATSAPP_TOKEN)');

    // Asegurar formato internacional sin +
    const numero = telefono.replace(/\D/g, '');

    const { data } = await axios.post(
        `${BASE_URL}/${phoneId}/messages`,
        {
            messaging_product: 'whatsapp',
            to: numero,
            type: 'text',
            text: { body: mensaje }
        },
        { headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' } }
    );

    return data;
}

module.exports = { enviarMensaje };
