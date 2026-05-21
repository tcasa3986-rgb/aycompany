-- =============================================================
-- CRM CONDOMINIO — DATOS DE PRUEBA (SEED)
-- =============================================================

USE condominio_crm;

-- Condominio
INSERT INTO condominios (nombre, direccion, rfc, telefono, email) VALUES
('Residencial Las Palmas', 'Av. Insurgentes Sur 1234, Col. Del Valle, CDMX', 'COND850101ABC', '55-1234-5678', 'admin@laspalmas.com');

-- Roles
INSERT INTO roles (nombre, descripcion) VALUES
('super_admin', 'Acceso total al sistema'),
('administrador', 'Gestión completa del condominio'),
('contador', 'Módulos de finanzas y reportes'),
('residente', 'Portal de residente'),
('guardia', 'Control de acceso y seguridad'),
('mantenimiento', 'Órdenes de trabajo');

-- Torres
INSERT INTO torres (condominio_id, nombre, total_pisos) VALUES
(1, 'Torre A', 10),
(1, 'Torre B', 10),
(1, 'Torre C', 8);

-- Unidades
INSERT INTO unidades (torre_id, numero, piso, tipo, metros_cuadrados, estado) VALUES
(1, 'A-101', 1, 'departamento', 85.00, 'habitada'),
(1, 'A-102', 1, 'departamento', 90.00, 'habitada'),
(1, 'A-201', 2, 'departamento', 85.00, 'habitada'),
(1, 'A-202', 2, 'departamento', 90.00, 'vacía'),
(1, 'A-301', 3, 'departamento', 120.00, 'habitada'),
(2, 'B-101', 1, 'departamento', 75.00, 'habitada'),
(2, 'B-102', 1, 'departamento', 75.00, 'en_renta'),
(2, 'B-201', 2, 'departamento', 95.00, 'habitada'),
(3, 'C-101', 1, 'departamento', 110.00, 'habitada'),
(3, 'C-102', 1, 'departamento', 110.00, 'en_venta');

-- Usuarios (password: Admin123! — hash bcrypt)
INSERT INTO usuarios (nombre, apellidos, email, password_hash, telefono, rol_id, unidad_id) VALUES
('Carlos', 'Mendoza López', 'admin@laspalmas.com', '$2b$10$XZv7iXnVPYp.h7G5TZAU1.wY9y9O1OY5N7Q6JTxNr5p3O4VuFAdYq', '55-1111-2222', 2, NULL),
('Ana', 'García Ramos', 'ana.garcia@email.com', '$2b$10$XZv7iXnVPYp.h7G5TZAU1.wY9y9O1OY5N7Q6JTxNr5p3O4VuFAdYq', '55-3333-4444', 4, 1),
('Roberto', 'Flores Díaz', 'roberto.flores@email.com', '$2b$10$XZv7iXnVPYp.h7G5TZAU1.wY9y9O1OY5N7Q6JTxNr5p3O4VuFAdYq', '55-5555-6666', 4, 3),
('María', 'Torres Vega', 'maria.torres@email.com', '$2b$10$XZv7iXnVPYp.h7G5TZAU1.wY9y9O1OY5N7Q6JTxNr5p3O4VuFAdYq', '55-7777-8888', 4, 5),
('Juan', 'Ramírez Cruz', 'juan.guardia@laspalmas.com', '$2b$10$XZv7iXnVPYp.h7G5TZAU1.wY9y9O1OY5N7Q6JTxNr5p3O4VuFAdYq', '55-9999-0000', 5, NULL),
('Laura', 'Sánchez Mora', 'laura.contadora@laspalmas.com', '$2b$10$XZv7iXnVPYp.h7G5TZAU1.wY9y9O1OY5N7Q6JTxNr5p3O4VuFAdYq', '55-1212-3434', 3, NULL);

-- Residentes
INSERT INTO residentes (unidad_id, usuario_id, nombre, apellidos, tipo, documento_id, email, telefono, fecha_ingreso) VALUES
(1, 2, 'Ana', 'García Ramos', 'propietario', 'GARA850101', 'ana.garcia@email.com', '55-3333-4444', '2020-01-15'),
(2, NULL, 'Pedro', 'Martínez Silva', 'propietario', 'MASP780505', 'pedro.martinez@email.com', '55-2222-1111', '2019-06-01'),
(3, 3, 'Roberto', 'Flores Díaz', 'propietario', 'FLDR750220', 'roberto.flores@email.com', '55-5555-6666', '2021-03-10'),
(5, 4, 'María', 'Torres Vega', 'inquilino', 'TORM900315', 'maria.torres@email.com', '55-7777-8888', '2023-01-01'),
(6, NULL, 'Jorge', 'López Hernández', 'propietario', 'LOHJ820810', 'jorge.lopez@email.com', '55-4444-5555', '2018-09-20'),
(8, NULL, 'Carmen', 'Vázquez Ruiz', 'propietario', 'VARC891201', 'carmen.vazquez@email.com', '55-6666-7777', '2022-07-15'),
(9, NULL, 'Diego', 'Morales Peña', 'propietario', 'MOPD950401', 'diego.morales@email.com', '55-8888-9999', '2023-05-01');

-- Vehículos
INSERT INTO vehiculos (unidad_id, residente_id, marca, modelo, anio, color, placas) VALUES
(1, 1, 'Toyota', 'Corolla', 2021, 'Blanco', 'ABC-123'),
(1, 1, 'Nissan', 'Pathfinder', 2019, 'Negro', 'XYZ-789'),
(2, 2, 'Chevrolet', 'Trax', 2022, 'Rojo', 'DEF-456'),
(3, 3, 'Honda', 'CRV', 2020, 'Gris', 'GHI-321'),
(5, 4, 'Kia', 'Sportage', 2023, 'Azul', 'JKL-654');

-- Tipos de cuota
INSERT INTO tipos_cuota (nombre, descripcion, monto_base, periodicidad, tasa_mora, dias_gracia) VALUES
('Cuota Ordinaria', 'Cuota mensual de mantenimiento', 2500.00, 'mensual', 0.05, 5),
('Cuota Extraordinaria', 'Reparación de elevadores', 1500.00, 'única', 0.00, 30),
('Salón de Eventos', 'Renta salón de usos múltiples', 800.00, 'única', 0.00, 0);

-- Cuotas (últimos 3 meses)
INSERT INTO cuotas (unidad_id, tipo_cuota_id, monto, fecha_emision, fecha_vencimiento, estado, referencia) VALUES
(1, 1, 2500.00, '2026-02-01', '2026-02-10', 'pagado', 'ENE26-A101'),
(2, 1, 2500.00, '2026-02-01', '2026-02-10', 'pagado', 'ENE26-A102'),
(3, 1, 2500.00, '2026-02-01', '2026-02-10', 'pagado', 'ENE26-A201'),
(5, 1, 2500.00, '2026-02-01', '2026-02-10', 'vencido', 'ENE26-A301'),
(6, 1, 2500.00, '2026-02-01', '2026-02-10', 'pagado', 'ENE26-B101'),
(1, 1, 2500.00, '2026-03-01', '2026-03-10', 'pagado', 'FEB26-A101'),
(2, 1, 2500.00, '2026-03-01', '2026-03-10', 'pagado', 'FEB26-A102'),
(3, 1, 2500.00, '2026-03-01', '2026-03-10', 'vencido', 'FEB26-A201'),
(5, 1, 2500.00, '2026-03-01', '2026-03-10', 'vencido', 'FEB26-A301'),
(6, 1, 2500.00, '2026-03-01', '2026-03-10', 'pagado', 'FEB26-B101'),
(1, 1, 2500.00, '2026-04-01', '2026-04-10', 'pagado', 'MAR26-A101'),
(2, 1, 2500.00, '2026-04-01', '2026-04-10', 'pendiente', 'MAR26-A102'),
(3, 1, 2500.00, '2026-04-01', '2026-04-10', 'pendiente', 'MAR26-A201'),
(5, 1, 2500.00, '2026-04-01', '2026-04-10', 'vencido', 'MAR26-A301'),
(6, 1, 2500.00, '2026-04-01', '2026-04-10', 'pagado', 'MAR26-B101');

-- Pagos
INSERT INTO pagos (cuota_id, unidad_id, monto_pagado, fecha_pago, metodo, referencia_pago, registrado_por) VALUES
(1, 1, 2500.00, '2026-02-05 10:30:00', 'transferencia', 'TRF-001', 1),
(2, 2, 2500.00, '2026-02-08 14:00:00', 'efectivo', NULL, 1),
(3, 3, 2500.00, '2026-02-09 11:00:00', 'transferencia', 'TRF-002', 1),
(6, 1, 2500.00, '2026-03-04 09:15:00', 'app', 'APP-001', 1),
(7, 2, 2500.00, '2026-03-07 16:30:00', 'transferencia', 'TRF-003', 1),
(11, 1, 2500.00, '2026-04-03 10:00:00', 'transferencia', 'TRF-004', 1),
(5, 6, 2500.00, '2026-02-06 13:00:00', 'efectivo', NULL, 1),
(10, 6, 2500.00, '2026-03-05 12:00:00', 'efectivo', NULL, 1),
(15, 6, 2500.00, '2026-04-02 11:30:00', 'efectivo', NULL, 1);

-- Áreas comunes
INSERT INTO areas_comunes (nombre, tipo, descripcion) VALUES
('Alberca', 'Recreativa', 'Piscina olímpica climatizada'),
('Salón de Eventos', 'Social', 'Salón de usos múltiples cap. 100 personas'),
('Gimnasio', 'Deportiva', 'Equipped gym 24/7'),
('Cancha de Tenis', 'Deportiva', 'Cancha de tenis con iluminación'),
('Jardín Central', 'Verde', 'Jardín con área de juegos infantiles'),
('Lobby / Recepción', 'Acceso', 'Área de recepción principal');

-- Proveedores
INSERT INTO proveedores (nombre, rfc, tipo_servicio, contacto_nombre, contacto_telefono, contacto_email) VALUES
('Servicios Integrales SA', 'SINM800101ABC', 'Mantenimiento General', 'Miguel Ángel Ruiz', '55-1234-9876', 'miguel@serviciosint.com'),
('Vigilancia Pro SC', 'VPSC901115XYZ', 'Vigilancia y Seguridad', 'Patricia Leal', '55-9876-5432', 'patricia@vigilanciapro.com'),
('Verde Jardines', 'VEJM851020DEF', 'Jardinería', 'Ernesto Montes', '55-4567-8901', 'ernesto@verdejardines.com'),
('Elevadores Modernos', 'ELMO780612GHI', 'Mantenimiento Elevadores', 'Samuel Torres', '55-2345-6789', 'samuel@elevmod.com'),
('Limpieza Express', 'LIEX950308JKL', 'Limpieza y sanitización', 'Sandra Pérez', '55-3456-7890', 'sandra@limpiezaex.com');

-- Amenidades
INSERT INTO amenidades (nombre, descripcion, capacidad_max, tiene_costo, costo, horario_inicio, horario_fin, limite_reservas_mes) VALUES
('Salón de Eventos', 'Salón para fiestas y reuniones', 100, 1, 800.00, '09:00:00', '22:00:00', 2),
('Alberca', 'Alberca olímpica climatizada', 30, 0, 0, '07:00:00', '20:00:00', 8),
('Gimnasio', 'Gym completamente equipado', 15, 0, 0, '06:00:00', '22:00:00', 20),
('Cancha de Tenis', 'Cancha profesional', 4, 0, 0, '07:00:00', '21:00:00', 6),
('Asador / BBQ', 'Área de asadores techada', 20, 1, 300.00, '10:00:00', '20:00:00', 4);

-- Reservaciones
INSERT INTO reservaciones (amenidad_id, unidad_id, residente_id, fecha, hora_inicio, hora_fin, num_personas, estado, costo_cobrado) VALUES
(1, 1, 1, '2026-04-20', '14:00:00', '22:00:00', 50, 'confirmada', 800.00),
(2, 3, 3, '2026-04-19', '09:00:00', '11:00:00', 4, 'confirmada', 0),
(3, 6, 6, '2026-04-19', '07:00:00', '08:00:00', 2, 'completada', 0),
(5, 2, 2, '2026-04-25', '12:00:00', '17:00:00', 15, 'confirmada', 300.00);

-- Visitantes
INSERT INTO visitantes (nombre, documento_id, unidad_id, residente_id, motivo, tipo, entrada, salida, guardia_id) VALUES
('Luis Fernández', 'FELT900101', 1, 1, 'Visita familiar', 'visita', '2026-04-18 10:00:00', '2026-04-18 13:00:00', 5),
('Plomería González', 'GOMA750501', 3, 3, 'Reparación de fuga', 'proveedor', '2026-04-18 09:00:00', '2026-04-18 11:30:00', 5),
('Amazon Delivery', NULL, 5, 4, 'Entrega de paquete', 'delivery', '2026-04-18 08:45:00', '2026-04-18 08:50:00', 5),
('Sofía Gutiérrez', 'GUTS950615', 6, 6, 'Visita amiga', 'visita', '2026-04-18 15:00:00', NULL, 5);

-- Incidentes
INSERT INTO incidentes (tipo, descripcion, ubicacion, nivel, estado, reportado_por) VALUES
('Ruido', 'Fiesta excesiva después de las 11pm en departamento B-102', 'Torre B Piso 1', 'medio', 'cerrado', 5),
('Vandalismo', 'Grafiti en puerta del estacionamiento', 'Estacionamiento nivel -1', 'medio', 'cerrado', 5),
('Robo', 'Reporte de intento de robo a vehículo en estacionamiento', 'Estacionamiento nivel -1', 'alto', 'en_investigacion', 5);

-- Cuentas contables
INSERT INTO cuentas_contables (codigo, nombre, tipo) VALUES
('1000', 'Ingresos por cuotas', 'ingreso'),
('1001', 'Ingresos por amenidades', 'ingreso'),
('1002', 'Ingresos por multas', 'ingreso'),
('2000', 'Mantenimiento general', 'egreso'),
('2001', 'Vigilancia y seguridad', 'egreso'),
('2002', 'Jardinería y limpieza', 'egreso'),
('2003', 'Servicios (agua, luz, gas)', 'egreso'),
('2004', 'Administración', 'egreso'),
('3000', 'Fondo de reserva', 'activo');

-- Transacciones
INSERT INTO transacciones (cuenta_id, tipo, monto, fecha, descripcion, categoria, registrado_por) VALUES
(1, 'ingreso', 22500.00, '2026-02-28', 'Cuotas cobradas febrero 2026', 'Cuotas', 6),
(1, 'ingreso', 17500.00, '2026-03-31', 'Cuotas cobradas marzo 2026', 'Cuotas', 6),
(1, 'ingreso', 10000.00, '2026-04-17', 'Cuotas cobradas abril 2026 (parcial)', 'Cuotas', 6),
(4, 'egreso', 8000.00, '2026-02-15', 'Servicio de mantenimiento elevadores', 'Mantenimiento', 6),
(5, 'egreso', 15000.00, '2026-02-28', 'Pago mensual vigilancia', 'Seguridad', 6),
(6, 'egreso', 4500.00, '2026-02-28', 'Jardinería y limpieza', 'Limpieza', 6),
(7, 'egreso', 6200.00, '2026-02-28', 'Agua y luz áreas comunes', 'Servicios', 6),
(5, 'egreso', 15000.00, '2026-03-31', 'Pago mensual vigilancia', 'Seguridad', 6),
(6, 'egreso', 4500.00, '2026-03-31', 'Jardinería y limpieza', 'Limpieza', 6),
(7, 'egreso', 6200.00, '2026-03-31', 'Agua y luz áreas comunes', 'Servicios', 6);

-- Anuncios
INSERT INTO anuncios (titulo, contenido, tipo, publicado_por, fecha_expiracion) VALUES
('Corte de agua programado', 'El lunes 21 de abril se realizará mantenimiento a la red hidráulica de 9am a 2pm. Se sugiere almacenar agua.', 'mantenimiento', 1, '2026-04-22 00:00:00'),
('Asamblea ordinaria abril 2026', 'Se convoca a todos los condóminos a la asamblea ordinaria del mes el sábado 26 de abril a las 11am en el salón de eventos.', 'evento', 1, '2026-04-26 12:00:00'),
('Bienvenidos al portal de condóminos', 'Estrenamos el nuevo sistema de administración. Pueden consultar sus estados de cuenta, hacer reservaciones y más.', 'informativo', 1, '2026-05-01 00:00:00');

-- Fondo de reserva
INSERT INTO fondo_reserva (tipo, monto, fecha, descripcion, aprobado_por, saldo_resultante) VALUES
('aporte', 25000.00, '2026-01-31', 'Aportación enero 2026', 1, 25000.00),
('aporte', 25000.00, '2026-02-28', 'Aportación febrero 2026', 1, 50000.00),
('retiro', 12000.00, '2026-03-10', 'Reparación bomba de agua', 1, 38000.00),
('aporte', 25000.00, '2026-03-31', 'Aportación marzo 2026', 1, 63000.00);

-- Configuración inicial
INSERT INTO configuracion (clave, valor, descripcion, tipo) VALUES
('tasa_mora_default', '0.05', 'Tasa de mora mensual por defecto (5%)', 'numero'),
('dias_gracia_default', '5', 'Días de gracia antes de aplicar mora', 'numero'),
('email_notificaciones', 'admin@laspalmas.com', 'Email para notificaciones del sistema', 'texto'),
('generacion_cuotas_dia', '1', 'Día del mes para generar cuotas automáticamente', 'numero'),
('smtp_host', 'smtp.gmail.com', 'Servidor SMTP para envío de correos', 'texto'),
('modulo_mantenimiento', 'true', 'Habilitar módulo de mantenimiento', 'booleano');
