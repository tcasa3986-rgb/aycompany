const Usuario  = require('./Usuario');
const Empresa  = require('./Empresa');
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
const Configuracion  = require('./Configuracion');
const Proyecto       = require('./Proyecto');
const Tarea          = require('./Tarea');
const Contrato       = require('./Contrato');

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

Empresa.hasMany(Usuario,  { foreignKey: 'empresa_id', as: 'usuarios' });
Usuario.belongsTo(Empresa,  { foreignKey: 'empresa_id', as: 'empresa' });
Empresa.hasMany(Cliente,  { foreignKey: 'empresa_id', as: 'clientes' });
Cliente.belongsTo(Empresa,  { foreignKey: 'empresa_id', as: 'empresa' });

Cliente.hasMany(Ticket, { foreignKey: 'cliente_id', as: 'tickets' });
Ticket.belongsTo(Cliente, { foreignKey: 'cliente_id', as: 'cliente' });

Cliente.hasMany(Proyecto,  { foreignKey: 'cliente_id', as: 'proyectos' });
Proyecto.belongsTo(Cliente, { foreignKey: 'cliente_id', as: 'cliente' });
Proyecto.hasMany(Tarea,    { foreignKey: 'proyecto_id', as: 'tareas' });
Tarea.belongsTo(Proyecto,  { foreignKey: 'proyecto_id', as: 'proyecto' });

Cliente.hasMany(Contrato,  { foreignKey: 'cliente_id', as: 'contratos' });
Contrato.belongsTo(Cliente, { foreignKey: 'cliente_id', as: 'cliente' });

module.exports = { Usuario, Empresa, Cliente, Producto, Licencia, Pago, Factura, EstrategiaMarketing, Reunion, IdeaContenido, MetricaMarketing, MetaMarketing, Evento, MensajeSocial, Lead, AgentActividad, AgenteConfig, Ticket, Configuracion, Proyecto, Tarea, Contrato };
