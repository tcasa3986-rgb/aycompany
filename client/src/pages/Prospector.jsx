import { useState, useEffect } from 'react';
import api from '../api/axios';

const TIPOS_SUGERIDOS = [
    'Restaurante', 'Farmacia', 'Ferretería', 'Clínica', 'Odontología',
    'Salón de belleza', 'Hotel', 'Parqueadero', 'Tienda de ropa', 'Panadería',
    'Constructora', 'Colegio', 'Gimnasio', 'Veterinaria', 'Distribuidora',
];

const CATEGORIAS_AUTO = [
    'restaurantes', 'ferreterías', 'clínicas', 'tiendas de ropa',
    'hoteles', 'colegios', 'farmacias', 'peluquerías',
    'talleres mecánicos', 'panaderías', 'gimnasios', 'veterinarias',
    'odontólogos', 'parqueaderos', 'distribuidoras', 'supermercados',
];

const CIUDADES_CO = [
    'Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Bucaramanga',
    'Cartagena', 'Cúcuta', 'Pereira', 'Manizales', 'Santa Marta',
    'Ibagué', 'Villavicencio', 'Pasto', 'Montería', 'Armenia',
];

const PASOS = [
    'Buscando en Google Maps...',
    'Analizando sitio web...',
    'Generando propuesta con IA...',
];

// ─────────────────────────────────────────────────────────────
//  Tarjeta de resultado de propuesta
// ─────────────────────────────────────────────────────────────
function TarjetaPropuesta({ r, onEmail }) {
    const [pdfOk, setPdfOk]     = useState(false);
    const [genPdf, setGenPdf]   = useState(false);

    async function hacerPdf() {
        setGenPdf(true);
        try {
            await api.post(`/prospector/${r.key}/pdf`);
            setPdfOk(true);
        } catch (e) { alert('Error PDF: ' + (e.response?.data?.error || e.message)); }
        setGenPdf(false);
    }

    return (
        <div style={{ background: '#fff', border: '1px solid #e2e8f0', borderRadius: 10, padding: 16, marginBottom: 10 }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: 8 }}>
                <div>
                    <strong style={{ color: '#1e1b4b', fontSize: '.95rem' }}>{r.nombre}</strong>
                    <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap', marginTop: 3, fontSize: '.78rem', color: '#64748b' }}>
                        <span>📍 {r.ciudad}</span>
                        {r.telefono && <span>📞 {r.telefono}</span>}
                        {r.rating   && <span>⭐ {r.rating}</span>}
                        {r.sitioUrl
                            ? <a href={r.sitioUrl} target="_blank" rel="noreferrer" style={{ color: '#6366f1' }}>🌐 sitio web</a>
                            : <span style={{ color: '#ef4444' }}>sin sitio web</span>}
                    </div>
                    {r.analisis?.falta?.length > 0 && (
                        <div style={{ marginTop: 5, fontSize: '.73rem', color: '#94a3b8' }}>
                            Le falta: {r.analisis.falta.slice(0, 4).join(', ')}
                        </div>
                    )}
                </div>
                <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap' }}>
                    <a href={`/api/prospector/${r.key}/html`} target="_blank" rel="noreferrer"
                       style={{ background: '#6366f1', color: '#fff', padding: '5px 12px', borderRadius: 6, textDecoration: 'none', fontSize: '.78rem', fontWeight: 600 }}>
                        👁 Ver
                    </a>
                    <button onClick={hacerPdf} disabled={genPdf}
                        style={{ background: pdfOk ? '#10b981' : '#0f172a', color: '#fff', border: 'none', borderRadius: 6, padding: '5px 12px', fontSize: '.78rem', fontWeight: 600, cursor: 'pointer' }}>
                        {genPdf ? '⟳' : pdfOk ? '✓ PDF' : '📄 PDF'}
                    </button>
                    <button onClick={() => onEmail(r)}
                        style={{ background: '#e2e8f0', color: '#475569', border: 'none', borderRadius: 6, padding: '5px 12px', fontSize: '.78rem', fontWeight: 600, cursor: 'pointer' }}>
                        📧 Email
                    </button>
                </div>
            </div>
        </div>
    );
}

// ─────────────────────────────────────────────────────────────
//  Modal de envío de email
// ─────────────────────────────────────────────────────────────
function ModalEmail({ propuesta, onClose }) {
    const [dest, setDest]     = useState('');
    const [asunto, setAsunto] = useState(`Propuesta digital para ${propuesta?.nombre || ''}`);
    const [enviando, setEnviando] = useState(false);
    const [msg, setMsg]       = useState('');

    async function enviar(e) {
        e.preventDefault();
        setEnviando(true); setMsg('');
        try {
            const { data } = await api.post(`/prospector/${propuesta.key}/email`, { destinatario: dest, asunto });
            setMsg('✓ ' + data.msg);
        } catch (err) {
            setMsg('✗ ' + (err.response?.data?.error || err.message));
        }
        setEnviando(false);
    }

    if (!propuesta) return null;
    return (
        <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 1000 }}>
            <div style={{ background: '#fff', borderRadius: 12, padding: 28, width: '100%', maxWidth: 480 }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 16 }}>
                    <h3 style={{ margin: 0, color: '#1e1b4b' }}>Enviar propuesta por email</h3>
                    <button onClick={onClose} style={{ background: 'none', border: 'none', fontSize: 20, cursor: 'pointer', color: '#94a3b8' }}>✕</button>
                </div>
                <p style={{ marginBottom: 16, fontSize: '.85rem', color: '#64748b' }}>Para: <strong>{propuesta.nombre}</strong></p>
                <form onSubmit={enviar}>
                    <label style={lbl}>Email del destinatario</label>
                    <input type="email" value={dest} onChange={e => setDest(e.target.value)} placeholder="contacto@negocio.com" style={{ ...inp, marginBottom: 12 }} required />
                    <label style={lbl}>Asunto</label>
                    <input value={asunto} onChange={e => setAsunto(e.target.value)} style={{ ...inp, marginBottom: 20 }} />
                    <button type="submit" disabled={enviando} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '10px 24px', fontWeight: 600, cursor: 'pointer', width: '100%' }}>
                        {enviando ? '⟳ Enviando...' : '📧 Enviar propuesta'}
                    </button>
                </form>
                {msg && <p style={{ marginTop: 12, fontSize: '.85rem', color: msg.startsWith('✓') ? '#16a34a' : '#dc2626', fontWeight: 600 }}>{msg}</p>}
            </div>
        </div>
    );
}

// ─────────────────────────────────────────────────────────────
//  Componente principal
// ─────────────────────────────────────────────────────────────
export default function Prospector() {
    const [tab, setTab] = useState('manual'); // manual | auto | historial

    // ── Tab manual ────────────────────────────────────────────
    const [form, setForm]         = useState({ nombre: '', ciudad: '', tipo: '', urlDirecta: '' });
    const [cargando, setCargando] = useState(false);
    const [paso, setPaso]         = useState(0);
    const [resultado, setResultado] = useState(null);

    // ── Tab auto ──────────────────────────────────────────────
    const [config, setConfig]       = useState({ activo: false, hora: 8, categorias: [], ciudades: [], maxPorBusqueda: 5, siguiente: '' });
    const [buscandoAuto, setBuscandoAuto] = useState(false);
    const [catAuto, setCatAuto]     = useState('restaurantes');
    const [ciudadAuto, setCiudadAuto] = useState('Medellín');
    const [maxAuto, setMaxAuto]     = useState(5);
    const [resultadosAuto, setResultadosAuto] = useState([]);
    const [guardandoConfig, setGuardandoConfig] = useState(false);

    // ── Historial ─────────────────────────────────────────────
    const [historial, setHistorial] = useState([]);
    const [cargandoHist, setCargandoHist] = useState(false);

    // ── Email modal ───────────────────────────────────────────
    const [emailTarget, setEmailTarget] = useState(null);

    useEffect(() => {
        api.get('/prospector/config').then(r => setConfig(r.data)).catch(() => {});
    }, []);

    function setField(k, v) { setForm(f => ({ ...f, [k]: v })); }

    // ── Buscar manualmente un negocio ─────────────────────────
    async function investigar(e) {
        e.preventDefault();
        setCargando(true); setResultado(null); setPaso(1);
        const iv = setInterval(() => setPaso(p => Math.min(p + 1, 3)), 8000);
        try {
            const { data } = await api.post('/prospector/investigar', {
                nombre:     form.nombre.trim(),
                ciudad:     form.ciudad.trim(),
                tipo:       form.tipo.trim(),
                urlDirecta: form.urlDirecta.trim() || undefined,
            });
            clearInterval(iv); setPaso(0);
            setResultado(data);
        } catch (err) {
            clearInterval(iv); setPaso(0);
            alert('Error: ' + (err.response?.data?.error || err.message));
        }
        setCargando(false);
    }

    // ── Búsqueda automática por categoría ─────────────────────
    async function buscarCategoria() {
        setBuscandoAuto(true); setResultadosAuto([]);
        try {
            const { data } = await api.post('/prospector/buscar-categoria', {
                categoria: catAuto, ciudad: ciudadAuto, maxResultados: maxAuto
            });
            setResultadosAuto(data.data || []);
            if ((data.data || []).length === 0) alert('No se encontraron negocios. Intenta otra categoría o ciudad.');
        } catch (e) {
            alert('Error: ' + (e.response?.data?.error || e.message));
        }
        setBuscandoAuto(false);
    }

    async function guardarConfig() {
        setGuardandoConfig(true);
        await api.put('/prospector/config', config).catch(() => {});
        setGuardandoConfig(false);
        alert('Configuración guardada');
    }

    async function ejecutarAhora() {
        if (!confirm('¿Iniciar búsqueda automática ahora? Usará la categoría configurada como "siguiente".')) return;
        await api.post('/prospector/ejecutar');
        alert('Búsqueda iniciada en segundo plano. Revisa el historial en unos minutos.');
    }

    async function cargarHistorial() {
        setCargandoHist(true);
        try {
            const { data } = await api.get('/prospector/historial');
            setHistorial(data.data || []);
        } catch {}
        setCargandoHist(false);
    }

    useEffect(() => {
        if (tab === 'historial') cargarHistorial();
    }, [tab]);

    function toggleCat(cat) {
        setConfig(c => ({
            ...c,
            categorias: c.categorias.includes(cat) ? c.categorias.filter(x => x !== cat) : [...c.categorias, cat]
        }));
    }
    function toggleCiudad(city) {
        setConfig(c => ({
            ...c,
            ciudades: c.ciudades.includes(city) ? c.ciudades.filter(x => x !== city) : [...c.ciudades, city]
        }));
    }

    // ─────────────────────────────────────────────────────────
    return (
        <div style={{ padding: 28, background: '#f8fafc', minHeight: '100vh' }}>

            <div style={{ marginBottom: 24 }}>
                <h1 style={{ margin: '0 0 4px', fontSize: '1.5rem', fontWeight: 700, color: '#1e1b4b' }}>Generador de Propuestas</h1>
                <p style={{ margin: 0, color: '#64748b', fontSize: '.9rem' }}>Investiga negocios con Google Maps (sin API de pago) y genera propuestas personalizadas</p>
            </div>

            {/* Tabs */}
            <div style={{ display: 'flex', gap: 4, marginBottom: 20 }}>
                {[
                    ['manual',   '🔍 Búsqueda individual'],
                    ['auto',     '⚡ Búsqueda automática'],
                    ['historial','📋 Historial'],
                ].map(([k, l]) => (
                    <button key={k} onClick={() => setTab(k)} style={{ padding: '8px 18px', borderRadius: 8, border: 'none', cursor: 'pointer', fontWeight: 600, fontSize: '.88rem', background: tab === k ? '#6366f1' : '#e2e8f0', color: tab === k ? '#fff' : '#475569' }}>
                        {l}
                    </button>
                ))}
            </div>

            {/* ══════════════ TAB MANUAL ══════════════ */}
            {tab === 'manual' && (
                <>
                    <div style={card}>
                        <form onSubmit={investigar}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
                                <div>
                                    <label style={lbl}>Nombre del negocio *</label>
                                    <input value={form.nombre} onChange={e => setField('nombre', e.target.value)} placeholder="Ej: Restaurante El Fogón" style={inp} required />
                                </div>
                                <div>
                                    <label style={lbl}>Ciudad *</label>
                                    <input value={form.ciudad} onChange={e => setField('ciudad', e.target.value)} placeholder="Ej: Medellín" style={inp} required />
                                </div>
                            </div>

                            <div style={{ marginBottom: 12 }}>
                                <label style={lbl}>Tipo de negocio</label>
                                <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6, marginBottom: 8 }}>
                                    {TIPOS_SUGERIDOS.map(t => (
                                        <button key={t} type="button" onClick={() => setField('tipo', form.tipo === t ? '' : t)}
                                            style={{ padding: '3px 10px', borderRadius: 12, border: `1px solid ${form.tipo === t ? '#6366f1' : '#e2e8f0'}`, background: form.tipo === t ? '#6366f1' : '#fff', color: form.tipo === t ? '#fff' : '#475569', cursor: 'pointer', fontSize: '.78rem', fontWeight: form.tipo === t ? 600 : 400 }}>
                                            {t}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            <div style={{ marginBottom: 20 }}>
                                <label style={lbl}>URL del sitio web (opcional)</label>
                                <input value={form.urlDirecta} onChange={e => setField('urlDirecta', e.target.value)} placeholder="https://..." style={{ ...inp, maxWidth: 480 }} />
                            </div>

                            <button type="submit" disabled={cargando} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '11px 32px', fontWeight: 700, cursor: 'pointer', fontSize: '.95rem', opacity: cargando ? .7 : 1 }}>
                                {cargando ? '⟳ Investigando...' : '🔍 Investigar y generar propuesta'}
                            </button>
                        </form>
                    </div>

                    {/* Progreso */}
                    {cargando && (
                        <div style={{ ...card, background: '#1e1b4b', color: '#fff' }}>
                            <div style={{ display: 'flex', gap: 16, alignItems: 'center', flexWrap: 'wrap' }}>
                                {PASOS.map((p, i) => (
                                    <div key={i} style={{ display: 'flex', alignItems: 'center', gap: 8, opacity: paso >= i + 1 ? 1 : .3 }}>
                                        <div style={{ width: 26, height: 26, borderRadius: '50%', background: paso > i + 1 ? '#10b981' : paso === i + 1 ? '#6366f1' : '#3a3a6a', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 12, fontWeight: 700, flexShrink: 0, color: '#fff' }}>
                                            {paso > i + 1 ? '✓' : i + 1}
                                        </div>
                                        <span style={{ fontSize: '.83rem' }}>{p}</span>
                                        {i < 2 && <span style={{ color: '#4a4a8a' }}>→</span>}
                                    </div>
                                ))}
                            </div>
                            <p style={{ marginTop: 12, color: '#9090c0', fontSize: '.8rem' }}>Tarda entre 30 y 90 segundos. No cierres la ventana.</p>
                        </div>
                    )}

                    {/* Resultado */}
                    {resultado && !cargando && (
                        <>
                            <TarjetaPropuesta r={resultado} onEmail={setEmailTarget} />

                            {resultado.analisis && (
                                <div style={{ ...card, marginBottom: 10 }}>
                                    <h3 style={{ margin: '0 0 10px', fontSize: '.95rem', color: '#1e1b4b' }}>Presencia digital</h3>
                                    <div style={{ display: 'flex', gap: 16, flexWrap: 'wrap' }}>
                                        <div>
                                            <p style={{ fontSize: '.75rem', color: '#16a34a', fontWeight: 700, marginBottom: 5 }}>✓ Tiene</p>
                                            <div style={{ display: 'flex', flexWrap: 'wrap', gap: 4 }}>
                                                {resultado.analisis.tiene.map(t => <span key={t} style={{ background: '#f0fdf4', border: '1px solid #86efac', borderRadius: 10, padding: '2px 8px', fontSize: '.75rem', color: '#15803d' }}>{t}</span>)}
                                            </div>
                                        </div>
                                        <div>
                                            <p style={{ fontSize: '.75rem', color: '#dc2626', fontWeight: 700, marginBottom: 5 }}>✗ Le falta</p>
                                            <div style={{ display: 'flex', flexWrap: 'wrap', gap: 4 }}>
                                                {resultado.analisis.falta.map(f => <span key={f} style={{ background: '#fef2f2', border: '1px solid #fca5a5', borderRadius: 10, padding: '2px 8px', fontSize: '.75rem', color: '#dc2626' }}>{f}</span>)}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

                            <button onClick={() => { setResultado(null); setForm({ nombre: '', ciudad: '', tipo: '', urlDirecta: '' }); }} style={{ background: '#e2e8f0', color: '#475569', border: 'none', borderRadius: 8, padding: '9px 20px', fontWeight: 600, cursor: 'pointer', fontSize: '.85rem', marginBottom: 16 }}>
                                + Nueva búsqueda
                            </button>
                        </>
                    )}
                </>
            )}

            {/* ══════════════ TAB AUTO ══════════════ */}
            {tab === 'auto' && (
                <>
                    {/* Búsqueda manual por categoría */}
                    <div style={card}>
                        <h3 style={{ margin: '0 0 4px', fontSize: '1rem', color: '#1e1b4b' }}>Buscar ahora por categoría</h3>
                        <p style={{ margin: '0 0 16px', fontSize: '.83rem', color: '#64748b' }}>El sistema abre Google Maps, extrae varios negocios de esa categoría y genera una propuesta para cada uno.</p>

                        <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap', alignItems: 'flex-end', marginBottom: 16 }}>
                            <div>
                                <label style={lbl}>Categoría</label>
                                <select value={catAuto} onChange={e => setCatAuto(e.target.value)} style={{ ...inp, width: 'auto' }}>
                                    {CATEGORIAS_AUTO.map(c => <option key={c}>{c}</option>)}
                                </select>
                            </div>
                            <div>
                                <label style={lbl}>Ciudad</label>
                                <select value={ciudadAuto} onChange={e => setCiudadAuto(e.target.value)} style={{ ...inp, width: 'auto' }}>
                                    {CIUDADES_CO.map(c => <option key={c}>{c}</option>)}
                                </select>
                            </div>
                            <div>
                                <label style={lbl}>Cantidad (máx 10)</label>
                                <input type="number" min={1} max={10} value={maxAuto} onChange={e => setMaxAuto(+e.target.value)} style={{ ...inp, width: 70 }} />
                            </div>
                            <button onClick={buscarCategoria} disabled={buscandoAuto} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '10px 24px', fontWeight: 700, cursor: 'pointer', opacity: buscandoAuto ? .7 : 1 }}>
                                {buscandoAuto ? '⟳ Buscando...' : '🔍 Buscar'}
                            </button>
                        </div>

                        {buscandoAuto && (
                            <div style={{ background: '#f1f5f9', borderRadius: 8, padding: 14, fontSize: '.83rem', color: '#475569' }}>
                                ⟳ Navegando Google Maps para "<strong>{catAuto}</strong>" en <strong>{ciudadAuto}</strong>... Esto tarda varios minutos.
                            </div>
                        )}

                        {resultadosAuto.length > 0 && (
                            <div style={{ marginTop: 16 }}>
                                <p style={{ margin: '0 0 10px', fontSize: '.83rem', color: '#16a34a', fontWeight: 700 }}>✓ {resultadosAuto.length} propuestas generadas</p>
                                {resultadosAuto.map(r => <TarjetaPropuesta key={r.key} r={r} onEmail={setEmailTarget} />)}
                            </div>
                        )}
                    </div>

                    {/* Scheduler automático */}
                    <div style={card}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
                            <div>
                                <h3 style={{ margin: '0 0 4px', fontSize: '1rem', color: '#1e1b4b' }}>Búsqueda automática diaria</h3>
                                <p style={{ margin: 0, fontSize: '.83rem', color: '#64748b' }}>Cada día busca una categoría en una ciudad (rotando) y guarda las propuestas en el historial.</p>
                            </div>
                            <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
                                <button onClick={ejecutarAhora} style={{ background: '#e2e8f0', color: '#475569', border: 'none', borderRadius: 7, padding: '8px 14px', fontSize: '.8rem', fontWeight: 600, cursor: 'pointer' }}>
                                    ▶ Ejecutar ahora
                                </button>
                                <button onClick={() => { const n = { ...config, activo: !config.activo }; setConfig(n); api.put('/prospector/config', n); }}
                                    style={{ padding: '8px 16px', borderRadius: 8, border: 'none', cursor: 'pointer', fontWeight: 700, background: config.activo ? '#dcfce7' : '#fee2e2', color: config.activo ? '#16a34a' : '#dc2626' }}>
                                    {config.activo ? '● ACTIVO' : '○ INACTIVO'}
                                </button>
                            </div>
                        </div>

                        {config.siguiente && (
                            <div style={{ background: '#f8fafc', border: '1px solid #e2e8f0', borderRadius: 8, padding: 10, marginBottom: 16, fontSize: '.83rem', color: '#475569' }}>
                                Próxima búsqueda: <strong>{config.siguiente}</strong> a las {config.hora}:00
                            </div>
                        )}

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20 }}>
                            <div>
                                <label style={lbl}>Categorías a rotar</label>
                                <div style={{ display: 'flex', flexWrap: 'wrap', gap: 5, maxHeight: 180, overflowY: 'auto' }}>
                                    {CATEGORIAS_AUTO.map(c => (
                                        <button key={c} type="button" onClick={() => toggleCat(c)}
                                            style={{ padding: '3px 10px', borderRadius: 12, border: `1px solid ${config.categorias?.includes(c) ? '#6366f1' : '#e2e8f0'}`, background: config.categorias?.includes(c) ? '#6366f1' : '#fff', color: config.categorias?.includes(c) ? '#fff' : '#475569', cursor: 'pointer', fontSize: '.78rem', fontWeight: config.categorias?.includes(c) ? 600 : 400 }}>
                                            {c}
                                        </button>
                                    ))}
                                </div>
                            </div>
                            <div>
                                <label style={lbl}>Ciudades a rotar</label>
                                <div style={{ display: 'flex', flexWrap: 'wrap', gap: 5, maxHeight: 180, overflowY: 'auto' }}>
                                    {CIUDADES_CO.map(c => (
                                        <button key={c} type="button" onClick={() => toggleCiudad(c)}
                                            style={{ padding: '3px 10px', borderRadius: 12, border: `1px solid ${config.ciudades?.includes(c) ? '#10b981' : '#e2e8f0'}`, background: config.ciudades?.includes(c) ? '#10b981' : '#fff', color: config.ciudades?.includes(c) ? '#fff' : '#475569', cursor: 'pointer', fontSize: '.78rem', fontWeight: config.ciudades?.includes(c) ? 600 : 400 }}>
                                            {c}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </div>

                        <div style={{ display: 'flex', gap: 20, marginTop: 16, flexWrap: 'wrap', alignItems: 'flex-end' }}>
                            <div>
                                <label style={lbl}>Hora de ejecución</label>
                                <input type="number" min={0} max={23} value={config.hora || 8} onChange={e => setConfig(c => ({ ...c, hora: +e.target.value }))} style={{ ...inp, width: 80 }} />
                            </div>
                            <div>
                                <label style={lbl}>Máx. negocios por día</label>
                                <input type="number" min={1} max={10} value={config.maxPorBusqueda || 5} onChange={e => setConfig(c => ({ ...c, maxPorBusqueda: +e.target.value }))} style={{ ...inp, width: 80 }} />
                            </div>
                            <button onClick={guardarConfig} disabled={guardandoConfig} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 20px', fontWeight: 600, cursor: 'pointer' }}>
                                {guardandoConfig ? 'Guardando...' : 'Guardar configuración'}
                            </button>
                        </div>
                    </div>
                </>
            )}

            {/* ══════════════ TAB HISTORIAL ══════════════ */}
            {tab === 'historial' && (
                <div style={card}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 12 }}>
                        <h3 style={{ margin: 0, color: '#1e1b4b' }}>Historial de propuestas</h3>
                        <button onClick={cargarHistorial} disabled={cargandoHist} style={{ background: '#e2e8f0', border: 'none', borderRadius: 6, padding: '6px 14px', cursor: 'pointer', fontSize: '.8rem', fontWeight: 600, color: '#475569' }}>
                            ↻ Actualizar
                        </button>
                    </div>

                    {cargandoHist && <p style={{ color: '#94a3b8', fontSize: '.85rem' }}>Cargando...</p>}
                    {!cargandoHist && historial.length === 0 && (
                        <p style={{ color: '#94a3b8', fontSize: '.85rem' }}>Ninguna propuesta en esta sesión. Genera una desde las otras pestañas.</p>
                    )}
                    {historial.map(h => <TarjetaPropuesta key={h.key} r={h} onEmail={setEmailTarget} />)}
                </div>
            )}

            {/* Modal email */}
            <ModalEmail propuesta={emailTarget} onClose={() => setEmailTarget(null)} />
        </div>
    );
}

const card = { background: '#fff', borderRadius: 10, padding: 22, boxShadow: '0 1px 4px rgba(0,0,0,.08)', marginBottom: 16 };
const lbl  = { display: 'block', fontSize: '.78rem', color: '#64748b', marginBottom: 5, fontWeight: 600 };
const inp  = { padding: '9px 12px', border: '1px solid #e2e8f0', borderRadius: 7, fontSize: '.88rem', width: '100%', boxSizing: 'border-box' };
