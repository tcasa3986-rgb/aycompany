USE viaje360_crm;

-- ─── Costo Neto para Paquetes (margen real ~25-35%) ──────────────
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.68, 2) WHERE id = 1;   -- París: margen 32%
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.70, 2) WHERE id = 2;   -- Roma: 30%
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.72, 2) WHERE id = 3;   -- Japón: 28%
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.67, 2) WHERE id = 4;   -- Tailandia: 33%
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.65, 2) WHERE id = 5;   -- Cancún: 35%
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.71, 2) WHERE id = 6;   -- Barcelona: 29%
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.73, 2) WHERE id = 7;   -- Nueva York: 27%
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.74, 2) WHERE id = 8;   -- Maldivas: 26%
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.66, 2) WHERE id = 9;   -- Estambul: 34%
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.60, 2) WHERE id = 10;  -- Cusco: 40%
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.70, 2) WHERE id = 11;  -- Santorini: 30%
-- Paquetes extra (ids 12-22 copias)
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.68, 2) WHERE id BETWEEN 12 AND 22;
-- Paquetes test nuevos (23+)
UPDATE paquetes SET costo_neto = ROUND(precio_base * 0.69, 2) WHERE id >= 23;

-- ─── Poblar costo_neto en reservas existentes ────────────────────
-- Reservas completadas/confirmadas vinculadas a paquetes
UPDATE reservas r
JOIN paquetes p ON p.id = r.paquete_id
SET r.costo_neto = ROUND(p.costo_neto * (r.num_adultos + r.num_ninos), 2)
WHERE r.costo_neto = 0 AND r.paquete_id IS NOT NULL;

-- Para reservas sin paquete (personalizadas), usar 70% del total como costo estimado
UPDATE reservas
SET costo_neto = ROUND(total_final * 0.70, 2)
WHERE costo_neto = 0 AND paquete_id IS NULL
  AND estado != 'Cancelada';

SELECT 
  COUNT(*) AS paquetes_con_costo
FROM paquetes WHERE costo_neto > 0;

SELECT 
  COUNT(*) AS reservas_con_costo,
  ROUND(SUM(total_final), 2) AS ingresos_total,
  ROUND(SUM(costo_neto), 2) AS costos_total,
  ROUND(SUM(total_final) - SUM(costo_neto), 2) AS utilidad_estimada,
  ROUND(((SUM(total_final) - SUM(costo_neto)) / SUM(total_final)) * 100, 1) AS margen_pct
FROM reservas
WHERE costo_neto > 0 AND estado != 'Cancelada';
