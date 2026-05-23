'use strict';
/**
 * Servidor mock universal para todos los demos.
 * - Login siempre funciona
 * - Datos realistas precargados por demo
 * - CRUD en memoria: guarda durante la sesión, se borra al reiniciar
 * Uso: PORT=5200 DEMO_NAME=viaje360 node universal-mock.js
 */
const express = require('express');
const app     = express();
const DEMO    = process.env.DEMO_NAME || 'default';
const PORT    = parseInt(process.env.PORT || '3000');

app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true }));

// CORS abierto para demos
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
            { id:1, nombre:'Cartagena Mágica 5 días',    precio:1850000, destino:'Cartagena',  dias:5, cupos:20, estado:'activo' },
            { id:2, nombre:'San Andrés Paraíso 7 días',  precio:3200000, destino:'San Andrés', dias:7, cupos:15, estado:'activo' },
            { id:3, nombre:'Medellín City Tour 3 días',  precio:890000,  destino:'Medellín',   dias:3, cupos:30, estado:'activo' },
            { id:4, nombre:'Eje Cafetero 4 días',        precio:1350000, destino:'Armenia',    dias:4, cupos:25, estado:'activo' },
        ],
        clientes: [
            { id:1, nombre:'María González',   email:'maria@gmail.com',   telefono:'3001234567', ciudad:'Bogotá'   },
            { id:2, nombre:'Carlos Rodríguez', email:'carlos@gmail.com',  telefono:'3109876543', ciudad:'Medellín' },
            { id:3, nombre:'Ana Martínez',     email:'ana@gmail.com',     telefono:'3207654321', ciudad:'Cali'     },
        ],
        reservas: [
            { id:1, cliente:'María González',   paquete:'Cartagena Mágica',     fecha_salida:'2026-06-15', personas:2, total:3700000, estado:'confirmada' },
            { id:2, nombre:'Carlos Rodríguez',  paquete:'San Andrés Paraíso',   fecha_salida:'2026-07-01', personas:3, total:9600000, estado:'pendiente'  },
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
            { id:4, numero:'202', torre:'A', area:80, propietario:'',              telefono:'',            estado:'disponible', cuota:400000 },
        ],
        pagos: [
            { id:1, apartamento:'101-A', concepto:'Administración Mayo', valor:350000, fecha:'2026-05-05', estado:'pagado'   },
            { id:2, apartamento:'102-A', concepto:'Administración Mayo', valor:350000, fecha:'2026-05-08', estado:'pagado'   },
            { id:3, apartamento:'201-A', concepto:'Administración Mayo', valor:350000, fecha:null,          estado:'pendiente'},
        ],
        mantenimientos: [
            { id:1, area:'Piscina',         descripcion:'Limpieza preventiva',  fecha:'2026-05-20', estado:'completado', tecnico:'Miguel Torres'          },
            { id:2, area:'Ascensor Torre A', descripcion:'Revisión anual',       fecha:'2026-05-28', estado:'programado', tecnico:'Tecno Ascensores S.A.' },
        ],
        anuncios: [
            { id:1, titulo:'Reunión de copropietarios', contenido:'Sábado 25 de mayo a las 10am en el salón comunal.', fecha:'2026-05-20' },
        ],
    },

    odontologia: {
        _user: { id:1, nombre:'Dr. García Admin', email:'admin@clinica.com', rol:'admin' },
        _dash: { citas_hoy:8, pacientes:234, ingresos_mes:8500000, tratamientos_activos:15 },
        pacientes: [
            { id:1, nombre:'Ana López',     documento:'1020345678', telefono:'3001234567', email:'ana@gmail.com',   ultima_visita:'2026-04-10' },
            { id:2, nombre:'Carlos Moreno', documento:'1045678901', telefono:'3109876543', email:'carlos@gmail.com',ultima_visita:'2026-03-28' },
            { id:3, nombre:'María Torres',  documento:'1067890123', telefono:'3207654321', email:'maria@gmail.com', ultima_visita:'2026-05-02' },
        ],
        citas: [
            { id:1, paciente:'Ana López',     doctor:'Dr. García',   tratamiento:'Limpieza dental',    fecha:'2026-05-23', hora:'09:00', estado:'confirmada', valor:120000 },
            { id:2, paciente:'Carlos Moreno', doctor:'Dr. García',   tratamiento:'Ortodoncia revisión',fecha:'2026-05-23', hora:'10:30', estado:'confirmada', valor:80000  },
            { id:3, paciente:'María Torres',  doctor:'Dra. López',   tratamiento:'Extracción',         fecha:'2026-05-23', hora:'14:00', estado:'pendiente',  valor:150000 },
        ],
        tratamientos: [
            { id:1, nombre:'Limpieza dental',      precio:120000, duracion_min:45  },
            { id:2, nombre:'Extracción simple',    precio:150000, duracion_min:30  },
            { id:3, nombre:'Ortodoncia mensual',   precio:80000,  duracion_min:30  },
            { id:4, nombre:'Blanqueamiento',       precio:350000, duracion_min:90  },
            { id:5, nombre:'Corona porcelana',     precio:800000, duracion_min:60  },
        ],
    },

    ventas: {
        _user: { id:1, nombre:'Admin Ventas', email:'admin@empresa.com', rol:'admin' },
        _dash: { ventas_mes:45800000, clientes:89, productos:156, pedidos_pendientes:12, facturas_vencidas:3 },
        productos: [
            { id:1, nombre:'Laptop Dell Inspiron 15',    precio:3200000, stock:8,  categoria:'Tecnología',  codigo:'DELL-INS-15' },
            { id:2, nombre:'Mouse Inalámbrico Logitech', precio:85000,   stock:45, categoria:'Tecnología',  codigo:'LOG-MOU-WL'  },
            { id:3, nombre:'Teclado Mecánico Redragon',  precio:180000,  stock:20, categoria:'Tecnología',  codigo:'RED-TEC-MEC' },
            { id:4, nombre:'Monitor Samsung 24"',        precio:750000,  stock:12, categoria:'Tecnología',  codigo:'SAM-MON-24'  },
            { id:5, nombre:'Silla ergonómica OfficePro', precio:680000,  stock:6,  categoria:'Mobiliario',  codigo:'SIL-ERG-001' },
        ],
        clientes: [
            { id:1, nombre:'Tech Solutions S.A.S.', nit:'900123456-1', email:'compras@techsol.com',  telefono:'6015551234', ciudad:'Bogotá'   },
            { id:2, nombre:'Distribuidora El Éxito', nit:'800987654-2', email:'pedidos@exito.com',   telefono:'6045559876', ciudad:'Medellín' },
        ],
        facturas: [
            { id:1, numero:'FAC-2026-001', cliente:'Tech Solutions S.A.S.',  fecha:'2026-05-15', total:6450000, estado:'pagada'   },
            { id:2, numero:'FAC-2026-002', cliente:'Distribuidora El Éxito', fecha:'2026-05-18', total:3200000, estado:'pendiente'},
        ],
        cotizaciones: [
            { id:1, numero:'COT-2026-005', cliente:'Tech Solutions S.A.S.', fecha:'2026-05-22', total:1850000, estado:'enviada' },
        ],
    },

    ferreteria: {
        _user: { id:1, nombre:'Administrador', email:'admin@ferreteria.com', rol:'admin' },
        _dash: { ventas_hoy:2850000, productos:487, clientes:124, alertas_stock:8, ventas_mes:45000000 },
        productos: [
            { id:1, nombre:'Cemento Argos 50kg',       precio:35000,  stock:120, categoria:'Construcción', codigo:'CEM-ARG-50'  },
            { id:2, nombre:'Varilla corrugada 3/8"',   precio:28000,  stock:250, categoria:'Hierro',       codigo:'VAR-COR-38'  },
            { id:3, nombre:'Pintura Viniltex 1gal',    precio:42000,  stock:60,  categoria:'Pintura',      codigo:'PIN-VIN-BL1' },
            { id:4, nombre:'Taladro DeWalt 3/8"',      precio:320000, stock:5,   categoria:'Herramientas', codigo:'TAL-DEW-38'  },
            { id:5, nombre:'Cable THW calibre 12',     precio:8500,   stock:500, categoria:'Eléctrico',    codigo:'CAB-THW-12'  },
        ],
        clientes: [
            { id:1, nombre:'Constructora Bolívar',   nit:'900111222-1', telefono:'6015551234', ciudad:'Bogotá'   },
            { id:2, nombre:'Ferresurtidor Ltda.',    nit:'800333444-2', telefono:'6045554321', ciudad:'Medellín' },
        ],
        ventas: [
            { id:1, cliente:'Constructora Bolívar',  fecha:'2026-05-23', total:1850000, estado:'completada', items:8 },
            { id:2, cliente:'Ferresurtidor Ltda.',   fecha:'2026-05-23', total:680000,  estado:'completada', items:3 },
        ],
    },

    polleria: {
        _user: { id:1, nombre:'Administrador', email:'admin@polleria.com', rol:'admin' },
        _dash: { ventas_hoy:1850000, pedidos_hoy:47, mesas_ocupadas:8, mesas_disponibles:4, ventas_mes:38000000 },
        productos: [
            { id:1, nombre:'Pollo a la brasa entero', precio:38000, categoria:'Pollos',  disponible:true },
            { id:2, nombre:'1/2 Pollo a la brasa',   precio:22000, categoria:'Pollos',  disponible:true },
            { id:3, nombre:'1/4 Pollo + papas',      precio:18000, categoria:'Combos',  disponible:true },
            { id:4, nombre:'Alitas x 10',            precio:25000, categoria:'Alitas',  disponible:true },
            { id:5, nombre:'Gaseosa 400ml',          precio:4000,  categoria:'Bebidas', disponible:true },
        ],
        mesas: [
            { id:1, numero:1, capacidad:4, estado:'disponible' },
            { id:2, numero:2, capacidad:4, estado:'disponible' },
            { id:3, numero:3, capacidad:6, estado:'ocupada'    },
            { id:4, numero:4, capacidad:2, estado:'disponible' },
            { id:5, numero:5, capacidad:4, estado:'disponible' },
        ],
        pedidos: [
            { id:1, mesa:3, detalle:'1/2 Pollo + 2 Gaseosas', total:30000, estado:'entregado',  hora:'12:30' },
            { id:2, mesa:7, detalle:'Pollo entero + Gaseosas', total:50000, estado:'en_cocina', hora:'12:45' },
        ],
    },

    salon: {
        _user: { id:1, nombre:'Administrador', email:'admin@salon.com', rol:'admin' },
        _dash: { citas_hoy:12, ingresos_mes:8500000, clientes:186, servicios:18, estilistas:4 },
        citas: [
            { id:1, cliente:'Ana Martínez',  servicio:'Corte y tintura',    estilista:'Valentina R.', fecha:'2026-05-23', hora:'09:00', estado:'confirmada', precio:120000 },
            { id:2, cliente:'Laura Sánchez', servicio:'Manicure + Pedicure', estilista:'Camila L.',   fecha:'2026-05-23', hora:'10:00', estado:'confirmada', precio:75000  },
            { id:3, cliente:'Sofía Reyes',   servicio:'Alisado keratina',    estilista:'Valentina R.', fecha:'2026-05-23', hora:'11:00', estado:'pendiente',  precio:280000 },
        ],
        servicios: [
            { id:1, nombre:'Corte de cabello',     precio:35000,  duracion:30  },
            { id:2, nombre:'Tintura completa',     precio:120000, duracion:120 },
            { id:3, nombre:'Manicure',             precio:35000,  duracion:45  },
            { id:4, nombre:'Pedicure',             precio:40000,  duracion:60  },
            { id:5, nombre:'Alisado keratina',     precio:280000, duracion:180 },
            { id:6, nombre:'Tratamiento capilar',  precio:85000,  duracion:60  },
        ],
        clientes: [
            { id:1, nombre:'Ana Martínez',  telefono:'3001234567', email:'ana@gmail.com',   visitas:12 },
            { id:2, nombre:'Laura Sánchez', telefono:'3109876543', email:'laura@gmail.com', visitas:8  },
            { id:3, nombre:'Sofía Reyes',   telefono:'3207654321', email:'sofia@gmail.com', visitas:5  },
        ],
    },

    parqueo: {
        _user: { id:1, nombre:'Administrador', email:'admin@parqueo.com', rol:'admin' },
        _dash: { vehiculos_adentro:23, ingresos_hoy:185000, ingresos_mes:4200000, espacios_libres:17 },
        vehiculos: [
            { id:1, placa:'ABC123', tipo:'carro', hora_entrada:'08:15', estado:'adentro', propietario:'Juan Pérez'    },
            { id:2, placa:'XYZ789', tipo:'moto',  hora_entrada:'09:30', estado:'adentro', propietario:'Carlos López'  },
            { id:3, placa:'DEF456', tipo:'carro', hora_entrada:'07:45', estado:'adentro', propietario:'María García'  },
        ],
        tarifas: [
            { id:1, tipo:'carro',      precio_hora:5000, precio_fraccion:2500 },
            { id:2, tipo:'moto',       precio_hora:2000, precio_fraccion:1000 },
            { id:3, tipo:'bicicleta',  precio_hora:1000, precio_fraccion:500  },
        ],
        historial: [
            { id:1, placa:'HIJ321', tipo:'carro', entrada:'07:30', salida:'09:30', horas:2,   total:10000 },
            { id:2, placa:'KLM654', tipo:'moto',  entrada:'08:00', salida:'09:00', horas:1,   total:2000  },
        ],
    },

    prestamos: {
        _user: { id:1, nombre:'Administrador', email:'admin@prestamos.com', rol:'admin' },
        _dash: { prestamos_activos:34, cartera_total:185000000, pagos_hoy:3, en_mora:4, clientes:89 },
        clientes: [
            { id:1, nombre:'Hernando Vargas', documento:'1020345678', telefono:'3001234567', ciudad:'Bogotá',   estado:'activo' },
            { id:2, nombre:'Patricia Mora',   documento:'1045678901', telefono:'3109876543', ciudad:'Medellín', estado:'activo' },
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
app.post(['/api/auth/logout','/api/logout'],    (_, res) => res.json({ ok:true }));
app.get(['/api/auth/me','/api/me','/api/usuarios/me','/api/user'],
        (_, res) => res.json({ ok:true, ...demoUser, user:demoUser }));

// ─── Dashboard ─────────────────────────────────────────────────────────────────
const dashR = (_, res) => res.json({ ok:true, success:true, ...demoDash, data:demoDash });
app.get(['/api/dashboard','/api/stats','/api/resumen','/api/home','/api/reportes',
         '/api/metricas','/api/reporte'], dashR);

// ─── CRUD genérico ─────────────────────────────────────────────────────────────
// GET lista
app.get('/api/:r', (req, res) => okL(res, req.params.r));

// GET uno
app.get('/api/:r/:id', (req, res) => {
    if (isNaN(req.params.id)) return okL(res, req.params.r); // sub-ruta tipo /api/auth/me
    const item = lst(req.params.r).find(x => String(x.id) === req.params.id);
    return item ? ok(res, item) : res.json({ ok:true, data:[], total:0 });
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
app.put('/api/:r/:id', upd);
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
