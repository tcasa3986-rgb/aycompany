USE viaje360_crm;

-- Destinos Extras
INSERT IGNORE INTO destinos (pais_id, nombre, descripcion, activo) VALUES
(12, 'Londres', 'La capital británica, historia viva.', 1),
(13, 'Sídney', 'Icono de Australia con su Ópera y playas.', 1),
(14, 'Berlín', 'Cultura alternativa, historia y modernidad.', 1),
(16, 'El Cairo', 'Pirámides, faraones y la magia del Nilo.', 1),
(17, 'Dubái', 'Rascacielos y lujo extremo en el desierto.', 1),
(1, 'Arequipa', 'La ciudad blanca, volcanes y gastronomía.', 1),
(1, 'Máncora', 'Playas, sol, arena y relajo norteño.', 1),
(6, 'Tulum', 'Ruinas mayas sobre el mar Caribe.', 1),
(2, 'Burdeos', 'La capital del vino mundial.', 1),
(4, 'Osaka', 'Capital gastronómica de Japón.', 1);

-- Paquetes Extras
INSERT IGNORE INTO paquetes (destino_id, categoria_id, nombre, descripcion, duracion_dias, precio_base, disponible) VALUES
(13, 2, 'Londres Histórico 5D', 'Tour por el Tower Bridge y Big Ben.', 5, 2000.00, 1),
(14, 3, 'Sídney Espectacular 8D', 'Explora Sídney y la barrera de coral.', 8, 4500.00, 1),
(15, 6, 'Berlín Alternativo 4D', 'Muro de Berlín, museos y vida nocturna.', 4, 1500.00, 1),
(16, 2, 'Misterios del Cairo 6D', 'Visita a las pirámides y crucero por el Nilo.', 6, 2200.00, 1),
(17, 1, 'Lujo en Dubái 5D', 'Safari, cena en dunas y Burj Khalifa.', 5, 3000.00, 1),
(18, 4, 'Encanto de Arequipa 4D', 'Cañón del Colca y city tour.', 4, 800.00, 1),
(19, 4, 'Máncora Relax 5D', 'Cabañas frente al mar, clases de surf.', 5, 900.00, 1),
(20, 4, 'Tulum Místico 4D', 'Cenotes y descanso en hotel boutique.', 4, 1800.00, 1),
(21, 5, 'Ruta del Vino en Burdeos', 'Degustación y catas exclusivas.', 6, 2600.00, 1),
(22, 6, 'Sabores de Osaka 6D', 'El mejor tour culinario de Japón.', 6, 3100.00, 1);

-- Clientes Extras
INSERT IGNORE INTO clientes (fuente_id, agente_id, nombre, apellido, email, telefono, pais, ciudad, documento_tipo, documento_num, genero, categoria, creado_en) VALUES
(1, 2, 'Pedro',  'Castillo',  'pedroc@gmail.com',  '+51 911 111 111', 'Perú', 'Lima',      'DNI', '11111111', 'M', 'Nuevo', NOW()),
(2, 3, 'Juana',  'López',     'juanal@gmail.com',  '+51 922 222 222', 'Perú', 'Arequipa',  'DNI', '22222222', 'F', 'VIP', NOW()),
(3, 4, 'Mario',  'Vargas',    'mariov@yahoo.com',  '+51 933 333 333', 'Perú', 'Piura',     'DNI', '33333333', 'M', 'Recurrente', NOW()),
(4, 2, 'Elena',  'Alvarez',   'elenaa@gmail.com',  '+51 944 444 444', 'Perú', 'Chiclayo',  'DNI', '44444444', 'F', 'Nuevo', NOW()),
(5, 3, 'Martín', 'Chavez',    'martinc@hotmail.com','+51 955 555 555', 'Perú','Trujillo',  'DNI', '55555555', 'M', 'Nuevo', NOW()),
(6, 4, 'Rosa',   'Díaz',      'rosad@gmail.com',   '+51 966 666 666', 'Perú', 'Tacna',     'DNI', '66666666', 'F', 'Nuevo', NOW()),
(1, 2, 'Manuel', 'Ríos',      'manuelr@gmail.com', '+51 977 777 777', 'Perú', 'Cusco',     'DNI', '77777777', 'M', 'VIP', NOW()),
(2, 3, 'Carmen', 'Paredes',   'carmenp@outlook.com','+51 988 888 888', 'Perú','Iquitos',   'DNI', '88888888', 'F', 'Recurrente', NOW()),
(3, 4, 'Luis',   'Guerrero',  'luisg@gmail.com',   '+51 999 999 999', 'Perú', 'Puno',      'DNI', '99999999', 'M', 'Nuevo', NOW()),
(4, 2, 'Diana',  'Vega',      'dianav@gmail.com',  '+51 900 000 000', 'Perú', 'Cajamarca', 'DNI', '00000000', 'F', 'Nuevo', NOW());

-- Interacciones Extras
INSERT IGNORE INTO interacciones (cliente_id, usuario_id, tipo, descripcion, fecha) VALUES
(13, 2, 'Llamada', 'Consulta inicial.', NOW()),
(14, 3, 'Email', 'Cotización enviada.', NOW()),
(15, 4, 'WhatsApp', 'Pidiendo descuento.', NOW()),
(16, 2, 'Reunión', 'Explicación del itinerario.', NOW()),
(17, 3, 'Llamada', 'Confirmación.', NOW()),
(18, 4, 'WhatsApp', 'Dudas de visa.', NOW()),
(19, 2, 'Email', 'Envío de vouchers.', NOW()),
(20, 3, 'Seguimiento', 'Post viaje feedback.', NOW()),
(21, 4, 'Llamada', 'Ofreciendo paquete de fin de año.', NOW()),
(22, 2, 'Nota', 'Cliente prefiere asiento ventana.', NOW());

-- Oportunidades Extras
INSERT IGNORE INTO oportunidades (cliente_id, agente_id, paquete_id, etapa_id, titulo, valor_estimado, probabilidad, estado, creado_en) VALUES
(13, 2, 12, 1, 'Interés en Londres', 2000.00, 20, 'Activa', NOW()),
(14, 3, 13, 2, 'Cotizando Australia', 4500.00, 40, 'Activa', NOW()),
(15, 4, 14, 3, 'Viaje Grupal', 1500.00, 60, 'Activa', NOW()),
(16, 2, 15, 4, 'Novios en El Cairo', 2200.00, 80, 'Activa', NOW()),
(17, 3, 16, 5, 'Luna de miel Dubái', 3000.00, 90, 'Activa', NOW()),
(18, 4, 17, 6, 'Arequipa familiar', 800.00, 100, 'Ganada', NOW()),
(19, 2, 18, 6, 'Máncora fin de semana', 900.00, 100, 'Ganada', NOW()),
(20, 3, 19, 6, 'Tulum relax', 1800.00, 100, 'Ganada', NOW()),
(21, 4, 20, 7, 'Burdeos vino', 2600.00, 0, 'Perdida', NOW()),
(22, 2, 21, 6, 'Osaka negocios', 3100.00, 100, 'Ganada', NOW());

-- Reservas Extras
INSERT IGNORE INTO reservas (cliente_id, agente_id, paquete_id, codigo_reserva, fecha_salida, fecha_regreso, precio_total, total_final, estado, creado_en) VALUES
(18, 4, 17, 'V360-TEST-001', DATE_ADD(NOW(), INTERVAL 1 MONTH), DATE_ADD(NOW(), INTERVAL 35 DAY), 800.00, 800.00, 'Confirmada', NOW()),
(19, 2, 18, 'V360-TEST-002', DATE_ADD(NOW(), INTERVAL 2 MONTH), DATE_ADD(NOW(), INTERVAL 65 DAY), 900.00, 900.00, 'Confirmada', NOW()),
(20, 3, 19, 'V360-TEST-003', DATE_ADD(NOW(), INTERVAL 3 MONTH), DATE_ADD(NOW(), INTERVAL 95 DAY), 1800.00, 1800.00, 'Confirmada', NOW()),
(22, 2, 21, 'V360-TEST-004', DATE_ADD(NOW(), INTERVAL 4 MONTH), DATE_ADD(NOW(), INTERVAL 130 DAY), 3100.00, 3100.00, 'En Curso', NOW()),
(13, 2, 12, 'V360-TEST-005', DATE_ADD(NOW(), INTERVAL 5 MONTH), DATE_ADD(NOW(), INTERVAL 155 DAY), 2000.00, 2000.00, 'Pendiente', NOW()),
(14, 3, 13, 'V360-TEST-006', DATE_ADD(NOW(), INTERVAL 6 MONTH), DATE_ADD(NOW(), INTERVAL 185 DAY), 4500.00, 4500.00, 'Pendiente', NOW()),
(15, 4, 14, 'V360-TEST-007', DATE_ADD(NOW(), INTERVAL 7 MONTH), DATE_ADD(NOW(), INTERVAL 215 DAY), 1500.00, 1500.00, 'Completada', DATE_SUB(NOW(), INTERVAL 1 MONTH)),
(16, 2, 15, 'V360-TEST-008', DATE_ADD(NOW(), INTERVAL 8 MONTH), DATE_ADD(NOW(), INTERVAL 245 DAY), 2200.00, 2200.00, 'Completada', DATE_SUB(NOW(), INTERVAL 2 MONTH)),
(17, 3, 16, 'V360-TEST-009', DATE_ADD(NOW(), INTERVAL 9 MONTH), DATE_ADD(NOW(), INTERVAL 275 DAY), 3000.00, 3000.00, 'Cancelada', NOW()),
(18, 4, 17, 'V360-TEST-010', DATE_ADD(NOW(), INTERVAL 10 MONTH), DATE_ADD(NOW(), INTERVAL 305 DAY), 800.00, 800.00, 'Confirmada', NOW());

-- Pasajeros Extras
INSERT IGNORE INTO pasajeros (reserva_id, nombre, apellido, pasaporte, tipo) VALUES
(11, 'Pasajero', 'Prueba1', 'TEST01', 'Adulto'),
(12, 'Pasajero', 'Prueba2', 'TEST02', 'Adulto'),
(13, 'Pasajero', 'Prueba3', 'TEST03', 'Adulto'),
(14, 'Pasajero', 'Prueba4', 'TEST04', 'Adulto'),
(15, 'Pasajero', 'Prueba5', 'TEST05', 'Adulto'),
(16, 'Pasajero', 'Prueba6', 'TEST06', 'Adulto'),
(17, 'Pasajero', 'Prueba7', 'TEST07', 'Adulto'),
(18, 'Pasajero', 'Prueba8', 'TEST08', 'Adulto'),
(19, 'Pasajero', 'Prueba9', 'TEST09', 'Adulto'),
(20, 'Pasajero', 'Prueba10', 'TEST10', 'Adulto');

-- Pagos Extras
INSERT IGNORE INTO pagos (reserva_id, metodo_id, monto, estado, fecha_pago, registrado_por) VALUES
(11, 1, 800.00, 'Verificado', NOW(), 4),
(12, 2, 900.00, 'Verificado', NOW(), 2),
(13, 3, 1800.00, 'Verificado', NOW(), 3),
(14, 4, 3100.00, 'Verificado', NOW(), 2),
(17, 1, 1500.00, 'Verificado', DATE_SUB(NOW(), INTERVAL 1 MONTH), 4),
(18, 2, 2200.00, 'Verificado', DATE_SUB(NOW(), INTERVAL 2 MONTH), 2),
(11, 3, 0.00, 'Rechazado', NOW(), 4),
(12, 4, 0.00, 'Rechazado', NOW(), 2),
(13, 1, 50.00, 'Pendiente', NOW(), 3),
(14, 2, 100.00, 'Pendiente', NOW(), 2);

-- Proveedores Extras
INSERT IGNORE INTO proveedores (nombre, tipo, contacto, pais, activo) VALUES
('Tren Andino', 'Transporte', 'Jorge', 'Perú', 1),
('Buses del sur', 'Transporte', 'Ana', 'Chile', 1),
('Seguro Viajero X', 'Seguro', 'Pedro', 'México', 1),
('Guías Pro Europa', 'Operadora', 'Maria', 'España', 1),
('Hoteles Encanto', 'Hotel', 'Jose', 'Perú', 1),
('Aerovías Nacionales', 'Aerolínea', 'Luis', 'Perú', 1),
('Global Res', 'Operadora', 'Katy', 'EEUU', 1),
('Tours Andinos', 'Operadora', 'Saul', 'Bolivia', 1),
('Hotel Resort X', 'Hotel', 'Felipe', 'Ecuador', 1),
('Fly Express', 'Aerolínea', 'Diana', 'Colombia', 1);

-- Campañas Extras
INSERT IGNORE INTO campanas (nombre, tipo, estado, presupuesto) VALUES
('Campaña Verano Test1', 'Email', 'Activa', 100.0),
('Campaña Verano Test2', 'WhatsApp', 'Activa', 200.0),
('Campaña Verano Test3', 'SMS', 'Borrador', 300.0),
('Campaña Verano Test4', 'Redes Sociales', 'Pausada', 400.0),
('Campaña Verano Test5', 'Email', 'Finalizada', 500.0),
('Campaña Verano Test6', 'WhatsApp', 'Activa', 600.0),
('Campaña Verano Test7', 'SMS', 'Borrador', 700.0),
('Campaña Verano Test8', 'Redes Sociales', 'Activa', 800.0),
('Campaña Verano Test9', 'Email', 'Pausada', 900.0),
('Campaña Verano Test10', 'WhatsApp', 'Activa', 1000.0);

-- Tareas Extras
INSERT IGNORE INTO tareas (asignado_a, creado_por, titulo, estado, prioridad) VALUES
(2, 1, 'Tarea de test 1', 'Pendiente', 'Baja'),
(3, 1, 'Tarea de test 2', 'En Progreso', 'Media'),
(4, 1, 'Tarea de test 3', 'Completada', 'Alta'),
(2, 1, 'Tarea de test 4', 'Pendiente', 'Urgente'),
(3, 1, 'Tarea de test 5', 'Completada', 'Media'),
(4, 1, 'Tarea de test 6', 'En Progreso', 'Alta'),
(2, 1, 'Tarea de test 7', 'Cancelada', 'Media'),
(3, 1, 'Tarea de test 8', 'Pendiente', 'Alta'),
(4, 1, 'Tarea de test 9', 'Pendiente', 'Media'),
(2, 1, 'Tarea de test 10', 'Completada', 'Baja');
