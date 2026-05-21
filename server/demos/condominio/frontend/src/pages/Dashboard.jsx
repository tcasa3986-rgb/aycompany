import { useState, useEffect } from 'react';
import {
  LineChart, Line, BarChart, Bar, PieChart, Pie, Cell,
  XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, Area, AreaChart
} from 'recharts';
import api from '../services/api';

const fmt = (n) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 0 }).format(n || 0);

const COLORS = ['#7C3AED', '#3B82F6', '#10B981', '#F59E0B', '#EC4899', '#06B6D4'];

export default function Dashboard() {
  const [kpis, setKpis] = useState(null);
  const [chartData, setChartData] = useState([]);
  const [gastos, setGastos] = useState([]);
  const [morosos, setMorosos] = useState([]);
  const [actividad, setActividad] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchAll = async () => {
      try {
        const [k, c, g, m, a] = await Promise.all([
          api.get('/dashboard/kpis'),
          api.get('/dashboard/ingresos-egresos'),
          api.get('/dashboard/distribucion-gastos'),
          api.get('/dashboard/morosos'),
          api.get('/dashboard/actividad-reciente'),
        ]);
        setKpis(k.data.data);
        setChartData(c.data.data.map(item => ({ ...item, ingresos: Number(item.ingresos), egresos: Number(item.egresos) })));
        setGastos(g.data.data.map(item => ({ ...item, total: Number(item.total) })));
        setMorosos(m.data.data);
        setActividad(a.data.data);
      } catch (err) {
        console.error('Dashboard error:', err);
      } finally {
        setLoading(false);
      }
    };
    fetchAll();
  }, []);

  if (loading) return (
    <div className="loading-overlay" style={{ minHeight: 400 }}>
      <div style={{ textAlign: 'center' }}>
        <div className="spinner" style={{ margin: '0 auto 16px' }} />
        <p style={{ color: 'var(--text-secondary)' }}>Cargando datos del dashboard...</p>
      </div>
    </div>
  );

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <div className="page-title">Dashboard</div>
          <div className="page-subtitle">Resumen ejecutivo del condominio</div>
        </div>
      </div>

      {/* KPIs */}
      <div className="kpi-grid">
        <div className="kpi-card purple">
          <div className="kpi-icon">💰</div>
          <div className="kpi-label">Cobrado este mes</div>
          <div className="kpi-value">{fmt(kpis?.cobradoMes)}</div>
          <div className="kpi-change up">▲ Ingresos del mes</div>
        </div>
        <div className="kpi-card blue">
          <div className="kpi-icon">⚠️</div>
          <div className="kpi-label">Cuotas pendientes</div>
          <div className="kpi-value">{fmt(kpis?.cuotasPendientes?.monto)}</div>
          <div className="kpi-change">{kpis?.cuotasPendientes?.cantidad} cuotas</div>
        </div>
        <div className="kpi-card green">
          <div className="kpi-icon">💸</div>
          <div className="kpi-label">Gastos del mes</div>
          <div className="kpi-value">{fmt(kpis?.gastosMes)}</div>
          <div className="kpi-change">Egresos acumulados</div>
        </div>
        <div className="kpi-card amber">
          <div className="kpi-icon">🏦</div>
          <div className="kpi-label">Fondo de reserva</div>
          <div className="kpi-value">{fmt(kpis?.fondoReserva)}</div>
          <div className="kpi-change up">▲ Balance actual</div>
        </div>
      </div>

      {/* Mini Stats */}
      <div className="stats-row mb-24">
        {[
          { label: 'Unidades totales', value: kpis?.unidades?.total || 0, icon: '🏠' },
          { label: 'Habitadas', value: kpis?.unidades?.habitadas || 0, icon: '✅', color: '#10B981' },
          { label: 'Vacías', value: kpis?.unidades?.vacias || 0, icon: '⭕', color: '#6B7280' },
          { label: 'En venta', value: kpis?.unidades?.en_venta || 0, icon: '🏷️', color: '#F59E0B' },
          { label: 'Órdenes activas', value: kpis?.ordenesAbiertas || 0, icon: '🔧', color: '#7C3AED' },
          { label: 'Visitantes hoy', value: kpis?.visitantesHoy || 0, icon: '👤', color: '#3B82F6' },
        ].map((s, i) => (
          <div className="stat-mini" key={i}>
            <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
              <span>{s.icon}</span>
              <div className="stat-mini-value" style={s.color ? { color: s.color } : {}}>{s.value}</div>
            </div>
            <div className="stat-mini-label">{s.label}</div>
          </div>
        ))}
      </div>

      {/* Gráficas */}
      <div className="grid-2 mb-24">
        {/* Ingresos vs Egresos */}
        <div className="card">
          <div className="card-header">
            <div>
              <div className="card-title">Ingresos vs Egresos</div>
              <div className="card-subtitle">Comparativo mensual {new Date().getFullYear()}</div>
            </div>
          </div>
          <ResponsiveContainer width="100%" height={260}>
            <AreaChart data={chartData}>
              <defs>
                <linearGradient id="colorIng" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="5%" stopColor="#7C3AED" stopOpacity={0.3}/>
                  <stop offset="95%" stopColor="#7C3AED" stopOpacity={0}/>
                </linearGradient>
                <linearGradient id="colorEgr" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="5%" stopColor="#3B82F6" stopOpacity={0.3}/>
                  <stop offset="95%" stopColor="#3B82F6" stopOpacity={0}/>
                </linearGradient>
              </defs>
              <CartesianGrid strokeDasharray="3 3" stroke="var(--border)" />
              <XAxis dataKey="mes" tick={{ fontSize: 12, fill: 'var(--text-muted)' }} />
              <YAxis tickFormatter={v => `$${(v/1000).toFixed(0)}k`} tick={{ fontSize: 12, fill: 'var(--text-muted)' }} />
              <Tooltip formatter={(v) => fmt(v)} contentStyle={{ background: 'var(--bg-card)', border: '1px solid var(--border)', borderRadius: 8 }} />
              <Legend />
              <Area type="monotone" dataKey="ingresos" name="Ingresos" stroke="#7C3AED" fill="url(#colorIng)" strokeWidth={2} />
              <Area type="monotone" dataKey="egresos" name="Egresos" stroke="#3B82F6" fill="url(#colorEgr)" strokeWidth={2} />
            </AreaChart>
          </ResponsiveContainer>
        </div>

        {/* Distribución de gastos */}
        <div className="card">
          <div className="card-header">
            <div>
              <div className="card-title">Distribución de Gastos</div>
              <div className="card-subtitle">Por categoría este año</div>
            </div>
          </div>
          <div style={{ display: 'flex', alignItems: 'center', gap: 16 }}>
            <ResponsiveContainer width="55%" height={220}>
              <PieChart>
                <Pie data={gastos} dataKey="total" nameKey="categoria" cx="50%" cy="50%" innerRadius={55} outerRadius={90} paddingAngle={3}>
                  {gastos.map((_, i) => <Cell key={i} fill={COLORS[i % COLORS.length]} />)}
                </Pie>
                <Tooltip formatter={(v) => fmt(v)} contentStyle={{ background: 'var(--bg-card)', border: '1px solid var(--border)', borderRadius: 8 }} />
              </PieChart>
            </ResponsiveContainer>
            <div style={{ flex: 1 }}>
              {gastos.slice(0, 5).map((g, i) => (
                <div key={i} style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 10 }}>
                  <div style={{ width: 10, height: 10, borderRadius: '50%', background: COLORS[i], flexShrink: 0 }} />
                  <span style={{ fontSize: 12, color: 'var(--text-secondary)', flex: 1 }}>{g.categoria || 'Otros'}</span>
                  <span style={{ fontSize: 12, fontWeight: 600 }}>{fmt(g.total)}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* Morosos y Actividad */}
      <div className="grid-2">
        {/* Top morosos */}
        <div className="table-container">
          <div className="table-header">
            <div>
              <div className="card-title">Top Morosos</div>
              <div className="card-subtitle">Unidades con cuotas vencidas</div>
            </div>
            <span className="badge badge-danger">{morosos.length} unidades</span>
          </div>
          {morosos.length === 0 ? (
            <div className="empty-state" style={{ padding: 40 }}>
              <div className="empty-state-icon">✅</div>
              <div className="empty-state-title">¡Sin morosos!</div>
              <div className="empty-state-text">Todos los pagos están al corriente</div>
            </div>
          ) : (
            <table>
              <thead>
                <tr>
                  <th>Unidad</th>
                  <th>Torre</th>
                  <th>Cuotas</th>
                  <th>Deuda</th>
                  <th>Días</th>
                </tr>
              </thead>
              <tbody>
                {morosos.map((m, i) => (
                  <tr key={i}>
                    <td><strong>{m.unidad}</strong></td>
                    <td>{m.torre}</td>
                    <td><span className="badge badge-danger">{m.cuotas_vencidas}</span></td>
                    <td style={{ fontWeight: 700, color: 'var(--accent-red)' }}>{fmt(m.deuda_total)}</td>
                    <td><span className="badge badge-warning">{m.dias_mora}d</span></td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>

        {/* Actividad reciente */}
        <div className="card">
          <div className="card-header">
            <div className="card-title">Actividad Reciente</div>
          </div>
          {actividad.length === 0 ? (
            <div className="empty-state" style={{ padding: 20 }}>
              <div className="empty-state-icon">📋</div>
              <div className="empty-state-title">Sin actividad reciente</div>
            </div>
          ) : (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
              {actividad.map((a, i) => (
                <div key={i} style={{
                  display: 'flex', alignItems: 'center', gap: 12,
                  padding: '12px 14px',
                  background: 'var(--bg-main)',
                  borderRadius: 'var(--radius-sm)',
                  border: '1px solid var(--border-light)',
                }}>
                  <div style={{
                    width: 36, height: 36,
                    borderRadius: '50%',
                    background: a.tipo === 'pago' ? 'rgba(16,185,129,0.1)' : 'rgba(124,58,237,0.1)',
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    fontSize: 16, flexShrink: 0,
                  }}>
                    {a.tipo === 'pago' ? '💰' : '🔧'}
                  </div>
                  <div style={{ flex: 1, minWidth: 0 }}>
                    <div style={{ fontSize: 13, fontWeight: 500, truncate: true }}>{a.descripcion}</div>
                    <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>
                      {new Date(a.fecha).toLocaleString('es-MX')}
                    </div>
                  </div>
                  {a.monto && <div style={{ fontSize: 13, fontWeight: 700, color: 'var(--accent-green)', flexShrink: 0 }}>{fmt(a.monto)}</div>}
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
