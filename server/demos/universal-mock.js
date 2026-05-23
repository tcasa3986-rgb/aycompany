'use strict';
const express = require('express');
const app     = express();
const DEMO    = process.env.DEMO_NAME || 'default';
const PORT    = parseInt(process.env.PORT || '3000');

app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true }));

app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    res.header('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE,OPTIONS');
    if (req.method === 'OPTIONS') return res.sendStatus(204);
    next();
});

// ─── Datos iniciales por demo ──────────────────────────────────────────────────
const SEED = {
    viaje360: {
        _user: { id:1, nombre:'Administrador', email:'admin@viaje360.com', rol:'admin' },
        _dash: { ventas_mes:24500000, clientes_nuevos:12, paquetes_activos:18, reservas_pendientes:7 },
        paquetes: [
            { id:1, nombre:'Cartagena Mágica 5 días',   precio:1850000, destino:'Cartagena',  dias:5, cupos:20, estado:'activo' },
            { id:2, nombre:'San Andrés Paraíso 7 días', precio:3200000, destino:'San Andrés', dias:7, cupos:15, estado:'activo' },
            { id:3, nombre:'Medellín City Tour 3 días', precio:890000,  destino:'Medellín',   dias:3, cupos:30, estado:'activo' },
            { id:4, nombre:'Eje Cafetero 4 días',       precio:1350000, destino:'Armenia',    dias:4, cupos:25, estado:'activo' },
        ],
        clientes: [
            { id:1, nombre:'María González',   email:'maria@gmail.com',  telefono:'3001234567', ciudad:'Bogotá'   },
            { id:2, nombre:'Carlos Rodríguez', email:'carlos@gmail.com', telefono:'3109876543', ciudad:'Medellín' },
            { id:3, nombre:'Ana Martínez',     email:'ana@gmail.com',    telefono:'3207654321', ciudad:'Cali'     },
        ],
        reservas: [
            { id:1, cliente:'María González',  paquete:'Cartagena Mágica',   fecha_salida:'2026-06-15', personas:2, total:3700000, estado:'confirmada' },
            { id:2, cliente:'Carlos Rodríguez',paquete:'San Andrés Paraíso', fecha_salida:'2026-07-01', personas:3, total:9600000, estado:'pendiente'  },
        ],
        cotizaciones: [
            { id:1, cliente:'Ana Martínez', paquete:'Eje Cafetero 4 días', fecha:'2026-05-20', total:2700000, estado:'enviada' },
        ],
    },

    condominio: {
        _user: { id:1, nombre:'Administrador', email:'admin@laspalmas.com', rol:'admin' },
        _dash: { apartamentos:48, residentes:142, pagos_pendientes:8, mantenimientos:3, recaudado_mes:24000000 },
        apartamentos: [
            { id:1, numero:'101', torre:'A', area:68, propietario:'Juan Pérez',    telefono:'3001234567', estado:'ocupado',    cuota:350000 },
            { id:2, numero:'102', torre:'A', area:72, propietario:'Laura Gómez',   telefono:'3109876543', estado:'ocupado',    cuota:350000 },
            { id:3, numero:'201', torre:'A', area:68, propietario:'Pedro Sánchez', telefono:'3207654321', estado:'ocupado',    cuota:350000 },
            { id:4, numero:'202', torre:'A', area:80, propietario:'',              telefono:'',           estado:'disponible', cuota:400000 },
        ],
        pagos: [
            { id:1, apartamento:'101-A', concepto:'Administración Mayo', valor:350000, fecha:'2026-05-05', estado:'pagado'    },
            { id:2, apartamento:'102-A', concepto:'Administración Mayo', valor:350000, fecha:'2026-05-08', estado:'pagado'    },
            { id:3, apartamento:'201-A', concepto:'Administración Mayo', valor:350000, fecha:null,          estado:'pendiente' },
        ],
        mantenimientos: [
            { id:1, area:'Piscina',          descripcion:'Limpieza preventiva', fecha:'2026-05-20', estado:'completado', tecnico:'Miguel Torres'          },
            { id:2, area:'Ascensor Torre A', descripcion:'Revisión anual',      fecha:'2026-05-28', estado:'programado', tecnico:'Tecno Ascensores S.A.' },
        ],
        anuncios: [
            { id:1, titulo:'Reunión de copropietarios', contenido:'Sábado 25 de mayo a las 10am en el salón comunal.', fecha:'2026-05-20' },
        ],
    },

    odontologia: {
        _user: { id:1, nombre:'Dr. García Admin', email:'admin@clinica.com', rol:'admin' },
        _dash: { citas_hoy:8, pacientes:234, ingresos_mes:8500000, tratamientos_activos:15 },
        pacientes: [
            { id:1, nombre:'Ana López',     documento:'1020345678', telefono:'3001234567', email:'ana@gmail.com',    ultima_visita:'2026-04-10' },
            { id:2, nombre:'Carlos Moreno', documento:'1045678901', telefono:'3109876543', email:'carlos@gmail.com', ultima_visita:'2026-03-28' },
            { id:3, nombre:'María Torres',  documento:'1067890123', telefono:'3207654321', email:'maria@gmail.com',  ultima_visita:'2026-05-02' },
        ],
        citas: [
            { id:1, paciente:'Ana López',     doctor:'Dr. García', tratamiento:'Limpieza dental',    fecha:'2026-05-23', hora:'09:00', estado:'confirmada', valor:120000 },
            { id:2, paciente:'Carlos Moreno', doctor:'Dr. García', tratamiento:'Ortodoncia revisión',fecha:'2026-05-23', hora:'10:30', estado:'confirmada', valor:80000  },
            { id:3, paciente:'María Torres',  doctor:'Dra. López', tratamiento:'Extracción',         fecha:'2026-05-23', hora:'14:00', estado:'pendiente',  valor:150000 },
        ],
        tratamientos: [
            { id:1, nombre:'Limpieza dental',    precio:120000, duracion_min:45 },
            { id:2, nombre:'Extracción simple',  precio:150000, duracion_min:30 },
            { id:3, nombre:'Ortodoncia mensual', precio:80000,  duracion_min:30 },
            { id:4, nombre:'Blanqueamiento',     precio:350000, duracion_min:90 },
            { id:5, nombre:'Corona porcelana',   precio:800000, duracion_min:60 },
        ],
    },

    ventas: {
        _user: { id:1, nombre:'Admin Ventas', email:'admin@empresa.com', rol:'admin' },
        _dash: { ventas_mes:45800000, clientes:89, productos:156, pedidos_pendientes:12, facturas_vencidas:3 },
        productos: [
            { id:1, nombre:'Laptop Dell Inspiron 15',    precio:3200000, stock:8,  categoria:'Tecnología', codigo:'DELL-INS-15' },
            { id:2, nombre:'Mouse Inalámbrico Logitech', precio:85000,   stock:45, categoria:'Tecnología', codigo:'LOG-MOU-WL'  },
            { id:3, nombre:'Teclado Mecánico Redragon',  precio:180000,  stock:20, categoria:'Tecnología', codigo:'RED-TEC-MEC' },
            { id:4, nombre:'Monitor Samsung 24"',        precio:750000,  stock:12, categoria:'Tecnología', codigo:'SAM-MON-24'  },
            { id:5, nombre:'Silla ergonómica OfficePro', precio:680000,  stock:6,  categoria:'Mobiliario', codigo:'SIL-ERG-001' },
        ],
        clientes: [
            { id:1, nombre:'Tech Solutions S.A.S.',  nit:'900123456-1', email:'compras@techsol.com', telefono:'6015551234', ciudad:'Bogotá'   },
            { id:2, nombre:'Distribuidora El Éxito', nit:'800987654-2', email:'pedidos@exito.com',   telefono:'6045559876', ciudad:'Medellín' },
        ],
        facturas: [
            { id:1, numero:'FAC-2026-001', cliente:'Tech Solutions S.A.S.',  fecha:'2026-05-15', total:6450000, estado:'pagada'    },
            { id:2, numero:'FAC-2026-002', cliente:'Distribuidora El Éxito', fecha:'2026-05-18', total:3200000, estado:'pendiente' },
        ],
        cotizaciones: [
            { id:1, numero:'COT-2026-005', cliente:'Tech Solutions S.A.S.', fecha:'2026-05-22', total:1850000, estado:'enviada' },
        ],
    },

    ferreteria: {
        _user: { id:1, nombre:'Administrador', email:'admin@ferreteria.com', rol:'admin' },
        _dash: { ventas_hoy:2850000, productos:487, clientes:124, alertas_stock:8, ventas_mes:45000000 },
        productos: [
            { id:1, nombre:'Cemento Argos 50kg',     precio:35000,  stock:120, categoria:'Construcción', codigo:'CEM-ARG-50'  },
            { id:2, nombre:'Varilla corrugada 3/8"', precio:28000,  stock:250, categoria:'Hierro',       codigo:'VAR-COR-38'  },
            { id:3, nombre:'Pintura Viniltex 1gal',  precio:42000,  stock:60,  categoria:'Pintura',      codigo:'PIN-VIN-BL1' },
            { id:4, nombre:'Taladro DeWalt 3/8"',    precio:320000, stock:5,   categoria:'Herramientas', codigo:'TAL-DEW-38'  },
            { id:5, nombre:'Cable THW calibre 12',   precio:8500,   stock:500, categoria:'Eléctrico',    codigo:'CAB-THW-12'  },
        ],
        clientes: [
            { id:1, nombre:'Constructora Bolívar', nit:'900111222-1', telefono:'6015551234', ciudad:'Bogotá'   },
            { id:2, nombre:'Ferresurtidor Ltda.',  nit:'800333444-2', telefono:'6045554321', ciudad:'Medellín' },
        ],
        ventas: [
            { id:1, cliente:'Constructora Bolívar', fecha:'2026-05-23', total:1850000, estado:'completada', items:8 },
            { id:2, cliente:'Ferresurtidor Ltda.',  fecha:'2026-05-23', total:680000,  estado:'completada', items:3 },
        ],
    },

    polleria: {
        _user: { id:1, nombre:'Administrador', email:'admin@polleria.com', rol:'admin' },
        _dash: { ventas_hoy:1850000, pedidos_hoy:47, mesas_ocupadas:8, mesas_disponibles:4, ventas_mes:38000000 },
        productos: [
            { id:1, nombre:'Pollo a la brasa entero', precio:38000, categoria:'Pollos',  disponible:true,  activo:1 },
            { id:2, nombre:'1/2 Pollo a la brasa',   precio:22000, categoria:'Pollos',  disponible:true,  activo:1 },
            { id:3, nombre:'1/4 Pollo + papas',      precio:18000, categoria:'Combos',  disponible:true,  activo:1 },
            { id:4, nombre:'Alitas x 10',            precio:25000, categoria:'Alitas',  disponible:true,  activo:1 },
            { id:5, nombre:'Gaseosa 400ml',          precio:4000,  categoria:'Bebidas', disponible:true,  activo:1 },
        ],
        categorias: [
            { id:1, nombre:'Pollos',  activo:1 },
            { id:2, nombre:'Combos',  activo:1 },
            { id:3, nombre:'Alitas',  activo:1 },
            { id:4, nombre:'Bebidas', activo:1 },
        ],
        clientes: [
            { id:1, nombre:'Juan García',  telefono:'3001234567', email:'juan@gmail.com',  activo:1, segmento:'frecuente' },
            { id:2, nombre:'María López',  telefono:'3109876543', email:'maria@gmail.com', activo:1, segmento:'nuevo'    },
        ],
        proveedores: [
            { id:1, nombre:'Avícola San Pedro', telefono:'6015551234', ciudad:'Bogotá',   activo:1 },
            { id:2, nombre:'Distribuidora APC', telefono:'6045559876', ciudad:'Medellín', activo:1 },
        ],
        mesas: [
            { id:1, numero:1, capacidad:4, estado:'disponible' },
            { id:2, numero:2, capacidad:4, estado:'disponible' },
            { id:3, numero:3, capacidad:6, estado:'ocupada'    },
            { id:4, numero:4, capacidad:2, estado:'disponible' },
        ],
        pedidos: [
            { id:1, mesa:3, detalle:'1/2 Pollo + 2 Gaseosas', total:30000, estado:'entregado',  hora:'12:30' },
            { id:2, mesa:7, detalle:'Pollo entero + Gaseosas', total:50000, estado:'en_cocina', hora:'12:45' },
        ],
        ventas: [
            { id:1, fecha:'2026-05-23', total:380000, metodo_pago:'efectivo', items:12 },
            { id:2, fecha:'2026-05-22', total:420000, metodo_pago:'tarjeta',  items:15 },
        ],
        compras: [
            { id:1, proveedor:'Avícola San Pedro', total:1200000, fecha:'2026-05-20', estado:'recibida' },
        ],
        promociones: [
            { id:1, nombre:'Combo familiar', descripcion:'Pollo + 4 gaseosas', descuento:10, activo:true },
        ],
        usuarios: [
            { id:1, nombre:'Admin', email:'admin@polleria.com', rol:'admin', activo:1 },
        ],
    },

    salon: {
        _user: { id:1, nombre:'Administrador', email:'admin@salon.com', rol:'admin' },
        _dash: { citas_hoy:12, ingresos_mes:8500000, clientes:186, servicios:18, estilistas:4 },
        citas: [
            { id:1, cliente:'Ana Martínez',  servicio:'Corte y tintura',     estilista:'Valentina R.', fecha:'2026-05-23', hora:'09:00', estado:'confirmada', precio:120000 },
            { id:2, cliente:'Laura Sánchez', servicio:'Manicure + Pedicure', estilista:'Camila L.',    fecha:'2026-05-23', hora:'10:00', estado:'confirmada', precio:75000  },
            { id:3, cliente:'Sofía Reyes',   servicio:'Alisado keratina',    estilista:'Valentina R.', fecha:'2026-05-23', hora:'11:00', estado:'pendiente',  precio:280000 },
        ],
        servicios: [
            { id:1, nombre:'Corte de cabello',    precio:35000,  duracion:30  },
            { id:2, nombre:'Tintura completa',    precio:120000, duracion:120 },
            { id:3, nombre:'Manicure',            precio:35000,  duracion:45  },
            { id:4, nombre:'Pedicure',            precio:40000,  duracion:60  },
            { id:5, nombre:'Alisado keratina',    precio:280000, duracion:180 },
            { id:6, nombre:'Tratamiento capilar', precio:85000,  duracion:60  },
        ],
        clientes: [
            { id:1, nombre:'Ana Martínez',  telefono:'3001234567', email:'ana@gmail.com',   visitas:12 },
            { id:2, nombre:'Laura Sánchez', telefono:'3109876543', email:'laura@gmail.com', visitas:8  },
            { id:3, nombre:'Sofía Reyes',   telefono:'3207654321', email:'sofia@gmail.com', visitas:5  },
        ],
        usuarios: [
            { id:1, nombre:'Valentina Rodríguez', email:'valentina@salon.com', rol:'estilista', activo:true },
            { id:2, nombre:'Camila López',        email:'camila@salon.com',    rol:'estilista', activo:true },
        ],
    },

    parqueo: {
        _user: { id:1, nombre:'Administrador', email:'admin@parqueo.com', rol:'admin' },
        _dash: { vehiculos_adentro:23, ingresos_hoy:185000, ingresos_mes:4200000, espacios_libres:17 },
        vehiculos: [
            { id:1, placa:'ABC123', tipo:'carro', hora_entrada:'08:15', estado:'adentro', propietario:'Juan Pérez'   },
            { id:2, placa:'XYZ789', tipo:'moto',  hora_entrada:'09:30', estado:'adentro', propietario:'Carlos López' },
            { id:3, placa:'DEF456', tipo:'carro', hora_entrada:'07:45', estado:'adentro', propietario:'María García' },
        ],
        tarifas: [
            { id:1, tipo:'carro',     precio_hora:5000, precio_fraccion:2500 },
            { id:2, tipo:'moto',      precio_hora:2000, precio_fraccion:1000 },
            { id:3, tipo:'bicicleta', precio_hora:1000, precio_fraccion:500  },
        ],
        historial: [
            { id:1, placa:'HIJ321', tipo:'carro', entrada:'07:30', salida:'09:30', horas:2, total:10000 },
            { id:2, placa:'KLM654', tipo:'moto',  entrada:'08:00', salida:'09:00', horas:1, total:2000  },
        ],
    },

    prestamos: {
        _user: { id:1, nombre:'Administrador', email:'admin@prestamos.com', rol:'admin' },
        _dash: { prestamos_activos:34, cartera_total:185000000, pagos_hoy:3, en_mora:4, clientes:89 },
        clientes: [
            { id:1, nombre:'Hernando Vargas', documento:'1020345678', telefono:'3001234567', ciudad:'Bogotá',   estado:'activo' },
            { id:2, nombre:'Patricia Mora',   documento:'1045678901', telefono:'3109876543', ciudad:'Medellín', estado:'activo' },
            { id:3, nombre:'Luis Herrera',    documento:'1067890123', telefono:'3207654321', ciudad:'Cali',     estado:'activo' },
        ],
        prestamos: [
            { id:1, cliente:'Hernando Vargas', monto:5000000,  tasa:2.5, plazo:12, cuota:476000, saldo:3500000, estado:'al_dia' },
            { id:2, cliente:'Patricia Mora',   monto:10000000, tasa:2.5, plazo:24, cuota:590000, saldo:8200000, estado:'al_dia' },
        ],
        pagos: [
            { id:1, cliente:'Hernando Vargas', prestamo_id:1, valor:476000, fecha:'2026-05-05', estado:'aplicado' },
            { id:2, cliente:'Patricia Mora',   prestamo_id:2, valor:590000, fecha:'2026-05-10', estado:'aplicado' },
        ],
    },

    // ── Demos PHP convertidos a React SPA ──────────────────────────────────────
    delivery: {
        _user: { id:1, nombre:'Admin Delivery', email:'admin@delivery.com', rol:'admin' },
        _dash: { pedidos_hoy:34, en_camino:8, entregados:26, ingresos_hoy:1850000, repartidores:5 },
        pedidos: [
            { id:1, cliente:'Juan García',  direccion:'Calle 15 #8-45',     total:65000, estado:'en_camino',  repartidor:'Carlos M.', hora:'12:30' },
            { id:2, cliente:'María López',  direccion:'Carrera 7 #12-30',   total:45000, estado:'entregado',  repartidor:'Andrés P.', hora:'11:45' },
            { id:3, cliente:'Pedro Reyes',  direccion:'Trans. 6 #45-12',    total:32000, estado:'pendiente',  repartidor:'',          hora:'13:15' },
            { id:4, cliente:'Ana Torres',   direccion:'Av. Principal #200', total:78000, estado:'en_camino',  repartidor:'Luis G.',   hora:'12:50' },
        ],
        repartidores: [
            { id:1, nombre:'Carlos Martínez', telefono:'3001234567', zona:'Norte',  activo:true, pedidos_hoy:8 },
            { id:2, nombre:'Andrés Pérez',    telefono:'3109876543', zona:'Sur',    activo:true, pedidos_hoy:6 },
            { id:3, nombre:'Luis García',     telefono:'3207654321', zona:'Centro', activo:true, pedidos_hoy:4 },
        ],
        clientes: [
            { id:1, nombre:'Juan García', telefono:'3001234561', direccion:'Calle 15 #8-45',     pedidos:12 },
            { id:2, nombre:'María López', telefono:'3109876541', direccion:'Carrera 7 #12-30',   pedidos:8  },
            { id:3, nombre:'Ana Torres',  telefono:'3207654321', direccion:'Av. Principal #200', pedidos:5  },
        ],
        productos: [
            { id:1, nombre:'Hamburguesa Classic', precio:18000, categoria:'Hamburguesas', disponible:true },
            { id:2, nombre:'Pizza Pepperoni',     precio:35000, categoria:'Pizzas',       disponible:true },
            { id:3, nombre:'Pollo Broaster',      precio:22000, categoria:'Pollos',       disponible:true },
            { id:4, nombre:'Gaseosa 400ml',       precio:4000,  categoria:'Bebidas',      disponible:true },
        ],
    },

    celulares: {
        _user: { id:1, nombre:'Administrador', email:'admin@celulares.com', rol:'admin' },
        _dash: { ventas_hoy:4500000, productos:245, clientes:89, garantias_activas:23 },
        productos: [
            { id:1, nombre:'Samsung Galaxy A55',    marca:'Samsung',  precio:1350000, stock:8,  imei:'358012345678901' },
            { id:2, nombre:'iPhone 15',             marca:'Apple',    precio:4200000, stock:3,  imei:'356123456789012' },
            { id:3, nombre:'Xiaomi Redmi Note 13',  marca:'Xiaomi',   precio:890000,  stock:15, imei:'869123456789014' },
            { id:4, nombre:'Funda protectora univ', marca:'Genérico', precio:25000,   stock:50, imei:'-'              },
            { id:5, nombre:'Audífonos Bluetooth',   marca:'JBL',      precio:150000,  stock:20, imei:'-'              },
        ],
        ventas: [
            { id:1, cliente:'Pedro Gómez', producto:'Samsung Galaxy A55', precio:1350000, fecha:'2026-05-23', estado:'completada' },
            { id:2, cliente:'Ana Torres',  producto:'Audífonos Bluetooth', precio:150000, fecha:'2026-05-22', estado:'completada' },
        ],
        reparaciones: [
            { id:1, cliente:'Juan López',  equipo:'iPhone 14',    falla:'Pantalla rota', estado:'en_proceso', costo:350000 },
            { id:2, cliente:'María García',equipo:'Samsung A52',  falla:'No carga',      estado:'listo',      costo:80000  },
        ],
        clientes: [
            { id:1, nombre:'Pedro Gómez', telefono:'3001234567', email:'pedro@gmail.com' },
            { id:2, nombre:'Ana Torres',  telefono:'3109876543', email:'ana@gmail.com'   },
        ],
    },

    colegio: {
        _user: { id:1, nombre:'Rector Admin', email:'admin@colegio.com', rol:'admin' },
        _dash: { estudiantes:485, profesores:24, cursos:18, pagos_pendientes:45 },
        estudiantes: [
            { id:1, nombre:'Valentina Ramírez', grado:'9°',  documento:'1020345678', acudiente:'Carmen Ramírez', telefono:'3001234567' },
            { id:2, nombre:'Santiago Pérez',    grado:'10°', documento:'1045678901', acudiente:'Roberto Pérez',  telefono:'3109876543' },
            { id:3, nombre:'Isabella García',   grado:'8°',  documento:'1067890123', acudiente:'Patricia García',telefono:'3207654321' },
            { id:4, nombre:'Sebastián López',   grado:'11°', documento:'1089012345', acudiente:'Jorge López',    telefono:'3301234567' },
        ],
        profesores: [
            { id:1, nombre:'Carlos Mendoza', materia:'Matemáticas', email:'c.mendoza@colegio.com', telefono:'3001111111' },
            { id:2, nombre:'Adriana Rojas',  materia:'Español',     email:'a.rojas@colegio.com',   telefono:'3002222222' },
            { id:3, nombre:'Felipe Torres',  materia:'Ciencias',    email:'f.torres@colegio.com',   telefono:'3003333333' },
        ],
        cursos: [
            { id:1, nombre:'Matemáticas 9°', grado:'9°',  profesor:'Carlos Mendoza', estudiantes:28 },
            { id:2, nombre:'Español 10°',    grado:'10°', profesor:'Adriana Rojas',  estudiantes:30 },
            { id:3, nombre:'Ciencias 8°',    grado:'8°',  profesor:'Felipe Torres',  estudiantes:25 },
        ],
        pagos: [
            { id:1, estudiante:'Valentina Ramírez', concepto:'Pensión Mayo', valor:320000, fecha:'2026-05-05', estado:'pagado'    },
            { id:2, estudiante:'Santiago Pérez',    concepto:'Pensión Mayo', valor:320000, fecha:null,          estado:'pendiente' },
        ],
    },

    farmacia: {
        _user: { id:1, nombre:'Administrador', email:'admin@farmacia.com', rol:'admin' },
        _dash: { ventas_hoy:2850000, medicamentos:856, clientes:234, vencimientos_proximos:12 },
        medicamentos: [
            { id:1, nombre:'Acetaminofén 500mg x100', laboratorio:'Genfar',    precio:12000, stock:150, vencimiento:'2027-03-01' },
            { id:2, nombre:'Ibuprofeno 400mg x50',    laboratorio:'Lafrancol', precio:18000, stock:80,  vencimiento:'2027-06-01' },
            { id:3, nombre:'Amoxicilina 500mg x21',   laboratorio:'Pfizer',    precio:35000, stock:45,  vencimiento:'2026-12-01' },
            { id:4, nombre:'Metformina 850mg x30',    laboratorio:'Genfar',    precio:22000, stock:120, vencimiento:'2027-01-01' },
            { id:5, nombre:'Losartan 50mg x30',       laboratorio:'Lafrancol', precio:28000, stock:95,  vencimiento:'2026-11-01' },
        ],
        ventas: [
            { id:1, cliente:'Ana Martínez',  medicamento:'Acetaminofén 500mg', cantidad:2, total:24000, fecha:'2026-05-23' },
            { id:2, cliente:'Carlos López',  medicamento:'Ibuprofeno 400mg',   cantidad:1, total:18000, fecha:'2026-05-23' },
        ],
        proveedores: [
            { id:1, nombre:'Drogas La Rebaja', telefono:'6015551234', ciudad:'Bogotá',   contacto:'Juan Herrera' },
            { id:2, nombre:'COPIDROGAS',       telefono:'6015554321', ciudad:'Bogotá',   contacto:'Martha Soto'  },
        ],
        alertas: [
            { id:1, medicamento:'Amoxicilina 500mg', stock_actual:5, stock_minimo:10, estado:'critico' },
            { id:2, medicamento:'Bisoprolol 5mg',    stock_actual:8, stock_minimo:15, estado:'bajo'    },
        ],
    },

    panaderia: {
        _user: { id:1, nombre:'Administrador', email:'admin@panaderia.com', rol:'admin' },
        _dash: { ventas_hoy:850000, productos:45, pedidos_pendientes:8, clientes:156 },
        productos: [
            { id:1, nombre:'Pan tajado blanco',   precio:8500,  categoria:'Panes',      disponible:true },
            { id:2, nombre:'Croissant mantequilla',precio:4500, categoria:'Croissants', disponible:true },
            { id:3, nombre:'Torta de cumpleaños', precio:85000, categoria:'Tortas',     disponible:true },
            { id:4, nombre:'Pandebono x6',        precio:8000,  categoria:'Tradicional',disponible:true },
            { id:5, nombre:'Almojábana x6',       precio:7500,  categoria:'Tradicional',disponible:true },
        ],
        pedidos: [
            { id:1, cliente:'Hotel Dann',         total:450000, fecha:'2026-05-23', estado:'listo'          },
            { id:2, cliente:'Supermercado La 14', total:280000, fecha:'2026-05-23', estado:'en_preparacion' },
            { id:3, cliente:'Cafetería Central',  total:120000, fecha:'2026-05-23', estado:'pendiente'      },
        ],
        insumos: [
            { id:1, nombre:'Harina de trigo', unidad:'kg',     stock:200, precio_kg:3200,  proveedor:'Molinos Roa'    },
            { id:2, nombre:'Azúcar',          unidad:'kg',     stock:80,  precio_kg:2800,  proveedor:'Providencia'    },
            { id:3, nombre:'Mantequilla',     unidad:'kg',     stock:25,  precio_kg:18000, proveedor:'Colanta'        },
            { id:4, nombre:'Huevos',          unidad:'unidad', stock:360, precio_kg:550,   proveedor:'Avicola S.Pedro'},
        ],
        clientes: [
            { id:1, nombre:'Hotel Dann',        telefono:'6015551111', pedidos_mes:8  },
            { id:2, nombre:'Cafetería Central', telefono:'6015552222', pedidos_mes:20 },
        ],
    },

    restaurante: {
        _user: { id:1, nombre:'Administrador', email:'admin@restaurante.com', rol:'admin' },
        _dash: { mesas_ocupadas:8, pedidos_activos:12, ventas_hoy:1850000, clientes_hoy:48 },
        mesas: [
            { id:1, numero:1, capacidad:4, estado:'ocupada',    mesero:'Juan'  },
            { id:2, numero:2, capacidad:4, estado:'disponible', mesero:''      },
            { id:3, numero:3, capacidad:6, estado:'ocupada',    mesero:'María' },
            { id:4, numero:4, capacidad:2, estado:'disponible', mesero:''      },
            { id:5, numero:5, capacidad:8, estado:'disponible', mesero:''      },
            { id:6, numero:6, capacidad:4, estado:'ocupada',    mesero:'Juan'  },
        ],
        pedidos: [
            { id:1, mesa:1, detalle:'Bandeja paisa x2, Jugos x2',       total:68000,  estado:'entregado', hora:'12:30' },
            { id:2, mesa:3, detalle:'Sancocho x3, Gaseosas x3',         total:85000,  estado:'en_cocina', hora:'13:00' },
            { id:3, mesa:6, detalle:'Cazuela de mariscos x2',           total:120000, estado:'servido',   hora:'13:15' },
        ],
        productos: [
            { id:1, nombre:'Bandeja paisa',        precio:28000, categoria:'Platos fuertes', disponible:true },
            { id:2, nombre:'Sancocho de gallina',  precio:25000, categoria:'Sopas',          disponible:true },
            { id:3, nombre:'Cazuela de mariscos',  precio:45000, categoria:'Especiales',     disponible:true },
            { id:4, nombre:'Jugo natural',         precio:8000,  categoria:'Bebidas',        disponible:true },
            { id:5, nombre:'Gaseosa 400ml',        precio:5000,  categoria:'Bebidas',        disponible:true },
        ],
        meseros: [
            { id:1, nombre:'Juan Ramírez', turno:'Almuerzo', mesas_asignadas:'1,6' },
            { id:2, nombre:'María López',  turno:'Almuerzo', mesas_asignadas:'3,4' },
        ],
    },

    citas: {
        _user: { id:1, nombre:'Administrador', email:'admin@citas.com', rol:'admin' },
        _dash: { citas_hoy:18, en_espera:3, completadas:12, ingresos_hoy:1250000 },
        citas: [
            { id:1, paciente:'Laura Medina',  doctor:'Dr. Acosta',  servicio:'Consulta general',  fecha:'2026-05-23', hora:'09:00', estado:'confirmada'  },
            { id:2, paciente:'Carlos Suárez', doctor:'Dra. Vargas', servicio:'Examen preventivo', fecha:'2026-05-23', hora:'10:00', estado:'en_atencion' },
            { id:3, paciente:'Sofía Castro',  doctor:'Dr. Acosta',  servicio:'Control',           fecha:'2026-05-23', hora:'11:00', estado:'pendiente'   },
            { id:4, paciente:'Miguel Torres', doctor:'Dra. Vargas', servicio:'Vacunación',        fecha:'2026-05-24', hora:'09:00', estado:'confirmada'  },
        ],
        doctores: [
            { id:1, nombre:'Dr. Acosta',  especialidad:'Medicina General', disponibilidad:'Lun-Vie 8am-4pm'  },
            { id:2, nombre:'Dra. Vargas', especialidad:'Pediatría',        disponibilidad:'Lun-Sab 9am-5pm'  },
        ],
        pacientes: [
            { id:1, nombre:'Laura Medina',  documento:'1020345678', telefono:'3001234567', email:'laura@gmail.com'  },
            { id:2, nombre:'Carlos Suárez', documento:'1045678901', telefono:'3109876543', email:'carlos@gmail.com' },
            { id:3, nombre:'Sofía Castro',  documento:'1067890123', telefono:'3207654321', email:'sofia@gmail.com'  },
        ],
        servicios: [
            { id:1, nombre:'Consulta general',    duracion:30, precio:80000  },
            { id:2, nombre:'Examen preventivo',   duracion:45, precio:120000 },
            { id:3, nombre:'Control crecimiento', duracion:20, precio:60000  },
            { id:4, nombre:'Vacunación',          duracion:15, precio:45000  },
        ],
    },

    hospedaje: {
        _user: { id:1, nombre:'Recepcionista', email:'admin@hospedaje.com', rol:'admin' },
        _dash: { habitaciones_ocupadas:12, disponibles:8, reservas_hoy:3, ingresos_mes:18500000 },
        habitaciones: [
            { id:1, numero:'101', tipo:'Individual', precio:120000, estado:'disponible'    },
            { id:2, numero:'102', tipo:'Doble',      precio:180000, estado:'ocupada'       },
            { id:3, numero:'201', tipo:'Suite',      precio:350000, estado:'ocupada'       },
            { id:4, numero:'202', tipo:'Doble',      precio:180000, estado:'disponible'    },
            { id:5, numero:'301', tipo:'Individual', precio:120000, estado:'mantenimiento' },
        ],
        reservas: [
            { id:1, huesped:'Roberto Díaz', habitacion:'102', ingreso:'2026-05-22', salida:'2026-05-25', total:540000,  estado:'activa'     },
            { id:2, huesped:'Ana López',    habitacion:'201', ingreso:'2026-05-21', salida:'2026-05-24', total:1050000, estado:'activa'     },
            { id:3, huesped:'Carlos Mora',  habitacion:'101', ingreso:'2026-05-26', salida:'2026-05-28', total:240000,  estado:'confirmada' },
        ],
        huespedes: [
            { id:1, nombre:'Roberto Díaz', documento:'1020345678', telefono:'3001234567', pais:'Colombia'  },
            { id:2, nombre:'Ana López',    documento:'1045678901', telefono:'3109876543', pais:'Venezuela' },
        ],
        servicios: [
            { id:1, nombre:'Desayuno continental', precio:25000, descripcion:'Incluido en suites' },
            { id:2, nombre:'Parking',              precio:15000, descripcion:'Por día'            },
            { id:3, nombre:'Lavandería',           precio:35000, descripcion:'Por kilogramo'      },
        ],
    },

    inventario: {
        _user: { id:1, nombre:'Almacenista', email:'admin@inventario.com', rol:'admin' },
        _dash: { productos_totales:1245, valor_inventario:285000000, alertas_stock:18, movimientos_hoy:34 },
        productos: [
            { id:1, codigo:'PROD-001', nombre:'Cemento Argos 50kg',    categoria:'Construcción', stock:250, precio:35000, bodega:'Principal'  },
            { id:2, codigo:'PROD-002', nombre:'Varilla 3/8" x6m',      categoria:'Hierro',       stock:180, precio:28000, bodega:'Principal'  },
            { id:3, codigo:'PROD-003', nombre:'Pintura blanca 1gl',    categoria:'Pintura',      stock:45,  precio:42000, bodega:'Secundaria' },
            { id:4, codigo:'PROD-004', nombre:'Cable eléctrico Cal.12', categoria:'Eléctrico',   stock:800, precio:4500,  bodega:'Principal'  },
        ],
        entradas: [
            { id:1, producto:'Cemento Argos 50kg', cantidad:100, proveedor:'Argos Colombia', fecha:'2026-05-20', costo:3200000 },
            { id:2, producto:'Varilla 3/8"',       cantidad:50,  proveedor:'Ferrasa S.A.',   fecha:'2026-05-21', costo:1250000 },
        ],
        salidas: [
            { id:1, producto:'Cemento Argos 50kg',    cantidad:20,  destino:'Obra Calle 15', fecha:'2026-05-22', motivo:'Venta' },
            { id:2, producto:'Cable eléctrico Cal.12', cantidad:100, destino:'Cliente ACME', fecha:'2026-05-23', motivo:'Venta' },
        ],
        proveedores: [
            { id:1, nombre:'Argos Colombia', nit:'890903939-2', telefono:'6014441111', ciudad:'Medellín' },
            { id:2, nombre:'Ferrasa S.A.',   nit:'890903940-1', telefono:'6014442222', ciudad:'Bogotá'   },
        ],
    },

    laboratorio: {
        _user: { id:1, nombre:'Jefe Lab.', email:'admin@laboratorio.com', rol:'admin' },
        _dash: { examenes_hoy:28, pendientes:8, listos:20, ingresos_hoy:3450000 },
        examenes: [
            { id:1, paciente:'María Gutiérrez', tipo:'Hemograma completo', estado:'listo',      fecha:'2026-05-23', resultado:'Normal' },
            { id:2, paciente:'Luis Vargas',     tipo:'Glicemia en ayunas', estado:'en_proceso', fecha:'2026-05-23', resultado:'-'      },
            { id:3, paciente:'Carmen Díaz',     tipo:'Perfil lipídico',    estado:'pendiente',  fecha:'2026-05-23', resultado:'-'      },
            { id:4, paciente:'Roberto Soto',    tipo:'Parcial de orina',   estado:'listo',      fecha:'2026-05-23', resultado:'Normal' },
        ],
        pacientes: [
            { id:1, nombre:'María Gutiérrez', documento:'1020345678', telefono:'3001234567', medico:'Dr. Pérez' },
            { id:2, nombre:'Luis Vargas',     documento:'1045678901', telefono:'3109876543', medico:'Dra. Soto' },
        ],
        resultados: [
            { id:1, examen:'Hemograma completo', paciente:'María Gutiérrez', valor:'Normal', referencia:'Normal', estado:'entregado' },
            { id:2, examen:'Parcial de orina',   paciente:'Roberto Soto',    valor:'Normal', referencia:'Normal', estado:'entregado' },
        ],
        equipos: [
            { id:1, nombre:'Analizador hematológico', marca:'Sysmex', ultimo_mantenimiento:'2026-04-01', estado:'operativo' },
            { id:2, nombre:'Microscopio binocular',   marca:'Zeiss',  ultimo_mantenimiento:'2026-03-15', estado:'operativo' },
        ],
    },

    cotizacion: {
        _user: { id:1, nombre:'Comercial', email:'admin@cotizacion.com', rol:'admin' },
        _dash: { cotizaciones_mes:45, aprobadas:28, pendientes:12, valor_total:185000000 },
        cotizaciones: [
            { id:1, numero:'COT-2026-045', cliente:'Tech Solutions S.A.S.',  fecha:'2026-05-23', total:8500000,  estado:'enviada',   validez:'2026-06-23' },
            { id:2, numero:'COT-2026-044', cliente:'Constructora Bolívar',   fecha:'2026-05-22', total:45000000, estado:'aprobada',  validez:'2026-06-22' },
            { id:3, numero:'COT-2026-043', cliente:'Distribuidora Nacional', fecha:'2026-05-20', total:12500000, estado:'rechazada', validez:'2026-06-20' },
        ],
        clientes: [
            { id:1, nombre:'Tech Solutions S.A.S.', nit:'900123456-1', email:'compras@techsol.com',      telefono:'6015551234', ciudad:'Bogotá'   },
            { id:2, nombre:'Constructora Bolívar',  nit:'900789012-3', email:'licitaciones@bolivar.com', telefono:'6045559876', ciudad:'Medellín' },
        ],
        productos: [
            { id:1, nombre:'Consultoría tecnológica', codigo:'CONS-001', precio:5000000, unidad:'mes',     descripcion:'Consultoría IT mensual' },
            { id:2, nombre:'Soporte técnico',         codigo:'SOPO-001', precio:1500000, unidad:'mes',     descripcion:'Soporte 8x5'           },
            { id:3, nombre:'Desarrollo software',     codigo:'DESA-001', precio:8000000, unidad:'proyecto',descripcion:'Desarrollo a medida'   },
        ],
        configuracion: [
            { id:1, empresa:'AI Company CO', nit:'900123456-7', email:'info@aicompany.co', telefono:'6015551234' },
        ],
    },
};

// ─── Inicializar store en memoria ──────────────────────────────────────────────
const cfg   = SEED[DEMO] || {};
const store = {};
let   nextId = 1000;

for (const [k, v] of Object.entries(cfg)) {
    if (!k.startsWith('_') && Array.isArray(v)) store[k] = v.map(x => ({ ...x }));
}

const demoUser = cfg._user || { id:1, nombre:'Admin Demo', email:'admin@demo.com', rol:'admin' };
const demoDash = cfg._dash || {};

function lst(r) { if (!store[r]) store[r] = []; return store[r]; }

// ─── Helpers de respuesta ──────────────────────────────────────────────────────
const ok  = (res, data, extra={}) => res.json({ ok:true, success:true, data, ...extra });
const okL = (res, r)  => { const l = lst(r); res.json({ ok:true, success:true, data:l, [r]:l, total:l.length, count:l.length }); };

// ─── Auth — siempre exitoso ────────────────────────────────────────────────────
const token = () => 'demo-' + Date.now();
const loginResp = (res) => res.json({
    ok:true, success:true,
    token:token(), access_token:token(),
    user:demoUser, usuario:demoUser, data:demoUser
});

app.post(['/api/auth/login','/api/login','/api/usuarios/login','/api/users/login',
          '/api/auth/register','/api/register'], (_, res) => loginResp(res));
app.post(['/api/auth/logout','/api/logout'], (_, res) => res.json({ ok:true }));
app.get(['/api/auth/me','/api/me','/api/usuarios/me','/api/user'],
        (_, res) => res.json({ ok:true, ...demoUser, user:demoUser, usuario:demoUser }));

// ─── Endpoints específicos por demo (ANTES de los genéricos) ──────────────────

// ── Salon ──────────────────────────────────────────────────────────────────────
if (DEMO === 'salon') {
    app.get('/api/dashboard/stats', (_, res) => res.json({
        ok:true,
        ingresos: demoDash.ingresos_mes || 8500000,
        clientes: lst('clientes').length || 186,
        citas: {
            total:      lst('citas').length || 12,
            completadas:lst('citas').filter(c => c.estado === 'completada').length || 8,
            canceladas: lst('citas').filter(c => c.estado === 'cancelada').length  || 2,
        },
        ingresosMensuales: [
            { mes:'Ene', total:6800000 }, { mes:'Feb', total:7200000 },
            { mes:'Mar', total:7800000 }, { mes:'Abr', total:8100000 },
            { mes:'May', total:8500000 },
        ],
    }));
    app.get('/api/reportes', (req, res) => res.json({
        ok:true, data:{ ingresos:8500000, citas:45, clientes_nuevos:12 }
    }));
    app.get('/api/configuracion', (_, res) => res.json({
        ok:true, data:{ nombre:'Salón Beauty', telefono:'3001234567', direccion:'Calle 15 #8-45', moneda:'COP' }
    }));
    app.put('/api/configuracion', (req, res) => res.json({ ok:true, success:true, data:req.body }));
    app.get('/api/notificaciones/config', (_, res) => res.json({
        ok:true, data:{ email:true, sms:false, whatsapp:true }
    }));
    app.put('/api/notificaciones/config', (req, res) => res.json({ ok:true, success:true }));
    app.get('/api/mantenimiento', (_, res) => okL(res, 'mantenimiento'));
    app.get('/api/inventario',    (_, res) => okL(res, 'inventario_items'));
    app.get('/api/gastos',        (_, res) => okL(res, 'gastos'));
    app.get('/api/ventas',        (_, res) => okL(res, 'ventas'));
    app.get('/api/database/backup', (_, res) => { res.setHeader('Content-Type','application/json'); res.send('{}'); });
    app.post('/api/database/reset',   (_, res) => res.json({ ok:true, success:true }));
    app.post('/api/database/restore', (_, res) => res.json({ ok:true, success:true }));
}

// ── Polleria ───────────────────────────────────────────────────────────────────
if (DEMO === 'polleria') {
    const dias = ['2026-05-17','2026-05-18','2026-05-19','2026-05-20','2026-05-21','2026-05-22','2026-05-23'];
    const ventasPorDia = dias.map(dia => ({ dia, total: Math.floor(Math.random()*400000)+800000 }));

    app.get('/api/reportes/resumen', (_, res) => res.json({
        ok:true,
        data: {
            ventasPorDia,
            topProductos: [
                { producto:{ nombre:'Pollo a la brasa entero' }, total_cantidad:89  },
                { producto:{ nombre:'1/2 Pollo a la brasa'   }, total_cantidad:145 },
                { producto:{ nombre:'1/4 Pollo + papas'      }, total_cantidad:210 },
            ],
            ventasPorMetodo: [
                { metodo_pago:'efectivo',     total:18000000 },
                { metodo_pago:'tarjeta',      total:12000000 },
                { metodo_pago:'transferencia',total:8000000  },
            ],
            totalVentas:   { total_sum:38000000, count:950 },
            totalClientes: 87,
        }
    }));
    app.get('/api/reportes/rentabilidad', (_, res) => res.json({
        ok:true, data:{ margen:35, costos:12000000, ingresos:38000000, utilidad:8000000 }
    }));
    app.get('/api/reportes/clientes-top', (_, res) => res.json({
        ok:true, data:[
            { cliente:{ nombre:'Juan García'  }, total_compras:450000 },
            { cliente:{ nombre:'María López'  }, total_compras:380000 },
        ]
    }));
    app.get('/api/caja/activa', (_, res) => res.json({
        ok:true,
        data:{ id:1, saldo_inicial:200000, fecha_apertura:'2026-05-23', estado:'abierta',
               total_ventas:1850000, total_egresos:50000 }
    }));
    app.get('/api/caja/egresos', (_, res) => res.json({ ok:true, data:[
        { id:1, concepto:'Compra insumos', monto:150000, hora:'09:00' }
    ]}));
    app.post('/api/caja/abrir',       (req, res) => res.json({ ok:true, success:true, data:{ id:2, saldo_inicial:req.body.saldo_inicial||200000, estado:'abierta' } }));
    app.put('/api/caja/:id/cerrar',   (_, res)   => res.json({ ok:true, success:true }));
    app.post('/api/caja/egresos',     (req, res) => res.json({ ok:true, success:true, data:{ id:Date.now(), ...req.body } }));
    app.get('/api/configuracion', (_, res) => res.json({
        ok:true, configuracion:{ nombre:'Pollería El Dorado', telefono:'3001234567', direccion:'Av. Principal 123' }
    }));
    app.put('/api/configuracion', (req, res) => res.json({ ok:true, success:true }));
    app.get('/api/logs', (_, res) => res.json({ ok:true, data:[
        { id:1, usuario:'Admin', accion:'login', fecha:'2026-05-23 08:00' }
    ]}));
    app.post('/api/crm/actualizar-segmentos', (_, res) => res.json({ ok:true, success:true }));
    app.post('/api/crm/campana-email',        (_, res) => res.json({ ok:true, success:true }));
    app.get('/api/crm/interacciones/:id',     (_, res) => res.json({ ok:true, data:[] }));
    app.post('/api/crm/interacciones',        (req, res) => res.json({ ok:true, success:true, data:{ id:Date.now(), ...req.body } }));
    app.put('/api/ventas/:id/anular',         (_, res) => res.json({ ok:true, success:true }));
    app.post('/api/ventas/:id/imprimir',      (_, res) => res.json({ ok:true, success:true }));
    app.put('/api/pedidos/:id/estado',        (req, res) => res.json({ ok:true, success:true }));
    app.get('/api/productos/movimientos',     (_, res) => res.json({ ok:true, data:[] }));
    app.put('/api/productos/:id/ajustar-stock',(req, res) => res.json({ ok:true, success:true }));
}

// ─── Dashboard genérico ─────────────────────────────────────────────────────────
const dashR = (_, res) => res.json({ ok:true, success:true, ...demoDash, data:demoDash });
app.get(['/api/dashboard','/api/stats','/api/resumen','/api/home',
         '/api/metricas','/api/reporte'], dashR);
app.get('/api/reportes', dashR);

// ─── CRUD genérico ─────────────────────────────────────────────────────────────
// GET lista
app.get('/api/:r', (req, res) => okL(res, req.params.r));

// GET uno o sub-ruta
app.get('/api/:r/:id', (req, res) => {
    if (isNaN(Number(req.params.id))) return okL(res, req.params.r);
    const item = lst(req.params.r).find(x => String(x.id) === req.params.id);
    return item ? ok(res, item) : res.json({ ok:true, data:null });
});

// GET sub-recurso  /api/:r/:id/:sub
app.get('/api/:r/:id/:sub', (req, res) => {
    const l = lst(`${req.params.r}_${req.params.sub}`);
    res.json({ ok:true, data:l, total:l.length });
});

// POST crear
app.post('/api/:r', (req, res) => {
    const l = lst(req.params.r);
    const item = { id:++nextId, ...req.body, creado_en:new Date().toISOString() };
    l.push(item);
    res.status(201).json({ ok:true, success:true, data:item, id:item.id, message:'Guardado correctamente' });
});

// PUT / PATCH actualizar
const upd = (req, res) => {
    const l = lst(req.params.r);
    const i = l.findIndex(x => String(x.id) === req.params.id);
    if (i >= 0) l[i] = { ...l[i], ...req.body };
    res.json({ ok:true, success:true, data: i >= 0 ? l[i] : req.body, message:'Actualizado correctamente' });
};
app.put('/api/:r/:id',   upd);
app.patch('/api/:r/:id', upd);

// DELETE
app.delete('/api/:r/:id', (req, res) => {
    const l = lst(req.params.r);
    const i = l.findIndex(x => String(x.id) === req.params.id);
    if (i >= 0) l.splice(i, 1);
    res.json({ ok:true, success:true, message:'Eliminado correctamente' });
});

// ─── Catch-all ─────────────────────────────────────────────────────────────────
app.all('*', (_, res) => res.json({ ok:true, success:true, data:[], total:0, message:'Demo activo' }));

app.listen(PORT, () => console.log(`[mock:${DEMO}] Puerto ${PORT} — datos en memoria`));
