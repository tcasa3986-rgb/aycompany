import React, { useEffect, useState } from 'react';
import { CheckSquare, Plus, Edit2, Trash2, AlertCircle, Clock, CheckCircle2 } from 'lucide-react';
import api from '../services/api';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import useAuthStore from '../store/authStore';

const estadoColor = { Pendiente:'amber', 'En Progreso':'blue', Completada:'green', Cancelada:'gray' };
const prioColor   = { Urgente:'red', Alta:'amber', Media:'blue', Baja:'gray' };

function TareaModal({ tarea, clientes, agentes, onClose, onSaved }) {
  const { usuario } = useAuthStore();
  const [form, setForm] = useState(tarea || {
    titulo:'', descripcion:'', asignado_a: usuario?.id || '',
    cliente_id:'', prioridad:'Media', estado:'Pendiente', fecha_vence:''
  });
  const [saving, setSaving] = useState(false);
  const set = (k, v) => setForm(f => ({ ...f, [k]: v }));

  const handleSubmit = async (e) => {
    e.preventDefault(); setSaving(true);
    try {
      if (tarea?.id) await api.put(`/tareas/${tarea.id}`, form);
      else await api.post('/tareas', form);
      onSaved();
    } catch(e) { console.error(e); }
    finally { setSaving(false); }
  };

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal animate-fade-in-up">
        <div className="modal-header">
          <h2 className="modal-title">{tarea ? 'Editar Tarea' : 'Nueva Tarea'}</h2>
          <button className="modal-close" onClick={onClose}>✕</button>
        </div>
        <form onSubmit={handleSubmit}>
          <div className="modal-body">
            <div className="form-group">
              <label className="form-label required">Título</label>
              <input className="form-control" value={form.titulo} required onChange={e=>set('titulo',e.target.value)} />
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Asignar a</label>
                <select className="form-control" value={form.asignado_a} onChange={e=>set('asignado_a',e.target.value)}>
                  {agentes.map(a=><option key={a.id} value={a.id}>{a.nombre} {a.apellido}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Cliente</label>
                <select className="form-control" value={form.cliente_id} onChange={e=>set('cliente_id',e.target.value)}>
                  <option value="">Sin cliente</option>
                  {clientes.map(c=><option key={c.id} value={c.id}>{c.nombre} {c.apellido}</option>)}
                </select>
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Prioridad</label>
                <select className="form-control" value={form.prioridad} onChange={e=>set('prioridad',e.target.value)}>
                  {['Baja','Media','Alta','Urgente'].map(p=><option key={p}>{p}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Fecha de Vencimiento</label>
                <input type="datetime-local" className="form-control" value={form.fecha_vence?.slice(0,16) || ''}
                  onChange={e=>set('fecha_vence',e.target.value)} />
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Descripción</label>
              <textarea className="form-control" rows={3} value={form.descripcion} onChange={e=>set('descripcion',e.target.value)} />
            </div>
            {tarea && (
              <div className="form-group">
                <label className="form-label">Estado</label>
                <select className="form-control" value={form.estado} onChange={e=>set('estado',e.target.value)}>
                  {['Pendiente','En Progreso','Completada','Cancelada'].map(s=><option key={s}>{s}</option>)}
                </select>
              </div>
            )}
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
            <button type="submit" className="btn btn-primary" disabled={saving}>
              {saving ? 'Guardando...' : (tarea ? 'Actualizar' : 'Crear Tarea')}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default function TareasPage() {
  const [tareas,   setTareas]   = useState([]);
  const [clientes, setClientes] = useState([]);
  const [agentes,  setAgentes]  = useState([]);
  const [loading,  setLoading]  = useState(true);
  const [modal,    setModal]    = useState(null);
  const [filtroEst,setFiltroEst]= useState('');
  const { usuario } = useAuthStore();

  const fetch = async () => {
    setLoading(true);
    try {
      const params = filtroEst ? `?estado=${filtroEst}` : '';
      const [t, c, a] = await Promise.all([
        api.get(`/tareas${params}`),
        api.get('/clientes?limit=200'),
        api.get('/agentes'),
      ]);
      setTareas(t.data); setClientes(c.data); setAgentes(a.data);
    } catch(e) { console.error(e); }
    finally { setLoading(false); }
  };

  useEffect(() => { fetch(); }, [filtroEst]);

  const completar = async (id) => {
    await api.put(`/tareas/${id}`, { estado: 'Completada' });
    fetch();
  };

  const eliminar = async (id) => {
    if (!confirm('¿Cancelar esta tarea?')) return;
    await api.delete(`/tareas/${id}`);
    fetch();
  };

  const pendientes  = tareas.filter(t => t.estado === 'Pendiente');
  const enProgreso  = tareas.filter(t => t.estado === 'En Progreso');
  const completadas = tareas.filter(t => t.estado === 'Completada');

  const ColTarea = ({ title, items, colorClass }) => (
    <div className="card" style={{ flex:1 }}>
      <div className="card-header">
        <div className="card-title" style={{ display:'flex', alignItems:'center', gap:8 }}>
          <span className={`badge badge-${colorClass}`}>{title}</span>
          <span style={{fontSize:'0.8rem',fontWeight:400,color:'var(--text-muted)'}}>{items.length}</span>
        </div>
        <button className="btn btn-primary btn-sm" onClick={() => setModal('crear')}>
          <Plus size={13}/> Nueva
        </button>
      </div>
      <div style={{ display:'flex', flexDirection:'column', gap:10 }}>
        {items.length === 0 ? (
          <div style={{ textAlign:'center', padding:'20px 0', color:'var(--text-muted)', fontSize:'0.8rem' }}>
            Sin tareas
          </div>
        ) : items.map(t => (
          <div key={t.id} style={{
            background:'var(--bg-input)', borderRadius:'var(--radius-sm)',
            padding:'12px 14px', borderLeft:`3px solid var(--color-${prioColor[t.prioridad] || 'blue'})`
          }}>
            <div style={{ display:'flex', justifyContent:'space-between', alignItems:'flex-start', gap:8 }}>
              <div style={{ flex:1 }}>
                <div className="font-semibold text-sm">{t.titulo}</div>
                {t.cliente && (
                  <div className="text-xs text-muted mt-1">👤 {t.cliente.nombre} {t.cliente.apellido}</div>
                )}
                {t.fecha_vence && (
                  <div className="text-xs mt-1" style={{
                    color: new Date(t.fecha_vence) < new Date() ? 'var(--color-danger)' : 'var(--text-muted)'
                  }}>
                    <Clock size={10} style={{ display:'inline', marginRight:3 }} />
                    {format(new Date(t.fecha_vence), "d MMM · HH:mm", { locale:es })}
                  </div>
                )}
              </div>
              <div style={{ display:'flex', gap:4 }}>
                {t.estado !== 'Completada' && (
                  <button className="btn btn-ghost btn-icon btn-sm" title="Completar" onClick={() => completar(t.id)}
                    style={{ color:'var(--color-success)' }}>
                    <CheckCircle2 size={14} />
                  </button>
                )}
                <button className="btn btn-ghost btn-icon btn-sm" title="Editar" onClick={() => setModal(t)}>
                  <Edit2 size={13}/>
                </button>
                <button className="btn btn-ghost btn-icon btn-sm" title="Cancelar" onClick={() => eliminar(t.id)}
                  style={{ color:'var(--color-danger)' }}>
                  <Trash2 size={13}/>
                </button>
              </div>
            </div>
            <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginTop:8 }}>
              <span className={`badge badge-${prioColor[t.prioridad] || 'blue'}`} style={{ fontSize:'0.65rem' }}>
                {t.prioridad}
              </span>
              {t.asignado && (
                <span className="text-xs text-muted">{t.asignado.nombre}</span>
              )}
            </div>
          </div>
        ))}
      </div>
    </div>
  );

  return (
    <div className="animate-fade-in">
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Tareas</h1>
          <p className="page-subtitle">{tareas.length} tareas en total</p>
        </div>
        <button className="btn btn-primary" onClick={() => setModal('crear')}>
          <Plus size={16} /> Nueva Tarea
        </button>
      </div>

      {loading ? (
        <div style={{ textAlign:'center', padding:40 }}><div className="spinner" style={{ margin:'0 auto' }}/></div>
      ) : (
        <div style={{ display:'flex', gap:20, alignItems:'flex-start' }}>
          <ColTarea title="Pendiente"   items={pendientes}  colorClass="amber" />
          <ColTarea title="En Progreso" items={enProgreso}  colorClass="blue"  />
          <ColTarea title="Completada"  items={completadas} colorClass="green" />
        </div>
      )}

      {(modal === 'crear' || (modal && modal.id)) && (
        <TareaModal
          tarea={modal === 'crear' ? null : modal}
          clientes={clientes} agentes={agentes}
          onClose={() => setModal(null)}
          onSaved={() => { setModal(null); fetch(); }}
        />
      )}
    </div>
  );
}
