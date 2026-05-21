const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Producto = sequelize.define('Producto', {
    nombre:             { type: DataTypes.STRING(100), allowNull: false },
    descripcion:        { type: DataTypes.TEXT },
    descripcion_venta:  { type: DataTypes.TEXT },
    precio_mensual:     { type: DataTypes.DECIMAL(10, 2), defaultValue: 250000 },
    categoria:          { type: DataTypes.STRING(60), defaultValue: 'Sistema' },
    demo_url:           { type: DataTypes.STRING(300) },
    imagen_url:         { type: DataTypes.STRING(300) },
    visible_vendedor:   { type: DataTypes.BOOLEAN, defaultValue: true },
    activo:             { type: DataTypes.BOOLEAN, defaultValue: true }
}, { tableName: 'productos', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = Producto;
