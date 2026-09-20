/**
 * Alinea la base de PRODUCCIÓN con la realidad, sin romper lo que está vivo.
 *
 * El script de siembra normal (cargar-datos-cristian.js) busca por nombre y
 * crearía un cliente "ASOERC" y una licencia NUEVA, dejando la que su sistema
 * ya usa (7b3d4b18…) colgando en paralelo. Este script prepara el terreno para
 * que la siembra reconozca lo que ya existe:
 *
 *   1. Renombra el cliente "asoerc" a "ASOERC".
 *   2. Renombra el producto "asorec" a "Sistema ASOERC" (conserva el id 3, que
 *      es al que apunta la licencia viva) y borra el duplicado vacío.
 *
 * NO toca DINASTY POKER CLUB ni los clientes de prueba: eso se decide aparte.
 *
 *   MYSQL_URL="…" node server/scripts/preparar-produccion.js            (informe)
 *   MYSQL_URL="…" node server/scripts/preparar-produccion.js --aplicar
 */
const mysql = require('mysql2/promise');
const APLICAR = process.argv.includes('--aplicar');

(async () => {
    const url = process.env.MYSQL_URL;
    if (!url) { console.error('Falta MYSQL_URL'); process.exit(1); }
    const c = await mysql.createConnection(url);
    const pasos = [];

    const [[cliAsoerc]] = await c.query("SELECT id, nombre FROM clientes WHERE nombre = 'asoerc' COLLATE utf8mb4_general_ci LIMIT 1");
    if (cliAsoerc && cliAsoerc.nombre !== 'ASOERC') {
        pasos.push({
            que: `cliente ${cliAsoerc.id}: "${cliAsoerc.nombre}" -> "ASOERC"`,
            sql: ['UPDATE clientes SET nombre = ? WHERE id = ?', ['ASOERC', cliAsoerc.id]],
        });
    }

    const [[prodViejo]] = await c.query("SELECT id, nombre FROM productos WHERE nombre = 'asorec' LIMIT 1");
    const [[prodDup]]   = await c.query("SELECT id FROM productos WHERE nombre = 'ASOERC' LIMIT 1");
    if (prodViejo) {
        // El duplicado vacío estorba: el nombre destino tiene que quedar libre.
        if (prodDup && prodDup.id !== prodViejo.id) {
            const [[{ n }]] = await c.query('SELECT COUNT(*) n FROM licencias WHERE producto_id = ?', [prodDup.id]);
            if (n === 0) pasos.push({ que: `borrar producto ${prodDup.id} "ASOERC" (duplicado sin licencias)`, sql: ['DELETE FROM productos WHERE id = ?', [prodDup.id]] });
            else console.log(`  ! el producto ${prodDup.id} tiene ${n} licencias: no se borra`);
        }
        pasos.push({
            que: `producto ${prodViejo.id}: "asorec" -> "Sistema ASOERC" (conserva el id al que apunta la licencia viva)`,
            sql: ['UPDATE productos SET nombre = ?, precio_mensual = ? WHERE id = ?', ['Sistema ASOERC', 250000, prodViejo.id]],
        });
    }

    console.log('\n── LO QUE SE VA A HACER ──');
    if (!pasos.length) console.log('  nada: ya está alineado');
    for (const p of pasos) console.log('  · ' + p.que);

    console.log('\n── LO QUE NO SE TOCA ──');
    const [vivas] = await c.query(`
        SELECT cl.nombre cliente, p.nombre producto, l.last_check
        FROM licencias l JOIN clientes cl ON cl.id = l.cliente_id JOIN productos p ON p.id = l.producto_id
        WHERE l.last_check > DATE_SUB(NOW(), INTERVAL 7 DAY)`);
    for (const v of vivas) console.log(`  · ${v.cliente} (${v.producto}) — su sistema consultó hace poco, sigue igual`);

    if (!APLICAR) { console.log('\nSolo informe. Para ejecutar: agregar --aplicar'); await c.end(); return; }

    for (const p of pasos) { await c.query(p.sql[0], p.sql[1]); console.log('  hecho: ' + p.que); }

    console.log('\n── ESTADO FINAL ──');
    const [cl] = await c.query('SELECT id, nombre FROM clientes ORDER BY id');
    console.log('  clientes: ' + cl.map(x => `${x.id}:${x.nombre}`).join(' · '));
    const [pr] = await c.query('SELECT id, nombre FROM productos ORDER BY id');
    console.log('  productos: ' + pr.map(x => `${x.id}:${x.nombre}`).join(' · '));
    await c.end();
})().catch(e => { console.error('ERR', e.message); process.exit(1); });
