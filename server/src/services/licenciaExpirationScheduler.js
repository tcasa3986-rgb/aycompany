const cron = require('node-cron');
const { Op } = require('sequelize');
const { Licencia, Cliente, Producto } = require('../models');
const { notificarVencimientoCercano, notificarLicenciaBloqueada } = require('./licenciaNotificaciones');

const includeCompleto = [
    { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'email'] },
    { model: Producto, as: 'producto', attributes: ['nombre'] }
];

async function verificarVencimientos() {
    const hoy   = new Date();
    const hoyStr = hoy.toISOString().split('T')[0];

    // Licencias que vencen dentro de 7 días (±1 día de margen para no duplicar)
    const en7 = new Date(hoy);
    en7.setDate(en7.getDate() + 7);
    const en6 = new Date(hoy);
    en6.setDate(en6.getDate() + 6);
    const en8 = new Date(hoy);
    en8.setDate(en8.getDate() + 8);

    const porVencer = await Licencia.findAll({
        where: {
            activo: true,
            fecha_vencimiento: {
                [Op.between]: [en6.toISOString().split('T')[0], en8.toISOString().split('T')[0]]
            }
        },
        include: includeCompleto
    });

    for (const lic of porVencer) {
        if (!lic.cliente?.email) continue;
        const dias = Math.max(1, Math.ceil((new Date(lic.fecha_vencimiento) - hoy) / 86400000));
        await notificarVencimientoCercano({
            clienteEmail:      lic.cliente.email,
            clienteNombre:     lic.cliente.nombre,
            productoNombre:    lic.producto?.nombre || 'Sistema',
            fechaVencimiento:  lic.fecha_vencimiento,
            diasRestantes:     dias,
            licenseKey:        lic.license_key
        });
    }

    // Licencias que vencen hoy → bloqueo
    const vencidasHoy = await Licencia.findAll({
        where: { activo: true, fecha_vencimiento: hoyStr },
        include: includeCompleto
    });

    for (const lic of vencidasHoy) {
        if (!lic.cliente?.email) continue;
        await notificarLicenciaBloqueada({
            clienteEmail:   lic.cliente.email,
            clienteNombre:  lic.cliente.nombre,
            productoNombre: lic.producto?.nombre || 'Sistema',
            licenseKey:     lic.license_key
        });
    }
}

function iniciarLicenciaExpirationScheduler() {
    // Todos los días a las 9am hora Bogotá
    cron.schedule('0 9 * * *', verificarVencimientos, { timezone: 'America/Bogota' });
    console.log('📅 Scheduler vencimientos de licencias iniciado (diario 9am Bogotá)');
}

module.exports = { iniciarLicenciaExpirationScheduler };
