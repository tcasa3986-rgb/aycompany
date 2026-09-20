/**
 * Carga la realidad financiera de AI Company CO / Cristian (declarada 20/09/2026).
 *
 * Es idempotente: se puede correr varias veces sin duplicar nada. Busca por
 * nombre y actualiza en vez de insertar.
 *
 * Uso:  node server/scripts/cargar-datos-cristian.js
 */
require('dotenv').config();
const sequelize = require('../src/config/db');
const {
    Cliente, Producto, Licencia, Contrato, CostoServidor,
    MovimientoFinanciero, Deuda, Meta
} = require('../src/models');
const { hoyBogota } = require('../src/utils/licenciaCiclo');
const { v4: uuidv4 } = require('uuid');

const HOY = hoyBogota();

// ── Clientes reales ───────────────────────────────────────────────────
const CLIENTES = [
    { nombre: 'Ferre Láser CDA',   telefono: '', notas: 'Corte láser — negocio 1 de 2' },
    { nombre: 'Ferre Láser SAS',   telefono: '', notas: 'Corte láser — negocio 2 de 2' },
    { nombre: 'JD Metales',        telefono: '', notas: 'Ibagué — sistema Láser Ejecutivo' },
    { nombre: 'ASOERC',            telefono: '321 9544178', notas: 'Reciclaje — solo el sistema' },
    { nombre: 'Maderas Montoya',   telefono: '', notas: 'Dueño: César. Marketing digital, NO paga sistema (amigo)' },
];

// ── Productos (sistemas reales, no el catálogo de demos) ──────────────
const PRODUCTOS = [
    { nombre: 'Sistema Ferre Láser',    precio_mensual: 250000, categoria: 'Sistema' },
    { nombre: 'Sistema Láser Ejecutivo', precio_mensual: 200000, categoria: 'Sistema' },
    { nombre: 'Sistema ASOERC',          precio_mensual: 250000, categoria: 'Sistema' },
    { nombre: 'Marketing Digital',       precio_mensual: 1000000, categoria: 'Marketing' },
];

// ── Licencias: día de corte y regla de bloqueo por cliente ────────────
const LICENCIAS = [
    { cliente: 'ASOERC',          producto: 'Sistema ASOERC',           precio: 250000,  dia_corte: 20, dias_gracia: 0,  bloquear: true,  notas: 'Se bloquea el mismo día 20 si no paga' },
    { cliente: 'Ferre Láser CDA', producto: 'Sistema Ferre Láser',      precio: 250000,  dia_corte: 1,  dias_gracia: 15, bloquear: true,  notas: 'Corte el 1, se bloquea el 16' },
    { cliente: 'Ferre Láser SAS', producto: 'Sistema Ferre Láser',      precio: 250000,  dia_corte: 1,  dias_gracia: 15, bloquear: true,  notas: 'Corte el 1, se bloquea el 16' },
    { cliente: 'JD Metales',      producto: 'Sistema Láser Ejecutivo',  precio: 200000,  dia_corte: 1,  dias_gracia: 15, bloquear: true,  notas: 'Corte el 1, se bloquea el 16' },
    { cliente: 'Maderas Montoya', producto: 'Marketing Digital',        precio: 1000000, dia_corte: 1,  dias_gracia: 0,  bloquear: false, notas: 'NUNCA se bloquea. César es amigo de Cristian' },
];

// ── Contratos con su estructura real de pago ──────────────────────────
const CONTRATOS = [
    {
        cliente: 'Ferre Láser CDA', titulo: 'Sistema Ferre Láser (CDA + SAS)',
        descripcion: 'Contrato conjunto por los dos negocios. Mitad de contado, mitad financiada a cuotas.',
        monto: 16000000, anticipo: 8000000, cuota_mensual: 1000000, dia_cobro: 1,
        mensualidad: 500000, tipo_servicio: 'sistema', estado: 'firmado',
        notas: 'OJO: el primer millón de la cuota NUNCA ha llegado. Es la cobranza más grande pendiente.'
    },
    {
        cliente: 'JD Metales', titulo: 'Sistema Láser Ejecutivo',
        descripcion: 'Mitad de contado ($3.500.000), mitad financiada a $1.000.000 mensual.',
        monto: 7000000, anticipo: 3500000, cuota_mensual: 1000000, dia_cobro: 1,
        mensualidad: 200000, tipo_servicio: 'sistema', estado: 'firmado'
    },
    {
        cliente: 'Maderas Montoya', titulo: 'Marketing digital — redes sociales',
        descripcion: 'Manejo de redes de Maderas Montoya. Sin contrato de sistema.',
        monto: 0, anticipo: 0, cuota_mensual: 0, mensualidad: 1000000,
        tipo_servicio: 'marketing', estado: 'firmado',
        notas: 'ACUERDO DE PALABRA. Es el 51% del ingreso recurrente: debería quedar por escrito.'
    },
    {
        cliente: 'ASOERC', titulo: 'Mantenimiento Sistema ASOERC',
        descripcion: 'Solo servicio mensual del sistema.',
        monto: 0, anticipo: 0, cuota_mensual: 0, mensualidad: 250000,
        tipo_servicio: 'sistema', estado: 'firmado'
    },
];

// ── Gastos fijos mensuales ────────────────────────────────────────────
const GASTOS_FIJOS = [
    { ambito: 'personal', categoria: 'arriendo',   concepto: 'Arriendo',                monto: 500000 },
    { ambito: 'personal', categoria: 'recibos',    concepto: 'Recibos (servicios)',     monto: 200000 },
    { ambito: 'personal', categoria: 'comida',     concepto: 'Comida',                  monto: 200000 },
    { ambito: 'personal', categoria: 'transporte', concepto: 'Gasolina moto',           monto: 108000 },
    { ambito: 'personal', categoria: 'celular',    concepto: 'Plan celular',            monto: 50000  },
    { ambito: 'personal', categoria: 'gimnasio',   concepto: 'Gimnasio',                monto: 100000 },
    { ambito: 'empresa',  categoria: 'herramientas', concepto: 'Codex',                 monto: 100000 },
    { ambito: 'empresa',  categoria: 'herramientas', concepto: 'Claude / Cloud',        monto: 20000  },
];

const DEUDAS = [
    {
        acreedor: 'Deuda principal', concepto: 'Deuda a liquidar lo antes posible',
        monto_original: 6000000, tasa_mensual: 0, ambito: 'personal', prioridad: 1,
        notas: 'Sin intereses. Cristian quiere saldarla cuanto antes.'
    },
];

const METAS = [
    { nombre: 'Liquidar la deuda de $6.000.000', monto_objetivo: 6000000, tipo: 'personal',    prioridad: 1, descripcion: 'Quedar libre de deuda' },
    { nombre: 'Segunda moto para alquiler',      monto_objetivo: 8000000, tipo: 'activo',      prioridad: 2, retorno_mensual: 750000, descripcion: 'Una moto renta ~$750.000/mes: es el mejor retorno que tiene hoy (~9,4% mensual)' },
    { nombre: 'Tercera moto para alquiler',      monto_objetivo: 8000000, tipo: 'activo',      prioridad: 3, retorno_mensual: 750000 },
    { nombre: 'PIN de ingreso a la universidad', monto_objetivo: 500000,  tipo: 'educacion',   prioridad: 4, descripcion: 'Entra el año entrante' },
    { nombre: 'iPhone 17 Pro Max',               monto_objetivo: 6500000, tipo: 'herramienta', prioridad: 5, descripcion: 'Grabación de contenido para la empresa' },
    { nombre: 'Estabilizador',                   monto_objetivo: 500000,  tipo: 'herramienta', prioridad: 6 },
    { nombre: 'Dron',                            monto_objetivo: 3000000, tipo: 'herramienta', prioridad: 7 },
];

async function buscarOCrear(Modelo, where, datos) {
    const existente = await Modelo.findOne({ where });
    if (existente) { await existente.update(datos); return { fila: existente, creado: false }; }
    return { fila: await Modelo.create({ ...where, ...datos }), creado: true };
}

(async () => {
    await sequelize.authenticate();
    const r = { creados: 0, actualizados: 0 };
    const marcar = x => { x.creado ? r.creados++ : r.actualizados++; return x.fila; };

    const clientes = {};
    for (const c of CLIENTES) clientes[c.nombre] = marcar(await buscarOCrear(Cliente, { nombre: c.nombre }, c));

    const productos = {};
    for (const p of PRODUCTOS) productos[p.nombre] = marcar(await buscarOCrear(Producto, { nombre: p.nombre }, p));

    for (const l of LICENCIAS) {
        const cliente = clientes[l.cliente], producto = productos[l.producto];
        const existente = await Licencia.findOne({ where: { cliente_id: cliente.id, producto_id: producto.id } });
        const datos = {
            dia_corte: l.dia_corte, dias_gracia: l.dias_gracia, bloquear: l.bloquear,
            precio_mensual: l.precio, notas: l.notas, activo: true
        };
        if (existente) { await existente.update(datos); r.actualizados++; }
        else {
            // Primer vencimiento: el próximo día de corte, para no cobrar de más al arrancar
            const { siguienteVencimiento } = require('../src/utils/licenciaCiclo');
            await Licencia.create({
                cliente_id: cliente.id, producto_id: producto.id, license_key: uuidv4(),
                fecha_inicio: HOY, fecha_vencimiento: siguienteVencimiento({ fecha_vencimiento: HOY, dia_corte: l.dia_corte }, 1),
                ...datos
            });
            r.creados++;
        }
    }

    for (const c of CONTRATOS) {
        const { cliente, ...datos } = c;
        marcar(await buscarOCrear(Contrato, { titulo: c.titulo }, { ...datos, cliente_id: clientes[cliente].id, fecha_inicio: HOY }));
    }

    for (const g of GASTOS_FIJOS) {
        marcar(await buscarOCrear(
            MovimientoFinanciero,
            { concepto: g.concepto, recurrente: true },
            { ...g, tipo: 'egreso', monto: g.monto, fecha: HOY, recurrente: true, origen: 'manual',
              notas: 'Gasto fijo mensual declarado el 20/09/2026' }
        ));
    }

    for (const d of DEUDAS) marcar(await buscarOCrear(Deuda, { acreedor: d.acreedor }, d));
    for (const m of METAS)  marcar(await buscarOCrear(Meta,  { nombre: m.nombre }, m));

    console.log(`✅ Datos cargados: ${r.creados} creados, ${r.actualizados} actualizados`);
    console.log('   Recuerda: falta medir Railway, dominios y APIs en Costos de servidores.');
    await sequelize.close();
})().catch(e => { console.error('❌', e.message); process.exit(1); });
