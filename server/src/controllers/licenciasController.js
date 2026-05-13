const { v4: uuidv4 } = require('uuid');
const { Licencia, Cliente, Producto } = require('../models');
const { notificarNuevaLicencia, notificarRenovacion } = require('../services/licenciaNotificaciones');

const include = [
    { model: Cliente,  as: 'cliente',  attributes: ['id', 'nombre', 'telefono', 'email'] },
    { model: Producto, as: 'producto', attributes: ['id', 'nombre', 'precio_mensual'] }
];

exports.listar = async (req, res) => {
    const licencias = await Licencia.findAll({ include, order: [['created_at', 'DESC']] });
    res.json({ ok: true, data: licencias });
};

exports.crear = async (req, res) => {
    const { cliente_id, producto_id, meses = 1 } = req.body;
    const fecha_inicio = new Date();
    const fecha_vencimiento = new Date();
    fecha_vencimiento.setMonth(fecha_vencimiento.getMonth() + parseInt(meses));

    const licencia = await Licencia.create({
        cliente_id, producto_id,
        license_key: uuidv4(),
        fecha_inicio: fecha_inicio.toISOString().split('T')[0],
        fecha_vencimiento: fecha_vencimiento.toISOString().split('T')[0]
    });
    const completa = await Licencia.findByPk(licencia.id, { include });
    if (completa?.cliente?.email) {
        notificarNuevaLicencia({
            clienteEmail:     completa.cliente.email,
            clienteNombre:    completa.cliente.nombre,
            productoNombre:   completa.producto?.nombre || 'Sistema',
            fechaVencimiento: completa.fecha_vencimiento,
            licenseKey:       completa.license_key
        });
    }
    res.json({ ok: true, data: completa });
};

exports.toggle = async (req, res) => {
    const lic = await Licencia.findByPk(req.params.id);
    if (!lic) return res.status(404).json({ ok: false, msg: 'No encontrada' });
    await lic.update({ activo: !lic.activo });
    res.json({ ok: true, activo: !lic.activo, msg: lic.activo ? 'Licencia desactivada' : 'Licencia activada' });
};

exports.renovar = async (req, res) => {
    const { meses = 1 } = req.body;
    const lic = await Licencia.findByPk(req.params.id, { include });
    if (!lic) return res.status(404).json({ ok: false, msg: 'No encontrada' });
    const base = new Date(lic.fecha_vencimiento) > new Date() ? new Date(lic.fecha_vencimiento) : new Date();
    base.setMonth(base.getMonth() + parseInt(meses));
    await lic.update({ fecha_vencimiento: base.toISOString().split('T')[0], activo: true });
    if (lic.cliente?.email) {
        notificarRenovacion({
            clienteEmail:          lic.cliente.email,
            clienteNombre:         lic.cliente.nombre,
            productoNombre:        lic.producto?.nombre || 'Sistema',
            nuevaFechaVencimiento: base.toISOString().split('T')[0]
        });
    }
    res.json({ ok: true, msg: `Renovada por ${meses} mes(es)`, fecha_vencimiento: base });
};

exports.eliminar = async (req, res) => {
    await Licencia.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Licencia eliminada' });
};

// Endpoint público — lo llaman los sistemas instalados en los clientes
exports.validar = async (req, res) => {
    const { license_key } = req.body;
    if (!license_key) return res.status(400).json({ valid: false, msg: 'Clave requerida' });

    const lic = await Licencia.findOne({ where: { license_key }, include });
    if (!lic)        return res.status(404).json({ valid: false, msg: 'Licencia no encontrada' });
    if (!lic.activo) return res.status(403).json({ valid: false, msg: 'Licencia desactivada. Contacte a su proveedor.' });

    const ahora  = new Date();
    const vence  = new Date(lic.fecha_vencimiento);
    if (ahora > vence) return res.status(403).json({ valid: false, msg: 'Licencia vencida. Contacte a su proveedor.' });

    await lic.update({ last_check: new Date() });
    const dias = Math.ceil((vence - ahora) / 86400000);
    res.json({ valid: true, client_name: lic.cliente.nombre, expires_at: lic.fecha_vencimiento, dias_restantes: dias });
};
