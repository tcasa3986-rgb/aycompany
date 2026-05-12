const Anthropic = require('@anthropic-ai/sdk');
const { Lead, AgentActividad, AgenteConfig } = require('../models');
const whatsapp = require('./whatsappService');

const client = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

// Herramientas que el agente puede usar
const TOOLS = [
    {
        name: 'enviar_whatsapp',
        description: 'Enviar un mensaje de WhatsApp al lead',
        input_schema: {
            type: 'object',
            properties: {
                mensaje: { type: 'string', description: 'Mensaje a enviar. Debe ser natural, personalizado y conciso.' }
            },
            required: ['mensaje']
        }
    },
    {
        name: 'proponer_reunion',
        description: 'Enviar link de Calendly al lead para que agende una reunión. Usar cuando el lead muestre interés real.',
        input_schema: {
            type: 'object',
            properties: {
                mensaje_previo: { type: 'string', description: 'Mensaje que acompaña el link de la reunión' }
            },
            required: ['mensaje_previo']
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
                nota: { type: 'string', description: 'Nota interna sobre el lead' }
            },
            required: ['estado']
        }
    },
    {
        name: 'no_hacer_nada',
        description: 'No realizar ninguna acción ahora. Usar cuando no es el momento correcto o el lead no requiere acción inmediata.',
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

    // Historial de actividad del lead
    const historial = await AgentActividad.findAll({
        where: { lead_id: lead.id },
        order: [['created_at', 'ASC']],
        limit: 20
    });

    const historialTexto = historial.map(a =>
        `[${new Date(a.created_at).toLocaleDateString('es-CO')}] ${a.tipo}: ${a.mensaje || ''} ${a.resultado ? '→ Respuesta: ' + a.resultado : ''}`
    ).join('\n');

    const systemPrompt = `Eres ${config.nombre_agente}, un asistente de ventas profesional de ${config.nombre_empresa}.
Tu objetivo es contactar leads, generar interés y agendar reuniones de ventas.

SOBRE EL PRODUCTO:
${config.descripcion_saas || 'Una plataforma SaaS para gestión de negocios.'}

REGLAS:
- Sé natural, amigable y conciso. Nunca seas invasivo ni presionado.
- Mensajes de WhatsApp cortos (máximo 3 oraciones).
- Si el lead muestra interés, propone la reunión inmediatamente.
- Si es el primer contacto, preséntate brevemente y pregunta si tiene interés.
- Si ya van 3+ intentos sin respuesta, actualiza a sin_respuesta y no contactes más.
- Personaliza cada mensaje con el nombre y empresa del lead.
- Escribe en español colombiano natural, sin formalidades excesivas.
- Nunca menciones que eres un agente de IA.`;

    const userPrompt = `LEAD:
- Nombre: ${lead.nombre}
- Empresa: ${lead.empresa || 'No especificada'}
- Teléfono: ${lead.telefono}
- Estado actual: ${lead.estado}
- Intentos de contacto: ${lead.intentos_contacto}
- Fuente: ${lead.fuente}

EVENTO: ${evento}
${mensajeRecibido ? `MENSAJE RECIBIDO DEL LEAD: "${mensajeRecibido}"` : ''}

HISTORIAL:
${historialTexto || 'Sin historial previo.'}

¿Qué acción debes tomar ahora?`;

    const response = await client.messages.create({
        model: 'claude-sonnet-4-6',
        max_tokens: 1024,
        system: systemPrompt,
        tools: TOOLS,
        messages: [{ role: 'user', content: userPrompt }]
    });

    const tokensUsados = response.usage?.input_tokens + response.usage?.output_tokens || 0;

    // Ejecutar las herramientas que el agente decidió usar
    for (const bloque of response.content) {
        if (bloque.type !== 'tool_use') continue;

        const { name, input } = bloque;

        if (name === 'enviar_whatsapp') {
            await whatsapp.enviarMensaje(lead.telefono, input.mensaje);
            await Lead.update(
                { ultimo_contacto: new Date(), intentos_contacto: lead.intentos_contacto + 1, estado: lead.estado === 'nuevo' ? 'contactado' : lead.estado },
                { where: { id: lead.id } }
            );
            await AgentActividad.create({
                lead_id: lead.id, tipo: 'mensaje_enviado', canal: 'whatsapp',
                mensaje: input.mensaje, tokens_usados: tokensUsados
            });
        }

        if (name === 'proponer_reunion') {
            const mensajeCompleto = `${input.mensaje_previo}\n\nAquí puedes escoger el horario: ${config.calendly_link || '[configura tu link de Calendly]'}`;
            await whatsapp.enviarMensaje(lead.telefono, mensajeCompleto);
            await Lead.update(
                { ultimo_contacto: new Date(), estado: 'interesado', link_reunion: config.calendly_link },
                { where: { id: lead.id } }
            );
            await AgentActividad.create({
                lead_id: lead.id, tipo: 'reunion_propuesta', canal: 'whatsapp',
                mensaje: mensajeCompleto, tokens_usados: tokensUsados
            });
        }

        if (name === 'actualizar_estado') {
            await Lead.update(
                { estado: input.estado, notas: input.nota ? (lead.notas ? lead.notas + '\n' + input.nota : input.nota) : lead.notas },
                { where: { id: lead.id } }
            );
            await AgentActividad.create({
                lead_id: lead.id, tipo: 'decision_agente', canal: 'sistema',
                mensaje: `Estado actualizado a: ${input.estado}. ${input.nota || ''}`, tokens_usados: tokensUsados
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

// Procesar un mensaje entrante de WhatsApp de un lead
async function procesarRespuestaWhatsApp(telefono, mensajeRecibido) {
    const numero = telefono.replace(/\D/g, '');
    const lead = await Lead.findOne({ where: { telefono: numero, agente_activo: true } });
    if (!lead) return; // No es un lead gestionado por el agente

    // Guardar respuesta
    await AgentActividad.create({
        lead_id: lead.id, tipo: 'respuesta_recibida', canal: 'whatsapp', resultado: mensajeRecibido
    });

    // Actualizar estado si aún no había respondido
    if (['nuevo','contactado'].includes(lead.estado)) {
        await Lead.update({ estado: 'respondio' }, { where: { id: lead.id } });
        lead.estado = 'respondio';
    }

    await procesarLead(lead, 'El lead respondió a nuestro mensaje', mensajeRecibido);
}

module.exports = { procesarLead, procesarRespuestaWhatsApp };
