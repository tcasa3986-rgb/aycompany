import React, { useEffect, useState } from 'react';
import { Plus, Edit2, Trash2, Search, Send } from 'lucide-react';
import toast from 'react-hot-toast';
import api from '../services/api';
import useConfigStore from '../store/configStore';

import { format } from 'date-fns';
import { es } from 'date-fns/locale';

const estadoColor = { Borrador:'gray', Activa:'green', Pausada:'amber', Finalizada:'blue' };

export default function CampanasPage() {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const [campanas, setCampanas] = useState([]);

  const [loading,  setLoading]  = useState(true);
  const [modal,    setModal]    = useState(null);
  const [form,     setForm]     = useState({ nombre:'', tipo:'Email', estado:'Borrador', descripcion:'', fecha_inicio:'', fecha_fin:'', presupuesto:'' });
  const [saving,   setSaving]   = useState(false);
  const set = (k, v) => setForm(f => ({ ...f, [k]: v }));

  const fetch = async () => {
    setLoading(true);
    try { const r = await api.get('/campanas'); setCampanas(r.data); }
    catch(e) { console.error(e); } finally { setLoading(false); }
  };
  useEffect(() => { fetch(); }, []);

  const openModal = (c = null) => {
    setForm(c || { nombre:'', tipo:'Email', estado:'Borrador', descripcion:'', fecha_inicio:'', fecha_fin:'', presupuesto:'' });
    setModal(c?.id || 'crear');
  };

  const handleSubmit = async (e) => {
    e.preventDefault(); setSaving(true);
    try {
      if (modal !== 'crear') await api.put(`/campanas/${modal}`, form);
      else await api.post('/campanas', form);
      setModal(null); fetch();
      toast.success(modal === 'crear' ? 'Campaña creada' : 'Campaña actualizada');
    } catch(e) { console.error(e); toast.error('Ocurrió un error'); } finally { setSaving(false); }
  };

  const handleEnviar = async (id) => {
    if (!window.confirm('¿Estás seguro de enviar/iniciar esta campaña ahora mismo?')) return;
    const toastId = toast.loading('Procesando campaña...');
    try {
      const res = await api.post(`/campanas/${id}/enviar`);
      toast.success(res.data.msg || 'Campaña en curso!', { id: toastId });
      fetch();
    } catch (e) {
      toast.error('Error al iniciar campaña', { id: toastId });
    }
  };

  return (
    <div className="animate-fade-in">
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Campañas de Marketing</h1>
          <p className="page-subtitle">{campanas.length} campañas</p>
        </div>
        <button className="btn btn-primary" onClick={() => openModal()}>
          <Plus size={16} /> Nueva Campaña
        </button>
      </div>

      <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(300px,1fr))', gap:20 }}>
        {loading ? (
          Array.from({length:3}).map((_,i) => <div key={i} className="card"><div className="skeleton" style={{height:120}} /></div>)
        ) : campanas.map(c => (
          <div key={c.id} className="card">
            <div style={{ display:'flex', justifyContent:'space-between', alignItems:'flex-start', marginBottom:12 }}>
              <div>
                <div className="font-bold" style={{ marginBottom:4 }}>{c.nombre}</div>
                <div style={{ display:'flex', gap:6 }}>
                  <span className="badge badge-blue">{c.tipo}</span>
                  <span className={`badge badge-${estadoColor[c.estado]||'gray'}`}>{c.estado}</span>
                </div>
              </div>
              <div style={{ display:'flex', gap:4 }}>
                {['Borrador', 'Pausada'].includes(c.estado) && (
                  <button 
                    className="btn btn-ghost btn-icon btn-sm" 
                    style={{ color: 'var(--color-primary)' }} 
                    onClick={() => handleEnviar(c.id)} 
                    title="Ejecutar/Enviar Campaña"
                  >
                    <Send size={15}/>
                  </button>
                )}
                <button className="btn btn-ghost btn-icon btn-sm" onClick={() => openModal(c)}><Edit2 size={14}/></button>
              </div>
            </div>
            {c.descripcion && <p className="text-sm text-muted" style={{ marginBottom:12, lineHeight:1.5 }}>{c.descripcion.substring(0,80)}...</p>}
            <div style={{ display:'flex', gap:16 }}>
              {c.fecha_inicio && <div className="text-xs text-muted">Inicio: {format(new Date(c.fecha_inicio),'d MMM',{locale:es})}</div>}
              {c.fecha_fin    && <div className="text-xs text-muted">Fin: {format(new Date(c.fecha_fin),'d MMM',{locale:es})}</div>}
              {c.presupuesto  && <div className="text-xs font-semibold text-success">${c.presupuesto}</div>}
            </div>
          </div>
        ))}
      </div>

      {modal && (
        <div className="modal-overlay" onClick={e => e.target === e.currentTarget && setModal(null)}>
          <div className="modal animate-fade-in-up">
            <div className="modal-header">
              <h2 className="modal-title">{modal === 'crear' ? 'Nueva Campaña' : 'Editar Campaña'}</h2>
              <button className="modal-close" onClick={() => setModal(null)}>✕</button>
            </div>
            <form onSubmit={handleSubmit}>
              <div className="modal-body">
                <div className="form-group">
                  <label className="form-label required">Nombre</label>
                  <input className="form-control" value={form.nombre} required onChange={e=>set('nombre',e.target.value)} />
                </div>
                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">Tipo</label>
                    <select className="form-control" value={form.tipo} onChange={e=>set('tipo',e.target.value)}>
                      {['Email','WhatsApp','SMS','Redes Sociales','Otro'].map(t=><option key={t}>{t}</option>)}
                    </select>
                  </div>
                  <div className="form-group">
                    <label className="form-label">Estado</label>
                    <select className="form-control" value={form.estado} onChange={e=>set('estado',e.target.value)}>
                      {['Borrador','Activa','Pausada','Finalizada'].map(s=><option key={s}>{s}</option>)}
                    </select>
                  </div>
                </div>
                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">Fecha Inicio</label>
                    <input type="date" className="form-control" value={form.fecha_inicio} onChange={e=>set('fecha_inicio',e.target.value)} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">Fecha Fin</label>
                    <input type="date" className="form-control" value={form.fecha_fin} onChange={e=>set('fecha_fin',e.target.value)} />
                  </div>
                </div>
                <div className="form-group">
                  <label className="form-label">Presupuesto ({m})</label>

                  <input type="number" className="form-control" value={form.presupuesto} onChange={e=>set('presupuesto',e.target.value)} />
                </div>
                <div className="form-group">
                  <label className="form-label">Descripción</label>
                  <textarea className="form-control" rows={3} value={form.descripcion} onChange={e=>set('descripcion',e.target.value)} />
                </div>
              </div>
              <div className="modal-footer">
                <button type="button" className="btn btn-secondary" onClick={() => setModal(null)}>Cancelar</button>
                <button type="submit" className="btn btn-primary" disabled={saving}>
                  {saving ? 'Guardando...' : (modal === 'crear' ? 'Crear' : 'Actualizar')}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
