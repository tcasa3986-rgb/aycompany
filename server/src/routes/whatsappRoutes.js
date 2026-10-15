// Entrada de WhatsApp (Meta Cloud API).
//
// El sistema ya sabía MANDAR mensajes (whatsappService.js) pero no tenía cómo
// RECIBIRLOS: por eso se le escribía al 321 267 4754 y no contestaba nadie.
// Meta entrega los mensajes aquí.
//
// Esta ruta es pública a la fuerza (Meta no inicia sesión), así que se protege
// de otra forma: token de verificación al conectar, y firma HMAC en cada
// mensaje. Sin firma, cualquiera podría inventar conversaciones y ventas.
const router = require('express').Router();
const crypto = require('crypto');
const { recibir } = require('../services/whatsappEntrada');

// ── 1. Verificación: Meta llama una vez al conectar el webhook ───────────────
router.get('/webhook', (req, res) => {
    const modo   = req.query['hub.mode'];
    const token  = req.query['hub.verify_token'];
    const reto   = req.query['hub.challenge'];
    const esperados = [process.env.META_VERIFY_TOKEN, process.env.WA_DIGITAL_VERIFY_TOKEN].filter(Boolean);

    if (esperados.length === 0) {
        console.warn('WhatsApp: falta META_VERIFY_TOKEN, no se puede verificar el webhook');
        return res.sendStatus(500);
    }
    if (modo === 'subscribe' && esperados.includes(token)) {
        console.log('✅ WhatsApp: webhook verificado por Meta');
        return res.status(200).send(reto);
    }
    console.warn('WhatsApp: intento de verificación con token que no coincide');
    res.sendStatus(403);
});

/** ¿El mensaje viene de verdad de Meta? */
function firmaValida(req) {
    // Son DOS apps de Meta (la de AI Company y la de productos digitales), y
    // cada una firma con su propio secreto. Vale si coincide con cualquiera.
    const secretos = [process.env.META_APP_SECRET, process.env.WA_DIGITAL_APP_SECRET].filter(Boolean);

    // Sin ningún secreto configurado no se puede comprobar. Se deja pasar para
    // no tumbar el canal, pero queda gritando en los logs: es un hueco real.
    if (secretos.length === 0) return 'sin_secreto';

    const firma = req.get('X-Hub-Signature-256') || '';
    if (!req.rawBody || !firma) return false;

    return secretos.some(s => {
        const esperada = 'sha256=' + crypto.createHmac('sha256', s).update(req.rawBody).digest('hex');
        try { return crypto.timingSafeEqual(Buffer.from(firma), Buffer.from(esperada)); }
        catch (_) { return false; }
    });
}

// ── 2. Mensajes entrantes ────────────────────────────────────────────────────
router.post('/webhook', (req, res) => {
    const firma = firmaValida(req);
    if (firma === false) {
        console.warn('WhatsApp: mensaje con firma inválida, descartado');
        return res.sendStatus(403);
    }
    if (firma === 'sin_secreto') {
        console.warn('⚠️  WhatsApp: recibiendo SIN verificar firma. Falta META_APP_SECRET en Railway.');
    }

    // Se responde YA. Meta reintenta si uno tarda, y un reintento es un mensaje
    // duplicado. Procesar va aparte. (Misma lección del webhook de MercadoPago.)
    res.sendStatus(200);

    setImmediate(() => {
        recibir(req.body).catch(e => console.error('WhatsApp (procesando):', e.message));
    });
});

module.exports = router;
