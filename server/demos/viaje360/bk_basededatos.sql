-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para viaje360_crm
CREATE DATABASE IF NOT EXISTS `viaje360_crm` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `viaje360_crm`;

-- Volcando estructura para tabla viaje360_crm.auditoria
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int unsigned DEFAULT NULL,
  `accion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabla` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registro_id` int unsigned DEFAULT NULL,
  `datos` json DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_audit_usuario` (`usuario_id`),
  CONSTRAINT `fk_audit_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.auditoria: ~0 rows (aproximadamente)
DELETE FROM `auditoria`;

-- Volcando estructura para tabla viaje360_crm.campanas
CREATE TABLE IF NOT EXISTS `campanas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('Email','WhatsApp','SMS','Redes Sociales','Otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Borrador','Activa','Pausada','Finalizada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Borrador',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `presupuesto` decimal(10,2) DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creado_por` int unsigned DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_camp_usuario` (`creado_por`),
  CONSTRAINT `fk_camp_usuario` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.campanas: ~10 rows (aproximadamente)
DELETE FROM `campanas`;
INSERT INTO `campanas` (`id`, `nombre`, `tipo`, `estado`, `fecha_inicio`, `fecha_fin`, `presupuesto`, `descripcion`, `creado_por`, `creado_en`) VALUES
	(1, 'Campaña 1 - Promo Destinos', 'Email', 'Borrador', '2026-04-19', NULL, 500.00, NULL, 1, '2026-04-19 14:00:41'),
	(2, 'Campaña 2 - Promo Destinos', 'WhatsApp', 'Activa', '2026-04-14', NULL, 650.00, NULL, 1, '2026-04-19 14:00:41'),
	(3, 'Campaña 3 - Promo Destinos', 'SMS', 'Pausada', '2026-04-09', NULL, 800.00, NULL, 1, '2026-04-19 14:00:41'),
	(4, 'Campaña 4 - Promo Destinos', 'Redes Sociales', 'Finalizada', '2026-04-04', NULL, 950.00, NULL, 1, '2026-04-19 14:00:41'),
	(5, 'Campaña 5 - Promo Destinos', 'Otro', 'Borrador', '2026-03-30', NULL, 1100.00, NULL, 1, '2026-04-19 14:00:41'),
	(6, 'Campaña 6 - Promo Destinos', 'Email', 'Activa', '2026-03-25', NULL, 1250.00, NULL, 1, '2026-04-19 14:00:41'),
	(7, 'Campaña 7 - Promo Destinos', 'WhatsApp', 'Pausada', '2026-03-20', NULL, 1400.00, NULL, 1, '2026-04-19 14:00:41'),
	(8, 'Campaña 8 - Promo Destinos', 'SMS', 'Finalizada', '2026-03-15', NULL, 1550.00, NULL, 1, '2026-04-19 14:00:41'),
	(9, 'Campaña 9 - Promo Destinos', 'Redes Sociales', 'Borrador', '2026-03-10', NULL, 1700.00, NULL, 1, '2026-04-19 14:00:41'),
	(10, 'Campaña 10 - Promo Destinos', 'Otro', 'Activa', '2026-03-05', NULL, 1850.00, NULL, 1, '2026-04-19 14:00:41');

-- Volcando estructura para tabla viaje360_crm.campana_clientes
CREATE TABLE IF NOT EXISTS `campana_clientes` (
  `campana_id` int unsigned NOT NULL,
  `cliente_id` int unsigned NOT NULL,
  `enviado` tinyint(1) DEFAULT '0',
  `abierto` tinyint(1) DEFAULT '0',
  `convertido` tinyint(1) DEFAULT '0',
  `fecha_envio` datetime DEFAULT NULL,
  PRIMARY KEY (`campana_id`,`cliente_id`),
  KEY `fk_cc_cliente` (`cliente_id`),
  CONSTRAINT `fk_cc_campana` FOREIGN KEY (`campana_id`) REFERENCES `campanas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cc_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.campana_clientes: ~0 rows (aproximadamente)
DELETE FROM `campana_clientes`;

-- Volcando estructura para tabla viaje360_crm.categorias_paquete
CREATE TABLE IF NOT EXISTS `categorias_paquete` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.categorias_paquete: ~3 rows (aproximadamente)
DELETE FROM `categorias_paquete`;
INSERT INTO `categorias_paquete` (`id`, `nombre`) VALUES
	(1, 'Aventura'),
	(2, 'Relax & Playa'),
	(3, 'Cultural');

-- Volcando estructura para tabla viaje360_crm.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `fuente_id` int unsigned DEFAULT NULL,
  `agente_id` int unsigned DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono_alt` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('M','F','Otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documento_tipo` enum('DNI','Pasaporte','CE','RUC') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'DNI',
  `documento_num` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `categoria` enum('Nuevo','Recurrente','VIP','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Nuevo',
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_clientes_fuente` (`fuente_id`),
  KEY `fk_clientes_agente` (`agente_id`),
  CONSTRAINT `fk_clientes_agente` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_clientes_fuente` FOREIGN KEY (`fuente_id`) REFERENCES `fuentes_origen` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.clientes: ~20 rows (aproximadamente)
DELETE FROM `clientes`;
INSERT INTO `clientes` (`id`, `fuente_id`, `agente_id`, `nombre`, `apellido`, `email`, `telefono`, `telefono_alt`, `fecha_nacimiento`, `genero`, `documento_tipo`, `documento_num`, `pais`, `ciudad`, `direccion`, `categoria`, `notas`, `activo`, `creado_en`, `actualizado_en`) VALUES
	(1, 1, 2, 'Lucía', 'García', 'cliente1@ejemplo.com', '+51 987 654 300', NULL, NULL, NULL, 'DNI', NULL, 'Perú', NULL, NULL, 'VIP', NULL, 1, '2026-04-19 14:00:41', '2026-04-19 14:00:41'),
	(2, 2, 3, 'Íñigo', 'Núñez', 'cliente2@ejemplo.com', '+51 987 654 301', NULL, NULL, NULL, 'DNI', NULL, 'Perú', NULL, NULL, 'Nuevo', NULL, 1, '2026-04-19 14:00:41', '2026-04-19 14:00:41'),
	(3, 3, 4, 'Josué', 'Suárez', 'cliente3@ejemplo.com', '+51 987 654 302', NULL, NULL, NULL, 'DNI', NULL, 'Perú', NULL, NULL, 'Nuevo', NULL, 1, '2026-04-19 14:00:41', '2026-04-19 14:00:41'),
	(4, 4, 5, 'Ángel', 'López', 'cliente4@ejemplo.com', '+51 987 654 303', NULL, NULL, NULL, 'DNI', NULL, 'Perú', NULL, NULL, 'Recurrente', NULL, 1, '2026-04-19 14:00:41', '2026-04-19 14:00:41'),
	(5, 5, 6, 'Sofía', 'Rodríguez', 'cliente5@ejemplo.com', '+51 987 654 304', NULL, NULL, NULL, 'DNI', NULL, 'Perú', NULL, NULL, 'VIP', NULL, 1, '2026-04-19 14:00:41', '2026-04-19 14:00:41'),
	(6, 1, 7, 'Daniela', 'Pérez', 'cliente6@ejemplo.com', '+51 987 654 305', NULL, NULL, NULL, 'DNI', NULL, 'Perú', NULL, NULL, 'Nuevo', NULL, 1, '2026-04-19 14:00:41', '2026-04-19 14:00:41'),
	(7, 2, 8, 'Raúl', 'Sánchez', 'cliente7@ejemplo.com', '+51 987 654 306', NULL, NULL, NULL, 'DNI', NULL, 'Perú', NULL, NULL, 'Recurrente', NULL, 1, '2026-04-19 14:00:41', '2026-04-19 14:00:41'),
	(8, 3, 9, 'Verónica', 'Martínez', 'cliente8@ejemplo.com', '+51 987 654 307', NULL, NULL, NULL, 'DNI', NULL, 'Perú', NULL, NULL, 'Nuevo', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(9, 4, 10, 'Andrés', 'Torres', 'cliente9@ejemplo.com', '+51 987 654 308', NULL, NULL, NULL, 'DNI', NULL, 'Perú', NULL, NULL, 'VIP', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(10, 5, 11, 'Carmen', 'Ramírez', 'cliente10@ejemplo.com', '+51 987 654 309', NULL, NULL, NULL, 'DNI', NULL, 'Perú', NULL, NULL, 'Recurrente', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(11, 1, 2, 'Santiago', 'Díaz', 'cliente11@ejemplo.com', '+51 987 654 310', NULL, NULL, NULL, 'DNI', NULL, 'Otros', NULL, NULL, 'Nuevo', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(12, 2, 3, 'Mónica', 'Vásquez', 'cliente12@ejemplo.com', '+51 987 654 311', NULL, NULL, NULL, 'DNI', NULL, 'Otros', NULL, NULL, 'Nuevo', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(13, 3, 4, 'Felipe', 'Castro', 'cliente13@ejemplo.com', '+51 987 654 312', NULL, NULL, NULL, 'DNI', NULL, 'Otros', NULL, NULL, 'VIP', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(14, 4, 5, 'Úrsula', 'Ortiz', 'cliente14@ejemplo.com', '+51 987 654 313', NULL, NULL, NULL, 'DNI', NULL, 'Otros', NULL, NULL, 'Nuevo', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(15, 5, 6, 'Mateo', 'Gómez', 'cliente15@ejemplo.com', '+51 987 654 314', NULL, NULL, NULL, 'DNI', NULL, 'Otros', NULL, NULL, 'Nuevo', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(16, 1, 7, 'Beatriz', 'Ruiz', 'cliente16@ejemplo.com', '+51 987 654 315', NULL, NULL, NULL, 'DNI', NULL, 'Otros', NULL, NULL, 'Recurrente', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(17, 2, 8, 'Ignacio', 'Morales', 'cliente17@ejemplo.com', '+51 987 654 316', NULL, NULL, NULL, 'DNI', NULL, 'Otros', NULL, NULL, 'VIP', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(18, 3, 9, 'Leticia', 'Jiménez', 'cliente18@ejemplo.com', '+51 987 654 317', NULL, NULL, NULL, 'DNI', NULL, 'Otros', NULL, NULL, 'Nuevo', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(19, 4, 10, 'Damián', 'Cáceres', 'cliente19@ejemplo.com', '+51 987 654 318', NULL, NULL, NULL, 'DNI', NULL, 'Otros', NULL, NULL, 'Recurrente', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(20, 5, 11, 'Silvia', 'Vidal', 'cliente20@ejemplo.com', '+51 987 654 319', NULL, NULL, NULL, 'DNI', NULL, 'Otros', NULL, NULL, 'Nuevo', NULL, 1, '2026-04-19 14:00:42', '2026-04-19 14:00:42');

-- Volcando estructura para tabla viaje360_crm.cliente_etiquetas
CREATE TABLE IF NOT EXISTS `cliente_etiquetas` (
  `cliente_id` int unsigned NOT NULL,
  `etiqueta_id` int unsigned NOT NULL,
  PRIMARY KEY (`cliente_id`,`etiqueta_id`),
  KEY `fk_ce_etiqueta` (`etiqueta_id`),
  CONSTRAINT `fk_ce_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ce_etiqueta` FOREIGN KEY (`etiqueta_id`) REFERENCES `etiquetas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.cliente_etiquetas: ~0 rows (aproximadamente)
DELETE FROM `cliente_etiquetas`;

-- Volcando estructura para tabla viaje360_crm.configuracion_general
CREATE TABLE IF NOT EXISTS `configuracion_general` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Viaje 360 CRM',
  `documento_identidad` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moneda_simbolo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '$',
  `impuesto_nombre` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'IGV',
  `impuesto_porcentaje` decimal(5,2) DEFAULT '18.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.configuracion_general: ~0 rows (aproximadamente)
DELETE FROM `configuracion_general`;
INSERT INTO `configuracion_general` (`id`, `empresa_nombre`, `documento_identidad`, `direccion`, `telefono`, `logo_url`, `moneda_simbolo`, `impuesto_nombre`, `impuesto_porcentaje`) VALUES
	(1, 'Viaje 360 CRM', NULL, NULL, NULL, NULL, 'S/', 'IGV', 18.00);

-- Volcando estructura para tabla viaje360_crm.destinos
CREATE TABLE IF NOT EXISTS `destinos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pais_id` int unsigned NOT NULL,
  `nombre` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `imagen_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_destinos_pais` (`pais_id`),
  CONSTRAINT `fk_destinos_pais` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.destinos: ~10 rows (aproximadamente)
DELETE FROM `destinos`;
INSERT INTO `destinos` (`id`, `pais_id`, `nombre`, `descripcion`, `imagen_url`, `activo`) VALUES
	(1, 1, 'Cusco & Machu Picchu', 'La capital del imperio Inca.', NULL, 1),
	(2, 3, 'Cancún', 'Playas paradisíacas del Caribe mexicano.', NULL, 1),
	(3, 2, 'Medellín', 'La ciudad de la eterna primavera.', NULL, 1),
	(4, 4, 'Madrid', 'Corazón cultural de España.', NULL, 1),
	(5, 6, 'París', 'La ciudad del amor y las luces.', NULL, 1),
	(6, 5, 'Roma', 'Cuna de la civilización occidental.', NULL, 1),
	(7, 1, 'Arequipa & Colca', 'La ciudad blanca y el cañón profundo.', NULL, 1),
	(8, 2, 'Cartagena', 'Ciudad colonial amurallada.', NULL, 1),
	(9, 4, 'Barcelona', 'Arquitectura modernista y playa.', NULL, 1),
	(10, 6, 'Niza', 'La magia de la Riviera Francesa.', NULL, 1);

-- Volcando estructura para tabla viaje360_crm.etapas_pipeline
CREATE TABLE IF NOT EXISTS `etapas_pipeline` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` tinyint unsigned NOT NULL,
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6366F1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.etapas_pipeline: ~6 rows (aproximadamente)
DELETE FROM `etapas_pipeline`;
INSERT INTO `etapas_pipeline` (`id`, `nombre`, `orden`, `color`) VALUES
	(1, 'Prospecto', 1, '#94A3B8'),
	(2, 'Calificación', 2, '#3B82F6'),
	(3, 'Propuesta Enviada', 3, '#8B5CF6'),
	(4, 'Negociación', 4, '#F59E0B'),
	(5, 'Cerrado Ganado', 5, '#10B981'),
	(6, 'Cerrado Perdido', 6, '#EF4444');

-- Volcando estructura para tabla viaje360_crm.etiquetas
CREATE TABLE IF NOT EXISTS `etiquetas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.etiquetas: ~0 rows (aproximadamente)
DELETE FROM `etiquetas`;

-- Volcando estructura para tabla viaje360_crm.facturas
CREATE TABLE IF NOT EXISTS `facturas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reserva_id` int unsigned NOT NULL,
  `numero_factura` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_emision` date NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `impuesto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `estado` enum('Emitida','Pagada','Anulada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Emitida',
  `pdf_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reserva_id` (`reserva_id`),
  UNIQUE KEY `numero_factura` (`numero_factura`),
  CONSTRAINT `fk_fact_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.facturas: ~0 rows (aproximadamente)
DELETE FROM `facturas`;

-- Volcando estructura para tabla viaje360_crm.fuentes_origen
CREATE TABLE IF NOT EXISTS `fuentes_origen` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.fuentes_origen: ~5 rows (aproximadamente)
DELETE FROM `fuentes_origen`;
INSERT INTO `fuentes_origen` (`id`, `nombre`) VALUES
	(1, 'Sitio Web'),
	(2, 'Redes Sociales'),
	(3, 'WhatsApp'),
	(4, 'Referido'),
	(5, 'Feria de Viajes');

-- Volcando estructura para tabla viaje360_crm.interacciones
CREATE TABLE IF NOT EXISTS `interacciones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` int unsigned NOT NULL,
  `usuario_id` int unsigned NOT NULL,
  `tipo` enum('Llamada','Email','WhatsApp','Reunion','Nota','Cotizacion','Seguimiento') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `adjunto_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_int_cliente` (`cliente_id`),
  KEY `fk_int_usuario` (`usuario_id`),
  CONSTRAINT `fk_int_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_int_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.interacciones: ~20 rows (aproximadamente)
DELETE FROM `interacciones`;
INSERT INTO `interacciones` (`id`, `cliente_id`, `usuario_id`, `tipo`, `descripcion`, `adjunto_url`, `fecha`) VALUES
	(1, 1, 2, 'WhatsApp', 'Se contactó a Lucía para dar seguimiento a su interés en Cusco & Machu Picchu. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-19 14:00:41'),
	(2, 2, 3, 'Email', 'Se contactó a Íñigo para dar seguimiento a su interés en Cancún. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-18 14:00:41'),
	(3, 3, 4, 'Llamada', 'Se contactó a Josué para dar seguimiento a su interés en Medellín. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-17 14:00:41'),
	(4, 4, 5, 'WhatsApp', 'Se contactó a Ángel para dar seguimiento a su interés en Madrid. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-16 14:00:41'),
	(5, 5, 6, 'Llamada', 'Se contactó a Sofía para dar seguimiento a su interés en París. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-15 14:00:41'),
	(6, 6, 7, 'Email', 'Se contactó a Daniela para dar seguimiento a su interés en Roma. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-19 14:00:41'),
	(7, 7, 8, 'WhatsApp', 'Se contactó a Raúl para dar seguimiento a su interés en Arequipa & Colca. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-18 14:00:42'),
	(8, 8, 9, 'Email', 'Se contactó a Verónica para dar seguimiento a su interés en Cartagena. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-17 14:00:42'),
	(9, 9, 10, 'Llamada', 'Se contactó a Andrés para dar seguimiento a su interés en Barcelona. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-16 14:00:42'),
	(10, 10, 11, 'WhatsApp', 'Se contactó a Carmen para dar seguimiento a su interés en Niza. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-15 14:00:42'),
	(11, 11, 2, 'Llamada', 'Se contactó a Santiago para dar seguimiento a su interés en Cusco & Machu Picchu. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-19 14:00:42'),
	(12, 12, 3, 'Email', 'Se contactó a Mónica para dar seguimiento a su interés en Cancún. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-18 14:00:42'),
	(13, 13, 4, 'WhatsApp', 'Se contactó a Felipe para dar seguimiento a su interés en Medellín. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-17 14:00:42'),
	(14, 14, 5, 'Email', 'Se contactó a Úrsula para dar seguimiento a su interés en Madrid. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-16 14:00:42'),
	(15, 15, 6, 'Llamada', 'Se contactó a Mateo para dar seguimiento a su interés en París. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-15 14:00:42'),
	(16, 16, 7, 'WhatsApp', 'Se contactó a Beatriz para dar seguimiento a su interés en Roma. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-19 14:00:42'),
	(17, 17, 8, 'Llamada', 'Se contactó a Ignacio para dar seguimiento a su interés en Arequipa & Colca. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-18 14:00:42'),
	(18, 18, 9, 'Email', 'Se contactó a Leticia para dar seguimiento a su interés en Cartagena. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-17 14:00:42'),
	(19, 19, 10, 'WhatsApp', 'Se contactó a Damián para dar seguimiento a su interés en Barcelona. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-16 14:00:42'),
	(20, 20, 11, 'Email', 'Se contactó a Silvia para dar seguimiento a su interés en Niza. Muestra mucho interés en paquetes de Aventura.', NULL, '2026-04-15 14:00:42');

-- Volcando estructura para tabla viaje360_crm.metodos_pago
CREATE TABLE IF NOT EXISTS `metodos_pago` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.metodos_pago: ~3 rows (aproximadamente)
DELETE FROM `metodos_pago`;
INSERT INTO `metodos_pago` (`id`, `nombre`) VALUES
	(1, 'Transferencia Bancaria'),
	(2, 'Tarjeta de Crédito'),
	(3, 'Efectivo');

-- Volcando estructura para tabla viaje360_crm.oportunidades
CREATE TABLE IF NOT EXISTS `oportunidades` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` int unsigned NOT NULL,
  `agente_id` int unsigned NOT NULL,
  `paquete_id` int unsigned DEFAULT NULL,
  `etapa_id` int unsigned NOT NULL,
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_estimado` decimal(12,2) DEFAULT NULL,
  `probabilidad` tinyint unsigned DEFAULT '50',
  `fecha_cierre` date DEFAULT NULL,
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` enum('Activa','Ganada','Perdida','Cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Activa',
  `motivo_perdida` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_op_cliente` (`cliente_id`),
  KEY `fk_op_agente` (`agente_id`),
  KEY `fk_op_paquete` (`paquete_id`),
  KEY `fk_op_etapa` (`etapa_id`),
  CONSTRAINT `fk_op_agente` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_op_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `fk_op_etapa` FOREIGN KEY (`etapa_id`) REFERENCES `etapas_pipeline` (`id`),
  CONSTRAINT `fk_op_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.oportunidades: ~20 rows (aproximadamente)
DELETE FROM `oportunidades`;
INSERT INTO `oportunidades` (`id`, `cliente_id`, `agente_id`, `paquete_id`, `etapa_id`, `titulo`, `valor_estimado`, `probabilidad`, `fecha_cierre`, `notas`, `estado`, `motivo_perdida`, `creado_en`, `actualizado_en`) VALUES
	(1, 1, 2, NULL, 1, 'Viaje a Cusco & Machu Picchu', 1000.00, 20, NULL, NULL, 'Activa', NULL, '2026-04-19 14:00:41', '2026-04-19 14:00:41'),
	(2, 2, 3, NULL, 2, 'Viaje a Cancún', 1200.00, 24, NULL, NULL, 'Activa', NULL, '2026-04-04 14:00:41', '2026-04-19 14:00:41'),
	(3, 3, 4, NULL, 3, 'Viaje a Medellín', 1400.00, 28, NULL, NULL, 'Activa', NULL, '2026-03-20 14:00:41', '2026-04-19 14:00:41'),
	(4, 4, 5, NULL, 4, 'Viaje a Madrid', 1600.00, 32, NULL, NULL, 'Activa', NULL, '2026-03-05 14:00:41', '2026-04-19 14:00:41'),
	(5, 5, 6, NULL, 5, 'Viaje a París', 1800.00, 36, NULL, NULL, 'Activa', NULL, '2026-02-18 14:00:41', '2026-04-19 14:00:41'),
	(6, 6, 7, NULL, 1, 'Viaje a Roma', 2000.00, 40, NULL, NULL, 'Perdida', NULL, '2026-02-03 14:00:41', '2026-04-19 14:00:41'),
	(7, 7, 8, NULL, 2, 'Viaje a Arequipa & Colca', 2200.00, 44, NULL, NULL, 'Activa', NULL, '2026-01-19 14:00:41', '2026-04-19 14:00:41'),
	(8, 8, 9, NULL, 3, 'Viaje a Cartagena', 2400.00, 48, NULL, NULL, 'Activa', NULL, '2026-01-04 14:00:42', '2026-04-19 14:00:42'),
	(9, 9, 10, NULL, 4, 'Viaje a Barcelona', 2600.00, 52, NULL, NULL, 'Activa', NULL, '2025-12-20 14:00:42', '2026-04-19 14:00:42'),
	(10, 10, 11, NULL, 5, 'Viaje a Niza', 2800.00, 56, NULL, NULL, 'Activa', NULL, '2025-12-05 14:00:42', '2026-04-19 14:00:42'),
	(11, 11, 2, NULL, 1, 'Viaje a Cusco & Machu Picchu', 3000.00, 60, NULL, NULL, 'Activa', NULL, '2025-11-20 14:00:42', '2026-04-19 14:00:42'),
	(12, 12, 3, NULL, 2, 'Viaje a Cancún', 3200.00, 64, NULL, NULL, 'Perdida', NULL, '2025-11-05 14:00:42', '2026-04-19 14:00:42'),
	(13, 13, 4, NULL, 3, 'Viaje a Medellín', 3400.00, 68, NULL, NULL, 'Activa', NULL, '2025-10-21 14:00:42', '2026-04-19 14:00:42'),
	(14, 14, 5, NULL, 4, 'Viaje a Madrid', 3600.00, 72, NULL, NULL, 'Activa', NULL, '2025-10-06 14:00:42', '2026-04-19 14:00:42'),
	(15, 15, 6, NULL, 5, 'Viaje a París', 3800.00, 76, NULL, NULL, 'Activa', NULL, '2025-09-21 14:00:42', '2026-04-19 14:00:42'),
	(16, 16, 7, NULL, 1, 'Viaje a Roma', 4000.00, 80, NULL, NULL, 'Activa', NULL, '2025-09-06 14:00:42', '2026-04-19 14:00:42'),
	(17, 17, 8, NULL, 2, 'Viaje a Arequipa & Colca', 4200.00, 84, NULL, NULL, 'Activa', NULL, '2025-08-22 14:00:42', '2026-04-19 14:00:42'),
	(18, 18, 9, NULL, 3, 'Viaje a Cartagena', 4400.00, 88, NULL, NULL, 'Perdida', NULL, '2025-08-07 14:00:42', '2026-04-19 14:00:42'),
	(19, 19, 10, NULL, 4, 'Viaje a Barcelona', 4600.00, 92, NULL, NULL, 'Activa', NULL, '2025-07-23 14:00:42', '2026-04-19 14:00:42'),
	(20, 20, 11, NULL, 5, 'Viaje a Niza', 4800.00, 96, NULL, NULL, 'Activa', NULL, '2025-07-08 14:00:42', '2026-04-19 14:00:42');

-- Volcando estructura para tabla viaje360_crm.oportunidad_historial
CREATE TABLE IF NOT EXISTS `oportunidad_historial` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `oportunidad_id` int unsigned NOT NULL,
  `etapa_anterior` int unsigned DEFAULT NULL,
  `etapa_nueva` int unsigned NOT NULL,
  `usuario_id` int unsigned NOT NULL,
  `nota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cambiado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_oh_oportunidad` (`oportunidad_id`),
  CONSTRAINT `fk_oh_oportunidad` FOREIGN KEY (`oportunidad_id`) REFERENCES `oportunidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.oportunidad_historial: ~0 rows (aproximadamente)
DELETE FROM `oportunidad_historial`;

-- Volcando estructura para tabla viaje360_crm.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reserva_id` int unsigned NOT NULL,
  `metodo_id` int unsigned NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `referencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprobante_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('Pendiente','Verificado','Rechazado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pendiente',
  `fecha_pago` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registrado_por` int unsigned DEFAULT NULL,
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `fk_pagos_reserva` (`reserva_id`),
  KEY `fk_pagos_metodo` (`metodo_id`),
  KEY `fk_pagos_usuario` (`registrado_por`),
  CONSTRAINT `fk_pagos_metodo` FOREIGN KEY (`metodo_id`) REFERENCES `metodos_pago` (`id`),
  CONSTRAINT `fk_pagos_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`),
  CONSTRAINT `fk_pagos_usuario` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.pagos: ~7 rows (aproximadamente)
DELETE FROM `pagos`;
INSERT INTO `pagos` (`id`, `reserva_id`, `metodo_id`, `monto`, `referencia`, `comprobante_url`, `estado`, `fecha_pago`, `registrado_por`, `notas`) VALUES
	(1, 1, 1, 450.00, NULL, NULL, 'Pendiente', '2026-04-19 14:00:41', NULL, NULL),
	(2, 2, 3, 680.00, NULL, NULL, 'Verificado', '2026-02-19 14:00:41', NULL, NULL),
	(3, 3, 2, 950.00, NULL, NULL, 'Verificado', '2025-12-19 14:00:41', NULL, NULL),
	(4, 4, 1, 1260.00, NULL, NULL, 'Pendiente', '2025-10-19 14:00:42', NULL, NULL),
	(5, 6, 2, 2000.00, NULL, NULL, 'Verificado', '2026-02-19 14:00:42', NULL, NULL),
	(6, 8, 3, 2900.00, NULL, NULL, 'Verificado', '2025-10-19 14:00:42', NULL, NULL),
	(7, 10, 1, 3960.00, NULL, NULL, 'Pendiente', '2026-02-19 14:00:42', NULL, NULL);

-- Volcando estructura para tabla viaje360_crm.paises
CREATE TABLE IF NOT EXISTS `paises` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `zona` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.paises: ~6 rows (aproximadamente)
DELETE FROM `paises`;
INSERT INTO `paises` (`id`, `nombre`, `codigo`, `zona`) VALUES
	(1, 'Perú', 'PE', 'América del Sur'),
	(2, 'Colombia', 'CO', 'América del Sur'),
	(3, 'México', 'MX', 'América del Norte'),
	(4, 'España', 'ES', 'Europa'),
	(5, 'Italia', 'IT', 'Europa'),
	(6, 'Francia', 'FR', 'Europa');

-- Volcando estructura para tabla viaje360_crm.paquetes
CREATE TABLE IF NOT EXISTS `paquetes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `destino_id` int unsigned NOT NULL,
  `categoria_id` int unsigned DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `itinerario` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `duracion_dias` smallint unsigned DEFAULT NULL,
  `costo_neto` decimal(10,2) DEFAULT '0.00',
  `precio_base` decimal(10,2) NOT NULL,
  `precio_adulto` decimal(10,2) DEFAULT NULL,
  `precio_nino` decimal(10,2) DEFAULT NULL,
  `incluye` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `no_incluye` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `imagen_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_paquetes_destino` (`destino_id`),
  KEY `fk_paquetes_categoria` (`categoria_id`),
  CONSTRAINT `fk_paquetes_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_paquete` (`id`),
  CONSTRAINT `fk_paquetes_destino` FOREIGN KEY (`destino_id`) REFERENCES `destinos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.paquetes: ~10 rows (aproximadamente)
DELETE FROM `paquetes`;
INSERT INTO `paquetes` (`id`, `destino_id`, `categoria_id`, `nombre`, `descripcion`, `itinerario`, `duracion_dias`, `costo_neto`, `precio_base`, `precio_adulto`, `precio_nino`, `incluye`, `no_incluye`, `imagen_url`, `disponible`, `creado_en`) VALUES
	(1, 1, 3, 'Caminos del Inca', NULL, NULL, 5, 800.00, 1200.00, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-19 14:00:41'),
	(2, 2, 2, 'Verano en Cancún', NULL, NULL, 7, 600.00, 950.00, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-19 14:00:41'),
	(3, 5, 2, 'Luces de París', NULL, NULL, 10, 1500.00, 2200.00, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-19 14:00:41'),
	(4, 3, 1, 'Eje Cafetero', NULL, NULL, 4, 500.00, 800.00, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-19 14:00:41'),
	(5, 7, 1, 'Ruta Colca Extrema', NULL, NULL, 3, 300.00, 450.00, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-19 14:00:41'),
	(6, 8, 2, 'Magia de Cartagena', NULL, NULL, 6, 750.00, 1100.00, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-19 14:00:41'),
	(7, 9, 3, 'Ruta Gaudí', NULL, NULL, 7, 1000.00, 1600.00, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-19 14:00:41'),
	(8, 10, 2, 'Costa Azul VIP', NULL, NULL, 8, 1800.00, 2500.00, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-19 14:00:41'),
	(9, 6, 3, 'Roma Ancestral', NULL, NULL, 6, 1300.00, 1900.00, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-19 14:00:41'),
	(10, 1, 1, 'Salkantay Trek', NULL, NULL, 5, 600.00, 850.00, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-19 14:00:41');

-- Volcando estructura para tabla viaje360_crm.paquete_proveedores
CREATE TABLE IF NOT EXISTS `paquete_proveedores` (
  `paquete_id` int unsigned NOT NULL,
  `proveedor_id` int unsigned NOT NULL,
  `descripcion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`paquete_id`,`proveedor_id`),
  KEY `fk_pp_proveedor` (`proveedor_id`),
  CONSTRAINT `fk_pp_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pp_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.paquete_proveedores: ~0 rows (aproximadamente)
DELETE FROM `paquete_proveedores`;

-- Volcando estructura para tabla viaje360_crm.pasajeros
CREATE TABLE IF NOT EXISTS `pasajeros` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reserva_id` int unsigned NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pasaporte` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `tipo` enum('Adulto','Ni??o','Infante') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Adulto',
  PRIMARY KEY (`id`),
  KEY `fk_pas_reserva` (`reserva_id`),
  CONSTRAINT `fk_pas_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.pasajeros: ~0 rows (aproximadamente)
DELETE FROM `pasajeros`;

-- Volcando estructura para tabla viaje360_crm.proveedores
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('Aerolinea','Hotel','Operadora','Seguro','Transporte','Otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contacto` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sitio_web` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.proveedores: ~10 rows (aproximadamente)
DELETE FROM `proveedores`;
INSERT INTO `proveedores` (`id`, `nombre`, `tipo`, `contacto`, `email`, `telefono`, `pais`, `sitio_web`, `notas`, `activo`, `creado_en`) VALUES
	(1, 'Proveedor 1 Travel Cia', 'Aerolinea', 'Contacto Manager 1', 'prov1@proveedor.com', '+10000555', 'Perú', NULL, NULL, 1, '2026-04-19 14:00:41'),
	(2, 'Proveedor 2 Travel Cia', 'Hotel', 'Contacto Manager 2', 'prov2@proveedor.com', '+10001555', 'Colombia', NULL, NULL, 1, '2026-04-19 14:00:41'),
	(3, 'Proveedor 3 Travel Cia', 'Operadora', 'Contacto Manager 3', 'prov3@proveedor.com', '+10002555', 'México', NULL, NULL, 1, '2026-04-19 14:00:41'),
	(4, 'Proveedor 4 Travel Cia', 'Seguro', 'Contacto Manager 4', 'prov4@proveedor.com', '+10003555', 'España', NULL, NULL, 1, '2026-04-19 14:00:41'),
	(5, 'Proveedor 5 Travel Cia', 'Transporte', 'Contacto Manager 5', 'prov5@proveedor.com', '+10004555', 'Italia', NULL, NULL, 1, '2026-04-19 14:00:41'),
	(6, 'Proveedor 6 Travel Cia', 'Otro', 'Contacto Manager 6', 'prov6@proveedor.com', '+10005555', 'Francia', NULL, NULL, 1, '2026-04-19 14:00:41'),
	(7, 'Proveedor 7 Travel Cia', 'Aerolinea', 'Contacto Manager 7', 'prov7@proveedor.com', '+10006555', 'Perú', NULL, NULL, 1, '2026-04-19 14:00:41'),
	(8, 'Proveedor 8 Travel Cia', 'Hotel', 'Contacto Manager 8', 'prov8@proveedor.com', '+10007555', 'Colombia', NULL, NULL, 1, '2026-04-19 14:00:41'),
	(9, 'Proveedor 9 Travel Cia', 'Operadora', 'Contacto Manager 9', 'prov9@proveedor.com', '+10008555', 'México', NULL, NULL, 1, '2026-04-19 14:00:41'),
	(10, 'Proveedor 10 Travel Cia', 'Seguro', 'Contacto Manager 10', 'prov10@proveedor.com', '+10009555', 'España', NULL, NULL, 1, '2026-04-19 14:00:41');

-- Volcando estructura para tabla viaje360_crm.reservas
CREATE TABLE IF NOT EXISTS `reservas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `oportunidad_id` int unsigned DEFAULT NULL,
  `cliente_id` int unsigned NOT NULL,
  `agente_id` int unsigned NOT NULL,
  `paquete_id` int unsigned DEFAULT NULL,
  `codigo_reserva` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_salida` date NOT NULL,
  `fecha_regreso` date DEFAULT NULL,
  `num_adultos` tinyint unsigned NOT NULL DEFAULT '1',
  `num_ninos` tinyint unsigned NOT NULL DEFAULT '0',
  `precio_total` decimal(12,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `impuesto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_final` decimal(12,2) NOT NULL,
  `costo_neto` decimal(12,2) DEFAULT '0.00',
  `estado` enum('Pendiente','Confirmada','En Curso','Completada','Cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pendiente',
  `notas_internas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_reserva` (`codigo_reserva`),
  KEY `fk_res_oportunidad` (`oportunidad_id`),
  KEY `fk_res_cliente` (`cliente_id`),
  KEY `fk_res_agente` (`agente_id`),
  KEY `fk_res_paquete` (`paquete_id`),
  CONSTRAINT `fk_res_agente` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_res_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `fk_res_oportunidad` FOREIGN KEY (`oportunidad_id`) REFERENCES `oportunidades` (`id`),
  CONSTRAINT `fk_res_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.reservas: ~10 rows (aproximadamente)
DELETE FROM `reservas`;
INSERT INTO `reservas` (`id`, `oportunidad_id`, `cliente_id`, `agente_id`, `paquete_id`, `codigo_reserva`, `fecha_salida`, `fecha_regreso`, `num_adultos`, `num_ninos`, `precio_total`, `descuento`, `impuesto`, `total_final`, `costo_neto`, `estado`, `notas_internas`, `creado_en`, `actualizado_en`) VALUES
	(1, NULL, 1, 2, 1, 'BK-2024100', '2026-05-19', NULL, 1, 0, 1500.00, 0.00, 0.00, 1500.00, 1000.00, 'Completada', NULL, '2026-04-19 14:00:41', '2026-04-19 14:00:41'),
	(2, NULL, 3, 4, 3, 'BK-2024102', '2026-03-21', NULL, 1, 0, 1700.00, 0.00, 0.00, 1700.00, 1100.00, 'Confirmada', NULL, '2026-02-19 14:00:41', '2026-04-19 14:00:41'),
	(3, NULL, 5, 6, 5, 'BK-2024104', '2026-01-18', NULL, 1, 0, 1900.00, 0.00, 0.00, 1900.00, 1200.00, 'Cancelada', NULL, '2025-12-19 14:00:41', '2026-04-19 14:00:41'),
	(4, NULL, 7, 8, 7, 'BK-2024106', '2025-11-18', NULL, 1, 0, 2100.00, 0.00, 0.00, 2100.00, 1300.00, 'Confirmada', NULL, '2025-10-19 14:00:42', '2026-04-19 14:00:42'),
	(5, NULL, 9, 10, 9, 'BK-2024108', '2026-05-19', NULL, 1, 0, 2300.00, 0.00, 0.00, 2300.00, 1400.00, 'Cancelada', NULL, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(6, NULL, 11, 2, 1, 'BK-2024110', '2026-03-21', NULL, 1, 0, 2500.00, 0.00, 0.00, 2500.00, 1500.00, 'Completada', NULL, '2026-02-19 14:00:42', '2026-04-19 14:00:42'),
	(7, NULL, 13, 4, 3, 'BK-2024112', '2026-01-18', NULL, 1, 0, 2700.00, 0.00, 0.00, 2700.00, 1600.00, 'Cancelada', NULL, '2025-12-19 14:00:42', '2026-04-19 14:00:42'),
	(8, NULL, 15, 6, 5, 'BK-2024114', '2025-11-18', NULL, 1, 0, 2900.00, 0.00, 0.00, 2900.00, 1700.00, 'Confirmada', NULL, '2025-10-19 14:00:42', '2026-04-19 14:00:42'),
	(9, NULL, 17, 8, 7, 'BK-2024116', '2026-05-19', NULL, 1, 0, 3100.00, 0.00, 0.00, 3100.00, 1800.00, 'Cancelada', NULL, '2026-04-19 14:00:42', '2026-04-19 14:00:42'),
	(10, NULL, 19, 10, 9, 'BK-2024118', '2026-03-21', NULL, 1, 0, 3300.00, 0.00, 0.00, 3300.00, 1900.00, 'Confirmada', NULL, '2026-02-19 14:00:42', '2026-04-19 14:00:42');

-- Volcando estructura para tabla viaje360_crm.reserva_servicios
CREATE TABLE IF NOT EXISTS `reserva_servicios` (
  `reserva_id` int unsigned NOT NULL,
  `servicio_id` int unsigned NOT NULL,
  `cantidad` tinyint unsigned NOT NULL DEFAULT '1',
  `precio_unit` decimal(10,2) NOT NULL,
  PRIMARY KEY (`reserva_id`,`servicio_id`),
  KEY `fk_rs_servicio` (`servicio_id`),
  CONSTRAINT `fk_rs_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rs_servicio` FOREIGN KEY (`servicio_id`) REFERENCES `servicios_adicionales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.reserva_servicios: ~0 rows (aproximadamente)
DELETE FROM `reserva_servicios`;

-- Volcando estructura para tabla viaje360_crm.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `permisos` json DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.roles: ~2 rows (aproximadamente)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `permisos`, `activo`, `creado_en`) VALUES
	(1, 'Administrador', 'Acceso total al sistema', NULL, 1, '2026-04-19 14:00:41'),
	(2, 'Agente de Ventas', 'Gestión de sus propios clientes y reservas', NULL, 1, '2026-04-19 14:00:41');

-- Volcando estructura para tabla viaje360_crm.servicios_adicionales
CREATE TABLE IF NOT EXISTS `servicios_adicionales` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) NOT NULL,
  `tipo` enum('Hotel','Transporte','Seguro','Tour','Visado','Otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Otro',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.servicios_adicionales: ~0 rows (aproximadamente)
DELETE FROM `servicios_adicionales`;

-- Volcando estructura para tabla viaje360_crm.sesiones
CREATE TABLE IF NOT EXISTS `sesiones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int unsigned NOT NULL,
  `token_jti` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expira_en` datetime NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_jti` (`token_jti`),
  KEY `fk_sesiones_usuario` (`usuario_id`),
  CONSTRAINT `fk_sesiones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.sesiones: ~0 rows (aproximadamente)
DELETE FROM `sesiones`;

-- Volcando estructura para tabla viaje360_crm.tareas
CREATE TABLE IF NOT EXISTS `tareas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `asignado_a` int unsigned NOT NULL,
  `creado_por` int unsigned NOT NULL,
  `cliente_id` int unsigned DEFAULT NULL,
  `oportunidad_id` int unsigned DEFAULT NULL,
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `prioridad` enum('Baja','Media','Alta','Urgente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Media',
  `estado` enum('Pendiente','En Progreso','Completada','Cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pendiente',
  `fecha_vence` datetime DEFAULT NULL,
  `completada_en` datetime DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_tareas_asignado` (`asignado_a`),
  KEY `fk_tareas_creador` (`creado_por`),
  KEY `fk_tareas_cliente` (`cliente_id`),
  KEY `fk_tareas_oportunidad` (`oportunidad_id`),
  CONSTRAINT `fk_tareas_asignado` FOREIGN KEY (`asignado_a`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_tareas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `fk_tareas_creador` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_tareas_oportunidad` FOREIGN KEY (`oportunidad_id`) REFERENCES `oportunidades` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.tareas: ~10 rows (aproximadamente)
DELETE FROM `tareas`;
INSERT INTO `tareas` (`id`, `asignado_a`, `creado_por`, `cliente_id`, `oportunidad_id`, `titulo`, `descripcion`, `prioridad`, `estado`, `fecha_vence`, `completada_en`, `creado_en`) VALUES
	(1, 2, 1, 1, NULL, 'Seguimiento de Reserva - Lucía', NULL, 'Urgente', 'Completada', '2026-04-19 14:00:41', NULL, '2026-04-19 14:00:41'),
	(2, 4, 1, 3, NULL, 'Seguimiento de Reserva - Josué', NULL, 'Media', 'Pendiente', '2026-04-21 14:00:41', NULL, '2026-04-19 14:00:41'),
	(3, 6, 1, 5, NULL, 'Seguimiento de Reserva - Sofía', NULL, 'Urgente', 'Pendiente', '2026-04-23 14:00:41', NULL, '2026-04-19 14:00:41'),
	(4, 8, 1, 7, NULL, 'Seguimiento de Reserva - Raúl', NULL, 'Alta', 'Completada', '2026-04-25 14:00:42', NULL, '2026-04-19 14:00:42'),
	(5, 10, 1, 9, NULL, 'Seguimiento de Reserva - Andrés', NULL, 'Urgente', 'Pendiente', '2026-04-27 14:00:42', NULL, '2026-04-19 14:00:42'),
	(6, 2, 1, 11, NULL, 'Seguimiento de Reserva - Santiago', NULL, 'Media', 'Pendiente', '2026-04-29 14:00:42', NULL, '2026-04-19 14:00:42'),
	(7, 4, 1, 13, NULL, 'Seguimiento de Reserva - Felipe', NULL, 'Urgente', 'Completada', '2026-05-01 14:00:42', NULL, '2026-04-19 14:00:42'),
	(8, 6, 1, 15, NULL, 'Seguimiento de Reserva - Mateo', NULL, 'Media', 'Pendiente', '2026-05-03 14:00:42', NULL, '2026-04-19 14:00:42'),
	(9, 8, 1, 17, NULL, 'Seguimiento de Reserva - Ignacio', NULL, 'Urgente', 'Pendiente', '2026-05-05 14:00:42', NULL, '2026-04-19 14:00:42'),
	(10, 10, 1, 19, NULL, 'Seguimiento de Reserva - Damián', NULL, 'Alta', 'Completada', '2026-05-07 14:00:42', NULL, '2026-04-19 14:00:42');

-- Volcando estructura para tabla viaje360_crm.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `rol_id` int unsigned NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `ultimo_login` datetime DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_usuarios_rol` (`rol_id`),
  CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla viaje360_crm.usuarios: ~11 rows (aproximadamente)
DELETE FROM `usuarios`;
INSERT INTO `usuarios` (`id`, `rol_id`, `nombre`, `apellido`, `email`, `password_hash`, `telefono`, `avatar_url`, `activo`, `ultimo_login`, `creado_en`) VALUES
	(1, 1, 'Admin', 'Sistema', 'admin@viaje360.com', '$2b$10$urUM50qsTg/3RbDI1BriMu6EgXKNSZIVjfBmdFISxvGAXZMngIJbW', NULL, NULL, 1, '2026-04-22 12:49:12', '2026-04-19 14:00:41'),
	(2, 2, 'María', 'Rodríguez', 'maria@viaje360.com', '$2b$10$urUM50qsTg/3RbDI1BriMu6EgXKNSZIVjfBmdFISxvGAXZMngIJbW', NULL, NULL, 1, NULL, '2026-04-19 14:00:41'),
	(3, 2, 'Sofía', 'Pérez', 'sofia@viaje360.com', '$2b$10$urUM50qsTg/3RbDI1BriMu6EgXKNSZIVjfBmdFISxvGAXZMngIJbW', NULL, NULL, 1, NULL, '2026-04-19 14:00:41'),
	(4, 2, 'Juan', 'Órdoñez', 'juan@viaje360.com', '$2b$10$urUM50qsTg/3RbDI1BriMu6EgXKNSZIVjfBmdFISxvGAXZMngIJbW', NULL, NULL, 1, NULL, '2026-04-19 14:00:41'),
	(5, 2, 'Carlos', 'Gutiérrez', 'carlos@viaje360.com', '$2b$10$urUM50qsTg/3RbDI1BriMu6EgXKNSZIVjfBmdFISxvGAXZMngIJbW', NULL, NULL, 1, NULL, '2026-04-19 14:00:41'),
	(6, 2, 'Ana', 'López', 'ana@viaje360.com', '$2b$10$urUM50qsTg/3RbDI1BriMu6EgXKNSZIVjfBmdFISxvGAXZMngIJbW', NULL, NULL, 1, NULL, '2026-04-19 14:00:41'),
	(7, 2, 'Luis', 'Martínez', 'luis@viaje360.com', '$2b$10$urUM50qsTg/3RbDI1BriMu6EgXKNSZIVjfBmdFISxvGAXZMngIJbW', NULL, NULL, 1, NULL, '2026-04-19 14:00:41'),
	(8, 2, 'Luisa', 'Fernández', 'luisa@viaje360.com', '$2b$10$urUM50qsTg/3RbDI1BriMu6EgXKNSZIVjfBmdFISxvGAXZMngIJbW', NULL, NULL, 1, NULL, '2026-04-19 14:00:41'),
	(9, 2, 'Pedro', 'Gómez', 'pedro@viaje360.com', '$2b$10$urUM50qsTg/3RbDI1BriMu6EgXKNSZIVjfBmdFISxvGAXZMngIJbW', NULL, NULL, 1, NULL, '2026-04-19 14:00:41'),
	(10, 2, 'Carmen', 'Díaz', 'carmen@viaje360.com', '$2b$10$urUM50qsTg/3RbDI1BriMu6EgXKNSZIVjfBmdFISxvGAXZMngIJbW', NULL, NULL, 1, NULL, '2026-04-19 14:00:41'),
	(11, 2, 'Roberto', 'Vargas', 'roberto@viaje360.com', '$2b$10$urUM50qsTg/3RbDI1BriMu6EgXKNSZIVjfBmdFISxvGAXZMngIJbW', NULL, NULL, 1, NULL, '2026-04-19 14:00:41');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
