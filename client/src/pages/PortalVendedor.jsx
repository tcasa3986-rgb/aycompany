import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { useAuthStore } from '../store/authStore';
import { useNavigate } from 'react-router-dom';
import {
    LayoutDashboard, Users, Calendar, Package, LogOut,
    Plus, X, Phone, Mail, ChevronRight, CheckCircle,
    Clock, UserPlus, TrendingUp, ExternalLink
} from 'lucide-react';

const TABS = [
    { key: 'dashboard',  label: 'Dashboard',   icon: LayoutDashboard },
    { key: 'leads',      label: 'Mis leads',    icon: Users },
    { key: 'reuniones',  label: 'Reuniones',    icon: Calendar },
    { key: 'catalogo',   label: 'Catálogo',     icon: Package },
];

const ESTADOS = [
    { key: 'nuevo',             label: 'Nuevo',            color: '#6366f1' },
    { key: 'contactado',        label: 'Contactado',       color: '#f59e0b' },
    { key: 'interesado',        label: 'Interesado',       color: '#8b5cf6' },
    { key: 'reunion_agendada',  label: 'Reunión agendada', color: '#0284c7' },
    { key: 'reunion_realizada', label: 'Reunión hecha',    color: '#10b981' },
    { key: 'cliente',           label: 'Cliente ✓',        color: '#16a34a' },
    { key: 'sin_respuesta',     label: 'Sin respuesta',    color: '#94a3b8' },
    { key: 'descartado',        label: 'Descartado',       color: '#ef4444' },
];

export default function PortalVendedor() {
    const [tab,        setTab]        = useState('dashboard');
    const [stats,      setStats]      = useState(null);
    const [leads,      setLeads]      = useState([]);
    const [catalogo,   setCatalogo]   = useState([]);
    const [modalLead,  setModalLead]  = useState(false);
    const [modalReu,   setModalReu]   = useState(false);
    const [formLead,   setFormLead]   = useState({ nombre:'', telefono:'', email:'', empresa:'', sistema_interes:'', notas:'' });
    const [formReu,    setFormReu]    = useState({ prospecto:'', telefono:'', sistema:'', fecha:'', duracion:60, notas:'' });

    const { user, logout } = useAuthStore();
    const navigate = useNavigate();

    useEffect(() => {
        api.get('/vendedor/stats').then(r => setStats(r.data.data)).catch(() => {});
        api.get('/vendedor/leads').then(r => setLeads(r.data.data || [])).catch(() => {});
        api.get('/vendedor/catalogo').then(r => setCatalogo(r.data.data || [])).catch(() => {});
    }, []);

    function handleLogout() { logout(); navigate('/'); }

    async function guardarLead(e) {
        e.preventDefault();
        try {
            const r = await api.post('/vendedor/leads', formLead);
            setLeads(l => [r.data.data, ...l]);
            toast.success('Lead agregado');
            setModalLead(false);
            setFormLead({ nombre:'', telefono:'', email:'', empresa:'', sistema_interes:'', notas:'' });
        } catch { toast.error('Error al guardar'); }
    }

    async function cambiarEstado(id, estado) {
        await api.put(`/vendedor/leads/${id}`, { estado });
        setLeads(l => l.map(x => x.id === id ? { ...x, estado } : x));
    }

    async function agendarReunion(e) {
        e.preventDefault();
        try {
            await api.post('/vendedor/reuniones', formReu);
            toast.success('Reunión agendada — el admin fue notificado en Telegram y en el calendario');
            setModalReu(false);
            setFormReu({ prospecto:'', telefono:'', sistema:'', fecha:'', duracion:60, notas:'' });
            api.get('/vendedor/stats').then(r => setStats(r.data.data)).catch(() => {});
        } catch { toast.error('Error al agendar'); }
    }

    const categorias = [...new Set(catalogo.map(p => p.categoria))].sort();
    const leadsActivos = leads.filter(l => !['cliente','descartado','sin_respuesta'].includes(l.estado));
    const leadsClientes = leads.filter(l => l.estado === 'cliente');

    return (
        <div style={{ display: 'flex', height: '100vh', overflow: 'hidden', fontFamily: 'Inter, sans-serif' }}>

            {/* Sidebar */}
            <aside style={{ width: 220, background: '#1e1b4b', display: 'flex', flexDirection: 'column', padding: '24px 0', flexShrink: 0 }}>
                <div style={{ padding: '0 20px 20px', borderBottom: '1px solid rgba(255,255,255,.1)' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                        <img src="/logo-ai-company.png" alt="AI Company" style={{ width: 30, height: 30, objectFit: 'contain' }} onError={e => e.target.style.display='none'}/>
                        <div style={{ fontSize: '1rem', fontWeight: 700, color: '#a5b4fc' }}>AI Company</div>
                    </div>
                    <div style={{ fontSize: '.75rem', color: '#94a3b8', marginTop: 4 }}>Hola, {user?.nombre}</div>
                    <div style={{ fontSize: '.68rem', color: '#6366f1', marginTop: 2, fontWeight: 600 }}>● Portal Vendedor</div>
                </div>

                <nav style={{ flex: 1, padding: '12px 0' }}>
                    {TABS.map(({ key, label, icon: Icon }) => (
                        <button key={key} onClick={() => setTab(key)} style={{
                            display: 'flex', alignItems: 'center', gap: 10, padding: '10px 20px', width: '100%',
                            color: tab === key ? '#fff' : '#94a3b8',
                            background: tab === key ? 'rgba(99,102,241,.3)' : 'transparent',
                            borderLeft: tab === key ? '3px solid #818cf8' : '3px solid transparent',
                            borderRight: 'none', borderTop: 'none', borderBottom: 'none',
                            fontSize: '.9rem', cursor: 'pointer', textAlign: 'left'
                        }}>
                            <Icon size={16}/> {label}
                        </button>
                    ))}
                </nav>

                <button onClick={handleLogout} style={{ display: 'flex', alignItems: 'center', gap: 8, margin: '0 12px', padding: '10px 12px', background: 'rgba(239,68,68,.15)', color: '#fca5a5', border: 'none', borderRadius: 8, fontSize: '.85rem', cursor: 'pointer' }}>
                    <LogOut size={15}/> Cerrar sesión
                </button>
            </aside>

            {/* Main */}
            <main style={{ flex: 1, overflowY: 'auto', background: '#f8fafc' }}>

                {/* ── DASHBOARD ── */}
                {tab === 'dashboard' && (
                    <div style={{ padding: 28 }}>
                        <h1 style={{ fontSize: '1.3rem', fontWeight: 700, color: '#1e293b', marginBottom: 4 }}>Tu panel de ventas</h1>
                        <p style={{ color: '#64748b', fontSize: '.88rem', marginBottom: 20 }}>Gestiona tus prospectos y agenda reuniones con el admin</p>

                        {/* Stats */}
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit,minmax(160px,1fr))', gap: 14, marginBottom: 24 }}>
                            {[
                                { label: 'Leads activos',       value: stats?.leadsActivos || 0,       color: '#6366f1', icon: TrendingUp },
                                { label: 'Reuniones pendientes',value: stats?.reunionesPendientes || 0, color: '#f59e0b', icon: Calendar },
                                { label: 'Clientes este mes',   value: stats?.clientesMes || 0,         color: '#10b981', icon: CheckCircle },
                                { label: 'Total clientes',      value: stats?.clientesTotal || 0,        color: '#8b5cf6', icon: Users },
                            ].map(s => (
                                <div key={s.label} style={{ background: '#fff', borderRadius: 12, padding: '14px 16px', boxShadow: '0 1px 4px rgba(0,0,0,.07)' }}>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 8 }}>
                                        <div style={{ background: s.color + '18', borderRadius: 8, padding: 7 }}><s.icon size={16} color={s.color}/></div>
                                        <span style={{ fontSize: '.75rem', color: '#64748b' }}>{s.label}</span>
                                    </div>
                                    <div style={{ fontSize: '1.6rem', fontWeight: 700, color: '#1e293b' }}>{s.value}</div>
                                </div>
                            ))}
                        </div>

                        {/* Próximas reuniones */}
                        <div style={{ background: '#fff', borderRadius: 12, padding: 20, boxShadow: '0 1px 4px rgba(0,0,0,.07)', marginBottom: 16 }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 14 }}>
                                <span style={{ fontWeight: 700, fontSize: '.95rem', color: '#1e293b' }}>Próximas reuniones</span>
                                <button onClick={() => setModalReu(true)} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '7px 14px', fontSize: '.82rem', fontWeight: 600, cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 5 }}>
                                    <Plus size={13}/> Agendar
                                </button>
                            </div>
                            {(stats?.reuniones || []).length === 0
                                ? <p style={{ color: '#94a3b8', fontSize: '.85rem' }}>No hay reuniones pendientes. ¡Agenda una!</p>
                                : (stats?.reuniones || []).map(r => (
                                    <div key={r.id} style={{ display: 'flex', alignItems: 'center', gap: 12, padding: '10px 0', borderBottom: '1px solid #f1f5f9' }}>
                                        <div style={{ background: '#ede9fe', borderRadius: 8, padding: 8 }}><Calendar size={16} color="#6366f1"/></div>
                                        <div>
                                            <div style={{ fontWeight: 600, fontSize: '.88rem', color: '#1e293b' }}>{r.titulo}</div>
                                            <div style={{ fontSize: '.75rem', color: '#64748b' }}>{new Date(r.fecha).toLocaleDateString('es-CO', { weekday:'long', day:'numeric', month:'long', hour:'2-digit', minute:'2-digit' })}</div>
                                        </div>
                                    </div>
                                ))
                            }
                        </div>

                        {/* Acciones rápidas */}
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                            <button onClick={() => { setTab('leads'); setModalLead(true); }} style={{ background: '#fff', border: '2px dashed #e2e8f0', borderRadius: 12, padding: 16, cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 10, color: '#374151' }}>
                                <UserPlus size={18} color="#6366f1"/> <span style={{ fontWeight: 600, fontSize: '.88rem' }}>Agregar prospecto</span>
                            </button>
                            <button onClick={() => setModalReu(true)} style={{ background: '#fff', border: '2px dashed #e2e8f0', borderRadius: 12, padding: 16, cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 10, color: '#374151' }}>
                                <Calendar size={18} color="#6366f1"/> <span style={{ fontWeight: 600, fontSize: '.88rem' }}>Agendar reunión</span>
                            </button>
                        </div>
                    </div>
                )}

                {/* ── LEADS ── */}
                {tab === 'leads' && (
                    <div style={{ padding: 28 }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
                            <div>
                                <h1 style={{ fontSize: '1.2rem', fontWeight: 700, color: '#1e293b', margin: 0 }}>Mis prospectos</h1>
                                <p style={{ color: '#64748b', fontSize: '.82rem', marginTop: 2 }}>{leadsActivos.length} activos · {leadsClientes.length} convertidos</p>
                            </div>
                            <button onClick={() => setModalLead(true)} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 16px', fontSize: '.85rem', fontWeight: 600, cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 6 }}>
                                <Plus size={15}/> Nuevo prospecto
                            </button>
                        </div>

                        {/* Kanban simplificado */}
                        <div style={{ display: 'flex', gap: 12, overflowX: 'auto', paddingBottom: 12 }}>
                            {ESTADOS.slice(0, 6).map(col => {
                                const items = leads.filter(l => l.estado === col.key);
                                return (
                                    <div key={col.key} style={{ minWidth: 220, flex: '0 0 220px' }}>
                                        <div style={{ background: col.color + '20', borderRadius: '8px 8px 0 0', padding: '8px 12px', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                            <span style={{ fontSize: '.82rem', fontWeight: 700, color: col.color }}>{col.label}</span>
                                            <span style={{ background: col.color, color: '#fff', borderRadius: 20, padding: '1px 7px', fontSize: '.72rem', fontWeight: 700 }}>{items.length}</span>
                                        </div>
                                        <div style={{ background: '#f1f5f9', borderRadius: '0 0 8px 8px', minHeight: 300, padding: 8, display: 'flex', flexDirection: 'column', gap: 8 }}>
                                            {items.map(l => (
                                                <div key={l.id} style={{ background: '#fff', borderRadius: 8, padding: '10px 12px', boxShadow: '0 1px 3px rgba(0,0,0,.06)' }}>
                                                    <div style={{ fontWeight: 600, fontSize: '.85rem', color: '#1e293b' }}>{l.nombre}</div>
                                                    {l.empresa && <div style={{ fontSize: '.72rem', color: '#64748b', marginTop: 2 }}>{l.empresa}</div>}
                                                    {l.sistema_interes && <div style={{ fontSize: '.72rem', color: '#6366f1', marginTop: 2 }}>🖥 {l.sistema_interes}</div>}
                                                    <div style={{ display: 'flex', gap: 6, margin: '6px 0' }}>
                                                        {l.telefono && <a href={`https://wa.me/${l.telefono.replace(/\D/g,'')}`} target="_blank" rel="noopener noreferrer" style={{ fontSize: '.7rem', color: '#25d366', textDecoration: 'none', display: 'flex', alignItems: 'center', gap: 2 }}><Phone size={10}/> WA</a>}
                                                        {l.email && <a href={`mailto:${l.email}`} style={{ fontSize: '.7rem', color: '#6366f1', textDecoration: 'none', display: 'flex', alignItems: 'center', gap: 2 }}><Mail size={10}/> Email</a>}
                                                    </div>
                                                    <select value={l.estado} onChange={e => cambiarEstado(l.id, e.target.value)} style={{ width: '100%', padding: '4px 6px', border: '1px solid #e2e8f0', borderRadius: 5, fontSize: '.72rem', background: '#fafafa' }}>
                                                        {ESTADOS.map(s => <option key={s.key} value={s.key}>{s.label}</option>)}
                                                    </select>
                                                </div>
                                            ))}
                                            {items.length === 0 && <div style={{ textAlign: 'center', color: '#cbd5e1', fontSize: '.78rem', padding: '16px 0' }}>Vacío</div>}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                )}

                {/* ── REUNIONES ── */}
                {tab === 'reuniones' && (
                    <div style={{ padding: 28 }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
                            <div>
                                <h1 style={{ fontSize: '1.2rem', fontWeight: 700, color: '#1e293b', margin: 0 }}>Reuniones agendadas</h1>
                                <p style={{ color: '#64748b', fontSize: '.82rem', marginTop: 2 }}>Cuando agendas una reunión, el admin la recibe en su calendario y Telegram</p>
                            </div>
                            <button onClick={() => setModalReu(true)} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 16px', fontSize: '.85rem', fontWeight: 600, cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 6 }}>
                                <Plus size={15}/> Agendar reunión
                            </button>
                        </div>

                        <div style={{ background: '#fff', borderRadius: 12, boxShadow: '0 1px 4px rgba(0,0,0,.07)', padding: '10px 0' }}>
                            {(stats?.reuniones || []).length === 0
                                ? <div style={{ padding: 40, textAlign: 'center', color: '#94a3b8' }}>No hay reuniones pendientes</div>
                                : (stats?.reuniones || []).map(r => (
                                    <div key={r.id} style={{ display: 'flex', alignItems: 'center', gap: 14, padding: '14px 20px', borderBottom: '1px solid #f1f5f9' }}>
                                        <div style={{ background: '#ede9fe', borderRadius: 10, padding: 10 }}><Calendar size={18} color="#6366f1"/></div>
                                        <div style={{ flex: 1 }}>
                                            <div style={{ fontWeight: 700, color: '#1e293b', fontSize: '.9rem' }}>{r.titulo}</div>
                                            <div style={{ fontSize: '.78rem', color: '#64748b', marginTop: 2 }}>
                                                {new Date(r.fecha).toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                            </div>
                                            {r.descripcion && <div style={{ fontSize: '.75rem', color: '#94a3b8', marginTop: 2 }}>{r.descripcion}</div>}
                                        </div>
                                        <span style={{ background: '#d1fae5', color: '#065f46', padding: '3px 9px', borderRadius: 20, fontSize: '.72rem', fontWeight: 700 }}>Pendiente</span>
                                    </div>
                                ))
                            }
                        </div>
                    </div>
                )}

                {/* ── CATÁLOGO ── */}
                {tab === 'catalogo' && (
                    <div style={{ padding: 28 }}>
                        <div style={{ marginBottom: 20 }}>
                            <h1 style={{ fontSize: '1.2rem', fontWeight: 700, color: '#1e293b', margin: 0 }}>Catálogo de sistemas</h1>
                            <p style={{ color: '#64748b', fontSize: '.82rem', marginTop: 2 }}>Todos los sistemas que puedes vender — desde $250.000/mes</p>
                        </div>

                        {categorias.map(cat => (
                            <div key={cat} style={{ marginBottom: 28 }}>
                                <div style={{ fontSize: '.78rem', fontWeight: 700, color: '#94a3b8', textTransform: 'uppercase', letterSpacing: '.08em', marginBottom: 12 }}>{cat}</div>
                                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill,minmax(260px,1fr))', gap: 14 }}>
                                    {catalogo.filter(p => p.categoria === cat).map(p => (
                                        <div key={p.id} style={{ background: '#fff', borderRadius: 12, padding: '16px 18px', boxShadow: '0 1px 4px rgba(0,0,0,.07)', display: 'flex', flexDirection: 'column', gap: 10 }}>
                                            <div>
                                                <div style={{ fontWeight: 700, fontSize: '.92rem', color: '#1e293b', marginBottom: 4 }}>{p.nombre}</div>
                                                <div style={{ fontSize: '.8rem', color: '#64748b', lineHeight: 1.5 }}>{p.descripcion_venta || p.descripcion}</div>
                                            </div>
                                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: 'auto' }}>
                                                <div>
                                                    <span style={{ fontWeight: 700, color: '#10b981', fontSize: '.95rem' }}>$250.000</span>
                                                    <span style={{ fontSize: '.72rem', color: '#94a3b8' }}>/mes</span>
                                                </div>
                                                <div style={{ display: 'flex', gap: 6 }}>
                                                    {p.demo_url && (
                                                        <a href={p.demo_url} target="_blank" rel="noopener noreferrer" style={{ background: '#ede9fe', color: '#6366f1', border: 'none', borderRadius: 6, padding: '5px 10px', fontSize: '.75rem', fontWeight: 600, cursor: 'pointer', textDecoration: 'none', display: 'flex', alignItems: 'center', gap: 4 }}>
                                                            <ExternalLink size={11}/> Demo
                                                        </a>
                                                    )}
                                                    <button onClick={() => { setFormReu(f => ({ ...f, sistema: p.nombre })); setModalReu(true); }} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 6, padding: '5px 10px', fontSize: '.75rem', fontWeight: 600, cursor: 'pointer' }}>
                                                        Agendar reunión
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </main>

            {/* Modal: nuevo lead */}
            {modalLead && (
                <div style={overlay}>
                    <div style={modalBox}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 18 }}>
                            <h2 style={{ fontSize: '1rem', fontWeight: 700 }}>Nuevo prospecto</h2>
                            <button onClick={() => setModalLead(false)} style={{ background: 'none', border: 'none', cursor: 'pointer' }}><X size={20}/></button>
                        </div>
                        <form onSubmit={guardarLead}>
                            <F label="Nombre *"><input value={formLead.nombre} onChange={e => setFormLead({...formLead,nombre:e.target.value})} required style={inp}/></F>
                            <F label="Teléfono"><input value={formLead.telefono} onChange={e => setFormLead({...formLead,telefono:e.target.value})} style={inp} placeholder="+57 300..."/></F>
                            <F label="Email"><input type="email" value={formLead.email} onChange={e => setFormLead({...formLead,email:e.target.value})} style={inp}/></F>
                            <F label="Empresa"><input value={formLead.empresa} onChange={e => setFormLead({...formLead,empresa:e.target.value})} style={inp}/></F>
                            <F label="Sistema de interés">
                                <select value={formLead.sistema_interes} onChange={e => setFormLead({...formLead,sistema_interes:e.target.value})} style={inp}>
                                    <option value="">— Seleccionar —</option>
                                    {catalogo.map(p => <option key={p.id} value={p.nombre}>{p.nombre}</option>)}
                                </select>
                            </F>
                            <F label="Notas"><textarea value={formLead.notas} onChange={e => setFormLead({...formLead,notas:e.target.value})} style={{ ...inp, resize: 'none' }} rows={2}/></F>
                            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 16 }}>
                                <button type="button" onClick={() => setModalLead(false)} style={btnS('#94a3b8')}>Cancelar</button>
                                <button type="submit" style={btnS('#6366f1')}>Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Modal: agendar reunión */}
            {modalReu && (
                <div style={overlay}>
                    <div style={modalBox}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 18 }}>
                            <h2 style={{ fontSize: '1rem', fontWeight: 700 }}>Agendar reunión</h2>
                            <button onClick={() => setModalReu(false)} style={{ background: 'none', border: 'none', cursor: 'pointer' }}><X size={20}/></button>
                        </div>
                        <div style={{ background: '#ede9fe', borderRadius: 8, padding: '10px 14px', marginBottom: 14, fontSize: '.8rem', color: '#4c1d95' }}>
                            ℹ️ Cuando guardes, el admin recibe una notificación en Telegram y la reunión queda en el calendario.
                        </div>
                        <form onSubmit={agendarReunion}>
                            <F label="Nombre del prospecto *"><input value={formReu.prospecto} onChange={e => setFormReu({...formReu,prospecto:e.target.value})} required style={inp}/></F>
                            <F label="Teléfono"><input value={formReu.telefono} onChange={e => setFormReu({...formReu,telefono:e.target.value})} style={inp} placeholder="+57 300..."/></F>
                            <F label="Sistema de interés">
                                <select value={formReu.sistema} onChange={e => setFormReu({...formReu,sistema:e.target.value})} style={inp}>
                                    <option value="">— Seleccionar —</option>
                                    {catalogo.map(p => <option key={p.id} value={p.nombre}>{p.nombre}</option>)}
                                </select>
                            </F>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                                <F label="Fecha y hora *"><input type="datetime-local" value={formReu.fecha} onChange={e => setFormReu({...formReu,fecha:e.target.value})} required style={inp}/></F>
                                <F label="Duración (min)"><input type="number" value={formReu.duracion} onChange={e => setFormReu({...formReu,duracion:e.target.value})} style={inp} min={15} max={240}/></F>
                            </div>
                            <F label="Notas adicionales"><textarea value={formReu.notas} onChange={e => setFormReu({...formReu,notas:e.target.value})} style={{ ...inp, resize: 'none' }} rows={2} placeholder="Qué quiere el cliente, necesidades especiales..."/></F>
                            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 16 }}>
                                <button type="button" onClick={() => setModalReu(false)} style={btnS('#94a3b8')}>Cancelar</button>
                                <button type="submit" style={btnS('#6366f1')}>Agendar y notificar admin</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

const overlay  = { position: 'fixed', inset: 0, background: 'rgba(0,0,0,.45)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50 };
const modalBox = { background: '#fff', borderRadius: 14, padding: 24, width: 440, maxHeight: '90vh', overflowY: 'auto' };
const inp      = { width: '100%', padding: '8px 11px', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: '.88rem', outline: 'none', boxSizing: 'border-box', background: '#fafafa' };
const btnS     = bg => ({ display: 'inline-flex', alignItems: 'center', gap: 5, padding: '8px 16px', background: bg, color: '#fff', border: 'none', borderRadius: 8, fontSize: '.85rem', fontWeight: 600, cursor: 'pointer' });
function F({ label, children }) { return <div style={{ marginBottom: 12 }}><label style={{ display: 'block', fontSize: '.78rem', fontWeight: 600, color: '#374151', marginBottom: 4 }}>{label}</label>{children}</div>; }
