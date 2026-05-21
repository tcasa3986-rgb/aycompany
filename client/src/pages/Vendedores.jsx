import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Users, ChevronDown, ChevronRight, ToggleLeft, ToggleRight, Trash2, UserPlus, Copy } from 'lucide-react';

const BASE_URL = window.location.origin;

export default function Vendedores() {
    const [vendedores, setVendedores]   = useState([]);
    const [expandido,  setExpandido]    = useState({});
    const [loading,    setLoading]      = useState(true);

    const cargar = () => {
        setLoading(true);
        api.get('/admin/vendedores')
            .then(r => setVendedores(r.data.data))
            .finally(() => setLoading(false));
    };
    useEffect(() => { cargar(); }, []);

    async function toggleActivo(id) {
        await api.patch(`/admin/vendedores/${id}/activo`);
        cargar();
    }

    async function eliminar(id, nombre) {
        if (!confirm(`¿Eliminar a ${nombre}? Esta acción no se puede deshacer.`)) return;
        try {
            await api.delete(`/admin/vendedores/${id}`);
            toast.success('Vendedor eliminado');
            cargar();
        } catch { toast.error('Error al eliminar'); }
    }

    function copiarLink(codigo) {
        const link = `${BASE_URL}/unirse?ref=${codigo}`;
        navigator.clipboard.writeText(link).then(() => toast.success('Enlace copiado'));
    }

    const totalLeads    = vendedores.reduce((s, v) => s + (Number(v.leads) || 0), 0);
    const totalClientes = vendedores.reduce((s, v) => s + (Number(v.clientes) || 0), 0);
    const totalEquipo   = vendedores.reduce((s, v) => s + (v.equipo?.length || 0), 0);

    return (
        <div style={{ padding: 32, maxWidth: 1000 }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <div>
                    <h1 style={{ fontSize: '1.4rem', fontWeight: 700, display: 'flex', alignItems: 'center', gap: 8 }}>
                        <Users size={22} color="#6366f1" /> Vendedores
                    </h1>
                    <p style={{ color: '#64748b', fontSize: '.88rem', marginTop: 2 }}>
                        Equipo de ventas, sus prospectos y sus redes de referidos
                    </p>
                </div>
                <a href="/unirse" target="_blank" style={{ display: 'inline-flex', alignItems: 'center', gap: 6, padding: '9px 16px', background: '#6366f1', color: '#fff', borderRadius: 8, fontSize: '.88rem', fontWeight: 600, textDecoration: 'none' }}>
                    <UserPlus size={15} /> Invitar vendedor
                </a>
            </div>

            {/* Resumen */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 14, marginBottom: 24 }}>
                {[
                    { label: 'Vendedores',     value: vendedores.length,                    color: '#6366f1', bg: '#ede9fe' },
                    { label: 'Activos',        value: vendedores.filter(v => v.activo).length, color: '#10b981', bg: '#d1fae5' },
                    { label: 'Leads totales',  value: totalLeads,                            color: '#f59e0b', bg: '#fef3c7' },
                    { label: 'Clientes traídos', value: totalClientes,                       color: '#0ea5e9', bg: '#e0f2fe' },
                ].map(c => (
                    <div key={c.label} style={{ background: '#fff', borderRadius: 10, padding: '16px 18px', boxShadow: '0 1px 4px rgba(0,0,0,.07)' }}>
                        <div style={{ fontSize: '1.6rem', fontWeight: 800, color: c.color }}>{c.value}</div>
                        <div style={{ fontSize: '.8rem', color: '#64748b', marginTop: 2 }}>{c.label}</div>
                    </div>
                ))}
            </div>

            {loading && <p style={{ color: '#94a3b8', textAlign: 'center', padding: 40 }}>Cargando...</p>}

            {!loading && vendedores.length === 0 && (
                <div style={{ background: '#fff', borderRadius: 12, padding: 40, textAlign: 'center', boxShadow: '0 1px 4px rgba(0,0,0,.07)' }}>
                    <Users size={40} color="#e2e8f0" style={{ marginBottom: 12 }} />
                    <p style={{ color: '#64748b' }}>Aún no hay vendedores registrados.</p>
                    <a href="/unirse" target="_blank" style={{ display: 'inline-block', marginTop: 12, color: '#6366f1', fontWeight: 600, fontSize: '.9rem' }}>
                        Compartir enlace de registro →
                    </a>
                </div>
            )}

            <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                {vendedores.map(v => {
                    const abierto = expandido[v.id];
                    return (
                        <div key={v.id} style={{ background: '#fff', borderRadius: 12, boxShadow: '0 1px 4px rgba(0,0,0,.07)', overflow: 'hidden' }}>
                            {/* Fila principal */}
                            <div style={{ display: 'flex', alignItems: 'center', padding: '14px 18px', gap: 12 }}>
                                <button onClick={() => setExpandido(e => ({ ...e, [v.id]: !abierto }))}
                                    style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#94a3b8', padding: 0, flexShrink: 0 }}>
                                    {abierto ? <ChevronDown size={18} /> : <ChevronRight size={18} />}
                                </button>

                                <div style={{ flex: 1, minWidth: 0 }}>
                                    <div style={{ fontWeight: 700, fontSize: '.95rem', display: 'flex', alignItems: 'center', gap: 8 }}>
                                        {v.nombre}
                                        <span style={{ fontSize: '.72rem', fontWeight: 600, padding: '2px 8px', borderRadius: 20,
                                            background: v.activo ? '#d1fae5' : '#fee2e2',
                                            color: v.activo ? '#059669' : '#dc2626' }}>
                                            {v.activo ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </div>
                                    <div style={{ fontSize: '.8rem', color: '#94a3b8', marginTop: 2 }}>
                                        {v.email}{v.ciudad ? ` · ${v.ciudad}` : ''}
                                    </div>
                                </div>

                                {/* Stats */}
                                <div style={{ display: 'flex', gap: 20, flexShrink: 0 }}>
                                    <Stat label="Leads"    value={Number(v.leads)}        color="#f59e0b" />
                                    <Stat label="Clientes" value={Number(v.clientes)}     color="#10b981" />
                                    <Stat label="Equipo"   value={v.equipo?.length ?? 0}  color="#6366f1" />
                                </div>

                                {/* Acciones */}
                                <div style={{ display: 'flex', gap: 6, flexShrink: 0 }}>
                                    {v.codigo_referido && (
                                        <button onClick={() => copiarLink(v.codigo_referido)}
                                            title="Copiar enlace de invitación"
                                            style={{ background: '#f1f5f9', border: 'none', borderRadius: 6, padding: '6px 10px', cursor: 'pointer' }}>
                                            <Copy size={14} color="#64748b" />
                                        </button>
                                    )}
                                    <button onClick={() => toggleActivo(v.id)}
                                        title={v.activo ? 'Desactivar' : 'Activar'}
                                        style={{ background: v.activo ? '#fef3c7' : '#d1fae5', border: 'none', borderRadius: 6, padding: '6px 10px', cursor: 'pointer' }}>
                                        {v.activo ? <ToggleRight size={16} color="#f59e0b" /> : <ToggleLeft size={16} color="#10b981" />}
                                    </button>
                                    <button onClick={() => eliminar(v.id, v.nombre)}
                                        style={{ background: '#fef2f2', border: 'none', borderRadius: 6, padding: '6px 8px', cursor: 'pointer' }}>
                                        <Trash2 size={14} color="#ef4444" />
                                    </button>
                                </div>
                            </div>

                            {/* Equipo expandido */}
                            {abierto && (
                                <div style={{ borderTop: '1px solid #f1f5f9', padding: '12px 18px 16px 48px', background: '#fafafa' }}>
                                    <div style={{ fontSize: '.78rem', fontWeight: 700, color: '#94a3b8', textTransform: 'uppercase', letterSpacing: '.06em', marginBottom: 10 }}>
                                        Equipo de {v.nombre}
                                    </div>

                                    {v.codigo_referido && (
                                        <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 12, background: '#ede9fe', borderRadius: 8, padding: '8px 12px' }}>
                                            <span style={{ fontSize: '.8rem', color: '#6366f1', fontWeight: 600 }}>Enlace de invitación:</span>
                                            <code style={{ fontSize: '.78rem', color: '#4338ca', flex: 1 }}>{BASE_URL}/unirse?ref={v.codigo_referido}</code>
                                            <button onClick={() => copiarLink(v.codigo_referido)}
                                                style={{ background: '#6366f1', border: 'none', borderRadius: 6, padding: '4px 10px', cursor: 'pointer', color: '#fff', fontSize: '.75rem' }}>
                                                Copiar
                                            </button>
                                        </div>
                                    )}

                                    {!v.equipo?.length ? (
                                        <p style={{ fontSize: '.85rem', color: '#94a3b8' }}>Aún no ha invitado a nadie.</p>
                                    ) : (
                                        <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '.85rem' }}>
                                            <thead><tr style={{ color: '#64748b' }}>
                                                <th style={tth}>Nombre</th>
                                                <th style={tth}>Email</th>
                                                <th style={tth}>Ciudad</th>
                                                <th style={tth}>Se unió</th>
                                                <th style={tth}>Estado</th>
                                            </tr></thead>
                                            <tbody>
                                                {v.equipo.map(m => (
                                                    <tr key={m.id} style={{ borderTop: '1px solid #f1f5f9' }}>
                                                        <td style={ttd}><strong>{m.nombre}</strong></td>
                                                        <td style={ttd}>{m.email}</td>
                                                        <td style={ttd}>{m.ciudad || '—'}</td>
                                                        <td style={ttd}>{m.created_at ? new Date(m.created_at).toLocaleDateString('es') : '—'}</td>
                                                        <td style={ttd}>
                                                            <span style={{ fontSize: '.72rem', fontWeight: 600, padding: '2px 8px', borderRadius: 20,
                                                                background: m.activo ? '#d1fae5' : '#fee2e2',
                                                                color: m.activo ? '#059669' : '#dc2626' }}>
                                                                {m.activo ? 'Activo' : 'Inactivo'}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    )}
                                </div>
                            )}
                        </div>
                    );
                })}
            </div>
        </div>
    );
}

function Stat({ label, value, color }) {
    return (
        <div style={{ textAlign: 'center', minWidth: 48 }}>
            <div style={{ fontSize: '1.1rem', fontWeight: 800, color }}>{value ?? 0}</div>
            <div style={{ fontSize: '.7rem', color: '#94a3b8' }}>{label}</div>
        </div>
    );
}

const tth = { padding: '6px 12px', textAlign: 'left', fontWeight: 600, fontSize: '.75rem' };
const ttd = { padding: '8px 12px' };
