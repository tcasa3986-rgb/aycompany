import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, X, Trash2, CheckSquare, Square, ChevronDown, ChevronUp, FolderOpen } from 'lucide-react';

const ESTADOS_PROY = [
  { key:'planeacion', label:'Planeación',   color:'#6366f1', bg:'#ede9fe' },
  { key:'en_curso',   label:'En curso',     color:'#059669', bg:'#d1fae5' },
  { key:'pausado',    label:'Pausado',      color:'#d97706', bg:'#fef3c7' },
  { key:'completado', label:'Completado',   color:'#0284c7', bg:'#dbeafe' },
  { key:'cancelado',  label:'Cancelado',    color:'#dc2626', bg:'#fee2e2' },
];
const PRIORIDADES = ['baja','media','alta','critica'];
const PRIO_COLOR = { baja:'#94a3b8', media:'#f59e0b', alta:'#ef4444', critica:'#7c3aed' };

const VACIO = { nombre:'', cliente_id:'', descripcion:'', estado:'planeacion', fecha_inicio:'', fecha_fin:'', presupuesto:'', notas:'' };
const VACIO_TAREA = { titulo:'', descripcion:'', responsable:'', prioridad:'media', fecha_limite:'', estado:'pendiente' };

export default function Proyectos() {
  const [proyectos,  setProyectos]  = useState([]);
  const [clientes,   setClientes]   = useState([]);
  const [modal,      setModal]      = useState(false);
  const [form,       setForm]       = useState(VACIO);
  const [editId,     setEditId]     = useState(null);
  const [expandido,  setExpandido]  = useState(null);
  const [nuevaTarea, setNuevaTarea] = useState({});
  const [filtro,     setFiltro]     = useState('');

  const cargar = () => api.get('/proyectos').then(r => setProyectos(r.data.data));
  useEffect(() => {
    cargar();
    api.get('/clientes').then(r => setClientes(r.data.data));
  }, []);

  function abrirModal(p = null) {
    if (p) { setForm({ nombre:p.nombre, cliente_id:p.cliente_id||'', descripcion:p.descripcion||'', estado:p.estado, fecha_inicio:p.fecha_inicio||'', fecha_fin:p.fecha_fin||'', presupuesto:p.presupuesto||'', notas:p.notas||'' }); setEditId(p.id); }
    else   { setForm(VACIO); setEditId(null); }
    setModal(true);
  }

  async function guardar(e) {
    e.preventDefault();
    try {
      if (editId) { await api.put(`/proyectos/${editId}`, form); toast.success('Proyecto actualizado'); }
      else        { await api.post('/proyectos', form); toast.success('Proyecto creado'); }
      setModal(false); cargar();
    } catch { toast.error('Error al guardar'); }
  }

  async function eliminar(id) {
    if (!confirm('¿Eliminar este proyecto y sus tareas?')) return;
    await api.delete(`/proyectos/${id}`); toast.success('Eliminado'); cargar();
  }

  async function crearTarea(proyId) {
    const t = nuevaTarea[proyId] || {};
    if (!t.titulo?.trim()) return toast.error('El título es requerido');
    try {
      await api.post(`/proyectos/${proyId}/tareas`, t);
      setNuevaTarea(n => ({ ...n, [proyId]: VACIO_TAREA }));
      cargar();
    } catch { toast.error('Error al crear tarea'); }
  }

  async function toggleTarea(proyId, tareaId, estadoActual) {
    const nuevoEstado = estadoActual === 'completado' ? 'pendiente' : 'completado';
    await api.put(`/proyectos/${proyId}/tareas/${tareaId}`, { estado: nuevoEstado });
    cargar();
  }

  async function eliminarTarea(proyId, tareaId) {
    await api.delete(`/proyectos/${proyId}/tareas/${tareaId}`); cargar();
  }

  const lista = filtro ? proyectos.filter(p => p.estado === filtro) : proyectos;
  const completadas = (tareas) => tareas?.filter(t => t.estado === 'completado').length || 0;

  return (
    <div style={{ padding:32 }}>
      <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:20 }}>
        <div>
          <h1 style={{ fontSize:'1.4rem', fontWeight:700, display:'flex', alignItems:'center', gap:8 }}><FolderOpen size={22} color="#6366f1"/> Proyectos</h1>
          <p style={{ color:'#64748b', fontSize:'.88rem', marginTop:2 }}>{proyectos.length} proyecto{proyectos.length !== 1 ? 's' : ''} en total</p>
        </div>
        <button onClick={() => abrirModal()} style={btn('#6366f1')}><Plus size={16}/> Nuevo proyecto</button>
      </div>

      {/* Filtros */}
      <div style={{ display:'flex', gap:6, marginBottom:16, flexWrap:'wrap' }}>
        <button onClick={() => setFiltro('')} style={{ ...filterBtn, background: filtro===''?'#6366f1':'#fff', color: filtro===''?'#fff':'#64748b', border:'1px solid '+(filtro===''?'#6366f1':'#e2e8f0') }}>Todos</button>
        {ESTADOS_PROY.map(e => (
          <button key={e.key} onClick={() => setFiltro(e.key)} style={{ ...filterBtn, background: filtro===e.key?e.bg:'#fff', color: filtro===e.key?e.color:'#64748b', border:'1px solid '+(filtro===e.key?e.color:'#e2e8f0'), fontWeight: filtro===e.key?700:500 }}>{e.label}</button>
        ))}
      </div>

      {lista.map(p => {
        const est = ESTADOS_PROY.find(e => e.key === p.estado) || ESTADOS_PROY[0];
        const open = expandido === p.id;
        const total = p.tareas?.length || 0;
        const done  = completadas(p.tareas);
        const pct   = total > 0 ? Math.round((done/total)*100) : 0;

        return (
          <div key={p.id} style={{ background:'#fff', borderRadius:14, marginBottom:12, boxShadow:'0 1px 6px rgba(0,0,0,.07)', overflow:'hidden' }}>
            {/* Header */}
            <div onClick={() => setExpandido(open ? null : p.id)} style={{ display:'flex', alignItems:'center', gap:14, padding:'16px 20px', cursor:'pointer' }}>
              <div style={{ width:4, height:48, borderRadius:4, background:est.color, flexShrink:0 }}/>
              <div style={{ flex:1, minWidth:0 }}>
                <div style={{ display:'flex', alignItems:'center', gap:10, flexWrap:'wrap' }}>
                  <span style={{ fontWeight:700, fontSize:'1rem', color:'#1e293b' }}>{p.nombre}</span>
                  <span style={{ background:est.bg, color:est.color, padding:'2px 10px', borderRadius:20, fontSize:'.75rem', fontWeight:700 }}>{est.label}</span>
                  {p.cliente && <span style={{ fontSize:'.8rem', color:'#64748b' }}>· {p.cliente.nombre}</span>}
                </div>
                {total > 0 && (
                  <div style={{ display:'flex', alignItems:'center', gap:8, marginTop:6 }}>
                    <div style={{ flex:1, maxWidth:200, height:4, background:'#f1f5f9', borderRadius:4 }}>
                      <div style={{ width:`${pct}%`, height:'100%', background:est.color, borderRadius:4 }}/>
                    </div>
                    <span style={{ fontSize:'.75rem', color:'#64748b' }}>{done}/{total} tareas · {pct}%</span>
                  </div>
                )}
              </div>
              <div style={{ display:'flex', gap:8, alignItems:'center', flexShrink:0 }}>
                {p.presupuesto && <span style={{ fontSize:'.85rem', fontWeight:700, color:'#10b981' }}>${Number(p.presupuesto).toLocaleString('es')}</span>}
                {p.fecha_fin && <span style={{ fontSize:'.78rem', color:'#94a3b8' }}>{p.fecha_fin}</span>}
                <button onClick={e => {e.stopPropagation(); abrirModal(p);}} style={{ background:'#f1f5f9', border:'none', borderRadius:6, padding:'5px 10px', fontSize:'.78rem', cursor:'pointer' }}>Editar</button>
                <button onClick={e => {e.stopPropagation(); eliminar(p.id);}} style={{ background:'#fef2f2', color:'#ef4444', border:'none', borderRadius:6, padding:'5px 8px', cursor:'pointer' }}><Trash2 size={13}/></button>
                {open ? <ChevronUp size={16} color="#94a3b8"/> : <ChevronDown size={16} color="#94a3b8"/>}
              </div>
            </div>

            {/* Tareas expandidas */}
            {open && (
              <div style={{ borderTop:'1px solid #f1f5f9', padding:'16px 20px 20px' }}>
                {p.descripcion && <p style={{ fontSize:'.88rem', color:'#64748b', marginBottom:14 }}>{p.descripcion}</p>}

                <div style={{ marginBottom:12 }}>
                  {(p.tareas || []).map(t => {
                    const done = t.estado === 'completado';
                    return (
                      <div key={t.id} style={{ display:'flex', alignItems:'center', gap:10, padding:'8px 12px', background:done?'#f8fafc':'#fff', borderRadius:8, marginBottom:6, border:'1px solid #f1f5f9' }}>
                        <button onClick={() => toggleTarea(p.id, t.id, t.estado)} style={{ background:'none', border:'none', cursor:'pointer', color:done?'#10b981':'#94a3b8', flexShrink:0, display:'flex' }}>
                          {done ? <CheckSquare size={18}/> : <Square size={18}/>}
                        </button>
                        <div style={{ flex:1, minWidth:0 }}>
                          <span style={{ fontSize:'.9rem', color:done?'#94a3b8':'#374151', textDecoration:done?'line-through':'none' }}>{t.titulo}</span>
                          {t.responsable && <span style={{ fontSize:'.75rem', color:'#94a3b8', marginLeft:8 }}>· {t.responsable}</span>}
                          {t.fecha_limite && <span style={{ fontSize:'.75rem', color:'#94a3b8', marginLeft:8 }}>· {t.fecha_limite}</span>}
                        </div>
                        <span style={{ background:PRIO_COLOR[t.prioridad]+'22', color:PRIO_COLOR[t.prioridad], fontSize:'.7rem', fontWeight:700, padding:'2px 8px', borderRadius:20 }}>{t.prioridad}</span>
                        <button onClick={() => eliminarTarea(p.id, t.id)} style={{ background:'none', border:'none', color:'#ef4444', cursor:'pointer' }}><Trash2 size={13}/></button>
                      </div>
                    );
                  })}
                </div>

                {/* Nueva tarea */}
                <div style={{ display:'flex', gap:8, flexWrap:'wrap' }}>
                  <input placeholder="Nueva tarea..." value={nuevaTarea[p.id]?.titulo || ''} onChange={e => setNuevaTarea(n => ({...n, [p.id]: {...(n[p.id]||VACIO_TAREA), titulo:e.target.value}}))}
                    style={{ flex:1, minWidth:140, padding:'7px 10px', border:'1px solid #e2e8f0', borderRadius:8, fontSize:'.88rem' }}/>
                  <input placeholder="Responsable" value={nuevaTarea[p.id]?.responsable || ''} onChange={e => setNuevaTarea(n => ({...n, [p.id]: {...(n[p.id]||VACIO_TAREA), responsable:e.target.value}}))}
                    style={{ width:120, padding:'7px 10px', border:'1px solid #e2e8f0', borderRadius:8, fontSize:'.88rem' }}/>
                  <input type="date" value={nuevaTarea[p.id]?.fecha_limite || ''} onChange={e => setNuevaTarea(n => ({...n, [p.id]: {...(n[p.id]||VACIO_TAREA), fecha_limite:e.target.value}}))}
                    style={{ width:130, padding:'7px 10px', border:'1px solid #e2e8f0', borderRadius:8, fontSize:'.88rem' }}/>
                  <select value={nuevaTarea[p.id]?.prioridad || 'media'} onChange={e => setNuevaTarea(n => ({...n, [p.id]: {...(n[p.id]||VACIO_TAREA), prioridad:e.target.value}}))}
                    style={{ padding:'7px 10px', border:'1px solid #e2e8f0', borderRadius:8, fontSize:'.88rem' }}>
                    {PRIORIDADES.map(p => <option key={p} value={p}>{p}</option>)}
                  </select>
                  <button onClick={() => crearTarea(p.id)} style={btn('#10b981')}><Plus size={14}/> Agregar</button>
                </div>
              </div>
            )}
          </div>
        );
      })}

      {lista.length === 0 && (
        <div style={{ textAlign:'center', padding:60, color:'#94a3b8' }}>
          <FolderOpen size={40} style={{ marginBottom:12, opacity:.4 }}/>
          <div>No hay proyectos {filtro ? `en estado "${filtro}"` : ''}</div>
        </div>
      )}

      {/* Modal crear/editar */}
      {modal && (
        <div style={overlay}>
          <div style={modalBox}>
            <div style={{ display:'flex', justifyContent:'space-between', marginBottom:20 }}>
              <h2 style={{ fontSize:'1.1rem', fontWeight:700 }}>{editId ? 'Editar proyecto' : 'Nuevo proyecto'}</h2>
              <button onClick={() => setModal(false)} style={{ background:'none', border:'none' }}><X size={20}/></button>
            </div>
            <form onSubmit={guardar}>
              <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:12 }}>
                <Field label="Nombre *" style={{ gridColumn:'1/-1' }}>
                  <input value={form.nombre} onChange={e => setForm({...form, nombre:e.target.value})} required style={inp}/>
                </Field>
                <Field label="Cliente">
                  <select value={form.cliente_id} onChange={e => setForm({...form, cliente_id:e.target.value})} style={inp}>
                    <option value="">Sin cliente</option>
                    {clientes.map(c => <option key={c.id} value={c.id}>{c.nombre}</option>)}
                  </select>
                </Field>
                <Field label="Estado">
                  <select value={form.estado} onChange={e => setForm({...form, estado:e.target.value})} style={inp}>
                    {ESTADOS_PROY.map(e => <option key={e.key} value={e.key}>{e.label}</option>)}
                  </select>
                </Field>
                <Field label="Fecha inicio"><input type="date" value={form.fecha_inicio} onChange={e => setForm({...form, fecha_inicio:e.target.value})} style={inp}/></Field>
                <Field label="Fecha fin"><input type="date" value={form.fecha_fin} onChange={e => setForm({...form, fecha_fin:e.target.value})} style={inp}/></Field>
                <Field label="Presupuesto ($)" style={{ gridColumn:'1/-1' }}>
                  <input type="number" value={form.presupuesto} onChange={e => setForm({...form, presupuesto:e.target.value})} style={inp} placeholder="0"/>
                </Field>
                <Field label="Descripción" style={{ gridColumn:'1/-1' }}>
                  <textarea rows={2} value={form.descripcion} onChange={e => setForm({...form, descripcion:e.target.value})} style={{ ...inp, resize:'none' }}/>
                </Field>
              </div>
              <div style={{ display:'flex', justifyContent:'flex-end', gap:10, marginTop:20 }}>
                <button type="button" onClick={() => setModal(false)} style={btn('#94a3b8')}>Cancelar</button>
                <button type="submit" style={btn('#6366f1')}>Guardar</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

const overlay   = { position:'fixed', inset:0, background:'rgba(0,0,0,.45)', display:'flex', alignItems:'center', justifyContent:'center', zIndex:50 };
const modalBox  = { background:'#fff', borderRadius:14, padding:28, width:560, maxHeight:'90vh', overflowY:'auto' };
const btn = bg => ({ display:'inline-flex', alignItems:'center', gap:6, padding:'9px 16px', background:bg, color:'#fff', border:'none', borderRadius:8, fontSize:'.88rem', fontWeight:600, cursor:'pointer' });
const filterBtn = { padding:'6px 14px', borderRadius:8, fontSize:'.82rem', fontWeight:500, cursor:'pointer' };
const inp = { width:'100%', padding:'8px 11px', border:'1px solid #e2e8f0', borderRadius:8, fontSize:'.9rem', outline:'none', boxSizing:'border-box', background:'#fafafa' };
function Field({ label, children, style }) { return <div style={{ marginBottom:4, ...style }}><label style={{ display:'block', fontSize:'.8rem', fontWeight:600, color:'#374151', marginBottom:4 }}>{label}</label>{children}</div>; }
