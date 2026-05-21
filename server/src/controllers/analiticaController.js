const { Cliente, Licencia, Pago, Producto, Ticket, Proyecto } = require('../models');
const { Op } = require('sequelize');
const sequelize = require('../config/db');
const Anthropic = require('@anthropic-ai/sdk');

exports.predicciones = async (req, res) => {
    try {
        const ahora = new Date();
        const hace60 = new Date(ahora.getTime() - 60 * 86400000);
        const en7    = new Date(ahora.getTime() +  7 * 86400000);
        const en30   = new Date(ahora.getTime() + 30 * 86400000);

        // ── Obtener todos los clientes con sus licencias y pagos ──────────────
        const clientes = await Cliente.findAll({
            where: { activo: true },
            include: [
                {
                    model: Licencia, as: 'licencias',
                    include: [{ model: Producto, as: 'producto', attributes: ['nombre', 'precio_mensual'] }]
                },
                { model: Pago, as: 'pagos', attributes: ['fecha_pago', 'monto'], limit: 10, order: [['fecha_pago', 'DESC']] }
            ]
        });

        // ── Calcular riesgo de churn por cliente ──────────────────────────────
        const scoredClientes = clientes.map(c => {
            const licencias = c.licencias || [];
            const pagos     = c.pagos || [];

            let score = 0;
            let razon = [];

            const licActiva  = licencias.find(l => l.activo && new Date(l.fecha_vencimiento) >= ahora);
            const licVencida = licencias.find(l => new Date(l.fecha_vencimiento) < ahora);
            const ultimoPago = pagos.length > 0 ? new Date(pagos[0].fecha_pago) : null;
            const diasSinPago = ultimoPago ? Math.ceil((ahora - ultimoPago) / 86400000) : 999;

            if (!licActiva && licVencida) { score += 80; razon.push('Sin licencia activa'); }
            if (licActiva) {
                const vence = new Date(licActiva.fecha_vencimiento);
                const diasRestantes = Math.ceil((vence - ahora) / 86400000);
                if (diasRestantes <= 7)  { score += 60; razon.push(`Vence en ${diasRestantes}d`); }
                else if (diasRestantes <= 15) { score += 40; razon.push(`Vence en ${diasRestantes}d`); }
                else if (diasRestantes <= 30) { score += 20; razon.push(`Vence en ${diasRestantes}d`); }
            }
            if (diasSinPago > 90)  { score += 30; razon.push('Sin pagos +90 días'); }
            else if (diasSinPago > 60) { score += 15; razon.push('Sin pagos +60 días'); }

            const mrr = licencias
                .filter(l => l.activo && new Date(l.fecha_vencimiento) >= ahora)
                .reduce((s, l) => s + Number(l.producto?.precio_mensual || 0), 0);

            return {
                id:      c.id,
                nombre:  c.nombre,
                empresa: c.empresa,
                email:   c.email,
                score:   Math.min(score, 100),
                razon:   razon.join(' · ') || 'Cliente estable',
                riesgo:  score >= 60 ? 'alto' : score >= 30 ? 'medio' : 'bajo',
                mrr,
                ultimoPago: ultimoPago?.toISOString().split('T')[0] || null,
                diasSinPago
            };
        });

        // ── Segmentos ─────────────────────────────────────────────────────────
        const enRiesgo    = scoredClientes.filter(c => c.riesgo === 'alto').sort((a,b) => b.score - a.score);
        const medioRiesgo = scoredClientes.filter(c => c.riesgo === 'medio').sort((a,b) => b.score - a.score);
        const estables    = scoredClientes.filter(c => c.riesgo === 'bajo');

        // ── MRR actual ────────────────────────────────────────────────────────
        const mrrTotal = scoredClientes.reduce((s, c) => s + c.mrr, 0);
        const mrrEnRiesgo = enRiesgo.reduce((s, c) => s + c.mrr, 0);

        // ── Forecast de ingresos (últimos 6 meses → proyección 3 meses) ───────
        const meses = [];
        for (let i = 5; i >= 0; i--) {
            const ini = new Date(ahora.getFullYear(), ahora.getMonth() - i, 1);
            const fin = new Date(ahora.getFullYear(), ahora.getMonth() - i + 1, 0);
            const total = await Pago.findOne({
                attributes: [[sequelize.fn('SUM', sequelize.col('monto')), 'total']],
                where: { fecha_pago: { [Op.between]: [ini.toISOString().split('T')[0], fin.toISOString().split('T')[0]] } },
                raw: true
            });
            meses.push({
                mes:   ini.toLocaleDateString('es-CO', { month: 'short', year: 'numeric' }),
                total: Number(total?.total || 0)
            });
        }

        // Promedio últimos 3 meses
        const promedio3m = meses.slice(-3).reduce((s, m) => s + m.total, 0) / 3;
        const forecast = [1, 2, 3].map(i => {
            const d = new Date(ahora.getFullYear(), ahora.getMonth() + i, 1);
            return {
                mes: d.toLocaleDateString('es-CO', { month: 'short', year: 'numeric' }),
                total: Math.round(promedio3m * (1 + (i === 1 ? 0 : i === 2 ? 0.03 : 0.05)))
            };
        });

        // ── Licencias que vencen este mes ─────────────────────────────────────
        const vencenEsteMes = await Licencia.count({
            where: {
                activo: true,
                fecha_vencimiento: {
                    [Op.between]: [ahora.toISOString().split('T')[0], en30.toISOString().split('T')[0]]
                }
            }
        });

        // ── Tickets pendientes ────────────────────────────────────────────────
        const ticketsPendientes = await Ticket.count({ where: { estado: { [Op.ne]: 'cerrado' } } });

        res.json({
            ok: true,
            data: {
                resumen: {
                    totalClientes:    scoredClientes.length,
                    enRiesgoAlto:     enRiesgo.length,
                    mrrTotal,
                    mrrEnRiesgo,
                    pctRiesgo:        scoredClientes.length > 0 ? Math.round(enRiesgo.length / scoredClientes.length * 100) : 0,
                    vencenEsteMes,
                    ticketsPendientes
                },
                enRiesgo:    enRiesgo.slice(0, 20),
                medioRiesgo: medioRiesgo.slice(0, 10),
                historial:   meses,
                forecast,
                segmentos: {
                    alto:  enRiesgo.length,
                    medio: medioRiesgo.length,
                    bajo:  estables.length
                }
            }
        });
    } catch (err) {
        console.error('Error analitica:', err);
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.insightsIA = async (req, res) => {
    if (!process.env.ANTHROPIC_API_KEY) return res.json({ ok: false, msg: 'IA no configurada' });
    try {
        const { resumen, enRiesgo, historial } = req.body;
        const anthropic = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

        const prompt = `Eres un consultor de negocios SaaS. Analiza estos datos de una plataforma de licencias de software y da 3 recomendaciones concretas y accionables.

DATOS:
- Clientes activos: ${resumen?.totalClientes}
- Clientes en alto riesgo de churn: ${resumen?.enRiesgoAlto} (${resumen?.pctRiesgo}% del total)
- MRR actual: $${Number(resumen?.mrrTotal || 0).toLocaleString('es-CO')} COP
- MRR en riesgo: $${Number(resumen?.mrrEnRiesgo || 0).toLocaleString('es-CO')} COP
- Licencias que vencen este mes: ${resumen?.vencenEsteMes}
- Tickets de soporte pendientes: ${resumen?.ticketsPendientes}
- Clientes top en riesgo: ${enRiesgo?.slice(0,3).map(c => `${c.nombre} (${c.razon})`).join(', ')}
- Ingresos últimos 6 meses: ${historial?.map(m => `${m.mes}: $${Number(m.total).toLocaleString('es-CO')}`).join(', ')}

Responde con exactamente 3 recomendaciones. Formato JSON:
[
  { "titulo": "...", "accion": "...", "impacto": "alto|medio", "plazo": "esta semana|este mes" },
  ...
]

Solo el JSON, sin texto adicional.`;

        const response = await anthropic.messages.create({
            model: 'claude-haiku-4-5-20251001',
            max_tokens: 600,
            messages: [{ role: 'user', content: prompt }]
        });

        const texto = response.content[0]?.text?.trim();
        let insights = [];
        try { insights = JSON.parse(texto); } catch { insights = []; }

        res.json({ ok: true, data: insights });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};
