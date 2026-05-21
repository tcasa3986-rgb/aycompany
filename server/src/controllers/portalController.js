const { Cliente, Licencia, Producto, Factura, Pago, Ticket } = require('../models');
const { generarPDFFactura } = require('../services/pdfFactura');

const BASE_URL = () => process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app';

async function clientePorToken(token) {
    return Cliente.findOne({ where: { token_portal: token } });
}

// GET /api/portal/:token
exports.obtener = async (req, res) => {
    try {
        const cliente = await clientePorToken(req.params.token);
        if (!cliente) return res.status(404).json({ ok: false, msg: 'Portal no encontrado' });

        const licencias = await Licencia.findAll({
            where: { cliente_id: cliente.id },
            include: [{ model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }],
            order: [['id', 'DESC']]
        });

        const ahora = new Date();
        const licsConEstado = licencias.map(l => {
            const vence = new Date(l.fecha_vencimiento + 'T23:59:59');
            const dias  = Math.ceil((vence - ahora) / 86400000);
            return {
                id:                l.id,
                license_key:       l.license_key,
                producto:          l.producto?.nombre,
                precio:            l.producto?.precio_mensual,
                fecha_vencimiento: l.fecha_vencimiento,
                activo:            l.activo,
                valida:            l.activo && vence >= ahora,
                dias_restantes:    dias,
                suscripcion_activa: l.suscripcion_activa,
                pago_url:          `${BASE_URL()}/pagar/${l.license_key}`
            };
        });

        res.json({
            ok: true,
            cliente: {
                nombre:   cliente.nombre,
                email:    cliente.email,
                telefono: cliente.telefono,
                empresa:  cliente.empresa
            },
            licencias: licsConEstado
        });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

// GET /api/portal/:token/facturas
exports.facturas = async (req, res) => {
    try {
        const cliente = await clientePorToken(req.params.token);
        if (!cliente) return res.status(404).json({ ok: false, msg: 'Portal no encontrado' });

        const facturas = await Factura.findAll({
            where: { cliente_id: cliente.id },
            include: [{ model: Pago, as: 'pago', attributes: ['metodo_pago'] }],
            order: [['id', 'DESC']]
        });

        res.json({ ok: true, data: facturas });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

// GET /api/portal/:token/facturas/:id/pdf
exports.facturaPDF = async (req, res) => {
    try {
        const cliente = await clientePorToken(req.params.token);
        if (!cliente) return res.status(404).json({ ok: false, msg: 'Portal no encontrado' });

        const factura = await Factura.findOne({
            where: { id: req.params.id, cliente_id: cliente.id }
        });
        if (!factura) return res.status(404).json({ ok: false, msg: 'Factura no encontrada' });

        const pdf = await generarPDFFactura({
            numero:          factura.numero,
            fecha:           factura.fecha,
            concepto:        factura.concepto,
            monto:           factura.monto,
            metodo_pago:     factura.metodo_pago,
            clienteNombre:   cliente.nombre,
            clienteEmail:    cliente.email,
            clienteTelefono: cliente.telefono
        });

        res.setHeader('Content-Type', 'application/pdf');
        res.setHeader('Content-Disposition', `attachment; filename="${factura.numero}.pdf"`);
        res.send(pdf);
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

// PUT /api/portal/:token/datos
exports.actualizarDatos = async (req, res) => {
    try {
        const cliente = await clientePorToken(req.params.token);
        if (!cliente) return res.status(404).json({ ok: false, msg: 'Portal no encontrado' });

        const { nombre, email, telefono, empresa } = req.body;
        const updates = {};
        if (nombre)   updates.nombre   = nombre;
        if (email)    updates.email    = email;
        if (telefono) updates.telefono = telefono;
        if (empresa)  updates.empresa  = empresa;

        await cliente.update(updates);
        res.json({ ok: true, msg: 'Datos actualizados correctamente' });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

// GET /api/portal/:token/tickets
exports.ticketsCliente = async (req, res) => {
    try {
        const cliente = await clientePorToken(req.params.token);
        if (!cliente) return res.status(404).json({ ok: false, msg: 'Portal no encontrado' });

        const tickets = await Ticket.findAll({
            where: { cliente_id: cliente.id },
            order: [['created_at', 'DESC']]
        });
        res.json({ ok: true, data: tickets });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

// POST /api/portal/:token/tickets
exports.crearTicket = async (req, res) => {
    try {
        const cliente = await clientePorToken(req.params.token);
        if (!cliente) return res.status(404).json({ ok: false, msg: 'Portal no encontrado' });

        const { asunto, mensaje } = req.body;
        if (!asunto || !mensaje) return res.status(400).json({ ok: false, msg: 'Asunto y mensaje son requeridos' });

        const ticket = await Ticket.create({ cliente_id: cliente.id, asunto, mensaje });
        res.status(201).json({ ok: true, msg: 'Ticket creado', data: ticket });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};