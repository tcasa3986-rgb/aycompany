const Usuario  = require('./Usuario');
const Cliente  = require('./Cliente');
const Producto = require('./Producto');
const Licencia = require('./Licencia');
const Pago     = require('./Pago');

Cliente.hasMany(Licencia,  { foreignKey: 'cliente_id',  as: 'licencias' });
Producto.hasMany(Licencia, { foreignKey: 'producto_id', as: 'licencias' });
Licencia.belongsTo(Cliente,  { foreignKey: 'cliente_id',  as: 'cliente' });
Licencia.belongsTo(Producto, { foreignKey: 'producto_id', as: 'producto' });

Cliente.hasMany(Pago,  { foreignKey: 'cliente_id',  as: 'pagos' });
Licencia.hasMany(Pago, { foreignKey: 'licencia_id', as: 'pagos' });
Pago.belongsTo(Cliente,  { foreignKey: 'cliente_id',  as: 'cliente' });
Pago.belongsTo(Licencia, { foreignKey: 'licencia_id', as: 'licencia' });

module.exports = { Usuario, Cliente, Producto, Licencia, Pago };
