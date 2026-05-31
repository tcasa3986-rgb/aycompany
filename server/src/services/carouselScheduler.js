const cron = require('node-cron');
const { generarYPublicar } = require('./carouselService');

// Temas educativos rotativos — 1 por semana
const TEMAS_EDUCATIVOS = [
    'tiempo perdido en tareas manuales y repetitivas',
    'pérdida de clientes por lenta respuesta',
    'proceso de ventas caótico sin sistema',
    'no saber qué está funcionando en el negocio',
    'crecimiento frenado por falta de automatización',
    'competencia que ya usa IA y está ganando',
    'atención al cliente 24/7 sin contratar personal',
    'seguimiento de leads que se pierden',
];

// Servicios de AI Company CO para carruseles de ventas
const SERVICIOS_VENTAS = [
    'bot de WhatsApp que vende 24/7 sin vendedor',
    'automatización de atención al cliente con IA',
    'sistema de captación y cierre de leads automático',
    'integración de IA en procesos de ventas',
];

let diaActual = 0;

async function ejecutarCarruselDiario() {
    // Día par → educativo, día impar → ventas
    const tipo = diaActual % 2 === 0 ? 'educativo' : 'ventas';
    const lista = tipo === 'educativo' ? TEMAS_EDUCATIVOS : SERVICIOS_VENTAS;
    const contexto = lista[Math.floor(diaActual / 2) % lista.length];

    console.log(`[CarouselScheduler] Generando carrusel ${tipo}: "${contexto}"`);

    try {
        const resultado = await generarYPublicar(tipo, contexto);
        console.log(`[CarouselScheduler] ✅ Publicado — ${resultado.slides} slides`);
    } catch (e) {
        console.error('[CarouselScheduler] Error:', e.message);
    }

    diaActual++;
}

function iniciarCarouselScheduler() {
    // Publicar todos los días a las 10am hora Colombia (3pm UTC)
    cron.schedule('0 15 * * *', ejecutarCarruselDiario, { timezone: 'America/Bogota' });
    console.log('✅ Carousel scheduler activo (10am Colombia, alternando educativo/ventas)');
}

module.exports = { iniciarCarouselScheduler, ejecutarCarruselDiario };
