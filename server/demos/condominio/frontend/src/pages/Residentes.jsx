import { useState, useEffect } from 'react';
import api from '../services/api';

const tipoColors = { propietario: 'purple', inquilino: 'blue', familiar: 'green', dependiente: 'amber' };

export default function Residentes() {
  const [residentes, setResidentes] = useState([]);
  const [unidades, setUnidades] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editItem, setEditItem] = useState(null);
  const [filtroTipo, setFiltroTipo] = useState('');
  
  const [form, setForm] = useState({
    nombre: '', apellidos: '', email: '', telefono: '', 
    unidad_id: '', tipo: 'propietario', documento_id: '',
    fecha_ingreso: new Date().toISOString().split('T')[0]
  });

  const fetchData = async () => {
    setLoading(true);
    try {
      const p = filtroTipo ? `?tipo=${filtroTipo}` : '';
      const [resRes, resUni] = await Promise.all([
        api.get(`/residentes${p}`),
        api.get('/unidades')
      ]);
      setResidentes(resRes.data.data);
      setUnidades(resUni.data.data);
    } catch (err) {
      console.error(err);
    } finally { 
      setLoading(false); 
    }
  };

  useEffect(() => { fetchData(); }, [filtroTipo]);

  const openModal = (item = null) => {
    setEditItem(item);
    if (item) {
      setForm({
        ...item,
        fecha_ingreso: item.fecha_ingreso ? new Date(item.fecha_ingreso).toISOString().split('T')[0] : ''
      });
    } else {
      setForm({
        nombre: '', apellidos: '', email: '', telefono: '', 
        unidad_id: '', tipo: 'propietario', documento_id: '',
        fecha_ingreso: new Date().toISOString().split('T')[0]
      });
    }
    setShowModal(true);
  };

  const handleSave = async () => {
    if (!form.nombre || !form.unidad_id) {
      alert('Nombre y Unidad son obligatorios');
      return;
    }
    
    try {
      if (editItem) { 
        await api.put(`/residentes/${editItem.id}`, form); 
      } else { 
        await api.post('/residentes', form); 
      }
      setShowModal(false);
      fetchData();
    } catch (err) { 
      alert(err.response?.data?.message || 'Error al guardar residente'); 
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('¿Dar de baja a este residente?')) return;
    try {
      await api.delete(`/residentes/${id}`);
      fetchData();
    } catch (err) {
      alert(err.response?.data?.message || 'Error al eliminar');
    }
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <div className="page-title">👥 Residentes</div>
          <div className="page-subtitle">{residentes.length} residentes registrados</div>
        </div>
        <div className="page-actions">
          <button className="btn btn-primary" onClick={() => openModal()}>+ Nuevo Residente</button>
        </div>
      </div>

      {/* Filtros */}
      <div className="filters-bar">
        <span style={{ fontSize: 13, fontWeight: 600, color: 'var(--text-secondary)' }}>Filtrar por tipo:</span>
        {['', 'propietario', 'inquilino', 'familiar', 'dependiente'].map(t => (
          <button key={t} className={`btn btn-sm ${filtroTipo === t ? 'btn-primary' : 'btn-secondary'}`}
            onClick={() => setFiltroTipo(t)}>
            {t || 'Todos'}
          </button>
        ))}
      </div>

      {/* Tabla */}
      <div className="table-container">
        {loading ? (
          <div className="loading-overlay"><div className="spinner" /></div>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Unidad</th>
                <th>Tipo</th>
                <th>Contacto</th>
                <th>Documento</th>
                <th>Ingreso</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              {residentes.map(r => (
                <tr key={r.id}>
                  <td>
                    <strong>{r.nombre} {r.apellidos}</strong>
                  </td>
                  <td>
                    <div style={{ fontWeight: 600 }}>{r.unidad_numero || '—'}</div>
                    <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>{r.torre_nombre}</div>
                  </td>
                  <td><span className={`badge badge-${tipoColors[r.tipo] || 'gray'}`}>{r.tipo}</span></td>
                  <td>
                    <div style={{ fontSize: 12 }}>{r.telefono}</div>
                    <div style={{ fontSize: 12, color: 'var(--text-secondary)' }}>{r.email}</div>
                  </td>
                  <td style={{ fontSize: 13 }}>{r.documento_id || '—'}</td>
                  <td style={{ fontSize: 13 }}>{r.fecha_ingreso ? new Date(r.fecha_ingreso).toLocaleDateString('es-MX') : '—'}</td>
                  <td>
                    <div style={{ display: 'flex', gap: 6 }}>
                      <button className="btn btn-sm btn-secondary" onClick={() => openModal(r)}>✏️</button>
                      <button className="btn btn-sm btn-danger btn-sm" onClick={() => handleDelete(r.id)}>🗑️</button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
        {!loading && residentes.length === 0 && (
          <div className="empty-state">
            <div className="empty-state-icon">👥</div>
            <div className="empty-state-title">Sin residentes registrados</div>
            <button className="btn btn-primary mt-16" onClick={() => openModal()}>+ Agregar residente</button>
          </div>
        )}
      </div>

      {/* Modal CRUD */}
      {showModal && (
        <div className="modal-overlay" onClick={() => setShowModal(false)}>
          <div className="modal" onClick={e => e.stopPropagation()}>
            <div className="modal-header">
              <div className="modal-title">{editItem ? 'Editar Residente' : 'Nuevo Residente'}</div>
              <button className="modal-close" onClick={() => setShowModal(false)}>✕</button>
            </div>
            
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Nombre <span>*</span></label>
                <input className="form-input" value={form.nombre} onChange={e => setForm({...form, nombre: e.target.value})} placeholder="Ej. Juan" />
              </div>
              <div className="form-group">
                <label className="form-label">Apellidos</label>
                <input className="form-input" value={form.apellidos || ''} onChange={e => setForm({...form, apellidos: e.target.value})} placeholder="Ej. Pérez" />
              </div>
            </div>

            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Unidad <span>*</span></label>
                <select className="form-select" value={form.unidad_id} onChange={e => setForm({...form, unidad_id: e.target.value})}>
                  <option value="">Seleccione una unidad...</option>
                  {unidades.map(u => (
                    <option key={u.id} value={u.id}>{u.numero} - {u.torre_nombre || 'Sin torre'}</option>
                  ))}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Tipo <span>*</span></label>
                <select className="form-select" value={form.tipo} onChange={e => setForm({...form, tipo: e.target.value})}>
                  {Object.keys(tipoColors).map(t => <option key={t} value={t}>{t}</option>)}
                </select>
              </div>
            </div>

            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Teléfono</label>
                <input className="form-input" value={form.telefono || ''} onChange={e => setForm({...form, telefono: e.target.value})} />
              </div>
              <div className="form-group">
                <label className="form-label">Email</label>
                <input type="email" className="form-input" value={form.email || ''} onChange={e => setForm({...form, email: e.target.value})} />
              </div>
            </div>

            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Documento ID (INE, etc.)</label>
                <input className="form-input" value={form.documento_id || ''} onChange={e => setForm({...form, documento_id: e.target.value})} />
              </div>
              <div className="form-group">
                <label className="form-label">Fecha de Ingreso</label>
                <input type="date" className="form-input" value={form.fecha_ingreso || ''} onChange={e => setForm({...form, fecha_ingreso: e.target.value})} />
              </div>
            </div>

            <div className="modal-footer">
              <button className="btn btn-secondary" onClick={() => setShowModal(false)}>Cancelar</button>
              <button className="btn btn-primary" onClick={handleSave}>
                {editItem ? '💾 Actualizar' : '✅ Crear Residente'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
