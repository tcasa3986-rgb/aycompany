import { useState, useEffect } from 'react';
import api from '../services/api';

const estadoColors = { pendiente: 'amber', confirmada: 'green', completada: 'blue', cancelada: 'red' };

export default function Amenidades() {
  const [amenidades, setAmenidades] = useState([]);
  const [reservaciones, setReservaciones] = useState([]);
  const [unidades, setUnidades] = useState([]);
  const [residentes, setResidentes] = useState([]);
  const [loading, setLoading] = useState(true);
  
  const [showModal, setShowModal] = useState(false);
  const [editItem, setEditItem] = useState(null);
  
  const [form, setForm] = useState({
    amenidad_id: '', unidad_id: '', residente_id: '',
    fecha: new Date().toISOString().split('T')[0],
    hora_inicio: '10:00', hora_fin: '12:00',
    num_personas: 1, notas: '', estado: 'confirmada'
  });

  const fetchData = async () => {
    setLoading(true);
    try {
      const [resAm, resRv, resUni, resRes] = await Promise.all([
        api.get('/amenidades'),
        api.get('/amenidades/reservaciones'),
        api.get('/unidades'),
        api.get('/residentes')
      ]);
      setAmenidades(resAm.data.data);
      setReservaciones(resRv.data.data);
      setUnidades(resUni.data.data);
      setResidentes(resRes.data.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchData(); }, []);

  const openModal = (item = null) => {
    setEditItem(item);
    if (item) {
      setForm({ ...item, fecha: new Date(item.fecha).toISOString().split('T')[0] });
    } else {
      setForm({
        amenidad_id: amenidades[0]?.id || '', unidad_id: '', residente_id: '',
        fecha: new Date().toISOString().split('T')[0],
        hora_inicio: '10:00', hora_fin: '12:00',
        num_personas: 1, notas: '', estado: 'confirmada'
      });
    }
    setShowModal(true);
  };

  const handleSave = async () => {
    if (!form.amenidad_id || !form.unidad_id || !form.fecha || !form.hora_inicio || !form.hora_fin) {
      alert('Favor de completar todos los campos requeridos (*)');
      return;
    }
    try {
      if (editItem) {
        await api.put(`/amenidades/reservaciones/${editItem.id}`, { estado: form.estado, notas: form.notas });
      } else {
        await api.post('/amenidades/reservaciones', form);
      }
      setShowModal(false);
      fetchData();
    } catch (err) {
      alert(err.response?.data?.message || 'Error al guardar reservación');
    }
  };

  const formatTime = (timeStr) => {
    if (!timeStr) return '';
    return timeStr.substring(0, 5); // '10:00:00' -> '10:00'
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <div className="page-title">🏋️ Amenidades</div>
          <div className="page-subtitle">Reservaciones y control de uso</div>
        </div>
        <div className="page-actions">
          <button className="btn btn-primary" onClick={() => openModal()}>+ Nueva Reservación</button>
        </div>
      </div>

      <div className="grid-4" style={{ marginBottom: 24 }}>
        {amenidades.map(a => (
          <div className="card" key={a.id} style={{ display: 'flex', alignItems: 'center', gap: 16 }}>
            <div style={{ fontSize: 32, width: 50, height: 50, background: 'var(--bg-main)', borderRadius: 10, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
              {a.nombre.toLowerCase().includes('alberca') ? '🏊' : a.nombre.toLowerCase().includes('gym') || a.nombre.toLowerCase().includes('gimnasio') ? '🏋️' : '🎪'}
            </div>
            <div>
              <div style={{ fontWeight: 600 }}>{a.nombre}</div>
              <div style={{ fontSize: 12, color: 'var(--text-secondary)' }}>Max: {a.capacidad_max} per.</div>
            </div>
          </div>
        ))}
      </div>

      <div className="table-container">
        <div className="p-16 border-b"><h3 style={{ margin: 0 }}>Historial de Reservaciones</h3></div>
        {loading ? (
          <div className="loading-overlay"><div className="spinner" /></div>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Fecha y Hora</th>
                <th>Amenidad</th>
                <th>Unidad</th>
                <th>Solicitante</th>
                <th>Personas</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              {reservaciones.map(r => (
                <tr key={r.id}>
                  <td>
                    <strong>{new Date(r.fecha).toLocaleDateString('es-MX')}</strong>
                    <div style={{ fontSize: 12, color: 'var(--text-secondary)' }}>
                      {formatTime(r.hora_inicio)} - {formatTime(r.hora_fin)}
                    </div>
                  </td>
                  <td><strong>{r.amenidad_nombre}</strong></td>
                  <td>{r.unidad_numero || '—'}</td>
                  <td>{r.residente_nombre || '—'}</td>
                  <td>{r.num_personas} per.</td>
                  <td><span className={`badge badge-${estadoColors[r.estado] || 'gray'}`}>{r.estado}</span></td>
                  <td>
                    <button className="btn btn-sm btn-secondary" onClick={() => openModal(r)}>Editar</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
        {!loading && reservaciones.length === 0 && (
          <div className="empty-state">
            <div className="empty-state-icon">📅</div>
            <div className="empty-state-title">No hay reservaciones</div>
            <div className="empty-state-text">Comienza creando la primera reservación de área común.</div>
          </div>
        )}
      </div>

      {showModal && (
        <div className="modal-overlay" onClick={() => setShowModal(false)}>
          <div className="modal" onClick={e => e.stopPropagation()}>
            <div className="modal-header">
              <div className="modal-title">{editItem ? 'Editar Reservación' : 'Nueva Reservación'}</div>
              <button className="modal-close" onClick={() => setShowModal(false)}>✕</button>
            </div>
            
            {!editItem && (
              <>
                <div className="form-group">
                  <label className="form-label">Amenidad <span>*</span></label>
                  <select className="form-select" value={form.amenidad_id} onChange={e => setForm({...form, amenidad_id: e.target.value})}>
                    {amenidades.map(a => <option key={a.id} value={a.id}>{a.nombre} (Max {a.capacidad_max})</option>)}
                  </select>
                </div>
                
                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">Unidad <span>*</span></label>
                    <select className="form-select" value={form.unidad_id} onChange={e => setForm({...form, unidad_id: e.target.value})}>
                      <option value="">Seleccione...</option>
                      {unidades.map(u => <option key={u.id} value={u.id}>{u.numero}</option>)}
                    </select>
                  </div>
                  <div className="form-group">
                    <label className="form-label">Residente (Opcional)</label>
                    <select className="form-select" value={form.residente_id} onChange={e => setForm({...form, residente_id: e.target.value})}>
                      <option value="">Cualquiera en unidad...</option>
                      {residentes.filter(r => r.unidad_id == form.unidad_id).map(r => (
                        <option key={r.id} value={r.id}>{r.nombre} {r.apellidos}</option>
                      ))}
                    </select>
                  </div>
                </div>

                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">Fecha <span>*</span></label>
                    <input type="date" className="form-input" value={form.fecha} onChange={e => setForm({...form, fecha: e.target.value})} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">N° Personas</label>
                    <input type="number" min="1" className="form-input" value={form.num_personas} onChange={e => setForm({...form, num_personas: e.target.value})} />
                  </div>
                </div>

                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">Hora Inicio <span>*</span></label>
                    <input type="time" className="form-input" value={form.hora_inicio} onChange={e => setForm({...form, hora_inicio: e.target.value})} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">Hora Fin <span>*</span></label>
                    <input type="time" className="form-input" value={form.hora_fin} onChange={e => setForm({...form, hora_fin: e.target.value})} />
                  </div>
                </div>
              </>
            )}

            {editItem && (
              <div className="form-group">
                <label className="form-label">Estado de la Reservación</label>
                <select className="form-select" value={form.estado} onChange={e => setForm({...form, estado: e.target.value})}>
                  <option value="pendiente">Pendiente</option>
                  <option value="confirmada">Confirmada</option>
                  <option value="completada">Completada</option>
                  <option value="cancelada">Cancelada</option>
                </select>
              </div>
            )}

            <div className="form-group">
              <label className="form-label">Notas Adicionales</label>
              <textarea className="form-input" rows="3" value={form.notas || ''} onChange={e => setForm({...form, notas: e.target.value})} />
            </div>

            <div className="modal-footer">
              <button className="btn btn-secondary" onClick={() => setShowModal(false)}>Cancelar</button>
              <button className="btn btn-primary" onClick={handleSave}>💾 Guardar</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
