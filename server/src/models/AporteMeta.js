const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const AporteMeta = sequelize.define('AporteMeta', {
    meta_id: { type: DataTypes.INTEGER, allowNull: false },
    monto:   { type: DataTypes.DECIMAL(12, 2), allowNull: false },
    fecha:   { type: DataTypes.DATEONLY, allowNull: false },
    notas:   { type: DataTypes.TEXT }
}, { tableName: 'aportes_meta', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = AporteMeta;
