const jwt = require('jsonwebtoken');
require('dotenv').config();

// Verificar token de acceso
const verifyToken = (req, res, next) => {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1]; // Bearer <token>

  if (!token) {
    return res.status(401).json({ success: false, message: 'Token de acceso requerido' });
  }

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    req.usuario = decoded;
    next();
  } catch (err) {
    if (err.name === 'TokenExpiredError') {
      return res.status(401).json({ success: false, message: 'Token expirado', expired: true });
    }
    return res.status(403).json({ success: false, message: 'Token inválido' });
  }
};

// Verificar roles permitidos
const requireRol = (...roles) => {
  return (req, res, next) => {
    if (!req.usuario) {
      return res.status(401).json({ success: false, message: 'No autenticado' });
    }
    if (!roles.includes(req.usuario.rol)) {
      return res.status(403).json({ success: false, message: `Acceso denegado. Roles permitidos: ${roles.join(', ')}` });
    }
    next();
  };
};

module.exports = { verifyToken, requireRol };
