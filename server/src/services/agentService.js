const Anthropic = require('@anthropic-ai/sdk');
const { Lead, AgentActividad, AgenteConfig, Reunion, MensajeSocial } = require('../models');
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

    // Detectar si el lead tiene web desde las notas
    const tieneWeb = !!(lead.notas && lead.notas.includes('Web:'));
    const urlWebMatch = lead.notas && lead.notas.match(/Web:\s*(https?:\/\/[^\s]+)/);
    const urlWeb = urlWebMatch ? urlWebMatch[1] : null;

    // Extraer ciudad y categoría de las notas
    const categoriaMatch = lead.notas && lead.notas.match(/^([^.]+)\s+en\s+([^.]+)\./);
    const categoriaNegocio = categoriaMatch ? categoriaMatch[1] : (lead.fuente === 'apollo' ? 'empresa' : 'negocio');
    const ciudadLead = categoriaMatch ? categoriaMatch[2] : 'Colombia';

    const systemPrompt = `Eres Cristian, asesor comercial independiente. Contactas negocios para ofrecerles soluciones digitales.

SOBRE LO QUE OFRECES:
${config.descripcion_saas || `Desarrollo de páginas web, posicionamiento en Google (SEO), pauta en redes sociales (Facebook, Instagram, TikTok, Google Ads), sistemas de gestión a medida y automatizaciones para negocios.`}

REGLAS DE MENSAJE — MUY IMPORTANTES:
- MÁXIMO 2 oraciones. Si escribes más, fallas.
- CERO emojis. Ninguno. Suenan a spam.
- Termina siempre en una pregunta corta que se responda con sí/no.
- No te presentes como empresa en el primer mensaje, solo como Cristian.
- Nunca menciones "AI Company" en primer contacto.
- Tono: persona real escribiendo desde el celular, no vendedor corporativo.
- Nunca digas que eres un bot o IA.

ESTRATEGIA SEGÚN SITUACIÓN DEL LEAD:

PRIMER CONTACTO — sin página web:
→ Menciona que buscaste "${categoriaNegocio} en ${ciudadLead}" en Google y no aparecen. Di que su competencia sí tiene web y se lleva esos clientes. Pregunta si les interesa tener una página que los posicione.
→ Ejemplo: "Hola, busqué ${categoriaNegocio} en ${ciudadLead} y [empresa] no tiene página web mientras sus competidores sí aparecen en Google. ¿Les interesa una que les traiga clientes nuevos cada mes?"
→ En el seguimiento si no respondieron: refuerza el dolor — cada día sin web es un cliente más que se va a la competencia. Invita a una llamada de 15 minutos para mostrarles cómo quedaría.

PRIMER CONTACTO — con página web:
→ Menciona que viste su web y tiene algo mejorable (velocidad, posicionamiento, sin chat de WhatsApp, diseño antiguo). Pregunta si les interesa mejorar eso.
→ Ejemplo: "Hola, entré a la web de [empresa] y no está posicionada en Google para búsquedas de ${categoriaNegocio} en ${ciudadLead}. ¿Les interesa que eso cambie?"

SEGUIMIENTO (ya contactado, sin respuesta):
→ Mensaje completamente diferente al anterior. Ángulo nuevo: resultado concreto o caso de éxito.
→ Ejemplo: "Cristian de nuevo, hace poco le hicimos el SEO a una ${categoriaNegocio} similar en ${ciudadLead} y en 3 meses duplicaron las visitas. ¿Vale la pena hablar 20 minutos?"

LEAD INTERESADO (respondió con interés, primer intento de agendar):
→ Antes de proponer la reunión, haz UNA sola pregunta de calificación para tener contexto:
  - Si no tiene web: "¿Cuántas personas trabajan en [empresa] aproximadamente?"
  - Si tiene web: "¿Actualmente están invirtiendo en publicidad o es solo la web que tienen?"
→ Solo una pregunta. Breve. No más.

LEAD YA DIO INFORMACIÓN DE CALIFICACIÓN (ya respondió la pregunta anterior):
→ Ahora sí propone la reunión: "Perfecto, ¿tienen disponibilidad esta semana para una llamada de 20 minutos?"

LEAD CONFIRMA FECHA Y HORA:
→ Usa agendar_reunion para crear la cita en el calendario. Incluye en mensaje_confirmacion la fecha, hora y un Google Meet o llamada normal.

ÚLTIMO INTENTO (${config.max_intentos}+ sin respuesta):
→ Cierre amable y diferente: "Entiendo que están ocupados. Si en algún momento quieren que [empresa] aparezca primero en Google, aquí estoy. ¿Prefieren que los contacte en otro momento?"
→ Luego usa actualizar_estado con sin_respuesta.

CANALES: WhatsApp si hay teléfono. Email si hay correo. Si ambos, WhatsApp primero.
Fecha y hora actual: ${ahora}`;

    const userPrompt = `LEAD:
- Nombre: ${lead.nombre}
- Empresa: ${lead.empresa || 'No especificada'}
- Teléfono: ${lead.telefono || 'No disponible'}
- Email: ${lead.email || 'No disponible'}
- Estado actual: ${lead.estado}
- Intentos de contacto: ${lead.intentos_contacto}
- Fuente: ${lead.fuente}
- Tiene página web: ${tieneWeb ? `SÍ — ${urlWeb || 'URL en notas'}` : 'NO — oportunidad de ofrecerles una'}
${lead.notas ? `- Notas: ${lead.notas}` : ''}

EVENTO: ${evento}
${mensajeRecibido ? `MENSAJE DEL LEAD: "${mensajeRecibido}"` : ''}

HISTORIAL:
${historialTexto || 'Sin historial previo — primer contacto.'}

Recuerda: máximo 2 oraciones, cero emojis, termina en pregunta sí/no. ¿Qué acción tomas ahora?`;

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
            // WhatsApp
            let waError = null;
            if (lead.telefono) {
                try {
                    await whatsapp.enviarMensaje(lead.telefono, input.mensaje);
                } catch (e) {
                    waError = e.message;
                    console.error(`[Agente] WhatsApp error lead ${lead.id}:`, e.message);
                }
                await AgentActividad.create({
                    lead_id: lead.id,
                    tipo: waError ? 'error' : 'mensaje_enviado',
                    canal: 'whatsapp',
                    mensaje: input.mensaje,
                    resultado: waError ? `Error: ${waError}` : null,
                    tokens_usados: tokensUsados
                });
                // Registrar en Bandeja Social para verlo como conversación
                await MensajeSocial.create({
                    red: 'whatsapp', tipo: 'mensaje',
                    remitente: lead.nombre || lead.empresa,
                    remitente_id: lead.telefono,
                    contenido: null,
                    respuesta: input.mensaje,
                    leido: true, respondido: true,
                }).catch(() => {});
            }
            // Email automático si tiene correo
            if (lead.email && config.gmail_user && config.gmail_app_password) {
                let emailError = null;
                try {
                    await enviarEmail({
                        gmailUser: config.gmail_user, gmailPass: config.gmail_app_password,
                        nombreAgente: config.nombre_agente || 'Cristian',
                        nombreEmpresa: config.nombre_empresa || 'AI Company',
                        to: lead.email, subject: `Hola ${lead.nombre}, ¿buscas crecer en internet?`,
                        body: input.mensaje,
                    });
                } catch (e) {
                    emailError = e.message;
                }
                await AgentActividad.create({
                    lead_id: lead.id,
                    tipo: emailError ? 'error' : 'mensaje_enviado',
                    canal: 'email',
                    mensaje: input.mensaje,
                    resultado: emailError ? `Error: ${emailError}` : null,
                    tokens_usados: 0
                });
            }
            await Lead.update(
                { ultimo_contacto: new Date(), intentos_contacto: lead.intentos_contacto + 1, estado: lead.estado === 'nuevo' ? 'contactado' : lead.estado },
                { where: { id: lead.id } }
            );
        }

        if (name === 'agendar_reunion') {
            // Construir brief de cierre con todo el contexto del lead
            const resumenConversacion = historial
                .filter(a => ['respuesta_recibida', 'mensaje_enviado'].includes(a.tipo))
                .map(a => `  [${a.tipo === 'respuesta_recibida' ? 'LEAD' : 'Cristian'}] ${a.mensaje || a.resultado || ''}`)
                .join('\n');

            const descripcionReunion = [
                `=== BRIEF PARA CIERRE DE VENTAS ===`,
                ``,
                `CONTACTO:`,
                `  Nombre: ${lead.nombre}`,
                `  Empresa: ${lead.empresa || 'No especificada'}`,
                `  Teléfono: ${lead.telefono || '—'}`,
                `  Email: ${lead.email || '—'}`,
                `  Fuente: ${lead.fuente}`,
                ``,
                `SITUACIÓN DIGITAL:`,
                `  Página web: ${tieneWeb ? `SÍ — ${urlWeb || 'ver notas'}` : 'NO TIENE — oportunidad de venta directa'}`,
                `  Categoría: ${categoriaNegocio} en ${ciudadLead}`,
                ``,
                `CONVERSACIÓN PREVIA:`,
                resumenConversacion || '  (sin mensajes registrados)',
                ``,
                `NOTAS ORIGINALES DEL LEAD:`,
                `  ${lead.notas || '—'}`,
                ``,
                `Intentos de contacto antes de agendar: ${lead.intentos_contacto}`,
            ].join('\n');

            await Reunion.create({
                titulo:        `${lead.empresa || lead.nombre} — ${categoriaNegocio} en ${ciudadLead}`,
                descripcion:   descripcionReunion,
                fecha:         new Date(input.fecha_iso),
                duracion:      input.duracion_minutos || 30,
                participantes: lead.nombre,
                estado:        'pendiente',
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
            // Email
            if (lead.email && config.gmail_user && config.gmail_app_password) {
                let emailError = null;
                try {
                    await enviarEmail({
                        gmailUser: config.gmail_user, gmailPass: config.gmail_app_password,
                        nombreAgente: config.nombre_agente || 'Cristian',
                        nombreEmpresa: config.nombre_empresa || 'AI Company',
                        to: lead.email, subject: input.asunto, body: input.cuerpo,
                    });
                } catch (e) {
                    emailError = e.message;
                    console.error(`[Agente] Email error lead ${lead.id}:`, e.message);
                }
                await AgentActividad.create({
                    lead_id: lead.id,
                    tipo: emailError ? 'error' : 'mensaje_enviado',
                    canal: 'email',
                    mensaje: `[${input.asunto}] ${input.cuerpo}`,
                    resultado: emailError ? `Error: ${emailError}` : null,
                    tokens_usados: tokensUsados,
                });
            }
            // WhatsApp automático si tiene teléfono
            if (lead.telefono) {
                let waError = null;
                try {
                    await whatsapp.enviarMensaje(lead.telefono, input.cuerpo);
                } catch (e) {
                    waError = e.message;
                }
                await AgentActividad.create({
                    lead_id: lead.id,
                    tipo: waError ? 'error' : 'mensaje_enviado',
                    canal: 'whatsapp',
                    mensaje: input.cuerpo,
                    resultado: waError ? `Error: ${waError}` : null,
                    tokens_usados: 0
                });
            }
            await Lead.update(
                { ultimo_contacto: new Date(), intentos_contacto: lead.intentos_contacto + 1, estado: lead.estado === 'nuevo' ? 'contactado' : lead.estado },
                { where: { id: lead.id } }
            );
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
