const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

// ─── Rol ──────────────────────────────────────────────────────
const Rol = sequelize.define('Rol', {
  id:          { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  nombre:      { type: DataTypes.STRING(60), allowNull: false },
  descripcion: { type: DataTypes.TEXT },
  permisos:    { type: DataTypes.JSON },
  activo:      { type: DataTypes.TINYINT(1), defaultValue: 1 },
  creado_en:   { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
}, { tableName: 'roles' });

// ─── Usuario ──────────────────────────────────────────────────
const Usuario = sequelize.define('Usuario', {
  id:            { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  rol_id:        { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  nombre:        { type: DataTypes.STRING(100), allowNull: false },
  apellido:      { type: DataTypes.STRING(100), allowNull: false },
  email:         { type: DataTypes.STRING(150), allowNull: false, unique: true },
  password_hash: { type: DataTypes.STRING(255), allowNull: false },
  telefono:      { type: DataTypes.STRING(20) },
  avatar_url:    { type: DataTypes.STRING(255) },
  activo:        { type: DataTypes.TINYINT(1), defaultValue: 1 },
  ultimo_login:  { type: DataTypes.DATE },
  creado_en:     { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
}, { tableName: 'usuarios' });

// ─── ConfiguracionGeneral ─────────────────────────────────────
const ConfiguracionGeneral = sequelize.define('ConfiguracionGeneral', {
  id:                  { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  empresa_nombre:      { type: DataTypes.STRING(150), defaultValue: 'Viaje 360 CRM' },
  documento_identidad: { type: DataTypes.STRING(50) },
  direccion:           { type: DataTypes.STRING(255) },
  telefono:            { type: DataTypes.STRING(50) },
  logo_url:            { type: DataTypes.STRING(255) },
  moneda_simbolo:      { type: DataTypes.STRING(10), defaultValue: '$' },
  impuesto_nombre:     { type: DataTypes.STRING(20), defaultValue: 'IGV' },
  impuesto_porcentaje: { type: DataTypes.DECIMAL(5,2), defaultValue: 18.00 },
}, { tableName: 'configuracion_general', timestamps: false });

// ─── FuenteOrigen ─────────────────────────────────────────────
const FuenteOrigen = sequelize.define('FuenteOrigen', {
  id:     { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  nombre: { type: DataTypes.STRING(80), allowNull: false },
}, { tableName: 'fuentes_origen' });

// ─── Etiqueta ─────────────────────────────────────────────────
const Etiqueta = sequelize.define('Etiqueta', {
  id:     { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  nombre: { type: DataTypes.STRING(60), allowNull: false },
  color:  { type: DataTypes.STRING(7), defaultValue: '#3B82F6' },
}, { tableName: 'etiquetas' });

// ─── Cliente ──────────────────────────────────────────────────
const Cliente = sequelize.define('Cliente', {
  id:              { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  fuente_id:       { type: DataTypes.INTEGER.UNSIGNED },
  agente_id:       { type: DataTypes.INTEGER.UNSIGNED },
  nombre:          { type: DataTypes.STRING(100), allowNull: false },
  apellido:        { type: DataTypes.STRING(100), allowNull: false },
  email:           { type: DataTypes.STRING(150), allowNull: false, unique: true },
  telefono:        { type: DataTypes.STRING(30) },
  telefono_alt:    { type: DataTypes.STRING(30) },
  fecha_nacimiento:{ type: DataTypes.DATEONLY },
  genero:          { type: DataTypes.ENUM('M','F','Otro') },
  documento_tipo:  { type: DataTypes.ENUM('DNI','Pasaporte','CE','RUC'), defaultValue: 'DNI' },
  documento_num:   { type: DataTypes.STRING(30) },
  pais:            { type: DataTypes.STRING(80) },
  ciudad:          { type: DataTypes.STRING(80) },
  direccion:       { type: DataTypes.TEXT },
  categoria:       { type: DataTypes.ENUM('Nuevo','Recurrente','VIP','Inactivo'), defaultValue: 'Nuevo' },
  notas:           { type: DataTypes.TEXT },
  activo:          { type: DataTypes.TINYINT(1), defaultValue: 1 },
  creado_en:       { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
  actualizado_en:  { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
}, { tableName: 'clientes' });

// ─── Interaccion ──────────────────────────────────────────────
const Interaccion = sequelize.define('Interaccion', {
  id:          { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  cliente_id:  { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  usuario_id:  { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  tipo:        { type: DataTypes.ENUM('Llamada','Email','WhatsApp','Reunion','Nota','Cotizacion','Seguimiento'), allowNull: false },
  descripcion: { type: DataTypes.TEXT, allowNull: false },
  adjunto_url: { type: DataTypes.STRING(255) },
  fecha:       { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
}, { tableName: 'interacciones' });

// ─── Pais ─────────────────────────────────────────────────────
const Pais = sequelize.define('Pais', {
  id:     { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  nombre: { type: DataTypes.STRING(80), allowNull: false },
  codigo: { type: DataTypes.CHAR(2), allowNull: false, unique: true },
  zona:   { type: DataTypes.STRING(60) },
}, { tableName: 'paises' });

// ─── Destino ──────────────────────────────────────────────────
const Destino = sequelize.define('Destino', {
  id:          { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  pais_id:     { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  nombre:      { type: DataTypes.STRING(120), allowNull: false },
  descripcion: { type: DataTypes.TEXT },
  imagen_url:  { type: DataTypes.STRING(255) },
  activo:      { type: DataTypes.TINYINT(1), defaultValue: 1 },
}, { tableName: 'destinos' });

// ─── CategoriaPaquete ─────────────────────────────────────────
const CategoriaPaquete = sequelize.define('CategoriaPaquete', {
  id:     { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  nombre: { type: DataTypes.STRING(80), allowNull: false },
}, { tableName: 'categorias_paquete' });

// ─── Paquete ──────────────────────────────────────────────────
const Paquete = sequelize.define('Paquete', {
  id:            { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  destino_id:    { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  categoria_id:  { type: DataTypes.INTEGER.UNSIGNED },
  nombre:        { type: DataTypes.STRING(150), allowNull: false },
  descripcion:   { type: DataTypes.TEXT },
  itinerario:    { type: DataTypes.TEXT('long') },
  duracion_dias: { type: DataTypes.SMALLINT.UNSIGNED },
  costo_neto:    { type: DataTypes.DECIMAL(10,2), defaultValue: 0 },
  precio_base:   { type: DataTypes.DECIMAL(10,2), allowNull: false },
  precio_adulto: { type: DataTypes.DECIMAL(10,2) },
  precio_nino:   { type: DataTypes.DECIMAL(10,2) },
  incluye:       { type: DataTypes.TEXT },
  no_incluye:    { type: DataTypes.TEXT },
  imagen_url:    { type: DataTypes.STRING(255) },
  disponible:    { type: DataTypes.TINYINT(1), defaultValue: 1 },
  creado_en:     { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
}, { tableName: 'paquetes' });

// ─── EtapaPipeline ────────────────────────────────────────────
const EtapaPipeline = sequelize.define('EtapaPipeline', {
  id:     { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  nombre: { type: DataTypes.STRING(80), allowNull: false },
  orden:  { type: DataTypes.TINYINT.UNSIGNED, allowNull: false },
  color:  { type: DataTypes.STRING(7), defaultValue: '#6366F1' },
}, { tableName: 'etapas_pipeline' });

// ─── Oportunidad ──────────────────────────────────────────────
const Oportunidad = sequelize.define('Oportunidad', {
  id:             { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  cliente_id:     { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  agente_id:      { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  paquete_id:     { type: DataTypes.INTEGER.UNSIGNED },
  etapa_id:       { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  titulo:         { type: DataTypes.STRING(200), allowNull: false },
  valor_estimado: { type: DataTypes.DECIMAL(12,2) },
  probabilidad:   { type: DataTypes.TINYINT.UNSIGNED, defaultValue: 50 },
  fecha_cierre:   { type: DataTypes.DATEONLY },
  notas:          { type: DataTypes.TEXT },
  estado:         { type: DataTypes.ENUM('Activa','Ganada','Perdida','Cancelada'), defaultValue: 'Activa' },
  motivo_perdida: { type: DataTypes.STRING(200) },
  creado_en:      { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
  actualizado_en: { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
}, { tableName: 'oportunidades' });

// ─── Reserva ──────────────────────────────────────────────────
const Reserva = sequelize.define('Reserva', {
  id:               { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  oportunidad_id:   { type: DataTypes.INTEGER.UNSIGNED },
  cliente_id:       { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  agente_id:        { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  paquete_id:       { type: DataTypes.INTEGER.UNSIGNED },
  codigo_reserva:   { type: DataTypes.STRING(20), allowNull: false, unique: true },
  fecha_salida:     { type: DataTypes.DATEONLY, allowNull: false },
  fecha_regreso:    { type: DataTypes.DATEONLY },
  num_adultos:      { type: DataTypes.TINYINT.UNSIGNED, defaultValue: 1 },
  num_ninos:        { type: DataTypes.TINYINT.UNSIGNED, defaultValue: 0 },
  precio_total:     { type: DataTypes.DECIMAL(12,2), allowNull: false },
  descuento:        { type: DataTypes.DECIMAL(10,2), defaultValue: 0 },
  impuesto:         { type: DataTypes.DECIMAL(10,2), defaultValue: 0 },
  total_final:      { type: DataTypes.DECIMAL(12,2), allowNull: false },
  costo_neto:       { type: DataTypes.DECIMAL(12,2), defaultValue: 0 },
  estado:           { type: DataTypes.ENUM('Pendiente','Confirmada','En Curso','Completada','Cancelada'), defaultValue: 'Pendiente' },
  notas_internas:   { type: DataTypes.TEXT },
  creado_en:        { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
  actualizado_en:   { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
}, { tableName: 'reservas' });

// ─── Pasajero ─────────────────────────────────────────────────
const Pasajero = sequelize.define('Pasajero', {
  id:         { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  reserva_id: { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  nombre:     { type: DataTypes.STRING(100), allowNull: false },
  apellido:   { type: DataTypes.STRING(100), allowNull: false },
  pasaporte:  { type: DataTypes.STRING(30) },
  fecha_nac:  { type: DataTypes.DATEONLY },
  tipo:       { type: DataTypes.ENUM('Adulto','Niño','Infante'), defaultValue: 'Adulto' },
}, { tableName: 'pasajeros' });

// ─── MetodoPago ───────────────────────────────────────────────
const MetodoPago = sequelize.define('MetodoPago', {
  id:     { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  nombre: { type: DataTypes.STRING(60), allowNull: false },
}, { tableName: 'metodos_pago' });

// ─── Pago ─────────────────────────────────────────────────────
const Pago = sequelize.define('Pago', {
  id:              { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  reserva_id:      { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  metodo_id:       { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  monto:           { type: DataTypes.DECIMAL(12,2), allowNull: false },
  referencia:      { type: DataTypes.STRING(100) },
  comprobante_url: { type: DataTypes.STRING(255) },
  estado:          { type: DataTypes.ENUM('Pendiente','Verificado','Rechazado'), defaultValue: 'Pendiente' },
  fecha_pago:      { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
  registrado_por:  { type: DataTypes.INTEGER.UNSIGNED },
  notas:           { type: DataTypes.TEXT },
}, { tableName: 'pagos' });

// ─── Proveedor ────────────────────────────────────────────────
const Proveedor = sequelize.define('Proveedor', {
  id:        { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  nombre:    { type: DataTypes.STRING(150), allowNull: false },
  tipo:      { type: DataTypes.ENUM('Aerolínea','Hotel','Operadora','Seguro','Transporte','Otro'), allowNull: false },
  contacto:  { type: DataTypes.STRING(100) },
  email:     { type: DataTypes.STRING(150) },
  telefono:  { type: DataTypes.STRING(30) },
  pais:      { type: DataTypes.STRING(80) },
  sitio_web: { type: DataTypes.STRING(200) },
  notas:     { type: DataTypes.TEXT },
  activo:    { type: DataTypes.TINYINT(1), defaultValue: 1 },
  creado_en: { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
}, { tableName: 'proveedores' });

// ─── Campana ──────────────────────────────────────────────────
const Campana = sequelize.define('Campana', {
  id:          { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  nombre:      { type: DataTypes.STRING(150), allowNull: false },
  tipo:        { type: DataTypes.ENUM('Email','WhatsApp','SMS','Redes Sociales','Otro'), allowNull: false },
  estado:      { type: DataTypes.ENUM('Borrador','Activa','Pausada','Finalizada'), defaultValue: 'Borrador' },
  fecha_inicio:{ type: DataTypes.DATEONLY },
  fecha_fin:   { type: DataTypes.DATEONLY },
  presupuesto: { type: DataTypes.DECIMAL(10,2) },
  descripcion: { type: DataTypes.TEXT },
  creado_por:  { type: DataTypes.INTEGER.UNSIGNED },
  creado_en:   { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
}, { tableName: 'campanas' });

// ─── Tarea ────────────────────────────────────────────────────
const Tarea = sequelize.define('Tarea', {
  id:             { type: DataTypes.INTEGER.UNSIGNED, primaryKey: true, autoIncrement: true },
  asignado_a:     { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  creado_por:     { type: DataTypes.INTEGER.UNSIGNED, allowNull: false },
  cliente_id:     { type: DataTypes.INTEGER.UNSIGNED },
  oportunidad_id: { type: DataTypes.INTEGER.UNSIGNED },
  titulo:         { type: DataTypes.STRING(200), allowNull: false },
  descripcion:    { type: DataTypes.TEXT },
  prioridad:      { type: DataTypes.ENUM('Baja','Media','Alta','Urgente'), defaultValue: 'Media' },
  estado:         { type: DataTypes.ENUM('Pendiente','En Progreso','Completada','Cancelada'), defaultValue: 'Pendiente' },
  fecha_vence:    { type: DataTypes.DATE },
  completada_en:  { type: DataTypes.DATE },
  creado_en:      { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
}, { tableName: 'tareas' });

// ═══════════════════════════════════════════════════════════════
// ASOCIACIONES
// ═══════════════════════════════════════════════════════════════

Rol.hasMany(Usuario, { foreignKey: 'rol_id' });
Usuario.belongsTo(Rol, { foreignKey: 'rol_id', as: 'rol' });

FuenteOrigen.hasMany(Cliente, { foreignKey: 'fuente_id' });
Cliente.belongsTo(FuenteOrigen, { foreignKey: 'fuente_id', as: 'fuente' });

Usuario.hasMany(Cliente, { foreignKey: 'agente_id' });
Cliente.belongsTo(Usuario, { foreignKey: 'agente_id', as: 'agente' });

Cliente.hasMany(Interaccion, { foreignKey: 'cliente_id', as: 'interacciones' });
Interaccion.belongsTo(Cliente, { foreignKey: 'cliente_id' });

Usuario.hasMany(Interaccion, { foreignKey: 'usuario_id' });
Interaccion.belongsTo(Usuario, { foreignKey: 'usuario_id', as: 'usuario' });

Cliente.belongsToMany(Etiqueta, { through: 'cliente_etiquetas', foreignKey: 'cliente_id', as: 'etiquetas' });
Etiqueta.belongsToMany(Cliente, { through: 'cliente_etiquetas', foreignKey: 'etiqueta_id' });

Pais.hasMany(Destino, { foreignKey: 'pais_id' });
Destino.belongsTo(Pais, { foreignKey: 'pais_id', as: 'pais' });

Destino.hasMany(Paquete, { foreignKey: 'destino_id' });
Paquete.belongsTo(Destino, { foreignKey: 'destino_id', as: 'destino' });

CategoriaPaquete.hasMany(Paquete, { foreignKey: 'categoria_id' });
Paquete.belongsTo(CategoriaPaquete, { foreignKey: 'categoria_id', as: 'categoria' });

EtapaPipeline.hasMany(Oportunidad, { foreignKey: 'etapa_id' });
Oportunidad.belongsTo(EtapaPipeline, { foreignKey: 'etapa_id', as: 'etapa' });

Cliente.hasMany(Oportunidad, { foreignKey: 'cliente_id', as: 'oportunidades' });
Oportunidad.belongsTo(Cliente, { foreignKey: 'cliente_id', as: 'cliente' });

Usuario.hasMany(Oportunidad, { foreignKey: 'agente_id' });
Oportunidad.belongsTo(Usuario, { foreignKey: 'agente_id', as: 'agente' });

Paquete.hasMany(Oportunidad, { foreignKey: 'paquete_id' });
Oportunidad.belongsTo(Paquete, { foreignKey: 'paquete_id', as: 'paquete' });

Oportunidad.hasMany(Reserva, { foreignKey: 'oportunidad_id' });
Reserva.belongsTo(Oportunidad, { foreignKey: 'oportunidad_id', as: 'oportunidad' });

Cliente.hasMany(Reserva, { foreignKey: 'cliente_id', as: 'reservas' });
Reserva.belongsTo(Cliente, { foreignKey: 'cliente_id', as: 'cliente' });

Usuario.hasMany(Reserva, { foreignKey: 'agente_id' });
Reserva.belongsTo(Usuario, { foreignKey: 'agente_id', as: 'agente' });

Paquete.hasMany(Reserva, { foreignKey: 'paquete_id' });
Reserva.belongsTo(Paquete, { foreignKey: 'paquete_id', as: 'paquete' });

Reserva.hasMany(Pasajero, { foreignKey: 'reserva_id', as: 'pasajeros' });
Pasajero.belongsTo(Reserva, { foreignKey: 'reserva_id' });

Reserva.hasMany(Pago, { foreignKey: 'reserva_id', as: 'pagos' });
Pago.belongsTo(Reserva, { foreignKey: 'reserva_id' });

MetodoPago.hasMany(Pago, { foreignKey: 'metodo_id' });
Pago.belongsTo(MetodoPago, { foreignKey: 'metodo_id', as: 'metodo' });

Cliente.hasMany(Tarea, { foreignKey: 'cliente_id' });
Tarea.belongsTo(Cliente, { foreignKey: 'cliente_id', as: 'cliente' });

Usuario.hasMany(Tarea, { foreignKey: 'asignado_a', as: 'tareasAsignadas' });
Tarea.belongsTo(Usuario, { foreignKey: 'asignado_a', as: 'asignado' });

module.exports = {
  sequelize,
  Rol, Usuario,
  FuenteOrigen, Etiqueta, Cliente, Interaccion,
  Pais, Destino, CategoriaPaquete, Paquete,
  EtapaPipeline, Oportunidad,
  Reserva, Pasajero, MetodoPago, Pago,
  Proveedor, Campana, Tarea, ConfiguracionGeneral
};
