const { enviarEmail } = require('./emailService');

const BASE_URL   = process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app';
const EMPRESA    = process.env.NOMBRE_EMPRESA || 'AI Company CO';
const SOPORTE_WA = 'https://wa.me/573212674754';

function fecha(f) {
    return new Date(f).toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' });
}

async function notificar(to, subject, body) {
    const gmailUser = process.env.GMAIL_USER;
    const gmailPass = process.env.GMAIL_APP_PASSWORD;
    if (!gmailUser || !gmailPass || !to) return;
    try {
        await enviarEmail({
            gmailUser, gmailPass,
            nombreAgente: 'Sistema de Licencias',
            nombreEmpresa: EMPRESA,
            to, subject, body
        });
    } catch (err) {
        console.error(`📧 Error enviando email a ${to}:`, err.message);
    }
}

async function notificarNuevaLicencia({ clienteEmail, clienteNombre, productoNombre, fechaVencimiento, licenseKey }) {
    const subject = `✅ Licencia activada — ${productoNombre}`;
    const body = [
        `Hola ${clienteNombre},`,
        ``,
        `Su licencia de ${productoNombre} ha sido activada exitosamente.`,
        ``,
        `📅 Fecha de vencimiento: ${fecha(fechaVencimiento)}`,
        `🔑 Clave de licencia: ${licenseKey}`,
        ``,
        `Si necesita ayuda contáctenos por WhatsApp:`,
        SOPORTE_WA,
        ``,
        `Gracias por confiar en ${EMPRESA}.`
    ].join('\n');
    await notificar(clienteEmail, subject, body);
    console.log(`📧 Bienvenida enviada a ${clienteEmail}`);
}

async function notificarRenovacion({ clienteEmail, clienteNombre, productoNombre, nuevaFechaVencimiento, monto }) {
    const subject = `🔄 Licencia renovada — ${productoNombre}`;
    const body = [
        `Hola ${clienteNombre},`,
        ``,
        `Su licencia de ${productoNombre} ha sido renovada exitosamente.`,
        ``,
        `📅 Nueva fecha de vencimiento: ${fecha(nuevaFechaVencimiento)}`,
        monto ? `💰 Monto cobrado: $${Number(monto).toLocaleString('es-CO')} COP` : '',
        ``,
        `Gracias por continuar con ${EMPRESA}.`,
        `Soporte: ${SOPORTE_WA}`
    ].filter(l => l !== undefined).join('\n');
    await notificar(clienteEmail, subject, body);
    console.log(`📧 Confirmación renovación enviada a ${clienteEmail}`);
}

async function notificarVencimientoCercano({ clienteEmail, clienteNombre, productoNombre, fechaVencimiento, diasRestantes, licenseKey }) {
    const dias = diasRestantes === 1 ? '1 día' : `${diasRestantes} días`;
    const subject = `⚠️ Su licencia vence en ${dias} — ${productoNombre}`;
    const pagoUrl = `${BASE_URL}/pagar/${licenseKey}`;
    const body = [
        `Hola ${clienteNombre},`,
        ``,
        `Le recordamos que su licencia de ${productoNombre} vence el ${fecha(fechaVencimiento)} (en ${dias}).`,
        ``,
        `Para renovar ahora:`,
        pagoUrl,
        ``,
        `O contáctenos por WhatsApp:`,
        SOPORTE_WA,
        ``,
        `${EMPRESA} · +57 321 267 4754`
    ].join('\n');
    await notificar(clienteEmail, subject, body);
    console.log(`📧 Aviso vencimiento (${diasRestantes}d) enviado a ${clienteEmail}`);
}

async function notificarLicenciaBloqueada({ clienteEmail, clienteNombre, productoNombre, licenseKey }) {
    const subject = `🔒 Su sistema está bloqueado — ${productoNombre}`;
    const pagoUrl = `${BASE_URL}/pagar/${licenseKey}`;
    const body = [
        `Hola ${clienteNombre},`,
        ``,
        `Su licencia de ${productoNombre} ha vencido y el acceso al sistema está bloqueado.`,
        ``,
        `Renueve ahora para restablecer el acceso:`,
        pagoUrl,
        ``,
        `O contáctenos por WhatsApp:`,
        SOPORTE_WA,
        ``,
        `${EMPRESA} · +57 321 267 4754`
    ].join('\n');
    await notificar(clienteEmail, subject, body);
    console.log(`📧 Aviso bloqueo enviado a ${clienteEmail}`);
}

module.exports = { notificarNuevaLicencia, notificarRenovacion, notificarVencimientoCercano, notificarLicenciaBloqueada };
