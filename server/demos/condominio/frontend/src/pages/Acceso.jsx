import { useState, useEffect } from 'react';
import api from '../services/api';

export default function Acceso() {
  const [visitantes, setVisitantes] = useState([]);
  const [paquetes, setPaquetes] = useState([]);
  const [incidentes, setIncidentes] = useState([]);
  const [tab, setTab] = useState('visitantes');
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [form, setForm] = useState({ nombre: '', documento_id: '', unidad_id: '', motivo: '', tipo: 'visita' });

  const fetchData = async () => {
    setLoading(true);
    try {
      const [v, p, i] = await Promise.all([
        api.get('/acceso/visitantes?fecha=' + new Date().toISOString().split('T')[0]),
        api.get('/acceso/paquetes'),
        api.get('/acceso/incidentes'),
      ]);
      setVisitantes(v.data.data);
      setPaquetes(p.data.data);
      setIncidentes(i.data.data);
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchData(); }, []);

  const registrarVisitante = async () => {
    try {
      await api.post('/acceso/visitantes', form);
      setShowModal(false);
      fetchData();
    } catch (err) { alert(err.response?.data?.message || 'Error'); }
  };

  const registrarSalida = async (id) => {
    await api.put(`/acceso/visitantes/${id}/salida`);
    fetchData();
  };

  const nivelBadge = (n) => {
    const map = { bajo: 'info', medio: 'warning', alto: 'danger', critico: 'danger' };
    return <span className={`badge badge-${map[n]}`}>{n}</span>;
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <div className="page-title">🛡️ Control de Acceso</div>
          <div className="page-subtitle">Visitantes hoy: {visitantes.length} | Paquetes pendientes: {paquetes.filter(p => p.estado !== 'entregado').length}</div>
        </div>
        <div className="page-actions">
          <button className="btn btn-primary" onClick={() => setShowModal(true)}>+ Registrar Visitante</button>
        </div>
      </div>

      {/* Tabs */}
      <div style={{ display: 'flex', gap: 4, marginBottom: 20, background: 'var(--bg-card)', padding: 6, borderRadius: 'var(--radius-md)', width: 'fit-content', border: '1px solid var(--border-light)' }}>
        {[
          { key: 'visitantes', label: '👤 Visitantes', count: visitantes.filter(v => !v.salida).length },
          { key: 'paquetes', label: '📦 Paquetes', count: paquetes.filter(p => p.estado !== 'entregado').length },
          { key: 'incidentes', label: '🚨 Incidentes' },
        ].map(t => (
          <button key={t.key}
            className={`btn btn-sm ${tab === t.key ? 'btn-primary' : 'btn-secondary'}`}
            style={{ borderRadius: 8 }}
            onClick={() => setTab(t.key)}>
            {t.label}
            {t.count > 0 && <span className="nav-badge">{t.count}</span>}
          </button>
        ))}
      </div>

      {/* Visitantes */}
      {tab === 'visitantes' && (
        <div className="table-container">
          {loading ? <div className="loading-overlay"><div className="spinner" /></div> : (
            <table>
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Tipo</th>
                  <th>Unidad</th>
                  <th>Motivo</th>
                  <th>Entrada</th>
                  <th>Salida</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                {visitantes.map(v => (
                  <tr key={v.id}>
                    <td><strong>{v.nombre}</strong><div style={{ fontSize: 11, color: 'var(--text-muted)' }}>{v.documento_id}</div></td>
                    <td><span className="badge badge-info">{v.tipo}</span></td>
                    <td>{v.unidad_numero}</td>
                    <td>{v.motivo}</td>
                    <td style={{ fontSize: 12 }}>{new Date(v.entrada).toLocaleTimeString('es-MX')}</td>
                    <td>
                      {v.salida ? (
                        <span style={{ fontSize: 12 }}>{new Date(v.salida).toLocaleTimeString('es-MX')}</span>
                      ) : (
                        <div style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                          <div className="status-dot green" />
                          <span style={{ fontSize: 12, color: 'var(--accent-green)' }}>Dentro</span>
                        </div>
                      )}
                    </td>
                    <td>
                      {!v.salida && (
                        <button className="btn btn-sm btn-secondary" onClick={() => registrarSalida(v.id)}>
                          🚪 Salida
                        </button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
          {!loading && visitantes.length === 0 && (
            <div className="empty-state">
              <div className="empty-state-icon">👤</div>
              <div className="empty-state-title">Sin visitantes hoy</div>
            </div>
          )}
        </div>
      )}

      {/* Paquetes */}
      {tab === 'paquetes' && (
        <div className="table-container">
          <table>
            <thead>
              <tr>
                <th>Unidad</th>
                <th>Descripción</th>
                <th>Empresa</th>
                <th>Recibido</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              {paquetes.map(p => (
                <tr key={p.id}>
                  <td><strong>{p.unidad_numero}</strong></td>
                  <td>{p.descripcion}</td>
                  <td>{p.empresa_mensajeria}</td>
                  <td style={{ fontSize: 12 }}>{new Date(p.fecha_recepcion).toLocaleString('es-MX')}</td>
                  <td>
                    <span className={`badge badge-${p.estado === 'entregado' ? 'success' : p.estado === 'notificado' ? 'info' : 'warning'}`}>
                      {p.estado}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
          {paquetes.length === 0 && (
            <div className="empty-state">
              <div className="empty-state-icon">📦</div>
              <div className="empty-state-title">Sin paquetes registrados</div>
            </div>
          )}
        </div>
      )}

      {/* Incidentes */}
      {tab === 'incidentes' && (
        <div className="table-container">
          <table>
            <thead>
              <tr>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Ubicación</th>
                <th>Nivel</th>
                <th>Estado</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody>
              {incidentes.map(i => (
                <tr key={i.id}>
                  <td><strong>{i.tipo}</strong></td>
                  <td>{i.descripcion?.slice(0, 80)}</td>
                  <td>{i.ubicacion}</td>
                  <td>{nivelBadge(i.nivel)}</td>
                  <td>
                    <span className={`badge badge-${i.estado === 'cerrado' ? 'gray' : i.estado === 'en_investigacion' ? 'warning' : 'danger'}`}>
                      {i.estado.replace('_', ' ')}
                    </span>
                  </td>
                  <td style={{ fontSize: 12 }}>{new Date(i.fecha).toLocaleString('es-MX')}</td>
                </tr>
              ))}
            </tbody>
          </table>
          {incidentes.length === 0 && (
            <div className="empty-state">
              <div className="empty-state-icon">🚨</div>
              <div className="empty-state-title">Sin incidentes registrados</div>
            </div>
          )}
        </div>
      )}

      {/* Modal visitante */}
      {showModal && (
        <div className="modal-overlay" onClick={() => setShowModal(false)}>
          <div className="modal" onClick={e => e.stopPropagation()}>
            <div className="modal-header">
              <div className="modal-title">Registrar Visitante</div>
              <button className="modal-close" onClick={() => setShowModal(false)}>✕</button>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Nombre completo <span>*</span></label>
                <input className="form-input" value={form.nombre} onChange={e => setForm({...form, nombre: e.target.value})} />
              </div>
              <div className="form-group">
                <label className="form-label">Documento (ID)</label>
                <input className="form-input" value={form.documento_id} onChange={e => setForm({...form, documento_id: e.target.value})} />
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Tipo</label>
                <select className="form-select" value={form.tipo} onChange={e => setForm({...form, tipo: e.target.value})}>
                  {['visita','proveedor','delivery','otro'].map(t => <option key={t} value={t}>{t}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">No. Unidad destino</label>
                <input className="form-input" value={form.unidad_id} onChange={e => setForm({...form, unidad_id: e.target.value})} placeholder="Ej: A-101" />
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Motivo de visita</label>
              <input className="form-input" value={form.motivo} onChange={e => setForm({...form, motivo: e.target.value})} />
            </div>
            <div className="modal-footer">
              <button className="btn btn-secondary" onClick={() => setShowModal(false)}>Cancelar</button>
              <button className="btn btn-primary" onClick={registrarVisitante}>✅ Registrar entrada</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
