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


-- Volcando estructura de base de datos para salon_belleza_db
CREATE DATABASE IF NOT EXISTS `salon_belleza_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `salon_belleza_db`;

-- Volcando estructura para tabla salon_belleza_db.citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `servicio_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `estado` enum('pendiente','confirmada','completada','cancelada') DEFAULT 'pendiente',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `servicio_id` (`servicio_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.citas: ~11 rows (aproximadamente)
DELETE FROM `citas`;
INSERT INTO `citas` (`id`, `cliente_id`, `servicio_id`, `usuario_id`, `fecha_hora`, `estado`, `creado_en`) VALUES
	(1, 1, 1, 1, '2026-12-10 10:00:00', 'pendiente', '2026-03-07 06:49:35'),
	(2, 1, 1, 2, '2026-02-28 15:39:16', 'completada', '2026-03-07 15:39:16'),
	(3, 2, 2, 3, '2026-03-04 15:39:16', 'completada', '2026-03-07 15:39:16'),
	(4, 3, 3, 4, '2026-02-21 15:39:16', 'completada', '2026-03-07 15:39:16'),
	(5, 4, 4, 5, '2026-02-21 15:39:16', 'completada', '2026-03-07 15:39:16'),
	(6, 5, 5, 6, '2026-03-13 15:39:16', 'confirmada', '2026-03-07 15:39:16'),
	(7, 6, 6, 2, '2026-03-14 15:39:16', 'confirmada', '2026-03-07 15:39:16'),
	(8, 7, 7, 3, '2026-03-16 15:39:16', 'pendiente', '2026-03-07 15:39:16'),
	(9, 8, 8, 4, '2026-03-09 15:39:16', 'pendiente', '2026-03-07 15:39:16'),
	(10, 9, 9, 5, '2026-03-07 15:39:16', 'cancelada', '2026-03-07 15:39:16'),
	(11, 10, 10, 6, '2026-02-25 15:39:16', 'cancelada', '2026-03-07 15:39:16');

-- Volcando estructura para tabla salon_belleza_db.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `whatsapp_apikey` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.clientes: ~11 rows (aproximadamente)
DELETE FROM `clientes`;
INSERT INTO `clientes` (`id`, `nombre`, `telefono`, `email`, `creado_en`, `whatsapp_apikey`) VALUES
	(1, 'Juan Perez', '123456789', 'juan@test.com', '2026-03-07 06:40:09', NULL),
	(2, 'Valentina Rosas', '555-0001', 'val.rosas@mail.com', '2026-03-07 15:39:16', NULL),
	(3, 'Andrés Hurtado', '555-0002', 'ahurtado@mail.com', '2026-03-07 15:39:16', NULL),
	(4, 'Camila Cabello', '555-0003', 'camila@mail.com', '2026-03-07 15:39:16', NULL),
	(5, 'Diego Forlán', '555-0004', 'diego@mail.com', '2026-03-07 15:39:16', NULL),
	(6, 'Elena Gómez', '555-0005', 'elena.g@mail.com', '2026-03-07 15:39:16', NULL),
	(7, 'Felipe Reyes', '555-0006', 'felipe@mail.com', '2026-03-07 15:39:16', NULL),
	(8, 'Gabriela Montes', '555-0007', 'gaby@mail.com', '2026-03-07 15:39:16', NULL),
	(9, 'Héctor Lavoe', '555-0008', 'hector@mail.com', '2026-03-07 15:39:16', NULL),
	(10, 'Inés Arrimadas', '555-0009', 'ines@mail.com', '2026-03-07 15:39:16', NULL),
	(11, 'Javier Solís', '555-0010', 'javier@mail.com', '2026-03-07 15:39:16', NULL);

-- Volcando estructura para tabla salon_belleza_db.cliente_suscripciones
CREATE TABLE IF NOT EXISTS `cliente_suscripciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `plan_id` int NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('activa','vencida','cancelada') DEFAULT 'activa',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `plan_id` (`plan_id`),
  CONSTRAINT `cliente_suscripciones_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cliente_suscripciones_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `suscripcion_planes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.cliente_suscripciones: ~0 rows (aproximadamente)
DELETE FROM `cliente_suscripciones`;

-- Volcando estructura para tabla salon_belleza_db.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` int NOT NULL DEFAULT '1',
  `nombre_empresa` varchar(150) NOT NULL DEFAULT 'Belleza Admin',
  `logo_url` varchar(255) DEFAULT NULL,
  `simbolo_moneda` varchar(10) NOT NULL DEFAULT '$',
  `telefono` varchar(50) DEFAULT '',
  `direccion` text,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `configuracion_chk_1` CHECK ((`id` = 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.configuracion: ~0 rows (aproximadamente)
DELETE FROM `configuracion`;
INSERT INTO `configuracion` (`id`, `nombre_empresa`, `logo_url`, `simbolo_moneda`, `telefono`, `direccion`, `creado_en`, `actualizado_en`) VALUES
	(1, 'Salón de Belleza Elegance', '/uploads/logo_empresa_1773633729822.png', 'S/', '+1 234 567 8900', '123 Beauty St, Fashion City', '2026-03-07 15:43:18', '2026-03-20 06:16:23');

-- Volcando estructura para tabla salon_belleza_db.configuracion_notificaciones
CREATE TABLE IF NOT EXISTS `configuracion_notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `notificar_nueva_cita` tinyint(1) DEFAULT '1',
  `notificar_cancelacion` tinyint(1) DEFAULT '1',
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `plantilla_nueva_cita` text,
  `plantilla_cancelacion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.configuracion_notificaciones: ~0 rows (aproximadamente)
DELETE FROM `configuracion_notificaciones`;
INSERT INTO `configuracion_notificaciones` (`id`, `notificar_nueva_cita`, `notificar_cancelacion`, `actualizado_en`, `plantilla_nueva_cita`, `plantilla_cancelacion`) VALUES
	(1, 1, 1, '2026-03-20 06:11:27', 'Hola [CLIENTE], tu cita para *[SERVICIO]* ha sido confirmada para el *[FECHA]*. ¡Te esperamos!', 'Hola [CLIENTE], te informamos que tu cita para *[SERVICIO]* del *[FECHA]* ha sido cancelada. Contáctanos para reprogramar.');

-- Volcando estructura para tabla salon_belleza_db.galeria_clientes
CREATE TABLE IF NOT EXISTS `galeria_clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `cita_id` int DEFAULT NULL,
  `url_foto` varchar(255) NOT NULL,
  `tipo` enum('antes','despues','general') DEFAULT 'general',
  `descripcion` text,
  `fecha_subida` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `cita_id` (`cita_id`),
  CONSTRAINT `galeria_clientes_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `galeria_clientes_ibfk_2` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.galeria_clientes: ~0 rows (aproximadamente)
DELETE FROM `galeria_clientes`;

-- Volcando estructura para tabla salon_belleza_db.gastos
CREATE TABLE IF NOT EXISTS `gastos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `concepto` varchar(255) NOT NULL,
  `descripcion` text,
  `monto` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `categoria` enum('servicios','insumos','nomina','mantenimiento','otros') DEFAULT 'otros',
  `usuario_id` int DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.gastos: ~10 rows (aproximadamente)
DELETE FROM `gastos`;
INSERT INTO `gastos` (`id`, `concepto`, `descripcion`, `monto`, `fecha`, `categoria`, `usuario_id`, `creado_en`) VALUES
	(1, 'Pago Alquiler Local', 'Alquiler mes actual', 500.00, '2026-02-27', 'mantenimiento', 1, '2026-03-07 15:39:16'),
	(2, 'Recibo de Luz', 'Servicio eléctrico', 85.50, '2026-02-19', 'servicios', 1, '2026-03-07 15:39:16'),
	(3, 'Recibo de Agua', 'Servicio de agua', 35.00, '2026-02-24', 'servicios', 1, '2026-03-07 15:39:16'),
	(4, 'Compra Insumos L´Oreal', 'Tintes y shampoos', 320.00, '2026-03-02', 'insumos', 1, '2026-03-07 15:39:16'),
	(5, 'Pago Quincena Sofía', 'Recepción', 250.00, '2026-02-24', 'nomina', 1, '2026-03-07 15:39:16'),
	(6, 'Mantenimiento A/C', 'Limpieza filtros', 60.00, '2026-02-25', 'mantenimiento', 1, '2026-03-07 15:39:16'),
	(7, 'Compra Café e Insumos', 'Snacks para clientes', 45.00, '2026-03-06', 'otros', 1, '2026-03-07 15:39:16'),
	(8, 'Publicidad Facebook', 'Campaña mensual', 100.00, '2026-02-23', 'otros', 1, '2026-03-07 15:39:16'),
	(9, 'Material de Limpieza', 'Cloro, escobas', 38.00, '2026-02-26', 'insumos', 1, '2026-03-07 15:39:16'),
	(10, 'Pago Internet', 'Servicio fibra óptica', 40.00, '2026-02-20', 'servicios', 1, '2026-03-07 15:39:16');

-- Volcando estructura para tabla salon_belleza_db.mantenimiento_fisico
CREATE TABLE IF NOT EXISTS `mantenimiento_fisico` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipo` varchar(255) NOT NULL,
  `descripcion` text,
  `fecha_mantenimiento` date NOT NULL,
  `proxima_fecha` date DEFAULT NULL,
  `costo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('Pendiente','En Proceso','Completado') DEFAULT 'Pendiente',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.mantenimiento_fisico: ~0 rows (aproximadamente)
DELETE FROM `mantenimiento_fisico`;

-- Volcando estructura para tabla salon_belleza_db.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cita_id` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta','transferencia','suscripcion') DEFAULT 'efectivo',
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cita_id` (`cita_id`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.pagos: ~0 rows (aproximadamente)
DELETE FROM `pagos`;

-- Volcando estructura para tabla salon_belleza_db.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `stock` int DEFAULT '0',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.productos: ~10 rows (aproximadamente)
DELETE FROM `productos`;
INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `creado_en`) VALUES
	(1, 'Shampoo Matizador 500ml', 'Neutraliza tonos amarillos', 15.00, 20, '2026-03-07 15:39:16'),
	(2, 'Acondicionador Hidratante', 'Para cabello seco', 14.00, 15, '2026-03-07 15:39:16'),
	(3, 'Mascarilla Reparadora', 'Tratamiento profundo semanal', 25.00, 10, '2026-03-07 15:39:16'),
	(4, 'Aceite de Argán', 'Sérum para puntas', 18.00, 30, '2026-03-07 15:39:16'),
	(5, 'Laca Fijación Fuerte', 'Para peinados', 10.00, 25, '2026-03-07 15:39:16'),
	(6, 'Tinte Castaño Claro', 'Tinte permanente', 8.50, 50, '2026-03-07 15:39:16'),
	(7, 'Esmalte Rojo Clásico', 'Esmalte tradicional', 5.00, 40, '2026-03-07 15:39:16'),
	(8, 'Decolorante Profesional', 'Polvo decolorante 500g', 22.00, 12, '2026-03-07 15:39:16'),
	(9, 'Cera Moldeadora', 'Fijación media para hombres', 11.00, 20, '2026-03-07 15:39:16'),
	(10, 'Protector Térmico', 'Spray para plancha y secadora', 16.00, 18, '2026-03-07 15:39:16');

-- Volcando estructura para tabla salon_belleza_db.servicios
CREATE TABLE IF NOT EXISTS `servicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `duracion_minutos` int NOT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.servicios: ~12 rows (aproximadamente)
DELETE FROM `servicios`;
INSERT INTO `servicios` (`id`, `nombre`, `descripcion`, `precio`, `duracion_minutos`, `creado_en`) VALUES
	(1, 'Corte Hombre', 'Corte clasico', 15.00, 30, '2026-03-07 06:44:08'),
	(2, 'Corte de Cabello Mujer', 'Corte moderno con lavado', 25.00, 45, '2026-03-07 15:39:16'),
	(3, 'Corte de Cabello Hombre', 'Corte a máquina y tijera', 15.00, 30, '2026-03-07 15:39:16'),
	(4, 'Tinte Completo', 'Aplicación de tinte en todo el cabello', 60.00, 120, '2026-03-07 15:39:16'),
	(5, 'Mechas Balayage', 'Técnica de decoloración', 80.00, 180, '2026-03-07 15:39:16'),
	(6, 'Manicura Tradicional', 'Limpieza y esmalte tradicional', 12.00, 40, '2026-03-07 15:39:16'),
	(7, 'Pedicura Spa', 'Exfoliación, masaje y esmalte', 20.00, 60, '2026-03-07 15:39:16'),
	(8, 'Uñas Acrílicas', 'Set completo de acrílico', 35.00, 90, '2026-03-07 15:39:16'),
	(9, 'Maquillaje Profesional', 'Maquillaje de noche o evento', 50.00, 60, '2026-03-07 15:39:16'),
	(10, 'Peinado de Gala', 'Recogidos y semi-recogidos', 40.00, 60, '2026-03-07 15:39:16'),
	(11, 'Tratamiento Capilar', 'Hidratación profunda', 30.00, 45, '2026-03-07 15:39:16'),
	(12, 'Servicio 1', 'servicio de prueba', 70.00, 40, '2026-03-20 06:46:21');

-- Volcando estructura para tabla salon_belleza_db.suscripcion_planes
CREATE TABLE IF NOT EXISTS `suscripcion_planes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `duracion_dias` int NOT NULL,
  `servicios_incluidos` int DEFAULT '0',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.suscripcion_planes: ~0 rows (aproximadamente)
DELETE FROM `suscripcion_planes`;

-- Volcando estructura para tabla salon_belleza_db.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','recepcionista','estilista') DEFAULT 'recepcionista',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.usuarios: ~10 rows (aproximadamente)
DELETE FROM `usuarios`;
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `creado_en`) VALUES
	(1, 'Administrador', 'admin@salon.com', '$2b$10$fZ/4SGvorPvq.UkXFavqwenIP/WctrjUuavD4myO0DmmKhXXeh7d2', 'admin', '2026-03-07 06:20:24'),
	(2, 'María Gómez', 'maria@salon.com', '$2b$10$.bEKqjm8DfqofmWoShtqPOepZJUS9G7jPyv5KTiXGpmbCFz3qfiKW', 'estilista', '2026-03-07 15:39:16'),
	(3, 'Pedro Pérez', 'pedro@salon.com', '$2b$10$.bEKqjm8DfqofmWoShtqPOepZJUS9G7jPyv5KTiXGpmbCFz3qfiKW', 'estilista', '2026-03-07 15:39:16'),
	(4, 'Ana Torres', 'ana@salon.com', '$2b$10$.bEKqjm8DfqofmWoShtqPOepZJUS9G7jPyv5KTiXGpmbCFz3qfiKW', 'estilista', '2026-03-07 15:39:16'),
	(5, 'Luis Sánchez', 'luis@salon.com', '$2b$10$.bEKqjm8DfqofmWoShtqPOepZJUS9G7jPyv5KTiXGpmbCFz3qfiKW', 'estilista', '2026-03-07 15:39:16'),
	(6, 'Laura Diaz', 'laura@salon.com', '$2b$10$.bEKqjm8DfqofmWoShtqPOepZJUS9G7jPyv5KTiXGpmbCFz3qfiKW', 'estilista', '2026-03-07 15:39:16'),
	(7, 'Carlos Ruiz', 'carlos@salon.com', '$2b$10$.bEKqjm8DfqofmWoShtqPOepZJUS9G7jPyv5KTiXGpmbCFz3qfiKW', 'recepcionista', '2026-03-07 15:39:16'),
	(8, 'Sofía López', 'sofia@salon.com', '$2b$10$.bEKqjm8DfqofmWoShtqPOepZJUS9G7jPyv5KTiXGpmbCFz3qfiKW', 'recepcionista', '2026-03-07 15:39:16'),
	(9, 'Elena Castro', 'elena@salon.com', '$2b$10$.bEKqjm8DfqofmWoShtqPOepZJUS9G7jPyv5KTiXGpmbCFz3qfiKW', 'recepcionista', '2026-03-07 15:39:16'),
	(10, 'Jorge Mendieta', 'jorge@salon.com', '$2b$10$.bEKqjm8DfqofmWoShtqPOepZJUS9G7jPyv5KTiXGpmbCFz3qfiKW', 'recepcionista', '2026-03-07 15:39:16');

-- Volcando estructura para tabla salon_belleza_db.ventas
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cita_id` int DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta','transferencia') DEFAULT 'efectivo',
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cita_id` (`cita_id`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla salon_belleza_db.ventas: ~10 rows (aproximadamente)
DELETE FROM `ventas`;
INSERT INTO `ventas` (`id`, `cita_id`, `total`, `metodo_pago`, `fecha`) VALUES
	(1, 2, 15.00, 'efectivo', '2026-03-07 15:39:16'),
	(2, 3, 25.00, 'tarjeta', '2026-03-07 15:39:16'),
	(3, 4, 15.00, 'transferencia', '2026-03-07 15:39:16'),
	(4, 5, 60.00, 'efectivo', '2026-03-07 15:39:16'),
	(5, NULL, 60.00, 'efectivo', '2026-03-07 15:39:16'),
	(6, NULL, 52.00, 'tarjeta', '2026-03-07 15:39:16'),
	(7, NULL, 57.00, 'transferencia', '2026-03-07 15:39:16'),
	(8, NULL, 28.00, 'efectivo', '2026-03-07 15:39:16'),
	(9, NULL, 34.00, 'tarjeta', '2026-03-07 15:39:16'),
	(10, NULL, 48.00, 'efectivo', '2026-03-07 15:39:16');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
