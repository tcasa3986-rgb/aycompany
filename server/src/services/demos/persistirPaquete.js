const { construirDemo, leerPaquete, propuestaBase } = require('./paqueteDemo');

// Se genera fuera de la transacción; dentro se vuelve a leer y bloquear la fila.
// También serializa dos preparaciones simultáneas del mismo negocio.
async function persistirPaquete({ NegocioSinWeb, DemoVersion, sequelize, id, propuesta, crearSlug }) {
    return sequelize.transaction(async transaction => {
        const n = await NegocioSinWeb.findByPk(id, { transaction, lock: transaction.LOCK.UPDATE });
        if (!n) throw Object.assign(new Error('No existe el negocio'), { status: 404 });
        const p = propuesta || leerPaquete(n.demo_html)?.propuesta || propuestaBase();
        const demo_slug = n.demo_slug || crearSlug(n.nombre);
        const html = construirDemo({ ...n.get({ plain: true }), demo_slug }, p);
        if (n.demo_html && n.demo_html !== html) await DemoVersion.create({
            negocio_id: n.id, html: n.demo_html, generada_en: n.demo_generada_en,
        }, { transaction });
        await n.update({ demo_html: html, demo_slug, demo_generada_en: new Date(),
            estado: n.estado === 'encontrado' ? 'demo_generada' : n.estado }, { transaction });
        return n;
    });
}

module.exports = { persistirPaquete };
