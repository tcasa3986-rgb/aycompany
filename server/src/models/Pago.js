const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Pago = sequelize.define('Pago', {
    licencia_id:  { type: DataTypes.INTEGER, allowNull: false },
    cliente_id:   { type: DataTypes.INTEGER, allowNull: false },
    monto:        { type: DataTypes.DECIMAL(10, 2), allowNull: false },
    fecha_pago:   { type: DataTypes.DATEONLY, allowNull: false },
    metodo_pago:  { type: DataTypes.ENUM('efectivo', 'transferencia', 'tarjeta', 'otro'), defaultValue: 'efectivo' },
    meses:        { type: DataTypes.INTEGER, defaultValue: 1 },
    notas:        { type: DataTypes.TEXT }
}, { tableName: 'pagos', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = Pago;
