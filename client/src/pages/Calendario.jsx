import { useEffect, useState } from 'react';
import axios from '../api/axios';
import { Calendar, Plus, X, ExternalLink, Clock, Users } from 'lucide-react';

const empty = { titulo: '', descripcion: '', fecha: '', duracion: 60, participantes: '', link: '', estado: 'pendiente' };
const ESTADOS = { pendiente: { label: 'Pendiente', color: '#f59e0b', bg: '#fef3c7' }, completada: { label: 'Completada', color: '#22c55e', bg: '#dcfce7' }, cancelada: { label: 'Cancelada', color: '#ef4444', bg: '#fee2e2' } };

function formatFecha(f) {
    if (!f) return '';
    const d = new Date(f);
    return d.toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' — ' + d.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

export default function Calendario() {
    const [reuniones, setReuniones] = useState([]);
    const [modal, setModal] = useState(false);
    const [form, setForm] = useState(empty);
    const [editId, setEditId] = useState(null);
    const [filtro, setFiltro] = useState('pendiente');

    useEffect(() => { cargar(); }, []);
    async function cargar() { const { data } = await axios.get('/reuniones'); setReuniones(data); }

    async function guardar(e) {
        e.preventDefault();
        if (editId) await axios.put(`/reuniones/${editId}`, form);
        else await axios.post('/reuniones', form);
        setModal(false); setForm(empty); setEditId(null); cargar();
    }

    function editar(r) { setForm({ titulo: r.titulo, descripcion: r.descripcion || '', fecha: r.fecha?.slice(0, 16) || '', duracion: r.duracion, participantes: r.participantes || '', link: r.link || '', estado: r.estado }); setEditId(r.id); setModal(true); }

    async function eliminar(id) { if (!confirm('¿Eliminar reunión?')) return; await axios.delete(`/reuniones/${id}`); cargar(); }
    async function cambiarEstado(id, estado) { await axios.put(`/reuniones/${id}`, { estado }); cargar(); }

    const filtradas = filtro === 'todas' ? reuniones : reuniones.filter(r => r.estado === filtro);
    const proximas = reuniones.filter(r => r.estado === 'pendiente' && new Date(r.fecha) >= new Date()).slice(0, 3);

    return (
        <div style={{ padding: 28 }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <Calendar size={22} color="#6366f1" />
                    <h2 style={{ margin: 0, fontSize: '1.3rem', fontWeight: 700 }}>Calendario de Reuniones</h2>
                </div>
                <button onClick={() => { setForm(empty); setEditId(null); setModal(true); }} style={{ display: 'flex', alignItems: 'center', gap: 6, background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 16px', cursor: 'pointer', fontWeight: 600 }}>
                    <Plus size={16} /> Nueva reunión
                </button>
            </div>

            {proximas.length > 0 && (
                <div style={{ background: 'linear-gradient(135deg,#6366f1,#8b5cf6)', borderRadius: 12, padding: 20, marginBottom: 24, color: '#fff' }}>
                    <div style={{ fontWeight: 700, marginBottom: 12, fontSize: 14 }}>📅 Próximas reuniones</div>
                    {proximas.map(r => (
                        <div key={r.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: 'rgba(255,255,255,.15)', borderRadius: 8, padding: '10px 14px', marginBottom: 8 }}>
                            <div>
                                <div style={{ fontWeight: 600 }}>{r.titulo}</div>
                                <div style={{ fontSize: 12, opacity: .8, marginTop: 2 }}>{formatFecha(r.fecha)}</div>
                            </div>
                            {r.link && <a href={r.link} target="_blank" rel="noreferrer" style={{ color: '#fff', display: 'flex', alignItems: 'center', gap: 4, fontSize: 12 }}><ExternalLink size={14} /> Unirse</a>}
                        </div>
                    ))}
                </div>
            )}

            <div style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                {[['todas', 'Todas'], ['pendiente', 'Pendientes'], ['completada', 'Completadas'], ['cancelada', 'Canceladas']].map(([val, label]) => (
                    <button key={val} onClick={() => setFiltro(val)} style={{ padding: '6px 14px', border: '1px solid #e5e7eb', borderRadius: 20, fontSize: 13, background: filtro === val ? '#6366f1' : '#fff', color: filtro === val ? '#fff' : '#374151', cursor: 'pointer', fontWeight: filtro === val ? 600 : 400 }}>{label}</button>
                ))}
            </div>

            <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                {filtradas.length === 0 && <div style={{ textAlign: 'center', color: '#9ca3af', padding: '40px 0' }}>Sin reuniones</div>}
                {filtradas.map(r => {
                    const est = ESTADOS[r.estado];
                    return (
                        <div key={r.id} style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 10, padding: 18, display: 'flex', gap: 16, alignItems: 'flex-start' }}>
                            <div style={{ width: 4, alignSelf: 'stretch', borderRadius: 4, background: est.color, flexShrink: 0 }} />
                            <div style={{ flex: 1 }}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                    <div>
                                        <div style={{ fontWeight: 600, fontSize: '1rem', marginBottom: 4 }}>{r.titulo}</div>
                                        <div style={{ display: 'flex', gap: 12, fontSize: 13, color: '#6b7280', flexWrap: 'wrap' }}>
                                            <span><Clock size={13} style={{ verticalAlign: 'middle' }} /> {formatFecha(r.fecha)}</span>
                                            <span>{r.duracion} min</span>
                                            {r.participantes && <span><Users size={13} style={{ verticalAlign: 'middle' }} /> {r.participantes}</span>}
                                        </div>
                                    </div>
                                    <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
                                        <span style={{ background: est.bg, color: est.color, borderRadius: 20, padding: '3px 10px', fontSize: 12, fontWeight: 600 }}>{est.label}</span>
                                        <button onClick={() => editar(r)} style={{ background: '#f3f4f6', border: 'none', borderRadius: 6, padding: '5px 10px', cursor: 'pointer', fontSize: 12 }}>Editar</button>
                                        <button onClick={() => eliminar(r.id)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#ef4444' }}><X size={16} /></button>
                                    </div>
                                </div>
                                {r.descripcion && <div style={{ fontSize: 13, color: '#9ca3af', marginTop: 8 }}>{r.descripcion}</div>}
                                <div style={{ display: 'flex', gap: 8, marginTop: 10, alignItems: 'center' }}>
                                    {r.link && <a href={r.link} target="_blank" rel="noreferrer" style={{ display: 'flex', alignItems: 'center', gap: 4, fontSize: 12, color: '#6366f1', textDecoration: 'none', fontWeight: 600 }}><ExternalLink size={13} /> Unirse al link</a>}
                                    {r.estado === 'pendiente' && <button onClick={() => cambiarEstado(r.id, 'completada')} style={{ fontSize: 12, padding: '4px 10px', background: '#dcfce7', color: '#16a34a', border: 'none', borderRadius: 6, cursor: 'pointer' }}>✓ Completar</button>}
                                    {r.estado === 'pendiente' && <button onClick={() => cambiarEstado(r.id, 'cancelada')} style={{ fontSize: 12, padding: '4px 10px', background: '#fee2e2', color: '#ef4444', border: 'none', borderRadius: 6, cursor: 'pointer' }}>✕ Cancelar</button>}
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>

            {modal && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,.4)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100 }} onClick={() => setModal(false)}>
                    <div style={{ background: '#fff', borderRadius: 12, padding: 28, width: 500, maxHeight: '90vh', overflowY: 'auto' }} onClick={e => e.stopPropagation()}>
                        <h3 style={{ margin: '0 0 20px', fontSize: '1.1rem' }}>{editId ? 'Editar' : 'Nueva'} reunión</h3>
                        <form onSubmit={guardar}>
                            <div style={{ marginBottom: 14 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5 }}>Título *</label>
                                <input type="text" value={form.titulo} onChange={e => setForm(f => ({ ...f, titulo: e.target.value }))} required style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' }} />
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 14 }}>
                                <div>
                                    <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5 }}>Fecha y hora *</label>
                                    <input type="datetime-local" value={form.fecha} onChange={e => setForm(f => ({ ...f, fecha: e.target.value }))} required style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' }} />
                                </div>
                                <div>
                                    <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5 }}>Duración (min)</label>
                                    <input type="number" value={form.duracion} onChange={e => setForm(f => ({ ...f, duracion: e.target.value }))} style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' }} />
                                </div>
                            </div>
                            <div style={{ marginBottom: 14 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5 }}>Participantes</label>
                                <input type="text" value={form.participantes} onChange={e => setForm(f => ({ ...f, participantes: e.target.value }))} placeholder="Ej: Juan, María, Cliente X" style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' }} />
                            </div>
                            <div style={{ marginBottom: 14 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5 }}>Link (Google Meet, Zoom...)</label>
                                <input type="url" value={form.link} onChange={e => setForm(f => ({ ...f, link: e.target.value }))} placeholder="https://meet.google.com/..." style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' }} />
                            </div>
                            <div style={{ marginBottom: 20 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5 }}>Descripción</label>
                                <textarea value={form.descripcion} onChange={e => setForm(f => ({ ...f, descripcion: e.target.value }))} rows={3} style={{ width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, resize: 'vertical', boxSizing: 'border-box' }} />
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
