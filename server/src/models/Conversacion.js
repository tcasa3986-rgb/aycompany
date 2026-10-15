const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// El hilo de WhatsApp con una persona. Portado del sistema viejo de productos
// digitales (junio 2026), que ya vendía con anuncios de Meta.
//
// `historial` es lo que hace que el bot no parezca un robot: antes de responder
// relee los últimos 15 mensajes. Sin esto contestaba como si fuera el primer
// mensaje, volvía a preguntar lo ya respondido y se perdían ventas.
const Conversacion = sequelize.define('Conversacion', {
    numero_wa:      { type: DataTypes.STRING(50), allowNull: false, unique: true },
    nombre_cliente: { type: DataTypes.STRING(200) },

    estado: {
        type: DataTypes.ENUM('nuevo', 'menu', 'viendo_producto', 'esperando_email', 'esperando_comprobante', 'completado'),
        defaultValue: 'nuevo',
    },

    producto_id:    { type: DataTypes.INTEGER },
    email_cliente:  { type: DataTypes.STRING(200) },
    ultimo_mensaje: { type: DataTypes.TEXT },

    // [{ rol: 'user'|'assistant', texto, en }]
    historial: { type: DataTypes.JSON, defaultValue: [] },

    // Cosas sueltas del trato: precio especial, de qué anuncio vino, banderas.
    notas: { type: DataTypes.JSON, defaultValue: {} },
}, {
    tableName: 'conversaciones', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at',
    indexes: [{ fields: ['estado'] }],
});

module.exports = Conversacion;
