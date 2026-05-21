import React, { useEffect, useState, useCallback } from 'react';
import { Plus, Search, Edit2, DollarSign, FileText } from 'lucide-react';
import api from '../services/api';
import toast from 'react-hot-toast';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import useConfigStore from '../store/configStore';

const estadoColor = {
  Pendiente: 'amber', Confirmada: 'blue',
  'En Curso': 'purple', Completada: 'green', Cancelada: 'gray'
};

function ReservaModal({ clientes, agentes, paquetes, onClose, onSaved }) {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const [paso, setPaso] = useState(1); // 1=datos, 2=pasajeros, 3=pago inicial

  const [form, setForm] = useState({
    cliente_id:'', agente_id:'', paquete_id:'', fecha_salida:'',
    fecha_regreso:'', num_adultos:1, num_ninos:0,
    precio_total:'', descuento:0, impuesto:0, total_final:'',
    costo_neto: 0,
    notas_internas:''
  });
  const [pasajeros, setPasajeros] = useState([{ nombre:'', apellido:'', pasaporte:'', tipo:'Adulto' }]);
  const [saving, setSaving] = useState(false);
  const set = (k, v) => setForm(f => ({ ...f, [k]: v }));

  const calcTotal = () => {
    const t = (+form.precio_total || 0) - (+form.descuento || 0) + (+form.impuesto || 0);
    setForm(f => ({ ...f, total_final: t.toFixed(2) }));
  };

  const handleSubmit = async () => {
    setSaving(true);
    try {
      await api.post('/reservas', { ...form, pasajeros });
      onSaved();
    } catch(e) { console.error(e); }
    finally { setSaving(false); }
  };

  const addPasajero = () => setPasajeros(p => [...p, { nombre:'', apellido:'', pasaporte:'', tipo:'Adulto' }]);
  const updPasajero = (i, k, v) => setPasajeros(p => p.map((x, idx) => idx === i ? {...x,[k]:v} : x));

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal modal-xl animate-fade-in-up">
        <div className="modal-header">
          <h2 className="modal-title">Nueva Reserva</h2>
          <div style={{ display:'flex', alignItems:'center', gap: 8 }}>
            {[1,2].map(p => (
              <div key={p} style={{
                width:28, height:28, borderRadius:'50%', display:'flex', alignItems:'center', justifyContent:'center',
                background: paso >= p ? 'var(--color-primary)' : 'var(--bg-input)',
                border: `2px solid ${paso >= p ? 'var(--color-primary)' : 'var(--border)'}`,
                fontSize: '0.75rem', fontWeight: 700, color: paso >= p ? 'white' : 'var(--text-muted)'
              }}>{p}</div>
            ))}
          </div>
          <button className="modal-close" onClick={onClose}>✕</button>
        </div>

        <div className="modal-body">
          {paso === 1 && (
            <>
              <div className="form-row">
                <div className="form-group">
                  <label className="form-label required">Cliente</label>
                  <select className="form-control" value={form.cliente_id} required onChange={e=>set('cliente_id',e.target.value)}>
                    <option value="">Seleccionar</option>
                    {clientes.map(c=><option key={c.id} value={c.id}>{c.nombre} {c.apellido}</option>)}
                  </select>
                </div>
                <div className="form-group">
                  <label className="form-label">Agente</label>
                  <select className="form-control" value={form.agente_id} onChange={e=>set('agente_id',e.target.value)}>
                    <option value="">Seleccionar</option>
                    {agentes.map(a=><option key={a.id} value={a.id}>{a.nombre} {a.apellido}</option>)}
                  </select>
                </div>
              </div>
              <div className="form-group">
                <label className="form-label">Paquete Turístico</label>
                <select className="form-control" value={form.paquete_id} onChange={e => {
                  const p = paquetes.find(x => x.id == e.target.value);
                  if (p) {
                    setForm(f => ({ ...f, paquete_id: e.target.value, precio_total: p.precio_base, total_final: p.precio_base, costo_neto: p.costo_neto || 0 }));
                  } else {
                    setForm(f => ({ ...f, paquete_id: e.target.value }));
                  }
                }}>
                  <option value="">Seleccionar paquete</option>
                  {paquetes.map(p=><option key={p.id} value={p.id}>{p.nombre} — {m}{p.precio_base}</option>)}
                </select>
              </div>
              <div className="form-row">
                <div className="form-group">
                  <label className="form-label required">Fecha de Salida</label>
                  <input type="date" className="form-control" value={form.fecha_salida} required onChange={e=>set('fecha_salida',e.target.value)} />
                </div>
                <div className="form-group">
                  <label className="form-label">Fecha de Regreso</label>
                  <input type="date" className="form-control" value={form.fecha_regreso} onChange={e=>set('fecha_regreso',e.target.value)} />
                </div>
              </div>
              <div className="form-row-3">
                <div className="form-group">
                  <label className="form-label">Adultos</label>
                  <input type="number" min={1} className="form-control" value={form.num_adultos} onChange={e=>set('num_adultos',+e.target.value)} />
                </div>
                <div className="form-group">
                  <label className="form-label">Niños</label>
                  <input type="number" min={0} className="form-control" value={form.num_ninos} onChange={e=>set('num_ninos',+e.target.value)} />
                </div>
                <div className="form-group">
                  <label className="form-label">Precio Total ({m})</label>

                  <input type="number" className="form-control" value={form.precio_total}
                    onChange={e=>{ set('precio_total',e.target.value); calcTotal(); }} />
                </div>
              </div>
              <div className="form-row-3">
                <div className="form-group">
                  <label className="form-label">Descuento</label>
                  <input type="number" className="form-control" value={form.descuento} onChange={e=>{set('descuento',e.target.value); calcTotal();}} />
                </div>
                <div className="form-group">
                  <label className="form-label">Impuesto</label>
                  <input type="number" className="form-control" value={form.impuesto} onChange={e=>{set('impuesto',e.target.value); calcTotal();}} />
                </div>
                <div className="form-group">
                  <label className="form-label">Total Final ({m})</label>

                  <input type="number" className="form-control" value={form.total_final}
                    onChange={e=>set('total_final',e.target.value)}
                    style={{ fontWeight:700, color:'var(--color-success)' }} />
                </div>
              </div>
              <div className="form-group">
                <label className="form-label">Notas Internas</label>
                <textarea className="form-control" rows={2} value={form.notas_internas} onChange={e=>set('notas_internas',e.target.value)} />
              </div>
              <div className="form-row">
                <div className="form-group">
                  <label className="form-label">Costo Neto Estimado ({m})</label>

                  <input type="number" step="0.01" className="form-control" value={form.costo_neto}
                    onChange={e=>set('costo_neto',e.target.value)}
                    placeholder="Costo real del proveedor"
                    style={{ borderColor: 'var(--color-warning)' }} />
                </div>
                <div className="form-group">
                  <label className="form-label">Margen Estimado</label>
                  <div className="form-control" style={{ background:'var(--bg-card)', display:'flex', alignItems:'center', color:'var(--color-success)', fontWeight:700 }}>
                    {form.total_final && form.costo_neto
                      ? `${m}${(+form.total_final - +form.costo_neto).toFixed(2)}`
                      : '—'}
                  </div>
                </div>
              </div>
            </>
          )}

          {paso === 2 && (
            <>
              <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:16 }}>
                <h3 className="font-semibold">Pasajeros</h3>
                <button className="btn btn-secondary btn-sm" onClick={addPasajero}><Plus size={14} /> Agregar</button>
              </div>
              {pasajeros.map((p, i) => (
                <div key={i} style={{ background:'var(--bg-input)', borderRadius:'var(--radius-sm)', padding:16, marginBottom:12 }}>
                  <div className="form-row">
                    <div className="form-group">
                      <label className="form-label">Nombre</label>
                      <input className="form-control" value={p.nombre} onChange={e=>updPasajero(i,'nombre',e.target.value)} />
                    </div>
                    <div className="form-group">
                      <label className="form-label">Apellido</label>
                      <input className="form-control" value={p.apellido} onChange={e=>updPasajero(i,'apellido',e.target.value)} />
                    </div>
                  </div>
                  <div className="form-row">
                    <div className="form-group">
                      <label className="form-label">Pasaporte/DNI</label>
                      <input className="form-control" value={p.pasaporte} onChange={e=>updPasajero(i,'pasaporte',e.target.value)} />
                    </div>
                    <div className="form-group">
                      <label className="form-label">Tipo</label>
                      <select className="form-control" value={p.tipo} onChange={e=>updPasajero(i,'tipo',e.target.value)}>
                        <option>Adulto</option><option>Niño</option><option>Infante</option>
                      </select>
                    </div>
                  </div>
                </div>
              ))}
            </>
          )}
        </div>

        <div className="modal-footer">
          {paso > 1 && (
            <button className="btn btn-secondary" onClick={() => setPaso(p => p - 1)}>Anterior</button>
          )}
          <button className="btn btn-secondary" onClick={onClose}>Cancelar</button>
          {paso < 2 ? (
            <button className="btn btn-primary" onClick={() => setPaso(p => p + 1)}>Siguiente →</button>
          ) : (
            <button className="btn btn-primary" onClick={handleSubmit} disabled={saving || !form.cliente_id || !form.fecha_salida || !form.total_final}>
              {saving ? 'Creando...' : '✓ Crear Reserva'}
            </button>
          )}
        </div>
      </div>
    </div>
  );
}


// ─── Modal Registrar Pago ─────────────────────────────────────
function PagoModal({ reserva, metodosPago, onClose, onSaved }) {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const [form, setForm] = useState({
    metodo_id: metodosPago[0]?.id || '',
    monto: '', referencia: '', notas: '', estado: 'Verificado',
    fecha_pago: new Date().toISOString().split('T')[0]
  });
  const [saving, setSaving] = useState(false);
  const set = (k, v) => setForm(f => ({ ...f, [k]: v }));

  const saldoPendiente = (+reserva.total_final || 0) -
    (reserva.pagos || []).filter(p => p.estado === 'Verificado').reduce((s, p) => s + +p.monto, 0);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      await api.post(`/reservas/${reserva.id}/pagos`, form);
      toast.success('Pago registrado exitosamente');
      onSaved();
    } catch(e) {
      toast.error(e.msg || 'Error al registrar pago');
    } finally { setSaving(false); }
  };

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal animate-fade-in-up">
        <div className="modal-header">
          <h2 className="modal-title">Registrar Pago</h2>
          <button className="modal-close" onClick={onClose}>✕</button>
        </div>
        <form onSubmit={handleSubmit}>
          <div className="modal-body">
            {/* Resumen reserva */}
            <div style={{
              background: 'var(--bg-input)', borderRadius: 'var(--radius-sm)',
              padding: '12px 16px', marginBottom: 20,
              display: 'flex', justifyContent: 'space-between', alignItems: 'center'
            }}>
              <div>
                <div className="text-xs text-muted">Reserva</div>
                <div className="font-bold" style={{ fontFamily: 'monospace' }}>{reserva.codigo_reserva}</div>
              </div>
              <div style={{ textAlign: 'right' }}>
                <div className="text-xs text-muted">Saldo pendiente</div>
                <div className="font-bold" style={{ color: saldoPendiente > 0 ? 'var(--color-warning)' : 'var(--color-success)', fontSize: '1.1rem' }}>
                  {m}{saldoPendiente.toLocaleString('es')}
                </div>
              </div>
            </div>

            <div className="form-row">
              <div className="form-group">
                <label className="form-label required">Monto ({m})</label>

                <input
                  type="number"
                  step="0.01"
                  className="form-control"
                  value={form.monto}
                  required
                  min="0.01"
                  max={saldoPendiente}
                  onChange={e => set('monto', e.target.value)}
                  placeholder="0.00"
                  style={{ fontWeight: 700, color: 'var(--color-success)' }}
                />
              </div>
              <div className="form-group">
                <label className="form-label required">Método de Pago</label>
                <select className="form-control" value={form.metodo_id} required onChange={e => set('metodo_id', e.target.value)}>
                  {metodosPago.map(m => <option key={m.id} value={m.id}>{m.nombre}</option>)}
                </select>
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Nº de Referencia</label>
                <input className="form-control" value={form.referencia}
                  onChange={e => set('referencia', e.target.value)}
                  placeholder="Operación, voucher, transferencia..." />
              </div>
              <div className="form-group">
                <label className="form-label">Fecha de Pago</label>
                <input type="date" className="form-control" value={form.fecha_pago}
                  onChange={e => set('fecha_pago', e.target.value)} />
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Estado</label>
                <select className="form-control" value={form.estado} onChange={e => set('estado', e.target.value)}>
                  <option value="Verificado">Verificado</option>
                  <option value="Pendiente">Pendiente (por verificar)</option>
                </select>
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Notas</label>
              <textarea className="form-control" rows={2} value={form.notas}
                onChange={e => set('notas', e.target.value)} />
            </div>
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
            <button type="submit" className="btn btn-primary" disabled={saving || !form.monto}>
              {saving ? 'Registrando...' : '💳 Registrar Pago'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default function ReservasPage() {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const [reservas,  setReservas]  = useState([]);

  const [total,     setTotal]     = useState(0);
  const [page,      setPage]      = useState(1);
  const [estado,    setEstado]    = useState('');
  const [loading,   setLoading]   = useState(true);
  const [modal,     setModal]     = useState(false);
  const [pagoModal, setPagoModal] = useState(null);
  const [clientes,  setClientes]  = useState([]);
  const [agentes,   setAgentes]   = useState([]);
  const [paquetes,  setPaquetes]  = useState([]);
  const [metodosPago, setMetodosPago] = useState([]);
  const limit = 15;

  const fetchReservas = useCallback(async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams({ page, limit });
      if (estado) params.set('estado', estado);
      const res = await api.get(`/reservas?${params}`);
      setReservas(res.data);
      setTotal(res.total);
    } catch(e) { console.error(e); }
    finally { setLoading(false); }
  }, [page, estado]);

  useEffect(() => { fetchReservas(); }, [fetchReservas]);

  useEffect(() => {
    Promise.all([api.get('/clientes?limit=200'), api.get('/agentes'), api.get('/paquetes'), api.get('/metodos-pago')])
      .then(([c,a,p,m]) => { setClientes(c.data); setAgentes(a.data); setPaquetes(p.data); setMetodosPago(m.data || []); });
  }, []);

  const pagoTotal = (pagos) => (pagos || []).filter(p => p.estado === 'Verificado').reduce((s, p) => s + +p.monto, 0);

  const handleDownloadPdf = async (id, codigo) => {
    try {
      const toastId = toast.loading('Generando PDF...');
      const res = await api.get(`/reservas/${id}/pdf`, { responseType: 'blob' });
      const url = window.URL.createObjectURL(new Blob([res.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `Voucher-${codigo}.pdf`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      toast.success('PDF generado exitosamente', { id: toastId });
    } catch (err) {
      toast.error('Error generando el PDF');
    }
  };

  return (
    <div className="animate-fade-in">
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Reservas</h1>
          <p className="page-subtitle">{total} reservas en total</p>
        </div>
        <button className="btn btn-primary" onClick={() => setModal(true)}>
          <Plus size={16} /> Nueva Reserva
        </button>
      </div>

      <div className="table-wrapper">
        <div className="table-header">
          <div className="table-controls">
            <select className="table-filter" value={estado} onChange={e => { setEstado(e.target.value); setPage(1); }}>
              <option value="">Todos los estados</option>
              {['Pendiente','Confirmada','En Curso','Completada','Cancelada'].map(s=><option key={s}>{s}</option>)}
            </select>
          </div>
          <span className="text-sm text-muted">{total} reservas</span>
        </div>

        {loading ? (
          <div style={{ padding:'40px', textAlign:'center' }}><div className="spinner" style={{ margin:'0 auto' }} /></div>
        ) : reservas.length === 0 ? (
          <div className="empty-state">
            <p className="empty-state-title">No hay reservas</p>
            <p className="empty-state-desc">Crea la primera reserva del sistema</p>
          </div>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Código</th>
                <th>Cliente</th>
                <th>Paquete</th>
                <th>Salida</th>
                <th>Pasajeros</th>
                <th>Total</th>
                <th>Pagado</th>
                <th>Estado</th>
                <th>Agente</th>
                <th>Pago</th>
              </tr>
            </thead>
            <tbody>
              {reservas.map(r => {
                const pagado = pagoTotal(r.pagos);
                const pendiente = (+r.total_final || 0) - pagado;
                return (
                  <tr key={r.id}>
                    <td>
                      <span className="badge badge-blue" style={{ fontFamily:'monospace', fontSize:'0.75rem' }}>
                        {r.codigo_reserva}
                      </span>
                    </td>
                    <td>
                      <div className="font-semibold text-sm">{r.cliente?.nombre} {r.cliente?.apellido}</div>
                      <div className="text-xs text-muted">{r.cliente?.email}</div>
                    </td>
                    <td className="text-sm">{r.paquete?.nombre || <span className="text-muted">Personalizado</span>}</td>
                    <td className="text-sm">
                      {r.fecha_salida ? format(new Date(r.fecha_salida), 'd MMM yyyy', { locale: es }) : '—'}
                    </td>
                    <td className="text-center">
                      <span className="badge badge-gray">
                        {(+r.num_adultos || 0) + (+r.num_ninos || 0)} pax
                      </span>
                    </td>
                    <td>
                      <div className="font-semibold text-sm text-success">
                        {m}{(+r.total_final || 0).toLocaleString('es')}
                      </div>
                    </td>
                    <td>
                      <div className="text-sm">{m}{pagado.toLocaleString('es')}</div>
                      {pendiente > 0 && (
                        <div className="text-xs" style={{ color:'var(--color-warning)' }}>
                          Pendiente: {m}{pendiente.toLocaleString('es')}
                        </div>
                      )}
                    </td>
                    <td>
                      <span className={`badge badge-${estadoColor[r.estado] || 'gray'}`}>
                        {r.estado}
                      </span>
                    </td>
                    <td className="text-xs text-muted">
                      {r.agente?.nombre} {r.agente?.apellido}
                    </td>
                    <td className="td-actions">
                      {r.estado !== 'Cancelada' && (
                        <button
                          className="btn btn-ghost btn-icon btn-sm"
                          title="Registrar Pago"
                          style={{ color: 'var(--color-success)' }}
                          onClick={() => setPagoModal(r)}
                        >
                          <DollarSign size={14} />
                        </button>
                      )}
                      <button
                        className="btn btn-ghost btn-icon btn-sm"
                        title="Descargar Voucher"
                        style={{ color: 'var(--color-primary)' }}
                        onClick={() => handleDownloadPdf(r.id, r.codigo_reserva)}
                      >
                        <FileText size={14} />
                      </button>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        )}

        {Math.ceil(total/limit) > 1 && (
          <div className="pagination">
            <button className="page-btn" onClick={() => setPage(p=>Math.max(1,p-1))} disabled={page===1}>‹</button>
            {Array.from({length: Math.min(Math.ceil(total/limit),5)},(_,i)=>i+1).map(p=>(
              <button key={p} className={`page-btn ${p===page?'active':''}`} onClick={()=>setPage(p)}>{p}</button>
            ))}
            <button className="page-btn" onClick={() => setPage(p=>p+1)} disabled={page===Math.ceil(total/limit)}>›</button>
          </div>
        )}
      </div>

      {modal && (
        <ReservaModal
          clientes={clientes} agentes={agentes} paquetes={paquetes}
          onClose={() => setModal(false)}
          onSaved={() => { setModal(false); fetchReservas(); }}
        />
      )}

      {pagoModal && (
        <PagoModal
          reserva={pagoModal}
          metodosPago={metodosPago}
          onClose={() => setPagoModal(null)}
          onSaved={() => { setPagoModal(null); fetchReservas(); }}
        />
      )}
    </div>
  );
}
