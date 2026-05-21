const { Op } = require('sequelize');
const { Reserva, Cliente, Usuario, Paquete, Pasajero, Pago, MetodoPago } = require('../models');
const PDFDocument = require('pdfkit');

// Genera código de reserva único
const generarCodigo = () => {
  const now  = new Date();
  const year = now.getFullYear().toString().slice(-2);
  const mes  = String(now.getMonth() + 1).padStart(2, '0');
  const rand = Math.random().toString(36).substring(2,6).toUpperCase();
  return `V360-${year}${mes}-${rand}`;
};

// GET /api/reservas
const listar = async (req, res) => {
  try {
    const { estado, agente_id, cliente_id, page = 1, limit = 20 } = req.query;
    const where = {};
    if (estado)     where.estado     = estado;
    if (cliente_id) where.cliente_id = cliente_id;

    if (req.usuario.rol !== 'Administrador') {
      where.agente_id = req.usuario.id;
    } else if (agente_id) {
      where.agente_id = agente_id;
    }

    const offset = (parseInt(page) - 1) * parseInt(limit);
    const { count, rows } = await Reserva.findAndCountAll({
      where,
      include: [
        { association: 'cliente', attributes: ['id','nombre','apellido','email','telefono'] },
        { association: 'agente',  attributes: ['id','nombre','apellido'] },
        { association: 'paquete', attributes: ['id','nombre','imagen_url'] },
        { association: 'pagos',   attributes: ['id','monto','estado'] },
      ],
      order: [['creado_en', 'DESC']],
      limit: parseInt(limit),
      offset,
    });
    return res.json({ ok: true, total: count, page: parseInt(page), data: rows });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/reservas/:id
const obtener = async (req, res) => {
  try {
    const reserva = await Reserva.findByPk(req.params.id, {
      include: [
        { association: 'cliente',   attributes: ['id','nombre','apellido','email','telefono','documento_tipo','documento_num'] },
        { association: 'agente',    attributes: ['id','nombre','apellido'] },
        { association: 'paquete',   include: [{ association: 'destino' }] },
        { association: 'pasajeros' },
        { association: 'pagos',     include: [{ association: 'metodo' }] },
        { association: 'oportunidad' },
      ],
    });
    if (!reserva) return res.status(404).json({ ok: false, msg: 'Reserva no encontrada' });
    if (req.usuario.rol !== 'Administrador' && reserva.agente_id !== req.usuario.id) {
      return res.status(403).json({ ok: false, msg: 'Acceso denegado' });
    }
    return res.json({ ok: true, data: reserva });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// POST /api/reservas
const crear = async (req, res) => {
  try {
    const { pasajeros = [], ...data } = req.body;
    const codigo = generarCodigo();
    const isVendedor = req.usuario.rol !== 'Administrador';
    const reserva = await Reserva.create({ ...data, codigo_reserva: codigo, agente_id: isVendedor ? req.usuario.id : (data.agente_id || req.usuario.id) });
    if (pasajeros.length) {
      await Pasajero.bulkCreate(pasajeros.map(p => ({ ...p, reserva_id: reserva.id })));
    }
    return res.status(201).json({ ok: true, data: reserva });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// PUT /api/reservas/:id
const actualizar = async (req, res) => {
  try {
    const reserva = await Reserva.findByPk(req.params.id);
    if (!reserva) return res.status(404).json({ ok: false, msg: 'No encontrada' });
    await reserva.update({ ...req.body, actualizado_en: new Date() });
    return res.json({ ok: true, data: reserva });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// POST /api/reservas/:id/pagos
const registrarPago = async (req, res) => {
  try {
    const pago = await Pago.create({
      ...req.body,
      reserva_id:     req.params.id,
      registrado_por: req.usuario.id,
    });
    return res.status(201).json({ ok: true, data: pago });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/reservas/:id/pdf
const generarPDF = async (req, res) => {
  try {
    const reserva = await Reserva.findByPk(req.params.id, {
      include: [
        { association: 'cliente', attributes: ['nombre','apellido','email','documento_num'] },
        { association: 'paquete' },
        { association: 'pasajeros' },
        { association: 'pagos' }
      ]
    });
    
    if (!reserva) return res.status(404).json({ ok: false, msg: 'Reserva no encontrada' });
    if (req.usuario.rol !== 'Administrador' && reserva.agente_id !== req.usuario.id) {
      return res.status(403).json({ ok: false, msg: 'Acceso denegado a este documento' });
    }

    const doc = new PDFDocument({ margin: 50 });
    
    res.setHeader('Content-Type', 'application/pdf');
    res.setHeader('Content-Disposition', `attachment; filename=Voucher-${reserva.codigo_reserva}.pdf`);
    
    doc.pipe(res);

    // Header
    doc.fontSize(22).fillColor('#0EA5E9').text('VIAJE 360', { align: 'right' });
    doc.fontSize(10).fillColor('#475569').text('Agencia de Viajes y Turismo', { align: 'right' });
    doc.moveDown(2);

    // Title
    doc.fontSize(16).fillColor('#1E293B').text('VOUCHER DE SERVICIOS / COTIZACION', { align: 'left', underline: true });
    doc.moveDown();

    // Reserva Details
    doc.fontSize(12).fillColor('#1E293B').text(`Codigo de Reserva: `, { continued: true }).fillColor('#475569').text(reserva.codigo_reserva);
    doc.fillColor('#1E293B').text(`Estado: `, { continued: true }).fillColor('#475569').text(reserva.estado);
    if (reserva.fecha_salida) {
      doc.fillColor('#1E293B').text(`Fecha de Salida: `, { continued: true }).fillColor('#475569').text(new Date(reserva.fecha_salida).toLocaleDateString('es-ES'));
    }
    if (reserva.fecha_regreso) {
      doc.fillColor('#1E293B').text(`Fecha de Regreso: `, { continued: true }).fillColor('#475569').text(new Date(reserva.fecha_regreso).toLocaleDateString('es-ES'));
    }
    doc.moveDown();

    // Cliente
    doc.fontSize(14).fillColor('#0284C7').text('Datos del Titular');
    doc.rect(50, doc.y, 500, 1).fill('#E5E7EB');
    doc.moveDown(0.5);
    doc.fontSize(11).fillColor('#1E293B').text(`Nombre: `, { continued: true }).fillColor('#475569').text(`${reserva.cliente?.nombre || ''} ${reserva.cliente?.apellido || ''}`);
    doc.fillColor('#1E293B').text(`Documento: `, { continued: true }).fillColor('#475569').text(reserva.cliente?.documento_num || '---');
    doc.fillColor('#1E293B').text(`Email: `, { continued: true }).fillColor('#475569').text(reserva.cliente?.email || '---');
    doc.moveDown();

    // Paquete
    doc.fontSize(14).fillColor('#0284C7').text('Detalles del Paquete');
    doc.rect(50, doc.y, 500, 1).fill('#E5E7EB');
    doc.moveDown(0.5);
    doc.fontSize(11).fillColor('#1E293B').text(`Paquete: `, { continued: true }).fillColor('#475569').text(reserva.paquete?.nombre || 'Personalizado');
    doc.fillColor('#1E293B').text(`Pasajeros: `, { continued: true }).fillColor('#475569').text(`Adultos: ${reserva.num_adultos || 0} - Ninos: ${reserva.num_ninos || 0}`);
    doc.moveDown();

    // Resumen de Pagos
    const pagado = (reserva.pagos || []).filter(p => p.estado === 'Verificado').reduce((s, p) => s + +p.monto, 0);
    const total = +reserva.total_final || 0;
    const pendiente = total - pagado;

    doc.fontSize(14).fillColor('#0284C7').text('Resumen Financiero');
    doc.rect(50, doc.y, 500, 1).fill('#E5E7EB');
    doc.moveDown();
    
    doc.fontSize(12);
    doc.fillColor('#1E293B').text(`Total del Paquete: `, { continued: true }).fillColor('#10B981').text(`$${total.toLocaleString('en-US', { minimumFractionDigits: 2 })}`);
    doc.fillColor('#1E293B').text(`Total Pagado: `, { continued: true }).fillColor('#475569').text(`$${pagado.toLocaleString('en-US', { minimumFractionDigits: 2 })}`);
    if (pendiente > 0) {
      doc.fillColor('#1E293B').text(`Saldo Pendiente: `, { continued: true }).fillColor('#EF4444').text(`$${pendiente.toLocaleString('en-US', { minimumFractionDigits: 2 })}`);
    } else {
      doc.fillColor('#10B981').text(`ESTADO DE CUENTA: TOTALMENTE PAGADO`);
    }
    
    doc.moveDown(3);
    doc.fontSize(10).fillColor('#94A3B8').text('Gracias por confiar en Viaje 360 para organizar su aventura.', { align: 'center' });

    doc.end();
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

module.exports = { listar, obtener, crear, actualizar, registrarPago, generarPDF };
