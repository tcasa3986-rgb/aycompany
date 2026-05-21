import React, { useEffect, useState } from 'react';
import {
  Chart as ChartJS, CategoryScale, LinearScale, PointElement,
  LineElement, BarElement, ArcElement, RadialLinearScale, Filler, Tooltip, Legend
} from 'chart.js';
import { Line, Doughnut, Bar, Radar } from 'react-chartjs-2';
import {
  CalendarCheck, DollarSign, Users, TrendingUp, TrendingDown,
  ArrowUpRight, ArrowDownRight, Clock, CheckCircle2,
  Phone, Mail, MessageSquare, Clipboard, BarChart2, Target, Activity,
} from 'lucide-react';
import api from '../services/api';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

ChartJS.register(
  CategoryScale, LinearScale, PointElement, LineElement,
  BarElement, ArcElement, RadialLinearScale, Filler, Tooltip, Legend
);

import useConfigStore from '../store/configStore';

// ─── Helpers ──────────────────────────────────────────────────
const fmtMoney = (n) => {
  const m = useConfigStore.getState().config?.moneda_simbolo || '$';
  return `${m}${(+(n||0)).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
};
const tipoInteraccionIcon = {
  Llamada: <Phone size={14} />, Email: <Mail size={14} />, WhatsApp: <MessageSquare size={14} />,
  Reunión: <Users size={14} />, Nota: <Clipboard size={14} />,
  Cotización: <DollarSign size={14} />, Seguimiento: <Clock size={14} />,
};
const tipoColor = {
  Llamada: 'blue', Email: 'purple', WhatsApp: 'green',
  Reunión: 'amber', Nota: 'gray', Cotización: 'cyan', Seguimiento: 'amber',
};
const tooltipLight = {
  backgroundColor: 'white', borderColor: '#E2E8F0', borderWidth: 1,
  titleColor: '#0F172A', bodyColor: '#475569', padding: 12,
};

// ─── KPI Card ─────────────────────────────────────────────────
function KpiCard({ icon: Icon, label, value, cambio, color, prefix = '', suffix = '', delay = 0 }) {
  const [displayed, setDisplayed] = useState(0);
  const isUp = cambio >= 0;

  useEffect(() => {
    let start = 0;
    const end = parseFloat(value) || 0;
    if (end === 0) return;
    const step = end / (1200 / 16);
    const timer = setInterval(() => {
      start += step;
      if (start >= end) { setDisplayed(end); clearInterval(timer); }
      else setDisplayed(Math.floor(start));
    }, 16);
    return () => clearInterval(timer);
  }, [value]);

  return (
    <div className={`kpi-card ${color} animate-fade-in-up stagger-${delay}`}>
      <div className="kpi-top">
        <div className="kpi-icon-wrap"><Icon size={20} /></div>
        <span className={`kpi-badge ${isUp ? 'up' : 'down'}`}>
          {isUp ? <ArrowUpRight size={12} /> : <ArrowDownRight size={12} />}
          {Math.abs(cambio)}%
        </span>
      </div>
      <div className="kpi-label">{label}</div>
      <div className="kpi-value">
        {prefix}{suffix === '%' ? value : displayed.toLocaleString('es')}{suffix}
      </div>
    </div>
  );
}

// ─── Gráfico Ingresos ─────────────────────────────────────────
function GraficoIngresos({ data, esAdmin }) {
  const labels = data.map(d => {
    const [y, m] = d.mes.split('-');
    return format(new Date(+y, +m - 1), 'MMM yy', { locale: es });
  });
  const showUtilidad = esAdmin && data.some(d => d.utilidad > 0);

  const cfg = {
    labels,
    datasets: [
      {
        label: `Ingresos (${useConfigStore.getState().config?.moneda_simbolo || '$'})`,

        data: data.map(d => +d.total),
        borderColor: '#7C3AED',
        backgroundColor: (ctx) => {
          const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 220);
          g.addColorStop(0, 'rgba(124,58,237,0.20)');
          g.addColorStop(1, 'rgba(124,58,237,0)');
          return g;
        },
        borderWidth: 2.5, tension: 0.4, fill: true,
        pointBackgroundColor: '#7C3AED', pointRadius: 4, pointHoverRadius: 6,
      },
      ...(showUtilidad ? [{
        label: 'Utilidad Neta',
        data: data.map(d => +d.utilidad > 0 ? +d.utilidad : 0),
        borderColor: '#0D9488',
        backgroundColor: (ctx) => {
          const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 220);
          g.addColorStop(0, 'rgba(13,148,136,0.14)');
          g.addColorStop(1, 'rgba(13,148,136,0)');
          return g;
        },
        borderWidth: 2, tension: 0.4, fill: true, borderDash: [5, 3],
        pointBackgroundColor: '#0D9488', pointRadius: 4, pointHoverRadius: 6,
      }] : [])
    ],
  };

  const opts = {
    responsive: true, maintainAspectRatio: false,
    plugins: {
      legend: {
        display: showUtilidad, position: 'top',
        labels: { color: '#64748B', font: { family: 'Inter', size: 11 }, usePointStyle: true, boxWidth: 8 }
      },
      tooltip: { ...tooltipLight, callbacks: { label: (c) => ` ${fmtMoney(c.parsed.y)}` } }
    },
    scales: {
      x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { family: 'Inter', size: 11 } } },
      y: {
        grid: { color: 'rgba(0,0,0,0.04)', borderDash: [4, 4] },
        ticks: { color: '#94A3B8', font: { family: 'Inter', size: 11 }, maxTicksLimit: 6 }
      },
    },
  };

  return (
    <div className="card" style={{ height: '300px' }}>
      <div className="card-header">
        <div>
          <div className="card-title">Ingresos Mensuales</div>
          <div className="card-subtitle">{showUtilidad ? 'Ingresos vs Utilidad — Últimos 12 meses' : 'Últimos 12 meses'}</div>
        </div>
        <Activity size={18} style={{ color: '#7C3AED' }} />
      </div>
      <div style={{ height: '210px' }}>
        {data.length
          ? <Line data={cfg} options={opts} />
          : <div className="empty-state" style={{ padding: 20 }}><p className="text-muted">Sin datos aún</p></div>}
      </div>
    </div>
  );
}

// ─── Top Destinos Donut ───────────────────────────────────────
function GraficoDestinos({ data }) {
  const cfg = {
    labels: data.map(d => d.destino),
    datasets: [{
      data: data.map(d => +d.total),
      backgroundColor: ['#7C3AED', '#EC4899', '#0D9488', '#F59E0B', '#3B82F6'],
      borderColor: '#FFFFFF', borderWidth: 3, hoverOffset: 8,
    }],
  };
  const opts = {
    responsive: true, maintainAspectRatio: false, cutout: '70%',
    plugins: {
      legend: {
        display: true, position: window.innerWidth < 640 ? 'bottom' : 'right',
        labels: { color: '#475569', font: { family: 'Inter', size: 12 }, usePointStyle: true, boxWidth: 8, padding: 16 }
      },
      tooltip: tooltipLight,
    },
  };
  return (
    <div className="card" style={{ height: '300px' }}>
      <div className="card-header">
        <div>
          <div className="card-title">Top Destinos</div>
          <div className="card-subtitle">Por reservas totales</div>
        </div>
        <Target size={18} style={{ color: '#EC4899' }} />
      </div>
      <div style={{ height: '210px' }}>
        {data.length
          ? <Doughnut data={cfg} options={opts} />
          : <div className="empty-state" style={{ padding: 20 }}><p className="text-muted">Sin datos aún</p></div>}
      </div>
    </div>
  );
}

// ─── Reservas por Mes (Bar) ───────────────────────────────────
function GraficoReservasMes({ data }) {
  const labels = data.map(d => {
    const [y, m] = d.mes.split('-');
    return format(new Date(+y, +m - 1), 'MMM', { locale: es });
  });
  const cfg = {
    labels,
    datasets: [
      {
        label: 'Total', data: data.map(d => +d.total),
        backgroundColor: 'rgba(124,58,237,0.80)', borderRadius: 6, borderSkipped: false,
      },
      {
        label: 'Completadas', data: data.map(d => +d.completadas),
        backgroundColor: 'rgba(13,148,136,0.75)', borderRadius: 6, borderSkipped: false,
      },
      {
        label: 'Canceladas', data: data.map(d => +d.canceladas),
        backgroundColor: 'rgba(236,72,153,0.70)', borderRadius: 6, borderSkipped: false,
      },
    ],
  };
  const opts = {
    responsive: true, maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'top',
        labels: { color: '#64748B', font: { family: 'Inter', size: 11 }, usePointStyle: true, boxWidth: 8 }
      },
      tooltip: tooltipLight,
    },
    scales: {
      x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { family: 'Inter', size: 11 } } },
      y: {
        grid: { color: 'rgba(0,0,0,0.04)' },
        ticks: { color: '#94A3B8', font: { family: 'Inter', size: 11 }, stepSize: 1 }
      },
    },
  };
  return (
    <div className="card" style={{ height: '300px' }}>
      <div className="card-header">
        <div>
          <div className="card-title">Reservas por Mes</div>
          <div className="card-subtitle">Últimos 6 meses — Total, Completadas y Canceladas</div>
        </div>
        <BarChart2 size={18} style={{ color: '#7C3AED' }} />
      </div>
      <div style={{ height: '210px' }}>
        {data.length
          ? <Bar data={cfg} options={opts} />
          : <div className="empty-state" style={{ padding: 20 }}><p className="text-muted">Sin datos aún</p></div>}
      </div>
    </div>
  );
}

// ─── Estado de Reservas Donut ─────────────────────────────────
function GraficoEstadoReservas({ data }) {
  const estadoColors = {
    Completada: '#0D9488', Confirmada: '#7C3AED',
    Pendiente: '#F59E0B', 'En Curso': '#3B82F6', Cancelada: '#EC4899',
  };
  const cfg = {
    labels: data.map(d => d.estado),
    datasets: [{
      data: data.map(d => +d.total),
      backgroundColor: data.map(d => estadoColors[d.estado] || '#94A3B8'),
      borderColor: '#FFFFFF', borderWidth: 3, hoverOffset: 8,
    }],
  };
  const opts = {
    responsive: true, maintainAspectRatio: false, cutout: '68%',
    plugins: {
      legend: {
        display: true, position: 'bottom',
        labels: { color: '#475569', font: { family: 'Inter', size: 11 }, usePointStyle: true, boxWidth: 8, padding: 12 }
      },
      tooltip: tooltipLight,
    },
  };
  const total = data.reduce((s, d) => s + +d.total, 0);
  return (
    <div className="card" style={{ height: '300px' }}>
      <div className="card-header">
        <div>
          <div className="card-title">Estado de Reservas</div>
          <div className="card-subtitle">{total} reservas en total</div>
        </div>
        <CalendarCheck size={18} style={{ color: '#0D9488' }} />
      </div>
      <div style={{ height: '210px', position: 'relative', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
        {data.length ? (
          <>
            <div style={{ width: '100%', height: '100%' }}>
              <Doughnut data={cfg} options={opts} />
            </div>
            <div style={{
              position: 'absolute', top: '44%', left: '50%', transform: 'translate(-50%, -50%)',
              textAlign: 'center', pointerEvents: 'none',
            }}>
              <div style={{ fontSize: '1.4rem', fontWeight: 900, color: '#0F172A', fontFamily: 'Plus Jakarta Sans' }}>{total}</div>
              <div style={{ fontSize: '0.68rem', color: '#94A3B8' }}>Total</div>
            </div>
          </>
        ) : <div className="empty-state" style={{ padding: 20 }}><p className="text-muted">Sin datos aún</p></div>}
      </div>
    </div>
  );
}

// ─── Fuentes de Leads (Radar) ─────────────────────────────────
function GraficoFuentes({ data }) {
  const cfg = {
    labels: data.map(d => d.fuente),
    datasets: [{
      label: 'Cantidad de Clientes',
      data: data.map(d => +d.total),
      backgroundColor: 'rgba(124, 58, 237, 0.2)',
      borderColor: '#7C3AED',
      borderWidth: 2,
      pointBackgroundColor: '#7C3AED',
      pointBorderColor: '#fff',
      pointHoverBackgroundColor: '#fff',
      pointHoverBorderColor: '#7C3AED'
    }]
  };

  const opts = {
    responsive: true, maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: tooltipLight,
    },
    scales: {
      r: {
        angleLines: { color: 'rgba(0,0,0,0.05)' },
        grid: { color: 'rgba(0,0,0,0.05)' },
        pointLabels: { color: '#64748B', font: { size: 10, family: 'Inter' } },
        ticks: { display: false, stepSize: 1 }
      }
    }
  };

  return (
    <div className="card" style={{ height: '300px' }}>
      <div className="card-header">
        <div>
          <div className="card-title">Fuentes de Leads</div>
          <div className="card-subtitle">Distribución por origen</div>
        </div>
        <Target size={18} style={{ color: '#7C3AED' }} />
      </div>
      <div style={{ height: '210px' }}>
        {data.length
          ? <Radar data={cfg} options={opts} />
          : <div className="empty-state" style={{ padding: 20 }}><p className="text-muted">Sin datos aún</p></div>}
      </div>
    </div>
  );
}

// ─── Pipeline Funnel (Progress bars) ─────────────────────────
const PIPE_COLORS = ['#7C3AED', '#8B5CF6', '#A78BFA', '#EC4899', '#F472B6', '#0D9488', '#2DD4BF'];


function GraficoPipeline({ data }) {
  const max = Math.max(...data.map(d => +d.total), 1);
  return (
    <div className="card">
      <div className="card-header">
        <div>
          <div className="card-title">Pipeline de Ventas</div>
          <div className="card-subtitle">Oportunidades y valor por etapa</div>
        </div>
        <TrendingUp size={18} style={{ color: '#7C3AED' }} />
      </div>
      <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
        {data.map((row, i) => {
          const pct = Math.round((+row.total / max) * 100);
          const color = PIPE_COLORS[i % PIPE_COLORS.length];
          return (
            <div key={row.etapa}>
              <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 6, alignItems: 'center' }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                  <div style={{ width: 8, height: 8, borderRadius: '50%', background: color, flexShrink: 0 }} />
                  <span style={{ fontSize: '0.82rem', fontWeight: 600, color: '#475569' }}>{row.etapa}</span>
                </div>
                <div style={{ display: 'flex', gap: 12, alignItems: 'center' }}>
                  <span style={{ fontSize: '0.74rem', color: '#94A3B8', background: '#F1F5F9', padding: '2px 8px', borderRadius: 20 }}>
                    {+row.total} ops
                  </span>
                  <span style={{ fontSize: '0.78rem', fontWeight: 700, color }}>{fmtMoney(+row.valor_total)}</span>
                </div>
              </div>
              <div style={{ height: 7, background: '#F1F5F9', borderRadius: 20, overflow: 'hidden' }}>
                <div style={{
                  height: '100%', width: `${pct}%`, borderRadius: 20,
                  background: `linear-gradient(90deg, ${color}, ${color}88)`,
                  transition: 'width 1.2s ease',
                }} />
              </div>
            </div>
          );
        })}
        {data.length === 0 && <div className="empty-state" style={{ padding: 20 }}><p className="text-muted">Sin datos de pipeline</p></div>}
      </div>
    </div>
  );
}

// ─── Mini Sparkline Card ──────────────────────────────────────
function MiniStatCard({ title, value, prefix = '', suffix = '', gradient, sparkData = [] }) {
  const cfg = {
    labels: sparkData.map((_, i) => i),
    datasets: [{
      data: sparkData,
      borderColor: 'rgba(255,255,255,0.80)',
      backgroundColor: 'rgba(255,255,255,0.12)',
      borderWidth: 2, tension: 0.4, fill: true, pointRadius: 0,
    }],
  };
  const opts = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { enabled: false } },
    scales: { x: { display: false }, y: { display: false } },
    elements: { line: { borderJoinStyle: 'round' } },
  };
  return (
    <div style={{
      background: gradient, borderRadius: 14, padding: '18px 18px 10px',
      position: 'relative', overflow: 'hidden',
      boxShadow: '0 8px 24px rgba(0,0,0,0.14)',
      border: '1px solid rgba(255,255,255,0.12)',
    }}>
      <div style={{ color: 'rgba(255,255,255,0.78)', fontSize: '0.78rem', fontWeight: 500, marginBottom: 6 }}>{title}</div>
      <div style={{ color: 'white', fontSize: '1.65rem', fontWeight: 900, fontFamily: 'Plus Jakarta Sans', letterSpacing: '-0.03em', marginBottom: 10 }}>
        {prefix}{typeof value === 'number' ? value.toLocaleString('es') : value}{suffix}
      </div>
      {sparkData.length > 1 && (
        <div style={{ height: 46, marginBottom: -6 }}>
          <Line data={cfg} options={opts} />
        </div>
      )}
    </div>
  );
}

// ─── Actividad Reciente ───────────────────────────────────────
function ActividadReciente({ items }) {
  return (
    <div className="card">
      <div className="card-header">
        <div className="card-title">Actividad Reciente</div>
        <Activity size={16} style={{ color: '#7C3AED' }} />
      </div>
      {items.length === 0 ? (
        <div className="empty-state"><p className="text-muted">Sin actividad reciente</p></div>
      ) : (
        <div className="timeline">
          {items.map((item) => (
            <div className="timeline-item" key={item.id}>
              <div className="timeline-dot">
                {tipoInteraccionIcon[item.tipo] || <Clock size={14} />}
              </div>
              <div className="timeline-content">
                <div className="timeline-title">
                  {item.Cliente?.nombre} {item.Cliente?.apellido}
                  <span className={`badge badge-${tipoColor[item.tipo] || 'blue'}`} style={{ marginLeft: 8, fontSize: '0.63rem' }}>
                    {item.tipo}
                  </span>
                </div>
                <div className="timeline-desc">
                  {item.descripcion?.substring(0, 80)}{item.descripcion?.length > 80 ? '...' : ''}
                </div>
                <div className="timeline-time">
                  {item.usuario?.nombre} · {item.fecha ? format(new Date(item.fecha), 'd MMM · HH:mm', { locale: es }) : ''}
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

// ─── Tareas Pendientes ────────────────────────────────────────
const prioridadColor = { Urgente: 'red', Alta: 'amber', Media: 'blue', Baja: 'gray' };
const prioridadDot   = { Urgente: '#DC2626', Alta: '#D97706', Media: '#7C3AED', Baja: '#94A3B8' };

function TareasPendientes({ tareas }) {
  return (
    <div className="card">
      <div className="card-header">
        <div className="card-title">Mis Tareas Pendientes</div>
        <a href="/tareas" className="btn btn-ghost btn-sm">Ver todas</a>
      </div>
      {tareas.length === 0 ? (
        <div className="empty-state" style={{ padding: '24px' }}>
          <CheckCircle2 size={36} className="empty-state-icon" />
          <p className="empty-state-title">¡Todo al día!</p>
          <p className="empty-state-desc">No tienes tareas pendientes</p>
        </div>
      ) : (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
          {tareas.map(t => (
            <div key={t.id} style={{
              background: 'var(--bg-elevated)', borderRadius: 10,
              padding: '11px 13px', display: 'flex', gap: 12, alignItems: 'flex-start',
              border: '1px solid var(--border)',
            }}>
              <div style={{ width: 8, height: 8, borderRadius: '50%', flexShrink: 0, marginTop: 6, background: prioridadDot[t.prioridad] || '#94A3B8' }} />
              <div style={{ flex: 1 }}>
                <div className="font-semibold text-sm">{t.titulo}</div>
                {t.cliente && <div className="text-xs text-muted mt-1">{t.cliente.nombre} {t.cliente.apellido}</div>}
                {t.fecha_vence && (
                  <div className="text-xs text-muted mt-1">
                    Vence: {format(new Date(t.fecha_vence), 'd MMM yyyy', { locale: es })}
                  </div>
                )}
              </div>
              <span className={`badge badge-${prioridadColor[t.prioridad] || 'blue'}`}>{t.prioridad}</span>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

// ═══ DASHBOARD PRINCIPAL ══════════════════════════════════════
export default function DashboardPage() {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const [kpis,      setKpis]      = useState(null);
  const [ingresos,  setIngresos]  = useState([]);
  const [esAdmin,   setEsAdmin]   = useState(false);

  const [destinos,  setDestinos]  = useState([]);
  const [actividad, setActividad] = useState([]);
  const [tareas,    setTareas]    = useState([]);
  const [estadoRes, setEstadoRes] = useState([]);
  const [pipeline,  setPipeline]  = useState([]);
  const [resMes,    setResMes]    = useState([]);
  const [fuentes,   setFuentes]   = useState([]);
  const [loading,   setLoading]   = useState(true);


  useEffect(() => {
    const fetchAll = async () => {
      try {
        const [k, i, d, a, t, er, pp, rm, f] = await Promise.all([
          api.get('/dashboard/kpis'),
          api.get('/dashboard/ingresos-mensuales'),
          api.get('/dashboard/top-destinos'),
          api.get('/dashboard/actividad-reciente'),
          api.get('/dashboard/tareas-pendientes'),
          api.get('/dashboard/reservas-por-estado'),
          api.get('/dashboard/oportunidades-etapa'),
          api.get('/dashboard/reservas-por-mes'),
          api.get('/dashboard/clientes-por-fuente'),
        ]);
        setKpis(k.data);
        setEsAdmin(k.data?.esAdmin || false);
        setIngresos(i.data);
        setDestinos(d.data);
        setActividad(a.data);
        setTareas(t.data);
        setEstadoRes(er.data);
        setPipeline(pp.data);
        setResMes(rm.data);
        setFuentes(f.data);


      } catch (e) {
        console.error(e);
      } finally {
        setLoading(false);
      }
    };
    fetchAll();
  }, []);

  if (loading) return (
    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '60vh' }}>
      <div style={{ textAlign: 'center' }}>
        <div className="spinner" style={{ margin: '0 auto 16px' }} />
        <p className="text-muted">Cargando dashboard...</p>
      </div>
    </div>
  );

  const sparkIngresos = ingresos.slice(-8).map(d => +d.total);
  const sparkUtilidad = ingresos.slice(-8).map(d => Math.max(+d.utilidad, 0));
  const totalIngresos = ingresos.reduce((s, d) => s + +d.total, 0);
  const totalUtilidad = ingresos.reduce((s, d) => s + Math.max(+d.utilidad, 0), 0);

  return (
    <div className="animate-fade-in">
      {/* Header */}
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Dashboard</h1>
          <p className="page-subtitle">Bienvenido al panel de control de Viaje 360</p>
        </div>
      </div>

      {/* ── SECCIÓN 1: KPIs Principales (Responsivo) ── */}
      <div className="grid-dashboard">
        <KpiCard icon={CalendarCheck} label="Reservas del Mes"   value={kpis?.reservas?.valor || 0}       cambio={kpis?.reservas?.cambio || 0}       color="blue"   delay={1} />
        <KpiCard icon={DollarSign}    label="Ingresos del Mes"   value={kpis?.ingresos?.valor || 0}       cambio={kpis?.ingresos?.cambio || 0}       color="green"  delay={2} prefix={m} />

        <KpiCard icon={Users}         label="Nuevos Clientes"    value={kpis?.nuevosClientes?.valor || 0} cambio={kpis?.nuevosClientes?.cambio || 0} color="purple" delay={3} />
        <KpiCard icon={TrendingUp}    label="Tasa de Conversión" value={kpis?.tasaConversion?.valor || 0} cambio={kpis?.tasaConversion?.cambio || 0} color="amber"  delay={4} suffix="%" />
      </div>

      {/* ── SECCIÓN 2: Métricas de Rentabilidad (Admin) ── */}
      {esAdmin && (
        <div className="grid-dashboard">
          <KpiCard icon={TrendingDown} label="Utilidad / Mes" value={kpis?.utilidad?.valor || 0} cambio={kpis?.utilidad?.cambio || 0} color="green" delay={5} prefix={m} />

          <MiniStatCard
            title="Ingresos Acumulados (12m)"
            value={totalIngresos}
            prefix={m}
            gradient="linear-gradient(135deg, #7C3AED 0%, #5B21B6 100%)"
            sparkData={sparkIngresos}
          />
          <MiniStatCard
            title="Utilidad Total (12m)"
            value={totalUtilidad}
            prefix={m}
            gradient="linear-gradient(135deg, #0D9488 0%, #0F766E 100%)"
            sparkData={sparkUtilidad}
          />
          <MiniStatCard
            title="Reservas Completadas"
            value={estadoRes.find(e => e.estado === 'Completada')?.total || 0}
            gradient="linear-gradient(135deg, #EC4899 0%, #BE185D 100%)"
            sparkData={resMes.map(d => +d.completadas)}
          />
        </div>
      )}

      {/* ── SECCIÓN 3: Desempeño y Distribución ── */}
      <div className="charts-grid">
        <div style={{ display: 'flex', flexDirection: 'column', gap: 18 }}>
          <GraficoIngresos data={ingresos} esAdmin={esAdmin} />
          <GraficoFuentes data={fuentes} />
        </div>
        
        <div style={{ display: 'flex', flexDirection: 'column', gap: 18 }}>
          <GraficoDestinos data={destinos} />
          <GraficoEstadoReservas data={estadoRes} />
        </div>
      </div>


      {/* ── SECCIÓN 4: Pipeline y Tendencias ── */}
      <div className="grid-dashboard">
        <GraficoReservasMes data={resMes} />
        <GraficoPipeline data={pipeline} />
      </div>

      {/* ── SECCIÓN 5: Gestión Diaria ── */}
      <div className="charts-grid">
        <ActividadReciente items={actividad} />
        <TareasPendientes tareas={tareas} />
      </div>
    </div>
  );
}
