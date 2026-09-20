/**
 * MySQL permite máximo 64 índices por tabla. `sequelize.sync({ alter: true })`
 * agrega un índice nuevo en CADA despliegue por cada campo `unique`, así que
 * las tablas fueron acumulando copias: license_key, license_key_2, license_key_3…
 * Por eso el arranque del 20/09/2026 falló con "Too many keys specified".
 *
 * Este script borra SOLO los duplicados: índices sobre exactamente las mismas
 * columnas que otro que se conserva. Nunca toca PRIMARY ni el primero de cada
 * grupo, así que las restricciones de unicidad se mantienen intactas.
 *
 * Por defecto solo INFORMA. Para ejecutar de verdad hay que pasar --aplicar.
 *
 *   MYSQL_URL="…" node server/scripts/limpiar-indices-duplicados.js
 *   MYSQL_URL="…" node server/scripts/limpiar-indices-duplicados.js --aplicar
 */
const mysql = require('mysql2/promise');
const APLICAR = process.argv.includes('--aplicar');

(async () => {
    const url = process.env.MYSQL_URL;
    if (!url) { console.error('Falta MYSQL_URL'); process.exit(1); }
    const c = await mysql.createConnection(url);

    const [tablas] = await c.query('SHOW TABLES');
    let totalSobran = 0;
    const plan = [];

    for (const row of tablas) {
        const tabla = Object.values(row)[0];
        const [idx] = await c.query(`SHOW INDEX FROM \`${tabla}\``);

        // Agrupar: nombre del índice -> columnas en orden
        const porNombre = {};
        for (const i of idx) {
            (porNombre[i.Key_name] = porNombre[i.Key_name] || []).push(
                { col: i.Column_name, seq: i.Seq_in_index, unico: i.Non_unique === 0 }
            );
        }

        // Firma = columnas + unicidad. Dos índices con la misma firma son
        // intercambiables: sobra uno.
        const porFirma = {};
        for (const [nombre, cols] of Object.entries(porNombre)) {
            if (nombre === 'PRIMARY') continue;
            cols.sort((a, b) => a.seq - b.seq);
            const firma = (cols[0].unico ? 'U:' : 'I:') + cols.map(x => x.col).join(',');
            (porFirma[firma] = porFirma[firma] || []).push(nombre);
        }

        const sobran = [];
        for (const [firma, nombres] of Object.entries(porFirma)) {
            if (nombres.length < 2) continue;
            // Se conserva el de nombre más corto (el original, sin sufijo _2, _3…)
            nombres.sort((a, b) => a.length - b.length || a.localeCompare(b));
            const conservar = nombres[0];
            for (const n of nombres.slice(1)) sobran.push({ nombre: n, firma, conservar });
        }

        if (sobran.length) {
            const total = Object.keys(porNombre).length;
            console.log(`\n${tabla}: ${total} índices, sobran ${sobran.length} -> quedarían ${total - sobran.length}`);
            for (const s of sobran.slice(0, 4)) console.log(`   borrar ${s.nombre}  (duplica ${s.conservar} sobre ${s.firma})`);
            if (sobran.length > 4) console.log(`   … y ${sobran.length - 4} más sobre las mismas columnas`);
            totalSobran += sobran.length;
            plan.push({ tabla, sobran });
        }
    }

    if (!totalSobran) { console.log('\nNo hay índices duplicados.'); await c.end(); return; }

    console.log(`\n═══ TOTAL: ${totalSobran} índices duplicados ═══`);
    if (!APLICAR) {
        console.log('Esto fue solo un informe. Para borrarlos: agregar --aplicar');
        await c.end(); return;
    }

    console.log('\nAplicando…');
    let ok = 0, err = 0;
    for (const { tabla, sobran } of plan) {
        for (const s of sobran) {
            try { await c.query(`ALTER TABLE \`${tabla}\` DROP INDEX \`${s.nombre}\``); ok++; }
            catch (e) { console.error(`  ERROR ${tabla}.${s.nombre}: ${e.message}`); err++; }
        }
        const [idx] = await c.query(`SHOW INDEX FROM \`${tabla}\``);
        console.log(`  ${tabla}: quedan ${new Set(idx.map(i => i.Key_name)).size} índices`);
    }
    console.log(`\nListo: ${ok} borrados, ${err} errores`);
    await c.end();
})().catch(e => { console.error('ERR', e.message); process.exit(1); });
