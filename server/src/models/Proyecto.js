const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Proyecto = sequelize.define('Proyecto', {
    cliente_id:   { type: DataTypes.INTEGER },
    nombre:       { type: DataTypes.STRING(200), allowNull: false },
    descripcion:  { type: DataTypes.TEXT },
    estado:       { type: DataTypes.ENUM('planeacion','en_curso','completado','pausado','cancelado'), defaultValue: 'planeacion' },
    fecha_inicio: { type: DataTypes.DATEONLY },
    fecha_fin:    { type: DataTypes.DATEONLY },
    presupuesto:  { type: DataTypes.DECIMAL(12,2) },
    facturado:    { type: DataTypes.DECIMAL(12,2), defaultValue: 0 },
    notas:        { type: DataTypes.TEXT },
    color:        { type: DataTypes.STRING(20), defaultValue: '#6366f1' }
}, { tableName: 'proyectos', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at' });

module.exports = Proyecto;
