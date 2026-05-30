const Anthropic = require('@anthropic-ai/sdk');
const { MensajeSocial, Reunion, Evento, Cliente, Licencia, Producto, Ticket } = require('../models');
const { Op } = require('sequelize');
const telegramService = require('./telegramService');

// ── Sistema dual: ventas para prospectos, soporte para clientes ───────────────

const SYSTEM_PROMPT = `Eres el asistente virtual de AI Company, una agencia de tecnología, IA y marketing digital con sede en Bogotá, Colombia. Representas a Cristian Gutiérrez y al equipo de AI Company.

## TU PRIMERA TAREA EN CADA CONVERSACIÓN
Cuando alguien te escriba, SIEMPRE usa la herramienta "identificar_cliente" primero (solo una vez por conversación).
- Si ES cliente: activa el MODO SERVICIO AL CLIENTE
- Si NO es cliente: activa el MODO VENTAS

---

## MODO SERVICIO AL CLIENTE (cuando es cliente existente)

Eres el soporte técnico y comercial de AI Company. El cliente ya compró y necesita ayuda.

### Cómo te comportas:
- Salúdalo por su nombre desde el primer mensaje
- Eres amable, paciente y resolutivo
- Mensajes cortos, claros y directos
- Siempre verifica el estado de su licencia antes de responder sobre problemas de acceso

### Qué puedes resolver:
**Problemas de acceso:**
- "No puedo entrar" → Verifica si la licencia está activa con ver_licencia. Si está vencida → da el link de pago. Si está activa → pide más detalles.
- "Mi sistema no funciona" → Verifica licencia. Si está activa, crea ticket de soporte.
- "Me sale error de licencia" → Verifica estado y fecha de vencimiento.

**Facturación y pagos:**
- "No me llega factura" → Explica que puede descargarla en su portal: {BASE_URL}/cliente/{token}
- "Quiero renovar" → Usa ver_licencia para obtener el link de pago directo
- "¿Cuánto cuesta?" → Muestra el precio mensual de su producto con los descuentos por volumen: 3m=5%, 6m=10%, 12m=15%

**Portal del cliente:**
- "¿Cómo entro a mi portal?" → Da el link del portal: {BASE_URL}/cliente/{token}
- "¿Cómo actualizo mis datos?" → Portal → pestaña "Mis datos"
- "¿Cómo descargo facturas?" → Portal → pestaña "Mis facturas" → botón PDF

**Problemas comunes (guía paso a paso):**
1. Sistema bloqueado → Renovar en: [link de pago]
2. No recibe emails → Revisar spam o actualizar email en portal → Mis datos
3. Cambiar contraseña de su sistema → Contactar soporte técnico (crear ticket)
4. Necesita instalar en nuevo dispositivo → Crear ticket con detalles del dispositivo

### Cuándo escalar:
- Si el problema es técnico y no puedes resolverlo en 2 intentos → usa "escalar_a_humano"
- Si el cliente está molesto o insiste en hablar con alguien → usa "escalar_a_humano"
- Si es un error grave o pérdida de datos → usa "escalar_a_humano" inmediatamente
- Si pide algo que no está en el sistema → usa "escalar_a_humano"

---

## MODO VENTAS (cuando NO es cliente)

Eres el mejor cerrador de ventas de Colombia. Tu único objetivo en cada conversación es agendar una reunión. No hay otro cierre posible — la reunión ES la venta.

### Quiénes somos:
La única agencia en Colombia que une marketing digital, IA, automatizaciones y desarrollo bajo un mismo equipo. No tercerizamos — ejecutamos de principio a fin.

### Servicios:
- SEO, Google Ads, Meta Ads, TikTok Ads
- Redes sociales, community management, branding
- Desarrollo web, landing pages, e-commerce, apps
- Automatización de ventas y operaciones
- Chatbots con IA para WhatsApp, Instagram y web
- Agentes de IA, análisis predictivo
- Consultoría y auditorías digitales

### Tu estrategia de cierre (SIEMPRE en este orden):
1. **Mensaje 1:** Saluda + haz UNA pregunta sobre su negocio (ej: "¿Qué tipo de negocio tienes?")
2. **Mensaje 2:** Conecta su respuesta con un beneficio concreto de nuestros servicios. Termina siempre con una pregunta que lleve hacia la reunión.
3. **Mensaje 3 en adelante:** Si mostró cualquier interés → usa ver_disponibilidad INMEDIATAMENTE y ofrece 3 horarios. No esperes a que él lo pida.
4. **Cierre:** Cuando confirme un horario → usa agendar_reunion al instante.

### Reglas de oro:
- Mensajes cortos: máximo 3 oraciones. Como WhatsApp real.
- NUNCA des precios por chat — "eso lo vemos en la reunión"
- NUNCA mandes listas largas de servicios
- Si responde con cualquier cosa positiva → ya es momento de proponer horario
- Atendemos 24 horas, 7 días. No hay "fuera de horario".
- Si dice "no tengo tiempo" → "La reunión son 15 minutos, ¿te queda bien hoy a las [hora] o mañana a las [hora]?"
- Si dice "no me interesa" → cambia el ángulo con un caso de éxito de su industria, luego vuelve a proponer horario
- Máximo 5 mensajes sin proponer reunión — al 3er mensaje ya debes haber ofrecido horarios

---

## REGLAS UNIVERSALES
1. Siempre en español colombiano, amigable y profesional
2. Máximo 3-4 oraciones por mensaje
3. Nunca mandes listas largas en modo ventas
4. Si el mensaje es "[Audio de voz]" → pide que escriba
5. Si empieza con "[Audio]: " → responde al contenido de forma natural`;

const TOOLS = [
    // ── Identificación ────────────────────────────────────────────────────────
    {
        name: 'identificar_cliente',
        description: 'Verifica si el número de teléfono del remitente es un cliente registrado en el sistema. SIEMPRE llamar primero al inicio de la conversación.',
        input_schema: {
            type: 'object',
            properties: {
                telefono: { type: 'string', description: 'Número de teléfono del remitente (el que envió el mensaje)' }
            },
            required: ['telefono']
        }
    },
    // ── Soporte al cliente ────────────────────────────────────────────────────
    {
        name: 'ver_licencia',
        description: 'Obtiene el estado completo de la licencia del cliente: activa/vencida, días restantes, fecha de vencimiento, precio mensual, link de pago y link del portal.',
        input_schema: {
            type: 'object',
            properties: {
                cliente_id: { type: 'number', description: 'ID del cliente obtenido con identificar_cliente' }
            },
            required: ['cliente_id']
        }
    },
    {
        name: 'crear_ticket_soporte',
        description: 'Crea un ticket de soporte en el sistema cuando el problema no se puede resolver por chat. El admin verá el ticket en el panel.',
        input_schema: {
            type: 'object',
            properties: {
                cliente_id: { type: 'number', description: 'ID del cliente' },
                asunto:     { type: 'string', description: 'Resumen breve del problema (máx 200 chars)' },
                mensaje:    { type: 'string', description: 'Descripción completa del problema reportado por el cliente' }
            },
            required: ['cliente_id', 'asunto', 'mensaje']
        }
    },
    {
        name: 'escalar_a_humano',
        description: 'Envía una alerta urgente al administrador por Telegram cuando el bot no puede resolver el problema del cliente. Úsala cuando el problema sea técnico complejo, el cliente esté molesto, o hayan pasado 2 intentos sin resolver.',
        input_schema: {
            type: 'object',
            properties: {
                cliente_nombre: { type: 'string', description: 'Nombre del cliente' },
                telefono:       { type: 'string', description: 'Número de teléfono del cliente' },
                problema:       { type: 'string', description: 'Descripción del problema que no se pudo resolver' },
                urgencia:       { type: 'string', description: 'alta | media | baja' }
            },
            required: ['cliente_nombre', 'telefono', 'problema']
        }
    },
    // ── Ventas ────────────────────────────────────────────────────────────────
    {
        name: 'ver_disponibilidad',
        description: 'Consulta la disponibilidad para reuniones en los próximos 14 días. Usar solo en modo ventas cuando el prospecto quiera reunirse.',
        input_schema: { type: 'object', properties: {}, required: [] }
    },
    {
        name: 'agendar_reunion',
        description: 'Agenda una reunión con el prospecto. Usar solo cuando el cliente confirme explícitamente fecha y hora.',
        input_schema: {
            type: 'object',
            properties: {
                nombre_cliente: { type: 'string' },
                fecha:          { type: 'string', description: 'ISO 8601: 2026-05-10T10:00:00' },
                duracion:       { type: 'number', description: 'Minutos (default 60)' },
                descripcion:    { type: 'string' }
            },
            required: ['nombre_cliente', 'fecha']
        }
    }
];

// ── Implementaciones de herramientas ─────────────────────────────────────────

async function ejecutarTool(nombre, input, msgCtx) {
    const BASE_URL = process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app';

    // ── identificar_cliente ────────────────────────────────────────────────
    if (nombre === 'identificar_cliente') {
        const tel = (input.telefono || '').replace(/\D/g, '');
        if (!tel) return JSON.stringify({ es_cliente: false });

        // Busca por número limpio o con prefijos colombianos
        const variantes = [tel, `57${tel}`, tel.replace(/^57/, '')];
        const cliente = await Cliente.findOne({
            where: { telefono: { [Op.in]: variantes }, activo: true }
        });

        if (!cliente) {
            return JSON.stringify({ es_cliente: false, mensaje: 'No encontrado en base de datos' });
        }

        const licencias = await Licencia.findAll({
            where: { cliente_id: cliente.id },
            include: [{ model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }],
            order: [['id', 'DESC']]
        });
        const licActiva = licencias.find(l => l.activo && new Date(l.fecha_vencimiento) >= new Date());

        return JSON.stringify({
            es_cliente:   true,
            cliente_id:   cliente.id,
            nombre:       cliente.nombre,
            empresa:      cliente.empresa || '',
            token_portal: cliente.token_portal || '',
            portal_url:   cliente.token_portal ? `${BASE_URL}/cliente/${cliente.token_portal}` : null,
            tiene_licencia_activa: !!licActiva,
            total_licencias: licencias.length
        });
    }

    // ── ver_licencia ───────────────────────────────────────────────────────
    if (nombre === 'ver_licencia') {
        const licencias = await Licencia.findAll({
            where: { cliente_id: input.cliente_id },
            include: [{ model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }],
            order: [['id', 'DESC']]
        });

        if (!licencias.length) return JSON.stringify({ error: 'No hay licencias registradas para este cliente' });

        const cliente = await Cliente.findByPk(input.cliente_id, { attributes: ['token_portal'] });
        const portalUrl = cliente?.token_portal ? `${BASE_URL}/cliente/${cliente.token_portal}` : null;

        const ahora = new Date();
        return JSON.stringify(licencias.map(l => {
            const vence  = new Date(l.fecha_vencimiento + 'T23:59:59');
            const dias   = Math.ceil((vence - ahora) / 86400000);
            const valida = l.activo && vence >= ahora;
            return {
                id:                l.id,
                producto:          l.producto?.nombre,
                precio_mensual:    l.producto?.precio_mensual,
                estado:            valida ? 'ACTIVA' : 'VENCIDA',
                fecha_vencimiento: l.fecha_vencimiento,
                dias_restantes:    dias,
                license_key:       l.license_key,
                pago_url:          `${BASE_URL}/pagar/${l.license_key}`,
                portal_url:        portalUrl
            };
        }));
    }

    // ── crear_ticket_soporte ───────────────────────────────────────────────
    if (nombre === 'crear_ticket_soporte') {
        const ticket = await Ticket.create({
            cliente_id: input.cliente_id,
            asunto:     input.asunto.slice(0, 200),
            mensaje:    input.mensaje
        });

        // Alerta Telegram al crear ticket
        telegramService.enviar(
            `🎫 *Nuevo ticket de soporte (WhatsApp)*\n\n*Asunto:* ${ticket.asunto}\n*Cliente ID:* ${input.cliente_id}\n*Ticket ID:* ${ticket.id}\n\n${input.mensaje.slice(0, 300)}`
        ).catch(() => {});

        return `Ticket #${ticket.id} creado exitosamente. El equipo de soporte lo revisará pronto.`;
    }

    // ── escalar_a_humano ───────────────────────────────────────────────────
    if (nombre === 'escalar_a_humano') {
        const urgEmoji = input.urgencia === 'alta' ? '🚨' : input.urgencia === 'media' ? '⚠️' : 'ℹ️';
        const msg = `${urgEmoji} *ESCALADO A HUMANO — Cliente requiere atención*\n\n` +
            `👤 *Cliente:* ${input.cliente_nombre}\n` +
            `📱 *Teléfono:* ${input.telefono}\n` +
            `🔴 *Urgencia:* ${input.urgencia || 'media'}\n\n` +
            `*Problema:*\n${input.problema}\n\n` +
            `_Por favor contacta al cliente directamente por WhatsApp._`;

        await telegramService.enviar(msg).catch(() => {});

        return 'Alerta enviada al equipo. Un asesor humano contactará al cliente.';
    }

    // ── ver_disponibilidad ─────────────────────────────────────────────────
    if (nombre === 'ver_disponibilidad') {
        const desde = new Date();
        const hasta = new Date(Date.now() + 14 * 24 * 60 * 60 * 1000);
        const reuniones = await Reunion.findAll({
            where: { fecha: { [Op.between]: [desde, hasta] }, estado: 'pendiente' },
            order: [['fecha', 'ASC']]
        });

        // Generar 3 opciones concretas en las próximas 48h (cualquier hora, 24/7)
        const ocupadas = new Set(reuniones.map(r => new Date(r.fecha).toISOString().slice(0, 16)));
        const opciones = [];
        let cursor = new Date(Date.now() + 60 * 60 * 1000); // +1h desde ahora
        cursor.setMinutes(0, 0, 0);

        while (opciones.length < 3) {
            cursor = new Date(cursor.getTime() + 60 * 60 * 1000);
            const key = cursor.toISOString().slice(0, 16);
            if (!ocupadas.has(key)) {
                opciones.push(new Date(cursor));
            }
        }

        const fmt = d => d.toLocaleDateString('es-CO', {
            weekday: 'long', day: 'numeric', month: 'long',
            hour: '2-digit', minute: '2-digit', timeZone: 'America/Bogota'
        });

        return JSON.stringify({
            disponible_24_7: true,
            opciones_sugeridas: opciones.map(d => ({
                iso: d.toISOString(),
                legible: fmt(d)
            })),
            instruccion: 'Ofrece estas 3 opciones al prospecto. Atendemos a cualquier hora, todos los días.'
        });
    }

    // ── agendar_reunion ────────────────────────────────────────────────────
    if (nombre === 'agendar_reunion') {
        const { nombre_cliente, fecha, duracion = 60, descripcion = '' } = input;
        const COLOMBIA_MS = 5 * 60 * 60 * 1000;
        const fechaInicio = new Date(new Date(fecha).getTime() + COLOMBIA_MS);
        const fechaFin    = new Date(fechaInicio.getTime() + duracion * 60000);
        const titulo      = `Reunión con ${nombre_cliente}`;

        const reunion = await Reunion.create({
            titulo, descripcion: descripcion || 'Agendado desde chat',
            fecha: fechaInicio, duracion, participantes: nombre_cliente, estado: 'pendiente'
        });
        Evento.create({
            titulo, descripcion: descripcion || '', fecha_inicio: fechaInicio,
            fecha_fin: fechaFin, color: '#6366f1', participantes: nombre_cliente, recordatorio: true
        }).catch(() => {});

        const fechaTexto = `${fechaInicio.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'America/Bogota' })} a las ${fechaInicio.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', timeZone: 'America/Bogota' })}`;

        telegramService.enviar(`📅 *Nueva reunión agendada*\n👤 ${nombre_cliente}\n🕐 ${fechaTexto}\n📝 ${descripcion.slice(0, 120)}`).catch(() => {});

        if (msgCtx?.remitente_id && msgCtx?.red) {
            MensajeSocial.update({ etiqueta: 'Reunión agendada' }, { where: { remitente_id: msgCtx.remitente_id, red: msgCtx.red } }).catch(() => {});
        }

        return `Reunión agendada: ${fechaTexto}. ID: ${reunion.id}.`;
    }

    return 'Herramienta no reconocida.';
}

// ── Motor de respuesta ────────────────────────────────────────────────────────

async function responder(msg) {
    console.log(`🤖 Auto-responder llamado — red=${msg?.red} AUTO_RESPONDER=${process.env.AUTO_RESPONDER}`);
    if (process.env.AUTO_RESPONDER !== 'true') return;
    if (!process.env.ANTHROPIC_API_KEY) { console.error('🤖 ANTHROPIC_API_KEY no configurado'); return; }
    if (!msg?.contenido) return;
    if (msg.respondido) return;

    console.log(`🤖 Procesando mensaje de ${msg.remitente} (${msg.red}): "${msg.contenido?.slice(0, 60)}"`);

    try {
        const anthropic = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

        const historial = await MensajeSocial.findAll({
            where: { remitente_id: msg.remitente_id, red: msg.red, id: { [Op.lt]: msg.id } },
            order: [['createdAt', 'DESC']], limit: 40
        });
        historial.reverse();

        const messages = [];
        for (const m of historial) {
            if (m.contenido && m.respuesta) {
                messages.push({ role: 'user',      content: m.contenido });
                messages.push({ role: 'assistant', content: m.respuesta });
            }
        }
        messages.push({ role: 'user', content: msg.contenido });

        const hoy = new Date().toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        const tel  = (msg.remitente_id || '').replace(/\D/g, '');
        const systemConContexto = SYSTEM_PROMPT +
            `\n\n## Contexto del mensaje actual\nHoy es ${hoy}.\nNúmero del remitente: ${tel}\nRed: ${msg.red}\n\nUSA identificar_cliente con telefono="${tel}" al inicio de la primera respuesta si aún no lo has hecho.`;

        let response = await anthropic.messages.create({
            model: 'claude-sonnet-4-6',
            max_tokens: 600,
            system: systemConContexto,
            tools: TOOLS,
            messages
        });

        while (response.stop_reason === 'tool_use') {
            const toolBlock = response.content.find(b => b.type === 'tool_use');
            console.log(`🔧 Tool: ${toolBlock.name}`, JSON.stringify(toolBlock.input).slice(0, 100));
            const resultado = await ejecutarTool(toolBlock.name, toolBlock.input, msg);
            console.log(`🔧 Resultado: ${String(resultado).slice(0, 120)}`);

            messages.push({ role: 'assistant', content: response.content });
            messages.push({ role: 'user', content: [{ type: 'tool_result', tool_use_id: toolBlock.id, content: resultado }] });

            response = await anthropic.messages.create({
                model: 'claude-sonnet-4-6',
                max_tokens: 600,
                system: systemConContexto,
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
                body: JSON.stringify({ access_token: process.env.META_PAGE_TOKEN, recipient: { id: msg.remitente_id }, message: { text: texto } })
            });
            const data = await r.json();
            if (!data.error) enviado = true;
            else console.error(`Auto-responder ${msg.red} error:`, data.error.message);

        } else if (msg.red === 'whatsapp' && process.env.WHATSAPP_TOKEN && process.env.WHATSAPP_PHONE_ID) {
            const r = await fetch(`https://graph.facebook.com/v21.0/${process.env.WHATSAPP_PHONE_ID}/messages`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${process.env.WHATSAPP_TOKEN}` },
                body: JSON.stringify({ messaging_product: 'whatsapp', to: msg.remitente_id, type: 'text', text: { body: texto } })
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

// ── Generador síncrono (para Make, webhooks externos) ────────────────────────
async function generarRespuesta(msg) {
    if (!process.env.ANTHROPIC_API_KEY || !msg?.contenido) return null;

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
    const tel  = (msg.remitente_id || '').replace(/\D/g, '');
    const systemConContexto = SYSTEM_PROMPT + `\n\n## Contexto\nHoy es ${hoy}.\nNúmero: ${tel}\nUSA identificar_cliente con telefono="${tel}" al inicio si aún no lo has hecho.`;

    let response = await anthropic.messages.create({
        model: 'claude-sonnet-4-6', max_tokens: 600, system: systemConContexto, tools: TOOLS, messages
    });

    while (response.stop_reason === 'tool_use') {
        const toolBlock = response.content.find(b => b.type === 'tool_use');
        const resultado = await ejecutarTool(toolBlock.name, toolBlock.input, msg);
        messages.push({ role: 'assistant', content: response.content });
        messages.push({ role: 'user', content: [{ type: 'tool_result', tool_use_id: toolBlock.id, content: resultado }] });
        response = await anthropic.messages.create({
            model: 'claude-sonnet-4-6', max_tokens: 600, system: systemConContexto, tools: TOOLS, messages
        });
    }

    return response.content.find(b => b.type === 'text')?.text?.trim() || null;
}

module.exports = { responder, generarRespuesta };
