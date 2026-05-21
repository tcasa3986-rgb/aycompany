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


-- Volcando estructura de base de datos para farmacia_erp
CREATE DATABASE IF NOT EXISTS `farmacia_erp` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `farmacia_erp`;

-- Volcando estructura para tabla farmacia_erp.audit_logs
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auditable_id` bigint unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `url` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.audit_logs: ~0 rows (aproximadamente)
DELETE FROM `audit_logs`;
INSERT INTO `audit_logs` (`id`, `user_id`, `event`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `url`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
	(1, 1, 'created', 'App\\Models\\Venta', 112, NULL, '{"id": 112, "fecha": "2026-04-30 02:16:02", "total": 0.94, "cambio": 0, "codigo": "V-20260430021602", "estado": "emitida", "caja_id": 1, "user_id": 1, "impuesto": 0.14, "subtotal": 0.8, "descuento": 0, "cliente_id": null, "created_at": "2026-04-30 02:16:02", "forma_pago": "efectivo", "updated_at": "2026-04-30 02:16:02", "sucursal_id": 1, "observaciones": null, "pago_recibido": 0, "puntos_canjeados": 0, "tipo_comprobante": "boleta"}', 'http://127.0.0.1:8007/pos', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 07:16:02', '2026-04-30 07:16:02');

-- Volcando estructura para tabla farmacia_erp.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.cache: ~3 rows (aproximadamente)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('erp_farmacia_cache_5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1777547470),
	('erp_farmacia_cache_5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1777547470;', 1777547470),
	('erp_farmacia_cache_spatie.permission.cache', 'a:3:{s:5:"alias";a:4:{s:1:"a";s:2:"id";s:1:"b";s:4:"name";s:1:"c";s:10:"guard_name";s:1:"r";s:5:"roles";}s:11:"permissions";a:21:{i:0;a:4:{s:1:"a";i:1;s:1:"b";s:14:"dashboard.view";s:1:"c";s:3:"web";s:1:"r";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:1;a:4:{s:1:"a";i:2;s:1:"b";s:15:"inventario.view";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:2;a:4:{s:1:"a";i:3;s:1:"b";s:17:"inventario.manage";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:3;a:4:{s:1:"a";i:4;s:1:"b";s:17:"categorias.manage";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:4;}}i:4;a:4:{s:1:"a";i:5;s:1:"b";s:18:"proveedores.manage";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:4;}}i:5;a:4:{s:1:"a";i:6;s:1:"b";s:12:"lotes.manage";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:6;a:4:{s:1:"a";i:7;s:1:"b";s:13:"clientes.view";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:7;a:4:{s:1:"a";i:8;s:1:"b";s:15:"clientes.manage";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:"a";i:9;s:1:"b";s:7:"pos.use";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:9;a:4:{s:1:"a";i:10;s:1:"b";s:8:"caja.use";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:10;a:4:{s:1:"a";i:11;s:1:"b";s:11:"caja.cerrar";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:5;}}i:11;a:4:{s:1:"a";i:12;s:1:"b";s:12:"compras.view";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:4;}}i:12;a:4:{s:1:"a";i:13;s:1:"b";s:14:"compras.manage";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:4;}}i:13;a:4:{s:1:"a";i:14;s:1:"b";s:12:"recetas.view";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:14;a:4:{s:1:"a";i:15;s:1:"b";s:14:"recetas.manage";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:"a";i:16;s:1:"b";s:13:"reportes.view";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:5;}}i:16;a:4:{s:1:"a";i:17;s:1:"b";s:15:"reportes.export";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:5;}}i:17;a:4:{s:1:"a";i:18;s:1:"b";s:15:"usuarios.manage";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:18;a:3:{s:1:"a";i:19;s:1:"b";s:13:"config.manage";s:1:"c";s:3:"web";}i:19;a:4:{s:1:"a";i:20;s:1:"b";s:15:"settings.manage";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:20;a:4:{s:1:"a";i:21;s:1:"b";s:10:"audit.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}}s:5:"roles";a:5:{i:0;a:3:{s:1:"a";i:1;s:1:"b";s:13:"Administrador";s:1:"c";s:3:"web";}i:1;a:3:{s:1:"a";i:2;s:1:"b";s:12:"Farmaceutico";s:1:"c";s:3:"web";}i:2;a:3:{s:1:"a";i:3;s:1:"b";s:6:"Cajero";s:1:"c";s:3:"web";}i:3;a:3:{s:1:"a";i:4;s:1:"b";s:10:"Almacenero";s:1:"c";s:3:"web";}i:4;a:3:{s:1:"a";i:5;s:1:"b";s:8:"Contador";s:1:"c";s:3:"web";}}}', 1777616905);

-- Volcando estructura para tabla farmacia_erp.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Volcando estructura para tabla farmacia_erp.cajas
CREATE TABLE IF NOT EXISTS `cajas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `sucursal_id` bigint unsigned DEFAULT NULL,
  `monto_apertura` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_cierre` decimal(12,2) DEFAULT NULL,
  `total_ventas` decimal(12,2) NOT NULL DEFAULT '0.00',
  `apertura` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cierre` timestamp NULL DEFAULT NULL,
  `estado` enum('abierta','cerrada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'abierta',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cajas_user_id_foreign` (`user_id`),
  KEY `cajas_sucursal_id_foreign` (`sucursal_id`),
  CONSTRAINT `cajas_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `cajas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.cajas: ~1 rows (aproximadamente)
DELETE FROM `cajas`;
INSERT INTO `cajas` (`id`, `user_id`, `sucursal_id`, `monto_apertura`, `monto_cierre`, `total_ventas`, `apertura`, `cierre`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 100.00, NULL, 0.00, '2026-04-30 02:31:28', NULL, 'abierta', NULL, '2026-04-30 02:31:28', '2026-04-30 02:31:28');

-- Volcando estructura para tabla farmacia_erp.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorias_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.categorias: ~8 rows (aproximadamente)
DELETE FROM `categorias`;
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Analgésicos', 'Para alivio del dolor', 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(2, 'Antibióticos', 'Tratamiento de infecciones', 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(3, 'Antialérgicos', 'Tratamiento de alergias', 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(4, 'Antiinflamatorios', 'Reducen la inflamación', 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(5, 'Vitaminas', 'Suplementos vitamínicos', 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(6, 'Cosméticos', 'Cuidado personal y belleza', 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(7, 'Insumos médicos', 'Material e insumos', 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(8, 'Cuidado infantil', 'Productos para bebés', 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53');

-- Volcando estructura para tabla farmacia_erp.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `documento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombres` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('M','F','O') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alergias` text COLLATE utf8mb4_unicode_ci,
  `enfermedades_cronicas` text COLLATE utf8mb4_unicode_ci,
  `puntos_fidelidad` int NOT NULL DEFAULT '0',
  `limite_credito` decimal(12,2) NOT NULL DEFAULT '0.00',
  `saldo_deudor` decimal(12,2) NOT NULL DEFAULT '0.00',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_documento_unique` (`documento`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.clientes: ~35 rows (aproximadamente)
DELETE FROM `clientes`;
INSERT INTO `clientes` (`id`, `documento`, `nombres`, `apellidos`, `telefono`, `email`, `direccion`, `fecha_nacimiento`, `genero`, `alergias`, `enfermedades_cronicas`, `puntos_fidelidad`, `limite_credito`, `saldo_deudor`, `activo`, `created_at`, `updated_at`) VALUES
	(1, '00000000', 'Cliente', 'Genérico', '000000000', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(2, '12345678', 'María', 'García López', '987654321', NULL, NULL, NULL, NULL, 'Penicilina', NULL, 0, 0.00, 0.00, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(3, '23456789', 'Carlos', 'Mendoza Ruiz', '976543210', NULL, NULL, NULL, NULL, NULL, 'Hipertensión', 0, 0.00, 0.00, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(4, '34567890', 'Lucía', 'Pérez Silva', '965432109', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(5, '45678901', 'Jorge', 'Vargas Soto', '954321098', NULL, NULL, NULL, NULL, NULL, 'Diabetes tipo 2', 0, 0.00, 0.00, 1, '2026-04-30 01:52:54', '2026-04-30 01:52:54'),
	(6, '88888806', 'Cliente Demo 6', 'Prueba', '999888006', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(7, '88888807', 'Cliente Demo 7', 'Prueba', '999888007', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(8, '88888808', 'Cliente Demo 8', 'Prueba', '999888008', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(9, '88888809', 'Cliente Demo 9', 'Prueba', '999888009', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(10, '88888810', 'Cliente Demo 10', 'Prueba', '999888010', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(11, '88888811', 'Cliente Demo 11', 'Prueba', '999888011', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(12, '88888812', 'Cliente Demo 12', 'Prueba', '999888012', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(13, '88888813', 'Cliente Demo 13', 'Prueba', '999888013', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(14, '88888814', 'Cliente Demo 14', 'Prueba', '999888014', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(15, '88888815', 'Cliente Demo 15', 'Prueba', '999888015', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(16, '88880101', 'Cliente Nuevo 101', 'Demo', '999888101', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(17, '88880102', 'Cliente Nuevo 102', 'Demo', '999888102', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(18, '88880103', 'Cliente Nuevo 103', 'Demo', '999888103', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(19, '88880104', 'Cliente Nuevo 104', 'Demo', '999888104', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(20, '88880105', 'Cliente Nuevo 105', 'Demo', '999888105', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(21, '88880106', 'Cliente Nuevo 106', 'Demo', '999888106', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(22, '88880107', 'Cliente Nuevo 107', 'Demo', '999888107', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(23, '88880108', 'Cliente Nuevo 108', 'Demo', '999888108', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(24, '88880109', 'Cliente Nuevo 109', 'Demo', '999888109', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(25, '88880110', 'Cliente Nuevo 110', 'Demo', '999888110', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(26, '88880111', 'Cliente Nuevo 111', 'Demo', '999888111', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(27, '88880112', 'Cliente Nuevo 112', 'Demo', '999888112', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(28, '88880113', 'Cliente Nuevo 113', 'Demo', '999888113', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(29, '88880114', 'Cliente Nuevo 114', 'Demo', '999888114', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(30, '88880115', 'Cliente Nuevo 115', 'Demo', '999888115', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(31, '88880116', 'Cliente Nuevo 116', 'Demo', '999888116', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(32, '88880117', 'Cliente Nuevo 117', 'Demo', '999888117', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(33, '88880118', 'Cliente Nuevo 118', 'Demo', '999888118', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(34, '88880119', 'Cliente Nuevo 119', 'Demo', '999888119', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(35, '88880120', 'Cliente Nuevo 120', 'Demo', '999888120', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45');

-- Volcando estructura para tabla farmacia_erp.compras
CREATE TABLE IF NOT EXISTS `compras` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proveedor_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `sucursal_id` bigint unsigned DEFAULT NULL,
  `estado` enum('pendiente','parcial','recibida','anulada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `impuesto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fecha` date NOT NULL,
  `fecha_recepcion` date DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compras_codigo_unique` (`codigo`),
  KEY `compras_proveedor_id_foreign` (`proveedor_id`),
  KEY `compras_user_id_foreign` (`user_id`),
  KEY `compras_sucursal_id_foreign` (`sucursal_id`),
  CONSTRAINT `compras_proveedor_id_foreign` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compras_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `compras_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.compras: ~35 rows (aproximadamente)
DELETE FROM `compras`;
INSERT INTO `compras` (`id`, `codigo`, `proveedor_id`, `user_id`, `sucursal_id`, `estado`, `subtotal`, `impuesto`, `total`, `fecha`, `fecha_recepcion`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 'C-DEMO-1-958', 15, 1, 1, 'recibida', 12.37, 2.23, 14.60, '2026-04-01', '2026-04-10', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(2, 'C-DEMO-2-318', 2, 1, 1, 'recibida', 13.05, 2.35, 15.40, '2026-04-15', '2026-04-13', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(3, 'C-DEMO-3-670', 10, 1, 1, 'recibida', 1286.86, 231.64, 1518.50, '2026-04-16', '2026-03-31', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(4, 'C-DEMO-4-372', 12, 1, 1, 'recibida', 724.15, 130.35, 854.50, '2026-04-04', '2026-04-21', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(5, 'C-DEMO-5-423', 15, 1, 1, 'recibida', 486.69, 87.61, 574.30, '2026-04-26', '2026-04-28', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(6, 'C-DEMO-6-445', 7, 1, 1, 'recibida', 7.46, 1.34, 8.80, '2026-04-20', '2026-04-26', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(7, 'C-DEMO-7-984', 15, 1, 1, 'recibida', 369.24, 66.46, 435.70, '2026-04-13', '2026-04-22', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(8, 'C-DEMO-8-111', 7, 1, 1, 'recibida', 15.64, 2.81, 18.45, '2026-04-17', '2026-04-09', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(9, 'C-DEMO-9-969', 6, 1, 1, 'recibida', 5.08, 0.92, 6.00, '2026-03-31', '2026-04-24', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(10, 'C-DEMO-10-796', 14, 1, 1, 'recibida', 4.83, 0.87, 5.70, '2026-04-18', '2026-04-06', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(11, 'C-DEMO-11-219', 4, 1, 1, 'recibida', 958.90, 172.60, 1131.50, '2026-04-23', '2026-04-13', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(12, 'C-DEMO-12-157', 12, 1, 1, 'recibida', 11.53, 2.07, 13.60, '2026-04-18', '2026-04-01', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(13, 'C-DEMO-13-893', 14, 1, 1, 'recibida', 293.90, 52.90, 346.80, '2026-04-12', '2026-04-22', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(14, 'C-DEMO-14-927', 11, 1, 1, 'recibida', 525.17, 94.53, 619.70, '2026-04-09', '2026-04-25', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(15, 'C-DEMO-15-809', 9, 1, 1, 'recibida', 372.03, 66.97, 439.00, '2026-04-10', '2026-04-18', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(16, 'C-DEMO5-1-399', 16, 1, 1, 'recibida', 23.35, 4.20, 27.55, '2026-03-11', '2026-03-13', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(17, 'C-DEMO5-2-414', 16, 1, 1, 'recibida', 878.81, 158.19, 1037.00, '2026-03-04', '2026-03-05', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(18, 'C-DEMO5-3-236', 24, 1, 1, 'recibida', 8.31, 1.49, 9.80, '2026-03-16', '2026-03-18', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(19, 'C-DEMO5-4-817', 28, 1, 1, 'recibida', 362.88, 65.32, 428.20, '2025-12-23', '2025-12-23', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(20, 'C-DEMO5-5-161', 28, 1, 1, 'recibida', 5.85, 1.05, 6.90, '2026-03-16', '2026-03-16', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(21, 'C-DEMO5-6-864', 6, 1, 1, 'recibida', 766.44, 137.96, 904.40, '2026-03-15', '2026-03-18', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(22, 'C-DEMO5-7-805', 17, 1, 1, 'recibida', 11.53, 2.07, 13.60, '2025-12-08', '2025-12-10', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(23, 'C-DEMO5-8-615', 7, 1, 1, 'recibida', 473.56, 85.24, 558.80, '2026-03-22', '2026-03-24', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(24, 'C-DEMO5-9-817', 35, 1, 1, 'recibida', 4.92, 0.88, 5.80, '2026-03-19', '2026-03-21', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(25, 'C-DEMO5-10-835', 15, 1, 1, 'recibida', 11.69, 2.11, 13.80, '2026-03-04', '2026-03-06', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(26, 'C-DEMO5-11-411', 32, 1, 1, 'recibida', 3.05, 0.55, 3.60, '2025-12-21', '2025-12-23', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(27, 'C-DEMO5-12-683', 34, 1, 1, 'recibida', 343.90, 61.90, 405.80, '2026-03-10', '2026-03-13', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(28, 'C-DEMO5-13-474', 21, 1, 1, 'recibida', 17.88, 3.22, 21.10, '2026-04-04', '2026-04-05', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(29, 'C-DEMO5-14-305', 24, 1, 1, 'recibida', 15.93, 2.87, 18.80, '2026-01-05', '2026-01-08', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(30, 'C-DEMO5-15-222', 2, 1, 1, 'recibida', 11.40, 2.05, 13.45, '2025-12-21', '2025-12-21', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(31, 'C-DEMO5-16-524', 29, 1, 1, 'recibida', 2.63, 0.47, 3.10, '2026-03-20', '2026-03-22', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(32, 'C-DEMO5-17-735', 11, 1, 1, 'recibida', 9.41, 1.69, 11.10, '2026-03-09', '2026-03-12', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(33, 'C-DEMO5-18-689', 17, 1, 1, 'recibida', 14.15, 2.55, 16.70, '2026-01-19', '2026-01-20', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(34, 'C-DEMO5-19-824', 13, 1, 1, 'recibida', 13.31, 2.39, 15.70, '2026-04-22', '2026-04-24', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(35, 'C-DEMO5-20-406', 25, 1, 1, 'recibida', 8.56, 1.54, 10.10, '2026-03-22', '2026-03-24', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45');

-- Volcando estructura para tabla farmacia_erp.detalle_compra
CREATE TABLE IF NOT EXISTS `detalle_compra` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `compra_id` bigint unsigned NOT NULL,
  `producto_id` bigint unsigned NOT NULL,
  `numero_lote` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detalle_compra_compra_id_foreign` (`compra_id`),
  KEY `detalle_compra_producto_id_foreign` (`producto_id`),
  CONSTRAINT `detalle_compra_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detalle_compra_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.detalle_compra: ~74 rows (aproximadamente)
DELETE FROM `detalle_compra`;
INSERT INTO `detalle_compra` (`id`, `compra_id`, `producto_id`, `numero_lote`, `fecha_vencimiento`, `cantidad`, `precio_unitario`, `subtotal`, `created_at`, `updated_at`) VALUES
	(1, 1, 3, 'LD-4910', '2026-10-11', 12, 0.30, 3.60, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(2, 1, 1, 'LD-3189', '2026-10-23', 20, 0.10, 2.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(3, 1, 6, 'LD-4813', '2027-04-29', 18, 0.50, 9.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(4, 2, 5, 'LD-8466', '2027-04-16', 10, 0.40, 4.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(5, 2, 2, 'LD-1030', '2027-04-25', 16, 0.15, 2.40, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(6, 2, 7, 'LD-4727', '2026-10-12', 45, 0.20, 9.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(7, 3, 6, 'LD-4824', '2026-12-07', 41, 0.50, 20.50, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(8, 3, 8, 'LD-1050', '2027-03-11', 27, 18.00, 486.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(9, 3, 10, 'LD-3403', '2026-06-18', 46, 22.00, 1012.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(10, 4, 10, 'LD-1116', '2026-12-04', 38, 22.00, 836.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(11, 4, 6, 'LD-4926', '2026-05-31', 37, 0.50, 18.50, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(12, 5, 10, 'LD-8378', '2027-03-16', 26, 22.00, 572.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(13, 5, 1, 'LD-1693', '2026-08-02', 23, 0.10, 2.30, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(14, 6, 4, 'LD-2886', '2027-01-05', 44, 0.20, 8.80, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(15, 7, 8, 'LD-8317', '2027-03-09', 24, 18.00, 432.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(16, 7, 1, 'LD-6511', '2027-01-03', 37, 0.10, 3.70, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(17, 8, 2, 'LD-2265', '2027-02-11', 29, 0.15, 4.35, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(18, 8, 9, 'LD-9956', '2026-06-25', 49, 0.15, 7.35, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(19, 8, 9, 'LD-1561', '2026-12-01', 45, 0.15, 6.75, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(20, 9, 6, 'LD-3891', '2026-07-17', 12, 0.50, 6.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(21, 10, 3, 'LD-6303', '2027-04-12', 19, 0.30, 5.70, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(22, 11, 10, 'LD-5776', '2026-09-04', 17, 22.00, 374.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(23, 11, 8, 'LD-9356', '2026-10-20', 42, 18.00, 756.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(24, 11, 2, 'LD-4818', '2026-06-15', 10, 0.15, 1.50, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(25, 12, 7, 'LD-5995', '2027-03-08', 35, 0.20, 7.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(26, 12, 9, 'LD-7626', '2027-04-17', 44, 0.15, 6.60, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(27, 13, 8, 'LD-2586', '2027-02-08', 19, 18.00, 342.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(28, 13, 3, 'LD-5331', '2026-09-19', 16, 0.30, 4.80, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(29, 14, 7, 'LD-8746', '2026-08-07', 25, 0.20, 5.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(30, 14, 2, 'LD-9427', '2026-08-04', 18, 0.15, 2.70, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(31, 14, 8, 'LD-4393', '2027-01-06', 34, 18.00, 612.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(32, 15, 9, 'LD-8458', '2026-10-04', 34, 0.15, 5.10, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(33, 15, 1, 'LD-2005', '2026-12-31', 19, 0.10, 1.90, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(34, 15, 8, 'LD-1840', '2026-12-14', 24, 18.00, 432.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(35, 16, 9, 'LD5-1852', '2026-11-23', 21, 0.15, 3.15, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(36, 16, 6, 'LD5-7819', '2027-01-29', 44, 0.50, 22.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(37, 16, 4, 'LD5-1543', '2026-07-30', 12, 0.20, 2.40, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(38, 17, 10, 'LD5-3536', '2026-12-15', 14, 22.00, 308.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(39, 17, 3, 'LD5-9171', '2026-06-29', 30, 0.30, 9.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(40, 17, 8, 'LD5-6970', '2026-09-11', 40, 18.00, 720.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(41, 18, 7, 'LD5-1897', '2026-12-04', 49, 0.20, 9.80, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(42, 19, 3, 'LD5-2521', '2026-10-15', 34, 0.30, 10.20, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(43, 19, 10, 'LD5-6639', '2026-09-06', 19, 22.00, 418.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(44, 20, 9, 'LD5-9281', '2026-08-11', 46, 0.15, 6.90, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(45, 21, 5, 'LD5-4842', '2027-02-20', 11, 0.40, 4.40, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(46, 21, 5, 'LD5-9044', '2026-05-31', 45, 0.40, 18.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(47, 21, 8, 'LD5-4038', '2026-10-31', 49, 18.00, 882.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(48, 22, 1, 'LD5-3904', '2026-06-08', 28, 0.10, 2.80, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(49, 22, 5, 'LD5-5614', '2026-10-16', 27, 0.40, 10.80, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(50, 23, 4, 'LD5-6418', '2027-02-10', 26, 0.20, 5.20, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(51, 23, 9, 'LD5-1679', '2026-06-15', 24, 0.15, 3.60, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(52, 23, 10, 'LD5-9001', '2026-10-24', 25, 22.00, 550.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(53, 24, 7, 'LD5-3543', '2027-01-06', 29, 0.20, 5.80, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(54, 25, 3, 'LD5-4479', '2026-12-24', 46, 0.30, 13.80, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(55, 26, 1, 'LD5-8264', '2026-06-15', 36, 0.10, 3.60, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(56, 27, 7, 'LD5-4249', '2026-07-26', 49, 0.20, 9.80, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(57, 27, 10, 'LD5-6896', '2027-01-13', 18, 22.00, 396.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(58, 28, 9, 'LD5-5817', '2027-02-22', 10, 0.15, 1.50, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(59, 28, 5, 'LD5-4044', '2026-12-29', 24, 0.40, 9.60, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(60, 28, 7, 'LD5-4184', '2027-01-09', 50, 0.20, 10.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(61, 29, 4, 'LD5-1526', '2027-02-02', 50, 0.20, 10.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(62, 29, 5, 'LD5-6780', '2026-11-20', 22, 0.40, 8.80, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(63, 30, 4, 'LD5-5923', '2026-08-31', 25, 0.20, 5.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(64, 30, 1, 'LD5-5407', '2027-03-21', 32, 0.10, 3.20, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(65, 30, 9, 'LD5-1365', '2026-09-18', 35, 0.15, 5.25, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(66, 31, 1, 'LD5-5647', '2026-08-07', 31, 0.10, 3.10, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(67, 32, 4, 'LD5-2846', '2027-03-09', 36, 0.20, 7.20, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(68, 32, 3, 'LD5-8087', '2026-10-28', 13, 0.30, 3.90, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(69, 33, 3, 'LD5-7700', '2026-06-27', 46, 0.30, 13.80, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(70, 33, 1, 'LD5-9284', '2027-04-16', 29, 0.10, 2.90, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(71, 34, 3, 'LD5-7693', '2026-08-15', 49, 0.30, 14.70, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(72, 34, 1, 'LD5-1328', '2027-03-26', 10, 0.10, 1.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(73, 35, 1, 'LD5-5692', '2027-03-23', 14, 0.10, 1.40, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(74, 35, 3, 'LD5-1758', '2027-04-23', 29, 0.30, 8.70, '2026-04-30 02:47:45', '2026-04-30 02:47:45');

-- Volcando estructura para tabla farmacia_erp.detalle_receta
CREATE TABLE IF NOT EXISTS `detalle_receta` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `receta_id` bigint unsigned NOT NULL,
  `producto_id` bigint unsigned NOT NULL,
  `cantidad` int NOT NULL,
  `indicaciones` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detalle_receta_receta_id_foreign` (`receta_id`),
  KEY `detalle_receta_producto_id_foreign` (`producto_id`),
  CONSTRAINT `detalle_receta_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detalle_receta_receta_id_foreign` FOREIGN KEY (`receta_id`) REFERENCES `recetas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.detalle_receta: ~35 rows (aproximadamente)
DELETE FROM `detalle_receta`;
INSERT INTO `detalle_receta` (`id`, `receta_id`, `producto_id`, `cantidad`, `indicaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 8, 2, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(2, 2, 8, 2, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(3, 3, 9, 3, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(4, 4, 7, 3, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(5, 5, 6, 3, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(6, 6, 6, 1, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(7, 7, 2, 2, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(8, 8, 1, 2, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(9, 9, 2, 1, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(10, 10, 6, 3, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(11, 11, 2, 3, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(12, 12, 4, 2, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(13, 13, 4, 2, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(14, 14, 2, 2, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(15, 15, 7, 2, 'Tomar 1 cada 8 horas', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(16, 16, 5, 2, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(17, 17, 6, 2, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(18, 18, 2, 1, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(19, 19, 6, 2, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(20, 20, 6, 1, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(21, 21, 10, 2, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(22, 22, 5, 4, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(23, 23, 2, 3, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(24, 24, 4, 1, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(25, 25, 9, 1, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(26, 26, 3, 2, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(27, 27, 10, 2, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(28, 28, 2, 4, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(29, 29, 2, 4, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(30, 30, 2, 1, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(31, 31, 8, 4, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(32, 32, 2, 1, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(33, 33, 4, 1, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(34, 34, 6, 3, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(35, 35, 7, 2, 'Tomar según indicación médica', '2026-04-30 02:47:45', '2026-04-30 02:47:45');

-- Volcando estructura para tabla farmacia_erp.detalle_venta
CREATE TABLE IF NOT EXISTS `detalle_venta` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `venta_id` bigint unsigned NOT NULL,
  `producto_id` bigint unsigned NOT NULL,
  `lote_id` bigint unsigned DEFAULT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detalle_venta_venta_id_foreign` (`venta_id`),
  KEY `detalle_venta_producto_id_foreign` (`producto_id`),
  KEY `detalle_venta_lote_id_foreign` (`lote_id`),
  CONSTRAINT `detalle_venta_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `detalle_venta_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detalle_venta_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=214 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.detalle_venta: ~212 rows (aproximadamente)
DELETE FROM `detalle_venta`;
INSERT INTO `detalle_venta` (`id`, `venta_id`, `producto_id`, `lote_id`, `cantidad`, `precio_unitario`, `descuento`, `subtotal`, `created_at`, `updated_at`) VALUES
	(1, 1, 8, 8, 5, 32.00, 0.00, 160.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(2, 2, 7, 7, 4, 0.55, 0.00, 2.20, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(3, 2, 3, 3, 1, 0.80, 0.00, 0.80, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(4, 3, 6, 6, 3, 1.20, 0.00, 3.60, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(5, 3, 1, 1, 5, 0.30, 0.00, 1.50, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(6, 4, 9, 9, 1, 0.50, 0.00, 0.50, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(7, 4, 2, 2, 5, 0.45, 0.00, 2.25, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(8, 4, 6, 6, 2, 1.20, 0.00, 2.40, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(9, 5, 10, 10, 2, 35.00, 0.00, 70.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(10, 5, 10, 10, 3, 35.00, 0.00, 105.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(11, 6, 4, 4, 1, 0.60, 0.00, 0.60, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(12, 7, 2, 2, 2, 0.45, 0.00, 0.90, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(13, 8, 2, 2, 5, 0.45, 0.00, 2.25, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(14, 8, 2, 2, 5, 0.45, 0.00, 2.25, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(15, 8, 5, 5, 2, 1.20, 0.00, 2.40, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(16, 9, 10, 10, 3, 35.00, 0.00, 105.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(17, 9, 9, 9, 5, 0.50, 0.00, 2.50, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(18, 10, 5, 5, 3, 1.20, 0.00, 3.60, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(19, 10, 1, 1, 3, 0.30, 0.00, 0.90, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(20, 11, 7, 7, 5, 0.55, 0.00, 2.75, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(21, 11, 1, 1, 5, 0.30, 0.00, 1.50, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(22, 12, 9, 9, 2, 0.50, 0.00, 1.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(23, 12, 9, 9, 4, 0.50, 0.00, 2.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(24, 13, 1, 1, 5, 0.30, 0.00, 1.50, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(25, 13, 1, 1, 3, 0.30, 0.00, 0.90, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(26, 14, 5, 5, 1, 1.20, 0.00, 1.20, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(27, 15, 4, 4, 2, 0.60, 0.00, 1.20, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(28, 16, 7, 7, 2, 0.55, 0.00, 1.10, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(29, 17, 2, 2, 5, 0.45, 0.00, 2.25, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(30, 17, 2, 2, 5, 0.45, 0.00, 2.25, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(31, 17, 5, 5, 4, 1.20, 0.00, 4.80, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(32, 18, 10, 10, 4, 35.00, 0.00, 140.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(33, 18, 5, 5, 5, 1.20, 0.00, 6.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(34, 19, 5, 5, 4, 1.20, 0.00, 4.80, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(35, 19, 7, 7, 3, 0.55, 0.00, 1.65, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(36, 20, 8, 8, 4, 32.00, 0.00, 128.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(37, 21, 7, 7, 5, 0.55, 0.00, 2.75, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(38, 21, 7, 7, 4, 0.55, 0.00, 2.20, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(39, 22, 10, 10, 1, 35.00, 0.00, 35.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(40, 23, 5, 5, 1, 1.20, 0.00, 1.20, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(41, 23, 7, 7, 3, 0.55, 0.00, 1.65, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(42, 23, 1, 1, 2, 0.30, 0.00, 0.60, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(43, 24, 10, 10, 1, 35.00, 0.00, 35.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(44, 24, 6, 6, 4, 1.20, 0.00, 4.80, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(45, 25, 2, 2, 4, 0.45, 0.00, 1.80, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(46, 25, 1, 1, 3, 0.30, 0.00, 0.90, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(47, 25, 4, 4, 3, 0.60, 0.00, 1.80, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(48, 26, 10, 10, 2, 35.00, 0.00, 70.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(49, 27, 3, 3, 2, 0.80, 0.00, 1.60, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(50, 27, 1, 1, 1, 0.30, 0.00, 0.30, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(51, 27, 4, 4, 1, 0.60, 0.00, 0.60, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(52, 28, 4, 4, 5, 0.60, 0.00, 3.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(53, 28, 10, 10, 5, 35.00, 0.00, 175.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(54, 29, 9, 9, 4, 0.50, 0.00, 2.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(55, 30, 9, 9, 1, 0.50, 0.00, 0.50, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(56, 30, 8, 8, 5, 32.00, 0.00, 160.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(57, 31, 7, 7, 3, 0.55, 0.00, 1.65, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(58, 32, 8, 8, 1, 32.00, 0.00, 32.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(59, 33, 8, 8, 3, 32.00, 0.00, 96.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(60, 33, 10, 10, 1, 35.00, 0.00, 35.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(61, 34, 3, 3, 5, 0.80, 0.00, 4.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(62, 34, 1, 1, 4, 0.30, 0.00, 1.20, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(63, 35, 9, 9, 3, 0.50, 0.00, 1.50, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(64, 35, 3, 3, 3, 0.80, 0.00, 2.40, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(65, 36, 3, 3, 5, 0.80, 0.00, 4.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(66, 36, 2, 2, 2, 0.45, 0.00, 0.90, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(67, 37, 1, 1, 3, 0.30, 0.00, 0.90, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(68, 37, 1, 1, 2, 0.30, 0.00, 0.60, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(69, 37, 6, 6, 3, 1.20, 0.00, 3.60, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(70, 38, 2, 2, 3, 0.45, 0.00, 1.35, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(71, 39, 6, 6, 4, 1.20, 0.00, 4.80, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(72, 40, 10, 10, 5, 35.00, 0.00, 175.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(73, 40, 7, 7, 2, 0.55, 0.00, 1.10, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(74, 40, 10, 10, 2, 35.00, 0.00, 70.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(75, 41, 2, 2, 2, 0.45, 0.00, 0.90, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(76, 41, 10, 10, 5, 35.00, 0.00, 175.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(77, 42, 7, 7, 4, 0.55, 0.00, 2.20, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(78, 42, 10, 10, 1, 35.00, 0.00, 35.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(79, 42, 4, 4, 4, 0.60, 0.00, 2.40, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(80, 43, 2, 2, 1, 0.45, 0.00, 0.45, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(81, 44, 3, 3, 1, 0.80, 0.00, 0.80, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(82, 44, 7, 7, 1, 0.55, 0.00, 0.55, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(83, 45, 3, 3, 1, 0.80, 0.00, 0.80, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(84, 45, 6, 6, 5, 1.20, 0.00, 6.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(85, 46, 1, 1, 2, 0.30, 0.00, 0.60, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(86, 46, 2, 2, 1, 0.45, 0.00, 0.45, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(87, 47, 3, 3, 3, 0.80, 0.00, 2.40, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(88, 48, 6, 6, 5, 1.20, 0.00, 6.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(89, 48, 2, 2, 3, 0.45, 0.00, 1.35, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(90, 49, 3, 3, 1, 0.80, 0.00, 0.80, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(91, 50, 8, 8, 2, 32.00, 0.00, 64.00, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(92, 51, 3, 3, 4, 0.80, 0.00, 3.20, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(93, 51, 2, 2, 5, 0.45, 0.00, 2.25, '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(94, 52, 1, 1, 4, 0.30, 0.00, 1.20, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(95, 53, 6, 6, 2, 1.20, 0.00, 2.40, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(96, 53, 3, 3, 5, 0.80, 0.00, 4.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(97, 53, 6, 6, 3, 1.20, 0.00, 3.60, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(98, 54, 7, 7, 4, 0.55, 0.00, 2.20, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(99, 55, 4, 4, 4, 0.60, 0.00, 2.40, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(100, 56, 8, 8, 5, 32.00, 0.00, 160.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(101, 57, 1, 1, 2, 0.30, 0.00, 0.60, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(102, 57, 9, 9, 3, 0.50, 0.00, 1.50, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(103, 57, 6, 6, 2, 1.20, 0.00, 2.40, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(104, 58, 8, 8, 5, 32.00, 0.00, 160.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(105, 58, 3, 3, 1, 0.80, 0.00, 0.80, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(106, 59, 7, 7, 2, 0.55, 0.00, 1.10, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(107, 59, 4, 4, 1, 0.60, 0.00, 0.60, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(108, 60, 7, 7, 5, 0.55, 0.00, 2.75, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(109, 60, 1, 1, 5, 0.30, 0.00, 1.50, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(110, 60, 7, 7, 1, 0.55, 0.00, 0.55, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(111, 61, 6, 6, 5, 1.20, 0.00, 6.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(112, 62, 5, 5, 1, 1.20, 0.00, 1.20, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(113, 63, 1, 1, 3, 0.30, 0.00, 0.90, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(114, 64, 3, 3, 1, 0.80, 0.00, 0.80, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(115, 64, 10, 10, 5, 35.00, 0.00, 175.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(116, 65, 9, 9, 4, 0.50, 0.00, 2.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(117, 66, 8, 8, 5, 32.00, 0.00, 160.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(118, 66, 1, 1, 2, 0.30, 0.00, 0.60, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(119, 67, 5, 5, 2, 1.20, 0.00, 2.40, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(120, 67, 10, 10, 1, 35.00, 0.00, 35.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(121, 67, 3, 3, 4, 0.80, 0.00, 3.20, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(122, 68, 10, 10, 4, 35.00, 0.00, 140.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(123, 68, 5, 5, 2, 1.20, 0.00, 2.40, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(124, 69, 10, 10, 4, 35.00, 0.00, 140.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(125, 69, 10, 10, 4, 35.00, 0.00, 140.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(126, 69, 9, 9, 1, 0.50, 0.00, 0.50, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(127, 70, 5, 5, 4, 1.20, 0.00, 4.80, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(128, 70, 7, 7, 2, 0.55, 0.00, 1.10, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(129, 71, 7, 7, 1, 0.55, 0.00, 0.55, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(130, 71, 3, 3, 5, 0.80, 0.00, 4.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(131, 72, 8, 8, 3, 32.00, 0.00, 96.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(132, 73, 7, 7, 4, 0.55, 0.00, 2.20, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(133, 73, 9, 9, 4, 0.50, 0.00, 2.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(134, 74, 5, 5, 1, 1.20, 0.00, 1.20, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(135, 74, 10, 10, 2, 35.00, 0.00, 70.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(136, 75, 10, 10, 1, 35.00, 0.00, 35.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(137, 76, 8, 8, 3, 32.00, 0.00, 96.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(138, 76, 3, 3, 2, 0.80, 0.00, 1.60, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(139, 77, 9, 9, 3, 0.50, 0.00, 1.50, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(140, 77, 8, 8, 1, 32.00, 0.00, 32.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(141, 78, 1, 1, 3, 0.30, 0.00, 0.90, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(142, 78, 1, 1, 3, 0.30, 0.00, 0.90, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(143, 79, 7, 7, 3, 0.55, 0.00, 1.65, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(144, 80, 2, 2, 3, 0.45, 0.00, 1.35, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(145, 80, 7, 7, 5, 0.55, 0.00, 2.75, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(146, 80, 1, 1, 5, 0.30, 0.00, 1.50, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(147, 81, 8, 8, 5, 32.00, 0.00, 160.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(148, 81, 1, 1, 2, 0.30, 0.00, 0.60, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(149, 82, 3, 3, 3, 0.80, 0.00, 2.40, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(150, 82, 5, 5, 3, 1.20, 0.00, 3.60, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(151, 83, 7, 7, 2, 0.55, 0.00, 1.10, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(152, 83, 10, 10, 3, 35.00, 0.00, 105.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(153, 84, 5, 5, 5, 1.20, 0.00, 6.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(154, 85, 9, 9, 4, 0.50, 0.00, 2.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(155, 85, 1, 1, 3, 0.30, 0.00, 0.90, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(156, 86, 4, 4, 4, 0.60, 0.00, 2.40, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(157, 87, 9, 9, 2, 0.50, 0.00, 1.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(158, 87, 7, 7, 1, 0.55, 0.00, 0.55, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(159, 87, 9, 9, 5, 0.50, 0.00, 2.50, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(160, 88, 3, 3, 5, 0.80, 0.00, 4.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(161, 89, 9, 9, 4, 0.50, 0.00, 2.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(162, 90, 3, 3, 2, 0.80, 0.00, 1.60, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(163, 91, 1, 1, 2, 0.30, 0.00, 0.60, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(164, 91, 10, 10, 3, 35.00, 0.00, 105.00, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(165, 92, 6, 6, 1, 1.20, 0.00, 1.20, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(166, 92, 10, 14, 3, 35.00, 0.00, 105.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(167, 93, 8, 15, 3, 32.00, 0.00, 96.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(168, 94, 7, 7, 4, 0.55, 0.00, 2.20, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(169, 94, 9, 9, 3, 0.50, 0.00, 1.50, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(170, 94, 10, 14, 4, 35.00, 0.00, 140.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(171, 95, 9, 9, 1, 0.50, 0.00, 0.50, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(172, 95, 8, 15, 5, 32.00, 0.00, 160.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(173, 96, 7, 7, 4, 0.55, 0.00, 2.20, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(174, 96, 7, 7, 3, 0.55, 0.00, 1.65, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(175, 97, 8, 15, 5, 32.00, 0.00, 160.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(176, 97, 1, 1, 2, 0.30, 0.00, 0.60, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(177, 97, 9, 9, 5, 0.50, 0.00, 2.50, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(178, 98, 8, 15, 5, 32.00, 0.00, 160.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(179, 98, 8, 15, 1, 32.00, 0.00, 32.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(180, 99, 10, 14, 1, 35.00, 0.00, 35.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(181, 99, 3, 3, 3, 0.80, 0.00, 2.40, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(182, 99, 8, 15, 5, 32.00, 0.00, 160.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(183, 100, 5, 11, 5, 1.20, 0.00, 6.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(184, 100, 10, 14, 1, 35.00, 0.00, 35.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(185, 100, 2, 12, 4, 0.45, 0.00, 1.80, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(186, 101, 3, 3, 2, 0.80, 0.00, 1.60, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(187, 101, 7, 7, 2, 0.55, 0.00, 1.10, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(188, 101, 8, 15, 2, 32.00, 0.00, 64.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(189, 101, 6, 6, 2, 1.20, 0.00, 2.40, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(190, 102, 7, 7, 4, 0.55, 0.00, 2.20, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(191, 102, 9, 9, 4, 0.50, 0.00, 2.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(192, 102, 10, 14, 3, 35.00, 0.00, 105.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(193, 103, 8, 15, 5, 32.00, 0.00, 160.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(194, 103, 1, 1, 3, 0.30, 0.00, 0.90, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(195, 104, 9, 9, 2, 0.50, 0.00, 1.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(196, 104, 6, 6, 2, 1.20, 0.00, 2.40, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(197, 104, 4, 4, 3, 0.60, 0.00, 1.80, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(198, 105, 10, 14, 5, 35.00, 0.00, 175.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(199, 105, 10, 14, 2, 35.00, 0.00, 70.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(200, 105, 5, 11, 4, 1.20, 0.00, 4.80, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(201, 106, 3, 3, 4, 0.80, 0.00, 3.20, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(202, 106, 9, 9, 5, 0.50, 0.00, 2.50, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(203, 106, 6, 6, 2, 1.20, 0.00, 2.40, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(204, 107, 1, 1, 2, 0.30, 0.00, 0.60, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(205, 107, 7, 7, 2, 0.55, 0.00, 1.10, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(206, 108, 8, 15, 5, 32.00, 0.00, 160.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(207, 108, 8, 15, 2, 32.00, 0.00, 64.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(208, 109, 8, 15, 5, 32.00, 0.00, 160.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(209, 109, 1, 1, 2, 0.30, 0.00, 0.60, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(210, 110, 8, 15, 4, 32.00, 0.00, 128.00, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(211, 110, 2, 12, 1, 0.45, 0.00, 0.45, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(212, 111, 4, 4, 4, 0.60, 0.00, 2.40, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(213, 112, 3, NULL, 1, 0.80, 0.00, 0.80, '2026-04-30 07:16:02', '2026-04-30 07:16:02');

-- Volcando estructura para tabla farmacia_erp.failed_jobs
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

-- Volcando datos para la tabla farmacia_erp.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Volcando estructura para tabla farmacia_erp.interacciones
CREATE TABLE IF NOT EXISTS `interacciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `principio_a` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `principio_b` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `severidad` enum('baja','moderada','severa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'moderada',
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interacciones_principio_a_principio_b_index` (`principio_a`,`principio_b`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.interacciones: ~0 rows (aproximadamente)
DELETE FROM `interacciones`;

-- Volcando estructura para tabla farmacia_erp.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.jobs: ~0 rows (aproximadamente)
DELETE FROM `jobs`;

-- Volcando estructura para tabla farmacia_erp.job_batches
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

-- Volcando datos para la tabla farmacia_erp.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Volcando estructura para tabla farmacia_erp.lotes
CREATE TABLE IF NOT EXISTS `lotes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint unsigned NOT NULL,
  `numero_lote` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `cantidad` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lotes_producto_id_fecha_vencimiento_index` (`producto_id`,`fecha_vencimiento`),
  CONSTRAINT `lotes_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.lotes: ~24 rows (aproximadamente)
DELETE FROM `lotes`;
INSERT INTO `lotes` (`id`, `producto_id`, `numero_lote`, `fecha_vencimiento`, `cantidad`, `created_at`, `updated_at`) VALUES
	(1, 1, 'L2026-001', '2028-03-29', 480, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(2, 2, 'L2026-002', '2026-07-29', 320, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(3, 3, 'L2026-003', '2028-01-29', 210, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(4, 4, 'L2026-004', '2027-05-29', 150, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(5, 5, 'L2026-005', '2028-03-29', 90, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(6, 6, 'L2026-006', '2027-03-01', 60, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(7, 7, 'L2026-007', '2026-07-29', 4, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(8, 8, 'L2026-008', '2026-11-29', 22, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(9, 9, 'L2026-009', '2026-07-29', 800, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(10, 10, 'L2026-010', '2026-08-29', 18, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(11, 5, 'LD-7198', '2026-07-04', 10, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(12, 2, 'LD-6774', '2026-06-16', 16, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(13, 10, 'LD-8124', '2026-07-02', 38, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(14, 10, 'LD-6204', '2026-06-21', 26, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(15, 8, 'LD-9850', '2026-05-20', 24, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(16, 4, 'LD5-V-2369', '2026-05-09', 12, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(17, 4, 'LD5-V-3777', '2026-06-04', 26, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(18, 3, 'LD5-V-5439', '2026-07-07', 46, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(19, 1, 'LD5-V-7338', '2026-07-09', 36, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(20, 7, 'LD5-V-9076', '2026-06-18', 49, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(21, 7, 'LD5-V-5376', '2026-06-10', 50, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(22, 9, 'LD5-V-5666', '2026-05-24', 35, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(23, 1, 'LD5-V-6024', '2026-05-23', 31, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(24, 3, 'LD5-V-7943', '2026-05-09', 46, '2026-04-30 02:47:45', '2026-04-30 02:47:45');

-- Volcando estructura para tabla farmacia_erp.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.migrations: ~19 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2024_01_01_000000_create_permission_tables', 1),
	(5, '2024_02_01_000000_create_categorias_table', 1),
	(6, '2024_02_01_000001_create_proveedores_table', 1),
	(7, '2024_02_01_000002_create_productos_table', 1),
	(8, '2024_02_01_000003_create_lotes_table', 1),
	(9, '2024_02_01_000004_create_clientes_table', 1),
	(10, '2024_02_01_000005_create_ventas_table', 1),
	(11, '2024_02_01_000006_create_compras_table', 1),
	(12, '2024_02_01_000007_create_movimientos_caja_table', 1),
	(13, '2024_02_01_000008_create_recetas_table', 1),
	(14, '2024_02_02_000000_add_caja_id_to_ventas', 1),
	(15, '2024_03_01_000000_add_atc_imagen_to_productos', 2),
	(16, '2024_03_01_000001_add_motivo_a_ventas', 2),
	(17, '2024_03_01_000002_create_interacciones_table', 2),
	(18, '2026_04_30_012130_create_settings_table', 2),
	(19, '2026_04_30_012329_create_audit_logs_table', 3),
	(20, '2026_04_30_012659_add_credit_fields_to_clientes_table', 4),
	(21, '2026_04_30_013607_create_multi_branch_infrastructure', 5);

-- Volcando estructura para tabla farmacia_erp.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.model_has_permissions: ~0 rows (aproximadamente)
DELETE FROM `model_has_permissions`;

-- Volcando estructura para tabla farmacia_erp.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.model_has_roles: ~3 rows (aproximadamente)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(3, 'App\\Models\\User', 2),
	(2, 'App\\Models\\User', 3);

-- Volcando estructura para tabla farmacia_erp.movimientos_caja
CREATE TABLE IF NOT EXISTS `movimientos_caja` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `caja_id` bigint unsigned NOT NULL,
  `tipo` enum('ingreso','egreso') COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `concepto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_caja_caja_id_foreign` (`caja_id`),
  CONSTRAINT `movimientos_caja_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.movimientos_caja: ~0 rows (aproximadamente)
DELETE FROM `movimientos_caja`;

-- Volcando estructura para tabla farmacia_erp.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Volcando estructura para tabla farmacia_erp.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.permissions: ~19 rows (aproximadamente)
DELETE FROM `permissions`;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'dashboard.view', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(2, 'inventario.view', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(3, 'inventario.manage', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(4, 'categorias.manage', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(5, 'proveedores.manage', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(6, 'lotes.manage', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(7, 'clientes.view', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(8, 'clientes.manage', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(9, 'pos.use', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(10, 'caja.use', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(11, 'caja.cerrar', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(12, 'compras.view', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(13, 'compras.manage', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(14, 'recetas.view', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(15, 'recetas.manage', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(16, 'reportes.view', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(17, 'reportes.export', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(18, 'usuarios.manage', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(19, 'config.manage', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(20, 'settings.manage', 'web', '2026-04-30 06:23:27', '2026-04-30 06:23:27'),
	(21, 'audit.view', 'web', '2026-04-30 06:23:27', '2026-04-30 06:23:27');

-- Volcando estructura para tabla farmacia_erp.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `principio_activo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `presentacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `concentracion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_atc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria_id` bigint unsigned DEFAULT NULL,
  `proveedor_id` bigint unsigned DEFAULT NULL,
  `tipo` enum('generico','marca','controlado','refrigerado','cosmetico','insumo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generico',
  `precio_compra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio_venta` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_maximo` int NOT NULL DEFAULT '500',
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requiere_receta` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_codigo_unique` (`codigo`),
  KEY `productos_categoria_id_foreign` (`categoria_id`),
  KEY `productos_proveedor_id_foreign` (`proveedor_id`),
  CONSTRAINT `productos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `productos_proveedor_id_foreign` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.productos: ~10 rows (aproximadamente)
DELETE FROM `productos`;
INSERT INTO `productos` (`id`, `codigo`, `nombre`, `principio_activo`, `presentacion`, `concentracion`, `codigo_atc`, `categoria_id`, `proveedor_id`, `tipo`, `precio_compra`, `precio_venta`, `stock_maximo`, `imagen`, `requiere_receta`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'PARAC500', 'Paracetamol 500 mg', 'Paracetamol', 'Tableta', '500 mg', NULL, 1, 1, 'generico', 0.10, 0.30, 800, NULL, 0, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(2, 'IBU400', 'Ibuprofeno 400 mg', 'Ibuprofeno', 'Tableta', '400 mg', NULL, 4, 3, 'generico', 0.15, 0.45, 800, NULL, 0, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(3, 'AMOX500', 'Amoxicilina 500 mg', 'Amoxicilina', 'Cápsula', '500 mg', NULL, 2, 1, 'generico', 0.30, 0.80, 800, NULL, 1, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(4, 'LORAT10', 'Loratadina 10 mg', 'Loratadina', 'Tableta', '10 mg', NULL, 3, 2, 'generico', 0.20, 0.60, 800, NULL, 0, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(5, 'VITC1G', 'Vitamina C 1 g', 'Ácido ascórbico', 'Tableta efervescente', '1 g', NULL, 5, 3, 'generico', 0.40, 1.20, 800, NULL, 0, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(6, 'PANAD500', 'Panadol 500 mg', 'Paracetamol', 'Tableta', '500 mg', NULL, 1, 3, 'marca', 0.50, 1.20, 800, NULL, 0, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(7, 'CETIR10', 'Cetirizina 10 mg', 'Cetirizina', 'Tableta', '10 mg', NULL, 3, 1, 'generico', 0.20, 0.55, 800, NULL, 0, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(8, 'PROT100', 'Protector solar FPS 50', NULL, 'Crema', '100 ml', NULL, 6, 4, 'cosmetico', 18.00, 32.00, 800, NULL, 0, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(9, 'JERIN5', 'Jeringa descartable 5 ml', NULL, 'Unidad', '5 ml', NULL, 7, 3, 'insumo', 0.15, 0.50, 800, NULL, 0, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(10, 'PANIB', 'Pañal infantil M x 30', NULL, 'Paquete', '30 unid', NULL, 8, 4, 'cosmetico', 22.00, 35.00, 800, NULL, 0, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53');

-- Volcando estructura para tabla farmacia_erp.proveedores
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ruc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razon_social` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contacto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proveedores_ruc_unique` (`ruc`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.proveedores: ~35 rows (aproximadamente)
DELETE FROM `proveedores`;
INSERT INTO `proveedores` (`id`, `ruc`, `razon_social`, `contacto`, `telefono`, `email`, `direccion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, '20100070970', 'Laboratorios Bayer S.A.', 'Juan Pérez', '014567890', 'ventas@bayer.com', NULL, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(2, '20101111111', 'Pfizer Perú S.A.', 'María Soto', '014444444', 'pedidos@pfizer.com', NULL, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(3, '20102222222', 'Genfar Perú S.A.', 'Luis Quispe', '015555555', 'contacto@genfar.pe', NULL, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(4, '20103333333', 'Distribuidora Drokasa S.A.', 'Ana Vega', '016666666', 'comercial@drokasa.pe', NULL, 1, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(5, '20500000005', 'Proveedor Demo 5 S.A.C', 'Vendedor 5', '012345605', 'ventas5@demo.com', NULL, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(6, '20500000006', 'Proveedor Demo 6 S.A.C', 'Vendedor 6', '012345606', 'ventas6@demo.com', NULL, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(7, '20500000007', 'Proveedor Demo 7 S.A.C', 'Vendedor 7', '012345607', 'ventas7@demo.com', NULL, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(8, '20500000008', 'Proveedor Demo 8 S.A.C', 'Vendedor 8', '012345608', 'ventas8@demo.com', NULL, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(9, '20500000009', 'Proveedor Demo 9 S.A.C', 'Vendedor 9', '012345609', 'ventas9@demo.com', NULL, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(10, '20500000010', 'Proveedor Demo 10 S.A.C', 'Vendedor 10', '012345610', 'ventas10@demo.com', NULL, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(11, '20500000011', 'Proveedor Demo 11 S.A.C', 'Vendedor 11', '012345611', 'ventas11@demo.com', NULL, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(12, '20500000012', 'Proveedor Demo 12 S.A.C', 'Vendedor 12', '012345612', 'ventas12@demo.com', NULL, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(13, '20500000013', 'Proveedor Demo 13 S.A.C', 'Vendedor 13', '012345613', 'ventas13@demo.com', NULL, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(14, '20500000014', 'Proveedor Demo 14 S.A.C', 'Vendedor 14', '012345614', 'ventas14@demo.com', NULL, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(15, '20500000015', 'Proveedor Demo 15 S.A.C', 'Vendedor 15', '012345615', 'ventas15@demo.com', NULL, 1, '2026-04-30 02:30:55', '2026-04-30 02:30:55'),
	(16, '20500101', 'Proveedor Nuevo 101 S.A.C', 'Vendedor 101', '012345101', 'ventas101@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(17, '20500102', 'Proveedor Nuevo 102 S.A.C', 'Vendedor 102', '012345102', 'ventas102@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(18, '20500103', 'Proveedor Nuevo 103 S.A.C', 'Vendedor 103', '012345103', 'ventas103@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(19, '20500104', 'Proveedor Nuevo 104 S.A.C', 'Vendedor 104', '012345104', 'ventas104@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(20, '20500105', 'Proveedor Nuevo 105 S.A.C', 'Vendedor 105', '012345105', 'ventas105@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(21, '20500106', 'Proveedor Nuevo 106 S.A.C', 'Vendedor 106', '012345106', 'ventas106@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(22, '20500107', 'Proveedor Nuevo 107 S.A.C', 'Vendedor 107', '012345107', 'ventas107@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(23, '20500108', 'Proveedor Nuevo 108 S.A.C', 'Vendedor 108', '012345108', 'ventas108@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(24, '20500109', 'Proveedor Nuevo 109 S.A.C', 'Vendedor 109', '012345109', 'ventas109@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(25, '20500110', 'Proveedor Nuevo 110 S.A.C', 'Vendedor 110', '012345110', 'ventas110@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(26, '20500111', 'Proveedor Nuevo 111 S.A.C', 'Vendedor 111', '012345111', 'ventas111@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(27, '20500112', 'Proveedor Nuevo 112 S.A.C', 'Vendedor 112', '012345112', 'ventas112@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(28, '20500113', 'Proveedor Nuevo 113 S.A.C', 'Vendedor 113', '012345113', 'ventas113@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(29, '20500114', 'Proveedor Nuevo 114 S.A.C', 'Vendedor 114', '012345114', 'ventas114@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(30, '20500115', 'Proveedor Nuevo 115 S.A.C', 'Vendedor 115', '012345115', 'ventas115@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(31, '20500116', 'Proveedor Nuevo 116 S.A.C', 'Vendedor 116', '012345116', 'ventas116@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(32, '20500117', 'Proveedor Nuevo 117 S.A.C', 'Vendedor 117', '012345117', 'ventas117@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(33, '20500118', 'Proveedor Nuevo 118 S.A.C', 'Vendedor 118', '012345118', 'ventas118@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(34, '20500119', 'Proveedor Nuevo 119 S.A.C', 'Vendedor 119', '012345119', 'ventas119@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(35, '20500120', 'Proveedor Nuevo 120 S.A.C', 'Vendedor 120', '012345120', 'ventas120@demo.com', NULL, 1, '2026-04-30 02:47:45', '2026-04-30 02:47:45');

-- Volcando estructura para tabla farmacia_erp.recetas
CREATE TABLE IF NOT EXISTS `recetas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `medico` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `especialidad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cmp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `retenida` tinyint(1) NOT NULL DEFAULT '0',
  `diagnostico` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recetas_codigo_unique` (`codigo`),
  KEY `recetas_cliente_id_foreign` (`cliente_id`),
  KEY `recetas_user_id_foreign` (`user_id`),
  CONSTRAINT `recetas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `recetas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.recetas: ~35 rows (aproximadamente)
DELETE FROM `recetas`;
INSERT INTO `recetas` (`id`, `codigo`, `cliente_id`, `user_id`, `medico`, `especialidad`, `cmp`, `fecha`, `retenida`, `diagnostico`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 'R-DEMO-1-560', 10, 1, 'Dr. Demo 1', 'Medicina General', '20079', '2026-04-05', 1, 'Diagnostico Demo 1', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(2, 'R-DEMO-2-465', 9, 1, 'Dr. Demo 2', 'Medicina General', '58377', '2026-04-06', 0, 'Diagnostico Demo 2', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(3, 'R-DEMO-3-928', 12, 1, 'Dr. Demo 3', 'Medicina General', '90099', '2026-04-20', 1, 'Diagnostico Demo 3', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(4, 'R-DEMO-4-536', 14, 1, 'Dr. Demo 4', 'Medicina General', '74264', '2026-04-04', 0, 'Diagnostico Demo 4', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(5, 'R-DEMO-5-899', 9, 1, 'Dr. Demo 5', 'Medicina General', '63971', '2026-04-21', 1, 'Diagnostico Demo 5', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(6, 'R-DEMO-6-392', 5, 1, 'Dr. Demo 6', 'Medicina General', '11963', '2026-04-10', 1, 'Diagnostico Demo 6', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(7, 'R-DEMO-7-325', 5, 1, 'Dr. Demo 7', 'Medicina General', '82799', '2026-04-18', 1, 'Diagnostico Demo 7', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(8, 'R-DEMO-8-783', 3, 1, 'Dr. Demo 8', 'Medicina General', '16172', '2026-04-17', 0, 'Diagnostico Demo 8', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(9, 'R-DEMO-9-341', 11, 1, 'Dr. Demo 9', 'Medicina General', '27979', '2026-03-31', 0, 'Diagnostico Demo 9', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(10, 'R-DEMO-10-819', 8, 1, 'Dr. Demo 10', 'Medicina General', '92599', '2026-04-22', 0, 'Diagnostico Demo 10', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(11, 'R-DEMO-11-281', 5, 1, 'Dr. Demo 11', 'Medicina General', '31652', '2026-04-17', 1, 'Diagnostico Demo 11', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(12, 'R-DEMO-12-961', 14, 1, 'Dr. Demo 12', 'Medicina General', '71934', '2026-04-01', 1, 'Diagnostico Demo 12', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(13, 'R-DEMO-13-881', 9, 1, 'Dr. Demo 13', 'Medicina General', '34921', '2026-04-10', 0, 'Diagnostico Demo 13', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(14, 'R-DEMO-14-917', 2, 1, 'Dr. Demo 14', 'Medicina General', '87828', '2026-04-04', 1, 'Diagnostico Demo 14', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(15, 'R-DEMO-15-969', 3, 1, 'Dr. Demo 15', 'Medicina General', '87169', '2026-04-08', 0, 'Diagnostico Demo 15', NULL, '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(16, 'R-DEMO5-1-278', 12, 1, 'Dr. Especialista 1', 'Especialidad 1', '23872', '2026-03-05', 0, 'Diagnostico Nuevo 1', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(17, 'R-DEMO5-2-736', 30, 1, 'Dr. Especialista 2', 'Especialidad 1', '69066', '2026-03-13', 1, 'Diagnostico Nuevo 2', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(18, 'R-DEMO5-3-114', 1, 1, 'Dr. Especialista 3', 'Especialidad 2', '34762', '2026-01-09', 0, 'Diagnostico Nuevo 3', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(19, 'R-DEMO5-4-126', 6, 1, 'Dr. Especialista 4', 'Especialidad 1', '94379', '2025-12-09', 0, 'Diagnostico Nuevo 4', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(20, 'R-DEMO5-5-164', 17, 1, 'Dr. Especialista 5', 'Especialidad 5', '43685', '2026-04-20', 1, 'Diagnostico Nuevo 5', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(21, 'R-DEMO5-6-805', 19, 1, 'Dr. Especialista 6', 'Especialidad 4', '98831', '2026-04-06', 0, 'Diagnostico Nuevo 6', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(22, 'R-DEMO5-7-965', 25, 1, 'Dr. Especialista 7', 'Especialidad 3', '66466', '2026-01-23', 1, 'Diagnostico Nuevo 7', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(23, 'R-DEMO5-8-375', 15, 1, 'Dr. Especialista 8', 'Especialidad 5', '19155', '2026-01-02', 1, 'Diagnostico Nuevo 8', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(24, 'R-DEMO5-9-190', 18, 1, 'Dr. Especialista 9', 'Especialidad 2', '48272', '2026-03-09', 1, 'Diagnostico Nuevo 9', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(25, 'R-DEMO5-10-487', 3, 1, 'Dr. Especialista 10', 'Especialidad 1', '65392', '2026-03-04', 0, 'Diagnostico Nuevo 10', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(26, 'R-DEMO5-11-603', 8, 1, 'Dr. Especialista 11', 'Especialidad 4', '51663', '2025-12-23', 1, 'Diagnostico Nuevo 11', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(27, 'R-DEMO5-12-177', 1, 1, 'Dr. Especialista 12', 'Especialidad 4', '67179', '2026-03-09', 0, 'Diagnostico Nuevo 12', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(28, 'R-DEMO5-13-296', 22, 1, 'Dr. Especialista 13', 'Especialidad 5', '39908', '2026-01-17', 0, 'Diagnostico Nuevo 13', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(29, 'R-DEMO5-14-431', 7, 1, 'Dr. Especialista 14', 'Especialidad 3', '96043', '2025-12-23', 0, 'Diagnostico Nuevo 14', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(30, 'R-DEMO5-15-686', 34, 1, 'Dr. Especialista 15', 'Especialidad 5', '84450', '2026-04-23', 1, 'Diagnostico Nuevo 15', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(31, 'R-DEMO5-16-935', 4, 1, 'Dr. Especialista 16', 'Especialidad 3', '47742', '2026-03-19', 0, 'Diagnostico Nuevo 16', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(32, 'R-DEMO5-17-886', 29, 1, 'Dr. Especialista 17', 'Especialidad 4', '35188', '2025-12-12', 1, 'Diagnostico Nuevo 17', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(33, 'R-DEMO5-18-583', 35, 1, 'Dr. Especialista 18', 'Especialidad 2', '95946', '2026-01-24', 0, 'Diagnostico Nuevo 18', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(34, 'R-DEMO5-19-752', 21, 1, 'Dr. Especialista 19', 'Especialidad 5', '37304', '2026-04-10', 1, 'Diagnostico Nuevo 19', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(35, 'R-DEMO5-20-878', 25, 1, 'Dr. Especialista 20', 'Especialidad 2', '77349', '2026-03-09', 0, 'Diagnostico Nuevo 20', NULL, '2026-04-30 02:47:45', '2026-04-30 02:47:45');

-- Volcando estructura para tabla farmacia_erp.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.roles: ~5 rows (aproximadamente)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(2, 'Farmaceutico', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(3, 'Cajero', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(4, 'Almacenero', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52'),
	(5, 'Contador', 'web', '2026-04-30 01:52:52', '2026-04-30 01:52:52');

-- Volcando estructura para tabla farmacia_erp.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.role_has_permissions: ~46 rows (aproximadamente)
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
	(20, 1),
	(21, 1),
	(1, 2),
	(2, 2),
	(3, 2),
	(6, 2),
	(7, 2),
	(8, 2),
	(9, 2),
	(10, 2),
	(14, 2),
	(15, 2),
	(1, 3),
	(7, 3),
	(9, 3),
	(10, 3),
	(1, 4),
	(2, 4),
	(3, 4),
	(4, 4),
	(5, 4),
	(6, 4),
	(12, 4),
	(13, 4),
	(1, 5),
	(11, 5),
	(16, 5),
	(17, 5);

-- Volcando estructura para tabla farmacia_erp.sessions
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

-- Volcando datos para la tabla farmacia_erp.sessions: ~3 rows (aproximadamente)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('2FqTjqlhxh1lnEFZvSbF0d6c6T51VWPJMG4WjPkt', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaVFjVjZmSWtobGNyRFZIZFJQR0FnaGllUFh2N1c3c3JJOHFseW9FbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwNy9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1777533918),
	('kYBgnXS5xaTKprosg6ljRXStuutoDVSoP5HOFq3n', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUWg1M2V5R2Y0VDZsMHdQS284dElyQVF3TFhFQ3liYlptd05mUmhYaCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwNy9sb2dpbiI7fX0=', 1777548261),
	('tzN9IkiGSqxCV7gmLcUahEysvNXQ7ZeQz8rxODKa', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRERUd0tnQWxwdGp2YTZrVU8zRTNUZjZYTkFFbUxyd3FxT09WWXU1UiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwNy9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1777517396);

-- Volcando estructura para tabla farmacia_erp.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.settings: ~0 rows (aproximadamente)
DELETE FROM `settings`;

-- Volcando estructura para tabla farmacia_erp.sucursales
CREATE TABLE IF NOT EXISTS `sucursales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.sucursales: ~0 rows (aproximadamente)
DELETE FROM `sucursales`;
INSERT INTO `sucursales` (`id`, `nombre`, `direccion`, `telefono`, `es_principal`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Casa Matriz', 'Sede Principal', NULL, 1, 1, '2026-04-30 06:36:28', '2026-04-30 06:36:28');

-- Volcando estructura para tabla farmacia_erp.sucursal_producto
CREATE TABLE IF NOT EXISTS `sucursal_producto` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sucursal_id` bigint unsigned NOT NULL,
  `producto_id` bigint unsigned NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '5',
  `ubicacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sucursal_producto_sucursal_id_producto_id_unique` (`sucursal_id`,`producto_id`),
  KEY `sucursal_producto_producto_id_foreign` (`producto_id`),
  CONSTRAINT `sucursal_producto_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sucursal_producto_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.sucursal_producto: ~10 rows (aproximadamente)
DELETE FROM `sucursal_producto`;
INSERT INTO `sucursal_producto` (`id`, `sucursal_id`, `producto_id`, `stock`, `stock_minimo`, `ubicacion`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 480, 5, 'Estante B-5', '2026-04-30 06:36:28', '2026-04-30 06:36:28'),
	(2, 1, 2, 320, 5, 'Estante C-9', '2026-04-30 06:36:28', '2026-04-30 06:36:28'),
	(3, 1, 3, 209, 5, 'Estante B-5', '2026-04-30 06:36:28', '2026-04-30 06:36:28'),
	(4, 1, 4, 150, 5, 'Estante D-5', '2026-04-30 06:36:28', '2026-04-30 06:36:28'),
	(5, 1, 5, 90, 5, 'Estante A-2', '2026-04-30 06:36:28', '2026-04-30 06:36:28'),
	(6, 1, 6, 60, 5, 'Estante B-1', '2026-04-30 06:36:28', '2026-04-30 06:36:28'),
	(7, 1, 7, 4, 10, 'Estante A-8', '2026-04-30 06:36:28', '2026-04-30 06:36:28'),
	(8, 1, 8, 22, 5, 'Estante D-6', '2026-04-30 06:36:28', '2026-04-30 06:36:28'),
	(9, 1, 9, 800, 5, 'Estante F-1', '2026-04-30 06:36:28', '2026-04-30 06:36:28'),
	(10, 1, 10, 18, 5, 'Estante D-9', '2026-04-30 06:36:28', '2026-04-30 06:36:28');

-- Volcando estructura para tabla farmacia_erp.sucursal_user
CREATE TABLE IF NOT EXISTS `sucursal_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sucursal_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `es_predeterminada` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sucursal_user_sucursal_id_foreign` (`sucursal_id`),
  KEY `sucursal_user_user_id_foreign` (`user_id`),
  CONSTRAINT `sucursal_user_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sucursal_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.sucursal_user: ~3 rows (aproximadamente)
DELETE FROM `sucursal_user`;
INSERT INTO `sucursal_user` (`id`, `sucursal_id`, `user_id`, `es_predeterminada`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, NULL, NULL),
	(2, 1, 2, 1, NULL, NULL),
	(3, 1, 3, 1, NULL, NULL);

-- Volcando estructura para tabla farmacia_erp.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.users: ~3 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `active`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'admin@farmacia.test', NULL, '$2y$12$ATdMp19YdVbBpWNT8ViGL.dLzB6YdmuhFpuytI21ghJh/qmG6ddPW', '999000000', 1, NULL, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(2, 'Cajero Demo', 'cajero@farmacia.test', NULL, '$2y$12$YgzNItYagj0HBC93Q7P3De4LZWka8M5svekCWioM2z2HIhqux8ccO', '999111111', 1, NULL, '2026-04-30 01:52:53', '2026-04-30 01:52:53'),
	(3, 'Farmaceutico Demo', 'farmaceutico@farmacia.test', NULL, '$2y$12$NwEhj83i1w/CSTFcXcaMq.d/FFy3V7qfn5Hlbe2ELxy4FpWDP0NGG', '999222222', 1, NULL, '2026-04-30 01:52:53', '2026-04-30 01:52:53');

-- Volcando estructura para tabla farmacia_erp.ventas
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `sucursal_id` bigint unsigned DEFAULT NULL,
  `caja_id` bigint unsigned DEFAULT NULL,
  `tipo_comprobante` enum('boleta','factura','nota') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'boleta',
  `serie` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `impuesto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `forma_pago` enum('efectivo','tarjeta','transferencia','mixto','credito') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `pago_recibido` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cambio` decimal(12,2) NOT NULL DEFAULT '0.00',
  `puntos_canjeados` int NOT NULL DEFAULT '0',
  `estado` enum('emitida','anulada','devuelta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'emitida',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `motivo_anulacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anulada_at` timestamp NULL DEFAULT NULL,
  `anulada_por` bigint unsigned DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ventas_codigo_unique` (`codigo`),
  KEY `ventas_cliente_id_foreign` (`cliente_id`),
  KEY `ventas_user_id_foreign` (`user_id`),
  KEY `ventas_caja_id_foreign` (`caja_id`),
  KEY `ventas_anulada_por_foreign` (`anulada_por`),
  KEY `ventas_sucursal_id_foreign` (`sucursal_id`),
  CONSTRAINT `ventas_anulada_por_foreign` FOREIGN KEY (`anulada_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ventas_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ventas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ventas_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `ventas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla farmacia_erp.ventas: ~111 rows (aproximadamente)
DELETE FROM `ventas`;
INSERT INTO `ventas` (`id`, `codigo`, `cliente_id`, `user_id`, `sucursal_id`, `caja_id`, `tipo_comprobante`, `serie`, `numero`, `subtotal`, `descuento`, `impuesto`, `total`, `forma_pago`, `pago_recibido`, `cambio`, `puntos_canjeados`, `estado`, `observaciones`, `motivo_anulacion`, `anulada_at`, `anulada_por`, `fecha`, `created_at`, `updated_at`) VALUES
	(1, 'V-DEMO-0-1-230', 6, 1, 1, 1, 'boleta', 'B001', '1757', 135.59, 0.00, 24.41, 160.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-02 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(2, 'V-DEMO-0-2-990', 4, 1, 1, 1, 'boleta', 'B001', '4757', 2.54, 0.00, 0.46, 3.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-14 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(3, 'V-DEMO-0-3-288', 7, 1, 1, 1, 'boleta', 'B001', '5939', 4.32, 0.00, 0.78, 5.10, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-20 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(4, 'V-DEMO-0-4-186', 10, 1, 1, 1, 'boleta', 'B001', '2063', 4.36, 0.00, 0.79, 5.15, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-13 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(5, 'V-DEMO-0-5-800', 4, 1, 1, 1, 'boleta', 'B001', '4888', 148.31, 0.00, 26.69, 175.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-10 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(6, 'V-DEMO-0-6-820', 7, 1, 1, 1, 'boleta', 'B001', '1425', 0.51, 0.00, 0.09, 0.60, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-03 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(7, 'V-DEMO-0-7-572', 10, 1, 1, 1, 'boleta', 'B001', '3824', 0.76, 0.00, 0.14, 0.90, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-21 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(8, 'V-DEMO-0-8-642', 2, 1, 1, 1, 'boleta', 'B001', '3489', 5.85, 0.00, 1.05, 6.90, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-08 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(9, 'V-DEMO-0-9-151', 1, 1, 1, 1, 'boleta', 'B001', '5474', 91.10, 0.00, 16.40, 107.50, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-25 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(10, 'V-DEMO-0-10-198', 3, 1, 1, 1, 'boleta', 'B001', '3241', 3.81, 0.00, 0.69, 4.50, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-13 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(11, 'V-DEMO-1-1-761', 3, 1, 1, 1, 'boleta', 'B001', '1567', 3.60, 0.00, 0.65, 4.25, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-18 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(12, 'V-DEMO-1-2-344', 10, 1, 1, 1, 'boleta', 'B001', '1044', 2.54, 0.00, 0.46, 3.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-06 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(13, 'V-DEMO-1-3-204', 13, 1, 1, 1, 'boleta', 'B001', '2324', 2.03, 0.00, 0.37, 2.40, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-25 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(14, 'V-DEMO-1-4-627', 7, 1, 1, 1, 'boleta', 'B001', '9012', 1.02, 0.00, 0.18, 1.20, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-26 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(15, 'V-DEMO-1-5-838', 15, 1, 1, 1, 'boleta', 'B001', '8443', 1.02, 0.00, 0.18, 1.20, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-11 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(16, 'V-DEMO-1-6-282', 15, 1, 1, 1, 'boleta', 'B001', '1174', 0.93, 0.00, 0.17, 1.10, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-10 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(17, 'V-DEMO-1-7-233', 9, 1, 1, 1, 'boleta', 'B001', '6685', 7.88, 0.00, 1.42, 9.30, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-10 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(18, 'V-DEMO-1-8-705', 4, 1, 1, 1, 'boleta', 'B001', '9758', 123.73, 0.00, 22.27, 146.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-19 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(19, 'V-DEMO-1-9-563', 8, 1, 1, 1, 'boleta', 'B001', '2854', 5.47, 0.00, 0.98, 6.45, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-19 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(20, 'V-DEMO-2-1-608', 5, 1, 1, 1, 'boleta', 'B001', '9544', 108.47, 0.00, 19.53, 128.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-03 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(21, 'V-DEMO-2-2-806', 4, 1, 1, 1, 'boleta', 'B001', '2092', 4.19, 0.00, 0.76, 4.95, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-17 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(22, 'V-DEMO-2-3-656', 10, 1, 1, 1, 'boleta', 'B001', '8292', 29.66, 0.00, 5.34, 35.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-10 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(23, 'V-DEMO-2-4-597', 11, 1, 1, 1, 'boleta', 'B001', '2233', 2.92, 0.00, 0.53, 3.45, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-13 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(24, 'V-DEMO-2-5-916', 11, 1, 1, 1, 'boleta', 'B001', '1447', 33.73, 0.00, 6.07, 39.80, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-24 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(25, 'V-DEMO-2-6-792', 10, 1, 1, 1, 'boleta', 'B001', '2346', 3.81, 0.00, 0.69, 4.50, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-06 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(26, 'V-DEMO-3-1-592', 15, 1, 1, 1, 'boleta', 'B001', '2178', 59.32, 0.00, 10.68, 70.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-01-26 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(27, 'V-DEMO-3-2-880', 7, 1, 1, 1, 'boleta', 'B001', '8440', 2.12, 0.00, 0.38, 2.50, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-01-16 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(28, 'V-DEMO-3-3-111', 8, 1, 1, 1, 'boleta', 'B001', '4379', 150.85, 0.00, 27.15, 178.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-01-09 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(29, 'V-DEMO-3-4-225', 14, 1, 1, 1, 'boleta', 'B001', '7372', 1.69, 0.00, 0.31, 2.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-01-19 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(30, 'V-DEMO-3-5-793', 6, 1, 1, 1, 'boleta', 'B001', '5910', 136.02, 0.00, 24.48, 160.50, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-01-16 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(31, 'V-DEMO-3-6-437', 4, 1, 1, 1, 'boleta', 'B001', '3879', 1.40, 0.00, 0.25, 1.65, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-01-08 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(32, 'V-DEMO-4-1-871', 7, 1, 1, 1, 'boleta', 'B001', '1308', 27.12, 0.00, 4.88, 32.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-12-25 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(33, 'V-DEMO-4-2-179', 9, 1, 1, 1, 'boleta', 'B001', '6506', 111.02, 0.00, 19.98, 131.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-12-18 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(34, 'V-DEMO-4-3-427', 14, 1, 1, 1, 'boleta', 'B001', '5212', 4.41, 0.00, 0.79, 5.20, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-12-12 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(35, 'V-DEMO-4-4-950', 7, 1, 1, 1, 'boleta', 'B001', '1581', 3.31, 0.00, 0.59, 3.90, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-12-19 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(36, 'V-DEMO-4-5-196', 15, 1, 1, 1, 'boleta', 'B001', '7313', 4.15, 0.00, 0.75, 4.90, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-12-09 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(37, 'V-DEMO-4-6-255', 4, 1, 1, 1, 'boleta', 'B001', '3197', 4.32, 0.00, 0.78, 5.10, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-12-19 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(38, 'V-DEMO-4-7-756', 6, 1, 1, 1, 'boleta', 'B001', '4479', 1.14, 0.00, 0.21, 1.35, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-12-09 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(39, 'V-DEMO-5-1-700', 2, 1, 1, 1, 'boleta', 'B001', '8269', 4.07, 0.00, 0.73, 4.80, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-11-15 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(40, 'V-DEMO-5-2-561', 1, 1, 1, 1, 'boleta', 'B001', '5035', 208.56, 0.00, 37.54, 246.10, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-11-22 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(41, 'V-DEMO-5-3-607', 14, 1, 1, 1, 'boleta', 'B001', '9912', 149.07, 0.00, 26.83, 175.90, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-11-03 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(42, 'V-DEMO-5-4-493', 7, 1, 1, 1, 'boleta', 'B001', '8611', 33.56, 0.00, 6.04, 39.60, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-11-03 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(43, 'V-DEMO-5-5-166', 10, 1, 1, 1, 'boleta', 'B001', '5238', 0.38, 0.00, 0.07, 0.45, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-11-20 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(44, 'V-DEMO-5-6-605', 11, 1, 1, 1, 'boleta', 'B001', '3019', 1.14, 0.00, 0.21, 1.35, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-11-08 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(45, 'V-DEMO-5-7-219', 15, 1, 1, 1, 'boleta', 'B001', '9040', 5.76, 0.00, 1.04, 6.80, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-11-15 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(46, 'V-DEMO-5-8-290', 14, 1, 1, 1, 'boleta', 'B001', '3245', 0.89, 0.00, 0.16, 1.05, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-11-19 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(47, 'V-DEMO-5-9-974', 8, 1, 1, 1, 'boleta', 'B001', '4972', 2.03, 0.00, 0.37, 2.40, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-11-23 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(48, 'V-DEMO-5-10-390', 3, 1, 1, 1, 'boleta', 'B001', '1303', 6.23, 0.00, 1.12, 7.35, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-11-05 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(49, 'V-DEMO-6-1-510', 8, 1, 1, 1, 'boleta', 'B001', '1745', 0.68, 0.00, 0.12, 0.80, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-10-02 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(50, 'V-DEMO-6-2-966', 4, 1, 1, 1, 'boleta', 'B001', '8665', 54.24, 0.00, 9.76, 64.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-10-14 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:28'),
	(51, 'V-DEMO-6-3-208', 9, 1, 1, 1, 'boleta', 'B001', '6085', 4.62, 0.00, 0.83, 5.45, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-10-05 05:00:00', '2026-04-30 02:31:28', '2026-04-30 02:31:29'),
	(52, 'V-DEMO-6-4-249', 13, 1, 1, 1, 'boleta', 'B001', '8739', 1.02, 0.00, 0.18, 1.20, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-10-14 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(53, 'V-DEMO-6-5-106', 14, 1, 1, 1, 'boleta', 'B001', '3571', 8.47, 0.00, 1.53, 10.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-10-23 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(54, 'V-DEMO-6-6-880', 15, 1, 1, 1, 'boleta', 'B001', '5478', 1.86, 0.00, 0.34, 2.20, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-10-23 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(55, 'V-DEMO-6-7-937', 6, 1, 1, 1, 'boleta', 'B001', '2708', 2.03, 0.00, 0.37, 2.40, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-10-03 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(56, 'V-DEMO-6-8-744', 4, 1, 1, 1, 'boleta', 'B001', '1535', 135.59, 0.00, 24.41, 160.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-10-05 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(57, 'V-DEMO-7-1-816', 13, 1, 1, 1, 'boleta', 'B001', '9485', 3.81, 0.00, 0.69, 4.50, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-09-21 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(58, 'V-DEMO-7-2-669', 4, 1, 1, 1, 'boleta', 'B001', '3375', 136.27, 0.00, 24.53, 160.80, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-09-12 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(59, 'V-DEMO-7-3-127', 8, 1, 1, 1, 'boleta', 'B001', '6884', 1.44, 0.00, 0.26, 1.70, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-09-20 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(60, 'V-DEMO-7-4-162', 8, 1, 1, 1, 'boleta', 'B001', '4776', 4.07, 0.00, 0.73, 4.80, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-09-07 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(61, 'V-DEMO-7-5-606', 12, 1, 1, 1, 'boleta', 'B001', '7889', 5.08, 0.00, 0.92, 6.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-09-10 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(62, 'V-DEMO-8-1-357', 4, 1, 1, 1, 'boleta', 'B001', '9397', 1.02, 0.00, 0.18, 1.20, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-08-13 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(63, 'V-DEMO-8-2-976', 2, 1, 1, 1, 'boleta', 'B001', '4013', 0.76, 0.00, 0.14, 0.90, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-08-08 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(64, 'V-DEMO-8-3-790', 10, 1, 1, 1, 'boleta', 'B001', '7830', 148.98, 0.00, 26.82, 175.80, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-08-23 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(65, 'V-DEMO-8-4-648', 8, 1, 1, 1, 'boleta', 'B001', '4873', 1.69, 0.00, 0.31, 2.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-08-21 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(66, 'V-DEMO-8-5-699', 2, 1, 1, 1, 'boleta', 'B001', '9295', 136.10, 0.00, 24.50, 160.60, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-08-25 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(67, 'V-DEMO-8-6-897', 13, 1, 1, 1, 'boleta', 'B001', '4128', 34.41, 0.00, 6.19, 40.60, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-08-25 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(68, 'V-DEMO-8-7-882', 5, 1, 1, 1, 'boleta', 'B001', '2889', 120.68, 0.00, 21.72, 142.40, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-08-10 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(69, 'V-DEMO-8-8-966', 5, 1, 1, 1, 'boleta', 'B001', '1758', 237.71, 0.00, 42.79, 280.50, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-08-20 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(70, 'V-DEMO-9-1-366', 2, 1, 1, 1, 'boleta', 'B001', '6038', 5.00, 0.00, 0.90, 5.90, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-07-15 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(71, 'V-DEMO-9-2-810', 11, 1, 1, 1, 'boleta', 'B001', '9183', 3.86, 0.00, 0.69, 4.55, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-07-05 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(72, 'V-DEMO-9-3-953', 12, 1, 1, 1, 'boleta', 'B001', '1137', 81.36, 0.00, 14.64, 96.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-07-14 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(73, 'V-DEMO-9-4-308', 3, 1, 1, 1, 'boleta', 'B001', '2238', 3.56, 0.00, 0.64, 4.20, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-07-11 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(74, 'V-DEMO-9-5-515', 11, 1, 1, 1, 'boleta', 'B001', '4005', 60.34, 0.00, 10.86, 71.20, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-07-13 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(75, 'V-DEMO-9-6-360', 1, 1, 1, 1, 'boleta', 'B001', '8933', 29.66, 0.00, 5.34, 35.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-07-23 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(76, 'V-DEMO-9-7-801', 4, 1, 1, 1, 'boleta', 'B001', '3212', 82.71, 0.00, 14.89, 97.60, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-07-09 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(77, 'V-DEMO-10-1-934', 9, 1, 1, 1, 'boleta', 'B001', '9639', 28.39, 0.00, 5.11, 33.50, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-06-04 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(78, 'V-DEMO-10-2-501', 5, 1, 1, 1, 'boleta', 'B001', '7052', 1.53, 0.00, 0.27, 1.80, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-06-08 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(79, 'V-DEMO-10-3-501', 15, 1, 1, 1, 'boleta', 'B001', '5544', 1.40, 0.00, 0.25, 1.65, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-06-19 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(80, 'V-DEMO-10-4-205', 5, 1, 1, 1, 'boleta', 'B001', '7333', 4.75, 0.00, 0.85, 5.60, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-06-24 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(81, 'V-DEMO-10-5-930', 6, 1, 1, 1, 'boleta', 'B001', '3114', 136.10, 0.00, 24.50, 160.60, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-06-14 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(82, 'V-DEMO-10-6-579', 3, 1, 1, 1, 'boleta', 'B001', '2441', 5.08, 0.00, 0.92, 6.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-06-10 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(83, 'V-DEMO-10-7-810', 6, 1, 1, 1, 'boleta', 'B001', '4266', 89.92, 0.00, 16.18, 106.10, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-06-09 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(84, 'V-DEMO-10-8-425', 2, 1, 1, 1, 'boleta', 'B001', '6098', 5.08, 0.00, 0.92, 6.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-06-05 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(85, 'V-DEMO-10-9-153', 12, 1, 1, 1, 'boleta', 'B001', '9804', 2.46, 0.00, 0.44, 2.90, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-06-14 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(86, 'V-DEMO-10-10-351', 5, 1, 1, 1, 'boleta', 'B001', '4454', 2.03, 0.00, 0.37, 2.40, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-06-13 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(87, 'V-DEMO-11-1-819', 8, 1, 1, 1, 'boleta', 'B001', '6424', 3.43, 0.00, 0.62, 4.05, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-05-23 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(88, 'V-DEMO-11-2-547', 11, 1, 1, 1, 'boleta', 'B001', '6395', 3.39, 0.00, 0.61, 4.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-05-02 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(89, 'V-DEMO-11-3-130', 2, 1, 1, 1, 'boleta', 'B001', '6299', 1.69, 0.00, 0.31, 2.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-05-20 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(90, 'V-DEMO-11-4-663', 12, 1, 1, 1, 'boleta', 'B001', '4348', 1.36, 0.00, 0.24, 1.60, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-05-25 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(91, 'V-DEMO-11-5-859', 7, 1, 1, 1, 'boleta', 'B001', '5678', 89.49, 0.00, 16.11, 105.60, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-05-22 05:00:00', '2026-04-30 02:31:29', '2026-04-30 02:31:29'),
	(92, 'V-DEMO5-1-179', 35, 1, 1, 1, 'boleta', 'B001', '57314', 90.00, 0.00, 16.20, 106.20, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-04 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(93, 'V-DEMO5-2-759', 12, 1, 1, 1, 'boleta', 'B001', '90364', 81.36, 0.00, 14.64, 96.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-02 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(94, 'V-DEMO5-3-562', 3, 1, 1, 1, 'boleta', 'B001', '99720', 121.78, 0.00, 21.92, 143.70, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-10 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(95, 'V-DEMO5-4-678', 29, 1, 1, 1, 'boleta', 'B001', '99925', 136.02, 0.00, 24.48, 160.50, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-03 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(96, 'V-DEMO5-5-186', 30, 1, 1, 1, 'boleta', 'B001', '95591', 3.26, 0.00, 0.59, 3.85, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-01-16 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(97, 'V-DEMO5-6-724', 21, 1, 1, 1, 'boleta', 'B001', '37114', 138.22, 0.00, 24.88, 163.10, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-01-22 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(98, 'V-DEMO5-7-294', 10, 1, 1, 1, 'boleta', 'B001', '20237', 162.71, 0.00, 29.29, 192.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-01-21 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(99, 'V-DEMO5-8-382', 24, 1, 1, 1, 'boleta', 'B001', '69853', 167.29, 0.00, 30.11, 197.40, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-01-24 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(100, 'V-DEMO5-9-262', 9, 1, 1, 1, 'boleta', 'B001', '55404', 36.27, 0.00, 6.53, 42.80, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-03 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(101, 'V-DEMO5-10-733', 5, 1, 1, 1, 'boleta', 'B001', '35685', 58.56, 0.00, 10.54, 69.10, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-04 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(102, 'V-DEMO5-11-655', 9, 1, 1, 1, 'boleta', 'B001', '96902', 92.54, 0.00, 16.66, 109.20, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-05 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(103, 'V-DEMO5-12-807', 24, 1, 1, 1, 'boleta', 'B001', '90611', 136.36, 0.00, 24.54, 160.90, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-12-05 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(104, 'V-DEMO5-13-504', 5, 1, 1, 1, 'boleta', 'B001', '19024', 4.41, 0.00, 0.79, 5.20, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-01-14 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(105, 'V-DEMO5-14-344', 34, 1, 1, 1, 'boleta', 'B001', '48087', 211.69, 0.00, 38.11, 249.80, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-18 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(106, 'V-DEMO5-15-322', 2, 1, 1, 1, 'boleta', 'B001', '76342', 6.86, 0.00, 1.24, 8.10, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-12-21 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(107, 'V-DEMO5-16-136', 12, 1, 1, 1, 'boleta', 'B001', '92813', 1.44, 0.00, 0.26, 1.70, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-12-05 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(108, 'V-DEMO5-17-813', 12, 1, 1, 1, 'boleta', 'B001', '22986', 189.83, 0.00, 34.17, 224.00, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2025-12-26 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(109, 'V-DEMO5-18-501', 35, 1, 1, 1, 'boleta', 'B001', '43987', 136.10, 0.00, 24.50, 160.60, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-18 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(110, 'V-DEMO5-19-779', 33, 1, 1, 1, 'boleta', 'B001', '20845', 108.86, 0.00, 19.59, 128.45, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-17 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(111, 'V-DEMO5-20-516', 28, 1, 1, 1, 'boleta', 'B001', '47757', 2.03, 0.00, 0.37, 2.40, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-03-04 05:00:00', '2026-04-30 02:47:45', '2026-04-30 02:47:45'),
	(112, 'V-20260430021602', NULL, 1, 1, 1, 'boleta', NULL, NULL, 0.80, 0.00, 0.14, 0.94, 'efectivo', 0.00, 0.00, 0, 'emitida', NULL, NULL, NULL, NULL, '2026-04-30 07:16:02', '2026-04-30 07:16:02', '2026-04-30 07:16:02');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
