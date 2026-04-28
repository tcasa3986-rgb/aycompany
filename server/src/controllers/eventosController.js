const { Evento } = require('../models');
const { Op } = require('sequelize');
const telegramService = require('../services/telegramService');

exports.listar = async (req, res) => {
    const { desde, hasta } = req.query;
    const where = {};
    if (desde || hasta) {
        where.fecha_inicio = {};
        if (desde) where.fecha_inicio[Op.gte] = new Date(desde);
        if (hasta) where.fecha_inicio[Op.lte] = new Date(hasta);
    }
    const data = await Evento.findAll({ where, order: [['fecha_inicio', 'ASC']] });
    res.json(data);
};

exports.crear = async (req, res) => {
    const evento = await Evento.create(req.body);
    if (req.body.recordatorio && process.env.PLATAFORMA_TELEGRAM_TOKEN && process.env.PLATAFORMA_TELEGRAM_CHAT_ID) {
        const fecha = new Date(evento.fecha_inicio).toLocaleString('es-CO', { dateStyle: 'full', timeStyle: 'short' });
        const msg = `📅 *Nuevo evento agendado*\n\n*${evento.titulo}*\n🕐 ${fecha}${evento.participantes ? `\n👥 ${evento.participantes}` : ''}${evento.link ? `\n🔗 ${evento.link}` : ''}${evento.descripcion ? `\n\n${evento.descripcion}` : ''}`;
        telegramService.enviar(msg).catch(() => {});
    }
    res.json(evento);
};

exports.actualizar = async (req, res) => {
    const e = await Evento.findByPk(req.params.id);
    if (!e) return res.status(404).json({ error: 'No encontrado' });
    await e.update(req.body);
    res.json(e);
};

exports.eliminar = async (req, res) => {
    await Evento.destroy({ where: { id: req.params.id } });
    res.json({ ok: true });
};
