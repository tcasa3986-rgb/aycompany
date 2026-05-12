const nodemailer = require('nodemailer');

let transporter = null;

function getTransporter(gmailUser, gmailPass) {
    if (
        transporter &&
        transporter._user === gmailUser &&
        transporter._pass === gmailPass
    ) return transporter;

    transporter = nodemailer.createTransport({
        service: 'gmail',
        auth: { user: gmailUser, pass: gmailPass },
    });
    transporter._user = gmailUser;
    transporter._pass = gmailPass;
    return transporter;
}

async function enviarEmail({ gmailUser, gmailPass, nombreAgente, nombreEmpresa, to, subject, body }) {
    if (!gmailUser || !gmailPass) throw new Error('Gmail no configurado (GMAIL_USER / GMAIL_APP_PASSWORD)');

    const t = getTransporter(gmailUser, gmailPass);

    await t.sendMail({
        from: `"${nombreAgente} — ${nombreEmpresa}" <${gmailUser}>`,
        to,
        subject,
        text: body,
        html: body.replace(/\n/g, '<br>'),
    });
}

module.exports = { enviarEmail };
