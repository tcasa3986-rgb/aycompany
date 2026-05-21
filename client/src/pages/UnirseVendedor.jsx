import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuthStore } from '../store/authStore';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { CheckCircle, TrendingUp, Users, Calendar, Package } from 'lucide-react';

const BENEFICIOS = [
    { icon: TrendingUp, title: 'Tú cobras la personalización', desc: 'Cada sistema se adapta al cliente. Ese cobro es tuyo.' },
    { icon: Package,    title: '17 sistemas para vender',     desc: 'CRMs, ERPs, sistemas especializados para cada sector.' },
    { icon: Calendar,   title: 'Reuniones gestionadas',       desc: 'Agendas la cita, el equipo técnico la hace contigo.' },
    { icon: Users,      title: 'Tu propio panel',             desc: 'Lleva tus prospectos, pipeline y clientes en un solo lugar.' },
];

export default function UnirseVendedor() {
    const [form,    setForm]    = useState({ nombre: '', email: '', password: '', confirmar: '', telefono: '', ciudad: '' });
    const [loading, setLoading] = useState(false);
    const [listo,   setListo]   = useState(false);
    const { login } = useAuthStore();
    const navigate  = useNavigate();

    async function handleSubmit(e) {
        e.preventDefault();
        if (form.password !== form.confirmar)
            return toast.error('Las contraseñas no coinciden');
        if (form.password.length < 6)
            return toast.error('La contraseña debe tener al menos 6 caracteres');

        setLoading(true);
        try {
            const { data } = await api.post('/auth/registro-vendedor', {
                nombre:   form.nombre,
                email:    form.email,
                password: form.password,
                telefono: form.telefono,
                ciudad:   form.ciudad
            });
            login(data.token, data.user);
            setListo(true);
            setTimeout(() => navigate('/vendedor'), 2500);
        } catch (err) {
            toast.error(err.response?.data?.msg || 'Error al crear la cuenta');
        } finally {
            setLoading(false);
        }
    }

    if (listo) return (
        <div style={{ minHeight: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center', background: 'linear-gradient(135deg,#1e1b4b,#312e81)' }}>
            <div style={{ background: '#fff', borderRadius: 16, padding: '40px 36px', textAlign: 'center', maxWidth: 380 }}>
                <div style={{ background: '#d1fae5', borderRadius: '50%', width: 64, height: 64, display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 16px' }}>
                    <CheckCircle size={32} color="#059669"/>
                </div>
                <h2 style={{ fontSize: '1.3rem', fontWeight: 700, color: '#1e293b', marginBottom: 8 }}>¡Bienvenido al equipo!</h2>
                <p style={{ color: '#64748b', fontSize: '.9rem' }}>Tu cuenta está lista. Te enviamos un email de bienvenida. Entrando a tu portal...</p>
            </div>
        </div>
    );

    return (
        <div style={{ minHeight: '100vh', background: 'linear-gradient(135deg,#1e1b4b 0%,#312e81 100%)', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '24px 16px' }}>
            <div style={{ width: '100%', maxWidth: 900, display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 0, borderRadius: 20, overflow: 'hidden', boxShadow: '0 24px 80px rgba(0,0,0,.4)' }}>

                {/* Panel izquierdo — info */}
                <div style={{ background: '#1e1b4b', padding: '48px 40px', display: 'flex', flexDirection: 'column', justifyContent: 'center' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 10, marginBottom: 32 }}>
                        <img src="/logo-ai-company.png" alt="AI Company" style={{ width: 40, height: 40, objectFit: 'contain' }} onError={e => e.target.style.display='none'}/>
                        <span style={{ fontSize: '1.2rem', fontWeight: 800, color: '#a5b4fc' }}>AI Company CO</span>
                    </div>

                    <h1 style={{ fontSize: '1.8rem', fontWeight: 800, color: '#fff', lineHeight: 1.2, marginBottom: 12 }}>
                        Únete a nuestro equipo de ventas
                    </h1>
                    <p style={{ color: '#94a3b8', fontSize: '.92rem', lineHeight: 1.6, marginBottom: 32 }}>
                        Vende sistemas de gestión a negocios colombianos. Tú consigues el cliente, nosotros hacemos la implementación. Tú cobras la personalización.
                    </p>

                    <div style={{ display: 'flex', flexDirection: 'column', gap: 18 }}>
                        {BENEFICIOS.map(b => (
                            <div key={b.title} style={{ display: 'flex', gap: 12, alignItems: 'flex-start' }}>
                                <div style={{ background: 'rgba(99,102,241,.2)', borderRadius: 8, padding: 8, flexShrink: 0 }}>
                                    <b.icon size={16} color="#818cf8"/>
                                </div>
                                <div>
                                    <div style={{ fontWeight: 700, color: '#fff', fontSize: '.88rem' }}>{b.title}</div>
                                    <div style={{ color: '#94a3b8', fontSize: '.78rem', marginTop: 2 }}>{b.desc}</div>
                                </div>
                            </div>
                        ))}
                    </div>

                    <div style={{ marginTop: 32, padding: '14px 16px', background: 'rgba(99,102,241,.15)', borderRadius: 10, border: '1px solid rgba(99,102,241,.3)' }}>
                        <div style={{ color: '#a5b4fc', fontWeight: 700, fontSize: '.85rem', marginBottom: 4 }}>Modelo de negocio</div>
                        <div style={{ color: '#94a3b8', fontSize: '.8rem', lineHeight: 1.5 }}>
                            Cliente paga personalización → <strong style={{ color: '#fff' }}>tuyo</strong><br/>
                            Cliente paga $250k/mes → <strong style={{ color: '#10b981' }}>AI Company</strong><br/>
                            Tú ganas hoy, nosotros ganamos siempre.
                        </div>
                    </div>
                </div>

                {/* Panel derecho — formulario */}
                <div style={{ background: '#fff', padding: '48px 40px' }}>
                    <h2 style={{ fontSize: '1.3rem', fontWeight: 700, color: '#1e293b', marginBottom: 4 }}>Crear tu cuenta</h2>
                    <p style={{ color: '#64748b', fontSize: '.85rem', marginBottom: 28 }}>Gratis. Sin contrato. Empieza hoy.</p>

                    <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
                        <Field label="Nombre completo *">
                            <input value={form.nombre} onChange={e => setForm({...form,nombre:e.target.value})} required placeholder="Tu nombre" style={inp}/>
                        </Field>
                        <Field label="Email *">
                            <input type="email" value={form.email} onChange={e => setForm({...form,email:e.target.value})} required placeholder="tu@email.com" style={inp}/>
                        </Field>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                            <Field label="Teléfono">
                                <input value={form.telefono} onChange={e => setForm({...form,telefono:e.target.value})} placeholder="+57 300..." style={inp}/>
                            </Field>
                            <Field label="Ciudad">
                                <input value={form.ciudad} onChange={e => setForm({...form,ciudad:e.target.value})} placeholder="Bogotá" style={inp}/>
                            </Field>
                        </div>
                        <Field label="Contraseña *">
                            <input type="password" value={form.password} onChange={e => setForm({...form,password:e.target.value})} required placeholder="Mín. 6 caracteres" style={inp} minLength={6}/>
                        </Field>
                        <Field label="Confirmar contraseña *">
                            <input type="password" value={form.confirmar} onChange={e => setForm({...form,confirmar:e.target.value})} required placeholder="Repite tu contraseña" style={inp}/>
                        </Field>

                        <button type="submit" disabled={loading} style={{ marginTop: 8, width: '100%', padding: '13px', background: loading ? '#94a3b8' : '#4f46e5', color: '#fff', border: 'none', borderRadius: 10, fontSize: '1rem', fontWeight: 700, cursor: loading ? 'not-allowed' : 'pointer' }}>
                            {loading ? 'Creando cuenta...' : 'Crear cuenta gratis →'}
                        </button>

                        <p style={{ textAlign: 'center', fontSize: '.78rem', color: '#94a3b8', marginTop: 4 }}>
                            ¿Ya tienes cuenta?{' '}
                            <a href="/" style={{ color: '#6366f1', textDecoration: 'none', fontWeight: 600 }}>Iniciar sesión</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    );
}

function Field({ label, children }) {
    return (
        <div>
            <label style={{ display: 'block', fontSize: '.78rem', fontWeight: 600, color: '#374151', marginBottom: 4 }}>{label}</label>
            {children}
        </div>
    );
}

const inp = { width: '100%', padding: '10px 12px', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: '.9rem', outline: 'none', boxSizing: 'border-box', background: '#fafafa', transition: 'border-color .15s' };
