import { useEffect, useState } from 'react';
import axios from '../api/axios';
import { Lightbulb, Plus, X, Edit2 } from 'lucide-react';

const CANALES = ['Instagram', 'Facebook', 'TikTok', 'YouTube', 'LinkedIn', 'Twitter/X', 'Blog', 'Email', 'WhatsApp', 'Otro'];
const FORMATOS = ['Video', 'Reel', 'Imagen', 'Carrusel', 'Historia', 'Texto', 'Podcast', 'Newsletter'];
const ESTADOS = {
    idea:        { label: '💡 Idea',        color: '#f59e0b', bg: '#fef3c7' },
    en_progreso: { label: '⚡ En progreso', color: '#6366f1', bg: '#ede9fe' },
    publicado:   { label: '✅ Publicado',   color: '#22c55e', bg: '#dcfce7' },
    descartado:  { label: '🗑 Descartado',  color: '#9ca3af', bg: '#f3f4f6' }
};

const empty = { titulo: '', descripcion: '', canal: 'Instagram', formato: 'Video', estado: 'idea', fecha_publicacion: '' };

export default function Contenido() {
    const [ideas, setIdeas] = useState([]);
    const [modal, setModal] = useState(false);
    const [form, setForm] = useState(empty);
    const [editId, setEditId] = useState(null);
    const [filtro, setFiltro] = useState('todas');

    useEffect(() => { cargar(); }, []);
    async function cargar() { const { data } = await axios.get('/contenido'); setIdeas(data); }

    async function guardar(e) {
        e.preventDefault();
        if (editId) await axios.put(`/contenido/${editId}`, form);
        else await axios.post('/contenido', form);
        setModal(false); setForm(empty); setEditId(null); cargar();
    }

    function editar(i) { setForm({ titulo: i.titulo, descripcion: i.descripcion || '', canal: i.canal, formato: i.formato, estado: i.estado, fecha_publicacion: i.fecha_publicacion || '' }); setEditId(i.id); setModal(true); }
    async function eliminar(id) { if (!confirm('¿Eliminar?')) return; await axios.delete(`/contenido/${id}`); cargar(); }
    async function avanzar(id, estado) { await axios.put(`/contenido/${id}`, { estado }); cargar(); }

    const filtradas = filtro === 'todas' ? ideas : ideas.filter(i => i.estado === filtro);

    const conteo = Object.keys(ESTADOS).reduce((acc, e) => { acc[e] = ideas.filter(i => i.estado === e).length; return acc; }, {});

    return (
        <div style={{ padding: 28 }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <Lightbulb size={22} color="#6366f1" />
                    <h2 style={{ margin: 0, fontSize: '1.3rem', fontWeight: 700 }}>Ideas de Contenido</h2>
                </div>
                <button onClick={() => { setForm(empty); setEditId(null); setModal(true); }} style={{ display: 'flex', alignItems: 'center', gap: 6, background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 16px', cursor: 'pointer', fontWeight: 600 }}>
                    <Plus size={16} /> Nueva idea
                </button>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4,1fr)', gap: 12, marginBottom: 24 }}>
                {Object.entries(ESTADOS).map(([e, { label, color, bg }]) => (
                    <div key={e} style={{ background: bg, borderRadius: 10, padding: '14px 16px', cursor: 'pointer', border: filtro === e ? `2px solid ${color}` : '2px solid transparent' }} onClick={() => setFiltro(filtro === e ? 'todas' : e)}>
                        <div style={{ fontSize: 13, fontWeight: 600, color }}>{label}</div>
                        <div style={{ fontSize: 24, fontWeight: 800, color, marginTop: 4 }}>{conteo[e]}</div>
                    </div>
                ))}
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(280px,1fr))', gap: 16 }}>
                {filtradas.length === 0 && <div style={{ gridColumn: '1/-1', textAlign: 'center', color: '#9ca3af', padding: '40px 0' }}>Sin ideas registradas</div>}
                {filtradas.map(idea => {
                    const est = ESTADOS[idea.estado];
                    return (
                        <div key={idea.id} style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 18, display: 'flex', flexDirection: 'column', gap: 10 }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                <div style={{ fontWeight: 600, fontSize: '.95rem', flex: 1 }}>{idea.titulo}</div>
                                <div style={{ display: 'flex', gap: 6 }}>
                                    <button onClick={() => editar(idea)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#9ca3af' }}><Edit2 size={14} /></button>
                                    <button onClick={() => eliminar(idea.id)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#ef4444' }}><X size={14} /></button>
                                </div>
                            </div>
                            <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap' }}>
                                <span style={{ background: '#ede9fe', color: '#7c3aed', borderRadius: 20, padding: '2px 10px', fontSize: 11, fontWeight: 600 }}>{idea.canal}</span>
                                <span style={{ background: '#f0fdf4', color: '#15803d', borderRadius: 20, padding: '2px 10px', fontSize: 11, fontWeight: 600 }}>{idea.formato}</span>
                                <span style={{ background: est.bg, color: est.color, borderRadius: 20, padding: '2px 10px', fontSize: 11, fontWeight: 600 }}>{est.label}</span>
                            </div>
                            {idea.descripcion && <div style={{ fontSize: 13, color: '#6b7280', lineHeight: 1.5 }}>{idea.descripcion}</div>}
                            {idea.fecha_publicacion && <div style={{ fontSize: 11, color: '#9ca3af' }}>📅 Publicar: {idea.fecha_publicacion}</div>}
                            <div style={{ display: 'flex', gap: 6, marginTop: 4 }}>
                                {idea.estado === 'idea' && <button onClick={() => avanzar(idea.id, 'en_progreso')} style={{ flex: 1, fontSize: 12, padding: '5px', background: '#ede9fe', color: '#6366f1', border: 'none', borderRadius: 6, cursor: 'pointer', fontWeight: 600 }}>→ En progreso</button>}
                                {idea.estado === 'en_progreso' && <button onClick={() => avanzar(idea.id, 'publicado')} style={{ flex: 1, fontSize: 12, padding: '5px', background: '#dcfce7', color: '#16a34a', border: 'none', borderRadius: 6, cursor: 'pointer', fontWeight: 600 }}>✓ Publicar</button>}
                                {idea.estado !== 'descartado' && idea.estado !== 'publicado' && <button onClick={() => avanzar(idea.id, 'descartado')} style={{ fontSize: 12, padding: '5px 10px', background: '#f3f4f6', color: '#9ca3af', border: 'none', borderRadius: 6, cursor: 'pointer' }}>Descartar</button>}
                            </div>
                        </div>
                    );
                })}
            </div>

            {modal && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,.4)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100 }} onClick={() => setModal(false)}>
                    <div style={{ background: '#fff', borderRadius: 12, padding: 28, width: 480, maxHeight: '90vh', overflowY: 'auto' }} onClick={e => e.stopPropagation()}>
                        <h3 style={{ margin: '0 0 20px', fontSize: '1.1rem' }}>{editId ? 'Editar' : 'Nueva'} idea</h3>
                        <form onSubmit={guardar}>
                            <div style={{ marginBottom: 14 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5 }}>Título *</label>
                                <input type="text" value={form.titulo} onChange={e => setForm(f => ({ ...f, titulo: e.target.value }))} required style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' }} />
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 14 }}>
                                <div>
                                    <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5 }}>Canal</label>
                                    <select value={form.canal} onChange={e => setForm(f => ({ ...f, canal: e.target.value }))} style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14 }}>
                                        {CANALES.map(c => <option key={c}>{c}</option>)}
                                    </select>
                                </div>
                                <div>
                                    <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5 }}>Formato</label>
                                    <select value={form.formato} onChange={e => setForm(f => ({ ...f, formato: e.target.value }))} style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14 }}>
                                        {FORMATOS.map(f => <option key={f}>{f}</option>)}
                                    </select>
                                </div>
                            </div>
                            <div style={{ marginBottom: 14 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5 }}>Descripción / Guión</label>
                                <textarea value={form.descripcion} onChange={e => setForm(f => ({ ...f, descripcion: e.target.value }))} rows={4} placeholder="Describe la idea, el guión, el mensaje clave..." style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, resize: 'vertical', boxSizing: 'border-box' }} />
                            </div>
                            <div style={{ marginBottom: 20 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5 }}>Fecha de publicación</label>
                                <input type="date" value={form.fecha_publicacion} onChange={e => setForm(f => ({ ...f, fecha_publicacion: e.target.value }))} style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' }} />
                            </div>
                            <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
                                <button type="button" onClick={() => setModal(false)} style={{ padding: '9px 18px', border: '1px solid #d1d5db', borderRadius: 8, background: '#fff', cursor: 'pointer' }}>Cancelar</button>
                                <button type="submit" style={{ padding: '9px 18px', background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, cursor: 'pointer', fontWeight: 600 }}>Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
