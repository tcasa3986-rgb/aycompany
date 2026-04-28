const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

module.exports = sequelize.define('EstrategiaMarketing', {
    titulo:      { type: DataTypes.STRING(200), allowNull: false },
    canal:       { type: DataTypes.STRING(100) },
    objetivo:    { type: DataTypes.STRING(255) },
    descripcion: { type: DataTypes.TEXT },
    estado:      { type: DataTypes.ENUM('activa', 'pausada', 'completada'), defaultValue: 'activa' },
    fecha_inicio: { type: DataTypes.DATEONLY },
    fecha_fin:    { type: DataTypes.DATEONLY }
}, { tableName: 'estrategias_marketing', timestamps: true });
