const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Licencia = sequelize.define('Licencia', {
    cliente_id:        { type: DataTypes.INTEGER, allowNull: false },
    producto_id:       { type: DataTypes.INTEGER, allowNull: false },
    license_key:       { type: DataTypes.STRING(36), allowNull: false, unique: true },
    activo:            { type: DataTypes.BOOLEAN, defaultValue: true },
    fecha_inicio:      { type: DataTypes.DATEONLY, allowNull: false },
    fecha_vencimiento: { type: DataTypes.DATEONLY, allowNull: false },
    last_check:        { type: DataTypes.DATE }
}, { tableName: 'licencias', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = Licencia;
