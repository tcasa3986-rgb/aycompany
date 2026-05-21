// Middleware global de manejo de errores
const errorHandler = (err, req, res, next) => {
  console.error('❌ Error:', err);

  // Error de MySQL duplicado
  if (err.code === 'ER_DUP_ENTRY') {
    return res.status(409).json({ success: false, message: 'El registro ya existe (valor duplicado)' });
  }

  // Error de FK constraint
  if (err.code === 'ER_ROW_IS_REFERENCED_2') {
    return res.status(409).json({ success: false, message: 'No se puede eliminar: el registro está relacionado con otros datos' });
  }

  // Error de validación
  if (err.name === 'ValidationError') {
    return res.status(400).json({ success: false, message: err.message });
  }

  // Error genérico
  const status = err.status || err.statusCode || 500;
  res.status(status).json({
    success: false,
    message: err.message || 'Error interno del servidor',
    ...(process.env.NODE_ENV === 'development' && { stack: err.stack }),
  });
};

// 404 Not Found
const notFound = (req, res, next) => {
  res.status(404).json({ success: false, message: `Ruta no encontrada: ${req.method} ${req.originalUrl}` });
};

module.exports = { errorHandler, notFound };
