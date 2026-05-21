const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Ticket = sequelize.define('Ticket', {
    cliente_id: { type: DataTypes.INTEGER, allowNull: false },
    asunto:     { type: DataTypes.STRING(200), allowNull: false },
    mensaje:    { type: DataTypes.TEXT, allowNull: false },
    estado:     { type: DataTypes.ENUM('abierto','en_proceso','cerrado'), defaultValue: 'abierto' },
    respuesta:  { type: DataTypes.TEXT },
    respondido_at: { type: DataTypes.DATE }
}, { tableName: 'tickets', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at' });

module.exports = Ticket;
