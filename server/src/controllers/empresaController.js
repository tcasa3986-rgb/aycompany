const { Empresa, Usuario, Cliente, Licencia } = require('../models');
const bcrypt = require('bcryptjs');

exports.listar = async (req, res) => {
    const empresas = await Empresa.findAll({ order: [['created_at', 'DESC']] });
    res.json({ ok: true, data: empresas });
};

exports.obtener = async (req, res) => {
    const empresa = await Empresa.findByPk(req.params.id);
    if (!empresa) return res.status(404).json({ ok: false, msg: 'Empresa no encontrada' });

    const [totalClientes, totalLicencias] = await Promise.all([
        Cliente.count({ where: { empresa_id: empresa.id } }),
        Licencia.count({ where: { empresa_id: empresa.id } })
    ]);

    res.json({ ok: true, data: { ...empresa.toJSON(), totalClientes, totalLicencias } });
};

exports.crear = async (req, res) => {
    const { nombre, email_admin, password_admin, ...datos } = req.body;
    if (!nombre) return res.status(400).json({ ok: false, msg: 'Nombre requerido' });

    const slug = (datos.slug || nombre.toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-'));
    const empresa = await Empresa.create({ nombre, slug, ...datos });

    // Crear usuario admin de la empresa si se especifica
    if (email_admin && password_admin) {
        const hash = await bcrypt.hash(password_admin, 10);
        await Usuario.create({
            nombre:     `Admin ${nombre}`,
            email:      email_admin,
            password:   hash,
            rol:        'admin',
            empresa_id: empresa.id
        });
    }

    res.status(201).json({ ok: true, data: empresa, msg: 'Empresa creada' });
};

exports.actualizar = async (req, res) => {
    const empresa = await Empresa.findByPk(req.params.id);
    if (!empresa) return res.status(404).json({ ok: false, msg: 'Empresa no encontrada' });
    await empresa.update(req.body);
    res.json({ ok: true, data: empresa, msg: 'Empresa actualizada' });
};

exports.eliminar = async (req, res) => {
    const empresa = await Empresa.findByPk(req.params.id);
    if (!empresa) return res.status(404).json({ ok: false, msg: 'Empresa no encontrada' });
    await empresa.update({ activa: false });
    res.json({ ok: true, msg: 'Empresa desactivada' });
};
