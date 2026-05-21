const { Op } = require('sequelize');
const { Paquete, Destino, Pais, CategoriaPaquete } = require('../models');

// GET /api/paquetes
const listar = async (req, res) => {
  try {
    const { destino_id, categoria_id, disponible, buscar } = req.query;
    const where = {};
    if (destino_id)  where.destino_id  = destino_id;
    if (categoria_id)where.categoria_id= categoria_id;
    if (disponible !== undefined) where.disponible = disponible;
    if (buscar) where.nombre = { [Op.like]: `%${buscar}%` };

    const rows = await Paquete.findAll({
      where,
      include: [
        { association: 'destino',  include: [{ association: 'pais', attributes: ['id','nombre','codigo','zona'] }] },
        { association: 'categoria', attributes: ['id','nombre'] },
      ],
      order: [['creado_en', 'DESC']],
    });
    return res.json({ ok: true, data: rows });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/paquetes/:id
const obtener = async (req, res) => {
  try {
    const p = await Paquete.findByPk(req.params.id, {
      include: [
        { association: 'destino', include: [{ association: 'pais' }] },
        { association: 'categoria' },
      ],
    });
    if (!p) return res.status(404).json({ ok: false, msg: 'Paquete no encontrado' });
    return res.json({ ok: true, data: p });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// POST /api/paquetes
const crear = async (req, res) => {
  try {
    const p = await Paquete.create(req.body);
    return res.status(201).json({ ok: true, data: p });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// PUT /api/paquetes/:id
const actualizar = async (req, res) => {
  try {
    const p = await Paquete.findByPk(req.params.id);
    if (!p) return res.status(404).json({ ok: false, msg: 'No encontrado' });
    await p.update(req.body);
    return res.json({ ok: true, data: p });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// DELETE /api/paquetes/:id
const eliminar = async (req, res) => {
  try {
    const p = await Paquete.findByPk(req.params.id);
    if (!p) return res.status(404).json({ ok: false, msg: 'No encontrado' });
    await p.update({ disponible: 0 });
    return res.json({ ok: true, msg: 'Paquete desactivado' });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/destinos
const listarDestinos = async (req, res) => {
  try {
    const rows = await Destino.findAll({
      where: { activo: 1 },
      include: [{ association: 'pais' }],
      order: [['nombre', 'ASC']],
    });
    return res.json({ ok: true, data: rows });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/paises
const listarPaises = async (req, res) => {
  try {
    const rows = await Pais.findAll({ order: [['nombre', 'ASC']] });
    return res.json({ ok: true, data: rows });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

module.exports = { listar, obtener, crear, actualizar, eliminar, listarDestinos, listarPaises };
