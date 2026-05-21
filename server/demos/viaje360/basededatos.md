# Base de Datos - CRM Viaje 360

## Configuración del Servidor MySQL

```
server   = localhost
user     = root
password = (vacío o tu contraseña)
database = viaje360_crm
port     = 3306
```

---

## Script Completo de Creación de Base de Datos

```sql
-- ============================================================
--  CRM VIAJE 360 - Script de Base de Datos
--  Motor: MySQL 8.0+
--  Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================

CREATE DATABASE IF NOT EXISTS viaje360_crm
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE viaje360_crm;

-- ============================================================
-- 1. MÓDULO: SEGURIDAD Y USUARIOS DEL SISTEMA
-- ============================================================

-- Roles del sistema
CREATE TABLE roles (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(60)  NOT NULL,
  descripcion TEXT,
  permisos    JSON,                           -- { "clientes":true, "ventas":true, ... }
  activo      TINYINT(1)   NOT NULL DEFAULT 1,
  creado_en   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Usuarios del sistema (agentes / admins)
CREATE TABLE usuarios (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rol_id        INT UNSIGNED NOT NULL,
  nombre        VARCHAR(100) NOT NULL,
  apellido      VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  telefono      VARCHAR(20),
  avatar_url    VARCHAR(255),
  activo        TINYINT(1)   NOT NULL DEFAULT 1,
  ultimo_login  DATETIME,
  creado_en     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- Sesiones / tokens JWT (blacklist de revocación)
CREATE TABLE sesiones (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  token_jti  VARCHAR(255) NOT NULL UNIQUE,
  ip_address VARCHAR(45),
  user_agent TEXT,
  expira_en  DATETIME     NOT NULL,
  creado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sesiones_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ============================================================
-- 2. MÓDULO: CLIENTES (CRM CORE)
-- ============================================================

-- Fuentes de origen del cliente
CREATE TABLE fuentes_origen (
  id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL   -- Redes Sociales, Referido, Web, Feria de Viajes...
) ENGINE=InnoDB;

-- Etiquetas/tags de clientes
CREATE TABLE etiquetas (
  id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(60)  NOT NULL,
  color  VARCHAR(7)   NOT NULL DEFAULT '#3B82F6'  -- Hex color
) ENGINE=InnoDB;

-- Clientes (viajeros)
CREATE TABLE clientes (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fuente_id       INT UNSIGNED,
  agente_id       INT UNSIGNED,                    -- Agente asignado
  nombre          VARCHAR(100) NOT NULL,
  apellido        VARCHAR(100) NOT NULL,
  email           VARCHAR(150) NOT NULL UNIQUE,
  telefono        VARCHAR(30),
  telefono_alt    VARCHAR(30),
  fecha_nacimiento DATE,
  genero          ENUM('M','F','Otro'),
  documento_tipo  ENUM('DNI','Pasaporte','CE','RUC') DEFAULT 'DNI',
  documento_num   VARCHAR(30),
  pais            VARCHAR(80),
  ciudad          VARCHAR(80),
  direccion       TEXT,
  categoria       ENUM('Nuevo','Recurrente','VIP','Inactivo') DEFAULT 'Nuevo',
  notas           TEXT,
  activo          TINYINT(1)   NOT NULL DEFAULT 1,
  creado_en       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_clientes_fuente FOREIGN KEY (fuente_id) REFERENCES fuentes_origen(id),
  CONSTRAINT fk_clientes_agente FOREIGN KEY (agente_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Relación cliente ↔ etiquetas (N:M)
CREATE TABLE cliente_etiquetas (
  cliente_id  INT UNSIGNED NOT NULL,
  etiqueta_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (cliente_id, etiqueta_id),
  CONSTRAINT fk_ce_cliente  FOREIGN KEY (cliente_id)  REFERENCES clientes(id) ON DELETE CASCADE,
  CONSTRAINT fk_ce_etiqueta FOREIGN KEY (etiqueta_id) REFERENCES etiquetas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Historial de interacciones con el cliente
CREATE TABLE interacciones (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id  INT UNSIGNED NOT NULL,
  usuario_id  INT UNSIGNED NOT NULL,
  tipo        ENUM('Llamada','Email','WhatsApp','Reunión','Nota','Cotización','Seguimiento') NOT NULL,
  descripcion TEXT         NOT NULL,
  adjunto_url VARCHAR(255),
  fecha       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_int_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
  CONSTRAINT fk_int_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ============================================================
-- 3. MÓDULO: DESTINOS Y CATÁLOGO DE VIAJES
-- ============================================================

-- Países de destino
CREATE TABLE paises (
  id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80)  NOT NULL,
  codigo CHAR(2)      NOT NULL UNIQUE,   -- ISO 3166-1 alpha-2
  zona   VARCHAR(60)                     -- Europa, Asia, Sudamérica...
) ENGINE=InnoDB;

-- Destinos específicos
CREATE TABLE destinos (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pais_id     INT UNSIGNED NOT NULL,
  nombre      VARCHAR(120) NOT NULL,
  descripcion TEXT,
  imagen_url  VARCHAR(255),
  activo      TINYINT(1)   NOT NULL DEFAULT 1,
  CONSTRAINT fk_destinos_pais FOREIGN KEY (pais_id) REFERENCES paises(id)
) ENGINE=InnoDB;

-- Categorías de paquetes
CREATE TABLE categorias_paquete (
  id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL    -- Luna de Miel, Aventura, Cultural, Crucero...
) ENGINE=InnoDB;

-- Paquetes turísticos
CREATE TABLE paquetes (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  destino_id       INT UNSIGNED NOT NULL,
  categoria_id     INT UNSIGNED,
  nombre           VARCHAR(150) NOT NULL,
  descripcion      TEXT,
  itinerario       LONGTEXT,                       -- JSON o texto detallado
  duracion_dias    SMALLINT UNSIGNED,
  precio_base      DECIMAL(10,2) NOT NULL,
  precio_adulto    DECIMAL(10,2),
  precio_nino      DECIMAL(10,2),
  incluye          TEXT,
  no_incluye       TEXT,
  imagen_url       VARCHAR(255),
  disponible       TINYINT(1) NOT NULL DEFAULT 1,
  creado_en        DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_paquetes_destino   FOREIGN KEY (destino_id)   REFERENCES destinos(id),
  CONSTRAINT fk_paquetes_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_paquete(id)
) ENGINE=InnoDB;

-- ============================================================
-- 4. MÓDULO: OPORTUNIDADES / PIPELINE DE VENTAS
-- ============================================================

-- Etapas del pipeline
CREATE TABLE etapas_pipeline (
  id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre   VARCHAR(80)  NOT NULL,   -- Interesado, Cotizado, Negociación, Cerrado-Ganado...
  orden    TINYINT UNSIGNED NOT NULL,
  color    VARCHAR(7)   NOT NULL DEFAULT '#6366F1'
) ENGINE=InnoDB;

-- Oportunidades de venta
CREATE TABLE oportunidades (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id      INT UNSIGNED NOT NULL,
  agente_id       INT UNSIGNED NOT NULL,
  paquete_id      INT UNSIGNED,
  etapa_id        INT UNSIGNED NOT NULL,
  titulo          VARCHAR(200) NOT NULL,
  valor_estimado  DECIMAL(12,2),
  probabilidad    TINYINT UNSIGNED DEFAULT 50,    -- % 0-100
  fecha_cierre    DATE,
  notas           TEXT,
  estado          ENUM('Activa','Ganada','Perdida','Cancelada') DEFAULT 'Activa',
  motivo_perdida  VARCHAR(200),
  creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_op_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  CONSTRAINT fk_op_agente  FOREIGN KEY (agente_id)  REFERENCES usuarios(id),
  CONSTRAINT fk_op_paquete FOREIGN KEY (paquete_id) REFERENCES paquetes(id),
  CONSTRAINT fk_op_etapa   FOREIGN KEY (etapa_id)   REFERENCES etapas_pipeline(id)
) ENGINE=InnoDB;

-- Historial de cambios de etapa
CREATE TABLE oportunidad_historial (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  oportunidad_id  INT UNSIGNED NOT NULL,
  etapa_anterior  INT UNSIGNED,
  etapa_nueva     INT UNSIGNED NOT NULL,
  usuario_id      INT UNSIGNED NOT NULL,
  nota            TEXT,
  cambiado_en     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_oh_oportunidad FOREIGN KEY (oportunidad_id) REFERENCES oportunidades(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 5. MÓDULO: RESERVAS Y VENTAS
-- ============================================================

-- Estados de reserva
-- Confirmada, Pendiente de Pago, Cancelada, En Curso, Completada

-- Reservas / Ventas cerradas
CREATE TABLE reservas (
  id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  oportunidad_id      INT UNSIGNED,
  cliente_id          INT UNSIGNED NOT NULL,
  agente_id           INT UNSIGNED NOT NULL,
  paquete_id          INT UNSIGNED,
  codigo_reserva      VARCHAR(20)  NOT NULL UNIQUE,
  fecha_salida        DATE         NOT NULL,
  fecha_regreso       DATE,
  num_adultos         TINYINT UNSIGNED NOT NULL DEFAULT 1,
  num_ninos           TINYINT UNSIGNED NOT NULL DEFAULT 0,
  precio_total        DECIMAL(12,2) NOT NULL,
  descuento           DECIMAL(10,2) NOT NULL DEFAULT 0,
  impuesto            DECIMAL(10,2) NOT NULL DEFAULT 0,
  total_final         DECIMAL(12,2) NOT NULL,
  estado              ENUM('Pendiente','Confirmada','En Curso','Completada','Cancelada') DEFAULT 'Pendiente',
  notas_internas      TEXT,
  creado_en           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_res_oportunidad FOREIGN KEY (oportunidad_id) REFERENCES oportunidades(id),
  CONSTRAINT fk_res_cliente     FOREIGN KEY (cliente_id)     REFERENCES clientes(id),
  CONSTRAINT fk_res_agente      FOREIGN KEY (agente_id)      REFERENCES usuarios(id),
  CONSTRAINT fk_res_paquete     FOREIGN KEY (paquete_id)     REFERENCES paquetes(id)
) ENGINE=InnoDB;

-- Pasajeros vinculados a una reserva
CREATE TABLE pasajeros (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reserva_id    INT UNSIGNED NOT NULL,
  nombre        VARCHAR(100) NOT NULL,
  apellido      VARCHAR(100) NOT NULL,
  pasaporte     VARCHAR(30),
  fecha_nac     DATE,
  tipo          ENUM('Adulto','Niño','Infante') DEFAULT 'Adulto',
  CONSTRAINT fk_pas_reserva FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Servicios adicionales (hotel extra, seguro, traslado...)
CREATE TABLE servicios_adicionales (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(120) NOT NULL,
  descripcion TEXT,
  precio      DECIMAL(10,2) NOT NULL,
  tipo        ENUM('Hotel','Transporte','Seguro','Tour','Visado','Otro') DEFAULT 'Otro'
) ENGINE=InnoDB;

-- Servicios contratados por reserva
CREATE TABLE reserva_servicios (
  reserva_id  INT UNSIGNED NOT NULL,
  servicio_id INT UNSIGNED NOT NULL,
  cantidad    TINYINT UNSIGNED NOT NULL DEFAULT 1,
  precio_unit DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (reserva_id, servicio_id),
  CONSTRAINT fk_rs_reserva  FOREIGN KEY (reserva_id)  REFERENCES reservas(id)  ON DELETE CASCADE,
  CONSTRAINT fk_rs_servicio FOREIGN KEY (servicio_id) REFERENCES servicios_adicionales(id)
) ENGINE=InnoDB;

-- ============================================================
-- 6. MÓDULO: PAGOS Y FACTURACIÓN
-- ============================================================

CREATE TABLE metodos_pago (
  id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(60) NOT NULL   -- Efectivo, Tarjeta, Transferencia, PayPal...
) ENGINE=InnoDB;

CREATE TABLE pagos (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reserva_id      INT UNSIGNED NOT NULL,
  metodo_id       INT UNSIGNED NOT NULL,
  monto           DECIMAL(12,2) NOT NULL,
  referencia      VARCHAR(100),
  comprobante_url VARCHAR(255),
  estado          ENUM('Pendiente','Verificado','Rechazado') DEFAULT 'Pendiente',
  fecha_pago      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  registrado_por  INT UNSIGNED,
  notas           TEXT,
  CONSTRAINT fk_pagos_reserva FOREIGN KEY (reserva_id) REFERENCES reservas(id),
  CONSTRAINT fk_pagos_metodo  FOREIGN KEY (metodo_id)  REFERENCES metodos_pago(id),
  CONSTRAINT fk_pagos_usuario FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Facturas
CREATE TABLE facturas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reserva_id      INT UNSIGNED NOT NULL UNIQUE,
  numero_factura  VARCHAR(30)  NOT NULL UNIQUE,
  fecha_emision   DATE         NOT NULL,
  subtotal        DECIMAL(12,2) NOT NULL,
  impuesto        DECIMAL(12,2) NOT NULL DEFAULT 0,
  total           DECIMAL(12,2) NOT NULL,
  estado          ENUM('Emitida','Pagada','Anulada') DEFAULT 'Emitida',
  pdf_url         VARCHAR(255),
  CONSTRAINT fk_fact_reserva FOREIGN KEY (reserva_id) REFERENCES reservas(id)
) ENGINE=InnoDB;

-- ============================================================
-- 7. MÓDULO: PROVEEDORES
-- ============================================================

CREATE TABLE proveedores (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(150) NOT NULL,
  tipo        ENUM('Aerolínea','Hotel','Operadora','Seguro','Transporte','Otro') NOT NULL,
  contacto    VARCHAR(100),
  email       VARCHAR(150),
  telefono    VARCHAR(30),
  pais        VARCHAR(80),
  sitio_web   VARCHAR(200),
  notas       TEXT,
  activo      TINYINT(1) NOT NULL DEFAULT 1,
  creado_en   DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Vínculos paquete ↔ proveedor
CREATE TABLE paquete_proveedores (
  paquete_id    INT UNSIGNED NOT NULL,
  proveedor_id  INT UNSIGNED NOT NULL,
  descripcion   VARCHAR(200),
  PRIMARY KEY (paquete_id, proveedor_id),
  CONSTRAINT fk_pp_paquete   FOREIGN KEY (paquete_id)   REFERENCES paquetes(id) ON DELETE CASCADE,
  CONSTRAINT fk_pp_proveedor FOREIGN KEY (proveedor_id) REFERENCES proveedores(id)
) ENGINE=InnoDB;

-- ============================================================
-- 8. MÓDULO: CAMPAÑAS DE MARKETING
-- ============================================================

CREATE TABLE campanas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre          VARCHAR(150) NOT NULL,
  tipo            ENUM('Email','WhatsApp','SMS','Redes Sociales','Otro') NOT NULL,
  estado          ENUM('Borrador','Activa','Pausada','Finalizada') DEFAULT 'Borrador',
  fecha_inicio    DATE,
  fecha_fin       DATE,
  presupuesto     DECIMAL(10,2),
  descripcion     TEXT,
  creado_por      INT UNSIGNED,
  creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_camp_usuario FOREIGN KEY (creado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Destinatarios de campaña
CREATE TABLE campana_clientes (
  campana_id    INT UNSIGNED NOT NULL,
  cliente_id    INT UNSIGNED NOT NULL,
  enviado       TINYINT(1)   DEFAULT 0,
  abierto       TINYINT(1)   DEFAULT 0,
  convertido    TINYINT(1)   DEFAULT 0,
  fecha_envio   DATETIME,
  PRIMARY KEY (campana_id, cliente_id),
  CONSTRAINT fk_cc_campana FOREIGN KEY (campana_id) REFERENCES campanas(id) ON DELETE CASCADE,
  CONSTRAINT fk_cc_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 9. MÓDULO: TAREAS Y SEGUIMIENTOS
-- ============================================================

CREATE TABLE tareas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  asignado_a      INT UNSIGNED NOT NULL,
  creado_por      INT UNSIGNED NOT NULL,
  cliente_id      INT UNSIGNED,
  oportunidad_id  INT UNSIGNED,
  titulo          VARCHAR(200) NOT NULL,
  descripcion     TEXT,
  prioridad       ENUM('Baja','Media','Alta','Urgente') DEFAULT 'Media',
  estado          ENUM('Pendiente','En Progreso','Completada','Cancelada') DEFAULT 'Pendiente',
  fecha_vence     DATETIME,
  completada_en   DATETIME,
  creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tareas_asignado    FOREIGN KEY (asignado_a)     REFERENCES usuarios(id),
  CONSTRAINT fk_tareas_creador     FOREIGN KEY (creado_por)     REFERENCES usuarios(id),
  CONSTRAINT fk_tareas_cliente     FOREIGN KEY (cliente_id)     REFERENCES clientes(id),
  CONSTRAINT fk_tareas_oportunidad FOREIGN KEY (oportunidad_id) REFERENCES oportunidades(id)
) ENGINE=InnoDB;

-- ============================================================
-- 10. MÓDULO: REPORTES Y AUDITORÍA
-- ============================================================

-- Log de auditoría general
CREATE TABLE auditoria (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id  INT UNSIGNED,
  accion      VARCHAR(100) NOT NULL,   -- CREATE, UPDATE, DELETE, LOGIN...
  tabla       VARCHAR(100),
  registro_id INT UNSIGNED,
  datos       JSON,                    -- { antes: {...}, despues: {...} }
  ip_address  VARCHAR(45),
  creado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ============================================================
-- 11. DATOS INICIALES (SEEDS)
-- ============================================================

-- Roles básicos
INSERT INTO roles (nombre, descripcion, permisos) VALUES
  ('Administrador', 'Acceso total al sistema', '{"all": true}'),
  ('Gerente',       'Gestión de agentes y reportes', '{"reportes":true,"clientes":true,"ventas":true,"configuracion":false}'),
  ('Agente',        'Gestión de clientes y ventas',  '{"clientes":true,"ventas":true,"reportes":false}'),
  ('Contador',      'Acceso a facturación y pagos',  '{"pagos":true,"facturas":true}');

-- Usuario administrador por defecto (password: Admin@360)
INSERT INTO usuarios (rol_id, nombre, apellido, email, password_hash) VALUES
  (1, 'Admin', 'Sistema', 'admin@viaje360.com',
   '$2b$10$rK8xGzBhNqXtVqePkjlYfuMr0J5zRpXgBGjKvHtHXOjUPXb.FxR5m');

-- Etapas del pipeline de ventas
INSERT INTO etapas_pipeline (nombre, orden, color) VALUES
  ('Nuevo Lead',       1, '#64748B'),
  ('Contactado',       2, '#3B82F6'),
  ('Interesado',       3, '#8B5CF6'),
  ('Cotización Enviada',4, '#F59E0B'),
  ('Negociación',      5, '#EF4444'),
  ('Cerrado - Ganado', 6, '#10B981'),
  ('Cerrado - Perdido',7, '#6B7280');

-- Fuentes de origen
INSERT INTO fuentes_origen (nombre) VALUES
  ('Sitio Web'), ('Redes Sociales'), ('Referido'), ('Feria de Viajes'),
  ('Anuncio Google'), ('WhatsApp'), ('Llamada Directa'), ('Email Campaign');

-- Métodos de pago
INSERT INTO metodos_pago (nombre) VALUES
  ('Efectivo'), ('Tarjeta de Crédito'), ('Tarjeta de Débito'),
  ('Transferencia Bancaria'), ('PayPal'), ('Yape/Plin'), ('Criptomoneda');

-- Categorías de paquetes
INSERT INTO categorias_paquete (nombre) VALUES
  ('Luna de Miel'), ('Aventura y Naturaleza'), ('Cultural e Histórico'),
  ('Playa y Relax'), ('Crucero'), ('City Break'), ('Safari'), ('Familiar');

-- Etiquetas de clientes
INSERT INTO etiquetas (nombre, color) VALUES
  ('VIP',             '#F59E0B'),
  ('Frecuente',       '#10B981'),
  ('Corporativo',     '#3B82F6'),
  ('Interesado Luna de Miel', '#EC4899'),
  ('Recomendado',     '#8B5CF6'),
  ('Pendiente Pago',  '#EF4444');

-- Países principales
INSERT INTO paises (nombre, codigo, zona) VALUES
  ('Perú',        'PE', 'Sudamérica'),
  ('Francia',     'FR', 'Europa'),
  ('Italia',      'IT', 'Europa'),
  ('Japón',       'JP', 'Asia'),
  ('Tailandia',   'TH', 'Asia'),
  ('México',      'MX', 'América Central'),
  ('Brasil',      'BR', 'Sudamérica'),
  ('España',      'ES', 'Europa'),
  ('Estados Unidos','US', 'América del Norte'),
  ('Maldivas',    'MV', 'Asia'),
  ('Turquía',     'TR', 'Europa/Asia'),
  ('Marruecos',   'MA', 'África'),
  ('Argentina',   'AR', 'Sudamérica'),
  ('Egipto',      'EG', 'África'),
  ('Grecia',      'GR', 'Europa');

-- ============================================================
-- FIN DEL SCRIPT
-- ============================================================
```

---

## Diagrama Entidad-Relación (Resumen)

```
usuarios ──────< interacciones >────── clientes
    │                                      │
    │                              cliente_etiquetas
    │                                      │
    └──────< oportunidades >───────────── etiquetas
                  │
              reservas ──────< pasajeros
                  │
              pagos / facturas
```

## Notas Técnicas

| Parámetro | Valor |
|---|---|
| Motor | `InnoDB` (soporte FK y transacciones) |
| Charset | `utf8mb4` (soporte emojis y caracteres especiales) |
| Collation | `utf8mb4_unicode_ci` |
| Versión mínima MySQL | 8.0 |
| ORM recomendado | Sequelize o Prisma (Node.js) |
| Puerto por defecto | 3306 |
