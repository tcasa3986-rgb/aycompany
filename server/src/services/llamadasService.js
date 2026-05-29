const { enviarMensaje: enviarWA } = require('./whatsappService');
const telegramService = require('./telegramService');

const VAPI_URL = 'https://api.vapi.ai';

const SCRIPT_SISTEMA = `Eres Cristian Gutiérrez, asesor comercial de AI Company CO, empresa colombiana de tecnología e inteligencia artificial con sede en Bogotá.

## Tu objetivo
Despertar interés en el dueño o encargado del negocio y lograr que acepte recibir una propuesta digital gratuita por WhatsApp.

## Guión de la llamada

**Al contestar:**
"Hola, buenas [días/tardes]. Mi nombre es Cristian Gutiérrez, llamo de parte de AI Company CO. Encontré su negocio y preparamos una propuesta de transformación digital sin costo para ustedes. ¿Me regala dos minuticos?"

**Si dicen SÍ:**
"Perfecto, gracias. Básicamente lo que hacemos es ayudar a negocios como el suyo a conseguir más clientes con inteligencia artificial — automatizaciones de WhatsApp, sistemas de reservas, páginas web y más. Le puedo enviar la propuesta completa por WhatsApp ahora mismo para que la vea con calma. ¿Le parece?"

**Si piden más info:**
Menciona 1-2 beneficios concretos: "Por ejemplo, con nuestro sistema de WhatsApp con IA los clientes pueden reservar, preguntar precios y recibir respuestas automáticas las 24 horas sin que usted tenga que estar pendiente."

**Si dicen que SÍ a recibir propuesta:**
"Excelente. Le llega por WhatsApp a este mismo número en los próximos minutos. Que tenga un excelente día."
→ Termina la llamada.

**Si dicen NO o están ocupados:**
"Entiendo perfectamente, no se preocupe. Que tenga un excelente día."
→ Termina la llamada amablemente.

**Si no contestan o buzón de voz:**
→ Cuelga sin dejar mensaje.

## Reglas
- Habla como colombiano natural, cálido y respetuoso
- Máximo 90 segundos de llamada
- Nunca seas insistente — un NO es suficiente para terminar
- No menciones precios en la llamada
- Si preguntan el precio: "Eso depende de lo que necesiten, por eso le envío la propuesta personalizada"
- Si insultan o cuelgan: termina inmediatamente`;

async function crearAsistente(infoNegocio) {
    const empresa = process.env.NOMBRE_EMPRESA || 'AI Company CO';
    return {
        name: `Llamada - ${infoNegocio.nombre}`,
        transcriber: {
            provider: 'deepgram',
            language: 'es',
            model: 'nova-2'
        },
        voice: {
            provider: 'azure',
            voiceId: 'es-CO-GonzaloNeural',
            speed: 1.0
        },
        model: {
            provider: 'anthropic',
            model: 'claude-haiku-4-5-20251001',
            messages: [{ role: 'system', content: SCRIPT_SISTEMA }],
            temperature: 0.7,
            maxTokens: 150
        },
        firstMessage: `Hola, buenas tardes. Mi nombre es Cristian Gutiérrez, llamo de parte de ${empresa}. Encontré su negocio ${infoNegocio.nombre} y preparamos una propuesta digital sin costo para ustedes. ¿Me regala dos minuticos?`,
        endCallMessage: 'Que tenga un excelente día.',
        endCallPhrases: [
            'adiós', 'hasta luego', 'no gracias', 'no me interesa',
            'estoy ocupado', 'no tenemos interés', 'quite', 'no moleste'
        ],
        maxDurationSeconds: 120,
        backgroundSound: 'office',
        silenceTimeoutSeconds: 20,
        responseDelaySeconds: 0.5,
        serverUrl: process.env.BASE_URL
            ? `${process.env.BASE_URL}/api/llamadas/webhook`
            : undefined,
    };
}

async function llamar({ telefono, infoNegocio }) {
    const apiKey  = process.env.VAPI_API_KEY;
    const phoneId = process.env.VAPI_PHONE_ID;

    if (!apiKey || !phoneId) {
        throw new Error('VAPI_API_KEY y VAPI_PHONE_ID son requeridos en variables de entorno');
    }

    const telLimpio = telefono.replace(/\D/g, '');
    const asistente = await crearAsistente(infoNegocio);

    const body = {
        phoneNumberId: phoneId,
        customer: { number: `+${telLimpio}` },
        assistant: asistente,
        metadata: {
            negocio: infoNegocio.nombre,
            ciudad:  infoNegocio.ciudad,
            telefono: telLimpio
        }
    };

    const res = await fetch(`${VAPI_URL}/call/phone`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${apiKey}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(body)
    });

    const data = await res.json();
    if (!res.ok) throw new Error(data.message || JSON.stringify(data));

    console.log(`📞 Llamada iniciada → ${telLimpio} (${infoNegocio.nombre}) — ID: ${data.id}`);
    return { callId: data.id, status: data.status };
}

// Webhook que Vapi llama cuando termina la llamada
async function procesarWebhook(evento) {
    try {
        const tipo = evento.message?.type;

        if (tipo === 'end-of-call-report') {
            const reporte  = evento.message;
            const meta     = reporte.call?.metadata || {};
            const duracion = Math.round((reporte.durationSeconds || 0));
            const resumen  = reporte.summary || '';
            const transcripcion = reporte.transcript || '';

            // Detectar si hubo interés
            const interesKeywords = ['sí', 'si', 'claro', 'me interesa', 'envíe', 'mándeme', 'propuesta'];
            const hubointeres = interesKeywords.some(k =>
                transcripcion.toLowerCase().includes(k)
            );

            console.log(`📞 Llamada terminada — ${meta.negocio} (${duracion}s) — Interés: ${hubointeres}`);

            // Notificar por Telegram
            const emoji = hubointeres ? '🔥' : '📞';
            await telegramService.enviar(
                `${emoji} *Llamada completada*\n\n` +
                `🏢 *Negocio:* ${meta.negocio || 'Desconocido'}\n` +
                `📍 *Ciudad:* ${meta.ciudad || ''}\n` +
                `📱 *Teléfono:* ${meta.telefono || ''}\n` +
                `⏱ *Duración:* ${duracion} segundos\n` +
                `${hubointeres ? '✅ *¡MOSTRÓ INTERÉS!* — Enviarle propuesta por WhatsApp' : '❌ Sin interés'}\n\n` +
                (resumen ? `*Resumen:* ${resumen.slice(0, 300)}` : '')
            ).catch(() => {});

            // Si hubo interés → enviar WhatsApp con propuesta
            if (hubointeres && meta.telefono && process.env.WHATSAPP_TOKEN) {
                const empresa = process.env.NOMBRE_EMPRESA || 'AI Company CO';
                const msg =
`Hola ${meta.negocio} 👋

Justo le llamé de parte de *${empresa}*. Como le mencioné, aquí le envío la propuesta de transformación digital que preparamos para su negocio.

🚀 *¿Qué incluye?*
• Sitio web profesional
• WhatsApp con IA (responde clientes 24/7)
• Sistema de reservas o pedidos
• Automatizaciones y reportes

¿Le gustaría agendar una videollamada de 15 minutos para revisar la propuesta juntos?`;

                await enviarWA(meta.telefono, msg).catch(e =>
                    console.error('WA post-llamada error:', e.message)
                );
                console.log(`💬 WhatsApp enviado post-llamada → ${meta.telefono}`);
            }
        }
    } catch (err) {
        console.error('Webhook llamadas error:', err.message);
    }
}

module.exports = { llamar, procesarWebhook };
