const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Un día de un hábito. Una fila por hábito y por fecha, nunca dos: el índice
// único lo garantiza, porque si se marca dos veces desde el celular y desde el
// widget al tiempo, la racha empezaría a contar días que no existen.
const HabitoRegistro = sequelize.define('HabitoRegistro', {
    habito_id: { type: DataTypes.INTEGER, allowNull: false },
    fecha:     { type: DataTypes.DATEONLY, allowNull: false },

    // Cuántas veces se hizo ese día. Para hábitos de meta_diaria = 1 es 0 o 1.
    veces:     { type: DataTypes.INTEGER, defaultValue: 1 },

    // Para los de tipo 'evitar': true = caí ese día.
    cai:       { type: DataTypes.BOOLEAN, defaultValue: false },

    nota:      { type: DataTypes.STRING(200) },
}, {
    tableName: 'habito_registros', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at',
    indexes: [
        { unique: true, fields: ['habito_id', 'fecha'], name: 'habito_fecha_unico' },
        { fields: ['fecha'] },
    ],
});

module.exports = HabitoRegistro;
