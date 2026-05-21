const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Empresa = sequelize.define('Empresa', {
    nombre:          { type: DataTypes.STRING(200), allowNull: false },
    slug:            { type: DataTypes.STRING(100), unique: true },
    nit:             { type: DataTypes.STRING(30) },
    email:           { type: DataTypes.STRING(150) },
    telefono:        { type: DataTypes.STRING(30) },
    ciudad:          { type: DataTypes.STRING(100) },
    pais:            { type: DataTypes.STRING(50), defaultValue: 'Colombia' },
    logo_url:        { type: DataTypes.TEXT },
    color_primario:  { type: DataTypes.STRING(20), defaultValue: '#6366f1' },
    color_secundario:{ type: DataTypes.STRING(20), defaultValue: '#1e1b4b' },
    dominio_custom:  { type: DataTypes.STRING(200) },
    plan:            { type: DataTypes.ENUM('starter','professional','enterprise'), defaultValue: 'starter' },
    activa:          { type: DataTypes.BOOLEAN, defaultValue: true },
    max_clientes:    { type: DataTypes.INTEGER, defaultValue: 100 },
    max_usuarios:    { type: DataTypes.INTEGER, defaultValue: 5 },
    siigo_user:      { type: DataTypes.STRING(200) },
    siigo_key:       { type: DataTypes.TEXT },
    mp_token:        { type: DataTypes.TEXT },
    notas:           { type: DataTypes.TEXT }
}, {
    tableName:  'empresas',
    timestamps: true,
    createdAt:  'created_at',
    updatedAt:  'updated_at'
});

module.exports = Empresa;
