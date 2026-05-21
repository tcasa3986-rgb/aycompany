import { useState, useEffect } from 'react';
import api from '../services/api';

const estadoColors = { activo: 'green', vencido: 'red', cancelado: 'gray', por_renovar: 'amber' };

export default function Proveedores() {
  const [proveedores, setProveedores] = useState([]);
  const [contratos, setContratos] = useState([]);
  const [tab, setTab] = useState('proveedores'); // 'proveedores' o 'contratos'
  const [loading, setLoading] = useState(true);
  
  const [showModalProv, setShowModalProv] = useState(false);
  const [editProv, setEditProv] = useState(null);
  
  const [formProv, setFormProv] = useState({
    nombre: '', rfc: '', tipo_servicio: '',
    contacto_nombre: '', contacto_telefono: '', contacto_email: '',
    direccion: '', notas: ''
  });

  const fetchData = async () => {
    setLoading(true);
    try {
      const [resProv, resContr] = await Promise.all([
        api.get('/proveedores'),
        api.get('/proveedores/todos/contratos')
      ]);
      setProveedores(resProv.data.data);
      setContratos(resContr.data.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchData(); }, []);

  const openProvModal = (item = null) => {
    setEditProv(item);
    if (item) {
      setFormProv({ ...item });
    } else {
      setFormProv({
        nombre: '', rfc: '', tipo_servicio: '',
        contacto_nombre: '', contacto_telefono: '', contacto_email: '',
        direccion: '', notas: ''
      });
    }
    setShowModalProv(true);
  };

  const handleSaveProv = async () => {
    if (!formProv.nombre || !formProv.tipo_servicio) {
      alert('Favor de completar Nombre y Tipo de Servicio referenciado (*)');
      return;
    }
    try {
      if (editProv) {
        await api.put(`/proveedores/${editProv.id}`, formProv);
      } else {
        await api.post('/proveedores', formProv);
      }
      setShowModalProv(false);
      fetchData();
    } catch (err) {
      alert(err.response?.data?.message || 'Error al guardar proveedor');
    }
  };

  const handleDeleteProv = async (id) => {
    if (!confirm('¿Seguro que deseas dar de baja a este proveedor?')) return;
    try {
      await api.delete(`/proveedores/${id}`);
      fetchData();
    } catch (err) {
      alert(err.response?.data?.message || 'Error al eliminar proveedor');
    }
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <div className="page-title">📋 Proveedores y Contratos</div>
          <div className="page-subtitle">Directorio de contratistas y servicios</div>
        </div>
        <div className="page-actions">
          {tab === 'proveedores' && (
            <button className="btn btn-primary" onClick={() => openProvModal()}>+ Nuevo Proveedor</button>
          )}
        </div>
      </div>

      <div className="tabs">
        <button className={`tab ${tab === 'proveedores' ? 'active' : ''}`} onClick={() => setTab('proveedores')}>Directorio de Proveedores</button>
        <button className={`tab ${tab === 'contratos' ? 'active' : ''}`} onClick={() => setTab('contratos')}>Gestión de Contratos</button>
      </div>

      {loading ? (
        <div className="loading-overlay"><div className="spinner" /></div>
      ) : (
        <div className="table-container fade-in">
          
          {tab === 'proveedores' && (
            <>
              <table>
                <thead>
                  <tr>
                    <th>Empresa / Proveedor</th>
                    <th>Servicio Principal</th>
                    <th>Contacto (Representante)</th>
                    <th>Tels/Mails</th>
                    <th>Calificación</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  {proveedores.map(p => (
                    <tr key={p.id}>
                      <td>
                        <strong>{p.nombre}</strong>
                        <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>RFC: {p.rfc || '—'}</div>
                      </td>
                      <td><span className="badge badge-blue">{p.tipo_servicio || 'Servicios Varios'}</span></td>
                      <td>{p.contacto_nombre || '—'}</td>
                      <td>
                        <div style={{ fontSize: 13 }}>{p.contacto_telefono || '—'}</div>
                        <div style={{ fontSize: 12, color: 'var(--text-secondary)' }}>{p.contacto_email}</div>
                      </td>
                      <td>
                        <span style={{ color: p.calificacion >= 4 ? '#EAB308' : '#9CA3AF' }}>
                          ★ {Number(p.calificacion || 0).toFixed(1)}
                        </span>
                      </td>
                      <td>
                        <div style={{ display: 'flex', gap: 6 }}>
                           <button className="btn btn-sm btn-secondary" onClick={() => openProvModal(p)}>✏️</button>
                           <button className="btn btn-sm btn-danger btn-sm" onClick={() => handleDeleteProv(p.id)}>🗑️</button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
              {proveedores.length === 0 && (
                <div className="empty-state">
                  <div className="empty-state-icon">🏢</div>
                  <div className="empty-state-title">Aún no hay proveedores</div>
                  <button className="btn btn-primary mt-16" onClick={() => openProvModal()}>+ Alta de Proveedor</button>
                </div>
              )}
            </>
          )}

          {tab === 'contratos' && (
            <>
              <table>
                <thead>
                  <tr>
                    <th>Proveedor</th>
                    <th>Tipo de Contrato</th>
                    <th>Periodo</th>
                    <th>Monto Mensual</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                  {contratos.map(c => (
                    <tr key={c.id}>
                      <td><strong>{c.proveedor_nombre}</strong></td>
                      <td>{c.tipo || c.descripcion || 'General'}</td>
                      <td>
                        <div>Inicio: {new Date(c.fecha_inicio).toLocaleDateString('es-MX')}</div>
                        <div style={{ fontSize: 12, color: 'var(--text-secondary)' }}>
                           Fin: {c.fecha_fin ? new Date(c.fecha_fin).toLocaleDateString('es-MX') : 'Indefinido'}
                        </div>
                      </td>
                      <td style={{ fontWeight: 600 }}>${Number(c.monto_mensual).toLocaleString('es-MX')}</td>
                      <td><span className={`badge badge-${estadoColors[c.estado] || 'gray'}`}>{c.estado}</span></td>
                    </tr>
                  ))}
                </tbody>
              </table>
              {contratos.length === 0 && (
                <div className="empty-state">
                  <div className="empty-state-icon">📋</div>
                  <div className="empty-state-title">No hay contratos registrados</div>
                  <div className="empty-state-text">Vincule contratos a sus proveedores para manejar fechas de expiración.</div>
                </div>
              )}
            </>
          )}

        </div>
      )}

      {/* MODAL PROVEEDORES */}
      {showModalProv && (
        <div className="modal-overlay" onClick={() => setShowModalProv(false)}>
          <div className="modal" style={{ maxWidth: 650 }} onClick={e => e.stopPropagation()}>
            <div className="modal-header">
              <div className="modal-title">{editProv ? 'Editar Proveedor' : 'Nuevo Proveedor'}</div>
              <button className="modal-close" onClick={() => setShowModalProv(false)}>✕</button>
            </div>
            
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Nombre Empresa / Emisor <span>*</span></label>
                <input className="form-input" value={formProv.nombre} onChange={e => setFormProv({...formProv, nombre: e.target.value})} placeholder="Ej. Jardinería S.A. de C.V." />
              </div>
              <div className="form-group">
                <label className="form-label">RFC</label>
                <input className="form-input" value={formProv.rfc || ''} onChange={e => setFormProv({...formProv, rfc: e.target.value})} />
              </div>
            </div>

            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Rubro o Tipo de Servicio <span>*</span></label>
                <input className="form-input" value={formProv.tipo_servicio || ''} onChange={e => setFormProv({...formProv, tipo_servicio: e.target.value})} placeholder="Ej. Jardinería, Mantenimiento, Limpieza" />
              </div>
            </div>

            <div className="form-row">
               <div className="form-group">
                  <label className="form-label">Nombre de Contacto Directo</label>
                  <input className="form-input" value={formProv.contacto_nombre || ''} onChange={e => setFormProv({...formProv, contacto_nombre: e.target.value})} />
               </div>
            </div>

            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Teléfono</label>
                <input className="form-input" value={formProv.contacto_telefono || ''} onChange={e => setFormProv({...formProv, contacto_telefono: e.target.value})} />
              </div>
              <div className="form-group">
                <label className="form-label">Correo Electrónico</label>
                <input type="email" className="form-input" value={formProv.contacto_email || ''} onChange={e => setFormProv({...formProv, contacto_email: e.target.value})} />
              </div>
            </div>

            <div className="form-group">
               <label className="form-label">Dirección Fiscal / Oficina</label>
               <input className="form-input" value={formProv.direccion || ''} onChange={e => setFormProv({...formProv, direccion: e.target.value})} />
            </div>

            <div className="form-group">
              <label className="form-label">Notas o Referencias</label>
              <textarea className="form-input" rows="2" value={formProv.notas || ''} onChange={e => setFormProv({...formProv, notas: e.target.value})} />
            </div>

            <div className="modal-footer">
              <button className="btn btn-secondary" onClick={() => setShowModalProv(false)}>Cancelar</button>
              <button className="btn btn-primary" onClick={handleSaveProv}>💾 Guardar</button>
            </div>
          </div>
        </div>
      )}

    </div>
  );
}
