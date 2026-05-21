const { sequelize, Reserva, Cliente, Oportunidad, Paquete, Pago } = require('../models');
const { Op } = require('sequelize');

// GET /api/dashboard/kpis
const kpis = async (req, res) => {
  try {
    const isVendedor = req.usuario.rol !== 'Administrador';
    const agenteId = req.usuario.id;

    const hoy = new Date();
    const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    const inicioMesAnt = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
    const finMesAnt = new Date(hoy.getFullYear(), hoy.getMonth(), 0);

    const resWhere = { creado_en: { [Op.gte]: inicioMes } };
    const resWhereAnt = { creado_en: { [Op.between]: [inicioMesAnt, finMesAnt] } };
    if (isVendedor) { resWhere.agente_id = agenteId; resWhereAnt.agente_id = agenteId; }
    const reservasMes = await Reserva.count({ where: resWhere });
    const reservasMesAnt = await Reserva.count({ where: resWhereAnt });

    const filterPago = isVendedor ? `AND r.agente_id = ${agenteId}` : '';
    const joinPago = isVendedor ? `JOIN reservas r ON p.reserva_id = r.id` : '';
    const [[{ total: ingresosMesRaw }]] = await sequelize.query(`SELECT SUM(p.monto) as total FROM pagos p ${joinPago} WHERE p.estado='Verificado' AND p.fecha_pago >= :inicioMes ${filterPago}`, { replacements: { inicioMes }});
    const [[{ total: ingresosMesAntRaw }]] = await sequelize.query(`SELECT SUM(p.monto) as total FROM pagos p ${joinPago} WHERE p.estado='Verificado' AND p.fecha_pago BETWEEN :inicioMesAnt AND :finMesAnt ${filterPago}`, { replacements: { inicioMesAnt, finMesAnt }});
    const ingresosMes = Number(ingresosMesRaw) || 0;
    const ingresosMesAnt = Number(ingresosMesAntRaw) || 0;

    let utilidadMes = 0, utilidadMesAnt = 0;
    if (!isVendedor) {
      const [[{ costo: costoMesRaw }]] = await sequelize.query(
        `SELECT SUM(rv.costo_neto) as costo FROM reservas rv WHERE rv.costo_neto > 0 AND rv.estado != 'Cancelada' AND rv.creado_en >= :inicioMes`,
        { replacements: { inicioMes } }
      );
      const [[{ costo: costoMesAntRaw }]] = await sequelize.query(
        `SELECT SUM(rv.costo_neto) as costo FROM reservas rv WHERE rv.costo_neto > 0 AND rv.estado != 'Cancelada' AND rv.creado_en BETWEEN :inicioMesAnt AND :finMesAnt`,
        { replacements: { inicioMesAnt, finMesAnt } }
      );
      utilidadMes = ingresosMes - (Number(costoMesRaw) || 0);
      utilidadMesAnt = ingresosMesAnt - (Number(costoMesAntRaw) || 0);
    }

    const cliWhere = { creado_en: { [Op.gte]: inicioMes }, activo: 1 };
    const cliWhereAnt = { creado_en: { [Op.between]: [inicioMesAnt, finMesAnt] }, activo: 1 };
    if (isVendedor) { cliWhere.agente_id = agenteId; cliWhereAnt.agente_id = agenteId; }
    const { Cliente } = require('../models');
    const clientesMes = await Cliente.count({ where: cliWhere });
    const clientesMesAnt = await Cliente.count({ where: cliWhereAnt });

    const opWhere = { creado_en: { [Op.gte]: inicioMes } };
    const opWhereAnt = { creado_en: { [Op.between]: [inicioMesAnt, finMesAnt] } };
    if (isVendedor) { opWhere.agente_id = agenteId; opWhereAnt.agente_id = agenteId; }
    const oportunidadesTotal = await Oportunidad.count({ where: opWhere });
    const oportunidadesGanadas = await Oportunidad.count({ where: { ...opWhere, estado: 'Ganada' } });
    const tasaConversion = oportunidadesTotal > 0 ? Math.round((oportunidadesGanadas / oportunidadesTotal) * 100) : 0;
    const oportunidadesTotalAnt = await Oportunidad.count({ where: opWhereAnt });
    const oportunidadesGanadasAnt = await Oportunidad.count({ where: { ...opWhereAnt, estado: 'Ganada' } });
    const tasaConversionAnt = oportunidadesTotalAnt > 0 ? Math.round((oportunidadesGanadasAnt / oportunidadesTotalAnt) * 100) : 0;

    const cambio = (actual, anterior) => {
      if (!anterior) return actual > 0 ? 100 : 0;
      return Math.round(((actual - anterior) / anterior) * 100);
    };

    return res.json({
      ok: true,
      data: {
        reservas:       { valor: reservasMes,     cambio: cambio(reservasMes, reservasMesAnt) },
        ingresos:       { valor: ingresosMes,     cambio: cambio(ingresosMes, ingresosMesAnt) },
        nuevosClientes: { valor: clientesMes,     cambio: cambio(clientesMes, clientesMesAnt) },
        tasaConversion: { valor: tasaConversion,  cambio: cambio(tasaConversion, tasaConversionAnt) },
        utilidad:       { valor: utilidadMes,     cambio: cambio(utilidadMes, utilidadMesAnt) },
        esAdmin: !isVendedor,
      },
    });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/dashboard/ingresos-mensuales
const ingresosMensuales = async (req, res) => {
  try {
    const isVendedor = req.usuario.rol !== 'Administrador';
    const filterPago = isVendedor ? `AND r.agente_id = ${req.usuario.id}` : '';
    const joinPago = isVendedor ? `JOIN reservas r ON p.reserva_id = r.id` : '';

    const [ingresoRows] = await sequelize.query(`
      SELECT DATE_FORMAT(p.fecha_pago, '%Y-%m') AS mes, SUM(p.monto) AS total
      FROM pagos p ${joinPago}
      WHERE p.estado = 'Verificado'
        AND p.fecha_pago >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        ${filterPago}
      GROUP BY mes ORDER BY mes ASC
    `);

    let costoRows = [];
    if (!isVendedor) {
      [costoRows] = await sequelize.query(`
        SELECT DATE_FORMAT(rv.creado_en, '%Y-%m') AS mes, SUM(rv.costo_neto) AS costo
        FROM reservas rv
        WHERE rv.costo_neto > 0 AND rv.estado != 'Cancelada'
          AND rv.creado_en >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY mes ORDER BY mes ASC
      `);
    }

    const costoMap = {};
    costoRows.forEach(r => { costoMap[r.mes] = Number(r.costo) || 0; });
    const data = ingresoRows.map(r => ({
      mes: r.mes,
      total: Number(r.total) || 0,
      utilidad: (Number(r.total) || 0) - (costoMap[r.mes] || 0),
    }));

    return res.json({ ok: true, data, esAdmin: !isVendedor });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/dashboard/top-destinos
const topDestinos = async (req, res) => {
  try {
    const isVendedor = req.usuario.rol !== 'Administrador';
    const filterAgente = isVendedor ? `AND r.agente_id = ${req.usuario.id}` : '';
    const [rows] = await sequelize.query(`
      SELECT d.nombre AS destino, COUNT(r.id) AS total
      FROM reservas r
      JOIN paquetes p ON p.id = r.paquete_id
      JOIN destinos d ON d.id = p.destino_id
      WHERE r.estado != 'Cancelada' ${filterAgente}
      GROUP BY d.id, d.nombre ORDER BY total DESC LIMIT 5
    `);
    return res.json({ ok: true, data: rows });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/dashboard/actividad-reciente
const actividadReciente = async (req, res) => {
  try {
    const isVendedor = req.usuario.rol !== 'Administrador';
    const { Interaccion, Usuario, Cliente } = require('../models');
    const whereCond = {};
    if (isVendedor) whereCond.usuario_id = req.usuario.id;
    const items = await Interaccion.findAll({
      where: whereCond,
      include: [
        { association: 'usuario', attributes: ['id','nombre','apellido','avatar_url'] },
        { model: Cliente, attributes: ['id','nombre','apellido'] },
      ],
      order: [['fecha', 'DESC']],
      limit: 10,
    });
    return res.json({ ok: true, data: items });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/dashboard/tareas-pendientes
const tareasPendientes = async (req, res) => {
  try {
    const { Tarea } = require('../models');
    const tareas = await Tarea.findAll({
      where: {
        asignado_a: req.usuario.id,
        estado: { [Op.in]: ['Pendiente', 'En Progreso'] },
      },
      include: [{ association: 'cliente', attributes: ['id','nombre','apellido'] }],
      order: [['fecha_vence', 'ASC']],
      limit: 5,
    });
    return res.json({ ok: true, data: tareas });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};


// GET /api/dashboard/reservas-por-estado
const reservasPorEstado = async (req, res) => {
  try {
    const isVendedor = req.usuario.rol !== 'Administrador';
    const filterAgente = isVendedor ? `AND agente_id = ${req.usuario.id}` : '';
    const [rows] = await sequelize.query(`
      SELECT estado, COUNT(*) AS total
      FROM reservas
      WHERE 1=1 ${filterAgente}
      GROUP BY estado ORDER BY total DESC
    `);
    return res.json({ ok: true, data: rows });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/dashboard/oportunidades-etapa
const oportunidadesPorEtapa = async (req, res) => {
  try {
    const isVendedor = req.usuario.rol !== 'Administrador';
    const filterAgente = isVendedor ? `AND op.agente_id = ${req.usuario.id}` : '';
    const [rows] = await sequelize.query(`
      SELECT e.nombre AS etapa, COUNT(op.id) AS total,
             COALESCE(SUM(op.valor_estimado), 0) AS valor_total
      FROM etapas_pipeline e
      LEFT JOIN oportunidades op ON op.etapa_id = e.id ${filterAgente}
      GROUP BY e.id, e.nombre, e.orden
      ORDER BY e.orden ASC
    `);
    return res.json({ ok: true, data: rows });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/dashboard/reservas-por-mes  (last 6 months, bar chart data)
const reservasPorMes = async (req, res) => {
  try {
    const isVendedor = req.usuario.rol !== 'Administrador';
    const filterAgente = isVendedor ? `AND agente_id = ${req.usuario.id}` : '';
    const [rows] = await sequelize.query(`
      SELECT
        DATE_FORMAT(creado_en, '%Y-%m') AS mes,
        COUNT(*) AS total,
        SUM(CASE WHEN estado = 'Completada' THEN 1 ELSE 0 END) AS completadas,
        SUM(CASE WHEN estado = 'Cancelada'  THEN 1 ELSE 0 END) AS canceladas
      FROM reservas
      WHERE creado_en >= DATE_SUB(NOW(), INTERVAL 6 MONTH) ${filterAgente}
      GROUP BY mes ORDER BY mes ASC
    `);
    return res.json({ ok: true, data: rows });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

// GET /api/dashboard/clientes-por-fuente
const clientesPorFuente = async (req, res) => {
  try {
    const isVendedor = req.usuario.rol !== 'Administrador';
    const filterAgente = isVendedor ? `AND c.agente_id = ${req.usuario.id}` : '';
    const [rows] = await sequelize.query(`
      SELECT f.nombre AS fuente, COUNT(c.id) AS total
      FROM clientes c
      JOIN fuentes_origen f ON f.id = c.fuente_id
      WHERE c.activo = 1 ${filterAgente}
      GROUP BY f.id, f.nombre ORDER BY total DESC
    `);
    return res.json({ ok: true, data: rows });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ ok: false, msg: err.message });
  }
};

module.exports = {
  kpis, ingresosMensuales, topDestinos, actividadReciente, tareasPendientes,
  reservasPorEstado, oportunidadesPorEtapa, reservasPorMes, clientesPorFuente
};


