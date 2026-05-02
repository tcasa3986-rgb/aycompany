const Anthropic = require('@anthropic-ai/sdk');
const { MensajeSocial, Reunion, Evento } = require('../models');
const { Op } = require('sequelize');
const telegramService = require('./telegramService');

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

## Tu mentalidad de ventas — eres un experto en ventas consultivas por WhatsApp
Entiendes que vender por WhatsApp no es enviar catálogos ni presionar: es construir confianza, entender el dolor real del cliente y guiarlo naturalmente hacia la reunión donde cerramos.

**Técnicas que aplicas:**
- **Escucha activa**: Antes de proponer, haz preguntas que descubran el dolor real. "¿Cuál es tu mayor reto hoy con el marketing?" es mejor que cualquier presentación.
- **Espejo y validación**: Repite o parafrasea lo que el cliente dijo para que sienta que lo entiendes. "Entiendo, entonces el problema real es que inviertes en redes pero no ves resultados concretos…"
- **Prueba social suave**: Menciona resultados de clientes similares sin presumir. "Con negocios como el tuyo hemos logrado X…"
- **Urgencia real**: Solo cuando el cliente ya mostró interés. Nunca artificialmente.
- **El método de la pregunta**: En vez de explicar todo, haz preguntas que lleven al cliente a sus propias conclusiones. "¿Qué pasaría con tu negocio si lograras el doble de clientes este mes?"
- **Micro-compromisos**: Pide pequeños síes antes del gran sí (la reunión). "¿Tienes 30 segundos para contarme qué estás haciendo hoy en marketing?"
- **Manejo de objeciones con el giro**: Si dicen "no tengo dinero", respondes entendiendo y reencuadrando: "Lo entiendo perfectamente. Por eso justamente quiero entender tu caso — la reunión es para ver si tiene sentido o no, sin compromiso."
- **Cierre suave hacia reunión**: "¿Qué te parece si agendamos 30 minutos esta semana y vemos juntos qué podría funcionar para ti?"

**Etapas de la venta que detectas:**
1. **Frío**: El cliente acaba de escribir, no sabe bien qué quiere. → Calentar, preguntar, despertar curiosidad.
2. **Interés**: Hizo preguntas, quiere saber más. → Profundizar en su dolor, mostrar que entiendes su negocio.
3. **Consideración**: Está comparando o evaluando. → Diferenciarte, usar prueba social, crear urgencia real.
4. **Listo para reunirse**: Ya mostró señales de cierre. → Llamar a ver_disponibilidad y proponer horario concreto.

## Cómo hablas
- Cercano, natural y directo — no hablas como robot ni como vendedor de feria
- Mensajes cortos como los de WhatsApp real: 1-3 oraciones máximo
- A veces haces UNA sola pregunta poderosa en vez de dar información
- Usas el nombre del cliente cuando lo sabes
- Generas confianza desde el primer mensaje
- Siempre en español colombiano, tono amigable y profesional
- Nunca mandas listas largas ni texto en bloque — eso mata la conversación en WhatsApp

## REGLAS CRÍTICAS — nunca las rompas
1. NUNCA des precios, tarifas ni cotizaciones. Si preguntan cuánto cuesta algo, lo entiendes perfectamente pero los invitas a una reunión para entender bien su caso.
2. El objetivo SIEMPRE es llegar a una reunión o videollamada — ahí es donde escuchamos, planeamos y proponemos.
3. No presiones, pero sé claro: la reunión es el primer paso para trabajar juntos.
4. Si no sabes algo específico del negocio del cliente, pregunta amablemente.
5. Respuestas cortas y naturales — máximo 3-4 oraciones. No des listas largas ni párrafos eternos.
6. Cuando el cliente muestre interés en reunirse, usa la herramienta ver_disponibilidad para consultar los horarios disponibles y luego propón opciones concretas.
7. Cuando el cliente confirme una fecha y hora específica, DEBES llamar a la herramienta agendar_reunion ANTES de decir que quedó agendada. NUNCA digas que la reunión quedó confirmada sin haber llamado exitosamente a agendar_reunion. Si no llamas a la herramienta, la reunión no existe en el sistema.
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

async function ejecutarTool(nombre, input, msgCtx) {
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
        // El bot genera hora Colombia (UTC-5). El servidor es UTC.
        // Sumamos 5h para almacenar en UTC correctamente.
        const COLOMBIA_MS = 5 * 60 * 60 * 1000;
        const fechaInicio = new Date(new Date(fecha).getTime() + COLOMBIA_MS);
        const fechaFin    = new Date(fechaInicio.getTime() + duracion * 60000);
        const titulo      = `Reunión con ${nombre_cliente}`;
        const desc        = descripcion || 'Cliente agendado desde chat';

        // Crear en Reuniones
        const reunion = await Reunion.create({
            titulo, descripcion: desc, fecha: fechaInicio, duracion, participantes: nombre_cliente, estado: 'pendiente'
        });

        // Crear en Calendario
        Evento.create({
            titulo, descripcion: desc, fecha_inicio: fechaInicio, fecha_fin: fechaFin,
            color: '#6366f1', participantes: nombre_cliente, recordatorio: true
        }).catch(e => console.error('Error creando Evento en calendario:', e.message));

        const f = fechaInicio;
        const fechaTexto = `${f.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'America/Bogota' })} a las ${f.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', timeZone: 'America/Bogota' })}`;
        console.log(`📅 Reunión agendada: ${nombre_cliente} — ${fechaTexto}`);

        // Alerta Telegram al agendar
        if (process.env.PLATAFORMA_TELEGRAM_TOKEN && process.env.PLATAFORMA_TELEGRAM_CHAT_ID) {
            const tgMsg = `📅 *Nueva reunión agendada*\n👤 ${nombre_cliente}\n🕐 ${fechaTexto}\n📝 ${desc.slice(0, 120)}`;
            telegramService.enviar(tgMsg).catch(() => {});
        }

        // Etiquetar al contacto en la bandeja
        if (msgCtx?.remitente_id && msgCtx?.red) {
            MensajeSocial.update(
                { etiqueta: 'Reunión agendada' },
                { where: { remitente_id: msgCtx.remitente_id, red: msgCtx.red } }
            ).catch(() => {});
        }

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

        const historial = await MensajeSocial.findAll({
            where: {
                remitente_id: msg.remitente_id,
                red: msg.red,
                id: { [Op.lt]: msg.id }
            },
            order: [['createdAt', 'DESC']],
            limit: 40
        });
        historial.reverse();

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
            model: 'claude-sonnet-4-6',
            max_tokens: 500,
            system: systemConFecha,
            tools: TOOLS,
            messages
        });

        while (response.stop_reason === 'tool_use') {
            const toolBlock = response.content.find(b => b.type === 'tool_use');
            console.log(`🔧 Tool: ${toolBlock.name}`, JSON.stringify(toolBlock.input));
            const resultado = await ejecutarTool(toolBlock.name, toolBlock.input, msg);
            console.log(`🔧 Resultado: ${resultado.slice(0, 100)}`);

            messages.push({ role: 'assistant', content: response.content });
            messages.push({ role: 'user', content: [{ type: 'tool_result', tool_use_id: toolBlock.id, content: resultado }] });

            response = await anthropic.messages.create({
                model: 'claude-sonnet-4-6',
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
            else console.error(`Auto-responder ${msg.red} error:`, data.error.message);

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

// Genera respuesta sin enviarla — para uso síncrono (Make, etc.)
async function generarRespuesta(msg) {
    if (!process.env.ANTHROPIC_API_KEY) return null;
    if (!msg?.contenido) return null;

    const anthropic = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

    const historial = await MensajeSocial.findAll({
        where: { remitente_id: msg.remitente_id, red: msg.red, id: { [Op.lt]: msg.id } },
        order: [['createdAt', 'DESC']], limit: 40
    });
    historial.reverse();

    const messages = [];
    for (const m of historial) {
        if (m.contenido && m.respuesta) {
            messages.push({ role: 'user', content: m.contenido });
            messages.push({ role: 'assistant', content: m.respuesta });
        }
    }
    messages.push({ role: 'user', content: msg.contenido });

    const hoy = new Date().toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    const systemConFecha = SYSTEM_PROMPT + `\n\n## Fecha actual\nHoy es ${hoy}.`;

    let response = await anthropic.messages.create({
        model: 'claude-sonnet-4-6', max_tokens: 500,
        system: systemConFecha, tools: TOOLS, messages
    });

    while (response.stop_reason === 'tool_use') {
        const toolBlock = response.content.find(b => b.type === 'tool_use');
        const resultado = await ejecutarTool(toolBlock.name, toolBlock.input, msg);
        messages.push({ role: 'assistant', content: response.content });
        messages.push({ role: 'user', content: [{ type: 'tool_result', tool_use_id: toolBlock.id, content: resultado }] });
        response = await anthropic.messages.create({
            model: 'claude-sonnet-4-6', max_tokens: 500,
            system: systemConFecha, tools: TOOLS, messages
        });
    }

    return response.content.find(b => b.type === 'text')?.text?.trim() || null;
}

module.exports = { responder, generarRespuesta };
