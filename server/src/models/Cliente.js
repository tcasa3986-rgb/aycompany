const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Cliente = sequelize.define('Cliente', {
    nombre:        { type: DataTypes.STRING(150), allowNull: false },
    email:         { type: DataTypes.STRING(100) },
    telefono:      { type: DataTypes.STRING(20) },
    empresa:       { type: DataTypes.STRING(150) },
    direccion:     { type: DataTypes.TEXT },
    notas:         { type: DataTypes.TEXT },
    activo:        { type: DataTypes.BOOLEAN, defaultValue: true },
    token_portal:  { type: DataTypes.STRING(100), unique: true }
}, { tableName: 'clientes', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = Cliente;
