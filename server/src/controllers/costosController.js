const { CostoServidor, Licencia, Cliente, Producto } = require('../models');
const { hoyBogota, aFecha, aStr } = require('../utils/licenciaCiclo');

const include = [{
    model: Licencia, as: 'licencia', attributes: ['id', 'license_key'],
    include: [
        { model: Cliente,  as: 'cliente',  attributes: ['nombre'] },
        { model: Producto, as: 'producto', attributes: ['nombre'] }
    ]
}];

function ultimoDiaMes(y, m0) { return new Date(Date.UTC(y, m0 + 1, 0)).getUTCDate(); }

// Primer día de pago estrictamente posterior a `desde` ('YYYY-MM-DD')
function siguientePago(diaPago, desde = hoyBogota()) {
    const d = aFecha(desde);
    let y = d.getUTCFullYear(), m = d.getUTCMonth();
    let cand = new Date(Date.UTC(y, m, Math.min(diaPago, ultimoDiaMes(y, m))));
    if (cand <= d) { m += 1; cand = new Date(Date.UTC(y, m, Math.min(diaPago, ultimoDiaMes(y, m)))); }
    return aStr(cand);
}

function conEstado(c) {
    const j = c.toJSON ? c.toJSON() : c;
    const dias = Math.round((aFecha(j.proximo_pago) - aFecha(hoyBogota())) / 86400000);
    let estado = 'al_dia';
    if (!j.activo) estado = 'inactivo';
    else if (dias < 0) estado = 'atrasado';
    else if (dias === 0) estado = 'hoy';
    else if (dias <= 5) estado = 'proximo';
    return { ...j, dias_para_pago: dias, estado };
}

function validar(body) {
    const out = {};
    if ('nombre' in body) { if (!String(body.nombre || '').trim()) throw new Error('nombre es obligatorio'); out.nombre = String(body.nombre).trim(); }
    if ('proveedor' in body) out.proveedor = String(body.proveedor || 'Railway').trim();
    if ('licencia_id' in body) out.licencia_id = body.licencia_id ? parseInt(body.licencia_id) : null;
    if ('monto' in body) { const m = Number(body.monto); if (isNaN(m) || m < 0) throw new Error('monto inválido'); out.monto = m; }
    if ('moneda' in body) { if (!['COP', 'USD'].includes(body.moneda)) throw new Error('moneda debe ser COP o USD'); out.moneda = body.moneda; }
    if ('dia_pago' in body) { const d = parseInt(body.dia_pago); if (isNaN(d) || d < 1 || d > 31) throw new Error('dia_pago debe estar entre 1 y 31'); out.dia_pago = d; }
    if ('proximo_pago' in body && body.proximo_pago) { if (!/^\d{4}-\d{2}-\d{2}$/.test(body.proximo_pago)) throw new Error('proximo_pago debe ser YYYY-MM-DD'); out.proximo_pago = body.proximo_pago; }
    if ('cuenta' in body) out.cuenta = body.cuenta || null;
    if ('activo' in body) out.activo = [true, 'true', 1, '1'].includes(body.activo);
    if ('notas' in body) out.notas = body.notas || null;
    return out;
}

exports.listar = async (req, res) => {
    const costos = await CostoServidor.findAll({ include, order: [['proximo_pago', 'ASC']] });
    res.json({ ok: true, data: costos.map(conEstado), hoy: hoyBogota() });
};

exports.crear = async (req, res) => {
    try {
        const v = validar(req.body);
        if (!v.nombre || v.monto == null || !v.dia_pago) return res.status(400).json({ ok: false, msg: 'nombre, monto y dia_pago son obligatorios' });
        if (!v.proximo_pago) v.proximo_pago = siguientePago(v.dia_pago);
        const c = await CostoServidor.create(v);
        res.json({ ok: true, data: conEstado(await CostoServidor.findByPk(c.id, { include })) });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const c = await CostoServidor.findByPk(req.params.id);
        if (!c) return res.status(404).json({ ok: false, msg: 'No encontrado' });
        await c.update(validar(req.body));
        res.json({ ok: true, data: conEstado(await CostoServidor.findByPk(c.id, { include })), msg: 'Costo actualizado' });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

// "Ya pagué": registra la fecha y corre el próximo pago al siguiente día de pago
exports.marcarPagado = async (req, res) => {
    try {
        const c = await CostoServidor.findByPk(req.params.id);
        if (!c) return res.status(404).json({ ok: false, msg: 'No encontrado' });
        const fecha = req.body?.fecha && /^\d{4}-\d{2}-\d{2}$/.test(req.body.fecha) ? req.body.fecha : hoyBogota();
        // Siempre se avanza UN ciclo desde el que estaba programado. Si el costo
        // venia atrasado varios meses, sigue marcado como atrasado hasta ponerse
        // al dia: marcar "pagado" una vez no debe borrar los meses que faltan.
        await c.update({ ultimo_pago: fecha, proximo_pago: siguientePago(c.dia_pago, c.proximo_pago) });
        res.json({ ok: true, data: conEstado(await CostoServidor.findByPk(c.id, { include })), msg: `Pagado. Próximo: ${c.proximo_pago}` });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.eliminar = async (req, res) => {
    await CostoServidor.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Costo eliminado' });
};

exports.siguientePago = siguientePago;
exports.conEstado = conEstado;
