const jwt = require('jsonwebtoken');

// Usar una clave secreta por defecto si no está en el .env
const JWT_SECRET = process.env.JWT_SECRET || 'salonbelleza_secreto_super_seguro_123';

const authMiddleware = (req, res, next) => {
    // Obtener el token del header (formato: "Bearer token...")
    const authHeader = req.headers.authorization;

    if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return res.status(401).json({ error: 'Acceso denegado. No se proporcionó un token o formato inválido.' });
    }

    const token = authHeader.split(' ')[1];

    try {
        // Verificar el token
        const decoded = jwt.verify(token, JWT_SECRET);

        // Agregar los datos del usuario decodificados al objeto de la petición (req)
        req.user = decoded;

        // Continuar a la siguiente ruta/middleware
        next();
    } catch (error) {
        if (error.name === 'TokenExpiredError') {
            return res.status(401).json({ error: 'Sesión expirada. Por favor inicie sesión nuevamente.' });
        }
        return res.status(403).json({ error: 'Token inválido o corrupto.' });
    }
};

module.exports = authMiddleware;
