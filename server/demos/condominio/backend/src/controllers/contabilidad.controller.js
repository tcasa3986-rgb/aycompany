const pool = require('../config/db');

// Obtener todas las cuentas contables / fondos
exports.getCuentas = async (req, res, next) => {
  try {
    const [rows] = await pool.query("SELECT id, nombre, tipo as banco, codigo as cuenta, (SELECT COALESCE(SUM(CASE WHEN tipo='ingreso' THEN monto ELSE -monto END),0) FROM transacciones WHERE cuenta_id = cuentas_contables.id) as saldo, 1 as activa, codigo as clabe FROM cuentas_contables WHERE activo = 1");
    // Si no hay cuentas, agregamos una simulada vacía para evitar fallos
    const data = rows.length ? rows : [{ id: 1, banco: 'Banco Principal', cuenta: '1234567890', clabe: '000000000', saldo: 0 }];
    res.json({ success: true, data });
  } catch (err) { next(err); }
};

// Obtener todos los movimientos
exports.getMovimientos = async (req, res, next) => {
  try {
    const { tipo, mes, anio } = req.query;
    let sql = `
      SELECT t.id, t.tipo, t.categoria, t.descripcion as concepto, t.monto, t.fecha, u.nombre as registrador 
      FROM transacciones t
      LEFT JOIN usuarios u ON t.registrado_por = u.id
      WHERE 1=1
    `;
    const params = [];

    if (tipo) {
      sql += ' AND t.tipo = ?';
      params.push(tipo);
    }
    if (mes && anio) {
      sql += ' AND MONTH(t.fecha) = ? AND YEAR(t.fecha) = ?';
      params.push(mes, anio);
    }

    sql += ' ORDER BY t.fecha DESC, t.id DESC';

    const [rows] = await pool.query(sql, params);
    res.json({ success: true, data: rows });
  } catch (err) { next(err); }
};

// Registrar un nuevo movimiento
exports.createMovimiento = async (req, res, next) => {
  try {
    const { tipo, categoria, concepto, monto, fecha } = req.body;
    
    // Buscar cuenta principal
    let [cuentas] = await pool.query('SELECT id FROM cuentas_contables LIMIT 1');
    if (!cuentas.length) {
       const [insertRes] = await pool.query("INSERT INTO cuentas_contables (codigo, nombre, tipo) VALUES ('001', 'Cuenta Principal', 'activo')");
       cuentas = [{ id: insertRes.insertId }];
    }
    const cuenta_id = cuentas[0].id;
    const registrador = req.usuario ? req.usuario.id : 1;

    // Registrar el movimiento
    const [result] = await pool.query(
      'INSERT INTO transacciones (cuenta_id, tipo, categoria, descripcion, monto, fecha, registrado_por) VALUES (?, ?, ?, ?, ?, ?, ?)',
      [cuenta_id, tipo, categoria, concepto, monto, fecha, registrador]
    );

    res.status(201).json({ success: true, message: 'Operación registrada correctamente', data: { id: result.insertId } });
  } catch (err) { console.error(err); next(err); }
};

// Resumen del Mes
exports.getResumen = async (req, res, next) => {
  try {
    const [ingresosRow] = await pool.query(`
      SELECT SUM(monto) as total FROM transacciones 
      WHERE tipo = 'ingreso' AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())
    `);
    const [egresosRow] = await pool.query(`
      SELECT SUM(monto) as total FROM transacciones 
      WHERE tipo = 'egreso' AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())
    `);
    
    const [saldosRow] = await pool.query(`
      SELECT SUM(CASE WHEN tipo='ingreso' THEN monto ELSE -monto END) as saldo_total FROM transacciones
    `);

    res.json({ 
      success: true, 
      data: {
        ingresos_mes: ingresosRow[0].total || 0,
        egresos_mes: egresosRow[0].total || 0,
        fondo_total: saldosRow[0].saldo_total || 0
      } 
    });
  } catch (err) { next(err); }
};
