const { Op } = require('sequelize');
const { Cliente, Usuario, FuenteOrigen, Etiqueta, Interaccion, Reserva } = require('../models');

// GET /api/clientes
const listar = async (req, res) => {
  try {
    const { buscar, categoria, agente_id, fuente_id, page = 1, limit = 20 } = req.query;
    const where = { activo: 1 };
    if (categoria) where.categoria = categoria;
    if (fuente_id) where.fuente_id = fuente_id;

    const isVendedor = req.usuario.rol !== 'Administrador';
    if (isVendedor) {
      where.agente_id = req.usuario.id;
    } else if (agente_id) {
      where.agente_id = agente_id;
    }
    if (buscar) {
      where[Op.or] = [
        { nombre:   { [Op.like]: `%${buscar}%` } },
        { apellido: { [Op.like]: `%${buscar}%` } },
        { email:    { [Op.like]: `%${buscar}%` } },
        { telefono: { [Op.like]: `%${buscar}%` } },
      ];
    }
    const offset = (parseInt(page) - 1) * parseInt(limit);
    const { count, rows } = await Cliente.findAndCountAll({
      where,
      include: [
        { association: 'agente',   attributes: ['id','nombre','apellido','avatar_url'] },
        { association: 'fuente',   attributes: ['id','nombre'] },
        { association: 'etiquetas',attributes: ['id','nombre','color'] },
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

// GET /api/clientes/:id
const obtener = async (req, res) => {
  try {
    const cliente = await Cliente.findByPk(req.params.id, {
      include: [
        { association: 'agente',       attributes: ['id','nombre','apellido','avatar_url'] },
        { association: 'fuente',       attributes: ['id','nombre'] },
        { association: 'etiquetas',    attributes: ['id','nombre','color'] },
        { association: 'interacciones',include: [{ association:'usuario', attributes:['id','nombre','apellido'] }], order: [['fecha','DESC']] },
        { association: 'reservas',     include: [{ association:'paquete', attributes:['id','nombre'] }] },
        { association: 'oportunidades',include: [{ association:'etapa', attributes:['id','nombre','color'] }] },
      ],
    });
    if (!cliente) return res.status(404).json({ ok: false, msg: 'Cliente no encontrado' });
    
    if (req.usuario.rol !== 'Administrador' && cliente.agente_id !== req.usuario.id) {
      return res.status(403).json({ ok: false, msg: 'Acceso denegado a este cliente' });
    }

    return res.json({ ok: true, data: cliente });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// POST /api/clientes
const crear = async (req, res) => {
  try {
    if (req.usuario.rol !== 'Administrador' || !req.body.agente_id) {
      req.body.agente_id = req.usuario.id;
    }
    const cliente = await Cliente.create(req.body);
    if (req.body.etiquetas?.length) {
      await cliente.setEtiquetas(req.body.etiquetas);
    }
    return res.status(201).json({ ok: true, data: cliente });
  } catch (err) {
    if (err.name === 'SequelizeUniqueConstraintError')
      return res.status(409).json({ ok: false, msg: 'El email ya está registrado' });
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// PUT /api/clientes/:id
const actualizar = async (req, res) => {
  try {
    const cliente = await Cliente.findByPk(req.params.id);
    if (!cliente) return res.status(404).json({ ok: false, msg: 'Cliente no encontrado' });
    await cliente.update({ ...req.body, actualizado_en: new Date() });
    if (req.body.etiquetas !== undefined) {
      await cliente.setEtiquetas(req.body.etiquetas);
    }
    return res.json({ ok: true, data: cliente });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// DELETE /api/clientes/:id (soft delete)
const eliminar = async (req, res) => {
  try {
    const cliente = await Cliente.findByPk(req.params.id);
    if (!cliente) return res.status(404).json({ ok: false, msg: 'Cliente no encontrado' });
    await cliente.update({ activo: 0 });
    return res.json({ ok: true, msg: 'Cliente desactivado' });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// POST /api/clientes/:id/interacciones
const agregarInteraccion = async (req, res) => {
  try {
    const interaccion = await Interaccion.create({
      ...req.body,
      cliente_id: req.params.id,
      usuario_id: req.usuario.id,
    });
    return res.status(201).json({ ok: true, data: interaccion });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

module.exports = { listar, obtener, crear, actualizar, eliminar, agregarInteraccion };
