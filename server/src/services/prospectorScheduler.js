const cron = require('node-cron');
const { buscarNegociosCategoria } = require('./propuestasScraper');
const { generarPropuesta }        = require('./propuestasEngine');
const { enviarMensaje: enviarWA } = require('./whatsappService');
const { llamar }                  = require('./llamadasService');

let CONFIG = {
    activo:          true,
    hora:            9,
    categorias: [
        'restaurantes', 'ferreterías', 'clínicas', 'tiendas de ropa',
        'hoteles', 'colegios', 'farmacias', 'peluquerías',
        'talleres mecánicos', 'panaderías', 'gimnasios', 'veterinarias',
        'constructoras', 'distribuidoras', 'agencias de viajes', 'salones de belleza',
    ],
    ciudades:        ['Medellín', 'Bogotá', 'Cali', 'Barranquilla', 'Bucaramanga'],
    maxPorBusqueda:  8,
    categoriaActual: 0,
    ciudadActual:    0,
};

// Referencia al cache del controlador — se inyecta al inicializar
let cacheRef = null;

function setCacheRef(cache) {
    cacheRef = cache;
}

async function ejecutarProspeccionDiaria() {
    if (!CONFIG.activo) return;

    const categoria = CONFIG.categorias[CONFIG.categoriaActual % CONFIG.categorias.length];
    const ciudad    = CONFIG.ciudades[CONFIG.ciudadActual % CONFIG.ciudades.length];

    console.log(`[Propuestas Auto] Buscando: "${categoria}" en ${ciudad}`);

    try {
        const negocios = await buscarNegociosCategoria({
            categoria,
            ciudad,
            maxResultados: CONFIG.maxPorBusqueda
        });

        let generadas = 0;
        for (const info of negocios) {
            try {
                const { html, demos, analisis } = await generarPropuesta(info);
                const key = `auto_${Date.now()}_${generadas}`;
                if (cacheRef) cacheRef[key] = { info, html, demos, analisis, auto: true };
                generadas++;

                const emails   = info.web?.emails || [];
                const telefono = info.telefono;
                const gmailUser = process.env.GMAIL_USER;
                const gmailPass = process.env.GMAIL_APP_PASSWORD;

                // ── Auto-envío de EMAIL ──────────────────────────────────────
                if (emails.length > 0 && gmailUser && gmailPass) {
                    try {
                        const nodemailer = require('nodemailer');
                        const transporter = nodemailer.createTransport({
                            service: 'gmail',
                            auth: { user: gmailUser, pass: gmailPass }
                        });
                        await transporter.sendMail({
                            from:    `"${process.env.NOMBRE_EMPRESA || 'AI Company CO'}" <${gmailUser}>`,
                            to:      emails[0],
                            subject: `Propuesta de transformación digital para ${info.nombre}`,
                            html
                        });
                        console.log(`  📧 Email → ${emails[0]} (${info.nombre})`);
                        if (cacheRef) cacheRef[key].emailEnviado = emails[0];
                    } catch (emailErr) {
                        console.error(`  ✗ Email fallido ${emails[0]}:`, emailErr.message);
                    }
                }

                // ── Llamada en frío automática (Vapi) ───────────────────────
                if (telefono && process.env.VAPI_API_KEY && process.env.VAPI_PHONE_ID) {
                    try {
                        // Esperar 30 min después del email/WA para no saturar
                        setTimeout(async () => {
                            await llamar({ telefono, infoNegocio: info });
                        }, 30 * 60 * 1000);
                        console.log(`  📞 Llamada programada en 30 min → ${telefono} (${info.nombre})`);
                        if (cacheRef) cacheRef[key].llamadaProgramada = true;
                    } catch (callErr) {
                        console.error(`  ✗ Llamada fallida ${telefono}:`, callErr.message);
                    }
                }

                // ── Auto-envío por WhatsApp con plantilla aprobada ──────────
                const telLimpio = telefono?.replace(/\D/g, '');
                // Solo enviar a números móviles colombianos (empiezan con 3)
                const esCelular = telLimpio && telLimpio.replace(/^57/, '').startsWith('3');
                if (esCelular && process.env.WHATSAPP_TOKEN && process.env.WHATSAPP_PHONE_ID) {
                    try {
                        // Rotar entre las 3 plantillas aprobadas
                        const plantillas = [
                            {
                                name: 'prospecto_ia_colombia_v1',
                                params: [info.nombre || 'equipo', categoria]
                            },
                            {
                                name: 'prospecto_whatsapp_automatico',
                                params: [info.nombre || 'equipo']
                            },
                            {
                                name: 'prospecto_reunion_gratis',
                                params: [info.nombre || 'equipo', categoria]
                            }
                        ];
                        const plantilla = plantillas[Math.floor(Math.random() * plantillas.length)];

                        const body = {
                            messaging_product: 'whatsapp',
                            to: telLimpio,
                            type: 'template',
                            template: {
                                name: plantilla.name,
                                language: { code: 'es' },
                                components: [{
                                    type: 'body',
                                    parameters: plantilla.params.map(p => ({ type: 'text', text: p }))
                                }]
                            }
                        };

                        const r = await fetch(
                            `https://graph.facebook.com/v21.0/${process.env.WHATSAPP_PHONE_ID}/messages`,
                            {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': `Bearer ${process.env.WHATSAPP_TOKEN}`
                                },
                                body: JSON.stringify(body)
                            }
                        );
                        const data = await r.json();
                        if (data.error) throw new Error(data.error.message);
                        console.log(`  💬 WhatsApp → ${telLimpio} (${info.nombre}) [${plantilla.name}]`);
                        if (cacheRef) cacheRef[key].waEnviado = telLimpio;
                    } catch (waErr) {
                        console.error(`  ✗ WA fallido ${telefono}:`, waErr.message);
                    }
                } else if (telefono && !esCelular) {
                    console.log(`  ⏭ WA omitido (número fijo): ${telefono}`);
                }
            } catch (e) {
                console.error(`  ✗ Error en ${info.nombre}:`, e.message);
            }
        }

        console.log(`[Propuestas Auto] ✓ ${generadas} propuestas generadas para "${categoria}" en ${ciudad}`);
    } catch (e) {
        console.error('[Propuestas Auto] Error:', e.message);
    }

    // Rotar para la próxima ejecución
    CONFIG.categoriaActual++;
    if (CONFIG.categoriaActual % CONFIG.categorias.length === 0) {
        CONFIG.ciudadActual = (CONFIG.ciudadActual + 1) % CONFIG.ciudades.length;
    }
}

let cronJob = null;

function reprogramar() {
    if (cronJob) { cronJob.stop(); cronJob = null; }
    if (!CONFIG.activo) return;
    cronJob = cron.schedule(`0 ${CONFIG.hora} * * *`, ejecutarProspeccionDiaria);
    console.log(`✅ Prospector auto activo: diario a las ${CONFIG.hora}:00`);
}

function iniciarProspectorScheduler() {
    reprogramar();
    if (!CONFIG.activo) {
        console.log('ℹ️  Prospector auto desactivado (actívalo desde el panel)');
    }
}

function actualizarConfig(nueva) {
    const hCambio = nueva.hora !== undefined && nueva.hora !== CONFIG.hora;
    const aCambio = nueva.activo !== undefined && nueva.activo !== CONFIG.activo;
    CONFIG = { ...CONFIG, ...nueva };
    if (hCambio || aCambio) reprogramar();
}

function getConfig() {
    const { categoriaActual, ciudadActual, ...visible } = CONFIG;
    return { ...visible, siguiente: `${CONFIG.categorias[CONFIG.categoriaActual % CONFIG.categorias.length]} en ${CONFIG.ciudades[CONFIG.ciudadActual % CONFIG.ciudades.length]}` };
}

module.exports = { iniciarProspectorScheduler, ejecutarProspeccionDiaria, actualizarConfig, getConfig, setCacheRef };
