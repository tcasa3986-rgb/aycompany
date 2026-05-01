const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

module.exports = sequelize.define('MensajeSocial', {
    red:         { type: DataTypes.STRING(30), allowNull: false }, // facebook, instagram, whatsapp
    tipo:        { type: DataTypes.STRING(20), allowNull: false }, // mensaje, comentario
    remitente:   { type: DataTypes.STRING(200) },
    remitente_id: { type: DataTypes.STRING(200) },
    contenido:   { type: DataTypes.TEXT },
    post_id:     { type: DataTypes.STRING(200) },
    mensaje_id:  { type: DataTypes.STRING(200) },
    leido:       { type: DataTypes.BOOLEAN, defaultValue: false },
    respondido:  { type: DataTypes.BOOLEAN, defaultValue: false },
    respuesta:   { type: DataTypes.TEXT },
    etiqueta:    { type: DataTypes.STRING(100) },
    seguimiento_enviado:    { type: DataTypes.BOOLEAN, defaultValue: false },
    seguimiento_enviado_at: { type: DataTypes.DATE },
    segundo_seguimiento:    { type: DataTypes.BOOLEAN, defaultValue: false },
    fecha_red:   { type: DataTypes.DATE },
    raw:         { type: DataTypes.TEXT }
}, { tableName: 'mensajes_social', timestamps: true });
