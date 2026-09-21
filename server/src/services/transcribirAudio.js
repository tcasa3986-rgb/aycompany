const OpenAI = require('openai');

// Pasa una nota de voz de Telegram a texto.
//
// Por que hace falta: el bot solo miraba `msg.text`, asi que un audio se
// descartaba en silencio — ni respondia ni dejaba rastro. Y Cristian le habla
// por audio, que es justamente como uno anota un gasto mientras maneja.
//
// Costo: Whisper cobra por minuto de audio, no por token. Una nota de 10
// segundos vale del orden de un peso. No es el "bot pendiente gastando tokens"
// que no queriamos: solo se llama cuando de verdad llega un audio.

const MAX_BYTES = 20 * 1024 * 1024;   // Telegram no manda notas mas grandes

// Whisper acierta mucho mas si sabe de que se le va a hablar. Sin esto, una
// frase corta como "como vamos este mes" salio transcrita como "Comambidensis".
// Aqui van los nombres propios y las palabras del negocio.
const VOCABULARIO = [
    'Gastos e ingresos de una empresa de software en Colombia.',
    'Clientes: ASOERC, Maderas Montoya, Ferre Laser, JD Metales, Laser Ejecutivo, Dinasty Poker, Cancha360.',
    'Palabras: gaste, pague, compre, me pagaron, entraron, recibi, cobre, mensualidad,',
    'cuota, contrato, licencia, arriendo, servicios, gasolina, moto, gimnasio, celular,',
    'comida, Railway, Codex, Claude, hosting, dominio, pauta, marketing, pesos, mil, millones.',
].join(' ');

/**
 * @param {string} url  enlace directo al archivo (bot.getFileLink)
 * @returns {Promise<{texto: string} | {error: string}>}
 */
async function transcribir(url, nombre = 'nota.ogg') {
    if (!process.env.OPENAI_API_KEY) {
        return { error: 'No tengo configurado el transcriptor (falta OPENAI_API_KEY). Escríbemelo y lo hago.' };
    }
    let buf;
    try {
        const r = await fetch(url);
        if (!r.ok) return { error: `No pude bajar el audio de Telegram (${r.status}).` };
        buf = Buffer.from(await r.arrayBuffer());
    } catch (e) {
        return { error: `No pude bajar el audio: ${e.message}` };
    }
    return transcribirBuffer(buf, nombre);
}

/** El trabajo de verdad, aparte para poder probarlo sin depender de Telegram. */
async function transcribirBuffer(buf, nombre = 'nota.ogg') {
    if (!process.env.OPENAI_API_KEY) {
        return { error: 'No tengo configurado el transcriptor (falta OPENAI_API_KEY).' };
    }
    try {
        if (buf.length > MAX_BYTES) return { error: 'Ese audio está muy largo. Mándame uno más corto.' };

        const openai = new OpenAI({ apiKey: process.env.OPENAI_API_KEY });
        const archivo = await OpenAI.toFile(buf, nombre);
        const resp = await openai.audio.transcriptions.create({
            file: archivo,
            model: 'whisper-1',
            language: 'es',   // sin esto a veces lo toma por portugues
            prompt: VOCABULARIO,
            temperature: 0,   // que no invente cuando dude
        });

        const texto = (resp.text || '').trim();
        if (!texto) return { error: 'No le entendí nada al audio. ¿Me lo repites?' };
        return { texto };
    } catch (e) {
        return { error: `No pude transcribir el audio: ${e.message}` };
    }
}

module.exports = { transcribir, transcribirBuffer };
