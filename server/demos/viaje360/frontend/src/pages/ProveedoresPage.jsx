import React, { useEffect, useState, useCallback } from 'react';
import { Plus, Edit2, Truck, Search, ToggleLeft, ToggleRight, Globe, Phone, Mail } from 'lucide-react';
import api from '../services/api';
import toast from 'react-hot-toast';

const tipoColor = {
  'Aerolínea': 'blue', 'Hotel': 'purple', 'Operadora': 'green',
  'Seguro': 'amber', 'Transporte': 'cyan', 'Otro': 'gray'
};

const tipoIcon = {
  'Aerolínea': '✈️', 'Hotel': '🏨', 'Operadora': '🗺️',
  'Seguro': '🛡️', 'Transporte': '🚌', 'Otro': '📦'
};

function ProveedorModal({ proveedor, onClose, onSaved }) {
  const [form, setForm] = useState(proveedor || {
    nombre: '', tipo: 'Hotel', contacto: '', email: '', telefono: '',
    pais: '', sitio_web: '', notas: '', activo: 1
  });
  const [saving, setSaving] = useState(false);
  const set = (k, v) => setForm(f => ({ ...f, [k]: v }));

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      if (proveedor?.id) await api.put(`/proveedores/${proveedor.id}`, form);
      else await api.post('/proveedores', form);
      toast.success(proveedor ? 'Proveedor actualizado' : 'Proveedor creado');
      onSaved();
    } catch(e) {
      toast.error(e.msg || 'Error al guardar');
    } finally { setSaving(false); }
  };

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal modal-lg animate-fade-in-up">
        <div className="modal-header">
          <h2 className="modal-title">{proveedor ? 'Editar Proveedor' : 'Nuevo Proveedor'}</h2>
          <button className="modal-close" onClick={onClose}>✕</button>
        </div>
        <form onSubmit={handleSubmit}>
          <div className="modal-body">
            <div className="form-row">
              <div className="form-group">
                <label className="form-label required">Nombre</label>
                <input className="form-control" value={form.nombre} required
                  onChange={e => set('nombre', e.target.value)}
                  placeholder="Ej: Latam Airlines, Marriott, etc." />
              </div>
              <div className="form-group">
                <label className="form-label required">Tipo</label>
                <select className="form-control" value={form.tipo} onChange={e => set('tipo', e.target.value)}>
                  {['Aerolínea','Hotel','Operadora','Seguro','Transporte','Otro'].map(t =>
                    <option key={t}>{t}</option>
                  )}
                </select>
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Contacto Principal</label>
                <input className="form-control" value={form.contacto || ''}
                  onChange={e => set('contacto', e.target.value)}
                  placeholder="Nombre del ejecutivo de cuenta" />
              </div>
              <div className="form-group">
                <label className="form-label">País</label>
                <input className="form-control" value={form.pais || ''}
                  onChange={e => set('pais', e.target.value)}
                  placeholder="Ej: Perú, Chile, EEUU..." />
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Email</label>
                <input type="email" className="form-control" value={form.email || ''}
                  onChange={e => set('email', e.target.value)} />
              </div>
              <div className="form-group">
                <label className="form-label">Teléfono</label>
                <input className="form-control" value={form.telefono || ''}
                  onChange={e => set('telefono', e.target.value)} />
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Sitio Web</label>
              <input className="form-control" value={form.sitio_web || ''}
                onChange={e => set('sitio_web', e.target.value)}
                placeholder="https://..." />
            </div>
            <div className="form-group">
              <label className="form-label">Notas</label>
              <textarea className="form-control" rows={3} value={form.notas || ''}
                onChange={e => set('notas', e.target.value)}
                placeholder="Condiciones, tarifas especiales, notas de negociación..." />
            </div>
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
            <button type="submit" className="btn btn-primary" disabled={saving}>
              {saving ? 'Guardando...' : (proveedor ? 'Actualizar' : 'Crear Proveedor')}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default function ProveedoresPage() {
  const [proveedores, setProveedores] = useState([]);
  const [loading,     setLoading]     = useState(true);
  const [buscar,      setBuscar]      = useState('');
  const [tipoFiltro,  setTipoFiltro]  = useState('');
  const [modal,       setModal]       = useState(null);

  const fetchProveedores = useCallback(async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams();
      if (buscar) params.set('buscar', buscar);
      if (tipoFiltro) params.set('tipo', tipoFiltro);
      const res = await api.get(`/proveedores?${params}`);
      setProveedores(res.data);
    } catch(e) { console.error(e); }
    finally { setLoading(false); }
  }, [buscar, tipoFiltro]);

  useEffect(() => { fetchProveedores(); }, [fetchProveedores]);

  const toggleActivo = async (p) => {
    await api.delete(`/proveedores/${p.id}`);
    toast.success(p.activo ? 'Proveedor desactivado' : 'Proveedor activado');
    fetchProveedores();
  };

  return (
    <div className="animate-fade-in">
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Proveedores</h1>
          <p className="page-subtitle">{proveedores.length} proveedores en el directorio</p>
        </div>
        <button className="btn btn-primary" onClick={() => setModal('crear')}>
          <Plus size={16} /> Nuevo Proveedor
        </button>
      </div>

      <div className="table-wrapper">
        <div className="table-header">
          <div className="table-controls">
            <div className="table-search">
              <Search size={14} style={{ color: 'var(--text-muted)', flexShrink: 0 }} />
              <input
                placeholder="Buscar proveedor..."
                value={buscar}
                onChange={e => { setBuscar(e.target.value); }}
              />
            </div>
            <select className="table-filter" value={tipoFiltro} onChange={e => setTipoFiltro(e.target.value)}>
              <option value="">Todos los tipos</option>
              {['Aerolínea','Hotel','Operadora','Seguro','Transporte','Otro'].map(t =>
                <option key={t}>{t}</option>
              )}
            </select>
          </div>
          <span className="text-sm text-muted">{proveedores.length} resultados</span>
        </div>

        {loading ? (
          <div style={{ padding: '40px', textAlign: 'center' }}>
            <div className="spinner" style={{ margin: '0 auto' }} />
          </div>
        ) : proveedores.length === 0 ? (
          <div className="empty-state">
            <Truck size={48} className="empty-state-icon" />
            <p className="empty-state-title">No hay proveedores registrados</p>
            <p className="empty-state-desc">Agrega aerolíneas, hoteles y operadoras con las que trabajas</p>
          </div>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Proveedor</th>
                <th>Tipo</th>
                <th>Contacto</th>
                <th>País</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              {proveedores.map(p => (
                <tr key={p.id} style={{ opacity: p.activo ? 1 : 0.6 }}>
                  <td>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                      <div style={{
                        width: 40, height: 40, borderRadius: 8,
                        background: 'var(--bg-input)',
                        display: 'flex', alignItems: 'center', justifyContent: 'center',
                        fontSize: '1.4rem', flexShrink: 0
                      }}>
                        {tipoIcon[p.tipo] || '📦'}
                      </div>
                      <div>
                        <div className="font-semibold text-sm">{p.nombre}</div>
                        {p.sitio_web && (
                          <a href={p.sitio_web} target="_blank" rel="noopener noreferrer"
                            className="text-xs text-muted" style={{ color: 'var(--color-primary)' }}>
                            <Globe size={10} style={{ display: 'inline', marginRight: 2 }} />
                            {p.sitio_web.replace(/^https?:\/\//, '').substring(0, 30)}
                          </a>
                        )}
                      </div>
                    </div>
                  </td>
                  <td>
                    <span className={`badge badge-${tipoColor[p.tipo] || 'gray'}`}>
                      {tipoIcon[p.tipo]} {p.tipo}
                    </span>
                  </td>
                  <td>
                    {p.contacto && <div className="text-sm font-semibold">{p.contacto}</div>}
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 2, marginTop: 2 }}>
                      {p.email && (
                        <a href={`mailto:${p.email}`} className="text-xs text-muted" style={{ display: 'flex', alignItems: 'center', gap: 3 }}>
                          <Mail size={10} /> {p.email}
                        </a>
                      )}
                      {p.telefono && (
                        <span className="text-xs text-muted" style={{ display: 'flex', alignItems: 'center', gap: 3 }}>
                          <Phone size={10} /> {p.telefono}
                        </span>
                      )}
                    </div>
                  </td>
                  <td className="text-sm">{p.pais || '—'}</td>
                  <td>
                    <span className={`badge badge-${p.activo ? 'green' : 'gray'}`}>
                      {p.activo ? 'Activo' : 'Inactivo'}
                    </span>
                  </td>
                  <td>
                    <div className="td-actions">
                      <button className="btn btn-ghost btn-icon btn-sm" title="Editar" onClick={() => setModal(p)}>
                        <Edit2 size={14} />
                      </button>
                      <button
                        className="btn btn-ghost btn-icon btn-sm"
                        title={p.activo ? 'Desactivar' : 'Activar'}
                        style={{ color: p.activo ? 'var(--color-danger)' : 'var(--color-success)' }}
                        onClick={() => toggleActivo(p)}
                      >
                        {p.activo ? <ToggleRight size={16} /> : <ToggleLeft size={16} />}
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>

      {(modal === 'crear' || (modal && modal.id)) && (
        <ProveedorModal
          proveedor={modal === 'crear' ? null : modal}
          onClose={() => setModal(null)}
          onSaved={() => { setModal(null); fetchProveedores(); }}
        />
      )}
    </div>
  );
}
