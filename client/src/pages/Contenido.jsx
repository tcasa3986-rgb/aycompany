import { useEffect, useState, useRef } from 'react';
import axios from '../api/axios';
import { Lightbulb, Plus, X, Edit2, Bot, Send, Sparkles } from 'lucide-react';

const CANALES = ['Instagram', 'Facebook', 'TikTok', 'YouTube', 'LinkedIn', 'Twitter/X', 'Blog', 'Email', 'WhatsApp', 'Otro'];
const FORMATOS = ['Video', 'Reel', 'Imagen', 'Carrusel', 'Historia', 'Texto', 'Podcast', 'Newsletter'];
const ESTADOS = {
    idea:        { label: '💡 Idea',        color: '#f59e0b', bg: '#fef3c7' },
    en_progreso: { label: '⚡ En progreso', color: '#6366f1', bg: '#ede9fe' },
    publicado:   { label: '✅ Publicado',   color: '#22c55e', bg: '#dcfce7' },
    descartado:  { label: '🗑 Descartado',  color: '#9ca3af', bg: '#f3f4f6' }
};

const empty = { titulo: '', descripcion: '', canal: 'Instagram', formato: 'Video', estado: 'idea', fecha_publicacion: '' };

const SUGERENCIAS_RAPIDAS = [
    'Dame 5 ideas de contenido para Instagram',
    '¿Qué tipo de reels funcionan mejor ahora?',
    '¿Cómo puedo crecer más rápido en TikTok?',
    'Genera un calendario de contenido para esta semana',
    '¿Qué estrategia me recomiendas según mis metas?',
];

export default function Contenido() {
    const [ideas, setIdeas] = useState([]);
    const [modal, setModal] = useState(false);
    const [form, setForm] = useState(empty);
    const [editId, setEditId] = useState(null);
    const [filtro, setFiltro] = useState('todas');
    const [panelIA, setPanelIA] = useState(false);
    const [mensajeIA, setMensajeIA] = useState('');
    const [historial, setHistorial] = useState([]);
    const [cargandoIA, setCargandoIA] = useState(false);
    const chatEndRef = useRef(null);

    useEffect(() => { cargar(); }, []);
    useEffect(() => { chatEndRef.current?.scrollIntoView({ behavior: 'smooth' }); }, [historial]);

    async function cargar() { const { data } = await axios.get('/contenido'); setIdeas(data); }

    async function guardar(e) {
        e.preventDefault();
        if (editId) await axios.put(`/contenido/${editId}`, form);
        else await axios.post('/contenido', form);
        setModal(false); setForm(empty); setEditId(null); cargar();
    }

    function editar(i) {
        setForm({ titulo: i.titulo, descripcion: i.descripcion || '', canal: i.canal, formato: i.formato, estado: i.estado, fecha_publicacion: i.fecha_publicacion || '' });
        setEditId(i.id); setModal(true);
    }
    async function eliminar(id) { if (!confirm('¿Eliminar?')) return; await axios.delete(`/contenido/${id}`); cargar(); }
    async function avanzar(id, estado) { await axios.put(`/contenido/${id}`, { estado }); cargar(); }

    async function enviarIA(texto) {
        const msg = texto || mensajeIA;
        if (!msg.trim()) return;
        const nuevoHistorial = [...historial, { role: 'user', content: msg }];
        setHistorial(nuevoHistorial);
        setMensajeIA('');
        setCargandoIA(true);
        try {
            const { data } = await axios.post('/asistente/chat', { mensaje: msg, historial: historial.slice(-8) });
            setHistorial(h => [...h, { role: 'assistant', content: data.respuesta }]);
        } catch {
            setHistorial(h => [...h, { role: 'assistant', content: '⚠️ Error al conectar con el asistente. Verifica la configuración de OpenAI.' }]);
        }
        setCargandoIA(false);
    }

    const filtradas = filtro === 'todas' ? ideas : ideas.filter(i => i.estado === filtro);
    const conteo = Object.keys(ESTADOS).reduce((acc, e) => { acc[e] = ideas.filter(i => i.estado === e).length; return acc; }, {});

    return (
        <div style={{ display: 'flex', height: '100%' }}>
            {/* Contenido principal */}
            <div style={{ flex: 1, padding: 28, overflowY: 'auto', transition: 'all .3s' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                        <Lightbulb size={22} color="#6366f1" />
                        <h2 style={{ margin: 0, fontSize: '1.3rem', fontWeight: 700 }}>Ideas de Contenido</h2>
                    </div>
                    <div style={{ display: 'flex', gap: 8 }}>
                        <button onClick={() => setPanelIA(p => !p)} style={{ display: 'flex', alignItems: 'center', gap: 6, background: panelIA ? '#6366f1' : '#ede9fe', color: panelIA ? '#fff' : '#6366f1', border: 'none', borderRadius: 8, padding: '9px 16px', cursor: 'pointer', fontWeight: 600, fontSize: 13 }}>
                            <Sparkles size={15} /> Asistente IA
                        </button>
                        <button onClick={() => { setForm(empty); setEditId(null); setModal(true); }} style={{ display: 'flex', alignItems: 'center', gap: 6, background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 16px', cursor: 'pointer', fontWeight: 600, fontSize: 13 }}>
                            <Plus size={15} /> Nueva idea
                        </button>
                    </div>
                </div>

                {/* Cards de estado */}
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4,1fr)', gap: 12, marginBottom: 24 }}>
                    {Object.entries(ESTADOS).map(([e, { label, color, bg }]) => (
                        <div key={e} style={{ background: bg, borderRadius: 10, padding: '14px 16px', cursor: 'pointer', border: filtro === e ? `2px solid ${color}` : '2px solid transparent' }} onClick={() => setFiltro(filtro === e ? 'todas' : e)}>
                            <div style={{ fontSize: 13, fontWeight: 600, color }}>{label}</div>
                            <div style={{ fontSize: 24, fontWeight: 800, color, marginTop: 4 }}>{conteo[e]}</div>
                        </div>
                    ))}
                </div>

                {/* Grid de ideas */}
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(260px,1fr))', gap: 14 }}>
                    {filtradas.length === 0 && <div style={{ gridColumn: '1/-1', textAlign: 'center', color: '#9ca3af', padding: '40px 0' }}>Sin ideas registradas</div>}
                    {filtradas.map(idea => {
                        const est = ESTADOS[idea.estado];
                        return (
                            <div key={idea.id} style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 18, display: 'flex', flexDirection: 'column', gap: 10 }}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                    <div style={{ fontWeight: 600, fontSize: '.95rem', flex: 1, lineHeight: 1.3 }}>{idea.titulo}</div>
                                    <div style={{ display: 'flex', gap: 4, flexShrink: 0 }}>
                                        <button onClick={() => editar(idea)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#9ca3af' }}><Edit2 size={13} /></button>
                                        <button onClick={() => eliminar(idea.id)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#ef4444' }}><X size={13} /></button>
                                    </div>
                                </div>
                                <div style={{ display: 'flex', gap: 5, flexWrap: 'wrap' }}>
                                    <span style={{ background: '#ede9fe', color: '#7c3aed', borderRadius: 20, padding: '2px 9px', fontSize: 11, fontWeight: 600 }}>{idea.canal}</span>
                                    <span style={{ background: '#f0fdf4', color: '#15803d', borderRadius: 20, padding: '2px 9px', fontSize: 11, fontWeight: 600 }}>{idea.formato}</span>
                                    <span style={{ background: est.bg, color: est.color, borderRadius: 20, padding: '2px 9px', fontSize: 11, fontWeight: 600 }}>{est.label}</span>
                                </div>
                                {idea.descripcion && <div style={{ fontSize: 12, color: '#6b7280', lineHeight: 1.5 }}>{idea.descripcion}</div>}
                                {idea.fecha_publicacion && <div style={{ fontSize: 11, color: '#9ca3af' }}>📅 {idea.fecha_publicacion}</div>}
                                <div style={{ display: 'flex', gap: 5, marginTop: 4 }}>
                                    {idea.estado === 'idea' && <button onClick={() => avanzar(idea.id, 'en_progreso')} style={{ flex: 1, fontSize: 12, padding: '5px', background: '#ede9fe', color: '#6366f1', border: 'none', borderRadius: 6, cursor: 'pointer', fontWeight: 600 }}>→ En progreso</button>}
                                    {idea.estado === 'en_progreso' && <button onClick={() => avanzar(idea.id, 'publicado')} style={{ flex: 1, fontSize: 12, padding: '5px', background: '#dcfce7', color: '#16a34a', border: 'none', borderRadius: 6, cursor: 'pointer', fontWeight: 600 }}>✓ Publicar</button>}
                                    {idea.estado !== 'descartado' && idea.estado !== 'publicado' && <button onClick={() => avanzar(idea.id, 'descartado')} style={{ fontSize: 12, padding: '5px 10px', background: '#f3f4f6', color: '#9ca3af', border: 'none', borderRadius: 6, cursor: 'pointer' }}>Descartar</button>}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>

            {/* Panel IA */}
            {panelIA && (
                <div style={{ width: 360, borderLeft: '1px solid #e5e7eb', display: 'flex', flexDirection: 'column', background: '#fff', flexShrink: 0 }}>
                    {/* Header IA */}
                    <div style={{ padding: '16px 20px', borderBottom: '1px solid #e5e7eb', background: 'linear-gradient(135deg,#6366f1,#8b5cf6)' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 8, color: '#fff' }}>
                            <Bot size={18} />
                            <div>
                                <div style={{ fontWeight: 700, fontSize: 14 }}>Asistente de Contenido</div>
                                <div style={{ fontSize: 11, opacity: .8 }}>Conoce tus metas y estrategias</div>
                            </div>
                        </div>
                    </div>

                    {/* Mensajes */}
                    <div style={{ flex: 1, overflowY: 'auto', padding: 16, display: 'flex', flexDirection: 'column', gap: 12 }}>
                        {historial.length === 0 && (
                            <div>
                                <div style={{ textAlign: 'center', color: '#9ca3af', fontSize: 13, marginBottom: 16 }}>
                                    <Sparkles size={28} color="#6366f1" style={{ marginBottom: 8 }} />
                                    <div style={{ fontWeight: 600, color: '#374151' }}>¡Hola! Soy tu asistente de marketing.</div>
                                    <div style={{ marginTop: 4 }}>Tengo acceso a tus metas y estrategias actuales. ¿En qué te ayudo?</div>
                                </div>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
                                    {SUGERENCIAS_RAPIDAS.map((s, i) => (
                                        <button key={i} onClick={() => enviarIA(s)} style={{ textAlign: 'left', padding: '8px 12px', background: '#f9fafb', border: '1px solid #e5e7eb', borderRadius: 8, cursor: 'pointer', fontSize: 12, color: '#374151', lineHeight: 1.4 }}>{s}</button>
                                    ))}
                                </div>
                            </div>
                        )}
                        {historial.map((msg, i) => (
                            <div key={i} style={{ display: 'flex', justifyContent: msg.role === 'user' ? 'flex-end' : 'flex-start' }}>
                                <div style={{
                                    maxWidth: '85%', padding: '10px 13px', borderRadius: msg.role === 'user' ? '12px 12px 2px 12px' : '12px 12px 12px 2px',
                                    background: msg.role === 'user' ? '#6366f1' : '#f3f4f6',
                                    color: msg.role === 'user' ? '#fff' : '#111',
                                    fontSize: 13, lineHeight: 1.5, whiteSpace: 'pre-wrap'
                                }}>
                                    {msg.content}
                                </div>
                            </div>
                        ))}
                        {cargandoIA && (
                            <div style={{ display: 'flex', gap: 4, padding: '10px 13px', background: '#f3f4f6', borderRadius: '12px 12px 12px 2px', width: 'fit-content' }}>
                                {[0, 1, 2].map(i => <div key={i} style={{ width: 7, height: 7, borderRadius: '50%', background: '#9ca3af', animation: `bounce .8s ${i * .15}s infinite` }} />)}
                            </div>
                        )}
                        <div ref={chatEndRef} />
                    </div>

                    {/* Input */}
                    <div style={{ padding: 12, borderTop: '1px solid #e5e7eb' }}>
                        <div style={{ display: 'flex', gap: 8 }}>
                            <input
                                type="text"
                                value={mensajeIA}
                                onChange={e => setMensajeIA(e.target.value)}
                                onKeyDown={e => e.key === 'Enter' && !e.shiftKey && enviarIA()}
                                placeholder="Escribe tu pregunta..."
                                disabled={cargandoIA}
                                style={{ flex: 1, padding: '9px 12px', border: '1px solid #d1d5db', borderRadius: 8, fontSize: 13, outline: 'none' }}
                            />
                            <button onClick={() => enviarIA()} disabled={cargandoIA || !mensajeIA.trim()} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '0 14px', cursor: 'pointer', opacity: (!mensajeIA.trim() || cargandoIA) ? 0.5 : 1 }}>
                                <Send size={16} />
                            </button>
                        </div>
                        {historial.length > 0 && (
                            <button onClick={() => setHistorial([])} style={{ marginTop: 6, fontSize: 11, color: '#9ca3af', background: 'none', border: 'none', cursor: 'pointer', width: '100%', textAlign: 'center' }}>
                                Limpiar conversación
                            </button>
                        )}
                    </div>
                </div>
            )}

            {/* Modal */}
            {modal && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,.4)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100 }} onClick={() => setModal(false)}>
                    <div style={{ background: '#fff', borderRadius: 12, padding: 28, width: 480, maxHeight: '90vh', overflowY: 'auto' }} onClick={e => e.stopPropagation()}>
                        <h3 style={{ margin: '0 0 20px', fontSize: '1.1rem' }}>{editId ? 'Editar' : 'Nueva'} idea</h3>
                        <form onSubmit={guardar}>
                            <div style={{ marginBottom: 14 }}>
                                <label style={labelStyle}>Título *</label>
                                <input type="text" value={form.titulo} onChange={e => setForm(f => ({ ...f, titulo: e.target.value }))} required style={inputStyle} />
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 14 }}>
                                <div>
                                    <label style={labelStyle}>Canal</label>
                                    <select value={form.canal} onChange={e => setForm(f => ({ ...f, canal: e.target.value }))} style={selectStyle}>
                                        {CANALES.map(c => <option key={c}>{c}</option>)}
                                    </select>
                                </div>
                                <div>
                                    <label style={labelStyle}>Formato</label>
                                    <select value={form.formato} onChange={e => setForm(f => ({ ...f, formato: e.target.value }))} style={selectStyle}>
                                        {FORMATOS.map(f => <option key={f}>{f}</option>)}
                                    </select>
                                </div>
                            </div>
                            <div style={{ marginBottom: 14 }}>
                                <label style={labelStyle}>Descripción / Guión</label>
                                <textarea value={form.descripcion} onChange={e => setForm(f => ({ ...f, descripcion: e.target.value }))} rows={4} placeholder="Describe la idea, el guión, el mensaje clave..." style={{ ...inputStyle, resize: 'vertical' }} />
                            </div>
                            <div style={{ marginBottom: 20 }}>
                                <label style={labelStyle}>Fecha de publicación</label>
                                <input type="date" value={form.fecha_publicacion} onChange={e => setForm(f => ({ ...f, fecha_publicacion: e.target.value }))} style={inputStyle} />
                            </div>
                            <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
                                <button type="button" onClick={() => setModal(false)} style={{ padding: '9px 18px', border: '1px solid #d1d5db', borderRadius: 8, background: '#fff', cursor: 'pointer' }}>Cancelar</button>
                                <button type="submit" style={{ padding: '9px 18px', background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, cursor: 'pointer', fontWeight: 600 }}>Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            <style>{`@keyframes bounce { 0%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-6px)} }`}</style>
        </div>
    );
}

const labelStyle = { display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5, color: '#374151' };
const inputStyle = { width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' };
const selectStyle = { width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14 };
