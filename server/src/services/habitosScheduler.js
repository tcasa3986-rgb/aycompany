// Avisos de hábitos por Telegram. Cero IA, cero tokens: es un reloj y una
// consulta a la base. Igual que las alertas de cobro.
//
// Tres momentos, que es como funciona una app de hábitos de verdad:
//   · A la hora que puso cada hábito  → "es la hora de X".
//   · 8:00 pm  → lo que falta, mientras todavía da tiempo de hacerlo.
//   · 9:30 pm  → cómo quedó el día y cómo van las rachas.
//
// El minuto a minuto no manda nada si no hay nada que mandar: el silencio es
// la mayoría de los minutos del día.
const cron = require('node-cron');
const { Habito, HabitoRegistro } = require('../models');
const telegram = require('./telegramService');
const { hoyBogota } = require('../utils/licenciaCiclo');
const { aplicaEn, cumplido, rachaActual, menosDias } = require('../utils/rachas');
const { Op } = require('sequelize');

const TZ = 'America/Bogota';

/** "06:05" en hora de Colombia. */
function horaBogota(d = new Date()) {
    const b = new Date(d.getTime() - 5 * 3600000);
    return String(b.getUTCHours()).padStart(2, '0') + ':' + String(b.getUTCMinutes()).padStart(2, '0');
}

/** Hábitos activos de hoy, con su registro del día. */
async function deHoy(hoy = hoyBogota()) {
    const habitos = await Habito.findAll({ where: { activo: true }, order: [['orden', 'ASC'], ['id', 'ASC']] });
    const regs = await HabitoRegistro.findAll({ where: { fecha: hoy } });
    const porHabito = new Map(regs.map(r => [r.habito_id, r]));
    return habitos
        .filter(h => aplicaEn(h.dias, hoy))
        .map(h => {
            const datos = { ...h.toJSON(), desde: String(h.created_at || h.createdAt || '').slice(0, 10) };
            return { h, datos, reg: porHabito.get(h.id), hecho: cumplido(datos, porHabito.get(h.id)) };
        });
}

// ── 1. El recordatorio de cada hábito, a su hora ──────────────────────────────
// Se guarda el último minuto avisado para que un reinicio del servidor a las
// 06:00 no mande el mismo aviso dos veces.
let ultimoMinuto = null;

async function revisarHoras() {
    const minuto = horaBogota();
    if (minuto === ultimoMinuto) return;
    ultimoMinuto = minuto;

    const hoy = hoyBogota();
    const lista = await deHoy(hoy);
    const toca = lista.filter(x => x.h.recordar && x.h.hora === minuto && !x.hecho);
    if (toca.length === 0) return;

    for (const { h, datos } of toca) {
        const porFecha = new Map();
        const regs = await HabitoRegistro.findAll({ where: { habito_id: h.id, fecha: { [Op.gte]: menosDias(hoy, 90) } } });
        for (const r of regs) porFecha.set(String(r.fecha).slice(0, 10), r);
        const racha = rachaActual(datos, porFecha, hoy);

        const cola = racha >= 2
            ? `\nLlevas *${racha} días seguidos*. No la rompas hoy.`
            : '';
        await telegram.enviar(
            `${h.icono || '⏰'} *${h.nombre}*\n_${minuto}_${h.detalle ? '\n' + h.detalle : ''}${cola}`
        ).catch(() => {});
    }
}

// ── 2. A las 8 pm: lo que falta, cuando todavía se puede ──────────────────────
async function avisoDeLaTarde() {
    const hoy = hoyBogota();
    const lista = await deHoy(hoy);
    const faltan = lista.filter(x => !x.hecho && x.h.tipo === 'hacer');
    if (faltan.length === 0) return;

    const puntos = faltan.map(x => `${x.h.icono || '•'} ${x.h.nombre}`).join('\n');
    await telegram.enviar(
        `🌙 *Te falta esto del día*\n\n${puntos}\n\n_Todavía hay tiempo._`
    ).catch(() => {});
}

// ── 3. A las 9:30 pm: cómo quedó el día ───────────────────────────────────────
async function cierreDelDia() {
    const hoy = hoyBogota();
    const lista = await deHoy(hoy);
    if (lista.length === 0) return;

    const hechos = lista.filter(x => x.hecho);
    const pct = Math.round((hechos.length / lista.length) * 100);

    // Las rachas se calculan una sola vez, con todos los registros de una.
    const regs = await HabitoRegistro.findAll({ where: { fecha: { [Op.gte]: menosDias(hoy, 90) } } });
    const porHabito = new Map();
    for (const r of regs) {
        if (!porHabito.has(r.habito_id)) porHabito.set(r.habito_id, new Map());
        porHabito.get(r.habito_id).set(String(r.fecha).slice(0, 10), r);
    }

    const lineas = lista.map(x => {
        const racha = rachaActual(x.datos, porHabito.get(x.h.id) || new Map(), hoy);
        const marca = x.hecho ? '✅' : '⬜';
        const fuego = racha >= 3 ? ` 🔥${racha}` : '';
        return `${marca} ${x.h.nombre}${fuego}`;
    }).join('\n');

    const remate = pct === 100 ? '*Día completo.* 💪'
        : pct >= 60            ? 'Buen día. Mañana se cierra el resto.'
        : hechos.length === 0  ? 'Mañana se vuelve a empezar. Una sola cosa ya cuenta.'
        :                        'Algo es algo. Mañana una más.';

    await telegram.enviar(
        `📊 *Cómo quedó el día*\n${hechos.length} de ${lista.length} · ${pct}%\n\n${lineas}\n\n${remate}`
    ).catch(() => {});
}

function iniciarHabitos() {
    if (!process.env.PLATAFORMA_TELEGRAM_CHAT_ID) {
        console.log('ℹ️  Hábitos: sin chat de Telegram configurado, no se mandan avisos.');
        return;
    }
    // Cada minuto, pero solo habla cuando algún hábito tiene esa hora puesta.
    cron.schedule('* * * * *', () => revisarHoras().catch(e => console.error('Hábitos (hora):', e.message)), { timezone: TZ });
    cron.schedule('0 20 * * *', () => avisoDeLaTarde().catch(e => console.error('Hábitos (tarde):', e.message)), { timezone: TZ });
    cron.schedule('30 21 * * *', () => cierreDelDia().catch(e => console.error('Hábitos (cierre):', e.message)), { timezone: TZ });
    console.log('🔁 Hábitos: recordatorios activos (a su hora, 8:00 pm y 9:30 pm Bogotá)');
}

module.exports = { iniciarHabitos, revisarHoras, avisoDeLaTarde, cierreDelDia, deHoy, horaBogota };
