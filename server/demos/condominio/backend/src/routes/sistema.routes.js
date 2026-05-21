/**
 * ═══════════════════════════════════════════════════════════
 *  Mantenimiento de Sistema - Condominio CRM
 *  Rutas: /api/sistema/...
 * ═══════════════════════════════════════════════════════════
 */

const express = require('express');
const router = express.Router();
const multer = require('multer');
const path = require('path');
const fs = require('fs');

const pool = require('../config/db');
const { verifyToken, requireRol } = require('../middlewares/auth.middleware');

// ── Multer: almacenamiento temporal en memoria ─────────────────────────
const storage = multer.memoryStorage();
const upload = multer({
  storage,
  limits: { fileSize: 100 * 1024 * 1024 }, // 100 MB máximo
  fileFilter: (req, file, cb) => {
    const ok = path.extname(file.originalname).toLowerCase() === '.sql';
    cb(ok ? null : new Error('Solo se permiten archivos .sql'), ok);
  },
});

// ── Tablas operativas (se borran en reset) ─────────────────────────────
const TABLAS_OPERATIVAS = [
  'incidentes',
  'log_actividad',
  'mantenimiento_preventivo',
  'mensajes',
  'ordenes_trabajo',
  'pagos',
  'paquetes',
  'recibos',
  'reservaciones',
  'rondas_vigilancia',
  'sesiones',
  'transacciones',
  'vehiculos',
  'visitantes',
  'votos',
  'encuestas',
  'asistentes_asamblea',
  'asambleas',
  'contactos_emergencia',
  'mascotas',
  'unidades',
  'residentes',
  'contratos_proveedor',
  'cuotas',
  'fondo_reserva',
  'presupuesto_anual',
  'anuncios'
];

router.use(verifyToken);
router.use(requireRol('super_admin', 'administrador')); // Solo admins

// ─────────────────────────────────────────────────────────────────────────
//  GET /api/sistema/info
// ─────────────────────────────────────────────────────────────────────────
router.get('/info', async (req, res) => {
  try {
    const dbName = process.env.DB_NAME || 'condominio_crm';

    const [[versionRow]] = await pool.query('SELECT VERSION() AS version');

    const [tablaInfo] = await pool.query(`
      SELECT
        TABLE_NAME AS nombre,
        TABLE_ROWS AS filas,
        ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024, 2) AS tamano_kb
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = ?
      ORDER BY TABLE_NAME ASC
    `, [dbName]);

    const totalRegistros = tablaInfo.reduce((s, t) => s + parseInt(t.filas || 0), 0);
    const totalTamanoKb = tablaInfo.reduce((s, t) => s + parseFloat(t.tamano_kb || 0), 0);

    res.json({
      success: true,
      data: {
        version: versionRow.version,
        base_datos: dbName,
        total_tablas: tablaInfo.length,
        total_registros: totalRegistros,
        tamano_total_kb: Math.round(totalTamanoKb * 100) / 100,
        tablas: tablaInfo,
        generado_en: new Date().toISOString(),
      },
    });
  } catch (err) {
    console.error('[Sistema/info]', err.message);
    res.status(500).json({ success: false, message: err.message });
  }
});

// ─────────────────────────────────────────────────────────────────────────
//  GET /api/sistema/backup
// ─────────────────────────────────────────────────────────────────────────
router.get('/backup', async (req, res) => {
  try {
    const dbName = process.env.DB_NAME || 'condominio_crm';
    const fecha = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
    const archivo = `backup_condominio_${fecha}.sql`;

    let sql = '';

    sql += `-- ═══════════════════════════════════════════════════════════\n`;
    sql += `-- Backup Condominio CRM\n`;
    sql += `-- Base de datos: ${dbName}\n`;
    sql += `-- Generado: ${new Date().toLocaleString('es-CO')}\n`;
    sql += `-- ═══════════════════════════════════════════════════════════\n\n`;
    sql += `SET NAMES utf8mb4;\n`;
    sql += `SET FOREIGN_KEY_CHECKS = 0;\n\n`;

    const [tablas] = await pool.query(`SHOW TABLES`);
    const nombreClave = `Tables_in_${dbName}`;

    for (const fila of tablas) {
      const tabla = fila[nombreClave] || Object.values(fila)[0];

      const [[createRow]] = await pool.query(`SHOW CREATE TABLE \`${tabla}\``);
      const createSql = createRow['Create Table'];

      sql += `-- ─── Tabla: ${tabla} ──────────────────────────────────────────────\n`;
      sql += `DROP TABLE IF EXISTS \`${tabla}\`;\n`;
      sql += `${createSql};\n\n`;

      const [filas] = await pool.query(`SELECT * FROM \`${tabla}\``);
      if (filas.length > 0) {
        const cols = Object.keys(filas[0]).map(c => `\`${c}\``).join(', ');

        const loteSize = 100;
        for (let i = 0; i < filas.length; i += loteSize) {
          const lote = filas.slice(i, i + loteSize);
          const valores = lote.map(fila => {
            const vals = Object.values(fila).map(v => {
              if (v === null || v === undefined) return 'NULL';
              if (typeof v === 'number') return v;
              if (v instanceof Date) return `'${v.toISOString().slice(0, 19).replace('T', ' ')}'`;
              if (Buffer.isBuffer(v)) return `X'${v.toString('hex')}'`;
              return `'${String(v).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/\n/g, '\\n').replace(/\r/g, '\\r')}'`;
            });
            return `(${vals.join(', ')})`;
          });
          sql += `INSERT INTO \`${tabla}\` (${cols}) VALUES\n${valores.join(',\n')};\n`;
        }
        sql += '\n';
      }
    }

    sql += `SET FOREIGN_KEY_CHECKS = 1;\n`;
    sql += `-- ═══════════════════════════════════════════════════════════\n`;

    const buffer = Buffer.from(sql, 'utf8');

    res.setHeader('Content-Type', 'application/octet-stream');
    res.setHeader('Content-Disposition', `attachment; filename="${archivo}"`);
    res.setHeader('Content-Length', buffer.length);
    res.send(buffer);

  } catch (err) {
    console.error('[Sistema/backup]', err.message);
    res.status(500).json({ success: false, message: err.message });
  }
});

// ─────────────────────────────────────────────────────────────────────────
//  POST /api/sistema/restaurar
// ─────────────────────────────────────────────────────────────────────────
router.post('/restaurar', upload.single('archivo'), async (req, res) => {
  if (!req.file) {
    return res.status(400).json({ success: false, message: 'No se recibió ningún archivo .sql' });
  }

  const contenido = req.file.buffer.toString('utf8');

  const todasSentencias = parsearSQL(contenido).filter(s => {
    const upper = s.trim().toUpperCase();
    if (!upper) return false;
    if (upper.startsWith('--')) return false;
    if (upper.startsWith('LOCK TABLE')) return false;
    if (upper.startsWith('UNLOCK TABLE')) return false;
    if (upper.startsWith('/*!')) return false;
    if (upper === 'SET FOREIGN_KEY_CHECKS = 0' || upper === 'SET FOREIGN_KEY_CHECKS=0') return false;
    if (upper === 'SET FOREIGN_KEY_CHECKS = 1' || upper === 'SET FOREIGN_KEY_CHECKS=1') return false;
    return true;
  });

  if (todasSentencias.length === 0) {
    return res.status(400).json({ success: false, message: 'El archivo SQL no contiene sentencias válidas' });
  }

  let ejecutadas = 0;
  let errores = [];
  const connection = await pool.getConnection();

  try {
    await connection.query('SET FOREIGN_KEY_CHECKS = 0');
    await connection.query("SET SESSION sql_mode = ''");

    // Quitar foreign keys existentes para evitar inconsistencias
    const [fks] = await connection.query(`
      SELECT TABLE_NAME, CONSTRAINT_NAME 
      FROM information_schema.key_column_usage 
      WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL
    `);

    for (const fk of fks) {
      try {
        await connection.query(`ALTER TABLE \`${fk.TABLE_NAME}\` DROP FOREIGN KEY \`${fk.CONSTRAINT_NAME}\``);
      } catch (e) {} // Ignorar si no existe
    }

    for (const stmt of todasSentencias) {
      const limpia = stmt.trim();
      if (!limpia) continue;
      try {
        await connection.query(limpia);
        ejecutadas++;
      } catch (e) {
        errores.push({
          sentencia: limpia.slice(0, 100) + (limpia.length > 100 ? '...' : ''),
          error: e.message,
        });
      }
    }

    await connection.query('SET FOREIGN_KEY_CHECKS = 1');
    connection.release();

    res.json({
      success: true,
      message: `Restauración completada: ${ejecutadas} sentencias ejecutadas${errores.length > 0 ? `, ${errores.length} errores` : ' sin errores'}.`,
      data: { total_sentencias: todasSentencias.length, ejecutadas, errores_count: errores.length, errores: errores.slice(0, 10) },
    });

  } catch (err) {
    console.error('[Sistema/restaurar]', err.message);
    try { await connection.query('SET FOREIGN_KEY_CHECKS = 1'); } catch (_) {}
    connection.release();
    res.status(500).json({ success: false, message: err.message });
  }
});

// ─────────────────────────────────────────────────────────────────────────
//  POST /api/sistema/reset
// ─────────────────────────────────────────────────────────────────────────
router.post('/reset', async (req, res) => {
  const { confirmacion } = req.body;
  if (confirmacion !== 'CONFIRMAR') {
    return res.status(400).json({
      success: false,
      message: 'Debe enviar { confirmacion: "CONFIRMAR" } para proceder con el reset',
    });
  }

  const connection = await pool.getConnection();

  try {
    await connection.query('SET FOREIGN_KEY_CHECKS = 0');
    const resultados = [];

    for (const tabla of TABLAS_OPERATIVAS) {
      try {
        const [check] = await connection.query(
          `SELECT COUNT(*) AS cnt FROM information_schema.TABLES 
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?`,
          [tabla]
        );
        
        if (parseInt(check[0].cnt) === 0) {
          resultados.push({ tabla, estado: 'no existe', filas_borradas: 0 });
          continue;
        }

        const [[cuentaAntes]] = await connection.query(`SELECT COUNT(*) AS n FROM \`${tabla}\``);
        await connection.query(`TRUNCATE TABLE \`${tabla}\``);
        resultados.push({ tabla, estado: 'limpiada', filas_borradas: parseInt(cuentaAntes.n) });

      } catch (e) {
        resultados.push({ tabla, estado: 'error', error: e.message });
      }
    }

    await connection.query('SET FOREIGN_KEY_CHECKS = 1');
    connection.release();

    const totalBorrados = resultados.reduce((s, r) => s + (r.filas_borradas || 0), 0);

    res.json({
      success: true,
      message: `Reset completado. ${totalBorrados} registros eliminados. Los catálogos y usuarios están intactos.`,
      data: { resultados, total_registros_eliminados: totalBorrados },
    });

  } catch (err) {
    console.error('[Sistema/reset]', err.message);
    try { await connection.query('SET FOREIGN_KEY_CHECKS = 1'); } catch (_) {}
    connection.release();
    res.status(500).json({ success: false, message: err.message });
  }
});

// ─────────────────────────────────────────────────────────────────────────
//  Utilidad: Parsear SQL
// ─────────────────────────────────────────────────────────────────────────
function parsearSQL(sql) {
  const sentencias = [];
  let actual = '';
  let enString = false;
  let charString = '';
  let i = 0;

  while (i < sql.length) {
    const c = sql[i];
    const c2 = sql[i + 1];

    if (!enString && c === '-' && c2 === '-') {
      while (i < sql.length && sql[i] !== '\n') i++;
      i++; continue;
    }
    if (!enString && c === '/' && c2 === '*') {
      i += 2;
      while (i < sql.length && !(sql[i] === '*' && sql[i + 1] === '/')) i++;
      i += 2; continue;
    }
    if (!enString && (c === "'" || c === '"' || c === '\`')) {
      enString = true; charString = c;
      actual += c; i++; continue;
    }
    if (enString) {
      if (c === '\\') { actual += c + (sql[i + 1] || ''); i += 2; continue; }
      if (c === charString) { enString = false; actual += c; i++; continue; }
    }
    if (!enString && c === ';') {
      const stmt = actual.trim();
      if (stmt) sentencias.push(stmt);
      actual = ''; i++; continue;
    }
    actual += c; i++;
  }
  const ultimo = actual.trim();
  if (ultimo) sentencias.push(ultimo);

  return sentencias;
}

module.exports = router;
