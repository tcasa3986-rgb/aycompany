import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, X, Pencil, CheckCircle2, Trash2, Send, Bell } from 'lucide-react';

// Todo lo que hay que pagar cada mes en una fecha: el Railway de cada sistema y
// los dominios del negocio, pero también el arriendo, los servicios o el gimnasio.
// El aviso diario sale de aquí, por Telegram, sin usar IA ni consumir tokens.
const ESTADOS = {
  al_dia:   { label: 'Al día',     bg: '#f0fdf4', color: '#16a34a' },
  proximo:  { label: 'Próximo',    bg: '#fffbeb', color: '#d97706' },
  hoy:      { label: 'Pagar hoy',  bg: '#fff7ed', color: '#ea580c' },
  atrasado: { label: 'Atrasado',   bg: '#fef2f2', color: '#dc2626' },
  inactivo: { label: 'Inactivo',   bg: '#f1f5f9', color: '#64748b' },
};
const fmtMonto = (m, mon) => (mon === 'USD' ? 'US$' : '$') + Number(m || 0).toLocaleString('es-CO');
const fmtFecha = s => s ? new Date(s + 'T12:00:00').toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
const CATEGORIAS = {
  empresa:  ['hosting', 'dominio', 'apis_ia', 'herramientas', 'publicidad', 'otro'],
  personal: ['arriendo', 'servicios', 'celular', 'gimnasio', 'transporte', 'salud', 'educacion', 'suscripcion', 'otro'],
};
const VACIO = { nombre: '', proveedor: '', ambito: 'empresa', categoria: 'hosting', licencia_id: '',
                monto: '', moneda: 'USD', dia_pago: '', proximo_pago: '', cuenta: '', notas: '', activo: true };

export default function CostosServidor() {
  const [costos, setCostos]       = useState([]);
  const [licencias, setLicencias] = useState([]);
  const [modal, setModal]         = useState(null);   // 'nuevo' | costo
  const [form, setForm]           = useState(VACIO);
  const [preview, setPreview]     = useState(null);

  const cargar = () => api.get('/costos').then(r => setCostos(r.data.data));
  useEffect(() => {
    cargar();
    api.get('/licencias').then(r => setLicencias(r.data.data));
  }, []);

  const totalUSD = costos.filter(c => c.activo && c.moneda === 'USD').reduce((s, c) => s + Number(c.monto), 0);
  const totalCOP = costos.filter(c => c.activo && c.moneda === 'COP').reduce((s, c) => s + Number(c.monto), 0);
  const atrasados = costos.filter(c => c.estado === 'atrasado').length;

  function abrirNuevo() { setForm(VACIO); setModal('nuevo'); }
  function abrirEditar(c) {
    setForm({ nombre: c.nombre, proveedor: c.proveedor || '', ambito: c.ambito || 'empresa',
              categoria: c.categoria || 'hosting', licencia_id: c.licencia_id || '', monto: c.monto,
              moneda: c.moneda, dia_pago: c.dia_pago, proximo_pago: c.proximo_pago || '',
              cuenta: c.cuenta || '', notas: c.notas || '', activo: c.activo });
    setModal(c);
  }

  async function guardar(e) {
    e.preventDefault();
    try {
      if (modal === 'nuevo') { await api.post('/costos', form); toast.success('Costo creado'); }
      else { await api.put(`/costos/${modal.id}`, form); toast.success('Costo actualizado'); }
      setModal(null);
      cargar();
    } catch (err) { toast.error(err.response?.data?.msg || 'Error al guardar'); }
  }

  async function pagado(c) {
    try {
      const r = await api.post(`/costos/${c.id}/pagado`, {});
      toast.success(r.data.msg);
      cargar();
    } catch (err) { toast.error(err.response?.data?.msg || 'Error'); }
  }

  async function eliminar(c) {
    if (!confirm(`¿Eliminar "${c.nombre}"?`)) return;
    await api.delete(`/costos/${c.id}`);
    toast.success('Eliminado');
    cargar();
  }

  async function verPreview() {
    const r = await api.get('/costos/alertas/preview');
    setPreview(r.data.msg);
  }
  async function enviarAhora() {
    const r = await api.post('/costos/alertas/enviar');
    if (r.data.enviado) toast.success('Aviso enviado a Telegram'); else toast.error(r.data.error || 'No se envió (¿bot de Telegram configurado?)');
  }

  const nombreLic = id => {
    const l = licencias.find(x => x.id === id);
    return l ? `${l.cliente?.nombre} · ${l.producto?.nombre}` : '—';
  };

  return (
    <div style={{ padding: 32 }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20, flexWrap: 'wrap', gap: 10 }}>
        <div>
          <h1 style={{ fontSize: '1.4rem', fontWeight: 700 }}>Qué tengo que pagar</h1>
          <p style={{ fontSize: '.85rem', color: '#64748b', marginTop: 4 }}>
            Al mes: <strong style={{ color: '#0f172a' }}>{fmtMonto(totalUSD, 'USD')}</strong>{totalCOP > 0 && <> + <strong style={{ color: '#0f172a' }}>{fmtMonto(totalCOP, 'COP')}</strong></>}
            {atrasados > 0 && <> · <span style={{ color: '#dc2626', fontWeight: 600 }}>{atrasados} atrasado{atrasados > 1 ? 's' : ''}</span></>}
            <span style={{ marginLeft: 8, color: '#94a3b8' }}>Te aviso por Telegram todos los días a las 8:00 am: 3 días antes, el día, y si ya se pasó.</span>
          </p>
        </div>
        <div style={{ display: 'flex', gap: 8 }}>
          <button onClick={verPreview} style={btn('#64748b')}><Bell size={15} /> Ver aviso de hoy</button>
          <button onClick={enviarAhora} style={btn('#0ea5e9')}><Send size={15} /> Enviar ahora</button>
          <button onClick={abrirNuevo} style={btn('#4f46e5')}><Plus size={16} /> Nuevo costo</button>
        </div>
      </div>

      {preview && (
        <div style={{ background: '#0f172a', color: '#e2e8f0', borderRadius: 12, padding: 18, marginBottom: 20, whiteSpace: 'pre-wrap', fontSize: '.85rem', position: 'relative', fontFamily: 'monospace' }}>
          <button onClick={() => setPreview(null)} style={{ position: 'absolute', top: 10, right: 10, background: 'none', border: 'none', color: '#94a3b8' }}><X size={16} /></button>
          {preview}
        </div>
      )}

      <div style={{ background: '#fff', borderRadius: 12, boxShadow: '0 1px 4px rgba(0,0,0,.07)', overflowX: 'auto' }}>
        <table style={{ width: '100%', borderCollapse: 'collapse', minWidth: 820 }}>
          <thead>
            <tr style={{ background: '#f8fafc' }}>
              {['Pago', 'De quién', 'Monto', 'Día', 'Próximo', 'Último', 'Estado', 'Acciones'].map(h => (
                <th key={h} style={{ padding: '10px 16px', textAlign: 'left', fontSize: '.8rem', color: '#64748b', fontWeight: 600 }}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {costos.map(c => {
              const b = ESTADOS[c.estado] || ESTADOS.al_dia;
              return (
                <tr key={c.id} style={{ borderTop: '1px solid #f1f5f9', opacity: c.activo ? 1 : .55 }}>
                  <td style={td}><strong>{c.nombre}</strong><div style={{ fontSize: '.75rem', color: '#94a3b8' }}>{c.proveedor}{c.cuenta ? ` · ${c.cuenta}` : ''}</div></td>
                  <td style={td}>
                    <span style={{ background: c.ambito === 'personal' ? '#F5F3FF' : '#EFF6FF',
                                   color: c.ambito === 'personal' ? '#7C3AED' : '#2563EB',
                                   padding: '2px 9px', borderRadius: 20, fontSize: '.72rem', fontWeight: 700 }}>
                      {c.ambito === 'personal' ? 'Mío' : 'Negocio'}
                    </span>
                    <div style={{ fontSize: '.73rem', color: '#94a3b8', marginTop: 3 }}>
                      {c.licencia ? c.licencia.cliente?.nombre : String(c.categoria || '').replace(/_/g, ' ')}
                    </div>
                  </td>
                  <td style={td}>{fmtMonto(c.monto, c.moneda)}</td>
                  <td style={td}>Día {c.dia_pago}</td>
                  <td style={td}>{fmtFecha(c.proximo_pago)}<div style={{ fontSize: '.72rem', color: '#94a3b8' }}>{c.dias_para_pago < 0 ? `hace ${-c.dias_para_pago} días` : c.dias_para_pago === 0 ? 'hoy' : `en ${c.dias_para_pago} días`}</div></td>
                  <td style={td}>{fmtFecha(c.ultimo_pago)}</td>
                  <td style={td}><span style={{ background: b.bg, color: b.color, padding: '3px 10px', borderRadius: 20, fontSize: '.78rem', fontWeight: 600 }}>{b.label}</span></td>
                  <td style={{ ...td, whiteSpace: 'nowrap' }}>
                    <button onClick={() => pagado(c)} title="Ya lo pagué" style={btnSm('#10b981')}><CheckCircle2 size={15} /></button>
                    <button onClick={() => abrirEditar(c)} title="Editar" style={btnSm('#4f46e5')}><Pencil size={14} /></button>
                    <button onClick={() => eliminar(c)} title="Eliminar" style={btnSm('#ef4444')}><Trash2 size={14} /></button>
                  </td>
                </tr>
              );
            })}
            {costos.length === 0 && <tr><td colSpan={8} style={{ padding: 24, textAlign: 'center', color: '#94a3b8' }}>Sin costos registrados. Agrega el Railway de cada sistema para que te avise cuándo pagar.</td></tr>}
          </tbody>
        </table>
      </div>

      {modal && (
        <div style={overlay}>
          <div style={modalBox}>
            <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 20 }}>
              <h2 style={{ fontSize: '1.1rem', fontWeight: 700 }}>{modal === 'nuevo' ? 'Nuevo costo' : 'Editar costo'}</h2>
              <button onClick={() => setModal(null)} style={{ background: 'none', border: 'none' }}><X size={20} /></button>
            </div>
            <form onSubmit={guardar}>
              <div style={grid2}>
                <Field label="Nombre *"><input value={form.nombre} onChange={e => setForm({ ...form, nombre: e.target.value })} placeholder="Railway — ASOERC, Arriendo…" required style={input} /></Field>
                <Field label="Proveedor"><input value={form.proveedor} onChange={e => setForm({ ...form, proveedor: e.target.value })} placeholder="Railway, Hostinger, arrendador…" style={input} /></Field>
              </div>
              <div style={grid2}>
                <Field label="¿De quién es este pago?">
                  <select value={form.ambito} onChange={e => setForm({ ...form, ambito: e.target.value, categoria: CATEGORIAS[e.target.value][0] })} style={input}>
                    <option value="empresa">Del negocio</option>
                    <option value="personal">Mío (personal)</option>
                  </select>
                </Field>
                <Field label="Categoría">
                  <select value={form.categoria} onChange={e => setForm({ ...form, categoria: e.target.value })} style={input}>
                    {(CATEGORIAS[form.ambito] || ['otro']).map(c => <option key={c} value={c}>{c.replace(/_/g, ' ')}</option>)}
                  </select>
                </Field>
              </div>
              <Field label="Sistema al que pertenece" hint="Solo si es de un cliente: sirve para saber cuánto te deja cada uno.">
                <select value={form.licencia_id} onChange={e => setForm({ ...form, licencia_id: e.target.value })}>
                  <option value="">General (no es de un cliente)</option>
                  {licencias.map(l => <option key={l.id} value={l.id}>{nombreLic(l.id)}</option>)}
                </select>
              </Field>
              <div style={grid2}>
                <Field label="Monto *"><input type="number" min="0" step="0.01" value={form.monto} onChange={e => setForm({ ...form, monto: e.target.value })} required /></Field>
                <Field label="Moneda">
                  <select value={form.moneda} onChange={e => setForm({ ...form, moneda: e.target.value })}><option value="USD">USD</option><option value="COP">COP</option></select>
                </Field>
              </div>
              <div style={grid2}>
                <Field label="Día de pago (1-31) *"><input type="number" min="1" max="31" value={form.dia_pago} onChange={e => setForm({ ...form, dia_pago: e.target.value })} required /></Field>
                <Field label="Próximo pago" hint="Vacío = se calcula con el día de pago."><input type="date" value={form.proximo_pago} onChange={e => setForm({ ...form, proximo_pago: e.target.value })} /></Field>
              </div>
              <div style={grid2}>
                <Field label="Con qué se paga"><input value={form.cuenta} onChange={e => setForm({ ...form, cuenta: e.target.value })} placeholder="Visa Bancolombia, Nequi, efectivo…" style={input} /></Field>
                <Field label="Notas"><input value={form.notas} onChange={e => setForm({ ...form, notas: e.target.value })} /></Field>
              </div>
              {modal !== 'nuevo' && (
                <label style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: '.88rem', marginBottom: 14, cursor: 'pointer' }}>
                  <input type="checkbox" checked={form.activo} onChange={e => setForm({ ...form, activo: e.target.checked })} /> Activo (genera avisos)
                </label>
              )}
              <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 10 }}>
                <button type="button" onClick={() => setModal(null)} style={btn('#94a3b8')}>Cancelar</button>
                <button type="submit" style={btn('#4f46e5')}>{modal === 'nuevo' ? 'Crear' : 'Guardar'}</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

const td = { padding: '12px 16px', fontSize: '.9rem', verticalAlign: 'top' };
const grid2 = { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 14 };
const overlay  = { position: 'fixed', inset: 0, background: 'rgba(0,0,0,.45)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50 };
const modalBox = { background: '#fff', borderRadius: 14, padding: 28, width: 540, maxWidth: '95vw', maxHeight: '92vh', overflowY: 'auto' };
const btn   = bg => ({ display: 'inline-flex', alignItems: 'center', gap: 6, padding: '9px 16px', background: bg, color: '#fff', border: 'none', borderRadius: 8, fontSize: '.88rem', fontWeight: 600, cursor: 'pointer' });
const btnSm = bg => ({ padding: '6px 9px', background: bg + '18', color: bg, border: 'none', borderRadius: 6, marginRight: 5, cursor: 'pointer' });
function Field({ label, hint, children }) {
  return (
    <div style={{ marginBottom: 14 }}>
      <label style={{ display: 'block', fontSize: '.82rem', fontWeight: 600, color: '#374151', marginBottom: 5 }}>{label}</label>
      {children}
      {hint && <div style={{ fontSize: '.72rem', color: '#94a3b8', marginTop: 4 }}>{hint}</div>}
    </div>
  );
}
