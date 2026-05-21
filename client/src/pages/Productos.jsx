import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, Pencil, Trash2, X, ExternalLink, Lock } from 'lucide-react';

const VACIO = {
    nombre: '', descripcion: '', descripcion_venta: '', precio_mensual: 250000,
    categoria: 'Sistema', activo: true, visible_vendedor: true,
    demo_url: '', demo_usuario: '', demo_password: ''
};

const CAT_COLORS = {
    CRM: '#0284c7', ERP: '#7c3aed', Salud: '#059669', Comercio: '#d97706',
    Servicios: '#4f46e5', Finanzas: '#dc2626', 'IA': '#db2777', Sistema: '#475569'
};

export default function Productos() {
    const [productos, setProductos] = useState([]);
    const [modal, setModal]         = useState(false);
    const [form, setForm]           = useState(VACIO);
    const [editId, setEditId]       = useState(null);

    const cargar = () => api.get('/productos').then(r => setProductos(r.data.data));
    useEffect(() => { cargar(); }, []);

    function abrirNuevo()  { setForm(VACIO); setEditId(null); setModal(true); }
    function abrirEditar(p) {
        setForm({
            ...VACIO, ...p,
            demo_url:      p.demo_url      || '',
            demo_usuario:  p.demo_usuario  || '',
            demo_password: p.demo_password || '',
        });
        setEditId(p.id);
        setModal(true);
    }

    async function guardar(e) {
        e.preventDefault();
        try {
            editId
                ? await api.put(`/productos/${editId}`, form)
                : await api.post('/productos', form);
            toast.success(editId ? 'Sistema actualizado' : 'Sistema creado');
            setModal(false);
            cargar();
        } catch { toast.error('Error al guardar'); }
    }

    async function eliminar(id) {
        if (!confirm('¿Eliminar este sistema?')) return;
        await api.delete(`/productos/${id}`);
        toast.success('Eliminado');
        cargar();
    }

    const f = (k, v) => setForm(p => ({ ...p, [k]: v }));

    return (
        <div style={{ padding: 32 }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <div>
                    <h1 style={{ fontSize: '1.4rem', fontWeight: 700 }}>Sistemas / Productos</h1>
                    <p style={{ fontSize: '.85rem', color: '#64748b', marginTop: 2 }}>
                        Configura las URLs de demo y credenciales para que los vendedores puedan mostrarlos
                    </p>
                </div>
                <button onClick={abrirNuevo} style={btn('#4f46e5')}><Plus size={16} /> Nuevo sistema</button>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(290px, 1fr))', gap: 16 }}>
                {productos.map(p => {
                    const color = CAT_COLORS[p.categoria] || '#6366f1';
                    return (
                        <div key={p.id} style={{ background: '#fff', borderRadius: 12, padding: 18, boxShadow: '0 1px 4px rgba(0,0,0,.07)', borderTop: `3px solid ${p.activo ? color : '#e2e8f0'}` }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 6 }}>
                                <div>
                                    <span style={{ fontSize: '.68rem', fontWeight: 700, color, background: color + '18', padding: '2px 8px', borderRadius: 20, textTransform: 'uppercase', letterSpacing: '.04em' }}>
                                        {p.categoria}
                                    </span>
                                    <h3 style={{ fontSize: '.95rem', fontWeight: 700, color: '#1e293b', marginTop: 6 }}>{p.nombre}</h3>
                                </div>
                                <span style={{ background: p.activo ? '#dcfce7' : '#f1f5f9', color: p.activo ? '#16a34a' : '#94a3b8', padding: '2px 8px', borderRadius: 20, fontSize: '.68rem', fontWeight: 600, flexShrink: 0 }}>
                                    {p.activo ? 'Activo' : 'Inactivo'}
                                </span>
                            </div>

                            <p style={{ fontSize: '.82rem', color: '#64748b', marginBottom: 10, minHeight: 32, lineHeight: 1.45 }}>
                                {p.descripcion_venta || p.descripcion || 'Sin descripción de venta'}
                            </p>

                            {/* Demo status */}
                            <div style={{ display: 'flex', alignItems: 'center', gap: 6, marginBottom: 12, minHeight: 22 }}>
                                {p.demo_url ? (
                                    <span style={{ fontSize: '.73rem', color: '#059669', background: '#d1fae5', padding: '2px 8px', borderRadius: 20, fontWeight: 600, display: 'flex', alignItems: 'center', gap: 4 }}>
                                        <ExternalLink size={10}/> Demo configurado
                                    </span>
                                ) : (
                                    <span style={{ fontSize: '.73rem', color: '#94a3b8', background: '#f1f5f9', padding: '2px 8px', borderRadius: 20 }}>
                                        Sin demo
                                    </span>
                                )}
                                {p.demo_usuario && (
                                    <span style={{ fontSize: '.73rem', color: '#6366f1', background: '#ede9fe', padding: '2px 8px', borderRadius: 20, fontWeight: 600, display: 'flex', alignItems: 'center', gap: 4 }}>
                                        <Lock size={10}/> Credenciales
                                    </span>
                                )}
                            </div>

                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                <span style={{ fontSize: '1.1rem', fontWeight: 700, color: '#4f46e5' }}>
                                    ${Number(p.precio_mensual).toLocaleString('es')}
                                    <span style={{ fontSize: '.72rem', color: '#94a3b8', fontWeight: 400 }}>/mes</span>
                                </span>
                                <div>
                                    <button onClick={() => abrirEditar(p)} style={btnSm('#6366f1')}><Pencil size={13} /></button>
                                    <button onClick={() => eliminar(p.id)} style={btnSm('#ef4444')}><Trash2 size={13} /></button>
                                </div>
                            </div>
                        </div>
                    );
                })}
                {productos.length === 0 && <p style={{ color: '#94a3b8' }}>No hay sistemas registrados</p>}
            </div>

            {modal && (
                <div style={overlay}>
                    <div style={{ ...modalBox, width: 560, maxHeight: '90vh', overflowY: 'auto' }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 20 }}>
                            <h2 style={{ fontSize: '1.1rem', fontWeight: 700 }}>{editId ? 'Editar' : 'Nuevo'} sistema</h2>
                            <button onClick={() => setModal(false)} style={{ background: 'none', border: 'none', cursor: 'pointer' }}><X size={20} /></button>
                        </div>
                        <form onSubmit={guardar}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                                <div style={{ gridColumn: '1/-1' }}>
                                    <Field label="Nombre del sistema *">
                                        <input value={form.nombre} onChange={e => f('nombre', e.target.value)} required style={inp}/>
                                    </Field>
                                </div>
                                <Field label="Categoría">
                                    <select value={form.categoria} onChange={e => f('categoria', e.target.value)} style={inp}>
                                        {['CRM','ERP','Salud','Comercio','Servicios','Finanzas','IA','Sistema'].map(c =>
                                            <option key={c} value={c}>{c}</option>
                                        )}
                                    </select>
                                </Field>
                                <Field label="Precio mensual ($)">
                                    <input type="number" min="0" value={form.precio_mensual} onChange={e => f('precio_mensual', e.target.value)} style={inp}/>
                                </Field>
                                <div style={{ gridColumn: '1/-1' }}>
                                    <Field label="Descripción de venta (para vendedores)">
                                        <textarea rows={2} value={form.descripcion_venta} onChange={e => f('descripcion_venta', e.target.value)} style={{ ...inp, resize: 'vertical' }}/>
                                    </Field>
                                </div>
                                <div style={{ gridColumn: '1/-1' }}>
                                    <Field label="Descripción técnica">
                                        <textarea rows={2} value={form.descripcion} onChange={e => f('descripcion', e.target.value)} style={{ ...inp, resize: 'vertical' }}/>
                                    </Field>
                                </div>
                            </div>

                            {/* Sección demo */}
                            <div style={{ margin: '16px 0 8px', padding: '14px 16px', background: '#f0fdf4', borderRadius: 10, border: '1px solid #bbf7d0' }}>
                                <div style={{ fontWeight: 700, fontSize: '.85rem', color: '#15803d', marginBottom: 12, display: 'flex', alignItems: 'center', gap: 6 }}>
                                    <ExternalLink size={14}/> Configuración de demo
                                </div>
                                <Field label="URL del sistema demo">
                                    <input value={form.demo_url} onChange={e => f('demo_url', e.target.value)} placeholder="https://demo.tusistema.com" style={inp}/>
                                </Field>
                                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                                    <Field label="Usuario demo">
                                        <input value={form.demo_usuario} onChange={e => f('demo_usuario', e.target.value)} placeholder="demo@empresa.com" style={inp}/>
                                    </Field>
                                    <Field label="Contraseña demo">
                                        <input value={form.demo_password} onChange={e => f('demo_password', e.target.value)} placeholder="demo123" style={inp}/>
                                    </Field>
                                </div>
                            </div>

                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10, marginTop: 8 }}>
                                <Field label="Estado">
                                    <select value={form.activo ? '1' : '0'} onChange={e => f('activo', e.target.value === '1')} style={inp}>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </Field>
                                <Field label="Visible para vendedores">
                                    <select value={form.visible_vendedor ? '1' : '0'} onChange={e => f('visible_vendedor', e.target.value === '1')} style={inp}>
                                        <option value="1">Sí</option>
                                        <option value="0">No</option>
                                    </select>
                                </Field>
                            </div>

                            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 20 }}>
                                <button type="button" onClick={() => setModal(false)} style={btn('#94a3b8')}>Cancelar</button>
                                <button type="submit" style={btn('#4f46e5')}>Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

const btn    = bg => ({ display: 'inline-flex', alignItems: 'center', gap: 6, padding: '9px 16px', background: bg, color: '#fff', border: 'none', borderRadius: 8, fontSize: '.88rem', fontWeight: 600, cursor: 'pointer' });
const btnSm  = bg => ({ padding: '5px 8px', background: bg + '18', color: bg, border: 'none', borderRadius: 6, marginLeft: 4, cursor: 'pointer' });
const overlay  = { position: 'fixed', inset: 0, background: 'rgba(0,0,0,.45)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50 };
const modalBox = { background: '#fff', borderRadius: 14, padding: 28 };
const inp      = { width: '100%', padding: '8px 11px', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: '.88rem', outline: 'none', boxSizing: 'border-box', background: '#fafafa' };
function Field({ label, children }) {
    return (
        <div style={{ marginBottom: 12 }}>
            <label style={{ display: 'block', fontSize: '.78rem', fontWeight: 600, color: '#374151', marginBottom: 4 }}>{label}</label>
            {children}
        </div>
    );
}
