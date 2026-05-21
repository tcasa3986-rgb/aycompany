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


-- Volcando estructura de base de datos para cotizacion
CREATE DATABASE IF NOT EXISTS `cotizacion` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `cotizacion`;

-- Volcando estructura para tabla cotizacion.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla cotizacion.cache: ~0 rows (aproximadamente)
DELETE FROM `cache`;

-- Volcando estructura para tabla cotizacion.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla cotizacion.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Volcando estructura para tabla cotizacion.clients
CREATE TABLE IF NOT EXISTS `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla cotizacion.clients: ~10 rows (aproximadamente)
DELETE FROM `clients`;
INSERT INTO `clients` (`id`, `name`, `document_number`, `email`, `phone`, `address`, `created_at`, `updated_at`) VALUES
	(1, 'Corporación Andina S.A.C.', '20100102475', 'compras@andina.pe', '01 234-5678', 'Jr. Camaná 410, Lima', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(2, 'Tech Solutions Perú S.R.L.', '20523456789', 'admin@techsol.pe', '01 987-6543', 'Av. Larco 1301, Miraflores', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(3, 'Distribuidora El Sol E.I.R.L.', '20234567890', 'gerencia@elsol.pe', '044 23-4567', 'Av. España 1801, Trujillo', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(4, 'Constructora Lima Norte SAC', '20345678901', 'obras@limanorte.pe', '01 345-6789', 'Av. Túpac Amaru 2850, Comas', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(5, 'Agro Export Ica S.A.', '20456789012', 'export@agroica.pe', '056 23-4567', 'Panamericana Sur km 305, Ica', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(6, 'Inversiones Pacífico S.A.C.', '20567890123', 'finanzas@pacifico.pe', '01 456-7890', 'Av. del Ejército 900, San Isidro', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(7, 'Clínica San Felipe S.A.', '20678901234', 'logistica@sanfelipe.pe', '01 567-8901', 'Av. Gregorio Escobedo 650, Jesús María', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(8, 'Minera Horizonte S.A.C.', '20789012345', 'adquisiciones@horizonte.pe', '044 56-7890', 'Av. Mansiche 1390, Trujillo', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(9, 'Grupo Educativo Innova SAC', '20890123456', 'compras@innova.edu.pe', '01 678-9012', 'Av. La Encalada 1257, Surco', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(10, 'Ferretería Industrial Norte SAC', '20901234567', 'ventas@ferronorte.pe', '073 34-5678', 'Jr. Loreto 560, Piura', '2026-04-24 17:38:37', '2026-04-24 17:38:37');

-- Volcando estructura para tabla cotizacion.companies
CREATE TABLE IF NOT EXISTS `companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla cotizacion.companies: ~10 rows (aproximadamente)
DELETE FROM `companies`;
INSERT INTO `companies` (`id`, `name`, `document_number`, `address`, `email`, `phone`, `created_at`, `updated_at`) VALUES
	(1, 'Proveedor Alpha S.A.C.', '20111222333', 'Av. Industrial 1234, Ate', 'info@alpha.pe', '01 678-9012', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(2, 'Importadora Beta E.I.R.L.', '20222333444', 'Jr. Ucayali 890, Lima', 'info@beta-imports.pe', '01 789-0123', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(3, 'Logística Gamma S.A.', '20333444555', 'Av. Argentina 1800, Callao', 'ops@loggamma.pe', '01 890-1234', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(4, 'Servicios Delta S.A.C.', '20444555666', 'Calle Los Pinos 220, San Borja', 'admin@servdelta.pe', '01 901-2345', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(5, 'Tecnología Epsilon S.R.L.', '20555666777', 'Av. Benavides 4550, Miraflores', 'soporte@epsilon.tech', '01 012-3456', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(6, 'Constructora Zeta SAC', '20666777888', 'Av. Húsares de Junín 320, Trujillo', 'obras@zeta.pe', '044 12-3456', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(7, 'Distribuidora Eta E.I.R.L.', '20777888999', 'Mercado Mayorista s/n, Arequipa', 'ventas@eta-distrib.pe', '054 23-4567', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(8, 'Consultora Theta S.A.C.', '20888999000', 'Av. Camino Real 390, San Isidro', 'consult@theta.pe', '01 123-4567', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(9, 'Agencia Iota S.A.', '20999000111', 'Jr. de la Unión 548, Lima', 'info@iota-agency.pe', '01 234-5670', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(10, 'Industrias Kappa SAC', '20100200300', 'Av. Ferrocarril 1050, Huancayo', 'planta@kappa-ind.pe', '064 34-5678', '2026-04-24 17:38:37', '2026-04-24 17:38:37');

-- Volcando estructura para tabla cotizacion.failed_jobs
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

-- Volcando datos para la tabla cotizacion.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Volcando estructura para tabla cotizacion.jobs
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

-- Volcando datos para la tabla cotizacion.jobs: ~0 rows (aproximadamente)
DELETE FROM `jobs`;

-- Volcando estructura para tabla cotizacion.job_batches
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

-- Volcando datos para la tabla cotizacion.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Volcando estructura para tabla cotizacion.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla cotizacion.migrations: ~1 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_04_22_064820_create_clients_table', 1),
	(5, '2026_04_22_064820_create_companies_table', 1),
	(6, '2026_04_22_064821_create_products_table', 1),
	(7, '2026_04_22_064822_create_quotations_table', 1),
	(8, '2026_04_22_064823_create_quotation_details_table', 1),
	(9, '2026_04_24_000001_add_real_world_features', 2);

-- Volcando estructura para tabla cotizacion.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla cotizacion.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Volcando estructura para tabla cotizacion.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Unidad de medida: und, kg, hr, m2, lt...',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla cotizacion.products: ~10 rows (aproximadamente)
DELETE FROM `products`;
INSERT INTO `products` (`id`, `name`, `description`, `price`, `unit`, `created_at`, `updated_at`) VALUES
	(1, 'Consultoría Tecnológica', 'Asesoría en transformación digital y sistemas de información', 250.00, 'hr', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(2, 'Desarrollo de Software a Medida', 'Desarrollo de aplicaciones web y móviles personalizadas', 3500.00, 'mes', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(3, 'Licencia ERP Anual', 'Licencia de uso anual del sistema ERP empresarial', 1800.00, 'año', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(4, 'Mantenimiento de Servidores', 'Servicio mensual de mantenimiento y monitoreo de infraestructura', 850.00, 'mes', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(5, 'Capacitación de Usuarios', 'Taller presencial o virtual de formación en el uso del sistema', 400.00, 'sesión', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(6, 'Soporte Técnico Premium', 'Soporte prioritario 24/7 con SLA garantizado', 600.00, 'mes', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(7, 'Migración de Base de Datos', 'Migración y validación de datos entre sistemas', 2200.00, 'und', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(8, 'Auditoría de Seguridad', 'Evaluación de vulnerabilidades y reporte ejecutivo', 3000.00, 'und', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(9, 'Diseño UI/UX de Aplicación', 'Diseño de interfaces y experiencia de usuario', 1500.00, 'proyecto', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(10, 'Hosting Cloud Premium (Anual)', 'Alojamiento en la nube con 99.9% uptime garantizado', 950.00, 'año', '2026-04-24 17:38:37', '2026-04-24 17:38:37');

-- Volcando estructura para tabla cotizacion.quotations
CREATE TABLE IF NOT EXISTS `quotations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `client_id` bigint unsigned NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `subtotal` decimal(12,2) NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Borrador',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotations_quotation_number_unique` (`quotation_number`),
  KEY `quotations_client_id_foreign` (`client_id`),
  CONSTRAINT `quotations_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla cotizacion.quotations: ~10 rows (aproximadamente)
DELETE FROM `quotations`;
INSERT INTO `quotations` (`id`, `quotation_number`, `issue_date`, `due_date`, `client_id`, `currency`, `subtotal`, `discount_amount`, `tax_amount`, `total`, `status`, `notes`, `terms`, `created_at`, `updated_at`) VALUES
	(1, 'COT-2025-0001', '2025-11-12', '2025-12-12', 1, 'PEN', 3300.00, 0.00, 594.00, 3894.00, 'Aprobada', 'Cotización de demostración generada automáticamente.', '1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV (18%).', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(2, 'COT-2025-0002', '2025-11-20', '2025-12-20', 2, 'PEN', 11700.00, 0.00, 2106.00, 13806.00, 'Aprobada', 'Cotización de demostración generada automáticamente.', '1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV (18%).', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(3, 'COT-2025-0003', '2025-12-20', '2026-01-19', 3, 'PEN', 4550.00, 0.00, 819.00, 5369.00, 'Aprobada', 'Cotización de demostración generada automáticamente.', '1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV (18%).', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(4, 'COT-2025-0004', '2025-12-22', '2026-01-21', 4, 'PEN', 4750.00, 0.00, 855.00, 5605.00, 'Rechazada', 'Cotización de demostración generada automáticamente.', '1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV (18%).', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(5, 'COT-2026-0005', '2026-01-13', '2026-02-12', 5, 'USD', 5400.00, 0.00, 972.00, 6372.00, 'Aprobada', 'Cotización de demostración generada automáticamente.', '1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV (18%).', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(6, 'COT-2026-0006', '2026-01-23', '2026-02-22', 6, 'PEN', 2300.00, 0.00, 414.00, 2714.00, 'Emitida', 'Cotización de demostración generada automáticamente.', '1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV (18%).', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(7, 'COT-2026-0007', '2026-02-09', '2026-03-11', 7, 'PEN', 12000.00, 0.00, 2160.00, 14160.00, 'Aprobada', 'Cotización de demostración generada automáticamente.', '1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV (18%).', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(8, 'COT-2026-0008', '2026-02-19', '2026-03-21', 8, 'PEN', 6900.00, 0.00, 1242.00, 8142.00, 'Emitida', 'Cotización de demostración generada automáticamente.', '1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV (18%).', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(9, 'COT-2026-0009', '2026-03-10', '2026-04-09', 9, 'PEN', 6500.00, 0.00, 1170.00, 7670.00, 'Aprobada', 'Cotización de demostración generada automáticamente.', '1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV (18%).', '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(10, 'COT-2026-0010', '2026-04-17', '2026-05-17', 10, 'PEN', 4600.00, 0.00, 828.00, 5428.00, 'Borrador', 'Cotización de demostración generada automáticamente.', '1. Los precios son válidos por 30 días.\n2. Plazo de entrega según acuerdo.\n3. Precios incluyen IGV (18%).', '2026-04-24 17:38:37', '2026-04-24 17:38:37');

-- Volcando estructura para tabla cotizacion.quotation_details
CREATE TABLE IF NOT EXISTS `quotation_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `discount_pct` decimal(5,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_details_quotation_id_foreign` (`quotation_id`),
  KEY `quotation_details_product_id_foreign` (`product_id`),
  CONSTRAINT `quotation_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotation_details_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla cotizacion.quotation_details: ~20 rows (aproximadamente)
DELETE FROM `quotation_details`;
INSERT INTO `quotation_details` (`id`, `quotation_id`, `product_id`, `product_name`, `unit`, `quantity`, `unit_price`, `discount_pct`, `subtotal`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Consultoría Tecnológica', 'hr', 10, 250.00, 0.00, 2500.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(2, 1, 5, 'Capacitación de Usuarios', 'sesión', 2, 400.00, 0.00, 800.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(3, 2, 2, 'Desarrollo de Software a Medida', 'mes', 3, 3500.00, 0.00, 10500.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(4, 2, 6, 'Soporte Técnico Premium', 'mes', 2, 600.00, 0.00, 1200.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(5, 3, 3, 'Licencia ERP Anual', 'año', 2, 1800.00, 0.00, 3600.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(6, 3, 10, 'Hosting Cloud Premium (Anual)', 'año', 1, 950.00, 0.00, 950.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(7, 4, 7, 'Migración de Base de Datos', 'und', 1, 2200.00, 0.00, 2200.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(8, 4, 4, 'Mantenimiento de Servidores', 'mes', 3, 850.00, 0.00, 2550.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(9, 5, 8, 'Auditoría de Seguridad', 'und', 1, 3000.00, 0.00, 3000.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(10, 5, 6, 'Soporte Técnico Premium', 'mes', 4, 600.00, 0.00, 2400.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(11, 6, 9, 'Diseño UI/UX de Aplicación', 'proyecto', 1, 1500.00, 0.00, 1500.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(12, 6, 5, 'Capacitación de Usuarios', 'sesión', 2, 400.00, 0.00, 800.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(13, 7, 2, 'Desarrollo de Software a Medida', 'mes', 2, 3500.00, 0.00, 7000.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(14, 7, 1, 'Consultoría Tecnológica', 'hr', 20, 250.00, 0.00, 5000.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(15, 8, 4, 'Mantenimiento de Servidores', 'mes', 6, 850.00, 0.00, 5100.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(16, 8, 6, 'Soporte Técnico Premium', 'mes', 3, 600.00, 0.00, 1800.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(17, 9, 9, 'Diseño UI/UX de Aplicación', 'proyecto', 2, 1500.00, 0.00, 3000.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(18, 9, 2, 'Desarrollo de Software a Medida', 'mes', 1, 3500.00, 0.00, 3500.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(19, 10, 8, 'Auditoría de Seguridad', 'und', 1, 3000.00, 0.00, 3000.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37'),
	(20, 10, 5, 'Capacitación de Usuarios', 'sesión', 4, 400.00, 0.00, 1600.00, '2026-04-24 17:38:37', '2026-04-24 17:38:37');

-- Volcando estructura para tabla cotizacion.sessions
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

-- Volcando datos para la tabla cotizacion.sessions: ~3 rows (aproximadamente)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('0Q7NN1EixZE7LSzEKR4CuQZQ2WdUq3utjpN4mavL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; es-PE) WindowsPowerShell/5.1.26100.8115', 'eyJfdG9rZW4iOiJsRUJMcXZiaGxzRHgycWw3aG4xSGlNalRCaGx5WUlZOXBUblNlcGs4IiwiZXJyb3IiOiJFcnJvciBnZW5lcmFuZG8gZWwgcmVzcGFsZG86IHJlc2V0KCk6IEFyZ3VtZW50ICMxICgkYXJyYXkpIG11c3QgYmUgcGFzc2VkIGJ5IHJlZmVyZW5jZSwgdmFsdWUgZ2l2ZW4iLCJfZmxhc2giOnsibmV3IjpbXSwib2xkIjpbImVycm9yIl19LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3Rlc3QtZHVtcCIsInJvdXRlIjpudWxsfX0=', 1777021252),
	('81IfjO8seie1uUJlqpnN1muZxukn3IEk7pgS6n8U', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; es-PE) WindowsPowerShell/5.1.26100.8115', 'eyJfdG9rZW4iOiJJUG5iVEZzZ2JDZUhlb1FPaE9QZ1FONTE5bWhHa1dZM3pNSjU0MHhSIiwiZXJyb3IiOiJFcnJvciBnZW5lcmFuZG8gZWwgcmVzcGFsZG86IHJlc2V0KCk6IEFyZ3VtZW50ICMxICgkYXJyYXkpIG11c3QgYmUgcGFzc2VkIGJ5IHJlZmVyZW5jZSwgdmFsdWUgZ2l2ZW4iLCJfZmxhc2giOnsibmV3IjpbXSwib2xkIjpbImVycm9yIl19LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3Rlc3QtZHVtcCIsInJvdXRlIjpudWxsfX0=', 1777021340),
	('BlQgXsw94PvivFn6iz1OK0YhyLsost7Wf35C63D2', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJwZTlwVEp6SWlKU0Y1Rm93cTJxdFpWYlh5MjB4dTU1NGlDWGhsQ29PIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1777021007),
	('fBSs447CUOyPByMQctI02zUtEoUc68C7IERs4Zc9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJSMXE2YTE3UUhMVFdFNzVrQnpoajNTTGtUb2RtNEltd1R0MnhLcEZKIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJ1cmwiOnsiaW50ZW5kZWQiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvZGFzaGJvYXJkIn19', 1777020766),
	('XGJ0QEcYMiCChtD06cw1EV3GwgNPlXEGNBXFGkP5', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJybW1IdlU5YTE0bnpnR0c3cDVPbTByZ241TlZXYmhyb1hmcXo4MDBZIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1777034384);

-- Volcando estructura para tabla cotizacion.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla cotizacion.settings: ~17 rows (aproximadamente)
DELETE FROM `settings`;
INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
	(1, 'company_name', 'Mi Empresa S.A.C.', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(2, 'company_ruc', '', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(3, 'company_address', '', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(4, 'company_phone', '', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(5, 'company_email', '', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(6, 'company_website', '', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(7, 'default_currency', 'PEN', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(8, 'default_tax_rate', '18', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(9, 'quotation_prefix', 'COT', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(10, 'terms_and_conditions', '1. Los precios indicados son válidos por 30 días desde la fecha de emisión.\n2. El plazo de entrega se coordinará al confirmar el pedido.\n3. Los pagos se realizarán según lo acordado entre las partes.', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(11, 'smtp_host', '', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(12, 'smtp_port', '587', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(13, 'smtp_username', '', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(14, 'smtp_password', '', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(15, 'smtp_encryption', 'tls', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(16, 'smtp_from_address', 'cotizaciones@miempresa.com', '2026-04-24 13:53:09', '2026-04-24 13:53:09'),
	(17, 'smtp_from_name', 'Mi Empresa', '2026-04-24 13:53:09', '2026-04-24 13:53:09');

-- Volcando estructura para tabla cotizacion.users
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla cotizacion.users: ~1 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Admin', 'admin@test.com', NULL, '$2y$12$qgN51EGRTx1ZuJS6xrPrFOLU9G8Iam7fl/7OPhD1v7S51aBSob82a', NULL, '2026-04-24 11:43:56', '2026-04-24 11:43:56');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
