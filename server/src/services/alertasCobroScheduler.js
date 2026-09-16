// Avisos diarios a Cristian por Telegram (chat PLATAFORMA_TELEGRAM_CHAT_ID):
//   - licencias: vence en 3 días / vence hoy / en mora (no pagaron) / se bloquea hoy
//   - costos de servidor (Railway, dominios...): pagar en 3 días / pagar hoy / atrasado
// Corre 8:00 am Bogotá. También se puede disparar a mano desde /api/costos/alertas/enviar.
const cron = require('node-cron');
const { Licencia, Cliente, Producto, CostoServidor } = require('../models');
const telegram = require('./telegramService');
const { estadoLicencia, precioLicencia, hoyBogota, aFecha } = require('../utils/licenciaCiclo');

const fmt = n => '$' + Number(n || 0).toLocaleString('es-CO');

async function armarResumen(hoy = hoyBogota()) {
    const licencias = await Licencia.findAll({
        where: { activo: true },
        include: [
            { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'telefono'] },
            { model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }
        ]
    });
    const costos = await CostoServidor.findAll({ where: { activo: true } });

    const L = { hoy: [], en3: [], mora: [], bloqueaHoy: [], bloqueadas: [] };
    for (const lic of licencias) {
        const c = estadoLicencia(lic, hoy);
        const nombre = `${lic.cliente?.nombre || '?'} · ${lic.producto?.nombre || 'Sistema'}`;
        const precio = fmt(precioLicencia(lic));
        if (c.dias_restantes === 3) L.en3.push(`• ${nombre} — ${precio} vence el ${c.fecha_vencimiento}`);
        if (c.dias_restantes === 0) L.hoy.push(`• ${nombre} — ${precio}`);
        // Con tolerancia 0 el bloqueo cae el mismo dia del corte, cuando la mora
        // todavia es 0: por eso se mira fecha_bloqueo, no dias_mora.
        if (c.bloquear && c.fecha_bloqueo === hoy) L.bloqueaHoy.push(`• ${nombre} — ${precio}`);
        if (c.dias_mora > 0) {
            const tag = c.bloquear ? (c.valida ? ` (se bloquea el ${c.fecha_bloqueo})` : ' — 🔒 BLOQUEADA') : ' (no se bloquea)';
            L.mora.push(`• ${nombre} — ${c.dias_mora} día${c.dias_mora > 1 ? 's' : ''} de mora${tag}`);
        }
    }

    const C = { hoy: [], en3: [], atrasados: [] };
    for (const co of costos) {
        const dias = Math.round((aFecha(co.proximo_pago) - aFecha(hoy)) / 86400000);
        const linea = `• ${co.nombre} — ${co.moneda === 'USD' ? 'US$' : '$'}${Number(co.monto).toLocaleString('es-CO')}${co.cuenta ? ' (' + co.cuenta + ')' : ''}`;
        if (dias === 3) C.en3.push(`${linea} el ${co.proximo_pago}`);
        else if (dias === 0) C.hoy.push(linea);
        else if (dias < 0) C.atrasados.push(`${linea} — venció el ${co.proximo_pago}`);
    }

    const partes = [];
    if (L.bloqueaHoy.length) partes.push(`🔒 *Se bloquean HOY (no pagaron):*\n${L.bloqueaHoy.join('\n')}`);
    if (L.hoy.length)        partes.push(`📅 *Licencias que vencen HOY:*\n${L.hoy.join('\n')}`);
    if (L.en3.length)        partes.push(`⏳ *Vencen en 3 días:*\n${L.en3.join('\n')}`);
    if (L.mora.length)       partes.push(`⚠️ *Clientes en mora:*\n${L.mora.join('\n')}`);
    if (C.hoy.length)        partes.push(`💳 *Servidores: pagar HOY:*\n${C.hoy.join('\n')}`);
    if (C.en3.length)        partes.push(`💳 *Servidores: pagar en 3 días:*\n${C.en3.join('\n')}`);
    if (C.atrasados.length)  partes.push(`🚨 *Servidores ATRASADOS (riesgo de caída):*\n${C.atrasados.join('\n')}`);

    if (!partes.length) return null;
    return `🗓 *Cobros y pagos — ${hoy}*\n\n${partes.join('\n\n')}`;
}

async function enviarResumen(forzar = false) {
    try {
        const msg = await armarResumen();
        if (!msg) {
            if (forzar) await telegram.enviar(`✅ *${hoyBogota()}* — Sin vencimientos, moras ni pagos de servidor pendientes.`);
            return { enviado: forzar, msg: msg || 'sin novedades' };
        }
        await telegram.enviar(msg);
        return { enviado: true, msg };
    } catch (e) {
        console.error('alertasCobro:', e.message);
        return { enviado: false, error: e.message };
    }
}

function iniciarAlertasCobro() {
    cron.schedule('0 8 * * *', () => enviarResumen(false), { timezone: 'America/Bogota' });
    console.log('🔔 Alertas de cobro/pagos activas (8:00 am Bogotá)');
}

module.exports = { iniciarAlertasCobro, enviarResumen, armarResumen };
