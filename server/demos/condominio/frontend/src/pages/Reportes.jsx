import { useState } from 'react';
import api from '../services/api';

export default function Reportes() {
  const [loading, setLoading] = useState({});

  const downloadCSV = (filename, rows) => {
    if (!rows || !rows.length) {
      alert('No hay datos disponibles para exportar.');
      return;
    }
    
    // Obtener los encabezados de las keys del primer objeto
    const headers = Object.keys(rows[0]);
    
    // Crear el contenido CSV
    const csvContent = [
      headers.join(','), // Fila de encabezados
      ...rows.map(row => 
        headers.map(header => {
          let val = row[header];
          // Limpiar el string si tiene comas o saltos de línea
          if (typeof val === 'string') {
            val = val.replace(/"/g, '""'); // Escapar comillas dobles
            if (val.includes(',') || val.includes('\n') || val.includes('\r')) {
              val = `"${val}"`;
            }
          } else if (val === null || val === undefined) {
            val = '';
          }
          return val;
        }).join(',')
      )
    ].join('\n');

    // Forzar decarga como Blob Blob type standard Excel CSV support
    const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `${filename}_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  const handleExport = async (endpoint, filename) => {
    setLoading(prev => ({ ...prev, [endpoint]: true }));
    try {
      const res = await api.get(`/reportes/${endpoint}`);
      downloadCSV(filename, res.data.data);
    } catch (err) {
      alert('Error al generar el reporte: ' + (err.response?.data?.message || err.message));
    } finally {
      setLoading(prev => ({ ...prev, [endpoint]: false }));
    }
  };

  const informes = [
    { id: 'pagos', icon: '💰', title: 'Reporte de Pagos', desc: 'Historial completo de pagos recibidos, métodos y folios.', color: 'var(--primary)' },
    { id: 'morosos', icon: '⚠️', title: 'Reporte de Morosos', desc: 'Residencias con cuotas vencidas, días de retraso y monto de deuda.', color: 'var(--accent-red)' },
    { id: 'accesos', icon: '🛡️', title: 'Registro de Accesos', desc: 'Control de ingresos y salidas de visitantes a la privada o torre.', color: 'var(--accent-pink)' },
    { id: 'mantenimiento', icon: '🔧', title: 'Órdenes de Trabajo', desc: 'Seguimiento de mantenimientos, estados y costos reales.', color: 'var(--accent-amber)' },
  ];

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <div className="page-title">📑 Reportes y Exportación</div>
          <div className="page-subtitle">Exporta fácilmente los datos del sistema a Excel (CSV)</div>
        </div>
      </div>

      <div className="grid-3">
        {informes.map((r) => (
          <div className="card" key={r.id}>
            <div style={{ fontSize: 40, marginBottom: 12 }}>{r.icon}</div>
            <div style={{ fontSize: 16, fontWeight: 700, marginBottom: 6 }}>{r.title}</div>
            <div style={{ fontSize: 13, color: 'var(--text-secondary)', marginBottom: 16 }}>{r.desc}</div>
            <div style={{ display: 'flex', gap: 8 }}>
              <button 
                className="btn btn-sm btn-primary" 
                onClick={() => handleExport(r.id, `Reporte_${r.id}`)}
                disabled={loading[r.id]}
              >
                {loading[r.id] ? 'Cargando...' : '📊 Exportar Excel CSV'}
              </button>
            </div>
          </div>
        ))}
        
        {/* Placeholder estático simulado de estado financiero */}
        <div className="card" style={{ opacity: 0.7 }}>
          <div style={{ fontSize: 40, marginBottom: 12 }}>💸</div>
          <div style={{ fontSize: 16, fontWeight: 700, marginBottom: 6 }}>Estado de Resultados</div>
          <div style={{ fontSize: 13, color: 'var(--text-secondary)', marginBottom: 16 }}>
             (Próximamente junto al módulo contable).
          </div>
          <div style={{ display: 'flex', gap: 8 }}>
             <button className="btn btn-sm btn-secondary" disabled>En desarrollo</button>
          </div>
        </div>
      </div>
    </div>
  );
}
