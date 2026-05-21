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
const MensajeSocial       = require('./MensajeSocial');
const Lead           = require('./Lead');
const AgentActividad = require('./AgentActividad');
const AgenteConfig   = require('./AgenteConfig');
const Ticket         = require('./Ticket');

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

Lead.hasMany(AgentActividad, { foreignKey: 'lead_id', as: 'actividad' });
AgentActividad.belongsTo(Lead, { foreignKey: 'lead_id', as: 'lead' });

Cliente.hasMany(Ticket, { foreignKey: 'cliente_id', as: 'tickets' });
Ticket.belongsTo(Cliente, { foreignKey: 'cliente_id', as: 'cliente' });

module.exports = { Usuario, Cliente, Producto, Licencia, Pago, Factura, EstrategiaMarketing, Reunion, IdeaContenido, MetricaMarketing, MetaMarketing, Evento, MensajeSocial, Lead, AgentActividad, AgenteConfig, Ticket };
