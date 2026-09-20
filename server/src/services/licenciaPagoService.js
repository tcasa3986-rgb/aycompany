// Único camino para "entró un pago de una licencia": renueva según el día de
// corte, registra el Pago, genera la Factura y avisa al cliente y a Cristian.
// Lo usan el pago manual (admin), el webhook de MercadoPago (único y suscripción)
// y el endpoint antiguo /api/pagos.
//
// La renovación + el registro del pago van en UNA transacción con la fila de la
// licencia bloqueada (SELECT ... FOR UPDATE). Sin eso, dos pagos simultáneos leen
// el mismo vencimiento y solo se acredita un mes: el cliente pagaría dos y quedaría
// bloqueado con uno. La factura y las notificaciones van después de confirmar.
const sequelize = require('../config/db');
const { Licencia, Cliente, Producto, Pago, MovimientoFinanciero } = require('../models');
const { generarFactura } = require('../controllers/facturasController');
const { notificarRenovacion } = require('./licenciaNotificaciones');
const { crearFactura: crearFacturaSiigo } = require('./siigoService');
const telegram = require('./telegramService');
const { siguienteVencimiento, precioLicencia, hoyBogota } = require('../utils/licenciaCiclo');

const include = [
    { model: Cliente,  as: 'cliente',  attributes: ['id', 'nombre', 'email', 'telefono'] },
    { model: Producto, as: 'producto', attributes: ['id', 'nombre', 'precio_mensual'] }
];

const METODOS = ['efectivo', 'transferencia', 'tarjeta', 'nequi', 'mercadopago', 'otro'];

// Error tipificado para que el webhook distinga "ya estaba registrado" de un fallo real.
class PagoDuplicado extends Error {
    constructor(ref) { super(`Pago ya registrado (${ref})`); this.name = 'PagoDuplicado'; this.duplicado = true; }
}

/**
 * @param {number} licenciaId
 * @param {object} p  { meses, monto, metodo_pago, fecha_pago, notas, referencia_externa, origen }
 *   origen: 'manual' | 'mp_unico' | 'mp_suscripcion'
 *   referencia_externa: id único del pago en MercadoPago. Tiene índice UNIQUE:
 *     es lo que hace idempotente el webhook incluso con dos entregas simultáneas.
 */
async function registrarPagoLicencia(licenciaId, p = {}) {
    const meses  = Math.min(Math.max(parseInt(p.meses) || 1, 1), 24);
    const metodo = METODOS.includes(String(p.metodo_pago || '').toLowerCase()) ? String(p.metodo_pago).toLowerCase() : 'otro';
    const fecha  = p.fecha_pago || hoyBogota();
    const ref    = p.referencia_externa || null;

    const { lic, pago, nuevaFecha, monto } = await sequelize.transaction(async (t) => {
        // Bloquea la fila hasta el commit: cualquier otro pago de esta licencia espera.
        const lic = await Licencia.findByPk(licenciaId, { include, transaction: t, lock: t.LOCK.UPDATE });
        if (!lic) throw new Error('Licencia no encontrada');

        if (ref) {
            const ya = await Pago.findOne({ where: { referencia_externa: ref }, transaction: t });
            if (ya) throw new PagoDuplicado(ref);
        }

        const monto = p.monto != null && p.monto !== '' ? Number(p.monto) : precioLicencia(lic) * meses;
        const nuevaFecha = siguienteVencimiento(lic, meses);

        const cambios = { fecha_vencimiento: nuevaFecha, activo: true };
        if (p.origen === 'mp_suscripcion') cambios.suscripcion_activa = true;
        await lic.update(cambios, { transaction: t });

        let pago;
        try {
            pago = await Pago.create({
                licencia_id: lic.id, cliente_id: lic.cliente_id,
                monto, fecha_pago: fecha, metodo_pago: metodo, meses,
                notas: p.notas || null, referencia_externa: ref
            }, { transaction: t });
        } catch (e) {
            // El UNIQUE de referencia_externa atrapa la carrera que findOne no ve.
            if (e.name === 'SequelizeUniqueConstraintError') throw new PagoDuplicado(ref);
            throw e;
        }

        // El dinero tiene que aparecer en Finanzas. Sin esto el cliente pagaba,
        // la licencia se renovaba y se emitía la factura, pero el tablero seguía
        // mostrando cero ingresos: la plata entraba y no se veía por ninguna parte.
        await MovimientoFinanciero.create({
            tipo: 'ingreso', ambito: 'empresa', categoria: 'mensualidad',
            concepto: `${lic.producto?.nombre || 'Sistema'} — ${lic.cliente?.nombre || 'Cliente'}`,
            monto, fecha, metodo_pago: metodo,
            origen: 'licencia', referencia_id: lic.id, cliente_id: lic.cliente_id,
            notas: p.notas || null
        }, { transaction: t });

        return { lic, pago, nuevaFecha, monto };
    });

    // ── Fuera de la transacción: nada de esto debe revertir un pago ya confirmado ──
    const plural = meses > 1 ? 'es' : '';
    const concepto = `Renovación ${lic.producto?.nombre || 'Sistema'} — ${meses} mes${plural}`;
    try {
        await generarFactura({ pago_id: pago.id, cliente_id: lic.cliente_id, concepto, monto, metodo_pago: metodo, fecha });
    } catch (e) {
        console.error(`⚠️  Pago ${pago.id} quedó SIN FACTURA:`, e.message);
        telegram.enviar(`⚠️ Pago #${pago.id} de ${lic.cliente?.nombre} se registró pero la factura falló: ${e.message}`).catch(() => {});
    }

    if (lic.cliente?.email) {
        notificarRenovacion({
            clienteEmail: lic.cliente.email, clienteNombre: lic.cliente.nombre,
            productoNombre: lic.producto?.nombre || 'Sistema',
            nuevaFechaVencimiento: nuevaFecha, monto
        });
    }
    if (p.origen && p.origen !== 'manual') {
        crearFacturaSiigo({
            clienteNombre: lic.cliente?.nombre, clienteEmail: lic.cliente?.email,
            concepto, monto, fecha
        }).catch(e => console.error('SIIGO sync error:', e.message));
    }

    const vias = { manual: 'registrado a mano', mp_unico: 'MercadoPago (pago único)', mp_suscripcion: 'MercadoPago (cobro automático)' };
    const via = vias[p.origen] || 'registrado';
    telegram.enviar(
        `💵 *Pago recibido* — ${lic.cliente?.nombre}\n` +
        `${lic.producto?.nombre || 'Sistema'} · $${monto.toLocaleString('es-CO')} · ${meses} mes${plural}\n` +
        `Vía: ${via}\nNuevo vencimiento: *${nuevaFecha}*`
    ).catch(() => {});

    return { licencia: await Licencia.findByPk(lic.id, { include }), pago, fecha_vencimiento: nuevaFecha };
}

module.exports = { registrarPagoLicencia, METODOS, PagoDuplicado };
