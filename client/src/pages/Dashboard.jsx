import { useEffect, useState } from 'react';
import api from '../api/axios';
import { Users, Key, AlertTriangle, DollarSign } from 'lucide-react';

function StatCard({ icon: Icon, label, value, color, sub }) {
  return (
    <div style={{ background: '#fff', borderRadius: 12, padding: '20px 24px', boxShadow: '0 1px 4px rgba(0,0,0,.07)', display: 'flex', alignItems: 'center', gap: 16 }}>
      <div style={{ background: color + '18', borderRadius: 10, padding: 12 }}>
        <Icon size={22} color={color} />
      </div>
      <div>
        <div style={{ fontSize: '1.7rem', fontWeight: 700, color: '#1e293b', lineHeight: 1 }}>{value}</div>
        <div style={{ fontSize: '.82rem', color: '#64748b', marginTop: 3 }}>{label}</div>
        {sub && <div style={{ fontSize: '.78rem', color: color, marginTop: 2 }}>{sub}</div>}
      </div>
    </div>
  );
}

function diasRestantes(fecha) {
  return Math.ceil((new Date(fecha) - new Date()) / 86400000);
}

export default function Dashboard() {
  const [data, setData] = useState(null);

  useEffect(() => {
    api.get('/dashboard/stats').then(r => setData(r.data.data));
  }, []);

  if (!data) return <div style={{ padding: 32, color: '#64748b' }}>Cargando...</div>;

  return (
    <div style={{ padding: 32 }}>
      <h1 style={{ fontSize: '1.4rem', fontWeight: 700, marginBottom: 24, color: '#1e293b' }}>Dashboard</h1>

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: 16, marginBottom: 32 }}>
        <StatCard icon={Users}        label="Clientes activos"      value={data.totalClientes}    color="#6366f1" />
        <StatCard icon={Key}          label="Licencias activas"     value={data.licenciasActivas} color="#10b981" />
        <StatCard icon={AlertTriangle} label="Vencen en 7 días"     value={data.porVencer}        color="#f59e0b" sub={data.porVencer > 0 ? 'Requieren atención' : ''} />
        <StatCard icon={DollarSign}   label="Ingresos del mes"      value={`$${Number(data.ingresosMes).toLocaleString('es')}`} color="#3b82f6" />
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20 }}>
        {/* Próximos a vencer */}
        <div style={{ background: '#fff', borderRadius: 12, padding: 24, boxShadow: '0 1px 4px rgba(0,0,0,.07)' }}>
          <h2 style={{ fontSize: '1rem', fontWeight: 700, marginBottom: 16, color: '#1e293b' }}>⚠️ Próximos a vencer</h2>
          {data.proximosVencer.length === 0
            ? <p style={{ color: '#94a3b8', fontSize: '.9rem' }}>No hay licencias por vencer esta semana</p>
            : data.proximosVencer.map(l => {
                const dias = diasRestantes(l.fecha_vencimiento);
                return (
                  <div key={l.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '10px 0', borderBottom: '1px solid #f1f5f9' }}>
                    <div>
                      <div style={{ fontWeight: 600, fontSize: '.9rem' }}>{l.cliente?.nombre}</div>
                      <div style={{ fontSize: '.78rem', color: '#64748b' }}>{l.producto?.nombre}</div>
                    </div>
                    <span style={{ background: dias <= 3 ? '#fef2f2' : '#fffbeb', color: dias <= 3 ? '#dc2626' : '#d97706', padding: '3px 10px', borderRadius: 20, fontSize: '.78rem', fontWeight: 600 }}>
                      {dias === 0 ? 'Hoy' : `${dias}d`}
                    </span>
                  </div>
                );
              })
          }
        </div>

        {/* Últimos pagos */}
        <div style={{ background: '#fff', borderRadius: 12, padding: 24, boxShadow: '0 1px 4px rgba(0,0,0,.07)' }}>
          <h2 style={{ fontSize: '1rem', fontWeight: 700, marginBottom: 16, color: '#1e293b' }}>💳 Últimos pagos</h2>
          {data.ultimosPagos.length === 0
            ? <p style={{ color: '#94a3b8', fontSize: '.9rem' }}>No hay pagos registrados</p>
            : data.ultimosPagos.map(p => (
                <div key={p.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '10px 0', borderBottom: '1px solid #f1f5f9' }}>
                  <div>
                    <div style={{ fontWeight: 600, fontSize: '.9rem' }}>{p.cliente?.nombre}</div>
                    <div style={{ fontSize: '.78rem', color: '#64748b' }}>{p.licencia?.producto?.nombre} · {new Date(p.fecha_pago).toLocaleDateString('es')}</div>
                  </div>
                  <span style={{ fontWeight: 700, color: '#10b981', fontSize: '.95rem' }}>${Number(p.monto).toLocaleString('es')}</span>
                </div>
              ))
          }
        </div>
      </div>
    </div>
  );
}
