const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Tarea = sequelize.define('Tarea', {
    proyecto_id:  { type: DataTypes.INTEGER, allowNull: false },
    titulo:       { type: DataTypes.STRING(300), allowNull: false },
    descripcion:  { type: DataTypes.TEXT },
    estado:       { type: DataTypes.ENUM('pendiente','en_progreso','completado','bloqueado'), defaultValue: 'pendiente' },
    prioridad:    { type: DataTypes.ENUM('baja','media','alta','critica'), defaultValue: 'media' },
    responsable:  { type: DataTypes.STRING(100) },
    fecha_limite: { type: DataTypes.DATEONLY },
    orden:        { type: DataTypes.INTEGER, defaultValue: 0 }
}, { tableName: 'tareas', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at' });

module.exports = Tarea;
