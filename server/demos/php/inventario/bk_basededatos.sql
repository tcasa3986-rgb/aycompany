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


-- Volcando estructura de base de datos para inventario_ti_laravel
CREATE DATABASE IF NOT EXISTS `inventario_ti_laravel` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `inventario_ti_laravel`;

-- Volcando estructura para tabla inventario_ti_laravel.areas
CREATE TABLE IF NOT EXISTS `areas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `areas_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.areas: ~3 rows (aproximadamente)
DELETE FROM `areas`;
INSERT INTO `areas` (`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 'Sistemas', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(2, 'Contabilidad', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(3, 'Ventas', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47');

-- Volcando estructura para tabla inventario_ti_laravel.asignaciones
CREATE TABLE IF NOT EXISTS `asignaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_equipo` bigint unsigned NOT NULL,
  `id_empleado` bigint unsigned NOT NULL,
  `fecha_entrega` datetime NOT NULL,
  `fecha_devolucion` datetime DEFAULT NULL,
  `estado_asignacion` enum('Activa','Finalizada','Anulada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activa',
  `observaciones_entrega` text COLLATE utf8mb4_unicode_ci,
  `observaciones_devolucion` text COLLATE utf8mb4_unicode_ci,
  `motivo_anulacion` text COLLATE utf8mb4_unicode_ci,
  `acta_firmada_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acta_devolucion_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_devolucion_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_devolucion_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_devolucion_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asignaciones_id_equipo_foreign` (`id_equipo`),
  KEY `asignaciones_id_empleado_foreign` (`id_empleado`),
  CONSTRAINT `asignaciones_id_empleado_foreign` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `asignaciones_id_equipo_foreign` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.asignaciones: ~1 rows (aproximadamente)
DELETE FROM `asignaciones`;
INSERT INTO `asignaciones` (`id`, `id_equipo`, `id_empleado`, `fecha_entrega`, `fecha_devolucion`, `estado_asignacion`, `observaciones_entrega`, `observaciones_devolucion`, `motivo_anulacion`, `acta_firmada_path`, `acta_devolucion_path`, `imagen_devolucion_1`, `imagen_devolucion_2`, `imagen_devolucion_3`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '2026-02-19 00:00:00', '2026-02-19 00:00:00', 'Finalizada', NULL, 'prueba 1', NULL, 'actas_firmadas/dySnIgwAWpFyqoJlzMWqeEBYdhFrkurpNvy19ux2.pdf', 'actas_devolucion/A3t6X1EaN5ZYnWmYE76BiXOrJP7cLSe3usztz4Lx.pdf', NULL, NULL, NULL, '2026-02-19 10:19:00', '2026-02-19 10:34:11');

-- Volcando estructura para tabla inventario_ti_laravel.bajas
CREATE TABLE IF NOT EXISTS `bajas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_equipo` bigint unsigned NOT NULL,
  `fecha_baja` date NOT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `acta_baja_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion_motivo` text COLLATE utf8mb4_unicode_ci,
  `id_usuario_responsable` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bajas_id_equipo_foreign` (`id_equipo`),
  CONSTRAINT `bajas_id_equipo_foreign` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.bajas: ~0 rows (aproximadamente)
DELETE FROM `bajas`;

-- Volcando estructura para tabla inventario_ti_laravel.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.cache: ~0 rows (aproximadamente)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('sistema-inventario-ti-cache-spatie.permission.cache', 'a:3:{s:5:"alias";a:4:{s:1:"a";s:2:"id";s:1:"b";s:4:"name";s:1:"c";s:10:"guard_name";s:1:"r";s:5:"roles";}s:11:"permissions";a:57:{i:0;a:4:{s:1:"a";i:1;s:1:"b";s:10:"users.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:1;a:4:{s:1:"a";i:2;s:1:"b";s:12:"users.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:2;a:4:{s:1:"a";i:3;s:1:"b";s:10:"users.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:3;a:4:{s:1:"a";i:4;s:1:"b";s:12:"users.toggle";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:4;a:4:{s:1:"a";i:5;s:1:"b";s:10:"roles.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:5;a:4:{s:1:"a";i:6;s:1:"b";s:12:"roles.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:6;a:4:{s:1:"a";i:7;s:1:"b";s:10:"roles.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:7;a:4:{s:1:"a";i:8;s:1:"b";s:12:"roles.delete";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:8;a:4:{s:1:"a";i:9;s:1:"b";s:12:"equipos.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:9;a:4:{s:1:"a";i:10;s:1:"b";s:14:"equipos.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:10;a:4:{s:1:"a";i:11;s:1:"b";s:12:"equipos.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:11;a:4:{s:1:"a";i:12;s:1:"b";s:14:"equipos.delete";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:12;a:4:{s:1:"a";i:13;s:1:"b";s:14:"equipos.export";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:13;a:4:{s:1:"a";i:14;s:1:"b";s:14:"empleados.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:14;a:4:{s:1:"a";i:15;s:1:"b";s:16:"empleados.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:15;a:4:{s:1:"a";i:16;s:1:"b";s:14:"empleados.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:16;a:4:{s:1:"a";i:17;s:1:"b";s:16:"empleados.toggle";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:17;a:4:{s:1:"a";i:18;s:1:"b";s:16:"empleados.export";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:18;a:4:{s:1:"a";i:19;s:1:"b";s:17:"asignaciones.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:19;a:4:{s:1:"a";i:20;s:1:"b";s:19:"asignaciones.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:20;a:4:{s:1:"a";i:21;s:1:"b";s:17:"asignaciones.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:21;a:4:{s:1:"a";i:22;s:1:"b";s:18:"asignaciones.annul";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:22;a:4:{s:1:"a";i:23;s:1:"b";s:19:"asignaciones.return";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:23;a:4:{s:1:"a";i:24;s:1:"b";s:19:"asignaciones.export";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:24;a:4:{s:1:"a";i:25;s:1:"b";s:17:"reparaciones.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:25;a:4:{s:1:"a";i:26;s:1:"b";s:19:"reparaciones.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:26;a:4:{s:1:"a";i:27;s:1:"b";s:17:"reparaciones.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:27;a:4:{s:1:"a";i:28;s:1:"b";s:19:"reparaciones.delete";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:28;a:4:{s:1:"a";i:29;s:1:"b";s:10:"bajas.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:29;a:4:{s:1:"a";i:30;s:1:"b";s:12:"bajas.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:30;a:4:{s:1:"a";i:31;s:1:"b";s:10:"bajas.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:31;a:4:{s:1:"a";i:32;s:1:"b";s:12:"bajas.delete";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:32;a:4:{s:1:"a";i:33;s:1:"b";s:15:"sucursales.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:33;a:4:{s:1:"a";i:34;s:1:"b";s:17:"sucursales.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:34;a:4:{s:1:"a";i:35;s:1:"b";s:15:"sucursales.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:35;a:4:{s:1:"a";i:36;s:1:"b";s:17:"sucursales.toggle";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:36;a:4:{s:1:"a";i:37;s:1:"b";s:10:"areas.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:37;a:4:{s:1:"a";i:38;s:1:"b";s:12:"areas.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:38;a:4:{s:1:"a";i:39;s:1:"b";s:10:"areas.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:39;a:4:{s:1:"a";i:40;s:1:"b";s:12:"areas.toggle";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:40;a:4:{s:1:"a";i:41;s:1:"b";s:11:"cargos.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:41;a:4:{s:1:"a";i:42;s:1:"b";s:13:"cargos.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:42;a:4:{s:1:"a";i:43;s:1:"b";s:11:"cargos.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:43;a:4:{s:1:"a";i:44;s:1:"b";s:13:"cargos.toggle";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:44;a:4:{s:1:"a";i:45;s:1:"b";s:11:"marcas.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:45;a:4:{s:1:"a";i:46;s:1:"b";s:13:"marcas.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:46;a:4:{s:1:"a";i:47;s:1:"b";s:11:"marcas.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:47;a:4:{s:1:"a";i:48;s:1:"b";s:13:"marcas.toggle";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:48;a:4:{s:1:"a";i:49;s:1:"b";s:12:"modelos.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:49;a:4:{s:1:"a";i:50;s:1:"b";s:14:"modelos.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:50;a:4:{s:1:"a";i:51;s:1:"b";s:12:"modelos.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:51;a:4:{s:1:"a";i:52;s:1:"b";s:14:"modelos.toggle";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:52;a:4:{s:1:"a";i:53;s:1:"b";s:17:"tipos_equipo.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:53;a:4:{s:1:"a";i:54;s:1:"b";s:19:"tipos_equipo.create";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:54;a:4:{s:1:"a";i:55;s:1:"b";s:17:"tipos_equipo.edit";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:55;a:4:{s:1:"a";i:56;s:1:"b";s:19:"tipos_equipo.toggle";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:56;a:4:{s:1:"a";i:57;s:1:"b";s:13:"reportes.view";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}}s:5:"roles";a:1:{i:0;a:3:{s:1:"a";i:1;s:1:"b";s:13:"Administrador";s:1:"c";s:3:"web";}}}', 1771521679);

-- Volcando estructura para tabla inventario_ti_laravel.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Volcando estructura para tabla inventario_ti_laravel.cargos
CREATE TABLE IF NOT EXISTS `cargos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_area` bigint unsigned NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cargos_nombre_unique` (`nombre`),
  KEY `cargos_id_area_foreign` (`id_area`),
  CONSTRAINT `cargos_id_area_foreign` FOREIGN KEY (`id_area`) REFERENCES `areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.cargos: ~4 rows (aproximadamente)
DELETE FROM `cargos`;
INSERT INTO `cargos` (`id`, `id_area`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Desarrollador', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(2, 1, 'Soporte Técnico', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(3, 2, 'Contador', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(4, 3, 'Ejecutivo de Ventas', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47');

-- Volcando estructura para tabla inventario_ti_laravel.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuracion_clave_unique` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.configuracion: ~0 rows (aproximadamente)
DELETE FROM `configuracion`;
INSERT INTO `configuracion` (`id`, `clave`, `valor`, `created_at`, `updated_at`) VALUES
	(1, 'moneda_simbolo', 'S/', '2026-02-18 22:19:44', '2026-02-18 22:19:44');

-- Volcando estructura para tabla inventario_ti_laravel.empleados
CREATE TABLE IF NOT EXISTS `empleados` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` bigint unsigned NOT NULL,
  `dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_cargo` bigint unsigned DEFAULT NULL,
  `id_area` bigint unsigned DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empleados_dni_unique` (`dni`),
  KEY `empleados_id_sucursal_foreign` (`id_sucursal`),
  KEY `empleados_id_cargo_foreign` (`id_cargo`),
  KEY `empleados_id_area_foreign` (`id_area`),
  CONSTRAINT `empleados_id_area_foreign` FOREIGN KEY (`id_area`) REFERENCES `areas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `empleados_id_cargo_foreign` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `empleados_id_sucursal_foreign` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.empleados: ~0 rows (aproximadamente)
DELETE FROM `empleados`;
INSERT INTO `empleados` (`id`, `id_sucursal`, `dni`, `nombres`, `apellidos`, `id_cargo`, `id_area`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 1, '44444444', 'Víctor', 'Ramos', 1, 1, 'Activo', '2026-02-19 10:18:24', '2026-02-19 10:18:24');

-- Volcando estructura para tabla inventario_ti_laravel.equipos
CREATE TABLE IF NOT EXISTS `equipos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` bigint unsigned NOT NULL,
  `codigo_inventario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_tipo_equipo` bigint unsigned NOT NULL,
  `id_marca` bigint unsigned NOT NULL,
  `id_modelo` bigint unsigned NOT NULL,
  `numero_serie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caracteristicas` text COLLATE utf8mb4_unicode_ci,
  `tipo_adquisicion` enum('Propio','Arrendado','Prestamo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_adquisicion` date DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `numero_guia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archivo_guia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proveedor` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('Disponible','Asignado','En Reparacion','De Baja') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `equipos_codigo_inventario_unique` (`codigo_inventario`),
  UNIQUE KEY `equipos_numero_serie_unique` (`numero_serie`),
  KEY `equipos_id_sucursal_foreign` (`id_sucursal`),
  KEY `equipos_id_tipo_equipo_foreign` (`id_tipo_equipo`),
  KEY `equipos_id_marca_foreign` (`id_marca`),
  KEY `equipos_id_modelo_foreign` (`id_modelo`),
  CONSTRAINT `equipos_id_marca_foreign` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id`),
  CONSTRAINT `equipos_id_modelo_foreign` FOREIGN KEY (`id_modelo`) REFERENCES `modelos` (`id`),
  CONSTRAINT `equipos_id_sucursal_foreign` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `equipos_id_tipo_equipo_foreign` FOREIGN KEY (`id_tipo_equipo`) REFERENCES `tipos_equipo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.equipos: ~2 rows (aproximadamente)
DELETE FROM `equipos`;
INSERT INTO `equipos` (`id`, `id_sucursal`, `codigo_inventario`, `id_tipo_equipo`, `id_marca`, `id_modelo`, `numero_serie`, `caracteristicas`, `tipo_adquisicion`, `fecha_adquisicion`, `costo`, `numero_guia`, `archivo_guia`, `proveedor`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 'INVENTARIO2026- 001', 1, 1, 1, 'SERIE-001', NULL, 'Propio', '2026-02-19', 3500.00, '001-0001', 'equipos/guias/FTacc04b02lWfUEBAfxNb13GNJDbmIGksWu5NLWD.pdf', 'PROVEEDOR 1', 'Disponible', NULL, '2026-02-19 10:16:19', '2026-02-19 10:51:05'),
	(2, 1, 'INVENTARIO2026- 002', 4, 2, 6, 'SERIE-002', NULL, 'Arrendado', '2026-02-19', 300.00, '001-0002', NULL, 'PROVEEDOR 1', 'Disponible', NULL, '2026-02-19 17:53:31', '2026-02-19 17:53:31');

-- Volcando estructura para tabla inventario_ti_laravel.failed_jobs
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

-- Volcando datos para la tabla inventario_ti_laravel.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Volcando estructura para tabla inventario_ti_laravel.jobs
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

-- Volcando datos para la tabla inventario_ti_laravel.jobs: ~0 rows (aproximadamente)
DELETE FROM `jobs`;

-- Volcando estructura para tabla inventario_ti_laravel.job_batches
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

-- Volcando datos para la tabla inventario_ti_laravel.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Volcando estructura para tabla inventario_ti_laravel.marcas
CREATE TABLE IF NOT EXISTS `marcas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marcas_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.marcas: ~3 rows (aproximadamente)
DELETE FROM `marcas`;
INSERT INTO `marcas` (`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 'Dell', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(2, 'HP', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(3, 'Lenovo', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47');

-- Volcando estructura para tabla inventario_ti_laravel.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.migrations: ~19 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000001_create_cache_table', 1),
	(2, '0001_01_01_000002_create_jobs_table', 1),
	(3, '2026_02_17_141320_create_sucursales_table', 1),
	(4, '2026_02_17_141322_create_tipos_equipo_table', 1),
	(5, '2026_02_17_141324_create_marcas_table', 1),
	(6, '2026_02_17_141326_create_modelos_table', 1),
	(7, '2026_02_17_141328_create_areas_table', 1),
	(8, '2026_02_17_141330_create_cargos_table', 1),
	(9, '2026_02_17_141356_create_equipos_table', 1),
	(10, '2026_02_17_141358_create_empleados_table', 1),
	(11, '2026_02_17_141400_create_asignaciones_table', 1),
	(12, '2026_02_17_141402_create_reparaciones_table', 1),
	(13, '2026_02_17_141404_create_bajas_table', 1),
	(14, '2026_02_17_141409_create_configuracion_table', 1),
	(15, '2026_02_17_141500_create_users_table', 1),
	(16, '2026_02_18_003428_add_motivo_anulacion_to_asignaciones_table', 1),
	(17, '2026_02_18_165820_create_permission_tables', 1),
	(18, '2026_02_18_172524_create_settings_table', 2),
	(19, '2026_02_18_174256_add_cost_and_guide_to_equipos_table', 3),
	(20, '2026_02_19_050418_add_currency_symbol_to_settings_table', 4),
	(21, '2026_02_19_070000_modify_estado_reparacion_enum', 5),
	(22, '2026_02_19_071500_update_reparaciones_table_columns', 6);

-- Volcando estructura para tabla inventario_ti_laravel.modelos
CREATE TABLE IF NOT EXISTS `modelos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_marca` bigint unsigned NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modelos_id_marca_foreign` (`id_marca`),
  CONSTRAINT `modelos_id_marca_foreign` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.modelos: ~6 rows (aproximadamente)
DELETE FROM `modelos`;
INSERT INTO `modelos` (`id`, `id_marca`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Latitude 5420', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(2, 1, 'OptiPlex 7090', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(3, 2, 'EliteBook 840', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(4, 2, 'ProDesk 600', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(5, 3, 'ThinkPad X1', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(6, 2, 'Laser Jet', 'Activo', '2026-02-19 17:52:36', '2026-02-19 17:52:36');

-- Volcando estructura para tabla inventario_ti_laravel.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.model_has_permissions: ~0 rows (aproximadamente)
DELETE FROM `model_has_permissions`;

-- Volcando estructura para tabla inventario_ti_laravel.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.model_has_roles: ~0 rows (aproximadamente)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 1);

-- Volcando estructura para tabla inventario_ti_laravel.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Volcando estructura para tabla inventario_ti_laravel.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.permissions: ~57 rows (aproximadamente)
DELETE FROM `permissions`;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'users.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(2, 'users.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(3, 'users.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(4, 'users.toggle', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(5, 'roles.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(6, 'roles.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(7, 'roles.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(8, 'roles.delete', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(9, 'equipos.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(10, 'equipos.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(11, 'equipos.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(12, 'equipos.delete', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(13, 'equipos.export', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(14, 'empleados.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(15, 'empleados.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(16, 'empleados.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(17, 'empleados.toggle', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(18, 'empleados.export', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(19, 'asignaciones.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(20, 'asignaciones.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(21, 'asignaciones.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(22, 'asignaciones.annul', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(23, 'asignaciones.return', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(24, 'asignaciones.export', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(25, 'reparaciones.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(26, 'reparaciones.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(27, 'reparaciones.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(28, 'reparaciones.delete', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(29, 'bajas.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(30, 'bajas.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(31, 'bajas.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(32, 'bajas.delete', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(33, 'sucursales.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(34, 'sucursales.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(35, 'sucursales.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(36, 'sucursales.toggle', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(37, 'areas.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(38, 'areas.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(39, 'areas.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(40, 'areas.toggle', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(41, 'cargos.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(42, 'cargos.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(43, 'cargos.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(44, 'cargos.toggle', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(45, 'marcas.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(46, 'marcas.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(47, 'marcas.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(48, 'marcas.toggle', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(49, 'modelos.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(50, 'modelos.create', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(51, 'modelos.edit', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(52, 'modelos.toggle', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(53, 'tipos_equipo.view', 'web', '2026-02-18 22:19:45', '2026-02-18 22:19:45'),
	(54, 'tipos_equipo.create', 'web', '2026-02-18 22:19:46', '2026-02-18 22:19:46'),
	(55, 'tipos_equipo.edit', 'web', '2026-02-18 22:19:46', '2026-02-18 22:19:46'),
	(56, 'tipos_equipo.toggle', 'web', '2026-02-18 22:19:46', '2026-02-18 22:19:46'),
	(57, 'reportes.view', 'web', '2026-02-18 22:19:46', '2026-02-18 22:19:46');

-- Volcando estructura para tabla inventario_ti_laravel.reparaciones
CREATE TABLE IF NOT EXISTS `reparaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_equipo` bigint unsigned NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `tecnico_asignado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_salida` date DEFAULT NULL,
  `descripcion_problema` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion_solucion` text COLLATE utf8mb4_unicode_ci,
  `proveedor_servicio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costo_estimado` decimal(10,2) DEFAULT NULL,
  `costo_real` decimal(10,2) DEFAULT NULL,
  `observaciones_salida` text COLLATE utf8mb4_unicode_ci,
  `estado_reparacion` enum('Pendiente','En Proceso','Completada','Cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reparaciones_id_equipo_foreign` (`id_equipo`),
  CONSTRAINT `reparaciones_id_equipo_foreign` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.reparaciones: ~1 rows (aproximadamente)
DELETE FROM `reparaciones`;
INSERT INTO `reparaciones` (`id`, `id_equipo`, `fecha_ingreso`, `tecnico_asignado`, `fecha_salida`, `descripcion_problema`, `descripcion_solucion`, `proveedor_servicio`, `costo_estimado`, `costo_real`, `observaciones_salida`, `estado_reparacion`, `created_at`, `updated_at`) VALUES
	(1, 1, '2026-02-19', 'Carlos', '2026-02-19', 'mantenimiento preventivo', NULL, NULL, 70.00, 90.00, NULL, 'Cancelada', '2026-02-19 10:44:01', '2026-02-19 10:51:17');

-- Volcando estructura para tabla inventario_ti_laravel.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.roles: ~0 rows (aproximadamente)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'web', '2026-02-18 22:19:46', '2026-02-18 22:19:46');

-- Volcando estructura para tabla inventario_ti_laravel.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.role_has_permissions: ~57 rows (aproximadamente)
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
	(30, 1),
	(31, 1),
	(32, 1),
	(33, 1),
	(34, 1),
	(35, 1),
	(36, 1),
	(37, 1),
	(38, 1),
	(39, 1),
	(40, 1),
	(41, 1),
	(42, 1),
	(43, 1),
	(44, 1),
	(45, 1),
	(46, 1),
	(47, 1),
	(48, 1),
	(49, 1),
	(50, 1),
	(51, 1),
	(52, 1),
	(53, 1),
	(54, 1),
	(55, 1),
	(56, 1),
	(57, 1);

-- Volcando estructura para tabla inventario_ti_laravel.sessions
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

-- Volcando datos para la tabla inventario_ti_laravel.sessions: ~2 rows (aproximadamente)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('j3D2GJDGj74WCzcYdur8je8eEaMkykKix5IA2aPf', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQmM5S01kcmVtWFFZWUF4dFRZV2lKajJRaXZicWJyb1F6RXJyT0V0TiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYmFqYXMiO3M6NToicm91dGUiO3M6MTE6ImJhamFzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1771480409),
	('vvB7fGns6VVShp252b5kErUowc7GY5j0KrZpr6FD', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVUcyYUh4UE8yeXhlUTJjd1Bkb1JzNTdMSVQ4Y1Z3UTRRRXhaWUJxMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1771505641);

-- Volcando estructura para tabla inventario_ti_laravel.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_ruc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_symbol` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'S/',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.settings: ~1 rows (aproximadamente)
DELETE FROM `settings`;
INSERT INTO `settings` (`id`, `empresa_nombre`, `empresa_direccion`, `empresa_telefono`, `empresa_ruc`, `empresa_logo`, `currency_symbol`, `created_at`, `updated_at`) VALUES
	(1, 'Mi Empresa S.A.C.', 'Lima Perú', '555-1234', '20000000001', 'logos/yb7Iufd0LoNVz0s2hzUbPHtScZCGn6xyszwLGiQn.png', 'S/', '2026-02-18 22:36:26', '2026-02-19 10:06:10');

-- Volcando estructura para tabla inventario_ti_laravel.sucursales
CREATE TABLE IF NOT EXISTS `sucursales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sucursales_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.sucursales: ~2 rows (aproximadamente)
DELETE FROM `sucursales`;
INSERT INTO `sucursales` (`id`, `nombre`, `direccion`, `telefono`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 'Sucursal Principal', 'Av. Principal 123', '555-0001', 'Activo', '2026-02-18 22:19:46', '2026-02-18 22:19:46'),
	(2, 'Sucursal Secundaria', 'Calle Secundaria 456', '555-0002', 'Activo', '2026-02-18 22:19:46', '2026-02-18 22:19:46');

-- Volcando estructura para tabla inventario_ti_laravel.tipos_equipo
CREATE TABLE IF NOT EXISTS `tipos_equipo` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_equipo_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.tipos_equipo: ~4 rows (aproximadamente)
DELETE FROM `tipos_equipo`;
INSERT INTO `tipos_equipo` (`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 'Laptop', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(2, 'Desktop', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(3, 'Monitor', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47'),
	(4, 'Impresora', 'Activo', '2026-02-18 22:19:47', '2026-02-18 22:19:47');

-- Volcando estructura para tabla inventario_ti_laravel.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_id_sucursal_foreign` (`id_sucursal`),
  CONSTRAINT `users_id_sucursal_foreign` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti_laravel.users: ~2 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `id_sucursal`, `name`, `email`, `email_verified_at`, `password`, `activo`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'Administrador', 'admin@inventario.com', NULL, '$2y$12$msrjnfus0mVJClNen/DSEOiCVITg3EYknQbDGcnteooFFlOpxhf9i', 1, NULL, '2026-02-18 22:19:46', '2026-02-18 22:19:46'),
	(2, 1, 'Usuario Normal', 'usuario@inventario.com', NULL, '$2y$12$RIyz/hS1DqAiDb4TcC6f6u0EXoNipShSYTdlUeIgSH7sv6uVEHEIq', 1, NULL, '2026-02-18 22:19:47', '2026-02-18 22:19:47');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
