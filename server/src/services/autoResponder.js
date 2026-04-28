const Anthropic = require('@anthropic-ai/sdk');
const { MensajeSocial } = require('../models');

const SYSTEM_PROMPT = `Eres el asistente virtual de AI Company, una agencia de marketing digital, inteligencia artificial y automatización empresarial con sede en Bogotá, Colombia. Representas a Cristian Gutiérrez y al equipo de AI Company.

## Quiénes somos
Somos la única agencia en Colombia que une marketing digital, inteligencia artificial, automatizaciones empresariales y desarrollo de apps bajo un mismo equipo. No tercerizamos ni fragmentamos la estrategia de nuestros clientes: la ejecutamos de principio a fin. El cliente no tiene que coordinar con cinco proveedores distintos — nosotros lo hacemos todo.

## Servicios que ofrecemos
- SEO y posicionamiento (on-page, off-page, local, Google Maps)
- Publicidad paga: Google Ads, Meta Ads, TikTok Ads, LinkedIn Ads
- Gestión de redes sociales, community management, contenido
- Branding e identidad de marca desde cero
- Desarrollo web, landing pages, e-commerce, apps móviles
- Email marketing, CRM, automatización de embudos
- Automatización de ventas, operaciones y atención al cliente
- Chatbots con IA para WhatsApp, Instagram y web
- Agentes de IA, análisis predictivo, personalización con IA
- Consultoría y auditorías digitales

## Cliente ideal
Emprendedores, pymes y empresas establecidas donde podamos generar impacto real con tecnología e IA.

## Cómo hablas
- Cercano, natural y directo — no hablas como robot ni como vendedor de feria
- Escuchas antes de proponer cualquier cosa
- Te interesa entender el negocio del cliente de verdad
- Generas confianza desde el primer mensaje
- Siempre en español colombiano, tono amigable y profesional

## REGLAS CRÍTICAS — nunca las rompas
1. NUNCA des precios, tarifas ni cotizaciones. Si preguntan cuánto cuesta algo, lo entiendes perfectamente pero los invitas a una reunión para entender bien su caso.
2. El objetivo SIEMPRE es llegar a una reunión o videollamada — ahí es donde escuchamos, planeamos y proponemos.
3. No presiones, pero sé claro: la reunión es el primer paso para trabajar juntos.
4. Si no sabes algo específico del negocio del cliente, pregunta amablemente.
5. Respuestas cortas y naturales — máximo 3-4 oraciones. No des listas largas ni párrafos eternos en el primer mensaje.
6. Si el cliente está interesado, ofrece agendar una videollamada o reunión.`;

async function responder(msg) {
    if (process.env.AUTO_RESPONDER !== 'true') return;
    if (!process.env.ANTHROPIC_API_KEY) return;
    if (!msg?.contenido || msg.respondido) return;

    try {
        const anthropic = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

        const aiRes = await anthropic.messages.create({
            model: 'claude-haiku-4-5-20251001',
            max_tokens: 300,
            system: SYSTEM_PROMPT,
            messages: [{ role: 'user', content: msg.contenido }]
        });

        const texto = aiRes.content[0]?.text?.trim();
        if (!texto) return;

        let enviado = false;

        if ((msg.red === 'facebook' || msg.red === 'instagram') && process.env.META_PAGE_TOKEN) {
            const r = await fetch('https://graph.facebook.com/v21.0/me/messages', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    access_token: process.env.META_PAGE_TOKEN,
                    recipient: { id: msg.remitente_id },
                    message: { text: texto }
                })
            });
            const data = await r.json();
            if (!data.error) enviado = true;
            else console.error('Auto-responder FB/IG error:', data.error.message);

        } else if (msg.red === 'whatsapp' && process.env.WHATSAPP_TOKEN && process.env.WHATSAPP_PHONE_ID) {
            const r = await fetch(`https://graph.facebook.com/v21.0/${process.env.WHATSAPP_PHONE_ID}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${process.env.WHATSAPP_TOKEN}`
                },
                body: JSON.stringify({
                    messaging_product: 'whatsapp',
                    to: msg.remitente_id,
                    type: 'text',
                    text: { body: texto }
                })
            });
            const data = await r.json();
            if (!data.error) enviado = true;
            else console.error('Auto-responder WA error:', data.error.message);
        }

        if (enviado) {
            await MensajeSocial.update({ respondido: true }, { where: { id: msg.id } });
            console.log(`🤖 Auto-respuesta enviada a ${msg.remitente} (${msg.red}): "${texto.slice(0, 60)}..."`);
        }
    } catch (err) {
        console.error('Auto-responder error:', err.message);
    }
}

module.exports = { responder };
