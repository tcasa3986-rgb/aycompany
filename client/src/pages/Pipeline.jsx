import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { TrendingUp, Phone, Mail } from 'lucide-react';

const COLUMNAS = [
  { key:'nuevo',             label:'Nuevos',           color:'#6366f1', bg:'#ede9fe' },
  { key:'contactado',        label:'Contactados',      color:'#f59e0b', bg:'#fef3c7' },
  { key:'interesado',        label:'Interesados',      color:'#8b5cf6', bg:'#f3e8ff' },
  { key:'reunion_agendada',  label:'Reunión agendada', color:'#10b981', bg:'#d1fae5' },
  { key:'reunion_realizada', label:'Reunión hecha',    color:'#059669', bg:'#bbf7d0' },
  { key:'cliente',           label:'Clientes',         color:'#16a34a', bg:'#dcfce7' },
];

const TODOS_ESTADOS = ['nuevo','contactado','respondio','interesado','reunion_agendada','reunion_realizada','cliente','sin_respuesta','descartado'];

export default function Pipeline() {
  const [leads,   setLeads]   = useState([]);
  const [moving,  setMoving]  = useState(null);

  const cargar = () => api.get('/leads').then(r => setLeads(r.data.data || []));
  useEffect(() => { cargar(); }, []);

  async function moverLead(lead, nuevoEstado) {
    if (lead.estado === nuevoEstado) return;
    setMoving(lead.id);
    try {
      await api.put(`/leads/${lead.id}`, { estado: nuevoEstado });
      toast.success(`Movido a "${nuevoEstado}"`);
      cargar();
    } catch { toast.error('Error al mover'); }
    setMoving(null);
  }

  const totalActivos = leads.filter(l => !['descartado','sin_respuesta'].includes(l.estado)).length;
  const totalClientes = leads.filter(l => l.estado === 'cliente').length;

  return (
    <div style={{ padding:'28px 24px' }}>
      <div style={{ display:'flex', alignItems:'center', gap:12, marginBottom:20 }}>
        <TrendingUp size={22} color="#6366f1"/>
        <div>
          <h1 style={{ fontSize:'1.4rem', fontWeight:700, color:'#1e293b', margin:0 }}>Pipeline CRM</h1>
          <p style={{ color:'#64748b', fontSize:'.88rem', margin:0 }}>{totalActivos} leads activos · {totalClientes} convertidos a cliente</p>
        </div>
      </div>

      {/* Kanban */}
      <div style={{ display:'flex', gap:12, overflowX:'auto', paddingBottom:12 }}>
        {COLUMNAS.map(col => {
          const items = leads.filter(l => l.estado === col.key);
          return (
            <div key={col.key} style={{ minWidth:240, flex:'0 0 240px' }}>
              {/* Encabezado columna */}
              <div style={{ background:col.bg, borderRadius:'10px 10px 0 0', padding:'10px 14px', display:'flex', justifyContent:'space-between', alignItems:'center' }}>
                <span style={{ fontSize:'.85rem', fontWeight:700, color:col.color }}>{col.label}</span>
                <span style={{ background:col.color, color:'#fff', borderRadius:20, padding:'2px 8px', fontSize:'.75rem', fontWeight:700 }}>{items.length}</span>
              </div>

              {/* Tarjetas */}
              <div style={{ background:'#f8fafc', borderRadius:'0 0 10px 10px', minHeight:400, padding:8, display:'flex', flexDirection:'column', gap:8 }}>
                {items.map(l => (
                  <div key={l.id} style={{ background:'#fff', borderRadius:10, padding:'12px 14px', boxShadow:'0 1px 4px rgba(0,0,0,.07)', border:`2px solid ${moving===l.id?col.color:'transparent'}` }}>
                    <div style={{ fontWeight:600, fontSize:'.9rem', color:'#1e293b', marginBottom:4 }}>{l.nombre}</div>
                    {l.empresa && <div style={{ fontSize:'.78rem', color:'#64748b', marginBottom:4 }}>{l.empresa}</div>}
                    <div style={{ display:'flex', gap:6, marginBottom:8 }}>
                      {l.telefono && <a href={`https://wa.me/${l.telefono.replace(/\D/g,'')}`} target="_blank" rel="noopener noreferrer" style={{ display:'inline-flex', alignItems:'center', gap:3, fontSize:'.75rem', color:'#25d366', textDecoration:'none' }}><Phone size={11}/> WA</a>}
                      {l.email && <a href={`mailto:${l.email}`} style={{ display:'inline-flex', alignItems:'center', gap:3, fontSize:'.75rem', color:'#6366f1', textDecoration:'none' }}><Mail size={11}/> Email</a>}
                    </div>
                    {/* Mover a otro estado */}
                    <select value={l.estado} onChange={e => moverLead(l, e.target.value)}
                      disabled={moving === l.id}
                      style={{ width:'100%', padding:'5px 8px', border:'1px solid #e2e8f0', borderRadius:6, fontSize:'.78rem', background:'#fafafa', cursor:'pointer' }}>
                      {TODOS_ESTADOS.map(s => <option key={s} value={s}>{s.replace(/_/g,' ')}</option>)}
                    </select>
                  </div>
                ))}
                {items.length === 0 && (
                  <div style={{ textAlign:'center', padding:'20px 0', color:'#cbd5e1', fontSize:'.82rem' }}>Sin leads aquí</div>
                )}
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
