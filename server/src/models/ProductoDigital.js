const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Un producto digital que se vende por WhatsApp.
//
// Lleva el guion de venta adentro a propósito: el dolor que resuelve y las
// objeciones típicas con su respuesta. Cuando el bot entre, esto es lo que va a
// leer — y mientras tanto le sirve a Cristian para no improvisar cada vez.
const ProductoDigital = sequelize.define('ProductoDigital', {
    nombre:      { type: DataTypes.STRING(160), allowNull: false },
    descripcion: { type: DataTypes.TEXT },
    precio:      { type: DataTypes.DECIMAL(12, 2), allowNull: false, defaultValue: 0 },

    // 'pdf' y 'plantilla' se despachan con marca de agua; 'acceso' va con
    // usuario y contraseña, que es lo único que se puede revocar de verdad.
    tipo:        { type: DataTypes.ENUM('pdf', 'plantilla', 'acceso', 'curso', 'otro'), defaultValue: 'pdf' },

    // Dónde vive el archivo. NUNCA se le manda este enlace al cliente: el
    // sistema lee de aquí y entrega una copia suya.
    origen_url:  { type: DataTypes.STRING(500) },

    // El guion de venta
    dolor:       { type: DataTypes.TEXT },   // qué problema resuelve, en las palabras del cliente
    objeciones:  { type: DataTypes.TEXT },   // "muy caro | ya lo tengo gratis" con su respuesta

    activo:      { type: DataTypes.BOOLEAN, defaultValue: true },
    orden:       { type: DataTypes.INTEGER, defaultValue: 0 },
    notas:       { type: DataTypes.TEXT },
}, {
    tableName: 'productos_digitales', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at',
});

module.exports = ProductoDigital;
