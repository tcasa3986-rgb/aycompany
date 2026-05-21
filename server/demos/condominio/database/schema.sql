-- =============================================================
-- CRM CONDOMINIO — ESQUEMA COMPLETO DE BASE DE DATOS
-- servidor: localhost | usuario: root | db: condominio_crm
-- =============================================================

CREATE DATABASE IF NOT EXISTS condominio_crm
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE condominio_crm;

-- -------------------------------------------------------------
-- CONFIGURACIÓN DEL CONDOMINIO
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS condominios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  logo_url VARCHAR(500),
  direccion TEXT,
  rfc VARCHAR(30),
  telefono VARCHAR(20),
  email VARCHAR(120),
  sitio_web VARCHAR(200),
  moneda VARCHAR(10) DEFAULT 'MXN',
  zona_horaria VARCHAR(50) DEFAULT 'America/Mexico_City',
  activo TINYINT(1) DEFAULT 1,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS configuracion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  clave VARCHAR(100) NOT NULL UNIQUE,
  valor TEXT,
  descripcion VARCHAR(255),
  tipo ENUM('texto','numero','booleano','json') DEFAULT 'texto',
  actualizado_en DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- -------------------------------------------------------------
-- ESTRUCTURA FÍSICA
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS torres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  condominio_id INT NOT NULL,
  nombre VARCHAR(80) NOT NULL,
  total_pisos SMALLINT DEFAULT 1,
  descripcion TEXT,
  activo TINYINT(1) DEFAULT 1,
  FOREIGN KEY (condominio_id) REFERENCES condominios(id)
);

CREATE TABLE IF NOT EXISTS unidades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  torre_id INT,
  numero VARCHAR(20) NOT NULL,
  piso SMALLINT DEFAULT 1,
  tipo ENUM('departamento','casa','local','bodega','cajón') DEFAULT 'departamento',
  metros_cuadrados DECIMAL(8,2),
  estado ENUM('habitada','vacía','en_venta','en_renta','en_construccion') DEFAULT 'vacía',
  descripcion TEXT,
  activo TINYINT(1) DEFAULT 1,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (torre_id) REFERENCES torres(id)
);

-- -------------------------------------------------------------
-- ROLES Y USUARIOS
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(60) NOT NULL UNIQUE,
  descripcion VARCHAR(200),
  permisos JSON
);

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellidos VARCHAR(100),
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  telefono VARCHAR(20),
  foto_url VARCHAR(500),
  rol_id INT NOT NULL,
  unidad_id INT,
  activo TINYINT(1) DEFAULT 1,
  token_recuperacion VARCHAR(255),
  token_expira DATETIME,
  ultimo_acceso DATETIME,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (rol_id) REFERENCES roles(id),
  FOREIGN KEY (unidad_id) REFERENCES unidades(id)
);

CREATE TABLE IF NOT EXISTS sesiones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  refresh_token VARCHAR(500),
  ip_address VARCHAR(50),
  user_agent TEXT,
  expira_en DATETIME,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- -------------------------------------------------------------
-- RESIDENTES Y PROPIETARIOS
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS residentes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  unidad_id INT NOT NULL,
  usuario_id INT,
  nombre VARCHAR(100) NOT NULL,
  apellidos VARCHAR(100),
  tipo ENUM('propietario','inquilino','familiar','dependiente') DEFAULT 'propietario',
  documento_id VARCHAR(30),
  tipo_documento ENUM('INE','Pasaporte','CURP','otro') DEFAULT 'INE',
  fecha_nacimiento DATE,
  genero ENUM('M','F','otro'),
  email VARCHAR(150),
  telefono VARCHAR(20),
  telefono_alt VARCHAR(20),
  foto_url VARCHAR(500),
  fecha_ingreso DATE,
  fecha_salida DATE,
  activo TINYINT(1) DEFAULT 1,
  notas TEXT,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (unidad_id) REFERENCES unidades(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS contactos_emergencia (
  id INT AUTO_INCREMENT PRIMARY KEY,
  residente_id INT NOT NULL,
  nombre VARCHAR(150) NOT NULL,
  parentesco VARCHAR(60),
  telefono VARCHAR(20) NOT NULL,
  email VARCHAR(150),
  FOREIGN KEY (residente_id) REFERENCES residentes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS vehiculos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  unidad_id INT NOT NULL,
  residente_id INT,
  marca VARCHAR(60),
  modelo VARCHAR(60),
  anio YEAR,
  color VARCHAR(40),
  placas VARCHAR(20) NOT NULL,
  tipo ENUM('auto','camioneta','moto','otro') DEFAULT 'auto',
  activo TINYINT(1) DEFAULT 1,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (unidad_id) REFERENCES unidades(id),
  FOREIGN KEY (residente_id) REFERENCES residentes(id)
);

CREATE TABLE IF NOT EXISTS mascotas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  unidad_id INT NOT NULL,
  residente_id INT,
  nombre VARCHAR(60) NOT NULL,
  especie ENUM('perro','gato','ave','pez','otro') DEFAULT 'perro',
  raza VARCHAR(80),
  color VARCHAR(60),
  chip_id VARCHAR(50),
  vacunas_al_dia TINYINT(1) DEFAULT 0,
  foto_url VARCHAR(500),
  activo TINYINT(1) DEFAULT 1,
  FOREIGN KEY (unidad_id) REFERENCES unidades(id),
  FOREIGN KEY (residente_id) REFERENCES residentes(id)
);

-- -------------------------------------------------------------
-- CUOTAS Y COBRANZA
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tipos_cuota (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  monto_base DECIMAL(10,2),
  periodicidad ENUM('mensual','bimestral','trimestral','anual','única') DEFAULT 'mensual',
  aplica_mora TINYINT(1) DEFAULT 1,
  tasa_mora DECIMAL(5,2) DEFAULT 0.05,
  dias_gracia SMALLINT DEFAULT 5,
  activo TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS cuotas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  unidad_id INT NOT NULL,
  tipo_cuota_id INT NOT NULL,
  monto DECIMAL(10,2) NOT NULL,
  fecha_emision DATE NOT NULL,
  fecha_vencimiento DATE NOT NULL,
  estado ENUM('pendiente','pagado','vencido','en_disputa','cancelado') DEFAULT 'pendiente',
  referencia VARCHAR(50),
  descripcion TEXT,
  mora_aplicada DECIMAL(10,2) DEFAULT 0,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (unidad_id) REFERENCES unidades(id),
  FOREIGN KEY (tipo_cuota_id) REFERENCES tipos_cuota(id)
);

CREATE TABLE IF NOT EXISTS pagos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cuota_id INT,
  unidad_id INT NOT NULL,
  monto_pagado DECIMAL(10,2) NOT NULL,
  fecha_pago DATETIME NOT NULL,
  metodo ENUM('efectivo','transferencia','cheque','tarjeta','app','otro') DEFAULT 'efectivo',
  referencia_pago VARCHAR(100),
  comprobante_url VARCHAR(500),
  registrado_por INT,
  notas TEXT,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cuota_id) REFERENCES cuotas(id),
  FOREIGN KEY (unidad_id) REFERENCES unidades(id),
  FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS recibos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pago_id INT NOT NULL,
  folio VARCHAR(30) NOT NULL UNIQUE,
  pdf_url VARCHAR(500),
  enviado_email TINYINT(1) DEFAULT 0,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pago_id) REFERENCES pagos(id)
);

-- -------------------------------------------------------------
-- CONTABILIDAD
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cuentas_contables (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(20) NOT NULL UNIQUE,
  nombre VARCHAR(150) NOT NULL,
  tipo ENUM('ingreso','egreso','activo','pasivo','capital') NOT NULL,
  padre_id INT,
  nivel TINYINT DEFAULT 1,
  activo TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS transacciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cuenta_id INT NOT NULL,
  tipo ENUM('ingreso','egreso') NOT NULL,
  monto DECIMAL(12,2) NOT NULL,
  fecha DATE NOT NULL,
  descripcion TEXT,
  categoria VARCHAR(100),
  comprobante_url VARCHAR(500),
  referencia VARCHAR(100),
  proveedor_id INT,
  registrado_por INT,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cuenta_id) REFERENCES cuentas_contables(id),
  FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS presupuesto_anual (
  id INT AUTO_INCREMENT PRIMARY KEY,
  anio YEAR NOT NULL,
  cuenta_id INT NOT NULL,
  monto_presupuestado DECIMAL(12,2) NOT NULL,
  notas TEXT,
  FOREIGN KEY (cuenta_id) REFERENCES cuentas_contables(id)
);

CREATE TABLE IF NOT EXISTS fondo_reserva (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipo ENUM('aporte','retiro') NOT NULL,
  monto DECIMAL(12,2) NOT NULL,
  fecha DATE NOT NULL,
  descripcion TEXT,
  aprobado_por INT,
  saldo_resultante DECIMAL(12,2),
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (aprobado_por) REFERENCES usuarios(id)
);

-- -------------------------------------------------------------
-- MANTENIMIENTO
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS areas_comunes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  tipo VARCHAR(80),
  descripcion TEXT,
  activo TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS proveedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  rfc VARCHAR(20),
  tipo_servicio VARCHAR(100),
  contacto_nombre VARCHAR(100),
  contacto_telefono VARCHAR(20),
  contacto_email VARCHAR(150),
  direccion TEXT,
  calificacion DECIMAL(3,2) DEFAULT 0,
  activo TINYINT(1) DEFAULT 1,
  notas TEXT,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contratos_proveedor (
  id INT AUTO_INCREMENT PRIMARY KEY,
  proveedor_id INT NOT NULL,
  tipo VARCHAR(100),
  descripcion TEXT,
  monto_mensual DECIMAL(10,2),
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE,
  documento_url VARCHAR(500),
  estado ENUM('activo','vencido','cancelado','por_renovar') DEFAULT 'activo',
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (proveedor_id) REFERENCES proveedores(id)
);

CREATE TABLE IF NOT EXISTS ordenes_trabajo (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipo ENUM('correctivo','preventivo','emergencia') DEFAULT 'correctivo',
  titulo VARCHAR(200) NOT NULL,
  descripcion TEXT,
  unidad_id INT,
  area_id INT,
  proveedor_id INT,
  asignado_a INT,
  reportado_por INT,
  estado ENUM('abierto','asignado','en_progreso','completado','cerrado','cancelado') DEFAULT 'abierto',
  prioridad ENUM('baja','media','alta','urgente') DEFAULT 'media',
  fecha_reporte DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_asignacion DATETIME,
  fecha_inicio DATETIME,
  fecha_fin DATETIME,
  costo_estimado DECIMAL(10,2),
  costo_real DECIMAL(10,2),
  foto_antes_url VARCHAR(500),
  foto_despues_url VARCHAR(500),
  notas TEXT,
  calificacion TINYINT,
  FOREIGN KEY (unidad_id) REFERENCES unidades(id),
  FOREIGN KEY (area_id) REFERENCES areas_comunes(id),
  FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
  FOREIGN KEY (asignado_a) REFERENCES usuarios(id),
  FOREIGN KEY (reportado_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS mantenimiento_preventivo (
  id INT AUTO_INCREMENT PRIMARY KEY,
  area_id INT,
  titulo VARCHAR(200) NOT NULL,
  descripcion TEXT,
  proveedor_id INT,
  frecuencia ENUM('semanal','mensual','bimestral','trimestral','semestral','anual'),
  proxima_fecha DATE,
  ultima_fecha DATE,
  costo_estimado DECIMAL(10,2),
  activo TINYINT(1) DEFAULT 1,
  FOREIGN KEY (area_id) REFERENCES areas_comunes(id),
  FOREIGN KEY (proveedor_id) REFERENCES proveedores(id)
);

-- -------------------------------------------------------------
-- AMENIDADES Y RESERVACIONES
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS amenidades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  capacidad_max SMALLINT,
  tiene_costo TINYINT(1) DEFAULT 0,
  costo DECIMAL(10,2) DEFAULT 0,
  horario_inicio TIME,
  horario_fin TIME,
  limite_reservas_mes TINYINT DEFAULT 4,
  dias_anticipacion TINYINT DEFAULT 3,
  foto_url VARCHAR(500),
  activo TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS reservaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  amenidad_id INT NOT NULL,
  unidad_id INT NOT NULL,
  residente_id INT,
  fecha DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  num_personas SMALLINT DEFAULT 1,
  estado ENUM('pendiente','confirmada','cancelada','completada') DEFAULT 'pendiente',
  costo_cobrado DECIMAL(10,2) DEFAULT 0,
  pago_id INT,
  notas TEXT,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (amenidad_id) REFERENCES amenidades(id),
  FOREIGN KEY (unidad_id) REFERENCES unidades(id),
  FOREIGN KEY (residente_id) REFERENCES residentes(id),
  FOREIGN KEY (pago_id) REFERENCES pagos(id)
);

-- -------------------------------------------------------------
-- CONTROL DE ACCESO Y SEGURIDAD
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS visitantes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  documento_id VARCHAR(30),
  foto_url VARCHAR(500),
  unidad_id INT,
  residente_id INT,
  motivo VARCHAR(200),
  tipo ENUM('visita','proveedor','delivery','otro') DEFAULT 'visita',
  entrada DATETIME DEFAULT CURRENT_TIMESTAMP,
  salida DATETIME,
  autorizado_por INT,
  guardia_id INT,
  vehiculo_placas VARCHAR(20),
  notas TEXT,
  FOREIGN KEY (unidad_id) REFERENCES unidades(id),
  FOREIGN KEY (residente_id) REFERENCES residentes(id),
  FOREIGN KEY (autorizado_por) REFERENCES residentes(id),
  FOREIGN KEY (guardia_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS paquetes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  unidad_id INT NOT NULL,
  descripcion VARCHAR(200),
  remitente VARCHAR(150),
  empresa_mensajeria VARCHAR(80),
  numero_guia VARCHAR(80),
  foto_url VARCHAR(500),
  estado ENUM('recibido','notificado','entregado') DEFAULT 'recibido',
  recibido_por INT,
  fecha_recepcion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_entrega DATETIME,
  entregado_a VARCHAR(150),
  FOREIGN KEY (unidad_id) REFERENCES unidades(id),
  FOREIGN KEY (recibido_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS incidentes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipo VARCHAR(100),
  descripcion TEXT NOT NULL,
  ubicacion VARCHAR(200),
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  nivel ENUM('bajo','medio','alto','critico') DEFAULT 'medio',
  estado ENUM('abierto','en_investigacion','cerrado') DEFAULT 'abierto',
  reportado_por INT,
  foto_url VARCHAR(500),
  seguimiento TEXT,
  FOREIGN KEY (reportado_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS rondas_vigilancia (
  id INT AUTO_INCREMENT PRIMARY KEY,
  guardia_id INT NOT NULL,
  inicio DATETIME NOT NULL,
  fin DATETIME,
  ruta VARCHAR(200),
  observaciones TEXT,
  estado ENUM('en_curso','completada','incompleta') DEFAULT 'en_curso',
  FOREIGN KEY (guardia_id) REFERENCES usuarios(id)
);

-- -------------------------------------------------------------
-- COMUNICACIONES
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS anuncios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(200) NOT NULL,
  contenido TEXT NOT NULL,
  tipo ENUM('informativo','urgente','evento','mantenimiento','cobranza') DEFAULT 'informativo',
  publicado_por INT,
  fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_expiracion DATETIME,
  adjunto_url VARCHAR(500),
  enviar_email TINYINT(1) DEFAULT 0,
  activo TINYINT(1) DEFAULT 1,
  FOREIGN KEY (publicado_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS mensajes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  de_usuario_id INT NOT NULL,
  para_usuario_id INT,
  unidad_id INT,
  asunto VARCHAR(200),
  contenido TEXT NOT NULL,
  leido TINYINT(1) DEFAULT 0,
  adjunto_url VARCHAR(500),
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (de_usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (para_usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (unidad_id) REFERENCES unidades(id)
);

CREATE TABLE IF NOT EXISTS asambleas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(200) NOT NULL,
  tipo ENUM('ordinaria','extraordinaria') DEFAULT 'ordinaria',
  fecha DATETIME NOT NULL,
  lugar VARCHAR(200),
  orden_dia TEXT,
  minuta TEXT,
  acuerdos TEXT,
  convocatoria_url VARCHAR(500),
  documento_url VARCHAR(500),
  estado ENUM('programada','en_curso','finalizada','cancelada') DEFAULT 'programada',
  creado_por INT,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creado_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS asistentes_asamblea (
  id INT AUTO_INCREMENT PRIMARY KEY,
  asamblea_id INT NOT NULL,
  residente_id INT NOT NULL,
  unidad_id INT NOT NULL,
  asistio TINYINT(1) DEFAULT 0,
  representante VARCHAR(150),
  firma_url VARCHAR(500),
  FOREIGN KEY (asamblea_id) REFERENCES asambleas(id) ON DELETE CASCADE,
  FOREIGN KEY (residente_id) REFERENCES residentes(id),
  FOREIGN KEY (unidad_id) REFERENCES unidades(id)
);

CREATE TABLE IF NOT EXISTS encuestas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(200) NOT NULL,
  descripcion TEXT,
  opciones JSON NOT NULL,
  publicado_por INT,
  fecha_inicio DATETIME,
  fecha_fin DATETIME,
  activo TINYINT(1) DEFAULT 1,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (publicado_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS votos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  encuesta_id INT NOT NULL,
  residente_id INT NOT NULL,
  respuesta VARCHAR(200),
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (encuesta_id) REFERENCES encuestas(id) ON DELETE CASCADE,
  FOREIGN KEY (residente_id) REFERENCES residentes(id)
);

-- -------------------------------------------------------------
-- AUDITORÍA
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS log_actividad (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  accion VARCHAR(100) NOT NULL,
  modulo VARCHAR(80),
  registro_id INT,
  datos_anteriores JSON,
  datos_nuevos JSON,
  ip_address VARCHAR(50),
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- -------------------------------------------------------------
-- ÍNDICES PARA RENDIMIENTO
-- -------------------------------------------------------------
CREATE INDEX idx_cuotas_estado ON cuotas(estado);
CREATE INDEX idx_cuotas_unidad ON cuotas(unidad_id);
CREATE INDEX idx_pagos_fecha ON pagos(fecha_pago);
CREATE INDEX idx_visitantes_fecha ON visitantes(entrada);
CREATE INDEX idx_ordenes_estado ON ordenes_trabajo(estado);
CREATE INDEX idx_reservaciones_fecha ON reservaciones(fecha);
CREATE INDEX idx_anuncios_activo ON anuncios(activo);
CREATE INDEX idx_log_usuario ON log_actividad(usuario_id);
CREATE INDEX idx_log_fecha ON log_actividad(fecha);
