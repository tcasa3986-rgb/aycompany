const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

module.exports = sequelize.define('IdeaContenido', {
    titulo:      { type: DataTypes.STRING(200), allowNull: false },
    descripcion: { type: DataTypes.TEXT },
    canal:       { type: DataTypes.STRING(100) },
    formato:     { type: DataTypes.STRING(100) },
    estado:      { type: DataTypes.ENUM('idea', 'en_progreso', 'publicado', 'descartado'), defaultValue: 'idea' },
    fecha_publicacion: { type: DataTypes.DATEONLY }
}, { tableName: 'ideas_contenido', timestamps: true });
