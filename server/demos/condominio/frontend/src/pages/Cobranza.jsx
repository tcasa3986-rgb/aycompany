import { useState, useEffect } from 'react';
import api from '../services/api';

const tipoColors = { propietario: 'purple', inquilino: 'blue', familiar: 'green', dependiente: 'amber' };
const fmt = (n) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n || 0);

export default function Cobranza() {
  const [cuotas, setCuotas] = useState([]);
  const [morosos, setMorosos] = useState([]);
  const [tipos, setTipos] = useState([]);
  const [tab, setTab] = useState('cuotas');
  const [loading, setLoading] = useState(true);
  const [showPagoModal, setShowPagoModal] = useState(false);
  const [cuotaSeleccionada, setCuotaSeleccionada] = useState(null);
  const [pago, setPago] = useState({ metodo: 'transferencia', referencia_pago: '', notas: '' });
  const [filtroEstado, setFiltroEstado] = useState('');

  const fetchData = async () => {
    setLoading(true);
    try {
      const p = filtroEstado ? `?estado=${filtroEstado}` : '';
      const [c, m, t] = await Promise.all([
        api.get(`/cobranza/cuotas${p}`),
        api.get('/cobranza/morosos'),
        api.get('/cobranza/tipos-cuota'),
      ]);
      setCuotas(c.data.data);
      setMorosos(m.data.data);
      setTipos(t.data.data);
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchData(); }, [filtroEstado]);

  const registrarPago = async () => {
    try {
      await api.post('/cobranza/pagos', {
        cuota_id: cuotaSeleccionada.id,
        unidad_id: cuotaSeleccionada.unidad_id,
        monto_pagado: cuotaSeleccionada.monto,
        fecha_pago: new Date(),
        ...pago,
      });
      setShowPagoModal(false);
      fetchData();
    } catch (err) { alert(err.response?.data?.message || 'Error al registrar pago'); }
  };

  const estadoBadge = (estado) => {
    const map = { pagado: 'success', pendiente: 'warning', vencido: 'danger', en_disputa: 'info', cancelado: 'gray' };
    return <span className={`badge badge-${map[estado] || 'gray'}`}>{estado}</span>;
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <div className="page-title">💰 Cobranza</div>
          <div className="page-subtitle">Gestión de cuotas, pagos y morosos</div>
        </div>
      </div>

      {/* Tabs */}
      <div style={{ display: 'flex', gap: 4, marginBottom: 20, background: 'var(--bg-card)', padding: 6, borderRadius: 'var(--radius-md)', width: 'fit-content', border: '1px solid var(--border-light)' }}>
        {[
          { key: 'cuotas', label: '📋 Cuotas' },
          { key: 'morosos', label: '⚠️ Morosos', count: morosos.length },
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

      {/* Filtros - solo en cuotas */}
      {tab === 'cuotas' && (
        <div className="filters-bar">
          <span style={{ fontSize: 13, fontWeight: 600, color: 'var(--text-secondary)' }}>Estado:</span>
          {['', 'pendiente', 'pagado', 'vencido'].map(e => (
            <button key={e} className={`btn btn-sm ${filtroEstado === e ? 'btn-primary' : 'btn-secondary'}`}
              onClick={() => setFiltroEstado(e)}>
              {e || 'Todos'}
            </button>
          ))}
        </div>
      )}

      {/* TABLA CUOTAS */}
      {tab === 'cuotas' && (
        <div className="table-container">
          {loading ? <div className="loading-overlay"><div className="spinner" /></div> : (
            <table>
              <thead>
                <tr>
                  <th>Unidad</th>
                  <th>Torre</th>
                  <th>Tipo</th>
                  <th>Monto</th>
                  <th>Vencimiento</th>
                  <th>Estado</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                {cuotas.map(c => (
                  <tr key={c.id}>
                    <td><strong>{c.unidad_numero}</strong></td>
                    <td>{c.torre}</td>
                    <td>{c.tipo_nombre}</td>
                    <td style={{ fontWeight: 700 }}>{fmt(c.monto)}</td>
                    <td style={{ color: c.estado === 'vencido' ? 'var(--accent-red)' : 'inherit' }}>
                      {new Date(c.fecha_vencimiento).toLocaleDateString('es-MX')}
                    </td>
                    <td>{estadoBadge(c.estado)}</td>
                    <td>
                      {['pendiente','vencido'].includes(c.estado) && (
                        <button className="btn btn-sm btn-success"
                          onClick={() => { setCuotaSeleccionada(c); setShowPagoModal(true); }}>
                          💳 Pagar
                        </button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
          {!loading && cuotas.length === 0 && (
            <div className="empty-state">
              <div className="empty-state-icon">💰</div>
              <div className="empty-state-title">Sin cuotas registradas</div>
            </div>
          )}
        </div>
      )}

      {/* TABLA MOROSOS */}
      {tab === 'morosos' && (
        <div className="table-container">
          <table>
            <thead>
              <tr>
                <th>Unidad</th>
                <th>Torre</th>
                <th>Cuotas vencidas</th>
                <th>Deuda total</th>
                <th>Días de mora</th>
              </tr>
            </thead>
            <tbody>
              {morosos.map((m, i) => (
                <tr key={i}>
                  <td><strong>{m.unidad}</strong></td>
                  <td>{m.torre}</td>
                  <td><span className="badge badge-danger">{m.cuotas_vencidas}</span></td>
                  <td style={{ fontWeight: 700, color: 'var(--accent-red)' }}>{fmt(m.deuda_total)}</td>
                  <td><span className="badge badge-warning">{m.dias_mora} días</span></td>
                </tr>
              ))}
            </tbody>
          </table>
          {morosos.length === 0 && (
            <div className="empty-state">
              <div className="empty-state-icon">✅</div>
              <div className="empty-state-title">¡Sin morosos!</div>
              <div className="empty-state-text">Todos los pagos están al corriente</div>
            </div>
          )}
        </div>
      )}

      {/* Modal pago */}
      {showPagoModal && cuotaSeleccionada && (
        <div className="modal-overlay" onClick={() => setShowPagoModal(false)}>
          <div className="modal modal-sm" onClick={e => e.stopPropagation()}>
            <div className="modal-header">
              <div className="modal-title">Registrar Pago</div>
              <button className="modal-close" onClick={() => setShowPagoModal(false)}>✕</button>
            </div>
            <div style={{ background: 'var(--bg-main)', borderRadius: 8, padding: 14, marginBottom: 16 }}>
              <div style={{ fontSize: 12, color: 'var(--text-secondary)', marginBottom: 4 }}>Detalles de la cuota</div>
              <div style={{ fontSize: 16, fontWeight: 700 }}>Unidad {cuotaSeleccionada.unidad_numero}</div>
              <div style={{ fontSize: 13, color: 'var(--text-secondary)' }}>{cuotaSeleccionada.tipo_nombre}</div>
              <div style={{ fontSize: 24, fontWeight: 800, color: 'var(--accent-green)', marginTop: 8 }}>
                {fmt(cuotaSeleccionada.monto)}
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Método de pago</label>
              <select className="form-select" value={pago.metodo} onChange={e => setPago({...pago, metodo: e.target.value})}>
                {['efectivo','transferencia','cheque','tarjeta','app'].map(m => <option key={m} value={m}>{m}</option>)}
              </select>
            </div>
            <div className="form-group">
              <label className="form-label">Referencia</label>
              <input className="form-input" value={pago.referencia_pago} onChange={e => setPago({...pago, referencia_pago: e.target.value})} placeholder="No. de transferencia / cheque" />
            </div>
            <div className="form-group">
              <label className="form-label">Notas</label>
              <textarea className="form-textarea" rows={2} value={pago.notas} onChange={e => setPago({...pago, notas: e.target.value})} />
            </div>
            <div className="modal-footer">
              <button className="btn btn-secondary" onClick={() => setShowPagoModal(false)}>Cancelar</button>
              <button className="btn btn-success" onClick={registrarPago}>✅ Registrar Pago</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
