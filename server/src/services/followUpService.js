const Anthropic = require('@anthropic-ai/sdk');
const { MensajeSocial } = require('../models');
const { Op } = require('sequelize');

const FOLLOW_UP_PROMPT = `Eres un experto en reactivación de ventas por WhatsApp de AI Company (agencia de marketing digital e IA en Bogotá).

El cliente vio el último mensaje pero no respondió. Tu trabajo: crear UN mensaje corto y natural que reactive la conversación SIN sonar desesperado ni presionar.

**Estrategias según el contexto (elige la más adecuada):**
- **Curiosidad**: Comparte algo de valor relacionado con su negocio que lo haga querer saber más.
- **Cambio de ángulo**: Replantea la propuesta desde otro beneficio que no se había mencionado.
- **Pregunta directa y simple**: "¿Sigue siendo algo que te interesa?" — simple, sin rodeos.
- **Prueba social**: Menciona un resultado reciente de un cliente en situación similar.
- **Urgencia real**: Solo si aplica (disponibilidad limitada de agenda, etc.).
- **Humor suave**: Si la conversación era amena, un toque de humor puede romper el hielo.

**Reglas:**
- Máximo 2-3 oraciones. Un solo mensaje, no varios.
- Nunca menciones que "te quedaste esperando" o que "no respondiste" — eso presiona y aleja.
- No repitas lo que ya se dijo en la conversación.
- No des precios ni cotizaciones.
- El tono debe sentirse como si un amigo te escribiera, no como un vendedor.
- En español colombiano natural.

Recibe el historial de conversación y genera SOLO el mensaje de seguimiento, sin explicaciones adicionales.`;

async function enviarSeguimiento(msg, historial) {
    if (!process.env.ANTHROPIC_API_KEY) return;
    if (!process.env.AUTO_RESPONDER || process.env.AUTO_RESPONDER !== 'true') return;

    try {
        const conversacionTexto = historial.map(m => {
            const lineas = [`Cliente: ${m.contenido || ''}`];
            if (m.respuesta) lineas.push(`Nosotros: ${m.respuesta}`);
            return lineas.join('\n');
        }).join('\n\n');

        const anthropic = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });
        const response = await anthropic.messages.create({
            model: 'claude-sonnet-4-6',
            max_tokens: 200,
            system: FOLLOW_UP_PROMPT,
            messages: [{ role: 'user', content: `Historial de la conversación:\n\n${conversacionTexto}\n\nGenera el mensaje de reactivación.` }]
        });

        const texto = response.content.find(b => b.type === 'text')?.text?.trim();
        if (!texto) return;

        let enviado = false;

        if (msg.red === 'whatsapp' && process.env.WHATSAPP_TOKEN && process.env.WHATSAPP_PHONE_ID) {
            const r = await fetch(`https://graph.facebook.com/v21.0/${process.env.WHATSAPP_PHONE_ID}/messages`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${process.env.WHATSAPP_TOKEN}` },
                body: JSON.stringify({ messaging_product: 'whatsapp', to: msg.remitente_id, type: 'text', text: { body: texto } })
            });
            const data = await r.json();
            if (!data.error) enviado = true;
            else console.error('Follow-up WA error:', data.error.message);

        } else if ((msg.red === 'facebook' || msg.red === 'instagram') && process.env.META_PAGE_TOKEN) {
            const r = await fetch('https://graph.facebook.com/v21.0/me/messages', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ access_token: process.env.META_PAGE_TOKEN, recipient: { id: msg.remitente_id }, message: { text: texto } })
            });
            const data = await r.json();
            if (!data.error) enviado = true;
            else console.error('Follow-up FB/IG error:', data.error.message);
        }

        if (enviado) {
            await msg.update({ seguimiento_enviado: true });
            // Guardar el mensaje de seguimiento en la bandeja para que quede visible
            await MensajeSocial.create({
                red: msg.red, tipo: 'mensaje',
                remitente_id: msg.remitente_id, remitente: msg.remitente,
                contenido: '[Seguimiento automático]',
                respuesta: texto, respondido: true, leido: true, seguimiento_enviado: true
            });
            console.log(`📤 Seguimiento enviado a ${msg.remitente} (${msg.red}): "${texto.slice(0, 60)}…"`);
        }
    } catch (err) {
        console.error('Follow-up error:', err.message);
    }
}

async function checkFollowUps() {
    if (process.env.AUTO_RESPONDER !== 'true') return;
    if (!process.env.ANTHROPIC_API_KEY) return;

    const hace4h  = new Date(Date.now() - 4  * 60 * 60 * 1000);
    const hace48h = new Date(Date.now() - 48 * 60 * 60 * 1000);

    // Mensajes donde el bot ya respondió, no se ha enviado seguimiento, y pasaron entre 4h y 48h
    const candidatos = await MensajeSocial.findAll({
        where: {
            respondido: true,
            seguimiento_enviado: { [Op.not]: true },
            updatedAt: { [Op.between]: [hace48h, hace4h] }
        },
        order: [['updatedAt', 'DESC']]
    });

    // Agrupar por contacto y quedarse con el más reciente
    const porContacto = {};
    for (const m of candidatos) {
        const key = `${m.red}:${m.remitente_id}`;
        if (!porContacto[key]) porContacto[key] = m;
    }

    for (const ultimoMsg of Object.values(porContacto)) {
        // Verificar que el cliente no haya respondido después
        const respuestaCliente = await MensajeSocial.findOne({
            where: { remitente_id: ultimoMsg.remitente_id, red: ultimoMsg.red, id: { [Op.gt]: ultimoMsg.id } }
        });
        if (respuestaCliente) continue;

        // Obtener historial de la conversación
        const historial = await MensajeSocial.findAll({
            where: { remitente_id: ultimoMsg.remitente_id, red: ultimoMsg.red },
            order: [['createdAt', 'ASC']],
            limit: 20
        });

        await enviarSeguimiento(ultimoMsg, historial);
    }
}

function startFollowUp() {
    console.log('📤 Servicio de seguimiento automático activo (cada 30 min)');
    setInterval(() => checkFollowUps().catch(e => console.error('Follow-up check error:', e.message)), 30 * 60 * 1000);
}

module.exports = { startFollowUp };
