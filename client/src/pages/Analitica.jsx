import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Brain, TrendingUp, AlertTriangle, CheckCircle, Users, DollarSign, RefreshCw, Sparkles } from 'lucide-react';

function RiskBadge({ riesgo }) {
    const map = { alto: ['#fef2f2','#ef4444','Alto'], medio: ['#fefce8','#f59e0b','Medio'], bajo: ['#f0fdf4','#22c55e','Bajo'] };
    const [bg, color, label] = map[riesgo] || map.bajo;
    return <span style={{ background: bg, color, padding: '3px 9px', borderRadius: 20, fontSize: '.75rem', fontWeight: 700 }}>{label}</span>;
}

function Bar({ value, max, color }) {
    return (
        <div style={{ background: '#f1f5f9', borderRadius: 4, height: 8, overflow: 'hidden' }}>
            <div style={{ width: `${max > 0 ? Math.round(value / max * 100) : 0}%`, background: color, height: '100%', borderRadius: 4, transition: 'width .4s' }}/>
        </div>
    );
}

export default function Analitica() {
    const [data,      setData]      = useState(null);
    const [insights,  setInsights]  = useState([]);
    const [loadingIA, setLoadingIA] = useState(false);
    const [loading,   setLoading]   = useState(true);
    const navigate = useNavigate();

    async function cargar() {
        setLoading(true);
        try {
            const r = await api.get('/analitica/predicciones');
            setData(r.data.data);
        } catch { toast.error('Error al cargar análisis'); }
        setLoading(false);
    }

    async function pedirInsights() {
        if (!data) return;
        setLoadingIA(true);
        try {
            const r = await api.post('/analitica/insights-ia', { resumen: data.resumen, enRiesgo: data.enRiesgo, historial: data.historial });
            setInsights(r.data.data || []);
        } catch { toast.error('Error al generar insights'); }
        setLoadingIA(false);
    }

    useEffect(() => { cargar(); }, []);

    if (loading) return <div style={{ padding: 40, textAlign: 'center', color: '#64748b' }}>Analizando datos...</div>;
    if (!data)   return null;

    const { resumen, enRiesgo, medioRiesgo, historial, forecast, segmentos } = data;
    const maxBar = Math.max(...[...historial, ...forecast].map(m => m.total), 1);

    return (
        <div style={{ padding: 32, maxWidth: 1100 }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24, flexWrap: 'wrap', gap: 12 }}>
                <div>
                    <h1 style={{ fontSize: '1.4rem', fontWeight: 700, display: 'flex', alignItems: 'center', gap: 8 }}>
                        <Brain size={22} color="#6366f1"/> Analítica Predictiva
                    </h1>
                    <p style={{ color: '#64748b', fontSize: '.88rem', marginTop: 2 }}>IA aplicada a tus datos para prevenir churn y maximizar ingresos</p>
                </div>
                <div style={{ display: 'flex', gap: 8 }}>
                    <button onClick={cargar} style={{ ...btn('#f1f5f9', '#374151'), gap: 5 }}><RefreshCw size={14}/> Actualizar</button>
                    <button onClick={pedirInsights} disabled={loadingIA} style={{ ...btn('#6366f1', '#fff'), gap: 6 }}>
                        <Sparkles size={14}/> {loadingIA ? 'Analizando con IA...' : 'Insights con IA'}
                    </button>
                </div>
            </div>

            {/* Stat cards */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit,minmax(180px,1fr))', gap: 14, marginBottom: 24 }}>
                <StatCard icon={Users}         label="Clientes activos"   value={resumen.totalClientes}   color="#6366f1"/>
                <StatCard icon={AlertTriangle} label="En riesgo alto"     value={resumen.enRiesgoAlto}    color="#ef4444" sub={`${resumen.pctRiesgo}% del total`}/>
                <StatCard icon={DollarSign}    label="MRR total"          value={`$${Number(resumen.mrrTotal).toLocaleString('es')}`} color="#10b981"/>
                <StatCard icon={AlertTriangle} label="MRR en riesgo"      value={`$${Number(resumen.mrrEnRiesgo).toLocaleString('es')}`} color="#f59e0b"/>
                <StatCard icon={TrendingUp}    label="Vencen este mes"    value={resumen.vencenEsteMes}   color="#8b5cf6"/>
                <StatCard icon={CheckCircle}   label="Tickets pendientes" value={resumen.ticketsPendientes} color="#0284c7"/>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20, marginBottom: 24 }}>
                {/* Segmentos */}
                <div style={card}>
                    <div style={cardTitle}>Segmentación de clientes</div>
                    {[
                        { label: 'Riesgo alto',  value: segmentos.alto,  color: '#ef4444', max: resumen.totalClientes },
                        { label: 'Riesgo medio', value: segmentos.medio, color: '#f59e0b', max: resumen.totalClientes },
                        { label: 'Estables',     value: segmentos.bajo,  color: '#22c55e', max: resumen.totalClientes },
                    ].map(s => (
                        <div key={s.label} style={{ marginBottom: 12 }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '.85rem', marginBottom: 4 }}>
                                <span style={{ color: '#374151' }}>{s.label}</span>
                                <strong style={{ color: s.color }}>{s.value}</strong>
                            </div>
                            <Bar value={s.value} max={s.max} color={s.color}/>
                        </div>
                    ))}
                </div>

                {/* Ingresos + forecast */}
                <div style={card}>
                    <div style={cardTitle}>Ingresos históricos y proyección</div>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
                        {historial.map(m => (
                            <div key={m.mes} style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                                <span style={{ width: 70, fontSize: '.75rem', color: '#64748b', flexShrink: 0 }}>{m.mes}</span>
                                <div style={{ flex: 1 }}><Bar value={m.total} max={maxBar} color="#6366f1"/></div>
                                <span style={{ width: 80, fontSize: '.75rem', textAlign: 'right', color: '#374151' }}>${Number(m.total).toLocaleString('es')}</span>
                            </div>
                        ))}
                        {forecast.map(m => (
                            <div key={m.mes} style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                                <span style={{ width: 70, fontSize: '.75rem', color: '#0284c7', flexShrink: 0 }}>{m.mes} *</span>
                                <div style={{ flex: 1 }}><Bar value={m.total} max={maxBar} color="#bae6fd"/></div>
                                <span style={{ width: 80, fontSize: '.75rem', textAlign: 'right', color: '#0284c7' }}>${Number(m.total).toLocaleString('es')}</span>
                            </div>
                        ))}
                        <p style={{ fontSize: '.72rem', color: '#94a3b8', marginTop: 4 }}>* Proyección basada en tendencia actual</p>
                    </div>
                </div>
            </div>

            {/* Insights IA */}
            {insights.length > 0 && (
                <div style={{ ...card, marginBottom: 24, background: 'linear-gradient(135deg,#ede9fe,#e0e7ff)' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 14 }}>
                        <Sparkles size={18} color="#6366f1"/>
                        <span style={{ fontWeight: 700, fontSize: '.95rem', color: '#1e1b4b' }}>Recomendaciones de IA</span>
                    </div>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit,minmax(280px,1fr))', gap: 12 }}>
                        {insights.map((ins, i) => (
                            <div key={i} style={{ background: '#fff', borderRadius: 10, padding: 14 }}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 6 }}>
                                    <strong style={{ fontSize: '.88rem', color: '#1e293b' }}>{ins.titulo}</strong>
                                    <span style={{ background: ins.impacto === 'alto' ? '#fef2f2' : '#fefce8', color: ins.impacto === 'alto' ? '#ef4444' : '#d97706', padding: '2px 8px', borderRadius: 20, fontSize: '.72rem', fontWeight: 700 }}>
                                        {ins.impacto?.toUpperCase()}
                                    </span>
                                </div>
                                <p style={{ fontSize: '.82rem', color: '#64748b', margin: '0 0 6px' }}>{ins.accion}</p>
                                <span style={{ fontSize: '.72rem', color: '#94a3b8' }}>Plazo: {ins.plazo}</span>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Clientes en alto riesgo */}
            {enRiesgo.length > 0 && (
                <div style={{ ...card, marginBottom: 20 }}>
                    <div style={cardTitle}> Clientes en alto riesgo de churn ({enRiesgo.length})</div>
                    <div style={{ overflowX: 'auto' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead><tr style={{ background: '#fef2f2' }}>
                                {['Cliente','Razón','Score','MRR','Último pago','Riesgo',''].map(h => <th key={h} style={th}>{h}</th>)}
                            </tr></thead>
                            <tbody>
                                {enRiesgo.map(c => (
                                    <tr key={c.id} style={{ borderTop: '1px solid #f1f5f9' }}>
                                        <td style={td}><strong>{c.nombre}</strong>{c.empresa && <div style={{ fontSize: '.75rem', color: '#94a3b8' }}>{c.empresa}</div>}</td>
                                        <td style={td}><span style={{ fontSize: '.8rem', color: '#64748b' }}>{c.razon}</span></td>
                                        <td style={td}>
                                            <div style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                                                <div style={{ width: 40, background: '#f1f5f9', borderRadius: 4, height: 6, overflow: 'hidden' }}>
                                                    <div style={{ width: `${c.score}%`, background: c.score >= 70 ? '#ef4444' : '#f59e0b', height: '100%' }}/>
                                                </div>
                                                <span style={{ fontSize: '.78rem', fontWeight: 700, color: c.score >= 70 ? '#ef4444' : '#f59e0b' }}>{c.score}</span>
                                            </div>
                                        </td>
                                        <td style={td}><span style={{ fontWeight: 700, color: '#10b981', fontSize: '.85rem' }}>${Number(c.mrr).toLocaleString('es')}</span></td>
                                        <td style={{ ...td, color: '#94a3b8', fontSize: '.82rem' }}>{c.ultimoPago || '—'}{c.diasSinPago < 999 && <span style={{ color: '#f59e0b', marginLeft: 4 }}>({c.diasSinPago}d)</span>}</td>
                                        <td style={td}><RiskBadge riesgo={c.riesgo}/></td>
                                        <td style={td}>
                                            <button onClick={() => navigate(`/clientes/${c.id}`)} style={{ background: '#f1f5f9', border: 'none', borderRadius: 6, padding: '5px 10px', fontSize: '.78rem', cursor: 'pointer' }}>Ver ficha</button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}

            {/* Riesgo medio */}
            {medioRiesgo.length > 0 && (
                <div style={card}>
                    <div style={cardTitle}>Clientes en riesgo medio — acción preventiva ({medioRiesgo.length})</div>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill,minmax(260px,1fr))', gap: 10 }}>
                        {medioRiesgo.map(c => (
                            <div key={c.id} onClick={() => navigate(`/clientes/${c.id}`)} style={{ background: '#fffbeb', borderRadius: 8, padding: '10px 14px', cursor: 'pointer', border: '1px solid #fde68a' }}>
                                <div style={{ fontWeight: 600, fontSize: '.88rem', color: '#1e293b' }}>{c.nombre}</div>
                                <div style={{ fontSize: '.75rem', color: '#92400e', marginTop: 3 }}>{c.razon}</div>
                                <div style={{ fontSize: '.75rem', color: '#10b981', fontWeight: 700, marginTop: 4 }}>MRR: ${Number(c.mrr).toLocaleString('es')}</div>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}

function StatCard({ icon: Icon, label, value, color, sub }) {
    return (
        <div style={{ background: '#fff', borderRadius: 12, padding: '16px 18px', boxShadow: '0 1px 4px rgba(0,0,0,.07)' }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: 10, marginBottom: 8 }}>
                <div style={{ background: color + '18', borderRadius: 8, padding: 8 }}><Icon size={18} color={color}/></div>
                <div style={{ fontSize: '.78rem', color: '#64748b' }}>{label}</div>
            </div>
            <div style={{ fontSize: '1.4rem', fontWeight: 700, lineHeight: 1, color: '#1e293b' }}>{value}</div>
            {sub && <div style={{ fontSize: '.72rem', color: '#94a3b8', marginTop: 3 }}>{sub}</div>}
        </div>
    );
}

const btn    = (bg, color) => ({ display: 'inline-flex', alignItems: 'center', padding: '8px 14px', background: bg, color, border: 'none', borderRadius: 8, fontSize: '.85rem', fontWeight: 600, cursor: 'pointer' });
const card   = { background: '#fff', borderRadius: 12, padding: '18px 20px', boxShadow: '0 1px 4px rgba(0,0,0,.07)' };
const cardTitle = { fontWeight: 700, fontSize: '.95rem', color: '#1e293b', marginBottom: 14 };
const td     = { padding: '11px 14px', fontSize: '.88rem' };
const th     = { padding: '9px 14px', textAlign: 'left', fontSize: '.78rem', color: '#64748b', fontWeight: 600 };
