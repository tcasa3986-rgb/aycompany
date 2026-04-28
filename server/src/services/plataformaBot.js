const TelegramBot = require('node-telegram-bot-api');
const Anthropic = require('@anthropic-ai/sdk');
const { Evento, MetricaMarketing, MetaMarketing, EstrategiaMarketing, IdeaContenido, Cliente, Licencia, Pago } = require('../models');
const { Op } = require('sequelize');

let bot = null;

const TOOLS = [
    {
        name: 'get_resumen_general',
        description: 'Obtiene un resumen general de AI Company: clientes, licencias activas, ingresos del mes',
        input_schema: { type: 'object', properties: {} }
    },
    {
        name: 'get_eventos_proximos',
        description: 'Lista los próximos eventos y reuniones agendados en el calendario',
        input_schema: {
            type: 'object',
            properties: { dias: { type: 'number', description: 'Días hacia adelante a buscar (default 7)' } }
        }
    },
    {
        name: 'get_marketing',
        description: 'Obtiene métricas de marketing, metas con progreso y estrategias activas',
        input_schema: { type: 'object', properties: {} }
    },
    {
        name: 'get_contenido',
        description: 'Lista las ideas de contenido por estado',
        input_schema: {
            type: 'object',
            properties: { estado: { type: 'string', description: 'idea | en_progreso | publicado | descartado | todas' } }
        }
    },
    {
        name: 'crear_evento',
        description: 'Crea un nuevo evento en el calendario',
        input_schema: {
            type: 'object',
            required: ['titulo', 'fecha_inicio'],
            properties: {
                titulo:        { type: 'string' },
                fecha_inicio:  { type: 'string', description: 'ISO: 2025-05-01T10:00:00' },
                fecha_fin:     { type: 'string' },
                descripcion:   { type: 'string' },
                participantes: { type: 'string' },
                link:          { type: 'string' },
                recordatorio:  { type: 'boolean' }
            }
        }
    },
    {
        name: 'registrar_metrica',
        description: 'Registra métricas de redes sociales (seguidores, alcance, interacciones)',
        input_schema: {
            type: 'object',
            required: ['plataforma', 'fecha'],
            properties: {
                plataforma:    { type: 'string' },
                fecha:         { type: 'string', description: 'YYYY-MM-DD' },
                seguidores:    { type: 'number' },
                alcance:       { type: 'number' },
                interacciones: { type: 'number' },
                publicaciones: { type: 'number' },
                notas:         { type: 'string' }
            }
        }
    },
    {
        name: 'actualizar_meta',
        description: 'Actualiza el valor actual de una meta de marketing por su ID',
        input_schema: {
            type: 'object',
            required: ['id', 'valor_actual'],
            properties: {
                id:           { type: 'number' },
                valor_actual: { type: 'number' }
            }
        }
    },
    {
        name: 'agregar_idea_contenido',
        description: 'Agrega una nueva idea de contenido',
        input_schema: {
            type: 'object',
            required: ['titulo', 'canal', 'formato'],
            properties: {
                titulo:            { type: 'string' },
                canal:             { type: 'string' },
                formato:           { type: 'string' },
                descripcion:       { type: 'string' },
                fecha_publicacion: { type: 'string' }
            }
        }
    }
];

async function ejecutarTool(name, input) {
    const hoy = new Date();

    if (name === 'get_resumen_general') {
        const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        const [totalClientes, licenciasActivas, pagosMes] = await Promise.all([
            Cliente.count(),
            Licencia.count({ where: { estado: 'activa' } }),
            Pago.findAll({ where: { createdAt: { [Op.gte]: inicioMes } } })
        ]);
        const ingresosMes = pagosMes.reduce((s, p) => s + Number(p.monto || 0), 0);
        return `📊 *Resumen AI Company*\n\n👥 Clientes: *${totalClientes}*\n🔑 Licencias activas: *${licenciasActivas}*\n💰 Ingresos este mes: *$${ingresosMes.toLocaleString('es-CO')}*\n📅 ${hoy.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}`;
    }

    if (name === 'get_eventos_proximos') {
        const dias = input.dias || 7;
        const hasta = new Date(hoy);
        hasta.setDate(hasta.getDate() + dias);
        const eventos = await Evento.findAll({
            where: { fecha_inicio: { [Op.between]: [hoy, hasta] } },
            order: [['fecha_inicio', 'ASC']]
        });
        if (!eventos.length) return `📅 Sin eventos en los próximos ${dias} días.`;
        return `📅 *Próximos ${dias} días:*\n\n` + eventos.map(e => {
            const f = new Date(e.fecha_inicio).toLocaleDateString('es-CO', { weekday: 'short', day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
            return `• *${e.titulo}*\n  🕐 ${f}${e.participantes ? `\n  👥 ${e.participantes}` : ''}${e.link ? `\n  🔗 ${e.link}` : ''}`;
        }).join('\n\n');
    }

    if (name === 'get_marketing') {
        const [metas, estrategias, metricas] = await Promise.all([
            MetaMarketing.findAll({ where: { completada: false } }),
            EstrategiaMarketing.findAll({ where: { estado: 'activa' } }),
            MetricaMarketing.findAll({ order: [['fecha', 'DESC']], limit: 6 })
        ]);
        let txt = '📈 *Marketing — AI Company*\n\n';
        if (metas.length) {
            txt += '*🎯 Metas activas:*\n';
            metas.forEach(m => {
                const pct = Math.min(100, Math.round((m.valor_actual / m.valor_meta) * 100));
                const llenas = Math.floor(pct / 10);
                const barra = '█'.repeat(llenas) + '░'.repeat(10 - llenas);
                txt += `• ${m.plataforma} — ${m.metrica}: ${Number(m.valor_actual).toLocaleString()}/${Number(m.valor_meta).toLocaleString()}\n  [${barra}] ${pct}%\n`;
            });
        }
        if (estrategias.length) {
            txt += `\n*⚡ Estrategias activas (${estrategias.length}):*\n`;
            estrategias.slice(0, 4).forEach(e => { txt += `• ${e.titulo} — ${e.canal}\n`; });
        }
        if (metricas.length) {
            txt += '\n*📊 Últimas métricas:*\n';
            metricas.forEach(m => { txt += `• ${m.plataforma} (${m.fecha}): ${Number(m.seguidores || 0).toLocaleString()} seguidores\n`; });
        }
        return txt.trim() || 'Sin datos de marketing registrados aún.';
    }

    if (name === 'get_contenido') {
        const where = input.estado && input.estado !== 'todas' ? { estado: input.estado } : {};
        const ideas = await IdeaContenido.findAll({ where, order: [['createdAt', 'DESC']], limit: 10 });
        if (!ideas.length) return 'Sin ideas de contenido registradas.';
        const emojis = { idea: '💡', en_progreso: '⚡', publicado: '✅', descartado: '🗑' };
        return `💡 *Ideas de contenido:*\n\n` + ideas.map(i => `${emojis[i.estado] || '•'} *${i.titulo}*\n  📱 ${i.canal} / ${i.formato}`).join('\n\n');
    }

    if (name === 'crear_evento') {
        const evento = await Evento.create({ ...input, color: '#6366f1' });
        const f = new Date(evento.fecha_inicio).toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' });
        return `✅ *Evento creado en el calendario*\n📌 ${evento.titulo}\n📅 ${f}${evento.participantes ? `\n👥 ${evento.participantes}` : ''}`;
    }

    if (name === 'registrar_metrica') {
        await MetricaMarketing.create(input);
        return `✅ *Métricas registradas — ${input.plataforma}*\n📅 ${input.fecha}\n👥 Seguidores: ${Number(input.seguidores || 0).toLocaleString()}\n👁 Alcance: ${Number(input.alcance || 0).toLocaleString()}\n❤️ Interacciones: ${Number(input.interacciones || 0).toLocaleString()}`;
    }

    if (name === 'actualizar_meta') {
        const meta = await MetaMarketing.findByPk(input.id);
        if (!meta) return '❌ Meta no encontrada. Usa "ver metas" para ver los IDs.';
        await meta.update({ valor_actual: input.valor_actual });
        const pct = Math.min(100, Math.round((input.valor_actual / meta.valor_meta) * 100));
        const llenas = Math.floor(pct / 10);
        const barra = '█'.repeat(llenas) + '░'.repeat(10 - llenas);
        return `✅ *Meta actualizada*\n${meta.plataforma} — ${meta.metrica}\n${Number(input.valor_actual).toLocaleString()} / ${Number(meta.valor_meta).toLocaleString()}\n[${barra}] ${pct}%`;
    }

    if (name === 'agregar_idea_contenido') {
        const idea = await IdeaContenido.create({ ...input, estado: 'idea' });
        return `✅ *Idea de contenido agregada*\n💡 ${idea.titulo}\n📱 ${idea.canal} / ${idea.formato}`;
    }

    return 'Acción no reconocida.';
}

async function procesarMensaje(texto) {
    if (!process.env.ANTHROPIC_API_KEY) return '⚠️ Claude no configurado. Contacta al administrador.';
    const anthropic = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

    const hoy = new Date().toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    const sistema = `Eres el asistente inteligente de AI Company, una empresa de tecnología y marketing digital. Hoy es ${hoy}.

Tienes acceso a toda la información de la empresa: eventos del calendario, métricas y metas de marketing, estrategias, ideas de contenido, clientes y licencias.

Cuando el usuario pida información, usa las herramientas para obtener datos reales y actualizados. Cuando quiera registrar o crear algo, usa la herramienta correspondiente.

Sé conciso, útil y profesional. Responde en español. Usa formato Markdown de Telegram: *negrita* para títulos importantes.`;

    const messages = [{ role: 'user', content: texto }];

    let response = await anthropic.messages.create({
        model: 'claude-haiku-4-5-20251001',
        max_tokens: 1024,
        system: sistema,
        tools: TOOLS,
        messages
    });

    while (response.stop_reason === 'tool_use') {
        const toolUses = response.content.filter(b => b.type === 'tool_use');
        const toolResults = [];
        for (const tu of toolUses) {
            const resultado = await ejecutarTool(tu.name, tu.input);
            toolResults.push({ type: 'tool_result', tool_use_id: tu.id, content: resultado });
        }
        messages.push({ role: 'assistant', content: response.content });
        messages.push({ role: 'user', content: toolResults });
        response = await anthropic.messages.create({
            model: 'claude-haiku-4-5-20251001',
            max_tokens: 1024,
            system: sistema,
            tools: TOOLS,
            messages
        });
    }

    return response.content.filter(b => b.type === 'text').map(b => b.text).join('\n') || '✅ Listo.';
}

function initBot() {
    const token = process.env.PLATAFORMA_TELEGRAM_TOKEN;
    if (!token) { console.log('ℹ️  AI Company Bot: sin token configurado.'); return; }

    bot = new TelegramBot(token, { polling: true });
    console.log('🤖 AI Company Bot activo en Telegram');

    const chatId = process.env.PLATAFORMA_TELEGRAM_CHAT_ID;

    bot.onText(/\/start/, (msg) => {
        if (chatId && String(msg.chat.id) !== String(chatId)) return;
        bot.sendMessage(msg.chat.id,
            `🤖 *Bienvenido al asistente de AI Company*\n\nPuedo ayudarte con información y acciones en tiempo real:\n\n📊 _"¿Cómo vamos este mes?"_\n📅 _"¿Qué tengo esta semana?"_\n📈 _"¿Cómo van nuestras metas de marketing?"_\n💡 _"¿Qué ideas de contenido tenemos?"_\n\n*También puedo actualizar datos:*\n\n📅 _"Crea una reunión mañana a las 3pm con Juan"_\n📊 _"Registra 800 seguidores en Instagram hoy"_\n💡 _"Agrega una idea: reel sobre beneficios del software"_\n\nEscríbeme en lenguaje natural 👇`,
            { parse_mode: 'Markdown' }
        );
    });

    bot.on('message', async (msg) => {
        if (!msg.text || msg.text.startsWith('/start')) return;
        if (chatId && String(msg.chat.id) !== String(chatId)) return;

        bot.sendChatAction(msg.chat.id, 'typing');
        const typing = setInterval(() => bot.sendChatAction(msg.chat.id, 'typing'), 4000);

        try {
            const respuesta = await procesarMensaje(msg.text);
            clearInterval(typing);
            await bot.sendMessage(msg.chat.id, respuesta, { parse_mode: 'Markdown' });
        } catch (err) {
            clearInterval(typing);
            console.error('AI Company Bot error:', err.message);
            bot.sendMessage(msg.chat.id, '⚠️ Ocurrió un error procesando tu mensaje. Intenta de nuevo.');
        }
    });
}

module.exports = { initBot };
