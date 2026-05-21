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


-- Volcando estructura de base de datos para odontologia_db
CREATE DATABASE IF NOT EXISTS `odontologia_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `odontologia_db`;

-- Volcando estructura para tabla odontologia_db.categorias_tratamiento
CREATE TABLE IF NOT EXISTS `categorias_tratamiento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.categorias_tratamiento: ~14 rows (aproximadamente)
DELETE FROM `categorias_tratamiento`;
INSERT INTO `categorias_tratamiento` (`id`, `nombre`, `descripcion`, `createdAt`, `updatedAt`) VALUES
	(1, 'Diagnóstico', 'Estudios y evaluaciones iniciales', '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(2, 'Prevención', 'Tratamientos preventivos y de higiene', '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(3, 'Operatoria Dental', 'Restauraciones y obturaciones', '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(4, 'Endodoncia', 'Tratamientos de conducto', '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(5, 'Periodoncia', 'Tratamientos de encías y tejidos de soporte', '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(6, 'Cirugía', 'Extracciones y procedimientos quirúrgicos', '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(7, 'Prótesis', 'Prótesis fijas y removibles', '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(8, 'Ortodoncia', 'Corrección de posición dental', '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(9, 'Implantología', 'Implantes dentales', '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(10, 'Estética Dental', 'Blanqueamiento y carillas', '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(11, 'Odontopediatría', 'Tratamientos para niños', '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(12, 'Radiología', 'Estudios radiográficos', '2026-04-10 06:42:09', '2026-04-10 06:42:09'),
	(13, 'Odontología General', 'Consultas, limpiezas y tratamientos preventivos', '2026-04-18 07:01:53', '2026-04-18 07:01:53'),
	(14, 'Cirugía Bucal', 'Extracciones y procedimientos quirúrgicos', '2026-04-18 07:01:53', '2026-04-18 07:01:53');

-- Volcando estructura para tabla odontologia_db.citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time DEFAULT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('programada','confirmada','en_curso','completada','cancelada','no_asistio') COLLATE utf8mb4_unicode_ci DEFAULT 'programada',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `doctor_id` (`doctor_id`),
  CONSTRAINT `citas_ibfk_57` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `citas_ibfk_58` FOREIGN KEY (`doctor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.citas: ~15 rows (aproximadamente)
DELETE FROM `citas`;
INSERT INTO `citas` (`id`, `paciente_id`, `doctor_id`, `fecha`, `hora_inicio`, `hora_fin`, `motivo`, `estado`, `notas`, `createdAt`, `updatedAt`) VALUES
	(1, 1, 2, '2026-04-06', '09:00:00', '09:30:00', 'Consulta de diagnóstico', 'completada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(2, 2, 2, '2026-04-09', '10:00:00', '10:45:00', 'Limpieza dental', 'completada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(3, 3, 3, '2026-04-11', '11:00:00', '12:00:00', 'Estudio de ortodoncia', 'completada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(4, 4, 4, '2026-04-13', '14:00:00', '15:30:00', 'Tratamiento de conducto', 'completada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(5, 5, 2, '2026-04-16', '09:30:00', '10:00:00', 'Control y revisión', 'completada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(6, 6, 5, '2026-04-18', '15:00:00', '16:00:00', 'Evaluación para implante', 'completada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(7, 7, 2, '2026-04-20', '10:00:00', '10:30:00', 'Consulta de urgencia - dolor molar', 'completada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(8, 1, 2, '2026-04-21', '09:00:00', '10:00:00', 'Obturación pieza 16', 'programada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(9, 8, 3, '2026-04-21', '11:00:00', '11:30:00', 'Control de brackets', 'confirmada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(10, 9, 4, '2026-04-21', '14:00:00', '15:00:00', 'Endodoncia pieza 36', 'programada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(11, 10, 2, '2026-04-22', '09:00:00', '09:30:00', 'Consulta primera vez', 'programada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(12, 3, 3, '2026-04-23', '10:00:00', '11:00:00', 'Colocación de brackets', 'programada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(13, 5, 5, '2026-04-24', '15:00:00', '16:30:00', 'Cirugía de implante', 'confirmada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(14, 2, 2, '2026-04-01', '16:00:00', '16:30:00', 'Consulta de urgencia', 'no_asistio', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(15, 6, 4, '2026-04-14', '11:00:00', '11:30:00', 'Pulpotomía', 'cancelada', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32');

-- Volcando estructura para tabla odontologia_db.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`),
  UNIQUE KEY `clave_2` (`clave`),
  UNIQUE KEY `clave_3` (`clave`),
  UNIQUE KEY `clave_4` (`clave`),
  UNIQUE KEY `clave_5` (`clave`),
  UNIQUE KEY `clave_6` (`clave`),
  UNIQUE KEY `clave_7` (`clave`),
  UNIQUE KEY `clave_8` (`clave`),
  UNIQUE KEY `clave_9` (`clave`),
  UNIQUE KEY `clave_10` (`clave`),
  UNIQUE KEY `clave_11` (`clave`),
  UNIQUE KEY `clave_12` (`clave`),
  UNIQUE KEY `clave_13` (`clave`),
  UNIQUE KEY `clave_14` (`clave`),
  UNIQUE KEY `clave_15` (`clave`),
  UNIQUE KEY `clave_16` (`clave`),
  UNIQUE KEY `clave_17` (`clave`),
  UNIQUE KEY `clave_18` (`clave`),
  UNIQUE KEY `clave_19` (`clave`),
  UNIQUE KEY `clave_20` (`clave`),
  UNIQUE KEY `clave_21` (`clave`),
  UNIQUE KEY `clave_22` (`clave`),
  UNIQUE KEY `clave_23` (`clave`),
  UNIQUE KEY `clave_24` (`clave`),
  UNIQUE KEY `clave_25` (`clave`),
  UNIQUE KEY `clave_26` (`clave`),
  UNIQUE KEY `clave_27` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.configuracion: ~11 rows (aproximadamente)
DELETE FROM `configuracion`;
INSERT INTO `configuracion` (`id`, `clave`, `valor`, `createdAt`, `updatedAt`) VALUES
	(1, 'clinica_nombre', 'Mi Clínica Dental', '2026-04-12 01:09:17', '2026-04-12 01:09:17'),
	(2, 'clinica_direccion', '', '2026-04-12 01:09:17', '2026-04-12 01:09:17'),
	(3, 'clinica_telefono', '', '2026-04-12 01:09:17', '2026-04-12 01:09:17'),
	(4, 'clinica_email', '', '2026-04-12 01:09:17', '2026-04-12 01:09:17'),
	(5, 'clinica_horario_inicio', '08:00', '2026-04-12 01:09:17', '2026-04-12 01:09:17'),
	(6, 'clinica_horario_fin', '18:00', '2026-04-12 01:09:17', '2026-04-12 01:09:17'),
	(7, 'clinica_dias_laborales', 'Lunes a Viernes', '2026-04-12 01:09:17', '2026-04-12 01:09:17'),
	(8, 'clinica_cuit', '', '2026-04-12 01:09:17', '2026-04-12 01:09:17'),
	(9, 'clinica_responsable', '', '2026-04-12 01:09:17', '2026-04-12 01:09:17'),
	(10, 'moneda_simbolo', 'S/', '2026-04-12 01:09:17', '2026-04-12 01:09:17'),
	(11, 'duracion_turno_default', '30', '2026-04-12 01:09:17', '2026-04-12 01:09:17');

-- Volcando estructura para tabla odontologia_db.consentimientos
CREATE TABLE IF NOT EXISTS `consentimientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `tipo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ej: Extracción, Endodoncia, Ortodoncia, Implante, Blanqueamiento',
  `contenido` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `firmado` tinyint(1) DEFAULT '0',
  `fecha_firma` datetime DEFAULT NULL,
  `ip_firma` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `doctor_id` (`doctor_id`),
  CONSTRAINT `consentimientos_ibfk_53` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `consentimientos_ibfk_54` FOREIGN KEY (`doctor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.consentimientos: ~10 rows (aproximadamente)
DELETE FROM `consentimientos`;
INSERT INTO `consentimientos` (`id`, `paciente_id`, `doctor_id`, `tipo`, `contenido`, `firmado`, `fecha_firma`, `ip_firma`, `createdAt`, `updatedAt`) VALUES
	(1, 4, 4, 'Tratamiento de Conducto', 'Yo, Carlos López...', 1, '2026-04-13 00:00:00', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(2, 6, 5, 'Implante Dental', 'Yo, Diego Sánchez...', 0, NULL, NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(3, 3, 3, 'Ortodoncia', 'Yo, Luciana Martínez...', 1, '2026-04-11 00:00:00', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(4, 7, 2, 'Extracción Dental', 'Yo, Valentina Torres...', 1, '2026-04-20 00:00:00', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(5, 9, 4, 'Endodoncia', 'Yo, Sofía Romero...', 0, NULL, NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(6, 1, 2, 'Restauración', 'Autorizo la obturación de pieza 16...', 1, '2026-04-07 00:00:00', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(7, 2, 2, 'Profilaxis', 'Autorizo limpieza...', 1, '2026-04-09 00:00:00', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(8, 5, 5, 'Cirugía', 'Autorizo injerto óseo...', 1, '2026-04-16 00:00:00', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(9, 8, 3, 'Ortodoncia Metálica', 'Autorizo instalación de brackets...', 1, '2026-04-19 00:00:00', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(10, 10, 2, 'Diagnóstico general', 'Autorizo toma de radiografías...', 1, '2026-04-21 14:13:32', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32');

-- Volcando estructura para tabla odontologia_db.detalle_presupuestos
CREATE TABLE IF NOT EXISTS `detalle_presupuestos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `presupuesto_id` int NOT NULL,
  `tratamiento_id` int NOT NULL,
  `pieza_dental` int DEFAULT NULL COMMENT 'Número de pieza dental (1-32)',
  `precio` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','en_curso','completado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `presupuesto_id` (`presupuesto_id`),
  KEY `tratamiento_id` (`tratamiento_id`),
  CONSTRAINT `detalle_presupuestos_ibfk_57` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_presupuestos_ibfk_58` FOREIGN KEY (`tratamiento_id`) REFERENCES `tratamientos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.detalle_presupuestos: ~21 rows (aproximadamente)
DELETE FROM `detalle_presupuestos`;
INSERT INTO `detalle_presupuestos` (`id`, `presupuesto_id`, `tratamiento_id`, `pieza_dental`, `precio`, `estado`, `notas`, `createdAt`, `updatedAt`) VALUES
	(1, 1, 8, 24, 15000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(2, 1, 9, 27, 20000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(3, 2, 4, 15, 12000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(4, 2, 5, 14, 5000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(5, 3, 1, 14, 5000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(6, 3, 2, 28, 3000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(7, 4, 12, 37, 35000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(8, 4, 13, 41, 35000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(9, 5, 1, 26, 5000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(10, 5, 3, 36, 8000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(11, 6, 8, 25, 15000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(12, 6, 10, 12, 25000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(13, 7, 1, 28, 5000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(14, 7, 8, 15, 15000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(15, 8, 1, 35, 5000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(16, 8, 4, 25, 12000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(17, 9, 12, 40, 35000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(18, 9, 14, 41, 45000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(19, 10, 1, 15, 5000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(20, 10, 4, 33, 12000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(21, 10, 5, 35, 5000.00, 'pendiente', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32');

-- Volcando estructura para tabla odontologia_db.historia_clinica
CREATE TABLE IF NOT EXISTS `historia_clinica` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `cita_id` int DEFAULT NULL,
  `fecha` date NOT NULL,
  `diagnostico` text COLLATE utf8mb4_unicode_ci,
  `tratamiento_realizado` text COLLATE utf8mb4_unicode_ci,
  `piezas_tratadas` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Piezas dentales separadas por coma',
  `receta` text COLLATE utf8mb4_unicode_ci,
  `proxima_visita` text COLLATE utf8mb4_unicode_ci,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `cita_id` (`cita_id`),
  CONSTRAINT `historia_clinica_ibfk_81` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `historia_clinica_ibfk_82` FOREIGN KEY (`doctor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `historia_clinica_ibfk_83` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.historia_clinica: ~10 rows (aproximadamente)
DELETE FROM `historia_clinica`;
INSERT INTO `historia_clinica` (`id`, `paciente_id`, `doctor_id`, `cita_id`, `fecha`, `diagnostico`, `tratamiento_realizado`, `piezas_tratadas`, `receta`, `proxima_visita`, `notas`, `createdAt`, `updatedAt`) VALUES
	(1, 1, 2, NULL, '2026-04-06', 'Caries en pieza 16 y 26', 'Radiografía panorámica, evaluación general', '16, 26', NULL, NULL, NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(2, 2, 2, NULL, '2026-04-09', 'Placa bacteriana moderada', 'Profilaxis dental completa con ultrasonido', NULL, NULL, NULL, 'Se recomienda control en 6 meses', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(3, 3, 3, NULL, '2026-04-11', 'Maloclusión clase II', 'Toma de modelos, fotografías y radiografías para estudio', NULL, NULL, NULL, 'Se planifica ortodoncia con brackets estéticos', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(4, 4, 4, NULL, '2026-04-13', 'Pulpitis irreversible pieza 46', 'Tratamiento de conducto birradicular pieza 46', '46', 'Ibuprofeno 600mg c/8hs por 3 días, Amoxicilina 500mg c/8hs por 7 días', NULL, NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(5, 5, 2, NULL, '2026-04-16', 'Control post-tratamiento', 'Revisión general, todo en orden', NULL, NULL, NULL, 'Paciente sin molestias. Próximo control en 3 meses', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(6, 6, 5, NULL, '2026-04-18', 'Ausencia pieza 36, reabsorción ósea leve', 'Tomografía CBCT, planificación de implante', '36', NULL, NULL, 'Hueso suficiente para implante directo sin injerto', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(7, 7, 2, NULL, '2026-04-20', 'Dolor agudo molar 48 - pericoronaritis', 'Drenaje de absceso, medicación', '48', 'Ketorolac 10mg c/8hs, Metronidazol 500mg c/8hs por 5 días', NULL, NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(8, 1, 2, NULL, '2026-04-07', 'Caries profunda pieza 16', 'Obturación compuesta con resina fotocurada', '16', NULL, NULL, NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(9, 4, 4, NULL, '2026-04-15', 'Control post-endodoncia pieza 46', 'Radiografía de control, obturación definitiva y tallado para corona', '46', NULL, NULL, 'Conductos en perfecto estado. Se prepara para corona de zirconio', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(10, 9, 4, NULL, '2026-04-19', 'Caries extensas en piezas 15 y 25', 'Radiografías periapicales, evaluación de vitalidad pulpar', '15, 25', NULL, NULL, 'Pieza 15 responde positivo a test frío, pieza 25 no responde - necrótica', '2026-04-21 14:13:32', '2026-04-21 14:13:32');

-- Volcando estructura para tabla odontologia_db.log_actividad
CREATE TABLE IF NOT EXISTS `log_actividad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `accion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'crear, actualizar, eliminar, login, logout',
  `entidad` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'paciente, cita, presupuesto, pago, etc.',
  `entidad_id` int DEFAULT NULL,
  `detalle` text COLLATE utf8mb4_unicode_ci,
  `ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `log_actividad_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.log_actividad: ~10 rows (aproximadamente)
DELETE FROM `log_actividad`;
INSERT INTO `log_actividad` (`id`, `usuario_id`, `accion`, `entidad`, `entidad_id`, `detalle`, `ip`, `createdAt`) VALUES
	(1, 2, 'login', 'sistema', NULL, 'Inicio de sesión exitoso', NULL, '2026-04-21 14:13:33'),
	(2, 2, 'crear', 'paciente', 1, 'Se registró nuevo paciente', NULL, '2026-04-21 14:13:33'),
	(3, 3, 'actualizar', 'cita', 3, 'Cambio de estado a completada', NULL, '2026-04-21 14:13:33'),
	(4, 4, 'crear', 'presupuesto', 1, 'Presupuesto generado', NULL, '2026-04-21 14:13:33'),
	(5, 5, 'crear', 'historia_clinica', 1, 'Registro de historia actualizado', NULL, '2026-04-21 14:13:33'),
	(6, 2, 'crear', 'pago', 1, 'Pago recibido en efectivo', NULL, '2026-04-21 14:13:33'),
	(7, 3, 'actualizar', 'presupuesto', 2, 'Presupuesto aceptado por paciente', NULL, '2026-04-21 14:13:33'),
	(8, 2, 'eliminar', 'cita', 15, 'Cita cancelada por el paciente', NULL, '2026-04-21 14:13:33'),
	(9, 4, 'crear', 'odontograma', 3, 'Pieza 46 marcada con endodoncia', NULL, '2026-04-21 14:13:33'),
	(10, 5, 'logout', 'sistema', NULL, 'Cierre de sesión', NULL, '2026-04-21 14:13:33');

-- Volcando estructura para tabla odontologia_db.odontograma
CREATE TABLE IF NOT EXISTS `odontograma` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `pieza_dental` int NOT NULL COMMENT 'Número de pieza dental (11-48 notación FDI)',
  `cara` enum('vestibular','lingual','mesial','distal','oclusal','completa') COLLATE utf8mb4_unicode_ci DEFAULT 'completa',
  `estado` enum('sano','caries','obturacion','corona','extraccion','endodoncia','implante','protesis','ausente','fractura') COLLATE utf8mb4_unicode_ci DEFAULT 'sano',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `doctor_id` int DEFAULT NULL,
  `fecha` date NOT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `doctor_id` (`doctor_id`),
  CONSTRAINT `odontograma_ibfk_57` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `odontograma_ibfk_58` FOREIGN KEY (`doctor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.odontograma: ~10 rows (aproximadamente)
DELETE FROM `odontograma`;
INSERT INTO `odontograma` (`id`, `paciente_id`, `pieza_dental`, `cara`, `estado`, `observacion`, `doctor_id`, `fecha`, `createdAt`, `updatedAt`) VALUES
	(1, 1, 16, 'oclusal', 'caries', 'Caries profunda', 2, '2026-04-21', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(2, 1, 26, 'distal', 'obturacion', 'Resina en buen estado', 2, '2026-04-21', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(3, 4, 46, 'completa', 'endodoncia', 'Conducto realizado', 4, '2026-04-21', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(4, 6, 36, 'completa', 'ausente', 'Pieza extraída previamente', 5, '2026-04-21', '2026-04-21 14:13:33', '2026-04-21 14:13:33'),
	(5, 7, 48, 'completa', 'extraccion', 'Indicada para extracción', 2, '2026-04-21', '2026-04-21 14:13:33', '2026-04-21 14:13:33'),
	(6, 9, 15, 'mesial', 'caries', 'Caries leve', 4, '2026-04-21', '2026-04-21 14:13:33', '2026-04-21 14:13:33'),
	(7, 9, 25, 'completa', 'caries', 'Pieza necrótica', 4, '2026-04-21', '2026-04-21 14:13:33', '2026-04-21 14:13:33'),
	(8, 3, 11, 'vestibular', 'sano', 'Control ortodoncia', 3, '2026-04-21', '2026-04-21 14:13:33', '2026-04-21 14:13:33'),
	(9, 3, 21, 'vestibular', 'sano', 'Control ortodoncia', 3, '2026-04-21', '2026-04-21 14:13:33', '2026-04-21 14:13:33'),
	(10, 5, 36, 'completa', 'implante', 'Implante planificado', 5, '2026-04-21', '2026-04-21 14:13:33', '2026-04-21 14:13:33');

-- Volcando estructura para tabla odontologia_db.pacientes
CREATE TABLE IF NOT EXISTS `pacientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('masculino','femenino','otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `obra_social` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_afiliado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `antecedentes_medicos` text COLLATE utf8mb4_unicode_ci,
  `alergias` text COLLATE utf8mb4_unicode_ci,
  `medicamentos` text COLLATE utf8mb4_unicode_ci,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni` (`dni`),
  UNIQUE KEY `dni_2` (`dni`),
  UNIQUE KEY `dni_3` (`dni`),
  UNIQUE KEY `dni_4` (`dni`),
  UNIQUE KEY `dni_5` (`dni`),
  UNIQUE KEY `dni_6` (`dni`),
  UNIQUE KEY `dni_7` (`dni`),
  UNIQUE KEY `dni_8` (`dni`),
  UNIQUE KEY `dni_9` (`dni`),
  UNIQUE KEY `dni_10` (`dni`),
  UNIQUE KEY `dni_11` (`dni`),
  UNIQUE KEY `dni_12` (`dni`),
  UNIQUE KEY `dni_13` (`dni`),
  UNIQUE KEY `dni_14` (`dni`),
  UNIQUE KEY `dni_15` (`dni`),
  UNIQUE KEY `dni_16` (`dni`),
  UNIQUE KEY `dni_17` (`dni`),
  UNIQUE KEY `dni_18` (`dni`),
  UNIQUE KEY `dni_19` (`dni`),
  UNIQUE KEY `dni_20` (`dni`),
  UNIQUE KEY `dni_21` (`dni`),
  UNIQUE KEY `dni_22` (`dni`),
  UNIQUE KEY `dni_23` (`dni`),
  UNIQUE KEY `dni_24` (`dni`),
  UNIQUE KEY `dni_25` (`dni`),
  UNIQUE KEY `dni_26` (`dni`),
  UNIQUE KEY `dni_27` (`dni`),
  UNIQUE KEY `dni_28` (`dni`),
  UNIQUE KEY `dni_29` (`dni`),
  UNIQUE KEY `dni_30` (`dni`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.pacientes: ~10 rows (aproximadamente)
DELETE FROM `pacientes`;
INSERT INTO `pacientes` (`id`, `nombre`, `apellido`, `dni`, `fecha_nacimiento`, `genero`, `telefono`, `email`, `direccion`, `obra_social`, `numero_afiliado`, `antecedentes_medicos`, `alergias`, `medicamentos`, `notas`, `activo`, `createdAt`, `updatedAt`) VALUES
	(1, 'María', 'García', '28456789', '1985-03-15', 'femenino', '1155001234', 'maria.garcia@email.com', 'Av. Rivadavia 1234, CABA', 'OSDE', 'OSE-28456789', NULL, NULL, NULL, NULL, 1, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(2, 'Roberto', 'Fernández', '31987654', '1978-08-22', 'masculino', '1155002345', 'roberto.f@email.com', 'Calle San Martín 567, CABA', 'Swiss Medical', 'SM-31987654', NULL, NULL, NULL, NULL, 1, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(3, 'Luciana', 'Martínez', '35678123', '1992-11-05', 'femenino', '1155003456', 'luciana.m@email.com', 'Av. Corrientes 890, CABA', 'Galeno', 'GAL-35678123', NULL, 'Penicilina', NULL, NULL, 1, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(4, 'Carlos', 'López', '25789456', '1975-01-30', 'masculino', '1155004567', 'carlos.lopez@email.com', 'Calle Belgrano 234, Quilmes', 'OSDE', 'OSE-25789456', 'Diabetes tipo 2', NULL, 'Metformina 500mg', NULL, 1, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(5, 'Ana', 'Rodríguez', '33456789', '1990-06-18', 'femenino', '1155005678', 'ana.rod@email.com', 'Av. Santa Fe 1567, CABA', 'Medicus', NULL, NULL, NULL, NULL, NULL, 1, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(6, 'Diego', 'Sánchez', '29876543', '1982-04-10', 'masculino', '1155006789', 'diego.s@email.com', 'Calle Mitre 456, Avellaneda', 'Swiss Medical', 'SM-29876543', NULL, 'Lidocaína', NULL, NULL, 1, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(7, 'Valentina', 'Torres', '37123456', '1995-09-25', 'femenino', '1155007890', 'vale.torres@email.com', 'Av. Callao 789, CABA', NULL, NULL, 'Embarazo 5 meses', NULL, NULL, NULL, 1, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(8, 'Martín', 'Díaz', '27654321', '1980-12-03', 'masculino', '1155008901', 'martin.diaz@email.com', 'Calle Lavalle 321, CABA', 'OSDE', 'OSE-27654321', NULL, NULL, NULL, NULL, 1, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(9, 'Sofía', 'Romero', '34567890', '1988-07-14', 'femenino', '1155009012', 'sofia.romero@email.com', 'Av. Libertador 2345, Vicente López', 'Galeno', 'GAL-34567890', 'Hipertensión', NULL, NULL, NULL, 1, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(10, 'Federico', 'Morales', '30234567', '1983-02-28', 'masculino', '1155010123', 'fede.morales@email.com', 'Calle Sarmiento 678, Lomas de Zamora', 'Medicus', 'MED-30234567', NULL, NULL, NULL, NULL, 1, '2026-04-21 14:13:32', '2026-04-21 14:13:32');

-- Volcando estructura para tabla odontologia_db.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `presupuesto_id` int DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta_debito','tarjeta_credito','transferencia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `numero_recibo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `presupuesto_id` (`presupuesto_id`),
  CONSTRAINT `pagos_ibfk_57` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pagos_ibfk_58` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.pagos: ~10 rows (aproximadamente)
DELETE FROM `pagos`;
INSERT INTO `pagos` (`id`, `paciente_id`, `presupuesto_id`, `monto`, `metodo_pago`, `fecha`, `numero_recibo`, `notas`, `createdAt`, `updatedAt`) VALUES
	(1, 1, 1, 15000.00, 'efectivo', '2026-04-07', 'R-001', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(2, 1, 1, 10000.00, 'tarjeta_debito', '2026-04-14', 'R-002', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(3, 2, 2, 12000.00, 'transferencia', '2026-04-10', 'R-003', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(4, 3, 3, 50000.00, 'tarjeta_credito', '2026-04-12', 'R-004', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(5, 4, 4, 95000.00, 'transferencia', '2026-04-15', 'R-005', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(6, 5, 5, 125000.00, 'tarjeta_credito', '2026-04-17', 'R-006', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(7, 6, NULL, 8000.00, 'efectivo', '2026-04-18', 'R-007', 'Consulta de urgencia', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(8, 7, 7, 5000.00, 'efectivo', '2026-04-20', 'R-008', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(9, 8, 8, 25000.00, 'transferencia', '2026-04-19', 'R-009', NULL, '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(10, 9, NULL, 35000.00, 'tarjeta_debito', '2026-04-21', 'R-010', 'Seña para tratamiento', '2026-04-21 14:13:32', '2026-04-21 14:13:32');

-- Volcando estructura para tabla odontologia_db.presupuestos
CREATE TABLE IF NOT EXISTS `presupuestos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `estado` enum('pendiente','aceptado','en_curso','finalizado','rechazado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `total` decimal(10,2) DEFAULT '0.00',
  `descuento` decimal(10,2) DEFAULT '0.00',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `doctor_id` (`doctor_id`),
  CONSTRAINT `presupuestos_ibfk_57` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `presupuestos_ibfk_58` FOREIGN KEY (`doctor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.presupuestos: ~10 rows (aproximadamente)
DELETE FROM `presupuestos`;
INSERT INTO `presupuestos` (`id`, `paciente_id`, `doctor_id`, `estado`, `total`, `descuento`, `notas`, `createdAt`, `updatedAt`) VALUES
	(1, 1, 2, 'en_curso', 35000.00, 0.00, 'Tratamiento restaurador integral', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(2, 2, 2, 'aceptado', 17000.00, 0.00, 'Plan preventivo', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(3, 3, 3, 'aceptado', 8000.00, 0.00, 'Tratamiento de ortodoncia completo', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(4, 4, 4, 'finalizado', 70000.00, 5000.00, 'Endodoncia y corona', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(5, 5, 5, 'aceptado', 13000.00, 0.00, 'Implante dental zona 36', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(6, 6, 2, 'pendiente', 40000.00, 0.00, 'Extracción + implante', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(7, 7, 2, 'en_curso', 20000.00, 0.00, 'Urgencia molar', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(8, 8, 3, 'aceptado', 17000.00, 0.00, 'Ortodoncia brackets metálicos', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(9, 9, 4, 'pendiente', 80000.00, 0.00, 'Endodoncia múltiple', '2026-04-21 14:13:32', '2026-04-21 14:13:32'),
	(10, 10, 2, 'pendiente', 22000.00, 0.00, 'Diagnóstico integral', '2026-04-21 14:13:32', '2026-04-21 14:13:32');

-- Volcando estructura para tabla odontologia_db.tratamientos
CREATE TABLE IF NOT EXISTS `tratamientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categoria_id` int DEFAULT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) NOT NULL,
  `duracion_minutos` int DEFAULT '30',
  `activo` tinyint(1) DEFAULT '1',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `tratamientos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_tratamiento` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.tratamientos: ~63 rows (aproximadamente)
DELETE FROM `tratamientos`;
INSERT INTO `tratamientos` (`id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `duracion_minutos`, `activo`, `createdAt`, `updatedAt`) VALUES
	(1, 1, 'Consulta de diagnóstico', NULL, 5000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(2, 1, 'Plan de tratamiento integral', NULL, 3000.00, 45, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(3, 1, 'Consulta de urgencia', NULL, 8000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(4, 2, 'Limpieza dental (profilaxis)', NULL, 12000.00, 40, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(5, 2, 'Aplicación de flúor', NULL, 5000.00, 15, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(6, 2, 'Sellador de fosas y fisuras (por pieza)', NULL, 6000.00, 20, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(7, 2, 'Destartraje (remoción de sarro)', NULL, 15000.00, 45, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(8, 3, 'Obturación simple (resina)', NULL, 15000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(9, 3, 'Obturación compuesta (resina)', NULL, 20000.00, 45, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(10, 3, 'Obturación compleja (resina)', NULL, 25000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(11, 3, 'Incrustación de porcelana', NULL, 45000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(12, 3, 'Reconstrucción con perno', NULL, 35000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(13, 4, 'Tratamiento de conducto (unirradicular)', NULL, 35000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(14, 4, 'Tratamiento de conducto (birradicular)', NULL, 45000.00, 90, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(15, 4, 'Tratamiento de conducto (multirradicular)', NULL, 55000.00, 90, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(16, 4, 'Retratamiento de conducto', NULL, 50000.00, 90, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(17, 4, 'Pulpotomía', NULL, 18000.00, 45, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(18, 5, 'Raspaje y alisado radicular (por cuadrante)', NULL, 18000.00, 45, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(19, 5, 'Cirugía periodontal (por cuadrante)', NULL, 40000.00, 90, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(20, 5, 'Injerto de encía', NULL, 55000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(21, 5, 'Alargamiento de corona clínica', NULL, 35000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(22, 6, 'Extracción simple', NULL, 12000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(23, 6, 'Extracción compleja', NULL, 20000.00, 45, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(24, 6, 'Extracción de tercer molar (muela de juicio)', NULL, 35000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(25, 6, 'Extracción de tercer molar incluido', NULL, 50000.00, 90, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(26, 6, 'Biopsia de tejidos blandos', NULL, 25000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(27, 6, 'Frenectomía', NULL, 20000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(28, 7, 'Corona de porcelana', NULL, 65000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(29, 7, 'Corona de zirconio', NULL, 85000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(30, 7, 'Corona provisoria', NULL, 15000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(31, 7, 'Puente fijo (por pieza)', NULL, 65000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(32, 7, 'Prótesis parcial removible', NULL, 80000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(33, 7, 'Prótesis completa (por arcada)', NULL, 120000.00, 90, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(34, 7, 'Reparación de prótesis', NULL, 18000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(35, 7, 'Rebasado de prótesis', NULL, 25000.00, 45, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(36, 8, 'Estudio de ortodoncia completo', NULL, 25000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(37, 8, 'Brackets metálicos (tratamiento completo)', NULL, 350000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(38, 8, 'Brackets estéticos (tratamiento completo)', NULL, 450000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(39, 8, 'Alineadores transparentes', NULL, 550000.00, 45, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(40, 8, 'Control de ortodoncia mensual', NULL, 12000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(41, 8, 'Contención fija', NULL, 25000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(42, 8, 'Placa de contención removible', NULL, 30000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(43, 9, 'Implante dental (pieza)', NULL, 250000.00, 90, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(44, 9, 'Pilar protésico sobre implante', NULL, 60000.00, 45, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(45, 9, 'Corona sobre implante', NULL, 85000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(46, 9, 'Elevación de seno maxilar', NULL, 180000.00, 120, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(47, 9, 'Injerto óseo', NULL, 120000.00, 90, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(48, 9, 'Prótesis sobre implantes (arcada completa)', NULL, 800000.00, 120, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(49, 10, 'Blanqueamiento en consultorio', NULL, 45000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(50, 10, 'Blanqueamiento con cubetas (domiciliario)', NULL, 30000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(51, 10, 'Carilla de porcelana (por pieza)', NULL, 75000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(52, 10, 'Carilla de resina (por pieza)', NULL, 30000.00, 45, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(53, 10, 'Diseño de sonrisa (diagnóstico)', NULL, 20000.00, 60, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(54, 11, 'Consulta pediátrica', NULL, 5000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(55, 11, 'Pulpotomía en diente temporal', NULL, 15000.00, 40, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(56, 11, 'Corona de acero (diente temporal)', NULL, 18000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(57, 11, 'Mantenedor de espacio', NULL, 25000.00, 45, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(58, 11, 'Obturación en diente temporal', NULL, 10000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(59, 12, 'Radiografía periapical', NULL, 3000.00, 10, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(60, 12, 'Radiografía panorámica', NULL, 8000.00, 15, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(61, 12, 'Radiografía oclusal', NULL, 4000.00, 10, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(62, 12, 'Tomografía Cone Beam (CBCT)', NULL, 25000.00, 20, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31'),
	(63, 12, 'Serie radiográfica completa', NULL, 15000.00, 30, 1, '2026-04-21 14:13:31', '2026-04-21 14:13:31');

-- Volcando estructura para tabla odontologia_db.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('administrador','doctor','recepcionista') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recepcionista',
  `especialidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `email_2` (`email`),
  UNIQUE KEY `email_3` (`email`),
  UNIQUE KEY `email_4` (`email`),
  UNIQUE KEY `email_5` (`email`),
  UNIQUE KEY `email_6` (`email`),
  UNIQUE KEY `email_7` (`email`),
  UNIQUE KEY `email_8` (`email`),
  UNIQUE KEY `email_9` (`email`),
  UNIQUE KEY `email_10` (`email`),
  UNIQUE KEY `email_11` (`email`),
  UNIQUE KEY `email_12` (`email`),
  UNIQUE KEY `email_13` (`email`),
  UNIQUE KEY `email_14` (`email`),
  UNIQUE KEY `email_15` (`email`),
  UNIQUE KEY `email_16` (`email`),
  UNIQUE KEY `email_17` (`email`),
  UNIQUE KEY `email_18` (`email`),
  UNIQUE KEY `email_19` (`email`),
  UNIQUE KEY `email_20` (`email`),
  UNIQUE KEY `email_21` (`email`),
  UNIQUE KEY `email_22` (`email`),
  UNIQUE KEY `email_23` (`email`),
  UNIQUE KEY `email_24` (`email`),
  UNIQUE KEY `email_25` (`email`),
  UNIQUE KEY `email_26` (`email`),
  UNIQUE KEY `email_27` (`email`),
  UNIQUE KEY `email_28` (`email`),
  UNIQUE KEY `email_29` (`email`),
  UNIQUE KEY `email_30` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla odontologia_db.usuarios: ~10 rows (aproximadamente)
DELETE FROM `usuarios`;
INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol`, `especialidad`, `telefono`, `activo`, `createdAt`, `updatedAt`) VALUES
	(1, 'Admin', 'Sistema', 'admin@clinica.com', '$2a$10$FsbBHK9zBrUsmzWWuPSPSeBzkaVRDfopOAjNTE8TgaSVHC71FO1Fy', 'administrador', NULL, NULL, 1, '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(2, 'Carlos', 'Rodríguez', 'carlos@clinica.com', '$2a$10$VRCbv5zrC/P/IX1vScjw4ejaaRVDlaCgqyzntdt9ODBaQehsqx2dS', 'doctor', 'Odontología General', NULL, 1, '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(3, 'María', 'González', 'maria@clinica.com', '$2a$10$vAeDgTis2pOnYl8c533afeB6N86zvmkNWFZmaP7k7MyyADQeCciCa', 'doctor', 'Ortodoncia', NULL, 1, '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(4, 'Andrés', 'López', 'andres@clinica.com', '$2a$10$rJAJtR0.wz15fjnA/sBjeeEAPvvW3Ann3Q5wPImxRYRNJ/A6T38Ia', 'doctor', 'Endodoncia', NULL, 1, '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(5, 'Laura', 'Martínez', 'laura@clinica.com', '$2a$10$ih2Nx.l5HnOukoApwI8v9u4HwOyjxyygBOp5uc/T28fWcoYqHsVyq', 'doctor', 'Implantología', NULL, 1, '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(6, 'Ana', 'Pérez', 'recepcion@clinica.com', '$2a$10$5sqLcq5OU3SMK5RAkSOvnupoWY0EYneRJ9ry5GbYq2rTaaR6s5qje', 'recepcionista', NULL, NULL, 1, '2026-04-10 06:42:08', '2026-04-10 06:42:08'),
	(7, 'Carlos', 'Mendoza', 'carlos.mendoza@clinica.com', '$2a$10$1NfVx7bU6L.DKsj.xZV.HOmouBRLG1giM.16tuTdbKAmtXjW8RVaS', 'doctor', 'Odontología General', '11-2345-6789', 1, '2026-04-18 07:01:53', '2026-04-18 07:01:53'),
	(8, 'Ana', 'Rodríguez', 'ana.rodriguez@clinica.com', '$2a$10$3SykONeZxwHsu0I.NsnF2uFG7Geduqf2vespBHViGo3j5O425I3ry', 'doctor', 'Ortodoncia', '11-3456-7890', 1, '2026-04-18 07:01:53', '2026-04-18 07:01:53'),
	(9, 'Lucas', 'Fernández', 'lucas.fernandez@clinica.com', '$2a$10$6wnfPyGXxek.jHpYGoG7DuAGNKGJbEgV.BmRlIqyNVyYIP/u5CNDu', 'doctor', 'Cirugía e Implantología', '11-4567-8901', 1, '2026-04-18 07:01:53', '2026-04-18 07:01:53'),
	(10, 'María', 'García', 'maria.garcia@clinica.com', '$2a$10$9Aw5R/AB8vkBj2NZF8o4ludDklMaM7vXi7eYvE4EqoRGa0sr9PkbO', 'recepcionista', NULL, '11-5678-9012', 1, '2026-04-18 07:01:53', '2026-04-18 07:01:53');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
