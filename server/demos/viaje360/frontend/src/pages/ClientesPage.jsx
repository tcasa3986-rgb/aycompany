import React, { useEffect, useState, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { Plus, Search, Eye, Edit2, MessageSquare, Trash2 } from 'lucide-react';
import api from '../services/api';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

// ─── Helpers ──────────────────────────────────────────────────
const catColor = { Nuevo:'blue', Recurrente:'green', VIP:'amber', Inactivo:'gray' };

const initiales = (nombre, apellido) =>
  `${nombre?.[0] || ''}${apellido?.[0] || ''}`.toUpperCase();

const gradients = [
  'linear-gradient(135deg,#0EA5E9,#06B6D4)',
  'linear-gradient(135deg,#8B5CF6,#EC4899)',
  'linear-gradient(135deg,#10B981,#06B6D4)',
  'linear-gradient(135deg,#F59E0B,#EF4444)',
  'linear-gradient(135deg,#6366F1,#8B5CF6)',
];
const avatarGradient = (id) => gradients[id % gradients.length];

// ─── Modal Crear/Editar ───────────────────────────────────────
function ClienteModal({ cliente, fuentes, agentes, onClose, onSaved }) {
  const [form, setForm] = useState(
    cliente || {
      nombre:'', apellido:'', email:'', telefono:'', pais:'',
      ciudad:'', documento_tipo:'DNI', documento_num:'',
      genero:'', categoria:'Nuevo', fuente_id:'', agente_id:'', notas:''
    }
  );
  const [saving, setSaving] = useState(false);
  const [err, setErr] = useState('');

  const set = (k, v) => setForm(f => ({ ...f, [k]: v }));

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true); setErr('');
    try {
      if (cliente?.id) await api.put(`/clientes/${cliente.id}`, form);
      else await api.post('/clientes', form);
      onSaved();
    } catch (e) {
      setErr(e.msg || 'Error al guardar');
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal modal-lg animate-fade-in-up">
        <div className="modal-header">
          <h2 className="modal-title">{cliente ? 'Editar Cliente' : 'Nuevo Cliente'}</h2>
          <button className="modal-close" onClick={onClose}>✕</button>
        </div>
        <form onSubmit={handleSubmit}>
          <div className="modal-body">
            {err && <div className="alert alert-danger">{err}</div>}
            <div className="form-row">
              <div className="form-group">
                <label className="form-label required">Nombre</label>
                <input className="form-control" value={form.nombre} onChange={e=>set('nombre',e.target.value)} required />
              </div>
              <div className="form-group">
                <label className="form-label required">Apellido</label>
                <input className="form-control" value={form.apellido} onChange={e=>set('apellido',e.target.value)} required />
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label required">Email</label>
                <input type="email" className="form-control" value={form.email} onChange={e=>set('email',e.target.value)} required />
              </div>
              <div className="form-group">
                <label className="form-label">Teléfono</label>
                <input className="form-control" value={form.telefono} onChange={e=>set('telefono',e.target.value)} />
              </div>
            </div>
            <div className="form-row-3">
              <div className="form-group">
                <label className="form-label">Documento</label>
                <select className="form-control" value={form.documento_tipo} onChange={e=>set('documento_tipo',e.target.value)}>
                  {['DNI','Pasaporte','CE','RUC'].map(t=><option key={t}>{t}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Número</label>
                <input className="form-control" value={form.documento_num} onChange={e=>set('documento_num',e.target.value)} />
              </div>
              <div className="form-group">
                <label className="form-label">Género</label>
                <select className="form-control" value={form.genero} onChange={e=>set('genero',e.target.value)}>
                  <option value="">Seleccionar</option>
                  <option value="M">Masculino</option>
                  <option value="F">Femenino</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">País</label>
                <input className="form-control" value={form.pais} onChange={e=>set('pais',e.target.value)} />
              </div>
              <div className="form-group">
                <label className="form-label">Ciudad</label>
                <input className="form-control" value={form.ciudad} onChange={e=>set('ciudad',e.target.value)} />
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Fuente de Origen</label>
                <select className="form-control" value={form.fuente_id} onChange={e=>set('fuente_id',e.target.value)}>
                  <option value="">Seleccionar</option>
                  {fuentes.map(f=><option key={f.id} value={f.id}>{f.nombre}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Agente Asignado</label>
                <select className="form-control" value={form.agente_id} onChange={e=>set('agente_id',e.target.value)}>
                  <option value="">Seleccionar</option>
                  {agentes.map(a=><option key={a.id} value={a.id}>{a.nombre} {a.apellido}</option>)}
                </select>
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Categoría</label>
              <select className="form-control" value={form.categoria} onChange={e=>set('categoria',e.target.value)}>
                {['Nuevo','Recurrente','VIP','Inactivo'].map(c=><option key={c}>{c}</option>)}
              </select>
            </div>
            <div className="form-group">
              <label className="form-label">Notas</label>
              <textarea className="form-control" value={form.notas} onChange={e=>set('notas',e.target.value)} rows={3} />
            </div>
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
            <button type="submit" className="btn btn-primary" disabled={saving}>
              {saving ? 'Guardando...' : (cliente ? 'Actualizar' : 'Crear Cliente')}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

// ─── Modal Interacción ────────────────────────────────────────
function InteraccionModal({ clienteId, onClose, onSaved }) {
  const [form, setForm] = useState({ tipo:'Llamada', descripcion:'' });
  const [saving, setSaving] = useState(false);
  const tipos = [
    { value: 'Llamada',     label: 'Llamada'     },
    { value: 'Email',       label: 'Email'        },
    { value: 'WhatsApp',    label: 'WhatsApp'     },
    { value: 'Reunion',     label: 'Reunión'      },
    { value: 'Nota',        label: 'Nota'         },
    { value: 'Cotizacion',  label: 'Cotización'   },
    { value: 'Seguimiento', label: 'Seguimiento'  },
  ];

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      await api.post(`/clientes/${clienteId}/interacciones`, form);
      onSaved();
    } catch(e) { console.error(e); }
    finally { setSaving(false); }
  };

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal animate-fade-in-up">
        <div className="modal-header">
          <h2 className="modal-title">Nueva Interacción</h2>
          <button className="modal-close" onClick={onClose}>✕</button>
        </div>
        <form onSubmit={handleSubmit}>
          <div className="modal-body">
            <div className="form-group">
              <label className="form-label required">Tipo</label>
              <select className="form-control" value={form.tipo} onChange={e=>setForm(f=>({...f,tipo:e.target.value}))}>
                {tipos.map(t=><option key={t.value} value={t.value}>{t.label}</option>)}
              </select>
            </div>
            <div className="form-group">
              <label className="form-label required">Descripción</label>
              <textarea className="form-control" rows={4} required
                value={form.descripcion} onChange={e=>setForm(f=>({...f,descripcion:e.target.value}))} />
            </div>
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
            <button type="submit" className="btn btn-primary" disabled={saving}>
              {saving ? 'Guardando...' : 'Registrar'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

// ─── CLIENTES PAGE ────────────────────────────────────────────
export default function ClientesPage() {
  const navigate = useNavigate();
  const [clientes, setClientes] = useState([]);
  const [total,    setTotal]    = useState(0);
  const [page,     setPage]     = useState(1);
  const [buscar,   setBuscar]   = useState('');
  const [categoria,setCat]      = useState('');
  const [loading,  setLoading]  = useState(true);
  const [fuentes,  setFuentes]  = useState([]);
  const [agentes,  setAgentes]  = useState([]);
  const [modal,    setModal]    = useState(null); // null | 'crear' | cliente obj
  const [intModal, setIntModal] = useState(null); // clienteId

  const limit = 15;
  const pages = Math.ceil(total / limit);

  const fetchClientes = useCallback(async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams({ page, limit });
      if (buscar)   params.set('buscar', buscar);
      if (categoria) params.set('categoria', categoria);
      const res = await api.get(`/clientes?${params}`);
      setClientes(res.data);
      setTotal(res.total);
    } catch(e) { console.error(e); }
    finally { setLoading(false); }
  }, [page, buscar, categoria]);

  useEffect(() => { fetchClientes(); }, [fetchClientes]);

  useEffect(() => {
    Promise.all([api.get('/fuentes'), api.get('/agentes')])
      .then(([f, a]) => { setFuentes(f.data); setAgentes(a.data); });
  }, []);

  const handleSaved = () => { setModal(null); fetchClientes(); };
  const handleIntSaved = () => { setIntModal(null); };

  const eliminar = async (id) => {
    if (!confirm('¿Desactivar este cliente?')) return;
    await api.delete(`/clientes/${id}`);
    fetchClientes();
  };

  return (
    <div className="animate-fade-in">
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Clientes</h1>
          <p className="page-subtitle">{total} clientes registrados</p>
        </div>
        <button className="btn btn-primary" onClick={() => setModal('crear')}>
          <Plus size={16} /> Nuevo Cliente
        </button>
      </div>

      <div className="table-wrapper">
        <div className="table-header">
          <div className="table-controls">
            <div className="table-search">
              <Search size={14} style={{ color: 'var(--text-muted)', flexShrink: 0 }} />
              <input
                placeholder="Buscar por nombre, email..."
                value={buscar}
                onChange={e => { setBuscar(e.target.value); setPage(1); }}
              />
            </div>
            <select className="table-filter" value={categoria} onChange={e => { setCat(e.target.value); setPage(1); }}>
              <option value="">Todas las categorías</option>
              {['Nuevo','Recurrente','VIP','Inactivo'].map(c => <option key={c}>{c}</option>)}
            </select>
          </div>
          <span className="text-sm text-muted">{total} resultados</span>
        </div>

        {loading ? (
          <div style={{ padding: '40px', textAlign: 'center' }}>
            <div className="spinner" style={{ margin: '0 auto' }} />
          </div>
        ) : clientes.length === 0 ? (
          <div className="empty-state">
            <p className="empty-state-title">No se encontraron clientes</p>
            <p className="empty-state-desc">Intenta cambiar los filtros o crea un nuevo cliente</p>
          </div>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Cliente</th>
                <th>Contacto</th>
                <th>País</th>
                <th>Fuente</th>
                <th>Categoría</th>
                <th>Agente</th>
                <th>Registrado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              {clientes.map(c => (
                <tr key={c.id} style={{ cursor: 'pointer' }}
                  onClick={() => navigate(`/clientes/${c.id}`)}
                >
                  <td>
                    <div style={{ display:'flex', alignItems:'center', gap:10 }}>
                      <div className="avatar avatar-sm" style={{ background: avatarGradient(c.id) }}>
                        {initiales(c.nombre, c.apellido)}
                      </div>
                      <div>
                        <div className="font-semibold">{c.nombre} {c.apellido}</div>
                        <div className="text-xs text-muted">{c.documento_tipo}: {c.documento_num}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div className="text-sm">{c.email}</div>
                    <div className="text-xs text-muted">{c.telefono}</div>
                  </td>
                  <td className="text-sm">{c.pais || '—'}</td>
                  <td className="text-xs text-muted">{c.fuente?.nombre || '—'}</td>
                  <td>
                    <span className={`badge badge-${catColor[c.categoria] || 'gray'}`}>
                      {c.categoria}
                    </span>
                  </td>
                  <td>
                    {c.agente ? (
                      <div style={{ display:'flex', alignItems:'center', gap:6 }}>
                        <div className="avatar" style={{ width:24, height:24, fontSize:'0.6rem', background: avatarGradient(c.agente.id) }}>
                          {initiales(c.agente.nombre, c.agente.apellido)}
                        </div>
                        <span className="text-xs">{c.agente.nombre}</span>
                      </div>
                    ) : <span className="text-muted text-xs">Sin asignar</span>}
                  </td>
                  <td className="text-xs text-muted">
                    {c.creado_en ? format(new Date(c.creado_en), 'd MMM yyyy', { locale: es }) : ''}
                  </td>
                  <td>
                    <div className="td-actions">
                      <button className="btn btn-ghost btn-icon btn-sm" title="Ver Perfil CRM"
                        onClick={(e) => { e.stopPropagation(); navigate(`/clientes/${c.id}`); }}
                        style={{ color: '#7C3AED' }}>
                        <Eye size={14} />
                      </button>
                      <button className="btn btn-ghost btn-icon btn-sm" title="Editar"
                        onClick={(e) => { e.stopPropagation(); setModal(c); }}>
                        <Edit2 size={14} />
                      </button>
                      <button className="btn btn-ghost btn-icon btn-sm" title="Nueva Interacción"
                        onClick={(e) => { e.stopPropagation(); setIntModal(c.id); }}>
                        <MessageSquare size={14} />
                      </button>
                      <button className="btn btn-ghost btn-icon btn-sm" title="Desactivar"
                        onClick={(e) => { e.stopPropagation(); eliminar(c.id); }}
                        style={{ color: 'var(--color-danger)' }}>
                        <Trash2 size={14} />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}

        {/* Paginación */}
        {pages > 1 && (
          <div className="pagination">
            <button className="page-btn" onClick={() => setPage(p => Math.max(1, p - 1))} disabled={page === 1}>‹</button>
            {Array.from({ length: Math.min(pages, 5) }, (_, i) => i + 1).map(p => (
              <button key={p} className={`page-btn ${p === page ? 'active' : ''}`} onClick={() => setPage(p)}>{p}</button>
            ))}
            <button className="page-btn" onClick={() => setPage(p => Math.min(pages, p + 1))} disabled={page === pages}>›</button>
          </div>
        )}
      </div>

      {/* Modales */}
      {(modal === 'crear' || (modal && modal.id)) && (
        <ClienteModal
          cliente={modal === 'crear' ? null : modal}
          fuentes={fuentes}
          agentes={agentes}
          onClose={() => setModal(null)}
          onSaved={handleSaved}
        />
      )}

      {intModal && (
        <InteraccionModal
          clienteId={intModal}
          onClose={() => setIntModal(null)}
          onSaved={handleIntSaved}
        />
      )}
    </div>
  );
}
