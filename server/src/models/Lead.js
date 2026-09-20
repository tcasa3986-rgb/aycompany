const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Lead = sequelize.define('Lead', {
    nombre:     { type: DataTypes.STRING(150), allowNull: false },
    email:      { type: DataTypes.STRING(100) },
    telefono:   { type: DataTypes.STRING(20) },
    empresa:    { type: DataTypes.STRING(150) },
    fuente:     { type: DataTypes.STRING(80), defaultValue: 'manual' }, // manual, formulario, importacion
    estado: {
        type: DataTypes.ENUM('nuevo','contactado','respondio','interesado','reunion_agendada','reunion_realizada','cliente','sin_respuesta','descartado'),
        defaultValue: 'nuevo'
    },
    notas:              { type: DataTypes.TEXT },
    ultimo_contacto:    { type: DataTypes.DATE },
    intentos_contacto:  { type: DataTypes.INTEGER, defaultValue: 0 },
    link_reunion:       { type: DataTypes.STRING(300) }, // link Calendly generado/enviado
    fecha_reunion:      { type: DataTypes.DATE },
    agente_activo:      { type: DataTypes.BOOLEAN, defaultValue: true },
    vendedor_id:        { type: DataTypes.INTEGER, allowNull: true },
    sistema_interes:    { type: DataTypes.STRING(150) },

    // Economía del trato. Sin esto, un trato en negociación no se distinguía
    // de un contacto suelto, y la plata probable terminaba mezclada de memoria
    // con la confirmada.
    valor_unico:        { type: DataTypes.DECIMAL(12,2), defaultValue: 0 },  // pago único (construcción)
    valor_mensual:      { type: DataTypes.DECIMAL(12,2), defaultValue: 0 },  // mensualidad si se cierra
    probabilidad:       { type: DataTypes.INTEGER, defaultValue: 50 },       // 0-100, cuán probable es cerrarlo
    fecha_estimada_cierre: { type: DataTypes.DATEONLY, allowNull: true },
}, { tableName: 'leads', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at' });

module.exports = Lead;
