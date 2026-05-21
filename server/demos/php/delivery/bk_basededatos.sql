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


-- Volcando estructura de base de datos para delivery_crm
CREATE DATABASE IF NOT EXISTS `delivery_crm` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `delivery_crm`;

-- Volcando estructura para tabla delivery_crm.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.cache: ~0 rows (aproximadamente)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('crm-delivery-cache-spatie.permission.cache', 'a:3:{s:5:"alias";a:4:{s:1:"a";s:2:"id";s:1:"b";s:4:"name";s:1:"c";s:10:"guard_name";s:1:"r";s:5:"roles";}s:11:"permissions";a:29:{i:0;a:4:{s:1:"a";i:1;s:1:"b";s:13:"ver dashboard";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:1;a:4:{s:1:"a";i:2;s:1:"b";s:12:"ver clientes";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:2;a:4:{s:1:"a";i:3;s:1:"b";s:14:"crear clientes";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:3;a:4:{s:1:"a";i:4;s:1:"b";s:15:"editar clientes";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:4;a:4:{s:1:"a";i:5;s:1:"b";s:17:"eliminar clientes";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:"a";i:6;s:1:"b";s:13:"ver productos";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:6;a:4:{s:1:"a";i:7;s:1:"b";s:15:"crear productos";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:"a";i:8;s:1:"b";s:16:"editar productos";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:"a";i:9;s:1:"b";s:18:"eliminar productos";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:9;a:4:{s:1:"a";i:10;s:1:"b";s:11:"ver pedidos";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:10;a:4:{s:1:"a";i:11;s:1:"b";s:13:"crear pedidos";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:11;a:4:{s:1:"a";i:12;s:1:"b";s:14:"editar pedidos";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:12;a:4:{s:1:"a";i:13;s:1:"b";s:16:"cancelar pedidos";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:13;a:4:{s:1:"a";i:14;s:1:"b";s:16:"ver repartidores";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:14;a:4:{s:1:"a";i:15;s:1:"b";s:18:"crear repartidores";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:"a";i:16;s:1:"b";s:19:"editar repartidores";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:16;a:4:{s:1:"a";i:17;s:1:"b";s:21:"eliminar repartidores";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:17;a:4:{s:1:"a";i:18;s:1:"b";s:12:"ver entregas";s:1:"c";s:3:"web";s:1:"r";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:18;a:4:{s:1:"a";i:19;s:1:"b";s:16:"asignar entregas";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:19;a:4:{s:1:"a";i:20;s:1:"b";s:19:"actualizar entregas";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:20;a:4:{s:1:"a";i:21;s:1:"b";s:9:"ver pagos";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:21;a:4:{s:1:"a";i:22;s:1:"b";s:15:"registrar pagos";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:22;a:4:{s:1:"a";i:23;s:1:"b";s:12:"ver reportes";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:23;a:4:{s:1:"a";i:24;s:1:"b";s:12:"ver usuarios";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:24;a:4:{s:1:"a";i:25;s:1:"b";s:14:"crear usuarios";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:25;a:4:{s:1:"a";i:26;s:1:"b";s:15:"editar usuarios";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:26;a:4:{s:1:"a";i:27;s:1:"b";s:17:"eliminar usuarios";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:27;a:4:{s:1:"a";i:28;s:1:"b";s:17:"ver configuracion";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:28;a:4:{s:1:"a";i:29;s:1:"b";s:20:"editar configuracion";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}}s:5:"roles";a:4:{i:0;a:3:{s:1:"a";i:1;s:1:"b";s:11:"super-admin";s:1:"c";s:3:"web";}i:1;a:3:{s:1:"a";i:2;s:1:"b";s:5:"admin";s:1:"c";s:3:"web";}i:2;a:3:{s:1:"a";i:3;s:1:"b";s:8:"operador";s:1:"c";s:3:"web";}i:3;a:3:{s:1:"a";i:4;s:1:"b";s:10:"repartidor";s:1:"c";s:3:"web";}}}', 1777783224);

-- Volcando estructura para tabla delivery_crm.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Volcando estructura para tabla delivery_crm.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `icono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'bi-tag',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#007bff',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `orden` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorias_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.categorias: ~10 rows (aproximadamente)
DELETE FROM `categorias`;
INSERT INTO `categorias` (`id`, `nombre`, `slug`, `descripcion`, `icono`, `color`, `activo`, `orden`, `created_at`, `updated_at`) VALUES
	(1, 'Comida Rápida', 'comida-rapida', NULL, 'bi-egg-fried', '#FF6B35', 1, 1, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(2, 'Bebidas', 'bebidas', NULL, 'bi-cup-hot', '#007BFF', 1, 2, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(3, 'Postres', 'postres', NULL, 'bi-cake2', '#E83E8C', 1, 3, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(4, 'Ensaladas', 'ensaladas', NULL, 'bi-flower1', '#28A745', 1, 4, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(5, 'Pizzas', 'pizzas', NULL, 'bi-circle-square', '#DC3545', 1, 5, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(6, 'Pollos y Carnes', 'pollos-carnes', NULL, 'bi-egg', '#FFC107', 1, 6, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(7, 'Menús del Día', 'menus-del-dia', NULL, 'bi-journal-text', '#6F42C1', 1, 7, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(8, 'Comida Marina', 'comida-marina', NULL, 'bi-droplet', '#17A2B8', 1, 8, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(9, 'Desayunos', 'desayunos', NULL, 'bi-sun', '#F0AD4E', 1, 9, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(10, 'Otros', 'otros', NULL, 'bi-box', '#6C757D', 1, 10, '2026-05-02 09:33:33', '2026-05-02 09:33:33');

-- Volcando estructura para tabla delivery_crm.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono_alt` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT 'Lima',
  `distrito` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `tipo` enum('regular','frecuente','vip') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'regular',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.clientes: ~12 rows (aproximadamente)
DELETE FROM `clientes`;
INSERT INTO `clientes` (`id`, `nombre`, `apellido`, `email`, `telefono`, `telefono_alt`, `direccion`, `referencia`, `ciudad`, `distrito`, `latitud`, `longitud`, `tipo`, `notas`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Juan', 'García López', 'juan@email.com', '987654321', NULL, 'Av. Arequipa 1234', NULL, 'Lima', 'Miraflores', NULL, NULL, 'vip', NULL, 1, '2026-04-17 22:33:33', '2026-04-17 22:33:33', NULL),
	(2, 'María', 'Torres Silva', 'maria@email.com', '976543210', NULL, 'Jr. Lima 567', NULL, 'Lima', 'San Isidro', NULL, NULL, 'frecuente', NULL, 1, '2026-04-23 21:33:33', '2026-04-23 21:33:33', NULL),
	(3, 'Carlos', 'Ruiz Méndez', 'carlos@email.com', '965432109', NULL, 'Av. Brasil 890', NULL, 'Lima', 'Pueblo Libre', NULL, NULL, 'regular', NULL, 1, '2026-04-05 17:33:33', '2026-04-05 17:33:33', NULL),
	(4, 'Ana', 'Flores Castro', 'ana@email.com', '954321098', NULL, 'Calle Los Pinos 12', NULL, 'Lima', 'Surco', NULL, NULL, 'frecuente', NULL, 1, '2026-03-22 03:33:33', '2026-03-22 03:33:33', NULL),
	(5, 'Luis', 'Mamani Quispe', NULL, '943210987', NULL, 'Av. Colonial 456', NULL, 'Lima', 'Cercado', NULL, NULL, 'regular', NULL, 1, '2026-04-21 01:33:33', '2026-04-21 01:33:33', NULL),
	(6, 'Rosa', 'Vargas Huanca', 'rosa@email.com', '932109876', NULL, 'Jr. Tacna 789', NULL, 'Lima', 'Breña', NULL, NULL, 'regular', NULL, 1, '2026-04-13 02:33:33', '2026-04-13 02:33:33', NULL),
	(7, 'Pedro', 'Díaz Ccori', NULL, '921098765', NULL, 'Av. Universitaria 88', NULL, 'Lima', 'Los Olivos', NULL, NULL, 'vip', NULL, 1, '2026-04-09 15:33:33', '2026-04-09 15:33:33', NULL),
	(8, 'Elena', 'Chávez Ramos', 'elena@email.com', '910987654', NULL, 'Calle Real 234', NULL, 'Lima', 'San Borja', NULL, NULL, 'frecuente', NULL, 1, '2026-04-09 08:33:33', '2026-04-09 08:33:33', NULL),
	(9, 'Miguel', 'Salazar Rojas', 'miguel@email.com', '909876543', NULL, 'Av. La Marina 1500', NULL, 'Lima', 'San Miguel', NULL, NULL, 'regular', NULL, 1, '2026-05-01 20:33:33', '2026-05-01 20:33:33', NULL),
	(10, 'Patricia', 'Morales Aguilar', 'patricia@email.com', '898765432', NULL, 'Calle Schell 250', NULL, 'Lima', 'Miraflores', NULL, NULL, 'vip', NULL, 1, '2026-03-12 10:33:33', '2026-03-12 10:33:33', NULL),
	(11, 'Roberto', 'Quispe Yauri', 'roberto@email.com', '887654321', NULL, 'Jr. Cusco 410', NULL, 'Lima', 'La Victoria', NULL, NULL, 'regular', NULL, 1, '2026-04-11 22:33:33', '2026-04-11 22:33:33', NULL),
	(12, 'Sofía', 'Castillo Bravo', 'sofia@email.com', '876543210', NULL, 'Av. Aviación 3300', NULL, 'Lima', 'San Borja', NULL, NULL, 'frecuente', NULL, 1, '2026-04-22 02:33:33', '2026-04-22 02:33:33', NULL);

-- Volcando estructura para tabla delivery_crm.configuraciones
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `grupo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuraciones_clave_unique` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.configuraciones: ~15 rows (aproximadamente)
DELETE FROM `configuraciones`;
INSERT INTO `configuraciones` (`id`, `clave`, `valor`, `tipo`, `grupo`, `descripcion`, `created_at`, `updated_at`) VALUES
	(1, 'empresa_nombre', 'CRM Delivery', 'text', 'empresa', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(2, 'empresa_ruc', '20123456789', 'text', 'empresa', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(3, 'empresa_telefono', '01-234-5678', 'text', 'empresa', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(4, 'empresa_email', 'info@crmdelivery.com', 'text', 'empresa', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(5, 'empresa_direccion', 'Av. Principal 123, Lima', 'text', 'empresa', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(6, 'empresa_web', 'www.crmdelivery.com', 'text', 'empresa', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(7, 'delivery_costo_base', '5.00', 'number', 'delivery', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(8, 'delivery_tiempo_est', '45', 'number', 'delivery', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(9, 'delivery_radio_km', '10', 'number', 'delivery', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(10, 'moneda_simbolo', 'S/', 'text', 'sistema', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(11, 'moneda_codigo', 'PEN', 'text', 'sistema', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(12, 'timezone', 'America/Lima', 'text', 'sistema', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(13, 'pedidos_por_pagina', '20', 'number', 'sistema', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(14, 'notif_email', '1', 'number', 'notificaciones', 'Enviar email al cliente en cambios de estado (1=sí, 0=no)', '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(15, 'notif_whatsapp', '1', 'number', 'notificaciones', 'Mostrar botón Notificar por WhatsApp en pedidos', '2026-05-02 09:33:32', '2026-05-02 09:33:32');

-- Volcando estructura para tabla delivery_crm.cupones
CREATE TABLE IF NOT EXISTS `cupones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('porcentaje','monto') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(8,2) NOT NULL,
  `monto_minimo` decimal(8,2) NOT NULL DEFAULT '0.00',
  `descuento_maximo` decimal(8,2) DEFAULT NULL,
  `usos_maximos` int unsigned DEFAULT NULL,
  `usos_actuales` int unsigned NOT NULL DEFAULT '0',
  `valido_desde` date DEFAULT NULL,
  `valido_hasta` date DEFAULT NULL,
  `solo_primer_pedido` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cupones_codigo_unique` (`codigo`),
  KEY `cupones_activo_codigo_index` (`activo`,`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.cupones: ~10 rows (aproximadamente)
DELETE FROM `cupones`;
INSERT INTO `cupones` (`id`, `codigo`, `descripcion`, `tipo`, `valor`, `monto_minimo`, `descuento_maximo`, `usos_maximos`, `usos_actuales`, `valido_desde`, `valido_hasta`, `solo_primer_pedido`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'BIENVENIDA10', '10% de descuento para tu primer pedido', 'porcentaje', 10.00, 20.00, 15.00, NULL, 2, '2026-04-27', '2026-11-02', 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(2, 'VERANO20', '20% verano - todos los pedidos', 'porcentaje', 20.00, 30.00, 20.00, 200, 6, '2026-04-27', '2026-07-02', 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(3, 'DELIVERYGRATIS', 'Envío gratis en pedidos grandes', 'monto', 10.00, 50.00, NULL, 100, 1, '2026-04-27', '2026-06-02', 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(4, 'CUMPLE15', 'Descuento de cumpleaños', 'porcentaje', 15.00, 25.00, 25.00, NULL, 7, '2026-04-27', '2027-05-02', 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(5, 'FIN5', 'S/5 off fin de semana', 'monto', 5.00, 30.00, NULL, 500, 5, '2026-04-27', '2026-08-02', 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(6, 'COMBO50', 'S/10 off combos sobre S/50', 'monto', 10.00, 50.00, NULL, 50, 0, '2026-04-27', '2026-06-02', 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(7, 'PRIMERA5', 'S/5 a tu primer pedido', 'monto', 5.00, 15.00, NULL, NULL, 4, '2026-04-27', '2026-11-02', 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(8, 'PROMO25', '25% off pedidos premium', 'porcentaje', 25.00, 80.00, 30.00, 30, 3, '2026-04-27', '2026-07-02', 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(9, 'NAVIDAD30', '30% campaña navideña', 'porcentaje', 30.00, 100.00, 50.00, 50, 2, '2026-04-27', '2027-04-02', 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(10, 'EXPIRADO', 'Cupón expirado (test)', 'porcentaje', 50.00, 0.00, NULL, NULL, 7, '2026-04-27', '2026-04-22', 0, 0, '2026-05-02 09:33:33', '2026-05-02 09:33:33');

-- Volcando estructura para tabla delivery_crm.entregas
CREATE TABLE IF NOT EXISTS `entregas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `repartidor_id` bigint unsigned NOT NULL,
  `asignado_por` bigint unsigned NOT NULL,
  `estado` enum('asignado','recogido','en_camino','entregado','fallido','devuelto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'asignado',
  `fecha_asignacion` datetime NOT NULL,
  `fecha_recogida` datetime DEFAULT NULL,
  `fecha_entrega_estimada` datetime DEFAULT NULL,
  `fecha_entrega_real` datetime DEFAULT NULL,
  `distancia_km` decimal(8,2) DEFAULT NULL,
  `tiempo_minutos` int DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `firma_cliente` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_evidencia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calificacion` decimal(3,2) DEFAULT NULL,
  `comentario_cliente` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `lat_entrega` decimal(10,7) DEFAULT NULL,
  `lng_entrega` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entregas_pedido_id_foreign` (`pedido_id`),
  KEY `entregas_repartidor_id_foreign` (`repartidor_id`),
  KEY `entregas_asignado_por_foreign` (`asignado_por`),
  CONSTRAINT `entregas_asignado_por_foreign` FOREIGN KEY (`asignado_por`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `entregas_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entregas_repartidor_id_foreign` FOREIGN KEY (`repartidor_id`) REFERENCES `repartidores` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.entregas: ~76 rows (aproximadamente)
DELETE FROM `entregas`;
INSERT INTO `entregas` (`id`, `pedido_id`, `repartidor_id`, `asignado_por`, `estado`, `fecha_asignacion`, `fecha_recogida`, `fecha_entrega_estimada`, `fecha_entrega_real`, `distancia_km`, `tiempo_minutos`, `observaciones`, `firma_cliente`, `foto_evidencia`, `calificacion`, `comentario_cliente`, `created_at`, `updated_at`, `lat_entrega`, `lng_entrega`) VALUES
	(1, 1, 2, 1, 'entregado', '2026-04-03 20:58:28', '2026-04-03 21:08:28', '2026-04-03 21:43:28', '2026-04-03 21:49:28', 6.10, 69, NULL, NULL, NULL, 3.90, NULL, '2026-04-04 01:58:28', '2026-04-04 01:58:28', NULL, NULL),
	(2, 2, 2, 1, 'entregado', '2026-04-03 19:18:03', '2026-04-03 19:28:03', '2026-04-03 20:03:03', '2026-04-03 20:29:03', 4.20, 42, NULL, NULL, NULL, 4.50, NULL, '2026-04-04 00:18:03', '2026-04-04 00:18:03', NULL, NULL),
	(3, 3, 8, 1, 'entregado', '2026-04-03 15:35:18', '2026-04-03 15:45:18', '2026-04-03 16:20:18', '2026-04-03 16:26:18', 6.60, 69, NULL, NULL, NULL, 4.10, NULL, '2026-04-03 20:35:18', '2026-04-03 20:35:18', NULL, NULL),
	(4, 4, 7, 1, 'entregado', '2026-04-04 13:11:46', '2026-04-04 13:21:46', '2026-04-04 13:56:46', '2026-04-04 14:01:46', 7.70, 21, NULL, NULL, NULL, 4.00, NULL, '2026-04-04 18:11:46', '2026-04-04 18:11:46', NULL, NULL),
	(5, 5, 10, 1, 'entregado', '2026-04-04 21:49:56', '2026-04-04 21:59:56', '2026-04-04 22:34:56', '2026-04-04 22:34:56', 6.10, 57, NULL, NULL, NULL, 4.90, NULL, '2026-04-05 02:49:56', '2026-04-05 02:49:56', NULL, NULL),
	(6, 6, 3, 1, 'entregado', '2026-04-04 19:06:07', '2026-04-04 19:16:07', '2026-04-04 19:51:07', '2026-04-04 19:45:07', 1.40, 52, NULL, NULL, NULL, 4.80, NULL, '2026-04-05 00:06:07', '2026-04-05 00:06:07', NULL, NULL),
	(7, 7, 3, 1, 'entregado', '2026-04-04 18:39:23', '2026-04-04 18:49:23', '2026-04-04 19:24:23', '2026-04-04 19:01:23', 6.90, 62, NULL, NULL, NULL, 3.80, NULL, '2026-04-04 23:39:23', '2026-04-04 23:39:23', NULL, NULL),
	(8, 10, 6, 1, 'entregado', '2026-04-05 16:00:10', '2026-04-05 16:10:10', '2026-04-05 16:45:10', '2026-04-05 17:13:10', 8.00, 39, NULL, NULL, NULL, 5.00, NULL, '2026-04-05 21:00:10', '2026-04-05 21:00:10', NULL, NULL),
	(9, 12, 4, 1, 'entregado', '2026-04-06 17:32:39', '2026-04-06 17:42:39', '2026-04-06 18:17:39', '2026-04-06 18:17:39', 7.70, 59, NULL, NULL, NULL, 5.00, NULL, '2026-04-06 22:32:39', '2026-04-06 22:32:39', NULL, NULL),
	(10, 13, 10, 1, 'entregado', '2026-04-06 14:02:14', '2026-04-06 14:12:14', '2026-04-06 14:47:14', '2026-04-06 15:09:14', 2.90, 54, NULL, NULL, NULL, 4.60, NULL, '2026-04-06 19:02:14', '2026-04-06 19:02:14', NULL, NULL),
	(11, 14, 3, 1, 'entregado', '2026-04-06 14:48:51', '2026-04-06 14:58:51', '2026-04-06 15:33:51', '2026-04-06 15:19:51', 7.70, 42, NULL, NULL, NULL, 4.70, NULL, '2026-04-06 19:48:51', '2026-04-06 19:48:51', NULL, NULL),
	(12, 15, 2, 1, 'entregado', '2026-04-06 21:55:04', '2026-04-06 22:05:04', '2026-04-06 22:40:04', '2026-04-06 22:49:04', 3.20, 31, NULL, NULL, NULL, 4.00, NULL, '2026-04-07 02:55:04', '2026-04-07 02:55:04', NULL, NULL),
	(13, 16, 2, 1, 'entregado', '2026-04-07 11:13:23', '2026-04-07 11:23:23', '2026-04-07 11:58:23', '2026-04-07 11:45:23', 1.90, 61, NULL, NULL, NULL, 4.70, NULL, '2026-04-07 16:13:23', '2026-04-07 16:13:23', NULL, NULL),
	(14, 17, 3, 1, 'entregado', '2026-04-07 11:57:03', '2026-04-07 12:07:03', '2026-04-07 12:42:03', '2026-04-07 13:03:03', 2.30, 29, NULL, NULL, NULL, 4.40, NULL, '2026-04-07 16:57:03', '2026-04-07 16:57:03', NULL, NULL),
	(15, 18, 2, 1, 'entregado', '2026-04-07 12:37:27', '2026-04-07 12:47:27', '2026-04-07 13:22:27', '2026-04-07 13:43:27', 1.90, 55, NULL, NULL, NULL, 4.00, NULL, '2026-04-07 17:37:27', '2026-04-07 17:37:27', NULL, NULL),
	(16, 19, 8, 1, 'entregado', '2026-04-07 18:06:49', '2026-04-07 18:16:49', '2026-04-07 18:51:49', '2026-04-07 19:05:49', 7.60, 47, NULL, NULL, NULL, 3.60, NULL, '2026-04-07 23:06:49', '2026-04-07 23:06:49', NULL, NULL),
	(17, 20, 3, 1, 'entregado', '2026-04-08 15:54:36', '2026-04-08 16:04:36', '2026-04-08 16:39:36', '2026-04-08 17:04:36', 4.10, 31, NULL, NULL, NULL, 4.60, NULL, '2026-04-08 20:54:36', '2026-04-08 20:54:36', NULL, NULL),
	(18, 22, 2, 1, 'entregado', '2026-04-09 18:46:52', '2026-04-09 18:56:52', '2026-04-09 19:31:52', '2026-04-09 19:15:52', 5.90, 25, NULL, NULL, NULL, 3.90, NULL, '2026-04-09 23:46:52', '2026-04-09 23:46:52', NULL, NULL),
	(19, 23, 7, 1, 'entregado', '2026-04-10 22:11:47', '2026-04-10 22:21:47', '2026-04-10 22:56:47', '2026-04-10 23:09:47', 6.70, 21, NULL, NULL, NULL, 4.60, NULL, '2026-04-11 03:11:47', '2026-04-11 03:11:47', NULL, NULL),
	(20, 24, 8, 1, 'entregado', '2026-04-10 13:10:51', '2026-04-10 13:20:51', '2026-04-10 13:55:51', '2026-04-10 14:07:51', 6.20, 21, NULL, NULL, NULL, 4.70, NULL, '2026-04-10 18:10:51', '2026-04-10 18:10:51', NULL, NULL),
	(21, 25, 6, 1, 'entregado', '2026-04-10 11:02:49', '2026-04-10 11:12:49', '2026-04-10 11:47:49', '2026-04-10 12:06:49', 4.10, 55, NULL, NULL, NULL, 4.20, NULL, '2026-04-10 16:02:49', '2026-04-10 16:02:49', NULL, NULL),
	(22, 26, 6, 1, 'entregado', '2026-04-10 11:56:13', '2026-04-10 12:06:13', '2026-04-10 12:41:13', '2026-04-10 12:25:13', 6.90, 46, NULL, NULL, NULL, 4.10, NULL, '2026-04-10 16:56:13', '2026-04-10 16:56:13', NULL, NULL),
	(23, 27, 5, 1, 'entregado', '2026-04-11 19:38:27', '2026-04-11 19:48:27', '2026-04-11 20:23:27', '2026-04-11 20:28:27', 4.70, 40, NULL, NULL, NULL, 4.90, NULL, '2026-04-12 00:38:27', '2026-04-12 00:38:27', NULL, NULL),
	(24, 28, 6, 1, 'entregado', '2026-04-11 13:04:04', '2026-04-11 13:14:04', '2026-04-11 13:49:04', '2026-04-11 13:32:04', 5.80, 28, NULL, NULL, NULL, 4.10, NULL, '2026-04-11 18:04:04', '2026-04-11 18:04:04', NULL, NULL),
	(25, 29, 8, 1, 'entregado', '2026-04-11 12:41:20', '2026-04-11 12:51:20', '2026-04-11 13:26:20', '2026-04-11 13:43:20', 2.70, 32, NULL, NULL, NULL, 4.70, NULL, '2026-04-11 17:41:20', '2026-04-11 17:41:20', NULL, NULL),
	(26, 30, 1, 1, 'entregado', '2026-04-11 14:11:56', '2026-04-11 14:21:56', '2026-04-11 14:56:56', '2026-04-11 14:53:56', 4.90, 48, NULL, NULL, NULL, 4.70, NULL, '2026-04-11 19:11:56', '2026-04-11 19:11:56', NULL, NULL),
	(27, 31, 9, 1, 'entregado', '2026-04-12 16:15:31', '2026-04-12 16:25:31', '2026-04-12 17:00:31', '2026-04-12 17:18:31', 6.30, 31, NULL, NULL, NULL, 4.90, NULL, '2026-04-12 21:15:31', '2026-04-12 21:15:31', NULL, NULL),
	(28, 32, 1, 1, 'entregado', '2026-04-12 20:11:56', '2026-04-12 20:21:56', '2026-04-12 20:56:56', '2026-04-12 20:38:56', 7.00, 30, NULL, NULL, NULL, 4.60, NULL, '2026-04-13 01:11:56', '2026-04-13 01:11:56', NULL, NULL),
	(29, 33, 8, 1, 'entregado', '2026-04-12 17:14:11', '2026-04-12 17:24:11', '2026-04-12 17:59:11', '2026-04-12 18:10:11', 5.40, 53, NULL, NULL, NULL, 4.70, NULL, '2026-04-12 22:14:11', '2026-04-12 22:14:11', NULL, NULL),
	(30, 35, 10, 1, 'entregado', '2026-04-13 22:28:29', '2026-04-13 22:38:29', '2026-04-13 23:13:29', '2026-04-13 22:56:29', 2.60, 29, NULL, NULL, NULL, 4.10, NULL, '2026-04-14 03:28:29', '2026-04-14 03:28:29', NULL, NULL),
	(31, 36, 7, 1, 'entregado', '2026-04-13 22:08:39', '2026-04-13 22:18:39', '2026-04-13 22:53:39', '2026-04-13 23:07:39', 2.20, 31, NULL, NULL, NULL, 4.20, NULL, '2026-04-14 03:08:39', '2026-04-14 03:08:39', NULL, NULL),
	(32, 37, 9, 1, 'entregado', '2026-04-14 11:19:55', '2026-04-14 11:29:55', '2026-04-14 12:04:55', '2026-04-14 11:58:55', 5.10, 59, NULL, NULL, NULL, 4.80, NULL, '2026-04-14 16:19:55', '2026-04-14 16:19:55', NULL, NULL),
	(33, 38, 7, 1, 'entregado', '2026-04-15 21:13:11', '2026-04-15 21:23:11', '2026-04-15 21:58:11', '2026-04-15 22:09:11', 6.10, 49, NULL, NULL, NULL, 5.00, NULL, '2026-04-16 02:13:11', '2026-04-16 02:13:11', NULL, NULL),
	(34, 39, 5, 1, 'entregado', '2026-04-15 17:18:45', '2026-04-15 17:28:45', '2026-04-15 18:03:45', '2026-04-15 18:05:45', 2.60, 23, NULL, NULL, NULL, 4.30, NULL, '2026-04-15 22:18:45', '2026-04-15 22:18:45', NULL, NULL),
	(35, 40, 7, 1, 'entregado', '2026-04-15 16:58:02', '2026-04-15 17:08:02', '2026-04-15 17:43:02', '2026-04-15 17:50:02', 7.90, 33, NULL, NULL, NULL, 4.30, NULL, '2026-04-15 21:58:02', '2026-04-15 21:58:02', NULL, NULL),
	(36, 41, 9, 1, 'entregado', '2026-04-16 13:23:14', '2026-04-16 13:33:14', '2026-04-16 14:08:14', '2026-04-16 14:04:14', 7.00, 68, NULL, NULL, NULL, 4.70, NULL, '2026-04-16 18:23:14', '2026-04-16 18:23:14', NULL, NULL),
	(37, 42, 6, 1, 'entregado', '2026-04-16 22:03:28', '2026-04-16 22:13:28', '2026-04-16 22:48:28', '2026-04-16 22:56:28', 7.30, 67, NULL, NULL, NULL, 4.40, NULL, '2026-04-17 03:03:28', '2026-04-17 03:03:28', NULL, NULL),
	(38, 43, 9, 1, 'entregado', '2026-04-16 11:36:35', '2026-04-16 11:46:35', '2026-04-16 12:21:35', '2026-04-16 12:51:35', 3.00, 30, NULL, NULL, NULL, 4.10, NULL, '2026-04-16 16:36:35', '2026-04-16 16:36:35', NULL, NULL),
	(39, 44, 9, 1, 'entregado', '2026-04-16 16:49:46', '2026-04-16 16:59:46', '2026-04-16 17:34:46', '2026-04-16 17:10:46', 5.50, 51, NULL, NULL, NULL, 4.20, NULL, '2026-04-16 21:49:46', '2026-04-16 21:49:46', NULL, NULL),
	(40, 45, 6, 1, 'entregado', '2026-04-17 18:04:59', '2026-04-17 18:14:59', '2026-04-17 18:49:59', '2026-04-17 18:59:59', 3.50, 22, NULL, NULL, NULL, 3.60, NULL, '2026-04-17 23:04:59', '2026-04-17 23:04:59', NULL, NULL),
	(41, 46, 7, 1, 'entregado', '2026-04-17 14:40:37', '2026-04-17 14:50:37', '2026-04-17 15:25:37', '2026-04-17 15:48:37', 6.30, 34, NULL, NULL, NULL, 3.80, NULL, '2026-04-17 19:40:37', '2026-04-17 19:40:37', NULL, NULL),
	(42, 47, 10, 1, 'entregado', '2026-04-17 14:50:19', '2026-04-17 15:00:19', '2026-04-17 15:35:19', '2026-04-17 15:50:19', 2.40, 44, NULL, NULL, NULL, 4.50, NULL, '2026-04-17 19:50:19', '2026-04-17 19:50:19', NULL, NULL),
	(43, 48, 8, 1, 'entregado', '2026-04-17 17:25:02', '2026-04-17 17:35:02', '2026-04-17 18:10:02', '2026-04-17 18:19:02', 6.90, 69, NULL, NULL, NULL, 4.70, NULL, '2026-04-17 22:25:02', '2026-04-17 22:25:02', NULL, NULL),
	(44, 50, 6, 1, 'entregado', '2026-04-18 11:10:40', '2026-04-18 11:20:40', '2026-04-18 11:55:40', '2026-04-18 12:16:40', 6.50, 41, NULL, NULL, NULL, 4.60, NULL, '2026-04-18 16:10:40', '2026-04-18 16:10:40', NULL, NULL),
	(45, 51, 8, 1, 'entregado', '2026-04-18 19:03:16', '2026-04-18 19:13:16', '2026-04-18 19:48:16', '2026-04-18 19:27:16', 6.00, 60, NULL, NULL, NULL, 4.70, NULL, '2026-04-19 00:03:16', '2026-04-19 00:03:16', NULL, NULL),
	(46, 52, 8, 1, 'entregado', '2026-04-18 20:52:56', '2026-04-18 21:02:56', '2026-04-18 21:37:56', '2026-04-18 21:29:56', 2.30, 62, NULL, NULL, NULL, 4.60, NULL, '2026-04-19 01:52:56', '2026-04-19 01:52:56', NULL, NULL),
	(47, 53, 1, 1, 'entregado', '2026-04-18 16:05:03', '2026-04-18 16:15:03', '2026-04-18 16:50:03', '2026-04-18 16:55:03', 7.20, 58, NULL, NULL, NULL, 4.10, NULL, '2026-04-18 21:05:03', '2026-04-18 21:05:03', NULL, NULL),
	(48, 54, 2, 1, 'entregado', '2026-04-19 21:53:26', '2026-04-19 22:03:26', '2026-04-19 22:38:26', '2026-04-19 23:02:26', 7.90, 68, NULL, NULL, NULL, 4.80, NULL, '2026-04-20 02:53:26', '2026-04-20 02:53:26', NULL, NULL),
	(49, 55, 4, 1, 'entregado', '2026-04-19 22:29:55', '2026-04-19 22:39:55', '2026-04-19 23:14:55', '2026-04-19 23:16:55', 5.90, 69, NULL, NULL, NULL, 4.30, NULL, '2026-04-20 03:29:55', '2026-04-20 03:29:55', NULL, NULL),
	(50, 56, 9, 1, 'entregado', '2026-04-19 15:02:32', '2026-04-19 15:12:32', '2026-04-19 15:47:32', '2026-04-19 15:40:32', 5.00, 56, NULL, NULL, NULL, 4.60, NULL, '2026-04-19 20:02:32', '2026-04-19 20:02:32', NULL, NULL),
	(51, 57, 2, 1, 'entregado', '2026-04-19 12:46:24', '2026-04-19 12:56:24', '2026-04-19 13:31:24', '2026-04-19 13:31:24', 1.80, 62, NULL, NULL, NULL, 4.90, NULL, '2026-04-19 17:46:24', '2026-04-19 17:46:24', NULL, NULL),
	(52, 59, 7, 1, 'entregado', '2026-04-20 16:44:57', '2026-04-20 16:54:57', '2026-04-20 17:29:57', '2026-04-20 17:42:57', 7.80, 35, NULL, NULL, NULL, 5.00, NULL, '2026-04-20 21:44:57', '2026-04-20 21:44:57', NULL, NULL),
	(53, 60, 5, 1, 'entregado', '2026-04-20 16:24:21', '2026-04-20 16:34:21', '2026-04-20 17:09:21', '2026-04-20 16:58:21', 6.40, 55, NULL, NULL, NULL, 3.50, NULL, '2026-04-20 21:24:21', '2026-04-20 21:24:21', NULL, NULL),
	(54, 61, 4, 1, 'entregado', '2026-04-20 16:37:04', '2026-04-20 16:47:04', '2026-04-20 17:22:04', '2026-04-20 17:52:04', 3.40, 33, NULL, NULL, NULL, 4.30, NULL, '2026-04-20 21:37:04', '2026-04-20 21:37:04', NULL, NULL),
	(55, 62, 6, 1, 'entregado', '2026-04-20 16:46:21', '2026-04-20 16:56:21', '2026-04-20 17:31:21', '2026-04-20 17:39:21', 2.10, 57, NULL, NULL, NULL, 4.20, NULL, '2026-04-20 21:46:21', '2026-04-20 21:46:21', NULL, NULL),
	(56, 63, 9, 1, 'entregado', '2026-04-21 11:47:40', '2026-04-21 11:57:40', '2026-04-21 12:32:40', '2026-04-21 12:45:40', 2.60, 47, NULL, NULL, NULL, 3.80, NULL, '2026-04-21 16:47:40', '2026-04-21 16:47:40', NULL, NULL),
	(57, 64, 8, 1, 'entregado', '2026-04-21 17:17:00', '2026-04-21 17:27:00', '2026-04-21 18:02:00', '2026-04-21 18:27:00', 2.20, 25, NULL, NULL, NULL, 4.80, NULL, '2026-04-21 22:17:00', '2026-04-21 22:17:00', NULL, NULL),
	(58, 65, 6, 1, 'entregado', '2026-04-21 16:39:57', '2026-04-21 16:49:57', '2026-04-21 17:24:57', '2026-04-21 17:45:57', 5.00, 62, NULL, NULL, NULL, 4.50, NULL, '2026-04-21 21:39:57', '2026-04-21 21:39:57', NULL, NULL),
	(59, 66, 2, 1, 'entregado', '2026-04-22 18:48:22', '2026-04-22 18:58:22', '2026-04-22 19:33:22', '2026-04-22 19:56:22', 1.60, 69, NULL, NULL, NULL, 4.50, NULL, '2026-04-22 23:48:22', '2026-04-22 23:48:22', NULL, NULL),
	(60, 67, 6, 1, 'entregado', '2026-04-22 17:23:04', '2026-04-22 17:33:04', '2026-04-22 18:08:04', '2026-04-22 17:46:04', 7.30, 56, NULL, NULL, NULL, 5.00, NULL, '2026-04-22 22:23:04', '2026-04-22 22:23:04', NULL, NULL),
	(61, 68, 1, 1, 'entregado', '2026-04-23 21:26:45', '2026-04-23 21:36:45', '2026-04-23 22:11:45', '2026-04-23 21:59:45', 2.90, 33, NULL, NULL, NULL, 4.30, NULL, '2026-04-24 02:26:45', '2026-04-24 02:26:45', NULL, NULL),
	(62, 69, 8, 1, 'entregado', '2026-04-23 17:36:32', '2026-04-23 17:46:32', '2026-04-23 18:21:32', '2026-04-23 18:25:32', 7.90, 70, NULL, NULL, NULL, 3.80, NULL, '2026-04-23 22:36:32', '2026-04-23 22:36:32', NULL, NULL),
	(63, 70, 1, 1, 'entregado', '2026-04-23 20:01:57', '2026-04-23 20:11:57', '2026-04-23 20:46:57', '2026-04-23 21:13:57', 1.50, 54, NULL, NULL, NULL, 4.60, NULL, '2026-04-24 01:01:57', '2026-04-24 01:01:57', NULL, NULL),
	(64, 71, 10, 1, 'entregado', '2026-04-23 21:10:24', '2026-04-23 21:20:24', '2026-04-23 21:55:24', '2026-04-23 21:46:24', 2.10, 57, NULL, NULL, NULL, 4.70, NULL, '2026-04-24 02:10:24', '2026-04-24 02:10:24', NULL, NULL),
	(65, 72, 7, 1, 'entregado', '2026-04-24 19:00:10', '2026-04-24 19:10:10', '2026-04-24 19:45:10', '2026-04-24 20:10:10', 1.10, 54, NULL, NULL, NULL, 4.60, NULL, '2026-04-25 00:00:10', '2026-04-25 00:00:10', NULL, NULL),
	(66, 73, 5, 1, 'entregado', '2026-04-24 12:19:12', '2026-04-24 12:29:12', '2026-04-24 13:04:12', '2026-04-24 12:55:12', 8.00, 55, NULL, NULL, NULL, 3.50, NULL, '2026-04-24 17:19:12', '2026-04-24 17:19:12', NULL, NULL),
	(67, 74, 6, 1, 'entregado', '2026-04-24 21:04:43', '2026-04-24 21:14:43', '2026-04-24 21:49:43', '2026-04-24 21:30:43', 5.90, 60, NULL, NULL, NULL, 4.50, NULL, '2026-04-25 02:04:43', '2026-04-25 02:04:43', NULL, NULL),
	(68, 75, 2, 1, 'en_camino', '2026-04-25 12:22:59', NULL, '2026-04-25 13:07:59', NULL, 4.70, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-25 17:22:59', '2026-04-25 17:22:59', NULL, NULL),
	(69, 76, 5, 1, 'entregado', '2026-04-25 12:28:22', '2026-04-25 12:38:22', '2026-04-25 13:13:22', '2026-04-25 13:10:22', 6.90, 56, NULL, NULL, NULL, 3.50, NULL, '2026-04-25 17:28:22', '2026-04-25 17:28:22', NULL, NULL),
	(70, 80, 5, 1, 'entregado', '2026-04-26 22:25:43', '2026-04-26 22:35:43', '2026-04-26 23:10:43', '2026-04-26 22:55:43', 3.50, 67, NULL, NULL, NULL, 4.80, NULL, '2026-04-27 03:25:43', '2026-04-27 03:25:43', NULL, NULL),
	(71, 82, 6, 1, 'entregado', '2026-04-27 17:03:46', '2026-04-27 17:13:46', '2026-04-27 17:48:46', '2026-04-27 17:26:46', 5.40, 34, NULL, NULL, NULL, 3.50, NULL, '2026-04-27 22:03:46', '2026-04-27 22:03:46', NULL, NULL),
	(72, 84, 8, 1, 'entregado', '2026-04-28 13:51:02', '2026-04-28 14:01:02', '2026-04-28 14:36:02', '2026-04-28 14:35:02', 1.80, 52, NULL, NULL, NULL, 3.50, NULL, '2026-04-28 18:51:02', '2026-04-28 18:51:02', NULL, NULL),
	(73, 85, 6, 1, 'en_camino', '2026-04-28 19:54:16', NULL, '2026-04-28 20:39:16', NULL, 5.80, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-29 00:54:16', '2026-04-29 00:54:16', NULL, NULL),
	(74, 89, 5, 1, 'entregado', '2026-05-01 13:52:09', '2026-05-01 14:02:09', '2026-05-01 14:37:09', '2026-05-01 14:51:09', 2.10, 43, NULL, NULL, NULL, 4.10, NULL, '2026-05-01 18:52:09', '2026-05-01 18:52:09', NULL, NULL),
	(75, 90, 3, 1, 'en_camino', '2026-05-02 21:41:51', NULL, '2026-05-02 22:26:51', NULL, 6.10, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 02:41:51', '2026-05-03 02:41:51', NULL, NULL),
	(76, 92, 5, 1, 'entregado', '2026-05-02 13:24:49', '2026-05-02 13:34:49', '2026-05-02 14:09:49', '2026-05-02 13:58:49', 3.50, 50, NULL, NULL, NULL, 4.10, NULL, '2026-05-02 18:24:49', '2026-05-02 18:24:49', NULL, NULL);

-- Volcando estructura para tabla delivery_crm.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Volcando estructura para tabla delivery_crm.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.jobs: ~0 rows (aproximadamente)
DELETE FROM `jobs`;

-- Volcando estructura para tabla delivery_crm.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Volcando estructura para tabla delivery_crm.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.migrations: ~0 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2024_01_01_000000_create_permission_tables', 1),
	(5, '2024_01_01_000001_add_fields_to_users_table', 1),
	(6, '2024_01_01_000002_create_clientes_table', 1),
	(7, '2024_01_01_000003_create_categorias_table', 1),
	(8, '2024_01_01_000004_create_productos_table', 1),
	(9, '2024_01_01_000005_create_repartidores_table', 1),
	(10, '2024_01_01_000006_create_pedidos_table', 1),
	(11, '2024_01_01_000007_create_pedido_items_table', 1),
	(12, '2024_01_01_000008_create_entregas_table', 1),
	(13, '2024_01_01_000009_create_pagos_table', 1),
	(14, '2024_01_01_000010_create_configuraciones_table', 1),
	(15, '2024_01_01_000011_create_zonas_table', 1),
	(16, '2024_01_01_000012_create_movimientos_stock_table', 1),
	(17, '2024_01_01_000013_create_cupones_table', 1),
	(18, '2024_01_01_000014_add_gps_to_repartidores_entregas', 1),
	(19, '2024_01_01_000015_create_personal_access_tokens_table', 1);

-- Volcando estructura para tabla delivery_crm.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.model_has_permissions: ~0 rows (aproximadamente)
DELETE FROM `model_has_permissions`;

-- Volcando estructura para tabla delivery_crm.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.model_has_roles: ~4 rows (aproximadamente)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(2, 'App\\Models\\User', 2),
	(3, 'App\\Models\\User', 3),
	(4, 'App\\Models\\User', 4);

-- Volcando estructura para tabla delivery_crm.movimientos_stock
CREATE TABLE IF NOT EXISTS `movimientos_stock` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `pedido_id` bigint unsigned DEFAULT NULL,
  `tipo` enum('entrada','salida','ajuste','merma') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'entrada',
  `cantidad` int NOT NULL,
  `stock_anterior` int NOT NULL,
  `stock_nuevo` int NOT NULL,
  `costo_unitario` decimal(10,2) DEFAULT NULL,
  `motivo` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_stock_user_id_foreign` (`user_id`),
  KEY `movimientos_stock_pedido_id_foreign` (`pedido_id`),
  KEY `movimientos_stock_producto_id_created_at_index` (`producto_id`,`created_at`),
  KEY `movimientos_stock_tipo_index` (`tipo`),
  CONSTRAINT `movimientos_stock_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `movimientos_stock_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `movimientos_stock_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.movimientos_stock: ~0 rows (aproximadamente)
DELETE FROM `movimientos_stock`;

-- Volcando estructura para tabla delivery_crm.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `registrado_por` bigint unsigned NOT NULL,
  `referencia` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metodo` enum('efectivo','tarjeta','transferencia','yape','plin','otro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `monto` decimal(10,2) NOT NULL,
  `vuelto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('completado','pendiente','rechazado','reembolsado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completado',
  `comprobante_tipo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprobante_numero` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `fecha_pago` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagos_pedido_id_foreign` (`pedido_id`),
  KEY `pagos_registrado_por_foreign` (`registrado_por`),
  CONSTRAINT `pagos_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pagos_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.pagos: ~73 rows (aproximadamente)
DELETE FROM `pagos`;
INSERT INTO `pagos` (`id`, `pedido_id`, `registrado_por`, `referencia`, `metodo`, `monto`, `vuelto`, `estado`, `comprobante_tipo`, `comprobante_numero`, `notas`, `fecha_pago`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'TXN549371', 'tarjeta', 31.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-04 02:49:28', '2026-04-04 02:49:28', '2026-04-04 02:49:28'),
	(2, 2, 1, NULL, 'transferencia', 46.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-04 01:29:03', '2026-04-04 01:29:03', '2026-04-04 01:29:03'),
	(3, 3, 1, NULL, 'transferencia', 79.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-03 21:26:18', '2026-04-03 21:26:18', '2026-04-03 21:26:18'),
	(4, 4, 1, NULL, 'yape', 112.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-04 19:01:46', '2026-04-04 19:01:46', '2026-04-04 19:01:46'),
	(5, 5, 1, NULL, 'transferencia', 220.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-05 03:34:56', '2026-04-05 03:34:56', '2026-04-05 03:34:56'),
	(6, 6, 1, NULL, 'plin', 45.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-05 00:45:07', '2026-04-05 00:45:07', '2026-04-05 00:45:07'),
	(7, 7, 1, 'TXN844511', 'tarjeta', 17.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-05 00:01:23', '2026-04-05 00:01:23', '2026-04-05 00:01:23'),
	(8, 10, 1, NULL, 'plin', 249.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-05 22:13:10', '2026-04-05 22:13:10', '2026-04-05 22:13:10'),
	(9, 12, 1, NULL, 'efectivo', 129.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-06 23:17:39', '2026-04-06 23:17:39', '2026-04-06 23:17:39'),
	(10, 13, 1, NULL, 'transferencia', 39.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-06 20:09:14', '2026-04-06 20:09:14', '2026-04-06 20:09:14'),
	(11, 14, 1, NULL, 'yape', 34.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-06 20:19:51', '2026-04-06 20:19:51', '2026-04-06 20:19:51'),
	(12, 15, 1, NULL, 'transferencia', 126.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-07 03:49:04', '2026-04-07 03:49:04', '2026-04-07 03:49:04'),
	(13, 16, 1, NULL, 'efectivo', 135.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-07 16:45:23', '2026-04-07 16:45:23', '2026-04-07 16:45:23'),
	(14, 17, 1, NULL, 'efectivo', 111.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-07 18:03:03', '2026-04-07 18:03:03', '2026-04-07 18:03:03'),
	(15, 18, 1, NULL, 'efectivo', 136.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-07 18:43:27', '2026-04-07 18:43:27', '2026-04-07 18:43:27'),
	(16, 19, 1, NULL, 'efectivo', 47.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-08 00:05:49', '2026-04-08 00:05:49', '2026-04-08 00:05:49'),
	(17, 20, 1, NULL, 'plin', 63.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-08 22:04:36', '2026-04-08 22:04:36', '2026-04-08 22:04:36'),
	(18, 22, 1, NULL, 'plin', 89.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-10 00:15:52', '2026-04-10 00:15:52', '2026-04-10 00:15:52'),
	(19, 23, 1, 'TXN691176', 'tarjeta', 182.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-11 04:09:47', '2026-04-11 04:09:47', '2026-04-11 04:09:47'),
	(20, 24, 1, NULL, 'transferencia', 66.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-10 19:07:51', '2026-04-10 19:07:51', '2026-04-10 19:07:51'),
	(21, 25, 1, NULL, 'transferencia', 129.40, 0.00, 'completado', NULL, NULL, NULL, '2026-04-10 17:06:49', '2026-04-10 17:06:49', '2026-04-10 17:06:49'),
	(22, 26, 1, NULL, 'transferencia', 17.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-10 17:25:13', '2026-04-10 17:25:13', '2026-04-10 17:25:13'),
	(23, 27, 1, NULL, 'efectivo', 46.50, 0.00, 'completado', NULL, NULL, NULL, '2026-04-12 01:28:27', '2026-04-12 01:28:27', '2026-04-12 01:28:27'),
	(24, 28, 1, NULL, 'transferencia', 99.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-11 18:32:04', '2026-04-11 18:32:04', '2026-04-11 18:32:04'),
	(25, 29, 1, NULL, 'efectivo', 46.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-11 18:43:20', '2026-04-11 18:43:20', '2026-04-11 18:43:20'),
	(26, 30, 1, NULL, 'plin', 76.50, 0.00, 'completado', NULL, NULL, NULL, '2026-04-11 19:53:56', '2026-04-11 19:53:56', '2026-04-11 19:53:56'),
	(27, 31, 1, NULL, 'efectivo', 90.30, 0.00, 'completado', NULL, NULL, NULL, '2026-04-12 22:18:31', '2026-04-12 22:18:31', '2026-04-12 22:18:31'),
	(28, 32, 1, NULL, 'yape', 16.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-13 01:38:56', '2026-04-13 01:38:56', '2026-04-13 01:38:56'),
	(29, 33, 1, NULL, 'yape', 135.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-12 23:10:11', '2026-04-12 23:10:11', '2026-04-12 23:10:11'),
	(30, 35, 1, 'TXN222129', 'tarjeta', 210.75, 0.00, 'completado', NULL, NULL, NULL, '2026-04-14 03:56:29', '2026-04-14 03:56:29', '2026-04-14 03:56:29'),
	(31, 36, 1, NULL, 'efectivo', 266.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-14 04:07:39', '2026-04-14 04:07:39', '2026-04-14 04:07:39'),
	(32, 37, 1, 'TXN804654', 'tarjeta', 50.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-14 16:58:55', '2026-04-14 16:58:55', '2026-04-14 16:58:55'),
	(33, 38, 1, NULL, 'yape', 29.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-16 03:09:11', '2026-04-16 03:09:11', '2026-04-16 03:09:11'),
	(34, 39, 1, NULL, 'yape', 73.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-15 23:05:45', '2026-04-15 23:05:45', '2026-04-15 23:05:45'),
	(35, 40, 1, NULL, 'transferencia', 106.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-15 22:50:02', '2026-04-15 22:50:02', '2026-04-15 22:50:02'),
	(36, 41, 1, 'TXN593625', 'tarjeta', 273.70, 0.00, 'completado', NULL, NULL, NULL, '2026-04-16 19:04:14', '2026-04-16 19:04:14', '2026-04-16 19:04:14'),
	(37, 42, 1, NULL, 'transferencia', 80.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-17 03:56:28', '2026-04-17 03:56:28', '2026-04-17 03:56:28'),
	(38, 43, 1, NULL, 'yape', 169.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-16 17:51:35', '2026-04-16 17:51:35', '2026-04-16 17:51:35'),
	(39, 44, 1, NULL, 'yape', 93.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-16 22:10:46', '2026-04-16 22:10:46', '2026-04-16 22:10:46'),
	(40, 45, 1, 'TXN556159', 'tarjeta', 47.50, 0.00, 'completado', NULL, NULL, NULL, '2026-04-17 23:59:59', '2026-04-17 23:59:59', '2026-04-17 23:59:59'),
	(41, 46, 1, 'TXN203890', 'tarjeta', 82.75, 0.00, 'completado', NULL, NULL, NULL, '2026-04-17 20:48:37', '2026-04-17 20:48:37', '2026-04-17 20:48:37'),
	(42, 47, 1, NULL, 'yape', 82.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-17 20:50:19', '2026-04-17 20:50:19', '2026-04-17 20:50:19'),
	(43, 48, 1, NULL, 'yape', 60.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-17 23:19:02', '2026-04-17 23:19:02', '2026-04-17 23:19:02'),
	(44, 50, 1, NULL, 'transferencia', 53.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-18 17:16:40', '2026-04-18 17:16:40', '2026-04-18 17:16:40'),
	(45, 51, 1, NULL, 'yape', 74.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-19 00:27:16', '2026-04-19 00:27:16', '2026-04-19 00:27:16'),
	(46, 52, 1, NULL, 'yape', 67.50, 0.00, 'completado', NULL, NULL, NULL, '2026-04-19 02:29:56', '2026-04-19 02:29:56', '2026-04-19 02:29:56'),
	(47, 53, 1, NULL, 'efectivo', 37.70, 0.00, 'completado', NULL, NULL, NULL, '2026-04-18 21:55:03', '2026-04-18 21:55:03', '2026-04-18 21:55:03'),
	(48, 54, 1, NULL, 'plin', 34.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-20 04:02:26', '2026-04-20 04:02:26', '2026-04-20 04:02:26'),
	(49, 55, 1, NULL, 'efectivo', 89.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-20 04:16:55', '2026-04-20 04:16:55', '2026-04-20 04:16:55'),
	(50, 56, 1, NULL, 'plin', 192.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-19 20:40:32', '2026-04-19 20:40:32', '2026-04-19 20:40:32'),
	(51, 57, 1, NULL, 'transferencia', 92.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-19 18:31:24', '2026-04-19 18:31:24', '2026-04-19 18:31:24'),
	(52, 59, 1, NULL, 'efectivo', 143.50, 0.00, 'completado', NULL, NULL, NULL, '2026-04-20 22:42:57', '2026-04-20 22:42:57', '2026-04-20 22:42:57'),
	(53, 60, 1, 'TXN257901', 'tarjeta', 158.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-20 21:58:21', '2026-04-20 21:58:21', '2026-04-20 21:58:21'),
	(54, 61, 1, NULL, 'yape', 122.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-20 22:52:04', '2026-04-20 22:52:04', '2026-04-20 22:52:04'),
	(55, 62, 1, NULL, 'plin', 36.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-20 22:39:21', '2026-04-20 22:39:21', '2026-04-20 22:39:21'),
	(56, 63, 1, NULL, 'yape', 153.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-21 17:45:40', '2026-04-21 17:45:40', '2026-04-21 17:45:40'),
	(57, 64, 1, NULL, 'efectivo', 58.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-21 23:27:00', '2026-04-21 23:27:00', '2026-04-21 23:27:00'),
	(58, 65, 1, 'TXN507402', 'tarjeta', 18.90, 0.00, 'completado', NULL, NULL, NULL, '2026-04-21 22:45:57', '2026-04-21 22:45:57', '2026-04-21 22:45:57'),
	(59, 66, 1, 'TXN773823', 'tarjeta', 72.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-23 00:56:22', '2026-04-23 00:56:22', '2026-04-23 00:56:22'),
	(60, 67, 1, NULL, 'plin', 253.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-22 22:46:04', '2026-04-22 22:46:04', '2026-04-22 22:46:04'),
	(61, 68, 1, NULL, 'yape', 154.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-24 02:59:45', '2026-04-24 02:59:45', '2026-04-24 02:59:45'),
	(62, 69, 1, NULL, 'efectivo', 176.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-23 23:25:32', '2026-04-23 23:25:32', '2026-04-23 23:25:32'),
	(63, 70, 1, 'TXN413073', 'tarjeta', 43.10, 0.00, 'completado', NULL, NULL, NULL, '2026-04-24 02:13:57', '2026-04-24 02:13:57', '2026-04-24 02:13:57'),
	(64, 71, 1, NULL, 'transferencia', 403.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-24 02:46:24', '2026-04-24 02:46:24', '2026-04-24 02:46:24'),
	(65, 72, 1, NULL, 'efectivo', 82.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-25 01:10:10', '2026-04-25 01:10:10', '2026-04-25 01:10:10'),
	(66, 73, 1, NULL, 'plin', 82.20, 0.00, 'completado', NULL, NULL, NULL, '2026-04-24 17:55:12', '2026-04-24 17:55:12', '2026-04-24 17:55:12'),
	(67, 74, 1, NULL, 'transferencia', 36.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-25 02:30:43', '2026-04-25 02:30:43', '2026-04-25 02:30:43'),
	(68, 76, 1, NULL, 'transferencia', 24.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-25 18:10:22', '2026-04-25 18:10:22', '2026-04-25 18:10:22'),
	(69, 80, 1, NULL, 'efectivo', 137.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-27 03:55:43', '2026-04-27 03:55:43', '2026-04-27 03:55:43'),
	(70, 82, 1, NULL, 'efectivo', 30.30, 0.00, 'completado', NULL, NULL, NULL, '2026-04-27 22:26:46', '2026-04-27 22:26:46', '2026-04-27 22:26:46'),
	(71, 84, 1, NULL, 'efectivo', 108.00, 0.00, 'completado', NULL, NULL, NULL, '2026-04-28 19:35:02', '2026-04-28 19:35:02', '2026-04-28 19:35:02'),
	(72, 89, 1, NULL, 'plin', 49.00, 0.00, 'completado', NULL, NULL, NULL, '2026-05-01 19:51:09', '2026-05-01 19:51:09', '2026-05-01 19:51:09'),
	(73, 92, 1, NULL, 'yape', 228.00, 0.00, 'completado', NULL, NULL, NULL, '2026-05-02 18:58:49', '2026-05-02 18:58:49', '2026-05-02 18:58:49');

-- Volcando estructura para tabla delivery_crm.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Volcando estructura para tabla delivery_crm.pedidos
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `repartidor_id` bigint unsigned DEFAULT NULL,
  `direccion_entrega` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia_entrega` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distrito_entrega` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zona_id` bigint unsigned DEFAULT NULL,
  `estado` enum('pendiente','confirmado','preparando','listo','en_camino','entregado','cancelado','devuelto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `tipo_pago` enum('efectivo','tarjeta','transferencia','yape','plin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `estado_pago` enum('pendiente','pagado','parcial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `costo_delivery` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cupon_id` bigint unsigned DEFAULT NULL,
  `codigo_cupon` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `motivo_cancelacion` text COLLATE utf8mb4_unicode_ci,
  `fecha_programada` datetime DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pedidos_numero_unique` (`numero`),
  KEY `pedidos_cliente_id_foreign` (`cliente_id`),
  KEY `pedidos_user_id_foreign` (`user_id`),
  KEY `pedidos_repartidor_id_foreign` (`repartidor_id`),
  KEY `pedidos_zona_id_foreign` (`zona_id`),
  KEY `pedidos_cupon_id_foreign` (`cupon_id`),
  CONSTRAINT `pedidos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `pedidos_cupon_id_foreign` FOREIGN KEY (`cupon_id`) REFERENCES `cupones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pedidos_repartidor_id_foreign` FOREIGN KEY (`repartidor_id`) REFERENCES `repartidores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pedidos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `pedidos_zona_id_foreign` FOREIGN KEY (`zona_id`) REFERENCES `zonas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.pedidos: ~92 rows (aproximadamente)
DELETE FROM `pedidos`;
INSERT INTO `pedidos` (`id`, `numero`, `cliente_id`, `user_id`, `repartidor_id`, `direccion_entrega`, `referencia_entrega`, `distrito_entrega`, `zona_id`, `estado`, `tipo_pago`, `estado_pago`, `subtotal`, `costo_delivery`, `descuento`, `cupon_id`, `codigo_cupon`, `total`, `notas`, `motivo_cancelacion`, `fecha_programada`, `fecha_entrega`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'PED-000001', 5, 1, 2, 'Av. Colonial 456', NULL, 'Pueblo Libre', 7, 'entregado', 'tarjeta', 'pagado', 24.00, 7.00, 0.00, NULL, NULL, 31.00, NULL, NULL, NULL, '2026-04-03 21:49:28', '2026-04-04 01:58:28', '2026-04-04 01:58:28', NULL),
	(2, 'PED-000002', 6, 1, 2, 'Jr. Tacna 789', NULL, 'Cercado de Lima', 1, 'entregado', 'transferencia', 'pagado', 40.00, 6.00, 0.00, NULL, NULL, 46.00, NULL, NULL, NULL, '2026-04-03 20:29:03', '2026-04-04 00:18:03', '2026-04-04 00:18:03', NULL),
	(3, 'PED-000003', 12, 1, 8, 'Av. Aviación 3300', NULL, 'San Miguel', 8, 'entregado', 'transferencia', 'pagado', 72.00, 7.00, 0.00, NULL, NULL, 79.00, NULL, NULL, NULL, '2026-04-03 16:26:18', '2026-04-03 20:35:18', '2026-04-03 20:35:18', NULL),
	(4, 'PED-000004', 9, 1, 7, 'Av. La Marina 1500', NULL, 'San Miguel', 8, 'entregado', 'yape', 'pagado', 105.00, 7.00, 0.00, NULL, NULL, 112.00, NULL, NULL, NULL, '2026-04-04 14:01:46', '2026-04-04 18:11:46', '2026-04-04 18:11:46', NULL),
	(5, 'PED-000005', 1, 1, 10, 'Av. Arequipa 1234', NULL, 'Ate', 10, 'entregado', 'transferencia', 'pagado', 207.00, 13.00, 0.00, NULL, NULL, 220.00, NULL, NULL, NULL, '2026-04-04 22:34:56', '2026-04-05 02:49:56', '2026-04-05 02:49:56', NULL),
	(6, 'PED-000006', 11, 1, 3, 'Jr. Cusco 410', NULL, 'Pueblo Libre', 7, 'entregado', 'plin', 'pagado', 38.00, 7.00, 0.00, NULL, NULL, 45.00, NULL, NULL, NULL, '2026-04-04 19:45:07', '2026-04-05 00:06:07', '2026-04-05 00:06:07', NULL),
	(7, 'PED-000007', 10, 1, 3, 'Calle Schell 250', NULL, 'Surco', 4, 'entregado', 'tarjeta', 'pagado', 7.00, 10.00, 0.00, NULL, NULL, 17.00, NULL, NULL, NULL, '2026-04-04 19:01:23', '2026-04-04 23:39:23', '2026-04-04 23:39:23', NULL),
	(8, 'PED-000008', 6, 1, NULL, 'Jr. Tacna 789', NULL, 'Surco', 4, 'cancelado', 'tarjeta', 'pendiente', 66.00, 10.00, 6.60, NULL, NULL, 69.40, NULL, NULL, NULL, NULL, '2026-04-05 21:56:48', '2026-04-05 21:56:48', NULL),
	(9, 'PED-000009', 12, 1, NULL, 'Av. Aviación 3300', NULL, 'Surco', 4, 'cancelado', 'yape', 'pendiente', 40.00, 10.00, 0.00, NULL, NULL, 50.00, NULL, NULL, NULL, NULL, '2026-04-05 16:33:55', '2026-04-05 16:33:55', NULL),
	(10, 'PED-000010', 8, 1, 6, 'Calle Real 234', NULL, 'San Miguel', 8, 'entregado', 'plin', 'pagado', 242.00, 7.00, 0.00, NULL, NULL, 249.00, NULL, NULL, NULL, '2026-04-05 17:13:10', '2026-04-05 21:00:10', '2026-04-05 21:00:10', NULL),
	(11, 'PED-000011', 1, 1, NULL, 'Av. Arequipa 1234', NULL, 'Pueblo Libre', 7, 'cancelado', 'yape', 'pendiente', 208.00, 7.00, 0.00, NULL, NULL, 215.00, NULL, NULL, NULL, NULL, '2026-04-06 01:17:04', '2026-04-06 01:17:04', NULL),
	(12, 'PED-000012', 7, 1, 4, 'Av. Universitaria 88', NULL, 'San Borja', 5, 'entregado', 'efectivo', 'pagado', 120.00, 9.00, 0.00, NULL, NULL, 129.00, NULL, NULL, NULL, '2026-04-06 18:17:39', '2026-04-06 22:32:39', '2026-04-06 22:32:39', NULL),
	(13, 'PED-000013', 9, 1, 10, 'Av. La Marina 1500', NULL, 'Ate', 10, 'entregado', 'transferencia', 'pagado', 26.00, 13.00, 0.00, NULL, NULL, 39.00, NULL, NULL, NULL, '2026-04-06 15:09:14', '2026-04-06 19:02:14', '2026-04-06 19:02:14', NULL),
	(14, 'PED-000014', 2, 1, 3, 'Jr. Lima 567', NULL, 'Ate', 10, 'entregado', 'yape', 'pagado', 21.00, 13.00, 0.00, NULL, NULL, 34.00, NULL, NULL, NULL, '2026-04-06 15:19:51', '2026-04-06 19:48:51', '2026-04-06 19:48:51', NULL),
	(15, 'PED-000015', 5, 1, 2, 'Av. Colonial 456', NULL, 'Barranco', 6, 'entregado', 'transferencia', 'pagado', 130.00, 9.00, 13.00, NULL, NULL, 126.00, NULL, NULL, NULL, '2026-04-06 22:49:04', '2026-04-07 02:55:04', '2026-04-07 02:55:04', NULL),
	(16, 'PED-000016', 10, 1, 2, 'Calle Schell 250', NULL, 'Barranco', 6, 'entregado', 'efectivo', 'pagado', 126.00, 9.00, 0.00, NULL, NULL, 135.00, NULL, NULL, NULL, '2026-04-07 11:45:23', '2026-04-07 16:13:23', '2026-04-07 16:13:23', NULL),
	(17, 'PED-000017', 6, 1, 3, 'Jr. Tacna 789', NULL, 'Cercado de Lima', 1, 'entregado', 'efectivo', 'pagado', 105.00, 6.00, 0.00, NULL, NULL, 111.00, NULL, NULL, NULL, '2026-04-07 13:03:03', '2026-04-07 16:57:03', '2026-04-07 16:57:03', NULL),
	(18, 'PED-000018', 12, 1, 2, 'Av. Aviación 3300', NULL, 'Los Olivos', 9, 'entregado', 'efectivo', 'pagado', 124.00, 12.00, 0.00, NULL, NULL, 136.00, NULL, NULL, NULL, '2026-04-07 13:43:27', '2026-04-07 17:37:27', '2026-04-07 17:37:27', NULL),
	(19, 'PED-000019', 6, 1, 8, 'Jr. Tacna 789', NULL, 'Ate', 10, 'entregado', 'efectivo', 'pagado', 34.00, 13.00, 0.00, NULL, NULL, 47.00, NULL, NULL, NULL, '2026-04-07 19:05:49', '2026-04-07 23:06:49', '2026-04-07 23:06:49', NULL),
	(20, 'PED-000020', 6, 1, 3, 'Jr. Tacna 789', NULL, 'Miraflores', 2, 'entregado', 'plin', 'pagado', 55.00, 8.00, 0.00, NULL, NULL, 63.00, NULL, NULL, NULL, '2026-04-08 17:04:36', '2026-04-08 20:54:36', '2026-04-08 20:54:36', NULL),
	(21, 'PED-000021', 11, 1, NULL, 'Jr. Cusco 410', NULL, 'Barranco', 6, 'cancelado', 'plin', 'pendiente', 7.00, 9.00, 0.00, NULL, NULL, 16.00, NULL, NULL, NULL, NULL, '2026-04-10 00:43:15', '2026-04-10 00:43:15', NULL),
	(22, 'PED-000022', 3, 1, 2, 'Av. Brasil 890', NULL, 'San Isidro', 3, 'entregado', 'plin', 'pagado', 81.00, 8.00, 0.00, NULL, NULL, 89.00, NULL, NULL, NULL, '2026-04-09 19:15:52', '2026-04-09 23:46:52', '2026-04-09 23:46:52', NULL),
	(23, 'PED-000023', 6, 1, 7, 'Jr. Tacna 789', NULL, 'San Borja', 5, 'entregado', 'tarjeta', 'pagado', 173.00, 9.00, 0.00, NULL, NULL, 182.00, NULL, NULL, NULL, '2026-04-10 23:09:47', '2026-04-11 03:11:47', '2026-04-11 03:11:47', NULL),
	(24, 'PED-000024', 1, 1, 8, 'Av. Arequipa 1234', NULL, 'Cercado de Lima', 1, 'entregado', 'transferencia', 'pagado', 60.00, 6.00, 0.00, NULL, NULL, 66.00, NULL, NULL, NULL, '2026-04-10 14:07:51', '2026-04-10 18:10:51', '2026-04-10 18:10:51', NULL),
	(25, 'PED-000025', 7, 1, 6, 'Av. Universitaria 88', NULL, 'Pueblo Libre', 7, 'entregado', 'transferencia', 'pagado', 136.00, 7.00, 13.60, NULL, NULL, 129.40, NULL, NULL, NULL, '2026-04-10 12:06:49', '2026-04-10 16:02:49', '2026-04-10 16:02:49', NULL),
	(26, 'PED-000026', 2, 1, 6, 'Jr. Lima 567', NULL, 'Surco', 4, 'entregado', 'transferencia', 'pagado', 7.00, 10.00, 0.00, NULL, NULL, 17.00, NULL, NULL, NULL, '2026-04-10 12:25:13', '2026-04-10 16:56:13', '2026-04-10 16:56:13', NULL),
	(27, 'PED-000027', 11, 1, 5, 'Jr. Cusco 410', NULL, 'Surco', 4, 'entregado', 'efectivo', 'pagado', 36.50, 10.00, 0.00, NULL, NULL, 46.50, NULL, NULL, NULL, '2026-04-11 20:28:27', '2026-04-12 00:38:27', '2026-04-12 00:38:27', NULL),
	(28, 'PED-000028', 5, 1, 6, 'Av. Colonial 456', NULL, 'San Borja', 5, 'entregado', 'transferencia', 'pagado', 90.00, 9.00, 0.00, NULL, NULL, 99.00, NULL, NULL, NULL, '2026-04-11 13:32:04', '2026-04-11 18:04:04', '2026-04-11 18:04:04', NULL),
	(29, 'PED-000029', 10, 1, 8, 'Calle Schell 250', NULL, 'Cercado de Lima', 1, 'entregado', 'efectivo', 'pagado', 40.00, 6.00, 0.00, NULL, NULL, 46.00, NULL, NULL, NULL, '2026-04-11 13:43:20', '2026-04-11 17:41:20', '2026-04-11 17:41:20', NULL),
	(30, 'PED-000030', 10, 1, 1, 'Calle Schell 250', NULL, 'San Borja', 5, 'entregado', 'plin', 'pagado', 75.00, 9.00, 7.50, NULL, NULL, 76.50, NULL, NULL, NULL, '2026-04-11 14:53:56', '2026-04-11 19:11:56', '2026-04-11 19:11:56', NULL),
	(31, 'PED-000031', 12, 1, 9, 'Av. Aviación 3300', NULL, 'Los Olivos', 9, 'entregado', 'efectivo', 'pagado', 87.00, 12.00, 8.70, NULL, NULL, 90.30, NULL, NULL, NULL, '2026-04-12 17:18:31', '2026-04-12 21:15:31', '2026-04-12 21:15:31', NULL),
	(32, 'PED-000032', 7, 1, 1, 'Av. Universitaria 88', NULL, 'San Borja', 5, 'entregado', 'yape', 'pagado', 7.00, 9.00, 0.00, NULL, NULL, 16.00, NULL, NULL, NULL, '2026-04-12 20:38:56', '2026-04-13 01:11:56', '2026-04-13 01:11:56', NULL),
	(33, 'PED-000033', 9, 1, 8, 'Av. La Marina 1500', NULL, 'San Isidro', 3, 'entregado', 'yape', 'pagado', 127.00, 8.00, 0.00, NULL, NULL, 135.00, NULL, NULL, NULL, '2026-04-12 18:10:11', '2026-04-12 22:14:11', '2026-04-12 22:14:11', NULL),
	(34, 'PED-000034', 9, 1, NULL, 'Av. La Marina 1500', NULL, 'Ate', 10, 'cancelado', 'transferencia', 'pendiente', 82.00, 13.00, 0.00, NULL, NULL, 95.00, NULL, NULL, NULL, NULL, '2026-04-14 00:36:12', '2026-04-14 00:36:12', NULL),
	(35, 'PED-000035', 5, 1, 10, 'Av. Colonial 456', NULL, 'Cercado de Lima', 1, 'entregado', 'tarjeta', 'pagado', 227.50, 6.00, 22.75, NULL, NULL, 210.75, NULL, NULL, NULL, '2026-04-13 22:56:29', '2026-04-14 03:28:29', '2026-04-14 03:28:29', NULL),
	(36, 'PED-000036', 3, 1, 7, 'Av. Brasil 890', NULL, 'Pueblo Libre', 7, 'entregado', 'efectivo', 'pagado', 259.00, 7.00, 0.00, NULL, NULL, 266.00, NULL, NULL, NULL, '2026-04-13 23:07:39', '2026-04-14 03:08:39', '2026-04-14 03:08:39', NULL),
	(37, 'PED-000037', 10, 1, 9, 'Calle Schell 250', NULL, 'Cercado de Lima', 1, 'entregado', 'tarjeta', 'pagado', 44.00, 6.00, 0.00, NULL, NULL, 50.00, NULL, NULL, NULL, '2026-04-14 11:58:55', '2026-04-14 16:19:55', '2026-04-14 16:19:55', NULL),
	(38, 'PED-000038', 7, 1, 7, 'Av. Universitaria 88', NULL, 'Los Olivos', 9, 'entregado', 'yape', 'pagado', 17.00, 12.00, 0.00, NULL, NULL, 29.00, NULL, NULL, NULL, '2026-04-15 22:09:11', '2026-04-16 02:13:11', '2026-04-16 02:13:11', NULL),
	(39, 'PED-000039', 3, 1, 5, 'Av. Brasil 890', NULL, 'Ate', 10, 'entregado', 'yape', 'pagado', 60.00, 13.00, 0.00, NULL, NULL, 73.00, NULL, NULL, NULL, '2026-04-15 18:05:45', '2026-04-15 22:18:45', '2026-04-15 22:18:45', NULL),
	(40, 'PED-000040', 6, 1, 7, 'Jr. Tacna 789', NULL, 'Surco', 4, 'entregado', 'transferencia', 'pagado', 96.00, 10.00, 0.00, NULL, NULL, 106.00, NULL, NULL, NULL, '2026-04-15 17:50:02', '2026-04-15 21:58:02', '2026-04-15 21:58:02', NULL),
	(41, 'PED-000041', 8, 1, 9, 'Calle Real 234', NULL, 'Surco', 4, 'entregado', 'tarjeta', 'pagado', 293.00, 10.00, 29.30, NULL, NULL, 273.70, NULL, NULL, NULL, '2026-04-16 14:04:14', '2026-04-16 18:23:14', '2026-04-16 18:23:14', NULL),
	(42, 'PED-000042', 2, 1, 6, 'Jr. Lima 567', NULL, 'Surco', 4, 'entregado', 'transferencia', 'pagado', 70.00, 10.00, 0.00, NULL, NULL, 80.00, NULL, NULL, NULL, '2026-04-16 22:56:28', '2026-04-17 03:03:28', '2026-04-17 03:03:28', NULL),
	(43, 'PED-000043', 2, 1, 9, 'Jr. Lima 567', NULL, 'Barranco', 6, 'entregado', 'yape', 'pagado', 160.00, 9.00, 0.00, NULL, NULL, 169.00, NULL, NULL, NULL, '2026-04-16 12:51:35', '2026-04-16 16:36:35', '2026-04-16 16:36:35', NULL),
	(44, 'PED-000044', 8, 1, 9, 'Calle Real 234', NULL, 'Ate', 10, 'entregado', 'yape', 'pagado', 80.00, 13.00, 0.00, NULL, NULL, 93.00, NULL, NULL, NULL, '2026-04-16 17:10:46', '2026-04-16 21:49:46', '2026-04-16 21:49:46', NULL),
	(45, 'PED-000045', 2, 1, 6, 'Jr. Lima 567', NULL, 'San Miguel', 8, 'entregado', 'tarjeta', 'pagado', 45.00, 7.00, 4.50, NULL, NULL, 47.50, NULL, NULL, NULL, '2026-04-17 18:59:59', '2026-04-17 23:04:59', '2026-04-17 23:04:59', NULL),
	(46, 'PED-000046', 6, 1, 7, 'Jr. Tacna 789', NULL, 'Ate', 10, 'entregado', 'tarjeta', 'pagado', 77.50, 13.00, 7.75, NULL, NULL, 82.75, NULL, NULL, NULL, '2026-04-17 15:48:37', '2026-04-17 19:40:37', '2026-04-17 19:40:37', NULL),
	(47, 'PED-000047', 11, 1, 10, 'Jr. Cusco 410', NULL, 'San Borja', 5, 'entregado', 'yape', 'pagado', 73.00, 9.00, 0.00, NULL, NULL, 82.00, NULL, NULL, NULL, '2026-04-17 15:50:19', '2026-04-17 19:50:19', '2026-04-17 19:50:19', NULL),
	(48, 'PED-000048', 10, 1, 8, 'Calle Schell 250', NULL, 'Los Olivos', 9, 'entregado', 'yape', 'pagado', 48.00, 12.00, 0.00, NULL, NULL, 60.00, NULL, NULL, NULL, '2026-04-17 18:19:02', '2026-04-17 22:25:02', '2026-04-17 22:25:02', NULL),
	(49, 'PED-000049', 9, 1, NULL, 'Av. La Marina 1500', NULL, 'Pueblo Libre', 7, 'cancelado', 'yape', 'pendiente', 71.00, 7.00, 0.00, NULL, NULL, 78.00, NULL, NULL, NULL, NULL, '2026-04-19 01:05:10', '2026-04-19 01:05:10', NULL),
	(50, 'PED-000050', 5, 1, 6, 'Av. Colonial 456', NULL, 'Surco', 4, 'entregado', 'transferencia', 'pagado', 43.00, 10.00, 0.00, NULL, NULL, 53.00, NULL, NULL, NULL, '2026-04-18 12:16:40', '2026-04-18 16:10:40', '2026-04-18 16:10:40', NULL),
	(51, 'PED-000051', 10, 1, 8, 'Calle Schell 250', NULL, 'Pueblo Libre', 7, 'entregado', 'yape', 'pagado', 67.00, 7.00, 0.00, NULL, NULL, 74.00, NULL, NULL, NULL, '2026-04-18 19:27:16', '2026-04-19 00:03:16', '2026-04-19 00:03:16', NULL),
	(52, 'PED-000052', 3, 1, 8, 'Av. Brasil 890', NULL, 'San Borja', 5, 'entregado', 'yape', 'pagado', 65.00, 9.00, 6.50, NULL, NULL, 67.50, NULL, NULL, NULL, '2026-04-18 21:29:56', '2026-04-19 01:52:56', '2026-04-19 01:52:56', NULL),
	(53, 'PED-000053', 12, 1, 1, 'Av. Aviación 3300', NULL, 'Miraflores', 2, 'entregado', 'efectivo', 'pagado', 33.00, 8.00, 3.30, NULL, NULL, 37.70, NULL, NULL, NULL, '2026-04-18 16:55:03', '2026-04-18 21:05:03', '2026-04-18 21:05:03', NULL),
	(54, 'PED-000054', 2, 1, 2, 'Jr. Lima 567', NULL, 'San Isidro', 3, 'entregado', 'plin', 'pagado', 26.00, 8.00, 0.00, NULL, NULL, 34.00, NULL, NULL, NULL, '2026-04-19 23:02:26', '2026-04-20 02:53:26', '2026-04-20 02:53:26', NULL),
	(55, 'PED-000055', 7, 1, 4, 'Av. Universitaria 88', NULL, 'Miraflores', 2, 'entregado', 'efectivo', 'pagado', 81.00, 8.00, 0.00, NULL, NULL, 89.00, NULL, NULL, NULL, '2026-04-19 23:16:55', '2026-04-20 03:29:55', '2026-04-20 03:29:55', NULL),
	(56, 'PED-000056', 5, 1, 9, 'Av. Colonial 456', NULL, 'San Miguel', 8, 'entregado', 'plin', 'pagado', 185.00, 7.00, 0.00, NULL, NULL, 192.00, NULL, NULL, NULL, '2026-04-19 15:40:32', '2026-04-19 20:02:32', '2026-04-19 20:02:32', NULL),
	(57, 'PED-000057', 11, 1, 2, 'Jr. Cusco 410', NULL, 'San Miguel', 8, 'entregado', 'transferencia', 'pagado', 85.00, 7.00, 0.00, NULL, NULL, 92.00, NULL, NULL, NULL, '2026-04-19 13:31:24', '2026-04-19 17:46:24', '2026-04-19 17:46:24', NULL),
	(58, 'PED-000058', 9, 1, NULL, 'Av. La Marina 1500', NULL, 'Surco', 4, 'cancelado', 'yape', 'pendiente', 105.00, 10.00, 0.00, NULL, NULL, 115.00, NULL, NULL, NULL, NULL, '2026-04-20 02:13:23', '2026-04-20 02:13:23', NULL),
	(59, 'PED-000059', 5, 1, 7, 'Av. Colonial 456', NULL, 'San Borja', 5, 'entregado', 'efectivo', 'pagado', 134.50, 9.00, 0.00, NULL, NULL, 143.50, NULL, NULL, NULL, '2026-04-20 17:42:57', '2026-04-20 21:44:57', '2026-04-20 21:44:57', NULL),
	(60, 'PED-000060', 12, 1, 5, 'Av. Aviación 3300', NULL, 'Ate', 10, 'entregado', 'tarjeta', 'pagado', 145.00, 13.00, 0.00, NULL, NULL, 158.00, NULL, NULL, NULL, '2026-04-20 16:58:21', '2026-04-20 21:24:21', '2026-04-20 21:24:21', NULL),
	(61, 'PED-000061', 7, 1, 4, 'Av. Universitaria 88', NULL, 'Miraflores', 2, 'entregado', 'yape', 'pagado', 114.00, 8.00, 0.00, NULL, NULL, 122.00, NULL, NULL, NULL, '2026-04-20 17:52:04', '2026-04-20 21:37:04', '2026-04-20 21:37:04', NULL),
	(62, 'PED-000062', 8, 1, 6, 'Calle Real 234', NULL, 'San Borja', 5, 'entregado', 'plin', 'pagado', 27.00, 9.00, 0.00, NULL, NULL, 36.00, NULL, NULL, NULL, '2026-04-20 17:39:21', '2026-04-20 21:46:21', '2026-04-20 21:46:21', NULL),
	(63, 'PED-000063', 10, 1, 9, 'Calle Schell 250', NULL, 'San Isidro', 3, 'entregado', 'yape', 'pagado', 145.00, 8.00, 0.00, NULL, NULL, 153.00, NULL, NULL, NULL, '2026-04-21 12:45:40', '2026-04-21 16:47:40', '2026-04-21 16:47:40', NULL),
	(64, 'PED-000064', 2, 1, 8, 'Jr. Lima 567', NULL, 'San Miguel', 8, 'entregado', 'efectivo', 'pagado', 51.00, 7.00, 0.00, NULL, NULL, 58.00, NULL, NULL, NULL, '2026-04-21 18:27:00', '2026-04-21 22:17:00', '2026-04-21 22:17:00', NULL),
	(65, 'PED-000065', 6, 1, 6, 'Jr. Tacna 789', NULL, 'Barranco', 6, 'entregado', 'tarjeta', 'pagado', 11.00, 9.00, 1.10, NULL, NULL, 18.90, NULL, NULL, NULL, '2026-04-21 17:45:57', '2026-04-21 21:39:57', '2026-04-21 21:39:57', NULL),
	(66, 'PED-000066', 7, 1, 2, 'Av. Universitaria 88', NULL, 'Los Olivos', 9, 'entregado', 'tarjeta', 'pagado', 60.00, 12.00, 0.00, NULL, NULL, 72.00, NULL, NULL, NULL, '2026-04-22 19:56:22', '2026-04-22 23:48:22', '2026-04-22 23:48:22', NULL),
	(67, 'PED-000067', 7, 1, 6, 'Av. Universitaria 88', NULL, 'Ate', 10, 'entregado', 'plin', 'pagado', 240.00, 13.00, 0.00, NULL, NULL, 253.00, NULL, NULL, NULL, '2026-04-22 17:46:04', '2026-04-22 22:23:04', '2026-04-22 22:23:04', NULL),
	(68, 'PED-000068', 9, 1, 1, 'Av. La Marina 1500', NULL, 'Ate', 10, 'entregado', 'yape', 'pagado', 141.00, 13.00, 0.00, NULL, NULL, 154.00, NULL, NULL, NULL, '2026-04-23 21:59:45', '2026-04-24 02:26:45', '2026-04-24 02:26:45', NULL),
	(69, 'PED-000069', 9, 1, 8, 'Av. La Marina 1500', NULL, 'Ate', 10, 'entregado', 'efectivo', 'pagado', 163.00, 13.00, 0.00, NULL, NULL, 176.00, NULL, NULL, NULL, '2026-04-23 18:25:32', '2026-04-23 22:36:32', '2026-04-23 22:36:32', NULL),
	(70, 'PED-000070', 2, 1, 1, 'Jr. Lima 567', NULL, 'Miraflores', 2, 'entregado', 'tarjeta', 'pagado', 39.00, 8.00, 3.90, NULL, NULL, 43.10, NULL, NULL, NULL, '2026-04-23 21:13:57', '2026-04-24 01:01:57', '2026-04-24 01:01:57', NULL),
	(71, 'PED-000071', 11, 1, 10, 'Jr. Cusco 410', NULL, 'Barranco', 6, 'entregado', 'transferencia', 'pagado', 394.00, 9.00, 0.00, NULL, NULL, 403.00, NULL, NULL, NULL, '2026-04-23 21:46:24', '2026-04-24 02:10:24', '2026-04-24 02:10:24', NULL),
	(72, 'PED-000072', 8, 1, 7, 'Calle Real 234', NULL, 'San Miguel', 8, 'entregado', 'efectivo', 'pagado', 75.00, 7.00, 0.00, NULL, NULL, 82.00, NULL, NULL, NULL, '2026-04-24 20:10:10', '2026-04-25 00:00:10', '2026-04-25 00:00:10', NULL),
	(73, 'PED-000073', 7, 1, 5, 'Av. Universitaria 88', NULL, 'Los Olivos', 9, 'entregado', 'plin', 'pagado', 78.00, 12.00, 7.80, NULL, NULL, 82.20, NULL, NULL, NULL, '2026-04-24 12:55:12', '2026-04-24 17:19:12', '2026-04-24 17:19:12', NULL),
	(74, 'PED-000074', 7, 1, 6, 'Av. Universitaria 88', NULL, 'Surco', 4, 'entregado', 'transferencia', 'pagado', 26.00, 10.00, 0.00, NULL, NULL, 36.00, NULL, NULL, NULL, '2026-04-24 21:30:43', '2026-04-25 02:04:43', '2026-04-25 02:04:43', NULL),
	(75, 'PED-000075', 3, 1, 2, 'Av. Brasil 890', NULL, 'Miraflores', 2, 'en_camino', 'plin', 'pendiente', 20.00, 8.00, 0.00, NULL, NULL, 28.00, NULL, NULL, NULL, NULL, '2026-04-25 17:22:59', '2026-04-25 17:22:59', NULL),
	(76, 'PED-000076', 7, 1, 5, 'Av. Universitaria 88', NULL, 'Pueblo Libre', 7, 'entregado', 'transferencia', 'pagado', 17.00, 7.00, 0.00, NULL, NULL, 24.00, NULL, NULL, NULL, '2026-04-25 13:10:22', '2026-04-25 17:28:22', '2026-04-25 17:28:22', NULL),
	(77, 'PED-000077', 1, 1, NULL, 'Av. Arequipa 1234', NULL, 'Cercado de Lima', 1, 'cancelado', 'tarjeta', 'pendiente', 18.00, 6.00, 0.00, NULL, NULL, 24.00, NULL, NULL, NULL, NULL, '2026-04-25 16:51:39', '2026-04-25 16:51:39', NULL),
	(78, 'PED-000078', 12, 1, NULL, 'Av. Aviación 3300', NULL, 'Miraflores', 2, 'confirmado', 'efectivo', 'pendiente', 70.50, 8.00, 0.00, NULL, NULL, 78.50, NULL, NULL, NULL, NULL, '2026-04-26 20:53:46', '2026-04-26 20:53:46', NULL),
	(79, 'PED-000079', 8, 1, NULL, 'Calle Real 234', NULL, 'Ate', 10, 'preparando', 'efectivo', 'pendiente', 34.00, 13.00, 0.00, NULL, NULL, 47.00, NULL, NULL, NULL, NULL, '2026-04-27 02:15:05', '2026-04-27 02:15:05', NULL),
	(80, 'PED-000080', 8, 1, 5, 'Calle Real 234', NULL, 'San Borja', 5, 'entregado', 'efectivo', 'pagado', 128.00, 9.00, 0.00, NULL, NULL, 137.00, NULL, NULL, NULL, '2026-04-26 22:55:43', '2026-04-27 03:25:43', '2026-04-27 03:25:43', NULL),
	(81, 'PED-000081', 1, 1, NULL, 'Av. Arequipa 1234', NULL, 'Los Olivos', 9, 'cancelado', 'efectivo', 'pendiente', 106.00, 12.00, 10.60, NULL, NULL, 107.40, NULL, NULL, NULL, NULL, '2026-04-27 21:54:36', '2026-04-27 21:54:36', NULL),
	(82, 'PED-000082', 8, 1, 6, 'Calle Real 234', NULL, 'Cercado de Lima', 1, 'entregado', 'efectivo', 'pagado', 27.00, 6.00, 2.70, NULL, NULL, 30.30, NULL, NULL, NULL, '2026-04-27 17:26:46', '2026-04-27 22:03:46', '2026-04-27 22:03:46', NULL),
	(83, 'PED-000083', 2, 1, NULL, 'Jr. Lima 567', NULL, 'San Isidro', 3, 'listo', 'yape', 'pendiente', 81.00, 8.00, 0.00, NULL, NULL, 89.00, NULL, NULL, NULL, NULL, '2026-04-28 21:47:56', '2026-04-28 21:47:56', NULL),
	(84, 'PED-000084', 9, 1, 8, 'Av. La Marina 1500', NULL, 'San Borja', 5, 'entregado', 'efectivo', 'pagado', 99.00, 9.00, 0.00, NULL, NULL, 108.00, NULL, NULL, NULL, '2026-04-28 14:35:02', '2026-04-28 18:51:02', '2026-04-28 18:51:02', NULL),
	(85, 'PED-000085', 9, 1, 6, 'Av. La Marina 1500', NULL, 'Cercado de Lima', 1, 'en_camino', 'tarjeta', 'pendiente', 97.50, 6.00, 0.00, NULL, NULL, 103.50, NULL, NULL, NULL, NULL, '2026-04-29 00:54:16', '2026-04-29 00:54:16', NULL),
	(86, 'PED-000086', 7, 1, NULL, 'Av. Universitaria 88', NULL, 'Pueblo Libre', 7, 'pendiente', 'tarjeta', 'pendiente', 50.50, 7.00, 5.05, NULL, NULL, 52.45, NULL, NULL, NULL, NULL, '2026-04-30 00:00:48', '2026-04-30 00:00:48', NULL),
	(87, 'PED-000087', 2, 1, NULL, 'Jr. Lima 567', NULL, 'San Isidro', 3, 'cancelado', 'tarjeta', 'pendiente', 22.00, 8.00, 0.00, NULL, NULL, 30.00, NULL, NULL, NULL, NULL, '2026-04-30 16:16:48', '2026-04-30 16:16:48', NULL),
	(88, 'PED-000088', 5, 1, NULL, 'Av. Colonial 456', NULL, 'San Isidro', 3, 'pendiente', 'plin', 'pendiente', 151.00, 8.00, 0.00, NULL, NULL, 159.00, NULL, NULL, NULL, NULL, '2026-04-30 20:35:57', '2026-04-30 20:35:57', NULL),
	(89, 'PED-000089', 11, 1, 5, 'Jr. Cusco 410', NULL, 'Ate', 10, 'entregado', 'plin', 'pagado', 40.00, 13.00, 4.00, NULL, NULL, 49.00, NULL, NULL, NULL, '2026-05-01 14:51:09', '2026-05-01 18:52:09', '2026-05-01 18:52:09', NULL),
	(90, 'PED-000090', 8, 1, 3, 'Calle Real 234', NULL, 'San Isidro', 3, 'en_camino', 'transferencia', 'pendiente', 84.00, 8.00, 8.40, NULL, NULL, 83.60, NULL, NULL, NULL, NULL, '2026-05-03 02:41:51', '2026-05-03 02:41:51', NULL),
	(91, 'PED-000091', 12, 1, NULL, 'Av. Aviación 3300', NULL, 'San Miguel', 8, 'pendiente', 'plin', 'pendiente', 92.00, 7.00, 0.00, NULL, NULL, 99.00, NULL, NULL, NULL, NULL, '2026-05-02 21:50:57', '2026-05-02 21:50:57', NULL),
	(92, 'PED-000092', 7, 1, 5, 'Av. Universitaria 88', NULL, 'Miraflores', 2, 'entregado', 'yape', 'pagado', 220.00, 8.00, 0.00, NULL, NULL, 228.00, NULL, NULL, NULL, '2026-05-02 13:58:49', '2026-05-02 18:24:49', '2026-05-02 18:24:49', NULL);

-- Volcando estructura para tabla delivery_crm.pedido_items
CREATE TABLE IF NOT EXISTS `pedido_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `producto_id` bigint unsigned DEFAULT NULL,
  `nombre_producto` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `cantidad` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_items_pedido_id_foreign` (`pedido_id`),
  KEY `pedido_items_producto_id_foreign` (`producto_id`),
  CONSTRAINT `pedido_items_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pedido_items_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.pedido_items: ~218 rows (aproximadamente)
DELETE FROM `pedido_items`;
INSERT INTO `pedido_items` (`id`, `pedido_id`, `producto_id`, `nombre_producto`, `precio_unitario`, `cantidad`, `subtotal`, `notas`, `created_at`, `updated_at`) VALUES
	(1, 1, 5, 'Gaseosa 1.5L', 8.00, 3, 24.00, NULL, '2026-04-04 01:58:28', '2026-04-04 01:58:28'),
	(2, 2, 16, 'Helado 2 bolas', 9.00, 2, 18.00, NULL, '2026-04-04 00:18:03', '2026-04-04 00:18:03'),
	(3, 2, 9, 'Pizza Margarita (personal)', 22.00, 1, 22.00, 'Sin cebolla', '2026-04-04 00:18:03', '2026-04-04 00:18:03'),
	(4, 3, 15, 'Menú Especial', 24.00, 3, 72.00, NULL, '2026-04-03 20:35:18', '2026-04-03 20:35:18'),
	(5, 4, 6, 'Jugo Natural 500ml', 9.00, 2, 18.00, NULL, '2026-04-04 18:11:46', '2026-04-04 18:11:46'),
	(6, 4, 1, 'Hamburguesa Clásica', 20.00, 2, 40.00, 'Sin cebolla', '2026-04-04 18:11:46', '2026-04-04 18:11:46'),
	(7, 4, 7, 'Agua Mineral 625ml', 3.50, 2, 7.00, NULL, '2026-04-04 18:11:46', '2026-04-04 18:11:46'),
	(8, 4, 1, 'Hamburguesa Clásica', 20.00, 2, 40.00, NULL, '2026-04-04 18:11:46', '2026-04-04 18:11:46'),
	(9, 5, 16, 'Helado 2 bolas', 9.00, 3, 27.00, NULL, '2026-04-05 02:49:56', '2026-04-05 02:49:56'),
	(10, 5, 3, 'Hot Dog Completo', 13.00, 3, 39.00, 'Sin cebolla', '2026-04-05 02:49:56', '2026-04-05 02:49:56'),
	(11, 5, 10, 'Pizza Especial (familiar)', 47.00, 3, 141.00, NULL, '2026-04-05 02:49:56', '2026-04-05 02:49:56'),
	(12, 6, 8, 'Chicha Morada 1L', 7.00, 2, 14.00, NULL, '2026-04-05 00:06:07', '2026-04-05 00:06:07'),
	(13, 6, 3, 'Hot Dog Completo', 13.00, 1, 13.00, NULL, '2026-04-05 00:06:07', '2026-04-05 00:06:07'),
	(14, 6, 4, 'Papas Fritas Grandes', 11.00, 1, 11.00, NULL, '2026-04-05 00:06:07', '2026-04-05 00:06:07'),
	(15, 7, 7, 'Agua Mineral 625ml', 3.50, 2, 7.00, NULL, '2026-04-04 23:39:23', '2026-04-04 23:39:23'),
	(16, 8, 4, 'Papas Fritas Grandes', 11.00, 2, 22.00, NULL, '2026-04-05 21:56:48', '2026-04-05 21:56:48'),
	(17, 8, 6, 'Jugo Natural 500ml', 9.00, 2, 18.00, NULL, '2026-04-05 21:56:48', '2026-04-05 21:56:48'),
	(18, 8, 17, 'Torta de Chocolate', 13.00, 2, 26.00, NULL, '2026-04-05 21:56:48', '2026-04-05 21:56:48'),
	(19, 9, 9, 'Pizza Margarita (personal)', 22.00, 1, 22.00, NULL, '2026-04-05 16:33:55', '2026-04-05 16:33:55'),
	(20, 9, 16, 'Helado 2 bolas', 9.00, 2, 18.00, NULL, '2026-04-05 16:33:55', '2026-04-05 16:33:55'),
	(21, 10, 4, 'Papas Fritas Grandes', 11.00, 1, 11.00, NULL, '2026-04-05 21:00:10', '2026-04-05 21:00:10'),
	(22, 10, 16, 'Helado 2 bolas', 9.00, 1, 9.00, 'Sin cebolla', '2026-04-05 21:00:10', '2026-04-05 21:00:10'),
	(23, 10, 12, 'Pollo a la Brasa (1/2)', 34.00, 3, 102.00, NULL, '2026-04-05 21:00:10', '2026-04-05 21:00:10'),
	(24, 10, 13, 'Pollo a la Brasa (entero)', 60.00, 2, 120.00, NULL, '2026-04-05 21:00:10', '2026-04-05 21:00:10'),
	(25, 11, 13, 'Pollo a la Brasa (entero)', 60.00, 2, 120.00, NULL, '2026-04-06 01:17:04', '2026-04-06 01:17:04'),
	(26, 11, 7, 'Agua Mineral 625ml', 3.50, 2, 7.00, NULL, '2026-04-06 01:17:04', '2026-04-06 01:17:04'),
	(27, 11, 2, 'Hamburguesa Especial', 27.00, 3, 81.00, NULL, '2026-04-06 01:17:04', '2026-04-06 01:17:04'),
	(28, 12, 10, 'Pizza Especial (familiar)', 47.00, 1, 47.00, NULL, '2026-04-06 22:32:39', '2026-04-06 22:32:39'),
	(29, 12, 10, 'Pizza Especial (familiar)', 47.00, 1, 47.00, 'Sin cebolla', '2026-04-06 22:32:39', '2026-04-06 22:32:39'),
	(30, 12, 3, 'Hot Dog Completo', 13.00, 2, 26.00, NULL, '2026-04-06 22:32:39', '2026-04-06 22:32:39'),
	(31, 13, 3, 'Hot Dog Completo', 13.00, 2, 26.00, 'Sin cebolla', '2026-04-06 19:02:14', '2026-04-06 19:02:14'),
	(32, 14, 8, 'Chicha Morada 1L', 7.00, 3, 21.00, 'Sin cebolla', '2026-04-06 19:48:51', '2026-04-06 19:48:51'),
	(33, 15, 2, 'Hamburguesa Especial', 27.00, 3, 81.00, 'Sin cebolla', '2026-04-07 02:55:04', '2026-04-07 02:55:04'),
	(34, 15, 16, 'Helado 2 bolas', 9.00, 1, 9.00, NULL, '2026-04-07 02:55:04', '2026-04-07 02:55:04'),
	(35, 15, 11, 'Pollo a la Brasa (1/4)', 20.00, 2, 40.00, NULL, '2026-04-07 02:55:04', '2026-04-07 02:55:04'),
	(36, 16, 9, 'Pizza Margarita (personal)', 22.00, 3, 66.00, 'Sin cebolla', '2026-04-07 16:13:23', '2026-04-07 16:13:23'),
	(37, 16, 1, 'Hamburguesa Clásica', 20.00, 3, 60.00, NULL, '2026-04-07 16:13:23', '2026-04-07 16:13:23'),
	(38, 17, 5, 'Gaseosa 1.5L', 8.00, 2, 16.00, NULL, '2026-04-07 16:57:03', '2026-04-07 16:57:03'),
	(39, 17, 11, 'Pollo a la Brasa (1/4)', 20.00, 1, 20.00, NULL, '2026-04-07 16:57:03', '2026-04-07 16:57:03'),
	(40, 17, 6, 'Jugo Natural 500ml', 9.00, 1, 9.00, NULL, '2026-04-07 16:57:03', '2026-04-07 16:57:03'),
	(41, 17, 13, 'Pollo a la Brasa (entero)', 60.00, 1, 60.00, NULL, '2026-04-07 16:57:03', '2026-04-07 16:57:03'),
	(42, 18, 9, 'Pizza Margarita (personal)', 22.00, 2, 44.00, NULL, '2026-04-07 17:37:27', '2026-04-07 17:37:27'),
	(43, 18, 10, 'Pizza Especial (familiar)', 47.00, 1, 47.00, NULL, '2026-04-07 17:37:27', '2026-04-07 17:37:27'),
	(44, 18, 4, 'Papas Fritas Grandes', 11.00, 3, 33.00, NULL, '2026-04-07 17:37:27', '2026-04-07 17:37:27'),
	(45, 19, 16, 'Helado 2 bolas', 9.00, 1, 9.00, NULL, '2026-04-07 23:06:49', '2026-04-07 23:06:49'),
	(46, 19, 8, 'Chicha Morada 1L', 7.00, 1, 7.00, NULL, '2026-04-07 23:06:49', '2026-04-07 23:06:49'),
	(47, 19, 16, 'Helado 2 bolas', 9.00, 2, 18.00, NULL, '2026-04-07 23:06:49', '2026-04-07 23:06:49'),
	(48, 20, 5, 'Gaseosa 1.5L', 8.00, 1, 8.00, 'Sin cebolla', '2026-04-08 20:54:36', '2026-04-08 20:54:36'),
	(49, 20, 5, 'Gaseosa 1.5L', 8.00, 2, 16.00, 'Sin cebolla', '2026-04-08 20:54:36', '2026-04-08 20:54:36'),
	(50, 20, 14, 'Menú Ejecutivo', 17.00, 1, 17.00, NULL, '2026-04-08 20:54:36', '2026-04-08 20:54:36'),
	(51, 20, 8, 'Chicha Morada 1L', 7.00, 2, 14.00, NULL, '2026-04-08 20:54:36', '2026-04-08 20:54:36'),
	(52, 21, 8, 'Chicha Morada 1L', 7.00, 1, 7.00, NULL, '2026-04-10 00:43:15', '2026-04-10 00:43:15'),
	(53, 22, 2, 'Hamburguesa Especial', 27.00, 3, 81.00, 'Sin cebolla', '2026-04-09 23:46:52', '2026-04-09 23:46:52'),
	(54, 23, 9, 'Pizza Margarita (personal)', 22.00, 3, 66.00, NULL, '2026-04-11 03:11:47', '2026-04-11 03:11:47'),
	(55, 23, 10, 'Pizza Especial (familiar)', 47.00, 1, 47.00, 'Sin cebolla', '2026-04-11 03:11:47', '2026-04-11 03:11:47'),
	(56, 23, 1, 'Hamburguesa Clásica', 20.00, 3, 60.00, NULL, '2026-04-11 03:11:47', '2026-04-11 03:11:47'),
	(57, 24, 13, 'Pollo a la Brasa (entero)', 60.00, 1, 60.00, 'Sin cebolla', '2026-04-10 18:10:51', '2026-04-10 18:10:51'),
	(58, 25, 12, 'Pollo a la Brasa (1/2)', 34.00, 1, 34.00, NULL, '2026-04-10 16:02:49', '2026-04-10 16:02:49'),
	(59, 25, 4, 'Papas Fritas Grandes', 11.00, 3, 33.00, NULL, '2026-04-10 16:02:49', '2026-04-10 16:02:49'),
	(60, 25, 10, 'Pizza Especial (familiar)', 47.00, 1, 47.00, NULL, '2026-04-10 16:02:49', '2026-04-10 16:02:49'),
	(61, 25, 9, 'Pizza Margarita (personal)', 22.00, 1, 22.00, NULL, '2026-04-10 16:02:49', '2026-04-10 16:02:49'),
	(62, 26, 7, 'Agua Mineral 625ml', 3.50, 2, 7.00, NULL, '2026-04-10 16:56:13', '2026-04-10 16:56:13'),
	(63, 27, 7, 'Agua Mineral 625ml', 3.50, 1, 3.50, NULL, '2026-04-12 00:38:27', '2026-04-12 00:38:27'),
	(64, 27, 4, 'Papas Fritas Grandes', 11.00, 3, 33.00, NULL, '2026-04-12 00:38:27', '2026-04-12 00:38:27'),
	(65, 28, 15, 'Menú Especial', 24.00, 1, 24.00, NULL, '2026-04-11 18:04:04', '2026-04-11 18:04:04'),
	(66, 28, 4, 'Papas Fritas Grandes', 11.00, 3, 33.00, NULL, '2026-04-11 18:04:04', '2026-04-11 18:04:04'),
	(67, 28, 4, 'Papas Fritas Grandes', 11.00, 3, 33.00, NULL, '2026-04-11 18:04:04', '2026-04-11 18:04:04'),
	(68, 29, 3, 'Hot Dog Completo', 13.00, 1, 13.00, NULL, '2026-04-11 17:41:20', '2026-04-11 17:41:20'),
	(69, 29, 6, 'Jugo Natural 500ml', 9.00, 3, 27.00, NULL, '2026-04-11 17:41:20', '2026-04-11 17:41:20'),
	(70, 30, 1, 'Hamburguesa Clásica', 20.00, 2, 40.00, 'Sin cebolla', '2026-04-11 19:11:56', '2026-04-11 19:11:56'),
	(71, 30, 16, 'Helado 2 bolas', 9.00, 1, 9.00, 'Sin cebolla', '2026-04-11 19:11:56', '2026-04-11 19:11:56'),
	(72, 30, 17, 'Torta de Chocolate', 13.00, 2, 26.00, NULL, '2026-04-11 19:11:56', '2026-04-11 19:11:56'),
	(73, 31, 1, 'Hamburguesa Clásica', 20.00, 3, 60.00, NULL, '2026-04-12 21:15:31', '2026-04-12 21:15:31'),
	(74, 31, 2, 'Hamburguesa Especial', 27.00, 1, 27.00, 'Sin cebolla', '2026-04-12 21:15:31', '2026-04-12 21:15:31'),
	(75, 32, 7, 'Agua Mineral 625ml', 3.50, 2, 7.00, 'Sin cebolla', '2026-04-13 01:11:56', '2026-04-13 01:11:56'),
	(76, 33, 10, 'Pizza Especial (familiar)', 47.00, 1, 47.00, NULL, '2026-04-12 22:14:11', '2026-04-12 22:14:11'),
	(77, 33, 1, 'Hamburguesa Clásica', 20.00, 1, 20.00, NULL, '2026-04-12 22:14:11', '2026-04-12 22:14:11'),
	(78, 33, 13, 'Pollo a la Brasa (entero)', 60.00, 1, 60.00, NULL, '2026-04-12 22:14:11', '2026-04-12 22:14:11'),
	(79, 34, 1, 'Hamburguesa Clásica', 20.00, 3, 60.00, 'Sin cebolla', '2026-04-14 00:36:12', '2026-04-14 00:36:12'),
	(80, 34, 4, 'Papas Fritas Grandes', 11.00, 2, 22.00, NULL, '2026-04-14 00:36:12', '2026-04-14 00:36:12'),
	(81, 35, 7, 'Agua Mineral 625ml', 3.50, 1, 3.50, 'Sin cebolla', '2026-04-14 03:28:29', '2026-04-14 03:28:29'),
	(82, 35, 16, 'Helado 2 bolas', 9.00, 2, 18.00, NULL, '2026-04-14 03:28:29', '2026-04-14 03:28:29'),
	(83, 35, 17, 'Torta de Chocolate', 13.00, 2, 26.00, 'Sin cebolla', '2026-04-14 03:28:29', '2026-04-14 03:28:29'),
	(84, 35, 13, 'Pollo a la Brasa (entero)', 60.00, 3, 180.00, NULL, '2026-04-14 03:28:29', '2026-04-14 03:28:29'),
	(85, 36, 12, 'Pollo a la Brasa (1/2)', 34.00, 1, 34.00, NULL, '2026-04-14 03:08:39', '2026-04-14 03:08:39'),
	(86, 36, 14, 'Menú Ejecutivo', 17.00, 3, 51.00, NULL, '2026-04-14 03:08:39', '2026-04-14 03:08:39'),
	(87, 36, 15, 'Menú Especial', 24.00, 3, 72.00, NULL, '2026-04-14 03:08:39', '2026-04-14 03:08:39'),
	(88, 36, 12, 'Pollo a la Brasa (1/2)', 34.00, 3, 102.00, NULL, '2026-04-14 03:08:39', '2026-04-14 03:08:39'),
	(89, 37, 6, 'Jugo Natural 500ml', 9.00, 1, 9.00, NULL, '2026-04-14 16:19:55', '2026-04-14 16:19:55'),
	(90, 37, 17, 'Torta de Chocolate', 13.00, 1, 13.00, 'Sin cebolla', '2026-04-14 16:19:55', '2026-04-14 16:19:55'),
	(91, 37, 9, 'Pizza Margarita (personal)', 22.00, 1, 22.00, 'Sin cebolla', '2026-04-14 16:19:55', '2026-04-14 16:19:55'),
	(92, 38, 14, 'Menú Ejecutivo', 17.00, 1, 17.00, NULL, '2026-04-16 02:13:11', '2026-04-16 02:13:11'),
	(93, 39, 1, 'Hamburguesa Clásica', 20.00, 3, 60.00, NULL, '2026-04-15 22:18:45', '2026-04-15 22:18:45'),
	(94, 40, 5, 'Gaseosa 1.5L', 8.00, 3, 24.00, 'Sin cebolla', '2026-04-15 21:58:02', '2026-04-15 21:58:02'),
	(95, 40, 15, 'Menú Especial', 24.00, 3, 72.00, NULL, '2026-04-15 21:58:02', '2026-04-15 21:58:02'),
	(96, 41, 10, 'Pizza Especial (familiar)', 47.00, 1, 47.00, NULL, '2026-04-16 18:23:14', '2026-04-16 18:23:14'),
	(97, 41, 13, 'Pollo a la Brasa (entero)', 60.00, 3, 180.00, NULL, '2026-04-16 18:23:14', '2026-04-16 18:23:14'),
	(98, 41, 9, 'Pizza Margarita (personal)', 22.00, 3, 66.00, NULL, '2026-04-16 18:23:14', '2026-04-16 18:23:14'),
	(99, 42, 9, 'Pizza Margarita (personal)', 22.00, 1, 22.00, NULL, '2026-04-17 03:03:28', '2026-04-17 03:03:28'),
	(100, 42, 8, 'Chicha Morada 1L', 7.00, 3, 21.00, NULL, '2026-04-17 03:03:28', '2026-04-17 03:03:28'),
	(101, 42, 16, 'Helado 2 bolas', 9.00, 3, 27.00, NULL, '2026-04-17 03:03:28', '2026-04-17 03:03:28'),
	(102, 43, 4, 'Papas Fritas Grandes', 11.00, 1, 11.00, 'Sin cebolla', '2026-04-16 16:36:35', '2026-04-16 16:36:35'),
	(103, 43, 4, 'Papas Fritas Grandes', 11.00, 3, 33.00, NULL, '2026-04-16 16:36:35', '2026-04-16 16:36:35'),
	(104, 43, 12, 'Pollo a la Brasa (1/2)', 34.00, 2, 68.00, 'Sin cebolla', '2026-04-16 16:36:35', '2026-04-16 16:36:35'),
	(105, 43, 15, 'Menú Especial', 24.00, 2, 48.00, NULL, '2026-04-16 16:36:35', '2026-04-16 16:36:35'),
	(106, 44, 11, 'Pollo a la Brasa (1/4)', 20.00, 1, 20.00, NULL, '2026-04-16 21:49:46', '2026-04-16 21:49:46'),
	(107, 44, 4, 'Papas Fritas Grandes', 11.00, 2, 22.00, NULL, '2026-04-16 21:49:46', '2026-04-16 21:49:46'),
	(108, 44, 5, 'Gaseosa 1.5L', 8.00, 3, 24.00, 'Sin cebolla', '2026-04-16 21:49:46', '2026-04-16 21:49:46'),
	(109, 44, 8, 'Chicha Morada 1L', 7.00, 2, 14.00, 'Sin cebolla', '2026-04-16 21:49:46', '2026-04-16 21:49:46'),
	(110, 45, 16, 'Helado 2 bolas', 9.00, 2, 18.00, NULL, '2026-04-17 23:04:59', '2026-04-17 23:04:59'),
	(111, 45, 2, 'Hamburguesa Especial', 27.00, 1, 27.00, NULL, '2026-04-17 23:04:59', '2026-04-17 23:04:59'),
	(112, 46, 7, 'Agua Mineral 625ml', 3.50, 2, 7.00, NULL, '2026-04-17 19:40:37', '2026-04-17 19:40:37'),
	(113, 46, 7, 'Agua Mineral 625ml', 3.50, 3, 10.50, 'Sin cebolla', '2026-04-17 19:40:37', '2026-04-17 19:40:37'),
	(114, 46, 1, 'Hamburguesa Clásica', 20.00, 3, 60.00, 'Sin cebolla', '2026-04-17 19:40:37', '2026-04-17 19:40:37'),
	(115, 47, 11, 'Pollo a la Brasa (1/4)', 20.00, 2, 40.00, NULL, '2026-04-17 19:50:19', '2026-04-17 19:50:19'),
	(116, 47, 4, 'Papas Fritas Grandes', 11.00, 3, 33.00, 'Sin cebolla', '2026-04-17 19:50:19', '2026-04-17 19:50:19'),
	(117, 48, 3, 'Hot Dog Completo', 13.00, 2, 26.00, NULL, '2026-04-17 22:25:02', '2026-04-17 22:25:02'),
	(118, 48, 9, 'Pizza Margarita (personal)', 22.00, 1, 22.00, NULL, '2026-04-17 22:25:02', '2026-04-17 22:25:02'),
	(119, 49, 2, 'Hamburguesa Especial', 27.00, 2, 54.00, 'Sin cebolla', '2026-04-19 01:05:10', '2026-04-19 01:05:10'),
	(120, 49, 14, 'Menú Ejecutivo', 17.00, 1, 17.00, NULL, '2026-04-19 01:05:10', '2026-04-19 01:05:10'),
	(121, 50, 8, 'Chicha Morada 1L', 7.00, 3, 21.00, NULL, '2026-04-18 16:10:40', '2026-04-18 16:10:40'),
	(122, 50, 4, 'Papas Fritas Grandes', 11.00, 2, 22.00, 'Sin cebolla', '2026-04-18 16:10:40', '2026-04-18 16:10:40'),
	(123, 51, 5, 'Gaseosa 1.5L', 8.00, 2, 16.00, NULL, '2026-04-19 00:03:16', '2026-04-19 00:03:16'),
	(124, 51, 14, 'Menú Ejecutivo', 17.00, 3, 51.00, NULL, '2026-04-19 00:03:16', '2026-04-19 00:03:16'),
	(125, 52, 6, 'Jugo Natural 500ml', 9.00, 2, 18.00, NULL, '2026-04-19 01:52:56', '2026-04-19 01:52:56'),
	(126, 52, 3, 'Hot Dog Completo', 13.00, 1, 13.00, 'Sin cebolla', '2026-04-19 01:52:56', '2026-04-19 01:52:56'),
	(127, 52, 12, 'Pollo a la Brasa (1/2)', 34.00, 1, 34.00, NULL, '2026-04-19 01:52:56', '2026-04-19 01:52:56'),
	(128, 53, 4, 'Papas Fritas Grandes', 11.00, 3, 33.00, NULL, '2026-04-18 21:05:03', '2026-04-18 21:05:03'),
	(129, 54, 3, 'Hot Dog Completo', 13.00, 2, 26.00, NULL, '2026-04-20 02:53:26', '2026-04-20 02:53:26'),
	(130, 55, 2, 'Hamburguesa Especial', 27.00, 2, 54.00, NULL, '2026-04-20 03:29:55', '2026-04-20 03:29:55'),
	(131, 55, 6, 'Jugo Natural 500ml', 9.00, 3, 27.00, NULL, '2026-04-20 03:29:55', '2026-04-20 03:29:55'),
	(132, 56, 11, 'Pollo a la Brasa (1/4)', 20.00, 3, 60.00, NULL, '2026-04-19 20:02:32', '2026-04-19 20:02:32'),
	(133, 56, 13, 'Pollo a la Brasa (entero)', 60.00, 1, 60.00, NULL, '2026-04-19 20:02:32', '2026-04-19 20:02:32'),
	(134, 56, 4, 'Papas Fritas Grandes', 11.00, 1, 11.00, NULL, '2026-04-19 20:02:32', '2026-04-19 20:02:32'),
	(135, 56, 2, 'Hamburguesa Especial', 27.00, 2, 54.00, 'Sin cebolla', '2026-04-19 20:02:32', '2026-04-19 20:02:32'),
	(136, 57, 14, 'Menú Ejecutivo', 17.00, 2, 34.00, NULL, '2026-04-19 17:46:24', '2026-04-19 17:46:24'),
	(137, 57, 14, 'Menú Ejecutivo', 17.00, 3, 51.00, NULL, '2026-04-19 17:46:24', '2026-04-19 17:46:24'),
	(138, 58, 16, 'Helado 2 bolas', 9.00, 3, 27.00, NULL, '2026-04-20 02:13:23', '2026-04-20 02:13:23'),
	(139, 58, 5, 'Gaseosa 1.5L', 8.00, 3, 24.00, NULL, '2026-04-20 02:13:23', '2026-04-20 02:13:23'),
	(140, 58, 2, 'Hamburguesa Especial', 27.00, 2, 54.00, 'Sin cebolla', '2026-04-20 02:13:23', '2026-04-20 02:13:23'),
	(141, 59, 7, 'Agua Mineral 625ml', 3.50, 3, 10.50, NULL, '2026-04-20 21:44:57', '2026-04-20 21:44:57'),
	(142, 59, 4, 'Papas Fritas Grandes', 11.00, 2, 22.00, NULL, '2026-04-20 21:44:57', '2026-04-20 21:44:57'),
	(143, 59, 12, 'Pollo a la Brasa (1/2)', 34.00, 3, 102.00, 'Sin cebolla', '2026-04-20 21:44:57', '2026-04-20 21:44:57'),
	(144, 60, 5, 'Gaseosa 1.5L', 8.00, 3, 24.00, NULL, '2026-04-20 21:24:21', '2026-04-20 21:24:21'),
	(145, 60, 16, 'Helado 2 bolas', 9.00, 3, 27.00, 'Sin cebolla', '2026-04-20 21:24:21', '2026-04-20 21:24:21'),
	(146, 60, 3, 'Hot Dog Completo', 13.00, 2, 26.00, NULL, '2026-04-20 21:24:21', '2026-04-20 21:24:21'),
	(147, 60, 12, 'Pollo a la Brasa (1/2)', 34.00, 2, 68.00, NULL, '2026-04-20 21:24:21', '2026-04-20 21:24:21'),
	(148, 61, 2, 'Hamburguesa Especial', 27.00, 2, 54.00, 'Sin cebolla', '2026-04-20 21:37:04', '2026-04-20 21:37:04'),
	(149, 61, 11, 'Pollo a la Brasa (1/4)', 20.00, 3, 60.00, 'Sin cebolla', '2026-04-20 21:37:04', '2026-04-20 21:37:04'),
	(150, 62, 6, 'Jugo Natural 500ml', 9.00, 3, 27.00, NULL, '2026-04-20 21:46:21', '2026-04-20 21:46:21'),
	(151, 63, 14, 'Menú Ejecutivo', 17.00, 3, 51.00, NULL, '2026-04-21 16:47:40', '2026-04-21 16:47:40'),
	(152, 63, 10, 'Pizza Especial (familiar)', 47.00, 2, 94.00, 'Sin cebolla', '2026-04-21 16:47:40', '2026-04-21 16:47:40'),
	(153, 64, 14, 'Menú Ejecutivo', 17.00, 3, 51.00, 'Sin cebolla', '2026-04-21 22:17:00', '2026-04-21 22:17:00'),
	(154, 65, 4, 'Papas Fritas Grandes', 11.00, 1, 11.00, NULL, '2026-04-21 21:39:57', '2026-04-21 21:39:57'),
	(155, 66, 1, 'Hamburguesa Clásica', 20.00, 3, 60.00, NULL, '2026-04-22 23:48:22', '2026-04-22 23:48:22'),
	(156, 67, 10, 'Pizza Especial (familiar)', 47.00, 1, 47.00, NULL, '2026-04-22 22:23:04', '2026-04-22 22:23:04'),
	(157, 67, 9, 'Pizza Margarita (personal)', 22.00, 3, 66.00, NULL, '2026-04-22 22:23:04', '2026-04-22 22:23:04'),
	(158, 67, 8, 'Chicha Morada 1L', 7.00, 1, 7.00, 'Sin cebolla', '2026-04-22 22:23:04', '2026-04-22 22:23:04'),
	(159, 67, 13, 'Pollo a la Brasa (entero)', 60.00, 2, 120.00, NULL, '2026-04-22 22:23:04', '2026-04-22 22:23:04'),
	(160, 68, 9, 'Pizza Margarita (personal)', 22.00, 2, 44.00, NULL, '2026-04-24 02:26:45', '2026-04-24 02:26:45'),
	(161, 68, 2, 'Hamburguesa Especial', 27.00, 3, 81.00, 'Sin cebolla', '2026-04-24 02:26:45', '2026-04-24 02:26:45'),
	(162, 68, 5, 'Gaseosa 1.5L', 8.00, 2, 16.00, 'Sin cebolla', '2026-04-24 02:26:45', '2026-04-24 02:26:45'),
	(163, 69, 11, 'Pollo a la Brasa (1/4)', 20.00, 3, 60.00, 'Sin cebolla', '2026-04-23 22:36:32', '2026-04-23 22:36:32'),
	(164, 69, 4, 'Papas Fritas Grandes', 11.00, 2, 22.00, NULL, '2026-04-23 22:36:32', '2026-04-23 22:36:32'),
	(165, 69, 17, 'Torta de Chocolate', 13.00, 1, 13.00, 'Sin cebolla', '2026-04-23 22:36:32', '2026-04-23 22:36:32'),
	(166, 69, 12, 'Pollo a la Brasa (1/2)', 34.00, 2, 68.00, NULL, '2026-04-23 22:36:32', '2026-04-23 22:36:32'),
	(167, 70, 17, 'Torta de Chocolate', 13.00, 3, 39.00, 'Sin cebolla', '2026-04-24 01:01:57', '2026-04-24 01:01:57'),
	(168, 71, 10, 'Pizza Especial (familiar)', 47.00, 3, 141.00, NULL, '2026-04-24 02:10:24', '2026-04-24 02:10:24'),
	(169, 71, 13, 'Pollo a la Brasa (entero)', 60.00, 3, 180.00, NULL, '2026-04-24 02:10:24', '2026-04-24 02:10:24'),
	(170, 71, 3, 'Hot Dog Completo', 13.00, 1, 13.00, NULL, '2026-04-24 02:10:24', '2026-04-24 02:10:24'),
	(171, 71, 13, 'Pollo a la Brasa (entero)', 60.00, 1, 60.00, NULL, '2026-04-24 02:10:24', '2026-04-24 02:10:24'),
	(172, 72, 13, 'Pollo a la Brasa (entero)', 60.00, 1, 60.00, 'Sin cebolla', '2026-04-25 00:00:10', '2026-04-25 00:00:10'),
	(173, 72, 5, 'Gaseosa 1.5L', 8.00, 1, 8.00, NULL, '2026-04-25 00:00:10', '2026-04-25 00:00:10'),
	(174, 72, 8, 'Chicha Morada 1L', 7.00, 1, 7.00, NULL, '2026-04-25 00:00:10', '2026-04-25 00:00:10'),
	(175, 73, 5, 'Gaseosa 1.5L', 8.00, 3, 24.00, NULL, '2026-04-24 17:19:12', '2026-04-24 17:19:12'),
	(176, 73, 16, 'Helado 2 bolas', 9.00, 3, 27.00, NULL, '2026-04-24 17:19:12', '2026-04-24 17:19:12'),
	(177, 73, 2, 'Hamburguesa Especial', 27.00, 1, 27.00, NULL, '2026-04-24 17:19:12', '2026-04-24 17:19:12'),
	(178, 74, 17, 'Torta de Chocolate', 13.00, 2, 26.00, NULL, '2026-04-25 02:04:43', '2026-04-25 02:04:43'),
	(179, 75, 11, 'Pollo a la Brasa (1/4)', 20.00, 1, 20.00, 'Sin cebolla', '2026-04-25 17:22:59', '2026-04-25 17:22:59'),
	(180, 76, 14, 'Menú Ejecutivo', 17.00, 1, 17.00, 'Sin cebolla', '2026-04-25 17:28:22', '2026-04-25 17:28:22'),
	(181, 77, 16, 'Helado 2 bolas', 9.00, 1, 9.00, NULL, '2026-04-25 16:51:39', '2026-04-25 16:51:39'),
	(182, 77, 16, 'Helado 2 bolas', 9.00, 1, 9.00, NULL, '2026-04-25 16:51:39', '2026-04-25 16:51:39'),
	(183, 78, 1, 'Hamburguesa Clásica', 20.00, 3, 60.00, NULL, '2026-04-26 20:53:46', '2026-04-26 20:53:46'),
	(184, 78, 7, 'Agua Mineral 625ml', 3.50, 2, 7.00, NULL, '2026-04-26 20:53:46', '2026-04-26 20:53:46'),
	(185, 78, 7, 'Agua Mineral 625ml', 3.50, 1, 3.50, NULL, '2026-04-26 20:53:46', '2026-04-26 20:53:46'),
	(186, 79, 14, 'Menú Ejecutivo', 17.00, 2, 34.00, NULL, '2026-04-27 02:15:05', '2026-04-27 02:15:05'),
	(187, 80, 3, 'Hot Dog Completo', 13.00, 2, 26.00, NULL, '2026-04-27 03:25:43', '2026-04-27 03:25:43'),
	(188, 80, 12, 'Pollo a la Brasa (1/2)', 34.00, 3, 102.00, NULL, '2026-04-27 03:25:43', '2026-04-27 03:25:43'),
	(189, 81, 17, 'Torta de Chocolate', 13.00, 2, 26.00, NULL, '2026-04-27 21:54:36', '2026-04-27 21:54:36'),
	(190, 81, 13, 'Pollo a la Brasa (entero)', 60.00, 1, 60.00, NULL, '2026-04-27 21:54:36', '2026-04-27 21:54:36'),
	(191, 81, 1, 'Hamburguesa Clásica', 20.00, 1, 20.00, 'Sin cebolla', '2026-04-27 21:54:36', '2026-04-27 21:54:36'),
	(192, 82, 2, 'Hamburguesa Especial', 27.00, 1, 27.00, NULL, '2026-04-27 22:03:46', '2026-04-27 22:03:46'),
	(193, 83, 17, 'Torta de Chocolate', 13.00, 2, 26.00, NULL, '2026-04-28 21:47:56', '2026-04-28 21:47:56'),
	(194, 83, 4, 'Papas Fritas Grandes', 11.00, 2, 22.00, NULL, '2026-04-28 21:47:56', '2026-04-28 21:47:56'),
	(195, 83, 4, 'Papas Fritas Grandes', 11.00, 3, 33.00, NULL, '2026-04-28 21:47:56', '2026-04-28 21:47:56'),
	(196, 84, 4, 'Papas Fritas Grandes', 11.00, 3, 33.00, NULL, '2026-04-28 18:51:02', '2026-04-28 18:51:02'),
	(197, 84, 15, 'Menú Especial', 24.00, 2, 48.00, NULL, '2026-04-28 18:51:02', '2026-04-28 18:51:02'),
	(198, 84, 16, 'Helado 2 bolas', 9.00, 2, 18.00, NULL, '2026-04-28 18:51:02', '2026-04-28 18:51:02'),
	(199, 85, 12, 'Pollo a la Brasa (1/2)', 34.00, 2, 68.00, NULL, '2026-04-29 00:54:16', '2026-04-29 00:54:16'),
	(200, 85, 3, 'Hot Dog Completo', 13.00, 2, 26.00, NULL, '2026-04-29 00:54:16', '2026-04-29 00:54:16'),
	(201, 85, 7, 'Agua Mineral 625ml', 3.50, 1, 3.50, NULL, '2026-04-29 00:54:16', '2026-04-29 00:54:16'),
	(202, 86, 15, 'Menú Especial', 24.00, 1, 24.00, NULL, '2026-04-30 00:00:48', '2026-04-30 00:00:48'),
	(203, 86, 7, 'Agua Mineral 625ml', 3.50, 3, 10.50, NULL, '2026-04-30 00:00:48', '2026-04-30 00:00:48'),
	(204, 86, 5, 'Gaseosa 1.5L', 8.00, 2, 16.00, NULL, '2026-04-30 00:00:48', '2026-04-30 00:00:48'),
	(205, 87, 4, 'Papas Fritas Grandes', 11.00, 2, 22.00, NULL, '2026-04-30 16:16:48', '2026-04-30 16:16:48'),
	(206, 88, 17, 'Torta de Chocolate', 13.00, 3, 39.00, 'Sin cebolla', '2026-04-30 20:35:57', '2026-04-30 20:35:57'),
	(207, 88, 4, 'Papas Fritas Grandes', 11.00, 2, 22.00, NULL, '2026-04-30 20:35:57', '2026-04-30 20:35:57'),
	(208, 88, 2, 'Hamburguesa Especial', 27.00, 3, 81.00, NULL, '2026-04-30 20:35:57', '2026-04-30 20:35:57'),
	(209, 88, 16, 'Helado 2 bolas', 9.00, 1, 9.00, NULL, '2026-04-30 20:35:57', '2026-04-30 20:35:57'),
	(210, 89, 11, 'Pollo a la Brasa (1/4)', 20.00, 2, 40.00, 'Sin cebolla', '2026-05-01 18:52:09', '2026-05-01 18:52:09'),
	(211, 90, 1, 'Hamburguesa Clásica', 20.00, 3, 60.00, NULL, '2026-05-03 02:41:51', '2026-05-03 02:41:51'),
	(212, 90, 5, 'Gaseosa 1.5L', 8.00, 3, 24.00, NULL, '2026-05-03 02:41:51', '2026-05-03 02:41:51'),
	(213, 91, 5, 'Gaseosa 1.5L', 8.00, 3, 24.00, 'Sin cebolla', '2026-05-02 21:50:57', '2026-05-02 21:50:57'),
	(214, 91, 12, 'Pollo a la Brasa (1/2)', 34.00, 2, 68.00, NULL, '2026-05-02 21:50:57', '2026-05-02 21:50:57'),
	(215, 92, 13, 'Pollo a la Brasa (entero)', 60.00, 2, 120.00, NULL, '2026-05-02 18:24:49', '2026-05-02 18:24:49'),
	(216, 92, 11, 'Pollo a la Brasa (1/4)', 20.00, 2, 40.00, NULL, '2026-05-02 18:24:49', '2026-05-02 18:24:49'),
	(217, 92, 11, 'Pollo a la Brasa (1/4)', 20.00, 2, 40.00, NULL, '2026-05-02 18:24:49', '2026-05-02 18:24:49'),
	(218, 92, 1, 'Hamburguesa Clásica', 20.00, 1, 20.00, NULL, '2026-05-02 18:24:49', '2026-05-02 18:24:49');

-- Volcando estructura para tabla delivery_crm.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.permissions: ~29 rows (aproximadamente)
DELETE FROM `permissions`;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'ver dashboard', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(2, 'ver clientes', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(3, 'crear clientes', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(4, 'editar clientes', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(5, 'eliminar clientes', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(6, 'ver productos', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(7, 'crear productos', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(8, 'editar productos', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(9, 'eliminar productos', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(10, 'ver pedidos', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(11, 'crear pedidos', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(12, 'editar pedidos', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(13, 'cancelar pedidos', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(14, 'ver repartidores', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(15, 'crear repartidores', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(16, 'editar repartidores', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(17, 'eliminar repartidores', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(18, 'ver entregas', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(19, 'asignar entregas', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(20, 'actualizar entregas', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(21, 'ver pagos', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(22, 'registrar pagos', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(23, 'ver reportes', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(24, 'ver usuarios', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(25, 'crear usuarios', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(26, 'editar usuarios', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(27, 'eliminar usuarios', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(28, 'ver configuracion', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(29, 'editar configuracion', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31');

-- Volcando estructura para tabla delivery_crm.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.personal_access_tokens: ~0 rows (aproximadamente)
DELETE FROM `personal_access_tokens`;

-- Volcando estructura para tabla delivery_crm.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `categoria_id` bigint unsigned NOT NULL,
  `codigo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) NOT NULL,
  `precio_delivery` decimal(10,2) DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidad` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidad',
  `stock` int NOT NULL DEFAULT '0',
  `stock_minimo` int unsigned NOT NULL DEFAULT '5',
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_codigo_unique` (`codigo`),
  KEY `productos_categoria_id_foreign` (`categoria_id`),
  CONSTRAINT `productos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.productos: ~17 rows (aproximadamente)
DELETE FROM `productos`;
INSERT INTO `productos` (`id`, `categoria_id`, `codigo`, `nombre`, `descripcion`, `precio`, `precio_delivery`, `imagen`, `unidad`, `stock`, `stock_minimo`, `disponible`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'CF001', 'Hamburguesa Clásica', NULL, 18.00, 20.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(2, 1, 'CF002', 'Hamburguesa Especial', NULL, 25.00, 27.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(3, 1, 'CF003', 'Hot Dog Completo', NULL, 12.00, 13.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(4, 1, 'CF004', 'Papas Fritas Grandes', NULL, 10.00, 11.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(5, 2, 'BEB001', 'Gaseosa 1.5L', NULL, 7.00, 8.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(6, 2, 'BEB002', 'Jugo Natural 500ml', NULL, 8.00, 9.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(7, 2, 'BEB003', 'Agua Mineral 625ml', NULL, 3.00, 3.50, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(8, 2, 'BEB004', 'Chicha Morada 1L', NULL, 6.00, 7.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(9, 5, 'PIZ001', 'Pizza Margarita (personal)', NULL, 20.00, 22.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(10, 5, 'PIZ002', 'Pizza Especial (familiar)', NULL, 45.00, 47.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(11, 6, 'POL001', 'Pollo a la Brasa (1/4)', NULL, 18.00, 20.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(12, 6, 'POL002', 'Pollo a la Brasa (1/2)', NULL, 32.00, 34.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(13, 6, 'POL003', 'Pollo a la Brasa (entero)', NULL, 58.00, 60.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(14, 7, 'MEN001', 'Menú Ejecutivo', NULL, 15.00, 17.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(15, 7, 'MEN002', 'Menú Especial', NULL, 22.00, 24.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(16, 3, 'POS001', 'Helado 2 bolas', NULL, 8.00, 9.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(17, 3, 'POS002', 'Torta de Chocolate', NULL, 12.00, 13.00, NULL, 'unidad', 100, 5, 1, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL);

-- Volcando estructura para tabla delivery_crm.repartidores
CREATE TABLE IF NOT EXISTS `repartidores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono_alt` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_vehiculo` enum('moto','bicicleta','auto','pie') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'moto',
  `placa` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zona_asignada` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('disponible','ocupado','inactivo','descanso') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponible',
  `lat_actual` decimal(10,7) DEFAULT NULL,
  `lng_actual` decimal(10,7) DEFAULT NULL,
  `ultima_ubicacion_at` timestamp NULL DEFAULT NULL,
  `calificacion` decimal(3,2) NOT NULL DEFAULT '5.00',
  `total_entregas` int NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `repartidores_dni_unique` (`dni`),
  KEY `repartidores_user_id_foreign` (`user_id`),
  CONSTRAINT `repartidores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.repartidores: ~10 rows (aproximadamente)
DELETE FROM `repartidores`;
INSERT INTO `repartidores` (`id`, `user_id`, `nombre`, `apellido`, `dni`, `telefono`, `telefono_alt`, `email`, `foto`, `tipo_vehiculo`, `placa`, `zona_asignada`, `estado`, `lat_actual`, `lng_actual`, `ultima_ubicacion_at`, `calificacion`, `total_entregas`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 4, 'Carlos', 'Quispe Mamani', '12345678', '999111001', NULL, NULL, NULL, 'moto', 'ABC-101', 'Miraflores, San Isidro', 'disponible', NULL, NULL, NULL, 4.85, 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(2, NULL, 'Jorge', 'Flores Inca', '23456789', '999111002', NULL, NULL, NULL, 'moto', 'ABC-102', 'Surco, La Molina', 'disponible', NULL, NULL, NULL, 4.70, 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(3, NULL, 'Miguel', 'Ramos Torres', '34567890', '999111003', NULL, NULL, NULL, 'bicicleta', NULL, 'Barranco, Chorrillos', 'disponible', NULL, NULL, NULL, 4.50, 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(4, NULL, 'Luis', 'Paucar Huanca', '45678901', '999111004', NULL, NULL, NULL, 'auto', 'XYZ-200', 'San Borja, Surquillo', 'descanso', NULL, NULL, NULL, 4.90, 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(5, NULL, 'Andrés', 'Cárdenas Ruiz', '56789012', '999111005', NULL, NULL, NULL, 'moto', 'ABC-103', 'Los Olivos, SMP', 'inactivo', NULL, NULL, NULL, 4.20, 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(6, NULL, 'Daniel', 'Huamán Pérez', '67890123', '999111006', NULL, NULL, NULL, 'moto', 'ABC-104', 'San Miguel, Magdalena', 'disponible', NULL, NULL, NULL, 4.65, 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(7, NULL, 'Roberto', 'Vega Mendoza', '78901234', '999111007', NULL, NULL, NULL, 'moto', 'ABC-105', 'Cercado, Breña', 'disponible', NULL, NULL, NULL, 4.40, 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(8, NULL, 'Pablo', 'Núñez Salinas', '89012345', '999111008', NULL, NULL, NULL, 'bicicleta', NULL, 'Pueblo Libre, Jesús María', 'ocupado', NULL, NULL, NULL, 4.55, 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(9, NULL, 'José', 'Aliaga Vilca', '90123456', '999111009', NULL, NULL, NULL, 'moto', 'ABC-106', 'Independencia, Comas', 'disponible', NULL, NULL, NULL, 4.75, 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL),
	(10, NULL, 'Walter', 'Tapia Choque', '01234567', '999111010', NULL, NULL, NULL, 'moto', 'ABC-107', 'Ate, Santa Anita', 'disponible', NULL, NULL, NULL, 4.30, 0, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33', NULL);

-- Volcando estructura para tabla delivery_crm.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.roles: ~4 rows (aproximadamente)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'super-admin', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(2, 'admin', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(3, 'operador', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31'),
	(4, 'repartidor', 'web', '2026-05-02 09:33:31', '2026-05-02 09:33:31');

-- Volcando estructura para tabla delivery_crm.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.role_has_permissions: ~66 rows (aproximadamente)
DELETE FROM `role_has_permissions`;
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(2, 1),
	(3, 1),
	(4, 1),
	(5, 1),
	(6, 1),
	(7, 1),
	(8, 1),
	(9, 1),
	(10, 1),
	(11, 1),
	(12, 1),
	(13, 1),
	(14, 1),
	(15, 1),
	(16, 1),
	(17, 1),
	(18, 1),
	(19, 1),
	(20, 1),
	(21, 1),
	(22, 1),
	(23, 1),
	(24, 1),
	(25, 1),
	(26, 1),
	(27, 1),
	(28, 1),
	(29, 1),
	(1, 2),
	(2, 2),
	(3, 2),
	(4, 2),
	(5, 2),
	(6, 2),
	(7, 2),
	(8, 2),
	(10, 2),
	(11, 2),
	(12, 2),
	(13, 2),
	(14, 2),
	(15, 2),
	(16, 2),
	(18, 2),
	(19, 2),
	(20, 2),
	(21, 2),
	(22, 2),
	(23, 2),
	(24, 2),
	(1, 3),
	(2, 3),
	(3, 3),
	(4, 3),
	(6, 3),
	(10, 3),
	(11, 3),
	(12, 3),
	(14, 3),
	(18, 3),
	(19, 3),
	(21, 3),
	(22, 3),
	(18, 4),
	(20, 4);

-- Volcando estructura para tabla delivery_crm.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.sessions: ~0 rows (aproximadamente)
DELETE FROM `sessions`;

-- Volcando estructura para tabla delivery_crm.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.users: ~4 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `telefono`, `avatar`, `activo`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Super Administrador', 'admin@crm.com', '999000001', NULL, 1, '2026-05-02 09:33:32', '$2y$12$1aXHEktTs1jti3iN1x2tFOU7Wn/psvN8pQUXsk8OiRojN2Lha1ndO', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(2, 'Gerente General', 'gerente@crm.com', '999000002', NULL, 1, '2026-05-02 09:33:32', '$2y$12$jHoRG88L.xbPk4Nbp5a3Tex0BQ2Mg.eWp4HSlFyblwxo.JoRsDP2W', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(3, 'María Operadora', 'operador@crm.com', '999000003', NULL, 1, '2026-05-02 09:33:32', '$2y$12$VUqiPG5dtJGxHycbv7QJP.Y7JKJS9UbZNXMsaOqXJz29L6ThPZSnm', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32'),
	(4, 'Juan Repartidor', 'repartidor@crm.com', '999000004', NULL, 1, '2026-05-02 09:33:32', '$2y$12$EWiMyR47WJtEJoFlM7B.Rum4htZxVECcBTewgrk1Az4iivlsQAL1O', NULL, '2026-05-02 09:33:32', '2026-05-02 09:33:32');

-- Volcando estructura para tabla delivery_crm.zonas
CREATE TABLE IF NOT EXISTS `zonas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `distrito` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costo_delivery` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tiempo_estimado_min` smallint unsigned NOT NULL DEFAULT '30',
  `monto_minimo_pedido` decimal(8,2) NOT NULL DEFAULT '0.00',
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `zonas_activo_distrito_index` (`activo`,`distrito`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla delivery_crm.zonas: ~10 rows (aproximadamente)
DELETE FROM `zonas`;
INSERT INTO `zonas` (`id`, `nombre`, `distrito`, `costo_delivery`, `tiempo_estimado_min`, `monto_minimo_pedido`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Centro Histórico', 'Cercado de Lima', 6.00, 30, 15.00, NULL, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(2, 'Miraflores', 'Miraflores', 8.00, 35, 20.00, NULL, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(3, 'San Isidro', 'San Isidro', 8.00, 35, 20.00, NULL, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(4, 'Surco / La Molina', 'Surco', 10.00, 45, 25.00, NULL, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(5, 'San Borja', 'San Borja', 9.00, 40, 20.00, NULL, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(6, 'Barranco', 'Barranco', 9.00, 40, 20.00, NULL, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(7, 'Pueblo Libre', 'Pueblo Libre', 7.00, 35, 15.00, NULL, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(8, 'San Miguel', 'San Miguel', 7.00, 35, 15.00, NULL, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(9, 'Los Olivos / SMP', 'Los Olivos', 12.00, 55, 25.00, NULL, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33'),
	(10, 'Ate / Santa Anita', 'Ate', 13.00, 60, 30.00, NULL, 1, '2026-05-02 09:33:33', '2026-05-02 09:33:33');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
