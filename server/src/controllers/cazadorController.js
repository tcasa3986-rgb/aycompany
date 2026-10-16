const { NegocioSinWeb } = require('../models');
const { cazar } = require('../services/cazadorSinWeb');
const { Op } = require('sequelize');
const crypto = require('crypto');
const { generar } = require('../services/generadorDemo');

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

        res.json({ ok: true, data, embudo: porEstado, calientes });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const n = await NegocioSinWeb.findByPk(req.params.id);
        if (!n) return res.status(404).json({ ok: false, msg: 'No existe' });
        const campos = ['estado', 'motivo_descarte', 'notas', 'telefono', 'contactado_en'];
        const datos = Object.fromEntries(campos.filter(c => c in (req.body || {})).map(c => [c, req.body[c]]));
        if (datos.estado === 'enviado' && !n.contactado_en) datos.contactado_en = new Date();
        await n.update(datos);
        res.json({ ok: true, msg: 'Actualizado', data: n });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

/** Guarda la demo ya generada y devuelve el enlace único para enviársela. */
exports.guardarDemo = async (req, res) => {
    try {
        const n = await NegocioSinWeb.findByPk(req.params.id);
        if (!n) return res.status(404).json({ ok: false, msg: 'No existe' });
        if (!req.body?.html) return res.status(400).json({ ok: false, msg: 'Falta el HTML de la demo' });

        await n.update({
            demo_html: req.body.html,
            demo_slug: n.demo_slug || slug(n.nombre),
            demo_generada_en: new Date(),
            estado: n.estado === 'encontrado' ? 'demo_generada' : n.estado,
        });
        const base = process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app';
        res.json({ ok: true, msg: 'Demo guardada', enlace: `${base}/demo/${n.demo_slug}`, data: n });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

/** Genera la demo de un negocio y devuelve el enlace listo para enviar. */
exports.generarDemo = async (req, res) => {
    try {
        const n = await NegocioSinWeb.findByPk(req.params.id);
        if (!n) return res.status(404).json({ ok: false, msg: 'No existe' });

        const { html, textos } = await generar(n);
        await n.update({
            demo_html: html,
            demo_slug: n.demo_slug || slug(n.nombre),
            demo_generada_en: new Date(),
            estado: n.estado === 'encontrado' ? 'demo_generada' : n.estado,
        });

        const base = process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app';
        res.json({
            ok: true,
            msg: `Demo de ${n.nombre} lista`,
            enlace: `${base}/demo/${n.demo_slug}`,
            titular: textos.titular,
            servicios: (textos.servicios || []).length,
            data: n,
        });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

/**
 * La demo, en público. Es la única ruta sin login de este módulo.
 *
 * Dos cosas que no son opcionales:
 *   · `noindex`: la página lleva el logo y las fotos del negocio, que son
 *     suyos. Mostrársela a él es una cosa; dejar que Google la indexe y
 *     compita con su propia ficha es otra muy distinta.
 *   · Contar la visita: saber que abrió la demo tres veces vale más que
 *     cualquier respuesta. Es la señal de compra más honesta que hay.
 */
exports.verDemo = async (req, res) => {
    try {
        const n = await NegocioSinWeb.findOne({ where: { demo_slug: req.params.slug } });
        if (!n || !n.demo_html) return res.status(404).send('Esta muestra ya no está disponible.');

        const ahora = new Date();
        await n.update({
            visitas: (n.visitas || 0) + 1,
            primera_visita: n.primera_visita || ahora,
            ultima_visita: ahora,
            estado: ['enviado', 'demo_generada'].includes(n.estado) ? 'abrio' : n.estado,
        });

        res.set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        res.set('Cache-Control', 'no-store');
        res.type('html').send(n.demo_html);
    } catch (e) { res.status(500).send('Error'); }
};

exports.slug = slug;
