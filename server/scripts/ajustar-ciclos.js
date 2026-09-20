/**
 * Fija el día de corte de cada cliente y alinea su vencimiento al PRÓXIMO cobro
 * real, según lo que confirmó Cristian el 20/09/2026:
 *   Ferre Láser (CDA y SAS) -> día 1
 *   JD Metales              -> día 15   (estaba mal puesto en 1)
 *   DINASTY POKER CLUB      -> día 30
 *   ASOERC                  -> día 20   (ya estaba)
 *
 * Al crear las licencias, el vencimiento se calculó con la regla de renovación
 * (día de corte más cercano al aniversario), que para un alta dejaba fechas
 * demasiado lejanas — Ferre quedaba cobrando hasta el 1 de noviembre cuando en
 * realidad cobra el 1 de octubre. Aquí se corrige al siguiente corte real.
 *
 *   MYSQL_URL="…" node server/scripts/ajustar-ciclos.js            (informe)
 *   MYSQL_URL="…" node server/scripts/ajustar-ciclos.js --aplicar
 */
const mysql = require('mysql2/promise');
const { estadoLicencia, hoyBogota, aFecha, aStr } = require('../src/utils/licenciaCiclo');
const APLICAR = process.argv.includes('--aplicar');

// cliente -> { corte, gracia }. La tolerancia se deja como está hoy salvo que
// se indique: cambiarla sin que el dueño lo pida puede apagar a quien sí paga.
const CICLOS = {
    'Ferre Láser CDA':    { corte: 1,  gracia: 15 },
    'Ferre Láser SAS':    { corte: 1,  gracia: 15 },
    'JD Metales':         { corte: 15, gracia: 15 },
    'DINASTY POKER CLUB': { corte: 30, gracia: 15 },
    'ASOERC':             { corte: 20, gracia: 0  },
    'Maderas Montoya':    { corte: 1,  gracia: 0  },   // bloquear=false, la gracia da igual
};

function ultimoDiaMes(y, m) { return new Date(Date.UTC(y, m + 1, 0)).getUTCDate(); }

// Primer día de corte estrictamente posterior a hoy.
function proximoCorte(corte, hoy) {
    const d = aFecha(hoy);
    let y = d.getUTCFullYear(), m = d.getUTCMonth();
    let c = new Date(Date.UTC(y, m, Math.min(corte, ultimoDiaMes(y, m))));
    if (c <= d) { m += 1; c = new Date(Date.UTC(y, m, Math.min(corte, ultimoDiaMes(y, m)))); }
    return aStr(c);
}

(async () => {
    const url = process.env.MYSQL_URL;
    if (!url) { console.error('Falta MYSQL_URL'); process.exit(1); }
    const c = await mysql.createConnection({ uri: url, dateStrings: true });
    const HOY = hoyBogota();

    const [lic] = await c.query(`
        SELECT l.id, cl.nombre cliente, l.fecha_vencimiento, l.dia_corte, l.dias_gracia, l.bloquear, l.activo
        FROM licencias l JOIN clientes cl ON cl.id = l.cliente_id WHERE l.activo = 1`);

    console.log(`\nHoy es ${HOY}\n`);
    const cambios = [];
    for (const l of lic) {
        const reg = CICLOS[l.cliente];
        if (!reg) { console.log(`  ? ${l.cliente}: sin regla definida, se deja como está`); continue; }
        const venc = proximoCorte(reg.corte, HOY);
        const nuevo = { ...l, dia_corte: reg.corte, dias_gracia: reg.gracia, fecha_vencimiento: venc };
        const e = estadoLicencia(nuevo, HOY);
        const bloqueo = l.bloquear ? `se bloquea el ${e.fecha_bloqueo}` : 'nunca se bloquea';

        console.log(`  ${l.cliente.padEnd(20)} corte ${String(reg.corte).padStart(2)} · ${reg.gracia} días de tolerancia`);
        console.log(`  ${''.padEnd(20)} vence ${l.fecha_vencimiento} -> ${venc}   (${bloqueo})`);
        if (l.dia_corte != reg.corte || l.dias_gracia != reg.gracia || l.fecha_vencimiento !== venc) {
            cambios.push({ id: l.id, corte: reg.corte, gracia: reg.gracia, venc });
        }
    }

    if (!cambios.length) { console.log('\nTodo alineado.'); await c.end(); return; }
    if (!APLICAR) { console.log(`\n${cambios.length} licencias por ajustar. Para ejecutar: agregar --aplicar`); await c.end(); return; }

    for (const x of cambios) {
        await c.query('UPDATE licencias SET dia_corte = ?, dias_gracia = ?, fecha_vencimiento = ? WHERE id = ?',
                      [x.corte, x.gracia, x.venc, x.id]);
    }
    console.log(`\n${cambios.length} licencias ajustadas.`);
    await c.end();
})().catch(e => { console.error('ERR', e.message); process.exit(1); });
