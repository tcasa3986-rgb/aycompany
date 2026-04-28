const OpenAI = require('openai');
const { MetaMarketing, EstrategiaMarketing, IdeaContenido } = require('../models');

const openai = new OpenAI({ apiKey: process.env.OPENAI_API_KEY });

exports.chat = async (req, res) => {
    const { mensaje, historial = [] } = req.body;

    const [metas, estrategias, ideas] = await Promise.all([
        MetaMarketing.findAll({ where: { completada: false } }),
        EstrategiaMarketing.findAll({ where: { estado: 'activa' } }),
        IdeaContenido.findAll({ order: [['createdAt', 'DESC']], limit: 10 })
    ]);

    const contexto = `
Eres el asistente de marketing y contenido de Mi Plataforma.

METAS ACTIVAS DE MARKETING:
${metas.length ? metas.map(m => `- ${m.plataforma} | ${m.metrica}: ${m.valor_actual}/${m.valor_meta}${m.fecha_limite ? ` (límite: ${m.fecha_limite})` : ''}`).join('\n') : 'Sin metas definidas'}

ESTRATEGIAS ACTIVAS:
${estrategias.length ? estrategias.map(e => `- ${e.titulo} (${e.canal}) — ${e.objetivo || 'sin objetivo'}`).join('\n') : 'Sin estrategias activas'}

IDEAS DE CONTENIDO RECIENTES:
${ideas.length ? ideas.map(i => `- [${i.estado}] ${i.titulo} (${i.canal}/${i.formato})`).join('\n') : 'Sin ideas registradas'}

Ayuda al usuario con ideas de contenido creativas, estrategias para alcanzar sus metas, y consejos de marketing digital. Sé específico y accionable. Responde en español.`;

    const messages = [
        { role: 'system', content: contexto },
        ...historial.slice(-10),
        { role: 'user', content: mensaje }
    ];

    const completion = await openai.chat.completions.create({
        model: 'gpt-4o-mini',
        messages,
        max_tokens: 800
    });

    res.json({ respuesta: completion.choices[0].message.content });
};
