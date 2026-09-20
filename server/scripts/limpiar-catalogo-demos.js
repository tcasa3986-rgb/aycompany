/**
 * Quita del catálogo los sistemas de demostración que quedaron cuando se
 * borraron los demos del servidor. Eran 33 productos de los cuales casi todos
 * eran vitrina (CRM Colegio, Sistema Pollería…), no sistemas reales.
 *
 * REGLA DE SEGURIDAD: nunca borra un producto que tenga una licencia asociada.
 * Si lo hiciera, dejaría huérfana la licencia de un cliente vivo.
 *
 *   MYSQL_URL="…" node server/scripts/limpiar-catalogo-demos.js            (informe)
 *   MYSQL_URL="…" node server/scripts/limpiar-catalogo-demos.js --aplicar
 */
const mysql = require('mysql2/promise');
const APLICAR = process.argv.includes('--aplicar');

// Productos que corresponden a sistemas reales y se conservan pase lo que pase.
const REALES = [
    'Sistema Ferre Láser', 'Sistema Láser Ejecutivo', 'Sistema ASOERC',
    'Marketing Digital', 'asorec', 'Boton', 'ASOERC',
];

(async () => {
    const url = process.env.MYSQL_URL;
    if (!url) { console.error('Falta MYSQL_URL'); process.exit(1); }
    const c = await mysql.createConnection(url);

    const [productos] = await c.query(`
        SELECT p.id, p.nombre, p.precio_mensual, COUNT(l.id) licencias
        FROM productos p LEFT JOIN licencias l ON l.producto_id = p.id
        GROUP BY p.id ORDER BY p.id`);

    const enUso   = productos.filter(p => p.licencias > 0);
    const proteg  = productos.filter(p => p.licencias == 0 && REALES.includes(p.nombre));
    const aBorrar = productos.filter(p => p.licencias == 0 && !REALES.includes(p.nombre));

    console.log(`\n── SE CONSERVAN: ${enUso.length} con licencia activa ──`);
    for (const p of enUso) console.log(`  ${String(p.id).padStart(3)} | ${p.nombre}  (${p.licencias} licencia${p.licencias > 1 ? 's' : ''})`);

    if (proteg.length) {
        console.log(`\n── SE CONSERVAN: ${proteg.length} sistemas reales sin licencia todavía ──`);
        for (const p of proteg) console.log(`  ${String(p.id).padStart(3)} | ${p.nombre}`);
    }

    console.log(`\n── SE BORRAN: ${aBorrar.length} productos de demostración ──`);
    for (const p of aBorrar) console.log(`  ${String(p.id).padStart(3)} | ${p.nombre}`);

    if (!aBorrar.length) { console.log('\nNada que borrar.'); await c.end(); return; }
    if (!APLICAR) { console.log('\nSolo informe. Para borrarlos: agregar --aplicar'); await c.end(); return; }

    const ids = aBorrar.map(p => p.id);
    const [r] = await c.query(`DELETE FROM productos WHERE id IN (${ids.map(() => '?').join(',')})`, ids);
    console.log(`\nBorrados ${r.affectedRows} productos.`);

    const [quedan] = await c.query('SELECT id, nombre FROM productos ORDER BY id');
    console.log(`Quedan ${quedan.length}:`);
    for (const p of quedan) console.log(`  ${String(p.id).padStart(3)} | ${p.nombre}`);
    await c.end();
})().catch(e => { console.error('ERR', e.message); process.exit(1); });
