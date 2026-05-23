import { useState } from 'react';
import api from '../api/axios';

const TIPOS_SUGERIDOS = [
    'Restaurante', 'Farmacia', 'Ferretería', 'Clínica', 'Odontología',
    'Salón de belleza', 'Hotel', 'Parqueadero', 'Tienda de ropa', 'Panadería',
    'Constructora', 'Colegio', 'Gimnasio', 'Veterinaria', 'Distribuidora',
];

const PASOS = [
    { n: 1, label: 'Investigando en Google Maps...' },
    { n: 2, label: 'Analizando el sitio web...' },
    { n: 3, label: 'Generando propuesta personalizada...' },
];

export default function Prospector() {
    const [form, setForm]           = useState({ nombre: '', ciudad: '', tipo: '', urlDirecta: '' });
    const [cargando, setCargando]   = useState(false);
    const [pasoActual, setPasoActual] = useState(0);
    const [resultado, setResultado] = useState(null);
    const [historial, setHistorial] = useState([]);
    const [verHistorial, setVerHistorial] = useState(false);
    const [emailForm, setEmailForm] = useState({ destinatario: '', asunto: '' });
    const [enviando, setEnviando]   = useState(false);
    const [generandoPdf, setGenerandoPdf] = useState(false);
    const [msgEmail, setMsgEmail]   = useState('');

    function setField(k, v) {
        setForm(f => ({ ...f, [k]: v }));
    }

    async function investigar(e) {
        e.preventDefault();
        if (!form.nombre.trim() || !form.ciudad.trim()) return alert('Ingresa nombre y ciudad del negocio');

        setCargando(true);
        setResultado(null);
        setPasoActual(1);

        const intervalo = setInterval(() => {
            setPasoActual(p => (p < 3 ? p + 1 : 3));
        }, 8000);

        try {
            const { data } = await api.post('/prospector/investigar', {
                nombre:     form.nombre.trim(),
                ciudad:     form.ciudad.trim(),
                tipo:       form.tipo.trim(),
                urlDirecta: form.urlDirecta.trim() || undefined,
            });
            clearInterval(intervalo);
            setPasoActual(0);
            setResultado(data);
            setEmailForm({ destinatario: '', asunto: `Propuesta digital para ${data.nombre}` });
        } catch (err) {
            clearInterval(intervalo);
            setPasoActual(0);
            alert('Error: ' + (err.response?.data?.error || err.message));
        }
        setCargando(false);
    }

    async function generarPdf() {
        if (!resultado?.key) return;
        setGenerandoPdf(true);
        try {
            await api.post(`/prospector/${resultado.key}/pdf`);
            alert('PDF generado correctamente. Puedes enviarlo por email.');
        } catch (e) {
            alert('Error al generar PDF: ' + (e.response?.data?.error || e.message));
        }
        setGenerandoPdf(false);
    }

    async function enviarEmail(e) {
        e.preventDefault();
        if (!emailForm.destinatario) return alert('Ingresa el email del destinatario');
        setEnviando(true);
        setMsgEmail('');
        try {
            const { data } = await api.post(`/prospector/${resultado.key}/email`, emailForm);
            setMsgEmail('✓ ' + data.msg);
        } catch (err) {
            setMsgEmail('✗ ' + (err.response?.data?.error || err.message));
        }
        setEnviando(false);
    }

    async function cargarHistorial() {
        try {
            const { data } = await api.get('/prospector/historial');
            setHistorial(data.data || []);
            setVerHistorial(true);
        } catch {}
    }

    function abrirPropuesta() {
        if (resultado?.key) {
            window.open(`/api/prospector/${resultado.key}/html`, '_blank');
        }
    }

    const s = {
        card: { background: '#fff', borderRadius: 10, padding: 22, boxShadow: '0 1px 4px rgba(0,0,0,.08)', marginBottom: 16 },
        inp:  { padding: '9px 12px', border: '1px solid #e2e8f0', borderRadius: 7, fontSize: '.88rem', width: '100%', boxSizing: 'border-box' },
        lbl:  { display: 'block', fontSize: '.78rem', color: '#64748b', marginBottom: 5, fontWeight: 600 },
    };

    return (
        <div style={{ padding: 28, background: '#f8fafc', minHeight: '100vh' }}>

            {/* Header */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
                <div>
                    <h1 style={{ margin: 0, fontSize: '1.5rem', fontWeight: 700, color: '#1e1b4b' }}>Generador de Propuestas</h1>
                    <p style={{ margin: '4px 0 0', color: '#64748b', fontSize: '.9rem' }}>Investiga un negocio con IA y genera una propuesta personalizada en minutos — sin APIs de pago</p>
                </div>
                <button onClick={cargarHistorial} style={{ background: '#e2e8f0', color: '#475569', border: 'none', borderRadius: 8, padding: '9px 16px', fontWeight: 600, cursor: 'pointer', fontSize: '.85rem' }}>
                    Historial
                </button>
            </div>

            {/* Formulario */}
            <div style={s.card}>
                <form onSubmit={investigar}>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
                        <div>
                            <label style={s.lbl}>Nombre del negocio *</label>
                            <input value={form.nombre} onChange={e => setField('nombre', e.target.value)} placeholder="Ej: Restaurante El Fogón" style={s.inp} required />
                        </div>
                        <div>
                            <label style={s.lbl}>Ciudad *</label>
                            <input value={form.ciudad} onChange={e => setField('ciudad', e.target.value)} placeholder="Ej: Medellín" style={s.inp} required />
                        </div>
                    </div>

                    <div style={{ marginBottom: 12 }}>
                        <label style={s.lbl}>Tipo de negocio (opcional — mejora la detección)</label>
                        <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6, marginBottom: 8 }}>
                            {TIPOS_SUGERIDOS.map(t => (
                                <button key={t} type="button" onClick={() => setField('tipo', form.tipo === t ? '' : t)}
                                    style={{ padding: '3px 10px', borderRadius: 12, border: `1px solid ${form.tipo === t ? '#6366f1' : '#e2e8f0'}`, background: form.tipo === t ? '#6366f1' : '#fff', color: form.tipo === t ? '#fff' : '#475569', cursor: 'pointer', fontSize: '.78rem', fontWeight: form.tipo === t ? 600 : 400 }}>
                                    {t}
                                </button>
                            ))}
                        </div>
                        <input value={form.tipo} onChange={e => setField('tipo', e.target.value)} placeholder="O escríbelo aquí..." style={{ ...s.inp, maxWidth: 320 }} />
                    </div>

                    <div style={{ marginBottom: 20 }}>
                        <label style={s.lbl}>URL directa del sitio web (opcional — si ya la conoces)</label>
                        <input value={form.urlDirecta} onChange={e => setField('urlDirecta', e.target.value)} placeholder="https://..." style={{ ...s.inp, maxWidth: 480 }} />
                    </div>

                    <button type="submit" disabled={cargando} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '11px 32px', fontWeight: 700, cursor: 'pointer', fontSize: '.95rem', opacity: cargando ? .7 : 1 }}>
                        {cargando ? '⟳ Investigando...' : '🔍 Investigar y generar propuesta'}
                    </button>
                </form>
            </div>

            {/* Progreso */}
            {cargando && (
                <div style={{ ...s.card, background: '#1e1b4b', color: '#fff' }}>
                    <div style={{ display: 'flex', gap: 16, alignItems: 'center', flexWrap: 'wrap' }}>
                        {PASOS.map(p => (
                            <div key={p.n} style={{ display: 'flex', alignItems: 'center', gap: 8, opacity: pasoActual >= p.n ? 1 : .35 }}>
                                <div style={{ width: 28, height: 28, borderRadius: '50%', background: pasoActual > p.n ? '#10b981' : pasoActual === p.n ? '#6366f1' : '#3a3a6a', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 13, fontWeight: 700, flexShrink: 0 }}>
                                    {pasoActual > p.n ? '✓' : p.n}
                                </div>
                                <span style={{ fontSize: '.85rem' }}>{p.label}</span>
                                {p.n < 3 && <span style={{ color: '#4a4a8a', fontSize: 18 }}>→</span>}
                            </div>
                        ))}
                    </div>
                    <p style={{ marginTop: 14, color: '#9090c0', fontSize: '.82rem' }}>Esto tarda entre 30 y 60 segundos. No cierres la ventana.</p>
                </div>
            )}

            {/* Resultado */}
            {resultado && !cargando && (
                <>
                    {/* Info del negocio */}
                    <div style={{ ...s.card, borderLeft: '4px solid #6366f1' }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: 12 }}>
                            <div>
                                <h2 style={{ margin: '0 0 4px', fontSize: '1.2rem', color: '#1e1b4b' }}>{resultado.nombre}</h2>
                                <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap', fontSize: '.83rem', color: '#64748b' }}>
                                    <span>📍 {resultado.ciudad}</span>
                                    {resultado.telefono && <span>📞 {resultado.telefono}</span>}
                                    {resultado.rating && <span>⭐ {resultado.rating}</span>}
                                    {resultado.direccion && <span>🏠 {resultado.direccion}</span>}
                                </div>
                                {resultado.sitioUrl
                                    ? <a href={resultado.sitioUrl} target="_blank" rel="noreferrer" style={{ color: '#6366f1', fontSize: '.83rem', marginTop: 4, display: 'inline-block' }}>🌐 {resultado.sitioUrl}</a>
                                    : <span style={{ color: '#ef4444', fontSize: '.83rem', marginTop: 4, display: 'inline-block' }}>⚠ Sin sitio web — oportunidad de venta</span>
                                }
                            </div>
                            <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
                                <button onClick={abrirPropuesta} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 7, padding: '9px 18px', fontWeight: 600, cursor: 'pointer', fontSize: '.85rem' }}>
                                    👁 Ver propuesta
                                </button>
                                <button onClick={generarPdf} disabled={generandoPdf} style={{ background: generandoPdf ? '#e2e8f0' : '#0f172a', color: generandoPdf ? '#94a3b8' : '#fff', border: 'none', borderRadius: 7, padding: '9px 18px', fontWeight: 600, cursor: 'pointer', fontSize: '.85rem' }}>
                                    {generandoPdf ? '⟳ Generando...' : '📄 Generar PDF'}
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Análisis */}
                    {resultado.analisis && (
                        <div style={{ ...s.card }}>
                            <h3 style={{ margin: '0 0 12px', fontSize: '1rem', color: '#1e1b4b' }}>Presencia digital detectada</h3>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                                <div>
                                    <p style={{ fontSize: '.78rem', color: '#16a34a', fontWeight: 700, marginBottom: 6 }}>✓ Tiene</p>
                                    <div style={{ display: 'flex', flexWrap: 'wrap', gap: 5 }}>
                                        {resultado.analisis.tiene.length > 0
                                            ? resultado.analisis.tiene.map(t => <span key={t} style={{ background: '#f0fdf4', border: '1px solid #86efac', borderRadius: 12, padding: '2px 10px', fontSize: '.78rem', color: '#15803d' }}>{t}</span>)
                                            : <span style={{ fontSize: '.78rem', color: '#94a3b8' }}>Ninguno detectado</span>
                                        }
                                    </div>
                                </div>
                                <div>
                                    <p style={{ fontSize: '.78rem', color: '#dc2626', fontWeight: 700, marginBottom: 6 }}>✗ Le falta</p>
                                    <div style={{ display: 'flex', flexWrap: 'wrap', gap: 5 }}>
                                        {resultado.analisis.falta.map(f => <span key={f} style={{ background: '#fef2f2', border: '1px solid #fca5a5', borderRadius: 12, padding: '2px 10px', fontSize: '.78rem', color: '#dc2626' }}>{f}</span>)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Demos sugeridas */}
                    {resultado.demos?.length > 0 && (
                        <div style={s.card}>
                            <h3 style={{ margin: '0 0 12px', fontSize: '1rem', color: '#1e1b4b' }}>Demos recomendadas para este negocio</h3>
                            <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
                                {resultado.demos.map(d => (
                                    <div key={d.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: '#f8fafc', border: '1px solid #e2e8f0', borderRadius: 8, padding: '10px 16px' }}>
                                        <span style={{ fontWeight: 600, color: '#1e1b4b', fontSize: '.9rem' }}>{d.label}</span>
                                        <a href={d.url} target="_blank" rel="noreferrer" style={{ background: '#6366f1', color: '#fff', padding: '5px 14px', borderRadius: 6, textDecoration: 'none', fontSize: '.8rem', fontWeight: 600 }}>Ver demo →</a>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Enviar por email */}
                    <div style={s.card}>
                        <h3 style={{ margin: '0 0 12px', fontSize: '1rem', color: '#1e1b4b' }}>Enviar propuesta por email</h3>
                        <form onSubmit={enviarEmail} style={{ display: 'flex', gap: 10, flexWrap: 'wrap', alignItems: 'flex-end' }}>
                            <div style={{ flex: 1, minWidth: 220 }}>
                                <label style={s.lbl}>Email del destinatario</label>
                                <input type="email" value={emailForm.destinatario} onChange={e => setEmailForm(f => ({ ...f, destinatario: e.target.value }))} placeholder="contacto@empresa.com" style={s.inp} required />
                            </div>
                            <div style={{ flex: 2, minWidth: 260 }}>
                                <label style={s.lbl}>Asunto</label>
                                <input value={emailForm.asunto} onChange={e => setEmailForm(f => ({ ...f, asunto: e.target.value }))} style={s.inp} />
                            </div>
                            <button type="submit" disabled={enviando} style={{ background: '#10b981', color: '#fff', border: 'none', borderRadius: 7, padding: '9px 20px', fontWeight: 600, cursor: 'pointer', fontSize: '.85rem', opacity: enviando ? .7 : 1 }}>
                                {enviando ? '⟳ Enviando...' : '📧 Enviar'}
                            </button>
                        </form>
                        {msgEmail && (
                            <p style={{ marginTop: 10, fontSize: '.85rem', color: msgEmail.startsWith('✓') ? '#16a34a' : '#dc2626', fontWeight: 600 }}>{msgEmail}</p>
                        )}
                        {!process.env.GMAIL_USER && (
                            <p style={{ marginTop: 8, fontSize: '.78rem', color: '#f59e0b' }}>⚠ Configura GMAIL_USER y GMAIL_PASS en Railway para activar el envío por email.</p>
                        )}
                    </div>

                    {/* Nueva propuesta */}
                    <button onClick={() => { setResultado(null); setForm({ nombre: '', ciudad: '', tipo: '', urlDirecta: '' }); }} style={{ background: '#e2e8f0', color: '#475569', border: 'none', borderRadius: 8, padding: '9px 20px', fontWeight: 600, cursor: 'pointer', fontSize: '.85rem', marginBottom: 24 }}>
                        + Nueva propuesta
                    </button>
                </>
            )}

            {/* Historial */}
            {verHistorial && (
                <div style={s.card}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 12 }}>
                        <h3 style={{ margin: 0, fontSize: '1rem', color: '#1e1b4b' }}>Historial de propuestas ({historial.length})</h3>
                        <button onClick={() => setVerHistorial(false)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#94a3b8', fontSize: 18 }}>✕</button>
                    </div>
                    {historial.length === 0
                        ? <p style={{ color: '#94a3b8', fontSize: '.85rem' }}>Ninguna propuesta generada en esta sesión.</p>
                        : historial.map(h => (
                            <div key={h.key} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '8px 0', borderBottom: '1px solid #f1f5f9', flexWrap: 'wrap', gap: 8 }}>
                                <div>
                                    <strong style={{ fontSize: '.88rem', color: '#1e1b4b' }}>{h.nombre}</strong>
                                    <span style={{ marginLeft: 8, fontSize: '.78rem', color: '#64748b' }}>{h.ciudad}</span>
                                    {h.tienePDF && <span style={{ marginLeft: 8, fontSize: '.72rem', background: '#f0fdf4', color: '#16a34a', border: '1px solid #86efac', borderRadius: 10, padding: '1px 7px' }}>PDF</span>}
                                </div>
                                <a href={`/api/prospector/${h.key}/html`} target="_blank" rel="noreferrer" style={{ color: '#6366f1', fontSize: '.8rem', fontWeight: 600 }}>Ver propuesta →</a>
                            </div>
                        ))
                    }
                </div>
            )}
        </div>
    );
}
