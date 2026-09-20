/**
 * Borra de producción los clientes de prueba que quedaron desde abril/mayo y
 * que inflaban el recurrente del tablero en $500.000.
 *
 * Borra en orden de dependencias (facturas -> pagos -> licencias -> cliente) y
 * al final los productos que SOLO usaban ellos.
 *
 * REGLA DE SEGURIDAD: se niega a borrar cualquier cliente cuyo sistema haya
 * consultado su licencia en los últimos 30 días. Si algo está vivo, no se toca.
 *
 *   MYSQL_URL="…" node server/scripts/borrar-clientes-prueba.js            (informe)
 *   MYSQL_URL="…" node server/scripts/borrar-clientes-prueba.js --aplicar
 */
const mysql = require('mysql2/promise');
const APLICAR = process.argv.includes('--aplicar');

const NOMBRES   = ['cristian', 'cristian3', 'lispan', 'juan'];
const PRODUCTOS = ['ferreteria', 'panaderia'];

(async () => {
    const url = process.env.MYSQL_URL;
    if (!url) { console.error('Falta MYSQL_URL'); process.exit(1); }
    const c = await mysql.createConnection({ uri: url, dateStrings: true });
    const ph = NOMBRES.map(() => '?').join(',');

    const [clientes] = await c.query(
        `SELECT id, nombre FROM clientes WHERE nombre IN (${ph})`, NOMBRES);
    if (!clientes.length) { console.log('No quedan clientes de prueba.'); await c.end(); return; }

    const ids = clientes.map(x => x.id);
    const idp = ids.map(() => '?').join(',');

    // Nadie vivo se borra, por muy "de prueba" que parezca el nombre.
    const [vivos] = await c.query(
        `SELECT cl.nombre, l.last_check FROM licencias l JOIN clientes cl ON cl.id = l.cliente_id
         WHERE l.cliente_id IN (${idp}) AND l.last_check > DATE_SUB(NOW(), INTERVAL 30 DAY)`, ids);
    if (vivos.length) {
        console.error('\nABORTADO: estos sistemas consultaron su licencia hace menos de 30 días:');
        for (const v of vivos) console.error(`  ${v.nombre} — ${v.last_check}`);
        await c.end(); process.exit(1);
    }

    const [lic] = await c.query(
        `SELECT l.id, cl.nombre cliente, p.nombre producto, l.last_check
         FROM licencias l JOIN clientes cl ON cl.id = l.cliente_id JOIN productos p ON p.id = l.producto_id
         WHERE l.cliente_id IN (${idp})`, ids);

    console.log('\n── SE BORRA ──');
    for (const x of clientes) console.log(`  cliente ${x.id}: ${x.nombre}`);
    for (const x of lic) console.log(`  licencia ${x.id}: ${x.cliente} / ${x.producto} — último contacto: ${x.last_check || 'nunca'}`);

    // Productos que solo usaban ellos
    const php = PRODUCTOS.map(() => '?').join(',');
    const [prods] = await c.query(
        `SELECT p.id, p.nombre, COUNT(l.id) otras
         FROM productos p LEFT JOIN licencias l ON l.producto_id = p.id AND l.cliente_id NOT IN (${idp})
         WHERE p.nombre IN (${php}) GROUP BY p.id`, [...ids, ...PRODUCTOS]);
    const prodBorrar = prods.filter(p => p.otras == 0);
    for (const p of prodBorrar) console.log(`  producto ${p.id}: ${p.nombre} (nadie más lo usa)`);
    for (const p of prods.filter(x => x.otras > 0)) console.log(`  ! producto ${p.nombre} lo usan ${p.otras} licencias más: NO se borra`);

    if (!APLICAR) { console.log('\nSolo informe. Para ejecutar: agregar --aplicar'); await c.end(); return; }

    console.log('\nAplicando…');
    const [f] = await c.query(`DELETE FROM facturas  WHERE cliente_id IN (${idp})`, ids);
    const [p] = await c.query(`DELETE FROM pagos     WHERE cliente_id IN (${idp})`, ids);
    const [l] = await c.query(`DELETE FROM licencias WHERE cliente_id IN (${idp})`, ids);
    const [k] = await c.query(`DELETE FROM clientes  WHERE id IN (${idp})`, ids);
    console.log(`  facturas ${f.affectedRows} · pagos ${p.affectedRows} · licencias ${l.affectedRows} · clientes ${k.affectedRows}`);

    if (prodBorrar.length) {
        const pid = prodBorrar.map(x => x.id);
        const [r] = await c.query(`DELETE FROM productos WHERE id IN (${pid.map(() => '?').join(',')})`, pid);
        console.log(`  productos ${r.affectedRows}`);
    }

    const [[m]] = await c.query(`SELECT SUM(COALESCE(l.precio_mensual, p.precio_mensual)) t
        FROM licencias l JOIN productos p ON p.id = l.producto_id WHERE l.activo = 1`);
    console.log(`\n  Recurrente ahora: $${Number(m.t || 0).toLocaleString('es-CO')}`);
    await c.end();
})().catch(e => { console.error('ERR', e.message); process.exit(1); });
