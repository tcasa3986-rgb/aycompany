import { useEffect, useState } from 'react';
import api from '../api/axios';
import { FileText, Trash2 } from 'lucide-react';

export default function Facturas() {
  const [facturas, setFacturas] = useState([]);

  const cargar = () => api.get('/facturas').then(r => setFacturas(r.data.data));
  useEffect(() => { cargar(); }, []);

  async function eliminar(id) {
    if (!confirm('¿Eliminar esta factura?')) return;
    await api.delete(`/facturas/${id}`);
    cargar();
  }

  const totalMes = facturas
    .filter(f => {
      const d = new Date(f.fecha);
      const hoy = new Date();
      return d.getMonth() === hoy.getMonth() && d.getFullYear() === hoy.getFullYear();
    })
    .reduce((acc, f) => acc + Number(f.monto), 0);

  return (
    <div style={{ padding: 32 }}>
      <div style={{ marginBottom: 24 }}>
        <h1 style={{ fontSize: '1.4rem', fontWeight: 700, display: 'flex', alignItems: 'center', gap: 8 }}>
          <FileText size={22} /> Facturas
        </h1>
        <p style={{ color: '#64748b', fontSize: '.88rem', marginTop: 2 }}>
          Facturado este mes: <strong style={{ color: '#10b981' }}>${totalMes.toLocaleString('es-CO')}</strong>
        </p>
      </div>

      <div style={{ background: '#fff', borderRadius: 12, boxShadow: '0 1px 4px rgba(0,0,0,.07)', overflow: 'hidden' }}>
        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
          <thead>
            <tr style={{ background: '#f8fafc' }}>
              {['N° Factura', 'Fecha', 'Cliente', 'Concepto', 'Método', 'Monto', ''].map(h => (
                <th key={h} style={{ padding: '10px 16px', textAlign: 'left', fontSize: '.8rem', color: '#64748b', fontWeight: 600 }}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {facturas.map(f => (
              <tr key={f.id} style={{ borderTop: '1px solid #f1f5f9' }}>
                <td style={td}>
                  <span style={{ fontFamily: 'monospace', fontWeight: 700, color: '#4f46e5' }}>{f.numero}</span>
                </td>
                <td style={td}>{new Date(f.fecha + 'T00:00:00').toLocaleDateString('es-CO')}</td>
                <td style={td}><strong>{f.cliente?.nombre}</strong></td>
                <td style={td}>{f.concepto}</td>
                <td style={td}>
                  <span style={{ background: f.metodo_pago === 'MercadoPago' ? '#e0f2fe' : '#f1f5f9', color: f.metodo_pago === 'MercadoPago' ? '#0284c7' : '#475569', padding: '2px 9px', borderRadius: 12, fontSize: '.78rem' }}>
                    {f.metodo_pago}
                  </span>
                </td>
                <td style={{ ...td, fontWeight: 700, color: '#10b981' }}>${Number(f.monto).toLocaleString('es-CO')}</td>
                <td style={td}>
                  <button onClick={() => eliminar(f.id)} style={{ background: '#fef2f2', color: '#ef4444', border: 'none', borderRadius: 6, padding: '5px 8px' }}>
                    <Trash2 size={13} />
                  </button>
                </td>
              </tr>
            ))}
            {facturas.length === 0 && (
              <tr><td colSpan={7} style={{ padding: 32, textAlign: 'center', color: '#94a3b8' }}>
                No hay facturas aún. Se generan automáticamente con cada pago.
              </td></tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

const td = { padding: '12px 16px', fontSize: '.9rem' };
