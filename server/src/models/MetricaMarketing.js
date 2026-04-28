const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

module.exports = sequelize.define('MetricaMarketing', {
    plataforma:  { type: DataTypes.STRING(50), allowNull: false },
    fecha:       { type: DataTypes.DATEONLY, allowNull: false },
    seguidores:  { type: DataTypes.INTEGER, defaultValue: 0 },
    alcance:     { type: DataTypes.INTEGER, defaultValue: 0 },
    interacciones: { type: DataTypes.INTEGER, defaultValue: 0 },
    publicaciones: { type: DataTypes.INTEGER, defaultValue: 0 },
    notas:       { type: DataTypes.TEXT }
}, { tableName: 'metricas_marketing', timestamps: true });
