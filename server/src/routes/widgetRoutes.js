// Endpoint para widgets de pantalla de inicio (Scriptable en iPhone, KWGT en
// Android) y para cualquier cosa que quiera pintar el resumen fuera de la app.
//
// Seguridad: NO usa el login normal. Un widget guarda su clave en el celular de
// forma permanente, así que no puede llevar un JWT (vence en 12 h) ni la
// contraseña de admin. Usa WIDGET_KEY, una clave aparte que SOLO abre esta
// ruta y solo lee. Si se filtra, lo peor que pasa es que alguien vea el
// resumen; no puede tocar nada. Se rota cambiando la variable en Railway.
const router = require('express').Router();
const { armarAcciones } = require('../controllers/hoyController');
const { Licencia, Producto, MovimientoFinanciero, Contrato, Deuda, AbonoDeuda } = require('../models');
const { Op } = require('sequelize');
const { hoyBogota, precioLicencia } = require('../utils/licenciaCiclo');

const num = v => Number(v || 0);

function conClave(req, res, next) {
    const esperada = process.env.WIDGET_KEY;
    if (!esperada) return res.status(503).json({ ok: false, msg: 'WIDGET_KEY no configurada en el servidor' });
    const dada = req.query.key || req.get('X-Widget-Key');
    if (!dada || dada !== esperada) return res.status(401).json({ ok: false, msg: 'Clave de widget inválida' });
    next();
}

router.get('/', conClave, async (req, res) => {
    try {
        const hoy = hoyBogota();
        const acciones = await armarAcciones(hoy);

        const licencias = await Licencia.findAll({ where: { activo: true }, include: [{ model: Producto, as: 'producto', attributes: ['precio_mensual'] }] });
        const recurrente = licencias.reduce((s, l) => s + precioLicencia(l), 0);
        const fijos = await MovimientoFinanciero.findAll({ where: { recurrente: true, tipo: 'egreso' } });
        const gastoFijo = fijos.reduce((s, m) => s + num(m.monto), 0);

        const contratos = await Contrato.findAll({ where: { estado: { [Op.in]: ['firmado', 'enviado'] } } });
        let porCobrar = 0;
        for (const ct of contratos) {
            const saldo = Math.max(0, num(ct.monto) - num(ct.anticipo));
            if (saldo <= 0) continue;
            const abonado = await MovimientoFinanciero.sum('monto', { where: { origen: 'contrato', referencia_id: ct.id, categoria: 'cuota_contrato' } }) || 0;
            porCobrar += Math.max(0, saldo - abonado);
        }
        const deudas = await Deuda.findAll({ where: { estado: 'activa' }, include: [{ model: AbonoDeuda, as: 'abonos' }] });
        const deuda = deudas.reduce((s, d) => s + Math.max(0, num(d.monto_original) - (d.abonos || []).reduce((x, a) => x + num(a.monto), 0)), 0);

        // Respuesta pequeña y plana: un widget tiene poco espacio y poca CPU.
        res.set('Cache-Control', 'no-store');
        res.json({
            ok: true, hoy,
            libre: recurrente - gastoFijo,
            te_deben: porCobrar,
            recurrente, deuda,
            pendientes: acciones.length,
            urgentes: acciones.filter(a => a.urgencia === 'alta').length,
            // Las 3 más importantes, con título corto para que quepan
            acciones: acciones.slice(0, 3).map(a => ({
                t: a.titulo.length > 42 ? a.titulo.slice(0, 40) + '…' : a.titulo,
                u: a.urgencia,
            })),
        });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
});

module.exports = router;
