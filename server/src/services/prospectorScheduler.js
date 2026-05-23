// Sistema migrado a propuestas personalizadas (propuestasScraper + propuestasEngine)
// Ya no hay búsqueda masiva programada — las propuestas se generan una a una desde el panel

function iniciarProspectorScheduler() {
    console.log('ℹ️  Prospector: modo propuestas individuales activo (sin scheduler)');
}

function ejecutarProspeccionDiaria() {}
function actualizarConfig() {}
function getConfig() { return { activo: false }; }

module.exports = { iniciarProspectorScheduler, ejecutarProspeccionDiaria, actualizarConfig, getConfig };
