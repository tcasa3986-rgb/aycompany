const { Licencia, Cliente, Producto } = require('../models');
const { Op } = require('sequelize');
const { notificarVencimientoCercano, notificarLicenciaBloqueada } = require('../services/licenciaNotificaciones');

const include = [
    { model: Cliente,  as: 'cliente',  attributes: ['id','nombre','email','telefono'] },
    { model: Producto, as: 'producto', attributes: ['nombre','precio_mensual'] }
];

exports.resumen = async (req, res) => {
    try {
        const hoy    = new Date();
        const en30   = new Date(); en30.setDate(en30.getDate() + 30);
        const hoyStr = hoy.toISOString().split('T')[0];
        const en30Str= en30.toISOString().split('T')[0];

        const [vencidas, porVencer] = await Promise.all([
            Licencia.findAll({
                where: { activo: true, fecha_vencimiento: { [Op.lt]: hoyStr } },
                include, order: [['fecha_vencimiento', 'ASC']]
            }),
            Licencia.findAll({
                where: { activo: true, fecha_vencimiento: { [Op.between]: [hoyStr, en30Str] } },
                include, order: [['fecha_vencimiento', 'ASC']]
            })
        ]);

        const calcDias = (f) => Math.ceil((new Date(f) - hoy) / 86400000);

        const mapLic = (l) => ({
            id:                l.id,
            license_key:       l.license_key,
            fecha_vencimiento: l.fecha_vencimiento,
            dias:              calcDias(l.fecha_vencimiento),
            cliente_id:        l.cliente?.id,
            cliente:           l.cliente?.nombre,
            email:             l.cliente?.email,
            telefono:          l.cliente?.telefono,
            producto:          l.producto?.nombre,
            precio_mensual:    Number(l.producto?.precio_mensual || 0)
        });

        const vencidasData  = vencidas.map(mapLic);
        const porVencerData = porVencer.map(mapLic);

        const valorRiesgo = vencidasData.reduce((s, l) => s + l.precio_mensual, 0);

        res.json({
            ok: true,
            data: {
                vencidas:   vencidasData,
                porVencer:  porVencerData,
                stats: {
                    totalVencidas:  vencidasData.length,
                    totalPorVencer: porVencerData.length,
                    valorRiesgo,
                    sinEmail: vencidasData.filter(l => !l.email).length
                }
            }
        });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.enviarRecordatorio = async (req, res) => {
    try {
        const { licencia_id } = req.body;
        const lic = await Licencia.findByPk(licencia_id, { include });
        if (!lic) return res.status(404).json({ ok: false, msg: 'Licencia no encontrada' });

        const hoy  = new Date();
        const dias = Math.ceil((new Date(lic.fecha_vencimiento) - hoy) / 86400000);

        if (dias < 0) {
            await notificarLicenciaBloqueada({
                clienteEmail: lic.cliente?.email, clienteNombre: lic.cliente?.nombre,
                productoNombre: lic.producto?.nombre, licenseKey: lic.license_key,
                clienteTelefono: lic.cliente?.telefono
            });
        } else {
            await notificarVencimientoCercano({
                clienteEmail: lic.cliente?.email, clienteNombre: lic.cliente?.nombre,
                productoNombre: lic.producto?.nombre, fechaVencimiento: lic.fecha_vencimiento,
                diasRestantes: dias, licenseKey: lic.license_key, clienteTelefono: lic.cliente?.telefono
            });
        }

        res.json({ ok: true, msg: `Recordatorio enviado a ${lic.cliente?.email || 'sin email'}` });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.enviarMasivo = async (req, res) => {
    try {
        const hoyStr = new Date().toISOString().split('T')[0];
        const vencidas = await Licencia.findAll({
            where: { activo: true, fecha_vencimiento: { [Op.lt]: hoyStr } },
            include
        });

        let enviados = 0;
        for (const lic of vencidas) {
            if (!lic.cliente?.email) continue;
            try {
                await notificarLicenciaBloqueada({
                    clienteEmail: lic.cliente.email, clienteNombre: lic.cliente.nombre,
                    productoNombre: lic.producto?.nombre, licenseKey: lic.license_key,
                    clienteTelefono: lic.cliente?.telefono
                });
                enviados++;
            } catch {}
        }

        res.json({ ok: true, msg: `Recordatorios enviados: ${enviados} de ${vencidas.length}` });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};
