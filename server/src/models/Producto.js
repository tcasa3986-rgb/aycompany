const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Producto = sequelize.define('Producto', {
    nombre:          { type: DataTypes.STRING(100), allowNull: false },
    descripcion:     { type: DataTypes.TEXT },
    precio_mensual:  { type: DataTypes.DECIMAL(10, 2), defaultValue: 0 },
    activo:          { type: DataTypes.BOOLEAN, defaultValue: true }
}, { tableName: 'productos', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = Producto;
