const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Pago = sequelize.define('Pago', {
    licencia_id:  { type: DataTypes.INTEGER, allowNull: false },
    cliente_id:   { type: DataTypes.INTEGER, allowNull: false },
    monto:        { type: DataTypes.DECIMAL(10, 2), allowNull: false },
    fecha_pago:   { type: DataTypes.DATEONLY, allowNull: false },
    metodo_pago:  { type: DataTypes.ENUM('efectivo', 'transferencia', 'tarjeta', 'nequi', 'mercadopago', 'otro'), defaultValue: 'efectivo' },
    meses:        { type: DataTypes.INTEGER, defaultValue: 1 },
    notas:        { type: DataTypes.TEXT },
    // id del pago en la pasarela. UNIQUE = el webhook es idempotente aunque
    // MercadoPago entregue el mismo evento dos veces a la vez.
    referencia_externa: { type: DataTypes.STRING(120), allowNull: true, unique: true }
}, { tableName: 'pagos', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = Pago;
