const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const AbonoDeuda = sequelize.define('AbonoDeuda', {
    deuda_id:    { type: DataTypes.INTEGER, allowNull: false },
    monto:       { type: DataTypes.DECIMAL(12, 2), allowNull: false },
    fecha:       { type: DataTypes.DATEONLY, allowNull: false },
    metodo_pago: { type: DataTypes.STRING(30), defaultValue: 'transferencia' },
    notas:       { type: DataTypes.TEXT }
}, { tableName: 'abonos_deuda', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = AbonoDeuda;
