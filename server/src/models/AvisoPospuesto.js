const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Un aviso de fondo que Cristian ya vio y no quiere volver a ver por un rato.
//
// Por qué existe: la pantalla Hoy mezclaba lo que vence hoy con problemas
// estructurales ("Ferre Láser nunca te ha pagado", "no tienes medida la
// infraestructura"). Esos son ciertos, pero salían TODOS los días, y una lista
// que dice lo mismo siempre se deja de leer — con lo urgente adentro.
//
// `clave` identifica el aviso de forma estable (por ejemplo "contrato:7"), para
// que posponerlo no dependa del texto, que cambia.
const AvisoPospuesto = sequelize.define('AvisoPospuesto', {
    clave: { type: DataTypes.STRING(80), allowNull: false, unique: true },
    hasta: { type: DataTypes.DATEONLY, allowNull: false },
    motivo:{ type: DataTypes.STRING(200) },
}, {
    tableName: 'avisos_pospuestos', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at',
});

module.exports = AvisoPospuesto;
