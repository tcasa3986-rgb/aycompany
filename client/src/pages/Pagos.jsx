import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, X, Trash2 } from 'lucide-react';

const VACIO = { licencia_id: '', cliente_id: '', monto: '', fecha_pago: new Date().toISOString().split('T')[0], metodo_pago: 'efectivo', meses: '1', notas: '' };

export default function Pagos() {
  const [pagos,         setPagos]         = useState([]);
  const [licencias,     setLicencias]     = useState([]);
  const [modal,         setModal]         = useState(false);
  const [form,          setForm]          = useState(VACIO);
  const [precioBase,    setPrecioBase]    = useState(0); // precio mensual de la licencia seleccionada
  const [vencimientoActual, setVencimientoActual] = useState(null);

  const cargar = () => api.get('/pagos').then(r => setPagos(r.data.data));
  useEffect(() => {
    cargar();
    api.get('/licencias').then(r => setLicencias(r.data.data));
  }, []);

  function seleccionarLicencia(licId) {
    const lic = licencias.find(l => String(l.id) === String(licId));
    const precio = Number(lic?.producto?.precio_mensual || 0);
    const meses  = parseInt(form.meses) || 1;
    setPrecioBase(precio);
    setVencimientoActual(lic?.fecha_vencimiento || null);
    setForm({ ...form, licencia_id: licId, cliente_id: lic?.cliente_id || '', monto: String(precio * meses) });
  }

  function cambiarMeses(nuevosMeses) {
    const meses = parseInt(nuevosMeses) || 1;
    setForm(f => ({ ...f, meses: String(meses), monto: precioBase > 0 ? String(precioBase * meses) : f.monto }));
  }

  function calcularNuevoVencimiento() {
    if (!vencimientoActual || !form.meses) return null;
    const base = new Date(vencimientoActual) > new Date() ? new Date(vencimientoActual) : new Date();
    base.setMonth(base.getMonth() + parseInt(form.meses));
    return base.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' });
  }

  function abrirModal() {
    setForm(VACIO);
    setPrecioBase(0);
    setVencimientoActual(null);
    setModal(true);
  }

  async function crear(e) {
    e.preventDefault();
    try {
      const r = await api.post('/pagos', form);
      toast.success(r.data.msg);
      setModal(false);
      setForm(VACIO);
      setPrecioBase(0);
      setVencimientoActual(null);
      cargar();
    } catch { toast.error('Error al registrar'); }
  }

  async function eliminar(id) {
    if (!confirm('¿Eliminar este pago?')) return;
    await api.delete(`/pagos/${id}`);
    toast.success('Pago eliminado');
    cargar();
  }

  const totalMes = pagos
    .filter(p => {
      const d = new Date(p.fecha_pago);
      const hoy = new Date();
      return d.getMonth() === hoy.getMonth() && d.getFullYear() === hoy.getFullYear();
    })
    .reduce((acc, p) => acc + Number(p.monto), 0);

  return (
    <div style={{ padding: 32 }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
        <div>
          <h1 style={{ fontSize: '1.4rem', fontWeight: 700 }}>Pagos</h1>
          <p style={{ color: '#64748b', fontSize: '.88rem', marginTop: 2 }}>Ingresos este mes: <strong style={{ color: '#10b981' }}>${totalMes.toLocaleString('es')}</strong></p>
        </div>
        <button onClick={abrirModal} style={btn('#4f46e5')}><Plus size={16} /> Registrar pago</button>
      </div>

      <div style={{ background: '#fff', borderRadius: 12, boxShadow: '0 1px 4px rgba(0,0,0,.07)', overflow: 'hidden' }}>
        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
          <thead>
            <tr style={{ background: '#f8fafc' }}>
              {['Fecha', 'Cliente', 'Producto', 'Meses', 'Método', 'Monto', ''].map(h => (
                <th key={h} style={{ padding: '10px 16px', textAlign: 'left', fontSize: '.8rem', color: '#64748b', fontWeight: 600 }}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {pagos.map(p => (
              <tr key={p.id} style={{ borderTop: '1px solid #f1f5f9' }}>
                <td style={td}>{new Date(p.fecha_pago).toLocaleDateString('es')}</td>
                <td style={td}><strong>{p.cliente?.nombre}</strong></td>
                <td style={td}>{p.licencia?.producto?.nombre || '—'}</td>
                <td style={td}>{p.meses}</td>
                <td style={td}>
                  <span style={{ background: '#f1f5f9', padding: '2px 9px', borderRadius: 12, fontSize: '.78rem', textTransform: 'capitalize' }}>{p.metodo_pago}</span>
                </td>
                <td style={{ ...td, fontWeight: 700, color: '#10b981' }}>${Number(p.monto).toLocaleString('es')}</td>
                <td style={td}>
                  <button onClick={() => eliminar(p.id)} style={{ background: '#fef2f2', color: '#ef4444', border: 'none', borderRadius: 6, padding: '5px 8px' }}><Trash2 size={13} /></button>
                </td>
              </tr>
            ))}
            {pagos.length === 0 && <tr><td colSpan={7} style={{ padding: 24, textAlign: 'center', color: '#94a3b8' }}>No hay pagos registrados</td></tr>}
          </tbody>
        </table>
      </div>

      {modal && (
        <div style={overlay}>
          <div style={modalBox}>
            <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 20 }}>
              <h2 style={{ fontSize: '1.1rem', fontWeight: 700 }}>Registrar pago</h2>
              <button onClick={() => { setModal(false); setPrecioBase(0); setVencimientoActual(null); }} style={{ background: 'none', border: 'none' }}><X size={20} /></button>
            </div>
            <form onSubmit={crear}>
              <Field label="Licencia *">
                <select value={form.licencia_id} onChange={e => seleccionarLicencia(e.target.value)} required>
                  <option value="">Seleccionar...</option>
                  {licencias.map(l => (
                    <option key={l.id} value={l.id}>
                      {l.cliente?.nombre} — {l.producto?.nombre}
                    </option>
                  ))}
                </select>
              </Field>
              {/* Selector de meses */}
              <Field label="Meses a renovar">
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(6,1fr)', gap: 6 }}>
                  {[1,2,3,6,12,24].map(m => (
                    <button key={m} type="button" onClick={() => cambiarMeses(m)}
                      style={{
                        padding: '8px 4px', borderRadius: 8, border: '2px solid',
                        borderColor: parseInt(form.meses) === m ? '#4f46e5' : '#e2e8f0',
                        background:  parseInt(form.meses) === m ? '#ede9fe' : '#fafafa',
                        color:       parseInt(form.meses) === m ? '#4f46e5' : '#374151',
                        fontWeight:  parseInt(form.meses) === m ? 700 : 500,
                        fontSize: '.82rem', cursor: 'pointer'
                      }}>
                      {m} {m === 1 ? 'mes' : 'meses'}
                    </button>
                  ))}
                </div>
              </Field>

              {/* Preview: monto calculado + nuevo vencimiento */}
              {precioBase > 0 && (
                <div style={{ background: '#f0fdf4', border: '1px solid #bbf7d0', borderRadius: 10, padding: '12px 16px', marginBottom: 14, display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: 8 }}>
                  <div>
                    <div style={{ fontSize: '.75rem', color: '#64748b', fontWeight: 600 }}>MONTO A COBRAR</div>
                    <div style={{ fontSize: '1.3rem', fontWeight: 800, color: '#059669' }}>
                      ${(precioBase * parseInt(form.meses)).toLocaleString('es-CO')} COP
                    </div>
                    <div style={{ fontSize: '.75rem', color: '#94a3b8' }}>
                      ${precioBase.toLocaleString('es-CO')} × {form.meses} mes{parseInt(form.meses) > 1 ? 'es' : ''}
                    </div>
                  </div>
                  {calcularNuevoVencimiento() && (
                    <div style={{ textAlign: 'right' }}>
                      <div style={{ fontSize: '.75rem', color: '#64748b', fontWeight: 600 }}>NUEVA FECHA VENCIMIENTO</div>
                      <div style={{ fontSize: '.92rem', fontWeight: 700, color: '#1e1b4b' }}>{calcularNuevoVencimiento()}</div>
                    </div>
                  )}
                </div>
              )}

              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                <Field label="Monto ($) — editable">
                  <input type="number" min="0" step="1000" value={form.monto}
                    onChange={e => setForm({...form, monto: e.target.value})} required />
                </Field>
                <Field label="Fecha de pago">
                  <input type="date" value={form.fecha_pago} onChange={e => setForm({...form, fecha_pago: e.target.value})} required />
                </Field>
                <Field label="Método de pago">
                  <select value={form.metodo_pago} onChange={e => setForm({...form, metodo_pago: e.target.value})}>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="nequi">Nequi</option>
                    <option value="daviplata">Daviplata</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="otro">Otro</option>
                  </select>
                </Field>
              </div>
              <Field label="Notas"><textarea rows={2} value={form.notas} onChange={e => setForm({...form, notas: e.target.value})} style={{ resize: 'none' }} /></Field>
              <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 20 }}>
                <button type="button" onClick={() => setModal(false)} style={btn('#94a3b8')}>Cancelar</button>
                <button type="submit" style={btn('#10b981')}>Registrar pago</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

const td = { padding: '12px 16px', fontSize: '.9rem' };
const overlay  = { position: 'fixed', inset: 0, background: 'rgba(0,0,0,.45)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50 };
const modalBox = { background: '#fff', borderRadius: 14, padding: 28, width: 520 };
const btn = bg => ({ display: 'inline-flex', alignItems: 'center', gap: 6, padding: '9px 16px', background: bg, color: '#fff', border: 'none', borderRadius: 8, fontSize: '.88rem', fontWeight: 600 });
function Field({ label, children }) { return <div style={{ marginBottom: 14 }}><label style={{ display: 'block', fontSize: '.82rem', fontWeight: 600, color: '#374151', marginBottom: 5 }}>{label}</label>{children}</div>; }
