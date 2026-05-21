const pool = require('./src/config/db');

async function sedear() {
  console.log('Iniciando poblado de la base de datos (10 registros por módulo)...');
  
  try {
    // 1. Unidades (Ya existen algunas, insertaremos hasta asegurar al menos 10)
    for (let i = 2; i <= 11; i++) {
        try { await pool.query(`INSERT IGNORE INTO unidades (torre_id, numero, estado) VALUES (1, 'B-${100 + i}', 'habitada')`); } catch(e){}
    }

    // Obtener IDs de unidades
    const [unidadesRows] = await pool.query('SELECT id FROM unidades LIMIT 10');
    const uIds = unidadesRows.map(u => u.id);

    // 2. Residentes
    const nombres = ['Roberto', 'Lucía', 'Fernando', 'Sofía', 'Andrés', 'Valeria', 'Javier', 'Carmen', 'Ricardo', 'Elena'];
    const apellidos = ['Martínez', 'López', 'González', 'Pérez', 'Rodríguez', 'Sánchez', 'Ramírez', 'Cruz', 'Gómez', 'Flores'];
    for (let i = 0; i < 10; i++) {
       try { await pool.query(`INSERT INTO residentes (unidad_id, nombre, apellidos, tipo, telefono, email, activo) VALUES (?, ?, ?, 'propietario', '555123456${i}', 'residente${i}@correo.com', 1)`, [uIds[i % uIds.length], nombres[i], apellidos[i]]); } catch(e){}
    }

    // 3. Amenidades (Gimnasio, Alberca ya existen) - Agregaremos reservaciones
    for (let i = 0; i < 10; i++) {
      const date = new Date();
      date.setDate(date.getDate() - (i % 5));
      try { await pool.query(`INSERT INTO reservaciones (amenidad_id, unidad_id, fecha, hora_inicio, hora_fin, num_personas, estado) VALUES (1, ?, ?, '10:00', '12:00', 2, 'completada')`, [uIds[i % uIds.length], date.toISOString().split('T')[0]]); } catch(e){}
    }

    // 4. Cuotas y Pagos
    try { await pool.query(`INSERT IGNORE INTO tipos_cuota (nombre, monto, periodicidad) VALUES ('Mantenimiento', 1500, 'mensual')`); } catch(e){}
    for (let i = 0; i < 10; i++) {
       const esVencida = i % 2 === 0;
       const fechaVenc = new Date();
       fechaVenc.setDate(fechaVenc.getDate() - (esVencida ? 15 : -15));
       
       try {
           const [cuotaRes] = await pool.query(`INSERT INTO cuotas (unidad_id, tipo_cuota_id, monto, fecha_emision, fecha_vencimiento, estado) VALUES (?, 1, 1500, CURDATE(), ?, ?)`, [uIds[i % uIds.length], fechaVenc.toISOString().split('T')[0], esVencida ? 'vencido' : 'pagado']);
           
           if (!esVencida) {
              await pool.query(`INSERT INTO pagos (cuota_id, unidad_id, monto_pagado, fecha_pago, metodo) VALUES (?, ?, 1500, NOW(), 'transferencia')`, [cuotaRes.insertId, uIds[i % uIds.length]]);
           }
       } catch(e) { console.error(e.message) }
    }
    console.log('✅ Cuotas y Pagos agregados.');

    // 5. Contabilidad (Transacciones)
    let [cuentas] = await pool.query('SELECT id FROM cuentas_contables LIMIT 1');
    if (!cuentas.length) {
       const [insertRes] = await pool.query("INSERT INTO cuentas_contables (codigo, nombre, tipo) VALUES ('001', 'Cuenta Principal', 'activo')");
       cuentas = [{ id: insertRes.insertId }];
    }
    const cid = cuentas[0].id;

    for (let i = 0; i < 10; i++) {
      const tipo = i % 3 === 0 ? 'egreso' : 'ingreso';
      const monto = tipo === 'ingreso' ? 1500 : (i * 200 + 500);
      const cat = tipo === 'ingreso' ? 'Cuotas de Mantenimiento' : 'Reparaciones';
      const fecha = new Date();
      fecha.setDate(fecha.getDate() - i);
      
      try { await pool.query(`INSERT INTO transacciones (cuenta_id, tipo, categoria, descripcion, monto, fecha) VALUES (?, ?, ?, ?, ?, ?)`, [cid, tipo, cat, `Operación de prueba #${i}`, monto, fecha.toISOString().split('T')[0]]); } catch(e){}
    }
    console.log('✅ Transacciones de Contabilidad agregadas.');

    // 6. Mantenimiento (Ordenes)
    const mantTitulos = ['Fuga de agua', 'Lámpara fundida', 'Pintura descascarada', 'Cerradura rota', 'Elevador fallando', 'Limpieza profunda', 'Jardín seco', 'Ventana trancada', 'Rejilla suelta', 'Piscina sucia'];
    for (let i = 0; i < 10; i++) {
        const estado = i % 2 === 0 ? 'pendiente' : 'completado';
        const date = new Date();
        date.setDate(date.getDate() - i);
        try { await pool.query(`INSERT INTO ordenes_trabajo (titulo, descripcion, unidad_id, estado, prioridad, fecha_reporte) VALUES (?, 'Reporte automático', ?, ?, 'media', ?)`, [mantTitulos[i], uIds[i % uIds.length], estado, date.toISOString().split('T')[0]]); } catch(e){ console.error(e.message) }
    }
    console.log('✅ Mantenimiento agregado.');

    // 7. Visitantes (Acceso)
    for (let i = 0; i < 10; i++) {
       const date = new Date();
       try { await pool.query(`INSERT INTO visitantes (unidad_id, matricula_vehiculo, nombre, motivo, tipo, entrada) VALUES (?, 'XYZ${100+i}', 'Repartidor ${i}', 'Paqueteria', 'proveedor', ?)`, [uIds[i % uIds.length], date.toISOString().replace('T', ' ').substring(0,19)]); } catch(e){}
    }
    console.log('✅ Registros de Acceso agregados.');

    // 8. Proveedores
    const servs = ['Gas', 'Agua', 'Internet', 'Basura', 'Seguridad', 'Jardineria', 'Plomeria', 'Electricidad', 'Pintura', 'Alberquero'];
    for (let i = 0; i < 10; i++) {
        try { await pool.query(`INSERT INTO proveedores (nombre, tipo_servicio, contacto_telefono) VALUES (?, ?, '551122334${i}')`, [`Proveedor ${servs[i]} S.A.`, servs[i]]); } catch(e){}
    }
    console.log('✅ Proveedores agregados.');

    console.log('');
    console.log('🎉 SEED COMPLETADO!');
    process.exit(0);

  } catch (err) {
    console.error('❌ Error general al insertar datos:', err);
    process.exit(1);
  }
}

sedear();
