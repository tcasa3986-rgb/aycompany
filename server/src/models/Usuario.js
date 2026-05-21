const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Usuario = sequelize.define('Usuario', {
    nombre:           { type: DataTypes.STRING(100), allowNull: false },
    email:            { type: DataTypes.STRING(100), allowNull: false, unique: true },
    password:         { type: DataTypes.STRING(255), allowNull: false },
    rol:              { type: DataTypes.ENUM('admin', 'vendedor', 'soporte'), defaultValue: 'admin' },
    empresa_id:       { type: DataTypes.INTEGER, allowNull: true },
    telefono:         { type: DataTypes.STRING(20),  allowNull: true },
    ciudad:           { type: DataTypes.STRING(100), allowNull: true },
    activo:           { type: DataTypes.BOOLEAN, defaultValue: true },
    referido_por:     { type: DataTypes.INTEGER, allowNull: true },
    codigo_referido:  { type: DataTypes.STRING(20), unique: true, allowNull: true },
}, { tableName: 'usuarios', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = Usuario;
