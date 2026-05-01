import { useEffect, useState, useRef, useMemo } from 'react';
import axios from '../api/axios';
import { MessageCircle, X, RefreshCw, Send } from 'lucide-react';

const ICONS = {
    facebook:  { bg: '#1877f2', label: 'Facebook',  emoji: '🔵' },
    instagram: { bg: '#e1306c', label: 'Instagram', emoji: '📸' },
    whatsapp:  { bg: '#25d366', label: 'WhatsApp',  emoji: '🟢' },
    tiktok:    { bg: '#010101', label: 'TikTok',    emoji: '🎵' }
};

function timeAgo(fecha) {
    const diff = (Date.now() - new Date(fecha)) / 1000;
    if (diff < 60)    return 'ahora';
    if (diff < 3600)  return `${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} h`;
    return new Date(fecha).toLocaleDateString('es-CO', { day: 'numeric', month: 'short' });
}

function Avatar({ nombre, red, size = 44 }) {
    const info = ICONS[red] || {};
    return (
        <div style={{ position: 'relative', flexShrink: 0 }}>
            <div style={{ width: size, height: size, borderRadius: '50%', background: '#e5e7eb', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: size * 0.42, fontWeight: 700, color: '#6366f1' }}>
                {(nombre || '?')[0].toUpperCase()}
            </div>
            <div style={{ position: 'absolute', bottom: 0, right: 0, width: size * 0.36, height: size * 0.36, borderRadius: '50%', background: info.bg || '#9ca3af', border: '2px solid #fff', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: size * 0.18 }}>
                {info.emoji}
            </div>
        </div>
    );
}

export default function Social() {
    const [items, setItems]               = useState([]);
    const [stats, setStats]               = useState({});
    const [filtroRed, setFiltroRed]       = useState('todas');
    const [filtroTipo, setFiltroTipo]     = useState('todos');
    const [soloNoLeidos, setSoloNoLeidos] = useState(false);
    const [contacto, setContacto]         = useState(null);
    const [conversacion, setConversacion] = useState([]);
    const [cargando, setCargando]         = useState(false);
    const [respuesta, setRespuesta]       = useState('');
    const [enviando, setEnviando]         = useState(false);
    const [errorResp, setErrorResp]       = useState('');
    const chatRef   = useRef(null);
    const estaAbajo = useRef(true);

    function onChatScroll() {
        const el = chatRef.current;
        if (!el) return;
        estaAbajo.current = el.scrollHeight - el.scrollTop - el.clientHeight < 60;
    }

    function scrollAbajo(forzar = false) {
        if (forzar || estaAbajo.current) {
            setTimeout(() => chatRef.current?.scrollTo({ top: 99999, behavior: 'smooth' }), 60);
        }
    }

    useEffect(() => { cargar(); }, [filtroRed, filtroTipo, soloNoLeidos]);

    async function cargar() {
        setCargando(true);
        const params = {};
        if (filtroRed  !== 'todas') params.red  = filtroRed;
        if (filtroTipo !== 'todos') params.tipo = filtroTipo;
        if (soloNoLeidos) params.leido = false;
        const [{ data: lista }, { data: st }] = await Promise.all([
            axios.get('/social', { params }),
            axios.get('/social/stats')
        ]);
        setItems(lista.items);
        setStats(st);
        setCargando(false);
    }

    // Agrupar mensajes por contacto (un contacto = remitente_id + red)
    const contactos = useMemo(() => {
        const map = {};
        items.forEach(item => {
            const key = `${item.red}:${item.remitente_id}`;
            if (!map[key]) {
                map[key] = { key, remitente_id: item.remitente_id, remitente: item.remitente, red: item.red, ultimoMensaje: item, noLeidos: 0, etiqueta: null };
            } else if (new Date(item.createdAt) > new Date(map[key].ultimoMensaje.createdAt)) {
                map[key].ultimoMensaje = item;
                if (item.remitente) map[key].remitente = item.remitente;
            }
            if (!item.leido) map[key].noLeidos++;
            if (item.etiqueta) map[key].etiqueta = item.etiqueta;
        });
        return Object.values(map).sort((a, b) =>
            new Date(b.ultimoMensaje.createdAt) - new Date(a.ultimoMensaje.createdAt)
        );
    }, [items]);

    async function abrirContacto(c) {
        setContacto(c);
        setRespuesta('');
        setErrorResp('');
        const { data } = await axios.get('/social/conversacion', {
            params: { remitente_id: c.remitente_id, red: c.red }
        });
        setConversacion(data);
        data.filter(m => !m.leido).forEach(m => axios.put(`/social/${m.id}/leido`).catch(() => {}));
        estaAbajo.current = true;
        scrollAbajo(true);
        cargar();
    }

    async function enviarRespuesta() {
        if (!respuesta.trim() || !contacto) return;
        const ultimoMsg = [...conversacion].reverse().find(m => m.remitente_id === contacto.remitente_id);
        if (!ultimoMsg) return;
        setEnviando(true);
        setErrorResp('');
        try {
            await axios.post(`/social/${ultimoMsg.id}/responder`, { texto: respuesta });
            setRespuesta('');
            const { data } = await axios.get('/social/conversacion', {
                params: { remitente_id: contacto.remitente_id, red: contacto.red }
            });
            setConversacion(data);
            scrollAbajo(true);
            cargar();
        } catch (err) {
            setErrorResp(err.response?.data?.error || 'Error al enviar');
        } finally {
            setEnviando(false);
        }
    }

    const esActivo = (c) => contacto?.remitente_id === c.remitente_id && contacto?.red === c.red;

    return (
        <div style={{ display: 'flex', position: 'absolute', top: 0, right: 0, bottom: 0, left: 0, background: '#f9fafb' }}>

            {/* ── Panel izquierdo: lista de contactos ── */}
            <div style={{ width: contacto ? 300 : '100%', flexShrink: 0, borderRight: contacto ? '1px solid #e5e7eb' : 'none', display: 'flex', flexDirection: 'column', background: '#fff' }}>

                {/* Header */}
                <div style={{ padding: '18px 16px 12px', borderBottom: '1px solid #e5e7eb' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 14 }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                            <MessageCircle size={20} color="#6366f1" />
                            <h2 style={{ margin: 0, fontSize: '1rem', fontWeight: 700 }}>Bandeja Social</h2>
                            {stats.noLeidos > 0 && (
                                <span style={{ background: '#ef4444', color: '#fff', borderRadius: 20, padding: '1px 7px', fontSize: 11, fontWeight: 700 }}>
                                    {stats.noLeidos}
                                </span>
                            )}
                        </div>
                        <button onClick={cargar} title="Actualizar" style={{ background: 'none', border: 'none', cursor: 'pointer', color: cargando ? '#6366f1' : '#9ca3af', padding: 4 }}>
                            <RefreshCw size={15} />
                        </button>
                    </div>

                    {/* Chips de red */}
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4,1fr)', gap: 5, marginBottom: 10 }}>
                        {['facebook', 'instagram', 'whatsapp', 'tiktok'].map(r => {
                            const info = ICONS[r];
                            const on = filtroRed === r;
                            return (
                                <div key={r} onClick={() => setFiltroRed(on ? 'todas' : r)}
                                    style={{ background: on ? info.bg : '#f3f4f6', color: on ? '#fff' : '#374151', borderRadius: 8, padding: '6px 4px', cursor: 'pointer', textAlign: 'center', border: `1px solid ${on ? info.bg : '#e5e7eb'}` }}>
                                    <div style={{ fontSize: 15 }}>{info.emoji}</div>
                                    <div style={{ fontSize: 9, fontWeight: 600, marginTop: 1 }}>{info.label}</div>
                                    <div style={{ fontSize: 14, fontWeight: 800 }}>{stats[r] || 0}</div>
                                </div>
                            );
                        })}
                    </div>

                    {/* Filtro tipo + no leídos */}
                    <div style={{ display: 'flex', gap: 5 }}>
                        <div style={{ display: 'flex', border: '1px solid #e5e7eb', borderRadius: 6, overflow: 'hidden', flex: 1 }}>
                            {['todos', 'mensaje', 'comentario'].map(t => (
                                <button key={t} onClick={() => setFiltroTipo(t)}
                                    style={{ flex: 1, padding: '4px 0', border: 'none', background: filtroTipo === t ? '#6366f1' : '#fff', color: filtroTipo === t ? '#fff' : '#6b7280', cursor: 'pointer', fontSize: 10, fontWeight: filtroTipo === t ? 600 : 400 }}>
                                    {t === 'todos' ? 'Todos' : t === 'mensaje' ? 'Msgs' : 'Cmts'}
                                </button>
                            ))}
                        </div>
                        <button onClick={() => setSoloNoLeidos(v => !v)}
                            style={{ padding: '4px 8px', border: `1px solid ${soloNoLeidos ? '#6366f1' : '#e5e7eb'}`, borderRadius: 6, background: soloNoLeidos ? '#ede9fe' : '#fff', color: soloNoLeidos ? '#6366f1' : '#6b7280', cursor: 'pointer', fontSize: 10, fontWeight: soloNoLeidos ? 700 : 400, whiteSpace: 'nowrap' }}>
                            No leídos
                        </button>
                    </div>
                </div>

                {/* Lista de contactos */}
                <div style={{ flex: 1, overflowY: 'auto' }}>
                    {contactos.length === 0 && (
                        <div style={{ textAlign: 'center', padding: '60px 20px', color: '#9ca3af' }}>
                            <MessageCircle size={40} style={{ marginBottom: 12, opacity: .25 }} />
                            <div style={{ fontWeight: 600, marginBottom: 4, fontSize: 14 }}>Sin mensajes aún</div>
                            <div style={{ fontSize: 12 }}>Los mensajes de tus redes aparecerán aquí.</div>
                        </div>
                    )}
                    {contactos.map(c => (
                        <div key={c.key} onClick={() => abrirContacto(c)}
                            style={{ padding: '11px 14px', borderBottom: '1px solid #f3f4f6', cursor: 'pointer', background: esActivo(c) ? '#ede9fe' : '#fff', display: 'flex', gap: 10, alignItems: 'center', transition: 'background .1s' }}>
                            <Avatar nombre={c.remitente} red={c.red} size={42} />
                            <div style={{ flex: 1, minWidth: 0 }}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 2 }}>
                                    <span style={{ fontWeight: c.noLeidos > 0 ? 700 : 500, fontSize: 13, color: '#111', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', maxWidth: 140 }}>
                                        {c.remitente || 'Desconocido'}
                                    </span>
                                    <span style={{ fontSize: 10, color: '#9ca3af', flexShrink: 0, marginLeft: 4 }}>
                                        {timeAgo(c.ultimoMensaje.createdAt)}
                                    </span>
                                </div>
                                <div style={{ fontSize: 11, color: '#9ca3af', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                    {c.ultimoMensaje.respuesta
                                        ? `🤖 ${c.ultimoMensaje.respuesta.slice(0, 38)}…`
                                        : (c.ultimoMensaje.contenido?.startsWith('[Audio]: ')
                                            ? `🎤 ${c.ultimoMensaje.contenido.slice(9, 48)}…`
                                            : c.ultimoMensaje.contenido?.slice(0, 50))}
                                </div>
                                {c.etiqueta && (
                                    <div style={{ fontSize: 10, color: '#6366f1', background: '#ede9fe', borderRadius: 10, padding: '1px 7px', marginTop: 3, display: 'inline-block', fontWeight: 600 }}>
                                        📅 {c.etiqueta}
                                    </div>
                                )}
                            </div>
                            {c.noLeidos > 0 && (
                                <div style={{ width: 20, height: 20, borderRadius: '50%', background: '#6366f1', color: '#fff', fontSize: 10, fontWeight: 700, display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                                    {c.noLeidos}
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            </div>

            {/* ── Panel derecho: conversación ── */}
            {contacto ? (
                <div style={{ flex: 1, display: 'flex', flexDirection: 'column', minWidth: 0, overflow: 'hidden' }}>

                    {/* Header conversación */}
                    <div style={{ padding: '12px 18px', borderBottom: '1px solid #e5e7eb', display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: '#fff' }}>
                        <div style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                            <Avatar nombre={contacto.remitente} red={contacto.red} size={38} />
                            <div>
                                <div style={{ fontWeight: 700, fontSize: '0.9rem' }}>{contacto.remitente || 'Desconocido'}</div>
                                <div style={{ fontSize: 11, color: '#9ca3af' }}>{ICONS[contacto.red]?.label} · {conversacion.length} mensajes</div>
                            </div>
                        </div>
                        <button onClick={() => { setContacto(null); setConversacion([]); }}
                            title="Cerrar conversación"
                            style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#9ca3af', padding: 6, borderRadius: 6, display: 'flex', alignItems: 'center' }}>
                            <X size={18} />
                        </button>
                    </div>

                    {/* Mensajes */}
                    <div ref={chatRef} onScroll={onChatScroll} style={{ flex: 1, padding: '14px 18px', overflowY: 'auto', display: 'flex', flexDirection: 'column', gap: 8, background: '#f9fafb' }}>
                        {conversacion.length === 0 && (
                            <div style={{ color: '#9ca3af', fontSize: 13, textAlign: 'center', marginTop: 60 }}>Cargando…</div>
                        )}
                        {conversacion.map(m => {
                            const esAudio = m.contenido?.startsWith('[Audio]: ');
                            const textoMostrar = esAudio ? m.contenido.slice(9) : m.contenido;
                            return (
                                <div key={m.id}>
                                    {/* Mensaje cliente */}
                                    <div style={{ display: 'flex', justifyContent: 'flex-start', marginBottom: m.respuesta ? 5 : 0 }}>
                                        <div style={{ maxWidth: '72%' }}>
                                            <div style={{ background: '#fff', borderRadius: '14px 14px 14px 3px', padding: '9px 13px', fontSize: 13, color: '#111', lineHeight: 1.5, boxShadow: '0 1px 2px rgba(0,0,0,.07)', border: '1px solid #e9eaec' }}>
                                                {textoMostrar}
                                            </div>
                                            <div style={{ fontSize: 10, color: '#b0b7c3', marginTop: 2, marginLeft: 4 }}>
                                                {esAudio && '🎤 audio · '}{timeAgo(m.createdAt)}
                                            </div>
                                        </div>
                                    </div>
                                    {/* Respuesta bot */}
                                    {m.respuesta && (
                                        <div style={{ display: 'flex', justifyContent: 'flex-end' }}>
                                            <div style={{ maxWidth: '72%' }}>
                                                <div style={{ background: '#6366f1', borderRadius: '14px 14px 3px 14px', padding: '9px 13px', fontSize: 13, color: '#fff', lineHeight: 1.5 }}>
                                                    {m.respuesta}
                                                </div>
                                                <div style={{ fontSize: 10, color: '#b0b7c3', marginTop: 2, textAlign: 'right', marginRight: 4 }}>
                                                    🤖 Bot · {timeAgo(m.updatedAt)}
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            );
                        })}
                    </div>

                    {/* Caja responder */}
                    {(contacto.red === 'facebook' || contacto.red === 'instagram' || contacto.red === 'whatsapp') && (
                        <div style={{ padding: '10px 16px', borderTop: '1px solid #e5e7eb', background: '#fff' }}>
                            <div style={{ display: 'flex', gap: 8, alignItems: 'flex-end' }}>
                                <textarea
                                    value={respuesta}
                                    onChange={e => setRespuesta(e.target.value)}
                                    onKeyDown={e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); enviarRespuesta(); } }}
                                    placeholder={`Responder via ${ICONS[contacto.red]?.label}…`}
                                    rows={2}
                                    style={{ flex: 1, resize: 'none', border: '1px solid #e5e7eb', borderRadius: 10, padding: '8px 12px', fontSize: 13, fontFamily: 'inherit', outline: 'none', background: '#f9fafb' }}
                                />
                                <button onClick={enviarRespuesta} disabled={enviando || !respuesta.trim()}
                                    style={{ padding: '10px 16px', background: enviando || !respuesta.trim() ? '#e5e7eb' : '#6366f1', color: enviando || !respuesta.trim() ? '#9ca3af' : '#fff', border: 'none', borderRadius: 10, cursor: enviando || !respuesta.trim() ? 'default' : 'pointer', display: 'flex', alignItems: 'center', gap: 6, fontSize: 13, fontWeight: 600, height: 40 }}>
                                    <Send size={14} /> {enviando ? '…' : 'Enviar'}
                                </button>
                            </div>
                            {errorResp && <div style={{ marginTop: 5, fontSize: 12, color: '#ef4444' }}>{errorResp}</div>}
                        </div>
                    )}
                </div>
            ) : contactos.length > 0 ? (
                <div style={{ flex: 1, display: 'flex', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', gap: 12, color: '#9ca3af' }}>
                    <MessageCircle size={48} style={{ opacity: 0.15 }} />
                    <span style={{ fontSize: 14 }}>Selecciona una conversación</span>
                </div>
            ) : null}
        </div>
    );
}
