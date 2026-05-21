import { useState, useEffect } from 'react';
import api from '../services/api';

export default function Mantenimiento() {
  const [ordenes, setOrdenes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [filtroEstado, setFiltroEstado] = useState('');
  const [form, setForm] = useState({ tipo: 'correctivo', titulo: '', descripcion: '', prioridad: 'media', unidad_id: '' });

  const fetchOrdenes = async () => {
    setLoading(true);
    try {
      const p = filtroEstado ? `?estado=${filtroEstado}` : '';
      const { data } = await api.get(`/mantenimiento/ordenes${p}`);
      setOrdenes(data.data);
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchOrdenes(); }, [filtroEstado]);

  const prioridadBadge = (p) => {
    const map = { urgente: 'danger', alta: 'warning', media: 'info', baja: 'gray' };
    return <span className={`badge badge-${map[p]}`}>{p}</span>;
  };

  const estadoBadge = (e) => {
    const map = { abierto: 'warning', asignado: 'info', en_progreso: 'purple', completado: 'success', cerrado: 'gray', cancelado: 'danger' };
    return <span className={`badge badge-${map[e] || 'gray'}`}>{e.replace('_', ' ')}</span>;
  };

  const updateEstado = async (id, estado) => {
    await api.put(`/mantenimiento/ordenes/${id}`, { estado });
    fetchOrdenes();
  };

  const handleCreate = async () => {
    try {
      await api.post('/mantenimiento/ordenes', form);
      setShowModal(false);
      fetchOrdenes();
    } catch (err) { alert(err.response?.data?.message || 'Error'); }
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <div className="page-title">🔧 Mantenimiento</div>
          <div className="page-subtitle">{ordenes.length} órdenes de trabajo</div>
        </div>
        <div className="page-actions">
          <button className="btn btn-primary" onClick={() => setShowModal(true)}>+ Nueva Orden</button>
        </div>
      </div>

      <div className="filters-bar">
        {['', 'abierto', 'asignado', 'en_progreso', 'completado', 'cerrado'].map(e => (
          <button key={e} className={`btn btn-sm ${filtroEstado === e ? 'btn-primary' : 'btn-secondary'}`}
            onClick={() => setFiltroEstado(e)}>
            {e || 'Todos'}
          </button>
        ))}
      </div>

      <div className="table-container">
        {loading ? <div className="loading-overlay"><div className="spinner" /></div> : (
          <table>
            <thead>
              <tr>
                <th>Orden</th>
                <th>Tipo</th>
                <th>Prioridad</th>
                <th>Unidad/Área</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              {ordenes.map(o => (
                <tr key={o.id}>
                  <td>
                    <div style={{ fontWeight: 700, fontSize: 14 }}>{o.titulo}</div>
                    <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>{o.descripcion?.slice(0, 60)}...</div>
                  </td>
                  <td><span className="badge badge-info">{o.tipo}</span></td>
                  <td>{prioridadBadge(o.prioridad)}</td>
                  <td>{o.unidad_numero || o.area_nombre || '—'}</td>
                  <td>{estadoBadge(o.estado)}</td>
                  <td style={{ fontSize: 12 }}>{new Date(o.fecha_reporte).toLocaleDateString('es-MX')}</td>
                  <td>
                    <select className="form-select" style={{ padding: '5px 8px', fontSize: 12 }}
                      value={o.estado}
                      onChange={e => updateEstado(o.id, e.target.value)}>
                      {['abierto','asignado','en_progreso','completado','cerrado'].map(s => (
                        <option key={s} value={s}>{s.replace('_', ' ')}</option>
                      ))}
                    </select>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
        {!loading && ordenes.length === 0 && (
          <div className="empty-state">
            <div className="empty-state-icon">🔧</div>
            <div className="empty-state-title">Sin órdenes de trabajo</div>
          </div>
        )}
      </div>

      {showModal && (
        <div className="modal-overlay" onClick={() => setShowModal(false)}>
          <div className="modal" onClick={e => e.stopPropagation()}>
            <div className="modal-header">
              <div className="modal-title">Nueva Orden de Trabajo</div>
              <button className="modal-close" onClick={() => setShowModal(false)}>✕</button>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Tipo</label>
                <select className="form-select" value={form.tipo} onChange={e => setForm({...form, tipo: e.target.value})}>
                  {['correctivo','preventivo','emergencia'].map(t => <option key={t} value={t}>{t}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Prioridad</label>
                <select className="form-select" value={form.prioridad} onChange={e => setForm({...form, prioridad: e.target.value})}>
                  {['baja','media','alta','urgente'].map(p => <option key={p} value={p}>{p}</option>)}
                </select>
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Título <span>*</span></label>
              <input className="form-input" value={form.titulo} onChange={e => setForm({...form, titulo: e.target.value})} placeholder="Descripción breve del problema" />
            </div>
            <div className="form-group">
              <label className="form-label">Descripción detallada</label>
              <textarea className="form-textarea" value={form.descripcion} onChange={e => setForm({...form, descripcion: e.target.value})} />
            </div>
            <div className="modal-footer">
              <button className="btn btn-secondary" onClick={() => setShowModal(false)}>Cancelar</button>
              <button className="btn btn-primary" onClick={handleCreate}>✅ Crear Orden</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
