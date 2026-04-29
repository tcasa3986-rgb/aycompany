const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

module.exports = sequelize.define('Reunion', {
    titulo:        { type: DataTypes.STRING(200), allowNull: false },
    descripcion:   { type: DataTypes.TEXT },
    fecha:         { type: DataTypes.DATE, allowNull: false },
    duracion:      { type: DataTypes.INTEGER, defaultValue: 60 },
    participantes: { type: DataTypes.TEXT },
    link:          { type: DataTypes.STRING(500) },
    estado:        { type: DataTypes.ENUM('pendiente', 'completada', 'cancelada'), defaultValue: 'pendiente' },
    recordatorio_enviado: { type: DataTypes.BOOLEAN, defaultValue: false }
}, { tableName: 'reuniones', timestamps: true });
