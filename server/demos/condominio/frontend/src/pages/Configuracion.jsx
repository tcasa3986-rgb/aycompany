import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';

export default function Configuracion() {
  const navigate = useNavigate();
  const [tab, setTab] = useState('menu');
  const [loading, setLoading] = useState(true);
  
  // Data states
  const [datosCondominio, setDatosCondominio] = useState({ nombre: '', direccion: '', rfc: '', telefono: '', email: '', sitio_web: '' });
  const [configuracion, setConfiguracion] = useState({});
  const [cuotas, setCuotas] = useState([]);
  const [usuarios, setUsuarios] = useState([]);
  const [roles, setRoles] = useState([]);
  const [auditoria, setAuditoria] = useState([]);

  // Modal states
  const [showUserModal, setShowUserModal] = useState(false);
  const [selectedUser, setSelectedUser] = useState(null);
  const [userForm, setUserForm] = useState({ nombre: '', apellidos: '', email: '', telefono: '', rol_id: '', password: '' });

  const fetchData = async () => {
    setLoading(true);
    try {
      const [d, c, u, a] = await Promise.all([
        api.get('/configuracion/datos'),
        api.get('/configuracion/cuotas'),
        api.get('/configuracion/usuarios'),
        api.get('/configuracion/auditoria')
      ]);
      setDatosCondominio(d.data.data.condominio || {});
      setConfiguracion(d.data.data.configuracion || {});
      setCuotas(c.data.data);
      setUsuarios(u.data.data.usuarios || []);
      setRoles(u.data.data.roles || []);
      setAuditoria(a.data.data);
    } catch(err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchData(); }, []);

  const saveDatos = async () => {
    try {
      await api.put('/configuracion/datos', { condominio: datosCondominio, configuracion });
      alert('Configuración guardada exitosamente');
      fetchData();
    } catch (err) {
      alert('Error al guardar: ' + (err.response?.data?.message || err.message));
    }
  };

  const handleOpenUserModal = (user = null) => {
    setSelectedUser(user);
    if (user) {
      setUserForm({
        nombre: user.nombre,
        apellidos: user.apellidos,
        email: user.email,
        telefono: user.telefono || '',
        rol_id: user.rol_id,
        password: '' // Reset password field for security
      });
    } else {
      setUserForm({ nombre: '', apellidos: '', email: '', telefono: '', rol_id: roles[0]?.id || '', password: '' });
    }
    setShowUserModal(true);
  };

  const handleSaveUser = async () => {
    try {
      if (selectedUser) {
        await api.put(`/configuracion/usuarios/${selectedUser.id}`, userForm);
      } else {
        if (!userForm.password) {
          alert('La contraseña es requerida para nuevos usuarios');
          return;
        }
        await api.post('/configuracion/usuarios', userForm);
      }
      setShowUserModal(false);
      fetchData();
    } catch (err) {
      alert('Error: ' + (err.response?.data?.message || err.message));
    }
  };

  const handleToggleUser = async (user) => {
    try {
      await api.patch(`/configuracion/usuarios/${user.id}/toggle`, { activo: !user.activo });
      fetchData();
    } catch (err) {
      alert('Error: ' + (err.response?.data?.message || err.message));
    }
  };

  const menuItems = [
    { id: 'datos', icon: '🏢', title: 'Datos del condominio', desc: 'Nombre, logo, dirección, RFC' },
    { id: 'cuotas', icon: '💰', title: 'Cuotas', desc: 'Tipos de cuota, tasas de mora, días de gracia' },
    { id: 'notificaciones', icon: '✉️', title: 'Notificaciones', desc: 'Plantillas de email y configuración SMTP' },
    { id: 'usuarios', icon: '👥', title: 'Usuarios y roles', desc: 'Gestión de usuarios del sistema' },
    { id: 'respaldo', icon: '🛡️', title: 'Mantenimiento y Respaldo', desc: 'Copias de seguridad, restauración y reset' },
    { id: 'auditoria', icon: '📋', title: 'Auditoría', desc: 'Historial de cambios en el sistema' },
  ];

  if (tab === 'menu') {
    return (
      <div className="fade-in">
        <div className="page-header">
          <div>
            <div className="page-title">⚙️ Configuración</div>
            <div className="page-subtitle">Configuración del sistema y condominio</div>
          </div>
        </div>
        <div className="grid-2">
          {menuItems.map((c) => (
            <div className="card" key={c.id} onClick={() => {
                if (c.id === 'respaldo') {
                  navigate('/sistema');
                } else {
                  setTab(c.id);
                }
            }} style={{ cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 16 }}>
              <div style={{ fontSize: 36, width: 60, height: 60, background: 'var(--bg-main)', borderRadius: 12, display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>{c.icon}</div>
              <div>
                <div style={{ fontSize: 15, fontWeight: 700 }}>{c.title}</div>
                <div style={{ fontSize: 13, color: 'var(--text-secondary)', marginTop: 3 }}>{c.desc}</div>
              </div>
              <div style={{ marginLeft: 'auto', color: 'var(--text-muted)', fontSize: 18 }}>→</div>
            </div>
          ))}
        </div>
      </div>
    );
  }

  return (
    <div className="fade-in">
      <div className="page-header" style={{ marginBottom: 16 }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: 16 }}>
          <button className="btn btn-secondary btn-icon" onClick={() => setTab('menu')}>←</button>
          <div>
            <div className="page-title">{menuItems.find(m => m.id === tab)?.title}</div>
            <div className="page-subtitle">{menuItems.find(m => m.id === tab)?.desc}</div>
          </div>
        </div>
      </div>

      {loading ? <div className="loading-overlay"><div className="spinner" /></div> : (
        <div className="card">
          
          {tab === 'datos' && (
            <div>
              <div className="form-row">
                <div className="form-group">
                  <label className="form-label">Nombre del Condominio</label>
                  <input className="form-input" value={datosCondominio.nombre || ''} onChange={e => setDatosCondominio({...datosCondominio, nombre: e.target.value})} />
                </div>
                <div className="form-group">
                  <label className="form-label">RFC</label>
                  <input className="form-input" value={datosCondominio.rfc || ''} onChange={e => setDatosCondominio({...datosCondominio, rfc: e.target.value})} />
                </div>
              </div>
              <div className="form-group">
                <label className="form-label">Dirección Completa</label>
                <input className="form-input" value={datosCondominio.direccion || ''} onChange={e => setDatosCondominio({...datosCondominio, direccion: e.target.value})} />
              </div>
              <div className="form-row">
                <div className="form-group">
                  <label className="form-label">Teléfono</label>
                  <input className="form-input" value={datosCondominio.telefono || ''} onChange={e => setDatosCondominio({...datosCondominio, telefono: e.target.value})} />
                </div>
                <div className="form-group">
                  <label className="form-label">Email de Contacto</label>
                  <input className="form-input" value={datosCondominio.email || ''} onChange={e => setDatosCondominio({...datosCondominio, email: e.target.value})} />
                </div>
              </div>
              <div style={{ marginTop: 20, textAlign: 'right' }}>
                <button className="btn btn-primary" onClick={saveDatos}>💾 Guardar Cambios</button>
              </div>
            </div>
          )}

          {tab === 'notificaciones' && (
            <div>
              <div className="form-row">
                <div className="form-group">
                  <label className="form-label">Servidor SMTP</label>
                  <input className="form-input" value={configuracion.smtp_host || ''} onChange={e => setConfiguracion({...configuracion, smtp_host: e.target.value})} placeholder="smtp.gmail.com" />
                </div>
                <div className="form-group">
                  <label className="form-label">Puerto SMTP</label>
                  <input className="form-input" value={configuracion.smtp_port || ''} onChange={e => setConfiguracion({...configuracion, smtp_port: e.target.value})} placeholder="587" />
                </div>
              </div>
              <div className="form-row">
                <div className="form-group">
                  <label className="form-label">Usuario SMTP</label>
                  <input className="form-input" value={configuracion.smtp_user || ''} onChange={e => setConfiguracion({...configuracion, smtp_user: e.target.value})} />
                </div>
                <div className="form-group">
                  <label className="form-label">Contraseña SMTP</label>
                  <input className="form-input" type="password" value={configuracion.smtp_pass || ''} onChange={e => setConfiguracion({...configuracion, smtp_pass: e.target.value})} />
                </div>
              </div>
              <div className="form-group">
                <label className="form-label">Envío automático de recibos</label>
                <select className="form-select" value={configuracion.auto_send_receipts || 'no'} onChange={e => setConfiguracion({...configuracion, auto_send_receipts: e.target.value})}>
                  <option value="si">Sí, enviar email al registrar pago</option>
                  <option value="no">No, solo manual</option>
                </select>
              </div>
              <div style={{ marginTop: 20, textAlign: 'right' }}>
                <button className="btn btn-primary" onClick={saveDatos}>💾 Guardar Cambios</button>
              </div>
            </div>
          )}

          {tab === 'cuotas' && (
            <div className="table-container" style={{ border: 'none', boxShadow: 'none' }}>
              <div style={{ textAlign: 'right', marginBottom: 16 }}>
                <button className="btn btn-primary btn-sm">+ Nuevo Tipo</button>
              </div>
              <table>
                <thead>
                  <tr><th>Nombre</th><th>Periodicidad</th><th>Monto Base</th><th>Días Gracia</th><th>Mora</th></tr>
                </thead>
                <tbody>
                  {cuotas.map(c => (
                    <tr key={c.id}>
                      <td><strong>{c.nombre}</strong></td>
                      <td style={{ textTransform: 'capitalize' }}>{c.periodicidad}</td>
                      <td>${c.monto_base}</td>
                      <td>{c.dias_gracia} días</td>
                      <td>{c.aplica_mora ? `${c.tasa_mora*100}%` : 'No'}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          {tab === 'usuarios' && (
            <div className="table-container" style={{ border: 'none', boxShadow: 'none' }}>
              <div style={{ textAlign: 'right', marginBottom: 16 }}>
                <button className="btn btn-primary btn-sm" onClick={() => handleOpenUserModal()}>+ Nuevo Usuario</button>
              </div>
              <table>
                <thead>
                  <tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Teléfono</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                  {usuarios.map(u => (
                    <tr key={u.id}>
                      <td><strong>{u.nombre} {u.apellidos}</strong></td>
                      <td>{u.email}</td>
                      <td><span className="badge badge-purple">{u.rol}</span></td>
                      <td>{u.telefono || '—'}</td>
                      <td>
                        <span className={`badge ${u.activo ? 'badge-green' : 'badge-red'}`}>
                          {u.activo ? 'Activo' : 'Inactivo'}
                        </span>
                      </td>
                      <td>
                        <div style={{ display: 'flex', gap: 8 }}>
                          <button className="btn btn-sm btn-secondary" onClick={() => handleOpenUserModal(u)}>Editar</button>
                          <button 
                            className={`btn btn-sm ${u.activo ? 'btn-danger' : 'btn-success'}`} 
                            onClick={() => handleToggleUser(u)}
                            style={u.activo ? { borderColor: 'var(--accent-red)', color: 'var(--accent-red)' } : { borderColor: 'var(--accent-green)', color: 'var(--accent-green)' }}
                          >
                            {u.activo ? 'Desactivar' : 'Activar'}
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
          
          {/* USER MODAL */}
          {showUserModal && (
            <div className="modal-overlay" onClick={() => setShowUserModal(false)}>
              <div className="modal" onClick={e => e.stopPropagation()} style={{ maxWidth: 600 }}>
                <div className="modal-header">
                  <div className="modal-title">{selectedUser ? 'Editar Usuario' : 'Nuevo Usuario'}</div>
                  <button className="modal-close" onClick={() => setShowUserModal(false)}>✕</button>
                </div>
                
                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">Nombre</label>
                    <input className="form-input" value={userForm.nombre} onChange={e => setUserForm({...userForm, nombre: e.target.value})} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">Apellidos</label>
                    <input className="form-input" value={userForm.apellidos} onChange={e => setUserForm({...userForm, apellidos: e.target.value})} />
                  </div>
                </div>

                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">Email</label>
                    <input className="form-input" type="email" value={userForm.email} onChange={e => setUserForm({...userForm, email: e.target.value})} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">Teléfono</label>
                    <input className="form-input" value={userForm.telefono} onChange={e => setUserForm({...userForm, telefono: e.target.value})} />
                  </div>
                </div>

                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">Rol</label>
                    <select className="form-select" value={userForm.rol_id} onChange={e => setUserForm({...userForm, rol_id: e.target.value})}>
                      {roles.map(r => <option key={r.id} value={r.id}>{r.nombre}</option>)}
                    </select>
                  </div>
                  <div className="form-group">
                    <label className="form-label">{selectedUser ? 'Nueva Contraseña (dejar vacío para mantener)' : 'Contraseña'}</label>
                    <input className="form-input" type="password" value={userForm.password} onChange={e => setUserForm({...userForm, password: e.target.value})} />
                  </div>
                </div>

                <div className="modal-footer">
                  <button className="btn btn-secondary" onClick={() => setShowUserModal(false)}>Cancelar</button>
                  <button className="btn btn-primary" onClick={handleSaveUser}>
                    {selectedUser ? '💾 Guardar Cambios' : '👤 Crear Usuario'}
                  </button>
                </div>
              </div>
            </div>
          )}

          {tab === 'auditoria' && (
            <div className="table-container" style={{ border: 'none', boxShadow: 'none' }}>
              <table>
                <thead>
                  <tr><th>Fecha</th><th>Usuario</th><th>Módulo</th><th>Acción</th></tr>
                </thead>
                <tbody>
                  {auditoria.map(a => (
                    <tr key={a.id}>
                      <td style={{ fontSize: 12 }}>{new Date(a.fecha).toLocaleString('es-MX')}</td>
                      <td>{a.nombre || a.email || 'Sistema'}</td>
                      <td><span className="badge badge-gray">{a.modulo || 'general'}</span></td>
                      <td>{a.accion}</td>
                    </tr>
                  ))}
                  {auditoria.length === 0 && <tr><td colSpan="4" className="text-center">No hay registros de auditoría recientes.</td></tr>}
                </tbody>
              </table>
            </div>
          )}

        </div>
      )}
    </div>
  );
}
