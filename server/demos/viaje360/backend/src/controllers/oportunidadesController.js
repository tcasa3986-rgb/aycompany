const { Op } = require('sequelize');
const { Oportunidad, Cliente, Usuario, EtapaPipeline, Paquete } = require('../models');

// GET /api/oportunidades
const listar = async (req, res) => {
  try {
    const { estado, etapa_id, agente_id, cliente_id } = req.query;
    const where = {};
    if (estado)    where.estado    = estado;
    if (etapa_id)  where.etapa_id  = etapa_id;
    if (cliente_id)where.cliente_id= cliente_id;

    if (req.usuario.rol !== 'Administrador') {
      where.agente_id = req.usuario.id;
    } else if (agente_id) {
      where.agente_id = agente_id;
    }
    if (cliente_id)where.cliente_id= cliente_id;

    const rows = await Oportunidad.findAll({
      where,
      include: [
        { association: 'cliente', attributes: ['id','nombre','apellido','email','telefono'] },
        { association: 'agente',  attributes: ['id','nombre','apellido','avatar_url'] },
        { association: 'etapa',   attributes: ['id','nombre','color','orden'] },
        { association: 'paquete', attributes: ['id','nombre','imagen_url'] },
      ],
      order: [['creado_en', 'DESC']],
    });
    return res.json({ ok: true, data: rows });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/oportunidades/kanban
const kanban = async (req, res) => {
  try {
    const etapas = await EtapaPipeline.findAll({ order: [['orden', 'ASC']] });
    const result = await Promise.all(etapas.map(async (etapa) => {
      const whereCond = { etapa_id: etapa.id, estado: 'Activa' };
      if (req.usuario.rol !== 'Administrador') {
        whereCond.agente_id = req.usuario.id;
      }
      const oportunidades = await Oportunidad.findAll({
        where: whereCond,
        include: [
          { association: 'cliente', attributes: ['id','nombre','apellido'] },
          { association: 'agente',  attributes: ['id','nombre','apellido','avatar_url'] },
          { association: 'paquete', attributes: ['id','nombre'] },
        ],
        order: [['actualizado_en', 'DESC']],
      });
      return { ...etapa.toJSON(), oportunidades };
    }));
    return res.json({ ok: true, data: result });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// POST /api/oportunidades
const crear = async (req, res) => {
  try {
    const isVendedor = req.usuario.rol !== 'Administrador';
    const op = await Oportunidad.create({ ...req.body, agente_id: isVendedor ? req.usuario.id : (req.body.agente_id || req.usuario.id) });
    return res.status(201).json({ ok: true, data: op });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// PUT /api/oportunidades/:id
const actualizar = async (req, res) => {
  try {
    const op = await Oportunidad.findByPk(req.params.id);
    if (!op) return res.status(404).json({ ok: false, msg: 'No encontrada' });
    if (req.usuario.rol !== 'Administrador' && op.agente_id !== req.usuario.id) {
      return res.status(403).json({ ok: false, msg: 'Acceso denegado' });
    }
    await op.update({ ...req.body, actualizado_en: new Date() });
    return res.json({ ok: true, data: op });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// PATCH /api/oportunidades/:id/etapa
const cambiarEtapa = async (req, res) => {
  try {
    const { etapa_id } = req.body;
    const op = await Oportunidad.findByPk(req.params.id);
    if (!op) return res.status(404).json({ ok: false, msg: 'No encontrada' });
    if (req.usuario.rol !== 'Administrador' && op.agente_id !== req.usuario.id) {
      return res.status(403).json({ ok: false, msg: 'Acceso denegado' });
    }
    const etapaAnterior = op.etapa_id;
    await op.update({ etapa_id, actualizado_en: new Date() });
    // Registrar historial
    const { sequelize: db } = require('../models');
    await db.query(
      `INSERT INTO oportunidad_historial (oportunidad_id, etapa_anterior, etapa_nueva, usuario_id) VALUES (?,?,?,?)`,
      { replacements: [op.id, etapaAnterior, etapa_id, req.usuario.id] }
    );
    return res.json({ ok: true, data: op });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

module.exports = { listar, kanban, crear, actualizar, cambiarEtapa };
