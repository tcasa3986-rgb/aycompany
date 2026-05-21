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


-- Volcando estructura de base de datos para laboratorio_clinico
CREATE DATABASE IF NOT EXISTS `laboratorio_clinico` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `laboratorio_clinico`;

-- Volcando estructura para tabla laboratorio_clinico.areas_laboratorio
CREATE TABLE IF NOT EXISTS `areas_laboratorio` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `areas_laboratorio_codigo_unique` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.areas_laboratorio: ~8 rows (aproximadamente)
DELETE FROM `areas_laboratorio`;
INSERT INTO `areas_laboratorio` (`id`, `nombre`, `codigo`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Hematología', 'HEM', 'Análisis de sangre y componentes sanguíneos', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 'Bioquímica', 'BIO', 'Análisis bioquímico y metabólico', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 'Microbiología', 'MIC', 'Cultivos y estudios microbiológicos', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 'Inmunología', 'INM', 'Pruebas inmunológicas y serológicas', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(5, 'Uroanálisis', 'URO', 'Análisis de orina y sedimento urinario', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(6, 'Parasitología', 'PAR', 'Estudio de parásitos intestinales', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(7, 'Hormonología', 'HOR', 'Dosaje hormonal y marcadores tumorales', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(8, 'Toxicología', 'TOX', 'Análisis toxicológicos', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.cache: ~0 rows (aproximadamente)
DELETE FROM `cache`;

-- Volcando estructura para tabla laboratorio_clinico.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Volcando estructura para tabla laboratorio_clinico.citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paciente_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `fecha_hora` datetime NOT NULL,
  `tipo_atencion` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Consulta',
  `estado` enum('Programada','Confirmada','Atendida','Cancelada','No asistió') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Programada',
  `motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `citas_paciente_id_foreign` (`paciente_id`),
  KEY `citas_medico_id_foreign` (`medico_id`),
  CONSTRAINT `citas_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `medicos_referidores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `citas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.citas: ~0 rows (aproximadamente)
DELETE FROM `citas`;

-- Volcando estructura para tabla laboratorio_clinico.configuraciones
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tipo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'texto',
  `descripcion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuraciones_clave_unique` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.configuraciones: ~16 rows (aproximadamente)
DELETE FROM `configuraciones`;
INSERT INTO `configuraciones` (`id`, `clave`, `valor`, `tipo`, `descripcion`, `created_at`, `updated_at`) VALUES
	(1, 'nombre_laboratorio', 'Laboratorio Clínico LabSalud', 'texto', 'Nombre del laboratorio', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 'ruc', '20123456789', 'texto', 'RUC del laboratorio', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 'direccion', 'Av. Javier Prado 1234, San Isidro, Lima', 'texto', 'Dirección', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 'telefono', '01-2345678', 'texto', 'Teléfono principal', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(5, 'email', 'info@labsalud.com', 'texto', 'Email de contacto', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(6, 'igv_porcentaje', '18', 'numero', 'Porcentaje de IGV', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(7, 'horario_atencion', 'Lun-Vie 7:00-19:00 | Sáb 7:00-14:00', 'texto', 'Horario de atención', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(8, 'lab_nombre', 'LabSalud Clínico', 'texto', 'Nombre del laboratorio', '2026-04-20 16:57:46', '2026-04-20 16:57:46'),
	(9, 'lab_ruc', '', 'texto', 'RUC del laboratorio', '2026-04-20 16:57:46', '2026-04-20 16:57:46'),
	(10, 'lab_direccion', '', 'texto', 'Dirección del laboratorio', '2026-04-20 16:57:46', '2026-04-20 16:57:46'),
	(11, 'lab_telefono', '', 'texto', 'Teléfono de contacto', '2026-04-20 16:57:46', '2026-04-20 16:57:46'),
	(12, 'lab_email', '', 'texto', 'Correo electrónico', '2026-04-20 16:57:46', '2026-04-20 16:57:46'),
	(13, 'lab_ciudad', 'Lima', 'texto', 'Ciudad', '2026-04-20 16:57:46', '2026-04-20 16:57:46'),
	(14, 'moneda_simbolo', 'S/', 'texto', 'Símbolo de moneda', '2026-04-20 16:57:46', '2026-04-20 16:57:46'),
	(15, 'dias_entrega', '1', 'numero', 'Días estándar de entrega de resultados', '2026-04-20 16:57:46', '2026-04-20 16:57:46'),
	(16, 'pie_resultado', 'Los resultados son confidenciales y de uso médico exclusivo.', 'texto', 'Texto al pie del reporte de resultados', '2026-04-20 16:57:46', '2026-04-20 16:57:46');

-- Volcando estructura para tabla laboratorio_clinico.convenios
CREATE TABLE IF NOT EXISTS `convenios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aseguradora',
  `descuento_porcentaje` decimal(5,2) NOT NULL DEFAULT '0.00',
  `condiciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `contacto_nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.convenios: ~3 rows (aproximadamente)
DELETE FROM `convenios`;
INSERT INTO `convenios` (`id`, `nombre`, `ruc`, `tipo`, `descuento_porcentaje`, `condiciones`, `contacto_nombre`, `contacto_telefono`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Rímac Seguros', '20102009749', 'Aseguradora', 15.00, NULL, 'Carmen Vega', '01-4111111', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 'Pacifico Seguros', '20112056913', 'Aseguradora', 10.00, NULL, 'Pedro Ruiz', '01-5131313', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 'Municipalidad de Miraflores', '20131312955', 'Empresa', 20.00, NULL, 'Rosa Salinas', '01-6170000', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.facturas
CREATE TABLE IF NOT EXISTS `facturas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `orden_id` bigint unsigned NOT NULL,
  `numero_factura` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_comprobante` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Boleta',
  `convenio_id` bigint unsigned DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `igv` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `estado` enum('Emitida','Pagada','Anulada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Emitida',
  `user_id` bigint unsigned NOT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facturas_numero_factura_unique` (`numero_factura`),
  KEY `facturas_orden_id_foreign` (`orden_id`),
  KEY `facturas_convenio_id_foreign` (`convenio_id`),
  KEY `facturas_user_id_foreign` (`user_id`),
  CONSTRAINT `facturas_convenio_id_foreign` FOREIGN KEY (`convenio_id`) REFERENCES `convenios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `facturas_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `facturas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.facturas: ~4 rows (aproximadamente)
DELETE FROM `facturas`;
INSERT INTO `facturas` (`id`, `orden_id`, `numero_factura`, `tipo_comprobante`, `convenio_id`, `subtotal`, `descuento`, `igv`, `total`, `estado`, `user_id`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 5, 'FAC-000005', 'Boleta', NULL, 80.00, 0.00, 0.00, 80.00, 'Pagada', 2, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 7, 'FAC-000007', 'Boleta', NULL, 45.00, 0.00, 0.00, 45.00, 'Pagada', 2, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 9, 'FAC-000009', 'Boleta', NULL, 59.00, 0.00, 0.00, 59.00, 'Pagada', 2, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 10, 'FAC-000010', 'Boleta', NULL, 82.00, 0.00, 0.00, 82.00, 'Pagada', 2, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Volcando estructura para tabla laboratorio_clinico.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.jobs: ~0 rows (aproximadamente)
DELETE FROM `jobs`;

-- Volcando estructura para tabla laboratorio_clinico.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Volcando estructura para tabla laboratorio_clinico.medicos_referidores
CREATE TABLE IF NOT EXISTS `medicos_referidores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cmp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombres` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `especialidad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institucion` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `medicos_referidores_cmp_unique` (`cmp`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.medicos_referidores: ~5 rows (aproximadamente)
DELETE FROM `medicos_referidores`;
INSERT INTO `medicos_referidores` (`id`, `cmp`, `nombres`, `apellidos`, `especialidad`, `telefono`, `email`, `institucion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'CMP-12345', 'Roberto', 'Sánchez Torres', 'Medicina General', '987654321', 'rsanchez@clinica.com', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 'CMP-23456', 'Ana María', 'López Castillo', 'Endocrinología', '987654322', 'alopez@clinica.com', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 'CMP-34567', 'Jorge', 'Vargas Ríos', 'Cardiología', '987654323', 'jvargas@clinica.com', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 'CMP-45678', 'Patricia', 'Huamán Díaz', 'Nefrología', '987654324', 'phuaman@clinica.com', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(5, 'CMP-56789', 'Luis', 'Flores Mamani', 'Gastroenterología', '987654325', 'lflores@clinica.com', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.migrations: ~19 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_04_12_062727_create_permission_tables', 1),
	(5, '2026_04_12_062838_create_areas_laboratorio_table', 1),
	(6, '2026_04_12_062838_create_pruebas_table', 1),
	(7, '2026_04_12_062839_create_pacientes_table', 1),
	(8, '2026_04_12_062840_create_convenios_table', 1),
	(9, '2026_04_12_062840_create_medicos_referidores_table', 1),
	(10, '2026_04_12_062841_create_citas_table', 1),
	(11, '2026_04_12_062841_create_ordenes_table', 1),
	(12, '2026_04_12_062842_create_orden_detalles_table', 1),
	(13, '2026_04_12_062843_create_muestras_table', 1),
	(14, '2026_04_12_062843_create_resultados_table', 1),
	(15, '2026_04_12_062844_create_valores_criticos_table', 1),
	(16, '2026_04_12_062845_create_reactivos_table', 1),
	(17, '2026_04_12_062846_create_facturas_table', 1),
	(18, '2026_04_12_062847_create_pagos_table', 1),
	(19, '2026_04_12_062848_create_configuraciones_table', 1);

-- Volcando estructura para tabla laboratorio_clinico.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.model_has_permissions: ~0 rows (aproximadamente)
DELETE FROM `model_has_permissions`;

-- Volcando estructura para tabla laboratorio_clinico.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.model_has_roles: ~3 rows (aproximadamente)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(2, 'App\\Models\\User', 2),
	(3, 'App\\Models\\User', 3);

-- Volcando estructura para tabla laboratorio_clinico.muestras
CREATE TABLE IF NOT EXISTS `muestras` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `orden_id` bigint unsigned NOT NULL,
  `codigo_muestra` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_muestra` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_toma` datetime NOT NULL,
  `tomado_por` bigint unsigned DEFAULT NULL,
  `estado` enum('Recibida','En análisis','Analizada','Rechazada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Recibida',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `muestras_codigo_muestra_unique` (`codigo_muestra`),
  KEY `muestras_orden_id_foreign` (`orden_id`),
  KEY `muestras_tomado_por_foreign` (`tomado_por`),
  CONSTRAINT `muestras_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `muestras_tomado_por_foreign` FOREIGN KEY (`tomado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.muestras: ~32 rows (aproximadamente)
DELETE FROM `muestras`;
INSERT INTO `muestras` (`id`, `orden_id`, `codigo_muestra`, `tipo_muestra`, `fecha_toma`, `tomado_por`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 'MUE-00001001', 'Sangre venosa', '2026-04-11 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 1, 'MUE-00001007', 'Sangre venosa', '2026-04-11 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 1, 'MUE-00001008', 'Sangre venosa', '2026-04-11 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 1, 'MUE-00001017', 'Sangre venosa', '2026-04-11 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(5, 2, 'MUE-00002008', 'Sangre venosa', '2026-04-09 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(6, 2, 'MUE-00002009', 'Sangre venosa', '2026-04-09 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(7, 2, 'MUE-00002010', 'Sangre venosa', '2026-04-09 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(8, 2, 'MUE-00002014', 'Orina chorro medio', '2026-04-09 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(9, 3, 'MUE-00003007', 'Sangre venosa', '2026-04-15 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(10, 3, 'MUE-00003018', 'Sangre venosa', '2026-04-15 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(11, 4, 'MUE-00004010', 'Sangre venosa', '2026-04-05 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(12, 4, 'MUE-00004011', 'Sangre venosa', '2026-04-05 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(13, 5, 'MUE-00005001', 'Sangre venosa', '2026-04-11 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(14, 5, 'MUE-00005003', 'Sangre venosa', '2026-04-11 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(15, 5, 'MUE-00005014', 'Orina chorro medio', '2026-04-11 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(16, 6, 'MUE-00006001', 'Sangre venosa', '2026-03-30 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(17, 6, 'MUE-00006005', 'Sangre venosa', '2026-03-30 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(18, 6, 'MUE-00006007', 'Sangre venosa', '2026-03-30 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(19, 7, 'MUE-00007003', 'Sangre venosa', '2026-03-24 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(20, 7, 'MUE-00007011', 'Sangre venosa', '2026-03-24 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(21, 8, 'MUE-00008002', 'Sangre venosa', '2026-03-27 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(22, 8, 'MUE-00008010', 'Sangre venosa', '2026-03-27 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(23, 8, 'MUE-00008011', 'Sangre venosa', '2026-03-27 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(24, 8, 'MUE-00008012', 'Sangre venosa', '2026-03-27 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(25, 9, 'MUE-00009003', 'Sangre venosa', '2026-03-21 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(26, 9, 'MUE-00009005', 'Sangre venosa', '2026-03-21 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(27, 9, 'MUE-00009008', 'Sangre venosa', '2026-03-21 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(28, 9, 'MUE-00009015', 'Heces', '2026-03-21 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(29, 10, 'MUE-00010006', 'Sangre venosa', '2026-04-19 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(30, 10, 'MUE-00010008', 'Sangre venosa', '2026-04-19 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(31, 10, 'MUE-00010013', 'Orina chorro medio', '2026-04-19 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(32, 10, 'MUE-00010017', 'Sangre venosa', '2026-04-19 12:57:07', 3, 'Analizada', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.ordenes
CREATE TABLE IF NOT EXISTS `ordenes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_orden` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `convenio_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `diagnostico_presuntivo` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('Pendiente','En proceso','Completado','Entregado','Anulado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `prioridad` enum('Normal','Urgente','Emergencia') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Normal',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pagado` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ordenes_numero_orden_unique` (`numero_orden`),
  KEY `ordenes_paciente_id_foreign` (`paciente_id`),
  KEY `ordenes_medico_id_foreign` (`medico_id`),
  KEY `ordenes_convenio_id_foreign` (`convenio_id`),
  KEY `ordenes_user_id_foreign` (`user_id`),
  CONSTRAINT `ordenes_convenio_id_foreign` FOREIGN KEY (`convenio_id`) REFERENCES `convenios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ordenes_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `medicos_referidores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ordenes_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ordenes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.ordenes: ~10 rows (aproximadamente)
DELETE FROM `ordenes`;
INSERT INTO `ordenes` (`id`, `numero_orden`, `paciente_id`, `medico_id`, `convenio_id`, `user_id`, `fecha_registro`, `diagnostico_presuntivo`, `estado`, `prioridad`, `subtotal`, `descuento`, `total`, `pagado`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 'ORD-000001', 9, 3, NULL, 2, '2026-04-11 11:57:07', 'Control de rutina', 'Pendiente', 'Urgente', 130.00, 0.00, 130.00, 0, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 'ORD-000002', 1, 5, NULL, 2, '2026-04-09 11:57:07', 'Control de rutina', 'En proceso', 'Normal', 113.00, 0.00, 113.00, 0, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 'ORD-000003', 5, 2, NULL, 2, '2026-04-15 11:57:07', 'Control de rutina', 'Pendiente', 'Normal', 70.00, 0.00, 70.00, 0, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 'ORD-000004', 9, 1, NULL, 2, '2026-04-05 11:57:07', 'Control de rutina', 'Pendiente', 'Normal', 48.00, 0.00, 48.00, 0, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(5, 'ORD-000005', 8, 5, NULL, 2, '2026-04-11 11:57:07', 'Control de rutina', 'Entregado', 'Urgente', 80.00, 0.00, 80.00, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(6, 'ORD-000006', 2, 4, NULL, 2, '2026-03-30 11:57:07', 'Control de rutina', 'Pendiente', 'Normal', 82.00, 0.00, 82.00, 0, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(7, 'ORD-000007', 9, 3, NULL, 2, '2026-03-24 11:57:07', 'Control de rutina', 'Completado', 'Normal', 45.00, 0.00, 45.00, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(8, 'ORD-000008', 1, 2, NULL, 2, '2026-03-27 11:57:07', 'Control de rutina', 'En proceso', 'Normal', 83.00, 0.00, 83.00, 0, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(9, 'ORD-000009', 5, 1, NULL, 2, '2026-03-21 11:57:07', 'Control de rutina', 'Completado', 'Normal', 59.00, 0.00, 59.00, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(10, 'ORD-000010', 4, 5, NULL, 2, '2026-04-19 11:57:07', 'Control de rutina', 'Completado', 'Normal', 82.00, 0.00, 82.00, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.orden_detalles
CREATE TABLE IF NOT EXISTS `orden_detalles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `orden_id` bigint unsigned NOT NULL,
  `prueba_id` bigint unsigned NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio_final` decimal(10,2) NOT NULL,
  `estado` enum('Pendiente','En proceso','Completado','Anulado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orden_detalles_orden_id_foreign` (`orden_id`),
  KEY `orden_detalles_prueba_id_foreign` (`prueba_id`),
  CONSTRAINT `orden_detalles_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orden_detalles_prueba_id_foreign` FOREIGN KEY (`prueba_id`) REFERENCES `pruebas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.orden_detalles: ~32 rows (aproximadamente)
DELETE FROM `orden_detalles`;
INSERT INTO `orden_detalles` (`id`, `orden_id`, `prueba_id`, `precio_unitario`, `descuento`, `precio_final`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 25.00, 0.00, 25.00, 'Pendiente', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 1, 7, 45.00, 0.00, 45.00, 'Pendiente', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 1, 8, 20.00, 0.00, 20.00, 'Pendiente', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 1, 17, 40.00, 0.00, 40.00, 'Pendiente', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(5, 2, 8, 20.00, 0.00, 20.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(6, 2, 9, 35.00, 0.00, 35.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(7, 2, 10, 18.00, 0.00, 18.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(8, 2, 14, 40.00, 0.00, 40.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(9, 3, 7, 45.00, 0.00, 45.00, 'Pendiente', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(10, 3, 18, 25.00, 0.00, 25.00, 'Pendiente', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(11, 4, 10, 18.00, 0.00, 18.00, 'Pendiente', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(12, 4, 11, 30.00, 0.00, 30.00, 'Pendiente', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(13, 5, 1, 25.00, 0.00, 25.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(14, 5, 3, 15.00, 0.00, 15.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(15, 5, 14, 40.00, 0.00, 40.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(16, 6, 1, 25.00, 0.00, 25.00, 'Pendiente', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(17, 6, 5, 12.00, 0.00, 12.00, 'Pendiente', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(18, 6, 7, 45.00, 0.00, 45.00, 'Pendiente', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(19, 7, 3, 15.00, 0.00, 15.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(20, 7, 11, 30.00, 0.00, 30.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(21, 8, 2, 20.00, 0.00, 20.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(22, 8, 10, 18.00, 0.00, 18.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(23, 8, 11, 30.00, 0.00, 30.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(24, 8, 12, 15.00, 0.00, 15.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(25, 9, 3, 15.00, 0.00, 15.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(26, 9, 5, 12.00, 0.00, 12.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(27, 9, 8, 20.00, 0.00, 20.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(28, 9, 15, 12.00, 0.00, 12.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(29, 10, 6, 12.00, 0.00, 12.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(30, 10, 8, 20.00, 0.00, 20.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(31, 10, 13, 10.00, 0.00, 10.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(32, 10, 17, 40.00, 0.00, 40.00, 'Completado', '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.pacientes
CREATE TABLE IF NOT EXISTS `pacientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `historia_clinica` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_documento` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DNI',
  `numero_documento` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombres` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_paterno` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_materno` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distrito` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_sangre` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alergias` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `antecedentes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pacientes_numero_documento_unique` (`numero_documento`),
  UNIQUE KEY `pacientes_historia_clinica_unique` (`historia_clinica`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.pacientes: ~10 rows (aproximadamente)
DELETE FROM `pacientes`;
INSERT INTO `pacientes` (`id`, `historia_clinica`, `tipo_documento`, `numero_documento`, `nombres`, `apellido_paterno`, `apellido_materno`, `fecha_nacimiento`, `sexo`, `telefono`, `email`, `direccion`, `distrito`, `ciudad`, `tipo_sangre`, `alergias`, `antecedentes`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'HC-000001', 'DNI', '45678901', 'Juan Carlos', 'Pérez', 'Gutiérrez', '1985-03-15', 'M', '987001001', 'jperez@gmail.com', NULL, NULL, NULL, 'O+', NULL, NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 'HC-000002', 'DNI', '56789012', 'María Elena', 'Torres', 'Quispe', '1990-07-22', 'F', '987001002', 'metorres@gmail.com', NULL, NULL, NULL, 'A+', NULL, NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 'HC-000003', 'DNI', '67890123', 'Pedro Antonio', 'Rodríguez', 'Vargas', '1978-11-08', 'M', '987001003', NULL, NULL, NULL, NULL, 'B+', NULL, NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 'HC-000004', 'DNI', '78901234', 'Carmen Rosa', 'Huanca', 'Mamani', '1965-05-30', 'F', '987001004', NULL, NULL, NULL, NULL, 'AB+', NULL, NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(5, 'HC-000005', 'DNI', '89012345', 'Luis Fernando', 'Chávez', 'Mendoza', '2000-12-01', 'M', '987001005', NULL, NULL, NULL, NULL, 'O-', NULL, NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(6, 'HC-000006', 'DNI', '90123456', 'Ana Lucía', 'Flores', 'Paz', '1995-09-14', 'F', '987001006', 'aflores@gmail.com', NULL, NULL, NULL, 'A-', NULL, NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(7, 'HC-000007', 'DNI', '01234567', 'Roberto Carlos', 'Mamani', 'Condori', '1970-04-20', 'M', '987001007', NULL, NULL, NULL, NULL, 'B-', NULL, NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(8, 'HC-000008', 'DNI', '12345670', 'Sofía Valentina', 'Ruiz', 'Salcedo', '2003-02-28', 'F', '987001008', NULL, NULL, NULL, NULL, 'AB-', NULL, NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(9, 'HC-000009', 'DNI', '23456701', 'Miguel Ángel', 'Castro', 'Benites', '1988-08-10', 'M', '987001009', NULL, NULL, NULL, NULL, 'O+', NULL, NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(10, 'HC-000010', 'DNI', '34567012', 'Elena Isabel', 'Vásquez', 'Lazo', '1975-01-17', 'F', '987001010', 'evasquez@gmail.com', NULL, NULL, NULL, 'A+', NULL, NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `factura_id` bigint unsigned NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `medio_pago` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Efectivo',
  `referencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_pago` datetime NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagos_factura_id_foreign` (`factura_id`),
  KEY `pagos_user_id_foreign` (`user_id`),
  CONSTRAINT `pagos_factura_id_foreign` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `pagos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.pagos: ~4 rows (aproximadamente)
DELETE FROM `pagos`;
INSERT INTO `pagos` (`id`, `factura_id`, `monto`, `medio_pago`, `referencia`, `fecha_pago`, `user_id`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 80.00, 'Tarjeta', NULL, '2026-04-11 12:57:07', 2, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 2, 45.00, 'Efectivo', NULL, '2026-03-24 12:57:07', 2, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 3, 59.00, 'Tarjeta', NULL, '2026-03-21 12:57:07', 2, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 4, 82.00, 'Tarjeta', NULL, '2026-04-19 12:57:07', 2, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Volcando estructura para tabla laboratorio_clinico.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.permissions: ~0 rows (aproximadamente)
DELETE FROM `permissions`;

-- Volcando estructura para tabla laboratorio_clinico.pruebas
CREATE TABLE IF NOT EXISTS `pruebas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `area_id` bigint unsigned NOT NULL,
  `codigo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `muestra_tipo` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sangre venosa',
  `tiempo_resultado` int NOT NULL DEFAULT '24' COMMENT 'Horas estimadas',
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `unidad` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valores_referencia` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pruebas_codigo_unique` (`codigo`),
  KEY `pruebas_area_id_foreign` (`area_id`),
  CONSTRAINT `pruebas_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas_laboratorio` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.pruebas: ~18 rows (aproximadamente)
DELETE FROM `pruebas`;
INSERT INTO `pruebas` (`id`, `area_id`, `codigo`, `nombre`, `descripcion`, `muestra_tipo`, `tiempo_resultado`, `precio`, `unidad`, `valores_referencia`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 1, 'HEM001', 'Hemograma Completo', NULL, 'Sangre venosa', 2, 25.00, 'cel/µL', 'Ver informe', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 1, 'HEM002', 'Tiempo de Coagulación', NULL, 'Sangre venosa', 2, 20.00, 'seg', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 1, 'HEM003', 'Grupo Sanguíneo y Factor Rh', NULL, 'Sangre venosa', 1, 15.00, '', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 2, 'BIO001', 'Glucosa en Ayunas', NULL, 'Sangre venosa', 2, 12.00, 'mg/dL', '70-100 mg/dL', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(5, 2, 'BIO002', 'Urea', NULL, 'Sangre venosa', 2, 12.00, 'mg/dL', '10-50 mg/dL', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(6, 2, 'BIO003', 'Creatinina', NULL, 'Sangre venosa', 2, 12.00, 'mg/dL', '0.6-1.2 mg/dL', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(7, 2, 'BIO004', 'Perfil Lipídico', NULL, 'Sangre venosa', 4, 45.00, 'mg/dL', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(8, 2, 'BIO005', 'TGO / TGP (Transaminasas)', NULL, 'Sangre venosa', 3, 20.00, 'U/L', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(9, 2, 'BIO006', 'Hemoglobina Glicosilada (HbA1c)', NULL, 'Sangre venosa', 4, 35.00, '%', '<5.7%', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(10, 4, 'INM001', 'PCR (Proteína C Reactiva)', NULL, 'Sangre venosa', 2, 18.00, 'mg/L', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(11, 4, 'INM002', 'VIH 1/2 (ELISA)', NULL, 'Sangre venosa', 4, 30.00, '', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(12, 4, 'INM003', 'VDRL (Sífilis)', NULL, 'Sangre venosa', 2, 15.00, '', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(13, 5, 'URO001', 'Examen de Orina Completo', NULL, 'Orina chorro medio', 1, 10.00, '', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(14, 5, 'URO002', 'Urocultivo', NULL, 'Orina chorro medio', 48, 40.00, '', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(15, 6, 'PAR001', 'Examen Coproparasitológico', NULL, 'Heces', 2, 12.00, '', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(16, 7, 'HOR001', 'TSH (Hormona Tiroidea)', NULL, 'Sangre venosa', 4, 35.00, 'µIU/mL', '0.5-5.0 µIU/mL', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(17, 7, 'HOR002', 'Testosterona Total', NULL, 'Sangre venosa', 4, 40.00, 'ng/dL', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(18, 7, 'HOR003', 'Beta HCG (Embarazo)', NULL, 'Sangre venosa', 2, 25.00, 'mIU/mL', NULL, 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.reactivos
CREATE TABLE IF NOT EXISTS `reactivos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `area_id` bigint unsigned NOT NULL,
  `codigo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proveedor` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidad_medida` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unidad',
  `stock_actual` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '5',
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha_vencimiento` date DEFAULT NULL,
  `lote` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('Disponible','Stock bajo','Sin stock','Vencido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reactivos_codigo_unique` (`codigo`),
  KEY `reactivos_area_id_foreign` (`area_id`),
  CONSTRAINT `reactivos_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas_laboratorio` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.reactivos: ~5 rows (aproximadamente)
DELETE FROM `reactivos`;
INSERT INTO `reactivos` (`id`, `area_id`, `codigo`, `nombre`, `marca`, `proveedor`, `unidad_medida`, `stock_actual`, `stock_minimo`, `precio_unitario`, `fecha_vencimiento`, `lote`, `estado`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 1, 'REA001', 'Reactivo Hemograma', 'Sysmex', 'MedLab Perú', 'Cartucho', 10, 3, 150.00, NULL, 'LOT2025A', 'Disponible', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 2, 'REA002', 'Reactivo Glucosa', 'Roche', 'Diagnostico SA', 'Frasco 500 mL', 5, 3, 80.00, NULL, 'LOT2025B', 'Disponible', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 2, 'REA003', 'Reactivo Creatinina', 'Roche', 'Diagnostico SA', 'Frasco 500 mL', 2, 3, 75.00, NULL, 'LOT2025C', 'Stock bajo', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 7, 'REA004', 'Kit TSH Ultrasensible', 'Abbott', 'Abbott Perú', 'Kit 100 pruebas', 1, 2, 450.00, NULL, 'LOT2025D', 'Stock bajo', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(5, 4, 'REA005', 'Kit VDRL', 'Wiener Lab', 'Wiener Lab Perú', 'Kit 100 pruebas', 8, 2, 120.00, NULL, 'LOT2025E', 'Disponible', 1, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.resultados
CREATE TABLE IF NOT EXISTS `resultados` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `orden_detalle_id` bigint unsigned NOT NULL,
  `muestra_id` bigint unsigned DEFAULT NULL,
  `valor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `unidad` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valores_referencia` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interpretacion` enum('Normal','Bajo','Alto','Crítico') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metodo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `validado_por` bigint unsigned DEFAULT NULL,
  `fecha_validacion` datetime DEFAULT NULL,
  `valor_critico` tinyint(1) NOT NULL DEFAULT '0',
  `notificado` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `resultados_orden_detalle_id_foreign` (`orden_detalle_id`),
  KEY `resultados_muestra_id_foreign` (`muestra_id`),
  KEY `resultados_validado_por_foreign` (`validado_por`),
  CONSTRAINT `resultados_muestra_id_foreign` FOREIGN KEY (`muestra_id`) REFERENCES `muestras` (`id`) ON DELETE SET NULL,
  CONSTRAINT `resultados_orden_detalle_id_foreign` FOREIGN KEY (`orden_detalle_id`) REFERENCES `orden_detalles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `resultados_validado_por_foreign` FOREIGN KEY (`validado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.resultados: ~13 rows (aproximadamente)
DELETE FROM `resultados`;
INSERT INTO `resultados` (`id`, `orden_detalle_id`, `muestra_id`, `valor`, `unidad`, `valores_referencia`, `interpretacion`, `metodo`, `equipo`, `validado_por`, `fecha_validacion`, `valor_critico`, `notificado`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 13, 13, '112 cel/µL', 'cel/µL', 'Ver informe', 'Normal', 'Automático', 'Analizador principal', 3, '2026-04-11 19:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(2, 14, 14, '121 ', '', NULL, 'Normal', 'Automático', 'Analizador principal', 3, '2026-04-11 17:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(3, 15, 15, '60 ', '', NULL, 'Normal', 'Automático', 'Analizador principal', 3, '2026-04-11 18:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(4, 19, 19, '60 ', '', NULL, 'Normal', 'Automático', 'Analizador principal', 3, '2026-03-24 19:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(5, 20, 20, '100 ', '', NULL, 'Normal', 'Automático', 'Analizador principal', 3, '2026-03-24 15:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(6, 25, 25, '106 ', '', NULL, 'Normal', 'Automático', 'Analizador principal', 3, '2026-03-21 18:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(7, 26, 26, '148 mg/dL', 'mg/dL', '10-50 mg/dL', 'Normal', 'Automático', 'Analizador principal', 3, '2026-03-21 14:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(8, 27, 27, '119 U/L', 'U/L', NULL, 'Normal', 'Automático', 'Analizador principal', 3, '2026-03-21 16:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(9, 28, 28, '119 ', '', NULL, 'Normal', 'Automático', 'Analizador principal', 3, '2026-03-21 15:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(10, 29, 29, '96 mg/dL', 'mg/dL', '0.6-1.2 mg/dL', 'Normal', 'Automático', 'Analizador principal', 3, '2026-04-19 16:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(11, 30, 30, '71 U/L', 'U/L', NULL, 'Normal', 'Automático', 'Analizador principal', 3, '2026-04-19 14:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(12, 31, 31, '73 ', '', NULL, 'Normal', 'Automático', 'Analizador principal', 3, '2026-04-19 18:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07'),
	(13, 32, 32, '66 ng/dL', 'ng/dL', NULL, 'Normal', 'Automático', 'Analizador principal', 3, '2026-04-19 16:57:07', 0, 1, NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.roles: ~4 rows (aproximadamente)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'web', '2026-04-20 16:57:06', '2026-04-20 16:57:06'),
	(2, 'Recepcionista', 'web', '2026-04-20 16:57:06', '2026-04-20 16:57:06'),
	(3, 'Tecnólogo', 'web', '2026-04-20 16:57:06', '2026-04-20 16:57:06'),
	(4, 'Médico', 'web', '2026-04-20 16:57:06', '2026-04-20 16:57:06');

-- Volcando estructura para tabla laboratorio_clinico.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.role_has_permissions: ~0 rows (aproximadamente)
DELETE FROM `role_has_permissions`;

-- Volcando estructura para tabla laboratorio_clinico.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.sessions: ~0 rows (aproximadamente)
DELETE FROM `sessions`;

-- Volcando estructura para tabla laboratorio_clinico.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.users: ~3 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador Sistema', 'admin@lab.com', NULL, '$2y$12$4i3h.PzKPv9Jt1Z1IGmzt.XKHXEgAS9wUn.EayAmA4y4oicsmFMRy', NULL, '2026-04-20 16:57:06', '2026-04-20 16:57:06'),
	(2, 'María García', 'recepcion@lab.com', NULL, '$2y$12$yN3lw4TSLNgs7J/FVnHCB.CQrCW6Qdusz8.q/HIXsKtjlh3qOhfUy', NULL, '2026-04-20 16:57:06', '2026-04-20 16:57:06'),
	(3, 'Carlos Mendoza', 'tecnologo@lab.com', NULL, '$2y$12$xduiMXt/ynniMdGayNGAfu3od/sep55ecBmv9HMoD5iHg0adN2fYe', NULL, '2026-04-20 16:57:07', '2026-04-20 16:57:07');

-- Volcando estructura para tabla laboratorio_clinico.valores_criticos
CREATE TABLE IF NOT EXISTS `valores_criticos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resultado_id` bigint unsigned NOT NULL,
  `orden_id` bigint unsigned NOT NULL,
  `descripcion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notificado_a` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_notificacion` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Notificado','Resuelto') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `accion_tomada` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `valores_criticos_resultado_id_foreign` (`resultado_id`),
  KEY `valores_criticos_orden_id_foreign` (`orden_id`),
  CONSTRAINT `valores_criticos_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `valores_criticos_resultado_id_foreign` FOREIGN KEY (`resultado_id`) REFERENCES `resultados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla laboratorio_clinico.valores_criticos: ~0 rows (aproximadamente)
DELETE FROM `valores_criticos`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
