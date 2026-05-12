const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const AgentActividad = sequelize.define('AgentActividad', {
    lead_id:   { type: DataTypes.INTEGER, allowNull: false },
    tipo: {
        type: DataTypes.ENUM('mensaje_enviado','respuesta_recibida','reunion_propuesta','reunion_confirmada','seguimiento','decision_agente','error'),
        allowNull: false
    },
    canal:     { type: DataTypes.ENUM('whatsapp','email','sistema'), defaultValue: 'whatsapp' },
    mensaje:   { type: DataTypes.TEXT },   // contenido del mensaje o descripcion de la accion
    resultado: { type: DataTypes.TEXT },   // respuesta del lead o resultado de la accion
    tokens_usados: { type: DataTypes.INTEGER, defaultValue: 0 },
}, { tableName: 'agent_actividad', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = AgentActividad;
