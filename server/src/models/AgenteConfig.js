const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Tabla de una sola fila con la configuracion del agente
const AgenteConfig = sequelize.define('AgenteConfig', {
    activo:             { type: DataTypes.BOOLEAN, defaultValue: false },
    nombre_agente:      { type: DataTypes.STRING(100), defaultValue: 'Asistente de ventas AI Company' },
    nombre_empresa:     { type: DataTypes.STRING(150), defaultValue: 'AI Company' },
    descripcion_saas:   { type: DataTypes.TEXT }, // que hace tu SaaS
    calendly_link:      { type: DataTypes.STRING(300) },
    dias_seguimiento_1: { type: DataTypes.INTEGER, defaultValue: 2 },
    dias_seguimiento_2: { type: DataTypes.INTEGER, defaultValue: 5 },
    max_intentos:       { type: DataTypes.INTEGER, defaultValue: 3 },
    horario_inicio:     { type: DataTypes.INTEGER, defaultValue: 8 },  // hora del dia (8 = 8am)
    horario_fin:        { type: DataTypes.INTEGER, defaultValue: 18 }, // hora del dia (18 = 6pm)
}, { tableName: 'agente_config', timestamps: false });

module.exports = AgenteConfig;
