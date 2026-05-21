import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { AlertTriangle, Clock, Send, SendHorizonal, DollarSign, Users } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

function StatCard({ icon: Icon, label, value, color }) {
  return (
    <div style={{ background:'#fff', borderRadius:12, padding:'18px 22px', boxShadow:'0 1px 4px rgba(0,0,0,.07)', display:'flex', alignItems:'center', gap:14 }}>
      <div style={{ background:color+'18', borderRadius:10, padding:10, flexShrink:0 }}><Icon size={20} color={color}/></div>
      <div>
        <div style={{ fontSize:'1.5rem', fontWeight:700, color:'#1e293b', lineHeight:1 }}>{value}</div>
        <div style={{ fontSize:'.8rem', color:'#64748b', marginTop:2 }}>{label}</div>
      </div>
    </div>
  );
}

export default function Cartera() {
  const [data,     setData]     = useState(null);
  const [tab,      setTab]      = useState('vencidas');
  const [enviando, setEnviando] = useState(null);
  const navigate = useNavigate();

  const cargar = () => api.get('/cartera').then(r => setData(r.data.data));
  useEffect(() => { cargar(); }, []);

  async function recordatorio(licId, nombre) {
    setEnviando(licId);
    try {
      const r = await api.post('/cartera/recordatorio', { licencia_id: licId });
      toast.success(r.data.msg);
    } catch { toast.error('Error al enviar'); }
    setEnviando(null);
  }

  async function masivo() {
    if (!confirm('¿Enviar recordatorio a TODOS los clientes con licencia vencida?')) return;
    setEnviando('masivo');
    try {
      const r = await api.post('/cartera/masivo');
      toast.success(r.data.msg);
    } catch { toast.error('Error al enviar masivo'); }
    setEnviando(null);
  }

  if (!data) return <div style={{ padding:32, color:'#64748b' }}>Cargando...</div>;

  const lista = tab === 'vencidas' ? data.vencidas : data.porVencer;

  return (
    <div style={{ padding:32 }}>
      <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:24 }}>
        <div>
          <h1 style={{ fontSize:'1.4rem', fontWeight:700, color:'#1e293b' }}>Cartera & Cobros</h1>
          <p style={{ color:'#64748b', fontSize:'.88rem', marginTop:2 }}>Control de licencias vencidas y por vencer</p>
        </div>
        <button onClick={masivo} disabled={enviando === 'masivo'}
          style={{ display:'inline-flex', alignItems:'center', gap:6, padding:'9px 18px', background:'#ef4444', color:'#fff', border:'none', borderRadius:8, fontSize:'.88rem', fontWeight:700, cursor:'pointer' }}>
          <SendHorizonal size={15}/> {enviando === 'masivo' ? 'Enviando...' : 'Recordatorio masivo'}
        </button>
      </div>

      <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fit,minmax(180px,1fr))', gap:14, marginBottom:24 }}>
        <StatCard icon={AlertTriangle} label="Licencias vencidas"   value={data.stats.totalVencidas}  color="#ef4444"/>
        <StatCard icon={Clock}         label="Vencen en 30 días"    value={data.stats.totalPorVencer} color="#f59e0b"/>
        <StatCard icon={DollarSign}    label="Valor en riesgo/mes"  value={`$${Number(data.stats.valorRiesgo).toLocaleString('es')}`} color="#8b5cf6"/>
        <StatCard icon={Users}         label="Sin email registrado" value={data.stats.sinEmail}       color="#94a3b8"/>
      </div>

      {/* Tabs */}
      <div style={{ display:'flex', gap:4, marginBottom:16, background:'#fff', borderRadius:10, padding:4, boxShadow:'0 1px 4px rgba(0,0,0,.06)', width:'fit-content' }}>
        {[['vencidas',`Vencidas (${data.vencidas.length})`],['porVencer',`Por vencer (${data.porVencer.length})`]].map(([k,label]) => (
          <button key={k} onClick={() => setTab(k)}
            style={{ padding:'8px 18px', borderRadius:8, border:'none', background:tab===k?'#ef4444':'transparent', color:tab===k?'#fff':'#64748b', fontWeight:tab===k?700:500, fontSize:'.88rem', cursor:'pointer' }}>
            {label}
          </button>
        ))}
      </div>

      <div style={{ background:'#fff', borderRadius:12, boxShadow:'0 1px 4px rgba(0,0,0,.07)', overflow:'hidden' }}>
        <table style={{ width:'100%', borderCollapse:'collapse' }}>
          <thead>
            <tr style={{ background:'#f8fafc' }}>
              {['Cliente','Producto','Vencimiento','Días','Precio/mes',''].map(h => (
                <th key={h} style={{ padding:'10px 16px', textAlign:'left', fontSize:'.8rem', color:'#64748b', fontWeight:600 }}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {lista.map(l => {
              const overdue = l.dias < 0;
              return (
                <tr key={l.id} style={{ borderTop:'1px solid #f1f5f9' }}>
                  <td style={td}>
                    <button onClick={() => navigate(`/clientes/${l.cliente_id}`)} style={{ background:'none', border:'none', fontWeight:600, color:'#6366f1', cursor:'pointer', fontSize:'.9rem', padding:0 }}>{l.cliente}</button>
                    {l.email && <div style={{ fontSize:'.75rem', color:'#94a3b8' }}>{l.email}</div>}
                  </td>
                  <td style={td}>{l.producto}</td>
                  <td style={td}><span style={{ fontFamily:'monospace', fontSize:'.88rem' }}>{l.fecha_vencimiento}</span></td>
                  <td style={td}>
                    <span style={{ background:overdue?'#fef2f2':'#fffbeb', color:overdue?'#dc2626':'#d97706', padding:'3px 10px', borderRadius:20, fontSize:'.78rem', fontWeight:700 }}>
                      {overdue ? `${Math.abs(l.dias)}d vencida` : `${l.dias}d`}
                    </span>
                  </td>
                  <td style={{ ...td, fontWeight:700, color:'#10b981' }}>${l.precio_mensual.toLocaleString('es')}</td>
                  <td style={td}>
                    <button onClick={() => recordatorio(l.id, l.cliente)} disabled={enviando === l.id}
                      style={{ display:'inline-flex', alignItems:'center', gap:5, padding:'5px 12px', background:'#ede9fe', color:'#7c3aed', border:'none', borderRadius:7, fontSize:'.78rem', fontWeight:700, cursor:'pointer' }}>
                      <Send size={12}/> {enviando === l.id ? '...' : 'Recordar'}
                    </button>
                  </td>
                </tr>
              );
            })}
            {lista.length === 0 && (
              <tr><td colSpan={6} style={{ padding:32, textAlign:'center', color:'#94a3b8' }}>
                {tab === 'vencidas' ? '✅ No hay licencias vencidas' : '✅ No hay licencias por vencer en 30 días'}
              </td></tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

const td = { padding:'12px 16px', fontSize:'.9rem' };
