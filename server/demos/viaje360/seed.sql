-- ============================================================
--  CRM VIAJE 360 - DATOS DE PRUEBA (SEED)
--  Ejecutar DESPUÉS de crear la base de datos y las tablas
--  Comando: mysql -u root -p viaje360_crm < seed.sql
-- ============================================================

USE viaje360_crm;

-- ── Agentes adicionales ───────────────────────────────────────
-- Password para todos: Viaje360@
INSERT IGNORE INTO usuarios (rol_id, nombre, apellido, email, password_hash, telefono, activo) VALUES
  (2, 'María',    'López',    'maria.lopez@viaje360.com',    '$2b$10$ntwUCHAHmIqIiBt2XKJpJ.7DmXWJjZ1.TZB813mNUQAkBkFqNiQLS', '+51 987 654 321', 1),
  (3, 'Carlos',   'Ramos',    'carlos.ramos@viaje360.com',   '$2b$10$ntwUCHAHmIqIiBt2XKJpJ.7DmXWJjZ1.TZB813mNUQAkBkFqNiQLS', '+51 912 345 678', 1),
  (3, 'Sofía',    'Mendoza',  'sofia.mendoza@viaje360.com',  '$2b$10$ntwUCHAHmIqIiBt2XKJpJ.7DmXWJjZ1.TZB813mNUQAkBkFqNiQLS', '+51 956 123 789', 1),
  (3, 'Diego',    'Vargas',   'diego.vargas@viaje360.com',   '$2b$10$ntwUCHAHmIqIiBt2XKJpJ.7DmXWJjZ1.TZB813mNUQAkBkFqNiQLS', '+51 943 567 890', 1),
  (4, 'Andrea',   'Torres',   'andrea.torres@viaje360.com',  '$2b$10$ntwUCHAHmIqIiBt2XKJpJ.7DmXWJjZ1.TZB813mNUQAkBkFqNiQLS', '+51 978 234 567', 1);

-- ── Destinos ─────────────────────────────────────────────────
INSERT IGNORE INTO destinos (pais_id, nombre, descripcion, activo) VALUES
  (2,  'París',         'La ciudad del amor, hogar de la Torre Eiffel y el Louvre.',             1),
  (3,  'Roma',          'La Ciudad Eterna, cuna del Imperio Romano y la gastronomía italiana.',   1),
  (4,  'Tokio',         'La megalópolis japonesa donde el futuro y la tradición se fusionan.',    1),
  (5,  'Bangkok',       'Templos dorados, mercados flotantes y vida nocturna inigualable.',       1),
  (6,  'Cancún',        'Playas de arena blanca y aguas turquesas en el Caribe mexicano.',        1),
  (7,  'Río de Janeiro','Tierra del carnaval, el Cristo Redentor y la playa de Copacabana.',     1),
  (8,  'Barcelona',     'Arte, arquitectura Gaudí y las Ramblas a orillas del Mediterráneo.',    1),
  (9,  'Nueva York',    'La Gran Manzana: Broadway, Central Park y la Estatua de la Libertad.',  1),
  (10, 'Maldivas',      'Atolones de arena blanca y villas sobre el agua en el Océano Índico.',  1),
  (11, 'Estambul',      'El puente entre Europa y Asia, mezquitas y el Gran Bazar.',             1),
  (1,  'Cusco',         'La ciudad imperial inca, puerta de entrada a Machu Picchu.',            1),
  (15, 'Santorini',     'Cúpulas azules, atardeceres mágicos y vino volcánico en el Egeo.',      1);

-- ── Paquetes turísticos ───────────────────────────────────────
INSERT IGNORE INTO paquetes (destino_id, categoria_id, nombre, descripcion, duracion_dias, precio_base, precio_adulto, precio_nino, incluye, no_incluye, disponible) VALUES
  (1,  1, 'Luna de Miel en París 7D/6N',    'Paquete romántico con hotel boutique, cena con vista a la Torre Eiffel y crucero por el Sena.', 7, 2800.00, 2800.00, 1500.00, 'Vuelos Lima-París-Lima, hotel 5★, desayunos, city tour, crucero Sena', 'Cenas (excepto bienvenida), visados, seguro', 1),
  (2,  3, 'Roma Clásica 6D/5N',             'Recorre el Coliseo, el Vaticano y la Fontana di Trevi con guía especializado.',                  6, 2200.00, 2200.00, 1200.00, 'Vuelos, hotel 4★ céntrico, desayunos, tours incluidos',              'Vuelo interno, propinas, gastos personales',  1),
  (3,  3, 'Japón Imperial 10D/9N',           'Tokio, Kyoto y Osaka: templos, cerezos y gastronomía japonesa auténtica.',                       10, 4500.00, 4500.00, 2800.00, 'Vuelos, hoteles 4★, JR Pass 14 días, desayunos y cenas',             'Actividades opcionales, seguro',              1),
  (4,  4, 'Tailandia Tropical 8D/7N',        'Playas de Phuket, templos de Bangkok y masajes tradicionales thai.',                             8,  2100.00, 2100.00, 1100.00, 'Vuelos, traslados, hotel 4★, tours, algunas comidas',               'Gastos personales, visado si aplica',         1),
  (5,  4, 'Cancún All Inclusive 7D/6N',      'Resort 5★ con todo incluido, actividades acuáticas y excursión a Chichén Itzá.',                 7,  1800.00, 1800.00,  900.00, 'Vuelos Lima-Cancún, resort all inclusive, excursión Chichén Itzá',   'Seguro, pasaporte, compras',                  1),
  (7,  6, 'Barcelona & Madrid 9D/8N',        'Gaudí, Sagrada Familia, El Prado y la gastronomía española al máximo nivel.',                    9,  3200.00, 3200.00, 1800.00, 'Vuelos, hoteles, desayunos, traslados intercity, tours guiados',     'Cenas, actividades opcionales, seguro',       1),
  (8,  6, 'Nueva York Exprés 6D/5N',         'Manhattan, Times Square, Central Park, Cena en Top of the Rock y show de Broadway.',             6,  3500.00, 3500.00, 2000.00, 'Vuelos, hotel Manhattan 4★, city pass, show Broadway',              'Comidas, visado USA, seguro',                 1),
  (9,  1, 'Maldivas Paraíso 7D/6N',          'Villa sobre el agua, snorkel, buceo y desayuno privado en el Océano Índico.',                    7,  5800.00, 5800.00, 3200.00, 'Vuelos, villa overwater 5★, pensión completa, snorkel, buceo',       'Alcohol, actividades extras, seguro',         1),
  (10, 2, 'Aventura en Estambul 5D/4N',      'Hagia Sophia, Palacio Topkapi, crucero Bósforo y bazar de especias.',                            5,  1600.00, 1600.00,  900.00, 'Vuelos, hotel 4★, desayunos, tours, crucero Bósforo',               'Visado, cenas, gastos personales',            1),
  (11, 3, 'Machu Picchu & Cusco 5D/4N',      'Tren a Machu Picchu, Valle Sagrado de los Incas y ceremonia andina.',                            5,   950.00,  950.00,  550.00, 'Traslados, hotel 3★ Cusco, tours, entrada Machu Picchu, tren',       'Vuelo Lima-Cusco, seguro, comidas',           1),
  (12, 4, 'Santorini & Mykonos 8D/7N',       'Las joyas del Egeo: atardeceres en Oia, playas de Mykonos y degustación de vinos locales.',      8,  3900.00, 3900.00, 2200.00, 'Vuelos, hoteles 4★, desayunos, ferry interislas, tours',            'Cenas, actividades opcionales, seguro',       1);

-- ── Clientes ─────────────────────────────────────────────────
INSERT IGNORE INTO clientes (fuente_id, agente_id, nombre, apellido, email, telefono, pais, ciudad, documento_tipo, documento_num, genero, categoria, creado_en) VALUES
  (1, 2, 'Laura',    'García',    'laura.garcia@gmail.com',    '+51 991 234 567', 'Perú',    'Lima',      'DNI', '45678901', 'F', 'VIP',       DATE_SUB(NOW(), INTERVAL 8 MONTH)),
  (2, 3, 'Roberto',  'Sánchez',   'rsanchez@hotmail.com',      '+51 987 345 678', 'Perú',    'Miraflores','DNI', '56789012', 'M', 'Recurrente',DATE_SUB(NOW(), INTERVAL 7 MONTH)),
  (3, 4, 'Valeria',  'Morales',   'vmorales@outlook.com',      '+51 956 456 789', 'Perú',    'San Isidro','DNI', '67890123', 'F', 'VIP',       DATE_SUB(NOW(), INTERVAL 6 MONTH)),
  (1, 2, 'Jorge',    'Pérez',     'jperez@empresa.com',        '+51 943 567 890', 'Perú',    'Surco',     'DNI', '78901234', 'M', 'Nuevo',     DATE_SUB(NOW(), INTERVAL 5 MONTH)),
  (4, 3, 'Carla',    'Flores',    'cflores@gmail.com',         '+51 978 678 901', 'Perú',    'San Borja', 'DNI', '89012345', 'F', 'Recurrente',DATE_SUB(NOW(), INTERVAL 5 MONTH)),
  (2, 4, 'Miguel',   'Castro',    'mcastro@yahoo.com',         '+51 961 789 012', 'Perú',    'Barranco',  'DNI', '90123456', 'M', 'Nuevo',     DATE_SUB(NOW(), INTERVAL 4 MONTH)),
  (1, 2, 'Patricia', 'Huamán',    'phuaman@gmail.com',         '+51 994 890 123', 'Perú',    'La Molina', 'DNI', '01234567', 'F', 'VIP',       DATE_SUB(NOW(), INTERVAL 4 MONTH)),
  (3, 3, 'Eduardo',  'Quispe',    'equispe@corp.com',          '+51 932 901 234', 'Perú',    'Lima',      'DNI', '12345678', 'M', 'Recurrente',DATE_SUB(NOW(), INTERVAL 3 MONTH)),
  (5, 4, 'Diana',    'Ramírez',   'draminez@gmail.com',        '+51 975 012 345', 'Perú',    'Jesús María','DNI','23456789', 'F', 'Nuevo',     DATE_SUB(NOW(), INTERVAL 3 MONTH)),
  (1, 2, 'Andrés',   'Medina',    'amedina@hotmail.com',       '+51 988 123 456', 'Perú',    'Miraflores','DNI', '34567890', 'M', 'Nuevo',     DATE_SUB(NOW(), INTERVAL 2 MONTH)),
  (2, 3, 'Gabriela', 'Torres',    'gtorres@gmail.com',         '+51 941 234 567', 'Perú',    'Lima',      'DNI', '45678902', 'F', 'Recurrente',DATE_SUB(NOW(), INTERVAL 2 MONTH)),
  (6, 4, 'Lucía',    'Aguilar',   'laguilar@empresa.pe',       '+51 923 345 678', 'Perú',    'Lima',      'DNI', '56789013', 'F', 'Nuevo',     DATE_SUB(NOW(), INTERVAL 1 MONTH));

-- ── Interacciones ─────────────────────────────────────────────
INSERT IGNORE INTO interacciones (cliente_id, usuario_id, tipo, descripcion, fecha) VALUES
  (1, 2, 'Llamada',   'Cliente interesada en paquete a Maldivas para aniversario. Busca villa overwater.', DATE_SUB(NOW(), INTERVAL 7 MONTH)),
  (1, 2, 'Email',     'Envío cotización detallada del paquete Maldivas Paraíso 7D.', DATE_SUB(NOW(), INTERVAL 6 MONTH)),
  (2, 3, 'WhatsApp',  'Consulta por disponibilidad de Japón en temporada de cerezos (abril).', DATE_SUB(NOW(), INTERVAL 6 MONTH)),
  (3, 4, 'Reunión',   'Reunión presencial en oficina. Interesada en Europa: París + Barcelona.', DATE_SUB(NOW(), INTERVAL 5 MONTH)),
  (4, 2, 'Cotización','Cotización personalizada para familia de 4: Cancún todo incluido.', DATE_SUB(NOW(), INTERVAL 4 MONTH)),
  (5, 3, 'Llamada',   'Seguimiento post-viaje. Muy satisfecha con su experiencia en Roma.', DATE_SUB(NOW(), INTERVAL 3 MONTH)),
  (6, 4, 'Email',     'Consulta inicial por destinos de aventura en Sudamérica.', DATE_SUB(NOW(), INTERVAL 3 MONTH)),
  (7, 2, 'WhatsApp',  'Confirmación de fechas para viaje a Santorini en temporada alta.', DATE_SUB(NOW(), INTERVAL 2 MONTH)),
  (8, 3, 'Reunión',   'Presentación de opciones para viaje corporativo grupal (15 personas).', DATE_SUB(NOW(), INTERVAL 2 MONTH)),
  (9, 4, 'Seguimiento','Primer contacto vía web. Interesa en paquete a Bangkok.', DATE_SUB(NOW(), INTERVAL 1 MONTH)),
  (10, 2, 'Llamada',  'Consulta sobre visado para viajar a Nueva York.', DATE_SUB(NOW(), INTERVAL 3 WEEK)),
  (11, 3, 'Email',    'Solicitud de itinerario detallado para Machu Picchu.', DATE_SUB(NOW(), INTERVAL 2 WEEK));

-- ── Oportunidades ─────────────────────────────────────────────
INSERT IGNORE INTO oportunidades (cliente_id, agente_id, paquete_id, etapa_id, titulo, valor_estimado, probabilidad, fecha_cierre, estado, creado_en) VALUES
  (1, 2, 8, 6, 'Maldivas Aniversario - García',           5800.00, 100, DATE_ADD(NOW(), INTERVAL 2 MONTH), 'Ganada',  DATE_SUB(NOW(), INTERVAL 6 MONTH)),
  (2, 3, 3, 6, 'Japón Primavera - Sánchez',               4500.00, 100, DATE_ADD(NOW(), INTERVAL 3 MONTH), 'Ganada',  DATE_SUB(NOW(), INTERVAL 5 MONTH)),
  (3, 4, 1, 6, 'Luna de Miel París - Morales',            2800.00, 100, DATE_ADD(NOW(), INTERVAL 1 MONTH), 'Ganada',  DATE_SUB(NOW(), INTERVAL 4 MONTH)),
  (4, 2, 5, 6, 'Cancún Familiar - Pérez',                 7200.00, 100, DATE_ADD(NOW(), INTERVAL 2 MONTH), 'Ganada',  DATE_SUB(NOW(), INTERVAL 3 MONTH)),
  (5, 3, 2, 4, 'Roma Cultural - Flores',                  2200.00,  80, DATE_ADD(NOW(), INTERVAL 1 MONTH), 'Activa',  DATE_SUB(NOW(), INTERVAL 2 MONTH)),
  (6, 4, 4, 3, 'Tailandia Aventura - Castro',             2100.00,  60, DATE_ADD(NOW(), INTERVAL 6 WEEK),  'Activa',  DATE_SUB(NOW(), INTERVAL 2 MONTH)),
  (7, 2, 11, 5,'Santorini Luna de Miel - Huamán',         3900.00,  90, DATE_ADD(NOW(), INTERVAL 3 WEEK),  'Activa',  DATE_SUB(NOW(), INTERVAL 6 WEEK)),
  (8, 3, 6, 4, 'Barcelona Grupal - Quispe',              16000.00,  70, DATE_ADD(NOW(), INTERVAL 2 MONTH), 'Activa',  DATE_SUB(NOW(), INTERVAL 5 WEEK)),
  (9, 4, 4, 2, 'Bangkok Relax - Ramírez',                 2100.00,  40, DATE_ADD(NOW(), INTERVAL 2 MONTH), 'Activa',  DATE_SUB(NOW(), INTERVAL 3 WEEK)),
  (10,2, 7, 3, 'Nueva York Exprés - Medina',              3500.00,  50, DATE_ADD(NOW(), INTERVAL 6 WEEK),  'Activa',  DATE_SUB(NOW(), INTERVAL 2 WEEK)),
  (11,3, 10,1, 'Machu Picchu - Torres',                    950.00,  20, DATE_ADD(NOW(), INTERVAL 3 MONTH), 'Activa',  DATE_SUB(NOW(), INTERVAL 1 WEEK)),
  (6, 4, 9, 7, 'Estambul - Castro (PERDIDA)',              1600.00,   0, DATE_SUB(NOW(), INTERVAL 1 MONTH), 'Perdida', DATE_SUB(NOW(), INTERVAL 3 MONTH));

-- ── Reservas ─────────────────────────────────────────────────
INSERT IGNORE INTO reservas (oportunidad_id, cliente_id, agente_id, paquete_id, codigo_reserva, fecha_salida, fecha_regreso, num_adultos, num_ninos, precio_total, descuento, impuesto, total_final, estado, creado_en) VALUES
  (1, 1, 2, 8, 'V360-2501-AA01', DATE_ADD(NOW(), INTERVAL 2 MONTH),  DATE_ADD(NOW(), INTERVAL 70 DAY), 2, 0, 11600.00, 600.00,  200.00, 11200.00, 'Confirmada', DATE_SUB(NOW(), INTERVAL 5 MONTH)),
  (2, 2, 3, 3, 'V360-2501-BB02', DATE_ADD(NOW(), INTERVAL 3 MONTH),  DATE_ADD(NOW(), INTERVAL 100 DAY),2, 0,  9000.00, 500.00,  150.00,  8650.00, 'Confirmada', DATE_SUB(NOW(), INTERVAL 4 MONTH)),
  (3, 3, 4, 1, 'V360-2501-CC03', DATE_ADD(NOW(), INTERVAL 45 DAY),   DATE_ADD(NOW(), INTERVAL 52 DAY), 2, 0,  5600.00, 200.00,  100.00,  5500.00, 'Confirmada', DATE_SUB(NOW(), INTERVAL 3 MONTH)),
  (4, 4, 2, 5, 'V360-2502-DD04', DATE_ADD(NOW(), INTERVAL 55 DAY),   DATE_ADD(NOW(), INTERVAL 62 DAY), 2, 2,  5400.00, 300.00,   80.00,  5180.00, 'Pendiente',  DATE_SUB(NOW(), INTERVAL 2 MONTH)),
  (1, 1, 2, 8, 'V360-2410-EE05', DATE_SUB(NOW(), INTERVAL 6 MONTH), DATE_SUB(NOW(), INTERVAL 5 MONTH),2, 0, 11600.00,   0.00,  200.00, 11800.00, 'Completada', DATE_SUB(NOW(), INTERVAL 8 MONTH)),
  (2, 2, 3, 3, 'V360-2411-FF06', DATE_SUB(NOW(), INTERVAL 5 MONTH), DATE_SUB(NOW(), INTERVAL 4 MONTH),2, 0,  9000.00, 200.00,  150.00,  8950.00, 'Completada', DATE_SUB(NOW(), INTERVAL 6 MONTH)),
  (3, 3, 4, 1, 'V360-2412-GG07', DATE_SUB(NOW(), INTERVAL 4 MONTH), DATE_SUB(NOW(), INTERVAL 3 MONTH),2, 0,  5600.00,   0.00,  100.00,  5700.00, 'Completada', DATE_SUB(NOW(), INTERVAL 5 MONTH)),
  (4, 4, 2, 5, 'V360-2501-HH08', DATE_SUB(NOW(), INTERVAL 3 MONTH), DATE_SUB(NOW(), INTERVAL 2 MONTH),2, 2,  5400.00, 100.00,   80.00,  5380.00, 'Completada', DATE_SUB(NOW(), INTERVAL 4 MONTH)),
  (5, 5, 3, 2, 'V360-2502-II09', DATE_SUB(NOW(), INTERVAL 2 MONTH), DATE_SUB(NOW(), INTERVAL 6 WEEK), 2, 0,  4400.00,   0.00,   80.00,  4480.00, 'Completada', DATE_SUB(NOW(), INTERVAL 3 MONTH)),
  (6, 6, 4, 4, 'V360-2503-JJ10', DATE_SUB(NOW(), INTERVAL 45 DAY),  DATE_SUB(NOW(), INTERVAL 2 WEEK), 1, 0,  2100.00,   0.00,   40.00,  2140.00, 'Completada', DATE_SUB(NOW(), INTERVAL 2 MONTH));

-- ── Pasajeros ─────────────────────────────────────────────────
INSERT IGNORE INTO pasajeros (reserva_id, nombre, apellido, pasaporte, tipo) VALUES
  (1, 'Laura',    'García',    'PE123456', 'Adulto'),
  (1, 'Marco',    'García',    'PE234567', 'Adulto'),
  (2, 'Roberto',  'Sánchez',   'PE345678', 'Adulto'),
  (2, 'Ana',      'Sánchez',   'PE456789', 'Adulto'),
  (3, 'Valeria',  'Morales',   'PE567890', 'Adulto'),
  (3, 'Carlos',   'Morales',   'PE678901', 'Adulto'),
  (4, 'Jorge',    'Pérez',     'PE789012', 'Adulto'),
  (4, 'Sandra',   'Pérez',     'PE890123', 'Adulto'),
  (4, 'Camila',   'Pérez',     '',         'Niño'),
  (4, 'Diego',    'Pérez',     '',         'Niño');

-- ── Pagos ────────────────────────────────────────────────────
INSERT IGNORE INTO pagos (reserva_id, metodo_id, monto, referencia, estado, fecha_pago, registrado_por) VALUES
  -- Reservas completadas (pagadas al 100%)
  (5, 4, 11800.00, 'TRF-20241010-001', 'Verificado', DATE_SUB(NOW(), INTERVAL 8 MONTH), 2),
  (6, 2, 8950.00,  'VISA-20241112-002', 'Verificado', DATE_SUB(NOW(), INTERVAL 6 MONTH), 3),
  (7, 4, 5700.00,  'TRF-20241215-003', 'Verificado', DATE_SUB(NOW(), INTERVAL 5 MONTH), 4),
  (8, 2, 2690.00,  'VISA-20250110-004', 'Verificado', DATE_SUB(NOW(), INTERVAL 4 MONTH), 2),
  (8, 4, 2690.00,  'TRF-20250120-005', 'Verificado', DATE_SUB(NOW(), INTERVAL 110 DAY), 2),
  (9, 3, 4480.00,  'DEBITO-20250205-006', 'Verificado', DATE_SUB(NOW(), INTERVAL 3 MONTH), 3),
  (10,4, 2140.00,  'TRF-20250301-007', 'Verificado', DATE_SUB(NOW(), INTERVAL 2 MONTH), 4),
  -- Reservas confirmadas (50% adelanto)
  (1, 4, 5600.00,  'TRF-20250315-008', 'Verificado', DATE_SUB(NOW(), INTERVAL 1 MONTH), 2),
  (2, 4, 4325.00,  'TRF-20250320-009', 'Verificado', DATE_SUB(NOW(), INTERVAL 3 WEEK),  3),
  (3, 2, 2750.00,  'VISA-20250325-010', 'Verificado', DATE_SUB(NOW(), INTERVAL 2 WEEK), 4);

-- ── Proveedores ───────────────────────────────────────────────
INSERT IGNORE INTO proveedores (nombre, tipo, contacto, email, telefono, pais, sitio_web, activo) VALUES
  ('LATAM Airlines',      'Aerolínea', 'Juan Rojas',      'grupos@latam.com',         '+51 213 8200', 'Perú',    'https://www.latam.com',        1),
  ('Avianca',             'Aerolínea', 'Paula Vera',      'corporativo@avianca.com',  '+57 401 3434', 'Colombia','https://www.avianca.com',       1),
  ('Marriott Lima',       'Hotel',     'Rosa Lima',       'sales.lima@marriott.com',  '+51 217 7000', 'Perú',    'https://www.marriott.com',      1),
  ('Four Seasons Paris',  'Hotel',     'Pierre Dupont',   'reservas@fshotels.com',    '+33 1 4952',   'Francia', 'https://www.fourseasons.com',   1),
  ('Booking.com',         'Operadora', 'Ana Torres',      'b2b@booking.com',          '+44 20 3320',  'UK',      'https://www.booking.com',       1),
  ('Seguros Pacífico',    'Seguro',    'Luis García',     'viajes@pacifico.com.pe',   '+51 224 4000', 'Perú',    'https://www.pacifico.com.pe',   1),
  ('Expedia Partner Solutions','Operadora','Mark Smith',  'partners@expedia.com',     '+1 800 397',   'EEUU',    'https://www.expedia.com',       1),
  ('Transporte VIP',      'Transporte','Carlos Ruiz',     'vip@transportevip.pe',     '+51 999 888',  'Perú',    '',                              1),
  ('Emirates',            'Aerolínea', 'Sara Hassan',     'groups@emirates.com',      '+971 600 555', 'EAU',     'https://www.emirates.com',      1),
  ('Sheraton Cancún',     'Hotel',     'María Jiménez',   'cancun@sheraton.com',      '+52 998 881',  'México',  'https://www.sheraton.com',      1);

-- ── Campañas ─────────────────────────────────────────────────
INSERT IGNORE INTO campanas (nombre, tipo, estado, fecha_inicio, fecha_fin, presupuesto, descripcion, creado_por) VALUES
  ('Semana Santa 2025',      'Email',         'Finalizada', '2025-03-01', '2025-04-05', 2000.00, 'Campaña especial de paquetes para Semana Santa con 15% de descuento.', 1),
  ('Europa en Verano',       'Redes Sociales','Activa',     '2025-04-01', '2025-07-31', 5000.00, 'Promoción de destinos europeos para la temporada de verano boreal.',   1),
  ('Black Friday Viajes',    'WhatsApp',      'Borrador',   '2025-11-20', '2025-11-30', 3000.00, 'Descuentos exclusivos de hasta 30% en paquetes seleccionados.',         2),
  ('Luna de Miel Especial',  'Email',         'Activa',     '2025-04-15', '2025-06-30', 1500.00, 'Paquetes románticos para recién casados con amenidades especiales.',    1),
  ('Machu Picchu Nacional',  'SMS',           'Activa',     '2025-04-01', '2025-05-31', 800.00,  'Promoevando el turismo interno: Cusco y Machu Picchu.',                2);

-- ── Tareas ────────────────────────────────────────────────────
INSERT IGNORE INTO tareas (asignado_a, creado_por, cliente_id, titulo, descripcion, prioridad, estado, fecha_vence) VALUES
  (2, 1, 1,  'Llamar a Laura García para confirmar detalles del viaje a Maldivas', 'Confirmar numero de pasaportes y fecha de vuelo preferida.', 'Urgente',   'Pendiente',   DATE_ADD(NOW(), INTERVAL 2 DAY)),
  (3, 1, 2,  'Enviar itinerario final Japón - Roberto Sánchez',                    'Preparar PDF con hoteles, vuelos y actividades detalladas.',  'Alta',      'En Progreso', DATE_ADD(NOW(), INTERVAL 5 DAY)),
  (4, 1, 4,  'Seguimiento reserva Cancún - Familia Pérez',                         'Confirmar pago del saldo pendiente del 50%.',                 'Alta',      'Pendiente',   DATE_ADD(NOW(), INTERVAL 3 DAY)),
  (2, 1, 7,  'Preparar propuesta Santorini - Patricia Huamán',                     'Presentar opciones de extensión a Mykonos.',                  'Media',     'Pendiente',   DATE_ADD(NOW(), INTERVAL 1 WEEK)),
  (3, 1, 8,  'Cotización grupal Barcelona - Eduardo Quispe',                       'Calcular precio para 15 personas con hotel y guía.',          'Alta',      'En Progreso', DATE_ADD(NOW(), INTERVAL 4 DAY)),
  (4, 2, 9,  'Contactar Lorena Ramírez - Bangkok',                                 'Primer seguimiento post-consulta web.',                       'Media',     'Pendiente',   DATE_ADD(NOW(), INTERVAL 2 WEEK)),
  (2, 1, 10, 'Resolver dudas sobre visado USA - Andrés Medina',                    'Informar sobre proceso de solicitud de visa B1/B2.',          'Baja',      'Pendiente',   DATE_ADD(NOW(), INTERVAL 10 DAY)),
  (3, 1, 11, 'Enviar opciones de hotel en Cusco - Gabriela Torres',                'Mínimo 3 opciones con precios y distancia al centro.',        'Media',     'Pendiente',   DATE_ADD(NOW(), INTERVAL 1 WEEK)),
  (2, 1, 5,  'Post-venta: encuesta de satisfacción Roma',                          'Enviar formulario de feedback del viaje a Roma.',             'Baja',      'Completada',  DATE_SUB(NOW(), INTERVAL 1 WEEK)),
  (4, 2, 6,  'Cierre documentación viaje Bangkok completado',                      'Archivar contratos y comprobantes de pago.',                  'Baja',      'Completada',  DATE_SUB(NOW(), INTERVAL 3 DAY));

-- ============================================================
-- FIN DEL SEED
-- ============================================================
SELECT '✅ Datos de prueba cargados exitosamente!' AS resultado;
