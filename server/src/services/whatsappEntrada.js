// Qué pasa cuando alguien escribe al WhatsApp de productos digitales.
//
// Hoy hace tres cosas, y ninguna gasta tokens:
//   1. Deja a la persona registrada en el embudo. Esto es lo importante: antes,
//      quien escribía y no era atendido se perdía sin rastro.
//   2. Le avisa a Cristian por Telegram, con lo que dijo.
//   3. Contesta con reglas — saludo, catálogo, precio, cómo se paga.
//
// El asesor con IA (objeciones, cierres, seguimientos) es el paso siguiente.
// Esto es el piso: que nadie se pierda y que nadie quede sin respuesta.
const { VentaDigital, ProductoDigital } = require('../models');
const { enviarMensaje } = require('./whatsappService');
const telegram = require('./telegramService');
const { transcribir } = require('./transcribirAudio');

const cop = n => '$' + Number(n || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 });

// Para no contestar dos veces el mismo mensaje si Meta lo reintenta.
const vistos = new Set();
const recordar = id => {
    vistos.add(id);
    if (vistos.size > 500) vistos.delete(vistos.values().next().value);
};

/** Baja el audio de Meta y lo pasa a texto. */
async function textoDeAudio(mediaId) {
    const token = process.env.WHATSAPP_TOKEN;
    if (!token || !mediaId) return null;
    try {
        const meta = await fetch(`https://graph.facebook.com/v19.0/${mediaId}`, {
            headers: { Authorization: `Bearer ${token}` },
        }).then(r => r.json());
        if (!meta?.url) return null;
        // El enlace de Meta necesita el token también para descargar.
        const bin = await fetch(meta.url, { headers: { Authorization: `Bearer ${token}` } });
        const buf = Buffer.from(await bin.arrayBuffer());
        const { transcribirBuffer } = require('./transcribirAudio');
        const r = await transcribirBuffer(buf, 'nota.ogg');
        return r.error ? null : r.texto;
    } catch (e) {
        console.warn('WhatsApp: no pude transcribir el audio:', e.message);
        return null;
    }
}

const SALUDOS = /\b(hola|buenas|buen d[ií]a|buenas tardes|buenas noches|qu[eé] tal|hey)\b/i;
const PRECIO  = /\b(precio|cu[aá]nto|vale|cuesta|valor)\b/i;
const PAGO    = /\b(pago|pagar|nequi|bre.?b|transferencia|consign)/i;
const CATALOGO= /\b(qu[eé] (vendes|tienes|hay)|cat[aá]logo|productos|informaci[oó]n|info)\b/i;

/** La respuesta automática, solo con reglas. Null = que conteste Cristian. */
async function respuestaAutomatica(texto, venta) {
    const t = String(texto || '');
    const productos = await ProductoDigital.findAll({ where: { activo: true }, order: [['orden', 'ASC']] });

    // Sin catálogo cargado no se inventa nada: se avisa y punto.
    if (productos.length === 0) return null;

    const lista = productos.map(p => `• *${p.nombre}* — ${cop(p.precio)}`).join('\n');

    if (PAGO.test(t)) {
        return `Se paga por *Nequi* o *Bre-B* al *322 480 7767*.\n\n` +
               `Me mandas el comprobante por aquí y te lo despacho de una.`;
    }
    if (PRECIO.test(t) || CATALOGO.test(t)) {
        return `Esto es lo que tengo:\n\n${lista}\n\n¿Cuál te sirve?`;
    }
    // Primer contacto
    if (venta.esNueva || SALUDOS.test(t)) {
        return `¡Hola! 👋 Qué bueno que escribes.\n\n${lista}\n\n¿Cuál te interesa?`;
    }
    return null;   // algo que las reglas no cubren: lo atiende Cristian
}

/** Procesa lo que manda Meta. */
async function recibir(cuerpo) {
    const entradas = cuerpo?.entry || [];
    for (const entrada of entradas) {
        for (const cambio of entrada.cambios || entrada.changes || []) {
            const valor = cambio.value || {};
            const perfil = valor.contacts?.[0]?.profile?.name || null;

            for (const m of valor.messages || []) {
                if (vistos.has(m.id)) continue;
                recordar(m.id);

                const telefono = m.from;
                let texto = m.text?.body || null;
                let tipo = m.type;

                if (tipo === 'audio' || tipo === 'voice') {
                    texto = await textoDeAudio(m.audio?.id || m.voice?.id);
                }
                if (tipo === 'image') {
                    // Una imagen en esta conversación casi siempre es el comprobante.
                    texto = m.image?.caption || '[imagen]';
                }

                // ── El embudo ────────────────────────────────────────────
                let venta = await VentaDigital.findOne({
                    where: { telefono },
                    order: [['created_at', 'DESC']],
                });
                const esNueva = !venta || ['despachado', 'perdido'].includes(venta.estado);
                if (esNueva) {
                    venta = await VentaDigital.create({
                        telefono, nombre: perfil, estado: 'escribio',
                        temperatura: 'tibio', ultimo_contacto: new Date(),
                    });
                } else {
                    await venta.update({
                        ultimo_contacto: new Date(),
                        nombre: venta.nombre || perfil,
                    });
                }

                // Si mandó imagen, es un comprobante: se marca y se avisa fuerte.
                if (tipo === 'image') {
                    await venta.update({ estado: 'comprobante', temperatura: 'caliente', comprobante: '[imagen por WhatsApp]' });
                }

                // ── Aviso a Cristian ─────────────────────────────────────
                const quien = perfil || telefono;
                const cabeza = tipo === 'image' ? '💸 *Posible comprobante*' : '💬 *Escribieron al WhatsApp*';
                await telegram.enviar(
                    `${cabeza}\n\n*${quien}* (${telefono})\n${texto || '(' + tipo + ')'}\n\n_Está en Digitales._`
                ).catch(() => {});

                // ── Respuesta automática ─────────────────────────────────
                try {
                    const resp = await respuestaAutomatica(texto, { ...venta.toJSON(), esNueva });
                    if (resp) {
                        await enviarMensaje(telefono, resp);
                        if (venta.estado === 'escribio' && /•/.test(resp)) await venta.update({ estado: 'cotizado' });
                    }
                } catch (e) {
                    console.warn('WhatsApp: no pude responder a', telefono, '-', e.message);
                }
            }
        }
    }
}

module.exports = { recibir, respuestaAutomatica };
