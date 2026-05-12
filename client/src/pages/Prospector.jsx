import { useEffect, useState } from 'react';
import api from '../api/axios';

const CATEGORIAS_SUGERIDAS = [
    'restaurantes', 'ferreterías', 'clínicas', 'tiendas de ropa', 'constructoras',
    'talleres mecánicos', 'hoteles', 'colegios', 'farmacias', 'consultorios médicos',
    'peluquerías', 'gimnasios', 'supermercados', 'inmobiliarias', 'talleres eléctricos',
    'veterinarias', 'odontólogos', 'abogados', 'contadores', 'agencias de viajes',
    'distribuidoras', 'empresas de transporte', 'servicios de limpieza', 'panaderías',
];

const CIUDADES_COLOMBIA = [
    'Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Bucaramanga',
    'Cartagena', 'Cúcuta', 'Pereira', 'Manizales', 'Santa Marta',
    'Ibagué', 'Villavicencio', 'Pasto', 'Montería', 'Armenia',
];

export default function Prospector() {
    const [keys, setKeys]           = useState({ google_places: false, apollo: false });
    const [config, setConfig]       = useState({ activo: false, categorias: [], ciudades: [], fuentes: ['google_places'], maxPorBusqueda: 10 });
    const [buscando, setBuscando]   = useState(false);
    const [resultado, setResultado] = useState(null);
    const [guardando, setGuardando] = useState(false);
    const [tab, setTab]             = useState('buscar'); // buscar | auto

    // Form búsqueda manual
    const [catInput, setCatInput]   = useState('');
    const [selCats, setSelCats]     = useState([]);
    const [selCiudades, setSelCiudades] = useState(['Bogotá']);
    const [fuentes, setFuentes]     = useState(['google_places']);
    const [maxRes, setMaxRes]       = useState(10);

    useEffect(() => {
        api.get('/prospector/keys').then(r => setKeys(r.data)).catch(() => {});
        api.get('/prospector/config').then(r => setConfig(r.data)).catch(() => {});
    }, []);

    async function buscarAhora() {
        if (!selCats.length) return alert('Agrega al menos una categoría');
        if (!selCiudades.length) return alert('Selecciona al menos una ciudad');
        setBuscando(true); setResultado(null);
        try {
            const { data } = await api.post('/prospector/buscar', { categorias: selCats, ciudades: selCiudades, fuentes, maxPorBusqueda: maxRes });
            setResultado(data);
        } catch (e) { alert('Error: ' + (e.response?.data?.error || e.message)); }
        setBuscando(false);
    }

    async function guardarAutoConfig() {
        setGuardando(true);
        await api.put('/prospector/config', config);
        setGuardando(false);
        alert('Configuración guardada');
    }

    async function ejecutarAhora() {
        if (!confirm('¿Ejecutar una búsqueda automática ahora?')) return;
        await api.post('/prospector/ejecutar');
        alert('Prospección iniciada. Revisa Leads en unos minutos.');
    }

    function toggleCat(cat) {
        setSelCats(c => c.includes(cat) ? c.filter(x => x !== cat) : [...c, cat]);
    }
    function toggleCiudad(c) {
        setSelCiudades(cs => cs.includes(c) ? cs.filter(x => x !== c) : [...cs, c]);
    }
    function toggleFuente(f) {
        setFuentes(fs => fs.includes(f) ? fs.filter(x => x !== f) : [...fs, f]);
    }
    function agregarCatPersonalizada() {
        const v = catInput.trim().toLowerCase();
        if (v && !selCats.includes(v)) setSelCats(c => [...c, v]);
        setCatInput('');
    }

    const s = { card: { background: '#fff', borderRadius: 10, padding: 22, boxShadow: '0 1px 4px rgba(0,0,0,.08)', marginBottom: 16 } };

    return (
        <div style={{ padding: 28, background: '#f8fafc', minHeight: '100vh' }}>
            {/* Header */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
                <div>
                    <h1 style={{ margin: 0, fontSize: '1.5rem', fontWeight: 700, color: '#1e1b4b' }}>Prospector Autónomo</h1>
                    <p style={{ margin: '4px 0 0', color: '#64748b', fontSize: '.9rem' }}>Encuentra clientes potenciales automáticamente en Google Maps y bases de datos B2B</p>
                </div>
                <button onClick={ejecutarAhora} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '10px 18px', fontWeight: 600, cursor: 'pointer', fontSize: '.88rem' }}>
                    ▶ Ejecutar ahora
                </button>
            </div>

            {/* Estado de API Keys */}
            <div style={{ ...s.card, display: 'flex', gap: 16, flexWrap: 'wrap', padding: 16 }}>
                <div style={{ fontSize: '.82rem', fontWeight: 600, color: '#475569' }}>API Keys:</div>
                <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap' }}>
                    {[
                        { key: 'google_places', label: 'Google Places', instruccion: 'Agrega GOOGLE_PLACES_API_KEY en Railway' },
                        { key: 'apollo',        label: 'Apollo.io B2B',  instruccion: 'Agrega APOLLO_API_KEY en Railway' },
                    ].map(({ key, label, instruccion }) => (
                        <div key={key} style={{ display: 'flex', alignItems: 'center', gap: 6, background: keys[key] ? '#f0fdf4' : '#fef2f2', border: `1px solid ${keys[key] ? '#86efac' : '#fca5a5'}`, borderRadius: 6, padding: '4px 10px', fontSize: '.8rem' }}>
                            <span style={{ color: keys[key] ? '#16a34a' : '#dc2626', fontWeight: 700 }}>{keys[key] ? '✓' : '✗'}</span>
                            <span style={{ color: keys[key] ? '#15803d' : '#dc2626', fontWeight: 600 }}>{label}</span>
                            {!keys[key] && <span style={{ color: '#94a3b8', fontSize: '.72rem' }}>— {instruccion}</span>}
                        </div>
                    ))}
                </div>
            </div>

            {/* Tabs */}
            <div style={{ display: 'flex', gap: 4, marginBottom: 16 }}>
                {[['buscar','🔍 Búsqueda manual'], ['auto','⚙ Búsqueda automática diaria']].map(([k, l]) => (
                    <button key={k} onClick={() => setTab(k)} style={{ padding: '8px 18px', borderRadius: 8, border: 'none', cursor: 'pointer', fontWeight: 600, fontSize: '.88rem', background: tab === k ? '#6366f1' : '#e2e8f0', color: tab === k ? '#fff' : '#475569' }}>{l}</button>
                ))}
            </div>

            {/* BUSQUEDA MANUAL */}
            {tab === 'buscar' && (
                <div style={s.card}>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20 }}>
                        {/* Categorías */}
                        <div>
                            <label style={lbl}>Tipos de negocio a buscar</label>
                            <div style={{ display: 'flex', gap: 6, marginBottom: 8 }}>
                                <input value={catInput} onChange={e => setCatInput(e.target.value)} onKeyDown={e => e.key === 'Enter' && agregarCatPersonalizada()} placeholder="Escribe una categoría y presiona Enter" style={{ ...inp, flex: 1 }} />
                                <button onClick={agregarCatPersonalizada} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 6, padding: '0 14px', cursor: 'pointer', fontWeight: 600 }}>+</button>
                            </div>
                            <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6, maxHeight: 200, overflowY: 'auto' }}>
                                {CATEGORIAS_SUGERIDAS.map(cat => (
                                    <button key={cat} onClick={() => toggleCat(cat)} style={{ padding: '3px 10px', borderRadius: 12, fontSize: '.78rem', border: `1px solid ${selCats.includes(cat) ? '#6366f1' : '#e2e8f0'}`, background: selCats.includes(cat) ? '#6366f1' : '#fff', color: selCats.includes(cat) ? '#fff' : '#475569', cursor: 'pointer', fontWeight: selCats.includes(cat) ? 600 : 400 }}>
                                        {cat}
                                    </button>
                                ))}
                            </div>
                            {selCats.length > 0 && (
                                <div style={{ marginTop: 10, background: '#f1f5f9', borderRadius: 6, padding: '6px 10px', fontSize: '.8rem', color: '#475569' }}>
                                    Seleccionadas: <strong>{selCats.join(', ')}</strong>
                                    <button onClick={() => setSelCats([])} style={{ marginLeft: 8, color: '#ef4444', background: 'none', border: 'none', cursor: 'pointer', fontSize: '.78rem' }}>Limpiar</button>
                                </div>
                            )}
                        </div>

                        {/* Ciudades */}
                        <div>
                            <label style={lbl}>Ciudades de Colombia</label>
                            <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6, maxHeight: 240, overflowY: 'auto' }}>
                                {CIUDADES_COLOMBIA.map(c => (
                                    <button key={c} onClick={() => toggleCiudad(c)} style={{ padding: '3px 10px', borderRadius: 12, fontSize: '.78rem', border: `1px solid ${selCiudades.includes(c) ? '#10b981' : '#e2e8f0'}`, background: selCiudades.includes(c) ? '#10b981' : '#fff', color: selCiudades.includes(c) ? '#fff' : '#475569', cursor: 'pointer', fontWeight: selCiudades.includes(c) ? 600 : 400 }}>
                                        {c}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Fuentes y cantidad */}
                    <div style={{ display: 'flex', gap: 20, marginTop: 16, alignItems: 'flex-end', flexWrap: 'wrap' }}>
                        <div>
                            <label style={lbl}>Fuentes de datos</label>
                            <div style={{ display: 'flex', gap: 8 }}>
                                {[['google_places','🗺 Google Maps'], ['apollo','🏢 Apollo B2B']].map(([f, l]) => (
                                    <button key={f} onClick={() => toggleFuente(f)} disabled={f === 'apollo' && !keys.apollo} style={{ padding: '6px 14px', borderRadius: 8, fontSize: '.82rem', border: `1px solid ${fuentes.includes(f) ? '#6366f1' : '#e2e8f0'}`, background: fuentes.includes(f) ? '#eef2ff' : '#fff', color: fuentes.includes(f) ? '#6366f1' : '#94a3b8', cursor: (f === 'apollo' && !keys.apollo) ? 'not-allowed' : 'pointer', fontWeight: 600, opacity: (f === 'apollo' && !keys.apollo) ? .5 : 1 }}>
                                        {l}{f === 'apollo' && !keys.apollo ? ' (requiere key)' : ''}
                                    </button>
                                ))}
                            </div>
                        </div>
                        <div>
                            <label style={lbl}>Máx. resultados por búsqueda</label>
                            <input type="number" min={1} max={50} value={maxRes} onChange={e => setMaxRes(+e.target.value)} style={{ ...inp, width: 80 }} />
                        </div>
                        <button onClick={buscarAhora} disabled={buscando || !keys.google_places && !keys.apollo} style={{ padding: '10px 28px', background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, fontWeight: 700, cursor: 'pointer', opacity: buscando ? .7 : 1, fontSize: '.9rem' }}>
                            {buscando ? '⟳ Buscando...' : '🔍 Buscar ahora'}
                        </button>
                        {!keys.google_places && !keys.apollo && (
                            <div style={{ fontSize: '.8rem', color: '#ef4444', fontWeight: 600 }}>⚠ Configura al menos una API key en Railway</div>
                        )}
                    </div>

                    {/* Resultado */}
                    {resultado && (
                        <div style={{ marginTop: 20, background: resultado.guardados > 0 ? '#f0fdf4' : '#fef2f2', border: `1px solid ${resultado.guardados > 0 ? '#86efac' : '#fca5a5'}`, borderRadius: 8, padding: 16 }}>
                            <div style={{ display: 'flex', gap: 24, flexWrap: 'wrap', marginBottom: 10 }}>
                                <div><span style={{ fontSize: '1.6rem', fontWeight: 800, color: '#6366f1' }}>{resultado.total}</span><br /><span style={{ fontSize: '.78rem', color: '#64748b' }}>Encontrados</span></div>
                                <div><span style={{ fontSize: '1.6rem', fontWeight: 800, color: '#16a34a' }}>{resultado.guardados}</span><br /><span style={{ fontSize: '.78rem', color: '#64748b' }}>Leads nuevos</span></div>
                                <div><span style={{ fontSize: '1.6rem', fontWeight: 800, color: '#f59e0b' }}>{resultado.duplicados}</span><br /><span style={{ fontSize: '.78rem', color: '#64748b' }}>Ya existían</span></div>
                            </div>
                            {resultado.guardados > 0 && <div style={{ fontSize: '.85rem', color: '#16a34a', fontWeight: 600 }}>✓ Los leads nuevos ya están en el pipeline. Cristian los contactará automáticamente.</div>}
                            {resultado.errores?.length > 0 && <div style={{ marginTop: 8, fontSize: '.78rem', color: '#ef4444' }}>{resultado.errores.join(' | ')}</div>}
                            {resultado.detalle?.length > 0 && (
                                <div style={{ marginTop: 10, fontSize: '.78rem', color: '#475569' }}>
                                    {resultado.detalle.map((d, i) => <div key={i}>• {d.fuente}: "{d.categoria}" en {d.ciudad} → {d.encontrados} encontrados, {d.guardados} guardados</div>)}
                                </div>
                            )}
                        </div>
                    )}
                </div>
            )}

            {/* BÚSQUEDA AUTOMÁTICA */}
            {tab === 'auto' && (
                <div style={s.card}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
                        <div>
                            <h3 style={{ margin: 0, fontSize: '1rem', color: '#1e1b4b' }}>Prospección autónoma diaria</h3>
                            <p style={{ margin: '4px 0 0', fontSize: '.83rem', color: '#64748b' }}>Cada día a las 8am el sistema busca una categoría en una ciudad diferente (rotando) y agrega los leads automáticamente.</p>
                        </div>
                        <button onClick={() => { const n = { ...config, activo: !config.activo }; setConfig(n); api.put('/prospector/config', n); }} style={{ padding: '10px 20px', borderRadius: 8, border: 'none', cursor: 'pointer', fontWeight: 700, background: config.activo ? '#dcfce7' : '#fee2e2', color: config.activo ? '#16a34a' : '#dc2626' }}>
                            {config.activo ? '● ACTIVO' : '○ INACTIVO'}
                        </button>
                    </div>

                    <div style={{ background: config.activo ? '#f0fdf4' : '#f8fafc', border: `1px solid ${config.activo ? '#86efac' : '#e2e8f0'}`, borderRadius: 8, padding: 14, marginBottom: 20, fontSize: '.85rem', color: config.activo ? '#15803d' : '#64748b' }}>
                        {config.activo
                            ? '✓ El prospector está activo. Cada día buscará negocios nuevos y los agregará como leads para que Cristian los contacte.'
                            : 'Activa el prospector para que encuentre clientes nuevos automáticamente cada día.'}
                    </div>

                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20, marginBottom: 16 }}>
                        <div>
                            <label style={lbl}>Máx. leads por búsqueda diaria</label>
                            <input type="number" min={1} max={50} value={config.maxPorBusqueda || 10} onChange={e => setConfig(c => ({ ...c, maxPorBusqueda: +e.target.value }))} style={{ ...inp, width: 100 }} />
                        </div>
                        <div>
                            <label style={lbl}>Fuente</label>
                            <div style={{ display: 'flex', gap: 8, marginTop: 4 }}>
                                {[['google_places','🗺 Google Maps'], ['apollo','🏢 Apollo B2B']].map(([f, l]) => {
                                    const activa = (config.fuentes || []).includes(f);
                                    return (
                                        <button key={f} onClick={() => {
                                            const fs = config.fuentes || [];
                                            setConfig(c => ({ ...c, fuentes: activa ? fs.filter(x => x !== f) : [...fs, f] }));
                                        }} disabled={f === 'apollo' && !keys.apollo} style={{ padding: '5px 12px', borderRadius: 7, fontSize: '.8rem', border: `1px solid ${activa ? '#6366f1' : '#e2e8f0'}`, background: activa ? '#eef2ff' : '#fff', color: activa ? '#6366f1' : '#94a3b8', cursor: 'pointer', fontWeight: 600, opacity: f === 'apollo' && !keys.apollo ? .5 : 1 }}>
                                            {l}
                                        </button>
                                    );
                                })}
                            </div>
                        </div>
                    </div>

                    <div style={{ background: '#f1f5f9', borderRadius: 8, padding: 14, fontSize: '.82rem', color: '#475569', marginBottom: 20 }}>
                        <strong>Flujo completo:</strong><br />
                        🗺 Prospector encuentra negocios → 📋 Se agregan como leads → 🤖 Cristian les escribe por WhatsApp → 📅 Reunión en el Calendario
                    </div>

                    <button onClick={guardarAutoConfig} disabled={guardando} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '10px 24px', fontWeight: 600, cursor: 'pointer' }}>
                        {guardando ? 'Guardando...' : 'Guardar configuración'}
                    </button>
                </div>
            )}
        </div>
    );
}

const lbl = { display: 'block', fontSize: '.8rem', color: '#64748b', marginBottom: 6, fontWeight: 600 };
const inp = { padding: '8px 12px', border: '1px solid #e2e8f0', borderRadius: 7, fontSize: '.88rem', boxSizing: 'border-box' };
