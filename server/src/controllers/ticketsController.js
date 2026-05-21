const { Ticket, Cliente } = require('../models');
const { Op } = require('sequelize');

const includeCliente = [{ model: Cliente, as: 'cliente', attributes: ['nombre', 'email', 'telefono'] }];

exports.listar = async (req, res) => {
    try {
        const where = {};
        if (req.query.estado) where.estado = req.query.estado;
        const tickets = await Ticket.findAll({ where, include: includeCliente, order: [['created_at', 'DESC']] });
        res.json({ ok: true, data: tickets });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.responder = async (req, res) => {
    try {
        const ticket = await Ticket.findByPk(req.params.id);
        if (!ticket) return res.status(404).json({ ok: false, msg: 'Ticket no encontrado' });

        const { respuesta, estado } = req.body;
        const updates = {};
        if (respuesta !== undefined) { updates.respuesta = respuesta; updates.respondido_at = new Date(); }
        if (estado)     updates.estado = estado;

        await ticket.update(updates);
        res.json({ ok: true, msg: 'Ticket actualizado' });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.eliminar = async (req, res) => {
    try {
        const ticket = await Ticket.findByPk(req.params.id);
        if (!ticket) return res.status(404).json({ ok: false, msg: 'Ticket no encontrado' });
        await ticket.destroy();
        res.json({ ok: true, msg: 'Ticket eliminado' });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};
