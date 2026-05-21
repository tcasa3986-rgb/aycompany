import { useState, useEffect, useCallback } from 'react';
import api from '../api/axios';
import {
  BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, CartesianGrid,
  AreaChart, Area, PieChart, Pie, Cell, Legend
} from 'recharts';
import { Car, DollarSign, Activity, TrendingUp, Clock, PieChart as PieIcon, CalendarDays } from 'lucide-react';

const TIPO_COLORS = { auto: '#3b82f6', moto: '#8b5cf6', VIP: '#f59e0b', discapacitado: '#10b981' };
const PIE_FALLBACK = ['#3b82f6', '#8b5cf6', '#f59e0b', '#10b981', '#ef4444'];

import { useConfig } from '../contexts/ConfigContext';

function StatCard({ icon: Icon, label, value, sub, color, iconBg, cardBg }) {
  return (
    <div className={`card p-5 flex items-center gap-4 hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden group ${cardBg} border-t-2`}>
      <div className={`absolute top-0 right-0 w-32 h-32 ${iconBg} rounded-full blur-3xl -mr-10 -mt-10 opacity-30 group-hover:opacity-50 transition-opacity`} />
      <div className={`w-14 h-14 ${iconBg} rounded-2xl flex items-center justify-center shrink-0 shadow-lg relative z-10 border border-white/5`}>
        <Icon className={`w-7 h-7 ${color}`} />
      </div>
      <div className="relative z-10 flex-1">
        <p className="text-park-muted text-xs font-bold uppercase tracking-widest">{label}</p>
        <p className="text-park-text text-3xl font-black mt-1 tracking-tight">{value}</p>
        {sub && <p className="text-park-muted text-xs mt-1 font-medium">{sub}</p>}
      </div>
    </div>
  );
}

const customTooltip = ({ active, payload, label }, pre="$") => {
  if (active && payload?.length) {
    return (
      <div className="bg-[#0f172a] border border-[#1e3a5f] rounded-xl p-3 text-sm shadow-2xl backdrop-blur-md">
        <p className="text-slate-400 mb-1 font-semibold">{label}</p>
        <p className="text-white font-black text-lg">
          {pre}{typeof payload[0].value === 'number' ? payload[0].value.toFixed(2) : payload[0].value}
        </p>
      </div>
    );
  }
  return null;
};

export default function Dashboard() {
  const { config } = useConfig();
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  const fetchData = useCallback(async () => {
    try {
      const res = await api.get('/reportes/dashboard');
      setData(res.data);
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchData();
    const interval = setInterval(fetchData, 30000); // auto-refresh cada 30s
    return () => clearInterval(interval);
  }, [fetchData]);

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="text-park-accent animate-pulse font-medium tracking-wide">Actualizando métricas...</div>
    </div>
  );

  const esp = data?.espacios || {};
  const ocupacion = esp.total > 0 ? Math.round((esp.ocupados / esp.total) * 100) : 0;

  const ingresosChart = (data?.ingresos_7dias || []).map(d => ({
    fecha: new Date(d.fecha).toLocaleDateString('es', { weekday: 'short', day: 'numeric' }),
    total: parseFloat(d.total) || 0
  }));

  const vehiculosChart = (data?.vehiculos_7dias || []).map(d => ({
    fecha: new Date(d.fecha).toLocaleDateString('es', { weekday: 'short', day: 'numeric' }),
    total: parseInt(d.total) || 0
  }));
  
  const tiposData = (data?.vehiculos_tipos || []).map(d => ({
    name: d.name,
    value: parseInt(d.value) || 0
  }));

  return (
    <div className="space-y-6 animate-fade-in pb-10">
      {/* KPI Cards con variaciones de color mejoradas */}
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <StatCard
          icon={DollarSign}
          label="Ingresos Hoy"
          value={`${config?.moneda || '$'}${(data?.ingreso_hoy || 0).toFixed(2)}`}
          sub="Facturación del día actual"
          color="text-amber-400"
          iconBg="bg-amber-500/20"
          cardBg="bg-gradient-to-b from-amber-500/5 to-transparent border-t-amber-500"
        />
        <StatCard
          icon={TrendingUp}
          label="Ingresos Mes"
          value={`${config?.moneda || '$'}${(data?.ingreso_mes || 0).toFixed(2)}`}
          sub="Acumulado mes en curso"
          color="text-emerald-400"
          iconBg="bg-emerald-500/20"
          cardBg="bg-gradient-to-b from-emerald-500/5 to-transparent border-t-emerald-500"
        />
        <StatCard
          icon={Car}
          label="Flujo Vehículos"
          value={data?.vehiculos_hoy || 0}
          sub={`${data?.vehiculos_activos || 0} vehículos parqueados ahora`}
          color="text-blue-400"
          iconBg="bg-blue-500/20"
          cardBg="bg-gradient-to-b from-blue-500/5 to-transparent border-t-blue-500"
        />
        <StatCard
          icon={Activity}
          label="Ocupación"
          value={`${ocupacion}%`}
          sub={`${esp.libres || 0} libres de ${esp.total || 0} espacios`}
          color="text-purple-400"
          iconBg="bg-purple-500/20"
          cardBg="bg-gradient-to-b from-purple-500/5 to-transparent border-t-purple-500"
        />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Gráfico ingresos (AreaChart) - spans 2 cols */}
        <div className="card lg:col-span-2">
          <div className="flex items-center justify-between mb-6">
            <div>
              <h3 className="text-white font-bold text-lg flex items-center gap-2">
                <DollarSign className="w-5 h-5 text-amber-500" /> Rendimiento Financiero
              </h3>
              <p className="text-park-muted text-xs mt-1">Ingresos de los últimos 7 días</p>
            </div>
          </div>
          {ingresosChart.length > 0 ? (
            <ResponsiveContainer width="100%" height={260}>
              <AreaChart data={ingresosChart} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
                <defs>
                  <linearGradient id="colorIngresos" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#f59e0b" stopOpacity={0.4}/>
                    <stop offset="95%" stopColor="#f59e0b" stopOpacity={0}/>
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="3 3" stroke="#1e3a5f" vertical={false} />
                <XAxis dataKey="fecha" tick={{ fill: '#64748b', fontSize: 11 }} axisLine={false} tickLine={false} dy={10} />
                <YAxis tick={{ fill: '#64748b', fontSize: 11 }} axisLine={false} tickLine={false} tickFormatter={v => `${config?.moneda || '$'}${v}`} />
                <Tooltip content={p => customTooltip(p, config?.moneda || '$')} cursor={{ stroke: '#f59e0b', strokeWidth: 1, strokeDasharray: '4 4' }} />
                <Area type="monotone" dataKey="total" stroke="#f59e0b" strokeWidth={3} fillOpacity={1} fill="url(#colorIngresos)" />
              </AreaChart>
            </ResponsiveContainer>
          ) : (
            <div className="h-64 flex items-center justify-center text-park-muted text-sm border-2 border-dashed border-park-border rounded-xl">
              Sin datos financieros suficientes
            </div>
          )}
        </div>

        {/* Distribucion Vehículos (PieChart) */}
        <div className="card flex flex-col">
          <div>
            <h3 className="text-white font-bold text-lg flex items-center gap-2">
              <PieIcon className="w-5 h-5 text-purple-500" /> Tipos de Vehículos
            </h3>
            <p className="text-park-muted text-xs mt-1">Distribución de accesos hoy</p>
          </div>
          <div className="flex-1 flex items-center justify-center min-h-[260px]">
            {tiposData.length > 0 ? (
              <ResponsiveContainer width="100%" height={240}>
                <PieChart>
                  <Pie data={tiposData} dataKey="value" nameKey="name" cx="50%" cy="50%" innerRadius={60} outerRadius={85} paddingAngle={4} stroke="none">
                    {tiposData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={TIPO_COLORS[entry.name] || PIE_FALLBACK[index % PIE_FALLBACK.length]} />
                    ))}
                  </Pie>
                  <Tooltip content={({ active, payload }) => {
                    if (active && payload?.length) {
                      return (
                        <div className="bg-[#0f172a] border border-[#1e3a5f] rounded-lg p-2 text-sm shadow-xl">
                          <p className="text-slate-300 capitalize">{payload[0].name}</p>
                          <p className="text-white font-bold text-lg">{payload[0].value} vehículos</p>
                        </div>
                      );
                    }
                    return null;
                  }} />
                  <Legend verticalAlign="bottom" height={36} iconType="circle" wrapperStyle={{ fontSize: '12px' }} formatter={(v) => <span className="capitalize text-slate-300">{v}</span>} />
                </PieChart>
              </ResponsiveContainer>
            ) : (
              <div className="text-park-muted text-sm border-2 border-dashed border-park-border rounded-xl p-8 text-center w-full">
                No hay vehículos hoy
              </div>
            )}
          </div>
        </div>

        {/* Gráfico Vehículos 7 días (BarChart) */}
        <div className="card lg:col-span-2">
          <div className="flex items-center justify-between mb-6">
            <div>
              <h3 className="text-white font-bold text-lg flex items-center gap-2">
                <CalendarDays className="w-5 h-5 text-blue-500" /> Afluencia Vehicular
              </h3>
              <p className="text-park-muted text-xs mt-1">Total de vehículos ingresados por día (7 días)</p>
            </div>
          </div>
          {vehiculosChart.length > 0 ? (
            <ResponsiveContainer width="100%" height={220}>
              <BarChart data={vehiculosChart} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="#1e3a5f" vertical={false} />
                <XAxis dataKey="fecha" tick={{ fill: '#64748b', fontSize: 11 }} axisLine={false} tickLine={false} dy={10} />
                <YAxis tick={{ fill: '#64748b', fontSize: 11 }} axisLine={false} tickLine={false} allowDecimals={false} />
                <Tooltip content={p => customTooltip(p, '')} cursor={{ fill: '#1e3a5f', opacity: 0.4 }} />
                <Bar dataKey="total" fill="#3b82f6" radius={[6, 6, 0, 0]} barSize={32} />
              </BarChart>
            </ResponsiveContainer>
          ) : (
            <div className="h-48 flex items-center justify-center text-park-muted text-sm border-2 border-dashed border-park-border rounded-xl">
              Sin datos de afluencia
            </div>
          )}
        </div>

        {/* Ocupación y Últimas entradas */}
        <div className="grid grid-rows-[auto_1fr] gap-4">
          <div className="card">
            <h3 className="text-white font-bold mb-4 flex items-center gap-2">
              <Activity className="w-4 h-4 text-emerald-500" /> Estado en Vivo
            </h3>
            <div className="space-y-4">
              {[
                { label: 'Espacios Libres', val: esp.libres || 0, color: 'bg-emerald-500', pct: esp.total ? (esp.libres / esp.total) * 100 : 0 },
                { label: 'Espacios Ocupados', val: esp.ocupados || 0, color: 'bg-red-500', pct: esp.total ? (esp.ocupados / esp.total) * 100 : 0 },
              ].map(s => (
                <div key={s.label}>
                  <div className="flex justify-between text-sm mb-1.5">
                    <span className="text-slate-400 font-medium">{s.label}</span>
                    <span className="text-white font-bold">{s.val} <span className="text-slate-500 text-xs font-normal">({Math.round(s.pct)}%)</span></span>
                  </div>
                  <div className="w-full bg-[#0f172a] rounded-full h-2.5 shadow-inner overflow-hidden">
                    <div className={`${s.color} h-full transition-all duration-1000 ease-out`} style={{ width: `${s.pct}%` }} />
                  </div>
                </div>
              ))}
            </div>
          </div>

          <div className="card flex flex-col">
            <h3 className="text-white font-bold mb-4 flex items-center gap-2">
              <Clock className="w-4 h-4 text-slate-400" /> Últimas Entradas
            </h3>
            <div className="space-y-3 flex-1">
              {(data?.ultimas_entradas || []).map((e, i) => (
                <div key={i} className="flex items-center justify-between p-3 rounded-xl bg-park-border/20 hover:bg-park-border/40 transition-colors">
                  <div className="flex items-center gap-3">
                    <div className={`w-2.5 h-2.5 rounded-full shadow-[0_0_8px_rgba(0,0,0,0.5)] ${e.estado === 'activo' ? 'bg-emerald-400 shadow-emerald-400/50' : 'bg-slate-500'}`} />
                    <div>
                      <p className="text-white font-bold text-sm tracking-wide">{e.placa}</p>
                      <p className="text-park-muted text-[10px] uppercase font-semibold">{e.tipo_vehiculo} • Espacio {e.espacio || 'N/A'}</p>
                    </div>
                  </div>
                  <span className="bg-[#0f172a] text-slate-300 px-2.5 py-1 rounded-md text-xs font-medium border border-park-border/50">
                    {new Date(e.hora_entrada).toLocaleTimeString('es-EC', { hour: '2-digit', minute: '2-digit' })}
                  </span>
                </div>
              ))}
              {!data?.ultimas_entradas?.length && (
                <div className="h-full flex items-center justify-center flex-col text-slate-500 text-sm gap-2 mt-4">
                  <Car className="w-8 h-8 opacity-20" />
                  No hay vehículos recientes
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

