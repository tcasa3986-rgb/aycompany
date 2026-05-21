const pool = require('../config/db');

// List providers
exports.getAllProveedores = async (req, res, next) => {
  try {
    const [rows] = await pool.query('SELECT * FROM proveedores WHERE activo = 1 ORDER BY nombre ASC');
    res.json({ success: true, data: rows });
  } catch (error) { next(error); }
};

// Get single provider with contracts
exports.getProveedorById = async (req, res, next) => {
  try {
    const [proveedor] = await pool.query('SELECT * FROM proveedores WHERE id = ?', [req.params.id]);
    if (!proveedor.length) return res.status(404).json({ success: false, message: 'Proveedor no encontrado' });

    const [contratos] = await pool.query('SELECT * FROM contratos_proveedor WHERE proveedor_id = ? ORDER BY fecha_inicio DESC', [req.params.id]);
    
    res.json({ success: true, data: { ...proveedor[0], contratos } });
  } catch (error) { next(error); }
};

// Create provider
exports.createProveedor = async (req, res, next) => {
  try {
    const { nombre, rfc, tipo_servicio, contacto_nombre, contacto_telefono, contacto_email, direccion, notas } = req.body;
    const [result] = await pool.query(
      'INSERT INTO proveedores (nombre, rfc, tipo_servicio, contacto_nombre, contacto_telefono, contacto_email, direccion, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
      [nombre, rfc, tipo_servicio, contacto_nombre, contacto_telefono, contacto_email, direccion, notas]
    );
    res.status(201).json({ success: true, message: 'Proveedor creado', data: { id: result.insertId } });
  } catch (error) { next(error); }
};

// Update provider
exports.updateProveedor = async (req, res, next) => {
  try {
    const { nombre, rfc, tipo_servicio, contacto_nombre, contacto_telefono, contacto_email, direccion, calificacion, notas } = req.body;
    await pool.query(
      'UPDATE proveedores SET nombre=?, rfc=?, tipo_servicio=?, contacto_nombre=?, contacto_telefono=?, contacto_email=?, direccion=?, calificacion=?, notas=? WHERE id=?',
      [nombre, rfc, tipo_servicio, contacto_nombre, contacto_telefono, contacto_email, direccion, calificacion || 0, notas, req.params.id]
    );
    res.json({ success: true, message: 'Proveedor actualizado' });
  } catch (error) { next(error); }
};

// Delete provider (soft)
exports.deleteProveedor = async (req, res, next) => {
  try {
    await pool.query('UPDATE proveedores SET activo = 0 WHERE id = ?', [req.params.id]);
    res.json({ success: true, message: 'Proveedor dado de baja' });
  } catch (error) { next(error); }
};

// Contracts CRUD
exports.getContratos = async (req, res, next) => {
  try {
    const [rows] = await pool.query(`
      SELECT c.*, p.nombre as proveedor_nombre 
      FROM contratos_proveedor c 
      JOIN proveedores p ON c.proveedor_id = p.id 
      ORDER BY c.fecha_inicio DESC
    `);
    res.json({ success: true, data: rows });
  } catch (error) { next(error); }
};

exports.createContrato = async (req, res, next) => {
  try {
    const { proveedor_id, tipo, descripcion, monto_mensual, fecha_inicio, fecha_fin, estado } = req.body;
    const [result] = await pool.query(
      'INSERT INTO contratos_proveedor (proveedor_id, tipo, descripcion, monto_mensual, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?, ?, ?)',
      [proveedor_id, tipo, descripcion, monto_mensual || 0, fecha_inicio, fecha_fin || null, estado || 'activo']
    );
    res.status(201).json({ success: true, message: 'Contrato registrado', data: { id: result.insertId } });
  } catch (error) { next(error); }
};
