const cron      = require('node-cron');
const nodemailer = require('nodemailer');
const { Op }    = require('sequelize');
const sequelize = require('../config/db');
const { Licencia, Cliente, Producto, Pago } = require('../models');
const { notificarVencimientoCercano, notificarLicenciaBloqueada } = require('./licenciaNotificaciones');

const includeCompleto = [
    { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'email', 'telefono'] },
    { model: Producto, as: 'producto', attributes: ['nombre'] }
];

// ── Verificar vencimientos diario 9am ────────────────────────
async function verificarVencimientos() {
    const hoy    = new Date();
    const hoyStr = hoy.toISOString().split('T')[0];
    const en6    = new Date(hoy); en6.setDate(en6.getDate() + 6);
    const en8    = new Date(hoy); en8.setDate(en8.getDate() + 8);

    const porVencer = await Licencia.findAll({
        where: { activo: true, fecha_vencimiento: { [Op.between]: [en6.toISOString().split('T')[0], en8.toISOString().split('T')[0]] } },
        include: includeCompleto
    });
    for (const lic of porVencer) {
        if (!lic.cliente?.email) continue;
        const dias = Math.max(1, Math.ceil((new Date(lic.fecha_vencimiento) - hoy) / 86400000));
        await notificarVencimientoCercano({ clienteEmail: lic.cliente.email, clienteNombre: lic.cliente.nombre, productoNombre: lic.producto?.nombre || 'Sistema', fechaVencimiento: lic.fecha_vencimiento, diasRestantes: dias, licenseKey: lic.license_key, clienteTelefono: lic.cliente.telefono });
    }

    const vencidasHoy = await Licencia.findAll({
        where: { activo: true, fecha_vencimiento: hoyStr },
        include: includeCompleto
    });
    for (const lic of vencidasHoy) {
        if (!lic.cliente?.email) continue;
        await notificarLicenciaBloqueada({ clienteEmail: lic.cliente.email, clienteNombre: lic.cliente.nombre, productoNombre: lic.producto?.nombre || 'Sistema', licenseKey: lic.license_key, clienteTelefono: lic.cliente.telefono });
    }
}

// ── Onboarding: día 3 y día 7 ────────────────────────────────
async function verificarOnboarding() {
    const gmailUser = process.env.GMAIL_USER;
    const gmailPass = process.env.GMAIL_APP_PASSWORD;
    if (!gmailUser || !gmailPass) return;

    const empresa  = process.env.NOMBRE_EMPRESA || 'AI Company CO';
    const BASE_URL = process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app';
    const t = nodemailer.createTransport({ service: 'gmail', auth: { user: gmailUser, pass: gmailPass } });

    const hace3 = new Date(); hace3.setDate(hace3.getDate() - 3);
    const hace4 = new Date(); hace4.setDate(hace4.getDate() - 4);
    const hace7 = new Date(); hace7.setDate(hace7.getDate() - 7);
    const hace8 = new Date(); hace8.setDate(hace8.getDate() - 8);

    // Día 3 — consejos de uso (onboarding_paso = 1 y creado hace 3 días)
    const clientes3 = await Cliente.findAll({
        where: {
            onboarding_paso: 1, email: { [Op.ne]: null },
            created_at: { [Op.between]: [hace4, hace3] }
        }
    });
    for (const c of clientes3) {
        try {
            await t.sendMail({
                from: `"${empresa}" <${gmailUser}>`, to: c.email,
                subject: `💡 Consejos para aprovechar su sistema — ${empresa}`,
                text: [`Hola ${c.nombre},`, ``, `Han pasado 3 días desde que activó su sistema. Aquí algunos consejos:`, ``, `✅ Acceda a su portal personal para ver el estado de su licencia en cualquier momento`, `✅ Active la suscripción automática para no preocuparse por renovaciones`, `✅ Descargue sus facturas en PDF desde el portal`, ``, `Su portal: ${BASE_URL}/cliente/${c.token_portal || ''}`, ``, `¿Tiene preguntas? Contáctenos: +57 321 267 4754`, ``, empresa].join('\n')
            });
            await c.update({ onboarding_paso: 2 });
            console.log(`📧 Onboarding día 3 enviado a ${c.email}`);
        } catch (e) { console.error('Error onboarding día 3:', e.message); }
    }

    // Día 7 — guía completa del portal (onboarding_paso = 2 y creado hace 7 días)
    const clientes7 = await Cliente.findAll({
        where: {
            onboarding_paso: 2, email: { [Op.ne]: null },
            created_at: { [Op.between]: [hace8, hace7] }
        }
    });
    for (const c of clientes7) {
        try {
            await t.sendMail({
                from: `"${empresa}" <${gmailUser}>`, to: c.email,
                subject: `🎯 Su guía completa del portal — ${empresa}`,
                text: [`Hola ${c.nombre},`, ``, `Esta es su guía final para sacar el máximo provecho:`, ``, `📊 PORTAL DEL CLIENTE`, `→ Ver licencias activas y días restantes`, `→ Pagar 1, 2, 3, 6 o 12 meses de una vez`, `→ Descargar facturas en PDF`, `→ Actualizar sus datos de contacto`, `→ Abrir tickets de soporte`, ``, `🔗 Su enlace permanente:`, `${BASE_URL}/cliente/${c.token_portal || '(solicite el link a soporte)'}`, ``, `¿Necesita ayuda? Siempre estamos disponibles:`, `WhatsApp: https://wa.me/573212674754`, ``, `¡Gracias por confiar en ${empresa}!`].join('\n')
            });
            await c.update({ onboarding_paso: 3 });
            console.log(`📧 Onboarding día 7 enviado a ${c.email}`);
        } catch (e) { console.error('Error onboarding día 7:', e.message); }
    }
}

// ── Informe mensual al admin (1ro de cada mes 8am) ───────────
async function enviarInformeMensual() {
    const gmailUser  = process.env.GMAIL_USER;
    const gmailPass  = process.env.GMAIL_APP_PASSWORD;
    const adminEmail = process.env.ADMIN_EMAIL;
    if (!gmailUser || !gmailPass || !adminEmail) return;

    const hoy      = new Date();
    const mesAnterior = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
    const iniMes   = mesAnterior.toISOString().split('T')[0];
    const finMes   = new Date(hoy.getFullYear(), hoy.getMonth(), 0).toISOString().split('T')[0];
    const nomMes   = mesAnterior.toLocaleDateString('es-CO', { month: 'long', year: 'numeric' });

    const [totalClientes, licActivas, licVencidas, ingresosRes, pagosCount, porVencer30] = await Promise.all([
        Cliente.count({ where: { activo: true } }),
        Licencia.count({ where: { activo: true, fecha_vencimiento: { [Op.gte]: hoy } } }),
        Licencia.count({ where: { activo: false } }),
        Pago.findOne({ attributes: [[sequelize.fn('SUM', sequelize.col('monto')), 'total']], where: { fecha_pago: { [Op.between]: [iniMes, finMes] } }, raw: true }),
        Pago.count({ where: { fecha_pago: { [Op.between]: [iniMes, finMes] } } }),
        Licencia.count({ where: { activo: true, fecha_vencimiento: { [Op.between]: [hoy.toISOString().split('T')[0], new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0).toISOString().split('T')[0]] } } })
    ]);

    const mrr = await Licencia.findAll({ where: { activo: true, fecha_vencimiento: { [Op.gte]: hoy } }, include: [{ model: Producto, as: 'producto', attributes: ['precio_mensual'] }] })
        .then(lics => lics.reduce((s, l) => s + Number(l.producto?.precio_mensual || 0), 0));

    const empresa = process.env.NOMBRE_EMPRESA || 'AI Company CO';
    const t = nodemailer.createTransport({ service: 'gmail', auth: { user: gmailUser, pass: gmailPass } });

    await t.sendMail({
        from: `"${empresa}" <${gmailUser}>`, to: adminEmail,
        subject: `📊 Informe mensual ${nomMes} — ${empresa}`,
        text: [
            `INFORME MENSUAL — ${nomMes.toUpperCase()}`,
            `${'─'.repeat(40)}`,
            ``,
            `💰 INGRESOS`,
            `  Recaudado en ${nomMes}: $${Number(ingresosRes?.total || 0).toLocaleString('es-CO')} COP`,
            `  Pagos recibidos: ${pagosCount}`,
            `  MRR actual: $${mrr.toLocaleString('es-CO')} COP`,
            `  ARR proyectado: $${(mrr * 12).toLocaleString('es-CO')} COP`,
            ``,
            `📋 LICENCIAS`,
            `  Clientes activos: ${totalClientes}`,
            `  Licencias vigentes: ${licActivas}`,
            `  Licencias vencidas: ${licVencidas}`,
            `  Por vencer este mes: ${porVencer30}`,
            ``,
            `⚠️  ACCIÓN REQUERIDA`,
            `  ${porVencer30} licencia(s) vencen este mes — contacte a los clientes.`,
            ``,
            `${'─'.repeat(40)}`,
            `${empresa} · Generado automáticamente`
        ].join('\n')
    });
    console.log(`📧 Informe mensual enviado a ${adminEmail}`);
}

function iniciarLicenciaExpirationScheduler() {
    cron.schedule('0 9 * * *',   verificarVencimientos,  { timezone: 'America/Bogota' });
    cron.schedule('0 9 * * *',   verificarOnboarding,    { timezone: 'America/Bogota' });
    cron.schedule('0 8 1 * *',   enviarInformeMensual,   { timezone: 'America/Bogota' });
    console.log('📅 Schedulers iniciados: vencimientos + onboarding (9am) + informe mensual (1ro 8am)');
}

module.exports = { iniciarLicenciaExpirationScheduler };
