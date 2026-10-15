const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Una conversación que puede terminar en venta. ES el embudo: cada fila es una
// persona que escribió, y `estado` dice dónde se quedó.
//
// Por qué una sola tabla y no "contacto" + "venta" aparte: para la semana 1
// sobra, y una tabla que se llena es mejor que dos que se quedan vacías. Si
// aparece gente que compra varias veces, se parte.
const VentaDigital = sequelize.define('VentaDigital', {
    // Quién
    telefono: { type: DataTypes.STRING(25), allowNull: false },
    nombre:   { type: DataTypes.STRING(120) },
    email:    { type: DataTypes.STRING(200) },   // a dónde se entrega el producto

    // Qué quiere (puede estar vacío al principio de la conversación)
    producto_id: { type: DataTypes.INTEGER, allowNull: true },

    // Dónde se quedó. El orden importa: es el embudo.
    //   escribio   → llegó del anuncio y saludó
    //   cotizado   → ya sabe el precio
    //   comprobante→ mandó el soporte de pago, falta confirmarlo
    //   pagado     → Cristian confirmó la plata
    //   despachado → ya recibió el producto
    //   perdido    → se cayó (con motivo)
    estado: {
        type: DataTypes.ENUM('escribio', 'cotizado', 'comprobante', 'pagado', 'despachado', 'perdido'),
        defaultValue: 'escribio',
    },
    // Lo que mide qué tan cerca está de comprar.
    temperatura: { type: DataTypes.ENUM('frio', 'tibio', 'caliente'), defaultValue: 'tibio' },

    // El motivo de pérdida es lo que después dice si el problema es el precio,
    // el producto o el anuncio. Sin esto no se aprende nada de los que no compran.
    motivo_perdida: { type: DataTypes.STRING(200) },

    // Plata
    monto:        { type: DataTypes.DECIMAL(12, 2), defaultValue: 0 },
    metodo:       { type: DataTypes.ENUM('nequi', 'breb', 'bancolombia', 'efectivo', 'otro'), defaultValue: 'nequi' },
    comprobante:  { type: DataTypes.TEXT },   // lo que mandó el cliente (texto o enlace)
    confirmado_en:{ type: DataTypes.DATE },

    // De qué anuncio vino. Sin esto, pautar es a ciegas: no se sabe cuánto
    // cuesta traer un cliente que SÍ paga.
    campana: { type: DataTypes.STRING(120) },
    utm:     { type: DataTypes.STRING(200) },

    // Rastro de la entrega
    despachado_en: { type: DataTypes.DATE },
    enlace_entrega:{ type: DataTypes.STRING(500) },

    // Para que el movimiento en Finanzas no se duplique si se confirma dos veces.
    movimiento_id: { type: DataTypes.INTEGER },

    ultimo_contacto: { type: DataTypes.DATE },
    notas:           { type: DataTypes.TEXT },
}, {
    tableName: 'ventas_digitales', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at',
    indexes: [{ fields: ['estado'] }, { fields: ['telefono'] }],
});

module.exports = VentaDigital;
