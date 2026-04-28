const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

module.exports = sequelize.define('MetaMarketing', {
    plataforma:   { type: DataTypes.STRING(50), allowNull: false },
    metrica:      { type: DataTypes.STRING(50), allowNull: false }, // seguidores, alcance, etc
    valor_meta:   { type: DataTypes.INTEGER, allowNull: false },
    valor_actual: { type: DataTypes.INTEGER, defaultValue: 0 },
    fecha_limite: { type: DataTypes.DATEONLY },
    descripcion:  { type: DataTypes.STRING(255) },
    completada:   { type: DataTypes.BOOLEAN, defaultValue: false }
}, { tableName: 'metas_marketing', timestamps: true });
