const { Op } = require('sequelize');
const sequelize = require('../config/db');
const {
    MovimientoFinanciero, Deuda, AbonoDeuda, Meta, AporteMeta,
    Contrato, Cliente, Licencia, Producto, CostoServidor, Lead
} = require('../models');
const { hoyBogota, aFecha, aStr, estadoLicencia, precioLicencia } = require('../utils/licenciaCiclo');

const num = v => Number(v || 0);

// ══════════════════════════ MOVIMIENTOS ══════════════════════════════

function validarMovimiento(body) {
    const out = {};
    if ('tipo' in body) {
        if (!['ingreso', 'egreso'].includes(body.tipo)) throw new Error("tipo debe ser 'ingreso' o 'egreso'");
        out.tipo = body.tipo;
    }
    if ('ambito' in body) {
        if (!['empresa', 'personal'].includes(body.ambito)) throw new Error("ambito debe ser 'empresa' o 'personal'");
        out.ambito = body.ambito;
    }
    if ('categoria' in body) {
        if (!String(body.categoria || '').trim()) throw new Error('categoria es obligatoria');
        out.categoria = String(body.categoria).trim();
    }
    if ('concepto' in body) {
        if (!String(body.concepto || '').trim()) throw new Error('concepto es obligatorio');
        out.concepto = String(body.concepto).trim();
    }
    if ('monto' in body) {
        const m = Number(body.monto);
        if (isNaN(m) || m <= 0) throw new Error('monto debe ser mayor que cero');
        out.monto = m;
    }
    if ('fecha' in body && body.fecha) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(body.fecha)) throw new Error('fecha debe ser YYYY-MM-DD');
        aFecha(body.fecha);   // rechaza fechas que no existen (31 de febrero)
        out.fecha = body.fecha;
    }
    if ('metodo_pago' in body) out.metodo_pago = body.metodo_pago || 'transferencia';
    if ('cliente_id' in body)  out.cliente_id  = body.cliente_id || null;
    if ('recurrente' in body)  out.recurrente  = [true, 'true', 1, '1'].includes(body.recurrente);
    if ('notas' in body)       out.notas       = body.notas || null;
    return out;
}

exports.listarMovimientos = async (req, res) => {
    try {
        const where = {};
        const { desde, hasta, tipo, ambito, categoria } = req.query;
        if (desde || hasta) {
            where.fecha = {};
            if (desde) where.fecha[Op.gte] = desde;
            if (hasta) where.fecha[Op.lte] = hasta;
        }
        if (tipo)      where.tipo = tipo;
        if (ambito)    where.ambito = ambito;
        if (categoria) where.categoria = categoria;

        const movs = await MovimientoFinanciero.findAll({
            where, order: [['fecha', 'DESC'], ['id', 'DESC']], limit: Number(req.query.limit) || 500
        });
        res.json({ ok: true, data: movs, hoy: hoyBogota() });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.crearMovimiento = async (req, res) => {
    try {
        const v = validarMovimiento(req.body);
        for (const req_ of ['tipo', 'categoria', 'concepto', 'monto']) {
            if (v[req_] === undefined) throw new Error(`${req_} es obligatorio`);
        }
        if (!v.fecha)  v.fecha  = hoyBogota();
        if (!v.ambito) v.ambito = 'empresa';
        v.origen = 'manual';

        // El sueldo es el puente entre las dos cajas: un egreso de la empresa
        // se convierte automáticamente en ingreso personal, para no tener que
        // registrarlo dos veces ni olvidarse la mitad.
        const esSueldo = v.ambito === 'empresa' && v.tipo === 'egreso' && v.categoria === 'sueldo';

        const creado = await sequelize.transaction(async (t) => {
            const mov = await MovimientoFinanciero.create({ ...v, origen: esSueldo ? 'sueldo' : 'manual' }, { transaction: t });
            if (esSueldo) {
                const espejo = await MovimientoFinanciero.create({
                    tipo: 'ingreso', ambito: 'personal', categoria: 'sueldo',
                    concepto: v.concepto, monto: v.monto, fecha: v.fecha,
                    metodo_pago: v.metodo_pago || 'transferencia',
                    origen: 'sueldo', espejo_id: mov.id,
                    notas: 'Generado automáticamente desde el sueldo de la empresa'
                }, { transaction: t });
                await mov.update({ espejo_id: espejo.id }, { transaction: t });
            }
            return mov;
        });
        res.json({ ok: true, data: creado, msg: esSueldo ? 'Sueldo registrado en las dos cajas' : 'Movimiento registrado' });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.actualizarMovimiento = async (req, res) => {
    try {
        const mov = await MovimientoFinanciero.findByPk(req.params.id);
        if (!mov) return res.status(404).json({ ok: false, msg: 'No encontrado' });
        if (mov.origen !== 'manual' && mov.origen !== 'sueldo') {
            return res.status(400).json({ ok: false, msg: `Este movimiento lo generó el sistema (${mov.origen}). Corrígelo en su módulo, no aquí.` });
        }
        await mov.update(validarMovimiento(req.body));
        res.json({ ok: true, data: mov, msg: 'Movimiento actualizado' });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.eliminarMovimiento = async (req, res) => {
    try {
        const mov = await MovimientoFinanciero.findByPk(req.params.id);
        if (!mov) return res.status(404).json({ ok: false, msg: 'No encontrado' });
        await sequelize.transaction(async (t) => {
            if (mov.espejo_id) await MovimientoFinanciero.destroy({ where: { id: mov.espejo_id }, transaction: t });
            await mov.destroy({ transaction: t });
        });
        res.json({ ok: true, msg: mov.espejo_id ? 'Movimiento y su espejo eliminados' : 'Movimiento eliminado' });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

// ══════════════════════════ DEUDAS ═══════════════════════════════════

async function deudaConSaldo(d) {
    const j = d.toJSON ? d.toJSON() : d;
    const abonos = j.abonos || await AbonoDeuda.findAll({ where: { deuda_id: j.id } });
    const pagado = abonos.reduce((s, a) => s + num(a.monto), 0);
    const saldo  = Math.max(0, num(j.monto_original) - pagado);
    const interes_mensual = saldo * num(j.tasa_mensual) / 100;
    let meses_restantes = null;
    if (num(j.cuota_pactada) > interes_mensual && saldo > 0) {
        meses_restantes = Math.ceil(saldo / (num(j.cuota_pactada) - interes_mensual));
    }
    return { ...j, pagado, saldo, interes_mensual, meses_restantes, avance_pct: num(j.monto_original) ? Math.round(pagado / num(j.monto_original) * 100) : 0 };
}

exports.listarDeudas = async (req, res) => {
    const deudas = await Deuda.findAll({ include: [{ model: AbonoDeuda, as: 'abonos' }], order: [['prioridad', 'ASC'], ['id', 'ASC']] });
    const data = await Promise.all(deudas.map(deudaConSaldo));
    const saldo_total = data.filter(d => d.estado === 'activa').reduce((s, d) => s + d.saldo, 0);
    res.json({ ok: true, data, saldo_total, hoy: hoyBogota() });
};

exports.crearDeuda = async (req, res) => {
    try {
        const { acreedor, monto_original } = req.body;
        if (!String(acreedor || '').trim()) throw new Error('acreedor es obligatorio');
        if (!(Number(monto_original) > 0)) throw new Error('monto_original debe ser mayor que cero');
        const d = await Deuda.create({ ...req.body, acreedor: String(acreedor).trim(), monto_original: Number(monto_original) });
        res.json({ ok: true, data: await deudaConSaldo(d) });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.actualizarDeuda = async (req, res) => {
    try {
        const d = await Deuda.findByPk(req.params.id);
        if (!d) return res.status(404).json({ ok: false, msg: 'No encontrada' });
        await d.update(req.body);
        res.json({ ok: true, data: await deudaConSaldo(d), msg: 'Deuda actualizada' });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

// Abonar: registra el abono, lo refleja como egreso y cierra la deuda si quedó en cero.
exports.abonarDeuda = async (req, res) => {
    try {
        const monto = Number(req.body.monto);
        if (!(monto > 0)) throw new Error('monto debe ser mayor que cero');
        const fecha = req.body.fecha && /^\d{4}-\d{2}-\d{2}$/.test(req.body.fecha) ? req.body.fecha : hoyBogota();

        const r = await sequelize.transaction(async (t) => {
            const d = await Deuda.findByPk(req.params.id, { transaction: t, lock: t.LOCK.UPDATE });
            if (!d) { const e = new Error('No encontrada'); e.status = 404; throw e; }

            const previos = await AbonoDeuda.findAll({ where: { deuda_id: d.id }, transaction: t });
            const pagado  = previos.reduce((s, a) => s + num(a.monto), 0);
            const saldo   = num(d.monto_original) - pagado;
            if (monto > saldo + 0.5) throw new Error(`El abono ($${monto.toLocaleString('es-CO')}) supera el saldo ($${saldo.toLocaleString('es-CO')}).`);

            await AbonoDeuda.create({ deuda_id: d.id, monto, fecha, metodo_pago: req.body.metodo_pago || 'transferencia', notas: req.body.notas || null }, { transaction: t });
            await MovimientoFinanciero.create({
                tipo: 'egreso', ambito: d.ambito, categoria: 'deuda',
                concepto: `Abono a deuda — ${d.acreedor}`, monto, fecha,
                metodo_pago: req.body.metodo_pago || 'transferencia',
                origen: 'deuda', referencia_id: d.id
            }, { transaction: t });

            if (saldo - monto <= 0.5) await d.update({ estado: 'pagada' }, { transaction: t });
            return d;
        });

        const full = await Deuda.findByPk(r.id, { include: [{ model: AbonoDeuda, as: 'abonos' }] });
        const con = await deudaConSaldo(full);
        res.json({ ok: true, data: con, msg: con.saldo <= 0 ? '¡Deuda saldada!' : `Abonado. Saldo: $${con.saldo.toLocaleString('es-CO')}` });
    } catch (e) { res.status(e.status || 400).json({ ok: false, msg: e.message }); }
};

exports.eliminarDeuda = async (req, res) => {
    await AbonoDeuda.destroy({ where: { deuda_id: req.params.id } });
    await Deuda.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Deuda eliminada' });
};

// ══════════════════════════ METAS ════════════════════════════════════

async function metaConAvance(m) {
    const j = m.toJSON ? m.toJSON() : m;
    const aportes = j.aportes || await AporteMeta.findAll({ where: { meta_id: j.id } });
    const ahorrado = aportes.reduce((s, a) => s + num(a.monto), 0);
    const falta = Math.max(0, num(j.monto_objetivo) - ahorrado);
    return { ...j, ahorrado, falta, avance_pct: num(j.monto_objetivo) ? Math.round(ahorrado / num(j.monto_objetivo) * 100) : 0 };
}

exports.listarMetas = async (req, res) => {
    const metas = await Meta.findAll({ include: [{ model: AporteMeta, as: 'aportes' }], order: [['prioridad', 'ASC'], ['id', 'ASC']] });
    res.json({ ok: true, data: await Promise.all(metas.map(metaConAvance)), hoy: hoyBogota() });
};

exports.crearMeta = async (req, res) => {
    try {
        if (!String(req.body.nombre || '').trim()) throw new Error('nombre es obligatorio');
        if (!(Number(req.body.monto_objetivo) > 0)) throw new Error('monto_objetivo debe ser mayor que cero');
        const m = await Meta.create(req.body);
        res.json({ ok: true, data: await metaConAvance(m) });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.actualizarMeta = async (req, res) => {
    try {
        const m = await Meta.findByPk(req.params.id);
        if (!m) return res.status(404).json({ ok: false, msg: 'No encontrada' });
        await m.update(req.body);
        res.json({ ok: true, data: await metaConAvance(m), msg: 'Meta actualizada' });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.aportarMeta = async (req, res) => {
    try {
        const monto = Number(req.body.monto);
        if (!(monto > 0)) throw new Error('monto debe ser mayor que cero');
        const fecha = req.body.fecha && /^\d{4}-\d{2}-\d{2}$/.test(req.body.fecha) ? req.body.fecha : hoyBogota();
        const m = await Meta.findByPk(req.params.id);
        if (!m) return res.status(404).json({ ok: false, msg: 'No encontrada' });

        await sequelize.transaction(async (t) => {
            await AporteMeta.create({ meta_id: m.id, monto, fecha, notas: req.body.notas || null }, { transaction: t });
            await MovimientoFinanciero.create({
                tipo: 'egreso', ambito: 'personal', categoria: 'ahorro_meta',
                concepto: `Ahorro — ${m.nombre}`, monto, fecha,
                origen: 'manual', referencia_id: m.id
            }, { transaction: t });
        });

        const full = await Meta.findByPk(m.id, { include: [{ model: AporteMeta, as: 'aportes' }] });
        const con = await metaConAvance(full);
        if (con.falta <= 0 && m.estado === 'activa') await m.update({ estado: 'lograda' });
        res.json({ ok: true, data: con, msg: con.falta <= 0 ? `¡Meta lograda: ${m.nombre}!` : `Faltan $${con.falta.toLocaleString('es-CO')}` });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.eliminarMeta = async (req, res) => {
    await AporteMeta.destroy({ where: { meta_id: req.params.id } });
    await Meta.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Meta eliminada' });
};

// ══════════════════════════ TABLERO ══════════════════════════════════

// Lo que falta por cobrar de un contrato financiado.
function saldoContrato(c) {
    const total = num(c.monto), anticipo = num(c.anticipo);
    const saldo_inicial = Math.max(0, total - anticipo);
    const cuota = num(c.cuota_mensual);
    const cuotas_totales = cuota > 0 ? Math.ceil(saldo_inicial / cuota) : 0;
    return { total, anticipo, saldo_inicial, cuota, cuotas_totales };
}

exports.resumen = async (req, res) => {
    try {
        const hoy = hoyBogota();
        const d = aFecha(hoy);
        const inicioMes = aStr(new Date(Date.UTC(d.getUTCFullYear(), d.getUTCMonth(), 1)));
        const finMes    = aStr(new Date(Date.UTC(d.getUTCFullYear(), d.getUTCMonth() + 1, 0)));

        const movsMes = await MovimientoFinanciero.findAll({ where: { fecha: { [Op.between]: [inicioMes, finMes] } } });
        const suma = (tipo, ambito) => movsMes
            .filter(m => m.tipo === tipo && (!ambito || m.ambito === ambito))
            .reduce((s, m) => s + num(m.monto), 0);

        // Gasto fijo declarado como recurrente (sirve para proyectar)
        const recurrentes = await MovimientoFinanciero.findAll({ where: { recurrente: true, tipo: 'egreso' } });
        const gastoFijo = { empresa: 0, personal: 0 };
        for (const m of recurrentes) gastoFijo[m.ambito] += num(m.monto);

        // Ingreso recurrente por licencias activas
        const licencias = await Licencia.findAll({
            where: { activo: true },
            include: [{ model: Cliente, as: 'cliente', attributes: ['nombre'] }, { model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }]
        });
        const mrr_licencias = licencias.reduce((s, l) => s + precioLicencia(l), 0);

        // Contratos: mensualidad + cuotas pendientes
        const contratos = await Contrato.findAll({
            where: { estado: { [Op.in]: ['firmado', 'enviado'] } },
            include: [{ model: Cliente, as: 'cliente', attributes: ['nombre'] }]
        });
        let por_cobrar = 0, cuotas_mes = 0, mensualidades = 0;
        const detalle_contratos = [];
        for (const c of contratos) {
            const s = saldoContrato(c);
            // lo ya abonado al saldo, por movimientos de cuota de este contrato
            const abonos = await MovimientoFinanciero.sum('monto', {
                where: { origen: 'contrato', referencia_id: c.id, categoria: 'cuota_contrato' }
            }) || 0;
            const pendiente = Math.max(0, s.saldo_inicial - abonos);
            por_cobrar += pendiente;
            if (pendiente > 0) cuotas_mes += Math.min(s.cuota, pendiente);
            mensualidades += num(c.mensualidad);
            detalle_contratos.push({
                id: c.id, cliente: c.cliente?.nombre, titulo: c.titulo, tipo_servicio: c.tipo_servicio,
                total: s.total, anticipo: s.anticipo, abonado_saldo: abonos, pendiente,
                cuota_mensual: s.cuota, dia_cobro: c.dia_cobro, mensualidad: num(c.mensualidad),
                cuotas_faltantes: s.cuota > 0 ? Math.ceil(pendiente / s.cuota) : 0
            });
        }

        // Tratos en negociación. NUNCA se suman a lo confirmado: van aparte,
        // con su probabilidad, para poder ver el escenario sin engañarse.
        const enNegociacion = await Lead.findAll({
            where: {
                estado: { [Op.notIn]: ['cliente', 'descartado', 'sin_respuesta'] },
                [Op.or]: [{ valor_unico: { [Op.gt]: 0 } }, { valor_mensual: { [Op.gt]: 0 } }]
            },
            order: [['probabilidad', 'DESC']]
        });
        const tratos = enNegociacion.map(l => ({
            id: l.id, nombre: l.nombre, empresa: l.empresa, estado: l.estado,
            valor_unico: num(l.valor_unico), valor_mensual: num(l.valor_mensual),
            probabilidad: Number(l.probabilidad) || 0,
            fecha_estimada_cierre: l.fecha_estimada_cierre,
            // Valor ponderado: lo que vale hoy ese trato dado lo probable que sea.
            ponderado_unico:   Math.round(num(l.valor_unico)   * (Number(l.probabilidad) || 0) / 100),
            ponderado_mensual: Math.round(num(l.valor_mensual) * (Number(l.probabilidad) || 0) / 100),
            notas: l.notas
        }));
        const tratos_por_cerrar = {
            cantidad: tratos.length,
            valor_unico:   tratos.reduce((s, t) => s + t.valor_unico, 0),
            valor_mensual: tratos.reduce((s, t) => s + t.valor_mensual, 0),
            ponderado_unico:   tratos.reduce((s, t) => s + t.ponderado_unico, 0),
            ponderado_mensual: tratos.reduce((s, t) => s + t.ponderado_mensual, 0),
            detalle: tratos
        };

        const costos = await CostoServidor.findAll({ where: { activo: true } });
        const costo_infra_usd = costos.filter(c => c.moneda === 'USD').reduce((s, c) => s + num(c.monto), 0);
        const costo_infra_cop = costos.filter(c => c.moneda === 'COP').reduce((s, c) => s + num(c.monto), 0);

        const deudas = await Deuda.findAll({ where: { estado: 'activa' }, include: [{ model: AbonoDeuda, as: 'abonos' }] });
        const deudasCon = await Promise.all(deudas.map(deudaConSaldo));
        const deuda_total = deudasCon.reduce((s, x) => s + x.saldo, 0);

        const metas = await Meta.findAll({ where: { estado: 'activa' }, include: [{ model: AporteMeta, as: 'aportes' }] });
        const metasCon = await Promise.all(metas.map(metaConAvance));

        // Recurrente "verdadero": lo que no se acaba cuando terminen los contratos.
        //
        // Sale SOLO de las licencias activas. La mensualidad también está escrita
        // en el contrato, pero sumar las dos contaba el mismo dinero dos veces.
        // La licencia manda porque es la que cobra y la que bloquea; lo pactado
        // en el contrato se reporta aparte para poder detectar si se separaron.
        const recurrente_real = mrr_licencias;
        const descuadre_mensualidad = mensualidades - mrr_licencias;
        const gasto_total_fijo = gastoFijo.empresa + gastoFijo.personal;

        res.json({
            ok: true, hoy, periodo: { desde: inicioMes, hasta: finMes },
            mes: {
                ingresos: suma('ingreso'), egresos: suma('egreso'),
                ingresos_empresa: suma('ingreso', 'empresa'), egresos_empresa: suma('egreso', 'empresa'),
                ingresos_personal: suma('ingreso', 'personal'), egresos_personal: suma('egreso', 'personal'),
                balance: suma('ingreso') - suma('egreso'),
                movimientos: movsMes.length
            },
            recurrente: {
                licencias: mrr_licencias,
                pactado_en_contratos: mensualidades,
                // Si no es 0, lo que dice el contrato y lo que cobra la licencia
                // se separaron: hay que revisar cuál de los dos está mal.
                descuadre_mensualidad: descuadre_mensualidad,
                total_real: recurrente_real,
                cuotas_contrato: cuotas_mes,
                total_con_cuotas: recurrente_real + cuotas_mes
            },
            gasto_fijo: { ...gastoFijo, total: gasto_total_fijo },
            margen: {
                real: recurrente_real - gasto_total_fijo,
                con_cuotas: recurrente_real + cuotas_mes - gasto_total_fijo
            },
            por_cobrar: { total: por_cobrar, contratos: detalle_contratos },
            tratos_por_cerrar,
            infraestructura: { usd: costo_infra_usd, cop: costo_infra_cop, items: costos.length },
            deudas: { total: deuda_total, detalle: deudasCon },
            metas: metasCon,
            concentracion: licencias.map(l => ({
                cliente: l.cliente?.nombre, sistema: l.producto?.nombre,
                monto: precioLicencia(l),
                pct: mrr_licencias ? Math.round(precioLicencia(l) / mrr_licencias * 100) : 0,
                estado: estadoLicencia(l, hoy).estado
            })).sort((a, b) => b.monto - a.monto)
        });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.deudaConSaldo = deudaConSaldo;
exports.saldoContrato = saldoContrato;
