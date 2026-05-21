import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, X, Building2, Trash2, CheckCircle, XCircle } from 'lucide-react';

const PLANES = [
    { key: 'starter',      label: 'Starter',      desc: 'Hasta 100 clientes / 5 usuarios',  color: '#6366f1' },
    { key: 'professional', label: 'Professional',  desc: 'Hasta 500 clientes / 20 usuarios', color: '#059669' },
    { key: 'enterprise',   label: 'Enterprise',    desc: 'Ilimitado',                        color: '#7c3aed' },
];

const VACIO = { nombre:'', nit:'', email:'', telefono:'', ciudad:'', plan:'starter', color_primario:'#6366f1', email_admin:'', password_admin:'' };

export default function Empresas() {
    const [empresas, setEmpresas] = useState([]);
    const [modal,    setModal]    = useState(false);
    const [form,     setForm]     = useState(VACIO);
    const [editId,   setEditId]   = useState(null);

    const cargar = () => api.get('/empresas').then(r => setEmpresas(r.data.data));
    useEffect(() => { cargar(); }, []);

    function abrirModal(e = null) {
        if (e) { setForm({ nombre:e.nombre, nit:e.nit||'', email:e.email||'', telefono:e.telefono||'', ciudad:e.ciudad||'', plan:e.plan, color_primario:e.color_primario||'#6366f1', email_admin:'', password_admin:'' }); setEditId(e.id); }
        else   { setForm(VACIO); setEditId(null); }
        setModal(true);
    }

    async function guardar(e2) {
        e2.preventDefault();
        try {
            if (editId) { await api.put(`/empresas/${editId}`, form); toast.success('Empresa actualizada'); }
            else        { await api.post('/empresas', form); toast.success('Empresa creada'); }
            setModal(false); cargar();
        } catch (err) { toast.error(err.response?.data?.msg || 'Error'); }
    }

    async function desactivar(id) {
        if (!confirm('¿Desactivar esta empresa?')) return;
        await api.delete(`/empresas/${id}`); toast.success('Desactivada'); cargar();
    }

    return (
        <div style={{ padding: 32, maxWidth: 1000 }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <div>
                    <h1 style={{ fontSize: '1.4rem', fontWeight: 700, display: 'flex', alignItems: 'center', gap: 8 }}>
                        <Building2 size={22} color="#6366f1"/> Empresas / White Label
                    </h1>
                    <p style={{ color: '#64748b', fontSize: '.88rem', marginTop: 2 }}>Gestiona múltiples empresas en la misma plataforma</p>
                </div>
                <button onClick={() => abrirModal()} style={btn('#6366f1')}><Plus size={16}/> Nueva empresa</button>
            </div>

            {/* Planes */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 12, marginBottom: 24 }}>
                {PLANES.map(p => (
                    <div key={p.key} style={{ background: '#fff', borderRadius: 10, padding: '14px 16px', boxShadow: '0 1px 4px rgba(0,0,0,.07)' }}>
                        <div style={{ fontWeight: 700, color: p.color, marginBottom: 4 }}>{p.label}</div>
                        <div style={{ fontSize: '.8rem', color: '#64748b' }}>{p.desc}</div>
                    </div>
                ))}
            </div>

            <div style={{ background: '#fff', borderRadius: 12, boxShadow: '0 1px 4px rgba(0,0,0,.07)', overflow: 'hidden' }}>
                <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                    <thead><tr style={{ background: '#f8fafc' }}>
                        {['Empresa','NIT','Email','Plan','Estado',''].map(h => <th key={h} style={th}>{h}</th>)}
                    </tr></thead>
                    <tbody>
                        {empresas.map(e => {
                            const plan = PLANES.find(p => p.key === e.plan) || PLANES[0];
                            return (
                                <tr key={e.id} style={{ borderTop: '1px solid #f1f5f9' }}>
                                    <td style={td}>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                                            <div style={{ width: 12, height: 12, borderRadius: '50%', background: e.color_primario || '#6366f1' }}/>
                                            <strong>{e.nombre}</strong>
                                        </div>
                                        {e.ciudad && <div style={{ fontSize: '.75rem', color: '#94a3b8', marginTop: 2 }}>{e.ciudad}</div>}
                                    </td>
                                    <td style={td}>{e.nit || '—'}</td>
                                    <td style={td}>{e.email || '—'}</td>
                                    <td style={td}><span style={{ background: plan.color + '20', color: plan.color, padding: '3px 9px', borderRadius: 20, fontSize: '.78rem', fontWeight: 700 }}>{plan.label}</span></td>
                                    <td style={td}>
                                        {e.activa
                                            ? <span style={{ display: 'flex', alignItems: 'center', gap: 4, color: '#22c55e', fontSize: '.82rem' }}><CheckCircle size={13}/> Activa</span>
                                            : <span style={{ display: 'flex', alignItems: 'center', gap: 4, color: '#94a3b8', fontSize: '.82rem' }}><XCircle size={13}/> Inactiva</span>}
                                    </td>
                                    <td style={td}>
                                        <div style={{ display: 'flex', gap: 6 }}>
                                            <button onClick={() => abrirModal(e)} style={{ background: '#f1f5f9', border: 'none', borderRadius: 6, padding: '5px 10px', fontSize: '.78rem', cursor: 'pointer' }}>Editar</button>
                                            {e.activa && <button onClick={() => desactivar(e.id)} style={{ background: '#fef2f2', color: '#ef4444', border: 'none', borderRadius: 6, padding: '5px 8px', cursor: 'pointer' }}><Trash2 size={13}/></button>}
                                        </div>
                                    </td>
                                </tr>
                            );
                        })}
                        {empresas.length === 0 && <tr><td colSpan={6} style={{ padding: 32, textAlign: 'center', color: '#94a3b8' }}>No hay empresas registradas</td></tr>}
                    </tbody>
                </table>
            </div>

            {modal && (
                <div style={overlay}>
                    <div style={modalBox}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 20 }}>
                            <h2 style={{ fontSize: '1.1rem', fontWeight: 700 }}>{editId ? 'Editar empresa' : 'Nueva empresa'}</h2>
                            <button onClick={() => setModal(false)} style={{ background: 'none', border: 'none' }}><X size={20}/></button>
                        </div>
                        <form onSubmit={guardar}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                                <F label="Nombre *" s={{ gridColumn: '1/-1' }}><input value={form.nombre} onChange={e => setForm({...form,nombre:e.target.value})} required style={inp}/></F>
                                <F label="NIT"><input value={form.nit} onChange={e => setForm({...form,nit:e.target.value})} style={inp} placeholder="900.000.000-0"/></F>
                                <F label="Email"><input type="email" value={form.email} onChange={e => setForm({...form,email:e.target.value})} style={inp}/></F>
                                <F label="Teléfono"><input value={form.telefono} onChange={e => setForm({...form,telefono:e.target.value})} style={inp}/></F>
                                <F label="Ciudad"><input value={form.ciudad} onChange={e => setForm({...form,ciudad:e.target.value})} style={inp}/></F>
                                <F label="Plan"><select value={form.plan} onChange={e => setForm({...form,plan:e.target.value})} style={inp}>{PLANES.map(p => <option key={p.key} value={p.key}>{p.label}</option>)}</select></F>
                                <F label="Color primario">
                                    <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
                                        <input type="color" value={form.color_primario} onChange={e => setForm({...form,color_primario:e.target.value})} style={{ width: 40, height: 36, borderRadius: 6, border: 'none', cursor: 'pointer', padding: 2 }}/>
                                        <input value={form.color_primario} onChange={e => setForm({...form,color_primario:e.target.value})} style={{ ...inp, flex: 1 }}/>
                                    </div>
                                </F>
                            </div>

                            {!editId && (
                                <div style={{ marginTop: 16, padding: 14, background: '#f8fafc', borderRadius: 8 }}>
                                    <div style={{ fontSize: '.82rem', fontWeight: 700, color: '#374151', marginBottom: 10 }}>Usuario administrador (opcional)</div>
                                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                                        <F label="Email admin"><input type="email" value={form.email_admin} onChange={e => setForm({...form,email_admin:e.target.value})} style={inp}/></F>
                                        <F label="Contraseña"><input type="password" value={form.password_admin} onChange={e => setForm({...form,password_admin:e.target.value})} style={inp} minLength={6}/></F>
                                    </div>
                                </div>
                            )}

                            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 20 }}>
                                <button type="button" onClick={() => setModal(false)} style={btn('#94a3b8')}>Cancelar</button>
                                <button type="submit" style={btn('#6366f1')}>Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

const overlay  = { position: 'fixed', inset: 0, background: 'rgba(0,0,0,.45)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50 };
const modalBox = { background: '#fff', borderRadius: 14, padding: 28, width: 540, maxHeight: '90vh', overflowY: 'auto' };
const btn  = bg => ({ display: 'inline-flex', alignItems: 'center', gap: 6, padding: '9px 16px', background: bg, color: '#fff', border: 'none', borderRadius: 8, fontSize: '.88rem', fontWeight: 600, cursor: 'pointer' });
const inp  = { width: '100%', padding: '8px 11px', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: '.9rem', outline: 'none', boxSizing: 'border-box', background: '#fafafa' };
const td   = { padding: '11px 16px', fontSize: '.9rem' };
const th   = { padding: '10px 16px', textAlign: 'left', fontSize: '.8rem', color: '#64748b', fontWeight: 600 };
function F({ label, children, s }) { return <div style={{ marginBottom: 0, ...s }}><label style={{ display: 'block', fontSize: '.8rem', fontWeight: 600, color: '#374151', marginBottom: 4 }}>{label}</label>{children}</div>; }
