import { useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { BarChart2, Download, Users, Key, CreditCard, AlertCircle, FolderOpen, Headphones } from 'lucide-react';

const REPORTES = [
  {
    key: 'clientes',
    label: 'Clientes',
    desc: 'Nombre, empresa, email, teléfono, ciudad, fecha de registro',
    icon: Users,
    color: '#6366f1',
    bg: '#ede9fe',
  },
  {
    key: 'licencias',
    label: 'Licencias',
    desc: 'Clave, cliente, producto, estado, vencimiento, días restantes',
    icon: Key,
    color: '#059669',
    bg: '#d1fae5',
  },
  {
    key: 'pagos',
    label: 'Pagos',
    desc: 'Fecha, cliente, producto, meses, método, monto, descuento',
    icon: CreditCard,
    color: '#0284c7',
    bg: '#dbeafe',
  },
  {
    key: 'cartera',
    label: 'Cartera vencida',
    desc: 'Licencias vencidas y por vencer con valor en riesgo',
    icon: AlertCircle,
    color: '#ef4444',
    bg: '#fee2e2',
  },
  {
    key: 'proyectos',
    label: 'Proyectos',
    desc: 'Nombre, cliente, estado, presupuesto, avance de tareas',
    icon: FolderOpen,
    color: '#f59e0b',
    bg: '#fef3c7',
  },
  {
    key: 'tickets',
    label: 'Tickets de soporte',
    desc: 'Asunto, cliente, estado, fecha de apertura y respuesta',
    icon: Headphones,
    color: '#7c3aed',
    bg: '#ede9fe',
  },
];

export default function Reportes() {
  const [loading, setLoading] = useState({});

  async function descargar(key) {
    setLoading(l => ({ ...l, [key]: true }));
    try {
      const res = await api.get(`/reportes/${key}`, { responseType: 'blob' });
      const url = URL.createObjectURL(new Blob([res.data]));
      const a = document.createElement('a');
      a.href = url;
      a.download = `reporte-${key}-${new Date().toISOString().slice(0,10)}.csv`;
      a.click();
      URL.revokeObjectURL(url);
      toast.success('Reporte descargado');
    } catch {
      toast.error('Error al generar reporte');
    }
    setLoading(l => ({ ...l, [key]: false }));
  }

  return (
    <div style={{ padding: 32, maxWidth: 900 }}>
      <div style={{ marginBottom: 28 }}>
        <h1 style={{ fontSize: '1.4rem', fontWeight: 700, display: 'flex', alignItems: 'center', gap: 8 }}>
          <BarChart2 size={22} color="#6366f1"/> Reportes y exportaciones
        </h1>
        <p style={{ color: '#64748b', fontSize: '.88rem', marginTop: 4 }}>
          Descarga tus datos en formato CSV para analizar en Excel o Google Sheets
        </p>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(260px, 1fr))', gap: 16 }}>
        {REPORTES.map(r => {
          const Icon = r.icon;
          const busy = loading[r.key];
          return (
            <div key={r.key} style={{ background: '#fff', borderRadius: 12, padding: 20, boxShadow: '0 1px 4px rgba(0,0,0,.07)', display: 'flex', flexDirection: 'column', gap: 14 }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                <div style={{ background: r.bg, borderRadius: 10, padding: 10 }}>
                  <Icon size={20} color={r.color}/>
                </div>
                <div>
                  <div style={{ fontWeight: 700, fontSize: '.95rem', color: '#1e293b' }}>{r.label}</div>
                  <div style={{ fontSize: '.78rem', color: '#64748b', marginTop: 2 }}>{r.desc}</div>
                </div>
              </div>
              <button
                onClick={() => descargar(r.key)}
                disabled={busy}
                style={{
                  display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 6,
                  padding: '9px 16px', background: busy ? '#f1f5f9' : r.color,
                  color: busy ? '#94a3b8' : '#fff',
                  border: 'none', borderRadius: 8, fontSize: '.85rem', fontWeight: 600, cursor: busy ? 'not-allowed' : 'pointer',
                  transition: 'opacity .15s'
                }}>
                <Download size={15}/>
                {busy ? 'Generando...' : 'Descargar CSV'}
              </button>
            </div>
          );
        })}
      </div>

      <div style={{ marginTop: 32, background: '#f8fafc', borderRadius: 12, padding: 20, border: '1px solid #e2e8f0' }}>
        <div style={{ fontWeight: 700, fontSize: '.9rem', color: '#374151', marginBottom: 8 }}>Reporte completo del período</div>
        <p style={{ fontSize: '.85rem', color: '#64748b', margin: '0 0 14px' }}>
          Descarga un ZIP con todos los reportes del período seleccionado.
        </p>
        <div style={{ display: 'flex', gap: 10, alignItems: 'center', flexWrap: 'wrap' }}>
          <input type="date" id="desde" style={inp} defaultValue={new Date(new Date().setDate(1)).toISOString().slice(0,10)}/>
          <span style={{ color: '#94a3b8' }}>→</span>
          <input type="date" id="hasta" style={inp} defaultValue={new Date().toISOString().slice(0,10)}/>
          <button
            onClick={() => {
              const desde = document.getElementById('desde').value;
              const hasta = document.getElementById('hasta').value;
              const token = localStorage.getItem('token') || sessionStorage.getItem('token');
              window.open(`/api/reportes/zip?desde=${desde}&hasta=${hasta}`, '_blank');
            }}
            style={{ display: 'flex', alignItems: 'center', gap: 6, padding: '9px 16px', background: '#1e1b4b', color: '#fff', border: 'none', borderRadius: 8, fontSize: '.85rem', fontWeight: 600, cursor: 'pointer' }}>
            <Download size={15}/> Descargar todo
          </button>
        </div>
      </div>
    </div>
  );
}

const inp = { padding: '8px 11px', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: '.88rem', outline: 'none', background: '#fff' };
