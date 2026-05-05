const bcrypt = require('bcryptjs');
const jwt    = require('jsonwebtoken');
const { Usuario } = require('../models');

// ── Bloqueo por intentos fallidos (en memoria) ───────────────────────────────
const intentosFallidos = new Map();
const MAX_INTENTOS = 5;
const BLOQUEO_MS   = 15 * 60 * 1000;

function estasBloqueado(email) {
    const info = intentosFallidos.get(email);
    if (!info?.lockUntil) return false;
    if (Date.now() < info.lockUntil) return true;
    intentosFallidos.delete(email);
    return false;
}

function registrarFallo(email) {
    const info = intentosFallidos.get(email) || { count: 0, lockUntil: null };
    info.count += 1;
    if (info.count >= MAX_INTENTOS) {
        info.lockUntil = Date.now() + BLOQUEO_MS;
        info.count = 0;
        console.warn(`🔒 Cuenta bloqueada temporalmente: ${email}`);
    }
    intentosFallidos.set(email, info);
}

function limpiarIntentos(email) {
    intentosFallidos.delete(email);
}

function minutosRestantes(email) {
    const info = intentosFallidos.get(email);
    if (!info?.lockUntil) return 0;
    return Math.ceil((info.lockUntil - Date.now()) / 60000);
}

// ── Login ────────────────────────────────────────────────────────────────────
exports.login = async (req, res) => {
    try {
        const { email, password } = req.body;

        if (!email || !password)
            return res.status(400).json({ ok: false, msg: 'Email y contraseña requeridos' });
        if (typeof email !== 'string' || typeof password !== 'string')
            return res.status(400).json({ ok: false, msg: 'Datos inválidos' });

        const emailLimpio = email.toLowerCase().trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailLimpio))
            return res.status(400).json({ ok: false, msg: 'Formato de email inválido' });
        if (password.length < 1 || password.length > 200)
            return res.status(400).json({ ok: false, msg: 'Contraseña inválida' });

        if (estasBloqueado(emailLimpio)) {
            const mins = minutosRestantes(emailLimpio);
            return res.status(429).json({
                ok: false,
                msg: `Cuenta bloqueada por demasiados intentos. Espera ${mins} minuto${mins !== 1 ? 's' : ''}.`
            });
        }

        const user = await Usuario.findOne({ where: { email: emailLimpio } });
        if (!user) {
            registrarFallo(emailLimpio);
            return res.status(401).json({ ok: false, msg: 'Credenciales incorrectas' });
        }

        const ok = await bcrypt.compare(password, user.password);
        if (!ok) {
            registrarFallo(emailLimpio);
            const info = intentosFallidos.get(emailLimpio);
            const restantes = MAX_INTENTOS - (info?.count || 0);
            return res.status(401).json({
                ok: false,
                msg: restantes > 0
                    ? `Credenciales incorrectas. ${restantes} intento${restantes !== 1 ? 's' : ''} restante${restantes !== 1 ? 's' : ''}.`
                    : 'Credenciales incorrectas'
            });
        }

        limpiarIntentos(emailLimpio);
        console.log(`✅ Login: ${emailLimpio} — IP: ${req.ip}`);

        const token = jwt.sign(
            { id: user.id, nombre: user.nombre, rol: user.rol },
            process.env.JWT_SECRET,
            { expiresIn: process.env.JWT_EXPIRES_IN || '12h' }
        );

        res.json({ ok: true, token, user: { id: user.id, nombre: user.nombre, email: user.email, rol: user.rol } });
    } catch (err) {
        console.error('Error en login:', err.message);
        res.status(500).json({ ok: false, msg: 'Error interno del servidor' });
    }
};
