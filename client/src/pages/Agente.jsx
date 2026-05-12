import { useEffect, useState } from 'react';
import api from '../api/axios';

const TIPOS_COLOR = {
    mensaje_enviado:    '#6366f1',
    respuesta_recibida: '#10b981',
    reunion_propuesta:  '#8b5cf6',
    reunion_confirmada: '#16a34a',
    seguimiento:        '#f59e0b',
    decision_agente:    '#3b82f6',
    error:              '#ef4444',
};

const configVacia = {
    activo: false, nombre_agente: 'Cristian', nombre_empresa: 'AI Company',
    descripcion_saas: 'AI Company es una empresa de desarrollo de software a medida. Creamos sistemas personalizados para negocios, automatizaciones de procesos y somos agencia de marketing digital.',
    dias_seguimiento_1: 2, dias_seguimiento_2: 5, max_intentos: 3,
    horario_inicio: 8, horario_fin: 18,
    gmail_user: '', gmail_app_password: '',
};

export default function Agente() {
    const [config, setConfig]         = useState(configVacia);
    const [actividad, setActividad]   = useState([]);
    const [guardando, setGuardando]   = useState(false);
    const [ejecutando, setEjecutando] = useState(false);
    const [tab, setTab]               = useState('config'); // 'config' | 'actividad'
    const [guardadoOk, setGuardadoOk] = useState(false);

    async function cargar() {
        const [r1, r2] = await Promise.all([
            api.get('/agente/config'),
            api.get('/agente/actividad')
        ]);
        setConfig(r1.data);
        setActividad(r2.data);
    }

    useEffect(() => { cargar(); }, []);

    async function guardarConfig() {
        setGuardando(true);
        await api.put('/agente/config', config);
        setGuardando(false);
        setGuardadoOk(true);
        setTimeout(() => setGuardadoOk(false), 2500);
    }

    async function ejecutarAhora() {
        setEjecutando(true);
        setTab('actividad');
        await api.post('/agente/ejecutar');
        // Poll cada 5s hasta ver actividad o máx 60s
        let intentos = 0;
        const poll = setInterval(async () => {
            intentos++;
            await cargar();
            if (intentos >= 12) { clearInterval(poll); setEjecutando(false); }
        }, 5000);
    }

    const s = { card: { background: '#fff', borderRadius: 10, padding: 24, boxShadow: '0 1px 4px rgba(0,0,0,.08)', marginBottom: 16 } };

    return (
        <div style={{ padding: 28, background: '#f8fafc', minHeight: '100vh' }}>
            {/* Header */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
                <div>
                    <h1 style={{ margin: 0, fontSize: '1.5rem', fontWeight: 700, color: '#1e1b4b' }}>Agente de Ventas</h1>
                    <p style={{ margin: '4px 0 0', color: '#64748b', fontSize: '.9rem' }}>IA autónoma que contacta leads y agenda reuniones</p>
                </div>
                <div style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                    {/* Toggle ON/OFF */}
                    <button
                        onClick={() => { const nuevo = { ...config, activo: !config.activo }; setConfig(nuevo); api.put('/agente/config', nuevo); }}
                        style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '10px 20px', borderRadius: 8, border: 'none', cursor: 'pointer', fontWeight: 700, fontSize: '.9rem', background: config.activo ? '#dcfce7' : '#fee2e2', color: config.activo ? '#16a34a' : '#dc2626' }}
                    >
                        <span style={{ fontSize: '1.1rem' }}>{config.activo ? '●' : '○'}</span>
                        {config.activo ? 'Agente ACTIVO' : 'Agente INACTIVO'}
                    </button>
                    <button
                        onClick={ejecutarAhora}
                        disabled={ejecutando}
                        style={{ padding: '10px 18px', borderRadius: 8, border: 'none', background: '#6366f1', color: '#fff', fontWeight: 600, cursor: ejecutando ? 'not-allowed' : 'pointer', opacity: ejecutando ? .7 : 1 }}
                    >
                        {ejecutando ? '⟳ Ejecutando...' : '▶ Ejecutar ahora'}
                    </button>
                </div>
            </div>

            {/* Status banner */}
            <div style={{ ...s.card, background: config.activo ? '#f0fdf4' : '#fef2f2', border: `1px solid ${config.activo ? '#86efac' : '#fca5a5'}`, marginBottom: 20 }}>
                <div style={{ display: 'flex', gap: 16, flexWrap: 'wrap' }}>
                    <div><span style={{ fontSize: '.8rem', color: '#64748b' }}>Estado</span><br /><strong style={{ color: config.activo ? '#16a34a' : '#dc2626' }}>{config.activo ? 'Funcionando — revisa leads cada hora' : 'Detenido — actívalo para empezar'}</strong></div>
                    <div><span style={{ fontSize: '.8rem', color: '#64748b' }}>Horario</span><br /><strong>{config.horario_inicio}:00 — {config.horario_fin}:00</strong></div>
                    <div><span style={{ fontSize: '.8rem', color: '#64748b' }}>Seguimiento 1</span><br /><strong>Día {config.dias_seguimiento_1}</strong></div>
                    <div><span style={{ fontSize: '.8rem', color: '#64748b' }}>Seguimiento 2</span><br /><strong>Día {config.dias_seguimiento_2}</strong></div>
                    <div><span style={{ fontSize: '.8rem', color: '#64748b' }}>Máx. intentos</span><br /><strong>{config.max_intentos}</strong></div>
                </div>
            </div>

            {/* Tabs */}
            <div style={{ display: 'flex', gap: 4, marginBottom: 16 }}>
                {[['config','⚙ Configuración'], ['actividad','📋 Actividad reciente']].map(([key, lbl]) => (
                    <button key={key} onClick={() => setTab(key)} style={{ padding: '8px 18px', borderRadius: 8, border: 'none', cursor: 'pointer', fontWeight: 600, fontSize: '.88rem', background: tab === key ? '#6366f1' : '#e2e8f0', color: tab === key ? '#fff' : '#475569' }}>
                        {lbl}
                    </button>
                ))}
            </div>

            {/* CONFIG TAB */}
            {tab === 'config' && (
                <div style={s.card}>
                    <h3 style={{ margin: '0 0 20px', color: '#1e1b4b', fontSize: '1rem' }}>Configuración del agente</h3>

                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                        <div>
                            <label style={lbl}>Nombre del agente</label>
                            <input value={config.nombre_agente} onChange={e => setConfig(c => ({ ...c, nombre_agente: e.target.value }))} style={inp} placeholder="Ej: Carlos de Mi Plataforma" />
                        </div>
                        <div>
                            <label style={lbl}>Nombre de tu empresa</label>
                            <input value={config.nombre_empresa} onChange={e => setConfig(c => ({ ...c, nombre_empresa: e.target.value }))} style={inp} placeholder="Ej: Mi Plataforma SaaS" />
                        </div>
                    </div>

                    <div style={{ marginTop: 14 }}>
                        <label style={lbl}>¿Qué hace tu SaaS? (el agente usa esto para presentar el producto)</label>
                        <textarea value={config.descripcion_saas} onChange={e => setConfig(c => ({ ...c, descripcion_saas: e.target.value }))} style={{ ...inp, height: 90, resize: 'vertical' }} placeholder="Ej: Sistema de gestión de licencias y clientes para negocios. Permite manejar pagos, renovaciones y accesos desde un panel centralizado..." />
                    </div>

                    <div style={{ marginTop: 14, background: '#f0fdf4', border: '1px solid #86efac', borderRadius: 8, padding: '10px 14px', fontSize: '.83rem', color: '#16a34a' }}>
                        Las reuniones se agendan automáticamente en el <strong>Calendario interno</strong> de AI Company — sin necesidad de Calendly.
                    </div>

                    <h4 style={{ margin: '20px 0 12px', color: '#475569', fontSize: '.9rem', fontWeight: 700 }}>Correo electrónico (Gmail)</h4>
                    <div style={{ background: '#fffbeb', border: '1px solid #fde68a', borderRadius: 8, padding: '10px 14px', fontSize: '.82rem', color: '#92400e', marginBottom: 12 }}>
                        Necesitas una <strong>App Password</strong> de Google (no tu contraseña normal).<br/>
                        Ve a <strong>myaccount.google.com → Seguridad → Verificación en 2 pasos → Contraseñas de aplicación</strong> y genera una para "Correo".
                    </div>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                        <div>
                            <label style={lbl}>Correo Gmail</label>
                            <input value={config.gmail_user || ''} onChange={e => setConfig(c => ({ ...c, gmail_user: e.target.value }))} style={inp} placeholder="tucorreo@gmail.com" type="email" />
                        </div>
                        <div>
                            <label style={lbl}>App Password (16 caracteres)</label>
                            <input value={config.gmail_app_password || ''} onChange={e => setConfig(c => ({ ...c, gmail_app_password: e.target.value }))} style={inp} placeholder="xxxx xxxx xxxx xxxx" type="password" />
                        </div>
                    </div>

                    <h4 style={{ margin: '20px 0 12px', color: '#475569', fontSize: '.9rem', fontWeight: 700 }}>Comportamiento del agente</h4>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(160px, 1fr))', gap: 14 }}>
                        {[
                            ['dias_seguimiento_1', 'Días para seguimiento 1', 1, 14],
                            ['dias_seguimiento_2', 'Días para seguimiento 2', 2, 30],
                            ['max_intentos',       'Máximo de intentos',      1, 10],
                            ['horario_inicio',     'Hora inicio (0-23)',       0, 23],
                            ['horario_fin',        'Hora fin (0-23)',          1, 23],
                        ].map(([key, etiqueta, min, max]) => (
                            <div key={key}>
                                <label style={lbl}>{etiqueta}</label>
                                <input type="number" min={min} max={max} value={config[key]} onChange={e => setConfig(c => ({ ...c, [key]: +e.target.value }))} style={inp} />
                            </div>
                        ))}
                    </div>

                    <div style={{ marginTop: 8, background: '#1e1b4b', borderRadius: 8, padding: '14px 18px', fontFamily: 'monospace', fontSize: '.82rem', color: '#a5b4fc' }}>
                        <div style={{ color: '#94a3b8', marginBottom: 6, fontFamily: 'sans-serif', fontSize: '.78rem' }}>Variables de entorno (ya configuradas en Railway):</div>
                        <div>ANTHROPIC_API_KEY <span style={{ color: '#4ade80' }}>✓</span></div>
                        <div>WHATSAPP_TOKEN <span style={{ color: '#4ade80' }}>✓</span></div>
                        <div>WHATSAPP_PHONE_ID <span style={{ color: '#4ade80' }}>✓</span></div>
                        <div>META_VERIFY_TOKEN <span style={{ color: '#4ade80' }}>✓</span></div>
                        <div style={{ marginTop: 8, color: '#94a3b8', fontFamily: 'sans-serif', fontSize: '.75rem' }}>Webhook: POST /api/agente/webhook/whatsapp</div>
                    </div>

                    <div style={{ marginTop: 20, display: 'flex', gap: 10, alignItems: 'center' }}>
                        <button onClick={guardarConfig} disabled={guardando} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '10px 24px', fontWeight: 600, cursor: 'pointer' }}>
                            {guardando ? 'Guardando...' : 'Guardar configuración'}
                        </button>
                        {guardadoOk && <span style={{ color: '#16a34a', fontWeight: 600, fontSize: '.88rem' }}>✓ Guardado</span>}
                    </div>
                </div>
            )}

            {/* ACTIVIDAD TAB */}
            {tab === 'actividad' && (
                <div style={s.card}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
                        <h3 style={{ margin: 0, color: '#1e1b4b', fontSize: '1rem' }}>Últimas 50 acciones del agente</h3>
                        <button onClick={cargar} style={{ background: '#f1f5f9', border: 'none', borderRadius: 6, padding: '6px 14px', cursor: 'pointer', fontSize: '.83rem', color: '#475569' }}>↻ Actualizar</button>
                    </div>
                    {actividad.length === 0 ? (
                        <p style={{ color: '#94a3b8', textAlign: 'center', padding: 32 }}>Sin actividad aún. Activa el agente y agrega leads.</p>
                    ) : (
                        <div>
                            {actividad.map(a => {
                                const color = TIPOS_COLOR[a.tipo] || '#6366f1';
                                return (
                                    <div key={a.id} style={{ display: 'flex', gap: 14, padding: '12px 0', borderBottom: '1px solid #f1f5f9' }}>
                                        <div style={{ width: 8, height: 8, borderRadius: '50%', background: color, marginTop: 6, flexShrink: 0 }} />
                                        <div style={{ flex: 1 }}>
                                            <div style={{ display: 'flex', justifyContent: 'space-between', gap: 8 }}>
                                                <span style={{ fontWeight: 600, color: '#1e293b', fontSize: '.88rem' }}>{a.lead?.nombre || 'Lead eliminado'}</span>
                                                <span style={{ fontSize: '.75rem', color: '#94a3b8', flexShrink: 0 }}>{new Date(a.created_at).toLocaleString('es-CO')}</span>
                                            </div>
                                            <div style={{ fontSize: '.78rem', color: '#64748b', marginTop: 1 }}>{a.lead?.empresa} · <span style={{ color }}>{a.tipo}</span> · {a.canal}</div>
                                            {a.mensaje && <div style={{ marginTop: 5, fontSize: '.85rem', color: '#475569', background: '#f8fafc', borderRadius: 6, padding: '6px 10px' }}>{a.mensaje}</div>}
                                            {a.resultado && <div style={{ marginTop: 4, fontSize: '.83rem', color: '#16a34a', background: '#f0fdf4', borderRadius: 6, padding: '4px 10px' }}>↩ {a.resultado}</div>}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}

const lbl = { display: 'block', fontSize: '.8rem', color: '#64748b', marginBottom: 4, fontWeight: 600 };
const inp = { width: '100%', padding: '8px 12px', border: '1px solid #e2e8f0', borderRadius: 7, fontSize: '.88rem', boxSizing: 'border-box' };
