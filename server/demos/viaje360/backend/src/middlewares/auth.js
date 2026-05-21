const jwt = require('jsonwebtoken');
require('dotenv').config();

const autenticar = (req, res, next) => {
  const auth = req.headers.authorization;
  let token = null;

  if (auth && auth.startsWith('Bearer ')) {
    token = auth.split(' ')[1];
  } else if (req.query.token) {
    token = req.query.token;
  }

  if (!token) {
    return res.status(401).json({ ok: false, msg: 'Token requerido' });
  }

  try {
    const payload = jwt.verify(token, process.env.JWT_SECRET);
    req.usuario = payload;
    next();
  } catch (err) {
    return res.status(401).json({ ok: false, msg: 'Token inválido o expirado' });
  }
};

const autorizar = (...roles) => (req, res, next) => {
  if (!roles.includes(req.usuario.rol))
    return res.status(403).json({ ok: false, msg: 'Acceso denegado' });
  next();
};

module.exports = { autenticar, autorizar };
