const { Habito, HabitoRegistro } = require('../models');
const { Op } = require('sequelize');
const { hoyBogota, aFecha, aStr } = require('../utils/licenciaCiclo');
const { rachaActual, mejorRacha, cumplimiento, ultimosDias, aplicaEn, cumplido, menosDias } = require('../utils/rachas');

const DIA = 86400000;

/** Carga los registros de un hábito en un mapa fecha -> registro. */
async function mapaDe(habitoId, desde) {
    const regs = await HabitoRegistro.findAll({
        where: { habito_id: habitoId, ...(desde ? { fecha: { [Op.gte]: desde } } : {}) },
    });
    const m = new Map();
    for (const r of regs) m.set(String(r.fecha).slice(0, 10), r);
    return m;
}

/** Arma el objeto que ve la pantalla: el hábito con sus cuentas. */
function conCuentas(h, porFecha, hoy) {
    const datos = { ...h.toJSON(), desde: String(h.created_at || h.createdAt || '').slice(0, 10) };
    const aplicaHoy = aplicaEn(h.dias, hoy);
    const regHoy = porFecha.get(hoy);
    return {
        ...datos,
        aplica_hoy:  aplicaHoy,
        hecho_hoy:   aplicaHoy ? cumplido(datos, regHoy) : null,
        veces_hoy:   Number(regHoy?.veces || 0),
        cai_hoy:     !!regHoy?.cai,
        racha:       rachaActual(datos, porFecha, hoy),
        mejor_racha: mejorRacha(datos, porFecha, hoy),
        mes:         cumplimiento(datos, porFecha, 30, hoy),
        semana:      ultimosDias(datos, porFecha, 7, hoy),
    };
}

// ── Listar ────────────────────────────────────────────────────────────────────
exports.listar = async (req, res) => {
    try {
        const hoy = hoyBogota();
        const incluirArchivados = req.query.todos === '1';
        const habitos = await Habito.findAll({
            where: incluirArchivados ? {} : { activo: true },
            order: [['orden', 'ASC'], ['id', 'ASC']],
        });

        // Un solo viaje a la base por todos los registros de los últimos 90 días.
        const desde = menosDias(hoy, 90);
        const regs = await HabitoRegistro.findAll({ where: { fecha: { [Op.gte]: desde } } });
        const porHabito = new Map();
        for (const r of regs) {
            if (!porHabito.has(r.habito_id)) porHabito.set(r.habito_id, new Map());
            porHabito.get(r.habito_id).set(String(r.fecha).slice(0, 10), r);
        }

        const data = habitos.map(h => conCuentas(h, porHabito.get(h.id) || new Map(), hoy));

        // Lo de hoy, para la cabecera y para el widget
        const deHoy = data.filter(h => h.activo && h.aplica_hoy);
        const hechos = deHoy.filter(h => h.hecho_hoy).length;

        res.json({
            ok: true, hoy, data,
            resumen: {
                de_hoy: deHoy.length,
                hechos,
                pendientes: deHoy.length - hechos,
                pct: deHoy.length ? Math.round((hechos / deHoy.length) * 100) : 100,
                mejor_racha: data.reduce((s, h) => Math.max(s, h.racha), 0),
            },
        });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

// ── Crear / editar / archivar ─────────────────────────────────────────────────
const CAMPOS = ['nombre', 'detalle', 'tipo', 'icono', 'color', 'dias', 'meta_diaria', 'hora', 'recordar', 'ambito', 'orden', 'activo', 'notas'];

function limpiar(body) {
    const out = {};
    for (const c of CAMPOS) if (c in body) out[c] = body[c];
    if (out.hora === '') out.hora = null;
    if (out.meta_diaria != null) out.meta_diaria = Math.max(1, Number(out.meta_diaria) || 1);
    // Días: se guarda normalizado para que el cálculo de racha no dependa de
    // cómo lo mandó la pantalla.
    if (out.dias != null) {
        const d = String(out.dias).split(',').map(s => s.trim()).filter(s => /^[0-6]$/.test(s));
        out.dias = [...new Set(d)].sort().join(',') || '0,1,2,3,4,5,6';
    }
    return out;
}

exports.crear = async (req, res) => {
    try {
        if (!req.body?.nombre) return res.status(400).json({ ok: false, msg: 'Ponle un nombre al hábito' });
        const h = await Habito.create(limpiar(req.body));
        res.json({ ok: true, msg: `"${h.nombre}" agregado`, data: h });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.actualizar = async (req, res) => {
    try {
        const h = await Habito.findByPk(req.params.id);
        if (!h) return res.status(404).json({ ok: false, msg: 'Ese hábito no existe' });
        await h.update(limpiar(req.body));
        res.json({ ok: true, msg: 'Actualizado', data: h });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

exports.eliminar = async (req, res) => {
    try {
        const h = await Habito.findByPk(req.params.id);
        if (!h) return res.status(404).json({ ok: false, msg: 'Ese hábito no existe' });
        // Archivar, no borrar: el historial de lo que ya cumpliste no se tira.
        // Con ?definitivo=1 sí se borra de verdad, con sus registros.
        if (req.query.definitivo === '1') {
            await HabitoRegistro.destroy({ where: { habito_id: h.id } });
            await h.destroy();
            return res.json({ ok: true, msg: `"${h.nombre}" eliminado` });
        }
        await h.update({ activo: false });
        res.json({ ok: true, msg: `"${h.nombre}" archivado` });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

// ── Marcar ────────────────────────────────────────────────────────────────────
/**
 * Marca o desmarca un día. Sin fecha, es hoy.
 *   { accion: 'alternar' | 'sumar' | 'quitar' | 'cai' }
 */
exports.marcar = async (req, res) => {
    try {
        const h = await Habito.findByPk(req.params.id);
        if (!h) return res.status(404).json({ ok: false, msg: 'Ese hábito no existe' });

        const hoy = hoyBogota();
        const fecha = /^\d{4}-\d{2}-\d{2}$/.test(req.body?.fecha || '') ? req.body.fecha : hoy;
        // No se deja marcar el futuro: inflaría la racha con días que no han pasado.
        if (fecha > hoy) return res.status(400).json({ ok: false, msg: 'No puedes marcar un día que no ha llegado' });

        const accion = req.body?.accion || 'alternar';
        const [reg] = await HabitoRegistro.findOrCreate({
            where: { habito_id: h.id, fecha },
            defaults: { habito_id: h.id, fecha, veces: 0, cai: false },
        });

        if (h.tipo === 'evitar') {
            const cai = accion === 'alternar' ? !reg.cai : accion === 'cai';
            await reg.update({ cai, veces: 0 });
        } else {
            const meta = Number(h.meta_diaria) || 1;
            let veces = Number(reg.veces || 0);
            if (accion === 'sumar')        veces = Math.min(meta, veces + 1);
            else if (accion === 'quitar')  veces = Math.max(0, veces - 1);
            else                           veces = veces >= meta ? 0 : meta;   // alternar
            await reg.update({ veces, cai: false });
        }
        if (req.body?.nota != null) await reg.update({ nota: String(req.body.nota).slice(0, 200) });

        const porFecha = await mapaDe(h.id, menosDias(hoy, 90));
        res.json({ ok: true, data: conCuentas(h, porFecha, hoy) });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

// ── Historial de un hábito (para el calendario) ───────────────────────────────
exports.historial = async (req, res) => {
    try {
        const h = await Habito.findByPk(req.params.id);
        if (!h) return res.status(404).json({ ok: false, msg: 'Ese hábito no existe' });
        const hoy = hoyBogota();
        const dias = Math.min(365, Math.max(7, Number(req.query.dias) || 90));
        const porFecha = await mapaDe(h.id, menosDias(hoy, dias));
        const datos = { ...h.toJSON(), desde: String(h.created_at || h.createdAt || '').slice(0, 10) };
        res.json({
            ok: true, hoy,
            habito: datos,
            dias: ultimosDias(datos, porFecha, dias, hoy),
            racha: rachaActual(datos, porFecha, hoy),
            mejor_racha: mejorRacha(datos, porFecha, hoy),
            cumplimiento: cumplimiento(datos, porFecha, dias, hoy),
        });
    } catch (e) { res.status(500).json({ ok: false, msg: e.message }); }
};

/**
 * Lo que falta por hacer hoy. Lo usan el aviso de Telegram, la pantalla Hoy y
 * el widget — una sola fuente para que no se contradigan entre sí.
 */
async function pendientesDeHoy(hoy = hoyBogota()) {
    const habitos = await Habito.findAll({ where: { activo: true }, order: [['hora', 'ASC'], ['orden', 'ASC']] });
    if (habitos.length === 0) return { de_hoy: [], pendientes: [], hechos: [] };

    const regs = await HabitoRegistro.findAll({ where: { fecha: hoy } });
    const porHabito = new Map(regs.map(r => [r.habito_id, r]));

    const deHoy = habitos.filter(h => aplicaEn(h.dias, hoy));
    const hechos = [], pendientes = [];
    for (const h of deHoy) {
        const datos = { ...h.toJSON(), desde: String(h.created_at || h.createdAt || '').slice(0, 10) };
        (cumplido(datos, porHabito.get(h.id)) ? hechos : pendientes).push(h);
    }
    return { de_hoy: deHoy, pendientes, hechos };
}

exports.pendientesDeHoy = pendientesDeHoy;
