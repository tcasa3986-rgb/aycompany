import { useEffect, useState } from 'react';
import api from '../api/axios';

const ESTADOS = [
    { key: 'nuevo',            label: 'Nuevo',            color: '#6366f1' },
    { key: 'contactado',       label: 'Contactado',       color: '#f59e0b' },
    { key: 'respondio',        label: 'Respondió',        color: '#3b82f6' },
    { key: 'interesado',       label: 'Interesado',       color: '#8b5cf6' },
    { key: 'reunion_agendada', label: 'Reunión agendada', color: '#10b981' },
    { key: 'reunion_realizada',label: 'Reunión realizada',color: '#059669' },
    { key: 'cliente',          label: 'Cliente',          color: '#16a34a' },
    { key: 'sin_respuesta',    label: 'Sin respuesta',    color: '#94a3b8' },
    { key: 'descartado',       label: 'Descartado',       color: '#ef4444' },
];

const FUENTES = ['manual', 'formulario', 'importacion', 'referido', 'redes sociales'];

const vacio = { nombre: '', email: '', telefono: '', empresa: '', fuente: 'manual', notas: '', estado: 'nuevo', agente_activo: true };

export default function Leads() {
    const [leads, setLeads]         = useState([]);
    const [stats, setStats]         = useState({});
    const [modal, setModal]         = useState(false);
    const [form, setForm]           = useState(vacio);
    const [editId, setEditId]       = useState(null);
    const [actividad, setActividad] = useState([]);
    const [leadVer, setLeadVer]     = useState(null);
    const [filtroEstado, setFiltro] = useState('todos');
    const [procesando, setProcesando] = useState(null);

    async function cargar() {
        const [r1, r2] = await Promise.all([api.get('/leads'), api.get('/leads/stats')]);
        setLeads(r1.data);
        setStats(r2.data);
    }

    useEffect(() => { cargar(); }, []);

    async function guardar() {
        if (!form.nombre || !form.telefono) return alert('Nombre y teléfono son obligatorios');
        if (editId) await api.put(`/leads/${editId}`, form);
        else await api.post('/leads', form);
        setModal(false); setForm(vacio); setEditId(null);
        cargar();
    }

    function abrirEditar(lead) {
        setForm({ nombre: lead.nombre, email: lead.email || '', telefono: lead.telefono || '', empresa: lead.empresa || '', fuente: lead.fuente, notas: lead.notas || '', estado: lead.estado, agente_activo: lead.agente_activo });
        setEditId(lead.id); setModal(true);
    }

    async function eliminar(id) {
        if (!confirm('¿Eliminar este lead?')) return;
        await api.delete(`/leads/${id}`);
        cargar();
    }

    async function verActividad(lead) {
        setLeadVer(lead);
        const r = await api.get(`/leads/${lead.id}/actividad`);
        setActividad(r.data);
    }

    async function procesarManual(lead) {
        setProcesando(lead.id);
        try {
            await api.post(`/leads/${lead.id}/procesar`, { evento: 'Acción manual del administrador' });
            alert('El agente procesó este lead');
            cargar();
        } catch { alert('Error al procesar'); }
        setProcesando(null);
    }

    const leadsFiltrados = filtroEstado === 'todos' ? leads : leads.filter(l => l.estado === filtroEstado);

    const est = (key) => ESTADOS.find(e => e.key === key) || { label: key, color: '#94a3b8' };

    const s = { card: { background: '#fff', borderRadius: 10, padding: 20, boxShadow: '0 1px 4px rgba(0,0,0,.08)' } };

    return (
        <div style={{ padding: 28, background: '#f8fafc', minHeight: '100vh' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <div>
                    <h1 style={{ margin: 0, fontSize: '1.5rem', fontWeight: 700, color: '#1e1b4b' }}>Leads</h1>
                    <p style={{ margin: '4px 0 0', color: '#64748b', fontSize: '.9rem' }}>Pipeline de prospectos del agente de ventas</p>
                </div>
                <button onClick={() => { setForm(vacio); setEditId(null); setModal(true); }} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '10px 20px', fontWeight: 600, cursor: 'pointer' }}>
                    + Nuevo lead
                </button>
            </div>

            {/* Stats */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(140px, 1fr))', gap: 12, marginBottom: 24 }}>
                {[
                    { label: 'Total leads',     val: stats.total || 0,         color: '#6366f1' },
                    { label: 'Respondieron',    val: stats.respondieron || 0,  color: '#3b82f6' },
                    { label: 'Reuniones',       val: stats.reuniones || 0,     color: '#10b981' },
                    { label: 'Clientes',        val: stats.clientes || 0,      color: '#16a34a' },
                    { label: 'Tasa respuesta',  val: `${stats.tasaRespuesta || 0}%`, color: '#f59e0b' },
                    { label: 'Tasa reunión',    val: `${stats.tasaReunion || 0}%`,   color: '#8b5cf6' },
                ].map(({ label, val, color }) => (
                    <div key={label} style={{ ...s.card, textAlign: 'center' }}>
                        <div style={{ fontSize: '1.6rem', fontWeight: 800, color }}>{val}</div>
                        <div style={{ fontSize: '.78rem', color: '#64748b', marginTop: 2 }}>{label}</div>
                    </div>
                ))}
            </div>

            {/* Filtro por estado */}
            <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap', marginBottom: 16 }}>
                <button onClick={() => setFiltro('todos')} style={{ padding: '5px 14px', borderRadius: 20, border: '1px solid #e2e8f0', background: filtroEstado === 'todos' ? '#6366f1' : '#fff', color: filtroEstado === 'todos' ? '#fff' : '#64748b', cursor: 'pointer', fontSize: '.83rem', fontWeight: 600 }}>
                    Todos ({leads.length})
                </button>
                {ESTADOS.map(e => {
                    const cnt = leads.filter(l => l.estado === e.key).length;
                    if (cnt === 0) return null;
                    return (
                        <button key={e.key} onClick={() => setFiltro(e.key)} style={{ padding: '5px 14px', borderRadius: 20, border: `1px solid ${e.color}`, background: filtroEstado === e.key ? e.color : '#fff', color: filtroEstado === e.key ? '#fff' : e.color, cursor: 'pointer', fontSize: '.83rem', fontWeight: 600 }}>
                            {e.label} ({cnt})
                        </button>
                    );
                })}
            </div>

            {/* Tabla */}
            <div style={{ ...s.card, padding: 0, overflow: 'hidden' }}>
                <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '.88rem' }}>
                    <thead>
                        <tr style={{ background: '#f1f5f9', color: '#64748b', textAlign: 'left' }}>
                            {['Nombre / Empresa', 'Teléfono', 'Fuente', 'Estado', 'Contactos', 'Agente', 'Acciones'].map(h => (
                                <th key={h} style={{ padding: '12px 16px', fontWeight: 600 }}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {leadsFiltrados.length === 0 && (
                            <tr><td colSpan={7} style={{ padding: 32, textAlign: 'center', color: '#94a3b8' }}>Sin leads en este estado</td></tr>
                        )}
                        {leadsFiltrados.map(lead => {
                            const e = est(lead.estado);
                            return (
                                <tr key={lead.id} style={{ borderTop: '1px solid #f1f5f9' }}>
                                    <td style={{ padding: '12px 16px' }}>
                                        <div style={{ fontWeight: 600, color: '#1e293b' }}>{lead.nombre}</div>
                                        {lead.empresa && <div style={{ fontSize: '.78rem', color: '#94a3b8' }}>{lead.empresa}</div>}
                                        {lead.email && <div style={{ fontSize: '.75rem', color: '#94a3b8' }}>{lead.email}</div>}
                                    </td>
                                    <td style={{ padding: '12px 16px', color: '#475569' }}>{lead.telefono}</td>
                                    <td style={{ padding: '12px 16px' }}>
                                        <span style={{ fontSize: '.78rem', background: '#f1f5f9', borderRadius: 4, padding: '2px 8px', color: '#64748b' }}>{lead.fuente}</span>
                                    </td>
                                    <td style={{ padding: '12px 16px' }}>
                                        <span style={{ background: e.color + '20', color: e.color, borderRadius: 12, padding: '3px 10px', fontWeight: 600, fontSize: '.78rem' }}>{e.label}</span>
                                    </td>
                                    <td style={{ padding: '12px 16px', color: '#475569', textAlign: 'center' }}>{lead.intentos_contacto}</td>
                                    <td style={{ padding: '12px 16px', textAlign: 'center' }}>
                                        <span style={{ color: lead.agente_activo ? '#10b981' : '#94a3b8', fontWeight: 700, fontSize: '.85rem' }}>
                                            {lead.agente_activo ? '● Activo' : '○ Pausado'}
                                        </span>
                                    </td>
                                    <td style={{ padding: '12px 16px' }}>
                                        <div style={{ display: 'flex', gap: 6 }}>
                                            <button onClick={() => verActividad(lead)} style={{ ...btnStyle('#6366f1') }}>Historial</button>
                                            <button onClick={() => procesarManual(lead)} disabled={procesando === lead.id} style={{ ...btnStyle('#8b5cf6') }}>
                                                {procesando === lead.id ? '...' : 'Agente'}
                                            </button>
                                            <button onClick={() => abrirEditar(lead)} style={{ ...btnStyle('#f59e0b') }}>Editar</button>
                                            <button onClick={() => eliminar(lead.id)} style={{ ...btnStyle('#ef4444') }}>×</button>
                                        </div>
                                    </td>
                                </tr>
                            );
                        })}
                    </tbody>
                </table>
            </div>

            {/* Modal crear/editar lead */}
            {modal && (
                <div style={overlay}>
                    <div style={modalBox}>
                        <h3 style={{ margin: '0 0 20px', color: '#1e1b4b' }}>{editId ? 'Editar lead' : 'Nuevo lead'}</h3>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                            {[['nombre','Nombre *'], ['email','Email'], ['telefono','Teléfono *'], ['empresa','Empresa']].map(([k, lbl]) => (
                                <div key={k}>
                                    <label style={labelStyle}>{lbl}</label>
                                    <input value={form[k]} onChange={e => setForm(f => ({ ...f, [k]: e.target.value }))} style={inputStyle} />
                                </div>
                            ))}
                            <div>
                                <label style={labelStyle}>Fuente</label>
                                <select value={form.fuente} onChange={e => setForm(f => ({ ...f, fuente: e.target.value }))} style={inputStyle}>
                                    {FUENTES.map(f => <option key={f}>{f}</option>)}
                                </select>
                            </div>
                            <div>
                                <label style={labelStyle}>Estado</label>
                                <select value={form.estado} onChange={e => setForm(f => ({ ...f, estado: e.target.value }))} style={inputStyle}>
                                    {ESTADOS.map(e => <option key={e.key} value={e.key}>{e.label}</option>)}
                                </select>
                            </div>
                        </div>
                        <div style={{ marginTop: 12 }}>
                            <label style={labelStyle}>Notas</label>
                            <textarea value={form.notas} onChange={e => setForm(f => ({ ...f, notas: e.target.value }))} style={{ ...inputStyle, height: 70, resize: 'vertical' }} />
                        </div>
                        <div style={{ marginTop: 12, display: 'flex', alignItems: 'center', gap: 8 }}>
                            <input type="checkbox" id="agente_activo" checked={form.agente_activo} onChange={e => setForm(f => ({ ...f, agente_activo: e.target.checked }))} />
                            <label htmlFor="agente_activo" style={{ fontSize: '.88rem', color: '#475569' }}>Agente activo para este lead</label>
                        </div>
                        <div style={{ display: 'flex', gap: 10, marginTop: 20, justifyContent: 'flex-end' }}>
                            <button onClick={() => { setModal(false); setEditId(null); }} style={{ ...btnStyle('#94a3b8'), padding: '8px 20px' }}>Cancelar</button>
                            <button onClick={guardar} style={{ ...btnStyle('#6366f1'), padding: '8px 20px' }}>Guardar</button>
                        </div>
                    </div>
                </div>
            )}

            {/* Modal historial de actividad */}
            {leadVer && (
                <div style={overlay}>
                    <div style={{ ...modalBox, maxWidth: 600 }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
                            <h3 style={{ margin: 0, color: '#1e1b4b' }}>Historial — {leadVer.nombre}</h3>
                            <button onClick={() => { setLeadVer(null); setActividad([]); }} style={{ background: 'none', border: 'none', fontSize: '1.4rem', cursor: 'pointer', color: '#94a3b8' }}>×</button>
                        </div>
                        {actividad.length === 0 ? (
                            <p style={{ color: '#94a3b8', textAlign: 'center', padding: 20 }}>Sin actividad registrada</p>
                        ) : (
                            <div style={{ maxHeight: 400, overflowY: 'auto' }}>
                                {actividad.map(a => (
                                    <div key={a.id} style={{ borderLeft: '3px solid #6366f1', paddingLeft: 12, marginBottom: 16 }}>
                                        <div style={{ fontSize: '.75rem', color: '#94a3b8' }}>{new Date(a.created_at).toLocaleString('es-CO')} · {a.tipo} · {a.canal}</div>
                                        {a.mensaje && <div style={{ marginTop: 4, color: '#1e293b', fontSize: '.88rem' }}>{a.mensaje}</div>}
                                        {a.resultado && <div style={{ marginTop: 4, background: '#f0fdf4', borderRadius: 6, padding: '6px 10px', color: '#16a34a', fontSize: '.85rem' }}>Respuesta: {a.resultado}</div>}
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}

const btnStyle = (bg) => ({ background: bg + '18', color: bg, border: `1px solid ${bg}30`, borderRadius: 6, padding: '4px 10px', cursor: 'pointer', fontSize: '.78rem', fontWeight: 600 });
const overlay   = { position: 'fixed', inset: 0, background: 'rgba(0,0,0,.4)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 1000 };
const modalBox  = { background: '#fff', borderRadius: 12, padding: 28, width: '90%', maxWidth: 520, maxHeight: '90vh', overflowY: 'auto' };
const labelStyle = { display: 'block', fontSize: '.8rem', color: '#64748b', marginBottom: 4, fontWeight: 600 };
const inputStyle = { width: '100%', padding: '8px 12px', border: '1px solid #e2e8f0', borderRadius: 7, fontSize: '.88rem', boxSizing: 'border-box' };
