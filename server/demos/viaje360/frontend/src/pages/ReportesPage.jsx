import React, { useEffect, useState } from 'react';
import {
  Chart as ChartJS, CategoryScale, LinearScale, BarElement,
  ArcElement, PointElement, LineElement, Filler, Tooltip, Legend
} from 'chart.js';
import { Bar, Doughnut, Line } from 'react-chartjs-2';
import api from '../services/api';
import useConfigStore from '../store/configStore';


ChartJS.register(CategoryScale, LinearScale, BarElement, ArcElement, PointElement, LineElement, Filler, Tooltip, Legend);

const chartOpts = {
  responsive: true, maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: '#FFFFFF', borderColor: '#E5E7EB', borderWidth: 1,
      titleColor: '#1E293B', bodyColor: '#475569', padding: 12, displayColors: false
    }
  },
  scales: {
    x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { family: 'Inter', size: 11 } } },
    y: {
      grid: { color: 'rgba(0,0,0,0.05)', borderDash: [4, 4] },
      ticks: { color: '#94A3B8', font: { family: 'Inter', size: 11 } }
    }
  },
};

const COLORES = ['#0EA5E9','#10B981','#0284C7','#353A45','#8B5CF6','#06B6D4','#EC4899','#6366F1'];

export default function ReportesPage() {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const [ventas,    setVentas]    = useState([]);
  const [agentes,   setAgentes]   = useState([]);
  const [destinos,  setDestinos]  = useState([]);
  const [loading,   setLoading]   = useState(true);
  const [tabActivo, setTabActivo] = useState('ventas');

  useEffect(() => {
    Promise.all([
      api.get('/reportes/ventas'),
      api.get('/reportes/agentes'),
      api.get('/reportes/destinos'),
    ])
      .then(([v, a, d]) => { setVentas(v.data); setAgentes(a.data); setDestinos(d.data); })
      .finally(() => setLoading(false));
  }, []);

  const ventasReservas = {
    labels: ventas.map(v => v.mes),
    datasets: [{
      label: 'Reservas', data: ventas.map(v => +v.reservas),
      backgroundColor: 'rgba(14,165,233,0.75)', borderRadius: 6,
    }],
  };

  const ventasIngresos = {
    labels: ventas.map(v => v.mes),
    datasets: [{
      label: `Ingresos (${m})`, data: ventas.map(v => +v.total_ventas),

      borderColor: '#0EA5E9',
      backgroundColor: ctx => {
        const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 200);
        g.addColorStop(0, 'rgba(14,165,233,0.35)');
        g.addColorStop(1, 'rgba(14,165,233,0)');
        return g;
      },
      fill: true, tension: 0.4, borderWidth: 2.5,
      pointBackgroundColor: '#0EA5E9', pointRadius: 3,
    }],
  };

  const agentesData = {
    labels: agentes.map(a => `${a.nombre} ${a.apellido}`),
    datasets: [{
      data: agentes.map(a => +a.total_ventas || 0),
      backgroundColor: COLORES,
      borderColor: '#FFFFFF', borderWidth: 2, hoverOffset: 6,
    }],
  };

  const destinosData = {
    labels: destinos.map(d => d.destino),
    datasets: [{
      label: `Ingresos (${m})`,

      data: destinos.map(d => +d.total_ingresos),
      backgroundColor: COLORES.map(c => c + 'CC'),
      borderRadius: 6,
    }],
  };

  const lineOpts = {
    ...chartOpts,
    scales: {
      ...chartOpts.scales,
      y: { ...chartOpts.scales.y, ticks: { ...chartOpts.scales.y.ticks, callback: v => `${m}${(v/1000).toFixed(0)}k` } }
    }
  };

  const tabs = ['ventas', 'agentes', 'destinos'];
  const tabLabel = { ventas: '📅 Ventas por Mes', agentes: '👤 Agentes', destinos: '📍 Destinos' };

  return (
    <div className="animate-fade-in">
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Reportes</h1>
          <p className="page-subtitle">Análisis y métricas de rendimiento del negocio</p>
        </div>
      </div>

      {loading ? (
        <div style={{ textAlign:'center', padding:60 }}><div className="spinner" style={{ margin:'0 auto' }}/></div>
      ) : (
        <>
          {/* KPI summary */}
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4,1fr)', gap: 16, marginBottom: 24 }}>
            {[
              { label: 'Total Reservas',    value: ventas.reduce((s,v)=>s+(+v.reservas||0),0),     icon: '📋', color: '#0EA5E9' },
              { label: 'Ingresos Totales',  value: `${m}${ventas.reduce((s,v)=>s+(+v.total_ventas||0),0).toLocaleString('es')}`, icon: '💰', color: '#0284C7' },
              { label: 'Ticket Promedio',   value: ventas.length ? `${m}${(ventas.reduce((s,v)=>s+(+v.ticket_promedio||0),0)/ventas.length).toFixed(0)}` : '—', icon: '🎫', color: '#10B981' },
              { label: 'Agentes Activos',   value: agentes.filter(a=>+a.reservas>0).length,        icon: '🏆', color: '#353A45' },

            ].map(k => (
              <div key={k.label} className="card" style={{ padding: '16px 20px' }}>
                <div style={{ fontSize: '1.6rem', marginBottom: 6 }}>{k.icon}</div>
                <div className="text-xs text-muted">{k.label}</div>
                <div style={{ fontSize: '1.4rem', fontWeight: 700, marginTop: 2, color: k.color }}>{k.value}</div>
              </div>
            ))}
          </div>

          {/* Tabs */}
          <div style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
            {tabs.map(t => (
              <button key={t}
                className={`btn ${tabActivo === t ? 'btn-primary' : 'btn-secondary'} btn-sm`}
                onClick={() => setTabActivo(t)}
              >
                {tabLabel[t]}
              </button>
            ))}
          </div>

          {/* Tab: Ventas */}
          {tabActivo === 'ventas' && (
            <>
              <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:20, marginBottom:20 }}>
                <div className="card" style={{ height:300 }}>
                  <div className="card-header">
                    <div className="card-title">Reservas por Mes</div>
                  </div>
                  <div style={{ height:210 }}>
                    {ventas.length ? <Bar data={ventasReservas} options={chartOpts} /> :
                      <p className="text-muted text-center" style={{ paddingTop:60 }}>Sin datos</p>}
                  </div>
                </div>
                <div className="card" style={{ height:300 }}>
                  <div className="card-header">
                    <div className="card-title">Ingresos por Mes</div>
                  </div>
                  <div style={{ height:210 }}>
                    {ventas.length ? <Line data={ventasIngresos} options={lineOpts} /> :
                      <p className="text-muted text-center" style={{ paddingTop:60 }}>Sin datos</p>}
                  </div>
                </div>
              </div>
              <div className="card">
                <div className="card-header"><div className="card-title">Detalle de Ventas Mensuales</div></div>
                <table>
                  <thead>
                    <tr><th>Mes</th><th>Reservas</th><th>Total Ventas</th><th>Ticket Promedio</th></tr>
                  </thead>
                  <tbody>
                    {ventas.map((v, i) => (
                      <tr key={i}>
                        <td className="font-semibold">{v.mes}</td>
                        <td>{v.reservas}</td>
                        <td className="text-success font-semibold">{m}{(+v.total_ventas||0).toLocaleString('es')}</td>
                        <td>{m}{(+v.ticket_promedio||0).toFixed(0)}</td>
                      </tr>

                    ))}
                    {ventas.length === 0 && (
                      <tr><td colSpan={4} style={{ textAlign:'center', padding:20, color:'var(--text-muted)' }}>Sin datos de ventas</td></tr>
                    )}
                  </tbody>
                </table>
              </div>
            </>
          )}

          {/* Tab: Agentes */}
          {tabActivo === 'agentes' && (
            <>
              <div style={{ display:'grid', gridTemplateColumns:'300px 1fr', gap:20, marginBottom:20 }}>
                <div className="card" style={{ height:320 }}>
                  <div className="card-header"><div className="card-title">Distribución Ventas</div></div>
                  <div style={{ height:230 }}>
                    {agentes.length ? <Doughnut data={agentesData} options={{ ...chartOpts, cutout:'65%', scales:{} }} /> :
                      <p className="text-muted text-center" style={{ paddingTop:80 }}>Sin datos</p>}
                  </div>
                </div>
                <div className="card">
                  <div className="card-header"><div className="card-title">Ranking de Agentes</div></div>
                  <table>
                    <thead>
                      <tr><th>#</th><th>Agente</th><th>Reservas</th><th>Total Ventas</th><th>Oportunidades</th><th>Ganadas</th></tr>
                    </thead>
                    <tbody>
                      {agentes.map((a, i) => (
                        <tr key={i}>
                          <td>
                            <div style={{
                              width:24, height:24, borderRadius:'50%', background: COLORES[i % COLORES.length],
                              display:'flex', alignItems:'center', justifyContent:'center',
                              fontSize:'0.65rem', fontWeight:700, color:'white', margin:'0 auto'
                            }}>#{i+1}</div>
                          </td>
                          <td className="font-semibold">{a.nombre} {a.apellido}</td>
                          <td>{a.reservas}</td>
                          <td className="text-success font-semibold">{m}{(+a.total_ventas||0).toLocaleString('es')}</td>
                          <td>{a.oportunidades}</td>

                          <td><span className="badge badge-green">{a.ganadas}</span></td>
                        </tr>
                      ))}
                      {agentes.length === 0 && <tr><td colSpan={6} style={{ textAlign:'center', padding:20, color:'var(--text-muted)' }}>Sin datos</td></tr>}
                    </tbody>
                  </table>
                </div>
              </div>
            </>
          )}

          {/* Tab: Destinos */}
          {tabActivo === 'destinos' && (
            <>
              <div className="card" style={{ height:380, marginBottom:20 }}>
                <div className="card-header">
                  <div>
                    <div className="card-title">Destinos más Rentables</div>
                    <div className="card-subtitle">Por ingresos totales en reservas</div>
                  </div>
                </div>
                <div style={{ height:280 }}>
                  {destinos.length ? <Bar data={destinosData} options={{
                    ...chartOpts,
                    scales: { ...chartOpts.scales, y: { ...chartOpts.scales.y, ticks: { ...chartOpts.scales.y.ticks, callback: v => `${m}${(v/1000).toFixed(0)}k` } } }
                  }} /> :

                    <p className="text-muted text-center" style={{ paddingTop:100 }}>Sin datos de destinos — reserva paquetes con destinos para ver estadísticas</p>}
                </div>
              </div>
              <div className="card">
                <div className="card-header"><div className="card-title">Tabla de Destinos</div></div>
                <table>
                  <thead>
                    <tr><th>#</th><th>Destino</th><th>Reservas</th><th>Ingresos Totales</th><th>Promedio por Reserva</th></tr>
                  </thead>
                  <tbody>
                    {destinos.map((d, i) => (
                      <tr key={i}>
                        <td><span className="badge badge-blue">#{i+1}</span></td>
                        <td className="font-semibold">📍 {d.destino}</td>
                        <td>{d.reservas}</td>
                        <td className="text-success font-semibold">{m}{(+d.total_ingresos||0).toLocaleString('es')}</td>
                        <td>{m}{(+d.promedio||0).toFixed(0)}</td>
                      </tr>

                    ))}
                    {destinos.length === 0 && (
                      <tr><td colSpan={5} style={{ textAlign:'center', padding:20, color:'var(--text-muted)' }}>Sin datos de destinos</td></tr>
                    )}
                  </tbody>
                </table>
              </div>
            </>
          )}
        </>
      )}
    </div>
  );
}
