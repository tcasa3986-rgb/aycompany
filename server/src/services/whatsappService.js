const axios = require('axios');

const BASE_URL = 'https://graph.facebook.com/v19.0';

// Hay DOS números de WhatsApp y no se pueden mezclar:
//
//   empresa  — AI Company (+57 321 267 4754). Le habla a los clientes de los
//              sistemas: vencimientos, bloqueos, avisos de licencia.
//   digital  — produc digital (+57 312 548 2027). El bot de productos digitales.
//
// Si los avisos de licencia salieran desde el número de ventas, el cliente
// recibiría un mensaje de alguien que no conoce sobre su sistema en producción.
const CUENTAS = {
    empresa: () => ({ id: process.env.WHATSAPP_PHONE_ID,    token: process.env.WHATSAPP_TOKEN }),
    digital: () => ({ id: process.env.WA_DIGITAL_PHONE_ID,  token: process.env.WA_DIGITAL_TOKEN }),
};

/** ¿De cuál cuenta es este phone_number_id? Sirve para responder por donde escribieron. */
function cuentaDe(phoneId) {
    if (!phoneId) return 'empresa';
    if (String(phoneId) === String(process.env.WA_DIGITAL_PHONE_ID)) return 'digital';
    return 'empresa';
}

/**
 * @param {string} telefono  a quién
 * @param {string} mensaje   qué
 * @param {'empresa'|'digital'} cuenta  desde cuál número
 */
async function enviarMensaje(telefono, mensaje, cuenta = 'empresa') {
    const { id, token } = (CUENTAS[cuenta] || CUENTAS.empresa)();

    if (!id || !token) {
        throw new Error(`WhatsApp "${cuenta}" no configurado (falta el phone id o el token)`);
    }

    // Formato internacional sin el +
    const numero = String(telefono).replace(/\D/g, '');

    const { data } = await axios.post(
        `${BASE_URL}/${id}/messages`,
        {
            messaging_product: 'whatsapp',
            to: numero,
            type: 'text',
            text: { body: mensaje },
        },
        { headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' } }
    );

    return data;
}

/** ¿Está lista esa cuenta para mandar? */
function configurada(cuenta = 'empresa') {
    const c = (CUENTAS[cuenta] || CUENTAS.empresa)();
    return !!(c.id && c.token);
}

module.exports = { enviarMensaje, cuentaDe, configurada };
