const Anthropic = require('@anthropic-ai/sdk');
const { Lead, AgentActividad, AgenteConfig, Reunion } = require('../models');
const whatsapp = require('./whatsappService');
const { enviarEmail } = require('./emailService');

const client = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

const TOOLS = [
    {
        name: 'enviar_whatsapp',
        description: 'Enviar un mensaje de WhatsApp al lead',
        input_schema: {
            type: 'object',
            properties: {
                mensaje: { type: 'string', description: 'Mensaje natural, personalizado y conciso (máximo 3 oraciones).' }
            },
            required: ['mensaje']
        }
    },
    {
        name: 'agendar_reunion',
        description: 'Crear una reunión de ventas en el calendario interno de AI Company cuando el lead confirme una fecha y hora. Úsalo cuando el lead acepte reunirse y dé disponibilidad.',
        input_schema: {
            type: 'object',
            properties: {
                mensaje_confirmacion: { type: 'string', description: 'Mensaje de confirmación a enviar al lead con los detalles de la reunión.' },
                fecha_iso:            { type: 'string', description: 'Fecha y hora de la reunión en formato ISO 8601. Ej: 2026-05-15T15:00:00-05:00' },
                duracion_minutos:     { type: 'number', description: 'Duración en minutos. Por defecto 30.' }
            },
            required: ['mensaje_confirmacion', 'fecha_iso']
        }
    },
    {
        name: 'actualizar_estado',
        description: 'Actualizar el estado del lead en el CRM',
        input_schema: {
            type: 'object',
            properties: {
                estado: {
                    type: 'string',
                    enum: ['contactado','respondio','interesado','reunion_agendada','sin_respuesta','descartado'],
                    description: 'Nuevo estado del lead'
                },
                nota: { type: 'string', description: 'Nota interna sobre el lead (opcional)' }
            },
            required: ['estado']
        }
    },
    {
        name: 'enviar_email',
        description: 'Enviar un correo electrónico al lead cuando tiene email disponible. Úsalo como canal adicional al WhatsApp o cuando no hay teléfono.',
        input_schema: {
            type: 'object',
            properties: {
                asunto:  { type: 'string', description: 'Asunto del correo. Corto, directo, sin spam words.' },
                cuerpo:  { type: 'string', description: 'Cuerpo del correo. Máximo 4 oraciones. Tono natural y colombiano.' }
            },
            required: ['asunto', 'cuerpo']
        }
    },
    {
        name: 'no_hacer_nada',
        description: 'No realizar ninguna acción. Usar cuando no es el momento correcto.',
        input_schema: {
            type: 'object',
            properties: {
                razon: { type: 'string', description: 'Razón por la que no se toma acción' }
            },
            required: ['razon']
        }
    }
];

async function procesarLead(lead, evento, mensajeRecibido = null) {
    const config = await AgenteConfig.findOne();
    if (!config || !config.activo) return;

    const historial = await AgentActividad.findAll({
        where: { lead_id: lead.id },
        order: [['created_at', 'ASC']],
        limit: 20
    });

    const historialTexto = historial.map(a =>
        `[${new Date(a.created_at).toLocaleDateString('es-CO')}] ${a.tipo}: ${a.mensaje || ''} ${a.resultado ? '→ Respuesta: ' + a.resultado : ''}`
    ).join('\n');

    const ahora = new Date().toLocaleString('es-CO', { timeZone: 'America/Bogota' });

    const systemPrompt = `Eres Cristian, representante de ventas de AI Company.
Tu objetivo es contactar leads, generar interés genuino y agendar reuniones de ventas en el calendario.

SOBRE AI COMPANY:
${config.descripcion_saas || `AI Company es una empresa de desarrollo de software a medida. Creamos sistemas personalizados para negocios, automatizaciones de procesos y somos agencia de marketing digital. Servicios: páginas web, SEO, pauta en Facebook/Instagram/TikTok/Google Ads, estrategias de lanzamiento y posicionamiento en buscadores.`}

REGLAS:
- Mensajes de WhatsApp cortos y naturales (máximo 3 oraciones). Nada de párrafos largos.
- Primer contacto: preséntate brevemente y pregunta por el dolor o necesidad del negocio.
- Si el lead muestra interés: propone la reunión preguntando qué día y hora le queda bien.
- Cuando el lead confirme fecha y hora: usa agendar_reunion para crearla en el calendario.
- Si ya van ${config.max_intentos}+ intentos sin respuesta: usa actualizar_estado con sin_respuesta.
- Canal: usa WhatsApp si hay teléfono, email si hay email. Si hay ambos, usa WhatsApp primero.
- Personaliza SIEMPRE con el nombre y empresa del lead.
- Tono: cercano, profesional, colombiano. Sin formalidades excesivas.
- Nunca digas que eres un bot o IA.
- Fecha y hora actual: ${ahora}`;

    const userPrompt = `LEAD:
- Nombre: ${lead.nombre}
- Empresa: ${lead.empresa || 'No especificada'}
- Teléfono: ${lead.telefono}
- Estado actual: ${lead.estado}
- Intentos de contacto: ${lead.intentos_contacto}
- Fuente: ${lead.fuente}
${lead.notas ? `- Notas: ${lead.notas}` : ''}

EVENTO: ${evento}
${mensajeRecibido ? `MENSAJE DEL LEAD: "${mensajeRecibido}"` : ''}

HISTORIAL:
${historialTexto || 'Sin historial previo — primer contacto.'}

¿Qué acción tomas ahora?`;

    const response = await client.messages.create({
        model: 'claude-sonnet-4-6',
        max_tokens: 1024,
        system: systemPrompt,
        tools: TOOLS,
        tool_choice: { type: 'any' },
        messages: [{ role: 'user', content: userPrompt }]
    });

    const tokensUsados = (response.usage?.input_tokens || 0) + (response.usage?.output_tokens || 0);

    for (const bloque of response.content) {
        if (bloque.type !== 'tool_use') continue;
        const { name, input } = bloque;

        if (name === 'enviar_whatsapp') {
            let waError = null;
            try {
                await whatsapp.enviarMensaje(lead.telefono, input.mensaje);
            } catch (e) {
                waError = e.message;
                console.error(`[Agente] WhatsApp error lead ${lead.id}:`, e.message);
            }
            await Lead.update(
                {
                    ultimo_contacto: new Date(),
                    intentos_contacto: lead.intentos_contacto + 1,
                    estado: lead.estado === 'nuevo' ? 'contactado' : lead.estado
                },
                { where: { id: lead.id } }
            );
            await AgentActividad.create({
                lead_id: lead.id,
                tipo: waError ? 'error' : 'mensaje_enviado',
                canal: 'whatsapp',
                mensaje: input.mensaje,
                resultado: waError ? `Error al enviar: ${waError}` : null,
                tokens_usados: tokensUsados
            });
        }

        if (name === 'agendar_reunion') {
            await Reunion.create({
                titulo:       `Reunión de ventas — ${lead.nombre}${lead.empresa ? ' (' + lead.empresa + ')' : ''}`,
                descripcion:  `Lead generado por el agente de ventas AI Company. Teléfono: ${lead.telefono}`,
                fecha:        new Date(input.fecha_iso),
                duracion:     input.duracion_minutos || 30,
                participantes: lead.nombre,
                estado:       'pendiente',
            });
            let waError = null;
            try {
                await whatsapp.enviarMensaje(lead.telefono, input.mensaje_confirmacion);
            } catch (e) {
                waError = e.message;
                console.error(`[Agente] WhatsApp error reunion lead ${lead.id}:`, e.message);
            }
            await Lead.update(
                { estado: 'reunion_agendada', fecha_reunion: new Date(input.fecha_iso), ultimo_contacto: new Date() },
                { where: { id: lead.id } }
            );
            await AgentActividad.create({
                lead_id: lead.id,
                tipo: 'reunion_confirmada',
                canal: 'whatsapp',
                mensaje: input.mensaje_confirmacion,
                resultado: waError ? `Error al enviar confirmación: ${waError}` : null,
                tokens_usados: tokensUsados
            });
        }

        if (name === 'actualizar_estado') {
            const notaActual = lead.notas || '';
            await Lead.update(
                { estado: input.estado, notas: input.nota ? (notaActual ? notaActual + '\n' + input.nota : input.nota) : notaActual },
                { where: { id: lead.id } }
            );
            await AgentActividad.create({
                lead_id: lead.id, tipo: 'decision_agente', canal: 'sistema',
                mensaje: `Estado → ${input.estado}${input.nota ? '. ' + input.nota : ''}`, tokens_usados: tokensUsados
            });
        }

        if (name === 'enviar_email') {
            let emailError = null;
            try {
                await enviarEmail({
                    gmailUser:    config.gmail_user,
                    gmailPass:    config.gmail_app_password,
                    nombreAgente: config.nombre_agente || 'Cristian',
                    nombreEmpresa: config.nombre_empresa || 'AI Company',
                    to:      lead.email,
                    subject: input.asunto,
                    body:    input.cuerpo,
                });
            } catch (e) {
                emailError = e.message;
                console.error(`[Agente] Email error lead ${lead.id}:`, e.message);
            }
            await Lead.update(
                { ultimo_contacto: new Date(), intentos_contacto: lead.intentos_contacto + 1, estado: lead.estado === 'nuevo' ? 'contactado' : lead.estado },
                { where: { id: lead.id } }
            );
            await AgentActividad.create({
                lead_id: lead.id,
                tipo: emailError ? 'error' : 'mensaje_enviado',
                canal: 'email',
                mensaje: `[${input.asunto}] ${input.cuerpo}`,
                resultado: emailError ? `Error al enviar: ${emailError}` : null,
                tokens_usados: tokensUsados,
            });
        }

        if (name === 'no_hacer_nada') {
            await AgentActividad.create({
                lead_id: lead.id, tipo: 'decision_agente', canal: 'sistema',
                mensaje: `Sin acción: ${input.razon}`, tokens_usados: tokensUsados
            });
        }
    }
}

async function procesarRespuestaWhatsApp(telefono, mensajeRecibido) {
    const numero = telefono.replace(/\D/g, '');
    const lead = await Lead.findOne({ where: { telefono: numero, agente_activo: true } });
    if (!lead) return;

    await AgentActividad.create({
        lead_id: lead.id, tipo: 'respuesta_recibida', canal: 'whatsapp', resultado: mensajeRecibido
    });

    if (['nuevo', 'contactado'].includes(lead.estado)) {
        await Lead.update({ estado: 'respondio' }, { where: { id: lead.id } });
        lead.estado = 'respondio';
    }

    await procesarLead(lead, 'El lead respondió a nuestro mensaje', mensajeRecibido);
}

module.exports = { procesarLead, procesarRespuestaWhatsApp };
