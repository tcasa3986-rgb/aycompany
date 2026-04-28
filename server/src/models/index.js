const Usuario  = require('./Usuario');
const Cliente  = require('./Cliente');
const Producto = require('./Producto');
const Licencia = require('./Licencia');
const Pago     = require('./Pago');
const Factura  = require('./Factura');
const EstrategiaMarketing = require('./EstrategiaMarketing');
const Reunion             = require('./Reunion');
const IdeaContenido       = require('./IdeaContenido');
const MetricaMarketing    = require('./MetricaMarketing');
const MetaMarketing       = require('./MetaMarketing');
const Evento              = require('./Evento');

Cliente.hasMany(Licencia,  { foreignKey: 'cliente_id',  as: 'licencias' });
Producto.hasMany(Licencia, { foreignKey: 'producto_id', as: 'licencias' });
Licencia.belongsTo(Cliente,  { foreignKey: 'cliente_id',  as: 'cliente' });
Licencia.belongsTo(Producto, { foreignKey: 'producto_id', as: 'producto' });

Cliente.hasMany(Pago,  { foreignKey: 'cliente_id',  as: 'pagos' });
Licencia.hasMany(Pago, { foreignKey: 'licencia_id', as: 'pagos' });
Pago.belongsTo(Cliente,  { foreignKey: 'cliente_id',  as: 'cliente' });
Pago.belongsTo(Licencia, { foreignKey: 'licencia_id', as: 'licencia' });

Cliente.hasMany(Factura, { foreignKey: 'cliente_id', as: 'facturas' });
Pago.hasOne(Factura,     { foreignKey: 'pago_id',    as: 'factura' });
Factura.belongsTo(Cliente, { foreignKey: 'cliente_id', as: 'cliente' });
Factura.belongsTo(Pago,    { foreignKey: 'pago_id',    as: 'pago' });

module.exports = { Usuario, Cliente, Producto, Licencia, Pago, Factura, EstrategiaMarketing, Reunion, IdeaContenido, MetricaMarketing, MetaMarketing, Evento };
