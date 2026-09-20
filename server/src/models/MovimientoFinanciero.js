const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

// Todo lo que entra y sale, del negocio y de Cristian.
//
// `ambito` es la columna que permite separar la plata de la empresa de la
// personal sin llevar dos sistemas: hoy están mezcladas porque trabaja solo,
// pero cada movimiento queda marcado desde el principio. El puente entre las
// dos es la categoría 'sueldo' (egreso de empresa -> ingreso personal).
const MovimientoFinanciero = sequelize.define('MovimientoFinanciero', {
    tipo:        { type: DataTypes.ENUM('ingreso', 'egreso'), allowNull: false },
    ambito:      { type: DataTypes.ENUM('empresa', 'personal'), allowNull: false, defaultValue: 'empresa' },
    categoria:   { type: DataTypes.STRING(60), allowNull: false },
    concepto:    { type: DataTypes.STRING(200), allowNull: false },
    monto:       { type: DataTypes.DECIMAL(12, 2), allowNull: false },
    fecha:       { type: DataTypes.DATEONLY, allowNull: false },
    metodo_pago: { type: DataTypes.STRING(30), defaultValue: 'transferencia' },

    // De dónde salió, cuando el movimiento lo generó el propio sistema.
    // Sirve para no contar dos veces lo mismo y para rastrear el origen.
    origen:       { type: DataTypes.ENUM('manual', 'licencia', 'contrato', 'costo_servidor', 'sueldo', 'deuda'), defaultValue: 'manual' },
    referencia_id: { type: DataTypes.INTEGER, allowNull: true },   // id de la licencia/contrato/costo/deuda
    cliente_id:   { type: DataTypes.INTEGER, allowNull: true },

    // Un egreso de empresa con categoría 'sueldo' crea su espejo personal.
    // Este campo enlaza los dos para poder auditar el puente.
    espejo_id:    { type: DataTypes.INTEGER, allowNull: true },

    recurrente:  { type: DataTypes.BOOLEAN, defaultValue: false },  // gasto fijo mensual
    notas:       { type: DataTypes.TEXT }
}, {
    tableName: 'movimientos_financieros',
    timestamps: true, createdAt: 'created_at', updatedAt: 'updated_at',
    indexes: [{ fields: ['fecha'] }, { fields: ['tipo', 'ambito'] }]
});

// Categorías sugeridas (la columna es libre para no encerrar a nadie)
MovimientoFinanciero.CATEGORIAS = {
    empresa: {
        ingreso: ['mensualidad', 'cuota_contrato', 'anticipo_contrato', 'marketing', 'pagina_web', 'otro'],
        egreso:  ['hosting', 'apis_ia', 'dominios', 'herramientas', 'sueldo', 'impuestos', 'comisiones', 'publicidad', 'otro']
    },
    personal: {
        ingreso: ['sueldo', 'alquiler_moto', 'otro'],
        egreso:  ['arriendo', 'recibos', 'comida', 'transporte', 'celular', 'gimnasio', 'salud', 'educacion', 'deuda', 'ahorro_meta', 'otro']
    }
};

module.exports = MovimientoFinanciero;
