import { useState, useEffect } from 'react';
import api from '../services/api';

export default function Comunicaciones() {
  const [anuncios, setAnuncios] = useState([]);
  const [asambleas, setAsambleas] = useState([]);
  const [tab, setTab] = useState('anuncios');
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [form, setForm] = useState({ titulo: '', contenido: '', tipo: 'informativo', fecha_expiracion: '' });

  const fetchData = async () => {
    setLoading(true);
    try {
      const [a, as] = await Promise.all([api.get('/comunicaciones/anuncios'), api.get('/comunicaciones/asambleas')]);
      setAnuncios(a.data.data);
      setAsambleas(as.data.data);
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchData(); }, []);

  const publicar = async () => {
    try { await api.post('/comunicaciones/anuncios', form); setShowModal(false); fetchData(); }
    catch (err) { alert(err.response?.data?.message || 'Error'); }
  };

  const tipoBadge = (t) => {
    const map = { informativo: 'info', urgente: 'danger', evento: 'purple', mantenimiento: 'warning', cobranza: 'amber' };
    return <span className={`badge badge-${map[t] || 'info'}`}>{t}</span>;
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <div className="page-title">📢 Comunicaciones</div>
          <div className="page-subtitle">Anuncios, asambleas y mensajería interna</div>
        </div>
        <div className="page-actions">
          <button className="btn btn-primary" onClick={() => setShowModal(true)}>+ Nuevo Anuncio</button>
        </div>
      </div>

      <div style={{ display: 'flex', gap: 4, marginBottom: 20, background: 'var(--bg-card)', padding: 6, borderRadius: 'var(--radius-md)', width: 'fit-content', border: '1px solid var(--border-light)' }}>
        {[{ key: 'anuncios', label: '📋 Anuncios' }, { key: 'asambleas', label: '🏛️ Asambleas' }].map(t => (
          <button key={t.key} className={`btn btn-sm ${tab === t.key ? 'btn-primary' : 'btn-secondary'}`} style={{ borderRadius: 8 }} onClick={() => setTab(t.key)}>{t.label}</button>
        ))}
      </div>

      {tab === 'anuncios' && (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
          {loading ? <div className="loading-overlay"><div className="spinner" /></div> : anuncios.map(a => (
            <div className="card" key={a.id} style={{ borderLeft: `4px solid ${a.tipo === 'urgente' ? 'var(--accent-red)' : a.tipo === 'evento' ? 'var(--primary)' : 'var(--accent-blue)'}` }}>
              <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', marginBottom: 8 }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                  {tipoBadge(a.tipo)}
                  <h3 style={{ fontSize: 16, fontWeight: 700 }}>{a.titulo}</h3>
                </div>
                <span style={{ fontSize: 12, color: 'var(--text-muted)' }}>{new Date(a.fecha_publicacion).toLocaleDateString('es-MX')}</span>
              </div>
              <p style={{ fontSize: 14, color: 'var(--text-secondary)', lineHeight: 1.6 }}>{a.contenido}</p>
              {a.fecha_expiracion && <div style={{ fontSize: 12, color: 'var(--text-muted)', marginTop: 8 }}>Expira: {new Date(a.fecha_expiracion).toLocaleDateString('es-MX')}</div>}
            </div>
          ))}
          {!loading && anuncios.length === 0 && (
            <div className="empty-state"><div className="empty-state-icon">📢</div><div className="empty-state-title">Sin anuncios publicados</div></div>
          )}
        </div>
      )}

      {tab === 'asambleas' && (
        <div className="table-container">
          <table>
            <thead><tr><th>Título</th><th>Tipo</th><th>Fecha</th><th>Lugar</th><th>Estado</th></tr></thead>
            <tbody>
              {asambleas.map(a => (
                <tr key={a.id}>
                  <td><strong>{a.titulo}</strong></td>
                  <td><span className="badge badge-info">{a.tipo}</span></td>
                  <td>{new Date(a.fecha).toLocaleString('es-MX')}</td>
                  <td>{a.lugar}</td>
                  <td><span className={`badge badge-${a.estado === 'finalizada' ? 'success' : a.estado === 'programada' ? 'info' : 'warning'}`}>{a.estado}</span></td>
                </tr>
              ))}
            </tbody>
          </table>
          {asambleas.length === 0 && <div className="empty-state"><div className="empty-state-icon">🏛️</div><div className="empty-state-title">Sin asambleas registradas</div></div>}
        </div>
      )}

      {showModal && (
        <div className="modal-overlay" onClick={() => setShowModal(false)}>
          <div className="modal" onClick={e => e.stopPropagation()}>
            <div className="modal-header">
              <div className="modal-title">Nuevo Anuncio</div>
              <button className="modal-close" onClick={() => setShowModal(false)}>✕</button>
            </div>
            <div className="form-group">
              <label className="form-label">Título <span>*</span></label>
              <input className="form-input" value={form.titulo} onChange={e => setForm({...form, titulo: e.target.value})} />
            </div>
            <div className="form-group">
              <label className="form-label">Tipo</label>
              <select className="form-select" value={form.tipo} onChange={e => setForm({...form, tipo: e.target.value})}>
                {['informativo','urgente','evento','mantenimiento','cobranza'].map(t => <option key={t} value={t}>{t}</option>)}
              </select>
            </div>
            <div className="form-group">
              <label className="form-label">Contenido <span>*</span></label>
              <textarea className="form-textarea" rows={4} value={form.contenido} onChange={e => setForm({...form, contenido: e.target.value})} />
            </div>
            <div className="form-group">
              <label className="form-label">Fecha de expiración</label>
              <input type="datetime-local" className="form-input" value={form.fecha_expiracion} onChange={e => setForm({...form, fecha_expiracion: e.target.value})} />
            </div>
            <div className="modal-footer">
              <button className="btn btn-secondary" onClick={() => setShowModal(false)}>Cancelar</button>
              <button className="btn btn-primary" onClick={publicar}>📢 Publicar</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
