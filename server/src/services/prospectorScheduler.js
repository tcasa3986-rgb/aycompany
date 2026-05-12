const cron = require('node-cron');
const { prospectar } = require('./prospectorService');

// Configuración por defecto del prospector autónomo
// El usuario puede sobreescribir esto desde el panel
let CONFIG = {
    activo: false,
    hora:   8, // 8am hora Colombia
    categorias: [
        'restaurantes', 'ferreterías', 'clínicas', 'tiendas de ropa',
        'constructoras', 'talleres mecánicos', 'hoteles', 'colegios',
        'farmacias', 'consultorios médicos', 'peluquerías', 'gimnasios'
    ],
    ciudades: ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Bucaramanga'],
    fuentes: ['google_places'],
    maxPorBusqueda: 10, // leads por combinación ciudad+categoría
    categoriaActual: 0, // índice rotativo para no repetir siempre las mismas
    ciudadActual: 0,
};

// Cada día a la hora configurada, busca en UNA categoría y UNA ciudad (rotando)
// Así no agota el crédito de Google Places de golpe
async function ejecutarProspeccionDiaria() {
    if (!CONFIG.activo) return;
    if (!process.env.GOOGLE_PLACES_API_KEY && !process.env.APOLLO_API_KEY) {
        console.log('[Prospector] Sin API keys configuradas, saltando...');
        return;
    }

    const categoria = CONFIG.categorias[CONFIG.categoriaActual % CONFIG.categorias.length];
    const ciudad    = CONFIG.ciudades[CONFIG.ciudadActual % CONFIG.ciudades.length];

    console.log(`[Prospector] Buscando: "${categoria}" en ${ciudad}`);

    try {
        const resultado = await prospectar({
            categorias:      [categoria],
            ciudades:        [ciudad],
            fuentes:         CONFIG.fuentes,
            maxPorBusqueda:  CONFIG.maxPorBusqueda,
        });

        console.log(`[Prospector] ✓ ${resultado.guardados} leads nuevos (${resultado.duplicados} duplicados)`);
        if (resultado.errores.length) console.warn('[Prospector] Errores:', resultado.errores);
    } catch (e) {
        console.error('[Prospector] Error en prospección diaria:', e.message);
    }

    // Avanzar al siguiente par ciudad+categoría para mañana
    CONFIG.categoriaActual++;
    if (CONFIG.categoriaActual % CONFIG.categorias.length === 0) {
        CONFIG.ciudadActual = (CONFIG.ciudadActual + 1) % CONFIG.ciudades.length;
    }
}

function iniciarProspectorScheduler() {
    // Correr todos los días a las 8am (hora del servidor)
    cron.schedule('0 8 * * *', ejecutarProspeccionDiaria);
    console.log('✅ Prospector autónomo iniciado (diario a las 8am)');
}

function actualizarConfig(nuevaConfig) {
    CONFIG = { ...CONFIG, ...nuevaConfig };
}

function getConfig() {
    return { ...CONFIG };
}

module.exports = { iniciarProspectorScheduler, ejecutarProspeccionDiaria, actualizarConfig, getConfig };
