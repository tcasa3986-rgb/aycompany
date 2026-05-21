const bcrypt = require('bcryptjs');
const jwt    = require('jsonwebtoken');
const { Usuario } = require('../models');
const telegramService = require('../services/telegramService');
const { enviarEmail }  = require('../services/emailService');

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

        if (user.activo === false) {
            return res.status(403).json({ ok: false, msg: 'Tu cuenta está desactivada. Contacta al administrador.' });
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

        const expiresIn = user.rol === 'vendedor' ? '7d' : (process.env.JWT_EXPIRES_IN || '12h');
        const token = jwt.sign(
            { id: user.id, nombre: user.nombre, rol: user.rol },
            process.env.JWT_SECRET,
            { expiresIn }
        );

        res.json({ ok: true, token, user: { id: user.id, nombre: user.nombre, email: user.email, rol: user.rol } });
    } catch (err) {
        console.error('Error en login:', err.message);
        res.status(500).json({ ok: false, msg: 'Error interno del servidor' });
    }
};

function generarCodigoReferido() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let code = '';
    for (let i = 0; i < 7; i++) code += chars[Math.floor(Math.random() * chars.length)];
    return code;
}

// ── Registro público de vendedores ───────────────────────────────────────────
exports.registroVendedor = async (req, res) => {
    try {
        const { nombre, email, password, telefono, ciudad, codigo_ref } = req.body;

        if (!nombre || !email || !password)
            return res.status(400).json({ ok: false, msg: 'Nombre, email y contraseña son requeridos' });
        if (password.length < 6)
            return res.status(400).json({ ok: false, msg: 'La contraseña debe tener al menos 6 caracteres' });

        const emailLimpio = email.toLowerCase().trim();
        const existe = await Usuario.findOne({ where: { email: emailLimpio } });
        if (existe)
            return res.status(409).json({ ok: false, msg: 'Este email ya está registrado' });

        // Resolver referidor
        let referido_por = null;
        if (codigo_ref) {
            const ref = await Usuario.findOne({ where: { codigo_referido: codigo_ref.toUpperCase(), rol: 'vendedor' } });
            if (ref) referido_por = ref.id;
        }

        // Generar código único para este nuevo vendedor
        let codigo_referido;
        let intentos = 0;
        do {
            codigo_referido = generarCodigoReferido();
            intentos++;
        } while (intentos < 10 && await Usuario.findOne({ where: { codigo_referido } }));

        const hash    = await bcrypt.hash(password, 10);
        const usuario = await Usuario.create({
            nombre, email: emailLimpio, password: hash, rol: 'vendedor',
            telefono: telefono || null, ciudad: ciudad || null,
            referido_por, codigo_referido
        });

        // Notificar al admin por Telegram
        telegramService.enviar(
            `🙋 *Nuevo vendedor registrado*\n\n` +
            `👤 *Nombre:* ${nombre}\n` +
            `📧 *Email:* ${emailLimpio}\n` +
            `📱 *Teléfono:* ${telefono || '—'}\n` +
            `📍 *Ciudad:* ${ciudad || '—'}\n\n` +
            `_Ya puede acceder al portal de vendedores._`
        ).catch(() => {});

        // Email de bienvenida al vendedor
        const empresa  = process.env.NOMBRE_EMPRESA || 'AI Company CO';
        const BASE_URL = process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app';
        if (process.env.GMAIL_USER && process.env.GMAIL_APP_PASSWORD) {
            enviarEmail({
                gmailUser:    process.env.GMAIL_USER,
                gmailPass:    process.env.GMAIL_APP_PASSWORD,
                nombreAgente: empresa,
                nombreEmpresa: empresa,
                to:      emailLimpio,
                subject: `✅ Bienvenido al equipo de ventas — ${empresa}`,
                body: [
                    `Hola ${nombre},`,
                    ``,
                    `¡Bienvenido al equipo de ventas de ${empresa}!`,
                    ``,
                    `Ya puedes acceder a tu portal de vendedores:`,
                    `${BASE_URL}`,
                    ``,
                    `Con tu cuenta puedes:`,
                    `✅ Ver todos los sistemas disponibles para vender`,
                    `✅ Registrar tus prospectos`,
                    `✅ Agendar reuniones (el admin es notificado automáticamente)`,
                    ``,
                    `Tu modelo: tú cobras la personalización, nosotros manejamos la mensualidad ($250.000/mes mínimo).`,
                    ``,
                    `¿Dudas? Escríbenos por WhatsApp: https://wa.me/573212674754`,
                    ``,
                    `Equipo ${empresa}`
                ].join('\n')
            }).catch(() => {});
        }

        const token = jwt.sign(
            { id: usuario.id, nombre: usuario.nombre, rol: 'vendedor' },
            process.env.JWT_SECRET,
            { expiresIn: process.env.JWT_EXPIRES_IN || '12h' }
        );

        res.status(201).json({
            ok: true,
            msg: '¡Cuenta creada! Bienvenido al equipo.',
            token,
            user: { id: usuario.id, nombre: usuario.nombre, email: emailLimpio, rol: 'vendedor' }
        });
    } catch (err) {
        console.error('Error registro vendedor:', err.message);
        res.status(500).json({ ok: false, msg: 'Error al crear la cuenta' });
    }
};
