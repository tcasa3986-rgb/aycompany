const { v4: uuidv4 } = require('uuid');
const nodemailer = require('nodemailer');
const { Cliente, Licencia, Producto } = require('../models');

exports.listar = async (req, res) => {
    const clientes = await Cliente.findAll({ order: [['created_at', 'DESC']] });
    res.json({ ok: true, data: clientes });
};

exports.obtener = async (req, res) => {
    const cliente = await Cliente.findByPk(req.params.id, {
        include: [{ model: Licencia, as: 'licencias', include: [{ model: Producto, as: 'producto' }] }]
    });
    if (!cliente) return res.status(404).json({ ok: false, msg: 'No encontrado' });
    res.json({ ok: true, data: cliente });
};

exports.crear = async (req, res) => {
    const cliente = await Cliente.create(req.body);
    res.json({ ok: true, data: cliente });
};

exports.actualizar = async (req, res) => {
    await Cliente.update(req.body, { where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Cliente actualizado' });
};

exports.eliminar = async (req, res) => {
    await Cliente.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Cliente eliminado' });
};

exports.generarPortalToken = async (req, res) => {
    try {
        const cliente = await Cliente.findByPk(req.params.id);
        if (!cliente) return res.status(404).json({ ok: false, msg: 'Cliente no encontrado' });

        const token = cliente.token_portal || uuidv4();
        await cliente.update({ token_portal: token });

        const BASE_URL = process.env.BASE_URL || 'https://mi-plataforma-production.up.railway.app';
        const portalUrl = `${BASE_URL}/cliente/${token}`;

        // Enviar por email si el cliente tiene correo
        if (cliente.email && req.body.enviarEmail !== false) {
            const gmailUser = process.env.GMAIL_USER;
            const gmailPass = process.env.GMAIL_APP_PASSWORD;
            const empresa   = process.env.NOMBRE_EMPRESA || 'AI Company CO';
            if (gmailUser && gmailPass) {
                const t = nodemailer.createTransport({ service: 'gmail', auth: { user: gmailUser, pass: gmailPass } });
                await t.sendMail({
                    from:    `"${empresa}" <${gmailUser}>`,
                    to:      cliente.email,
                    subject: `🔗 Su portal de cliente — ${empresa}`,
                    text: [
                        `Hola ${cliente.nombre},`,
                        ``,
                        `Aquí tiene el enlace a su portal personal donde puede:`,
                        `✅ Ver el estado de su licencia`,
                        `🔄 Renovar o pagar directamente`,
                        `🧾 Descargar sus facturas en PDF`,
                        `📝 Actualizar sus datos de contacto`,
                        ``,
                        `Su portal: ${portalUrl}`,
                        ``,
                        `Guarde este enlace — funciona siempre, no necesita contraseña.`,
                        ``,
                        `${empresa} · +57 321 267 4754`
                    ].join('\n')
                });
            }
        }

        res.json({ ok: true, token, portalUrl });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};
