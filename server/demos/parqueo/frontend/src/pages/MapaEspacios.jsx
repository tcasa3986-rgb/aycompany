import { useState, useEffect, useCallback } from 'react';
import api from '../api/axios';
import { RefreshCw } from 'lucide-react';

const TIPO_COLORS = {
  auto:          { libre: 'bg-emerald-500/20 border-emerald-500/50 text-emerald-400', ocupado: 'bg-red-500/20 border-red-500/50 text-red-400', mant: 'bg-gray-500/20 border-gray-400/50 text-gray-400' },
  moto:          { libre: 'bg-blue-500/20 border-blue-500/50 text-blue-400', ocupado: 'bg-red-500/20 border-red-500/50 text-red-400', mant: 'bg-gray-500/20 border-gray-400/50 text-gray-400' },
  VIP:           { libre: 'bg-purple-500/20 border-purple-500/50 text-purple-400', ocupado: 'bg-red-500/20 border-red-500/50 text-red-400', mant: 'bg-gray-500/20 border-gray-400/50 text-gray-400' },
  discapacitado: { libre: 'bg-amber-500/20 border-amber-500/50 text-amber-400', ocupado: 'bg-red-500/20 border-red-500/50 text-red-400', mant: 'bg-gray-500/20 border-gray-400/50 text-gray-400' },
};

const TIPO_ICON = { auto: '🚗', moto: '🏍️', VIP: '⭐', discapacitado: '♿' };

function EspacioCard({ e, onClick }) {
  const colors = TIPO_COLORS[e.tipo] || TIPO_COLORS.auto;
  const estadoColor = e.estado === 'libre' ? colors.libre : e.estado === 'ocupado' ? colors.ocupado : colors.mant;
  return (
    <button
      onClick={() => onClick(e)}
      className={`border rounded-xl p-3 flex flex-col items-center gap-1 transition-all duration-200 hover:scale-105 ${estadoColor}`}
    >
      <span className="text-xl">{TIPO_ICON[e.tipo]}</span>
      <span className="text-xs font-bold">{e.numero}</span>
      {e.estado === 'ocupado' && e.placa && (
        <span className="text-[10px] truncate w-full text-center font-medium">{e.placa}</span>
      )}
      <div className={`w-1.5 h-1.5 rounded-full ${e.estado === 'libre' ? 'bg-emerald-400' : e.estado === 'ocupado' ? 'bg-red-400' : 'bg-gray-400'} animate-pulse-slow`} />
    </button>
  );
}

export default function MapaEspacios() {
  const [espacios, setEspacios] = useState([]);
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);
  const [filtroTipo, setFiltroTipo] = useState('todos');
  const [selected, setSelected] = useState(null);

  const fetchData = useCallback(async () => {
    try {
      const [esp, st] = await Promise.all([api.get('/espacios'), api.get('/espacios/stats')]);
      setEspacios(esp.data);
      setStats(st.data);
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchData();
    const t = setInterval(fetchData, 15000);
    return () => clearInterval(t);
  }, [fetchData]);

  const filtrado = filtroTipo === 'todos' ? espacios : espacios.filter(e => e.tipo === filtroTipo);
  // agrupar por zona
  const porZona = filtrado.reduce((acc, e) => {
    const z = e.zona_nombre || 'Sin zona';
    if (!acc[z]) acc[z] = [];
    acc[z].push(e);
    return acc;
  }, {});

  return (
    <div className="space-y-5 animate-fade-in">
      {/* Stats bar */}
      {stats?.totales && (
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          {[
            { label: 'Total', val: stats.totales.total, color: 'text-park-text' },
            { label: 'Libres', val: stats.totales.libres, color: 'text-park-libre' },
            { label: 'Ocupados', val: stats.totales.ocupados, color: 'text-park-ocupado' },
          ].map(s => (
            <div key={s.label} className="card text-center py-4">
              <p className={`text-3xl font-black ${s.color}`}>{s.val}</p>
              <p className="text-park-muted text-xs mt-1">{s.label}</p>
            </div>
          ))}
        </div>
      )}

      {/* Filtros + refresh */}
      <div className="flex items-center gap-3 flex-wrap">
        {['todos', 'auto', 'moto', 'VIP', 'discapacitado'].map(t => (
          <button
            key={t}
            onClick={() => setFiltroTipo(t)}
            className={`px-3 py-1.5 rounded-lg text-sm font-medium transition-colors ${
              filtroTipo === t ? 'bg-park-accent text-park-dark' : 'bg-park-card text-park-muted hover:text-park-text border border-park-border'
            }`}
          >
            {t === 'todos' ? 'Todos' : `${TIPO_ICON[t]} ${t}`}
          </button>
        ))}
        <button onClick={fetchData} className="btn-secondary ml-auto text-sm py-1.5">
          <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`} />
          Actualizar
        </button>
      </div>

      {/* Leyenda */}
      <div className="flex items-center gap-5 text-xs text-park-muted">
        {[['bg-emerald-400', 'Libre'], ['bg-red-400', 'Ocupado'], ['bg-gray-400', 'Mantenimiento']].map(([c, l]) => (
          <span key={l} className="flex items-center gap-1.5">
            <span className={`w-2.5 h-2.5 rounded-full ${c}`} />{l}
          </span>
        ))}
      </div>

      {/* Mapa por zonas */}
      {Object.entries(porZona).map(([zona, esp]) => (
        <div key={zona} className="card">
          <h3 className="text-park-text font-semibold mb-3 flex items-center gap-2">
            <span className="w-2 h-4 bg-park-accent rounded-sm" />
            {zona}
            <span className="text-park-muted text-xs font-normal">({esp.filter(e => e.estado === 'libre').length} libres / {esp.length} total)</span>
          </h3>
          <div className="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 gap-2">
            {esp.map(e => (
              <EspacioCard key={e.id} e={e} onClick={setSelected} />
            ))}
          </div>
        </div>
      ))}

      {/* Modal info espacio */}
      {selected && (
        <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onClick={() => setSelected(null)}>
          <div className="card max-w-sm w-full animate-slide-in" onClick={e => e.stopPropagation()}>
            <h3 className="text-park-text font-bold text-lg mb-4">
              {TIPO_ICON[selected.tipo]} Espacio {selected.numero}
            </h3>
            <div className="space-y-2 text-sm">
              {[
                ['Zona', selected.zona_nombre],
                ['Tipo', selected.tipo],
                ['Estado', selected.estado],
                selected.placa && ['Placa', selected.placa],
                selected.hora_entrada && ['Entrada', new Date(selected.hora_entrada).toLocaleString('es-EC')],
              ].filter(Boolean).map(([k, v]) => (
                <div key={k} className="flex justify-between py-1.5 border-b border-park-border/30">
                  <span className="text-park-muted">{k}</span>
                  <span className="text-park-text font-medium capitalize">{v}</span>
                </div>
              ))}
            </div>
            <button onClick={() => setSelected(null)} className="btn-secondary w-full justify-center mt-4">Cerrar</button>
          </div>
        </div>
      )}
    </div>
  );
}
