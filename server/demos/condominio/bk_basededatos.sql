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


-- Volcando estructura de base de datos para condominio_crm
CREATE DATABASE IF NOT EXISTS `condominio_crm` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `condominio_crm`;

-- Volcando estructura para tabla condominio_crm.amenidades
CREATE TABLE IF NOT EXISTS `amenidades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `capacidad_max` smallint DEFAULT NULL,
  `tiene_costo` tinyint(1) DEFAULT '0',
  `costo` decimal(10,2) DEFAULT '0.00',
  `horario_inicio` time DEFAULT NULL,
  `horario_fin` time DEFAULT NULL,
  `limite_reservas_mes` tinyint DEFAULT '4',
  `dias_anticipacion` tinyint DEFAULT '3',
  `foto_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.amenidades: ~5 rows (aproximadamente)
DELETE FROM `amenidades`;
INSERT INTO `amenidades` (`id`, `nombre`, `descripcion`, `capacidad_max`, `tiene_costo`, `costo`, `horario_inicio`, `horario_fin`, `limite_reservas_mes`, `dias_anticipacion`, `foto_url`, `activo`) VALUES
	(1, 'Sal├│n de Eventos', 'Sal├│n para fiestas y reuniones', 100, 1, 800.00, '09:00:00', '22:00:00', 2, 3, NULL, 1),
	(2, 'Alberca', 'Alberca ol├¡mpica climatizada', 30, 0, 0.00, '07:00:00', '20:00:00', 8, 3, NULL, 1),
	(3, 'Gimnasio', 'Gym completamente equipado', 15, 0, 0.00, '06:00:00', '22:00:00', 20, 3, NULL, 1),
	(4, 'Cancha de Tenis', 'Cancha profesional', 4, 0, 0.00, '07:00:00', '21:00:00', 6, 3, NULL, 1),
	(5, 'Asador / BBQ', '├ürea de asadores techada', 20, 1, 300.00, '10:00:00', '20:00:00', 4, 3, NULL, 1);

-- Volcando estructura para tabla condominio_crm.anuncios
CREATE TABLE IF NOT EXISTS `anuncios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenido` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('informativo','urgente','evento','mantenimiento','cobranza') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'informativo',
  `publicado_por` int DEFAULT NULL,
  `fecha_publicacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` datetime DEFAULT NULL,
  `adjunto_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enviar_email` tinyint(1) DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `publicado_por` (`publicado_por`),
  KEY `idx_anuncios_activo` (`activo`),
  CONSTRAINT `anuncios_ibfk_1` FOREIGN KEY (`publicado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.anuncios: ~3 rows (aproximadamente)
DELETE FROM `anuncios`;
INSERT INTO `anuncios` (`id`, `titulo`, `contenido`, `tipo`, `publicado_por`, `fecha_publicacion`, `fecha_expiracion`, `adjunto_url`, `enviar_email`, `activo`) VALUES
	(1, 'Corte de agua programado', 'El lunes 21 de abril se realizar├í mantenimiento a la red hidr├íulica de 9am a 2pm. Se sugiere almacenar agua.', 'mantenimiento', 1, '2026-04-18 02:45:01', '2026-04-22 00:00:00', NULL, 0, 1),
	(2, 'Asamblea ordinaria abril 2026', 'Se convoca a todos los cond├│minos a la asamblea ordinaria del mes el s├íbado 26 de abril a las 11am en el sal├│n de eventos.', 'evento', 1, '2026-04-18 02:45:01', '2026-04-26 12:00:00', NULL, 0, 1),
	(3, 'Bienvenidos al portal de cond├│minos', 'Estrenamos el nuevo sistema de administraci├│n. Pueden consultar sus estados de cuenta, hacer reservaciones y m├ís.', 'informativo', 1, '2026-04-18 02:45:01', '2026-05-01 00:00:00', NULL, 0, 1);

-- Volcando estructura para tabla condominio_crm.areas_comunes
CREATE TABLE IF NOT EXISTS `areas_comunes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.areas_comunes: ~6 rows (aproximadamente)
DELETE FROM `areas_comunes`;
INSERT INTO `areas_comunes` (`id`, `nombre`, `tipo`, `descripcion`, `activo`) VALUES
	(1, 'Alberca', 'Recreativa', 'Piscina ol├¡mpica climatizada', 1),
	(2, 'Sal├│n de Eventos', 'Social', 'Sal├│n de usos m├║ltiples cap. 100 personas', 1),
	(3, 'Gimnasio', 'Deportiva', 'Equipped gym 24/7', 1),
	(4, 'Cancha de Tenis', 'Deportiva', 'Cancha de tenis con iluminaci├│n', 1),
	(5, 'Jard├¡n Central', 'Verde', 'Jard├¡n con ├írea de juegos infantiles', 1),
	(6, 'Lobby / Recepci├│n', 'Acceso', '├ürea de recepci├│n principal', 1);

-- Volcando estructura para tabla condominio_crm.asambleas
CREATE TABLE IF NOT EXISTS `asambleas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('ordinaria','extraordinaria') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ordinaria',
  `fecha` datetime NOT NULL,
  `lugar` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden_dia` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `minuta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `acuerdos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `convocatoria_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documento_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('programada','en_curso','finalizada','cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'programada',
  `creado_por` int DEFAULT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `creado_por` (`creado_por`),
  CONSTRAINT `asambleas_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.asambleas: ~0 rows (aproximadamente)
DELETE FROM `asambleas`;

-- Volcando estructura para tabla condominio_crm.asistentes_asamblea
CREATE TABLE IF NOT EXISTS `asistentes_asamblea` (
  `id` int NOT NULL AUTO_INCREMENT,
  `asamblea_id` int NOT NULL,
  `residente_id` int NOT NULL,
  `unidad_id` int NOT NULL,
  `asistio` tinyint(1) DEFAULT '0',
  `representante` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firma_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asamblea_id` (`asamblea_id`),
  KEY `residente_id` (`residente_id`),
  KEY `unidad_id` (`unidad_id`),
  CONSTRAINT `asistentes_asamblea_ibfk_1` FOREIGN KEY (`asamblea_id`) REFERENCES `asambleas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asistentes_asamblea_ibfk_2` FOREIGN KEY (`residente_id`) REFERENCES `residentes` (`id`),
  CONSTRAINT `asistentes_asamblea_ibfk_3` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.asistentes_asamblea: ~0 rows (aproximadamente)
DELETE FROM `asistentes_asamblea`;

-- Volcando estructura para tabla condominio_crm.condominios
CREATE TABLE IF NOT EXISTS `condominios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rfc` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sitio_web` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moneda` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'MXN',
  `zona_horaria` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'America/Mexico_City',
  `activo` tinyint(1) DEFAULT '1',
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.condominios: ~0 rows (aproximadamente)
DELETE FROM `condominios`;
INSERT INTO `condominios` (`id`, `nombre`, `logo_url`, `direccion`, `rfc`, `telefono`, `email`, `sitio_web`, `moneda`, `zona_horaria`, `activo`, `creado_en`) VALUES
	(1, 'Condominio El Bosque', NULL, 'Av. Insurgentes Sur 1234, Col. Del Valle, CDMX', 'BOSQ1234', '55-1234-5678', 'admin@laspalmas.com', NULL, 'MXN', 'America/Mexico_City', 1, '2026-04-18 02:45:01');

-- Volcando estructura para tabla condominio_crm.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('texto','numero','booleano','json') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'texto',
  `actualizado_en` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.configuracion: ~6 rows (aproximadamente)
DELETE FROM `configuracion`;
INSERT INTO `configuracion` (`id`, `clave`, `valor`, `descripcion`, `tipo`, `actualizado_en`) VALUES
	(1, 'tasa_mora_default', '0.05', 'Tasa de mora mensual por defecto (5%)', 'numero', '2026-04-18 02:45:01'),
	(2, 'dias_gracia_default', '5', 'D├¡as de gracia antes de aplicar mora', 'numero', '2026-04-18 02:45:01'),
	(3, 'email_notificaciones', 'admin@laspalmas.com', 'Email para notificaciones del sistema', 'texto', '2026-04-18 02:45:01'),
	(4, 'generacion_cuotas_dia', '1', 'D├¡a del mes para generar cuotas autom├íticamente', 'numero', '2026-04-18 02:45:01'),
	(5, 'smtp_host', 'smtp.gmail.com', 'Servidor SMTP para env├¡o de correos', 'texto', '2026-04-18 02:45:01'),
	(6, 'modulo_mantenimiento', 'true', 'Habilitar m├│dulo de mantenimiento', 'booleano', '2026-04-18 02:45:01');

-- Volcando estructura para tabla condominio_crm.contactos_emergencia
CREATE TABLE IF NOT EXISTS `contactos_emergencia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `residente_id` int NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parentesco` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `residente_id` (`residente_id`),
  CONSTRAINT `contactos_emergencia_ibfk_1` FOREIGN KEY (`residente_id`) REFERENCES `residentes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.contactos_emergencia: ~0 rows (aproximadamente)
DELETE FROM `contactos_emergencia`;

-- Volcando estructura para tabla condominio_crm.contratos_proveedor
CREATE TABLE IF NOT EXISTS `contratos_proveedor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `proveedor_id` int NOT NULL,
  `tipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `monto_mensual` decimal(10,2) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `documento_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activo','vencido','cancelado','por_renovar') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `proveedor_id` (`proveedor_id`),
  CONSTRAINT `contratos_proveedor_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.contratos_proveedor: ~0 rows (aproximadamente)
DELETE FROM `contratos_proveedor`;

-- Volcando estructura para tabla condominio_crm.cuentas_contables
CREATE TABLE IF NOT EXISTS `cuentas_contables` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('ingreso','egreso','activo','pasivo','capital') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `padre_id` int DEFAULT NULL,
  `nivel` tinyint DEFAULT '1',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.cuentas_contables: ~9 rows (aproximadamente)
DELETE FROM `cuentas_contables`;
INSERT INTO `cuentas_contables` (`id`, `codigo`, `nombre`, `tipo`, `padre_id`, `nivel`, `activo`) VALUES
	(1, '1000', 'Ingresos por cuotas', 'ingreso', NULL, 1, 1),
	(2, '1001', 'Ingresos por amenidades', 'ingreso', NULL, 1, 1),
	(3, '1002', 'Ingresos por multas', 'ingreso', NULL, 1, 1),
	(4, '2000', 'Mantenimiento general', 'egreso', NULL, 1, 1),
	(5, '2001', 'Vigilancia y seguridad', 'egreso', NULL, 1, 1),
	(6, '2002', 'Jardiner├¡a y limpieza', 'egreso', NULL, 1, 1),
	(7, '2003', 'Servicios (agua, luz, gas)', 'egreso', NULL, 1, 1),
	(8, '2004', 'Administraci├│n', 'egreso', NULL, 1, 1),
	(9, '3000', 'Fondo de reserva', 'activo', NULL, 1, 1);

-- Volcando estructura para tabla condominio_crm.cuotas
CREATE TABLE IF NOT EXISTS `cuotas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unidad_id` int NOT NULL,
  `tipo_cuota_id` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` enum('pendiente','pagado','vencido','en_disputa','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `referencia` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mora_aplicada` decimal(10,2) DEFAULT '0.00',
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tipo_cuota_id` (`tipo_cuota_id`),
  KEY `idx_cuotas_estado` (`estado`),
  KEY `idx_cuotas_unidad` (`unidad_id`),
  CONSTRAINT `cuotas_ibfk_1` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`),
  CONSTRAINT `cuotas_ibfk_2` FOREIGN KEY (`tipo_cuota_id`) REFERENCES `tipos_cuota` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.cuotas: ~25 rows (aproximadamente)
DELETE FROM `cuotas`;
INSERT INTO `cuotas` (`id`, `unidad_id`, `tipo_cuota_id`, `monto`, `fecha_emision`, `fecha_vencimiento`, `estado`, `referencia`, `descripcion`, `mora_aplicada`, `creado_en`) VALUES
	(1, 1, 1, 2500.00, '2026-02-01', '2026-02-10', 'pagado', 'ENE26-A101', NULL, 0.00, '2026-04-18 02:45:01'),
	(2, 2, 1, 2500.00, '2026-02-01', '2026-02-10', 'pagado', 'ENE26-A102', NULL, 0.00, '2026-04-18 02:45:01'),
	(3, 3, 1, 2500.00, '2026-02-01', '2026-02-10', 'pagado', 'ENE26-A201', NULL, 0.00, '2026-04-18 02:45:01'),
	(4, 5, 1, 2500.00, '2026-02-01', '2026-02-10', 'vencido', 'ENE26-A301', NULL, 0.00, '2026-04-18 02:45:01'),
	(5, 6, 1, 2500.00, '2026-02-01', '2026-02-10', 'pagado', 'ENE26-B101', NULL, 0.00, '2026-04-18 02:45:01'),
	(6, 1, 1, 2500.00, '2026-03-01', '2026-03-10', 'pagado', 'FEB26-A101', NULL, 0.00, '2026-04-18 02:45:01'),
	(7, 2, 1, 2500.00, '2026-03-01', '2026-03-10', 'pagado', 'FEB26-A102', NULL, 0.00, '2026-04-18 02:45:01'),
	(8, 3, 1, 2500.00, '2026-03-01', '2026-03-10', 'vencido', 'FEB26-A201', NULL, 0.00, '2026-04-18 02:45:01'),
	(9, 5, 1, 2500.00, '2026-03-01', '2026-03-10', 'vencido', 'FEB26-A301', NULL, 0.00, '2026-04-18 02:45:01'),
	(10, 6, 1, 2500.00, '2026-03-01', '2026-03-10', 'pagado', 'FEB26-B101', NULL, 0.00, '2026-04-18 02:45:01'),
	(11, 1, 1, 2500.00, '2026-04-01', '2026-04-10', 'pagado', 'MAR26-A101', NULL, 0.00, '2026-04-18 02:45:01'),
	(12, 2, 1, 2500.00, '2026-04-01', '2026-04-10', 'pendiente', 'MAR26-A102', NULL, 0.00, '2026-04-18 02:45:01'),
	(13, 3, 1, 2500.00, '2026-04-01', '2026-04-10', 'pendiente', 'MAR26-A201', NULL, 0.00, '2026-04-18 02:45:01'),
	(14, 5, 1, 2500.00, '2026-04-01', '2026-04-10', 'vencido', 'MAR26-A301', NULL, 0.00, '2026-04-18 02:45:01'),
	(15, 6, 1, 2500.00, '2026-04-01', '2026-04-10', 'pagado', 'MAR26-B101', NULL, 0.00, '2026-04-18 02:45:01'),
	(16, 1, 1, 1500.00, '2026-04-18', '2026-04-03', 'vencido', NULL, NULL, 0.00, '2026-04-18 04:08:19'),
	(17, 2, 1, 1500.00, '2026-04-18', '2026-05-03', 'pagado', NULL, NULL, 0.00, '2026-04-18 04:08:19'),
	(18, 3, 1, 1500.00, '2026-04-18', '2026-04-03', 'vencido', NULL, NULL, 0.00, '2026-04-18 04:08:19'),
	(19, 4, 1, 1500.00, '2026-04-18', '2026-05-03', 'pagado', NULL, NULL, 0.00, '2026-04-18 04:08:19'),
	(20, 5, 1, 1500.00, '2026-04-18', '2026-04-03', 'vencido', NULL, NULL, 0.00, '2026-04-18 04:08:19'),
	(21, 11, 1, 1500.00, '2026-04-18', '2026-05-03', 'pagado', NULL, NULL, 0.00, '2026-04-18 04:08:19'),
	(22, 12, 1, 1500.00, '2026-04-18', '2026-04-03', 'vencido', NULL, NULL, 0.00, '2026-04-18 04:08:19'),
	(23, 13, 1, 1500.00, '2026-04-18', '2026-05-03', 'pagado', NULL, NULL, 0.00, '2026-04-18 04:08:19'),
	(24, 14, 1, 1500.00, '2026-04-18', '2026-04-03', 'vencido', NULL, NULL, 0.00, '2026-04-18 04:08:19'),
	(25, 15, 1, 1500.00, '2026-04-18', '2026-05-03', 'pagado', NULL, NULL, 0.00, '2026-04-18 04:08:19');

-- Volcando estructura para tabla condominio_crm.encuestas
CREATE TABLE IF NOT EXISTS `encuestas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `opciones` json NOT NULL,
  `publicado_por` int DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `publicado_por` (`publicado_por`),
  CONSTRAINT `encuestas_ibfk_1` FOREIGN KEY (`publicado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.encuestas: ~0 rows (aproximadamente)
DELETE FROM `encuestas`;

-- Volcando estructura para tabla condominio_crm.fondo_reserva
CREATE TABLE IF NOT EXISTS `fondo_reserva` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` enum('aporte','retiro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `aprobado_por` int DEFAULT NULL,
  `saldo_resultante` decimal(12,2) DEFAULT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `aprobado_por` (`aprobado_por`),
  CONSTRAINT `fondo_reserva_ibfk_1` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.fondo_reserva: ~4 rows (aproximadamente)
DELETE FROM `fondo_reserva`;
INSERT INTO `fondo_reserva` (`id`, `tipo`, `monto`, `fecha`, `descripcion`, `aprobado_por`, `saldo_resultante`, `creado_en`) VALUES
	(1, 'aporte', 25000.00, '2026-01-31', 'Aportaci├│n enero 2026', 1, 25000.00, '2026-04-18 02:45:01'),
	(2, 'aporte', 25000.00, '2026-02-28', 'Aportaci├│n febrero 2026', 1, 50000.00, '2026-04-18 02:45:01'),
	(3, 'retiro', 12000.00, '2026-03-10', 'Reparaci├│n bomba de agua', 1, 38000.00, '2026-04-18 02:45:01'),
	(4, 'aporte', 25000.00, '2026-03-31', 'Aportaci├│n marzo 2026', 1, 63000.00, '2026-04-18 02:45:01');

-- Volcando estructura para tabla condominio_crm.incidentes
CREATE TABLE IF NOT EXISTS `incidentes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ubicacion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `nivel` enum('bajo','medio','alto','critico') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'medio',
  `estado` enum('abierto','en_investigacion','cerrado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'abierto',
  `reportado_por` int DEFAULT NULL,
  `foto_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seguimiento` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `reportado_por` (`reportado_por`),
  CONSTRAINT `incidentes_ibfk_1` FOREIGN KEY (`reportado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.incidentes: ~3 rows (aproximadamente)
DELETE FROM `incidentes`;
INSERT INTO `incidentes` (`id`, `tipo`, `descripcion`, `ubicacion`, `fecha`, `nivel`, `estado`, `reportado_por`, `foto_url`, `seguimiento`) VALUES
	(1, 'Ruido', 'Fiesta excesiva despu├®s de las 11pm en departamento B-102', 'Torre B Piso 1', '2026-04-18 02:45:01', 'medio', 'cerrado', 5, NULL, NULL),
	(2, 'Vandalismo', 'Grafiti en puerta del estacionamiento', 'Estacionamiento nivel -1', '2026-04-18 02:45:01', 'medio', 'cerrado', 5, NULL, NULL),
	(3, 'Robo', 'Reporte de intento de robo a veh├¡culo en estacionamiento', 'Estacionamiento nivel -1', '2026-04-18 02:45:01', 'alto', 'en_investigacion', 5, NULL, NULL);

-- Volcando estructura para tabla condominio_crm.log_actividad
CREATE TABLE IF NOT EXISTS `log_actividad` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `accion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `modulo` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registro_id` int DEFAULT NULL,
  `datos_anteriores` json DEFAULT NULL,
  `datos_nuevos` json DEFAULT NULL,
  `ip_address` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_log_usuario` (`usuario_id`),
  KEY `idx_log_fecha` (`fecha`),
  CONSTRAINT `log_actividad_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.log_actividad: ~0 rows (aproximadamente)
DELETE FROM `log_actividad`;

-- Volcando estructura para tabla condominio_crm.mantenimiento_preventivo
CREATE TABLE IF NOT EXISTS `mantenimiento_preventivo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `area_id` int DEFAULT NULL,
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `proveedor_id` int DEFAULT NULL,
  `frecuencia` enum('semanal','mensual','bimestral','trimestral','semestral','anual') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proxima_fecha` date DEFAULT NULL,
  `ultima_fecha` date DEFAULT NULL,
  `costo_estimado` decimal(10,2) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `area_id` (`area_id`),
  KEY `proveedor_id` (`proveedor_id`),
  CONSTRAINT `mantenimiento_preventivo_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `areas_comunes` (`id`),
  CONSTRAINT `mantenimiento_preventivo_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.mantenimiento_preventivo: ~0 rows (aproximadamente)
DELETE FROM `mantenimiento_preventivo`;

-- Volcando estructura para tabla condominio_crm.mascotas
CREATE TABLE IF NOT EXISTS `mascotas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unidad_id` int NOT NULL,
  `residente_id` int DEFAULT NULL,
  `nombre` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `especie` enum('perro','gato','ave','pez','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'perro',
  `raza` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chip_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vacunas_al_dia` tinyint(1) DEFAULT '0',
  `foto_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `unidad_id` (`unidad_id`),
  KEY `residente_id` (`residente_id`),
  CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`),
  CONSTRAINT `mascotas_ibfk_2` FOREIGN KEY (`residente_id`) REFERENCES `residentes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.mascotas: ~0 rows (aproximadamente)
DELETE FROM `mascotas`;

-- Volcando estructura para tabla condominio_crm.mensajes
CREATE TABLE IF NOT EXISTS `mensajes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `de_usuario_id` int NOT NULL,
  `para_usuario_id` int DEFAULT NULL,
  `unidad_id` int DEFAULT NULL,
  `asunto` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contenido` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `leido` tinyint(1) DEFAULT '0',
  `adjunto_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `de_usuario_id` (`de_usuario_id`),
  KEY `para_usuario_id` (`para_usuario_id`),
  KEY `unidad_id` (`unidad_id`),
  CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`de_usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`para_usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `mensajes_ibfk_3` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.mensajes: ~0 rows (aproximadamente)
DELETE FROM `mensajes`;

-- Volcando estructura para tabla condominio_crm.ordenes_trabajo
CREATE TABLE IF NOT EXISTS `ordenes_trabajo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` enum('correctivo','preventivo','emergencia') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'correctivo',
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `unidad_id` int DEFAULT NULL,
  `area_id` int DEFAULT NULL,
  `proveedor_id` int DEFAULT NULL,
  `asignado_a` int DEFAULT NULL,
  `reportado_por` int DEFAULT NULL,
  `estado` enum('abierto','asignado','en_progreso','completado','cerrado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'abierto',
  `prioridad` enum('baja','media','alta','urgente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'media',
  `fecha_reporte` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_asignacion` datetime DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `costo_estimado` decimal(10,2) DEFAULT NULL,
  `costo_real` decimal(10,2) DEFAULT NULL,
  `foto_antes_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_despues_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `calificacion` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unidad_id` (`unidad_id`),
  KEY `area_id` (`area_id`),
  KEY `proveedor_id` (`proveedor_id`),
  KEY `asignado_a` (`asignado_a`),
  KEY `reportado_por` (`reportado_por`),
  KEY `idx_ordenes_estado` (`estado`),
  CONSTRAINT `ordenes_trabajo_ibfk_1` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`),
  CONSTRAINT `ordenes_trabajo_ibfk_2` FOREIGN KEY (`area_id`) REFERENCES `areas_comunes` (`id`),
  CONSTRAINT `ordenes_trabajo_ibfk_3` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `ordenes_trabajo_ibfk_4` FOREIGN KEY (`asignado_a`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `ordenes_trabajo_ibfk_5` FOREIGN KEY (`reportado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.ordenes_trabajo: ~5 rows (aproximadamente)
DELETE FROM `ordenes_trabajo`;
INSERT INTO `ordenes_trabajo` (`id`, `tipo`, `titulo`, `descripcion`, `unidad_id`, `area_id`, `proveedor_id`, `asignado_a`, `reportado_por`, `estado`, `prioridad`, `fecha_reporte`, `fecha_asignacion`, `fecha_inicio`, `fecha_fin`, `costo_estimado`, `costo_real`, `foto_antes_url`, `foto_despues_url`, `notas`, `calificacion`) VALUES
	(1, 'correctivo', 'Lámpara fundida', 'Reporte automático', 2, NULL, NULL, NULL, NULL, 'completado', 'media', '2026-04-17 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(2, 'correctivo', 'Cerradura rota', 'Reporte automático', 4, NULL, NULL, NULL, NULL, 'completado', 'media', '2026-04-15 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(3, 'correctivo', 'Limpieza profunda', 'Reporte automático', 11, NULL, NULL, NULL, NULL, 'completado', 'media', '2026-04-13 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(4, 'correctivo', 'Ventana trancada', 'Reporte automático', 13, NULL, NULL, NULL, NULL, 'completado', 'media', '2026-04-11 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(5, 'correctivo', 'Piscina sucia', 'Reporte automático', 15, NULL, NULL, NULL, NULL, 'completado', 'media', '2026-04-09 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- Volcando estructura para tabla condominio_crm.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cuota_id` int DEFAULT NULL,
  `unidad_id` int NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `metodo` enum('efectivo','transferencia','cheque','tarjeta','app','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'efectivo',
  `referencia_pago` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprobante_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registrado_por` int DEFAULT NULL,
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cuota_id` (`cuota_id`),
  KEY `unidad_id` (`unidad_id`),
  KEY `registrado_por` (`registrado_por`),
  KEY `idx_pagos_fecha` (`fecha_pago`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`cuota_id`) REFERENCES `cuotas` (`id`),
  CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`),
  CONSTRAINT `pagos_ibfk_3` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.pagos: ~14 rows (aproximadamente)
DELETE FROM `pagos`;
INSERT INTO `pagos` (`id`, `cuota_id`, `unidad_id`, `monto_pagado`, `fecha_pago`, `metodo`, `referencia_pago`, `comprobante_url`, `registrado_por`, `notas`, `creado_en`) VALUES
	(1, 1, 1, 2500.00, '2026-02-05 10:30:00', 'transferencia', 'TRF-001', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(2, 2, 2, 2500.00, '2026-02-08 14:00:00', 'efectivo', NULL, NULL, 1, NULL, '2026-04-18 02:45:01'),
	(3, 3, 3, 2500.00, '2026-02-09 11:00:00', 'transferencia', 'TRF-002', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(4, 6, 1, 2500.00, '2026-03-04 09:15:00', 'app', 'APP-001', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(5, 7, 2, 2500.00, '2026-03-07 16:30:00', 'transferencia', 'TRF-003', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(6, 11, 1, 2500.00, '2026-04-03 10:00:00', 'transferencia', 'TRF-004', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(7, 5, 6, 2500.00, '2026-02-06 13:00:00', 'efectivo', NULL, NULL, 1, NULL, '2026-04-18 02:45:01'),
	(8, 10, 6, 2500.00, '2026-03-05 12:00:00', 'efectivo', NULL, NULL, 1, NULL, '2026-04-18 02:45:01'),
	(9, 15, 6, 2500.00, '2026-04-02 11:30:00', 'efectivo', NULL, NULL, 1, NULL, '2026-04-18 02:45:01'),
	(10, 17, 2, 1500.00, '2026-04-18 04:08:19', 'transferencia', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(11, 19, 4, 1500.00, '2026-04-18 04:08:19', 'transferencia', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(12, 21, 11, 1500.00, '2026-04-18 04:08:19', 'transferencia', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(13, 23, 13, 1500.00, '2026-04-18 04:08:19', 'transferencia', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(14, 25, 15, 1500.00, '2026-04-18 04:08:19', 'transferencia', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19');

-- Volcando estructura para tabla condominio_crm.paquetes
CREATE TABLE IF NOT EXISTS `paquetes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unidad_id` int NOT NULL,
  `descripcion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remitente` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_mensajeria` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_guia` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('recibido','notificado','entregado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'recibido',
  `recibido_por` int DEFAULT NULL,
  `fecha_recepcion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_entrega` datetime DEFAULT NULL,
  `entregado_a` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unidad_id` (`unidad_id`),
  KEY `recibido_por` (`recibido_por`),
  CONSTRAINT `paquetes_ibfk_1` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`),
  CONSTRAINT `paquetes_ibfk_2` FOREIGN KEY (`recibido_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.paquetes: ~0 rows (aproximadamente)
DELETE FROM `paquetes`;

-- Volcando estructura para tabla condominio_crm.presupuesto_anual
CREATE TABLE IF NOT EXISTS `presupuesto_anual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `anio` year NOT NULL,
  `cuenta_id` int NOT NULL,
  `monto_presupuestado` decimal(12,2) NOT NULL,
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `cuenta_id` (`cuenta_id`),
  CONSTRAINT `presupuesto_anual_ibfk_1` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas_contables` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.presupuesto_anual: ~0 rows (aproximadamente)
DELETE FROM `presupuesto_anual`;

-- Volcando estructura para tabla condominio_crm.proveedores
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_servicio` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `calificacion` decimal(3,2) DEFAULT '0.00',
  `activo` tinyint(1) DEFAULT '1',
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.proveedores: ~16 rows (aproximadamente)
DELETE FROM `proveedores`;
INSERT INTO `proveedores` (`id`, `nombre`, `rfc`, `tipo_servicio`, `contacto_nombre`, `contacto_telefono`, `contacto_email`, `direccion`, `calificacion`, `activo`, `notas`, `creado_en`) VALUES
	(1, 'Servicios Integrales SA', 'SINM800101ABC', 'Mantenimiento General', 'Miguel ├üngel Ruiz', '55-1234-9876', 'miguel@serviciosint.com', NULL, 0.00, 1, NULL, '2026-04-18 02:45:01'),
	(2, 'Vigilancia Pro SC', 'VPSC901115XYZ', 'Vigilancia y Seguridad', 'Patricia Leal', '55-9876-5432', 'patricia@vigilanciapro.com', NULL, 0.00, 1, NULL, '2026-04-18 02:45:01'),
	(3, 'Verde Jardines', 'VEJM851020DEF', 'Jardiner├¡a', 'Ernesto Montes', '55-4567-8901', 'ernesto@verdejardines.com', NULL, 0.00, 1, NULL, '2026-04-18 02:45:01'),
	(4, 'Elevadores Modernos', 'ELMO780612GHI', 'Mantenimiento Elevadores', 'Samuel Torres', '55-2345-6789', 'samuel@elevmod.com', NULL, 0.00, 1, NULL, '2026-04-18 02:45:01'),
	(5, 'Limpieza Express', 'LIEX950308JKL', 'Limpieza y sanitizaci├│n', 'Sandra P├®rez', '55-3456-7890', 'sandra@limpiezaex.com', NULL, 0.00, 1, NULL, '2026-04-18 02:45:01'),
	(6, 'Limpieza Pro', '', 'Servicios', '', '', '', '', 0.00, 1, '', '2026-04-18 03:36:34'),
	(7, 'Proveedor Gas S.A.', NULL, 'Gas', NULL, '5511223340', NULL, NULL, 0.00, 1, NULL, '2026-04-18 04:08:19'),
	(8, 'Proveedor Agua S.A.', NULL, 'Agua', NULL, '5511223341', NULL, NULL, 0.00, 1, NULL, '2026-04-18 04:08:19'),
	(9, 'Proveedor Internet S.A.', NULL, 'Internet', NULL, '5511223342', NULL, NULL, 0.00, 1, NULL, '2026-04-18 04:08:19'),
	(10, 'Proveedor Basura S.A.', NULL, 'Basura', NULL, '5511223343', NULL, NULL, 0.00, 1, NULL, '2026-04-18 04:08:19'),
	(11, 'Proveedor Seguridad S.A.', NULL, 'Seguridad', NULL, '5511223344', NULL, NULL, 0.00, 1, NULL, '2026-04-18 04:08:19'),
	(12, 'Proveedor Jardineria S.A.', NULL, 'Jardineria', NULL, '5511223345', NULL, NULL, 0.00, 1, NULL, '2026-04-18 04:08:19'),
	(13, 'Proveedor Plomeria S.A.', NULL, 'Plomeria', NULL, '5511223346', NULL, NULL, 0.00, 1, NULL, '2026-04-18 04:08:19'),
	(14, 'Proveedor Electricidad S.A.', NULL, 'Electricidad', NULL, '5511223347', NULL, NULL, 0.00, 1, NULL, '2026-04-18 04:08:19'),
	(15, 'Proveedor Pintura S.A.', NULL, 'Pintura', NULL, '5511223348', NULL, NULL, 0.00, 1, NULL, '2026-04-18 04:08:19'),
	(16, 'Proveedor Alberquero S.A.', NULL, 'Alberquero', NULL, '5511223349', NULL, NULL, 0.00, 1, NULL, '2026-04-18 04:08:19');

-- Volcando estructura para tabla condominio_crm.recibos
CREATE TABLE IF NOT EXISTS `recibos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pago_id` int NOT NULL,
  `folio` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdf_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enviado_email` tinyint(1) DEFAULT '0',
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `pago_id` (`pago_id`),
  CONSTRAINT `recibos_ibfk_1` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.recibos: ~0 rows (aproximadamente)
DELETE FROM `recibos`;

-- Volcando estructura para tabla condominio_crm.reservaciones
CREATE TABLE IF NOT EXISTS `reservaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `amenidad_id` int NOT NULL,
  `unidad_id` int NOT NULL,
  `residente_id` int DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `num_personas` smallint DEFAULT '1',
  `estado` enum('pendiente','confirmada','cancelada','completada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `costo_cobrado` decimal(10,2) DEFAULT '0.00',
  `pago_id` int DEFAULT NULL,
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `amenidad_id` (`amenidad_id`),
  KEY `unidad_id` (`unidad_id`),
  KEY `residente_id` (`residente_id`),
  KEY `pago_id` (`pago_id`),
  KEY `idx_reservaciones_fecha` (`fecha`),
  CONSTRAINT `reservaciones_ibfk_1` FOREIGN KEY (`amenidad_id`) REFERENCES `amenidades` (`id`),
  CONSTRAINT `reservaciones_ibfk_2` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`),
  CONSTRAINT `reservaciones_ibfk_3` FOREIGN KEY (`residente_id`) REFERENCES `residentes` (`id`),
  CONSTRAINT `reservaciones_ibfk_4` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.reservaciones: ~25 rows (aproximadamente)
DELETE FROM `reservaciones`;
INSERT INTO `reservaciones` (`id`, `amenidad_id`, `unidad_id`, `residente_id`, `fecha`, `hora_inicio`, `hora_fin`, `num_personas`, `estado`, `costo_cobrado`, `pago_id`, `notas`, `creado_en`) VALUES
	(1, 1, 1, 1, '2026-04-20', '14:00:00', '22:00:00', 50, 'confirmada', 800.00, NULL, NULL, '2026-04-18 02:45:01'),
	(2, 2, 3, 3, '2026-04-19', '09:00:00', '11:00:00', 4, 'confirmada', 0.00, NULL, NULL, '2026-04-18 02:45:01'),
	(3, 3, 6, 6, '2026-04-19', '07:00:00', '08:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 02:45:01'),
	(4, 5, 2, 2, '2026-04-25', '12:00:00', '17:00:00', 15, 'confirmada', 300.00, NULL, NULL, '2026-04-18 02:45:01'),
	(5, 3, 1, 1, '2026-04-18', '10:00:00', '12:00:00', 2, 'confirmada', 0.00, NULL, 'Prueba de reservacion automatica', '2026-04-18 03:28:56'),
	(6, 1, 1, NULL, '2026-04-18', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:07:16'),
	(7, 1, 2, NULL, '2026-04-17', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:07:16'),
	(8, 1, 3, NULL, '2026-04-16', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:07:16'),
	(9, 1, 4, NULL, '2026-04-15', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:07:16'),
	(10, 1, 5, NULL, '2026-04-14', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:07:16'),
	(11, 1, 11, NULL, '2026-04-18', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:07:16'),
	(12, 1, 12, NULL, '2026-04-17', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:07:16'),
	(13, 1, 13, NULL, '2026-04-16', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:07:16'),
	(14, 1, 14, NULL, '2026-04-15', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:07:16'),
	(15, 1, 15, NULL, '2026-04-14', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:07:16'),
	(16, 1, 1, NULL, '2026-04-18', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:08:19'),
	(17, 1, 2, NULL, '2026-04-17', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:08:19'),
	(18, 1, 3, NULL, '2026-04-16', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:08:19'),
	(19, 1, 4, NULL, '2026-04-15', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:08:19'),
	(20, 1, 5, NULL, '2026-04-14', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:08:19'),
	(21, 1, 11, NULL, '2026-04-18', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:08:19'),
	(22, 1, 12, NULL, '2026-04-17', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:08:19'),
	(23, 1, 13, NULL, '2026-04-16', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:08:19'),
	(24, 1, 14, NULL, '2026-04-15', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:08:19'),
	(25, 1, 15, NULL, '2026-04-14', '10:00:00', '12:00:00', 2, 'completada', 0.00, NULL, NULL, '2026-04-18 04:08:19');

-- Volcando estructura para tabla condominio_crm.residentes
CREATE TABLE IF NOT EXISTS `residentes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unidad_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('propietario','inquilino','familiar','dependiente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'propietario',
  `documento_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_documento` enum('INE','Pasaporte','CURP','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'INE',
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('M','F','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono_alt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_salida` date DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `unidad_id` (`unidad_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `residentes_ibfk_1` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`),
  CONSTRAINT `residentes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.residentes: ~29 rows (aproximadamente)
DELETE FROM `residentes`;
INSERT INTO `residentes` (`id`, `unidad_id`, `usuario_id`, `nombre`, `apellidos`, `tipo`, `documento_id`, `tipo_documento`, `fecha_nacimiento`, `genero`, `email`, `telefono`, `telefono_alt`, `foto_url`, `fecha_ingreso`, `fecha_salida`, `activo`, `notas`, `creado_en`) VALUES
	(1, 1, 2, 'Ana', 'Garc├¡a Ramos', 'propietario', 'GARA850101', 'INE', NULL, NULL, 'ana.garcia@email.com', '55-3333-4444', NULL, NULL, '2020-01-15', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(2, 2, NULL, 'Pedro', 'Mart├¡nez Silva', 'propietario', 'MASP780505', 'INE', NULL, NULL, 'pedro.martinez@email.com', '55-2222-1111', NULL, NULL, '2019-06-01', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(3, 3, 3, 'Roberto', 'Flores D├¡az', 'propietario', 'FLDR750220', 'INE', NULL, NULL, 'roberto.flores@email.com', '55-5555-6666', NULL, NULL, '2021-03-10', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(4, 5, 4, 'Mar├¡a', 'Torres Vega', 'inquilino', 'TORM900315', 'INE', NULL, NULL, 'maria.torres@email.com', '55-7777-8888', NULL, NULL, '2023-01-01', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(5, 6, NULL, 'Jorge', 'L├│pez Hern├índez', 'propietario', 'LOHJ820810', 'INE', NULL, NULL, 'jorge.lopez@email.com', '55-4444-5555', NULL, NULL, '2018-09-20', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(6, 8, NULL, 'Carmen', 'V├ízquez Ruiz', 'propietario', 'VARC891201', 'INE', NULL, NULL, 'carmen.vazquez@email.com', '55-6666-7777', NULL, NULL, '2022-07-15', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(7, 9, NULL, 'Diego', 'Morales Pe├▒a', 'propietario', 'MOPD950401', 'INE', NULL, NULL, 'diego.morales@email.com', '55-8888-9999', NULL, NULL, '2023-05-01', NULL, 1, NULL, '2026-04-18 02:45:01'),
	(8, 10, NULL, 'Carlos', 'Ram', 'inquilino', '123456789', NULL, NULL, NULL, '', '555-0199', NULL, NULL, '2026-04-18', NULL, 1, NULL, '2026-04-18 03:16:30'),
	(9, 1, NULL, 'Carlos', 'Ramirez', 'inquilino', '123456789', NULL, NULL, NULL, '', '555-0199', NULL, NULL, '2026-04-18', NULL, 1, NULL, '2026-04-18 03:18:25'),
	(10, 1, NULL, 'Roberto', 'Martínez', 'propietario', NULL, 'INE', NULL, NULL, 'residente0@correo.com', '5551234560', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:07:16'),
	(11, 2, NULL, 'Lucía', 'López', 'propietario', NULL, 'INE', NULL, NULL, 'residente1@correo.com', '5551234561', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:07:16'),
	(12, 3, NULL, 'Fernando', 'González', 'propietario', NULL, 'INE', NULL, NULL, 'residente2@correo.com', '5551234562', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:07:16'),
	(13, 4, NULL, 'Sofía', 'Pérez', 'propietario', NULL, 'INE', NULL, NULL, 'residente3@correo.com', '5551234563', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:07:16'),
	(14, 5, NULL, 'Andrés', 'Rodríguez', 'propietario', NULL, 'INE', NULL, NULL, 'residente4@correo.com', '5551234564', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:07:16'),
	(15, 11, NULL, 'Valeria', 'Sánchez', 'propietario', NULL, 'INE', NULL, NULL, 'residente5@correo.com', '5551234565', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:07:16'),
	(16, 12, NULL, 'Javier', 'Ramírez', 'propietario', NULL, 'INE', NULL, NULL, 'residente6@correo.com', '5551234566', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:07:16'),
	(17, 13, NULL, 'Carmen', 'Cruz', 'propietario', NULL, 'INE', NULL, NULL, 'residente7@correo.com', '5551234567', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:07:16'),
	(18, 14, NULL, 'Ricardo', 'Gómez', 'propietario', NULL, 'INE', NULL, NULL, 'residente8@correo.com', '5551234568', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:07:16'),
	(19, 15, NULL, 'Elena', 'Flores', 'propietario', NULL, 'INE', NULL, NULL, 'residente9@correo.com', '5551234569', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:07:16'),
	(20, 1, NULL, 'Roberto', 'Martínez', 'propietario', NULL, 'INE', NULL, NULL, 'residente0@correo.com', '5551234560', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:08:19'),
	(21, 2, NULL, 'Lucía', 'López', 'propietario', NULL, 'INE', NULL, NULL, 'residente1@correo.com', '5551234561', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:08:19'),
	(22, 3, NULL, 'Fernando', 'González', 'propietario', NULL, 'INE', NULL, NULL, 'residente2@correo.com', '5551234562', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:08:19'),
	(23, 4, NULL, 'Sofía', 'Pérez', 'propietario', NULL, 'INE', NULL, NULL, 'residente3@correo.com', '5551234563', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:08:19'),
	(24, 5, NULL, 'Andrés', 'Rodríguez', 'propietario', NULL, 'INE', NULL, NULL, 'residente4@correo.com', '5551234564', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:08:19'),
	(25, 11, NULL, 'Valeria', 'Sánchez', 'propietario', NULL, 'INE', NULL, NULL, 'residente5@correo.com', '5551234565', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:08:19'),
	(26, 12, NULL, 'Javier', 'Ramírez', 'propietario', NULL, 'INE', NULL, NULL, 'residente6@correo.com', '5551234566', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:08:19'),
	(27, 13, NULL, 'Carmen', 'Cruz', 'propietario', NULL, 'INE', NULL, NULL, 'residente7@correo.com', '5551234567', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:08:19'),
	(28, 14, NULL, 'Ricardo', 'Gómez', 'propietario', NULL, 'INE', NULL, NULL, 'residente8@correo.com', '5551234568', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:08:19'),
	(29, 15, NULL, 'Elena', 'Flores', 'propietario', NULL, 'INE', NULL, NULL, 'residente9@correo.com', '5551234569', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-18 04:08:19');

-- Volcando estructura para tabla condominio_crm.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permisos` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.roles: ~6 rows (aproximadamente)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `permisos`) VALUES
	(1, 'super_admin', 'Acceso total al sistema', NULL),
	(2, 'administrador', 'Gesti├│n completa del condominio', NULL),
	(3, 'contador', 'M├│dulos de finanzas y reportes', NULL),
	(4, 'residente', 'Portal de residente', NULL),
	(5, 'guardia', 'Control de acceso y seguridad', NULL),
	(6, 'mantenimiento', '├ôrdenes de trabajo', NULL);

-- Volcando estructura para tabla condominio_crm.rondas_vigilancia
CREATE TABLE IF NOT EXISTS `rondas_vigilancia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `guardia_id` int NOT NULL,
  `inicio` datetime NOT NULL,
  `fin` datetime DEFAULT NULL,
  `ruta` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` enum('en_curso','completada','incompleta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'en_curso',
  PRIMARY KEY (`id`),
  KEY `guardia_id` (`guardia_id`),
  CONSTRAINT `rondas_vigilancia_ibfk_1` FOREIGN KEY (`guardia_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.rondas_vigilancia: ~0 rows (aproximadamente)
DELETE FROM `rondas_vigilancia`;

-- Volcando estructura para tabla condominio_crm.sesiones
CREATE TABLE IF NOT EXISTS `sesiones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `refresh_token` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expira_en` datetime DEFAULT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `sesiones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.sesiones: ~9 rows (aproximadamente)
DELETE FROM `sesiones`;
INSERT INTO `sesiones` (`id`, `usuario_id`, `refresh_token`, `ip_address`, `user_agent`, `expira_en`, `creado_en`) VALUES
	(2, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNzc2NjA4NTIwLCJleHAiOjE3NzcyMTMzMjB9.o5zLjurpFH-IVGVwFYxQgVCJrblEzO3ehdOlqo7t5vY', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 14:22:00', '2026-04-18 04:27:43'),
	(3, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNzc2NTEzMTcwLCJleHAiOjE3NzcxMTc5NzB9.DcjG-ENhkX4xybXIKsci0_osoImenEXr-vTtNrIzMFw', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 11:52:50', '2026-04-18 06:52:50'),
	(4, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNzc2NjA4MTMwLCJleHAiOjE3NzcyMTI5MzB9.jpN_NGtiUM3_r9CBsUuGM5dXwYupqdoqZUaIdrkmSgc', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 14:15:30', '2026-04-19 09:15:30'),
	(5, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNzc2NjkyNzc0LCJleHAiOjE3NzcyOTc1NzR9.jb0vJ75Tegwk3l9kGBh4hmC8h0bS6wwopWjm7OlY99I', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 13:46:15', '2026-04-19 19:27:44'),
	(6, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNzc2NzQ1Njg1LCJleHAiOjE3NzczNTA0ODV9.fDhqjsyxJGVYU5MCfdWfnZjBxpg84KhXWj6DxwfXkxs', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 04:28:05', '2026-04-20 08:46:18'),
	(7, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNzc2Nzc1MDMwLCJleHAiOjE3NzczNzk4MzB9.nm5thnP-mngO4QZzzMI7v7i9_YaucB5Vc0d0KBJZp1A', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:37:11', '2026-04-20 23:28:07'),
	(9, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNzc2ODExNDE5LCJleHAiOjE3Nzc0MTYyMTl9.64eYf61sBkcUM0OKPkAnZQpzfBQsEWfo4vWwC3bedVk', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 22:43:39', '2026-04-21 07:55:31'),
	(10, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNzc2ODYyMTA5LCJleHAiOjE3Nzc0NjY5MDl9.QRuv4wrXkhnAEtcGWdDnbWqAyoiyFRJtbkeT-4Jw9UM', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 12:48:30', '2026-04-21 17:43:42'),
	(11, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNzc2ODYyMTEyLCJleHAiOjE3Nzc0NjY5MTJ9.cEDfrB9SeiDXLplGMwApAJDlGkyKlLqFk_EHTh9Fo-U', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 12:48:33', '2026-04-22 07:48:32');

-- Volcando estructura para tabla condominio_crm.tipos_cuota
CREATE TABLE IF NOT EXISTS `tipos_cuota` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `monto_base` decimal(10,2) DEFAULT NULL,
  `periodicidad` enum('mensual','bimestral','trimestral','anual','├║nica') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'mensual',
  `aplica_mora` tinyint(1) DEFAULT '1',
  `tasa_mora` decimal(5,2) DEFAULT '0.05',
  `dias_gracia` smallint DEFAULT '5',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.tipos_cuota: ~3 rows (aproximadamente)
DELETE FROM `tipos_cuota`;
INSERT INTO `tipos_cuota` (`id`, `nombre`, `descripcion`, `monto_base`, `periodicidad`, `aplica_mora`, `tasa_mora`, `dias_gracia`, `activo`) VALUES
	(1, 'Cuota Ordinaria', 'Cuota mensual de mantenimiento', 2500.00, 'mensual', 1, 0.05, 5, 1),
	(2, 'Cuota Extraordinaria', 'Reparaci├│n de elevadores', 1500.00, '├║nica', 1, 0.00, 30, 1),
	(3, 'Sal├│n de Eventos', 'Renta sal├│n de usos m├║ltiples', 800.00, '├║nica', 1, 0.00, 0, 1);

-- Volcando estructura para tabla condominio_crm.torres
CREATE TABLE IF NOT EXISTS `torres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `condominio_id` int NOT NULL,
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_pisos` smallint DEFAULT '1',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `condominio_id` (`condominio_id`),
  CONSTRAINT `torres_ibfk_1` FOREIGN KEY (`condominio_id`) REFERENCES `condominios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.torres: ~3 rows (aproximadamente)
DELETE FROM `torres`;
INSERT INTO `torres` (`id`, `condominio_id`, `nombre`, `total_pisos`, `descripcion`, `activo`) VALUES
	(1, 1, 'Torre A', 10, NULL, 1),
	(2, 1, 'Torre B', 10, NULL, 1),
	(3, 1, 'Torre C', 8, NULL, 1);

-- Volcando estructura para tabla condominio_crm.transacciones
CREATE TABLE IF NOT EXISTS `transacciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cuenta_id` int NOT NULL,
  `tipo` enum('ingreso','egreso') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `categoria` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprobante_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proveedor_id` int DEFAULT NULL,
  `registrado_por` int DEFAULT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cuenta_id` (`cuenta_id`),
  KEY `registrado_por` (`registrado_por`),
  CONSTRAINT `transacciones_ibfk_1` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas_contables` (`id`),
  CONSTRAINT `transacciones_ibfk_2` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.transacciones: ~21 rows (aproximadamente)
DELETE FROM `transacciones`;
INSERT INTO `transacciones` (`id`, `cuenta_id`, `tipo`, `monto`, `fecha`, `descripcion`, `categoria`, `comprobante_url`, `referencia`, `proveedor_id`, `registrado_por`, `creado_en`) VALUES
	(1, 1, 'ingreso', 22500.00, '2026-02-28', 'Cuotas cobradas febrero 2026', 'Cuotas', NULL, NULL, NULL, 6, '2026-04-18 02:45:01'),
	(2, 1, 'ingreso', 17500.00, '2026-03-31', 'Cuotas cobradas marzo 2026', 'Cuotas', NULL, NULL, NULL, 6, '2026-04-18 02:45:01'),
	(3, 1, 'ingreso', 10000.00, '2026-04-17', 'Cuotas cobradas abril 2026 (parcial)', 'Cuotas', NULL, NULL, NULL, 6, '2026-04-18 02:45:01'),
	(4, 4, 'egreso', 8000.00, '2026-02-15', 'Servicio de mantenimiento elevadores', 'Mantenimiento', NULL, NULL, NULL, 6, '2026-04-18 02:45:01'),
	(5, 5, 'egreso', 15000.00, '2026-02-28', 'Pago mensual vigilancia', 'Seguridad', NULL, NULL, NULL, 6, '2026-04-18 02:45:01'),
	(6, 6, 'egreso', 4500.00, '2026-02-28', 'Jardiner├¡a y limpieza', 'Limpieza', NULL, NULL, NULL, 6, '2026-04-18 02:45:01'),
	(7, 7, 'egreso', 6200.00, '2026-02-28', 'Agua y luz ├íreas comunes', 'Servicios', NULL, NULL, NULL, 6, '2026-04-18 02:45:01'),
	(8, 5, 'egreso', 15000.00, '2026-03-31', 'Pago mensual vigilancia', 'Seguridad', NULL, NULL, NULL, 6, '2026-04-18 02:45:01'),
	(9, 6, 'egreso', 4500.00, '2026-03-31', 'Jardiner├¡a y limpieza', 'Limpieza', NULL, NULL, NULL, 6, '2026-04-18 02:45:01'),
	(10, 7, 'egreso', 6200.00, '2026-03-31', 'Agua y luz ├íreas comunes', 'Servicios', NULL, NULL, NULL, 6, '2026-04-18 02:45:01'),
	(11, 1, 'ingreso', 1500.00, '2026-04-18', 'Pago Abril Confirmado', 'Cuotas de Mantenimiento', NULL, NULL, NULL, 1, '2026-04-18 04:04:04'),
	(12, 1, 'egreso', 500.00, '2026-04-18', 'Operación de prueba #0', 'Reparaciones', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(13, 1, 'ingreso', 1500.00, '2026-04-17', 'Operación de prueba #1', 'Cuotas de Mantenimiento', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(14, 1, 'ingreso', 1500.00, '2026-04-16', 'Operación de prueba #2', 'Cuotas de Mantenimiento', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(15, 1, 'egreso', 1100.00, '2026-04-15', 'Operación de prueba #3', 'Reparaciones', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(16, 1, 'ingreso', 1500.00, '2026-04-14', 'Operación de prueba #4', 'Cuotas de Mantenimiento', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(17, 1, 'ingreso', 1500.00, '2026-04-13', 'Operación de prueba #5', 'Cuotas de Mantenimiento', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(18, 1, 'egreso', 1700.00, '2026-04-12', 'Operación de prueba #6', 'Reparaciones', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(19, 1, 'ingreso', 1500.00, '2026-04-11', 'Operación de prueba #7', 'Cuotas de Mantenimiento', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(20, 1, 'ingreso', 1500.00, '2026-04-10', 'Operación de prueba #8', 'Cuotas de Mantenimiento', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19'),
	(21, 1, 'egreso', 2300.00, '2026-04-09', 'Operación de prueba #9', 'Reparaciones', NULL, NULL, NULL, NULL, '2026-04-18 04:08:19');

-- Volcando estructura para tabla condominio_crm.unidades
CREATE TABLE IF NOT EXISTS `unidades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `torre_id` int DEFAULT NULL,
  `numero` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `piso` smallint DEFAULT '1',
  `tipo` enum('departamento','casa','local','bodega','caj├│n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'departamento',
  `metros_cuadrados` decimal(8,2) DEFAULT NULL,
  `estado` enum('habitada','vac├¡a','en_venta','en_renta','en_construccion') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'vac├¡a',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `torre_id` (`torre_id`),
  CONSTRAINT `unidades_ibfk_1` FOREIGN KEY (`torre_id`) REFERENCES `torres` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.unidades: ~30 rows (aproximadamente)
DELETE FROM `unidades`;
INSERT INTO `unidades` (`id`, `torre_id`, `numero`, `piso`, `tipo`, `metros_cuadrados`, `estado`, `descripcion`, `activo`, `creado_en`) VALUES
	(1, 1, 'A-101', 1, 'departamento', 85.00, 'habitada', NULL, 1, '2026-04-18 02:45:01'),
	(2, 1, 'A-102', 1, 'departamento', 90.00, 'habitada', NULL, 1, '2026-04-18 02:45:01'),
	(3, 1, 'A-201', 2, 'departamento', 85.00, 'habitada', NULL, 1, '2026-04-18 02:45:01'),
	(4, 1, 'A-202', 2, 'departamento', 90.00, 'vac├¡a', NULL, 1, '2026-04-18 02:45:01'),
	(5, 1, 'A-301', 3, 'departamento', 120.00, 'habitada', NULL, 1, '2026-04-18 02:45:01'),
	(6, 2, 'B-101', 1, 'departamento', 75.00, 'habitada', NULL, 1, '2026-04-18 02:45:01'),
	(7, 2, 'B-102', 1, 'departamento', 75.00, 'en_renta', NULL, 1, '2026-04-18 02:45:01'),
	(8, 2, 'B-201', 2, 'departamento', 95.00, 'habitada', NULL, 1, '2026-04-18 02:45:01'),
	(9, 3, 'C-101', 1, 'departamento', 110.00, 'habitada', NULL, 1, '2026-04-18 02:45:01'),
	(10, 3, 'C-102', 1, 'departamento', 110.00, 'habitada', NULL, 1, '2026-04-18 02:45:01'),
	(11, 1, 'B-102', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:07:16'),
	(12, 1, 'B-103', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:07:16'),
	(13, 1, 'B-104', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:07:16'),
	(14, 1, 'B-105', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:07:16'),
	(15, 1, 'B-106', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:07:16'),
	(16, 1, 'B-107', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:07:16'),
	(17, 1, 'B-108', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:07:16'),
	(18, 1, 'B-109', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:07:16'),
	(19, 1, 'B-110', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:07:16'),
	(20, 1, 'B-111', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:07:16'),
	(21, 1, 'B-102', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:08:19'),
	(22, 1, 'B-103', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:08:19'),
	(23, 1, 'B-104', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:08:19'),
	(24, 1, 'B-105', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:08:19'),
	(25, 1, 'B-106', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:08:19'),
	(26, 1, 'B-107', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:08:19'),
	(27, 1, 'B-108', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:08:19'),
	(28, 1, 'B-109', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:08:19'),
	(29, 1, 'B-110', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:08:19'),
	(30, 1, 'B-111', 1, 'departamento', NULL, 'habitada', NULL, 1, '2026-04-18 04:08:19');

-- Volcando estructura para tabla condominio_crm.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol_id` int NOT NULL,
  `unidad_id` int DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `token_recuperacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `rol_id` (`rol_id`),
  KEY `unidad_id` (`unidad_id`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.usuarios: ~7 rows (aproximadamente)
DELETE FROM `usuarios`;
INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `password_hash`, `telefono`, `foto_url`, `rol_id`, `unidad_id`, `activo`, `token_recuperacion`, `token_expira`, `ultimo_acceso`, `creado_en`, `actualizado_en`) VALUES
	(1, 'Carlos', 'Mendoza Lopez', 'admin@laspalmas.com', '$2b$10$0mz9fC.jrAgklLexN4ivW.v.Q9fsYJ08Y.WtaHf7twyFH8.gSxHl2', '55-1111-2222', NULL, 2, NULL, 1, NULL, NULL, '2026-04-22 07:48:32', '2026-04-18 02:45:01', '2026-04-22 07:48:32'),
	(2, 'Ana', 'Garc├¡a Ramos', 'ana.garcia@email.com', '$2b$10$0mz9fC.jrAgklLexN4ivW.v.Q9fsYJ08Y.WtaHf7twyFH8.gSxHl2', '55-3333-4444', NULL, 4, 1, 1, NULL, NULL, NULL, '2026-04-18 02:45:01', '2026-04-18 02:54:52'),
	(3, 'Roberto', 'Flores D├¡az', 'roberto.flores@email.com', '$2b$10$0mz9fC.jrAgklLexN4ivW.v.Q9fsYJ08Y.WtaHf7twyFH8.gSxHl2', '55-5555-6666', NULL, 4, 3, 1, NULL, NULL, NULL, '2026-04-18 02:45:01', '2026-04-18 02:54:52'),
	(4, 'Mar├¡a', 'Torres Vega', 'maria.torres@email.com', '$2b$10$0mz9fC.jrAgklLexN4ivW.v.Q9fsYJ08Y.WtaHf7twyFH8.gSxHl2', '55-7777-8888', NULL, 4, 5, 1, NULL, NULL, NULL, '2026-04-18 02:45:01', '2026-04-18 02:54:52'),
	(5, 'Juan', 'Ram├¡rez Cruz', 'juan.guardia@laspalmas.com', '$2b$10$0mz9fC.jrAgklLexN4ivW.v.Q9fsYJ08Y.WtaHf7twyFH8.gSxHl2', '55-9999-0000', NULL, 5, NULL, 1, NULL, NULL, NULL, '2026-04-18 02:45:01', '2026-04-18 02:54:52'),
	(6, 'Laura', 'S├ínchez Mora', 'laura.contadora@laspalmas.com', '$2b$10$0mz9fC.jrAgklLexN4ivW.v.Q9fsYJ08Y.WtaHf7twyFH8.gSxHl2', '55-1212-3434', NULL, 3, NULL, 1, NULL, NULL, NULL, '2026-04-18 02:45:01', '2026-04-18 02:54:52'),
	(7, 'Alice', 'User', 'testuser@example.com', '$2b$10$XYpB0GQllUB61P49bjQ89etS/VGn0TxY/jotc9BDn.wCww9Zdpbhy', 'testuser2@test.com', NULL, 1, NULL, 1, NULL, NULL, NULL, '2026-04-18 04:25:30', '2026-04-18 04:26:44');

-- Volcando estructura para tabla condominio_crm.vehiculos
CREATE TABLE IF NOT EXISTS `vehiculos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unidad_id` int NOT NULL,
  `residente_id` int DEFAULT NULL,
  `marca` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anio` year DEFAULT NULL,
  `color` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `placas` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('auto','camioneta','moto','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'auto',
  `activo` tinyint(1) DEFAULT '1',
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `unidad_id` (`unidad_id`),
  KEY `residente_id` (`residente_id`),
  CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`),
  CONSTRAINT `vehiculos_ibfk_2` FOREIGN KEY (`residente_id`) REFERENCES `residentes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.vehiculos: ~5 rows (aproximadamente)
DELETE FROM `vehiculos`;
INSERT INTO `vehiculos` (`id`, `unidad_id`, `residente_id`, `marca`, `modelo`, `anio`, `color`, `placas`, `tipo`, `activo`, `creado_en`) VALUES
	(1, 1, 1, 'Toyota', 'Corolla', '2021', 'Blanco', 'ABC-123', 'auto', 1, '2026-04-18 02:45:01'),
	(2, 1, 1, 'Nissan', 'Pathfinder', '2019', 'Negro', 'XYZ-789', 'auto', 1, '2026-04-18 02:45:01'),
	(3, 2, 2, 'Chevrolet', 'Trax', '2022', 'Rojo', 'DEF-456', 'auto', 1, '2026-04-18 02:45:01'),
	(4, 3, 3, 'Honda', 'CRV', '2020', 'Gris', 'GHI-321', 'auto', 1, '2026-04-18 02:45:01'),
	(5, 5, 4, 'Kia', 'Sportage', '2023', 'Azul', 'JKL-654', 'auto', 1, '2026-04-18 02:45:01');

-- Volcando estructura para tabla condominio_crm.visitantes
CREATE TABLE IF NOT EXISTS `visitantes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidad_id` int DEFAULT NULL,
  `residente_id` int DEFAULT NULL,
  `motivo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('visita','proveedor','delivery','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'visita',
  `entrada` datetime DEFAULT CURRENT_TIMESTAMP,
  `salida` datetime DEFAULT NULL,
  `autorizado_por` int DEFAULT NULL,
  `guardia_id` int DEFAULT NULL,
  `vehiculo_placas` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `unidad_id` (`unidad_id`),
  KEY `residente_id` (`residente_id`),
  KEY `autorizado_por` (`autorizado_por`),
  KEY `guardia_id` (`guardia_id`),
  KEY `idx_visitantes_fecha` (`entrada`),
  CONSTRAINT `visitantes_ibfk_1` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`),
  CONSTRAINT `visitantes_ibfk_2` FOREIGN KEY (`residente_id`) REFERENCES `residentes` (`id`),
  CONSTRAINT `visitantes_ibfk_3` FOREIGN KEY (`autorizado_por`) REFERENCES `residentes` (`id`),
  CONSTRAINT `visitantes_ibfk_4` FOREIGN KEY (`guardia_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.visitantes: ~4 rows (aproximadamente)
DELETE FROM `visitantes`;
INSERT INTO `visitantes` (`id`, `nombre`, `documento_id`, `foto_url`, `unidad_id`, `residente_id`, `motivo`, `tipo`, `entrada`, `salida`, `autorizado_por`, `guardia_id`, `vehiculo_placas`, `notas`) VALUES
	(1, 'Luis Fern├índez', 'FELT900101', NULL, 1, 1, 'Visita familiar', 'visita', '2026-04-18 10:00:00', '2026-04-18 13:00:00', NULL, 5, NULL, NULL),
	(2, 'Plomer├¡a Gonz├ílez', 'GOMA750501', NULL, 3, 3, 'Reparaci├│n de fuga', 'proveedor', '2026-04-18 09:00:00', '2026-04-18 11:30:00', NULL, 5, NULL, NULL),
	(3, 'Amazon Delivery', NULL, NULL, 5, 4, 'Entrega de paquete', 'delivery', '2026-04-18 08:45:00', '2026-04-18 08:50:00', NULL, 5, NULL, NULL),
	(4, 'Sof├¡a Guti├®rrez', 'GUTS950615', NULL, 6, 6, 'Visita amiga', 'visita', '2026-04-18 15:00:00', NULL, NULL, 5, NULL, NULL);

-- Volcando estructura para tabla condominio_crm.votos
CREATE TABLE IF NOT EXISTS `votos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `encuesta_id` int NOT NULL,
  `residente_id` int NOT NULL,
  `respuesta` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `encuesta_id` (`encuesta_id`),
  KEY `residente_id` (`residente_id`),
  CONSTRAINT `votos_ibfk_1` FOREIGN KEY (`encuesta_id`) REFERENCES `encuestas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `votos_ibfk_2` FOREIGN KEY (`residente_id`) REFERENCES `residentes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla condominio_crm.votos: ~0 rows (aproximadamente)
DELETE FROM `votos`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
