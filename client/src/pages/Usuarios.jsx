import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, X, Trash2, Shield, Users as UsersIcon } from 'lucide-react';
import { useAuthStore } from '../store/authStore';

const VACIO = { nombre:'', email:'', password:'', rol:'soporte' };
const ROLES = [
  { key:'admin',    label:'Admin',    desc:'Acceso completo', color:'#7c3aed', bg:'#ede9fe' },
  { key:'vendedor', label:'Vendedor', desc:'Leads y clientes', color:'#0284c7', bg:'#dbeafe' },
  { key:'soporte',  label:'Soporte',  desc:'Tickets y clientes', color:'#059669', bg:'#d1fae5' },
];

export default function Usuarios() {
  const [users,  setUsers]  = useState([]);
  const [modal,  setModal]  = useState(false);
  const [form,   setForm]   = useState(VACIO);
  const [editId, setEditId] = useState(null);
  const me = useAuthStore(s => s.user);

  const cargar = () => api.get('/usuarios').then(r => setUsers(r.data.data));
  useEffect(() => { cargar(); }, []);

  function abrirModal(u = null) {
    if (u) { setForm({ nombre:u.nombre, email:u.email, password:'', rol:u.rol }); setEditId(u.id); }
    else   { setForm(VACIO); setEditId(null); }
    setModal(true);
  }

  async function guardar(e) {
    e.preventDefault();
    const payload = { ...form };
    if (!payload.password) delete payload.password;
    try {
      if (editId) { await api.put(`/usuarios/${editId}`, payload); toast.success('Usuario actualizado'); }
      else        { await api.post('/usuarios', payload); toast.success('Usuario creado'); }
      setModal(false); cargar();
    } catch (err) { toast.error(err.response?.data?.msg || 'Error'); }
  }

  async function eliminar(id) {
    if (!confirm('¿Eliminar este usuario?')) return;
    try {
      await api.delete(`/usuarios/${id}`); toast.success('Usuario eliminado'); cargar();
    } catch (err) { toast.error(err.response?.data?.msg || 'Error'); }
  }

  return (
    <div style={{ padding:32, maxWidth:900 }}>
      <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:24 }}>
        <div>
          <h1 style={{ fontSize:'1.4rem', fontWeight:700, display:'flex', alignItems:'center', gap:8 }}><UsersIcon size={22} color="#7c3aed"/> Usuarios del sistema</h1>
          <p style={{ color:'#64748b', fontSize:'.88rem', marginTop:2 }}>Gestiona quién tiene acceso y qué puede hacer</p>
        </div>
        <button onClick={() => abrirModal()} style={btn('#7c3aed')}><Plus size={16}/> Nuevo usuario</button>
      </div>

      {/* Explicación de roles */}
      <div style={{ display:'grid', gridTemplateColumns:'repeat(3,1fr)', gap:12, marginBottom:24 }}>
        {ROLES.map(r => (
          <div key={r.key} style={{ background:'#fff', borderRadius:10, padding:'14px 16px', boxShadow:'0 1px 4px rgba(0,0,0,.07)' }}>
            <div style={{ display:'flex', alignItems:'center', gap:8, marginBottom:6 }}>
              <Shield size={14} color={r.color}/>
              <span style={{ fontWeight:700, fontSize:'.9rem', color:r.color }}>{r.label}</span>
            </div>
            <div style={{ fontSize:'.8rem', color:'#64748b' }}>{r.desc}</div>
          </div>
        ))}
      </div>

      <div style={{ background:'#fff', borderRadius:12, boxShadow:'0 1px 4px rgba(0,0,0,.07)', overflow:'hidden' }}>
        <table style={{ width:'100%', borderCollapse:'collapse' }}>
          <thead><tr style={{ background:'#f8fafc' }}>
            {['Nombre','Email','Rol','Creado',''].map(h => <th key={h} style={th}>{h}</th>)}
          </tr></thead>
          <tbody>
            {users.map(u => {
              const rol = ROLES.find(r => r.key === u.rol) || ROLES[0];
              const esYo = String(u.id) === String(me?.id);
              return (
                <tr key={u.id} style={{ borderTop:'1px solid #f1f5f9', background:esYo?'#fafff7':'transparent' }}>
                  <td style={td}><strong>{u.nombre}</strong>{esYo && <span style={{ fontSize:'.72rem', color:'#10b981', marginLeft:6 }}>● Tú</span>}</td>
                  <td style={td}>{u.email}</td>
                  <td style={td}><span style={{ background:rol.bg, color:rol.color, padding:'3px 10px', borderRadius:20, fontSize:'.78rem', fontWeight:700 }}>{rol.label}</span></td>
                  <td style={{ ...td, color:'#94a3b8', fontSize:'.82rem' }}>{u.created_at ? new Date(u.created_at).toLocaleDateString('es') : '—'}</td>
                  <td style={td}>
                    <div style={{ display:'flex', gap:6 }}>
                      <button onClick={() => abrirModal(u)} style={{ background:'#f1f5f9', border:'none', borderRadius:6, padding:'5px 10px', fontSize:'.78rem', cursor:'pointer' }}>Editar</button>
                      {!esYo && <button onClick={() => eliminar(u.id)} style={{ background:'#fef2f2', color:'#ef4444', border:'none', borderRadius:6, padding:'5px 8px', cursor:'pointer' }}><Trash2 size={13}/></button>}
                    </div>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>

      {modal && (
        <div style={overlay}>
          <div style={modalBox}>
            <div style={{ display:'flex', justifyContent:'space-between', marginBottom:20 }}>
              <h2 style={{ fontSize:'1.1rem', fontWeight:700 }}>{editId ? 'Editar usuario' : 'Nuevo usuario'}</h2>
              <button onClick={() => setModal(false)} style={{ background:'none', border:'none' }}><X size={20}/></button>
            </div>
            <form onSubmit={guardar}>
              <F label="Nombre *"><input value={form.nombre} onChange={e => setForm({...form,nombre:e.target.value})} required style={inp}/></F>
              <F label="Email *"><input type="email" value={form.email} onChange={e => setForm({...form,email:e.target.value})} required style={inp}/></F>
              <F label={editId ? 'Nueva contraseña (dejar vacío para no cambiar)' : 'Contraseña *'}>
                <input type="password" value={form.password} onChange={e => setForm({...form,password:e.target.value})} required={!editId} style={inp} minLength={editId?0:6}/>
              </F>
              <F label="Rol">
                <select value={form.rol} onChange={e => setForm({...form,rol:e.target.value})} style={inp}>
                  {ROLES.map(r => <option key={r.key} value={r.key}>{r.label} — {r.desc}</option>)}
                </select>
              </F>
              <div style={{ display:'flex', justifyContent:'flex-end', gap:10, marginTop:20 }}>
                <button type="button" onClick={() => setModal(false)} style={btn('#94a3b8')}>Cancelar</button>
                <button type="submit" style={btn('#7c3aed')}>Guardar</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

const overlay  = { position:'fixed', inset:0, background:'rgba(0,0,0,.45)', display:'flex', alignItems:'center', justifyContent:'center', zIndex:50 };
const modalBox = { background:'#fff', borderRadius:14, padding:28, width:440 };
const btn  = bg => ({ display:'inline-flex', alignItems:'center', gap:6, padding:'9px 16px', background:bg, color:'#fff', border:'none', borderRadius:8, fontSize:'.88rem', fontWeight:600, cursor:'pointer' });
const inp  = { width:'100%', padding:'8px 11px', border:'1px solid #e2e8f0', borderRadius:8, fontSize:'.9rem', outline:'none', boxSizing:'border-box', background:'#fafafa' };
const td   = { padding:'11px 16px', fontSize:'.9rem' };
const th   = { padding:'10px 16px', textAlign:'left', fontSize:'.8rem', color:'#64748b', fontWeight:600 };
function F({ label, children }) { return <div style={{ marginBottom:14 }}><label style={{ display:'block', fontSize:'.8rem', fontWeight:600, color:'#374151', marginBottom:5 }}>{label}</label>{children}</div>; }
