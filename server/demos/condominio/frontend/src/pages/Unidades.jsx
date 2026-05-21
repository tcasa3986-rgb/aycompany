import { useState, useEffect } from 'react';
import api from '../services/api';

const estadoColors = { habitada: 'success', vacía: 'gray', en_venta: 'warning', en_renta: 'info', en_construccion: 'purple' };

export default function Unidades() {
  const [unidades, setUnidades] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editItem, setEditItem] = useState(null);
  const [filtroEstado, setFiltroEstado] = useState('');
  const [form, setForm] = useState({ torre_id: '', numero: '', piso: 1, tipo: 'departamento', metros_cuadrados: '', estado: 'vacía' });

  const fetchUnidades = async () => {
    setLoading(true);
    try {
      const p = filtroEstado ? `?estado=${filtroEstado}` : '';
      const { data } = await api.get(`/unidades${p}`);
      setUnidades(data.data);
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchUnidades(); }, [filtroEstado]);

  const openModal = (item = null) => {
    setEditItem(item);
    setForm(item ? { ...item } : { torre_id: 1, numero: '', piso: 1, tipo: 'departamento', metros_cuadrados: '', estado: 'vacía' });
    setShowModal(true);
  };

  const handleSave = async () => {
    try {
      if (editItem) { await api.put(`/unidades/${editItem.id}`, form); }
      else { await api.post('/unidades', form); }
      setShowModal(false);
      fetchUnidades();
    } catch (err) { alert(err.response?.data?.message || 'Error'); }
  };

  const handleDelete = async (id) => {
    if (!confirm('¿Eliminar esta unidad?')) return;
    await api.delete(`/unidades/${id}`);
    fetchUnidades();
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <div className="page-title">🏠 Unidades</div>
          <div className="page-subtitle">{unidades.length} unidades registradas</div>
        </div>
        <div className="page-actions">
          <button className="btn btn-primary" onClick={() => openModal()}>+ Nueva Unidad</button>
        </div>
      </div>

      {/* Filtros */}
      <div className="filters-bar">
        <span style={{ fontSize: 13, fontWeight: 600, color: 'var(--text-secondary)' }}>Filtrar por estado:</span>
        {['', 'habitada', 'vacía', 'en_venta', 'en_renta'].map(e => (
          <button key={e} className={`btn btn-sm ${filtroEstado === e ? 'btn-primary' : 'btn-secondary'}`}
            onClick={() => setFiltroEstado(e)}>
            {e || 'Todos'}
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
                <th>Unidad</th>
                <th>Torre</th>
                <th>Piso</th>
                <th>Tipo</th>
                <th>M²</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              {unidades.map(u => (
                <tr key={u.id}>
                  <td><strong>{u.numero}</strong></td>
                  <td>{u.torre_nombre}</td>
                  <td>{u.piso}</td>
                  <td style={{ textTransform: 'capitalize' }}>{u.tipo}</td>
                  <td>{u.metros_cuadrados ? `${u.metros_cuadrados} m²` : '—'}</td>
                  <td><span className={`badge badge-${estadoColors[u.estado] || 'gray'}`}>{u.estado}</span></td>
                  <td>
                    <div style={{ display: 'flex', gap: 6 }}>
                      <button className="btn btn-sm btn-secondary" onClick={() => openModal(u)}>✏️</button>
                      <button className="btn btn-sm btn-danger btn-sm" onClick={() => handleDelete(u.id)}>🗑️</button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
        {!loading && unidades.length === 0 && (
          <div className="empty-state">
            <div className="empty-state-icon">🏠</div>
            <div className="empty-state-title">Sin unidades registradas</div>
            <button className="btn btn-primary mt-16" onClick={() => openModal()}>+ Agregar unidad</button>
          </div>
        )}
      </div>

      {/* Modal */}
      {showModal && (
        <div className="modal-overlay" onClick={() => setShowModal(false)}>
          <div className="modal" onClick={e => e.stopPropagation()}>
            <div className="modal-header">
              <div className="modal-title">{editItem ? 'Editar Unidad' : 'Nueva Unidad'}</div>
              <button className="modal-close" onClick={() => setShowModal(false)}>✕</button>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Número <span>*</span></label>
                <input className="form-input" value={form.numero} onChange={e => setForm({...form, numero: e.target.value})} placeholder="A-101" />
              </div>
              <div className="form-group">
                <label className="form-label">Piso</label>
                <input className="form-input" type="number" value={form.piso} onChange={e => setForm({...form, piso: e.target.value})} />
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Tipo</label>
                <select className="form-select" value={form.tipo} onChange={e => setForm({...form, tipo: e.target.value})}>
                  {['departamento','casa','local','bodega'].map(t => <option key={t} value={t}>{t}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Estado</label>
                <select className="form-select" value={form.estado} onChange={e => setForm({...form, estado: e.target.value})}>
                  {['habitada','vacía','en_venta','en_renta','en_construccion'].map(s => <option key={s} value={s}>{s}</option>)}
                </select>
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Metros cuadrados</label>
              <input className="form-input" type="number" value={form.metros_cuadrados} onChange={e => setForm({...form, metros_cuadrados: e.target.value})} placeholder="85.00" />
            </div>
            <div className="modal-footer">
              <button className="btn btn-secondary" onClick={() => setShowModal(false)}>Cancelar</button>
              <button className="btn btn-primary" onClick={handleSave}>
                {editItem ? '💾 Actualizar' : '✅ Crear Unidad'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
