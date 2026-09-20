const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Deudas de Cristian. El saldo NO se escribe a mano: se calcula restando los
// abonos registrados, para que nunca haya dos versiones de la verdad.
const Deuda = sequelize.define('Deuda', {
    acreedor:       { type: DataTypes.STRING(120), allowNull: false },
    concepto:       { type: DataTypes.STRING(200) },
    monto_original: { type: DataTypes.DECIMAL(12, 2), allowNull: false },
    tasa_mensual:   { type: DataTypes.DECIMAL(5, 2), defaultValue: 0 },   // % mensual; 0 = sin intereses
    cuota_pactada:  { type: DataTypes.DECIMAL(12, 2), allowNull: true },  // null = sin cuota fija
    dia_pago:       { type: DataTypes.INTEGER, allowNull: true },
    fecha_limite:   { type: DataTypes.DATEONLY, allowNull: true },
    ambito:         { type: DataTypes.ENUM('empresa', 'personal'), defaultValue: 'personal' },
    prioridad:      { type: DataTypes.INTEGER, defaultValue: 1 },         // 1 = primero
    estado:         { type: DataTypes.ENUM('activa', 'pagada'), defaultValue: 'activa' },
    notas:          { type: DataTypes.TEXT }
}, { tableName: 'deudas', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at' });

module.exports = Deuda;
