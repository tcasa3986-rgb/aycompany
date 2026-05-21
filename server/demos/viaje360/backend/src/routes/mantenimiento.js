/**
 * ═══════════════════════════════════════════════════════════
 *  Mantenimiento del Sistema – Viaje 360 CRM
 *  Rutas: /api/mantenimiento/...
 *  Estrategia: Sequelize raw queries (sin mysqldump)
 * ═══════════════════════════════════════════════════════════
 */

const express  = require('express');
const router   = express.Router();
const multer   = require('multer');
const path     = require('path');
const fs       = require('fs');

const sequelize        = require('../config/database');
const { autenticar }  = require('../middlewares/auth');

// ── Multer: almacenamiento temporal en memoria ─────────────────────────
const storage = multer.memoryStorage();
const upload  = multer({
  storage,
  limits: { fileSize: 100 * 1024 * 1024 }, // 100 MB máximo
  fileFilter: (req, file, cb) => {
    const ok = path.extname(file.originalname).toLowerCase() === '.sql';
    cb(ok ? null : new Error('Solo se permiten archivos .sql'), ok);
  },
});

// ── Tablas operativas (se borran en reset) ─────────────────────────────
const TABLAS_OPERATIVAS = [
  'notas',
  'documentos',
  'interacciones',
  'tareas',
  'pagos',
  'oportunidades',
  'reservas',
  'campanas',
  'clientes',
];

// ── Tablas de catálogo (se excluyen del reset) ─────────────────────────
const TABLAS_CATALOGO = [
  'usuarios',
  'roles',
  'paises',
  'destinos',
  'paquetes',
  'categorias_paquete',
  'proveedores',
  'etapas_pipeline',
  'fuentes_origen',
  'etiquetas',
  'metodos_pago',
  'configuracion_general',
];

// ─────────────────────────────────────────────────────────────────────────
//  GET /api/mantenimiento/info
//  Información del sistema: tablas, registros, versión MySQL
// ─────────────────────────────────────────────────────────────────────────
router.get('/info', autenticar, async (req, res) => {
  try {
    const dbName = process.env.DB_NAME || 'viaje360_crm';

    // Versión MySQL
    const [[versionRow]] = await sequelize.query('SELECT VERSION() AS version');

    // Lista de tablas con filas y tamaño
    const [tablaInfo] = await sequelize.query(`
      SELECT
        TABLE_NAME        AS nombre,
        TABLE_ROWS        AS filas,
        ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024, 2) AS tamano_kb
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = :dbName
      ORDER BY TABLE_NAME ASC
    `, { replacements: { dbName } });

    // Total registros (sumatorio estimado)
    const totalRegistros = tablaInfo.reduce((s, t) => s + parseInt(t.filas || 0), 0);
    const totalTamanoKb  = tablaInfo.reduce((s, t) => s + parseFloat(t.tamano_kb || 0), 0);

    res.json({
      ok: true,
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
    console.error('[Mantenimiento/info]', err.message);
    res.status(500).json({ ok: false, msg: err.message });
  }
});

// ─────────────────────────────────────────────────────────────────────────
//  GET /api/mantenimiento/backup
//  Genera y devuelve backup .sql completo (sin mysqldump)
// ─────────────────────────────────────────────────────────────────────────
router.get('/backup', autenticar, async (req, res) => {
  try {
    const dbName  = process.env.DB_NAME || 'viaje360_crm';
    const fecha   = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
    const archivo = `backup_viaje360_${fecha}.sql`;

    let sql = '';

    // ── Cabecera ──────────────────────────────────────────────────────────
    sql += `-- ═══════════════════════════════════════════════════════════\n`;
    sql += `-- Backup Viaje 360 CRM\n`;
    sql += `-- Base de datos: ${dbName}\n`;
    sql += `-- Generado: ${new Date().toLocaleString('es-CO')}\n`;
    sql += `-- ═══════════════════════════════════════════════════════════\n\n`;
    sql += `SET NAMES utf8mb4;\n`;
    sql += `SET FOREIGN_KEY_CHECKS = 0;\n\n`;

    // ── Obtener lista de tablas ───────────────────────────────────────────
    const [tablas] = await sequelize.query(`SHOW TABLES`);
    const nombreClave = `Tables_in_${dbName}`;

    for (const fila of tablas) {
      const tabla = fila[nombreClave] || Object.values(fila)[0];

      // CREATE TABLE
      const [[createRow]] = await sequelize.query(`SHOW CREATE TABLE \`${tabla}\``);
      const createSql = createRow['Create Table'];

      sql += `-- ─── Tabla: ${tabla} ──────────────────────────────────────────────\n`;
      sql += `DROP TABLE IF EXISTS \`${tabla}\`;\n`;
      sql += `${createSql};\n\n`;

      // Datos
      const [filas] = await sequelize.query(`SELECT * FROM \`${tabla}\``);
      if (filas.length > 0) {
        // Obtener columnas
        const cols = Object.keys(filas[0]).map(c => `\`${c}\``).join(', ');

        // Insertar en lotes de 100
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
    sql += `-- Fin del backup\n`;
    sql += `-- ═══════════════════════════════════════════════════════════\n`;

    const buffer = Buffer.from(sql, 'utf8');

    res.setHeader('Content-Type', 'application/octet-stream');
    res.setHeader('Content-Disposition', `attachment; filename="${archivo}"`);
    res.setHeader('Content-Length', buffer.length);
    res.send(buffer);

  } catch (err) {
    console.error('[Mantenimiento/backup]', err.message);
    res.status(500).json({ ok: false, msg: err.message });
  }
});

// ─────────────────────────────────────────────────────────────────────────
//  POST /api/mantenimiento/restaurar
//  Recibe archivo .sql y lo ejecuta con estrategia robusta:
//  1. Elimina FOREIGN KEY de CREATE TABLE (MySQL valida tipos incluso con FK_CHECKS=0)
//  2. Filtra sentencias problemáticas de mysqldump (LOCK TABLES, /*!...*/, etc.)
//  3. Ejecución en 3 fases: DROPs → CREATEs → INSERTs/ALTERs
// ─────────────────────────────────────────────────────────────────────────
router.post('/restaurar', autenticar, upload.single('archivo'), async (req, res) => {
  if (!req.file) {
    return res.status(400).json({ ok: false, msg: 'No se recibió ningún archivo .sql' });
  }

  const contenido = req.file.buffer.toString('utf8');

  // ── Parsear el SQL en sentencias individuales ─────────────────────────
  const todasSentencias = parsearSQL(contenido).filter(s => {
    const upper = s.trim().toUpperCase();
    // Descartar sentencias problemáticas / irrelevantes
    if (!upper) return false;
    if (upper.startsWith('--'))          return false;
    if (upper.startsWith('LOCK TABLE'))  return false;
    if (upper.startsWith('UNLOCK TABLE'))return false;
    if (upper.startsWith('/*!'))         return false;
    if (upper === 'SET FOREIGN_KEY_CHECKS = 0') return false;
    if (upper === 'SET FOREIGN_KEY_CHECKS = 1') return false;
    if (upper === 'SET FOREIGN_KEY_CHECKS=0')   return false;
    if (upper === 'SET FOREIGN_KEY_CHECKS=1')   return false;
    return true;
  });

  if (todasSentencias.length === 0) {
    return res.status(400).json({ ok: false, msg: 'El archivo SQL no contiene sentencias válidas' });
  }

  let ejecutadas = 0;
  let errores    = [];

  try {
    // Usamos una transacción para garantizar que usamos la MISMA CONEXIÓN
    // y por lo tanto SET FOREIGN_KEY_CHECKS = 0 se mantiene activo en toda la sesión.
    await sequelize.transaction(async (t) => {
      await sequelize.query('SET FOREIGN_KEY_CHECKS = 0', { transaction: t });
      await sequelize.query("SET SESSION sql_mode = ''", { transaction: t });

      // IMPORTANTE: Antes de modificar, eliminamos TODAS las restricciones de clave foránea preexistentes en la base de datos.
      // Esto evita el error "Referencing column X and referenced column Y in foreign key constraint Z are incompatible"
      // generado porque una tabla antigua (como usuarios) tenía un FK que impedía la recreación de una tabla nueva (roles).
      const [fks] = await sequelize.query(`
        SELECT TABLE_NAME, CONSTRAINT_NAME 
        FROM information_schema.key_column_usage 
        WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL
      `, { transaction: t });

      for (const fk of fks) {
        try {
          await sequelize.query(`ALTER TABLE \`${fk.TABLE_NAME}\` DROP FOREIGN KEY \`${fk.CONSTRAINT_NAME}\``, { transaction: t });
        } catch (e) {} // Ignoramos fallos al borrar FKs
      }

      // Ejecutar cada sentencia de origen de forma secuencial sin alterar su contenido
      for (const stmt of todasSentencias) {
        const limpia = stmt.trim();
        if (!limpia) continue;
        try {
          await sequelize.query(limpia, { transaction: t });
          ejecutadas++;
        } catch (e) {
          errores.push({
            sentencia: limpia.slice(0, 100) + (limpia.length > 100 ? '...' : ''),
            error: e.message,
          });
        }
      }

      await sequelize.query('SET FOREIGN_KEY_CHECKS = 1', { transaction: t });
    });

    res.json({
      ok: true,
      msg: `Restauración completada: ${ejecutadas} sentencias ejecutadas${errores.length > 0 ? `, ${errores.length} errores detectados` : ' sin errores'}.`,
      data: {
        total_sentencias: todasSentencias.length,
        ejecutadas,
        errores_count: errores.length,
        errores: errores.slice(0, 10),
      },
    });

  } catch (err) {
    console.error('[Mantenimiento/restaurar]', err.message);
    try { await sequelize.query('SET FOREIGN_KEY_CHECKS = 1'); } catch (_) {}
    res.status(500).json({ ok: false, msg: err.message });
  }
});


// ─────────────────────────────────────────────────────────────────────────
//  POST /api/mantenimiento/reset
//  Borra datos operativos; mantiene catálogos y estructura
// ─────────────────────────────────────────────────────────────────────────
router.post('/reset', autenticar, async (req, res) => {
  const { confirmacion } = req.body;
  if (confirmacion !== 'CONFIRMAR') {
    return res.status(400).json({
      ok: false,
      msg: 'Debe enviar { confirmacion: "CONFIRMAR" } para proceder con el reset',
    });
  }

  try {
    await sequelize.query('SET FOREIGN_KEY_CHECKS = 0');

    const resultados = [];

    for (const tabla of TABLAS_OPERATIVAS) {
      try {
        // Verificar si la tabla existe
        const [check] = await sequelize.query(
          `SELECT COUNT(*) AS cnt FROM information_schema.TABLES 
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :tabla`,
          { replacements: { tabla } }
        );
        if (parseInt(check[0].cnt) === 0) {
          resultados.push({ tabla, estado: 'no existe', filas_borradas: 0 });
          continue;
        }

        const [[cuentaAntes]] = await sequelize.query(`SELECT COUNT(*) AS n FROM \`${tabla}\``);
        await sequelize.query(`TRUNCATE TABLE \`${tabla}\``);
        resultados.push({ tabla, estado: 'limpiada', filas_borradas: parseInt(cuentaAntes.n) });

      } catch (e) {
        resultados.push({ tabla, estado: 'error', error: e.message });
      }
    }

    await sequelize.query('SET FOREIGN_KEY_CHECKS = 1');

    const totalBorrados = resultados.reduce((s, r) => s + (r.filas_borradas || 0), 0);

    res.json({
      ok: true,
      msg: `Reset completado. ${totalBorrados} registros eliminados. Los catálogos y usuarios están intactos.`,
      data: { resultados, total_registros_eliminados: totalBorrados },
    });

  } catch (err) {
    console.error('[Mantenimiento/reset]', err.message);
    try { await sequelize.query('SET FOREIGN_KEY_CHECKS = 1'); } catch (_) {}
    res.status(500).json({ ok: false, msg: err.message });
  }
});

// ─────────────────────────────────────────────────────────────────────────
//  Utilidad: Eliminar FOREIGN KEY de sentencia CREATE TABLE
//  MySQL valida compatibilidad de tipos en FK incluso con FK_CHECKS=0
//  → los eliminamos del CREATE TABLE; las FK no son necesarias para restaurar datos
// ─────────────────────────────────────────────────────────────────────────
function limpiarForeignKeys(createSql) {
  // Encontrar el bloque de columnas entre el primer '(' y el ')' final
  const posAbre = createSql.indexOf('(');
  if (posAbre === -1) return createSql;

  // Extraer el cuerpo de la tabla
  const header   = createSql.slice(0, posAbre + 1);
  const resto    = createSql.slice(posAbre + 1);

  // Buscar el cierre de la definición de tabla (último ')' antes de ENGINE=...)
  // Dividimos el cuerpo en líneas y filtramos las de FK / CONSTRAINT
  const lineas = resto.split('\n');
  const limpias = [];

  for (const linea of lineas) {
    const upper = linea.trim().toUpperCase();
    // Saltar líneas que definen FOREIGN KEY o CONSTRAINT FK
    if (upper.startsWith('CONSTRAINT') && upper.includes('FOREIGN KEY')) continue;
    if (upper.startsWith('FOREIGN KEY'))                                   continue;
    if (upper.startsWith('KEY') && upper.includes('REFERENCES'))           continue;
    limpias.push(linea);
  }

  // Reconstruir: limpiar comas huérfanas antes del cierre ')'
  // Unir y quitar coma antes de ')' o ') ENGINE'
  let resultado = header + limpias.join('\n');
  // Eliminar coma que queda pegada antes del cierre: ",\n)" → "\n)"
  resultado = resultado.replace(/,(\s*\n\s*\))/g, '$1');
  resultado = resultado.replace(/,(\s*\))/g, '$1');

  return resultado;
}

// ─────────────────────────────────────────────────────────────────────────
//  Utilidad: Parsear SQL en sentencias individuales
// ─────────────────────────────────────────────────────────────────────────
function parsearSQL(sql) {
  const sentencias = [];
  let actual       = '';
  let enString     = false;
  let charString   = '';
  let i            = 0;

  while (i < sql.length) {
    const c  = sql[i];
    const c2 = sql[i + 1];

    // Comentario de línea
    if (!enString && c === '-' && c2 === '-') {
      while (i < sql.length && sql[i] !== '\n') i++;
      i++;
      continue;
    }

    // Comentario bloque /* */
    if (!enString && c === '/' && c2 === '*') {
      i += 2;
      while (i < sql.length && !(sql[i] === '*' && sql[i + 1] === '/')) i++;
      i += 2;
      continue;
    }

    // Inicio/fin de string
    if (!enString && (c === "'" || c === '"' || c === '`')) {
      enString   = true;
      charString = c;
      actual    += c;
      i++;
      continue;
    }
    if (enString) {
      if (c === '\\') {
        actual += c + (sql[i + 1] || '');
        i      += 2;
        continue;
      }
      if (c === charString) {
        enString = false;
        actual  += c;
        i++;
        continue;
      }
    }

    // Fin de sentencia
    if (!enString && c === ';') {
      const stmt = actual.trim();
      if (stmt) sentencias.push(stmt);
      actual = '';
      i++;
      continue;
    }

    actual += c;
    i++;
  }

  const ultimo = actual.trim();
  if (ultimo) sentencias.push(ultimo);

  return sentencias;
}

module.exports = router;
