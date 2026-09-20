const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Todo lo que hay que pagar cada mes en una fecha concreta: el Railway de cada
// sistema, los dominios y las APIs del negocio, y también el arriendo, los
// servicios o el gimnasio. Es lo que alimenta el aviso diario.
//
// La tabla se sigue llamando costos_servidor por compatibilidad, pero el modelo
// ya no es solo de infraestructura: `ambito` separa la plata del negocio de la
// personal, igual que en los movimientos.
const CostoServidor = sequelize.define('CostoServidor', {
    nombre:       { type: DataTypes.STRING(120), allowNull: false },   // "Railway — ASOERC", "Arriendo"
    proveedor:    { type: DataTypes.STRING(60),  defaultValue: 'Railway' },
    ambito:       { type: DataTypes.ENUM('empresa', 'personal'), defaultValue: 'empresa' },
    categoria:    { type: DataTypes.STRING(40),  defaultValue: 'hosting' },
    licencia_id:  { type: DataTypes.INTEGER, allowNull: true },
    monto:        { type: DataTypes.DECIMAL(12, 2), allowNull: false },
    moneda:       { type: DataTypes.ENUM('COP', 'USD'), defaultValue: 'USD' },
    dia_pago:     { type: DataTypes.INTEGER, allowNull: false },        // 1-31
    proximo_pago: { type: DataTypes.DATEONLY, allowNull: false },
    ultimo_pago:  { type: DataTypes.DATEONLY, allowNull: true },
    cuenta:       { type: DataTypes.STRING(120), allowNull: true },     // con qué cuenta/tarjeta se paga
    activo:       { type: DataTypes.BOOLEAN, defaultValue: true },
    notas:        { type: DataTypes.TEXT }
}, { tableName: 'costos_servidor', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at' });

// Categorías sugeridas por ámbito (la columna es libre, esto solo guía la UI)
CostoServidor.CATEGORIAS = {
    empresa:  ['hosting', 'dominio', 'apis_ia', 'herramientas', 'publicidad', 'otro'],
    personal: ['arriendo', 'servicios', 'celular', 'gimnasio', 'transporte', 'salud', 'educacion', 'suscripcion', 'otro'],
};

module.exports = CostoServidor;
