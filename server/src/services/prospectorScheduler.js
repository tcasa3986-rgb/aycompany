const cron = require('node-cron');
const { buscarNegociosCategoria } = require('./propuestasScraper');
const { generarPropuesta }        = require('./propuestasEngine');

let CONFIG = {
    activo:          false,
    hora:            8,
    categorias: [
        'restaurantes', 'ferreterías', 'clínicas', 'tiendas de ropa',
        'hoteles', 'colegios', 'farmacias', 'peluquerías',
        'talleres mecánicos', 'panaderías', 'gimnasios', 'veterinarias',
    ],
    ciudades:        ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Bucaramanga'],
    maxPorBusqueda:  5,
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
