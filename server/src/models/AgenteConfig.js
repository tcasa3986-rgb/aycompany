const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const DESCRIPCION_DEFAULT = `AI Company es una empresa de desarrollo de software a medida. Creamos sistemas personalizados para negocios, automatizaciones de procesos y somos agencia de marketing digital. Servicios: páginas web, SEO, pauta en Facebook/Instagram/TikTok/Google Ads, estrategias de lanzamiento y posicionamiento en buscadores.`;

const AgenteConfig = sequelize.define('AgenteConfig', {
    activo:             { type: DataTypes.BOOLEAN, defaultValue: false },
    nombre_agente:      { type: DataTypes.STRING(100), defaultValue: 'Cristian' },
    nombre_empresa:     { type: DataTypes.STRING(150), defaultValue: 'AI Company' },
    descripcion_saas:   { type: DataTypes.TEXT, defaultValue: DESCRIPCION_DEFAULT },
    dias_seguimiento_1: { type: DataTypes.INTEGER, defaultValue: 2 },
    dias_seguimiento_2: { type: DataTypes.INTEGER, defaultValue: 5 },
    max_intentos:       { type: DataTypes.INTEGER, defaultValue: 3 },
    horario_inicio:     { type: DataTypes.INTEGER, defaultValue: 8 },
    horario_fin:        { type: DataTypes.INTEGER, defaultValue: 18 },
    gmail_user:         { type: DataTypes.STRING(200), allowNull: true },
    gmail_app_password: { type: DataTypes.STRING(200), allowNull: true },
}, { tableName: 'agente_config', timestamps: false });

module.exports = AgenteConfig;
