const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Usuario = sequelize.define('Usuario', {
    nombre:   { type: DataTypes.STRING(100), allowNull: false },
    email:    { type: DataTypes.STRING(100), allowNull: false, unique: true },
    password: { type: DataTypes.STRING(255), allowNull: false },
    rol:      { type: DataTypes.ENUM('admin', 'vendedor', 'soporte'), defaultValue: 'admin' }
}, { tableName: 'usuarios', timestamps: true, createdAt: 'created_at', updatedAt: false });

module.exports = Usuario;
