const { 
  sequelize, Rol, Usuario, FuenteOrigen, Etiqueta, Cliente, 
  Interaccion, Pais, Destino, CategoriaPaquete, Paquete, 
  EtapaPipeline, Oportunidad, Reserva, Pasajero, 
  MetodoPago, Pago, Tarea, Proveedor, Campana
} = require('../src/models');
const bcrypt = require('bcryptjs');

async function reseed() {
  console.log('🚀 Iniciando limpieza y re-poblamiento de datos premium...');

  try {
    // 1. Desactivar restricciones de llaves foráneas para limpieza masiva
    await sequelize.query('SET FOREIGN_KEY_CHECKS = 0');

    // 2. Limpiar tablas transaccionales y de catálogo
    const tables = [
      'pagos', 'pasajeros', 'reservas', 'oportunidades', 'interacciones', 
      'tareas', 'clientes', 'cliente_etiquetas', 'paquetes', 'destinos', 
      'usuarios', 'roles', 'paises', 'fuentes_origen', 'etiquetas', 
      'categorias_paquete', 'metodos_pago', 'etapas_pipeline',
      'proveedores', 'campanas'
    ];
    
    for (const table of tables) {
      await sequelize.query(`TRUNCATE TABLE ${table}`);
      console.log(`  ✔ Tabla ${table} limpiada.`);
    }

    // 3. Re-activar restricciones
    await sequelize.query('SET FOREIGN_KEY_CHECKS = 1');

    console.log('\n📦 Insertando configuración base...');

    // ── Roles ──
    const rolAdmin = await Rol.create({ nombre: 'Administrador', descripcion: 'Acceso total al sistema' });
    const rolVendedor = await Rol.create({ nombre: 'Agente de Ventas', descripcion: 'Gestión de sus propios clientes y reservas' });

    // ── Usuarios (Agentes) ──
    const optPassword = await bcrypt.hash('admin123', 10);
    const admin = await Usuario.create({ 
      rol_id: rolAdmin.id, nombre: 'Admin', apellido: 'Sistema', 
      email: 'admin@viaje360.com', password_hash: optPassword 
    });
    const maria = await Usuario.create({ 
      rol_id: rolVendedor.id, nombre: 'María', apellido: 'Rodríguez', 
      email: 'maria@viaje360.com', password_hash: optPassword 
    });
    const sofia = await Usuario.create({ 
      rol_id: rolVendedor.id, nombre: 'Sofía', apellido: 'Pérez', 
      email: 'sofia@viaje360.com', password_hash: optPassword 
    });
    const juan = await Usuario.create({ 
      rol_id: rolVendedor.id, nombre: 'Juan', apellido: 'Órdoñez', 
      email: 'juan@viaje360.com', password_hash: optPassword 
    });

    const ag4 = await Usuario.create({ rol_id: rolVendedor.id, nombre: 'Carlos', apellido: 'Gutiérrez', email: 'carlos@viaje360.com', password_hash: optPassword });
    const ag5 = await Usuario.create({ rol_id: rolVendedor.id, nombre: 'Ana', apellido: 'López', email: 'ana@viaje360.com', password_hash: optPassword });
    const ag6 = await Usuario.create({ rol_id: rolVendedor.id, nombre: 'Luis', apellido: 'Martínez', email: 'luis@viaje360.com', password_hash: optPassword });
    const ag7 = await Usuario.create({ rol_id: rolVendedor.id, nombre: 'Luisa', apellido: 'Fernández', email: 'luisa@viaje360.com', password_hash: optPassword });
    const ag8 = await Usuario.create({ rol_id: rolVendedor.id, nombre: 'Pedro', apellido: 'Gómez', email: 'pedro@viaje360.com', password_hash: optPassword });
    const ag9 = await Usuario.create({ rol_id: rolVendedor.id, nombre: 'Carmen', apellido: 'Díaz', email: 'carmen@viaje360.com', password_hash: optPassword });
    const ag10 = await Usuario.create({ rol_id: rolVendedor.id, nombre: 'Roberto', apellido: 'Vargas', email: 'roberto@viaje360.com', password_hash: optPassword });

    const agentes = [maria, sofia, juan, ag4, ag5, ag6, ag7, ag8, ag9, ag10];

    // ── Fuentes ──
    const fuentes = await FuenteOrigen.bulkCreate([
      { nombre: 'Sitio Web' }, { nombre: 'Redes Sociales' }, 
      { nombre: 'WhatsApp' }, { nombre: 'Referido' }, { nombre: 'Feria de Viajes' }
    ]);

    // ── Países ──
    const paises = await Pais.bulkCreate([
      { nombre: 'Perú', codigo: 'PE', zona: 'América del Sur' },
      { nombre: 'Colombia', codigo: 'CO', zona: 'América del Sur' },
      { nombre: 'México', codigo: 'MX', zona: 'América del Norte' },
      { nombre: 'España', codigo: 'ES', zona: 'Europa' },
      { nombre: 'Italia', codigo: 'IT', zona: 'Europa' },
      { nombre: 'Francia', codigo: 'FR', zona: 'Europa' }
    ]);

    // ── Destinos ──
    const destinos = await Destino.bulkCreate([
      { pais_id: paises[0].id, nombre: 'Cusco & Machu Picchu', descripcion: 'La capital del imperio Inca.' },
      { pais_id: paises[2].id, nombre: 'Cancún', descripcion: 'Playas paradisíacas del Caribe mexicano.' },
      { pais_id: paises[1].id, nombre: 'Medellín', descripcion: 'La ciudad de la eterna primavera.' },
      { pais_id: paises[3].id, nombre: 'Madrid', descripcion: 'Corazón cultural de España.' },
      { pais_id: paises[5].id, nombre: 'París', descripcion: 'La ciudad del amor y las luces.' },
      { pais_id: paises[4].id, nombre: 'Roma', descripcion: 'Cuna de la civilización occidental.' },
      { pais_id: paises[0].id, nombre: 'Arequipa & Colca', descripcion: 'La ciudad blanca y el cañón profundo.' },
      { pais_id: paises[1].id, nombre: 'Cartagena', descripcion: 'Ciudad colonial amurallada.' },
      { pais_id: paises[3].id, nombre: 'Barcelona', descripcion: 'Arquitectura modernista y playa.' },
      { pais_id: paises[5].id, nombre: 'Niza', descripcion: 'La magia de la Riviera Francesa.' }
    ]);

    // ── Categorías y Paquetes ──
    const catAventura = await CategoriaPaquete.create({ nombre: 'Aventura' });
    const catRelax = await CategoriaPaquete.create({ nombre: 'Relax & Playa' });
    const catCultura = await CategoriaPaquete.create({ nombre: 'Cultural' });

    const paquetes = await Paquete.bulkCreate([
      { destino_id: destinos[0].id, categoria_id: catCultura.id, nombre: 'Caminos del Inca', precio_base: 1200, costo_neto: 800, duracion_dias: 5 },
      { destino_id: destinos[1].id, categoria_id: catRelax.id, nombre: 'Verano en Cancún', precio_base: 950, costo_neto: 600, duracion_dias: 7 },
      { destino_id: destinos[4].id, categoria_id: catRelax.id, nombre: 'Luces de París', precio_base: 2200, costo_neto: 1500, duracion_dias: 10 },
      { destino_id: destinos[2].id, categoria_id: catAventura.id, nombre: 'Eje Cafetero', precio_base: 800, costo_neto: 500, duracion_dias: 4 },
      { destino_id: destinos[6].id, categoria_id: catAventura.id, nombre: 'Ruta Colca Extrema', precio_base: 450, costo_neto: 300, duracion_dias: 3 },
      { destino_id: destinos[7].id, categoria_id: catRelax.id, nombre: 'Magia de Cartagena', precio_base: 1100, costo_neto: 750, duracion_dias: 6 },
      { destino_id: destinos[8].id, categoria_id: catCultura.id, nombre: 'Ruta Gaudí', precio_base: 1600, costo_neto: 1000, duracion_dias: 7 },
      { destino_id: destinos[9].id, categoria_id: catRelax.id, nombre: 'Costa Azul VIP', precio_base: 2500, costo_neto: 1800, duracion_dias: 8 },
      { destino_id: destinos[5].id, categoria_id: catCultura.id, nombre: 'Roma Ancestral', precio_base: 1900, costo_neto: 1300, duracion_dias: 6 },
      { destino_id: destinos[0].id, categoria_id: catAventura.id, nombre: 'Salkantay Trek', precio_base: 850, costo_neto: 600, duracion_dias: 5 }
    ]);

    // ── Etapas Pipeline ──
    const etapas = await EtapaPipeline.bulkCreate([
      { nombre: 'Prospecto', orden: 1, color: '#94A3B8' },
      { nombre: 'Calificación', orden: 2, color: '#3B82F6' },
      { nombre: 'Propuesta Enviada', orden: 3, color: '#8B5CF6' },
      { nombre: 'Negociación', orden: 4, color: '#F59E0B' },
      { nombre: 'Cerrado Ganado', orden: 5, color: '#10B981' },
      { nombre: 'Cerrado Perdido', orden: 6, color: '#EF4444' }
    ]);

    // ── Métodos de Pago ──
    const mtp = await MetodoPago.bulkCreate([
      { nombre: 'Transferencia Bancaria' }, { nombre: 'Tarjeta de Crédito' }, { nombre: 'Efectivo' }
    ]);

    console.log('👥 Generando 20 clientes, oportunidades, reservas y transacciones...');
    
    // ── Proveedores ──
    const provTipos = ['Aerolínea', 'Hotel', 'Operadora', 'Seguro', 'Transporte', 'Otro'];
    const proveedores = [];
    for(let i=0; i<10; i++) {
       proveedores.push({
         nombre: `Proveedor ${i+1} Travel Cia`,
         tipo: provTipos[i % provTipos.length],
         contacto: `Contacto Manager ${i+1}`,
         email: `prov${i+1}@proveedor.com`,
         telefono: `+1000${i}555`,
         pais: paises[i % paises.length].nombre,
         activo: 1
       });
    }
    await Proveedor.bulkCreate(proveedores);

    // ── Campañas ──
    const campTipos = ['Email', 'WhatsApp', 'SMS', 'Redes Sociales', 'Otro'];
    const campEstados = ['Borrador', 'Activa', 'Pausada', 'Finalizada'];
    const campanas = [];
    for(let j=0; j<10; j++) {
       campanas.push({
         nombre: `Campaña ${j+1} - Promo Destinos`,
         tipo: campTipos[j % campTipos.length],
         estado: campEstados[j % campEstados.length],
         fecha_inicio: new Date(Date.now() - (j * 5 * 24 * 60 * 60 * 1000)),
         presupuesto: 500 + (j * 150),
         creado_por: admin.id
       });
    }
    await Campana.bulkCreate(campanas);

    const nombres = ['Lucía', 'Íñigo', 'Josué', 'Ángel', 'Sofía', 'Daniela', 'Raúl', 'Verónica', 'Andrés', 'Carmen', 'Santiago', 'Mónica', 'Felipe', 'Úrsula', 'Mateo', 'Beatriz', 'Ignacio', 'Leticia', 'Damián', 'Silvia'];
    const apellidos = ['García', 'Núñez', 'Suárez', 'López', 'Rodríguez', 'Pérez', 'Sánchez', 'Martínez', 'Torres', 'Ramírez', 'Díaz', 'Vásquez', 'Castro', 'Ortiz', 'Gómez', 'Ruiz', 'Morales', 'Jiménez', 'Cáceres', 'Vidal'];
    
    const clientes = [];
    for (let i = 0; i < 20; i++) {
      const c = await Cliente.create({
        nombre: nombres[i],
        apellido: apellidos[i],
        email: `cliente${i+1}@ejemplo.com`,
        telefono: `+51 987 654 3${i < 10 ? '0'+i : i}`,
        pais: i < 10 ? 'Perú' : 'Otros',
        agente_id: agentes[i % agentes.length].id,
        fuente_id: fuentes[i % fuentes.length].id,
        categoria: i % 4 === 0 ? 'VIP' : (i % 3 === 0 ? 'Recurrente' : 'Nuevo')
      });
      clientes.push(c);

      // ── Oportunidades ──
      const op = await Oportunidad.create({
        cliente_id: c.id,
        agente_id: c.agente_id,
        etapa_id: etapas[i % 5].id, // Distribuidas en el pipeline
        titulo: `Viaje a ${destinos[i % destinos.length].nombre}`,
        valor_estimado: 1000 + (i * 200),
        probabilidad: 20 + (i * 4),
        estado: i % 6 === 5 ? 'Perdida' : 'Activa',
        creado_en: new Date(Date.now() - (i * 15 * 24 * 60 * 60 * 1000)) // Hack temporal para Dashboard
      });

      // ── Reservas (Para los que están más avanzados o cerrados) ──
      if (i % 2 === 0) {
        const fechaBase = new Date();
        fechaBase.setMonth(fechaBase.getMonth() - (i % 8)); // Distribuir en los últimos 8 meses
        
        const res = await Reserva.create({
          cliente_id: c.id,
          agente_id: c.agente_id,
          paquete_id: paquetes[i % paquetes.length].id,
          codigo_reserva: `BK-${2024}${100+i}`,
          fecha_salida: new Date(fechaBase.getTime() + (30 * 24 * 60 * 60 * 1000)),
          precio_total: 1500 + (i * 100),
          total_final: 1500 + (i * 100),
          costo_neto: 1000 + (i * 50),
          estado: i % 5 === 0 ? 'Completada' : (i % 4 === 0 ? 'Cancelada' : 'Confirmada'),
          creado_en: fechaBase
        });

        // ── Pagos ──
        // Creamos al menos un pago para casi todas las reservas para asegurar volumen en el dashboard
        if (res.estado !== 'Cancelada' || i === 4) { // Forzamos un pago incluso en una cancelada para pruebas de flujo si i=4
          await Pago.create({
            reserva_id: res.id,
            metodo_id: mtp[i % mtp.length].id,
            monto: res.total_final * (0.3 + (i * 0.05)), // Variable
            estado: i % 3 === 0 ? 'Pendiente' : 'Verificado',
            fecha_pago: fechaBase
          });
        }

      }

      // ── Interacciones ──
      await Interaccion.create({
        cliente_id: c.id,
        usuario_id: c.agente_id,
        tipo: i % 3 === 0 ? 'WhatsApp' : (i % 2 === 0 ? 'Llamada' : 'Email'),
        descripcion: `Se contactó a ${c.nombre} para dar seguimiento a su interés en ${destinos[i % destinos.length].nombre}. Muestra mucho interés en paquetes de ${catAventura.nombre}.`,
        fecha: new Date(Date.now() - (i % 5 * 24 * 60 * 60 * 1000))
      });

      // ── Tareas ──
      // Modificamos el modulo para asegurar al menos 10 tareas (i % 2 garantiza 10 si i va hasta 19)
      if (i % 2 === 0) {
        await Tarea.create({
          asignado_a: c.agente_id,
          creado_por: admin.id,
          cliente_id: c.id,
          titulo: `Seguimiento de ${i % 2 === 0 ? 'Reserva' : 'Cotización'} - ${c.nombre}`,
          prioridad: i % 4 === 0 ? 'Urgente' : (i % 3 === 0 ? 'Alta' : 'Media'),
          fecha_vence: new Date(Date.now() + (i * 24 * 60 * 60 * 1000)),
          estado: i % 3 === 0 ? 'Completada' : 'Pendiente'
        });
      }

    }

    console.log('\n✨ ¡Proceso completado con éxito!');
    console.log('📊 Se generaron:');
    console.log(`  - 20 Clientes`);
    console.log(`  - 20 Oportunidades`);
    console.log(`  - 10 Reservas`);
    console.log(`  - 20 Interacciones`);
    console.log(`  - Caracteres especiales correctos (María, Úrsula, Íñigo, Perú, etc.)`);

  } catch (error) {
    console.error('\n❌ Error durante el proceso:', error);
  } finally {
    process.exit();
  }
}

reseed();
