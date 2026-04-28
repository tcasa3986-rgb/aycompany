const TelegramBot = require('node-telegram-bot-api');

let bot = null;

function getBot() {
    if (!bot && process.env.PLATAFORMA_TELEGRAM_TOKEN) {
        bot = new TelegramBot(process.env.PLATAFORMA_TELEGRAM_TOKEN);
    }
    return bot;
}

exports.enviar = async (mensaje) => {
    const b = getBot();
    if (!b || !process.env.PLATAFORMA_TELEGRAM_CHAT_ID) return;
    return b.sendMessage(process.env.PLATAFORMA_TELEGRAM_CHAT_ID, mensaje, { parse_mode: 'Markdown' });
};
