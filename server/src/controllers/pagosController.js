const { Pago, Cliente, Licencia, Producto } = require('../models');
const { generarFactura } = require('./facturasController');
const { MercadoPagoConfig, Preference, Payment, PreApproval } = require('mercadopago');
const { notificarRenovacion } = require('../services/licenciaNotificaciones');

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
    const pago = await Pago.create(req.body);
    const { licencia_id, meses = 1 } = req.body;
    const lic = await Licencia.findByPk(licencia_id);
    if (lic) {
        const base = new Date(lic.fecha_vencimiento) > new Date() ? new Date(lic.fecha_vencimiento) : new Date();
        base.setMonth(base.getMonth() + parseInt(meses));
        await lic.update({ fecha_vencimiento: base.toISOString().split('T')[0], activo: true });
    }
    const lic2 = await Licencia.findByPk(licencia_id, {
        include: [
            { model: Producto, as: 'producto', attributes: ['nombre'] },
            { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'email'] }
        ]
    });
    await generarFactura({
        pago_id:     pago.id,
        cliente_id:  pago.cliente_id,
        concepto:    `Renovación ${lic2?.producto?.nombre || 'Sistema'} — ${req.body.meses || 1} mes(es)`,
        monto:       pago.monto,
        metodo_pago: pago.metodo_pago,
        fecha:       pago.fecha_pago
    });
    if (lic2?.cliente?.email) {
        notificarRenovacion({
            clienteEmail:         lic2.cliente.email,
            clienteNombre:        lic2.cliente.nombre,
            productoNombre:       lic2.producto?.nombre || 'Sistema',
            nuevaFechaVencimiento: lic?.fecha_vencimiento,
            monto:                pago.monto
        });
    }

    res.json({ ok: true, data: pago, msg: 'Pago registrado y licencia renovada' });
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
                { model: Cliente,  as: 'cliente',  attributes: ['nombre'] },
                { model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }
            ]
        });
        if (!lic) return res.status(404).json({ ok: false, msg: 'Licencia no encontrada' });

        const ahora = new Date();
        const vence = new Date(lic.fecha_vencimiento);
        const dias  = Math.ceil((vence - ahora) / 86400000);

        res.json({
            ok: true,
            cliente:           lic.cliente.nombre,
            producto:          lic.producto.nombre,
            precio:            lic.producto.precio_mensual,
            fecha_vencimiento: lic.fecha_vencimiento,
            dias_restantes:    dias,
            activo:            lic.activo
        });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.mpCrearPago = async (req, res) => {
    try {
        const { license_key } = req.params;
        const lic = await Licencia.findOne({
            where: { license_key },
            include: [
                { model: Cliente,  as: 'cliente',  attributes: ['nombre'] },
                { model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }
            ]
        });
        if (!lic) return res.status(404).json({ ok: false, msg: 'Licencia no encontrada' });

        const baseUrl = process.env.BASE_URL || `https://mi-plataforma-production.up.railway.app`;
        const preference = new Preference(mpClient());
        const result = await preference.create({
            body: {
                items: [{
                    title:      `Renovación ${lic.producto.nombre} — ${lic.cliente.nombre}`,
                    quantity:   1,
                    unit_price: Number(lic.producto.precio_mensual),
                    currency_id: 'COP'
                }],
                external_reference: license_key,
                back_urls: {
                    success: `${baseUrl}/pagar/${license_key}?estado=ok`,
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

exports.mpWebhook = async (req, res) => {
    res.sendStatus(200);
    const { type, data } = req.body;
    if (!data?.id) return;

    try {
        // ── Pago único ────────────────────────────────────────────
        if (type === 'payment') {
            const paymentApi = new Payment(mpClient());
            const pago = await paymentApi.get({ id: data.id });
            if (pago.status !== 'approved') return;

            const licenseKey = pago.external_reference;
            const lic = await Licencia.findOne({ where: { license_key: licenseKey } });
            if (!lic) return;

            const base = new Date(lic.fecha_vencimiento) > new Date() ? new Date(lic.fecha_vencimiento) : new Date();
            base.setMonth(base.getMonth() + 1);
            await lic.update({ fecha_vencimiento: base.toISOString().split('T')[0], activo: true });

            const licConProd = await Licencia.findByPk(lic.id, {
                include: [
                    { model: Producto, as: 'producto', attributes: ['nombre'] },
                    { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'email'] }
                ]
            });
            const nuevoPago = await Pago.create({
                licencia_id: lic.id, cliente_id: lic.cliente_id,
                monto: pago.transaction_amount, fecha_pago: new Date().toISOString().split('T')[0],
                metodo_pago: 'MercadoPago', meses: 1, notas: `MP pago único #${pago.id}`
            });
            await generarFactura({
                pago_id: nuevoPago.id, cliente_id: lic.cliente_id,
                concepto: `Renovación ${licConProd?.producto?.nombre || 'Sistema'} — 1 mes`,
                monto: pago.transaction_amount, metodo_pago: 'MercadoPago',
                fecha: new Date().toISOString().split('T')[0]
            });
            if (licConProd?.cliente?.email) {
                notificarRenovacion({
                    clienteEmail:          licConProd.cliente.email,
                    clienteNombre:         licConProd.cliente.nombre,
                    productoNombre:        licConProd.producto?.nombre || 'Sistema',
                    nuevaFechaVencimiento: lic.fecha_vencimiento,
                    monto:                 pago.transaction_amount
                });
            }
            console.log(`✅ Pago único MP — licencia ${licenseKey} renovada`);
        }

        // ── Cobro automático de suscripción ───────────────────────
        if (type === 'subscription_authorized_payment') {
            const paymentApi = new Payment(mpClient());
            const pago = await paymentApi.get({ id: data.id });
            if (pago.status !== 'approved') return;

            // external_reference = license_key guardado al crear la suscripción
            const licenseKey = pago.external_reference;
            const lic = await Licencia.findOne({ where: { license_key: licenseKey } });
            if (!lic) return;

            const base = new Date(lic.fecha_vencimiento) > new Date() ? new Date(lic.fecha_vencimiento) : new Date();
            base.setMonth(base.getMonth() + 1);
            await lic.update({ fecha_vencimiento: base.toISOString().split('T')[0], activo: true, suscripcion_activa: true });

            const licConProd = await Licencia.findByPk(lic.id, {
                include: [
                    { model: Producto, as: 'producto', attributes: ['nombre'] },
                    { model: Cliente,  as: 'cliente',  attributes: ['nombre', 'email'] }
                ]
            });
            const nuevoPago = await Pago.create({
                licencia_id: lic.id, cliente_id: lic.cliente_id,
                monto: pago.transaction_amount, fecha_pago: new Date().toISOString().split('T')[0],
                metodo_pago: 'MercadoPago', meses: 1, notas: `MP suscripción automática #${pago.id}`
            });
            await generarFactura({
                pago_id: nuevoPago.id, cliente_id: lic.cliente_id,
                concepto: `Suscripción ${licConProd?.producto?.nombre || 'Sistema'} — cobro automático`,
                monto: pago.transaction_amount, metodo_pago: 'MercadoPago',
                fecha: new Date().toISOString().split('T')[0]
            });
            if (licConProd?.cliente?.email) {
                notificarRenovacion({
                    clienteEmail:          licConProd.cliente.email,
                    clienteNombre:         licConProd.cliente.nombre,
                    productoNombre:        licConProd.producto?.nombre || 'Sistema',
                    nuevaFechaVencimiento: lic.fecha_vencimiento,
                    monto:                 pago.transaction_amount
                });
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
    } catch (err) {
        console.error('Error webhook MP:', err.message);
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

        const baseUrl = process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app';
        const api = new PreApproval(mpClient());

        const result = await api.create({
            body: {
                reason:          `${lic.producto.nombre} — ${lic.cliente.nombre}`,
                external_reference: license_key,
                payer_email:     lic.cliente.email || 'cliente@email.com',
                auto_recurring: {
                    frequency:           1,
                    frequency_type:      'months',
                    transaction_amount:  Number(lic.producto.precio_mensual),
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
        res.status(500).json({ ok: false, msg: err.message });
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
            include: [{ model: Producto, as: 'producto', attributes: ['nombre'] }]
        });
        if (!lic) return res.status(404).json({ ok: false, msg: 'Licencia no encontrada' });

        const ahora = new Date();
        const vence = new Date(lic.fecha_vencimiento + 'T23:59:59');
        const valida = lic.activo && vence >= ahora;
        const dias   = Math.ceil((vence - ahora) / 86400000);

        await lic.update({ last_check: ahora });

        res.json({
            ok:      valida,
            activo:  valida,
            producto: lic.producto?.nombre,
            fecha_vencimiento: lic.fecha_vencimiento,
            dias_restantes: dias,
            suscripcion_activa: lic.suscripcion_activa,
            pago_url: `${process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app'}/pagar/${license_key}`
        });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};
