import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, X, Copy, ToggleLeft, ToggleRight, RefreshCw } from 'lucide-react';

export default function Licencias() {
  const [licencias, setLicencias] = useState([]);
  const [clientes,  setClientes]  = useState([]);
  const [productos, setProductos] = useState([]);
  const [modal,     setModal]     = useState(false);
  const [modalRenew, setModalRenew] = useState(null);
  const [form, setForm] = useState({ cliente_id: '', producto_id: '', meses: '1' });
  const [mesesRenew, setMesesRenew] = useState('1');
  const [filtro,   setFiltro]   = useState('todas');
  const [negocio,  setNegocio]  = useState('');

  const cargar = () => api.get('/licencias').then(r => setLicencias(r.data.data));
  useEffect(() => {
    cargar();
    api.get('/clientes').then(r => setClientes(r.data.data));
    api.get('/productos').then(r => setProductos(r.data.data));
  }, []);

  const hoy = new Date();
  const filtradas = licencias.filter(l => {
    const vencida = new Date(l.fecha_vencimiento) < hoy;
    if (filtro === 'activas')    return l.activo && !vencida;
    if (filtro === 'vencidas')   return vencida || !l.activo;
    if (filtro === 'por_vencer') {
      const dias = Math.ceil((new Date(l.fecha_vencimiento) - hoy) / 86400000);
      return l.activo && dias >= 0 && dias <= 7;
    }
    return true;
  }).filter(l => negocio === '' || String(l.producto_id) === negocio);

  async function crear(e) {
    e.preventDefault();
    try {
      await api.post('/licencias', form);
      toast.success('Licencia creada');
      setModal(false);
      cargar();
    } catch { toast.error('Error al crear'); }
  }

  async function toggle(id) {
    const r = await api.put(`/licencias/${id}/toggle`);
    toast.success(r.data.msg);
    cargar();
  }

  async function renovar() {
    await api.put(`/licencias/${modalRenew}/renovar`, { meses: mesesRenew });
    toast.success('Licencia renovada');
    setModalRenew(null);
    cargar();
  }

  function copiar(key) {
    navigator.clipboard.writeText(key);
    toast.success('Clave copiada');
  }

  function badge(l) {
    const vencida = new Date(l.fecha_vencimiento) < hoy;
    const dias = Math.ceil((new Date(l.fecha_vencimiento) - hoy) / 86400000);
    if (!l.activo)      return { label: 'Inactiva',  bg: '#fef2f2', color: '#dc2626' };
    if (vencida)        return { label: 'Vencida',   bg: '#fef2f2', color: '#dc2626' };
    if (dias <= 7)      return { label: `${dias}d`,  bg: '#fffbeb', color: '#d97706' };
    return               { label: 'Activa',    bg: '#f0fdf4', color: '#16a34a' };
  }

  return (
    <div style={{ padding: 32 }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
        <h1 style={{ fontSize: '1.4rem', fontWeight: 700 }}>Licencias</h1>
        <button onClick={() => setModal(true)} style={btn('#4f46e5')}><Plus size={16} /> Nueva licencia</button>
      </div>

      <div style={{ display: 'flex', gap: 8, marginBottom: 20, flexWrap: 'wrap', alignItems: 'center' }}>
        {[['todas', 'Todas'], ['activas', 'Activas'], ['por_vencer', 'Por vencer'], ['vencidas', 'Vencidas/Inactivas']].map(([v, l]) => (
          <button key={v} onClick={() => setFiltro(v)} style={{ padding: '6px 14px', borderRadius: 20, border: 'none', fontWeight: 600, fontSize: '.82rem', background: filtro === v ? '#4f46e5' : '#e2e8f0', color: filtro === v ? '#fff' : '#64748b' }}>{l}</button>
        ))}
        <div style={{ marginLeft: 'auto' }}>
          <select value={negocio} onChange={e => setNegocio(e.target.value)}
            style={{ padding: '6px 14px', borderRadius: 8, border: '1px solid #e2e8f0', fontSize: '.85rem', color: '#374151', background: '#fff' }}>
            <option value="">Todos los negocios</option>
            {productos.map(p => <option key={p.id} value={p.id}>{p.nombre}</option>)}
          </select>
        </div>
      </div>

      <div style={{ background: '#fff', borderRadius: 12, boxShadow: '0 1px 4px rgba(0,0,0,.07)', overflow: 'hidden' }}>
        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
          <thead>
            <tr style={{ background: '#f8fafc' }}>
              {['Cliente', 'Producto', 'Clave', 'Vencimiento', 'Estado', 'Acciones'].map(h => (
                <th key={h} style={{ padding: '10px 16px', textAlign: 'left', fontSize: '.8rem', color: '#64748b', fontWeight: 600 }}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {filtradas.map(l => {
              const b = badge(l);
              return (
                <tr key={l.id} style={{ borderTop: '1px solid #f1f5f9' }}>
                  <td style={td}><strong>{l.cliente?.nombre}</strong><div style={{ fontSize: '.75rem', color: '#94a3b8' }}>{l.cliente?.telefono}</div></td>
                  <td style={td}>{l.producto?.nombre}</td>
                  <td style={td}>
                    <span style={{ fontFamily: 'monospace', fontSize: '.78rem', background: '#f8fafc', padding: '3px 8px', borderRadius: 5 }}>{l.license_key.substring(0, 18)}…</span>
                    <button onClick={() => copiar(l.license_key)} style={{ background: 'none', border: 'none', marginLeft: 4, color: '#94a3b8' }}><Copy size={13} /></button>
                  </td>
                  <td style={td}>{new Date(l.fecha_vencimiento).toLocaleDateString('es')}</td>
                  <td style={td}><span style={{ background: b.bg, color: b.color, padding: '3px 10px', borderRadius: 20, fontSize: '.78rem', fontWeight: 600 }}>{b.label}</span></td>
                  <td style={td}>
                    <button onClick={() => toggle(l.id)} title={l.activo ? 'Desactivar' : 'Activar'} style={btnSm(l.activo ? '#ef4444' : '#10b981')}>
                      {l.activo ? <ToggleRight size={15} /> : <ToggleLeft size={15} />}
                    </button>
                    <button onClick={() => setModalRenew(l.id)} title="Renovar" style={btnSm('#f59e0b')}><RefreshCw size={14} /></button>
                  </td>
                </tr>
              );
            })}
            {filtradas.length === 0 && <tr><td colSpan={6} style={{ padding: 24, textAlign: 'center', color: '#94a3b8' }}>No hay licencias</td></tr>}
          </tbody>
        </table>
      </div>

      {modal && (
        <div style={overlay}>
          <div style={modalBox}>
            <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 20 }}>
              <h2 style={{ fontSize: '1.1rem', fontWeight: 700 }}>Nueva licencia</h2>
              <button onClick={() => setModal(false)} style={{ background: 'none', border: 'none' }}><X size={20} /></button>
            </div>
            <form onSubmit={crear}>
              <Field label="Cliente *">
                <select value={form.cliente_id} onChange={e => setForm({...form, cliente_id: e.target.value})} required>
                  <option value="">Seleccionar...</option>
                  {clientes.map(c => <option key={c.id} value={c.id}>{c.nombre}</option>)}
                </select>
              </Field>
              <Field label="Producto *">
                <select value={form.producto_id} onChange={e => setForm({...form, producto_id: e.target.value})} required>
                  <option value="">Seleccionar...</option>
                  {productos.map(p => <option key={p.id} value={p.id}>{p.nombre} — ${Number(p.precio_mensual).toLocaleString('es')}/mes</option>)}
                </select>
              </Field>
              <Field label="Duración">
                <select value={form.meses} onChange={e => setForm({...form, meses: e.target.value})}>
                  {[1,3,6,12].map(m => <option key={m} value={m}>{m} mes{m > 1 ? 'es' : ''}</option>)}
                </select>
              </Field>
              <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 20 }}>
                <button type="button" onClick={() => setModal(false)} style={btn('#94a3b8')}>Cancelar</button>
                <button type="submit" style={btn('#4f46e5')}>Crear licencia</button>
              </div>
            </form>
          </div>
        </div>
      )}

      {modalRenew && (
        <div style={overlay}>
          <div style={{ ...modalBox, width: 360 }}>
            <h2 style={{ fontSize: '1.1rem', fontWeight: 700, marginBottom: 16 }}>Renovar licencia</h2>
            <Field label="Agregar meses">
              <select value={mesesRenew} onChange={e => setMesesRenew(e.target.value)}>
                {[1,3,6,12].map(m => <option key={m} value={m}>{m} mes{m > 1 ? 'es' : ''}</option>)}
              </select>
            </Field>
            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 20 }}>
              <button onClick={() => setModalRenew(null)} style={btn('#94a3b8')}>Cancelar</button>
              <button onClick={renovar} style={btn('#10b981')}>Renovar</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

const td = { padding: '12px 16px', fontSize: '.9rem' };
const overlay  = { position: 'fixed', inset: 0, background: 'rgba(0,0,0,.45)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50 };
const modalBox = { background: '#fff', borderRadius: 14, padding: 28, width: 480 };
const btn   = bg => ({ display: 'inline-flex', alignItems: 'center', gap: 6, padding: '9px 16px', background: bg, color: '#fff', border: 'none', borderRadius: 8, fontSize: '.88rem', fontWeight: 600 });
const btnSm = bg => ({ padding: '6px 9px', background: bg + '18', color: bg, border: 'none', borderRadius: 6, marginRight: 5 });
function Field({ label, children }) { return <div style={{ marginBottom: 14 }}><label style={{ display: 'block', fontSize: '.82rem', fontWeight: 600, color: '#374151', marginBottom: 5 }}>{label}</label>{children}</div>; }
