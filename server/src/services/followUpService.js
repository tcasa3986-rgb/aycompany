const Anthropic = require('@anthropic-ai/sdk');
const { MensajeSocial } = require('../models');
const { Op } = require('sequelize');

const PRIMER_SEGUIMIENTO = `Eres un experto en reactivación de ventas por WhatsApp de AI Company (agencia de marketing digital e IA en Bogotá).

El cliente vio el último mensaje pero no respondió. Crea UN mensaje corto y natural que reactive la conversación SIN sonar desesperado.

**Estrategias (elige la más adecuada al contexto):**
- Pregunta simple y directa: "¿Sigue siendo algo que te interesa?"
- Dato de valor relacionado con su negocio que genere curiosidad
- Prueba social: resultado de un cliente en situación similar
- Cambio de ángulo: un beneficio diferente que no se había mencionado

**Reglas:**
- Máximo 2-3 oraciones. Un solo mensaje.
- NUNCA menciones que no respondió ni que te quedaste esperando.
- No repitas lo que ya se dijo en la conversación.
- No des precios. Tono: amigo, no vendedor.
- Español colombiano natural.

Genera SOLO el mensaje, sin explicaciones.`;

const SEGUNDO_SEGUIMIENTO = `Eres un experto en ventas por WhatsApp de AI Company (agencia de marketing digital e IA en Bogotá).

Ya enviaste un seguimiento anterior y el cliente sigue sin responder. Este es el ÚLTIMO intento de reactivación — debe ser diferente al primero, con más impacto pero sin presionar.

**Para este segundo seguimiento usa UNA de estas estrategias:**
- **Cierre de puerta**: "Entiendo que quizás no es el momento. Si en algún momento quieres que revisemos cómo podemos ayudarte, aquí estaremos." — Genera FOMO sin presionar.
- **Resultado concreto**: Comparte un resultado específico de un cliente similar. "Esta semana ayudamos a [tipo de negocio similar] a conseguir X — me acordé de ti."
- **Oferta de valor gratuito**: Ofrece algo sin costo que lo enganche (diagnóstico rápido, revisión gratuita, tip específico).
- **Cambio de formato**: "¿Prefieres que te llame 5 minutos en vez de escribir?" — baja la fricción.

**Reglas:**
- Máximo 2-3 oraciones. Diferente al primer seguimiento.
- No menciones que no respondió ni el seguimiento anterior.
- No des precios. Tono: tranquilo, seguro, sin necesidad.
- Español colombiano natural.

Genera SOLO el mensaje, sin explicaciones.`;

async function generarYEnviar(msg, historial, prompt) {
    if (!process.env.ANTHROPIC_API_KEY) return false;

    const conversacionTexto = historial.map(m => {
        const lineas = [`Cliente: ${m.contenido || ''}`];
        if (m.respuesta) lineas.push(`Nosotros: ${m.respuesta}`);
        return lineas.join('\n');
    }).join('\n\n');

    const anthropic = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });
    const response = await anthropic.messages.create({
        model: 'claude-haiku-4-5-20251001',
        max_tokens: 200,
        system: prompt,
        messages: [{ role: 'user', content: `Historial:\n\n${conversacionTexto}\n\nGenera el mensaje de seguimiento.` }]
    });

    const texto = response.content.find(b => b.type === 'text')?.text?.trim();
    if (!texto) return false;

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
        else console.error(`Follow-up ${msg.red} error:`, data.error.message);
    }

    if (enviado) {
        await MensajeSocial.create({
            red: msg.red, tipo: 'mensaje',
            remitente_id: msg.remitente_id, remitente: msg.remitente,
            contenido: '[Seguimiento automático]',
            respuesta: texto, respondido: true, leido: true, seguimiento_enviado: true
        });
    }

    return enviado;
}

async function noHayRespuestaCliente(msg) {
    const respuesta = await MensajeSocial.findOne({
        where: {
            remitente_id: msg.remitente_id,
            red: msg.red,
            id: { [Op.gt]: msg.id },
            contenido: { [Op.not]: '[Seguimiento automático]' }
        }
    });
    return !respuesta;
}

async function checkFollowUps() {
    if (process.env.AUTO_RESPONDER !== 'true') return;
    if (!process.env.ANTHROPIC_API_KEY) return;

    const hace4h  = new Date(Date.now() - 4  * 60 * 60 * 1000);
    const hace48h = new Date(Date.now() - 48 * 60 * 60 * 1000);
    const hace24h = new Date(Date.now() - 24 * 60 * 60 * 1000);
    const hace72h = new Date(Date.now() - 72 * 60 * 60 * 1000);

    // ── PRIMER SEGUIMIENTO ──────────────────────────────────────────────
    const candidatosPrimero = await MensajeSocial.findAll({
        where: {
            respondido: true,
            seguimiento_enviado: false,
            updatedAt: { [Op.between]: [hace48h, hace4h] }
        },
        order: [['updatedAt', 'DESC']]
    });

    const porContacto = {};
    for (const m of candidatosPrimero) {
        const key = `${m.red}:${m.remitente_id}`;
        if (!porContacto[key]) porContacto[key] = m;
    }

    for (const ultimoMsg of Object.values(porContacto)) {
        if (!(await noHayRespuestaCliente(ultimoMsg))) continue;

        const historial = await MensajeSocial.findAll({
            where: { remitente_id: ultimoMsg.remitente_id, red: ultimoMsg.red },
            order: [['createdAt', 'ASC']], limit: 20
        });

        const enviado = await generarYEnviar(ultimoMsg, historial, PRIMER_SEGUIMIENTO);
        if (enviado) {
            await ultimoMsg.update({ seguimiento_enviado: true, seguimiento_enviado_at: new Date() });
            console.log(`📤 1er seguimiento → ${ultimoMsg.remitente} (${ultimoMsg.red})`);
        }
    }

    // ── SEGUNDO SEGUIMIENTO ─────────────────────────────────────────────
    // Buscar el mensaje de seguimiento creado hace 24-72h que aún no tiene segundo seguimiento
    const seguimientosEnviados = await MensajeSocial.findAll({
        where: {
            contenido: '[Seguimiento automático]',
            segundo_seguimiento: false,
            createdAt: { [Op.between]: [hace72h, hace24h] }
        },
        order: [['createdAt', 'DESC']]
    });

    const porContacto2 = {};
    for (const m of seguimientosEnviados) {
        const key = `${m.red}:${m.remitente_id}`;
        if (!porContacto2[key]) porContacto2[key] = m;
    }

    for (const seguimientoMsg of Object.values(porContacto2)) {
        if (!(await noHayRespuestaCliente(seguimientoMsg))) continue;

        const historial = await MensajeSocial.findAll({
            where: {
                remitente_id: seguimientoMsg.remitente_id,
                red: seguimientoMsg.red,
                contenido: { [Op.not]: '[Seguimiento automático]' }
            },
            order: [['createdAt', 'ASC']], limit: 20
        });

        const enviado = await generarYEnviar(seguimientoMsg, historial, SEGUNDO_SEGUIMIENTO);
        if (enviado) {
            await seguimientoMsg.update({ segundo_seguimiento: true });
            console.log(`📤 2do seguimiento → ${seguimientoMsg.remitente} (${seguimientoMsg.red})`);
        }
    }
}

function startFollowUp() {
    console.log('📤 Servicio de seguimiento automático activo (cada 30 min)');
    setInterval(() => checkFollowUps().catch(e => console.error('Follow-up check error:', e.message)), 30 * 60 * 1000);
}

module.exports = { startFollowUp };
