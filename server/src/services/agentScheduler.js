const cron = require('node-cron');
const { Op } = require('sequelize');
const { Lead, AgenteConfig, AgentActividad } = require('../models');
const { procesarLead } = require('./agentService');

function diasAtras(dias) {
    const d = new Date();
    d.setDate(d.getDate() - dias);
    return d;
}

async function ejecutarCiclo() {
    const config = await AgenteConfig.findOne();
    if (!config || !config.activo) return;

    const horaActual = parseInt(new Date().toLocaleString('en-US', { timeZone: 'America/Bogota', hour: 'numeric', hour12: false }));
    if (horaActual < config.horario_inicio || horaActual >= config.horario_fin) return;

    console.log(`[Agente] Iniciando ciclo - ${new Date().toLocaleString('es-CO')}`);

    // 1. Leads nuevos que nunca han sido contactados
    const leadsNuevos = await Lead.findAll({
        where: { estado: 'nuevo', agente_activo: true, intentos_contacto: 0 }
    });
    for (const lead of leadsNuevos) {
        try {
            await procesarLead(lead, 'Lead nuevo, primer contacto');
            await new Promise(r => setTimeout(r, 2000));
        } catch (e) {
            console.error(`[Agente] Error procesando lead ${lead.id}:`, e.message);
            await AgentActividad.create({ lead_id: lead.id, tipo: 'error', canal: 'sistema', mensaje: `Error al procesar: ${e.message}` }).catch(() => {});
        }
    }

    // 2. Leads contactados sin respuesta → seguimiento 1 (después de N días)
    const leadsSeguimiento1 = await Lead.findAll({
        where: {
            estado: 'contactado',
            agente_activo: true,
            intentos_contacto: 1,
            ultimo_contacto: { [Op.lt]: diasAtras(config.dias_seguimiento_1) }
        }
    });
    for (const lead of leadsSeguimiento1) {
        try {
            await procesarLead(lead, `Sin respuesta después de ${config.dias_seguimiento_1} días. Primer seguimiento.`);
            await new Promise(r => setTimeout(r, 2000));
        } catch (e) {
            console.error(`[Agente] Error seguimiento 1 lead ${lead.id}:`, e.message);
            await AgentActividad.create({ lead_id: lead.id, tipo: 'error', canal: 'sistema', mensaje: `Error seguimiento 1: ${e.message}` }).catch(() => {});
        }
    }

    // 3. Leads en seguimiento 2 (después de más días)
    const leadsSeguimiento2 = await Lead.findAll({
        where: {
            estado: 'contactado',
            agente_activo: true,
            intentos_contacto: 2,
            ultimo_contacto: { [Op.lt]: diasAtras(config.dias_seguimiento_2) }
        }
    });
    for (const lead of leadsSeguimiento2) {
        try {
            await procesarLead(lead, `Sin respuesta después de ${config.dias_seguimiento_2} días. Segundo seguimiento, último intento.`);
            await new Promise(r => setTimeout(r, 2000));
        } catch (e) {
            console.error(`[Agente] Error seguimiento 2 lead ${lead.id}:`, e.message);
            await AgentActividad.create({ lead_id: lead.id, tipo: 'error', canal: 'sistema', mensaje: `Error seguimiento 2: ${e.message}` }).catch(() => {});
        }
    }

    // 4. Archivar leads con demasiados intentos sin respuesta
    await Lead.update(
        { estado: 'sin_respuesta', agente_activo: false },
        {
            where: {
                estado: 'contactado',
                agente_activo: true,
                intentos_contacto: { [Op.gte]: config.max_intentos },
                ultimo_contacto: { [Op.lt]: diasAtras(config.dias_seguimiento_2 + 1) }
            }
        }
    );

    console.log(`[Agente] Ciclo completado`);
}

function iniciarScheduler() {
    // Corre cada hora
    cron.schedule('0 * * * *', ejecutarCiclo);
    console.log('✅ Agente de ventas scheduler iniciado (cada hora)');
}

module.exports = { iniciarScheduler, ejecutarCiclo };
