const { ProductoDigital, VentaDigital, MovimientoFinanciero } = require('../models');
const sequelize = require('../config/db');
const { Op } = require('sequelize');
const { hoyBogota, aFecha, aStr } = require('../utils/licenciaCiclo');
const telegram = require('../services/telegramService');

const num = v => Number(v || 0);
const cop = n => '$' + Number(n || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 });

// Los estados por los que pasa una venta, en orden. Sirve para pintar el embudo
// y para saber qué sigue.
const EMBUDO = ['escribio', 'cotizado', 'comprobante', 'pagado', 'despachado'];

// ── Catálogo ──────────────────────────────────────────────────────────────────
exports.listarProductos = async (req, res) => {
    try {
        const where = req.query.todos === '1' ? {} : { activo: true };
        const data = await ProductoDigital.findAll({ where, order: [['orden', 'ASC'], ['id', 'ASC']] });
        res.json({ ok: true, data });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

const CAMPOS_P = ['nombre', 'descripcion', 'precio', 'tipo', 'origen_url', 'dolor', 'objeciones', 'activo', 'orden', 'notas'];
const limpiarP = b => Object.fromEntries(CAMPOS_P.filter(c => c in b).map(c => [c, b[c]]));

exports.crearProducto = async (req, res) => {
    try {
        if (!req.body?.nombre) return res.status(400).json({ ok: false, msg: 'Ponle nombre al producto' });
        const p = await ProductoDigital.create(limpiarP(req.body));
        res.json({ ok: true, msg: `"${p.nombre}" agregado`, data: p });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.actualizarProducto = async (req, res) => {
    try {
        const p = await ProductoDigital.findByPk(req.params.id);
        if (!p) return res.status(404).json({ ok: false, msg: 'No existe' });
        await p.update(limpiarP(req.body));
        res.json({ ok: true, msg: 'Actualizado', data: p });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.eliminarProducto = async (req, res) => {
    try {
        const p = await ProductoDigital.findByPk(req.params.id);
        if (!p) return res.status(404).json({ ok: false, msg: 'No existe' });
        const ventas = await VentaDigital.count({ where: { producto_id: p.id } });
        // Si ya tiene ventas no se borra: se perdería el historial de la caja.
        if (ventas > 0) { await p.update({ activo: false }); return res.json({ ok: true, msg: `"${p.nombre}" archivado (tiene ${ventas} ventas)` }); }
        await p.destroy();
        res.json({ ok: true, msg: 'Borrado' });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

// ── Ventas / embudo ───────────────────────────────────────────────────────────
exports.listarVentas = async (req, res) => {
    try {
        const where = {};
        if (req.query.estado) where.estado = req.query.estado;
        const data = await VentaDigital.findAll({
            where,
            include: [{ model: ProductoDigital, as: 'producto', attributes: ['nombre', 'precio', 'tipo'] }],
            order: [['updated_at', 'DESC']], limit: 300,
        });

        // El embudo: cuántos hay en cada paso.
        const porEstado = {};
        for (const e of [...EMBUDO, 'perdido']) porEstado[e] = 0;
        for (const v of await VentaDigital.findAll({ attributes: ['estado'] })) porEstado[v.estado]++;

        res.json({ ok: true, data, embudo: porEstado });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

const CAMPOS_V = ['telefono', 'nombre', 'producto_id', 'estado', 'temperatura', 'motivo_perdida',
                  'monto', 'metodo', 'comprobante', 'campana', 'utm', 'notas', 'enlace_entrega'];
const limpiarV = b => Object.fromEntries(CAMPOS_V.filter(c => c in b).map(c => [c, b[c]]));

exports.crearVenta = async (req, res) => {
    try {
        if (!req.body?.telefono) return res.status(400).json({ ok: false, msg: 'Falta el teléfono' });
        const datos = limpiarV(req.body);
        // Si no dieron monto pero sí producto, se toma el precio de lista.
        if (!num(datos.monto) && datos.producto_id) {
            const p = await ProductoDigital.findByPk(datos.producto_id);
            if (p) datos.monto = p.precio;
        }
        const v = await VentaDigital.create({ ...datos, ultimo_contacto: new Date() });
        res.json({ ok: true, msg: 'Registrado', data: v });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.actualizarVenta = async (req, res) => {
    try {
        const v = await VentaDigital.findByPk(req.params.id);
        if (!v) return res.status(404).json({ ok: false, msg: 'No existe' });
        await v.update({ ...limpiarV(req.body), ultimo_contacto: new Date() });
        res.json({ ok: true, msg: 'Actualizado', data: v });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.eliminarVenta = async (req, res) => {
    try {
        const v = await VentaDigital.findByPk(req.params.id);
        if (!v) return res.status(404).json({ ok: false, msg: 'No existe' });
        if (v.movimiento_id) return res.status(400).json({ ok: false, msg: 'Esta venta ya entró a la caja. Córrígela en Finanzas si está mal.' });
        await v.destroy();
        res.json({ ok: true, msg: 'Borrado' });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

/**
 * Confirmar el pago. Es el único punto donde la plata entra a la caja, y por
 * eso va en transacción con candado: si se toca dos veces (desde el celular y
 * desde el widget, o por doble toque), se duplicaría el ingreso.
 */
exports.confirmarPago = async (req, res) => {
    try {
        const resultado = await sequelize.transaction(async (t) => {
            const v = await VentaDigital.findByPk(req.params.id, {
                include: [{ model: ProductoDigital, as: 'producto', attributes: ['nombre'] }],
                transaction: t, lock: t.LOCK.UPDATE,
            });
            if (!v) throw new Error('No existe');
            if (v.movimiento_id) return { v, yaEstaba: true };

            const monto = num(req.body?.monto) || num(v.monto);
            if (!(monto > 0)) throw new Error('¿De cuánto fue el pago?');

            const mov = await MovimientoFinanciero.create({
                tipo: 'ingreso', ambito: 'empresa', categoria: 'productos_digitales',
                concepto: `${v.producto?.nombre || 'Producto digital'} — ${v.nombre || v.telefono}`,
                monto, fecha: hoyBogota(),
                metodo_pago: v.metodo || 'nequi',
                origen: 'manual',
            }, { transaction: t });

            await v.update({
                estado: 'pagado', monto, confirmado_en: new Date(),
                movimiento_id: mov.id, temperatura: 'caliente',
            }, { transaction: t });

            return { v, monto, yaEstaba: false };
        });

        if (resultado.yaEstaba) return res.json({ ok: true, msg: 'Ese pago ya estaba confirmado', data: resultado.v });
        res.json({ ok: true, msg: `Pago de ${cop(resultado.monto)} confirmado y en la caja`, data: resultado.v });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.rechazarPago = async (req, res) => {
    try {
        const v = await VentaDigital.findByPk(req.params.id);
        if (!v) return res.status(404).json({ ok: false, msg: 'No existe' });
        if (v.movimiento_id) return res.status(400).json({ ok: false, msg: 'Ya fue confirmado. Para revertirlo, borra el movimiento en Finanzas.' });
        await v.update({ estado: 'cotizado', comprobante: null, notas: [v.notas, req.body?.motivo].filter(Boolean).join(' · ') });
        res.json({ ok: true, msg: 'Marcado como no válido. Vuelve a quedar en cotizado.', data: v });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.despachar = async (req, res) => {
    try {
        const v = await VentaDigital.findByPk(req.params.id);
        if (!v) return res.status(404).json({ ok: false, msg: 'No existe' });
        // No se despacha lo que no está pagado. Es la regla que protege contra
        // los comprobantes falsos.
        if (v.estado !== 'pagado' && v.estado !== 'despachado') {
            return res.status(400).json({ ok: false, msg: 'Todavía no has confirmado el pago de esta venta' });
        }
        await v.update({ estado: 'despachado', despachado_en: new Date(), enlace_entrega: req.body?.enlace || v.enlace_entrega });
        res.json({ ok: true, msg: 'Despachado', data: v });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

/**
 * Cómo va el negocio. El número que decide el presupuesto de pauta no es
 * cuántos escriben: es cuántos pagan.
 */
exports.resumen = async (req, res) => {
    try {
        const hoy = hoyBogota();
        const dias = Math.min(180, Math.max(1, Number(req.query.dias) || 7));
        const desde = aStr(new Date(aFecha(hoy).getTime() - (dias - 1) * 86400000));

        const ventas = await VentaDigital.findAll({
            where: { created_at: { [Op.gte]: desde + ' 00:00:00' } },
            include: [{ model: ProductoDigital, as: 'producto', attributes: ['nombre'] }],
        });

        const escribieron = ventas.length;
        const pagadas = ventas.filter(v => v.movimiento_id);
        const vendido = pagadas.reduce((s, v) => s + num(v.monto), 0);
        const conversion = escribieron ? Math.round((pagadas.length / escribieron) * 100) : 0;
        const ticket = pagadas.length ? Math.round(vendido / pagadas.length) : 0;

        // Por producto
        const porProducto = {};
        for (const v of pagadas) {
            const k = v.producto?.nombre || 'Sin producto';
            porProducto[k] = (porProducto[k] || 0) + 1;
        }

        // Dónde se cae la gente
        const porEstado = {};
        for (const v of ventas) porEstado[v.estado] = (porEstado[v.estado] || 0) + 1;

        // La caja de hoy
        const hoyVentas = pagadas.filter(v => String(v.confirmado_en || '').slice(0, 10) === hoy);

        res.json({
            ok: true, hoy, desde, dias,
            escribieron, compraron: pagadas.length, conversion,
            vendido, ticket,
            hoy_ventas: hoyVentas.length,
            hoy_vendido: hoyVentas.reduce((s, v) => s + num(v.monto), 0),
            por_producto: Object.entries(porProducto).sort((a, b) => b[1] - a[1]),
            por_estado: porEstado,
            // Sin esto, el margen es una ilusión: la pauta la pone Cristian a mano.
            falta_pauta: 'Registra lo que gastaste en anuncios en Finanzas (categoría publicidad) para ver el margen real',
        });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

/** El aviso al celular cuando llega un comprobante por confirmar. */
exports.avisarComprobante = async (venta) => {
    const quien = venta.nombre || venta.telefono;
    await telegram.enviar(
        `💸 *Comprobante por verificar*\n\n${quien}\n${cop(venta.monto)} · ${venta.metodo}\n` +
        `${venta.producto?.nombre || ''}\n\n_Confírmalo en Digitales para que se despache._`
    ).catch(() => {});
};

exports.EMBUDO = EMBUDO;
