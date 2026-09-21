const { Op } = require('sequelize');
const {
    Licencia, Cliente, Producto, Contrato, CostoServidor,
    MovimientoFinanciero, Deuda, AbonoDeuda, Reunion
} = require('../models');
const { hoyBogota, aFecha, aStr, estadoLicencia, precioLicencia } = require('../utils/licenciaCiclo');

const num = v => Number(v || 0);
const cop = n => '$' + Number(n || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 });

// Orden de urgencia: lo que cuesta plata hoy va primero.
const PESO = { alta: 0, media: 1, baja: 2 };

/**
 * Lo que necesita la atención de Cristian hoy, ya resuelto en acciones
 * concretas. El cálculo vive en el servidor a propósito: la misma lista la
 * consume la pantalla de Inicio y (más adelante) el aviso de Telegram, así
 * que no puede haber dos versiones de "qué es urgente".
 */
async function armarAcciones(hoy = hoyBogota()) {
    const acciones = [];
    const push = a => acciones.push(a);

    // ── Licencias: vencimientos, mora y bloqueos ──────────────────────
    const licencias = await Licencia.findAll({
        where: { activo: true },
        include: [
            { model: Cliente,  as: 'cliente',  attributes: ['id', 'nombre', 'telefono'] },
            { model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }
        ]
    });

    for (const lic of licencias) {
        const c = estadoLicencia(lic, hoy);
        const quien = lic.cliente?.nombre || 'Cliente';
        const cuanto = cop(precioLicencia(lic));

        if (c.estado === 'bloqueada') {
            push({ urgencia: 'alta', color: 'rojo', icono: 'lock',
                titulo: `${quien} está bloqueado`,
                detalle: `${cuanto} · ${c.dias_mora} días de mora · su sistema no funciona`,
                accion: 'Registrar pago', ruta: '/licencias', ref: lic.id });
        } else if (c.bloquear && c.fecha_bloqueo === hoy) {
            push({ urgencia: 'alta', color: 'rojo', icono: 'lock',
                titulo: `${quien} se bloquea hoy`,
                detalle: `${cuanto} · si no paga hoy, su sistema se apaga`,
                accion: 'Ver licencia', ruta: '/licencias', ref: lic.id });
        } else if (c.dias_restantes === 0) {
            push({ urgencia: 'media', color: 'ambar', icono: 'clock',
                titulo: `${quien} vence hoy`,
                detalle: `${cuanto}${c.bloquear ? ` · se bloquea el ${c.fecha_bloqueo}` : ' · no se bloquea'}`,
                accion: 'Cobrar', ruta: '/licencias', ref: lic.id });
        } else if (c.dias_mora > 0) {
            push({ urgencia: 'media', color: 'ambar', icono: 'alert',
                titulo: `${quien} lleva ${c.dias_mora} ${c.dias_mora === 1 ? 'día' : 'días'} de mora`,
                detalle: c.bloquear ? `${cuanto} · se bloquea el ${c.fecha_bloqueo}` : `${cuanto} · nunca se bloquea, pero no ha pagado`,
                accion: 'Cobrar', ruta: '/licencias', ref: lic.id });
        } else if (c.dias_restantes > 0 && c.dias_restantes <= 3) {
            push({ urgencia: 'baja', color: 'ambar', icono: 'clock',
                titulo: `${quien} vence en ${c.dias_restantes} ${c.dias_restantes === 1 ? 'día' : 'días'}`,
                detalle: `${cuanto} · el ${c.fecha_vencimiento}`,
                accion: 'Ver', ruta: '/licencias', ref: lic.id });
        }
    }

    // ── Contratos con saldo sin cobrar ────────────────────────────────
    const contratos = await Contrato.findAll({
        where: { estado: { [Op.in]: ['firmado', 'enviado'] } },
        include: [{ model: Cliente, as: 'cliente', attributes: ['nombre'] }]
    });

    for (const ct of contratos) {
        const saldoInicial = Math.max(0, num(ct.monto) - num(ct.anticipo));
        if (saldoInicial <= 0) continue;
        const abonado = await MovimientoFinanciero.sum('monto', {
            where: { origen: 'contrato', referencia_id: ct.id, categoria: 'cuota_contrato' }
        }) || 0;
        const pendiente = Math.max(0, saldoInicial - abonado);
        if (pendiente <= 0) continue;

        const cuota = num(ct.cuota_mensual);
        const cuotas = cuota > 0 ? Math.ceil(pendiente / cuota) : 0;
        const nuncaPago = abonado === 0;

        push({
            urgencia: nuncaPago ? 'alta' : 'media',
            color: nuncaPago ? 'rojo' : 'ambar', icono: 'money',
            titulo: nuncaPago
                ? `${ct.cliente?.nombre} nunca te ha pagado la cuota`
                : `${ct.cliente?.nombre} tiene saldo pendiente`,
            detalle: `${cop(pendiente)} pendientes${cuota > 0 ? ` · ${cuotas} ${cuotas === 1 ? 'cuota' : 'cuotas'} de ${cop(cuota)}` : ''}`,
            accion: 'Cobrar', ruta: '/contratos', ref: ct.id
        });
    }

    // ── Costos de servidor ────────────────────────────────────────────
    const costos = await CostoServidor.findAll({ where: { activo: true } });
    for (const co of costos) {
        const dias = Math.round((aFecha(co.proximo_pago) - aFecha(hoy)) / 86400000);
        const monto = (co.moneda === 'USD' ? 'US$' : '$') + Number(co.monto).toLocaleString('es-CO');
        if (dias < 0) {
            push({ urgencia: 'alta', color: 'rojo', icono: 'server',
                titulo: `${co.nombre} está atrasado`,
                detalle: `${monto} · venció el ${co.proximo_pago} · riesgo de que se caiga`,
                accion: 'Marcar pagado', ruta: '/costos', ref: co.id });
        } else if (dias === 0) {
            push({ urgencia: 'media', color: 'ambar', icono: 'server',
                titulo: `Hoy toca pagar ${co.nombre}`,
                detalle: `${monto}${co.cuenta ? ` · ${co.cuenta}` : ''}`,
                accion: 'Marcar pagado', ruta: '/costos', ref: co.id });
        } else if (dias <= 3) {
            push({ urgencia: 'baja', color: 'azul', icono: 'server',
                titulo: `${co.nombre} se paga en ${dias} ${dias === 1 ? 'día' : 'días'}`,
                detalle: `${monto} · el ${co.proximo_pago}`,
                accion: 'Ver', ruta: '/costos', ref: co.id });
        }
    }

    // Sin un solo costo registrado, el margen que muestra el tablero es mentira.
    if (costos.length === 0) {
        push({ urgencia: 'media', color: 'azul', icono: 'server',
            titulo: 'No tienes medido cuánto te cuesta la infraestructura',
            detalle: 'Railway, dominios y APIs no están registrados: tu margen real es menor al que ves',
            accion: 'Medir', ruta: '/costos' });
    }

    // ── Concentración de ingreso en un solo cliente ───────────────────
    const mrr = licencias.reduce((s, l) => s + precioLicencia(l), 0);
    if (mrr > 0) {
        const porCliente = {};
        for (const l of licencias) {
            const k = l.cliente?.nombre || '?';
            porCliente[k] = (porCliente[k] || 0) + precioLicencia(l);
        }
        const [quien, monto] = Object.entries(porCliente).sort((a, b) => b[1] - a[1])[0];
        const pct = Math.round(monto / mrr * 100);
        if (pct >= 40) {
            const tieneContrato = contratos.some(ct => ct.cliente?.nombre === quien && num(ct.monto) > 0);
            push({ urgencia: 'baja', color: 'violeta', icono: 'doc',
                titulo: `${quien} es el ${pct}% de tu ingreso`,
                detalle: tieneContrato
                    ? `${cop(monto)} al mes concentrados en un solo cliente`
                    : `${cop(monto)} al mes por acuerdo de palabra, sin contrato escrito`,
                accion: tieneContrato ? 'Ver' : 'Redactar contrato', ruta: '/contratos' });
        }
    }

    // ── Reuniones: hoy y lo que viene ─────────────────────────────────
    // Antes solo miraba HOY. Una reunión agendada para el martes no aparecía en
    // ninguna parte hasta el martes por la mañana, que es cuando ya no sirve
    // saberlo. Se muestran los próximos 3 días, con el día por delante.
    try {
        const fin = new Date(aFecha(hoy).getTime() + 3 * 86400000);
        const hasta = aStr(fin);
        const reuniones = await Reunion.findAll({
            where: { fecha: { [Op.between]: [`${hoy} 00:00:00`, `${hasta} 23:59:59`] } },
            order: [['fecha', 'ASC']],
        });
        for (const r of reuniones) {
            const f = new Date(r.fecha);
            const dia = aStr(f);
            const esHoy = dia === hoy;
            const hora = f.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', timeZone: 'America/Bogota' });
            const cuando = esHoy
                ? 'hoy'
                : f.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'short', timeZone: 'America/Bogota' });
            push({ urgencia: esHoy ? 'media' : 'baja', color: 'azul', icono: 'calendar',
                titulo: `Reunión ${cuando}: ${r.titulo || r.asunto || 'sin título'}`,
                detalle: `${hora}${r.participantes ? ' · ' + r.participantes : ''}`,
                accion: 'Ver', ruta: '/calendario', ref: r.id });
        }
    } catch (e) { console.warn('Hoy: no pude leer las reuniones:', e.message); }

    acciones.sort((a, b) => PESO[a.urgencia] - PESO[b.urgencia]);
    return acciones.map((a, i) => ({ id: i + 1, ...a }));
}

exports.resumen = async (req, res) => {
    try {
        const hoy = hoyBogota();
        const d = aFecha(hoy);
        const inicioMes = `${d.getUTCFullYear()}-${String(d.getUTCMonth() + 1).padStart(2, '0')}-01`;

        const acciones = await armarAcciones(hoy);

        // Contexto compacto: tres cifras, nada más. La pantalla de Inicio no es
        // un tablero; los números van de apoyo, no de protagonistas.
        const licencias = await Licencia.findAll({ where: { activo: true }, include: [{ model: Producto, as: 'producto', attributes: ['precio_mensual'] }] });
        const recurrente = licencias.reduce((s, l) => s + precioLicencia(l), 0);

        const fijos = await MovimientoFinanciero.findAll({ where: { recurrente: true, tipo: 'egreso' } });
        const gastoFijo = fijos.reduce((s, m) => s + num(m.monto), 0);

        const contratos = await Contrato.findAll({ where: { estado: { [Op.in]: ['firmado', 'enviado'] } } });
        let porCobrar = 0;
        for (const ct of contratos) {
            const saldo = Math.max(0, num(ct.monto) - num(ct.anticipo));
            if (saldo <= 0) continue;
            const abonado = await MovimientoFinanciero.sum('monto', {
                where: { origen: 'contrato', referencia_id: ct.id, categoria: 'cuota_contrato' }
            }) || 0;
            porCobrar += Math.max(0, saldo - abonado);
        }

        const deudas = await Deuda.findAll({ where: { estado: 'activa' }, include: [{ model: AbonoDeuda, as: 'abonos' }] });
        const deuda = deudas.reduce((s, d_) => {
            const pagado = (d_.abonos || []).reduce((x, a) => x + num(a.monto), 0);
            return s + Math.max(0, num(d_.monto_original) - pagado);
        }, 0);

        // "Ya resueltas hoy": lo que registró hoy, para que se vea el avance.
        const hechasHoy = await MovimientoFinanciero.findAll({
            where: { fecha: hoy }, order: [['id', 'DESC']], limit: 5
        });

        res.json({
            ok: true, hoy,
            acciones,
            pendientes: acciones.length,
            contexto: {
                libre: recurrente - gastoFijo,
                recurrente, gasto_fijo: gastoFijo,
                por_cobrar: porCobrar, deuda
            },
            hechas_hoy: hechasHoy.map(m => ({
                id: m.id, concepto: m.concepto, monto: num(m.monto), tipo: m.tipo, categoria: m.categoria
            })),
            periodo_desde: inicioMes
        });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.armarAcciones = armarAcciones;
