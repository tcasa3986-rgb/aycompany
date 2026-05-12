const { prospectar } = require('../services/prospectorService');
const { actualizarConfig, getConfig, ejecutarProspeccionDiaria } = require('../services/prospectorScheduler');

// Busqueda manual desde el panel
exports.buscar = async (req, res) => {
    try {
        const { categorias, ciudades, fuentes, maxPorBusqueda } = req.body;
        if (!categorias?.length || !ciudades?.length) {
            return res.status(400).json({ error: 'Debes indicar al menos una categoría y una ciudad' });
        }
        const resultado = await prospectar({
            categorias,
            ciudades,
            fuentes:        fuentes || ['google_places'],
            maxPorBusqueda: maxPorBusqueda || 10,
        });
        res.json(resultado);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

// Ver y guardar configuración del prospector autónomo
exports.getConfig = (req, res) => {
    const config = getConfig();
    // No exponer índices internos
    const { categoriaActual, ciudadActual, ...visible } = config;
    res.json(visible);
};

exports.updateConfig = (req, res) => {
    actualizarConfig(req.body);
    res.json({ ok: true });
};

// Ejecutar ciclo diario manualmente
exports.ejecutarAhora = async (req, res) => {
    res.json({ ok: true, mensaje: 'Prospección iniciada en segundo plano' });
    try { await ejecutarProspeccionDiaria(); } catch (e) { console.error('[Prospector manual]', e.message); }
};

// Estado de las API keys configuradas
exports.estadoKeys = (req, res) => {
    res.json({
        google_places: !!process.env.GOOGLE_PLACES_API_KEY,
        apollo:        !!process.env.APOLLO_API_KEY,
    });
};
