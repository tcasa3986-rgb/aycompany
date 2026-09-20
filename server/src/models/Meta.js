const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Metas de ahorro/compra. `tipo` separa lo que es gasto de lo que compra un
// activo que después genera plata (las motos), porque no se priorizan igual.
const Meta = sequelize.define('Meta', {
    nombre:        { type: DataTypes.STRING(120), allowNull: false },
    descripcion:   { type: DataTypes.TEXT },
    monto_objetivo:{ type: DataTypes.DECIMAL(12, 2), allowNull: false },
    tipo:          { type: DataTypes.ENUM('activo', 'herramienta', 'educacion', 'personal'), defaultValue: 'personal' },
    // Retorno esperado al mes, si la meta es un activo (una moto de alquiler).
    // Permite ordenar por lo que de verdad acelera todo lo demás.
    retorno_mensual:{ type: DataTypes.DECIMAL(12, 2), defaultValue: 0 },
    prioridad:     { type: DataTypes.INTEGER, defaultValue: 5 },
    fecha_objetivo:{ type: DataTypes.DATEONLY, allowNull: true },
    estado:        { type: DataTypes.ENUM('activa', 'lograda', 'pausada'), defaultValue: 'activa' },
    notas:         { type: DataTypes.TEXT }
}, { tableName: 'metas', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at' });

module.exports = Meta;
