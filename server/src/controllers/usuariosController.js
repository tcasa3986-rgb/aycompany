const bcrypt = require('bcryptjs');
const { Usuario } = require('../models');

exports.listar = async (req, res) => {
    try {
        const users = await Usuario.findAll({ attributes: ['id','nombre','email','rol','created_at'], order: [['created_at','ASC']] });
        res.json({ ok: true, data: users });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.crear = async (req, res) => {
    try {
        const { nombre, email, password, rol } = req.body;
        if (!nombre || !email || !password) return res.status(400).json({ ok: false, msg: 'Nombre, email y contraseña requeridos' });
        const existe = await Usuario.findOne({ where: { email } });
        if (existe) return res.status(400).json({ ok: false, msg: 'Ya existe un usuario con ese email' });
        const hash = await bcrypt.hash(password, 10);
        const user = await Usuario.create({ nombre, email, password: hash, rol: rol || 'soporte' });
        res.status(201).json({ ok: true, msg: 'Usuario creado', data: { id: user.id, nombre: user.nombre, email: user.email, rol: user.rol } });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const user = await Usuario.findByPk(req.params.id);
        if (!user) return res.status(404).json({ ok: false, msg: 'Usuario no encontrado' });
        const { nombre, email, rol, password } = req.body;
        const updates = {};
        if (nombre) updates.nombre = nombre;
        if (email)  updates.email  = email;
        if (rol)    updates.rol    = rol;
        if (password) updates.password = await bcrypt.hash(password, 10);
        await user.update(updates);
        res.json({ ok: true, msg: 'Usuario actualizado' });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};

exports.eliminar = async (req, res) => {
    try {
        if (String(req.params.id) === String(req.user?.id))
            return res.status(400).json({ ok: false, msg: 'No puede eliminar su propio usuario' });
        const user = await Usuario.findByPk(req.params.id);
        if (!user) return res.status(404).json({ ok: false, msg: 'Usuario no encontrado' });
        await user.destroy();
        res.json({ ok: true, msg: 'Usuario eliminado' });
    } catch (err) { res.status(500).json({ ok: false, msg: err.message }); }
};
