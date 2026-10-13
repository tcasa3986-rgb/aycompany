const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Un hábito que Cristian quiere sostener (o cortar).
//
// Dos decisiones de diseño que importan:
//
// 1. `dias` guarda en qué días de la semana aplica, como "1,2,3,4,5". Sin esto,
//    un hábito de lunes a viernes aparecería roto todos los sábados y la racha
//    no significaría nada. Una racha que miente deja de motivar.
//
// 2. `tipo` separa "hacer" de "no hacer". En uno marcar es ganar; en el otro
//    ganar es terminar el día sin marcar. Se cuentan al revés.
const Habito = sequelize.define('Habito', {
    nombre:      { type: DataTypes.STRING(120), allowNull: false },
    detalle:     { type: DataTypes.STRING(200) },

    // 'hacer' = sumar (entrenar, leer). 'evitar' = no caer (no fumar, no gastar de más).
    tipo:        { type: DataTypes.ENUM('hacer', 'evitar'), defaultValue: 'hacer' },

    // Para la pantalla: un emoji y un color. Nada de catálogos cerrados.
    icono:       { type: DataTypes.STRING(8),  defaultValue: '✅' },
    color:       { type: DataTypes.STRING(9),  defaultValue: '#2563EB' },

    // Días de la semana en que aplica: 0=domingo … 6=sábado. Vacío = todos.
    dias:        { type: DataTypes.STRING(20), defaultValue: '0,1,2,3,4,5,6' },

    // Cuántas veces al día cuenta como cumplido (vasos de agua, por ejemplo).
    meta_diaria: { type: DataTypes.INTEGER, defaultValue: 1 },

    // Hora del recordatorio, "06:00". Vacío = no molesta.
    hora:        { type: DataTypes.STRING(5), allowNull: true },
    recordar:    { type: DataTypes.BOOLEAN, defaultValue: true },

    ambito:      { type: DataTypes.ENUM('personal', 'empresa'), defaultValue: 'personal' },
    orden:       { type: DataTypes.INTEGER, defaultValue: 0 },

    // Se archiva, no se borra: el historial de lo que ya cumpliste no se tira.
    activo:      { type: DataTypes.BOOLEAN, defaultValue: true },
    notas:       { type: DataTypes.TEXT },
}, {
    tableName: 'habitos', timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at',
    indexes: [{ fields: ['activo'] }],
});

/** ¿Este hábito aplica el día de la semana dado? (0=domingo) */
Habito.prototype.aplicaEn = function (diaSemana) {
    const dias = String(this.dias || '').split(',').map(s => s.trim()).filter(Boolean);
    if (dias.length === 0) return true;
    return dias.includes(String(diaSemana));
};

module.exports = Habito;
