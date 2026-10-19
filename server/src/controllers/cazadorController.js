const { NegocioSinWeb, DemoVersion } = require('../models');
const sequelize = require('../config/db');
const { cazar } = require('../services/cazadorSinWeb');
const { Op } = require('sequelize');
const crypto = require('crypto');
const { construirDemo, construirPropuesta, validarPropuesta, leerPaquete, propuestaBase, VERSION } = require('../services/demos/paqueteDemo');
const { persistirPaquete } = require('../services/demos/persistirPaquete');
const { tokenVisita, tokenValido, nombreCookie, yaContada, conMedicion, TTL } = require('../services/demos/visitasDemo');

/** Un slug corto e imposible de adivinar: la demo no debe ser enumerable. */
function slug(nombre) {
    const base = String(nombre).toLowerCase()
        .normalize('NFD').replace(/[̀-ͯ]/g, '')
        .replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').slice(0, 28);
    return `${base}-${crypto.randomBytes(3).toString('hex')}`;
}

// ── Buscar en Google Maps y guardar los que valen ────────────────────────────
exports.buscar = async (req, res) => {
    try {
        const { rubro, ciudad } = req.body || {};
        if (!rubro || !ciudad) return res.status(400).json({ ok: false, msg: 'Dime el rubro y la ciudad' });

        const minimo = Number(req.body.minimo) || 40;
        const r = await cazar({ rubro, ciudad, minimo, max: Number(req.body.max) || 20 });

        // Se guardan solo los que pasan el corte. Los demás ni ocupan espacio:
        // si no puede pagar una web, tenerlo en la lista solo estorba.
        let nuevos = 0, repetidos = 0;
        for (const c of r.candidatos) {
            const [, creado] = await NegocioSinWeb.findOrCreate({
                where: { place_id: c.place_id },
                defaults: {
                    place_id: c.place_id, nombre: c.nombre, direccion: c.direccion,
                    telefono: c.telefono, ciudad: c.ciudad, rubro: c.rubro, categoria: c.categoria,
                    calificacion: c.calificacion, resenas: c.reseñas,
                    web_parcial: c.web_parcial, red_social: c.red_social, maps: c.maps,
                    score: c.score, razones: c.razones.join(' · '),
                },
            });
            creado ? nuevos++ : repetidos++;
        }

        res.json({
            ok: true,
            msg: `${nuevos} nuevos de ${r.total} que devolvió Google`,
            revisados: r.total, con_web: r.con_web, sin_web: r.sin_web,
            guardados: nuevos, ya_estaban: repetidos, bajo_puntaje: r.descartados,
            candidatos: r.candidatos,
        });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

// ── La lista ─────────────────────────────────────────────────────────────────
exports.listar = async (req, res) => {
    try {
        const where = {};
        if (req.query.estado) where.estado = req.query.estado;
        if (req.query.ciudad) where.ciudad = req.query.ciudad;

        const data = await NegocioSinWeb.findAll({
            where, order: [['score', 'DESC'], ['resenas', 'DESC']], limit: 300,
        });

        const porEstado = {};
        for (const n of await NegocioSinWeb.findAll({ attributes: ['estado'] })) {
            porEstado[n.estado] = (porEstado[n.estado] || 0) + 1;
        }

        // Los que abrieron la demo y no han contestado: ahí está la plata.
        const calientes = await NegocioSinWeb.findAll({
            where: { visitas: { [Op.gt]: 0 }, estado: { [Op.in]: ['enviado', 'abrio'] } },
            order: [['visitas', 'DESC']], limit: 20,
        });

        const resumir = n => { const v = n.get({ plain: true }); const paquete = leerPaquete(v.demo_html); delete v.demo_html; return { ...v, paquete, version_actual: VERSION }; };
        res.json({ ok: true, data: data.map(resumir), embudo: porEstado, calientes: calientes.map(resumir) });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const n = await NegocioSinWeb.findByPk(req.params.id);
        if (!n) return res.status(404).json({ ok: false, msg: 'No existe' });
        const campos = ['estado', 'motivo_descarte', 'notas', 'telefono', 'contactado_en'];
        const datos = Object.fromEntries(campos.filter(c => c in (req.body || {})).map(c => [c, req.body[c]]));
        if (datos.estado && !['encontrado','demo_generada','enviado','abrio','respondio','cliente','descartado'].includes(datos.estado))
            return res.status(400).json({ ok: false, msg: 'Estado inválido' });
        if (datos.estado === 'enviado' && !leerPaquete(n.demo_html)?.propuesta.aprobada)
            return res.status(400).json({ ok: false, msg: 'Revisa y aprueba el paquete antes de marcar el envío' });
        if (datos.estado === 'enviado' && !n.contactado_en) datos.contactado_en = new Date();
        await n.update(datos);
        res.json({ ok: true, msg: 'Actualizado', data: n });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};


// Preparar no envía mensajes ni consulta IA/Places. Conserva el enlace y respalda
// el HTML anterior en una transacción, antes de reemplazarlo.
exports.generarDemo = async (req, res) => {
    let propuesta;
    try { propuesta = req.body?.propuesta === undefined ? undefined : validarPropuesta(req.body.propuesta); }
    catch (e) { return res.status(400).json({ ok: false, msg: e.message }); }
    try {
        const n = await persistirPaquete({ NegocioSinWeb, DemoVersion, sequelize,
            id: req.params.id, propuesta, crearSlug: slug });
        const base = (process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app').replace(/\/$/, '');
        res.json({ ok: true, msg: `Demo y propuesta de ${n.nombre} preparadas`,
            enlace: `${base}/demo/${n.demo_slug}`, propuesta: `${base}/propuesta/${n.demo_slug}` });
    } catch (e) { res.status(e.status || 500).json({ ok: false, msg: e.status ? e.message : 'No se pudo preparar el paquete; la versión anterior se conserva. Inténtalo de nuevo.' }); }
};

// Se retira la escritura de HTML arbitrario: sólo se guarda el paquete validado.
exports.guardarDemo = (req, res) => req.body?.html
    ? res.status(400).json({ ok: false, msg: 'Usa Preparar paquete: el HTML y el respaldo se generan desde datos revisados.' })
    : exports.generarDemo(req, res);

exports.preview = async (req, res) => {
    try {
        const n = await NegocioSinWeb.findByPk(req.params.id);
        if (!n) return res.status(404).json({ ok: false, msg: 'No existe' });
        const datos = { ...n.get({ plain: true }), demo_slug: n.demo_slug || 'vista-previa' };
        const p = leerPaquete(n.demo_html)?.propuesta || propuestaBase();
        const html = req.query.tipo === 'propuesta' ? construirPropuesta(datos, p) : construirDemo(datos, p);
        res.set('Cache-Control', 'no-store');
        res.json({ ok: true, html });
    } catch { res.status(500).json({ ok: false, msg: 'No se pudo abrir la vista previa' }); }
};

function cabeceras(res) {
    res.set('X-Robots-Tag', 'noindex, nofollow, noarchive');
    res.set('Cache-Control', 'no-store');
    res.set('Referrer-Policy', 'strict-origin-when-cross-origin');
}
exports.verDemo = async (req, res) => {
    try {
        const n = await NegocioSinWeb.findOne({ where: { demo_slug: req.params.slug } });
        if (!n || !n.demo_html) return res.status(404).send('Esta muestra no está disponible.');
        const p = leerPaquete(n.demo_html)?.propuesta || propuestaBase();
        // Las demos anteriores también reciben el diseño nuevo; su HTML permanece
        // conservado hasta preparar explícitamente la nueva versión.
        const html = construirDemo(n.get({ plain: true }), p);
        cabeceras(res);
        res.type('html').send(conMedicion(html, n.demo_slug, tokenVisita(n.demo_slug, process.env.JWT_SECRET)));
    } catch { res.status(500).send('No pudimos abrir la muestra.'); }
};
exports.verPropuesta = async (req, res) => {
    try {
        const n = await NegocioSinWeb.findOne({ where: { demo_slug: req.params.slug } });
        if (!n || !n.demo_html) return res.status(404).send('Esta propuesta no está disponible.');
        cabeceras(res);
        res.type('html').send(construirPropuesta(n.get({ plain: true }), leerPaquete(n.demo_html)?.propuesta || propuestaBase()));
    } catch { res.status(500).send('No pudimos abrir la propuesta.'); }
};
exports.registrarVisita = async (req, res) => {
    const negocioSlug = req.params.slug;
    const secret = process.env.JWT_SECRET;
    const origin = req.get('origin');
    // El token se entrega en la página, no es una credencial de administración.
    // No se guardan IPs, mensajes ni datos personales del visitante.
    try {
        if (!origin || new URL(origin).host !== req.get('host') || !tokenValido(req.body?.token, negocioSlug, secret))
            return res.status(403).json({ ok: false });
        if (yaContada(req, negocioSlug, secret)) return res.json({ ok: true, contada: false });
        const existe = await sequelize.transaction(async transaction => {
            const n = await NegocioSinWeb.findOne({ where: { demo_slug: negocioSlug }, transaction, lock: transaction.LOCK.UPDATE });
            if (!n || !n.demo_html) return false;
            await n.increment('visitas', { transaction });
            const ahora = new Date();
            await n.update({ primera_visita: n.primera_visita || ahora, ultima_visita: ahora,
                estado: n.estado === 'enviado' ? 'abrio' : n.estado }, { transaction });
            return true;
        });
        if (!existe) return res.status(404).json({ ok: false });
        res.cookie(nombreCookie(negocioSlug), tokenVisita(negocioSlug, secret), {
            maxAge: TTL, httpOnly: true, sameSite: 'lax', secure: process.env.NODE_ENV === 'production', path: '/demo',
        });
        res.json({ ok: true, contada: true });
    } catch { res.status(400).json({ ok: false }); }
};
exports.slug = slug;
