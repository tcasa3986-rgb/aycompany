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


-- Volcando estructura de base de datos para sistema_veterinaria_db
CREATE DATABASE IF NOT EXISTS `sistema_veterinaria_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `sistema_veterinaria_db`;

-- Volcando estructura para tabla sistema_veterinaria_db.agenda_cita
CREATE TABLE IF NOT EXISTS `agenda_cita` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `hora` time(6) NOT NULL,
  `motivo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` longtext COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime(6) NOT NULL,
  `mascota_id` bigint NOT NULL,
  `veterinario_id` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `agenda_cita_mascota_id_10ddc0b7_fk_clientes_mascota_id` (`mascota_id`),
  KEY `agenda_cita_veterinario_id_a9e51059_fk_auth_user_id` (`veterinario_id`),
  CONSTRAINT `agenda_cita_mascota_id_10ddc0b7_fk_clientes_mascota_id` FOREIGN KEY (`mascota_id`) REFERENCES `clientes_mascota` (`id`),
  CONSTRAINT `agenda_cita_veterinario_id_a9e51059_fk_auth_user_id` FOREIGN KEY (`veterinario_id`) REFERENCES `auth_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.agenda_cita: ~7 rows (aproximadamente)
DELETE FROM `agenda_cita`;
INSERT INTO `agenda_cita` (`id`, `fecha`, `hora`, `motivo`, `tipo`, `estado`, `observaciones`, `fecha_creacion`, `mascota_id`, `veterinario_id`, `is_active`) VALUES
	(15, '2026-03-01', '10:00:00.000000', 'Control mensual', 'CONSULTA', 'COMPLETADA', NULL, '2026-03-06 17:25:06.038764', 1, 2, 1),
	(16, '2026-03-03', '11:30:00.000000', 'Vacuna triple felina', 'VACUNACION', 'COMPLETADA', NULL, '2026-03-06 17:25:06.042507', 2, 3, 1),
	(17, '2026-03-05', '15:00:00.000000', 'Cojera pata delantera', 'REVISION', 'COMPLETADA', NULL, '2026-03-06 17:25:06.050430', 3, 2, 1),
	(18, '2026-03-06', '09:00:00.000000', 'Problemas de piel', 'CONSULTA', 'COMPLETADA', NULL, '2026-03-06 17:25:06.053949', 4, 2, 1),
	(19, '2026-03-06', '14:00:00.000000', 'Decaimiento', 'CONSULTA', 'EN_ATENCION', NULL, '2026-03-06 17:25:06.056889', 5, 3, 1),
	(20, '2026-03-06', '17:30:00.000000', 'Baño y deslanado', 'PELUQUERIA', 'PENDIENTE', NULL, '2026-03-06 17:25:06.061117', 6, 2, 1),
	(21, '2026-03-07', '10:00:00.000000', 'Seguimiento profilaxis', 'CONSULTA', 'PENDIENTE', NULL, '2026-03-06 17:25:06.064923', 1, 3, 1),
	(22, '2026-03-07', '14:00:00.000000', 'Chequeo mensual', 'CONSULTA', 'COMPLETADA', 'chequeo rutinario', '2026-03-07 04:53:13.399360', 7, 2, 1);

-- Volcando estructura para tabla sistema_veterinaria_db.auth_group
CREATE TABLE IF NOT EXISTS `auth_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.auth_group: ~0 rows (aproximadamente)
DELETE FROM `auth_group`;

-- Volcando estructura para tabla sistema_veterinaria_db.auth_group_permissions
CREATE TABLE IF NOT EXISTS `auth_group_permissions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_group_permissions_group_id_permission_id_0cd325b0_uniq` (`group_id`,`permission_id`),
  KEY `auth_group_permissio_permission_id_84c5c92e_fk_auth_perm` (`permission_id`),
  CONSTRAINT `auth_group_permissio_permission_id_84c5c92e_fk_auth_perm` FOREIGN KEY (`permission_id`) REFERENCES `auth_permission` (`id`),
  CONSTRAINT `auth_group_permissions_group_id_b120cbf9_fk_auth_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.auth_group_permissions: ~0 rows (aproximadamente)
DELETE FROM `auth_group_permissions`;

-- Volcando estructura para tabla sistema_veterinaria_db.auth_permission
CREATE TABLE IF NOT EXISTS `auth_permission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_type_id` int NOT NULL,
  `codename` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_permission_content_type_id_codename_01ab375a_uniq` (`content_type_id`,`codename`),
  CONSTRAINT `auth_permission_content_type_id_2f476e4b_fk_django_co` FOREIGN KEY (`content_type_id`) REFERENCES `django_content_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.auth_permission: ~92 rows (aproximadamente)
DELETE FROM `auth_permission`;
INSERT INTO `auth_permission` (`id`, `name`, `content_type_id`, `codename`) VALUES
	(1, 'Can add log entry', 1, 'add_logentry'),
	(2, 'Can change log entry', 1, 'change_logentry'),
	(3, 'Can delete log entry', 1, 'delete_logentry'),
	(4, 'Can view log entry', 1, 'view_logentry'),
	(5, 'Can add permission', 3, 'add_permission'),
	(6, 'Can change permission', 3, 'change_permission'),
	(7, 'Can delete permission', 3, 'delete_permission'),
	(8, 'Can view permission', 3, 'view_permission'),
	(9, 'Can add group', 2, 'add_group'),
	(10, 'Can change group', 2, 'change_group'),
	(11, 'Can delete group', 2, 'delete_group'),
	(12, 'Can view group', 2, 'view_group'),
	(13, 'Can add user', 4, 'add_user'),
	(14, 'Can change user', 4, 'change_user'),
	(15, 'Can delete user', 4, 'delete_user'),
	(16, 'Can view user', 4, 'view_user'),
	(17, 'Can add content type', 5, 'add_contenttype'),
	(18, 'Can change content type', 5, 'change_contenttype'),
	(19, 'Can delete content type', 5, 'delete_contenttype'),
	(20, 'Can view content type', 5, 'view_contenttype'),
	(21, 'Can add session', 6, 'add_session'),
	(22, 'Can change session', 6, 'change_session'),
	(23, 'Can delete session', 6, 'delete_session'),
	(24, 'Can view session', 6, 'view_session'),
	(25, 'Can add clinica', 7, 'add_clinica'),
	(26, 'Can change clinica', 7, 'change_clinica'),
	(27, 'Can delete clinica', 7, 'delete_clinica'),
	(28, 'Can view clinica', 7, 'view_clinica'),
	(29, 'Can add cliente', 8, 'add_cliente'),
	(30, 'Can change cliente', 8, 'change_cliente'),
	(31, 'Can delete cliente', 8, 'delete_cliente'),
	(32, 'Can view cliente', 8, 'view_cliente'),
	(33, 'Can add mascota', 9, 'add_mascota'),
	(34, 'Can change mascota', 9, 'change_mascota'),
	(35, 'Can delete mascota', 9, 'delete_mascota'),
	(36, 'Can view mascota', 9, 'view_mascota'),
	(37, 'Can add cita', 10, 'add_cita'),
	(38, 'Can change cita', 10, 'change_cita'),
	(39, 'Can delete cita', 10, 'delete_cita'),
	(40, 'Can view cita', 10, 'view_cita'),
	(41, 'Can add historia clinica', 12, 'add_historiaclinica'),
	(42, 'Can change historia clinica', 12, 'change_historiaclinica'),
	(43, 'Can delete historia clinica', 12, 'delete_historiaclinica'),
	(44, 'Can view historia clinica', 12, 'view_historiaclinica'),
	(45, 'Can add receta', 13, 'add_receta'),
	(46, 'Can change receta', 13, 'change_receta'),
	(47, 'Can delete receta', 13, 'delete_receta'),
	(48, 'Can view receta', 13, 'view_receta'),
	(49, 'Can add detalle receta', 11, 'add_detallereceta'),
	(50, 'Can change detalle receta', 11, 'change_detallereceta'),
	(51, 'Can delete detalle receta', 11, 'delete_detallereceta'),
	(52, 'Can view detalle receta', 11, 'view_detallereceta'),
	(53, 'Can add vacuna', 14, 'add_vacuna'),
	(54, 'Can change vacuna', 14, 'change_vacuna'),
	(55, 'Can delete vacuna', 14, 'delete_vacuna'),
	(56, 'Can view vacuna', 14, 'view_vacuna'),
	(57, 'Can add categoria', 15, 'add_categoria'),
	(58, 'Can change categoria', 15, 'change_categoria'),
	(59, 'Can delete categoria', 15, 'delete_categoria'),
	(60, 'Can view categoria', 15, 'view_categoria'),
	(61, 'Can add proveedor', 18, 'add_proveedor'),
	(62, 'Can change proveedor', 18, 'change_proveedor'),
	(63, 'Can delete proveedor', 18, 'delete_proveedor'),
	(64, 'Can view proveedor', 18, 'view_proveedor'),
	(65, 'Can add producto', 17, 'add_producto'),
	(66, 'Can change producto', 17, 'change_producto'),
	(67, 'Can delete producto', 17, 'delete_producto'),
	(68, 'Can view producto', 17, 'view_producto'),
	(69, 'Can add movimiento', 16, 'add_movimiento'),
	(70, 'Can change movimiento', 16, 'change_movimiento'),
	(71, 'Can delete movimiento', 16, 'delete_movimiento'),
	(72, 'Can view movimiento', 16, 'view_movimiento'),
	(73, 'Can add caja', 19, 'add_caja'),
	(74, 'Can change caja', 19, 'change_caja'),
	(75, 'Can delete caja', 19, 'delete_caja'),
	(76, 'Can view caja', 19, 'view_caja'),
	(77, 'Can add factura', 21, 'add_factura'),
	(78, 'Can change factura', 21, 'change_factura'),
	(79, 'Can delete factura', 21, 'delete_factura'),
	(80, 'Can view factura', 21, 'view_factura'),
	(81, 'Can add detalle factura', 20, 'add_detallefactura'),
	(82, 'Can change detalle factura', 20, 'change_detallefactura'),
	(83, 'Can delete detalle factura', 20, 'delete_detallefactura'),
	(84, 'Can view detalle factura', 20, 'view_detallefactura'),
	(85, 'Can add servicio grooming', 22, 'add_serviciogrooming'),
	(86, 'Can change servicio grooming', 22, 'change_serviciogrooming'),
	(87, 'Can delete servicio grooming', 22, 'delete_serviciogrooming'),
	(88, 'Can view servicio grooming', 22, 'view_serviciogrooming'),
	(89, 'Can add Consulta Virtual', 23, 'add_consultavirtual'),
	(90, 'Can change Consulta Virtual', 23, 'change_consultavirtual'),
	(91, 'Can delete Consulta Virtual', 23, 'delete_consultavirtual'),
	(92, 'Can view Consulta Virtual', 23, 'view_consultavirtual'),
	(93, 'Can add Configuración', 24, 'add_configuracion'),
	(94, 'Can change Configuración', 24, 'change_configuracion'),
	(95, 'Can delete Configuración', 24, 'delete_configuracion'),
	(96, 'Can view Configuración', 24, 'view_configuracion');

-- Volcando estructura para tabla sistema_veterinaria_db.auth_user
CREATE TABLE IF NOT EXISTS `auth_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `password` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login` datetime(6) DEFAULT NULL,
  `is_superuser` tinyint(1) NOT NULL,
  `username` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_staff` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `date_joined` datetime(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.auth_user: ~3 rows (aproximadamente)
DELETE FROM `auth_user`;
INSERT INTO `auth_user` (`id`, `password`, `last_login`, `is_superuser`, `username`, `first_name`, `last_name`, `email`, `is_staff`, `is_active`, `date_joined`) VALUES
	(1, 'pbkdf2_sha256$1200000$lRqrVAWM0EQADk5ZWCUNc6$LX78a9JKBV6KIAHvJcHF8gW2X8XwLuPo1jUJi/zqDPs=', '2026-03-07 05:31:20.901096', 1, 'admin', '', '', 'admin@example.com', 1, 1, '2026-03-06 09:28:29.556283'),
	(2, 'pbkdf2_sha256$1200000$TvBiCVY5l0LjEBS3USw8HR$TQ+IEeTNFKEEm4FSYYvNSiZ5stv+FjTeJ678eYCvO54=', NULL, 0, 'dr.fernando', 'Fernando', 'Salas', 'fernando@vet.com', 1, 1, '2026-03-06 17:23:46.014535'),
	(3, 'pbkdf2_sha256$1200000$aW1EqmntjOkQ9KTVPhCW1P$PWGSGoHyaWke10dMlPLJ/BMVMyCHWhtGmgtMuDKOsU8=', NULL, 0, 'dra.carmen', 'Carmen', 'Rios', 'carmen@vet.com', 1, 1, '2026-03-06 17:23:49.505206');

-- Volcando estructura para tabla sistema_veterinaria_db.auth_user_groups
CREATE TABLE IF NOT EXISTS `auth_user_groups` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `group_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_user_groups_user_id_group_id_94350c0c_uniq` (`user_id`,`group_id`),
  KEY `auth_user_groups_group_id_97559544_fk_auth_group_id` (`group_id`),
  CONSTRAINT `auth_user_groups_group_id_97559544_fk_auth_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_group` (`id`),
  CONSTRAINT `auth_user_groups_user_id_6a12ed8b_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.auth_user_groups: ~0 rows (aproximadamente)
DELETE FROM `auth_user_groups`;

-- Volcando estructura para tabla sistema_veterinaria_db.auth_user_user_permissions
CREATE TABLE IF NOT EXISTS `auth_user_user_permissions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_user_user_permissions_user_id_permission_id_14a6b632_uniq` (`user_id`,`permission_id`),
  KEY `auth_user_user_permi_permission_id_1fbb5f2c_fk_auth_perm` (`permission_id`),
  CONSTRAINT `auth_user_user_permi_permission_id_1fbb5f2c_fk_auth_perm` FOREIGN KEY (`permission_id`) REFERENCES `auth_permission` (`id`),
  CONSTRAINT `auth_user_user_permissions_user_id_a95ead1b_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.auth_user_user_permissions: ~0 rows (aproximadamente)
DELETE FROM `auth_user_user_permissions`;

-- Volcando estructura para tabla sistema_veterinaria_db.clientes_cliente
CREATE TABLE IF NOT EXISTS `clientes_cliente` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` longtext COLLATE utf8mb4_unicode_ci,
  `fecha_registro` datetime(6) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni` (`dni`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.clientes_cliente: ~5 rows (aproximadamente)
DELETE FROM `clientes_cliente`;
INSERT INTO `clientes_cliente` (`id`, `nombres`, `apellidos`, `dni`, `telefono`, `email`, `direccion`, `fecha_registro`, `is_active`) VALUES
	(1, 'Ana María', 'López Vega', '45678912', '987654321', 'ana@email.com', 'Av. Principal 123', '2026-03-06 17:23:45.873747', 1),
	(2, 'Carlos', 'Ruiz Sosa', '78912345', '912345678', 'carlos@email.com', 'Calle Las Flores 45', '2026-03-06 17:23:45.920522', 1),
	(3, 'Elena', 'Torres Diaz', '12345678', '998877665', 'elena@email.com', 'Urb. Los Rosales Mz 2', '2026-03-06 17:23:45.926949', 1),
	(4, 'Jorge', 'Velasco', '65432198', '945612378', 'jorge@email.com', 'Av. Sol 567', '2026-03-06 17:23:45.932631', 1),
	(5, 'Patricia', 'Campos', '87654321', '934567812', 'patty@email.com', 'Residencial A-1', '2026-03-06 17:23:45.942916', 1),
	(6, 'VICTOR', 'RAMOS', '44444444', '90909090', 'victor.rs.datsoft@gmail.com', 'DIRECCION 1', '2026-03-07 04:38:08.704756', 1);

-- Volcando estructura para tabla sistema_veterinaria_db.clientes_mascota
CREATE TABLE IF NOT EXISTS `clientes_mascota` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `especie` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `raza` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexo` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `peso_actual` decimal(5,2) DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `microchip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` datetime(6) NOT NULL,
  `cliente_id` bigint NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `clientes_mascota_cliente_id_0959a759_fk_clientes_cliente_id` (`cliente_id`),
  CONSTRAINT `clientes_mascota_cliente_id_0959a759_fk_clientes_cliente_id` FOREIGN KEY (`cliente_id`) REFERENCES `clientes_cliente` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.clientes_mascota: ~6 rows (aproximadamente)
DELETE FROM `clientes_mascota`;
INSERT INTO `clientes_mascota` (`id`, `nombre`, `especie`, `raza`, `sexo`, `fecha_nacimiento`, `peso_actual`, `color`, `microchip`, `foto`, `fecha_registro`, `cliente_id`, `is_active`) VALUES
	(1, 'Luna', 'CANINO', 'Poodle', 'H', NULL, 4.50, 'Blanco', NULL, '', '2026-03-06 17:23:45.964404', 1, 1),
	(2, 'Michi', 'FELINO', 'Mestizo', 'M', NULL, 3.20, 'Naranja', NULL, '', '2026-03-06 17:23:45.978042', 1, 1),
	(3, 'Max', 'CANINO', 'Labrador', 'M', NULL, 25.00, 'Dorado', NULL, '', '2026-03-06 17:23:45.985265', 2, 1),
	(4, 'Bella', 'CANINO', 'Shih Tzu', 'H', NULL, 5.10, 'Blanco/Negro', NULL, '', '2026-03-06 17:23:45.993864', 3, 1),
	(5, 'Toby', 'CANINO', 'Beagle', 'M', NULL, 12.30, 'Tricolor', NULL, '', '2026-03-06 17:23:46.002245', 4, 1),
	(6, 'Simba', 'FELINO', 'Persa', 'M', NULL, 4.80, 'Gris', NULL, '', '2026-03-06 17:23:46.009071', 5, 1),
	(7, 'Doki', 'CANINO', 'pequines', 'M', '2025-01-01', 8.00, 'marron', NULL, 'mascotas/2026-03-06_23h45_32.png', '2026-03-07 04:45:42.617734', 6, 1);

-- Volcando estructura para tabla sistema_veterinaria_db.configuracion_configuracion
CREATE TABLE IF NOT EXISTS `configuracion_configuracion` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slogan` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `simbolo_moneda` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_moneda` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono2` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sitio_web` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_primario` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.configuracion_configuracion: ~0 rows (aproximadamente)
DELETE FROM `configuracion_configuracion`;
INSERT INTO `configuracion_configuracion` (`id`, `nombre_empresa`, `slogan`, `logo`, `simbolo_moneda`, `nombre_moneda`, `ruc`, `telefono`, `telefono2`, `email`, `sitio_web`, `direccion`, `ciudad`, `pais`, `color_primario`) VALUES
	(1, 'VetSystem', NULL, '', 'S/', 'Soles', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Perú', '#0bb8c8');

-- Volcando estructura para tabla sistema_veterinaria_db.core_clinica
CREATE TABLE IF NOT EXISTS `core_clinica` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_primario` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.core_clinica: ~0 rows (aproximadamente)
DELETE FROM `core_clinica`;

-- Volcando estructura para tabla sistema_veterinaria_db.django_admin_log
CREATE TABLE IF NOT EXISTS `django_admin_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `action_time` datetime(6) NOT NULL,
  `object_id` longtext COLLATE utf8mb4_unicode_ci,
  `object_repr` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_flag` smallint unsigned NOT NULL,
  `change_message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_type_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `django_admin_log_content_type_id_c4bce8eb_fk_django_co` (`content_type_id`),
  KEY `django_admin_log_user_id_c564eba6_fk_auth_user_id` (`user_id`),
  CONSTRAINT `django_admin_log_content_type_id_c4bce8eb_fk_django_co` FOREIGN KEY (`content_type_id`) REFERENCES `django_content_type` (`id`),
  CONSTRAINT `django_admin_log_user_id_c564eba6_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`),
  CONSTRAINT `django_admin_log_chk_1` CHECK ((`action_flag` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.django_admin_log: ~0 rows (aproximadamente)
DELETE FROM `django_admin_log`;

-- Volcando estructura para tabla sistema_veterinaria_db.django_content_type
CREATE TABLE IF NOT EXISTS `django_content_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `app_label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `django_content_type_app_label_model_76bd3d3b_uniq` (`app_label`,`model`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.django_content_type: ~23 rows (aproximadamente)
DELETE FROM `django_content_type`;
INSERT INTO `django_content_type` (`id`, `app_label`, `model`) VALUES
	(1, 'admin', 'logentry'),
	(10, 'agenda', 'cita'),
	(2, 'auth', 'group'),
	(3, 'auth', 'permission'),
	(4, 'auth', 'user'),
	(8, 'clientes', 'cliente'),
	(9, 'clientes', 'mascota'),
	(24, 'configuracion', 'configuracion'),
	(5, 'contenttypes', 'contenttype'),
	(7, 'core', 'clinica'),
	(19, 'facturacion', 'caja'),
	(20, 'facturacion', 'detallefactura'),
	(21, 'facturacion', 'factura'),
	(22, 'grooming', 'serviciogrooming'),
	(15, 'inventario', 'categoria'),
	(16, 'inventario', 'movimiento'),
	(17, 'inventario', 'producto'),
	(18, 'inventario', 'proveedor'),
	(11, 'medico', 'detallereceta'),
	(12, 'medico', 'historiaclinica'),
	(13, 'medico', 'receta'),
	(14, 'medico', 'vacuna'),
	(6, 'sessions', 'session'),
	(23, 'telemedicina', 'consultavirtual');

-- Volcando estructura para tabla sistema_veterinaria_db.django_migrations
CREATE TABLE IF NOT EXISTS `django_migrations` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `app` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applied` datetime(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.django_migrations: ~26 rows (aproximadamente)
DELETE FROM `django_migrations`;
INSERT INTO `django_migrations` (`id`, `app`, `name`, `applied`) VALUES
	(1, 'contenttypes', '0001_initial', '2026-03-06 09:28:05.483185'),
	(2, 'auth', '0001_initial', '2026-03-06 09:28:06.098633'),
	(3, 'admin', '0001_initial', '2026-03-06 09:28:06.222990'),
	(4, 'admin', '0002_logentry_remove_auto_add', '2026-03-06 09:28:06.232494'),
	(5, 'admin', '0003_logentry_add_action_flag_choices', '2026-03-06 09:28:06.241382'),
	(6, 'clientes', '0001_initial', '2026-03-06 09:28:06.325685'),
	(7, 'agenda', '0001_initial', '2026-03-06 09:28:06.457808'),
	(8, 'contenttypes', '0002_remove_content_type_name', '2026-03-06 09:28:06.532651'),
	(9, 'auth', '0002_alter_permission_name_max_length', '2026-03-06 09:28:06.599069'),
	(10, 'auth', '0003_alter_user_email_max_length', '2026-03-06 09:28:06.638250'),
	(11, 'auth', '0004_alter_user_username_opts', '2026-03-06 09:28:06.648020'),
	(12, 'auth', '0005_alter_user_last_login_null', '2026-03-06 09:28:06.700929'),
	(13, 'auth', '0006_require_contenttypes_0002', '2026-03-06 09:28:06.703794'),
	(14, 'auth', '0007_alter_validators_add_error_messages', '2026-03-06 09:28:06.712469'),
	(15, 'auth', '0008_alter_user_username_max_length', '2026-03-06 09:28:06.774255'),
	(16, 'auth', '0009_alter_user_last_name_max_length', '2026-03-06 09:28:06.840278'),
	(17, 'auth', '0010_alter_group_name_max_length', '2026-03-06 09:28:06.878697'),
	(18, 'auth', '0011_update_proxy_permissions', '2026-03-06 09:28:06.889882'),
	(19, 'auth', '0012_alter_user_first_name_max_length', '2026-03-06 09:28:06.960748'),
	(20, 'core', '0001_initial', '2026-03-06 09:28:06.978360'),
	(21, 'inventario', '0001_initial', '2026-03-06 09:28:07.234964'),
	(22, 'facturacion', '0001_initial', '2026-03-06 09:28:07.449193'),
	(23, 'grooming', '0001_initial', '2026-03-06 09:28:07.575574'),
	(24, 'medico', '0001_initial', '2026-03-06 09:28:08.217724'),
	(25, 'sessions', '0001_initial', '2026-03-06 09:28:08.250644'),
	(26, 'telemedicina', '0001_initial', '2026-03-07 01:08:51.908610'),
	(27, 'agenda', '0002_cita_is_active', '2026-03-07 01:29:28.680456'),
	(28, 'clientes', '0002_cliente_is_active_mascota_is_active', '2026-03-07 01:29:28.822415'),
	(29, 'grooming', '0002_serviciogrooming_is_active', '2026-03-07 01:29:28.906671'),
	(30, 'configuracion', '0001_initial', '2026-03-07 05:35:46.631350');

-- Volcando estructura para tabla sistema_veterinaria_db.django_session
CREATE TABLE IF NOT EXISTS `django_session` (
  `session_key` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expire_date` datetime(6) NOT NULL,
  PRIMARY KEY (`session_key`),
  KEY `django_session_expire_date_a5c62663` (`expire_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.django_session: ~1 rows (aproximadamente)
DELETE FROM `django_session`;
INSERT INTO `django_session` (`session_key`, `session_data`, `expire_date`) VALUES
	('lqpzbf8vp4y3s1i2bsszbw1c4hg0u7o1', '.eJxVjLsOwjAMAP_FM4qcpiQ2IzvfUDl2QguolfqYEP-OKnWA9e50b-hkW_tuW8rcDQYX8HD6ZVn0WcZd2EPG--R0Gtd5yG5P3GEXd5usvK5H-zfoZenhApWQNKgy0TlmK9SmhCFVS21CkqyBc1MZ1bx5YmlCRW2QY2SPEhk-X92tN1k:1vykG0:X4Y_pFhbm-bbsgbUSm_QVnIqjQaCfmTEt_VVzC-DHM0', '2026-03-21 05:31:20.906242');

-- Volcando estructura para tabla sistema_veterinaria_db.facturacion_caja
CREATE TABLE IF NOT EXISTS `facturacion_caja` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL,
  `total_ingresos` decimal(10,2) NOT NULL,
  `total_egresos` decimal(10,2) NOT NULL,
  `monto_final` decimal(10,2) NOT NULL,
  `cerrada` tinyint(1) NOT NULL,
  `fecha_cierre` datetime(6) DEFAULT NULL,
  `usuario_cierre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fecha` (`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.facturacion_caja: ~1 rows (aproximadamente)
DELETE FROM `facturacion_caja`;
INSERT INTO `facturacion_caja` (`id`, `fecha`, `monto_inicial`, `total_ingresos`, `total_egresos`, `monto_final`, `cerrada`, `fecha_cierre`, `usuario_cierre`) VALUES
	(1, '2026-03-06', 0.00, 1185.90, 0.00, 1185.90, 0, NULL, NULL),
	(3, '2026-03-07', 0.00, 0.00, 0.00, 0.00, 0, NULL, NULL);

-- Volcando estructura para tabla sistema_veterinaria_db.facturacion_detallefactura
CREATE TABLE IF NOT EXISTS `facturacion_detallefactura` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `producto_id` bigint DEFAULT NULL,
  `factura_id` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `facturacion_detallef_producto_id_bfc64d4a_fk_inventari` (`producto_id`),
  KEY `facturacion_detallef_factura_id_32a734c1_fk_facturaci` (`factura_id`),
  CONSTRAINT `facturacion_detallef_factura_id_32a734c1_fk_facturaci` FOREIGN KEY (`factura_id`) REFERENCES `facturacion_factura` (`id`),
  CONSTRAINT `facturacion_detallef_producto_id_bfc64d4a_fk_inventari` FOREIGN KEY (`producto_id`) REFERENCES `inventario_producto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.facturacion_detallefactura: ~9 rows (aproximadamente)
DELETE FROM `facturacion_detallefactura`;
INSERT INTO `facturacion_detallefactura` (`id`, `descripcion`, `cantidad`, `precio_unitario`, `subtotal`, `producto_id`, `factura_id`) VALUES
	(19, '', 1, 45.00, 45.00, 6, 11),
	(20, '', 1, 25.00, 25.00, 1, 11),
	(21, '', 1, 65.00, 65.00, 2, 12),
	(22, '', 1, 45.00, 45.00, 6, 13),
	(23, '', 1, 160.00, 160.00, 3, 13),
	(24, '', 1, 45.00, 45.00, 6, 14),
	(25, '', 1, 45.00, 45.00, 6, 15),
	(26, '', 1, 65.00, 65.00, 2, 15),
	(27, '', 1, 210.00, 210.00, 5, 15);

-- Volcando estructura para tabla sistema_veterinaria_db.facturacion_factura
CREATE TABLE IF NOT EXISTS `facturacion_factura` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fecha` datetime(6) NOT NULL,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cajero` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero` (`numero`),
  KEY `facturacion_factura_cliente_id_a467a777_fk_clientes_cliente_id` (`cliente_id`),
  CONSTRAINT `facturacion_factura_cliente_id_a467a777_fk_clientes_cliente_id` FOREIGN KEY (`cliente_id`) REFERENCES `clientes_cliente` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.facturacion_factura: ~5 rows (aproximadamente)
DELETE FROM `facturacion_factura`;
INSERT INTO `facturacion_factura` (`id`, `fecha`, `numero`, `subtotal`, `igv`, `total`, `metodo_pago`, `estado`, `cajero`, `cliente_id`) VALUES
	(11, '2026-03-06 17:25:06.078893', '000001', 70.00, 12.60, 82.60, 'EFECTIVO', 'PAGADA', NULL, 1),
	(12, '2026-03-06 17:25:06.091080', '000002', 65.00, 11.70, 76.70, 'TARJETA', 'PAGADA', NULL, 2),
	(13, '2026-03-06 17:25:06.096323', '000003', 205.00, 36.90, 241.90, 'TARJETA', 'PAGADA', NULL, 3),
	(14, '2026-03-06 17:25:06.104477', '000004', 45.00, 8.10, 53.10, 'EFECTIVO', 'PAGADA', NULL, 4),
	(15, '2026-03-06 17:25:06.109751', '000005', 290.00, 52.20, 342.20, 'TARJETA', 'PAGADA', NULL, 5);

-- Volcando estructura para tabla sistema_veterinaria_db.grooming_serviciogrooming
CREATE TABLE IF NOT EXISTS `grooming_serviciogrooming` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `bano` tinyint(1) NOT NULL,
  `corte_pelo` tinyint(1) NOT NULL,
  `corte_unas` tinyint(1) NOT NULL,
  `limpieza_oidos` tinyint(1) NOT NULL,
  `glandulas` tinyint(1) NOT NULL,
  `perfume` tinyint(1) NOT NULL,
  `observaciones` longtext COLLATE utf8mb4_unicode_ci,
  `fecha_inicio` datetime(6) DEFAULT NULL,
  `fecha_fin` datetime(6) DEFAULT NULL,
  `cita_id` bigint NOT NULL,
  `peluquero_id` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cita_id` (`cita_id`),
  KEY `grooming_serviciogrooming_peluquero_id_713d3f2c_fk_auth_user_id` (`peluquero_id`),
  CONSTRAINT `grooming_serviciogrooming_cita_id_adcf4f3d_fk_agenda_cita_id` FOREIGN KEY (`cita_id`) REFERENCES `agenda_cita` (`id`),
  CONSTRAINT `grooming_serviciogrooming_peluquero_id_713d3f2c_fk_auth_user_id` FOREIGN KEY (`peluquero_id`) REFERENCES `auth_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.grooming_serviciogrooming: ~0 rows (aproximadamente)
DELETE FROM `grooming_serviciogrooming`;
INSERT INTO `grooming_serviciogrooming` (`id`, `bano`, `corte_pelo`, `corte_unas`, `limpieza_oidos`, `glandulas`, `perfume`, `observaciones`, `fecha_inicio`, `fecha_fin`, `cita_id`, `peluquero_id`, `is_active`) VALUES
	(1, 1, 1, 1, 1, 0, 1, 'Mascota muy nerviosa, bozal en el corte de uñas.', NULL, NULL, 20, 3, 1);

-- Volcando estructura para tabla sistema_veterinaria_db.inventario_categoria
CREATE TABLE IF NOT EXISTS `inventario_categoria` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.inventario_categoria: ~3 rows (aproximadamente)
DELETE FROM `inventario_categoria`;
INSERT INTO `inventario_categoria` (`id`, `nombre`, `descripcion`) VALUES
	(1, 'Medicamentos', 'Inyectables y pastillas'),
	(2, 'Alimentos', 'Comida seca y húmeda'),
	(3, 'Accesorios', 'Correas, juguetes');

-- Volcando estructura para tabla sistema_veterinaria_db.inventario_movimiento
CREATE TABLE IF NOT EXISTS `inventario_movimiento` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `tipo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `fecha` datetime(6) NOT NULL,
  `motivo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comprobante` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `producto_id` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `inventario_movimient_producto_id_4356cb23_fk_inventari` (`producto_id`),
  CONSTRAINT `inventario_movimient_producto_id_4356cb23_fk_inventari` FOREIGN KEY (`producto_id`) REFERENCES `inventario_producto` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.inventario_movimiento: ~0 rows (aproximadamente)
DELETE FROM `inventario_movimiento`;

-- Volcando estructura para tabla sistema_veterinaria_db.inventario_producto
CREATE TABLE IF NOT EXISTS `inventario_producto` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` longtext COLLATE utf8mb4_unicode_ci,
  `precio_compra` decimal(8,2) NOT NULL,
  `precio_venta` decimal(8,2) NOT NULL,
  `stock_actual` int NOT NULL,
  `stock_minimo` int NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `categoria_id` bigint DEFAULT NULL,
  `proveedor_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `inventario_producto_categoria_id_7033fb47_fk_inventari` (`categoria_id`),
  KEY `inventario_producto_proveedor_id_2feee190_fk_inventari` (`proveedor_id`),
  CONSTRAINT `inventario_producto_categoria_id_7033fb47_fk_inventari` FOREIGN KEY (`categoria_id`) REFERENCES `inventario_categoria` (`id`),
  CONSTRAINT `inventario_producto_proveedor_id_2feee190_fk_inventari` FOREIGN KEY (`proveedor_id`) REFERENCES `inventario_proveedor` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.inventario_producto: ~7 rows (aproximadamente)
DELETE FROM `inventario_producto`;
INSERT INTO `inventario_producto` (`id`, `codigo`, `nombre`, `descripcion`, `precio_compra`, `precio_venta`, `stock_actual`, `stock_minimo`, `fecha_vencimiento`, `is_active`, `categoria_id`, `proveedor_id`) VALUES
	(1, 'MED-001', 'Antiparasitario Canino x1', NULL, 10.00, 25.00, 50, 10, NULL, 1, 1, 1),
	(2, 'MED-002', 'Vacuna Multiple', NULL, 30.00, 65.00, 12, 15, NULL, 1, 1, 1),
	(3, 'ALI-001', 'Dog Chow Adultos 15kg', NULL, 110.00, 160.00, 8, 5, NULL, 1, 2, 2),
	(4, 'ALI-002', 'Cat Chow Gatitos 3kg', NULL, 35.00, 50.00, 3, 5, NULL, 1, 2, 2),
	(5, 'ACC-001', 'Collar Antipulgas Seresto', NULL, 140.00, 210.00, 10, 3, NULL, 1, 3, 2),
	(6, 'SER-001', 'Consulta Medica General', NULL, 0.00, 45.00, 999, 0, NULL, 1, NULL, NULL),
	(7, 'SER-002', 'Bano y Corte M', NULL, 0.00, 60.00, 999, 0, NULL, 1, NULL, NULL);

-- Volcando estructura para tabla sistema_veterinaria_db.inventario_proveedor
CREATE TABLE IF NOT EXISTS `inventario_proveedor` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` longtext COLLATE utf8mb4_unicode_ci,
  `contacto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ruc` (`ruc`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.inventario_proveedor: ~2 rows (aproximadamente)
DELETE FROM `inventario_proveedor`;
INSERT INTO `inventario_proveedor` (`id`, `nombre`, `ruc`, `telefono`, `email`, `direccion`, `contacto`) VALUES
	(1, 'Distribuidora Vet', '20123456789', '01-555-1234', NULL, NULL, NULL),
	(2, 'PetSupply Peru', '20987654321', '01-666-4321', NULL, NULL, NULL);

-- Volcando estructura para tabla sistema_veterinaria_db.medico_detallereceta
CREATE TABLE IF NOT EXISTS `medico_detallereceta` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `medicamento` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosis` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frecuencia` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duracion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `receta_id` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `medico_detallereceta_receta_id_87b4dee9_fk_medico_receta_id` (`receta_id`),
  CONSTRAINT `medico_detallereceta_receta_id_87b4dee9_fk_medico_receta_id` FOREIGN KEY (`receta_id`) REFERENCES `medico_receta` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.medico_detallereceta: ~0 rows (aproximadamente)
DELETE FROM `medico_detallereceta`;

-- Volcando estructura para tabla sistema_veterinaria_db.medico_historiaclinica
CREATE TABLE IF NOT EXISTS `medico_historiaclinica` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fecha` datetime(6) NOT NULL,
  `motivo_consulta` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `anamnesis` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `temperatura` decimal(4,1) DEFAULT NULL,
  `frecuencia_cardiaca` int DEFAULT NULL,
  `frecuencia_respiratoria` int DEFAULT NULL,
  `mucosas` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tiempo_llenado_capilar` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diagnostico` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `tratamiento` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` longtext COLLATE utf8mb4_unicode_ci,
  `proxima_cita` date DEFAULT NULL,
  `cita_id` bigint DEFAULT NULL,
  `mascota_id` bigint NOT NULL,
  `veterinario_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cita_id` (`cita_id`),
  KEY `medico_historiaclini_mascota_id_51fe45ab_fk_clientes_` (`mascota_id`),
  KEY `medico_historiaclinica_veterinario_id_245dfd5d_fk_auth_user_id` (`veterinario_id`),
  CONSTRAINT `medico_historiaclini_mascota_id_51fe45ab_fk_clientes_` FOREIGN KEY (`mascota_id`) REFERENCES `clientes_mascota` (`id`),
  CONSTRAINT `medico_historiaclinica_cita_id_e420eb87_fk_agenda_cita_id` FOREIGN KEY (`cita_id`) REFERENCES `agenda_cita` (`id`),
  CONSTRAINT `medico_historiaclinica_veterinario_id_245dfd5d_fk_auth_user_id` FOREIGN KEY (`veterinario_id`) REFERENCES `auth_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.medico_historiaclinica: ~1 rows (aproximadamente)
DELETE FROM `medico_historiaclinica`;
INSERT INTO `medico_historiaclinica` (`id`, `fecha`, `motivo_consulta`, `anamnesis`, `peso`, `temperatura`, `frecuencia_cardiaca`, `frecuencia_respiratoria`, `mucosas`, `tiempo_llenado_capilar`, `diagnostico`, `tratamiento`, `observaciones`, `proxima_cita`, `cita_id`, `mascota_id`, `veterinario_id`) VALUES
	(2, '2026-03-06 17:25:06.168821', 'Problemas de piel', 'Rascado constante, enrojecimiento', NULL, NULL, NULL, NULL, NULL, NULL, 'Dermatitis alérgica a la picadura de pulga (DAPP)', 'Collar antipulgas, crema tópica antiinflamatoria.', NULL, NULL, 18, 4, 2),
	(3, '2026-03-07 05:00:19.323601', 'Chequeo médico', 'anmnesis 1', 7.00, NULL, NULL, NULL, NULL, NULL, 'diagnotico 1', 'tratamiento 1', '', NULL, 22, 7, 2);

-- Volcando estructura para tabla sistema_veterinaria_db.medico_receta
CREATE TABLE IF NOT EXISTS `medico_receta` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `indicaciones_generales` longtext COLLATE utf8mb4_unicode_ci,
  `fecha` datetime(6) NOT NULL,
  `historia_clinica_id` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `historia_clinica_id` (`historia_clinica_id`),
  CONSTRAINT `medico_receta_historia_clinica_id_f5b1fa06_fk_medico_hi` FOREIGN KEY (`historia_clinica_id`) REFERENCES `medico_historiaclinica` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.medico_receta: ~0 rows (aproximadamente)
DELETE FROM `medico_receta`;
INSERT INTO `medico_receta` (`id`, `indicaciones_generales`, `fecha`, `historia_clinica_id`) VALUES
	(1, NULL, '2026-03-07 05:00:19.329692', 3);

-- Volcando estructura para tabla sistema_veterinaria_db.medico_vacuna
CREATE TABLE IF NOT EXISTS `medico_vacuna` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nombre_vacuna` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lote` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_aplicacion` date NOT NULL,
  `fecha_proxima_dosis` date DEFAULT NULL,
  `observaciones` longtext COLLATE utf8mb4_unicode_ci,
  `mascota_id` bigint NOT NULL,
  `producto_id` bigint DEFAULT NULL,
  `veterinario_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medico_vacuna_mascota_id_f730ddde_fk_clientes_mascota_id` (`mascota_id`),
  KEY `medico_vacuna_producto_id_63da14c3_fk_inventario_producto_id` (`producto_id`),
  KEY `medico_vacuna_veterinario_id_69660832_fk_auth_user_id` (`veterinario_id`),
  CONSTRAINT `medico_vacuna_mascota_id_f730ddde_fk_clientes_mascota_id` FOREIGN KEY (`mascota_id`) REFERENCES `clientes_mascota` (`id`),
  CONSTRAINT `medico_vacuna_producto_id_63da14c3_fk_inventario_producto_id` FOREIGN KEY (`producto_id`) REFERENCES `inventario_producto` (`id`),
  CONSTRAINT `medico_vacuna_veterinario_id_69660832_fk_auth_user_id` FOREIGN KEY (`veterinario_id`) REFERENCES `auth_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.medico_vacuna: ~0 rows (aproximadamente)
DELETE FROM `medico_vacuna`;

-- Volcando estructura para tabla sistema_veterinaria_db.telemedicina_consultavirtual
CREATE TABLE IF NOT EXISTS `telemedicina_consultavirtual` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `plataforma` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enlace_reunion` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_acceso` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_conexion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notas_preliminares` longtext COLLATE utf8mb4_unicode_ci,
  `cita_id` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cita_id` (`cita_id`),
  CONSTRAINT `telemedicina_consultavirtual_cita_id_46cddf6e_fk_agenda_cita_id` FOREIGN KEY (`cita_id`) REFERENCES `agenda_cita` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sistema_veterinaria_db.telemedicina_consultavirtual: ~0 rows (aproximadamente)
DELETE FROM `telemedicina_consultavirtual`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
