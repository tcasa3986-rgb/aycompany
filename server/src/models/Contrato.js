const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Contrato = sequelize.define('Contrato', {
    cliente_id:   { type: DataTypes.INTEGER, allowNull: false },
    titulo:       { type: DataTypes.STRING(200), allowNull: false },
    descripcion:  { type: DataTypes.TEXT },
    monto:        { type: DataTypes.DECIMAL(12,2) },
    moneda:       { type: DataTypes.STRING(10), defaultValue: 'COP' },
    fecha_inicio: { type: DataTypes.DATEONLY },
    fecha_fin:    { type: DataTypes.DATEONLY },
    estado:       { type: DataTypes.ENUM('borrador','enviado','firmado','vencido','cancelado'), defaultValue: 'borrador' },
    clausulas:    { type: DataTypes.TEXT },
    notas:        { type: DataTypes.TEXT }
}, { tableName: 'contratos', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at' });

module.exports = Contrato;
