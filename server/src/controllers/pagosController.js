const { Pago, Cliente, Licencia, Producto } = require('../models');
const { generarFactura } = require('./facturasController');
const { MercadoPagoConfig, Preference, Payment } = require('mercadopago');

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
    const lic2 = await Licencia.findByPk(licencia_id, { include: [{ model: Producto, as: 'producto', attributes: ['nombre'] }] });
    await generarFactura({
        pago_id:     pago.id,
        cliente_id:  pago.cliente_id,
        concepto:    `Renovación ${lic2?.producto?.nombre || 'Sistema'} — ${req.body.meses || 1} mes(es)`,
        monto:       pago.monto,
        metodo_pago: pago.metodo_pago,
        fecha:       pago.fecha_pago
    });

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
    if (type !== 'payment' || !data?.id) return;

    try {
        const paymentApi = new Payment(mpClient());
        const pago = await paymentApi.get({ id: data.id });
        if (pago.status !== 'approved') return;

        const licenseKey = pago.external_reference;
        const lic = await Licencia.findOne({ where: { license_key: licenseKey } });
        if (!lic) return;

        const base = new Date(lic.fecha_vencimiento) > new Date() ? new Date(lic.fecha_vencimiento) : new Date();
        base.setMonth(base.getMonth() + 1);
        await lic.update({ fecha_vencimiento: base.toISOString().split('T')[0], activo: true });

        const licConProd = await Licencia.findByPk(lic.id, { include: [{ model: Producto, as: 'producto', attributes: ['nombre'] }] });
        const nuevoPago = await Pago.create({
            licencia_id: lic.id,
            cliente_id:  lic.cliente_id,
            monto:       pago.transaction_amount,
            fecha_pago:  new Date().toISOString().split('T')[0],
            metodo_pago: 'otro',
            meses:       1,
            notas:       `MercadoPago #${pago.id}`
        });

        await generarFactura({
            pago_id:     nuevoPago.id,
            cliente_id:  lic.cliente_id,
            concepto:    `Renovación ${licConProd?.producto?.nombre || 'Sistema'} — 1 mes (MercadoPago)`,
            monto:       pago.transaction_amount,
            metodo_pago: 'MercadoPago',
            fecha:       new Date().toISOString().split('T')[0]
        });

        console.log(`✅ Pago MP aprobado — licencia ${licenseKey} renovada 1 mes`);
    } catch (err) {
        console.error('Error procesando webhook MP:', err.message);
    }
};
