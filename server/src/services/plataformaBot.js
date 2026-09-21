const TelegramBot = require('node-telegram-bot-api');
const Anthropic = require('@anthropic-ai/sdk');
const { Evento, MetricaMarketing, MetaMarketing, EstrategiaMarketing, IdeaContenido, Cliente, Licencia, Pago, MovimientoFinanciero, Reunion } = require('../models');
const { interpretar } = require('./registroRapido');
const { transcribir } = require('./transcribirAudio');
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

/**
 * Toma lo que mande el modelo y lo trata como hora de Colombia.
 *
 * "2026-09-22T09:00:00", "2026-09-22T09:00:00Z" y "2026-09-22 09:00" son las
 * nueve de la mañana en Bogotá, las tres. Si el modelo pega una Z o un offset,
 * se ignora: el reloj que importa es el de Cristian, no el del servidor.
 */
function aHoraColombia(valor) {
    if (!valor) return valor;
    const t = String(valor).trim().replace(' ', 'T');
    const m = t.match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(?::(\d{2}))?/);
    if (!m) return valor;   // formato raro: que lo resuelva Sequelize como pueda
    const [, y, mes, dia, hh, mm, ss] = m;
    return new Date(Date.UTC(+y, +mes - 1, +dia, +hh + 5, +mm, +(ss || 0)));
}

async function ejecutarTool(name, input) {
    const hoy = new Date();

    if (name === 'get_resumen_general') {
        // Ojo con los nombres: Licencia no tiene columna `estado` (se calcula) y
        // Pago guarda la fecha en `fecha_pago`, no en `createdAt`. Con los dos
        // nombres viejos esta herramienta reventaba con "Unknown column".
        const d1 = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        const inicioMes = `${d1.getFullYear()}-${String(d1.getMonth() + 1).padStart(2, '0')}-01`;
        const [totalClientes, licenciasActivas, pagosMes] = await Promise.all([
            Cliente.count(),
            Licencia.count({ where: { activo: true } }),
            Pago.findAll({ where: { fecha_pago: { [Op.gte]: inicioMes } } })
        ]);
        const ingresosMes = pagosMes.reduce((s, p) => s + Number(p.monto || 0), 0);
        return `📊 *Resumen AI Company*\n\n👥 Clientes: *${totalClientes}*\n🔑 Licencias activas: *${licenciasActivas}*\n💰 Ingresos este mes: *$${ingresosMes.toLocaleString('es-CO')}*\n📅 ${hoy.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'America/Bogota' })}`;
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
            const f = new Date(e.fecha_inicio).toLocaleDateString('es-CO', { weekday: 'short', day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit', timeZone: 'America/Bogota' });
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
        // Hay DOS tablas de calendario: `eventos` es la que pinta la pantalla de
        // Calendario, y `reuniones` es la que miran Hoy, el widget y el
        // recordatorio de cada minuto. La app, cuando agenda, crea la reunion y
        // espeja el evento. El bot solo creaba el evento: por eso una reunion
        // dictada por voz salia en el calendario pero NO en el widget, y nunca
        // llegaba el recordatorio.
        // La hora SIEMPRE se entiende como hora de Colombia, venga como venga.
        // El modelo a veces manda "...T09:00:00Z" y eso quedaba a las 4 a.m.
        const entrada = { ...input };
        entrada.fecha_inicio = aHoraColombia(input.fecha_inicio);
        if (input.fecha_fin) entrada.fecha_fin = aHoraColombia(input.fecha_fin);

        const evento = await Evento.create({ ...entrada, color: '#6366f1' });
        const dura = input.fecha_fin
            ? Math.max(15, Math.round((new Date(input.fecha_fin) - new Date(input.fecha_inicio)) / 60000))
            : 60;
        await Reunion.create({
            titulo:        evento.titulo,
            descripcion:   evento.descripcion || '',
            fecha:         evento.fecha_inicio,
            duracion:      dura,
            participantes: evento.participantes || '',
            link:          evento.link || '',
        }).catch(err => console.warn('Bot: no pude espejar la reunion:', err.message));
        const f = new Date(evento.fecha_inicio).toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit', timeZone: 'America/Bogota' });
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

/**
 * El asistente, con dos proveedores.
 *
 * Por que: la noche del 20/09 el bot oyo perfecto una orden por voz y contesto
 * un error - "Your credit balance is too low to access the Anthropic API".
 * Un asistente que depende del saldo de UNA cuenta se cae entero el dia que esa
 * cuenta se seca. Se intenta con Claude y, si falla por lo que sea, con OpenAI,
 * que ya se usa para transcribir los audios.
 */
async function procesarMensaje(texto) {
    const hayClaude = !!process.env.ANTHROPIC_API_KEY;
    const hayOpenAI = !!process.env.OPENAI_API_KEY;
    if (!hayClaude && !hayOpenAI) return '⚠️ No hay ningun asistente configurado. Contacta al administrador.';

    if (hayClaude) {
        try {
            return await conClaude(texto);
        } catch (err) {
            console.warn('Bot: Claude fallo (' + String(err.message).slice(0, 120) + '). Paso a OpenAI.');
            if (!hayOpenAI) throw err;
        }
    }
    return conOpenAI(texto);
}

/**
 * El prompt es el mismo para los dos proveedores: que respondan igual.
 *
 * Lo importante de aqui es que NO interrogue. Un asistente que pregunta la hora,
 * el lugar y la duracion antes de crear una reunion cuesta mas trabajo que
 * abrir la app. Actua con un valor razonable y dice que asumio, para poder
 * corregirlo en un mensaje.
 */
function instrucciones() {
    const ahora = new Date(Date.now() - 5 * 3600000);   // Bogota
    const iso = ahora.toISOString().split('T')[0];
    const largo = ahora.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' });

    // Los modelos se equivocan contando dias: al pedirle "el martes" puso el 27,
    // que era domingo. Aqui va el calendario ya resuelto para que no calcule
    // nada, solo lea.
    const DIAS = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
    const proximos = [];
    for (let i = 0; i <= 14; i++) {
        const dd = new Date(ahora.getTime() + i * 86400000);
        const etiqueta = i === 0 ? ' (HOY)' : i === 1 ? ' (mañana)' : '';
        proximos.push(`${DIAS[dd.getUTCDay()]} ${dd.getUTCDate()} = ${dd.toISOString().split('T')[0]}${etiqueta}`);
    }
    const calendario = proximos.join('\n');

    return `Eres el asistente de AI Company, la empresa de Cristian. Hoy es ${largo} (${iso}), hora de Colombia.

Tienes acceso a los datos reales: calendario, marketing, contenido, clientes y licencias. Usa las herramientas para consultar y para registrar.

REGLA PRINCIPAL: NO PREGUNTES. ACTUA.
Cristian te habla casi siempre por nota de voz, muchas veces manejando. Cada pregunta tuya le cuesta otro audio. Si falta un dato, pon el valor razonable, hazlo, y al final dile en una linea que asumiste.

Valores por defecto cuando no te los digan:
- Hora de una reunion: 9:00 a.m.
- Duracion: 1 hora.
- Fechas: NO las calcules. Estan resueltas abajo, copia la que corresponda.
- Las horas son de Colombia y se escriben SIN zona horaria ni "Z". "2026-09-22T09:00:00" es las 9 de la mañana en Colombia.
- Las horas que te devuelven las herramientas YA estan en hora de Colombia: repitelas tal cual, no las conviertas.

CALENDARIO (usa estas fechas tal cual):
${calendario}

- Participantes, link, descripcion: dejalos vacios si no los menciono.
- Si nombra un cliente que ya existe, usalo tal cual esta guardado.

Solo pregunta si de verdad no se puede seguir: cuando no entiendes que te pidio, o cuando hay dos opciones muy distintas y elegir mal haria dano. Nunca pidas confirmacion antes de crear algo: crealo y avisa.

Responde corto, maximo 4 lineas, en español, con Markdown de Telegram (*negrita*). Nada de "¿deseas que...?" ni "¿te gustaria que...?": si se puede hacer, ya lo hiciste.`;
}

async function conClaude(texto) {
    const anthropic = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });
    const sistema = instrucciones();
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

/** Las mismas herramientas, en el formato que espera OpenAI. */
function herramientasOpenAI() {
    return TOOLS.map(t => ({
        type: 'function',
        function: { name: t.name, description: t.description, parameters: t.input_schema },
    }));
}

async function conOpenAI(texto) {
    const OpenAI = require('openai');
    const openai = new OpenAI({ apiKey: process.env.OPENAI_API_KEY });
    const mensajes = [
        { role: 'system', content: instrucciones() },
        { role: 'user', content: texto },
    ];
    const herramientas = herramientasOpenAI();

    // Tope de vueltas: si el modelo se enreda pidiendo herramientas, se corta en
    // vez de quedarse girando y gastando.
    for (let vuelta = 0; vuelta < 6; vuelta++) {
        const r = await openai.chat.completions.create({
            model: 'gpt-4o-mini',
            messages: mensajes,
            tools: herramientas,
            max_tokens: 1024,
        });
        const m = r.choices[0].message;
        if (!m.tool_calls || m.tool_calls.length === 0) {
            return (m.content || '').trim() || 'Listo.';
        }
        mensajes.push(m);
        for (const llamada of m.tool_calls) {
            let entrada = {};
            try { entrada = JSON.parse(llamada.function.arguments || '{}'); } catch (e) { /* el modelo mando basura */ }
            const resultado = await ejecutarTool(llamada.function.name, entrada);
            mensajes.push({ role: 'tool', tool_call_id: llamada.id, content: String(resultado) });
        }
    }
    return 'Me enrede con esa. Me la dices de otra forma?';
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
            `🤖 *Bienvenido al asistente de AI Company*\n\nPuedo ayudarte con información y acciones en tiempo real:\n\n📊 _"¿Cómo vamos este mes?"_\n📅 _"¿Qué tengo esta semana?"_\n📈 _"¿Cómo van nuestras metas de marketing?"_\n💡 _"¿Qué ideas de contenido tenemos?"_\n\n*Para anotar plata* (sin gastar tokens, responde de una):

_"gasté 30000 gasolina"_
_"me pagaron 250000 de mensualidad"_
_"recibí 750000 del alquiler moto"_

*También puedo actualizar datos:*\n\n📅 _"Crea una reunión mañana a las 3pm con Juan"_\n📊 _"Registra 800 seguidores en Instagram hoy"_\n💡 _"Agrega una idea: reel sobre beneficios del software"_\n\nEscríbeme en lenguaje natural 👇`,
            { parse_mode: 'Markdown' }
        );
    });

    bot.on('message', async (msg) => {
        if (msg.text && msg.text.startsWith('/start')) return;
        if (chatId && String(msg.chat.id) !== String(chatId)) {
            // Antes se descartaba en silencio: si el chat configurado no era el
            // suyo, el bot se quedaba mudo y no habia forma de saber por que.
            console.warn(`Bot: mensaje ignorado, viene del chat ${msg.chat.id} y el configurado es ${chatId}`);
            return;
        }

        // Audios
        // Antes `if (!msg.text) return` botaba las notas de voz sin responder ni
        // dejar rastro. Cristian le habla por audio: era el motivo real de que
        // el bot "no atendiera".
        let texto = msg.text;
        let deAudio = false;
        const nota = msg.voice || msg.audio || msg.video_note;
        if (!texto && nota) {
            bot.sendChatAction(msg.chat.id, 'typing');
            let enlace;
            try {
                enlace = await bot.getFileLink(nota.file_id);
            } catch (err) {
                console.error('Bot: no pude pedir el audio a Telegram:', err.message);
                await bot.sendMessage(msg.chat.id, '⚠️ No pude bajar tu audio. Intenta de nuevo.');
                return;
            }
            const r = await transcribir(enlace, 'nota.ogg');
            if (r.error) { await bot.sendMessage(msg.chat.id, '⚠️ ' + r.error); return; }
            texto = r.texto;
            deAudio = true;
            console.log('Bot: audio transcrito ->', texto);
        }

        // Cualquier otra cosa (foto, sticker, documento) se contesta igual: el
        // silencio es lo unico que no se vale.
        if (!texto) {
            await bot.sendMessage(msg.chat.id, 'Por ahora entiendo texto y notas de voz. Mandame eso y lo resuelvo.');
            return;
        }

        // Atajo sin IA: "gaste 30000 gasolina" se resuelve con expresiones
        // regulares. Cuesta cero tokens y responde de una. Solo lo que no cuadra
        // con ese formato pasa al asistente.
        const plata = interpretar(texto);
        if (plata) {
            try {
                const hoy = new Date(Date.now() - 5 * 3600000).toISOString().split('T')[0];
                await MovimientoFinanciero.create({ ...plata, fecha: hoy, origen: 'manual', recurrente: false });
                const signo = plata.tipo === 'egreso' ? '−' : '+';
                const cop = '$' + Number(plata.monto).toLocaleString('es-CO');
                await bot.sendMessage(msg.chat.id,
                    `✅ Anotado
${signo} *${cop}* · ${plata.concepto}
_${plata.categoria} · ${plata.ambito}_`,
                    { parse_mode: 'Markdown' });
            } catch (err) {
                console.error('Bot: no se pudo anotar el movimiento:', err.message);
                await bot.sendMessage(msg.chat.id, '⚠️ No pude anotarlo. Revisa en la app.');
            }
            return;
        }

        bot.sendChatAction(msg.chat.id, 'typing');
        const typing = setInterval(() => bot.sendChatAction(msg.chat.id, 'typing'), 4000);

        try {
            const respuesta = await procesarMensaje(texto);
            clearInterval(typing);
            // Si vino por voz se devuelve lo que se entendio: si transcribio mal,
            // la respuesta rara deja de ser un misterio.
            const cabeza = deAudio ? `_te entendí:_ "${texto}"

` : '';
            await bot.sendMessage(msg.chat.id, cabeza + respuesta, { parse_mode: 'Markdown' });
        } catch (err) {
            clearInterval(typing);
            console.error('AI Company Bot error:', err.message);
            bot.sendMessage(msg.chat.id, '⚠️ Ocurrió un error procesando tu mensaje. Intenta de nuevo.');
        }
    });
}

// procesarMensaje se exporta para poder probarlo sin levantar Telegram.
module.exports = { initBot, procesarMensaje };
