import React, { useEffect, useState } from 'react';
import { Plus, Package, DollarSign, Clock, Edit2, Trash2, Search } from 'lucide-react';
import api from '../services/api';
import useConfigStore from '../store/configStore';


function PaqueteModal({ paquete, destinos, categorias, onClose, onSaved }) {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const [form, setForm] = useState(paquete || {

    destino_id:'', categoria_id:'', nombre:'', descripcion:'',
    duracion_dias:'', precio_base:'', precio_adulto:'', precio_nino:'',
    costo_neto:'',
    incluye:'', no_incluye:'', disponible:1
  });
  const [saving, setSaving] = useState(false);
  const set = (k, v) => setForm(f => ({ ...f, [k]: v }));

  const handleSubmit = async (e) => {
    e.preventDefault(); setSaving(true);
    try {
      if (paquete?.id) await api.put(`/paquetes/${paquete.id}`, form);
      else await api.post('/paquetes', form);
      onSaved();
    } catch(e) { console.error(e); }
    finally { setSaving(false); }
  };

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal modal-xl animate-fade-in-up">
        <div className="modal-header">
          <h2 className="modal-title">{paquete ? 'Editar Paquete' : 'Nuevo Paquete'}</h2>
          <button className="modal-close" onClick={onClose}>✕</button>
        </div>
        <form onSubmit={handleSubmit}>
          <div className="modal-body">
            <div className="form-group">
              <label className="form-label required">Nombre del Paquete</label>
              <input className="form-control" value={form.nombre} required
                onChange={e=>set('nombre',e.target.value)}
                placeholder="Ej: Luna de Miel en Maldivas 7D/6N" />
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label required">Destino</label>
                <select className="form-control" value={form.destino_id} required onChange={e=>set('destino_id',e.target.value)}>
                  <option value="">Seleccionar</option>
                  {destinos.map(d=><option key={d.id} value={d.id}>{d.nombre} ({d.pais?.nombre})</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Categoría</label>
                <select className="form-control" value={form.categoria_id} onChange={e=>set('categoria_id',e.target.value)}>
                  <option value="">Seleccionar</option>
                  {categorias.map(c=><option key={c.id} value={c.id}>{c.nombre}</option>)}
                </select>
              </div>
            </div>
            <div className="form-row-3">
              <div className="form-group">
                <label className="form-label required">Precio Base ({m})</label>

                <input type="number" className="form-control" value={form.precio_base} required onChange={e=>set('precio_base',e.target.value)} />
              </div>
              <div className="form-group">
                <label className="form-label">Precio Adulto</label>
                <input type="number" className="form-control" value={form.precio_adulto} onChange={e=>set('precio_adulto',e.target.value)} />
              </div>
              <div className="form-group">
                <label className="form-label">Precio Niño</label>
                <input type="number" className="form-control" value={form.precio_nino} onChange={e=>set('precio_nino',e.target.value)} />
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Costo Neto Proveedor ({m})</label>

                <input type="number" className="form-control" value={form.costo_neto || ''} onChange={e=>set('costo_neto',e.target.value)}
                  placeholder="Costo real al proveedor" style={{ borderColor: 'var(--color-warning)' }} />
              </div>
              <div className="form-group">
                <label className="form-label">Margen Estimado</label>
                <div className="form-control" style={{
                  background: 'var(--bg-card)', display:'flex', alignItems:'center',
                  color: 'var(--color-success)', fontWeight: 700
                }}>
                  {form.precio_base && form.costo_neto
                    ? `${m}${(+form.precio_base - +form.costo_neto).toFixed(2)} (${Math.round(((+form.precio_base - +form.costo_neto) / +form.precio_base) * 100)}%)`
                    : '—'}

                </div>
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Duración (días)</label>
                <input type="number" className="form-control" value={form.duracion_dias} onChange={e=>set('duracion_dias',e.target.value)} />
              </div>
              <div className="form-group">
                <label className="form-label">Estado</label>
                <select className="form-control" value={form.disponible} onChange={e=>set('disponible',+e.target.value)}>
                  <option value={1}>Disponible</option>
                  <option value={0}>No disponible</option>
                </select>
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Descripción</label>
              <textarea className="form-control" rows={3} value={form.descripcion} onChange={e=>set('descripcion',e.target.value)} />
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">¿Qué Incluye?</label>
                <textarea className="form-control" rows={3} value={form.incluye} onChange={e=>set('incluye',e.target.value)}
                  placeholder="Vuelos, hotel, desayuno..." />
              </div>
              <div className="form-group">
                <label className="form-label">No Incluye</label>
                <textarea className="form-control" rows={3} value={form.no_incluye} onChange={e=>set('no_incluye',e.target.value)}
                  placeholder="Seguro de viaje, visado..." />
              </div>
            </div>
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
            <button type="submit" className="btn btn-primary" disabled={saving}>
              {saving ? 'Guardando...' : (paquete ? 'Actualizar' : 'Crear Paquete')}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default function PaquetesPage() {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const [paquetes,   setPaquetes]   = useState([]);

  const [destinos,   setDestinos]   = useState([]);
  const [categorias, setCategorias] = useState([]);
  const [loading,    setLoading]    = useState(true);
  const [buscar,     setBuscar]     = useState('');
  const [modal,      setModal]      = useState(null);

  const fetch = async () => {
    setLoading(true);
    try {
      const params = buscar ? `?buscar=${buscar}` : '';
      const [p, d, c] = await Promise.all([
        api.get(`/paquetes${params}`),
        api.get('/destinos'),
        api.get('/categorias-paquete'),
      ]);
      setPaquetes(p.data); setDestinos(d.data); setCategorias(c.data);
    } catch(e) { console.error(e); }
    finally { setLoading(false); }
  };

  useEffect(() => { fetch(); }, [buscar]);

  const toggleDisponible = async (p) => {
    await api.put(`/paquetes/${p.id}`, { ...p, disponible: p.disponible ? 0 : 1 });
    fetch();
  };

  return (
    <div className="animate-fade-in">
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Paquetes Turísticos</h1>
          <p className="page-subtitle">{paquetes.length} paquetes en catálogo</p>
        </div>
        <button className="btn btn-primary" onClick={() => setModal('crear')}>
          <Plus size={16} /> Nuevo Paquete
        </button>
      </div>

      {/* Cards de paquetes */}
      <div className="table-wrapper" style={{ background:'none', border:'none' }}>
        <div style={{ display:'flex', gap:10, marginBottom:20 }}>
          <div className="table-search">
            <Search size={14} style={{ color:'var(--text-muted)', flexShrink:0 }} />
            <input placeholder="Buscar paquetes..." value={buscar} onChange={e=>setBuscar(e.target.value)} />
          </div>
        </div>

        {loading ? (
          <div style={{ textAlign:'center', padding:40 }}><div className="spinner" style={{ margin:'0 auto' }}/></div>
        ) : (
          <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(320px,1fr))', gap:20 }}>
            {paquetes.map(p => (
              <div key={p.id} className="card" style={{ padding:0, overflow:'hidden' }}>
                <div style={{
                  height:140,
                  background: `linear-gradient(135deg, #0B0F1A, ${p.disponible ? '#0369A1' : '#374151'})`,
                  display:'flex', alignItems:'center', justifyContent:'center', position:'relative'
                }}>
                  <Package size={48} style={{ color:'rgba(255,255,255,0.2)' }} />
                  {!p.disponible && (
                    <div style={{
                      position:'absolute', top:12, right:12,
                      background:'var(--color-danger)', color:'white',
                      borderRadius:4, padding:'2px 8px', fontSize:'0.7rem', fontWeight:700
                    }}>SIN STOCK</div>
                  )}
                  <div style={{ position:'absolute', bottom:12, left:12 }}>
                    {p.categoria && <span className="badge badge-blue">{p.categoria.nombre}</span>}
                  </div>
                </div>
                <div style={{ padding:20 }}>
                  <div className="font-bold" style={{ fontSize:'1rem', marginBottom:6 }}>{p.nombre}</div>
                  <div className="text-sm text-muted" style={{ marginBottom:12 }}>
                    📍 {p.destino?.nombre} · {p.destino?.pais?.nombre}
                  </div>
                  <div style={{ display:'flex', gap:16, marginBottom:14 }}>
                    <div>
                      <div className="text-xs text-muted">Precio base</div>
                      <div className="font-bold" style={{ color:'var(--color-success)', fontSize:'1.1rem' }}>
                        {m}{(+p.precio_base).toLocaleString('es')}
                      </div>

                    </div>
                    {p.costo_neto > 0 && (
                      <div>
                        <div className="text-xs text-muted">Margen</div>
                        <div className="font-semibold text-sm" style={{ color:'var(--color-primary)' }}>
                          {m}{(+p.precio_base - +p.costo_neto).toFixed(0)} ({Math.round(((+p.precio_base - +p.costo_neto) / +p.precio_base) * 100)}%)
                        </div>

                      </div>
                    )}
                    {p.duracion_dias && (
                      <div>
                        <div className="text-xs text-muted">Duración</div>
                        <div className="font-semibold text-sm">{p.duracion_dias} días</div>
                      </div>
                    )}
                  </div>
                  {p.descripcion && (
                    <div className="text-xs text-muted" style={{ marginBottom:14, lineHeight:1.5 }}>
                      {p.descripcion.substring(0,100)}{p.descripcion.length > 100 ? '...' : ''}
                    </div>
                  )}
                  <div style={{ display:'flex', gap:8 }}>
                    <button className="btn btn-secondary btn-sm" style={{flex:1}} onClick={() => setModal(p)}>
                      <Edit2 size={13}/> Editar
                    </button>
                    <button
                      className={`btn btn-sm ${p.disponible ? 'btn-danger' : 'btn-primary'}`}
                      style={{flex:1}} onClick={() => toggleDisponible(p)}>
                      {p.disponible ? 'Deshabilitar' : 'Habilitar'}
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {(modal === 'crear' || (modal && modal.id)) && (
        <PaqueteModal
          paquete={modal === 'crear' ? null : modal}
          destinos={destinos} categorias={categorias}
          onClose={() => setModal(null)}
          onSaved={() => { setModal(null); fetch(); }}
        />
      )}
    </div>
  );
}
