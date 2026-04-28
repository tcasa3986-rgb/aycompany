const Anthropic = require('@anthropic-ai/sdk');
const { MetaMarketing, EstrategiaMarketing, IdeaContenido } = require('../models');

exports.chat = async (req, res) => {
    if (!process.env.ANTHROPIC_API_KEY) {
        return res.status(500).json({ error: 'ANTHROPIC_API_KEY no configurada' });
    }
    const anthropic = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });
    const { mensaje, historial = [] } = req.body;

    const [metas, estrategias, ideas] = await Promise.all([
        MetaMarketing.findAll({ where: { completada: false } }),
        EstrategiaMarketing.findAll({ where: { estado: 'activa' } }),
        IdeaContenido.findAll({ order: [['createdAt', 'DESC']], limit: 10 })
    ]);

    const sistema = `Eres el asistente de marketing y contenido de Mi Plataforma.

METAS ACTIVAS DE MARKETING:
${metas.length ? metas.map(m => `- ${m.plataforma} | ${m.metrica}: ${m.valor_actual}/${m.valor_meta}${m.fecha_limite ? ` (límite: ${m.fecha_limite})` : ''}`).join('\n') : 'Sin metas definidas'}

ESTRATEGIAS ACTIVAS:
${estrategias.length ? estrategias.map(e => `- ${e.titulo} (${e.canal}) — ${e.objetivo || 'sin objetivo'}`).join('\n') : 'Sin estrategias activas'}

IDEAS DE CONTENIDO RECIENTES:
${ideas.length ? ideas.map(i => `- [${i.estado}] ${i.titulo} (${i.canal}/${i.formato})`).join('\n') : 'Sin ideas registradas'}

Ayuda al usuario con ideas de contenido creativas, estrategias para alcanzar sus metas, y consejos de marketing digital. Sé específico y accionable. Responde en español.`;

    const messages = [
        ...historial.slice(-10),
        { role: 'user', content: mensaje }
    ];

    const response = await anthropic.messages.create({
        model: 'claude-haiku-4-5-20251001',
        max_tokens: 1024,
        system: sistema,
        messages
    });

    res.json({ respuesta: response.content[0].text });
};
