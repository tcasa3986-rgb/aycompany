const express = require('express');
const router  = express.Router();
const { autenticar } = require('../middlewares/auth');
const {
  kpis, ingresosMensuales, topDestinos, actividadReciente, tareasPendientes,
  reservasPorEstado, oportunidadesPorEtapa, reservasPorMes, clientesPorFuente
} = require('../controllers/dashboardController');

router.get('/kpis',                  autenticar, kpis);
router.get('/ingresos-mensuales',    autenticar, ingresosMensuales);
router.get('/top-destinos',          autenticar, topDestinos);
router.get('/actividad-reciente',    autenticar, actividadReciente);
router.get('/tareas-pendientes',     autenticar, tareasPendientes);
router.get('/reservas-por-estado',   autenticar, reservasPorEstado);
router.get('/oportunidades-etapa',   autenticar, oportunidadesPorEtapa);
router.get('/reservas-por-mes',      autenticar, reservasPorMes);
router.get('/clientes-por-fuente',   autenticar, clientesPorFuente);


module.exports = router;
