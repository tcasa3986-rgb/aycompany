import { useEffect, useState } from 'react';
import axios from '../api/axios';
import { TrendingUp, Plus, X, Edit2, Check } from 'lucide-react';

const CANALES = ['Instagram', 'Facebook', 'TikTok', 'YouTube', 'Email', 'WhatsApp', 'Google Ads', 'LinkedIn', 'Otro'];
const ESTADOS = { activa: { label: 'Activa', color: '#22c55e' }, pausada: { label: 'Pausada', color: '#f59e0b' }, completada: { label: 'Completada', color: '#6366f1' } };

const empty = { titulo: '', canal: 'Instagram', objetivo: '', descripcion: '', estado: 'activa', fecha_inicio: '', fecha_fin: '' };

export default function Marketing() {
    const [items, setItems] = useState([]);
    const [modal, setModal] = useState(false);
    const [form, setForm] = useState(empty);
    const [editId, setEditId] = useState(null);

    useEffect(() => { cargar(); }, []);
    async function cargar() { const { data } = await axios.get('/marketing'); setItems(data); }

    async function guardar(e) {
        e.preventDefault();
        if (editId) await axios.put(`/marketing/${editId}`, form);
        else await axios.post('/marketing', form);
        setModal(false); setForm(empty); setEditId(null); cargar();
    }

    function editar(item) { setForm({ titulo: item.titulo, canal: item.canal, objetivo: item.objetivo, descripcion: item.descripcion || '', estado: item.estado, fecha_inicio: item.fecha_inicio || '', fecha_fin: item.fecha_fin || '' }); setEditId(item.id); setModal(true); }

    async function eliminar(id) { if (!confirm('¿Eliminar?')) return; await axios.delete(`/marketing/${id}`); cargar(); }

    async function cambiarEstado(id, estado) { await axios.put(`/marketing/${id}`, { estado }); cargar(); }

    const porEstado = { activa: [], pausada: [], completada: [] };
    items.forEach(i => porEstado[i.estado]?.push(i));

    return (
        <div style={{ padding: 28 }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <TrendingUp size={22} color="#6366f1" />
                    <h2 style={{ margin: 0, fontSize: '1.3rem', fontWeight: 700 }}>Estrategias de Marketing</h2>
                </div>
                <button onClick={() => { setForm(empty); setEditId(null); setModal(true); }} style={{ display: 'flex', alignItems: 'center', gap: 6, background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 16px', cursor: 'pointer', fontWeight: 600 }}>
                    <Plus size={16} /> Nueva estrategia
                </button>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 20 }}>
                {Object.entries(ESTADOS).map(([estado, { label, color }]) => (
                    <div key={estado}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 12 }}>
                            <div style={{ width: 10, height: 10, borderRadius: '50%', background: color }} />
                            <span style={{ fontWeight: 700, fontSize: 13, color: '#374151' }}>{label}</span>
                            <span style={{ background: '#f3f4f6', borderRadius: 20, padding: '1px 8px', fontSize: 12, color: '#6b7280' }}>{porEstado[estado].length}</span>
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                            {porEstado[estado].map(item => (
                                <div key={item.id} style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 10, padding: 16, boxShadow: '0 1px 3px rgba(0,0,0,.05)' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                        <div style={{ fontWeight: 600, fontSize: '.95rem', color: '#111827', marginBottom: 4 }}>{item.titulo}</div>
                                        <div style={{ display: 'flex', gap: 6 }}>
                                            <button onClick={() => editar(item)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#9ca3af' }}><Edit2 size={14} /></button>
                                            <button onClick={() => eliminar(item.id)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#ef4444' }}><X size={14} /></button>
                                        </div>
                                    </div>
                                    <div style={{ display: 'flex', gap: 6, marginBottom: 8, flexWrap: 'wrap' }}>
                                        <span style={{ background: '#ede9fe', color: '#7c3aed', borderRadius: 20, padding: '2px 10px', fontSize: 11, fontWeight: 600 }}>{item.canal}</span>
                                    </div>
                                    {item.objetivo && <div style={{ fontSize: 12, color: '#6b7280', marginBottom: 8 }}>🎯 {item.objetivo}</div>}
                                    {item.descripcion && <div style={{ fontSize: 12, color: '#9ca3af', lineHeight: 1.5 }}>{item.descripcion}</div>}
                                    {item.fecha_inicio && <div style={{ fontSize: 11, color: '#d1d5db', marginTop: 8 }}>{item.fecha_inicio} {item.fecha_fin ? `→ ${item.fecha_fin}` : ''}</div>}
                                    <div style={{ display: 'flex', gap: 6, marginTop: 10 }}>
                                        {Object.entries(ESTADOS).filter(([e]) => e !== estado).map(([e, { label: l, color: c }]) => (
                                            <button key={e} onClick={() => cambiarEstado(item.id, e)} style={{ fontSize: 11, padding: '3px 8px', border: `1px solid ${c}`, color: c, borderRadius: 6, background: 'none', cursor: 'pointer' }}>→ {l}</button>
                                        ))}
                                    </div>
                                </div>
                            ))}
                            {porEstado[estado].length === 0 && <div style={{ textAlign: 'center', color: '#d1d5db', fontSize: 13, padding: '20px 0' }}>Sin estrategias</div>}
                        </div>
                    </div>
                ))}
            </div>

            {modal && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,.4)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100 }} onClick={() => setModal(false)}>
                    <div style={{ background: '#fff', borderRadius: 12, padding: 28, width: 480, maxHeight: '90vh', overflowY: 'auto' }} onClick={e => e.stopPropagation()}>
                        <h3 style={{ margin: '0 0 20px', fontSize: '1.1rem' }}>{editId ? 'Editar' : 'Nueva'} estrategia</h3>
                        <form onSubmit={guardar}>
                            {[['Título', 'titulo', 'text'], ['Objetivo', 'objetivo', 'text']].map(([label, key, type]) => (
                                <div key={key} style={{ marginBottom: 14 }}>
                                    <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5, color: '#374151' }}>{label}</label>
                                    <input type={type} value={form[key]} onChange={e => setForm(f => ({ ...f, [key]: e.target.value }))} required={key === 'titulo'} style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' }} />
                                </div>
                            ))}
                            <div style={{ marginBottom: 14 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5, color: '#374151' }}>Canal</label>
                                <select value={form.canal} onChange={e => setForm(f => ({ ...f, canal: e.target.value }))} style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14 }}>
                                    {CANALES.map(c => <option key={c}>{c}</option>)}
                                </select>
                            </div>
                            <div style={{ marginBottom: 14 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5, color: '#374151' }}>Descripción</label>
                                <textarea value={form.descripcion} onChange={e => setForm(f => ({ ...f, descripcion: e.target.value }))} rows={3} style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, resize: 'vertical', boxSizing: 'border-box' }} />
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 14 }}>
                                {[['Fecha inicio', 'fecha_inicio'], ['Fecha fin', 'fecha_fin']].map(([label, key]) => (
                                    <div key={key}>
                                        <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5, color: '#374151' }}>{label}</label>
                                        <input type="date" value={form[key]} onChange={e => setForm(f => ({ ...f, [key]: e.target.value }))} style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' }} />
                                    </div>
                                ))}
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
