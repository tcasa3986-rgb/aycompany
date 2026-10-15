const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Lo que Cristian se programa a sí mismo. Aparte de `Tarea`, que exige un
// proyecto y es para el trabajo de un sistema.
//
// La regla de esta tabla: **solo sale el día que toca**. Antes, la pantalla Hoy
// le mostraba todos los días las mismas cosas estructurales y dejó de leerla.
// Un pendiente con fecha de mañana no existe hoy.
const Pendiente = sequelize.define('Pendiente', {
    titulo:  { type: DataTypes.STRING(200), allowNull: false },
    detalle: { type: DataTypes.STRING(300) },

    // El día en que debe aparecer. Si ya pasó y sigue sin hacerse, sale como atrasado.
    fecha:   { type: DataTypes.DATEONLY, allowNull: false },
    hora:    { type: DataTypes.STRING(5) },

    urgencia: { type: DataTypes.ENUM('alta', 'media', 'baja'), defaultValue: 'media' },
    ambito:   { type: DataTypes.ENUM('personal', 'empresa'), defaultValue: 'empresa' },

    // Si se repite, al completarlo se crea el siguiente solo.
    repetir: { type: DataTypes.ENUM('no', 'diario', 'semanal', 'quincenal', 'mensual'), defaultValue: 'no' },

    hecho:        { type: DataTypes.BOOLEAN, defaultValue: false },
    fecha_hecho:  { type: DataTypes.DATEONLY },
    notas:        { type: DataTypes.TEXT },
}, {
    tableName: 'pendientes', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at',
    indexes: [{ fields: ['fecha', 'hecho'] }],
});

module.exports = Pendiente;
