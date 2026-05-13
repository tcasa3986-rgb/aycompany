const { Op } = require('sequelize');
const XLSX = require('xlsx');
const nodemailer = require('nodemailer');
const { Factura, Cliente, Pago } = require('../models');
const { generarPDFFactura } = require('../services/pdfFactura');

const include = [
    { model: Cliente, as: 'cliente', attributes: ['id', 'nombre', 'email', 'telefono'] },
    { model: Pago,    as: 'pago',    attributes: ['id', 'metodo_pago', 'meses', 'notas'] }
];

async function generarNumero() {
    const ultima = await Factura.findOne({ order: [['id', 'DESC']] });
    const num = ultima ? parseInt(ultima.numero.replace('FAC-', '')) + 1 : 1;
    return `FAC-${String(num).padStart(4, '0')}`;
}

async function enviarFacturaEmail(factura, cliente) {
    const gmailUser = process.env.GMAIL_USER;
    const gmailPass = process.env.GMAIL_APP_PASSWORD;
    if (!gmailUser || !gmailPass || !cliente?.email) return;

    const pdf     = await generarPDFFactura({
        numero:          factura.numero,
        fecha:           factura.fecha,
        concepto:        factura.concepto,
        monto:           factura.monto,
        metodo_pago:     factura.metodo_pago,
        clienteNombre:   cliente.nombre,
        clienteEmail:    cliente.email,
        clienteTelefono: cliente.telefono
    });

    const empresa = process.env.NOMBRE_EMPRESA || 'AI Company CO';
    const t = nodemailer.createTransport({ service: 'gmail', auth: { user: gmailUser, pass: gmailPass } });
    await t.sendMail({
        from:    `"${empresa}" <${gmailUser}>`,
        to:      cliente.email,
        subject: `🧾 Factura ${factura.numero} — ${empresa}`,
        text:    `Hola ${cliente.nombre},\n\nAdjunto su factura ${factura.numero} por $${Number(factura.monto).toLocaleString('es-CO')} COP.\n\nConcepto: ${factura.concepto}\n\nGracias por su pago.\n${empresa}`,
        attachments: [{ filename: `${factura.numero}.pdf`, content: pdf, contentType: 'application/pdf' }]
    });
    console.log(`📧 Factura ${factura.numero} enviada a ${cliente.email}`);
}

exports.generarFactura = async ({ pago_id, cliente_id, concepto, monto, metodo_pago, fecha }) => {
    const numero  = await generarNumero();
    const factura = await Factura.create({ numero, pago_id, cliente_id, concepto, monto, metodo_pago, fecha });

    // Enviar PDF por email en background (no bloquea la respuesta)
    Cliente.findByPk(cliente_id, { attributes: ['nombre', 'email', 'telefono'] })
        .then(cliente => enviarFacturaEmail(factura, cliente))
        .catch(err => console.error('Error enviando factura:', err.message));

    return factura;
};

exports.listar = async (req, res) => {
    const where = {};
    const { cliente_id, mes, anio, metodo } = req.query;

    if (cliente_id) where.cliente_id = cliente_id;
    if (metodo)     where.metodo_pago = metodo;
    if (mes && anio) {
        const inicio = `${anio}-${String(mes).padStart(2, '0')}-01`;
        const fin    = new Date(parseInt(anio), parseInt(mes), 0).toISOString().split('T')[0];
        where.fecha  = { [Op.between]: [inicio, fin] };
    }

    const facturas = await Factura.findAll({ where, include, order: [['id', 'DESC']] });
    res.json({ ok: true, data: facturas });
};

exports.descargarPDF = async (req, res) => {
    try {
        const factura = await Factura.findByPk(req.params.id, { include });
        if (!factura) return res.status(404).json({ ok: false, msg: 'Factura no encontrada' });

        const pdf = await generarPDFFactura({
            numero:          factura.numero,
            fecha:           factura.fecha,
            concepto:        factura.concepto,
            monto:           factura.monto,
            metodo_pago:     factura.metodo_pago,
            clienteNombre:   factura.cliente?.nombre,
            clienteEmail:    factura.cliente?.email,
            clienteTelefono: factura.cliente?.telefono
        });

        res.setHeader('Content-Type', 'application/pdf');
        res.setHeader('Content-Disposition', `attachment; filename="${factura.numero}.pdf"`);
        res.send(pdf);
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.enviarPorEmail = async (req, res) => {
    try {
        const gmailUser = process.env.GMAIL_USER;
        const gmailPass = process.env.GMAIL_APP_PASSWORD;
        if (!gmailUser || !gmailPass) return res.status(400).json({ ok: false, msg: 'Gmail no configurado en Railway' });

        const factura = await Factura.findByPk(req.params.id, { include });
        if (!factura) return res.status(404).json({ ok: false, msg: 'Factura no encontrada' });

        const emailDestino = req.body?.email || factura.cliente?.email;
        if (!emailDestino) return res.status(400).json({ ok: false, msg: 'El cliente no tiene email registrado' });

        await enviarFacturaEmail(factura, { ...factura.cliente?.dataValues, email: emailDestino });
        res.json({ ok: true, msg: `Factura enviada a ${emailDestino}` });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.exportarExcel = async (req, res) => {
    try {
        const { cliente_id, mes, anio, metodo } = req.query;
        const where = {};
        if (cliente_id) where.cliente_id = cliente_id;
        if (metodo)     where.metodo_pago = metodo;
        if (mes && anio) {
            const inicio = `${anio}-${String(mes).padStart(2, '0')}-01`;
            const fin    = new Date(parseInt(anio), parseInt(mes), 0).toISOString().split('T')[0];
            where.fecha  = { [Op.between]: [inicio, fin] };
        }

        const facturas = await Factura.findAll({ where, include, order: [['id', 'DESC']] });
        const rows = facturas.map(f => ({
            'N° Factura':  f.numero,
            'Fecha':       f.fecha,
            'Cliente':     f.cliente?.nombre || '',
            'Concepto':    f.concepto,
            'Método':      f.metodo_pago,
            'Monto (COP)': Number(f.monto)
        }));

        const wb  = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(rows), 'Facturas');
        const buf = XLSX.write(wb, { type: 'buffer', bookType: 'xlsx' });

        res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        res.setHeader('Content-Disposition', 'attachment; filename="facturas.xlsx"');
        res.send(buf);
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.eliminar = async (req, res) => {
    await Factura.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Factura eliminada' });
};
