import { useState, useEffect } from 'react';
import api from '../services/api';

const categoriasOptions = {
  ingreso: ['Cuotas de Mantenimiento', 'Cuotas Extraordinarias', 'Amenidades', 'Penalizaciones', 'Otros Ingresos'],
  egreso: ['Nómina', 'Mantenimiento General', 'Jardinería', 'Seguridad', 'Luz / CFE', 'Agua / CEA', 'Honorarios Admin', 'Otros Egresos']
};

export default function Contabilidad() {
  const [resumen, setResumen] = useState({ ingresos_mes: 0, egresos_mes: 0, fondo_total: 0 });
  const [movimientos, setMovimientos] = useState([]);
  const [cuentas, setCuentas] = useState([]);
  const [loading, setLoading] = useState(true);
  
  const [showModal, setShowModal] = useState(false);
  
  const [form, setForm] = useState({
    tipo: 'ingreso', categoria: '', concepto: '', monto: '',
    fecha: new Date().toISOString().split('T')[0]
  });

  const fetchData = async () => {
    setLoading(true);
    try {
      const [resResumen, resMovs, resCuentas] = await Promise.all([
        api.get('/contabilidad/resumen'),
        api.get('/contabilidad/movimientos'),
        api.get('/contabilidad/cuentas')
      ]);
      setResumen(resResumen.data.data);
      setMovimientos(resMovs.data.data);
      setCuentas(resCuentas.data.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchData(); }, []);

  const openModal = (tipoStr = 'ingreso') => {
    setForm({
      tipo: tipoStr, categoria: categoriasOptions[tipoStr][0], 
      concepto: '', monto: '', fecha: new Date().toISOString().split('T')[0]
    });
    setShowModal(true);
  };

  const handleSave = async () => {
    if (!form.concepto || !form.monto || !form.fecha) {
      alert('Favor de completar Concepto y Monto');
      return;
    }
    try {
      await api.post('/contabilidad/movimientos', form);
      setShowModal(false);
      fetchData();
    } catch (err) {
      alert(err.response?.data?.message || 'Error al registrar movimiento');
    }
  };

  const formatoMoneda = (num) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(num) || 0);
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <div className="page-title">📊 Contabilidad y Finanzas</div>
          <div className="page-subtitle">Presupuesto, ingresos, egresos y fondos</div>
        </div>
        <div className="page-actions">
          <button className="btn btn-secondary" style={{ borderColor: 'var(--accent-green)', color: 'var(--accent-green)' }} onClick={() => openModal('ingreso')}>
            ↑ Registrar Ingreso
          </button>
          <button className="btn btn-secondary" style={{ borderColor: 'var(--accent-red)', color: 'var(--accent-red)' }} onClick={() => openModal('egreso')}>
            ↓ Registrar Egreso
          </button>
        </div>
      </div>

      {/* KPIs */}
      <div className="grid-3" style={{ marginBottom: 24 }}>
        <div className="card stat-card">
          <div className="stat-icon" style={{ backgroundColor: '#DBEAFE', color: '#1D4ED8' }}>💰</div>
          <div className="stat-content">
            <div className="stat-label">Saldo Total a Favor</div>
            <div className="stat-value">{formatoMoneda(resumen.fondo_total)}</div>
            <div className="stat-trend trend-up">En {cuentas.length} cuentas bancarias</div>
          </div>
        </div>
        <div className="card stat-card">
          <div className="stat-icon" style={{ backgroundColor: '#DCFCE7', color: '#15803D' }}>↑</div>
          <div className="stat-content">
            <div className="stat-label">Ingresos del Mes</div>
            <div className="stat-value" style={{ color: 'var(--accent-green)' }}>{formatoMoneda(resumen.ingresos_mes)}</div>
            <div className="stat-trend trend-up">+ Cuotas pagadas</div>
          </div>
        </div>
        <div className="card stat-card">
          <div className="stat-icon" style={{ backgroundColor: '#FEE2E2', color: '#B91C1C' }}>↓</div>
          <div className="stat-content">
            <div className="stat-label">Egresos del Mes</div>
            <div className="stat-value" style={{ color: 'var(--accent-red)' }}>{formatoMoneda(resumen.egresos_mes)}</div>
            <div className="stat-trend trend-down">- Gastos fijos</div>
          </div>
        </div>
      </div>

      {/* Cuentas Bancarias */}
      <div className="grid-2" style={{ marginBottom: 24 }}>
        {cuentas.map(c => (
           <div className="card" key={c.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              <div>
                 <div style={{ fontWeight: 700 }}>{c.banco} - {c.cuenta}</div>
                 <div style={{ fontSize: 13, color: 'var(--text-secondary)' }}>CLABE: {c.clabe}</div>
              </div>
              <div style={{ fontSize: 24, fontWeight: 700, color: 'var(--primary)' }}>
                 {formatoMoneda(c.saldo)}
              </div>
           </div>
        ))}
      </div>

      {/* Ultimos Movimientos */}
      <div className="table-container">
        <div className="p-16 border-b"><h3 style={{ margin: 0 }}>Historial de Movimientos</h3></div>
        {loading ? (
          <div className="loading-overlay"><div className="spinner" /></div>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Concepto</th>
                <th>Categoría</th>
                <th>Registrado por</th>
                <th style={{ textAlign: 'right' }}>Monto</th>
              </tr>
            </thead>
            <tbody>
              {movimientos.map(m => (
                <tr key={m.id}>
                  <td><strong>{new Date(m.fecha).toLocaleDateString('es-MX')}</strong></td>
                  <td>
                    {m.tipo === 'ingreso' ? '🟢 ' : '🔴 '}
                    <strong>{m.concepto}</strong>
                  </td>
                  <td>{m.categoria}</td>
                  <td>{m.registrador || 'Sistema'}</td>
                  <td style={{ textAlign: 'right', fontWeight: 600, color: m.tipo === 'ingreso' ? 'var(--accent-green)' : 'var(--accent-red)' }}>
                    {m.tipo === 'ingreso' ? '+' : '-'} {formatoMoneda(m.monto)}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
        {!loading && movimientos.length === 0 && (
          <div className="empty-state">
            <div className="empty-state-icon">📄</div>
            <div className="empty-state-title">No hay movimientos financieros</div>
            <div className="empty-state-text">Registra una entrada o salida de dinero para visualizarla.</div>
          </div>
        )}
      </div>

      {/* MODAL MOVIMIENTO */}
      {showModal && (
        <div className="modal-overlay" onClick={() => setShowModal(false)}>
          <div className="modal" onClick={e => e.stopPropagation()}>
            <div className="modal-header">
              <div className="modal-title" style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                 {form.tipo === 'ingreso' ? <span style={{color:'var(--accent-green)'}}>↑ Nuevo Ingreso</span> : <span style={{color:'var(--accent-red)'}}>↓ Nuevo Egreso</span>}
              </div>
              <button className="modal-close" onClick={() => setShowModal(false)}>✕</button>
            </div>
            
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Categoría <span>*</span></label>
                <select className="form-select" value={form.categoria} onChange={e => setForm({...form, categoria: e.target.value})}>
                  {categoriasOptions[form.tipo].map(c => <option key={c} value={c}>{c}</option>)}
                  <option value="Otro">Otro</option>
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Fecha <span>*</span></label>
                <input type="date" className="form-input" value={form.fecha} onChange={e => setForm({...form, fecha: e.target.value})} />
              </div>
            </div>

            <div className="form-group">
               <label className="form-label">Concepto (Descripción) <span>*</span></label>
               <input className="form-input" value={form.concepto} onChange={e => setForm({...form, concepto: e.target.value})} placeholder="Ej. Pago de Mantenimiento / Compra material" />
            </div>

            <div className="form-group">
               <label className="form-label">Monto (MXN) <span>*</span></label>
               <input type="number" min="0" step="0.01" className="form-input" value={form.monto} onChange={e => setForm({...form, monto: e.target.value})} placeholder="0.00" />
            </div>

            <div className="modal-footer">
              <button className="btn btn-secondary" onClick={() => setShowModal(false)}>Cancelar</button>
              <button className="btn btn-primary" onClick={handleSave}>💾 Registrar Operación</button>
            </div>
          </div>
        </div>
      )}

    </div>
  );
}
