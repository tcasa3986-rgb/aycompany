import { useState, useEffect } from 'react';
import api from '../api/axios';
import { AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, BarChart, Bar } from 'recharts';
import { TrendingUp, Calendar } from 'lucide-react';

const customTooltip = ({ active, payload, label }, currency) => {
  if (active && payload?.length) return (
    <div className="bg-park-card border border-park-border rounded-lg p-3 text-sm shadow-xl">
      <p className="text-park-muted mb-1">{label}</p>
      <p className="text-park-accent font-bold">{currency}{Number(payload[0].value || 0).toFixed(2)}</p>
    </div>
  );
  return null;
};

import { useConfig } from '../contexts/ConfigContext';

export default function Reportes() {
  const { config } = useConfig();
  const currency = config?.moneda || '$';
  const [ingresos, setIngresos] = useState([]);
  const [ocupacion, setOcupacion] = useState([]);
  const [cierres, setCierres] = useState([]);
  const [desde, setDesde] = useState(new Date(Date.now() - 30*86400000).toISOString().slice(0,10));
  const [hasta, setHasta] = useState(new Date().toISOString().slice(0,10));
  const [loading, setLoading] = useState(false);

  const fetch = async () => {
    setLoading(true);
    try {
      const [i, o, c] = await Promise.all([
        api.get(`/reportes/ingresos?desde=${desde}&hasta=${hasta}&agrupar=dia`),
        api.get('/reportes/ocupacion'),
        api.get('/pagos/cierres'),
      ]);
      setIngresos(i.data.map(d => ({ ...d, total: parseFloat(d.total) })));
      setOcupacion(o.data.map(d => ({ fecha: d.fecha, vehiculos: parseInt(d.vehiculos) })));
      setCierres(c.data);
    } catch (e) { console.error(e); }
    finally { setLoading(false); }
  };

  useEffect(() => { fetch(); }, []);

  const totalPeriodo = ingresos.reduce((a, b) => a + b.total, 0);

  return (
    <div className="space-y-6 animate-fade-in">
      {/* Filtros */}
      <div className="card flex flex-col sm:flex-row flex-wrap items-start sm:items-end gap-4">
        <div>
          <label className="block text-park-muted text-sm mb-1">Desde</label>
          <input type="date" className="input" value={desde} onChange={e => setDesde(e.target.value)} />
        </div>
        <div>
          <label className="block text-park-muted text-sm mb-1">Hasta</label>
          <input type="date" className="input" value={hasta} onChange={e => setHasta(e.target.value)} />
        </div>
        <button onClick={fetch} disabled={loading} className="btn-primary">
          <TrendingUp className="w-4 h-4" /> {loading ? 'Cargando...' : 'Generar Reporte'}
        </button>
        <div className="w-full sm:w-auto sm:ml-auto text-left sm:text-right mt-2 sm:mt-0">
          <p className="text-park-muted text-xs">Total del período</p>
          <p className="text-park-accent text-2xl font-black">{currency}{totalPeriodo.toFixed(2)}</p>
        </div>
      </div>

      {/* Gráfico ingresos */}
      <div className="card">
        <h3 className="text-park-text font-semibold mb-4">Ingresos por Día</h3>
        {ingresos.length > 0 ? (
          <ResponsiveContainer width="100%" height={220}>
            <AreaChart data={ingresos}>
              <defs>
                <linearGradient id="gradIngresos" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="5%" stopColor="#f59e0b" stopOpacity={0.3} />
                  <stop offset="95%" stopColor="#f59e0b" stopOpacity={0} />
                </linearGradient>
              </defs>
              <CartesianGrid strokeDasharray="3 3" stroke="#1e3a5f" vertical={false} />
              <XAxis dataKey="periodo" tick={{ fill: '#94a3b8', fontSize: 10 }} axisLine={false} tickLine={false} />
              <YAxis tick={{ fill: '#94a3b8', fontSize: 10 }} axisLine={false} tickLine={false} tickFormatter={v => `${currency}${v}`} />
              <Tooltip content={p => customTooltip(p, currency)} />
              <Area type="monotone" dataKey="total" stroke="#f59e0b" strokeWidth={2} fill="url(#gradIngresos)" />
            </AreaChart>
          </ResponsiveContainer>
        ) : (
          <div className="h-48 flex items-center justify-center text-park-muted">Sin datos en el período</div>
        )}
      </div>

      {/* Vehículos por día */}
      <div className="card">
        <h3 className="text-park-text font-semibold mb-4">Vehículos Atendidos (últimos 30 días)</h3>
        {ocupacion.length > 0 ? (
          <ResponsiveContainer width="100%" height={180}>
            <BarChart data={ocupacion} barSize={16}>
              <CartesianGrid strokeDasharray="3 3" stroke="#1e3a5f" vertical={false} />
              <XAxis dataKey="fecha" tick={{ fill: '#94a3b8', fontSize: 10 }} axisLine={false} tickLine={false} />
              <YAxis tick={{ fill: '#94a3b8', fontSize: 10 }} axisLine={false} tickLine={false} allowDecimals={false} />
              <Tooltip content={({ active, payload, label }) => active && payload?.length ? (
                <div className="bg-park-card border border-park-border rounded-lg p-3 text-sm">
                  <p className="text-park-muted mb-1">{label}</p>
                  <p className="text-blue-400 font-bold">{payload[0].value} vehículos</p>
                </div>
              ) : null} />
              <Bar dataKey="vehiculos" fill="#3b82f6" radius={[4, 4, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        ) : (
          <div className="h-44 flex items-center justify-center text-park-muted">Sin datos de ocupación</div>
        )}
      </div>

      {/* Cierres de caja */}
      <div className="card">
        <h3 className="text-park-text font-semibold mb-4 flex items-center gap-2">
          <Calendar className="w-4 h-4 text-park-accent" /> Cierres de Caja
        </h3>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-park-border">
                {['Fecha', 'Cajero', 'Vehículos', 'Efectivo', 'Tarjeta', 'QR', 'Total'].map(h => (
                  <th key={h} className="table-header text-left pb-3 px-2">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody>
              {cierres.map(c => (
                <tr key={c.id} className="hover:bg-park-border/10 transition-colors">
                  <td className="table-cell px-2">{new Date(c.fecha_cierre).toLocaleDateString('es-EC')}</td>
                  <td className="table-cell px-2 text-park-muted">{c.cajero}</td>
                  <td className="table-cell px-2">{c.total_vehiculos}</td>
                  <td className="table-cell px-2">{currency}{parseFloat(c.total_efectivo||0).toFixed(2)}</td>
                  <td className="table-cell px-2">{currency}{parseFloat(c.total_tarjeta||0).toFixed(2)}</td>
                  <td className="table-cell px-2">{currency}{parseFloat(c.total_qr||0).toFixed(2)}</td>
                  <td className="table-cell px-2 font-bold text-park-accent">{currency}{parseFloat(c.total_general||0).toFixed(2)}</td>
                </tr>
              ))}
            </tbody>
          </table>
          {!cierres.length && <p className="text-center text-park-muted py-6">Sin cierres registrados</p>}
        </div>
      </div>
    </div>
  );
}
