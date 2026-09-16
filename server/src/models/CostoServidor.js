const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Gasto recurrente de infraestructura (Railway, dominio, API...) atado opcionalmente
// a la licencia del sistema que lo consume. Sirve para avisar cuándo toca pagar y
// para calcular la rentabilidad real de cada cliente.
const CostoServidor = sequelize.define('CostoServidor', {
    nombre:       { type: DataTypes.STRING(120), allowNull: false },   // "Railway — ASOERC"
    proveedor:    { type: DataTypes.STRING(60),  defaultValue: 'Railway' },
    licencia_id:  { type: DataTypes.INTEGER, allowNull: true },
    monto:        { type: DataTypes.DECIMAL(12, 2), allowNull: false },
    moneda:       { type: DataTypes.ENUM('COP', 'USD'), defaultValue: 'USD' },
    dia_pago:     { type: DataTypes.INTEGER, allowNull: false },        // 1-31
    proximo_pago: { type: DataTypes.DATEONLY, allowNull: false },
    ultimo_pago:  { type: DataTypes.DATEONLY, allowNull: true },
    cuenta:       { type: DataTypes.STRING(120), allowNull: true },     // con qué cuenta/tarjeta se paga
    activo:       { type: DataTypes.BOOLEAN, defaultValue: true },
    notas:        { type: DataTypes.TEXT }
}, { tableName: 'costos_servidor', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at' });

module.exports = CostoServidor;
