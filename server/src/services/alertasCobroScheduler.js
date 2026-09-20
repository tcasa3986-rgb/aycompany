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

    // Pagos con fecha: el Railway de cada sistema y los dominios del negocio,
    // pero también el arriendo o los servicios. Se separan por ámbito porque no
    // se deciden igual: uno tumba un sistema de un cliente, el otro es tu casa.
    const C = {
        empresa:  { hoy: [], en3: [], atrasados: [] },
        personal: { hoy: [], en3: [], atrasados: [] },
    };
    let porPagar = 0;
    for (const co of costos) {
        const dias  = Math.round((aFecha(co.proximo_pago) - aFecha(hoy)) / 86400000);
        const grupo = C[co.ambito === 'personal' ? 'personal' : 'empresa'];
        const plata = `${co.moneda === 'USD' ? 'US$' : '$'}${Number(co.monto).toLocaleString('es-CO')}`;
        const linea = `• ${co.nombre} — ${plata}${co.cuenta ? ` · ${co.cuenta}` : ''}`;
        if (dias === 3)      grupo.en3.push(`${linea} el ${co.proximo_pago}`);
        else if (dias === 0) grupo.hoy.push(linea);
        else if (dias < 0)   grupo.atrasados.push(`${linea} — venció el ${co.proximo_pago}`);
        // Solo se suman los pesos: mezclar dólares daría una cifra falsa.
        if (dias <= 3 && co.moneda === 'COP') porPagar += Number(co.monto);
    }

    const partes = [];
    if (L.bloqueaHoy.length) partes.push(`🔒 *Se bloquean HOY (no pagaron):*\n${L.bloqueaHoy.join('\n')}`);
    if (L.hoy.length)        partes.push(`📅 *Licencias que vencen HOY:*\n${L.hoy.join('\n')}`);
    if (L.en3.length)        partes.push(`⏳ *Vencen en 3 días:*\n${L.en3.join('\n')}`);
    if (L.mora.length)       partes.push(`⚠️ *Clientes en mora:*\n${L.mora.join('\n')}`);
    if (C.empresa.atrasados.length)  partes.push(`\ud83d\udea8 *Del negocio, ATRASADO (riesgo de que se caiga un sistema):*\n${C.empresa.atrasados.join('\n')}`);
    if (C.empresa.hoy.length)        partes.push(`\ud83d\udcb3 *Del negocio, pagar HOY:*\n${C.empresa.hoy.join('\n')}`);
    if (C.empresa.en3.length)        partes.push(`\ud83d\udcb3 *Del negocio, en 3 d\u00edas:*\n${C.empresa.en3.join('\n')}`);
    if (C.personal.atrasados.length) partes.push(`\ud83d\udea8 *Tuyo, ATRASADO:*\n${C.personal.atrasados.join('\n')}`);
    if (C.personal.hoy.length)       partes.push(`\ud83c\udfe0 *Tuyo, pagar HOY:*\n${C.personal.hoy.join('\n')}`);
    if (C.personal.en3.length)       partes.push(`\ud83c\udfe0 *Tuyo, en 3 d\u00edas:*\n${C.personal.en3.join('\n')}`);

    if (!partes.length) return null;
    const pie = porPagar > 0 ? `\n\n_En los próximos 3 días te salen ${fmt(porPagar)}._` : '';
    return `🗓 *Qué te toca hoy — ${hoy}*\n\n${partes.join('\n\n')}${pie}`;
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
