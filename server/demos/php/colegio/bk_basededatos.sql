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


-- Volcando estructura de base de datos para colegio_crm
CREATE DATABASE IF NOT EXISTS `colegio_crm` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `colegio_crm`;

-- Volcando estructura para tabla colegio_crm.alumnos
CREATE TABLE IF NOT EXISTS `alumnos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` enum('M','F') COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apoderado_nombre` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apoderado_dni` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apoderado_telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apoderado_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apoderado_parentesco` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activo','inactivo','trasladado','egresado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumnos_codigo_unique` (`codigo`),
  UNIQUE KEY `alumnos_dni_unique` (`dni`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.alumnos: ~12 rows (aproximadamente)
DELETE FROM `alumnos`;
INSERT INTO `alumnos` (`id`, `codigo`, `dni`, `nombres`, `apellidos`, `fecha_nacimiento`, `genero`, `direccion`, `telefono`, `email`, `foto`, `apoderado_nombre`, `apoderado_dni`, `apoderado_telefono`, `apoderado_email`, `apoderado_parentesco`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 'ALU00001', '42491506', 'Ricardo', 'Mendoza Vargas', '2020-05-05', 'F', NULL, NULL, NULL, NULL, 'Apoderado de Ricardo Mendoza', NULL, '999972937', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(2, 'ALU00002', '13727248', 'Elena', 'Rodriguez Torres', '2020-05-05', 'F', NULL, NULL, NULL, NULL, 'Apoderado de Elena Rodriguez', NULL, '959702458', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(3, 'ALU00003', '33181430', 'Carmen', 'Diaz Rodriguez', '2011-05-05', 'F', NULL, NULL, NULL, NULL, 'Apoderado de Carmen Diaz', NULL, '918406035', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(4, 'ALU00004', '63303627', 'Ana', 'Mendoza Castro', '2012-05-05', 'M', NULL, NULL, NULL, NULL, 'Apoderado de Ana Mendoza', NULL, '959250299', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(5, 'ALU00005', '82401722', 'Elena', 'Rodriguez Mendoza', '2019-05-05', 'F', NULL, NULL, NULL, NULL, 'Apoderado de Elena Rodriguez', NULL, '981467599', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(6, 'ALU00006', '21837726', 'Elena', 'Lopez Mendoza', '2018-05-05', 'M', NULL, NULL, NULL, NULL, 'Apoderado de Elena Lopez', NULL, '936641896', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(7, 'ALU00007', '36235146', 'Luis', 'Perez Rodriguez', '2016-05-05', 'F', NULL, NULL, NULL, NULL, 'Apoderado de Luis Perez', NULL, '918767224', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(8, 'ALU00008', '80594359', 'Pedro', 'Lopez Diaz', '2013-05-05', 'F', NULL, NULL, NULL, NULL, 'Apoderado de Pedro Lopez', NULL, '952045376', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(9, 'ALU00009', '67252455', 'Juan', 'Diaz Diaz', '2015-05-05', 'F', NULL, NULL, NULL, NULL, 'Apoderado de Juan Diaz', NULL, '964974685', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(10, 'ALU00010', '53497616', 'Ana', 'Torres Lopez', '2011-05-05', 'M', NULL, NULL, NULL, NULL, 'Apoderado de Ana Torres', NULL, '980016297', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(11, 'ALU00011', '29517613', 'Carmen', 'Perez Perez', '2013-05-05', 'F', NULL, NULL, NULL, NULL, 'Apoderado de Carmen Perez', NULL, '950154577', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(12, 'ALU00012', '84160716', 'Ana', 'Torres Vargas', '2016-05-05', 'M', NULL, NULL, NULL, NULL, 'Apoderado de Ana Torres', NULL, '945654953', NULL, 'Padre', 'activo', '2026-05-05 07:02:55', '2026-05-05 07:02:55');

-- Volcando estructura para tabla colegio_crm.asignaciones
CREATE TABLE IF NOT EXISTS `asignaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `personal_id` bigint unsigned NOT NULL,
  `materia_id` bigint unsigned NOT NULL,
  `seccion_id` bigint unsigned NOT NULL,
  `anio_escolar` year NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asignacion_unica` (`personal_id`,`materia_id`,`seccion_id`,`anio_escolar`),
  KEY `asignaciones_materia_id_foreign` (`materia_id`),
  KEY `asignaciones_seccion_id_foreign` (`seccion_id`),
  CONSTRAINT `asignaciones_materia_id_foreign` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asignaciones_personal_id_foreign` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asignaciones_seccion_id_foreign` FOREIGN KEY (`seccion_id`) REFERENCES `secciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.asignaciones: ~0 rows (aproximadamente)
DELETE FROM `asignaciones`;

-- Volcando estructura para tabla colegio_crm.asistencias
CREATE TABLE IF NOT EXISTS `asistencias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `alumno_id` bigint unsigned NOT NULL,
  `seccion_id` bigint unsigned NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('presente','tardanza','falta','justificado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'presente',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `registrado_por` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asistencia_diaria_unica` (`alumno_id`,`seccion_id`,`fecha`),
  KEY `asistencias_seccion_id_foreign` (`seccion_id`),
  KEY `asistencias_registrado_por_foreign` (`registrado_por`),
  CONSTRAINT `asistencias_alumno_id_foreign` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asistencias_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asistencias_seccion_id_foreign` FOREIGN KEY (`seccion_id`) REFERENCES `secciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.asistencias: ~0 rows (aproximadamente)
DELETE FROM `asistencias`;

-- Volcando estructura para tabla colegio_crm.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.cache: ~0 rows (aproximadamente)
DELETE FROM `cache`;

-- Volcando estructura para tabla colegio_crm.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Volcando estructura para tabla colegio_crm.conceptos_pago
CREATE TABLE IF NOT EXISTS `conceptos_pago` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `monto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tipo` enum('mensualidad','matricula','taller','otros') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mensualidad',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.conceptos_pago: ~2 rows (aproximadamente)
DELETE FROM `conceptos_pago`;
INSERT INTO `conceptos_pago` (`id`, `nombre`, `descripcion`, `monto`, `tipo`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Matrícula 2026', NULL, 450.00, 'matricula', 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(2, 'Mensualidad General', NULL, 300.00, 'mensualidad', 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55');

-- Volcando estructura para tabla colegio_crm.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci,
  `descripcion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grupo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuracion_clave_unique` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.configuracion: ~12 rows (aproximadamente)
DELETE FROM `configuracion`;
INSERT INTO `configuracion` (`id`, `clave`, `valor`, `descripcion`, `grupo`, `created_at`, `updated_at`) VALUES
	(1, 'colegio_nombre', 'Colegio CRM', 'Nombre del colegio', 'colegio', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(2, 'colegio_ruc', '20123456789', 'RUC de la institución', 'colegio', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(3, 'colegio_direccion', 'Av. Principal 123, Lima', 'Dirección del colegio', 'colegio', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(4, 'colegio_telefono', '(01) 234-5678', 'Teléfono principal', 'colegio', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(5, 'colegio_email', 'info@colegio.edu.pe', 'Email institucional', 'colegio', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(6, 'colegio_director', 'Director General', 'Nombre del director', 'colegio', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(7, 'anio_escolar', '2026', 'Año escolar activo', 'sistema', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(8, 'nota_minima', '11', 'Nota mínima para aprobar', 'academico', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(9, 'nota_maxima', '20', 'Nota máxima del sistema', 'academico', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(10, 'num_bimestres', '4', 'Número de bimestres', 'academico', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(11, 'moneda', 'S/.', 'Símbolo de moneda', 'sistema', '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(12, 'logo_url', '', 'URL del logo institucional', 'colegio', '2026-05-05 07:02:55', '2026-05-05 07:02:55');

-- Volcando estructura para tabla colegio_crm.failed_jobs
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

-- Volcando datos para la tabla colegio_crm.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Volcando estructura para tabla colegio_crm.grados
CREATE TABLE IF NOT EXISTS `grados` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel` enum('inicial','primaria','secundaria') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primaria',
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.grados: ~11 rows (aproximadamente)
DELETE FROM `grados`;
INSERT INTO `grados` (`id`, `nombre`, `nivel`, `descripcion`, `created_at`, `updated_at`) VALUES
	(1, '1er Grado', 'primaria', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(2, '2do Grado', 'primaria', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(3, '3er Grado', 'primaria', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(4, '4to Grado', 'primaria', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(5, '5to Grado', 'primaria', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(6, '6to Grado', 'primaria', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(7, '1er Año', 'secundaria', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(8, '2do Año', 'secundaria', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(9, '3er Año', 'secundaria', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(10, '4to Año', 'secundaria', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(11, '5to Año', 'secundaria', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55');

-- Volcando estructura para tabla colegio_crm.jobs
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

-- Volcando datos para la tabla colegio_crm.jobs: ~0 rows (aproximadamente)
DELETE FROM `jobs`;

-- Volcando estructura para tabla colegio_crm.job_batches
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

-- Volcando datos para la tabla colegio_crm.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Volcando estructura para tabla colegio_crm.materias
CREATE TABLE IF NOT EXISTS `materias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel` enum('inicial','primaria','secundaria','todos') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'todos',
  `horas_semanales` int NOT NULL DEFAULT '2',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3b82f6',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `materias_codigo_unique` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.materias: ~0 rows (aproximadamente)
DELETE FROM `materias`;

-- Volcando estructura para tabla colegio_crm.matriculas
CREATE TABLE IF NOT EXISTS `matriculas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alumno_id` bigint unsigned NOT NULL,
  `grado_id` bigint unsigned NOT NULL,
  `seccion_id` bigint unsigned NOT NULL,
  `anio_escolar` year NOT NULL,
  `fecha_matricula` date NOT NULL,
  `estado` enum('activo','retirado','trasladado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `registrado_por` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matriculas_numero_unique` (`numero`),
  KEY `matriculas_alumno_id_foreign` (`alumno_id`),
  KEY `matriculas_grado_id_foreign` (`grado_id`),
  KEY `matriculas_seccion_id_foreign` (`seccion_id`),
  KEY `matriculas_registrado_por_foreign` (`registrado_por`),
  CONSTRAINT `matriculas_alumno_id_foreign` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matriculas_grado_id_foreign` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `matriculas_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `matriculas_seccion_id_foreign` FOREIGN KEY (`seccion_id`) REFERENCES `secciones` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.matriculas: ~12 rows (aproximadamente)
DELETE FROM `matriculas`;
INSERT INTO `matriculas` (`id`, `numero`, `alumno_id`, `grado_id`, `seccion_id`, `anio_escolar`, `fecha_matricula`, `estado`, `observaciones`, `registrado_por`, `created_at`, `updated_at`) VALUES
	(1, 'MAT20260001', 1, 5, 10, '2026', '2026-01-28', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(2, 'MAT20260002', 2, 2, 3, '2026', '2026-01-20', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(3, 'MAT20260003', 3, 4, 8, '2026', '2026-01-02', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(4, 'MAT20260004', 4, 2, 4, '2026', '2026-01-11', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(5, 'MAT20260005', 5, 2, 4, '2026', '2026-01-07', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(6, 'MAT20260006', 6, 3, 5, '2026', '2026-01-11', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(7, 'MAT20260007', 7, 1, 1, '2026', '2026-01-19', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(8, 'MAT20260008', 8, 2, 4, '2026', '2026-01-25', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(9, 'MAT20260009', 9, 3, 5, '2026', '2026-01-04', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(10, 'MAT20260010', 10, 3, 5, '2026', '2026-01-21', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(11, 'MAT20260011', 11, 2, 4, '2026', '2026-01-03', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(12, 'MAT20260012', 12, 4, 7, '2026', '2026-01-20', 'activo', NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55');

-- Volcando estructura para tabla colegio_crm.mensajes
CREATE TABLE IF NOT EXISTS `mensajes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `remitente_id` bigint unsigned NOT NULL,
  `destinatario_id` bigint unsigned NOT NULL,
  `asunto` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cuerpo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `leido_en` timestamp NULL DEFAULT NULL,
  `archivado` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mensajes_remitente_id_foreign` (`remitente_id`),
  KEY `mensajes_destinatario_id_foreign` (`destinatario_id`),
  CONSTRAINT `mensajes_destinatario_id_foreign` FOREIGN KEY (`destinatario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mensajes_remitente_id_foreign` FOREIGN KEY (`remitente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.mensajes: ~0 rows (aproximadamente)
DELETE FROM `mensajes`;

-- Volcando estructura para tabla colegio_crm.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.migrations: ~0 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2024_01_01_000001_create_grados_table', 1),
	(5, '2024_01_01_000002_create_personal_table', 1),
	(6, '2024_01_01_000003_create_alumnos_table', 1),
	(7, '2024_01_01_000004_create_matriculas_table', 1),
	(8, '2024_01_01_000005_create_mensajes_table', 1),
	(9, '2024_01_01_000006_create_materias_table', 1),
	(10, '2024_01_01_000007_create_notas_table', 1),
	(11, '2024_01_01_000008_create_asistencias_table', 1),
	(12, '2024_01_01_000009_create_configuracion_table', 1);

-- Volcando estructura para tabla colegio_crm.notas
CREATE TABLE IF NOT EXISTS `notas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `alumno_id` bigint unsigned NOT NULL,
  `materia_id` bigint unsigned NOT NULL,
  `seccion_id` bigint unsigned NOT NULL,
  `anio_escolar` year NOT NULL,
  `bimestre` tinyint NOT NULL,
  `nota` decimal(5,2) DEFAULT NULL,
  `promedio_bimestral` decimal(5,2) DEFAULT NULL,
  `estado` enum('aprobado','desaprobado','pendiente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `registrado_por` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nota_unica` (`alumno_id`,`materia_id`,`seccion_id`,`anio_escolar`,`bimestre`),
  KEY `notas_materia_id_foreign` (`materia_id`),
  KEY `notas_seccion_id_foreign` (`seccion_id`),
  KEY `notas_registrado_por_foreign` (`registrado_por`),
  CONSTRAINT `notas_alumno_id_foreign` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notas_materia_id_foreign` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notas_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `notas_seccion_id_foreign` FOREIGN KEY (`seccion_id`) REFERENCES `secciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.notas: ~0 rows (aproximadamente)
DELETE FROM `notas`;

-- Volcando estructura para tabla colegio_crm.notificaciones
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('info','exito','advertencia','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notificaciones_user_id_foreign` (`user_id`),
  CONSTRAINT `notificaciones_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.notificaciones: ~0 rows (aproximadamente)
DELETE FROM `notificaciones`;

-- Volcando estructura para tabla colegio_crm.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_recibo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alumno_id` bigint unsigned NOT NULL,
  `concepto_id` bigint unsigned NOT NULL,
  `anio_escolar` year NOT NULL,
  `mes` tinyint DEFAULT NULL COMMENT '1-12 para mensualidades',
  `monto` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `monto_pagado` decimal(10,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `metodo_pago` enum('efectivo','transferencia','tarjeta','cheque') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `estado` enum('pagado','pendiente','vencido','anulado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `comprobante` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `registrado_por` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pagos_numero_recibo_unique` (`numero_recibo`),
  KEY `pagos_alumno_id_foreign` (`alumno_id`),
  KEY `pagos_concepto_id_foreign` (`concepto_id`),
  KEY `pagos_registrado_por_foreign` (`registrado_por`),
  CONSTRAINT `pagos_alumno_id_foreign` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `pagos_concepto_id_foreign` FOREIGN KEY (`concepto_id`) REFERENCES `conceptos_pago` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `pagos_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.pagos: ~60 rows (aproximadamente)
DELETE FROM `pagos`;
INSERT INTO `pagos` (`id`, `numero_recibo`, `alumno_id`, `concepto_id`, `anio_escolar`, `mes`, `monto`, `descuento`, `monto_pagado`, `fecha_pago`, `fecha_vencimiento`, `metodo_pago`, `estado`, `comprobante`, `observaciones`, `registrado_por`, `created_at`, `updated_at`) VALUES
	(1, 'REC202600011', 1, 2, '2026', 1, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(2, 'REC202600012', 1, 2, '2026', 2, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(3, 'REC202600013', 1, 2, '2026', 3, 300.00, 0.00, 300.00, '2026-03-12', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(4, 'REC202600014', 1, 2, '2026', 4, 300.00, 0.00, 300.00, '2026-04-12', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(5, 'REC202600015', 1, 2, '2026', 5, 300.00, 0.00, 300.00, '2026-05-08', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(6, 'REC202600021', 2, 2, '2026', 1, 300.00, 0.00, 300.00, '2026-01-12', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(7, 'REC202600022', 2, 2, '2026', 2, 300.00, 0.00, 300.00, '2026-02-01', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(8, 'REC202600023', 2, 2, '2026', 3, 300.00, 0.00, 300.00, '2026-03-11', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(9, 'REC202600024', 2, 2, '2026', 4, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(10, 'REC202600025', 2, 2, '2026', 5, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(11, 'REC202600031', 3, 2, '2026', 1, 300.00, 0.00, 300.00, '2026-01-17', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(12, 'REC202600032', 3, 2, '2026', 2, 300.00, 0.00, 300.00, '2026-02-28', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(13, 'REC202600033', 3, 2, '2026', 3, 300.00, 0.00, 300.00, '2026-03-13', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(14, 'REC202600034', 3, 2, '2026', 4, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(15, 'REC202600035', 3, 2, '2026', 5, 300.00, 0.00, 300.00, '2026-05-28', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(16, 'REC202600041', 4, 2, '2026', 1, 300.00, 0.00, 300.00, '2026-01-14', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(17, 'REC202600042', 4, 2, '2026', 2, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(18, 'REC202600043', 4, 2, '2026', 3, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(19, 'REC202600044', 4, 2, '2026', 4, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(20, 'REC202600045', 4, 2, '2026', 5, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(21, 'REC202600051', 5, 2, '2026', 1, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(22, 'REC202600052', 5, 2, '2026', 2, 300.00, 0.00, 300.00, '2026-02-01', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(23, 'REC202600053', 5, 2, '2026', 3, 300.00, 0.00, 300.00, '2026-03-02', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(24, 'REC202600054', 5, 2, '2026', 4, 300.00, 0.00, 300.00, '2026-04-02', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(25, 'REC202600055', 5, 2, '2026', 5, 300.00, 0.00, 300.00, '2026-05-14', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(26, 'REC202600061', 6, 2, '2026', 1, 300.00, 0.00, 300.00, '2026-01-09', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(27, 'REC202600062', 6, 2, '2026', 2, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(28, 'REC202600063', 6, 2, '2026', 3, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(29, 'REC202600064', 6, 2, '2026', 4, 300.00, 0.00, 300.00, '2026-04-28', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(30, 'REC202600065', 6, 2, '2026', 5, 300.00, 0.00, 300.00, '2026-05-21', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(31, 'REC202600071', 7, 2, '2026', 1, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(32, 'REC202600072', 7, 2, '2026', 2, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(33, 'REC202600073', 7, 2, '2026', 3, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(34, 'REC202600074', 7, 2, '2026', 4, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(35, 'REC202600075', 7, 2, '2026', 5, 300.00, 0.00, 300.00, '2026-05-24', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(36, 'REC202600081', 8, 2, '2026', 1, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(37, 'REC202600082', 8, 2, '2026', 2, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(38, 'REC202600083', 8, 2, '2026', 3, 300.00, 0.00, 300.00, '2026-03-25', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(39, 'REC202600084', 8, 2, '2026', 4, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(40, 'REC202600085', 8, 2, '2026', 5, 300.00, 0.00, 300.00, '2026-05-18', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(41, 'REC202600091', 9, 2, '2026', 1, 300.00, 0.00, 300.00, '2026-01-27', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(42, 'REC202600092', 9, 2, '2026', 2, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(43, 'REC202600093', 9, 2, '2026', 3, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(44, 'REC202600094', 9, 2, '2026', 4, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(45, 'REC202600095', 9, 2, '2026', 5, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(46, 'REC202600101', 10, 2, '2026', 1, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(47, 'REC202600102', 10, 2, '2026', 2, 300.00, 0.00, 300.00, '2026-02-10', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(48, 'REC202600103', 10, 2, '2026', 3, 300.00, 0.00, 300.00, '2026-03-19', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(49, 'REC202600104', 10, 2, '2026', 4, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(50, 'REC202600105', 10, 2, '2026', 5, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(51, 'REC202600111', 11, 2, '2026', 1, 300.00, 0.00, 300.00, '2026-01-20', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(52, 'REC202600112', 11, 2, '2026', 2, 300.00, 0.00, 300.00, '2026-02-22', NULL, 'efectivo', 'pagado', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(53, 'REC202600113', 11, 2, '2026', 3, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(54, 'REC202600114', 11, 2, '2026', 4, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(55, 'REC202600115', 11, 2, '2026', 5, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(56, 'REC202600121', 12, 2, '2026', 1, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(57, 'REC202600122', 12, 2, '2026', 2, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(58, 'REC202600123', 12, 2, '2026', 3, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:56', '2026-05-05 07:02:56'),
	(59, 'REC202600124', 12, 2, '2026', 4, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:56', '2026-05-05 07:02:56'),
	(60, 'REC202600125', 12, 2, '2026', 5, 300.00, 0.00, 0.00, '2026-05-05', NULL, 'transferencia', 'pendiente', NULL, NULL, 1, '2026-05-05 07:02:56', '2026-05-05 07:02:56');

-- Volcando estructura para tabla colegio_crm.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Volcando estructura para tabla colegio_crm.personal
CREATE TABLE IF NOT EXISTS `personal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('docente','administrativo','directivo','auxiliar') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'docente',
  `especialidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `fecha_ingreso` date NOT NULL,
  `salario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('activo','inactivo','licencia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_dni_unique` (`dni`),
  KEY `personal_user_id_foreign` (`user_id`),
  CONSTRAINT `personal_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.personal: ~10 rows (aproximadamente)
DELETE FROM `personal`;
INSERT INTO `personal` (`id`, `user_id`, `dni`, `nombres`, `apellidos`, `tipo`, `especialidad`, `telefono`, `email`, `direccion`, `fecha_ingreso`, `salario`, `estado`, `foto`, `created_at`, `updated_at`) VALUES
	(1, NULL, '29498821', 'Juan', 'Perez', 'docente', 'General', '921180527', 'juan@colegio.edu.pe', NULL, '2023-05-05', 2726.00, 'activo', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(2, NULL, '23869132', 'Maria', 'Gomez', 'docente', 'General', '968892882', 'maria@colegio.edu.pe', NULL, '2021-05-05', 2359.00, 'activo', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(3, NULL, '97334735', 'Pedro', 'Rodriguez', 'docente', 'General', '953377691', 'pedro@colegio.edu.pe', NULL, '2024-05-05', 3207.00, 'activo', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(4, NULL, '60450056', 'Ana', 'Sanches', 'docente', 'General', '948608637', 'ana@colegio.edu.pe', NULL, '2022-05-05', 2683.00, 'activo', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(5, NULL, '84914409', 'Luis', 'Lopez', 'docente', 'General', '954667629', 'luis@colegio.edu.pe', NULL, '2023-05-05', 2233.00, 'activo', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(6, NULL, '20295482', 'Carmen', 'Torres', 'docente', 'General', '980696366', 'carmen@colegio.edu.pe', NULL, '2021-05-05', 2480.00, 'activo', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(7, NULL, '77690442', 'Jose', 'Diaz', 'docente', 'General', '952386776', 'jose@colegio.edu.pe', NULL, '2021-05-05', 2453.00, 'activo', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(8, NULL, '58419448', 'Elena', 'Vargas', 'docente', 'General', '974843656', 'elena@colegio.edu.pe', NULL, '2023-05-05', 2829.00, 'activo', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(9, NULL, '64539755', 'Ricardo', 'Mendoza', 'docente', 'General', '969455360', 'ricardo@colegio.edu.pe', NULL, '2025-05-05', 2827.00, 'activo', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(10, NULL, '73496422', 'Sofia', 'Castro', 'docente', 'General', '935029804', 'sofia@colegio.edu.pe', NULL, '2022-05-05', 3248.00, 'activo', NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55');

-- Volcando estructura para tabla colegio_crm.secciones
CREATE TABLE IF NOT EXISTS `secciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grado_id` bigint unsigned NOT NULL,
  `nombre` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `turno` enum('mañana','tarde','noche') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mañana',
  `capacidad` int NOT NULL DEFAULT '30',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `secciones_grado_id_foreign` (`grado_id`),
  CONSTRAINT `secciones_grado_id_foreign` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.secciones: ~17 rows (aproximadamente)
DELETE FROM `secciones`;
INSERT INTO `secciones` (`id`, `grado_id`, `nombre`, `turno`, `capacidad`, `created_at`, `updated_at`) VALUES
	(1, 1, 'A', 'mañana', 30, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(2, 1, 'B', 'tarde', 25, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(3, 2, 'A', 'mañana', 30, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(4, 2, 'B', 'tarde', 25, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(5, 3, 'A', 'mañana', 30, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(6, 3, 'B', 'tarde', 25, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(7, 4, 'A', 'mañana', 30, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(8, 4, 'B', 'tarde', 25, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(9, 5, 'A', 'mañana', 30, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(10, 5, 'B', 'tarde', 25, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(11, 6, 'A', 'mañana', 30, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(12, 6, 'B', 'tarde', 25, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(13, 7, 'A', 'mañana', 30, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(14, 8, 'A', 'mañana', 30, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(15, 9, 'A', 'mañana', 30, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(16, 10, 'A', 'mañana', 30, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(17, 11, 'A', 'mañana', 30, '2026-05-05 07:02:55', '2026-05-05 07:02:55');

-- Volcando estructura para tabla colegio_crm.sessions
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

-- Volcando datos para la tabla colegio_crm.sessions: ~2 rows (aproximadamente)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('3fXrqrd771Kata959VwMTk6lS1fMb7Z12s30Dj0k', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidzlLVUFXRkI3cW1pSTRVVU5BMDlKbUtvUWRObHUzVHFTTlk1UExHUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAxMS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1777982473),
	('k7h5LVBIvQ1pWeb2JjCIpGJLPgmHoQUUtLK2w8X1', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidnhXV0ROaVo0T2phUVVvUUZ1dWxMZlV6d0F2WUF5NzNEMmRLVTUzQSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMTEvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1777965052);

-- Volcando estructura para tabla colegio_crm.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','secretaria','docente','contador') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'secretaria',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla colegio_crm.users: ~2 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `activo`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'admin@colegio.edu.pe', NULL, '$2y$12$YMR7C8RapoTWYrvn/.tpC.ISgkVht9Y9DMxqG0lDT6OnmG3YKyrF2', 'admin', NULL, 1, NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55'),
	(2, 'María García López', 'secretaria@colegio.edu.pe', NULL, '$2y$12$HaBY.dYFZaULXAdiin6NgeqRc1/6AoOlNUm5mJ1JfBv1meCyjIQR6', 'secretaria', NULL, 1, NULL, '2026-05-05 07:02:55', '2026-05-05 07:02:55');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
