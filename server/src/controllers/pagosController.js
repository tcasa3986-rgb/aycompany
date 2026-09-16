const { Pago, Cliente, Licencia, Producto } = require('../models');
const { generarFactura } = require('./facturasController');
const { MercadoPagoConfig, Preference, Payment, PreApproval } = require('mercadopago');
const { notificarRenovacion } = require('../services/licenciaNotificaciones');
const { crearFactura: crearFacturaSiigo } = require('../services/siigoService');
const { registrarPagoLicencia, PagoDuplicado } = require('../services/licenciaPagoService');
const { estadoLicencia, precioLicencia } = require('../utils/licenciaCiclo');

const include = [
    { model: Cliente,  as: 'cliente',  attributes: ['id', 'nombre'] },
    { model: Licencia, as: 'licencia', attributes: ['id', 'license_key'],
      include: [{ model: Producto, as: 'producto', attributes: ['nombre'] }] }
];

const mpClient = () => new MercadoPagoConfig({ accessToken: process.env.MP_ACCESS_TOKEN });

// ── CRUD manual ─────────────────────────────────────────────
exports.listar = async (req, res) => {
    const pagos = await Pago.findAll({ include, order: [['fecha_pago', 'DESC']] });
    res.json({ ok: true, data: pagos });
};

exports.crear = async (req, res) => {
    try {
        const { licencia_id } = req.body;
        if (!licencia_id) return res.status(400).json({ ok: false, msg: 'licencia_id es obligatorio' });
        const r = await registrarPagoLicencia(licencia_id, { ...req.body, origen: 'manual' });
        res.json({ ok: true, data: r.pago, msg: `Pago registrado y licencia renovada hasta ${r.fecha_vencimiento}` });
    } catch (e) { res.status(400).json({ ok: false, msg: e.message }); }
};

exports.eliminar = async (req, res) => {
    await Pago.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Pago eliminado' });
};

// ── Mercado Pago ─────────────────────────────────────────────

exports.mpInfoLicencia = async (req, res) => {
    try {
        const { license_key } = req.params;
        const lic = await Licencia.findOne({
            where: { license_key },
            include: [
                { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'email'] },
                { model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }
            ]
        });
        if (!lic) return res.status(404).json({ ok: false, msg: 'Licencia no encontrada' });

        const c = estadoLicencia(lic);
        res.json({
            ok: true,
            cliente:           lic.cliente.nombre,
            email:             lic.cliente.email || '',
            producto:          lic.producto.nombre,
            precio:            precioLicencia(lic),
            fecha_vencimiento: c.fecha_vencimiento,
            fecha_bloqueo:     c.fecha_bloqueo,
            dias_restantes:    c.dias_restantes,
            dias_mora:         c.dias_mora,
            estado:            c.estado,
            activo:            c.valida,
            suscripcion_activa: lic.suscripcion_activa
        });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.mpCrearPago = async (req, res) => {
    try {
        const { license_key } = req.params;
        const meses = Math.min(Math.max(parseInt(req.body?.meses) || 1, 1), 24);

        const lic = await Licencia.findOne({
            where: { license_key },
            include: [
                { model: Cliente,  as: 'cliente',  attributes: ['nombre'] },
                { model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }
            ]
        });
        if (!lic) return res.status(404).json({ ok: false, msg: 'Licencia no encontrada' });

        const baseUrl   = process.env.BASE_URL || `https://mi-plataforma-production.up.railway.app`;
        const descuentos = { 3: 5, 6: 10, 12: 15 };
        const pct        = descuentos[meses] || 0;
        const total      = Math.round(precioLicencia(lic) * meses * (1 - pct / 100));
        const titulo    = meses === 1
            ? `Renovación ${lic.producto.nombre} — ${lic.cliente.nombre}`
            : `Renovación ${lic.producto.nombre} ${meses} meses — ${lic.cliente.nombre}`;

        const preference = new Preference(mpClient());
        const result = await preference.create({
            body: {
                items: [{
                    title:       titulo,
                    quantity:    1,
                    unit_price:  total,
                    currency_id: 'COP'
                }],
                // Codificamos los meses en el external_reference: "license_key|meses"
                external_reference: `${license_key}|${meses}`,
                back_urls: {
                    success: `${baseUrl}/pagar/${license_key}?estado=ok&meses=${meses}`,
                    failure: `${baseUrl}/pagar/${license_key}?estado=error`,
                    pending: `${baseUrl}/pagar/${license_key}?estado=pendiente`
                },
                auto_return:      'approved',
                notification_url: `${baseUrl}/api/pagos/mp/webhook`
            }
        });

        res.json({ ok: true, init_point: result.init_point });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

// Trae el pago de una suscripcion. Para 'subscription_authorized_payment' el id
// que manda MP es de authorized_payments, no de /v1/payments: hay que pedirlo por
// su propio endpoint o el cobro recurrente se pierde en silencio.
async function obtenerPagoAutorizado(id) {
    const r = await fetch(`https://api.mercadopago.com/authorized_payments/${id}`, {
        headers: { Authorization: `Bearer ${process.env.MP_ACCESS_TOKEN}` }
    });
    if (!r.ok) throw new Error(`authorized_payments ${id}: HTTP ${r.status}`);
    const ap = await r.json();
    // El pago real viene anidado; se normaliza a la forma de /v1/payments.
    const pay = ap.payment || {};
    return {
        id:                 pay.id || ap.id,
        status:             pay.status || ap.status,
        transaction_amount: pay.transaction_amount ?? ap.transaction_amount,
        external_reference: ap.external_reference || pay.external_reference,
        preapproval_id:     ap.preapproval_id
    };
}

// MercadoPago reintenta mientras no reciba 2xx. Por eso NO se responde 200 antes
// de procesar: si la BD falla, un 500 hace que MP reenvie y el pago no se pierde.
exports.mpWebhook = async (req, res) => {
    const { type, data } = req.body;
    if (!data?.id) return res.sendStatus(200);

    try {
        // ── Pago único ────────────────────────────────────────────
        if (type === 'payment') {
            const paymentApi = new Payment(mpClient());
            const pago = await paymentApi.get({ id: data.id });
            if (pago.status !== 'approved') return res.sendStatus(200);

            // external_reference puede ser "license_key|meses" o solo "license_key" (pagos antiguos)
            const [licenseKey, mesesStr] = (pago.external_reference || '').split('|');
            const numMeses = Math.min(Math.max(parseInt(mesesStr) || 1, 1), 24);

            const lic = await Licencia.findOne({ where: { license_key: licenseKey } });
            if (!lic) { console.warn(`Webhook MP: licencia ${licenseKey} no existe`); return res.sendStatus(200); }

            // Idempotencia real: referencia_externa tiene UNIQUE, así que dos
            // entregas simultáneas del mismo webhook no duplican el pago.
            try {
                await registrarPagoLicencia(lic.id, {
                    meses: numMeses, monto: pago.transaction_amount, metodo_pago: 'mercadopago',
                    notas: `MP pago único #${pago.id}`, referencia_externa: `mp_pay_${pago.id}`,
                    origen: 'mp_unico'
                });
            } catch (e) {
                if (e instanceof PagoDuplicado || e.duplicado) { console.log(`Webhook repetido ignorado (pago ${pago.id})`); return res.sendStatus(200); }
                throw e;
            }
            console.log(`✅ Pago MP ${numMeses} mes(es) — licencia ${licenseKey} renovada`);
        }

        // ── Cobro automático de suscripción ───────────────────────
        if (type === 'subscription_authorized_payment') {
            const pago = await obtenerPagoAutorizado(data.id);
            if (pago.status !== 'approved') return res.sendStatus(200);

            // external_reference = license_key guardado al crear la suscripción
            const licenseKey = pago.external_reference;
            const lic = await Licencia.findOne({ where: { license_key: licenseKey } });
            if (!lic) { console.warn(`Webhook MP suscripcion: licencia ${licenseKey} no existe`); return res.sendStatus(200); }

            try {
                await registrarPagoLicencia(lic.id, {
                    meses: 1, monto: pago.transaction_amount, metodo_pago: 'mercadopago',
                    notas: `MP suscripción automática #${pago.id}`, referencia_externa: `mp_sub_${pago.id}`,
                    origen: 'mp_suscripcion'
                });
            } catch (e) {
                if (e instanceof PagoDuplicado || e.duplicado) { console.log(`Webhook de suscripcion repetido ignorado (pago ${pago.id})`); return res.sendStatus(200); }
                throw e;
            }
            console.log(`✅ Cobro automático MP — licencia ${licenseKey} renovada`);
        }

        // ── Cambio de estado de suscripción ──────────────────────
        if (type === 'subscription_preapproval') {
            const api = new PreApproval(mpClient());
            const sub = await api.get({ id: data.id });
            // si la cancelaron desde MP, marcar inactiva
            if (sub.status === 'cancelled' || sub.status === 'paused') {
                await Licencia.update({ suscripcion_activa: false }, { where: { mp_subscription_id: data.id } });
                console.log(`⚠️ Suscripción ${data.id} ${sub.status}`);
            }
        }
        return res.sendStatus(200);
    } catch (err) {
        // 500 = MercadoPago reintenta. Ademas se avisa, porque un webhook que
        // falla en silencio es un cliente que pago y no le renovaron.
        console.error('Error webhook MP:', err.message);
        require('../services/telegramService')
            .enviar(`*Webhook de MercadoPago fallo* (${type} #${data.id})\n${err.message}\nMP lo reintentara.`)
            .catch(() => {});
        return res.sendStatus(500);
    }
};

// ── Crear suscripción automática (Netflix-style) ─────────────
exports.mpCrearSuscripcion = async (req, res) => {
    try {
        const { license_key } = req.params;
        const lic = await Licencia.findOne({
            where: { license_key },
            include: [
                { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'email'] },
                { model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }
            ]
        });
        if (!lic) return res.status(404).json({ ok: false, msg: 'Licencia no encontrada' });

        // MercadoPago exige el correo de la cuenta MP del pagador (de Colombia).
        // Antes se mandaba un placeholder y MP respondía "Payer is associated with a different site".
        const email = String(req.body?.email || lic.cliente.email || '').trim().toLowerCase();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            return res.status(400).json({ ok: false, msg: 'Ingrese el correo de su cuenta de MercadoPago para activar el cobro automático.' });
        }
        if (!lic.cliente.email) await Cliente.update({ email }, { where: { id: lic.cliente_id } });

        const baseUrl = process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app';
        const api = new PreApproval(mpClient());

        const result = await api.create({
            body: {
                reason:          `${lic.producto.nombre} — ${lic.cliente.nombre}`,
                external_reference: license_key,
                payer_email:     email,
                auto_recurring: {
                    frequency:           1,
                    frequency_type:      'months',
                    transaction_amount:  precioLicencia(lic),
                    currency_id:         'COP',
                    start_date:          new Date().toISOString(),
                    end_date:            new Date(Date.now() + 10 * 365 * 24 * 3600 * 1000).toISOString() // 10 años
                },
                back_url: `${baseUrl}/pagar/${license_key}?estado=suscrito`,
                notification_url: `${baseUrl}/api/pagos/mp/webhook`
            }
        });

        // guardar el ID de suscripción en la licencia
        await lic.update({ mp_subscription_id: result.id, suscripcion_activa: result.status === 'authorized' });

        res.json({ ok: true, init_point: result.init_point, subscription_id: result.id });
    } catch (err) {
        const msg = /different site/i.test(err.message)
            ? 'Ese correo no corresponde a una cuenta de MercadoPago Colombia. Verifique el correo o use "Pagar una vez".'
            : err.message;
        res.status(500).json({ ok: false, msg });
    }
};

// ── Cancelar suscripción ──────────────────────────────────────
exports.mpCancelarSuscripcion = async (req, res) => {
    try {
        const { license_key } = req.params;
        const lic = await Licencia.findOne({ where: { license_key } });
        if (!lic || !lic.mp_subscription_id) return res.status(404).json({ ok: false, msg: 'Sin suscripción activa' });

        const api = new PreApproval(mpClient());
        await api.update({ id: lic.mp_subscription_id, body: { status: 'cancelled' } });
        await lic.update({ suscripcion_activa: false });

        res.json({ ok: true, msg: 'Suscripción cancelada' });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

// ── Validar licencia (llamado por sistemas externos como ASOERC) ──
exports.validarLicencia = async (req, res) => {
    try {
        const { license_key } = req.params;
        const lic = await Licencia.findOne({
            where: { license_key },
            include: [{ model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }]
        });
        if (!lic) return res.status(404).json({ ok: false, msg: 'Licencia no encontrada' });

        await lic.update({ last_check: new Date() });
        const c = estadoLicencia(lic);

        res.json({
            ok:      c.valida,
            activo:  c.valida,
            estado:  c.estado,
            producto: lic.producto?.nombre,
            fecha_vencimiento: c.fecha_vencimiento,
            fecha_bloqueo:     c.fecha_bloqueo,
            dias_restantes:    c.dias_restantes,
            dias_mora:         c.dias_mora,
            suscripcion_activa: lic.suscripcion_activa,
            pago_url: `${process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app'}/pagar/${license_key}`
        });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};
