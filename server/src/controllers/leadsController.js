const { Lead } = require('../models');

exports.listar = async (req, res) => {
    try {
        const leads = await Lead.findAll({ order: [['created_at', 'DESC']] });
        res.json(leads);
    } catch (e) { res.status(500).json({ error: e.message }); }
};

// Los campos de plata de un trato: se validan aparte porque un error aquí
// mete dinero inventado en las proyecciones.
function camposTrato(body) {
    const out = {};
    for (const campo of ['valor_unico', 'valor_mensual']) {
        if (campo in body) {
            const v = body[campo] === '' || body[campo] == null ? 0 : Number(body[campo]);
            if (isNaN(v) || v < 0) throw new Error(`${campo} inválido`);
            out[campo] = v;
        }
    }
    if ('probabilidad' in body) {
        const p = parseInt(body.probabilidad);
        if (isNaN(p) || p < 0 || p > 100) throw new Error('probabilidad debe estar entre 0 y 100');
        out.probabilidad = p;
    }
    if ('fecha_estimada_cierre' in body) {
        const f = body.fecha_estimada_cierre;
        if (f && !/^\d{4}-\d{2}-\d{2}$/.test(f)) throw new Error('fecha_estimada_cierre debe ser YYYY-MM-DD');
        out.fecha_estimada_cierre = f || null;
    }
    return out;
}

exports.crear = async (req, res) => {
    try {
        if (!String(req.body.nombre || '').trim()) throw new Error('nombre es obligatorio');
        const lead = await Lead.create({ ...req.body, ...camposTrato(req.body) });
        res.status(201).json(lead);
    } catch (e) { res.status(400).json({ error: e.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        await Lead.update({ ...req.body, ...camposTrato(req.body) }, { where: { id: req.params.id } });
        const lead = await Lead.findByPk(req.params.id);
        res.json(lead);
    } catch (e) { res.status(400).json({ error: e.message }); }
};

exports.eliminar = async (req, res) => {
    try {
        await Lead.destroy({ where: { id: req.params.id } });
        res.json({ ok: true });
    } catch (e) { res.status(500).json({ error: e.message }); }
};

exports.stats = async (req, res) => {
    try {
        const total      = await Lead.count();
        const nuevos     = await Lead.count({ where: { estado: 'nuevo' } });
        const contactados = await Lead.count({ where: { estado: 'contactado' } });
        const respondieron = await Lead.count({ where: { estado: ['respondio','interesado','reunion_agendada','reunion_realizada'] } });
        const reuniones  = await Lead.count({ where: { estado: ['reunion_agendada','reunion_realizada'] } });
        const clientes   = await Lead.count({ where: { estado: 'cliente' } });
        const descartados = await Lead.count({ where: { estado: ['descartado','sin_respuesta'] } });

        const tasaRespuesta = contactados > 0 ? Math.round((respondieron / (contactados + respondieron)) * 100) : 0;
        const tasaReunion   = respondieron > 0 ? Math.round((reuniones / respondieron) * 100) : 0;

        res.json({ total, nuevos, contactados, respondieron, reuniones, clientes, descartados, tasaRespuesta, tasaReunion });
    } catch (e) { res.status(500).json({ error: e.message }); }
};
