const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Licencia = sequelize.define('Licencia', {
    cliente_id:        { type: DataTypes.INTEGER, allowNull: false },
    producto_id:       { type: DataTypes.INTEGER, allowNull: false },
    license_key:       { type: DataTypes.STRING(36), allowNull: false, unique: true },
    activo:            { type: DataTypes.BOOLEAN, defaultValue: true },
    fecha_inicio:      { type: DataTypes.DATEONLY, allowNull: false },
    fecha_vencimiento: { type: DataTypes.DATEONLY, allowNull: false },
    last_check:          { type: DataTypes.DATE },
    mp_subscription_id:  { type: DataTypes.STRING(100) },
    suscripcion_activa:  { type: DataTypes.BOOLEAN, defaultValue: false },
    // Ciclo de cobro (ver utils/licenciaCiclo.js)
    dia_corte:           { type: DataTypes.INTEGER, allowNull: true },          // día del mes en que vence el pago; null = sin ciclo fijo
    dias_gracia:         { type: DataTypes.INTEGER, defaultValue: 0 },          // días después del vencimiento antes de bloquear
    bloquear:            { type: DataTypes.BOOLEAN, defaultValue: true },       // false = nunca bloquea, solo avisa
    precio_mensual:      { type: DataTypes.DECIMAL(10, 2), allowNull: true },   // override del precio del producto
    notas:               { type: DataTypes.TEXT }
}, { tableName: 'licencias', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = Licencia;
