const { Reunion } = require('../models');
const { Op }      = require('sequelize');
const telegramService = require('./telegramService');

async function checkReminders() {
    if (!process.env.PLATAFORMA_TELEGRAM_TOKEN || !process.env.PLATAFORMA_TELEGRAM_CHAT_ID) return;

    const ahora  = new Date();
    const en29   = new Date(ahora.getTime() + 29 * 60000);
    const en31   = new Date(ahora.getTime() + 31 * 60000);

    const reuniones = await Reunion.findAll({
        where: {
            fecha:                { [Op.between]: [en29, en31] },
            estado:               'pendiente',
            recordatorio_enviado: false
        }
    });

    for (const r of reuniones) {
        const f = new Date(r.fecha);
        const horaTexto = f.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', timeZone: 'America/Bogota' });
        const fechaTexto = f.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', timeZone: 'America/Bogota' });
        const msg = `⏰ *Reunión en 30 minutos*\n👤 ${r.participantes || 'Cliente'}\n📅 ${fechaTexto} a las ${horaTexto}\n📝 ${(r.descripcion || '').slice(0, 120)}`;
        await telegramService.enviar(msg).catch(e => console.error('Reminder Telegram error:', e.message));
        await r.update({ recordatorio_enviado: true });
        console.log(`⏰ Recordatorio enviado: ${r.participantes} — ${horaTexto}`);
    }
}

function startReminder() {
    console.log('⏰ Recordatorio de reuniones activo (cada 1 min)');
    setInterval(() => checkReminders().catch(e => console.error('Reminder error:', e.message)), 60000);
}

module.exports = { startReminder };
