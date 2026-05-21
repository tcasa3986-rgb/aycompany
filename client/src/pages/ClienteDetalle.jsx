import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import api from '../api/axios';
import { ArrowLeft, DollarSign, Key, Headphones, FolderOpen, FileText, CheckCircle, XCircle } from 'lucide-react';

function StatCard({ icon: Icon, label, value, color }) {
  return (
    <div style={{ background:'#fff', borderRadius:12, padding:'16px 20px', boxShadow:'0 1px 4px rgba(0,0,0,.07)', display:'flex', alignItems:'center', gap:12 }}>
      <div style={{ background:color+'18', borderRadius:9, padding:9 }}><Icon size={18} color={color}/></div>
      <div><div style={{ fontSize:'1.3rem', fontWeight:700, lineHeight:1 }}>{value}</div><div style={{ fontSize:'.78rem', color:'#64748b', marginTop:2 }}>{label}</div></div>
    </div>
  );
}

const TABS = [
  { key:'licencias',  label:'Licencias',  icon:'🔑' },
  { key:'pagos',      label:'Pagos',      icon:'💳' },
  { key:'facturas',   label:'Facturas',   icon:'📄' },
  { key:'tickets',    label:'Tickets',    icon:'🎫' },
  { key:'proyectos',  label:'Proyectos',  icon:'📁' },
  { key:'contratos',  label:'Contratos',  icon:'📋' },
];

export default function ClienteDetalle() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [data, setData] = useState(null);
  const [tab,  setTab]  = useState('licencias');

  useEffect(() => {
    api.get(`/clientes/${id}/ficha`).then(r => setData(r.data.data));
  }, [id]);

  if (!data) return <div style={{ padding:32, color:'#64748b' }}>Cargando ficha...</div>;

  const { cliente, licencias, pagos, facturas, tickets, proyectos, contratos, stats } = data;
  const ahora = new Date();

  return (
    <div style={{ padding:32 }}>
      {/* Header */}
      <div style={{ display:'flex', alignItems:'center', gap:12, marginBottom:24 }}>
        <button onClick={() => navigate('/clientes')} style={{ background:'#f1f5f9', border:'none', borderRadius:8, padding:'8px 12px', cursor:'pointer', display:'flex', alignItems:'center', gap:5, color:'#374151', fontSize:'.88rem' }}>
          <ArrowLeft size={16}/> Volver
        </button>
        <div style={{ flex:1 }}>
          <h1 style={{ fontSize:'1.4rem', fontWeight:700, color:'#1e293b', margin:0 }}>{cliente.nombre}</h1>
          <div style={{ color:'#64748b', fontSize:'.85rem', marginTop:3 }}>
            {cliente.empresa && <span>{cliente.empresa} · </span>}
            {cliente.email && <span>{cliente.email} · </span>}
            {cliente.telefono && <span>{cliente.telefono}</span>}
          </div>
        </div>
        <div style={{ fontSize:'.78rem', color:'#94a3b8' }}>
          Cliente desde {new Date(cliente.created_at).toLocaleDateString('es-CO', { day:'numeric', month:'long', year:'numeric' })}
        </div>
      </div>

      {/* Stats */}
      <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fit,minmax(160px,1fr))', gap:12, marginBottom:24 }}>
        <StatCard icon={DollarSign}  label="Total pagado"       value={`$${Number(stats.totalPagado).toLocaleString('es')}`}  color="#10b981"/>
        <StatCard icon={Key}         label="Licencias activas"  value={stats.licenciasActivas}   color="#6366f1"/>
        <StatCard icon={Headphones}  label="Tickets abiertos"   value={stats.ticketsAbiertos}    color="#ef4444"/>
        <StatCard icon={FolderOpen}  label="Proyectos activos"  value={stats.proyectosActivos}   color="#f59e0b"/>
        <StatCard icon={FileText}    label="Total contratos"    value={stats.totalContratos}     color="#8b5cf6"/>
      </div>

      {/* Tabs */}
      <div style={{ display:'flex', gap:4, marginBottom:20, background:'#fff', borderRadius:10, padding:4, boxShadow:'0 1px 4px rgba(0,0,0,.06)', flexWrap:'wrap' }}>
        {TABS.map(t => (
          <button key={t.key} onClick={() => setTab(t.key)}
            style={{ padding:'8px 14px', borderRadius:8, border:'none', background:tab===t.key?'#6366f1':'transparent', color:tab===t.key?'#fff':'#64748b', fontWeight:tab===t.key?700:500, fontSize:'.85rem', cursor:'pointer', whiteSpace:'nowrap' }}>
            {t.icon} {t.label}
          </button>
        ))}
      </div>

      {/* Tab: Licencias */}
      {tab === 'licencias' && (
        <div>
          {licencias.length === 0 && <Empty text="No hay licencias"/>}
          {licencias.map(l => {
            const valida = l.valida;
            return (
              <div key={l.id} style={card}>
                <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', flexWrap:'wrap', gap:10 }}>
                  <div>
                    <div style={{ fontWeight:700 }}>{l.producto?.nombre}</div>
                    <div style={{ fontSize:'.8rem', color:'#94a3b8', fontFamily:'monospace', marginTop:2 }}>{l.license_key}</div>
                  </div>
                  <div style={{ display:'flex', alignItems:'center', gap:10 }}>
                    <span style={{ ...badge, background:valida?'#d1fae5':'#fee2e2', color:valida?'#065f46':'#991b1b' }}>
                      {valida ? <CheckCircle size={13}/> : <XCircle size={13}/>} {valida ? 'Activa' : 'Vencida'}
                    </span>
                    <span style={{ fontSize:'.82rem', color:'#64748b' }}>Vence: {l.fecha_vencimiento}</span>
                    {valida && <span style={{ fontSize:'.82rem', color:'#059669', fontWeight:600 }}>{l.dias_restantes}d restantes</span>}
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      )}

      {/* Tab: Pagos */}
      {tab === 'pagos' && (
        <div style={{ background:'#fff', borderRadius:12, boxShadow:'0 1px 4px rgba(0,0,0,.07)', overflow:'hidden' }}>
          <table style={{ width:'100%', borderCollapse:'collapse' }}>
            <thead><tr style={{ background:'#f8fafc' }}>
              {['Fecha','Producto','Meses','Método','Monto'].map(h => <th key={h} style={th}>{h}</th>)}
            </tr></thead>
            <tbody>
              {pagos.length === 0 && <tr><td colSpan={5}><Empty text="No hay pagos"/></td></tr>}
              {pagos.map(p => (
                <tr key={p.id} style={{ borderTop:'1px solid #f1f5f9' }}>
                  <td style={td}>{new Date(p.fecha_pago).toLocaleDateString('es')}</td>
                  <td style={td}>{p.licencia?.producto?.nombre || '—'}</td>
                  <td style={td}>{p.meses || 1}</td>
                  <td style={td}><span style={{ background:'#f1f5f9', padding:'2px 9px', borderRadius:12, fontSize:'.78rem', textTransform:'capitalize' }}>{p.metodo_pago}</span></td>
                  <td style={{ ...td, fontWeight:700, color:'#10b981' }}>${Number(p.monto).toLocaleString('es')}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {/* Tab: Facturas */}
      {tab === 'facturas' && (
        <div>
          {facturas.length === 0 && <Empty text="No hay facturas"/>}
          {facturas.map(f => (
            <div key={f.id} style={{ ...card, display:'flex', justifyContent:'space-between', alignItems:'center' }}>
              <div>
                <div style={{ fontFamily:'monospace', fontWeight:700, color:'#6366f1' }}>{f.numero}</div>
                <div style={{ fontSize:'.8rem', color:'#64748b' }}>{f.concepto} · {f.fecha}</div>
              </div>
              <div style={{ fontWeight:700, color:'#10b981' }}>${Number(f.monto).toLocaleString('es')}</div>
            </div>
          ))}
        </div>
      )}

      {/* Tab: Tickets */}
      {tab === 'tickets' && (
        <div>
          {tickets.length === 0 && <Empty text="No hay tickets de soporte"/>}
          {tickets.map(t => {
            const colors = { abierto:['#fef3c7','#d97706'], en_proceso:['#dbeafe','#1d4ed8'], cerrado:['#d1fae5','#065f46'] };
            const [bg, color] = colors[t.estado] || colors.abierto;
            return (
              <div key={t.id} style={card}>
                <div style={{ display:'flex', justifyContent:'space-between', flexWrap:'wrap', gap:8 }}>
                  <div>
                    <div style={{ fontWeight:600 }}>{t.asunto}</div>
                    <div style={{ fontSize:'.8rem', color:'#64748b', marginTop:2 }}>{new Date(t.created_at).toLocaleDateString('es-CO')}</div>
                  </div>
                  <span style={{ background:bg, color, padding:'3px 10px', borderRadius:20, fontSize:'.78rem', fontWeight:700, height:'fit-content' }}>{t.estado}</span>
                </div>
                {t.respuesta && <div style={{ marginTop:10, background:'#f0fdf4', borderRadius:8, padding:'8px 12px', fontSize:'.85rem', color:'#374151' }}><strong>Respuesta: </strong>{t.respuesta}</div>}
              </div>
            );
          })}
        </div>
      )}

      {/* Tab: Proyectos */}
      {tab === 'proyectos' && (
        <div>
          {proyectos.length === 0 && <Empty text="No hay proyectos asociados"/>}
          {proyectos.map(p => {
            const COLORES = { planeacion:'#6366f1', en_curso:'#059669', pausado:'#d97706', completado:'#0284c7', cancelado:'#dc2626' };
            const color = COLORES[p.estado] || '#6366f1';
            return (
              <div key={p.id} style={card}>
                <div style={{ display:'flex', justifyContent:'space-between', flexWrap:'wrap', gap:8 }}>
                  <div>
                    <div style={{ fontWeight:700 }}>{p.nombre}</div>
                    {p.descripcion && <div style={{ fontSize:'.82rem', color:'#64748b', marginTop:2 }}>{p.descripcion}</div>}
                  </div>
                  <div style={{ display:'flex', alignItems:'center', gap:10 }}>
                    <span style={{ background:color+'20', color, padding:'3px 10px', borderRadius:20, fontSize:'.78rem', fontWeight:700 }}>{p.estado}</span>
                    {p.presupuesto && <span style={{ fontWeight:700, color:'#10b981' }}>${Number(p.presupuesto).toLocaleString('es')}</span>}
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      )}

      {/* Tab: Contratos */}
      {tab === 'contratos' && (
        <div>
          {contratos.length === 0 && <Empty text="No hay contratos"/>}
          {contratos.map(c => {
            const COLORES = { borrador:'#94a3b8', enviado:'#f59e0b', firmado:'#10b981', vencido:'#ef4444', cancelado:'#6b7280' };
            const color = COLORES[c.estado] || '#94a3b8';
            return (
              <div key={c.id} style={card}>
                <div style={{ display:'flex', justifyContent:'space-between', flexWrap:'wrap', gap:8 }}>
                  <div>
                    <div style={{ fontWeight:700 }}>{c.titulo}</div>
                    {c.fecha_inicio && <div style={{ fontSize:'.8rem', color:'#64748b' }}>{c.fecha_inicio} → {c.fecha_fin || '...'}</div>}
                  </div>
                  <div style={{ display:'flex', alignItems:'center', gap:10 }}>
                    <span style={{ background:color+'20', color, padding:'3px 10px', borderRadius:20, fontSize:'.78rem', fontWeight:700 }}>{c.estado}</span>
                    {c.monto && <span style={{ fontWeight:700, color:'#10b981' }}>${Number(c.monto).toLocaleString('es')}</span>}
                    <button onClick={() => window.open(`/api/contratos/${c.id}/pdf`,'_blank')} style={{ background:'#ede9fe', color:'#7c3aed', border:'none', borderRadius:7, padding:'5px 12px', fontSize:'.78rem', fontWeight:700, cursor:'pointer' }}>PDF</button>
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}

function Empty({ text }) { return <div style={{ textAlign:'center', padding:40, color:'#94a3b8', fontSize:'.9rem' }}>{text}</div>; }
const card  = { background:'#fff', borderRadius:12, padding:'14px 18px', marginBottom:10, boxShadow:'0 1px 4px rgba(0,0,0,.06)' };
const badge = { display:'inline-flex', alignItems:'center', gap:4, padding:'4px 10px', borderRadius:20, fontSize:'.78rem', fontWeight:700 };
const td    = { padding:'11px 16px', fontSize:'.9rem' };
const th    = { padding:'10px 16px', textAlign:'left', fontSize:'.8rem', color:'#64748b', fontWeight:600 };
