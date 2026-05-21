const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Configuracion = sequelize.define('Configuracion', {
    clave:    { type: DataTypes.STRING(100), allowNull: false, unique: true },
    valor:    { type: DataTypes.TEXT },
    tipo:     { type: DataTypes.ENUM('texto','numero','booleano','url','color'), defaultValue: 'texto' },
    etiqueta: { type: DataTypes.STRING(200) },
    grupo:    { type: DataTypes.STRING(50), defaultValue: 'general' }
}, { tableName: 'configuraciones', timestamps: false });

module.exports = Configuracion;
