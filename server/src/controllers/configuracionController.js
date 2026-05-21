const { Configuracion } = require('../models');

const DEFAULTS = [
    { clave: 'nombre_empresa',  valor: 'AI Company CO',   tipo: 'texto',   etiqueta: 'Nombre de la empresa',         grupo: 'empresa' },
    { clave: 'nit',             valor: '',                tipo: 'texto',   etiqueta: 'NIT',                           grupo: 'empresa' },
    { clave: 'telefono',        valor: '+57 321 267 4754',tipo: 'texto',   etiqueta: 'Teléfono de soporte',           grupo: 'empresa' },
    { clave: 'email_soporte',   valor: '',                tipo: 'texto',   etiqueta: 'Email de soporte',              grupo: 'empresa' },
    { clave: 'direccion',       valor: 'Bogotá, Colombia',tipo: 'texto',   etiqueta: 'Dirección',                     grupo: 'empresa' },
    { clave: 'logo_url',        valor: '',                tipo: 'url',     etiqueta: 'URL del logo',                  grupo: 'empresa' },
    { clave: 'color_primario',  valor: '#6366f1',         tipo: 'color',   etiqueta: 'Color primario',                grupo: 'apariencia' },
    { clave: 'color_secundario',valor: '#10b981',         tipo: 'color',   etiqueta: 'Color secundario',              grupo: 'apariencia' },
    { clave: 'moneda',          valor: 'COP',             tipo: 'texto',   etiqueta: 'Moneda',                        grupo: 'finanzas' },
    { clave: 'descuento_3m',    valor: '5',               tipo: 'numero',  etiqueta: 'Descuento 3 meses (%)',         grupo: 'finanzas' },
    { clave: 'descuento_6m',    valor: '10',              tipo: 'numero',  etiqueta: 'Descuento 6 meses (%)',         grupo: 'finanzas' },
    { clave: 'descuento_12m',   valor: '15',              tipo: 'numero',  etiqueta: 'Descuento 12 meses (%)',        grupo: 'finanzas' },
    { clave: 'whatsapp_soporte',valor: 'https://wa.me/573212674754', tipo: 'url', etiqueta: 'Link WhatsApp soporte', grupo: 'contacto' },
    { clave: 'base_url',        valor: 'https://mi-plataforma-production.up.railway.app', tipo: 'url', etiqueta: 'URL base del sistema', grupo: 'sistema' },
    { clave: 'auto_responder',  valor: 'false',           tipo: 'booleano',etiqueta: 'Auto-responder WhatsApp activo',grupo: 'sistema' },
    { clave: 'siigo_user',      valor: '',                tipo: 'texto',   etiqueta: 'Usuario SIIGO (email)',          grupo: 'integraciones' },
    { clave: 'siigo_partner',   valor: '',                tipo: 'texto',   etiqueta: 'Partner ID SIIGO',              grupo: 'integraciones' },
    { clave: 'siigo_activo',    valor: 'false',           tipo: 'booleano',etiqueta: 'Facturación SIIGO activa',       grupo: 'integraciones' },
];

async function seedDefaults() {
    for (const d of DEFAULTS) {
        await Configuracion.findOrCreate({ where: { clave: d.clave }, defaults: d });
    }
}

exports.listar = async (req, res) => {
    try {
        await seedDefaults();
        const configs = await Configuracion.findAll({ order: [['grupo','ASC'],['clave','ASC']] });
        // Convertir a mapa para fácil uso
        const mapa = {};
        configs.forEach(c => { mapa[c.clave] = c.valor; });
        res.json({ ok: true, data: configs, mapa });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.actualizar = async (req, res) => {
    try {
        const updates = req.body; // { clave: valor, ... }
        for (const [clave, valor] of Object.entries(updates)) {
            await Configuracion.upsert({ clave, valor });
        }
        res.json({ ok: true, msg: 'Configuración guardada' });
    } catch (err) {
        res.status(500).json({ ok: false, msg: err.message });
    }
};

exports.seedDefaults = seedDefaults;
