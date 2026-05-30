/**
 * Reporte diario del embudo de ventas por Telegram.
 * Muestra: prospectos contactados, respuestas, reuniones agendadas.
 */

const cron = require('node-cron');
const { Op } = require('sequelize');
const { MensajeSocial, Reunion, Lead } = require('../models');
const telegramService = require('./telegramService');

async function generarReporteVentas() {
    try {
        const hoy       = new Date();
        const inicioDia = new Date(hoy); inicioDia.setHours(0, 0, 0, 0);
        const fin7dias  = new Date(hoy.getTime() - 7 * 24 * 60 * 60 * 1000);
        const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);

        // ── Hoy ───────────────────────────────────────────────────────────────
        const contactadosHoy = await MensajeSocial.count({
            where: { respondido: true, createdAt: { [Op.gte]: inicioDia } }
        });
        const respuestasHoy = await MensajeSocial.count({
            where: {
                contenido: { [Op.not]: '[Seguimiento automático]' },
                createdAt: { [Op.gte]: inicioDia },
                respuesta: { [Op.not]: null }
            }
        });

        // ── Reuniones ──────────────────────────────────────────────────────────
        const reunionesHoy    = await Reunion.count({ where: { createdAt: { [Op.gte]: inicioDia } } });
        const reuniones7dias  = await Reunion.count({ where: { createdAt: { [Op.gte]: fin7dias } } });
        const reunionesMes    = await Reunion.count({ where: { createdAt: { [Op.gte]: inicioMes } } });
        const reunionesPend   = await Reunion.count({ where: { estado: 'pendiente', fecha: { [Op.gte]: hoy } } });

        // ── Próxima reunión ────────────────────────────────────────────────────
        const proximaReunion = await Reunion.findOne({
            where: { estado: 'pendiente', fecha: { [Op.gte]: hoy } },
            order: [['fecha', 'ASC']]
        });
        const proximaTxt = proximaReunion
            ? `${new Date(proximaReunion.fecha).toLocaleDateString('es-CO', {
                weekday: 'short', day: 'numeric', month: 'short',
                hour: '2-digit', minute: '2-digit', timeZone: 'America/Bogota'
              })} con ${proximaReunion.participantes || 'prospecto'}`
            : 'Ninguna agendada';

        // ── Conversaciones activas (respondieron en últimas 24h) ────────────────
        const hace24h = new Date(Date.now() - 24 * 60 * 60 * 1000);
        const conversacionesActivas = await MensajeSocial.count({
            where: {
                createdAt: { [Op.gte]: hace24h },
                contenido: { [Op.not]: '[Seguimiento automático]' }
            }
        });

        const fechaHoy = hoy.toLocaleDateString('es-CO', {
            weekday: 'long', day: 'numeric', month: 'long', timeZone: 'America/Bogota'
        });

        // ── Barra de progreso de reuniones semanales (meta: 5) ──────────────────
        const META_SEMANA = 5;
        const pct    = Math.min(100, Math.round((reuniones7dias / META_SEMANA) * 100));
        const filled = Math.round(pct / 10);
        const bar    = '█'.repeat(filled) + '░'.repeat(10 - filled);

        const msg =
`📊 *Reporte de Ventas — AI Company CO*
📅 ${fechaHoy}

*Hoy:*
📤 Mensajes enviados: *${contactadosHoy}*
💬 Conversaciones activas (24h): *${conversacionesActivas}*
📅 Reuniones agendadas hoy: *${reunionesHoy}*

*Esta semana:*
${bar} ${pct}% de meta (${reuniones7dias}/${META_SEMANA} reuniones)

*Este mes:*
📅 Total reuniones: *${reunionesMes}*
⏳ Pendientes por realizar: *${reunionesPend}*

*Próxima reunión:*
🗓 ${proximaTxt}`;

        await telegramService.enviarConBotones(msg, [[
            { text: '📋 Ver reuniones', url: `${process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app'}/reuniones` },
            { text: '💬 Ver mensajes', url: `${process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app'}/social` },
        ]]);

        console.log('[VentasReport] Reporte enviado');
        return { ok: true, reunionesHoy, reuniones7dias, reunionesMes };
    } catch (e) {
        console.error('[VentasReport] Error:', e.message);
        return { ok: false, error: e.message };
    }
}

function iniciarVentasReportScheduler() {
    // Reporte todos los días a las 9pm Colombia (2am UTC del día siguiente)
    cron.schedule('0 2 * * *', () => {
        console.log('[VentasReport] Enviando reporte diario...');
        generarReporteVentas();
    }, { timezone: 'America/Bogota' });

    console.log('📊 Ventas Report Scheduler activo (9pm Colombia cada día)');
}

module.exports = { iniciarVentasReportScheduler, generarReporteVentas };
