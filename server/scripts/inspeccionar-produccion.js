/**
 * SOLO LECTURA. Muestra qué hay hoy en la base de producción antes de tocarla.
 *
 * Uso:  MYSQL_URL="<MYSQL_PUBLIC_URL>" node server/scripts/inspeccionar-produccion.js
 */
const mysql = require('mysql2/promise');

const cop = n => '$' + Number(n || 0).toLocaleString('es-CO');

(async () => {
    const url = process.env.MYSQL_URL;
    if (!url) { console.error('Falta MYSQL_URL'); process.exit(1); }
    const c = await mysql.createConnection(url);

    const [clientes] = await c.query('SELECT id, nombre, email, telefono FROM clientes ORDER BY id');
    console.log('\n── CLIENTES ──');
    for (const x of clientes) console.log(`  ${String(x.id).padStart(3)} | ${String(x.nombre).padEnd(22)} | ${x.email || '—'}`);

    const [productos] = await c.query('SELECT id, nombre, precio_mensual FROM productos ORDER BY id');
    console.log(`\n── PRODUCTOS (${productos.length}) ──`);
    for (const x of productos.slice(0, 40)) console.log(`  ${String(x.id).padStart(3)} | ${String(x.nombre).padEnd(38)} | ${cop(x.precio_mensual)}`);

    const [lic] = await c.query(`
        SELECT l.id, cl.nombre cliente, p.nombre producto, l.license_key, l.activo,
               l.fecha_vencimiento, l.dia_corte, l.dias_gracia, l.bloquear, l.precio_mensual, l.last_check
        FROM licencias l
        JOIN clientes cl ON cl.id = l.cliente_id
        JOIN productos p ON p.id = l.producto_id
        ORDER BY l.id`);
    console.log('\n── LICENCIAS ──');
    for (const x of lic) {
        console.log(`  ${String(x.id).padStart(3)} | ${String(x.cliente).padEnd(12)} | ${String(x.producto).padEnd(14)} | ${x.license_key.slice(0, 13)}… | activo:${x.activo} | vence:${String(x.fecha_vencimiento).slice(0, 10)} | corte:${x.dia_corte ?? '—'} gracia:${x.dias_gracia} bloquea:${x.bloquear}`);
        console.log(`      último contacto del sistema cliente: ${x.last_check || 'NUNCA'}`);
    }

    for (const [t, q] of [
        ['CONTRATOS', 'SELECT id, titulo, monto, anticipo, estado FROM contratos ORDER BY id'],
        ['MOVIMIENTOS', 'SELECT COUNT(*) total, SUM(recurrente) fijos FROM movimientos_financieros'],
        ['DEUDAS', 'SELECT id, acreedor, monto_original FROM deudas'],
        ['METAS', 'SELECT id, nombre, monto_objetivo FROM metas'],
        ['COSTOS', 'SELECT id, nombre, monto, moneda FROM costos_servidor'],
        ['PAGOS', 'SELECT COUNT(*) total FROM pagos'],
        ['USUARIOS', "SELECT id, email, rol FROM usuarios ORDER BY id"],
    ]) {
        try {
            const [r] = await c.query(q);
            console.log(`\n── ${t} ──`);
            if (!r.length) console.log('  (vacío)');
            for (const x of r) console.log('  ' + JSON.stringify(x));
        } catch (e) { console.log(`\n── ${t} ── error: ${e.message}`); }
    }

    // El límite de 64 índices que hizo fallar sync alter
    console.log('\n── ÍNDICES POR TABLA (límite de MySQL: 64) ──');
    const [tablas] = await c.query('SHOW TABLES');
    for (const row of tablas) {
        const t = Object.values(row)[0];
        const [idx] = await c.query(`SHOW INDEX FROM \`${t}\``);
        const nombres = new Set(idx.map(i => i.Key_name));
        if (nombres.size >= 20) console.log(`  ⚠️  ${t.padEnd(26)} ${nombres.size} índices`);
    }

    await c.end();
})().catch(e => { console.error('ERR', e.message); process.exit(1); });
