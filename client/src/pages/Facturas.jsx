import { useEffect, useState } from 'react';
import api from '../api/axios';
import { FileText, Trash2, Download, Send, FileSpreadsheet } from 'lucide-react';

const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

export default function Facturas() {
  const hoy = new Date();
  const [facturas,  setFacturas]  = useState([]);
  const [clientes,  setClientes]  = useState([]);
  const [filtros,   setFiltros]   = useState({
    cliente_id: '', mes: String(hoy.getMonth() + 1), anio: String(hoy.getFullYear()), metodo: ''
  });
  const [enviando,  setEnviando]  = useState(null);
  const [msg,       setMsg]       = useState('');

  const cargar = () => {
    const params = new URLSearchParams();
    if (filtros.cliente_id) params.set('cliente_id', filtros.cliente_id);
    if (filtros.mes && filtros.anio) { params.set('mes', filtros.mes); params.set('anio', filtros.anio); }
    if (filtros.metodo) params.set('metodo', filtros.metodo);
    api.get(`/facturas?${params}`).then(r => setFacturas(r.data.data));
  };

  useEffect(() => {
    cargar();
    api.get('/clientes').then(r => setClientes(r.data.data || []));
  }, []);

  useEffect(() => { cargar(); }, [filtros]);

  function setF(k, v) { setFiltros(f => ({ ...f, [k]: v })); }
  function limpiar() { setFiltros({ cliente_id: '', mes: '', anio: '', metodo: '' }); }

  async function eliminar(id) {
    if (!confirm('¿Eliminar esta factura?')) return;
    await api.delete(`/facturas/${id}`);
    cargar();
  }

  async function enviarEmail(f) {
    setEnviando(f.id);
    setMsg('');
    try {
      const r = await api.post(`/facturas/${f.id}/enviar-email`, {});
      setMsg(r.data.msg);
    } catch (e) {
      setMsg(e.response?.data?.msg || 'Error al enviar');
    } finally {
      setEnviando(null);
    }
  }

  function descargarPDF(f) {
    const token = localStorage.getItem('token');
    const a = document.createElement('a');
    a.href = `/api/facturas/${f.id}/pdf`;
    // Añadir token via fetch para descarga con auth
    fetch(`/api/facturas/${f.id}/pdf`, { headers: { Authorization: `Bearer ${token}` } })
      .then(r => r.blob())
      .then(blob => {
        const url = URL.createObjectURL(blob);
        a.href = url;
        a.download = `${f.numero}.pdf`;
        a.click();
        URL.revokeObjectURL(url);
      });
  }

  function exportarExcel() {
    const token = localStorage.getItem('token');
    const params = new URLSearchParams();
    if (filtros.cliente_id) params.set('cliente_id', filtros.cliente_id);
    if (filtros.mes && filtros.anio) { params.set('mes', filtros.mes); params.set('anio', filtros.anio); }
    if (filtros.metodo) params.set('metodo', filtros.metodo);
    fetch(`/api/facturas/exportar/excel?${params}`, { headers: { Authorization: `Bearer ${token}` } })
      .then(r => r.blob())
      .then(blob => {
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'facturas.xlsx';
        a.click();
      });
  }

  const total = facturas.reduce((acc, f) => acc + Number(f.monto), 0);
  const anios = Array.from({ length: 5 }, (_, i) => String(hoy.getFullYear() - i));

  return (
    <div style={{ padding: 32 }}>
      {/* Encabezado */}
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 20, flexWrap: 'wrap', gap: 12 }}>
        <div>
          <h1 style={{ fontSize: '1.4rem', fontWeight: 700, display: 'flex', alignItems: 'center', gap: 8 }}>
            <FileText size={22} /> Facturas
          </h1>
          <p style={{ color: '#64748b', fontSize: '.88rem', marginTop: 2 }}>
            {facturas.length} factura{facturas.length !== 1 ? 's' : ''} ·{' '}
            <strong style={{ color: '#10b981' }}>${total.toLocaleString('es-CO')} COP</strong>
          </p>
        </div>
        <button onClick={exportarExcel}
          style={{ display: 'flex', alignItems: 'center', gap: 6, padding: '8px 16px', background: '#f0fdf4', color: '#16a34a', border: '1px solid #bbf7d0', borderRadius: 8, fontSize: '.85rem', fontWeight: 600, cursor: 'pointer' }}>
          <FileSpreadsheet size={15} /> Exportar Excel
        </button>
      </div>

      {/* Filtros */}
      <div style={{ background: '#fff', borderRadius: 10, padding: '14px 18px', marginBottom: 16, boxShadow: '0 1px 3px rgba(0,0,0,.06)', display: 'flex', gap: 10, flexWrap: 'wrap', alignItems: 'center' }}>
        <select value={filtros.mes} onChange={e => setF('mes', e.target.value)} style={sel}>
          <option value="">Todo el año</option>
          {MESES.map((m, i) => <option key={i} value={String(i + 1)}>{m}</option>)}
        </select>
        <select value={filtros.anio} onChange={e => setF('anio', e.target.value)} style={sel}>
          <option value="">Año</option>
          {anios.map(a => <option key={a} value={a}>{a}</option>)}
        </select>
        <select value={filtros.cliente_id} onChange={e => setF('cliente_id', e.target.value)} style={sel}>
          <option value="">Todos los clientes</option>
          {clientes.map(c => <option key={c.id} value={c.id}>{c.nombre}</option>)}
        </select>
        <select value={filtros.metodo} onChange={e => setF('metodo', e.target.value)} style={sel}>
          <option value="">Todos los métodos</option>
          {['efectivo','transferencia','MercadoPago','nequi','daviplata'].map(m => <option key={m} value={m}>{m}</option>)}
        </select>
        {(filtros.cliente_id || filtros.metodo || !filtros.mes) && (
          <button onClick={limpiar} style={{ fontSize: '.8rem', color: '#94a3b8', background: 'none', border: 'none', cursor: 'pointer' }}>
            Limpiar filtros
          </button>
        )}
      </div>

      {msg && (
        <div style={{ background: '#f0fdf4', border: '1px solid #bbf7d0', borderRadius: 8, padding: '10px 14px', marginBottom: 14, color: '#16a34a', fontSize: '.88rem' }}>
          {msg}
        </div>
      )}

      {/* Tabla */}
      <div style={{ background: '#fff', borderRadius: 12, boxShadow: '0 1px 4px rgba(0,0,0,.07)', overflow: 'hidden' }}>
        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
          <thead>
            <tr style={{ background: '#f8fafc' }}>
              {['N° Factura', 'Fecha', 'Cliente', 'Concepto', 'Método', 'Monto', 'Acciones'].map(h => (
                <th key={h} style={{ padding: '10px 14px', textAlign: 'left', fontSize: '.78rem', color: '#64748b', fontWeight: 600 }}>{h}</th>
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
                <td style={td}>
                  <div style={{ fontWeight: 600, fontSize: '.88rem' }}>{f.cliente?.nombre}</div>
                  {f.cliente?.email && <div style={{ fontSize: '.75rem', color: '#94a3b8' }}>{f.cliente.email}</div>}
                </td>
                <td style={{ ...td, maxWidth: 220 }}>
                  <span style={{ fontSize: '.85rem' }}>{f.concepto}</span>
                </td>
                <td style={td}>
                  <span style={{
                    background: f.metodo_pago === 'MercadoPago' ? '#e0f2fe' : '#f1f5f9',
                    color: f.metodo_pago === 'MercadoPago' ? '#0284c7' : '#475569',
                    padding: '2px 9px', borderRadius: 12, fontSize: '.75rem'
                  }}>{f.metodo_pago}</span>
                </td>
                <td style={{ ...td, fontWeight: 700, color: '#10b981', whiteSpace: 'nowrap' }}>
                  ${Number(f.monto).toLocaleString('es-CO')}
                </td>
                <td style={{ ...td, whiteSpace: 'nowrap' }}>
                  <div style={{ display: 'flex', gap: 4 }}>
                    <button onClick={() => descargarPDF(f)} title="Descargar PDF"
                      style={btn('#ede9fe', '#7c3aed')}>
                      <Download size={13} />
                    </button>
                    <button onClick={() => enviarEmail(f)} title="Enviar por email" disabled={enviando === f.id}
                      style={btn('#e0f2fe', '#0284c7')}>
                      {enviando === f.id ? '...' : <Send size={13} />}
                    </button>
                    <button onClick={() => eliminar(f.id)} title="Eliminar"
                      style={btn('#fef2f2', '#ef4444')}>
                      <Trash2 size={13} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
            {facturas.length === 0 && (
              <tr><td colSpan={7} style={{ padding: 40, textAlign: 'center', color: '#94a3b8', fontSize: '.9rem' }}>
                No hay facturas para los filtros seleccionados.
              </td></tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

const td  = { padding: '12px 14px', fontSize: '.88rem' };
const sel = { padding: '6px 10px', borderRadius: 7, border: '1px solid #e2e8f0', fontSize: '.83rem', background: '#fafafa', color: '#374151' };
const btn = (bg, color) => ({ background: bg, color, border: 'none', borderRadius: 6, padding: '5px 8px', cursor: 'pointer', display: 'flex', alignItems: 'center' });
