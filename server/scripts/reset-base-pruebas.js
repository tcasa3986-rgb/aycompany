/**
 * Deja la base de pruebas en el estado sembrado, para que las suites den el
 * mismo resultado siempre. Sin esto, cada corrida acumula abonos y licencias
 * y las pruebas empiezan a fallar en falso.
 *
 * Uso: DB_NAME=mi_plataforma_test node server/scripts/reset-base-pruebas.js
 *
 * SOLO toca la base 'mi_plataforma_test'. Nunca la de producción.
 */

require('dotenv').config();
const mysql = require('mysql2/promise');

const REALES = ['Ferre Láser CDA', 'Ferre Láser SAS', 'JD Metales', 'ASOERC', 'Maderas Montoya'];

(async () => {
    const c = await mysql.createConnection({
        host: process.env.DB_HOST, user: process.env.DB_USER,
        password: process.env.DB_PASSWORD, database: 'mi_plataforma_test', multipleStatements: true
    });
    const ph = REALES.map(() => '?').join(',');

    // rastro de las suites
    await c.query('DELETE FROM abonos_deuda');
    await c.query('DELETE FROM aportes_meta');
    await c.query("DELETE FROM movimientos_financieros WHERE recurrente = 0");
    // Los datos sembrados no traen pagos ni facturas: los que existan los dejó
    // una prueba. Sin borrarlos, una referencia_externa repetida hace fallar la
    // siguiente corrida por duplicado.
    await c.query('DELETE FROM facturas');
    await c.query('DELETE FROM pagos');
    await c.query("UPDATE deudas SET estado = 'activa'");
    await c.query("UPDATE metas SET estado = 'activa'");

    // clientes/licencias que crearon las pruebas
    await c.query('DELETE FROM licencias WHERE cliente_id NOT IN (SELECT id FROM clientes WHERE nombre IN (' + ph + '))', REALES);
    await c.query('DELETE FROM pagos     WHERE cliente_id NOT IN (SELECT id FROM clientes WHERE nombre IN (' + ph + '))', REALES);
    await c.query('DELETE FROM facturas  WHERE cliente_id NOT IN (SELECT id FROM clientes WHERE nombre IN (' + ph + '))', REALES);
    await c.query('DELETE FROM clientes  WHERE nombre NOT IN (' + ph + ')', REALES);
    // Los datos reales no traen costos: cualquiera que exista lo dejó una prueba.
    await c.query('DELETE FROM costos_servidor');
    await c.query("DELETE FROM leads WHERE valor_unico = 0 AND valor_mensual = 0");

    const [[{ n: lic }]]  = await c.query('SELECT COUNT(*) n FROM licencias WHERE activo = 1');
    const [[{ t: mrr }]]  = await c.query('SELECT SUM(COALESCE(l.precio_mensual, p.precio_mensual)) t FROM licencias l JOIN productos p ON p.id = l.producto_id WHERE l.activo = 1');
    const [[{ d: deu }]]  = await c.query("SELECT SUM(monto_original) d FROM deudas WHERE estado = 'activa'");
    console.log(`reset OK — ${lic} licencias, recurrente $${Number(mrr).toLocaleString('es-CO')}, deuda $${Number(deu).toLocaleString('es-CO')}`);
    await c.end();
})().catch(e => { console.error('ERR', e.message); process.exit(1); });
