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


-- Volcando estructura de base de datos para citasmedicas_laravel
CREATE DATABASE IF NOT EXISTS `citasmedicas_laravel` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `citasmedicas_laravel`;

-- Volcando estructura para tabla citasmedicas_laravel.appointments
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` bigint unsigned NOT NULL,
  `doctor_id` bigint unsigned NOT NULL,
  `specialty_id` bigint unsigned NOT NULL,
  `office_id` bigint unsigned DEFAULT NULL,
  `date` datetime NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reminded_24h` tinyint(1) NOT NULL DEFAULT '0',
  `reminded_1h` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `appointment_type_id` bigint unsigned DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointments_patient_id_foreign` (`patient_id`),
  KEY `appointments_doctor_id_foreign` (`doctor_id`),
  KEY `appointments_specialty_id_foreign` (`specialty_id`),
  KEY `appointments_office_id_foreign` (`office_id`),
  KEY `appointments_appointment_type_id_foreign` (`appointment_type_id`),
  CONSTRAINT `appointments_appointment_type_id_foreign` FOREIGN KEY (`appointment_type_id`) REFERENCES `appointment_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_office_id_foreign` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_specialty_id_foreign` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.appointments: ~5 rows (aproximadamente)
DELETE FROM `appointments`;
INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `specialty_id`, `office_id`, `date`, `status`, `reminded_24h`, `reminded_1h`, `notes`, `reason`, `cancellation_reason`, `created_at`, `updated_at`, `appointment_type_id`, `end_time`) VALUES
	(1, 1, 2, 2, 1, '2026-02-24 14:00:00', 'confirmed', 0, 0, 'chequeo', NULL, NULL, '2026-02-24 22:48:57', '2026-02-25 10:51:25', 1, '2026-02-24 14:30:00'),
	(2, 1, 2, 2, 1, '2026-02-26 15:00:00', 'pending', 0, 0, NULL, NULL, NULL, '2026-02-25 02:25:58', '2026-02-25 02:25:58', 1, '2026-02-26 15:30:00'),
	(3, 2, 4, 3, 2, '2026-02-27 10:00:00', 'pending', 0, 0, 'chequeo', NULL, NULL, '2026-02-25 10:51:07', '2026-02-25 10:51:07', 2, '2026-02-27 10:30:00'),
	(4, 1, 2, 2, 1, '2026-03-05 14:00:00', 'pending', 0, 0, 'chequeo', NULL, NULL, '2026-03-03 18:39:25', '2026-03-03 18:39:25', 1, '2026-03-05 14:30:00'),
	(5, 1, 2, 2, 1, '2026-03-05 15:30:00', 'pending', 0, 0, NULL, NULL, NULL, '2026-03-03 18:40:10', '2026-03-03 18:40:10', 1, '2026-03-05 16:00:00'),
	(6, 1, 2, 2, 1, '2026-03-05 14:30:00', 'pending', 0, 0, NULL, NULL, NULL, '2026-03-05 21:21:03', '2026-03-05 21:21:03', 1, '2026-03-05 15:00:00');

-- Volcando estructura para tabla citasmedicas_laravel.appointment_types
CREATE TABLE IF NOT EXISTS `appointment_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `doctor_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration_minutes` int NOT NULL DEFAULT '30',
  `price` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointment_types_doctor_id_foreign` (`doctor_id`),
  CONSTRAINT `appointment_types_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.appointment_types: ~2 rows (aproximadamente)
DELETE FROM `appointment_types`;
INSERT INTO `appointment_types` (`id`, `doctor_id`, `name`, `duration_minutes`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 2, 'Control general', 30, 60.00, 1, '2026-02-24 22:44:13', '2026-02-25 10:50:33'),
	(2, 4, 'Control general', 30, 60.00, 1, '2026-02-25 10:50:06', '2026-02-25 10:50:06');

-- Volcando estructura para tabla citasmedicas_laravel.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.cache: ~0 rows (aproximadamente)
DELETE FROM `cache`;

-- Volcando estructura para tabla citasmedicas_laravel.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Volcando estructura para tabla citasmedicas_laravel.diagnostic_templates
CREATE TABLE IF NOT EXISTS `diagnostic_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `doctor_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icd_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diagnosis_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `treatment_text` text COLLATE utf8mb4_unicode_ci,
  `prescriptions_text` text COLLATE utf8mb4_unicode_ci,
  `notes_text` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `diagnostic_templates_doctor_id_foreign` (`doctor_id`),
  CONSTRAINT `diagnostic_templates_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.diagnostic_templates: ~0 rows (aproximadamente)
DELETE FROM `diagnostic_templates`;

-- Volcando estructura para tabla citasmedicas_laravel.doctors
CREATE TABLE IF NOT EXISTS `doctors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `specialty_id` bigint unsigned NOT NULL,
  `collegiate_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `biography` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `doctors_collegiate_number_unique` (`collegiate_number`),
  KEY `doctors_user_id_foreign` (`user_id`),
  KEY `doctors_specialty_id_foreign` (`specialty_id`),
  CONSTRAINT `doctors_specialty_id_foreign` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `doctors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.doctors: ~2 rows (aproximadamente)
DELETE FROM `doctors`;
INSERT INTO `doctors` (`id`, `user_id`, `specialty_id`, `collegiate_number`, `biography`, `created_at`, `updated_at`) VALUES
	(1, 3, 1, '101010', NULL, '2026-02-24 22:41:16', '2026-02-24 22:41:16'),
	(2, 4, 2, '202020', NULL, '2026-02-24 22:41:41', '2026-02-24 22:41:41'),
	(3, 5, 1, '101011', NULL, '2026-02-24 22:42:09', '2026-02-24 22:42:09'),
	(4, 7, 3, '505050', NULL, '2026-02-25 10:28:14', '2026-02-25 10:28:14');

-- Volcando estructura para tabla citasmedicas_laravel.doctor_blocked_dates
CREATE TABLE IF NOT EXISTS `doctor_blocked_dates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `doctor_id` bigint unsigned NOT NULL,
  `blocked_date` date NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `doctor_blocked_dates_doctor_id_blocked_date_unique` (`doctor_id`,`blocked_date`),
  KEY `doctor_blocked_dates_doctor_id_blocked_date_index` (`doctor_id`,`blocked_date`),
  CONSTRAINT `doctor_blocked_dates_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.doctor_blocked_dates: ~0 rows (aproximadamente)
DELETE FROM `doctor_blocked_dates`;
INSERT INTO `doctor_blocked_dates` (`id`, `doctor_id`, `blocked_date`, `reason`, `created_at`, `updated_at`) VALUES
	(1, 2, '2026-02-27', 'permiso', '2026-02-24 22:43:42', '2026-02-24 22:43:42'),
	(2, 4, '2026-02-25', 'permiso', '2026-02-25 10:49:47', '2026-02-25 10:49:47');

-- Volcando estructura para tabla citasmedicas_laravel.doctor_schedules
CREATE TABLE IF NOT EXISTS `doctor_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `doctor_id` bigint unsigned NOT NULL,
  `day_of_week` tinyint NOT NULL COMMENT '0=Sunday, 1=Monday, ..., 6=Saturday',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `slot_duration` smallint NOT NULL DEFAULT '30' COMMENT 'Duration in minutes',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `doctor_schedules_doctor_id_day_of_week_index` (`doctor_id`,`day_of_week`),
  CONSTRAINT `doctor_schedules_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.doctor_schedules: ~2 rows (aproximadamente)
DELETE FROM `doctor_schedules`;
INSERT INTO `doctor_schedules` (`id`, `doctor_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 2, 2, '14:00:00', '17:00:00', 30, 1, '2026-02-24 22:43:09', '2026-02-24 22:43:09'),
	(2, 2, 4, '14:00:00', '16:00:00', 30, 1, '2026-02-24 22:43:28', '2026-02-24 22:43:28'),
	(3, 4, 5, '08:00:00', '13:00:00', 30, 1, '2026-02-25 10:49:34', '2026-02-25 10:49:34');

-- Volcando estructura para tabla citasmedicas_laravel.email_templates
CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `variables` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Available variables like {patient}, {doctor}',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_templates_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.email_templates: ~4 rows (aproximadamente)
DELETE FROM `email_templates`;
INSERT INTO `email_templates` (`id`, `name`, `subject`, `body`, `variables`, `created_at`, `updated_at`) VALUES
	(1, 'appointment_confirmed', 'Confirmación de su cita médica', 'Hola {paciente},\n\nLe confirmamos que su cita médica ha sido agendada exitosamente.\n\nDetalles de la cita:\n- Médico: {medico}\n- Especialidad: {especialidad}\n- Fecha: {fecha}\n- Hora: {hora}\n\nGracias por confiar en nosotros.', '{paciente}, {medico}, {especialidad}, {fecha}, {hora}', '2026-02-25 11:14:42', '2026-02-25 11:14:42'),
	(2, 'appointment_cancelled', 'Cancelación de su cita médica', 'Hola {paciente},\n\nLe informamos que su cita médica programada para el {fecha} a las {hora} con el Dr. {medico} ha sido cancelada.\n\nMotivo: {motivo}\n\nSi desea reagendar, por favor contáctenos.\n\nSaludos.', '{paciente}, {fecha}, {hora}, {medico}, {motivo}', '2026-02-25 11:14:42', '2026-02-25 11:14:42'),
	(3, 'appointment_reminder_24h', 'Recordatorio: Cita médica mañana', 'Hola {paciente},\n\nEste es un recordatorio de su próxima cita médica mañana.\n\nDetalles:\n- Médico: {medico}\n- Especialidad: {especialidad}\n- Fecha: {fecha}\n- Hora: {hora}\n\nPor favor, llegue 10 minutos antes.\n\n¡Le esperamos!', '{paciente}, {medico}, {especialidad}, {fecha}, {hora}', '2026-02-25 11:14:42', '2026-02-25 11:14:42'),
	(4, 'appointment_reminder_1h', 'Recordatorio Urgente: Su cita médica en 1 hora', 'Hola {paciente},\n\nEste es un recordatorio urgente. Su cita médica es dentro de 1 hora.\n\nDetalles:\n- Médico: {medico}\n- Especialidad: {especialidad}\n- Fecha: {fecha}\n- Hora: {hora}\n\nPor favor, llegue 10 minutos antes.\n\n¡Le esperamos!', '{paciente}, {medico}, {especialidad}, {fecha}, {hora}', '2026-02-25 11:14:42', '2026-02-25 11:14:42');

-- Volcando estructura para tabla citasmedicas_laravel.failed_jobs
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

-- Volcando datos para la tabla citasmedicas_laravel.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Volcando estructura para tabla citasmedicas_laravel.insurances
CREATE TABLE IF NOT EXISTS `insurances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rnc_or_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coverage_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.insurances: ~0 rows (aproximadamente)
DELETE FROM `insurances`;

-- Volcando estructura para tabla citasmedicas_laravel.invoices
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `appointment_id` bigint unsigned NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `status` enum('pending','paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `insurance_id` bigint unsigned DEFAULT NULL,
  `insurance_coverage_amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `patient_copay_amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `invoices_appointment_id_foreign` (`appointment_id`),
  KEY `invoices_insurance_id_foreign` (`insurance_id`),
  CONSTRAINT `invoices_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_insurance_id_foreign` FOREIGN KEY (`insurance_id`) REFERENCES `insurances` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.invoices: ~0 rows (aproximadamente)
DELETE FROM `invoices`;

-- Volcando estructura para tabla citasmedicas_laravel.jobs
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.jobs: ~6 rows (aproximadamente)
DELETE FROM `jobs`;
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
	(1, 'default', '{"uuid":"74ea78f4-eb45-4404-838e-342e2eb81772","displayName":"App\\\\Notifications\\\\AppointmentConfirmed","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:6;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\AppointmentConfirmed\\":2:{s:11:\\"appointment\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:22:\\"App\\\\Models\\\\Appointment\\";s:2:\\"id\\";i:1;s:9:\\"relations\\";a:4:{i:0;s:7:\\"patient\\";i:1;s:12:\\"patient.user\\";i:2;s:6:\\"doctor\\";i:3;s:9:\\"specialty\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:2:\\"id\\";s:36:\\"672d512b-3fdc-4574-85ef-3bde570e494a\\";}s:8:\\"channels\\";a:1:{i:0;s:4:\\"mail\\";}}","batchId":null},"createdAt":1771998685,"delay":null}', 0, NULL, 1771998685, 1771998685),
	(2, 'default', '{"uuid":"e89121b8-6e3c-4604-982a-f5836ab8d03a","displayName":"App\\\\Notifications\\\\AppointmentConfirmed","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:6;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\AppointmentConfirmed\\":2:{s:11:\\"appointment\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:22:\\"App\\\\Models\\\\Appointment\\";s:2:\\"id\\";i:1;s:9:\\"relations\\";a:4:{i:0;s:7:\\"patient\\";i:1;s:12:\\"patient.user\\";i:2;s:6:\\"doctor\\";i:3;s:9:\\"specialty\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:2:\\"id\\";s:36:\\"672d512b-3fdc-4574-85ef-3bde570e494a\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}","batchId":null},"createdAt":1771998685,"delay":null}', 0, NULL, 1771998685, 1771998685),
	(3, 'default', '{"uuid":"d40a1782-fbf1-4655-9335-0772470b1f78","displayName":"App\\\\Notifications\\\\AppointmentCancelled","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:6;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\AppointmentCancelled\\":2:{s:11:\\"appointment\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:22:\\"App\\\\Models\\\\Appointment\\";s:2:\\"id\\";i:1;s:9:\\"relations\\";a:5:{i:0;s:7:\\"patient\\";i:1;s:12:\\"patient.user\\";i:2;s:6:\\"doctor\\";i:3;s:11:\\"doctor.user\\";i:4;s:9:\\"specialty\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:2:\\"id\\";s:36:\\"e8742c10-de5e-4c08-88fd-79a2177b7774\\";}s:8:\\"channels\\";a:1:{i:0;s:4:\\"mail\\";}}","batchId":null},"createdAt":1772000482,"delay":null}', 0, NULL, 1772000482, 1772000482),
	(4, 'default', '{"uuid":"670c5097-7201-4a0f-981c-df2ca074c827","displayName":"App\\\\Notifications\\\\AppointmentCancelled","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:6;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\AppointmentCancelled\\":2:{s:11:\\"appointment\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:22:\\"App\\\\Models\\\\Appointment\\";s:2:\\"id\\";i:1;s:9:\\"relations\\";a:5:{i:0;s:7:\\"patient\\";i:1;s:12:\\"patient.user\\";i:2;s:6:\\"doctor\\";i:3;s:11:\\"doctor.user\\";i:4;s:9:\\"specialty\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:2:\\"id\\";s:36:\\"e8742c10-de5e-4c08-88fd-79a2177b7774\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}","batchId":null},"createdAt":1772000482,"delay":null}', 0, NULL, 1772000482, 1772000482),
	(5, 'default', '{"uuid":"3d5a8d74-30d1-429d-9285-11a790d0e8d6","displayName":"App\\\\Notifications\\\\AppointmentConfirmed","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:6;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\AppointmentConfirmed\\":2:{s:11:\\"appointment\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:22:\\"App\\\\Models\\\\Appointment\\";s:2:\\"id\\";i:1;s:9:\\"relations\\";a:5:{i:0;s:7:\\"patient\\";i:1;s:12:\\"patient.user\\";i:2;s:6:\\"doctor\\";i:3;s:11:\\"doctor.user\\";i:4;s:9:\\"specialty\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:2:\\"id\\";s:36:\\"447a1195-67ed-4125-a79a-6911a2f40dff\\";}s:8:\\"channels\\";a:1:{i:0;s:4:\\"mail\\";}}","batchId":null},"createdAt":1772000482,"delay":null}', 0, NULL, 1772000482, 1772000482),
	(6, 'default', '{"uuid":"78c1d61a-13c7-4ced-904e-4411aa1a2592","displayName":"App\\\\Notifications\\\\AppointmentConfirmed","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:6;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\AppointmentConfirmed\\":2:{s:11:\\"appointment\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:22:\\"App\\\\Models\\\\Appointment\\";s:2:\\"id\\";i:1;s:9:\\"relations\\";a:5:{i:0;s:7:\\"patient\\";i:1;s:12:\\"patient.user\\";i:2;s:6:\\"doctor\\";i:3;s:11:\\"doctor.user\\";i:4;s:9:\\"specialty\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:2:\\"id\\";s:36:\\"447a1195-67ed-4125-a79a-6911a2f40dff\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}","batchId":null},"createdAt":1772000482,"delay":null}', 0, NULL, 1772000482, 1772000482);

-- Volcando estructura para tabla citasmedicas_laravel.job_batches
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

-- Volcando datos para la tabla citasmedicas_laravel.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Volcando estructura para tabla citasmedicas_laravel.medical_records
CREATE TABLE IF NOT EXISTS `medical_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` bigint unsigned NOT NULL,
  `doctor_id` bigint unsigned NOT NULL,
  `appointment_id` bigint unsigned DEFAULT NULL,
  `record_date` date NOT NULL,
  `chief_complaint` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `diagnosis` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `treatment` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `prescriptions` text COLLATE utf8mb4_unicode_ci,
  `referred_to` text COLLATE utf8mb4_unicode_ci,
  `blood_pressure` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `heart_rate` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temperature` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oxygen_saturation` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medical_records_patient_id_foreign` (`patient_id`),
  KEY `medical_records_doctor_id_foreign` (`doctor_id`),
  KEY `medical_records_appointment_id_foreign` (`appointment_id`),
  CONSTRAINT `medical_records_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `medical_records_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `medical_records_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.medical_records: ~0 rows (aproximadamente)
DELETE FROM `medical_records`;

-- Volcando estructura para tabla citasmedicas_laravel.medical_record_attachments
CREATE TABLE IF NOT EXISTS `medical_record_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `medical_record_id` bigint unsigned NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medical_record_attachments_medical_record_id_foreign` (`medical_record_id`),
  CONSTRAINT `medical_record_attachments_medical_record_id_foreign` FOREIGN KEY (`medical_record_id`) REFERENCES `medical_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.medical_record_attachments: ~0 rows (aproximadamente)
DELETE FROM `medical_record_attachments`;

-- Volcando estructura para tabla citasmedicas_laravel.messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint unsigned NOT NULL,
  `receiver_id` bigint unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.messages: ~0 rows (aproximadamente)
DELETE FROM `messages`;

-- Volcando estructura para tabla citasmedicas_laravel.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.migrations: ~36 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_02_23_160809_create_permission_tables', 1),
	(5, '2026_02_23_161017_create_specialties_table', 1),
	(6, '2026_02_23_161018_create_doctors_table', 1),
	(7, '2026_02_23_161018_create_patients_table', 1),
	(8, '2026_02_23_161019_create_appointments_table', 1),
	(9, '2026_02_23_161019_create_invoices_table', 1),
	(10, '2026_02_24_012127_add_fields_to_appointments_table', 1),
	(11, '2026_02_24_015639_create_medical_records_table', 1),
	(12, '2026_02_24_061942_create_settings_table', 1),
	(13, '2026_02_24_063321_create_notifications_table', 1),
	(14, '2026_02_24_082000_create_doctor_schedules_table', 1),
	(15, '2026_02_24_082001_create_doctor_blocked_dates_table', 1),
	(16, '2026_02_24_084200_add_attachments_to_medical_records_table', 1),
	(17, '2026_02_24_090500_add_avatar_to_users_table', 1),
	(18, '2026_02_24_091500_add_primary_doctor_to_patients_table', 1),
	(19, '2026_02_24_092000_create_offices_table', 1),
	(20, '2026_02_24_092500_add_office_id_to_appointments_table', 1),
	(21, '2026_02_24_093000_create_prescriptions_table', 1),
	(22, '2026_02_24_094000_create_appointment_types_table', 1),
	(23, '2026_02_24_094005_add_appointment_type_to_appointments_table', 1),
	(24, '2026_02_24_095000_create_waitlists_table', 1),
	(25, '2026_02_24_100205_create_medical_record_attachments_table', 1),
	(26, '2026_02_24_165907_create_customer_columns', 1),
	(27, '2026_02_24_165908_create_subscriptions_table', 1),
	(28, '2026_02_24_165909_create_subscription_items_table', 1),
	(29, '2026_02_24_165910_add_meter_id_to_subscription_items_table', 1),
	(30, '2026_02_24_165911_add_meter_event_name_to_subscription_items_table', 1),
	(31, '2026_02_24_205051_create_diagnostic_templates_table', 2),
	(32, '2026_02_24_205757_create_insurances_table', 2),
	(33, '2026_02_24_205801_add_insurance_to_patients_table', 2),
	(34, '2026_02_24_205804_add_insurance_to_invoices_table', 2),
	(35, '2026_02_25_053244_create_messages_table', 2),
	(36, '2026_02_25_054124_add_two_factor_columns_to_users_table', 3),
	(37, '2026_02_25_060118_add_reminder_flags_to_appointments_table', 4),
	(38, '2026_02_25_061310_create_email_templates_table', 5);

-- Volcando estructura para tabla citasmedicas_laravel.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.model_has_permissions: ~0 rows (aproximadamente)
DELETE FROM `model_has_permissions`;

-- Volcando estructura para tabla citasmedicas_laravel.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.model_has_roles: ~0 rows (aproximadamente)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 2);

-- Volcando estructura para tabla citasmedicas_laravel.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.notifications: ~0 rows (aproximadamente)
DELETE FROM `notifications`;

-- Volcando estructura para tabla citasmedicas_laravel.offices
CREATE TABLE IF NOT EXISTS `offices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `doctor_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `floor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maps_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offices_doctor_id_foreign` (`doctor_id`),
  CONSTRAINT `offices_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.offices: ~0 rows (aproximadamente)
DELETE FROM `offices`;
INSERT INTO `offices` (`id`, `doctor_id`, `name`, `address`, `floor`, `phone`, `maps_url`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 2, 'Consultorio 1', 'direccion 1', '1', '999-999-999', NULL, 1, '2026-02-24 22:42:35', '2026-02-24 22:42:35'),
	(2, 4, 'Consultorio 1', 'LIMA', '1', '30303032', NULL, 1, '2026-02-25 10:49:10', '2026-02-25 10:49:10');

-- Volcando estructura para tabla citasmedicas_laravel.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;
INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
	('test@example.com', '$2y$12$NqoSS8/Pt8IHYlWS/CCGaOt6.2mZ4i3DaWCyjp.gAlIBVm.ff5W9e', '2026-02-25 18:45:45');

-- Volcando estructura para tabla citasmedicas_laravel.patients
CREATE TABLE IF NOT EXISTS `patients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blood_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allergies` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `primary_doctor_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `insurance_id` bigint unsigned DEFAULT NULL,
  `policy_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patients_user_id_foreign` (`user_id`),
  KEY `patients_primary_doctor_id_foreign` (`primary_doctor_id`),
  KEY `patients_insurance_id_foreign` (`insurance_id`),
  CONSTRAINT `patients_insurance_id_foreign` FOREIGN KEY (`insurance_id`) REFERENCES `insurances` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patients_primary_doctor_id_foreign` FOREIGN KEY (`primary_doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.patients: ~1 rows (aproximadamente)
DELETE FROM `patients`;
INSERT INTO `patients` (`id`, `user_id`, `dob`, `gender`, `blood_type`, `allergies`, `phone`, `address`, `primary_doctor_id`, `created_at`, `updated_at`, `insurance_id`, `policy_number`) VALUES
	(1, 6, '2000-04-01', 'male', 'O+', 'ninguna', '30303031', 'direccion 1', NULL, '2026-02-24 22:45:15', '2026-02-24 22:45:15', NULL, NULL),
	(2, 8, '2002-03-06', 'female', 'O+', 'ninguna', '30303032', 'direccion 2', NULL, '2026-02-25 10:47:54', '2026-02-25 10:47:54', NULL, NULL);

-- Volcando estructura para tabla citasmedicas_laravel.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.permissions: ~31 rows (aproximadamente)
DELETE FROM `permissions`;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'patients.view', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(2, 'patients.create', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(3, 'patients.edit', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(4, 'patients.delete', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(5, 'doctors.view', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(6, 'doctors.create', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(7, 'doctors.edit', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(8, 'doctors.delete', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(9, 'schedules.view', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(10, 'schedules.manage', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(11, 'appointments.view', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(12, 'appointments.create', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(13, 'appointments.cancel', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(14, 'appointments.reschedule', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(15, 'medical-records.view', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(16, 'medical-records.create', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(17, 'medical-records.edit', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(18, 'medical-records.delete', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(19, 'invoices.view', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(20, 'invoices.create', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(21, 'invoices.edit', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(22, 'invoices.delete', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(23, 'reports.view', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(24, 'reports.export', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(25, 'settings.view', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(26, 'settings.edit', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(27, 'users.view', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(28, 'users.create', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(29, 'users.edit', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(30, 'users.delete', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(31, 'users.assign-roles', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13');

-- Volcando estructura para tabla citasmedicas_laravel.prescriptions
CREATE TABLE IF NOT EXISTS `prescriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` bigint unsigned NOT NULL,
  `doctor_id` bigint unsigned NOT NULL,
  `medical_record_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prescriptions_patient_id_foreign` (`patient_id`),
  KEY `prescriptions_doctor_id_foreign` (`doctor_id`),
  KEY `prescriptions_medical_record_id_foreign` (`medical_record_id`),
  CONSTRAINT `prescriptions_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `prescriptions_medical_record_id_foreign` FOREIGN KEY (`medical_record_id`) REFERENCES `medical_records` (`id`) ON DELETE SET NULL,
  CONSTRAINT `prescriptions_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.prescriptions: ~0 rows (aproximadamente)
DELETE FROM `prescriptions`;

-- Volcando estructura para tabla citasmedicas_laravel.prescription_items
CREATE TABLE IF NOT EXISTS `prescription_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prescription_id` bigint unsigned NOT NULL,
  `medication_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prescription_items_prescription_id_foreign` (`prescription_id`),
  CONSTRAINT `prescription_items_prescription_id_foreign` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.prescription_items: ~0 rows (aproximadamente)
DELETE FROM `prescription_items`;

-- Volcando estructura para tabla citasmedicas_laravel.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.roles: ~4 rows (aproximadamente)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(2, 'doctor', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(3, 'receptionist', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13'),
	(4, 'patient', 'web', '2026-02-24 22:14:13', '2026-02-24 22:14:13');

-- Volcando estructura para tabla citasmedicas_laravel.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.role_has_permissions: ~52 rows (aproximadamente)
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
	(1, 2),
	(9, 2),
	(10, 2),
	(11, 2),
	(13, 2),
	(15, 2),
	(16, 2),
	(17, 2),
	(1, 3),
	(2, 3),
	(3, 3),
	(5, 3),
	(9, 3),
	(11, 3),
	(12, 3),
	(13, 3),
	(14, 3),
	(19, 3),
	(20, 3),
	(11, 4),
	(15, 4);

-- Volcando estructura para tabla citasmedicas_laravel.sessions
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

-- Volcando datos para la tabla citasmedicas_laravel.sessions: ~0 rows (aproximadamente)
DELETE FROM `sessions`;

-- Volcando estructura para tabla citasmedicas_laravel.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.settings: ~16 rows (aproximadamente)
DELETE FROM `settings`;
INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
	(1, 'logo_path', 'logos/ry1Tqmprr1xnp3JaY7sUhpT47BtbApb3xIokkjrt.png', '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(2, 'clinic_name', 'CitasMédicas', '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(3, 'clinic_tagline', 'Sistema de Gestión Médica', '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(4, 'clinic_ruc', NULL, '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(5, 'clinic_address', NULL, '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(6, 'clinic_phone', NULL, '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(7, 'clinic_email', NULL, '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(8, 'appointment_duration', '30', '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(9, 'appointment_max_days', '60', '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(10, 'working_hours_start', '08:00', '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(11, 'working_hours_end', '18:00', '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(12, 'timezone', 'America/Lima', '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(13, 'currency_symbol', 'S/', '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(14, 'notify_on_confirm', '1', '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(15, 'notify_on_cancel', '1', '2026-02-24 23:06:43', '2026-02-24 23:06:43'),
	(16, 'notify_reminder_24h', '1', '2026-02-24 23:06:43', '2026-02-24 23:06:43');

-- Volcando estructura para tabla citasmedicas_laravel.specialties
CREATE TABLE IF NOT EXISTS `specialties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `specialties_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.specialties: ~3 rows (aproximadamente)
DELETE FROM `specialties`;
INSERT INTO `specialties` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'Medicina general', 'varios', '2026-02-24 22:23:28', '2026-02-25 10:26:22'),
	(2, 'Odontólogia', 'varios', '2026-02-24 22:30:16', '2026-02-24 22:30:16'),
	(3, 'Pediatria', 'varios', '2026-02-25 10:25:59', '2026-02-25 10:26:09');

-- Volcando estructura para tabla citasmedicas_laravel.subscriptions
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriptions_stripe_id_unique` (`stripe_id`),
  KEY `subscriptions_user_id_stripe_status_index` (`user_id`,`stripe_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.subscriptions: ~0 rows (aproximadamente)
DELETE FROM `subscriptions`;

-- Volcando estructura para tabla citasmedicas_laravel.subscription_items
CREATE TABLE IF NOT EXISTS `subscription_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint unsigned NOT NULL,
  `stripe_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_product` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meter_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `meter_event_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_items_stripe_id_unique` (`stripe_id`),
  KEY `subscription_items_subscription_id_stripe_price_index` (`subscription_id`,`stripe_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.subscription_items: ~0 rows (aproximadamente)
DELETE FROM `subscription_items`;

-- Volcando estructura para tabla citasmedicas_laravel.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stripe_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_last_four` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_stripe_id_index` (`stripe_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.users: ~7 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `avatar`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `created_at`, `updated_at`, `stripe_id`, `pm_type`, `pm_last_four`, `trial_ends_at`) VALUES
	(1, 'Test User', NULL, 'test@example.com', '2026-02-24 22:14:12', '$2y$12$EjEME0z2MGWaqjZ7MkD/FeTmc0yj53JQh4wGI3bAzRMWyN18FjhJ6', NULL, NULL, NULL, 'zeHAz4P3yr', '2026-02-24 22:14:13', '2026-02-24 22:14:13', NULL, NULL, NULL, NULL),
	(2, 'Admin', NULL, 'admin@admin.com', NULL, '$2y$12$nHekXBSGrmEsvGec3.JBqeToxacF2wG5sKU6kZFkrCHaEeZdd.8kq', NULL, NULL, NULL, NULL, '2026-02-24 22:18:41', '2026-02-24 22:18:41', NULL, NULL, NULL, NULL),
	(3, 'medico 1', NULL, 'medico1@admin.com', NULL, '$2y$12$N0vrez1f/KhQPlNWfWkWtuwWs0pj28C1F55rBwvD1.tGArCSM.SZi', NULL, NULL, NULL, NULL, '2026-02-24 22:41:16', '2026-02-24 22:41:16', NULL, NULL, NULL, NULL),
	(4, 'medico 2', NULL, 'medico2@admin.com', NULL, '$2y$12$bdaOWUzvXGvDMYsfeNxGUefW1zioIfUzTu8omC7YOqzP41N4A7gJi', NULL, NULL, NULL, NULL, '2026-02-24 22:41:41', '2026-02-24 22:41:41', NULL, NULL, NULL, NULL),
	(5, 'medico 3', NULL, 'medico3@admin.com', NULL, '$2y$12$6CvNldwSKYWexCeFu3a/Yuu6BpwLG03ECcaVigz1AGeUpxssEwnHy', NULL, NULL, NULL, NULL, '2026-02-24 22:42:09', '2026-02-24 22:42:09', NULL, NULL, NULL, NULL),
	(6, 'paciente 1', NULL, 'paciente1@admin.com', NULL, '$2y$12$IxpoDRoSSFnA57rKhtzRP.4eUHSuTXE9uHcDoWc8zaeU629/DNH3y', NULL, NULL, NULL, NULL, '2026-02-24 22:45:15', '2026-02-24 22:45:15', NULL, NULL, NULL, NULL),
	(7, 'medico 4', NULL, 'medico4@admin.com', NULL, '$2y$12$OMJVnbsDDgO57bmzza2/qOM35LhmMFPvo2Kk4vf7ng2lLwDQKNdP6', NULL, NULL, NULL, NULL, '2026-02-25 10:28:14', '2026-02-25 10:28:14', NULL, NULL, NULL, NULL),
	(8, 'paciente 2', NULL, 'paciente2@admin.com', NULL, '$2y$12$Zxl6LmhZ7vsl1p96a7tjyehKet./8PaEIuxiA6HguVi7s.sJKgzUm', NULL, NULL, NULL, NULL, '2026-02-25 10:47:54', '2026-02-25 10:47:54', NULL, NULL, NULL, NULL);

-- Volcando estructura para tabla citasmedicas_laravel.waitlists
CREATE TABLE IF NOT EXISTS `waitlists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` bigint unsigned NOT NULL,
  `doctor_id` bigint unsigned NOT NULL,
  `appointment_type_id` bigint unsigned DEFAULT NULL,
  `requested_date_from` date DEFAULT NULL,
  `requested_date_to` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `waitlists_patient_id_foreign` (`patient_id`),
  KEY `waitlists_doctor_id_foreign` (`doctor_id`),
  KEY `waitlists_appointment_type_id_foreign` (`appointment_type_id`),
  CONSTRAINT `waitlists_appointment_type_id_foreign` FOREIGN KEY (`appointment_type_id`) REFERENCES `appointment_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `waitlists_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `waitlists_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla citasmedicas_laravel.waitlists: ~0 rows (aproximadamente)
DELETE FROM `waitlists`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
