const { Factura, Cliente, Pago, Licencia, Producto } = require('../models');

const include = [
    { model: Cliente, as: 'cliente', attributes: ['id', 'nombre', 'telefono'] },
    { model: Pago,    as: 'pago',    attributes: ['id', 'metodo_pago', 'meses', 'notas'] }
];

async function generarNumero() {
    const ultima = await Factura.findOne({ order: [['id', 'DESC']] });
    const num = ultima ? parseInt(ultima.numero.replace('FAC-', '')) + 1 : 1;
    return `FAC-${String(num).padStart(4, '0')}`;
}

exports.generarFactura = async ({ pago_id, cliente_id, concepto, monto, metodo_pago, fecha }) => {
    const numero = await generarNumero();
    return Factura.create({ numero, pago_id, cliente_id, concepto, monto, metodo_pago, fecha });
};

exports.listar = async (req, res) => {
    const facturas = await Factura.findAll({ include, order: [['id', 'DESC']] });
    res.json({ ok: true, data: facturas });
};

exports.eliminar = async (req, res) => {
    await Factura.destroy({ where: { id: req.params.id } });
    res.json({ ok: true, msg: 'Factura eliminada' });
};
