import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, X, Trash2, Download, FileText } from 'lucide-react';

const ESTADOS = [
  { key:'borrador', label:'Borrador', color:'#94a3b8' },
  { key:'enviado',  label:'Enviado',  color:'#f59e0b' },
  { key:'firmado',  label:'Firmado',  color:'#10b981' },
  { key:'vencido',  label:'Vencido',  color:'#ef4444' },
  { key:'cancelado',label:'Cancelado',color:'#6b7280' },
];

const VACIO = { cliente_id:'', titulo:'', descripcion:'', monto:'', moneda:'COP', fecha_inicio:'', fecha_fin:'', estado:'borrador', clausulas:'[]', notas:'' };

export default function Contratos() {
  const [contratos, setContratos] = useState([]);
  const [clientes,  setClientes]  = useState([]);
  const [modal,     setModal]     = useState(false);
  const [form,      setForm]      = useState(VACIO);
  const [editId,    setEditId]    = useState(null);
  const [clausulas, setClausulas] = useState([{ titulo:'', texto:'' }]);
  const [filtro,    setFiltro]    = useState('');

  const cargar = () => api.get('/contratos').then(r => setContratos(r.data.data));
  useEffect(() => { cargar(); api.get('/clientes').then(r => setClientes(r.data.data)); }, []);

  function abrirModal(c = null) {
    if (c) {
      setForm({ cliente_id:c.cliente_id||'', titulo:c.titulo, descripcion:c.descripcion||'', monto:c.monto||'', moneda:c.moneda||'COP', fecha_inicio:c.fecha_inicio||'', fecha_fin:c.fecha_fin||'', estado:c.estado, clausulas:c.clausulas||'[]', notas:c.notas||'' });
      try { setClausulas(JSON.parse(c.clausulas||'[]')); } catch { setClausulas([{ titulo:'', texto:'' }]); }
      setEditId(c.id);
    } else {
      setForm(VACIO); setClausulas([{ titulo:'', texto:'' }]); setEditId(null);
    }
    setModal(true);
  }

  function actualizarClausula(i, campo, valor) {
    setClausulas(cs => cs.map((c,idx) => idx===i ? {...c,[campo]:valor} : c));
  }

  async function guardar(e) {
    e.preventDefault();
    const payload = { ...form, clausulas: JSON.stringify(clausulas.filter(c => c.titulo || c.texto)) };
    try {
      if (editId) { await api.put(`/contratos/${editId}`, payload); toast.success('Actualizado'); }
      else        { await api.post('/contratos', payload); toast.success('Contrato creado'); }
      setModal(false); cargar();
    } catch { toast.error('Error al guardar'); }
  }

  async function eliminar(id) {
    if (!confirm('¿Eliminar este contrato?')) return;
    await api.delete(`/contratos/${id}`); toast.success('Eliminado'); cargar();
  }

  function descargarPDF(id) {
    const token = localStorage.getItem('token') || sessionStorage.getItem('token');
    window.open(`/api/contratos/${id}/pdf`, '_blank');
  }

  const lista = filtro ? contratos.filter(c => c.estado === filtro) : contratos;

  return (
    <div style={{ padding:32 }}>
      <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:20 }}>
        <div>
          <h1 style={{ fontSize:'1.4rem', fontWeight:700, display:'flex', alignItems:'center', gap:8 }}><FileText size={22} color="#6366f1"/> Contratos</h1>
          <p style={{ color:'#64748b', fontSize:'.88rem', marginTop:2 }}>{contratos.length} contrato{contratos.length!==1?'s':''}</p>
        </div>
        <button onClick={() => abrirModal()} style={btn('#6366f1')}><Plus size={16}/> Nuevo contrato</button>
      </div>

      {/* Filtros */}
      <div style={{ display:'flex', gap:6, marginBottom:16, flexWrap:'wrap' }}>
        <button onClick={() => setFiltro('')} style={{ ...fBtn, background:filtro===''?'#6366f1':'#fff', color:filtro===''?'#fff':'#64748b', border:'1px solid '+(filtro===''?'#6366f1':'#e2e8f0') }}>Todos</button>
        {ESTADOS.map(e => <button key={e.key} onClick={() => setFiltro(e.key)} style={{ ...fBtn, background:filtro===e.key?e.color+'20':'#fff', color:filtro===e.key?e.color:'#64748b', border:'1px solid '+(filtro===e.key?e.color:'#e2e8f0') }}>{e.label}</button>)}
      </div>

      <div style={{ background:'#fff', borderRadius:12, boxShadow:'0 1px 4px rgba(0,0,0,.07)', overflow:'hidden' }}>
        <table style={{ width:'100%', borderCollapse:'collapse' }}>
          <thead><tr style={{ background:'#f8fafc' }}>
            {['Título','Cliente','Vigencia','Valor','Estado',''].map(h => <th key={h} style={th}>{h}</th>)}
          </tr></thead>
          <tbody>
            {lista.map(c => {
              const est = ESTADOS.find(e => e.key === c.estado) || ESTADOS[0];
              return (
                <tr key={c.id} style={{ borderTop:'1px solid #f1f5f9' }}>
                  <td style={td}><strong>{c.titulo}</strong></td>
                  <td style={td}>{c.cliente?.nombre || '—'}</td>
                  <td style={td}><span style={{ fontSize:'.82rem', color:'#64748b' }}>{c.fecha_inicio||'—'} → {c.fecha_fin||'...'}</span></td>
                  <td style={{ ...td, fontWeight:700, color:'#10b981' }}>{c.monto ? `$${Number(c.monto).toLocaleString('es')}` : '—'}</td>
                  <td style={td}><span style={{ background:est.color+'20', color:est.color, padding:'3px 10px', borderRadius:20, fontSize:'.78rem', fontWeight:700 }}>{est.label}</span></td>
                  <td style={td}>
                    <div style={{ display:'flex', gap:6 }}>
                      <button onClick={() => descargarPDF(c.id)} style={{ background:'#ede9fe', color:'#7c3aed', border:'none', borderRadius:6, padding:'5px 9px', cursor:'pointer' }}><Download size={13}/></button>
                      <button onClick={() => abrirModal(c)} style={{ background:'#f1f5f9', border:'none', borderRadius:6, padding:'5px 10px', fontSize:'.78rem', cursor:'pointer' }}>Editar</button>
                      <button onClick={() => eliminar(c.id)} style={{ background:'#fef2f2', color:'#ef4444', border:'none', borderRadius:6, padding:'5px 8px', cursor:'pointer' }}><Trash2 size={13}/></button>
                    </div>
                  </td>
                </tr>
              );
            })}
            {lista.length === 0 && <tr><td colSpan={6} style={{ padding:32, textAlign:'center', color:'#94a3b8' }}>No hay contratos</td></tr>}
          </tbody>
        </table>
      </div>

      {modal && (
        <div style={overlay}>
          <div style={modalBox}>
            <div style={{ display:'flex', justifyContent:'space-between', marginBottom:20 }}>
              <h2 style={{ fontSize:'1.1rem', fontWeight:700 }}>{editId ? 'Editar contrato' : 'Nuevo contrato'}</h2>
              <button onClick={() => setModal(false)} style={{ background:'none', border:'none' }}><X size={20}/></button>
            </div>
            <form onSubmit={guardar}>
              <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:12 }}>
                <F label="Título *" style={{ gridColumn:'1/-1' }}><input value={form.titulo} onChange={e => setForm({...form,titulo:e.target.value})} required style={inp}/></F>
                <F label="Cliente"><select value={form.cliente_id} onChange={e => setForm({...form,cliente_id:e.target.value})} style={inp}><option value="">Sin cliente</option>{clientes.map(c => <option key={c.id} value={c.id}>{c.nombre}</option>)}</select></F>
                <F label="Estado"><select value={form.estado} onChange={e => setForm({...form,estado:e.target.value})} style={inp}>{ESTADOS.map(e => <option key={e.key} value={e.key}>{e.label}</option>)}</select></F>
                <F label="Monto ($)"><input type="number" value={form.monto} onChange={e => setForm({...form,monto:e.target.value})} style={inp}/></F>
                <F label="Moneda"><select value={form.moneda} onChange={e => setForm({...form,moneda:e.target.value})} style={inp}><option>COP</option><option>USD</option></select></F>
                <F label="Inicio"><input type="date" value={form.fecha_inicio} onChange={e => setForm({...form,fecha_inicio:e.target.value})} style={inp}/></F>
                <F label="Fin"><input type="date" value={form.fecha_fin} onChange={e => setForm({...form,fecha_fin:e.target.value})} style={inp}/></F>
                <F label="Objeto del contrato" style={{ gridColumn:'1/-1' }}><textarea rows={2} value={form.descripcion} onChange={e => setForm({...form,descripcion:e.target.value})} style={{ ...inp, resize:'none' }}/></F>
              </div>

              <div style={{ marginTop:16 }}>
                <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:10 }}>
                  <label style={{ fontSize:'.88rem', fontWeight:700, color:'#374151' }}>Cláusulas</label>
                  <button type="button" onClick={() => setClausulas(cs => [...cs, { titulo:'', texto:'' }])} style={{ background:'#f1f5f9', border:'none', borderRadius:6, padding:'4px 10px', fontSize:'.8rem', cursor:'pointer' }}>+ Agregar</button>
                </div>
                {clausulas.map((c, i) => (
                  <div key={i} style={{ border:'1px solid #e2e8f0', borderRadius:8, padding:12, marginBottom:8 }}>
                    <input placeholder={`Título cláusula ${i+1}`} value={c.titulo} onChange={e => actualizarClausula(i,'titulo',e.target.value)} style={{ ...inp, marginBottom:6 }}/>
                    <textarea rows={2} placeholder="Texto..." value={c.texto} onChange={e => actualizarClausula(i,'texto',e.target.value)} style={{ ...inp, resize:'none' }}/>
                  </div>
                ))}
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

const overlay  = { position:'fixed', inset:0, background:'rgba(0,0,0,.45)', display:'flex', alignItems:'center', justifyContent:'center', zIndex:50 };
const modalBox = { background:'#fff', borderRadius:14, padding:28, width:600, maxHeight:'90vh', overflowY:'auto' };
const btn = bg => ({ display:'inline-flex', alignItems:'center', gap:6, padding:'9px 16px', background:bg, color:'#fff', border:'none', borderRadius:8, fontSize:'.88rem', fontWeight:600, cursor:'pointer' });
const fBtn = { padding:'6px 14px', borderRadius:8, fontSize:'.82rem', fontWeight:500, cursor:'pointer' };
const inp  = { width:'100%', padding:'8px 11px', border:'1px solid #e2e8f0', borderRadius:8, fontSize:'.9rem', outline:'none', boxSizing:'border-box', background:'#fafafa' };
const td   = { padding:'11px 16px', fontSize:'.9rem' };
const th   = { padding:'10px 16px', textAlign:'left', fontSize:'.8rem', color:'#64748b', fontWeight:600 };
function F({ label, children, style }) { return <div style={{ marginBottom:0, ...style }}><label style={{ display:'block', fontSize:'.8rem', fontWeight:600, color:'#374151', marginBottom:4 }}>{label}</label>{children}</div>; }
