const cron = require('node-cron');
const { generarYPublicarStory } = require('./storiesService');

// Temas por tipo — complementan los carruseles del mismo día
const TEMAS = {
    curiosidad: [
        'tiempo perdido en tareas manuales',
        'clientes que no reciben respuesta rápida',
        'negocios sin sistema de seguimiento de leads',
        'costo de no tener automatización',
        'empresas que ya usan IA vs las que no',
        'pérdida de ventas por lenta atención',
        'procesos manuales que frenan el crecimiento',
        'estadísticas de respuesta al cliente en Colombia',
    ],
    educativo: [
        'cómo funciona un bot de ventas por WhatsApp',
        'diferencia entre chatbot básico y agente con IA',
        'automatización de seguimiento de leads paso a paso',
        'integración de IA en el proceso de ventas',
        'casos reales de automatización en Colombia',
        'herramientas de IA para pymes colombianas',
    ],
    ventas: [
        'bot de WhatsApp que vende 24/7',
        'sistema de captación automática de leads',
        'automatización de atención al cliente',
        'integración completa de IA en tu negocio',
    ],
};

// Ciclo: curiosidad → educativo → ventas → curiosidad...
const CICLO = ['curiosidad', 'educativo', 'curiosidad', 'ventas'];
let diaActual = 0;

async function ejecutarHistoriasDiarias() {
    const tipo     = CICLO[diaActual % CICLO.length];
    const lista    = TEMAS[tipo];
    const contexto = lista[Math.floor(diaActual / CICLO.length) % lista.length];

    console.log(`[StoriesScheduler] Generando historias ${tipo}: "${contexto}"`);

    try {
        const resultado = await generarYPublicarStory(tipo, contexto);
        console.log(`[StoriesScheduler] ✅ ${resultado.frames} frames listos`);
    } catch (e) {
        console.error('[StoriesScheduler] Error:', e.message);
    }

    diaActual++;
}

function iniciarStoriesScheduler() {
    // 9am Colombia (14:00 UTC) — 1 hora antes del carrusel
    cron.schedule('0 14 * * *', ejecutarHistoriasDiarias, { timezone: 'America/Bogota' });
    console.log('✅ Stories scheduler activo (9am Colombia, ciclo curiosidad→educativo→ventas)');
}

module.exports = { iniciarStoriesScheduler, ejecutarHistoriasDiarias };
