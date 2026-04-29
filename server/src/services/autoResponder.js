const Anthropic = require('@anthropic-ai/sdk');
const { MensajeSocial, Reunion, Evento } = require('../models');
const { Op } = require('sequelize');

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
6. Cuando el cliente muestre interés en reunirse, usa la herramienta ver_disponibilidad para consultar los horarios disponibles y luego propón opciones concretas.
7. Cuando el cliente confirme una fecha y hora, usa agendar_reunion para reservar en el calendario. Confirma siempre con el cliente antes de agendar.
8. Si el mensaje es exactamente "[Audio de voz]", el cliente envió un audio que no pudo transcribirse. Responde con amabilidad: recibiste su mensaje de voz pero necesitas que te escriba para poder ayudarle mejor.
9. Si el mensaje empieza con "[Audio]: ", lo que sigue es la transcripción automática de un audio de voz. Responde al contenido transcrito de manera completamente natural, sin mencionar que fue un audio ni que fue transcrito.`;

const TOOLS = [
    {
        name: 'ver_disponibilidad',
        description: 'Consulta las reuniones ya agendadas en los próximos 14 días para conocer la disponibilidad del equipo. Úsala cuando el cliente quiera reunirse para poder ofrecerle horarios disponibles.',
        input_schema: { type: 'object', properties: {}, required: [] }
    },
    {
        name: 'agendar_reunion',
        description: 'Agenda una reunión con el cliente en el calendario interno. Úsala solo cuando el cliente haya confirmado explícitamente una fecha y hora específica.',
        input_schema: {
            type: 'object',
            properties: {
                nombre_cliente: { type: 'string', description: 'Nombre completo o como se presenta el cliente' },
                fecha: { type: 'string', description: 'Fecha y hora en formato ISO 8601, ejemplo: 2026-05-10T10:00:00' },
                duracion: { type: 'number', description: 'Duración en minutos (por defecto 60)' },
                descripcion: { type: 'string', description: 'Resumen del negocio/necesidad del cliente para preparar la reunión' }
            },
            required: ['nombre_cliente', 'fecha']
        }
    }
];

async function ejecutarTool(nombre, input) {
    if (nombre === 'ver_disponibilidad') {
        const desde = new Date();
        const hasta = new Date(Date.now() + 14 * 24 * 60 * 60 * 1000);
        const reuniones = await Reunion.findAll({
            where: { fecha: { [Op.between]: [desde, hasta] }, estado: 'pendiente' },
            order: [['fecha', 'ASC']]
        });
        if (reuniones.length === 0) {
            return 'No hay reuniones agendadas en los próximos 14 días. Disponibilidad completa de lunes a viernes, 9am a 6pm hora Colombia.';
        }
        const ocupados = reuniones.map(r => {
            const f = new Date(r.fecha);
            return `- ${f.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long' })} a las ${f.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' })} (${r.duracion} min)`;
        }).join('\n');
        return `Horarios ya ocupados:\n${ocupados}\n\nDisponibilidad: lunes a viernes, 9am–6pm hora Colombia, excepto los anteriores.`;
    }

    if (nombre === 'agendar_reunion') {
        const { nombre_cliente, fecha, duracion = 60, descripcion = '' } = input;
        const fechaInicio = new Date(fecha);
        const fechaFin    = new Date(fechaInicio.getTime() + duracion * 60000);
        const titulo      = `Reunión con ${nombre_cliente}`;
        const desc        = descripcion || 'Cliente agendado desde chat';

        // Crear en Reuniones
        const reunion = await Reunion.create({
            titulo, descripcion: desc, fecha: fechaInicio, duracion, participantes: nombre_cliente, estado: 'pendiente'
        });

        // Crear en Calendario (Eventos) para que aparezca visualmente
        await Evento.create({
            titulo, descripcion: desc, fecha_inicio: fechaInicio, fecha_fin: fechaFin,
            color: '#6366f1', participantes: nombre_cliente, recordatorio: true
        });

        const f = fechaInicio;
        const fechaTexto = `${f.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })} a las ${f.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' })}`;
        console.log(`📅 Reunión agendada: ${nombre_cliente} — ${fechaTexto}`);
        return `Reunión agendada exitosamente para el ${fechaTexto}. ID: ${reunion.id}.`;
    }

    return 'Herramienta no reconocida.';
}

async function responder(msg) {
    console.log(`🤖 Auto-responder llamado — red=${msg?.red} AUTO_RESPONDER=${process.env.AUTO_RESPONDER} respondido=${msg?.respondido}`);
    if (process.env.AUTO_RESPONDER !== 'true') { console.log('🤖 Auto-responder desactivado (AUTO_RESPONDER != true)'); return; }
    if (!process.env.ANTHROPIC_API_KEY) { console.error('🤖 Auto-responder: ANTHROPIC_API_KEY no configurado'); return; }
    if (!msg?.contenido) { console.log('🤖 Auto-responder: mensaje sin contenido'); return; }
    if (msg.respondido) { console.log('🤖 Auto-responder: mensaje ya respondido, saltando'); return; }
    console.log(`🤖 Auto-responder activado para ${msg.remitente} (${msg.red}): "${msg.contenido?.slice(0, 60)}"`);

    try {
        const anthropic = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

        // Historial de conversación del mismo remitente
        const historial = await MensajeSocial.findAll({
            where: { remitente_id: msg.remitente_id, red: msg.red, id: { [Op.lt]: msg.id } },
            order: [['createdAt', 'ASC']],
            limit: 20
        });

        const messages = [];
        for (const m of historial) {
            if (m.contenido && m.respuesta) {
                messages.push({ role: 'user', content: m.contenido });
                messages.push({ role: 'assistant', content: m.respuesta });
            }
        }
        messages.push({ role: 'user', content: msg.contenido });

        // Inyectar fecha actual para que el bot calcule correctamente "este jueves", "mañana", etc.
        const hoy = new Date().toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        const systemConFecha = SYSTEM_PROMPT + `\n\n## Fecha actual\nHoy es ${hoy}. Usa esta fecha para interpretar referencias relativas del cliente como "este jueves", "mañana" o "la próxima semana".`;

        // Bucle de tool use
        let response = await anthropic.messages.create({
            model: 'claude-haiku-4-5-20251001',
            max_tokens: 500,
            system: systemConFecha,
            tools: TOOLS,
            messages
        });

        while (response.stop_reason === 'tool_use') {
            const toolBlock = response.content.find(b => b.type === 'tool_use');
            console.log(`🔧 Tool: ${toolBlock.name}`, JSON.stringify(toolBlock.input));
            const resultado = await ejecutarTool(toolBlock.name, toolBlock.input);
            console.log(`🔧 Resultado: ${resultado.slice(0, 100)}`);

            messages.push({ role: 'assistant', content: response.content });
            messages.push({ role: 'user', content: [{ type: 'tool_result', tool_use_id: toolBlock.id, content: resultado }] });

            response = await anthropic.messages.create({
                model: 'claude-haiku-4-5-20251001',
                max_tokens: 500,
                system: systemConFecha,
                tools: TOOLS,
                messages
            });
        }

        const texto = response.content.find(b => b.type === 'text')?.text?.trim();
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
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${process.env.WHATSAPP_TOKEN}` },
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
            await MensajeSocial.update({ respondido: true, respuesta: texto }, { where: { id: msg.id } });
            console.log(`🤖 Auto-respuesta enviada a ${msg.remitente} (${msg.red}): "${texto.slice(0, 60)}..."`);
        }
    } catch (err) {
        console.error('Auto-responder error:', err.message);
    }
}

module.exports = { responder };
