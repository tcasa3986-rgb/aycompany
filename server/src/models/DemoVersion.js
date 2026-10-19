const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Respaldo antes de reemplazar una demo. No altera la tabla de prospectos.
module.exports = sequelize.define('DemoVersion', {
    negocio_id: { type: DataTypes.INTEGER, allowNull: false },
    html: { type: DataTypes.TEXT('long'), allowNull: false },
    generada_en: { type: DataTypes.DATE, allowNull: true },
}, { tableName: 'demo_versiones', timestamps: true, updatedAt: false,
    createdAt: 'created_at', indexes: [{ fields: ['negocio_id', 'created_at'] }] });
