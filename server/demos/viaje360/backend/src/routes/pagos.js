const express = require('express');
const router = express.Router();
const { autenticar } = require('../middlewares/auth');
const { Pago, Reserva, MetodoPago, Cliente, Paquete } = require('../models');
const PDFDocument = require('pdfkit');

// Listar todos los pagos (para la bandeja de Pagos)
router.get('/', autenticar, async (req, res) => {
  try {
    const pagos = await Pago.findAll({
      include: [
        { model: MetodoPago, as: 'metodo' },
        { 
          model: Reserva,
          include: [
            { model: Cliente, as: 'cliente' },
            { model: Paquete, as: 'paquete' }
          ]
        }
      ],
      order: [['fecha_pago', 'DESC']]
    });
    res.json({ ok: true, data: pagos });
  } catch (error) {
    res.status(500).json({ ok: false, msg: error.message });
  }
});

// Generar Recibo PDF
router.get('/:id/recibo', autenticar, async (req, res) => {
  try {
    const pago = await Pago.findByPk(req.params.id, {
      include: [
        { model: MetodoPago, as: 'metodo' },
        { 
          model: Reserva,
          include: [
            { model: Cliente, as: 'cliente' },
            { model: Paquete, as: 'paquete' }
          ]
        }
      ]
    });

    if (!pago) {
      return res.status(404).json({ ok: false, msg: 'Pago no encontrado' });
    }

    const reserva = pago.Reserva;
    const cliente = reserva?.cliente;

    // Configurar encabezados para descarga del PDF
    res.setHeader('Content-Type', 'application/pdf');
    res.setHeader('Content-Disposition', `attachment; filename=Recibo-${pago.id}-Viaje360.pdf`);

    const doc = new PDFDocument({ margin: 50 });
    doc.pipe(res);

    // Cabecera
    doc.fontSize(20).fillColor('#0EA5E9').text('VIAJE 360 CRM', { align: 'center' });
    doc.fontSize(10).fillColor('#4B5563').text('Comprobante de Pago Oficial', { align: 'center' });
    doc.moveDown(2);

    // Detalles del Pago
    doc.fontSize(14).fillColor('#1F2937').text(`Recibo N°: ${pago.id.toString().padStart(6, '0')}`, { underline: true });
    doc.moveDown(0.5);
    doc.fontSize(11).fillColor('#374151');
    doc.text(`Fecha de Emisión: ${new Date().toLocaleDateString('es-PE')}`);
    doc.text(`Fecha de Pago: ${new Date(pago.fecha_pago).toLocaleDateString('es-PE')}`);
    doc.text(`Estado: ${pago.estado}`);
    doc.moveDown(1);

    // Información del Cliente
    doc.fontSize(12).fillColor('#1F2937').text('Datos del Cliente', { underline: true });
    doc.moveDown(0.5);
    doc.fontSize(11).fillColor('#374151');
    if (cliente) {
      doc.text(`Nombre: ${cliente.nombre} ${cliente.apellido}`);
      doc.text(`Email: ${cliente.email}`);
      doc.text(`Teléfono: ${cliente.telefono || 'N/A'}`);
    } else {
      doc.text('Cliente: Consumidor Final');
    }
    doc.moveDown(1);

    // Detalles de la Reserva
    doc.fontSize(12).fillColor('#1F2937').text('Detalles del Servicio', { underline: true });
    doc.moveDown(0.5);
    doc.fontSize(11).fillColor('#374151');
    if (reserva) {
      doc.text(`Código de Reserva: ${reserva.codigo_reserva}`);
      doc.text(`Paquete: ${reserva.paquete?.nombre || 'Paquete Personalizado'}`);
      doc.text(`Total de Reserva: $${parseFloat(reserva.total_final).toFixed(2)} USD`);
    } else {
      doc.text('Servicio: N/A');
    }
    doc.moveDown(1);

    // Resumen Financiero
    doc.rect(50, doc.y, 500, 70).fillAndStroke('#F3F4F6', '#E5E7EB');
    doc.fillColor('#1F2937');
    doc.text(`Método de Pago: ${pago.metodo?.nombre || 'General'}`, 60, doc.y - 55);
    if (pago.referencia) doc.text(`Referencia: ${pago.referencia}`, 60, doc.y);
    doc.fontSize(14).fillColor('#00A040').text(`Monto Pagado: $${parseFloat(pago.monto).toFixed(2)} USD`, 60, doc.y + 10);
    
    doc.moveDown(2);
    doc.fontSize(10).fillColor('#9CA3AF').text('Gracias por confiar en Viaje 360 para sus próximas aventuras.', 50, 700, { align: 'center' });

    doc.end();

  } catch (error) {
    if (!res.headersSent) {
      res.status(500).json({ ok: false, msg: error.message });
    }
  }
});

module.exports = router;
