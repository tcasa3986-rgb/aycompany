const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Factura = sequelize.define('Factura', {
    numero:     { type: DataTypes.STRING(20), allowNull: false, unique: true },
    pago_id:    { type: DataTypes.INTEGER, allowNull: false },
    cliente_id: { type: DataTypes.INTEGER, allowNull: false },
    concepto:   { type: DataTypes.STRING(255), allowNull: false },
    monto:      { type: DataTypes.DECIMAL(10, 2), allowNull: false },
    metodo_pago:{ type: DataTypes.STRING(50), defaultValue: 'efectivo' },
    fecha:      { type: DataTypes.DATEONLY, allowNull: false }
}, { tableName: 'facturas', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = Factura;
