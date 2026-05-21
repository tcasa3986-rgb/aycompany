const { Reunion, Evento } = require('../models');
const { Op } = require('sequelize');
const telegramService = require('../services/telegramService');

exports.listar = async (req, res) => {
    try {
        const reuniones = await Reunion.findAll({ order: [['fecha', 'ASC']] });
        res.json(reuniones);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.crear = async (req, res) => {
    try {
        const reunion = await Reunion.create(req.body);

        // Crear evento en el calendario del admin
        const fechaInicio = new Date(reunion.fecha);
        const fechaFin    = new Date(fechaInicio.getTime() + (reunion.duracion || 60) * 60000);
        Evento.create({
            titulo:       reunion.titulo,
            descripcion:  reunion.descripcion || '',
            fecha_inicio: fechaInicio,
            fecha_fin:    fechaFin,
            color:        '#6366f1',
            participantes: reunion.participantes || '',
            recordatorio: true
        }).catch(() => {});

        // Notificar al admin por Telegram si fue agendada por un vendedor
        const vendedor = req.user?.nombre || 'Un vendedor';
        const fechaTxt = fechaInicio.toLocaleDateString('es-CO', {
            weekday: 'long', day: 'numeric', month: 'long',
            hour: '2-digit', minute: '2-digit', timeZone: 'America/Bogota'
        });
        telegramService.enviar(
            `📅 *Nueva reunión agendada*\n\n` +
            `👤 *Vendedor:* ${vendedor}\n` +
            `🤝 *Prospecto:* ${reunion.participantes || reunion.titulo}\n` +
            `📅 *Fecha:* ${fechaTxt}\n` +
            `⏱ *Duración:* ${reunion.duracion || 60} min\n` +
            `${reunion.descripcion ? `📝 *Nota:* ${reunion.descripcion}` : ''}`
        ).catch(() => {});

        res.json(reunion);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const reunion = await Reunion.findByPk(req.params.id);
        if (!reunion) return res.status(404).json({ error: 'No encontrada' });
        await reunion.update(req.body);
        res.json(reunion);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.eliminar = async (req, res) => {
    try {
        await Reunion.destroy({ where: { id: req.params.id } });
        res.json({ ok: true });
    } catch (e) { res.status(500).json({ error: e.message }); }
};
