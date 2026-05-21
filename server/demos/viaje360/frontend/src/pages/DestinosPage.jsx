import React, { useEffect, useState, useCallback } from 'react';
import { Plus, Edit2, MapPin, Globe, ToggleLeft, ToggleRight, Search } from 'lucide-react';
import api from '../services/api';
import toast from 'react-hot-toast';

const destinoEmojis = ['🗼', '🏖️', '🏔️', '🌴', '🏛️', '🌊', '🌅', '🗽', '🏯', '🌋'];
const destinoColors = [
  'linear-gradient(135deg,#0369A1,#0EA5E9)',
  'linear-gradient(135deg,#7C3AED,#A855F7)',
  'linear-gradient(135deg,#065F46,#10B981)',
  'linear-gradient(135deg,#92400E,#F59E0B)',
  'linear-gradient(135deg,#991B1B,#EF4444)',
  'linear-gradient(135deg,#1E3A5F,#3B82F6)',
  'linear-gradient(135deg,#4A1042,#EC4899)',
  'linear-gradient(135deg,#064E3B,#34D399)',
];

function DestinoModal({ destino, paises, onClose, onSaved }) {
  const [form, setForm] = useState(destino || {
    pais_id: '', nombre: '', descripcion: '', imagen_url: '', activo: 1
  });
  const [saving, setSaving] = useState(false);
  const set = (k, v) => setForm(f => ({ ...f, [k]: v }));

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      if (destino?.id) await api.put(`/destinos/${destino.id}`, form);
      else await api.post('/destinos', form);
      toast.success(destino ? 'Destino actualizado' : 'Destino creado');
      onSaved();
    } catch (e) {
      toast.error(e.msg || 'Error al guardar');
    } finally { setSaving(false); }
  };

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal modal-lg animate-fade-in-up">
        <div className="modal-header">
          <h2 className="modal-title">{destino ? 'Editar Destino' : 'Nuevo Destino'}</h2>
          <button className="modal-close" onClick={onClose}>✕</button>
        </div>
        <form onSubmit={handleSubmit}>
          <div className="modal-body">
            <div className="form-group">
              <label className="form-label required">Nombre del Destino</label>
              <input className="form-control" value={form.nombre} required
                onChange={e => set('nombre', e.target.value)}
                placeholder="Ej: París, Cancún, Bangkok..." />
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label required">País</label>
                <select className="form-control" value={form.pais_id} required onChange={e => set('pais_id', e.target.value)}>
                  <option value="">Seleccionar país</option>
                  {paises.map(p => <option key={p.id} value={p.id}>{p.nombre}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Estado</label>
                <select className="form-control" value={form.activo} onChange={e => set('activo', +e.target.value)}>
                  <option value={1}>Activo</option>
                  <option value={0}>Inactivo</option>
                </select>
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">URL de Imagen</label>
              <input className="form-control" value={form.imagen_url || ''}
                onChange={e => set('imagen_url', e.target.value)}
                placeholder="https://..." />
            </div>
            <div className="form-group">
              <label className="form-label">Descripción</label>
              <textarea className="form-control" rows={3} value={form.descripcion || ''}
                onChange={e => set('descripcion', e.target.value)}
                placeholder="Describe el destino: clima, cultura, atracciones..." />
            </div>
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
            <button type="submit" className="btn btn-primary" disabled={saving}>
              {saving ? 'Guardando...' : (destino ? 'Actualizar' : 'Crear Destino')}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default function DestinosPage() {
  const [destinos,  setDestinos]  = useState([]);
  const [paises,    setPaises]    = useState([]);
  const [loading,   setLoading]   = useState(true);
  const [buscar,    setBuscar]    = useState('');
  const [modal,     setModal]     = useState(null);

  const fetchDestinos = useCallback(async () => {
    setLoading(true);
    try {
      const params = buscar ? `?buscar=${encodeURIComponent(buscar)}` : '';
      const [d, p] = await Promise.all([
        api.get(`/destinos/crud${params}`),
        api.get('/paises'),
      ]);
      setDestinos(d.data);
      setPaises(p.data);
    } catch(e) { console.error(e); }
    finally { setLoading(false); }
  }, [buscar]);

  useEffect(() => { fetchDestinos(); }, [fetchDestinos]);

  const toggleActivo = async (d) => {
    await api.delete(`/destinos/${d.id}`);
    toast.success(d.activo ? 'Destino desactivado' : 'Destino activado');
    fetchDestinos();
  };

  return (
    <div className="animate-fade-in">
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Destinos</h1>
          <p className="page-subtitle">{destinos.length} destinos registrados</p>
        </div>
        <button className="btn btn-primary" onClick={() => setModal('crear')}>
          <Plus size={16} /> Nuevo Destino
        </button>
      </div>

      {/* Búsqueda */}
      <div style={{ marginBottom: 24, display: 'flex', gap: 12 }}>
        <div className="table-search" style={{ flex: 1, maxWidth: 360 }}>
          <Search size={14} style={{ color: 'var(--text-muted)', flexShrink: 0 }} />
          <input
            placeholder="Buscar destino..."
            value={buscar}
            onChange={e => setBuscar(e.target.value)}
          />
        </div>
      </div>

      {loading ? (
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill,minmax(280px,1fr))', gap: 20 }}>
          {Array.from({length: 6}).map((_,i) => (
            <div key={i} className="card" style={{ padding: 0, overflow: 'hidden' }}>
              <div className="skeleton" style={{ height: 160 }} />
              <div style={{ padding: 20 }}>
                <div className="skeleton" style={{ height: 20, marginBottom: 8 }} />
                <div className="skeleton" style={{ height: 14, width: '60%' }} />
              </div>
            </div>
          ))}
        </div>
      ) : destinos.length === 0 ? (
        <div className="empty-state" style={{ padding: '80px 20px' }}>
          <MapPin size={48} className="empty-state-icon" />
          <p className="empty-state-title">No hay destinos registrados</p>
          <p className="empty-state-desc">Crea el primer destino del sistema</p>
          <button className="btn btn-primary" style={{ marginTop: 16 }} onClick={() => setModal('crear')}>
            <Plus size={16} /> Nuevo Destino
          </button>
        </div>
      ) : (
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill,minmax(280px,1fr))', gap: 20 }}>
          {destinos.map((d, idx) => (
            <div key={d.id} className="card" style={{ padding: 0, overflow: 'hidden', opacity: d.activo ? 1 : 0.6 }}>
              {/* Portada */}
              <div style={{
                height: 160,
                background: d.imagen_url
                  ? `linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5)), url(${d.imagen_url}) center/cover`
                  : destinoColors[idx % destinoColors.length],
                display: 'flex', alignItems: 'center', justifyContent: 'center',
                position: 'relative', fontSize: '4rem'
              }}>
                {!d.imagen_url && destinoEmojis[idx % destinoEmojis.length]}

                {/* País badge */}
                <div style={{ position: 'absolute', bottom: 12, left: 12, display: 'flex', gap: 6 }}>
                  {d.pais && (
                    <span className="badge badge-blue" style={{ background: 'rgba(0,0,0,0.5)', backdropFilter: 'blur(4px)' }}>
                      <Globe size={10} style={{ marginRight: 3 }} />
                      {d.pais.nombre}
                    </span>
                  )}
                  {!d.activo && (
                    <span className="badge badge-gray" style={{ background: 'rgba(0,0,0,0.5)' }}>Inactivo</span>
                  )}
                </div>

                {/* Botones acción */}
                <div style={{ position: 'absolute', top: 12, right: 12, display: 'flex', gap: 6 }}>
                  <button
                    className="btn btn-ghost btn-icon btn-sm"
                    style={{ background: 'rgba(0,0,0,0.4)', backdropFilter: 'blur(4px)', color: 'white' }}
                    onClick={() => setModal(d)}
                    title="Editar"
                  >
                    <Edit2 size={13} />
                  </button>
                  <button
                    className="btn btn-ghost btn-icon btn-sm"
                    style={{ background: 'rgba(0,0,0,0.4)', backdropFilter: 'blur(4px)', color: d.activo ? '#10B981' : '#9CA3AF' }}
                    onClick={() => toggleActivo(d)}
                    title={d.activo ? 'Desactivar' : 'Activar'}
                  >
                    {d.activo ? <ToggleRight size={16} /> : <ToggleLeft size={16} />}
                  </button>
                </div>
              </div>

              {/* Info */}
              <div style={{ padding: 20 }}>
                <div className="font-bold" style={{ fontSize: '1rem', marginBottom: 6 }}>{d.nombre}</div>
                {d.descripcion && (
                  <div className="text-sm text-muted" style={{ lineHeight: 1.5 }}>
                    {d.descripcion.substring(0, 100)}{d.descripcion.length > 100 ? '...' : ''}
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>
      )}

      {(modal === 'crear' || (modal && modal.id)) && (
        <DestinoModal
          destino={modal === 'crear' ? null : modal}
          paises={paises}
          onClose={() => setModal(null)}
          onSaved={() => { setModal(null); fetchDestinos(); }}
        />
      )}
    </div>
  );
}
