const { v4: uuidv4 } = require('uuid');
const sequelize = require('../config/db');
const { Licencia, Cliente, Producto, CostoServidor } = require('../models');
const { notificarNuevaLicencia, notificarRenovacion } = require('../services/licenciaNotificaciones');
const { registrarPagoLicencia } = require('../services/licenciaPagoService');
const { estadoLicencia, siguienteVencimiento, precioLicencia, hoyBogota } = require('../utils/licenciaCiclo');

const include = [
    { model: Cliente,  as: 'cliente',  attributes: ['id', 'nombre', 'telefono', 'email'] },
    { model: Producto, as: 'producto', attributes: ['id', 'nombre', 'precio_mensual'] },
    { model: CostoServidor, as: 'costos', attributes: ['id', 'nombre', 'monto', 'moneda', 'dia_pago', 'proximo_pago', 'activo'] }
];

// Adjunta el estado calculado (misma regla que usan los sistemas cliente)
function conEstado(lic) {
    const j = lic.toJSON ? lic.toJSON() : lic;
    return { ...j, precio_efectivo: precioLicencia(j), ciclo: estadoLicencia(j) };
}

// Campos del ciclo que el admin puede fijar; valida rangos
function camposCiclo(body) {
    const out = {};
    if ('dia_corte' in body) {
        const d = body.dia_corte === '' || body.dia_corte == null ? null : parseInt(body.dia_corte);
        if (d !== null && (isNaN(d) || d < 1 || d > 31)) throw new Error('dia_corte debe estar entre 1 y 31');
        out.dia_corte = d;
    }
    if ('dias_gracia' in body) {
        const g = parseInt(body.dias_gracia) || 0;
        if (g < 0 || g > 60) throw new Error('dias_gracia debe estar entre 0 y 60');
        out.dias_gracia = g;
    }
    if ('bloquear' in body) out.bloquear = [true, 'true', 1, '1'].includes(body.bloquear);
    if ('precio_mensual' in body) {
        const pr = body.precio_mensual === '' || body.precio_mensual == null ? null : Number(body.precio_mensual);
        if (pr !== null && (isNaN(pr) || pr < 0)) throw new Error('precio_mensual inválido');
        out.precio_mensual = pr;
    }
    if ('notas' in body) out.notas = body.notas || null;
    if ('fecha_vencimiento' in body && body.fecha_vencimiento) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(body.fecha_vencimiento)) throw new Error('fecha_vencimiento debe ser YYYY-MM-DD');
        out.fecha_vencimiento = body.fecha_vencimiento;
    }
    return out;
}

exports.listar = async (req, res) => {
    const licencias = await Licencia.findAll({ include, order: [['created_at', 'DESC']] });
    res.json({ ok: true, data: licencias.map(conEstado), hoy: hoyBogota() });
};

exports.crear = async (req, res) => {
    try {
        const { cliente_id, producto_id, meses = 1 } = req.body;
        if (!cliente_id || !producto_id) return res.status(400).json({ ok: false, msg: 'cliente_id y producto_id son obligatorios' });
        const ciclo = camposCiclo(req.body);

        // Primer vencimiento: explícito, o calculado desde hoy con el día de corte
        let fecha_vencimiento = ciclo.fecha_vencimiento;
        if (!fecha_vencimiento) {
            fecha_vencimiento = siguienteVencimiento({ fecha_vencimiento: hoyBogota(), dia_corte: ciclo.dia_corte }, meses);
        }
        delete ciclo.fecha_vencimiento;

        const licencia = await Licencia.create({
            cliente_id, producto_id,
            license_key: uuidv4(),
            fecha_inicio: hoyBogota(),
            fecha_vencimiento,
            ...ciclo
        });
        const completa = await Licencia.findByPk(licencia.id, { include });
        if (completa?.cliente?.email) {
            notificarNuevaLicencia({
                clienteEmail: completa.cliente.email, clienteNombre: completa.cliente.nombre,
                productoNombre: completa.producto?.nombre || 'Sistema',
                fechaVencimiento: completa.fecha_vencimiento, licenseKey: completa.license_key
            });
        }
        res.json({ ok: true, data: conEstado(completa) });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const completa = await sequelize.transaction(async (t) => {
            const lic = await Licencia.findByPk(req.params.id, { transaction: t, lock: t.LOCK.UPDATE });
            if (!lic) { const e = new Error('No encontrada'); e.status = 404; throw e; }
            const cambios = camposCiclo(req.body);

            // Si el formulario trae un vencimiento distinto al que hay en BD, es
            // porque se cargo antes de que entrara un pago. No se pisa a ciegas:
            // hay que mandar vencimiento_visto para confirmar que se vio el actual.
            if (cambios.fecha_vencimiento && cambios.fecha_vencimiento !== String(lic.fecha_vencimiento).split('T')[0]) {
                const visto = req.body.vencimiento_visto;
                if (visto && visto !== String(lic.fecha_vencimiento).split('T')[0]) {
                    const e = new Error(`La licencia cambió mientras editabas (ahora vence ${lic.fecha_vencimiento}). Recarga y vuelve a intentar.`);
                    e.status = 409; throw e;
                }
            }
            if (req.body.cliente_id)  cambios.cliente_id  = req.body.cliente_id;
            if (req.body.producto_id) cambios.producto_id = req.body.producto_id;
            await lic.update(cambios, { transaction: t });
            return Licencia.findByPk(lic.id, { include, transaction: t });
        });
        res.json({ ok: true, data: conEstado(completa), msg: 'Licencia actualizada' });
    } catch (e) { res.status(e.status || 400).json({ ok: false, msg: e.message }); }
};

exports.toggle = async (req, res) => {
    const lic = await Licencia.findByPk(req.params.id);
    if (!lic) return res.status(404).json({ ok: false, msg: 'No encontrada' });
    await lic.update({ activo: !lic.activo });
    res.json({ ok: true, activo: !lic.activo, msg: lic.activo ? 'Licencia desactivada' : 'Licencia activada' });
};

// Renovar sin registrar plata (cortesía, ajuste). Respeta el día de corte.
exports.renovar = async (req, res) => {
    const { meses = 1 } = req.body;
    // Misma transaccion con bloqueo que un pago: si entra un pago al mismo tiempo,
    // los dos meses se suman en vez de pisarse.
    const r = await sequelize.transaction(async (t) => {
        const lic = await Licencia.findByPk(req.params.id, { include, transaction: t, lock: t.LOCK.UPDATE });
        if (!lic) return null;
        const nueva = siguienteVencimiento(lic, meses);
        await lic.update({ fecha_vencimiento: nueva, activo: true }, { transaction: t });
        return { lic, nueva };
    });
    if (!r) return res.status(404).json({ ok: false, msg: 'No encontrada' });
    const { lic, nueva } = r;
    if (lic.cliente?.email) {
        notificarRenovacion({
            clienteEmail: lic.cliente.email, clienteNombre: lic.cliente.nombre,
            productoNombre: lic.producto?.nombre || 'Sistema', nuevaFechaVencimiento: nueva
        });
    }
    res.json({ ok: true, msg: `Renovada por ${meses} mes(es)`, fecha_vencimiento: nueva });
};

// Registrar un pago recibido por fuera de MercadoPago (Nequi, transferencia...)
exports.registrarPago = async (req, res) => {
    try {
        const r = await registrarPagoLicencia(req.params.id, { ...req.body, origen: 'manual' });
        res.json({ ok: true, data: conEstado(r.licencia), pago: r.pago, msg: `Pago registrado. Nuevo vencimiento: ${r.fecha_vencimiento}` });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.eliminar = async (req, res) => {
    await Licencia.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Licencia eliminada' });
};

// Endpoint público — lo llaman los sistemas instalados en los clientes.
// Misma regla que /api/pagos/mp/validar (utils/licenciaCiclo).
exports.validar = async (req, res) => {
    const { license_key } = req.body;
    if (!license_key) return res.status(400).json({ valid: false, msg: 'Clave requerida' });

    const lic = await Licencia.findOne({ where: { license_key }, include });
    if (!lic) return res.status(404).json({ valid: false, msg: 'Licencia no encontrada' });

    await lic.update({ last_check: new Date() });
    const c = estadoLicencia(lic);
    if (c.estado === 'desactivada') return res.status(403).json({ valid: false, msg: 'Licencia desactivada. Contacte a su proveedor.' });
    if (!c.valida) return res.status(403).json({ valid: false, msg: 'Licencia vencida. Contacte a su proveedor.', fecha_vencimiento: c.fecha_vencimiento });

    res.json({ valid: true, client_name: lic.cliente.nombre, expires_at: c.fecha_vencimiento, dias_restantes: c.dias_restantes, estado: c.estado });
};
