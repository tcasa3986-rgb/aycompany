import { useEffect, useState } from 'react';
import axios from '../api/axios';
import { TrendingUp, Plus, X, Target, BarChart2, Edit2, CheckCircle } from 'lucide-react';
import {
    LineChart, Line, BarChart, Bar, XAxis, YAxis, CartesianGrid,
    Tooltip, Legend, ResponsiveContainer
} from 'recharts';

const PLATAFORMAS = ['Instagram', 'Facebook', 'TikTok', 'YouTube', 'LinkedIn', 'Twitter/X'];
const METRICAS_LABELS = { seguidores: 'Seguidores', alcance: 'Alcance', interacciones: 'Interacciones', publicaciones: 'Publicaciones' };
const COLORES_PLATAFORMA = { Instagram: '#e1306c', Facebook: '#1877f2', TikTok: '#010101', YouTube: '#ff0000', LinkedIn: '#0077b5', 'Twitter/X': '#1da1f2' };
const ESTRATEGIA_ESTADOS = { activa: { label: 'Activa', color: '#22c55e' }, pausada: { label: 'Pausada', color: '#f59e0b' }, completada: { label: 'Completada', color: '#6366f1' } };

const emptyMetrica = { plataforma: 'Instagram', fecha: '', seguidores: '', alcance: '', interacciones: '', publicaciones: '', notas: '' };
const emptyMeta = { plataforma: 'Instagram', metrica: 'seguidores', valor_meta: '', valor_actual: '', fecha_limite: '', descripcion: '' };
const emptyEstrategia = { titulo: '', canal: 'Instagram', objetivo: '', descripcion: '', estado: 'activa', fecha_inicio: '', fecha_fin: '' };

const TABS = ['resumen', 'metricas', 'metas', 'estrategias'];
const TAB_LABELS = { resumen: 'Resumen', metricas: 'Métricas', metas: 'Metas', estrategias: 'Estrategias' };

export default function Marketing() {
    const [tab, setTab] = useState('resumen');
    const [metricas, setMetricas] = useState([]);
    const [metas, setMetas] = useState([]);
    const [estrategias, setEstrategias] = useState([]);
    const [modalMetrica, setModalMetrica] = useState(false);
    const [modalMeta, setModalMeta] = useState(false);
    const [modalEstrategia, setModalEstrategia] = useState(false);
    const [formMetrica, setFormMetrica] = useState(emptyMetrica);
    const [formMeta, setFormMeta] = useState(emptyMeta);
    const [formEstrategia, setFormEstrategia] = useState(emptyEstrategia);
    const [editMetricaId, setEditMetricaId] = useState(null);
    const [editMetaId, setEditMetaId] = useState(null);
    const [editEstrategiaId, setEditEstrategiaId] = useState(null);
    const [plataformaFiltro, setPlataformaFiltro] = useState('Instagram');

    useEffect(() => { cargarTodo(); }, []);

    async function cargarTodo() {
        const [m, mt, e] = await Promise.all([
            axios.get('/metricas/metricas'),
            axios.get('/metricas/metas'),
            axios.get('/marketing')
        ]);
        setMetricas(m.data);
        setMetas(mt.data);
        setEstrategias(e.data);
    }

    async function guardarMetrica(e) {
        e.preventDefault();
        if (editMetricaId) await axios.put(`/metricas/metricas/${editMetricaId}`, formMetrica);
        else await axios.post('/metricas/metricas', formMetrica);
        setModalMetrica(false); setFormMetrica(emptyMetrica); setEditMetricaId(null); cargarTodo();
    }

    async function guardarMeta(e) {
        e.preventDefault();
        if (editMetaId) await axios.put(`/metricas/metas/${editMetaId}`, formMeta);
        else await axios.post('/metricas/metas', formMeta);
        setModalMeta(false); setFormMeta(emptyMeta); setEditMetaId(null); cargarTodo();
    }

    async function guardarEstrategia(e) {
        e.preventDefault();
        if (editEstrategiaId) await axios.put(`/marketing/${editEstrategiaId}`, formEstrategia);
        else await axios.post('/marketing', formEstrategia);
        setModalEstrategia(false); setFormEstrategia(emptyEstrategia); setEditEstrategiaId(null); cargarTodo();
    }

    async function eliminarMetrica(id) { if (!confirm('¿Eliminar?')) return; await axios.delete(`/metricas/metricas/${id}`); cargarTodo(); }
    async function eliminarMeta(id) { if (!confirm('¿Eliminar?')) return; await axios.delete(`/metricas/metas/${id}`); cargarTodo(); }
    async function eliminarEstrategia(id) { if (!confirm('¿Eliminar?')) return; await axios.delete(`/marketing/${id}`); cargarTodo(); }
    async function completarMeta(id) { await axios.put(`/metricas/metas/${id}`, { completada: true }); cargarTodo(); }
    async function cambiarEstadoEstrategia(id, estado) { await axios.put(`/marketing/${id}`, { estado }); cargarTodo(); }

    // Datos para gráfico por plataforma
    const datosGrafico = metricas
        .filter(m => m.plataforma === plataformaFiltro)
        .map(m => ({ fecha: m.fecha, seguidores: m.seguidores, alcance: m.alcance, interacciones: m.interacciones }))
        .slice(-12);

    // Última métrica por plataforma
    const ultimaPorPlataforma = {};
    PLATAFORMAS.forEach(p => {
        const filtradas = metricas.filter(m => m.plataforma === p).sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
        if (filtradas.length) ultimaPorPlataforma[p] = filtradas[0];
    });

    // Metas activas con progreso
    const metasActivas = metas.filter(m => !m.completada);

    return (
        <div style={{ padding: 28, maxWidth: 1200, margin: '0 auto' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <TrendingUp size={22} color="#6366f1" />
                    <h2 style={{ margin: 0, fontSize: '1.3rem', fontWeight: 700 }}>Marketing</h2>
                </div>
                <div style={{ display: 'flex', gap: 8 }}>
                    {tab === 'metricas' && <button onClick={() => { setFormMetrica(emptyMetrica); setEditMetricaId(null); setModalMetrica(true); }} style={btnPrimary}><Plus size={15} /> Registrar métricas</button>}
                    {tab === 'metas' && <button onClick={() => { setFormMeta(emptyMeta); setEditMetaId(null); setModalMeta(true); }} style={btnPrimary}><Plus size={15} /> Nueva meta</button>}
                    {tab === 'estrategias' && <button onClick={() => { setFormEstrategia(emptyEstrategia); setEditEstrategiaId(null); setModalEstrategia(true); }} style={btnPrimary}><Plus size={15} /> Nueva estrategia</button>}
                </div>
            </div>

            {/* Tabs */}
            <div style={{ display: 'flex', gap: 4, marginBottom: 24, borderBottom: '2px solid #f3f4f6', paddingBottom: 0 }}>
                {TABS.map(t => (
                    <button key={t} onClick={() => setTab(t)} style={{ padding: '8px 20px', border: 'none', background: 'none', cursor: 'pointer', fontWeight: tab === t ? 700 : 400, color: tab === t ? '#6366f1' : '#6b7280', borderBottom: tab === t ? '2px solid #6366f1' : '2px solid transparent', marginBottom: -2, fontSize: 14 }}>
                        {TAB_LABELS[t]}
                    </button>
                ))}
            </div>

            {/* RESUMEN */}
            {tab === 'resumen' && (
                <div>
                    {/* Cards por plataforma */}
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(180px,1fr))', gap: 12, marginBottom: 28 }}>
                        {PLATAFORMAS.filter(p => ultimaPorPlataforma[p]).map(p => {
                            const u = ultimaPorPlataforma[p];
                            const color = COLORES_PLATAFORMA[p];
                            return (
                                <div key={p} style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 10, padding: 16, borderTop: `3px solid ${color}` }}>
                                    <div style={{ fontWeight: 700, fontSize: 13, color, marginBottom: 8 }}>{p}</div>
                                    <div style={{ fontSize: 22, fontWeight: 800, color: '#111' }}>{u.seguidores?.toLocaleString()}</div>
                                    <div style={{ fontSize: 11, color: '#9ca3af' }}>seguidores</div>
                                    <div style={{ marginTop: 8, fontSize: 12, color: '#6b7280' }}>👁 {u.alcance?.toLocaleString()} alcance</div>
                                    <div style={{ fontSize: 12, color: '#6b7280' }}>❤️ {u.interacciones?.toLocaleString()} interacciones</div>
                                </div>
                            );
                        })}
                        {Object.keys(ultimaPorPlataforma).length === 0 && (
                            <div style={{ gridColumn: '1/-1', textAlign: 'center', color: '#9ca3af', padding: '30px 0', fontSize: 14 }}>
                                Sin métricas registradas aún. Ve a la pestaña <strong>Métricas</strong> para ingresar datos.
                            </div>
                        )}
                    </div>

                    {/* Gráfico */}
                    {datosGrafico.length > 1 && (
                        <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 20, marginBottom: 20 }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
                                <div style={{ fontWeight: 700, fontSize: 14 }}>Evolución de seguidores</div>
                                <select value={plataformaFiltro} onChange={e => setPlataformaFiltro(e.target.value)} style={{ padding: '5px 10px', border: '1px solid #e5e7eb', borderRadius: 6, fontSize: 13 }}>
                                    {PLATAFORMAS.map(p => <option key={p}>{p}</option>)}
                                </select>
                            </div>
                            <ResponsiveContainer width="100%" height={220}>
                                <LineChart data={datosGrafico}>
                                    <CartesianGrid strokeDasharray="3 3" stroke="#f3f4f6" />
                                    <XAxis dataKey="fecha" tick={{ fontSize: 11 }} />
                                    <YAxis tick={{ fontSize: 11 }} />
                                    <Tooltip />
                                    <Legend />
                                    <Line type="monotone" dataKey="seguidores" stroke={COLORES_PLATAFORMA[plataformaFiltro] || '#6366f1'} strokeWidth={2} dot={{ r: 4 }} name="Seguidores" />
                                    <Line type="monotone" dataKey="interacciones" stroke="#f59e0b" strokeWidth={2} dot={{ r: 4 }} name="Interacciones" />
                                </LineChart>
                            </ResponsiveContainer>
                        </div>
                    )}

                    {/* Metas activas resumen */}
                    {metasActivas.length > 0 && (
                        <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 20 }}>
                            <div style={{ fontWeight: 700, fontSize: 14, marginBottom: 16 }}>🎯 Metas activas</div>
                            <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                                {metasActivas.map(m => {
                                    const pct = Math.min(100, Math.round((m.valor_actual / m.valor_meta) * 100));
                                    const color = COLORES_PLATAFORMA[m.plataforma] || '#6366f1';
                                    return (
                                        <div key={m.id}>
                                            <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 13, marginBottom: 4 }}>
                                                <span style={{ fontWeight: 600 }}>{m.plataforma} — {METRICAS_LABELS[m.metrica] || m.metrica}</span>
                                                <span style={{ color: '#6b7280' }}>{m.valor_actual?.toLocaleString()} / {m.valor_meta?.toLocaleString()} ({pct}%)</span>
                                            </div>
                                            <div style={{ background: '#f3f4f6', borderRadius: 20, height: 8 }}>
                                                <div style={{ background: color, width: `${pct}%`, height: '100%', borderRadius: 20, transition: 'width .5s' }} />
                                            </div>
                                            {m.descripcion && <div style={{ fontSize: 11, color: '#9ca3af', marginTop: 2 }}>{m.descripcion}</div>}
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </div>
            )}

            {/* MÉTRICAS */}
            {tab === 'metricas' && (
                <div>
                    {metricas.length === 0 && <div style={{ textAlign: 'center', color: '#9ca3af', padding: '40px 0' }}>Sin métricas. Registra tus primeros datos.</div>}
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(300px,1fr))', gap: 14 }}>
                        {metricas.map(m => (
                            <div key={m.id} style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 10, padding: 16 }}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 10 }}>
                                    <div>
                                        <span style={{ background: COLORES_PLATAFORMA[m.plataforma] + '20', color: COLORES_PLATAFORMA[m.plataforma], borderRadius: 20, padding: '2px 10px', fontSize: 11, fontWeight: 700 }}>{m.plataforma}</span>
                                        <div style={{ fontSize: 11, color: '#9ca3af', marginTop: 4 }}>📅 {m.fecha}</div>
                                    </div>
                                    <div style={{ display: 'flex', gap: 6 }}>
                                        <button onClick={() => { setFormMetrica({ plataforma: m.plataforma, fecha: m.fecha, seguidores: m.seguidores, alcance: m.alcance, interacciones: m.interacciones, publicaciones: m.publicaciones, notas: m.notas || '' }); setEditMetricaId(m.id); setModalMetrica(true); }} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#9ca3af' }}><Edit2 size={13} /></button>
                                        <button onClick={() => eliminarMetrica(m.id)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#ef4444' }}><X size={13} /></button>
                                    </div>
                                </div>
                                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 8 }}>
                                    {[['👥', 'Seguidores', m.seguidores], ['👁', 'Alcance', m.alcance], ['❤️', 'Interacciones', m.interacciones], ['📝', 'Publicaciones', m.publicaciones]].map(([ico, label, val]) => (
                                        <div key={label} style={{ background: '#f9fafb', borderRadius: 6, padding: '8px 10px' }}>
                                            <div style={{ fontSize: 11, color: '#9ca3af' }}>{ico} {label}</div>
                                            <div style={{ fontWeight: 700, fontSize: 16 }}>{Number(val || 0).toLocaleString()}</div>
                                        </div>
                                    ))}
                                </div>
                                {m.notas && <div style={{ marginTop: 8, fontSize: 12, color: '#6b7280', fontStyle: 'italic' }}>{m.notas}</div>}
                            </div>
                        ))}
                    </div>

                    {/* Gráfico comparativo */}
                    {metricas.length > 1 && (
                        <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 20, marginTop: 20 }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
                                <div style={{ fontWeight: 700, fontSize: 14 }}>Histórico de seguidores</div>
                                <select value={plataformaFiltro} onChange={e => setPlataformaFiltro(e.target.value)} style={{ padding: '5px 10px', border: '1px solid #e5e7eb', borderRadius: 6, fontSize: 13 }}>
                                    {PLATAFORMAS.map(p => <option key={p}>{p}</option>)}
                                </select>
                            </div>
                            <ResponsiveContainer width="100%" height={200}>
                                <BarChart data={metricas.filter(m => m.plataforma === plataformaFiltro).slice(-10)}>
                                    <CartesianGrid strokeDasharray="3 3" stroke="#f3f4f6" />
                                    <XAxis dataKey="fecha" tick={{ fontSize: 11 }} />
                                    <YAxis tick={{ fontSize: 11 }} />
                                    <Tooltip />
                                    <Bar dataKey="seguidores" fill={COLORES_PLATAFORMA[plataformaFiltro] || '#6366f1'} name="Seguidores" radius={[4,4,0,0]} />
                                    <Bar dataKey="interacciones" fill="#f59e0b" name="Interacciones" radius={[4,4,0,0]} />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>
                    )}
                </div>
            )}

            {/* METAS */}
            {tab === 'metas' && (
                <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
                    {metas.length === 0 && <div style={{ textAlign: 'center', color: '#9ca3af', padding: '40px 0' }}>Sin metas definidas. Crea tu primera meta.</div>}
                    {metas.map(m => {
                        const pct = Math.min(100, Math.round((m.valor_actual / m.valor_meta) * 100));
                        const color = COLORES_PLATAFORMA[m.plataforma] || '#6366f1';
                        return (
                            <div key={m.id} style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 10, padding: 18, opacity: m.completada ? 0.6 : 1 }}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 12 }}>
                                    <div>
                                        <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
                                            <span style={{ background: color + '20', color, borderRadius: 20, padding: '2px 10px', fontSize: 11, fontWeight: 700 }}>{m.plataforma}</span>
                                            <span style={{ background: '#f3f4f6', borderRadius: 20, padding: '2px 10px', fontSize: 11, fontWeight: 600, color: '#374151' }}>{METRICAS_LABELS[m.metrica] || m.metrica}</span>
                                            {m.completada && <span style={{ background: '#dcfce7', color: '#16a34a', borderRadius: 20, padding: '2px 10px', fontSize: 11, fontWeight: 700 }}>✅ Completada</span>}
                                        </div>
                                        {m.descripcion && <div style={{ fontSize: 13, color: '#6b7280', marginTop: 6 }}>{m.descripcion}</div>}
                                        {m.fecha_limite && <div style={{ fontSize: 11, color: '#9ca3af', marginTop: 2 }}>📅 Límite: {m.fecha_limite}</div>}
                                    </div>
                                    <div style={{ display: 'flex', gap: 6, alignItems: 'center' }}>
                                        <span style={{ fontSize: 14, fontWeight: 700 }}>{pct}%</span>
                                        {!m.completada && <button onClick={() => completarMeta(m.id)} style={{ background: '#dcfce7', border: 'none', borderRadius: 6, padding: '4px 8px', cursor: 'pointer', color: '#16a34a' }}><CheckCircle size={14} /></button>}
                                        <button onClick={() => { setFormMeta({ plataforma: m.plataforma, metrica: m.metrica, valor_meta: m.valor_meta, valor_actual: m.valor_actual, fecha_limite: m.fecha_limite || '', descripcion: m.descripcion || '' }); setEditMetaId(m.id); setModalMeta(true); }} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#9ca3af' }}><Edit2 size={13} /></button>
                                        <button onClick={() => eliminarMeta(m.id)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#ef4444' }}><X size={13} /></button>
                                    </div>
                                </div>
                                <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 12, color: '#6b7280', marginBottom: 6 }}>
                                    <span>Actual: <strong>{Number(m.valor_actual || 0).toLocaleString()}</strong></span>
                                    <span>Meta: <strong>{Number(m.valor_meta).toLocaleString()}</strong></span>
                                </div>
                                <div style={{ background: '#f3f4f6', borderRadius: 20, height: 10 }}>
                                    <div style={{ background: m.completada ? '#22c55e' : color, width: `${pct}%`, height: '100%', borderRadius: 20, transition: 'width .5s' }} />
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}

            {/* ESTRATEGIAS */}
            {tab === 'estrategias' && (
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 20 }}>
                    {Object.entries(ESTRATEGIA_ESTADOS).map(([estado, { label, color }]) => {
                        const items = estrategias.filter(e => e.estado === estado);
                        return (
                            <div key={estado}>
                                <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 12 }}>
                                    <div style={{ width: 10, height: 10, borderRadius: '50%', background: color }} />
                                    <span style={{ fontWeight: 700, fontSize: 13 }}>{label}</span>
                                    <span style={{ background: '#f3f4f6', borderRadius: 20, padding: '1px 8px', fontSize: 12, color: '#6b7280' }}>{items.length}</span>
                                </div>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
                                    {items.map(item => (
                                        <div key={item.id} style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 10, padding: 14 }}>
                                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 6 }}>
                                                <div style={{ fontWeight: 600, fontSize: '.9rem' }}>{item.titulo}</div>
                                                <div style={{ display: 'flex', gap: 4 }}>
                                                    <button onClick={() => { setFormEstrategia({ titulo: item.titulo, canal: item.canal, objetivo: item.objetivo || '', descripcion: item.descripcion || '', estado: item.estado, fecha_inicio: item.fecha_inicio || '', fecha_fin: item.fecha_fin || '' }); setEditEstrategiaId(item.id); setModalEstrategia(true); }} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#9ca3af' }}><Edit2 size={13} /></button>
                                                    <button onClick={() => eliminarEstrategia(item.id)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#ef4444' }}><X size={13} /></button>
                                                </div>
                                            </div>
                                            <span style={{ background: '#ede9fe', color: '#7c3aed', borderRadius: 20, padding: '2px 8px', fontSize: 11, fontWeight: 600 }}>{item.canal}</span>
                                            {item.objetivo && <div style={{ fontSize: 12, color: '#6b7280', marginTop: 6 }}>🎯 {item.objetivo}</div>}
                                            <div style={{ display: 'flex', gap: 4, marginTop: 8, flexWrap: 'wrap' }}>
                                                {Object.entries(ESTRATEGIA_ESTADOS).filter(([e]) => e !== estado).map(([e, { label: l, color: c }]) => (
                                                    <button key={e} onClick={() => cambiarEstadoEstrategia(item.id, e)} style={{ fontSize: 11, padding: '3px 8px', border: `1px solid ${c}`, color: c, borderRadius: 6, background: 'none', cursor: 'pointer' }}>→ {l}</button>
                                                ))}
                                            </div>
                                        </div>
                                    ))}
                                    {items.length === 0 && <div style={{ textAlign: 'center', color: '#d1d5db', fontSize: 12, padding: '16px 0' }}>Sin estrategias</div>}
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}

            {/* Modal Métrica */}
            {modalMetrica && (
                <Modal title={`${editMetricaId ? 'Editar' : 'Registrar'} métricas`} onClose={() => setModalMetrica(false)}>
                    <form onSubmit={guardarMetrica}>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 14 }}>
                            <Field label="Plataforma">
                                <select value={formMetrica.plataforma} onChange={e => setFormMetrica(f => ({ ...f, plataforma: e.target.value }))} style={selectStyle}>
                                    {PLATAFORMAS.map(p => <option key={p}>{p}</option>)}
                                </select>
                            </Field>
                            <Field label="Fecha *">
                                <input type="date" required value={formMetrica.fecha} onChange={e => setFormMetrica(f => ({ ...f, fecha: e.target.value }))} style={inputStyle} />
                            </Field>
                        </div>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 14 }}>
                            {[['Seguidores', 'seguidores'], ['Alcance', 'alcance'], ['Interacciones', 'interacciones'], ['Publicaciones', 'publicaciones']].map(([label, key]) => (
                                <Field key={key} label={label}>
                                    <input type="number" min="0" value={formMetrica[key]} onChange={e => setFormMetrica(f => ({ ...f, [key]: e.target.value }))} style={inputStyle} />
                                </Field>
                            ))}
                        </div>
                        <Field label="Notas">
                            <textarea value={formMetrica.notas} onChange={e => setFormMetrica(f => ({ ...f, notas: e.target.value }))} rows={2} style={{ ...inputStyle, resize: 'vertical' }} />
                        </Field>
                        <ModalActions onClose={() => setModalMetrica(false)} />
                    </form>
                </Modal>
            )}

            {/* Modal Meta */}
            {modalMeta && (
                <Modal title={`${editMetaId ? 'Editar' : 'Nueva'} meta`} onClose={() => setModalMeta(false)}>
                    <form onSubmit={guardarMeta}>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 14 }}>
                            <Field label="Plataforma">
                                <select value={formMeta.plataforma} onChange={e => setFormMeta(f => ({ ...f, plataforma: e.target.value }))} style={selectStyle}>
                                    {PLATAFORMAS.map(p => <option key={p}>{p}</option>)}
                                </select>
                            </Field>
                            <Field label="Métrica">
                                <select value={formMeta.metrica} onChange={e => setFormMeta(f => ({ ...f, metrica: e.target.value }))} style={selectStyle}>
                                    {Object.entries(METRICAS_LABELS).map(([k, v]) => <option key={k} value={k}>{v}</option>)}
                                </select>
                            </Field>
                            <Field label="Meta *">
                                <input type="number" required min="1" value={formMeta.valor_meta} onChange={e => setFormMeta(f => ({ ...f, valor_meta: e.target.value }))} style={inputStyle} />
                            </Field>
                            <Field label="Valor actual">
                                <input type="number" min="0" value={formMeta.valor_actual} onChange={e => setFormMeta(f => ({ ...f, valor_actual: e.target.value }))} style={inputStyle} />
                            </Field>
                        </div>
                        <Field label="Descripción">
                            <input type="text" value={formMeta.descripcion} onChange={e => setFormMeta(f => ({ ...f, descripcion: e.target.value }))} placeholder="Ej: Alcanzar 1000 seguidores para Q2" style={inputStyle} />
                        </Field>
                        <div style={{ marginTop: 12 }}>
                            <Field label="Fecha límite">
                                <input type="date" value={formMeta.fecha_limite} onChange={e => setFormMeta(f => ({ ...f, fecha_limite: e.target.value }))} style={inputStyle} />
                            </Field>
                        </div>
                        <ModalActions onClose={() => setModalMeta(false)} />
                    </form>
                </Modal>
            )}

            {/* Modal Estrategia */}
            {modalEstrategia && (
                <Modal title={`${editEstrategiaId ? 'Editar' : 'Nueva'} estrategia`} onClose={() => setModalEstrategia(false)}>
                    <form onSubmit={guardarEstrategia}>
                        <Field label="Título *">
                            <input type="text" required value={formEstrategia.titulo} onChange={e => setFormEstrategia(f => ({ ...f, titulo: e.target.value }))} style={inputStyle} />
                        </Field>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, margin: '12px 0' }}>
                            <Field label="Canal">
                                <select value={formEstrategia.canal} onChange={e => setFormEstrategia(f => ({ ...f, canal: e.target.value }))} style={selectStyle}>
                                    {PLATAFORMAS.map(p => <option key={p}>{p}</option>)}
                                </select>
                            </Field>
                            <Field label="Estado">
                                <select value={formEstrategia.estado} onChange={e => setFormEstrategia(f => ({ ...f, estado: e.target.value }))} style={selectStyle}>
                                    {Object.entries(ESTRATEGIA_ESTADOS).map(([k, v]) => <option key={k} value={k}>{v.label}</option>)}
                                </select>
                            </Field>
                        </div>
                        <Field label="Objetivo">
                            <input type="text" value={formEstrategia.objetivo} onChange={e => setFormEstrategia(f => ({ ...f, objetivo: e.target.value }))} style={inputStyle} />
                        </Field>
                        <div style={{ marginTop: 12 }}>
                            <Field label="Descripción">
                                <textarea value={formEstrategia.descripcion} onChange={e => setFormEstrategia(f => ({ ...f, descripcion: e.target.value }))} rows={3} style={{ ...inputStyle, resize: 'vertical' }} />
                            </Field>
                        </div>
                        <ModalActions onClose={() => setModalEstrategia(false)} />
                    </form>
                </Modal>
            )}
        </div>
    );
}

function Modal({ title, onClose, children }) {
    return (
        <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,.4)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100 }} onClick={onClose}>
            <div style={{ background: '#fff', borderRadius: 12, padding: 28, width: 500, maxHeight: '90vh', overflowY: 'auto' }} onClick={e => e.stopPropagation()}>
                <h3 style={{ margin: '0 0 20px', fontSize: '1.1rem' }}>{title}</h3>
                {children}
            </div>
        </div>
    );
}

function Field({ label, children }) {
    return (
        <div style={{ marginBottom: 4 }}>
            <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5, color: '#374151' }}>{label}</label>
            {children}
        </div>
    );
}

function ModalActions({ onClose }) {
    return (
        <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end', marginTop: 20 }}>
            <button type="button" onClick={onClose} style={{ padding: '9px 18px', border: '1px solid #d1d5db', borderRadius: 8, background: '#fff', cursor: 'pointer' }}>Cancelar</button>
            <button type="submit" style={{ padding: '9px 18px', background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, cursor: 'pointer', fontWeight: 600 }}>Guardar</button>
        </div>
    );
}

const inputStyle = { width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' };
const selectStyle = { width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14 };
const btnPrimary = { display: 'flex', alignItems: 'center', gap: 6, background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 16px', cursor: 'pointer', fontWeight: 600, fontSize: 13 };
