const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Un negocio que Google dice que NO tiene página web, y que puede ser cliente.
//
// La columna que decide todo es `score`: antes de gastar un token en generarle
// una demo, hay que saber si ese negocio puede pagarla. Veinte demos bien
// escogidas cierran más que quinientas genéricas.
const NegocioSinWeb = sequelize.define('NegocioSinWeb', {
    place_id:   { type: DataTypes.STRING(120), allowNull: false, unique: true },
    nombre:     { type: DataTypes.STRING(200), allowNull: false },
    direccion:  { type: DataTypes.STRING(300) },
    telefono:   { type: DataTypes.STRING(40) },
    ciudad:     { type: DataTypes.STRING(80) },
    rubro:      { type: DataTypes.STRING(80) },
    categoria:  { type: DataTypes.STRING(120) },

    calificacion: { type: DataTypes.DECIMAL(2, 1), defaultValue: 0 },
    resenas:      { type: DataTypes.INTEGER, defaultValue: 0 },

    // Tiene Facebook o Instagram puesto como "web". Es el mejor cliente: le
    // importa estar en internet, solo que lo resolvió a medias.
    web_parcial: { type: DataTypes.BOOLEAN, defaultValue: false },
    red_social:  { type: DataTypes.STRING(300) },
    maps:        { type: DataTypes.STRING(300) },

    score:   { type: DataTypes.INTEGER, defaultValue: 0 },
    razones: { type: DataTypes.STRING(300) },

    // Dónde va en el proceso
    estado: {
        type: DataTypes.ENUM('encontrado', 'demo_generada', 'enviado', 'abrio', 'respondio', 'cliente', 'descartado'),
        defaultValue: 'encontrado',
    },
    motivo_descarte: { type: DataTypes.STRING(200) },

    // La demo. El enlace es único por negocio para poder saber quién la abrió:
    // un negocio que la abrió tres veces es un cliente caliente aunque no haya
    // contestado, y eso convierte el seguimiento en certeza en vez de lotería.
    demo_slug:     { type: DataTypes.STRING(80), unique: true },
    demo_html:     { type: DataTypes.TEXT('long') },
    demo_generada_en: { type: DataTypes.DATE },

    visitas:        { type: DataTypes.INTEGER, defaultValue: 0 },
    primera_visita: { type: DataTypes.DATE },
    ultima_visita:  { type: DataTypes.DATE },

    contactado_en: { type: DataTypes.DATE },
    notas:         { type: DataTypes.TEXT },
}, {
    tableName: 'negocios_sin_web', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at',
    indexes: [{ fields: ['estado'] }, { fields: ['score'] }, { fields: ['ciudad'] }],
});

module.exports = NegocioSinWeb;
