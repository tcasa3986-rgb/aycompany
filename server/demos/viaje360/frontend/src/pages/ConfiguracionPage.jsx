import React, { useEffect, useState } from 'react';
import { Plus, Edit2, Users, Settings, Building, Globe, DollarSign, Image, HardDrive, Check } from 'lucide-react';
import api from '../services/api';
import useAuthStore from '../store/authStore';
import useConfigStore from '../store/configStore';
import toast from 'react-hot-toast';

export default function ConfiguracionPage() {
  const [tab, setTab] = useState('general'); // 'general' | 'usuarios' | 'sistema'
  const { usuario: me } = useAuthStore();
  
  // ─── Estado Usuarios ──────────────────────────────────────────
  const [usuarios, setUsuarios] = useState([]);
  const [roles,    setRoles]    = useState([]);
  const [uLoading, setULoading] = useState(true);
  const [uModal,   setUModal]   = useState(null);
  const [uForm,    setUForm]    = useState({ nombre:'', apellido:'', email:'', password:'', rol_id:'', telefono:'' });
  const [uSaving,  setUSaving]  = useState(false);
  const setU = (k, v) => setUForm(f => ({ ...f, [k]: v }));

  // ─── Estado General ───────────────────────────────────────────
  const [gLoading, setGLoading] = useState(true);
  const [gSaving,  setGSaving]  = useState(false);
  const [gForm,    setGForm]    = useState({
    empresa_nombre: '', documento_identidad: '', direccion: '', 
    telefono: '', logo_url: '', moneda_simbolo: '$', 
    impuesto_nombre: 'IGV', impuesto_porcentaje: 18.00
  });
  const setG = (k, v) => setGForm(f => ({ ...f, [k]: v }));

  const fetchTodo = async () => {
    try {
      const [u, r, g] = await Promise.all([
        api.get('/usuarios'), 
        api.get('/roles'),
        api.get('/configuracion_general')
      ]);
      setUsuarios(u.data); 
      setRoles(r.data);
      if (g.data) setGForm(g.data);
    } catch(e) { console.error(e); } finally { 
      setULoading(false); 
      setGLoading(false);
    }
  };
  
  useEffect(() => { fetchTodo(); }, []);

  // ─── Manejadores Usuarios ─────────────────────────────────────
  const handleUserSubmit = async (e) => {
    e.preventDefault(); setUSaving(true);
    try {
      if (uModal?.id) await api.put(`/usuarios/${uModal.id}`, uForm);
      else await api.post('/usuarios', uForm);
      setUModal(null); setUForm({ nombre:'', apellido:'', email:'', password:'', rol_id:'', telefono:'' });
      toast.success(uModal?.id ? 'Usuario actualizado' : 'Usuario creado');
      fetchTodo();
    } catch(e) { toast.error('Error al guardar el usuario'); } finally { setUSaving(false); }
  };

  const openEdit = (u) => {
    setUForm({ nombre:u.nombre, apellido:u.apellido, email:u.email, rol_id:u.rol_id, telefono:u.telefono||'', password:'' });
    setUModal(u);
  };

  // ─── Manejadores General ──────────────────────────────────────
  const handleGeneralSubmit = async (e) => {
    e.preventDefault(); setGSaving(true);
    try {
      const res = await api.put('/configuracion_general', gForm);
      setGForm(res.data.data ? res.data.data : res.data);
      await useConfigStore.getState().fetchConfig(); // Synchronize the new symbol dynamically
      toast.success('Configuración general guardada Exitosamente');
    } catch(e) { toast.error('Error al guardar la configuración'); } finally { setGSaving(false); }
  };


  const rolColor = { Administrador:'red', Gerente:'purple', Agente:'blue', Contador:'green' };

  return (
    <div className="animate-fade-in">
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Configuración</h1>
          <p className="page-subtitle">Administración global del sistema</p>
        </div>
      </div>

      {/* ─── TABS ─── */}
      <div style={{ display: 'flex', gap: 10, marginBottom: 20, borderBottom: '1px solid var(--border)' }}>
        <button onClick={()=>setTab('general')} style={{
          display: 'flex', alignItems: 'center', gap: 8, padding: '12px 18px', border: 'none', 
          background: 'transparent', cursor: 'pointer', fontWeight: tab==='general'?700:500,
          color: tab==='general'?'var(--color-primary)':'var(--text-secondary)',
          borderBottom: tab==='general'?'2px solid var(--color-primary)':'2px solid transparent',
        }}>
          <Building size={16}/> General
        </button>
        <button onClick={()=>setTab('usuarios')} style={{
          display: 'flex', alignItems: 'center', gap: 8, padding: '12px 18px', border: 'none', 
          background: 'transparent', cursor: 'pointer', fontWeight: tab==='usuarios'?700:500,
          color: tab==='usuarios'?'var(--color-primary)':'var(--text-secondary)',
          borderBottom: tab==='usuarios'?'2px solid var(--color-primary)':'2px solid transparent',
        }}>
          <Users size={16}/> Usuarios y Accesos
        </button>
        <button onClick={()=>setTab('sistema')} style={{
          display: 'flex', alignItems: 'center', gap: 8, padding: '12px 18px', border: 'none', 
          background: 'transparent', cursor: 'pointer', fontWeight: tab==='sistema'?700:500,
          color: tab==='sistema'?'var(--color-primary)':'var(--text-secondary)',
          borderBottom: tab==='sistema'?'2px solid var(--color-primary)':'2px solid transparent',
        }}>
          <HardDrive size={16}/> Acerca del Sistema
        </button>
      </div>

      {/* ─── TAB: GENERAL ─── */}
      {tab === 'general' && (
        <div style={{ maxWidth: 900 }} className="animate-fade-in-up">
          <form onSubmit={handleGeneralSubmit}>
            <div className="card" style={{ marginBottom: 20 }}>
              <div className="card-header border-bottom">
                <div style={{ display:'flex', alignItems:'center', gap:8 }}>
                  <Building size={20} style={{ color:'var(--color-primary)' }}/>
                  <div className="card-title">Datos de la Empresa</div>
                </div>
              </div>
              
              {gLoading ? <div className="skeleton" style={{ height:200, margin:20 }}/> : (
                <div className="p-4" style={{ display:'flex', flexWrap: 'wrap', gap: 30 }}>
                  <div style={{ flex:'2 1 400px', display:'flex', flexDirection:'column', gap: 16 }}>
                    <div className="form-group">
                      <label className="form-label required">Nombre de la Empresa</label>
                      <input className="form-control" value={gForm.empresa_nombre} required onChange={e=>setG('empresa_nombre', e.target.value)} />
                    </div>
                    
                    <div className="form-row">
                      <div className="form-group">
                        <label className="form-label">Número de Documento (RUC/NIT/CIF)</label>
                        <input className="form-control" value={gForm.documento_identidad || ''} onChange={e=>setG('documento_identidad', e.target.value)} />
                      </div>
                      <div className="form-group">
                        <label className="form-label">Teléfono</label>
                        <input className="form-control" value={gForm.telefono || ''} onChange={e=>setG('telefono', e.target.value)} />
                      </div>
                    </div>
                    
                    <div className="form-group">
                      <label className="form-label">Dirección Fiscal / Oficina</label>
                      <input className="form-control" value={gForm.direccion || ''} onChange={e=>setG('direccion', e.target.value)} />
                    </div>
                  </div>

                  <div style={{ flex:'1 1 200px', display:'flex', flexDirection:'column', alignItems:'center', background:'var(--bg-base)', padding:20, borderRadius:12, border:'1px dashed var(--border)' }}>
                    <div style={{ width:120, height:120, borderRadius:12, background:'white', display:'flex', alignItems:'center', justifyContent:'center', border:'1px solid var(--border)', overflow:'hidden', marginBottom: 16 }}>
                      {gForm.logo_url ? (
                        <img src={gForm.logo_url} alt="Logo" style={{ maxWidth:'100%', maxHeight:'100%', objectFit:'contain' }} />
                      ) : (
                        <Image size={40} color="var(--border)" />
                      )}
                    </div>
                    <div className="form-group" style={{ width: '100%' }}>
                      <label className="form-label" style={{ textAlign:'center' }}>URL del Logotipo</label>
                      <input className="form-control" placeholder="https://ejemplo.com/logo.png" style={{ fontSize:'0.75rem' }} value={gForm.logo_url || ''} onChange={e=>setG('logo_url', e.target.value)} />
                    </div>
                  </div>
                </div>
              )}
            </div>

            <div className="card" style={{ marginBottom: 20 }}>
              <div className="card-header border-bottom">
                <div style={{ display:'flex', alignItems:'center', gap:8 }}>
                  <Globe size={20} style={{ color:'var(--color-primary)' }}/>
                  <div className="card-title">Configuración Regional y Comercial</div>
                </div>
              </div>
              
              {!gLoading && (
                <div className="p-4" style={{ display:'grid', gridTemplateColumns:'repeat(auto-fit, minmax(240px, 1fr))', gap: 20 }}>
                  <div className="form-group">
                    <label className="form-label required">Símbolo Monetario</label>
                    <div style={{ position:'relative' }}>
                      <DollarSign size={16} style={{ position:'absolute', top:12, left:12, color:'var(--text-muted)' }} />
                      <input className="form-control" style={{ paddingLeft:36 }} value={gForm.moneda_simbolo} required onChange={e=>setG('moneda_simbolo', e.target.value)} />
                    </div>
                    <div className="text-xs text-muted mt-1">Ej: $, S/, €, Bs.</div>
                  </div>
                  
                  <div className="form-group">
                    <label className="form-label required">Nombre del Impuesto</label>
                    <input className="form-control" value={gForm.impuesto_nombre} required onChange={e=>setG('impuesto_nombre', e.target.value)} />
                    <div className="text-xs text-muted mt-1">Ej: IGV, IVA, IVA/R</div>
                  </div>
                  
                  <div className="form-group">
                    <label className="form-label required">Porcentaje del Impuesto (%)</label>
                    <input type="number" step="0.01" className="form-control" value={gForm.impuesto_porcentaje} required onChange={e=>setG('impuesto_porcentaje', e.target.value)} />
                  </div>
                </div>
              )}
            </div>
            
            <div style={{ display:'flex', justifyContent:'flex-end' }}>
              <button type="submit" className="btn btn-primary" disabled={gSaving}>
                {gSaving ? 'Guardando...' : <><Check size={16}/> Guardar Configuración</>}
              </button>
            </div>
          </form>
        </div>
      )}

      {/* ─── TAB: USUARIOS ─── */}
      {tab === 'usuarios' && (
        <div style={{ maxWidth: 1000 }} className="animate-fade-in-up">
          <div className="card" style={{ marginBottom:20 }}>
            <div className="card-header">
              <div style={{ display:'flex', alignItems:'center', gap:8 }}>
                <Users size={20} style={{ color:'var(--color-primary)' }}/>
                <div className="card-title">Usuarios del Sistema</div>
              </div>
              <button className="btn btn-primary btn-sm" onClick={() => { setUForm({nombre:'',apellido:'',email:'',password:'',rol_id:'',telefono:''}); setUModal(true); }}>
                <Plus size={14}/> Nuevo Usuario
              </button>
            </div>
            {uLoading ? <div className="skeleton" style={{ height:80, margin:16 }}/> : (
              <table>
                <thead>
                  <tr><th>Usuario</th><th>Email</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                  {usuarios.map(u => (
                    <tr key={u.id}>
                      <td>
                        <div style={{ display:'flex', alignItems:'center', gap:10 }}>
                          <div className="avatar avatar-sm" style={{ background:`linear-gradient(135deg,#0EA5E9,#8B5CF6)` }}>
                            {u.nombre?.[0]}{u.apellido?.[0]}
                          </div>
                          <div>
                            <div className="font-semibold text-sm">{u.nombre} {u.apellido}</div>
                            {u.telefono && <div className="text-xs text-muted">{u.telefono}</div>}
                          </div>
                        </div>
                      </td>
                      <td className="text-sm">{u.email}</td>
                      <td>
                        <span className={`badge badge-${rolColor[u.rol?.nombre] || 'gray'}`}>
                          {u.rol?.nombre || '—'}
                        </span>
                      </td>
                      <td>
                        <span className={`badge badge-${u.activo ? 'green' : 'gray'}`}>
                          {u.activo ? 'Activo' : 'Inactivo'}
                        </span>
                      </td>
                      <td>
                        {u.id !== me?.id && (
                          <button className="btn btn-ghost btn-icon btn-sm" onClick={() => openEdit(u)}>
                            <Edit2 size={14}/>
                          </button>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </div>
        </div>
      )}

      {/* ─── TAB: SISTEMA ─── */}
      {tab === 'sistema' && (
        <div style={{ maxWidth: 800 }} className="animate-fade-in-up">
          <div className="card">
            <div className="card-header">
              <div style={{ display:'flex', alignItems:'center', gap:8 }}>
                <Settings size={20} style={{ color:'var(--color-secondary)' }}/>
                <div className="card-title">Información Técnica del Sistema</div>
              </div>
            </div>
            <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fit, minmax(300px, 1fr))', gap:16, padding:24 }}>
              {[
                ['Sistema',  'CRM Viaje 360'],
                ['Versión',  '1.1.0'],
                ['Backend',  'Node.js + Express'],
                ['Frontend', 'React 18 + Vite'],
                ['Base de Datos', 'MySQL 8.0'],
                ['ORM',     'Sequelize 6'],
              ].map(([k, v]) => (
                <div key={k} style={{ background:'var(--bg-input)', borderRadius:'var(--radius-sm)', padding:'12px 16px', border:'1px solid var(--border)' }}>
                  <div className="text-xs text-muted">{k}</div>
                  <div className="font-semibold text-sm mt-1">{v}</div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* ─── MODAL: USUARIO ─── */}
      {uModal && (
        <div className="modal-overlay" onClick={e => e.target === e.currentTarget && setUModal(null)}>
          <div className="modal animate-fade-in-up">
            <div className="modal-header">
              <h2 className="modal-title">{uModal?.id ? 'Editar Usuario' : 'Nuevo Usuario'}</h2>
              <button className="modal-close" onClick={() => setUModal(null)}>✕</button>
            </div>
            <form onSubmit={handleUserSubmit}>
              <div className="modal-body">
                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label required">Nombre</label>
                    <input className="form-control" value={uForm.nombre} required onChange={e=>setU('nombre',e.target.value)} />
                  </div>
                  <div className="form-group">
                    <label className="form-label required">Apellido</label>
                    <input className="form-control" value={uForm.apellido} required onChange={e=>setU('apellido',e.target.value)} />
                  </div>
                </div>
                <div className="form-group">
                  <label className="form-label required">Email</label>
                  <input type="email" className="form-control" value={uForm.email} required onChange={e=>setU('email',e.target.value)} />
                </div>
                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">Teléfono</label>
                    <input className="form-control" value={uForm.telefono} onChange={e=>setU('telefono',e.target.value)} />
                  </div>
                  <div className="form-group">
                    <label className="form-label required">Rol</label>
                    <select className="form-control" value={uForm.rol_id} required onChange={e=>setU('rol_id',e.target.value)}>
                      <option value="">Seleccionar</option>
                      {roles.map(r=><option key={r.id} value={r.id}>{r.nombre}</option>)}
                    </select>
                  </div>
                </div>
                <div className="form-group">
                  <label className="form-label">{uModal?.id ? 'Nueva Contraseña (dejar vacío = no cambia)' : 'Contraseña'}</label>
                  <input type="password" className="form-control" value={uForm.password} onChange={e=>setU('password',e.target.value)} />
                </div>
              </div>
              <div className="modal-footer">
                <button type="button" className="btn btn-secondary" onClick={() => setUModal(null)}>Cancelar</button>
                <button type="submit" className="btn btn-primary" disabled={uSaving}>
                  {uSaving ? 'Guardando...' : (uModal?.id ? 'Actualizar' : 'Crear Usuario')}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
