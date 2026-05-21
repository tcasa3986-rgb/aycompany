const { Contrato, Cliente } = require('../models');
const { generarPDFFactura } = require('../services/pdfFactura');

const includeCliente = [{ model: Cliente, as: 'cliente', attributes: ['id','nombre','email','telefono','empresa'] }];

exports.listar = async (req, res) => {
    try {
        const where = {};
        if (req.query.estado)     where.estado     = req.query.estado;
        if (req.query.cliente_id) where.cliente_id = req.query.cliente_id;
        const contratos = await Contrato.findAll({ where, include: includeCliente, order: [['created_at','DESC']] });
        res.json({ ok: true, data: contratos });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.obtener = async (req, res) => {
    try {
        const c = await Contrato.findByPk(req.params.id, { include: includeCliente });
        if (!c) return res.status(404).json({ ok: false, msg: 'Contrato no encontrado' });
        res.json({ ok: true, data: c });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.crear = async (req, res) => {
    try {
        const c = await Contrato.create(req.body);
        res.status(201).json({ ok: true, msg: 'Contrato creado', data: c });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const c = await Contrato.findByPk(req.params.id);
        if (!c) return res.status(404).json({ ok: false, msg: 'Contrato no encontrado' });
        await c.update(req.body);
        res.json({ ok: true, msg: 'Contrato actualizado' });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.eliminar = async (req, res) => {
    try {
        const c = await Contrato.findByPk(req.params.id);
        if (!c) return res.status(404).json({ ok: false, msg: 'Contrato no encontrado' });
        await c.destroy();
        res.json({ ok: true, msg: 'Contrato eliminado' });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.descargarPDF = async (req, res) => {
    try {
        const contrato = await Contrato.findByPk(req.params.id, { include: includeCliente });
        if (!contrato) return res.status(404).json({ ok: false, msg: 'No encontrado' });

        const clausulas = (() => { try { return JSON.parse(contrato.clausulas || '[]'); } catch { return []; } })();
        const PDFDocument = require('pdfkit');
        const doc = new PDFDocument({ margin: 50, size: 'A4' });

        const chunks = [];
        doc.on('data', c => chunks.push(c));
        await new Promise(resolve => doc.on('end', resolve));

        // Encabezado
        doc.fontSize(20).fillColor('#1e1b4b').text(process.env.NOMBRE_EMPRESA || 'AI Company CO', { align: 'center' });
        doc.moveDown(0.5);
        doc.fontSize(16).fillColor('#6366f1').text('CONTRATO DE SERVICIOS', { align: 'center' });
        doc.moveDown(0.5);
        doc.fontSize(12).fillColor('#374151').text(contrato.titulo, { align: 'center' });
        doc.moveDown(1.5);

        doc.moveTo(50,doc.y).lineTo(545,doc.y).strokeColor('#e2e8f0').stroke();
        doc.moveDown(1);

        // Partes
        doc.fontSize(11).fillColor('#1e1b4b').text('PARTES DEL CONTRATO', { underline: true });
        doc.moveDown(0.5);
        doc.fillColor('#374151')
           .text(`Proveedor: ${process.env.NOMBRE_EMPRESA || 'AI Company CO'} — NIT: ${process.env.NIT || ''}`)
           .text(`Cliente: ${contrato.cliente?.nombre || ''} — ${contrato.cliente?.empresa || ''}`)
           .text(`Email: ${contrato.cliente?.email || ''} — Tel: ${contrato.cliente?.telefono || ''}`);
        doc.moveDown(1);

        // Vigencia y valor
        if (contrato.fecha_inicio || contrato.monto) {
            doc.fontSize(11).fillColor('#1e1b4b').text('VIGENCIA Y VALOR', { underline: true });
            doc.moveDown(0.5);
            doc.fillColor('#374151');
            if (contrato.fecha_inicio) doc.text(`Inicio: ${new Date(contrato.fecha_inicio+'T00:00:00').toLocaleDateString('es-CO',{day:'numeric',month:'long',year:'numeric'})}`);
            if (contrato.fecha_fin)    doc.text(`Fin: ${new Date(contrato.fecha_fin+'T00:00:00').toLocaleDateString('es-CO',{day:'numeric',month:'long',year:'numeric'})}`);
            if (contrato.monto)        doc.text(`Valor: $${Number(contrato.monto).toLocaleString('es-CO')} ${contrato.moneda}`);
            doc.moveDown(1);
        }

        // Descripción
        if (contrato.descripcion) {
            doc.fontSize(11).fillColor('#1e1b4b').text('OBJETO DEL CONTRATO', { underline: true });
            doc.moveDown(0.5);
            doc.fontSize(10).fillColor('#374151').text(contrato.descripcion, { lineGap: 4 });
            doc.moveDown(1);
        }

        // Cláusulas
        if (clausulas.length > 0) {
            doc.fontSize(11).fillColor('#1e1b4b').text('CLÁUSULAS', { underline: true });
            doc.moveDown(0.5);
            clausulas.forEach((c, i) => {
                doc.fontSize(10).fillColor('#1e1b4b').text(`${i+1}. ${c.titulo || `Cláusula ${i+1}`}`, { continued: false });
                if (c.texto) doc.fillColor('#374151').text(c.texto, { lineGap: 3 });
                doc.moveDown(0.5);
            });
        }

        // Firmas
        doc.moveDown(2);
        doc.moveTo(50,doc.y).lineTo(545,doc.y).strokeColor('#e2e8f0').stroke();
        doc.moveDown(1);
        doc.fontSize(10).fillColor('#94a3b8')
           .text(`Fecha de emisión: ${new Date().toLocaleDateString('es-CO',{day:'numeric',month:'long',year:'numeric'})}`, { align: 'center' });
        doc.moveDown(2);

        const firmaY = doc.y;
        doc.moveTo(80, firmaY).lineTo(230, firmaY).stroke('#374151');
        doc.moveTo(315, firmaY).lineTo(465, firmaY).stroke('#374151');
        doc.fontSize(9).fillColor('#374151')
           .text(process.env.NOMBRE_EMPRESA || 'AI Company CO', 80, firmaY+5, { width: 150, align: 'center' })
           .text(contrato.cliente?.nombre || 'Cliente', 315, firmaY+5, { width: 150, align: 'center' });

        doc.end();

        res.setHeader('Content-Type','application/pdf');
        res.setHeader('Content-Disposition',`attachment; filename="contrato-${contrato.id}.pdf"`);
        res.send(Buffer.concat(chunks));
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};
