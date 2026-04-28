const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

module.exports = sequelize.define('Evento', {
    titulo:       { type: DataTypes.STRING(200), allowNull: false },
    descripcion:  { type: DataTypes.TEXT },
    fecha_inicio: { type: DataTypes.DATE, allowNull: false },
    fecha_fin:    { type: DataTypes.DATE },
    todo_el_dia:  { type: DataTypes.BOOLEAN, defaultValue: false },
    color:        { type: DataTypes.STRING(20), defaultValue: '#6366f1' },
    recordatorio: { type: DataTypes.BOOLEAN, defaultValue: false },
    telegram_enviado: { type: DataTypes.BOOLEAN, defaultValue: false },
    participantes: { type: DataTypes.STRING(500) },
    link:         { type: DataTypes.STRING(500) }
}, { tableName: 'eventos', timestamps: true });
