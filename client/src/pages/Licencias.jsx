import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, X, Copy, ToggleLeft, ToggleRight, RefreshCw, Pencil, Banknote, ExternalLink } from 'lucide-react';

// Etiquetas del estado calculado por el servidor (utils/licenciaCiclo)
const ESTADOS = {
  al_dia:           { label: 'Al día',          bg: '#f0fdf4', color: '#16a34a' },
  por_vencer:       { label: 'Por vencer',      bg: '#fffbeb', color: '#d97706' },
  en_gracia:        { label: 'En mora',         bg: '#fff7ed', color: '#ea580c' },
  mora_sin_bloqueo: { label: 'En mora',         bg: '#fff7ed', color: '#ea580c' },
  bloqueada:        { label: 'Bloqueada',       bg: '#fef2f2', color: '#dc2626' },
  desactivada:      { label: 'Desactivada',     bg: '#f1f5f9', color: '#64748b' },
};
const METODOS = [['nequi', 'Nequi'], ['transferencia', 'Transferencia'], ['efectivo', 'Efectivo'], ['tarjeta', 'Tarjeta'], ['otro', 'Otro']];
const fmt = n => '$' + Number(n || 0).toLocaleString('es-CO');
const fmtFecha = s => s ? new Date(s + 'T12:00:00').toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';

const FORM_VACIO = { cliente_id: '', producto_id: '', dia_corte: '', dias_gracia: '0', bloquear: true, precio_mensual: '', fecha_vencimiento: '', notas: '' };

export default function Licencias() {
  const [licencias, setLicencias] = useState([]);
  const [clientes,  setClientes]  = useState([]);
  const [productos, setProductos] = useState([]);
  const [modal,     setModal]     = useState(null);   // 'nueva' | licencia (editar)
  const [modalPago, setModalPago] = useState(null);   // licencia
  const [modalRenew, setModalRenew] = useState(null);
  const [form, setForm]   = useState(FORM_VACIO);
  const [pago, setPago]   = useState({ metodo_pago: 'nequi', meses: '1', monto: '', fecha_pago: '', notas: '' });
  const [mesesRenew, setMesesRenew] = useState('1');
  const [filtro,   setFiltro]   = useState('todas');
  const [negocio,  setNegocio]  = useState('');

  const cargar = () => api.get('/licencias').then(r => setLicencias(r.data.data));
  useEffect(() => {
    cargar();
    api.get('/clientes').then(r => setClientes(r.data.data));
    api.get('/productos').then(r => setProductos(r.data.data));
  }, []);

  const filtradas = licencias.filter(l => {
    const e = l.ciclo?.estado;
    if (filtro === 'activas')    return l.activo && (e === 'al_dia' || e === 'por_vencer');
    if (filtro === 'mora')       return e === 'en_gracia' || e === 'mora_sin_bloqueo' || e === 'bloqueada';
    if (filtro === 'por_vencer') return e === 'por_vencer';
    if (filtro === 'inactivas')  return !l.activo;
    return true;
  }).filter(l => negocio === '' || String(l.producto_id) === negocio);

  const resumen = {
    mrr: licencias.filter(l => l.activo).reduce((s, l) => s + Number(l.precio_efectivo || 0), 0),
    mora: licencias.filter(l => ['en_gracia', 'mora_sin_bloqueo', 'bloqueada'].includes(l.ciclo?.estado)).length,
    bloqueadas: licencias.filter(l => l.ciclo?.estado === 'bloqueada').length,
  };

  function abrirNueva() { setForm(FORM_VACIO); setModal('nueva'); }
  function abrirEditar(l) {
    setForm({
      cliente_id: l.cliente_id, producto_id: l.producto_id,
      dia_corte: l.dia_corte ?? '', dias_gracia: String(l.dias_gracia ?? 0), bloquear: l.bloquear !== false,
      precio_mensual: l.precio_mensual ?? '', fecha_vencimiento: l.fecha_vencimiento || '', notas: l.notas || '',
      // Se manda de vuelta para detectar si entró un pago mientras el formulario
      // estaba abierto; el servidor responde 409 en vez de pisar la renovación.
      vencimiento_visto: l.fecha_vencimiento || ''
    });
    setModal(l);
  }

  async function guardar(e) {
    e.preventDefault();
    try {
      if (modal === 'nueva') { await api.post('/licencias', form); toast.success('Licencia creada'); }
      else { await api.put(`/licencias/${modal.id}`, form); toast.success('Licencia actualizada'); }
      setModal(null);
      cargar();
    } catch (err) {
      const msg = err.response?.data?.msg || 'Error al guardar';
      toast.error(msg, { duration: err.response?.status === 409 ? 8000 : 4000 });
      if (err.response?.status === 409) { setModal(null); cargar(); }
    }
  }

  async function registrarPago(e) {
    e.preventDefault();
    try {
      const r = await api.post(`/licencias/${modalPago.id}/pago`, pago);
      toast.success(r.data.msg);
      setModalPago(null);
      setPago({ metodo_pago: 'nequi', meses: '1', monto: '', fecha_pago: '', notas: '' });
      cargar();
    } catch (err) { toast.error(err.response?.data?.msg || 'Error al registrar el pago'); }
  }

  async function toggle(l) {
    if (l.activo && !confirm(`¿Desactivar la licencia de ${l.cliente?.nombre}? El sistema del cliente dejará de funcionar.`)) return;
    const r = await api.put(`/licencias/${l.id}/toggle`);
    toast.success(r.data.msg);
    cargar();
  }

  async function renovar() {
    const r = await api.put(`/licencias/${modalRenew}/renovar`, { meses: mesesRenew });
    toast.success(`Renovada hasta ${fmtFecha(r.data.fecha_vencimiento)}`);
    setModalRenew(null);
    cargar();
  }

  function copiar(key) {
    navigator.clipboard.writeText(key);
    toast.success('Clave copiada');
  }

  function detalleCiclo(l) {
    const c = l.ciclo || {};
    if (!l.activo) return 'Desactivada a mano';
    if (c.estado === 'bloqueada') return `Bloqueada desde el ${fmtFecha(c.fecha_bloqueo)} · ${c.dias_mora} días de mora`;
    if (c.estado === 'en_gracia') return `${c.dias_mora} días de mora · se bloquea el ${fmtFecha(c.fecha_bloqueo)}`;
    if (c.estado === 'mora_sin_bloqueo') return `${c.dias_mora} días de mora · nunca se bloquea`;
    if (c.estado === 'por_vencer') return c.dias_restantes === 0 ? 'Vence hoy' : `Vence en ${c.dias_restantes} días`;
    return `Vence en ${c.dias_restantes} días`;
  }

  return (
    <div style={{ padding: 32 }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
        <div>
          <h1 style={{ fontSize: '1.4rem', fontWeight: 700 }}>Licencias</h1>
          <p style={{ fontSize: '.85rem', color: '#64748b', marginTop: 4 }}>
            Recurrente activo: <strong style={{ color: '#0f172a' }}>{fmt(resumen.mrr)}/mes</strong>
            {resumen.mora > 0 && <> · <span style={{ color: '#ea580c', fontWeight: 600 }}>{resumen.mora} en mora</span></>}
            {resumen.bloqueadas > 0 && <> · <span style={{ color: '#dc2626', fontWeight: 600 }}>{resumen.bloqueadas} bloqueada{resumen.bloqueadas > 1 ? 's' : ''}</span></>}
          </p>
        </div>
        <button onClick={abrirNueva} style={btn('#4f46e5')}><Plus size={16} /> Nueva licencia</button>
      </div>

      <div style={{ display: 'flex', gap: 8, marginBottom: 20, flexWrap: 'wrap', alignItems: 'center' }}>
        {[['todas', 'Todas'], ['activas', 'Al día'], ['por_vencer', 'Por vencer'], ['mora', 'En mora / bloqueadas'], ['inactivas', 'Desactivadas']].map(([v, l]) => (
          <button key={v} onClick={() => setFiltro(v)} style={{ padding: '6px 14px', borderRadius: 20, border: 'none', fontWeight: 600, fontSize: '.82rem', background: filtro === v ? '#4f46e5' : '#e2e8f0', color: filtro === v ? '#fff' : '#64748b' }}>{l}</button>
        ))}
        <div style={{ marginLeft: 'auto' }}>
          <select value={negocio} onChange={e => setNegocio(e.target.value)}
            style={{ padding: '6px 14px', borderRadius: 8, border: '1px solid #e2e8f0', fontSize: '.85rem', color: '#374151', background: '#fff' }}>
            <option value="">Todos los sistemas</option>
            {productos.map(p => <option key={p.id} value={p.id}>{p.nombre}</option>)}
          </select>
        </div>
      </div>

      <div style={{ background: '#fff', borderRadius: 12, boxShadow: '0 1px 4px rgba(0,0,0,.07)', overflowX: 'auto' }}>
        <table style={{ width: '100%', borderCollapse: 'collapse', minWidth: 900 }}>
          <thead>
            <tr style={{ background: '#f8fafc' }}>
              {['Cliente', 'Sistema', 'Clave', 'Mensualidad', 'Ciclo', 'Vencimiento', 'Estado', 'Acciones'].map(h => (
                <th key={h} style={{ padding: '10px 16px', textAlign: 'left', fontSize: '.8rem', color: '#64748b', fontWeight: 600 }}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {filtradas.map(l => {
              const b = ESTADOS[l.ciclo?.estado] || ESTADOS.desactivada;
              return (
                <tr key={l.id} style={{ borderTop: '1px solid #f1f5f9' }}>
                  <td style={td}><strong>{l.cliente?.nombre}</strong><div style={{ fontSize: '.75rem', color: '#94a3b8' }}>{l.cliente?.telefono}</div></td>
                  <td style={td}>{l.producto?.nombre}</td>
                  <td style={td}>
                    <span style={{ fontFamily: 'monospace', fontSize: '.78rem', background: '#f8fafc', padding: '3px 8px', borderRadius: 5 }}>{l.license_key.substring(0, 13)}…</span>
                    <button onClick={() => copiar(l.license_key)} title="Copiar clave" style={{ background: 'none', border: 'none', marginLeft: 4, color: '#94a3b8' }}><Copy size={13} /></button>
                    <a href={`/pagar/${l.license_key}`} target="_blank" rel="noreferrer" title="Abrir página de pago del cliente" style={{ color: '#94a3b8', marginLeft: 2 }}><ExternalLink size={13} /></a>
                  </td>
                  <td style={td}>{fmt(l.precio_efectivo)}{l.precio_mensual != null && <span title="Precio propio de esta licencia" style={{ color: '#94a3b8', fontSize: '.7rem' }}> *</span>}</td>
                  <td style={td}>
                    {l.dia_corte ? <>Día <strong>{l.dia_corte}</strong></> : <span style={{ color: '#94a3b8' }}>Sin corte</span>}
                    <div style={{ fontSize: '.72rem', color: '#94a3b8' }}>
                      {l.bloquear === false ? 'Nunca bloquea' : (Number(l.dias_gracia) > 0 ? `${l.dias_gracia} días de tolerancia` : 'Bloquea al vencer')}
                    </div>
                  </td>
                  <td style={td}>{fmtFecha(l.fecha_vencimiento)}</td>
                  <td style={td}>
                    <span style={{ background: b.bg, color: b.color, padding: '3px 10px', borderRadius: 20, fontSize: '.78rem', fontWeight: 600, whiteSpace: 'nowrap' }}>{b.label}</span>
                    <div style={{ fontSize: '.72rem', color: '#94a3b8', marginTop: 3 }}>{detalleCiclo(l)}</div>
                  </td>
                  <td style={{ ...td, whiteSpace: 'nowrap' }}>
                    <button onClick={() => { setModalPago(l); setPago(p => ({ ...p, monto: '' })); }} title="Registrar pago recibido" style={btnSm('#10b981')}><Banknote size={15} /></button>
                    <button onClick={() => abrirEditar(l)} title="Editar ciclo / precio" style={btnSm('#4f46e5')}><Pencil size={14} /></button>
                    <button onClick={() => setModalRenew(l.id)} title="Renovar sin pago (cortesía)" style={btnSm('#f59e0b')}><RefreshCw size={14} /></button>
                    <button onClick={() => toggle(l)} title={l.activo ? 'Desactivar' : 'Activar'} style={btnSm(l.activo ? '#ef4444' : '#10b981')}>
                      {l.activo ? <ToggleRight size={15} /> : <ToggleLeft size={15} />}
                    </button>
                  </td>
                </tr>
              );
            })}
            {filtradas.length === 0 && <tr><td colSpan={8} style={{ padding: 24, textAlign: 'center', color: '#94a3b8' }}>No hay licencias</td></tr>}
          </tbody>
        </table>
      </div>

      {modal && (
        <div style={overlay}>
          <div style={modalBox}>
            <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 20 }}>
              <h2 style={{ fontSize: '1.1rem', fontWeight: 700 }}>{modal === 'nueva' ? 'Nueva licencia' : `Editar — ${modal.cliente?.nombre}`}</h2>
              <button onClick={() => setModal(null)} style={{ background: 'none', border: 'none' }}><X size={20} /></button>
            </div>
            <form onSubmit={guardar}>
              <div style={grid2}>
                <Field label="Cliente *">
                  <select value={form.cliente_id} onChange={e => setForm({ ...form, cliente_id: e.target.value })} required>
                    <option value="">Seleccionar...</option>
                    {clientes.map(c => <option key={c.id} value={c.id}>{c.nombre}</option>)}
                  </select>
                </Field>
                <Field label="Sistema *">
                  <select value={form.producto_id} onChange={e => setForm({ ...form, producto_id: e.target.value })} required>
                    <option value="">Seleccionar...</option>
                    {productos.map(p => <option key={p.id} value={p.id}>{p.nombre} — {fmt(p.precio_mensual)}/mes</option>)}
                  </select>
                </Field>
              </div>
              <div style={grid2}>
                <Field label="Día de corte (1-31)" hint="Día del mes en que vence el pago. Vacío = sin ciclo fijo.">
                  <input type="number" min="1" max="31" value={form.dia_corte} onChange={e => setForm({ ...form, dia_corte: e.target.value })} placeholder="Ej: 20" />
                </Field>
                <Field label="Días de tolerancia" hint="Días después del corte antes de bloquear. 0 = bloquea el mismo día.">
                  <input type="number" min="0" max="60" value={form.dias_gracia} onChange={e => setForm({ ...form, dias_gracia: e.target.value })} disabled={!form.bloquear} />
                </Field>
              </div>
              <div style={grid2}>
                <Field label="Mensualidad propia" hint="Vacío = usa el precio del sistema.">
                  <input type="number" min="0" step="1000" value={form.precio_mensual} onChange={e => setForm({ ...form, precio_mensual: e.target.value })} placeholder="250000" />
                </Field>
                <Field label={modal === 'nueva' ? 'Vencimiento inicial' : 'Vencimiento actual'} hint={modal === 'nueva' ? 'Vacío = se calcula con el día de corte.' : 'Cámbialo solo para corregir.'}>
                  <input type="date" value={form.fecha_vencimiento} onChange={e => setForm({ ...form, fecha_vencimiento: e.target.value })} />
                </Field>
              </div>
              <label style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: '.88rem', margin: '4px 0 14px', cursor: 'pointer' }}>
                <input type="checkbox" checked={form.bloquear} onChange={e => setForm({ ...form, bloquear: e.target.checked })} />
                Bloquear el sistema del cliente si no paga
                {!form.bloquear && <span style={{ fontSize: '.75rem', color: '#ea580c' }}>— solo te avisa la mora, nunca lo apaga</span>}
              </label>
              <Field label="Notas">
                <input value={form.notas} onChange={e => setForm({ ...form, notas: e.target.value })} placeholder="Ej: paga por Nequi los 20" />
              </Field>
              <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 20 }}>
                <button type="button" onClick={() => setModal(null)} style={btn('#94a3b8')}>Cancelar</button>
                <button type="submit" style={btn('#4f46e5')}>{modal === 'nueva' ? 'Crear licencia' : 'Guardar cambios'}</button>
              </div>
            </form>
          </div>
        </div>
      )}

      {modalPago && (
        <div style={overlay}>
          <div style={{ ...modalBox, width: 420 }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 6 }}>
              <h2 style={{ fontSize: '1.1rem', fontWeight: 700 }}>Registrar pago</h2>
              <button onClick={() => setModalPago(null)} style={{ background: 'none', border: 'none' }}><X size={20} /></button>
            </div>
            <p style={{ fontSize: '.85rem', color: '#64748b', marginBottom: 16 }}>
              {modalPago.cliente?.nombre} · {modalPago.producto?.nombre} · {fmt(modalPago.precio_efectivo)}/mes
              {modalPago.dia_corte && <> · corte el día {modalPago.dia_corte}</>}
            </p>
            <form onSubmit={registrarPago}>
              <div style={grid2}>
                <Field label="Método">
                  <select value={pago.metodo_pago} onChange={e => setPago({ ...pago, metodo_pago: e.target.value })}>
                    {METODOS.map(([v, l]) => <option key={v} value={v}>{l}</option>)}
                  </select>
                </Field>
                <Field label="Meses pagados">
                  <select value={pago.meses} onChange={e => setPago({ ...pago, meses: e.target.value })}>
                    {[1, 2, 3, 6, 12].map(m => <option key={m} value={m}>{m} mes{m > 1 ? 'es' : ''}</option>)}
                  </select>
                </Field>
              </div>
              <div style={grid2}>
                <Field label="Monto" hint={`Vacío = ${fmt(Number(modalPago.precio_efectivo) * Number(pago.meses))}`}>
                  <input type="number" min="0" value={pago.monto} onChange={e => setPago({ ...pago, monto: e.target.value })} />
                </Field>
                <Field label="Fecha" hint="Vacío = hoy">
                  <input type="date" value={pago.fecha_pago} onChange={e => setPago({ ...pago, fecha_pago: e.target.value })} />
                </Field>
              </div>
              <Field label="Referencia / notas">
                <input value={pago.notas} onChange={e => setPago({ ...pago, notas: e.target.value })} placeholder="Ej: Nequi #12345" />
              </Field>
              <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 20 }}>
                <button type="button" onClick={() => setModalPago(null)} style={btn('#94a3b8')}>Cancelar</button>
                <button type="submit" style={btn('#10b981')}>Registrar y renovar</button>
              </div>
            </form>
          </div>
        </div>
      )}

      {modalRenew && (
        <div style={overlay}>
          <div style={{ ...modalBox, width: 360 }}>
            <h2 style={{ fontSize: '1.1rem', fontWeight: 700, marginBottom: 6 }}>Renovar sin pago</h2>
            <p style={{ fontSize: '.8rem', color: '#64748b', marginBottom: 14 }}>Cortesía o ajuste. No registra plata ni factura. Si el cliente pagó, usa "Registrar pago".</p>
            <Field label="Agregar meses">
              <select value={mesesRenew} onChange={e => setMesesRenew(e.target.value)}>
                {[1, 3, 6, 12].map(m => <option key={m} value={m}>{m} mes{m > 1 ? 'es' : ''}</option>)}
              </select>
            </Field>
            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 20 }}>
              <button onClick={() => setModalRenew(null)} style={btn('#94a3b8')}>Cancelar</button>
              <button onClick={renovar} style={btn('#f59e0b')}>Renovar</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

const td = { padding: '12px 16px', fontSize: '.9rem', verticalAlign: 'top' };
const grid2 = { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 14 };
const overlay  = { position: 'fixed', inset: 0, background: 'rgba(0,0,0,.45)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50 };
const modalBox = { background: '#fff', borderRadius: 14, padding: 28, width: 560, maxWidth: '95vw', maxHeight: '92vh', overflowY: 'auto' };
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
