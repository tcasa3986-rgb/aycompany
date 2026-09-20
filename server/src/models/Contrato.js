const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Contrato = sequelize.define('Contrato', {
    cliente_id:   { type: DataTypes.INTEGER, allowNull: false },
    titulo:       { type: DataTypes.STRING(200), allowNull: false },
    descripcion:  { type: DataTypes.TEXT },
    monto:        { type: DataTypes.DECIMAL(12,2) },          // valor total del contrato
    moneda:       { type: DataTypes.STRING(10), defaultValue: 'COP' },

    // Estructura de pago. Los contratos reales no se pagan de una: la mitad de
    // contado y el resto financiado a cuotas mensuales. Sin estos campos el
    // saldo por cobrar sólo vivía en la cabeza de Cristian.
    anticipo:        { type: DataTypes.DECIMAL(12,2), defaultValue: 0 },   // lo que ya entregaron al firmar
    cuota_mensual:   { type: DataTypes.DECIMAL(12,2), defaultValue: 0 },   // valor de cada cuota del saldo
    dia_cobro:       { type: DataTypes.INTEGER, allowNull: true },         // día del mes en que se cobra la cuota
    primera_cuota:   { type: DataTypes.DATEONLY, allowNull: true },        // cuándo arranca a correr el saldo
    mensualidad:     { type: DataTypes.DECIMAL(12,2), defaultValue: 0 },   // mantenimiento aparte del contrato
    tipo_servicio:   { type: DataTypes.ENUM('sistema','marketing','pagina_web','mixto','otro'), defaultValue: 'sistema' },
    fecha_inicio: { type: DataTypes.DATEONLY },
    fecha_fin:    { type: DataTypes.DATEONLY },
    estado:       { type: DataTypes.ENUM('borrador','enviado','firmado','vencido','cancelado'), defaultValue: 'borrador' },
    clausulas:    { type: DataTypes.TEXT },
    notas:        { type: DataTypes.TEXT }
}, { tableName: 'contratos', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at' });

module.exports = Contrato;
