-- ============================================================
-- CRM COLEGIO - Script SQL Completo
-- Base de datos: colegio_crm
-- Servidor: localhost | Puerto: 3306
-- ============================================================

CREATE DATABASE IF NOT EXISTS `colegio_crm`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `colegio_crm`;

-- ============================================================
-- TABLA: users (Usuarios del sistema)
-- ============================================================
CREATE TABLE `users` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)    NOT NULL,
  `email`      VARCHAR(150)    NOT NULL UNIQUE,
  `password`   VARCHAR(255)    NOT NULL,
  `role`       ENUM('admin','secretaria','docente','contador') NOT NULL DEFAULT 'secretaria',
  `avatar`     VARCHAR(255)    NULL,
  `activo`     TINYINT(1)      NOT NULL DEFAULT 1,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: grados (Grados escolares)
-- ============================================================
CREATE TABLE `grados` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`      VARCHAR(50)     NOT NULL,
  `nivel`       ENUM('inicial','primaria','secundaria') NOT NULL DEFAULT 'primaria',
  `descripcion` VARCHAR(255)    NULL,
  `created_at`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: secciones
-- ============================================================
CREATE TABLE `secciones` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `grado_id`   BIGINT UNSIGNED NOT NULL,
  `nombre`     VARCHAR(10)     NOT NULL,
  `turno`      ENUM('mañana','tarde','noche') NOT NULL DEFAULT 'mañana',
  `capacidad`  INT             NOT NULL DEFAULT 30,
  `created_at` TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`grado_id`) REFERENCES `grados`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: personal (Docentes y administrativos)
-- ============================================================
CREATE TABLE `personal` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      BIGINT UNSIGNED NULL,
  `dni`          VARCHAR(20)     NOT NULL UNIQUE,
  `nombres`      VARCHAR(100)    NOT NULL,
  `apellidos`    VARCHAR(100)    NOT NULL,
  `tipo`         ENUM('docente','administrativo','directivo','auxiliar') NOT NULL DEFAULT 'docente',
  `especialidad` VARCHAR(100)    NULL,
  `telefono`     VARCHAR(20)     NULL,
  `email`        VARCHAR(150)    NULL,
  `direccion`    TEXT            NULL,
  `fecha_ingreso` DATE           NOT NULL,
  `salario`      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `estado`       ENUM('activo','inactivo','licencia') NOT NULL DEFAULT 'activo',
  `foto`         VARCHAR(255)    NULL,
  `created_at`   TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: alumnos
-- ============================================================
CREATE TABLE `alumnos` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo`          VARCHAR(20)     NOT NULL UNIQUE,
  `dni`             VARCHAR(20)     NOT NULL UNIQUE,
  `nombres`         VARCHAR(100)    NOT NULL,
  `apellidos`       VARCHAR(100)    NOT NULL,
  `fecha_nacimiento` DATE           NOT NULL,
  `genero`          ENUM('M','F')   NOT NULL,
  `direccion`       TEXT            NULL,
  `telefono`        VARCHAR(20)     NULL,
  `email`           VARCHAR(150)    NULL,
  `foto`            VARCHAR(255)    NULL,
  -- Datos del apoderado
  `apoderado_nombre`   VARCHAR(150) NULL,
  `apoderado_dni`      VARCHAR(20)  NULL,
  `apoderado_telefono` VARCHAR(20)  NULL,
  `apoderado_email`    VARCHAR(150) NULL,
  `apoderado_parentesco` VARCHAR(50) NULL,
  `estado`          ENUM('activo','inactivo','trasladado','egresado') NOT NULL DEFAULT 'activo',
  `created_at`      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: matriculas
-- ============================================================
CREATE TABLE `matriculas` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero`       VARCHAR(20)     NOT NULL UNIQUE,
  `alumno_id`    BIGINT UNSIGNED NOT NULL,
  `grado_id`     BIGINT UNSIGNED NOT NULL,
  `seccion_id`   BIGINT UNSIGNED NOT NULL,
  `anio_escolar` YEAR            NOT NULL,
  `fecha_matricula` DATE         NOT NULL,
  `estado`       ENUM('activo','retirado','trasladado') NOT NULL DEFAULT 'activo',
  `observaciones` TEXT           NULL,
  `registrado_por` BIGINT UNSIGNED NULL,
  `created_at`   TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`alumno_id`)   REFERENCES `alumnos`(`id`)   ON DELETE CASCADE,
  FOREIGN KEY (`grado_id`)    REFERENCES `grados`(`id`)    ON DELETE RESTRICT,
  FOREIGN KEY (`seccion_id`)  REFERENCES `secciones`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`registrado_por`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: conceptos_pago
-- ============================================================
CREATE TABLE `conceptos_pago` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`      VARCHAR(100)    NOT NULL,
  `descripcion` TEXT            NULL,
  `monto`       DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `tipo`        ENUM('mensualidad','matricula','taller','otros') NOT NULL DEFAULT 'mensualidad',
  `activo`      TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: pagos
-- ============================================================
CREATE TABLE `pagos` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_recibo`  VARCHAR(30)     NOT NULL UNIQUE,
  `alumno_id`      BIGINT UNSIGNED NOT NULL,
  `concepto_id`    BIGINT UNSIGNED NOT NULL,
  `anio_escolar`   YEAR            NOT NULL,
  `mes`            TINYINT         NULL COMMENT '1-12 para mensualidades',
  `monto`          DECIMAL(10,2)   NOT NULL,
  `descuento`      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `monto_pagado`   DECIMAL(10,2)   NOT NULL,
  `fecha_pago`     DATE            NOT NULL,
  `fecha_vencimiento` DATE         NULL,
  `metodo_pago`    ENUM('efectivo','transferencia','tarjeta','cheque') NOT NULL DEFAULT 'efectivo',
  `estado`         ENUM('pagado','pendiente','vencido','anulado') NOT NULL DEFAULT 'pendiente',
  `comprobante`    VARCHAR(255)    NULL,
  `observaciones`  TEXT            NULL,
  `registrado_por` BIGINT UNSIGNED NULL,
  `created_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`alumno_id`)      REFERENCES `alumnos`(`id`)        ON DELETE RESTRICT,
  FOREIGN KEY (`concepto_id`)    REFERENCES `conceptos_pago`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`registrado_por`) REFERENCES `users`(`id`)          ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: mensajes
-- ============================================================
CREATE TABLE `mensajes` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `remitente_id` BIGINT UNSIGNED NOT NULL,
  `destinatario_id` BIGINT UNSIGNED NOT NULL,
  `asunto`      VARCHAR(200)    NOT NULL,
  `cuerpo`      TEXT            NOT NULL,
  `leido`       TINYINT(1)      NOT NULL DEFAULT 0,
  `leido_en`    TIMESTAMP       NULL,
  `archivado`   TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`remitente_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`destinatario_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: notificaciones
-- ============================================================
CREATE TABLE `notificaciones` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `titulo`      VARCHAR(200)    NOT NULL,
  `mensaje`     TEXT            NOT NULL,
  `tipo`        ENUM('info','exito','advertencia','error') NOT NULL DEFAULT 'info',
  `leido`       TINYINT(1)      NOT NULL DEFAULT 0,
  `url`         VARCHAR(500)    NULL,
  `created_at`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATOS INICIALES (Seeds básicos)
-- ============================================================

-- Usuario administrador (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Administrador', 'admin@colegio.edu.pe', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('María García', 'secretaria@colegio.edu.pe', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'secretaria');

-- Grados
INSERT INTO `grados` (`nombre`, `nivel`) VALUES
('1er Grado', 'primaria'), ('2do Grado', 'primaria'), ('3er Grado', 'primaria'),
('4to Grado', 'primaria'), ('5to Grado', 'primaria'), ('6to Grado', 'primaria'),
('1er Año', 'secundaria'), ('2do Año', 'secundaria'), ('3er Año', 'secundaria'),
('4to Año', 'secundaria'), ('5to Año', 'secundaria');

-- Secciones
INSERT INTO `secciones` (`grado_id`, `nombre`, `turno`) VALUES
(1,'A','mañana'),(1,'B','mañana'),(2,'A','mañana'),(2,'B','mañana'),
(3,'A','mañana'),(3,'B','tarde'),(4,'A','mañana'),(5,'A','mañana'),(6,'A','mañana'),
(7,'A','tarde'),(8,'A','tarde'),(9,'A','tarde'),(10,'A','tarde'),(11,'A','tarde');

-- Conceptos de pago
INSERT INTO `conceptos_pago` (`nombre`, `monto`, `tipo`) VALUES
('Matrícula Anual', 350.00, 'matricula'),
('Mensualidad Primaria', 180.00, 'mensualidad'),
('Mensualidad Secundaria', 220.00, 'mensualidad'),
('Taller de Cómputo', 50.00, 'taller'),
('Taller de Arte', 40.00, 'taller'),
('Seguro Escolar', 30.00, 'otros');
