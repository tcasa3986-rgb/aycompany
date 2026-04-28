import { useEffect, useState } from 'react';
import axios from '../api/axios';
import { MessageCircle, X, Check, CheckCheck, Filter, RefreshCw, Facebook, Instagram, Send } from 'lucide-react';

const REDES = ['todas', 'facebook', 'instagram', 'whatsapp'];
const TIPOS = ['todos', 'mensaje', 'comentario'];

const ICONS = {
    facebook:  { bg: '#1877f2', label: 'Facebook',  emoji: '🔵' },
    instagram: { bg: '#e1306c', label: 'Instagram', emoji: '📸' },
    whatsapp:  { bg: '#25d366', label: 'WhatsApp',  emoji: '🟢' }
};

function timeAgo(fecha) {
    const diff = (Date.now() - new Date(fecha)) / 1000;
    if (diff < 60)   return 'hace un momento';
    if (diff < 3600) return `hace ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `hace ${Math.floor(diff / 3600)} h`;
    return new Date(fecha).toLocaleDateString('es-CO', { day: 'numeric', month: 'short' });
}

export default function Social() {
    const [items, setItems] = useState([]);
    const [stats, setStats] = useState({});
    const [filtroRed, setFiltroRed] = useState('todas');
    const [filtroTipo, setFiltroTipo] = useState('todos');
    const [soloNoLeidos, setSoloNoLeidos] = useState(false);
    const [seleccionado, setSeleccionado] = useState(null);
    const [cargando, setCargando] = useState(false);
    const [respuesta, setRespuesta] = useState('');
    const [enviando, setEnviando] = useState(false);
    const [errorRespuesta, setErrorRespuesta] = useState('');

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

    async function marcarLeido(id) {
        await axios.put(`/social/${id}/leido`);
        cargar();
        if (seleccionado?.id === id) setSeleccionado(s => ({ ...s, leido: true }));
    }

    async function marcarRespondido(id) {
        await axios.put(`/social/${id}/respondido`);
        cargar();
        if (seleccionado?.id === id) setSeleccionado(s => ({ ...s, respondido: true }));
    }

    async function eliminar(id) {
        if (!confirm('¿Eliminar?')) return;
        await axios.delete(`/social/${id}`);
        if (seleccionado?.id === id) setSeleccionado(null);
        cargar();
    }

    function abrir(item) {
        setSeleccionado(item);
        setRespuesta('');
        setErrorRespuesta('');
        if (!item.leido) marcarLeido(item.id);
    }

    async function enviarRespuesta() {
        if (!respuesta.trim() || !seleccionado) return;
        setEnviando(true);
        setErrorRespuesta('');
        try {
            await axios.post(`/social/${seleccionado.id}/responder`, { texto: respuesta });
            setRespuesta('');
            setSeleccionado(s => ({ ...s, respondido: true }));
            cargar();
        } catch (err) {
            setErrorRespuesta(err.response?.data?.error || 'Error al enviar');
        } finally {
            setEnviando(false);
        }
    }

    return (
        <div style={{ display: 'flex', height: '100%' }}>
            {/* Lista izquierda */}
            <div style={{ width: seleccionado ? 380 : '100%', borderRight: seleccionado ? '1px solid #e5e7eb' : 'none', display: 'flex', flexDirection: 'column', transition: 'width .2s' }}>
                {/* Header */}
                <div style={{ padding: '20px 24px 12px', borderBottom: '1px solid #e5e7eb' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                            <MessageCircle size={22} color="#6366f1" />
                            <h2 style={{ margin: 0, fontSize: '1.2rem', fontWeight: 700 }}>Bandeja Social</h2>
                            {stats.noLeidos > 0 && <span style={{ background: '#ef4444', color: '#fff', borderRadius: 20, padding: '1px 8px', fontSize: 12, fontWeight: 700 }}>{stats.noLeidos}</span>}
                        </div>
                        <button onClick={cargar} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#9ca3af', padding: 4 }}>
                            <RefreshCw size={16} className={cargando ? 'spin' : ''} />
                        </button>
                    </div>

                    {/* Stats */}
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 8, marginBottom: 14 }}>
                        {['facebook', 'instagram', 'whatsapp'].map(r => {
                            const info = ICONS[r];
                            return (
                                <div key={r} onClick={() => setFiltroRed(filtroRed === r ? 'todas' : r)} style={{ background: filtroRed === r ? info.bg : '#f9fafb', color: filtroRed === r ? '#fff' : '#374151', borderRadius: 8, padding: '8px 10px', cursor: 'pointer', border: `1px solid ${filtroRed === r ? info.bg : '#e5e7eb'}`, textAlign: 'center', transition: 'all .15s' }}>
                                    <div style={{ fontSize: 18 }}>{info.emoji}</div>
                                    <div style={{ fontSize: 11, fontWeight: 600, marginTop: 2 }}>{info.label}</div>
                                    <div style={{ fontSize: 16, fontWeight: 800 }}>{stats[r] || 0}</div>
                                </div>
                            );
                        })}
                    </div>

                    {/* Filtros */}
                    <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap' }}>
                        <div style={{ display: 'flex', border: '1px solid #e5e7eb', borderRadius: 6, overflow: 'hidden' }}>
                            {TIPOS.map(t => (
                                <button key={t} onClick={() => setFiltroTipo(t)} style={{ padding: '4px 10px', border: 'none', background: filtroTipo === t ? '#6366f1' : '#fff', color: filtroTipo === t ? '#fff' : '#6b7280', cursor: 'pointer', fontSize: 12 }}>
                                    {t === 'todos' ? 'Todos' : t === 'mensaje' ? 'Mensajes' : 'Comentarios'}
                                </button>
                            ))}
                        </div>
                        <button onClick={() => setSoloNoLeidos(v => !v)} style={{ padding: '4px 10px', border: `1px solid ${soloNoLeidos ? '#6366f1' : '#e5e7eb'}`, borderRadius: 6, background: soloNoLeidos ? '#ede9fe' : '#fff', color: soloNoLeidos ? '#6366f1' : '#6b7280', cursor: 'pointer', fontSize: 12, fontWeight: soloNoLeidos ? 600 : 400 }}>
                            No leídos
                        </button>
                    </div>
                </div>

                {/* Lista */}
                <div style={{ flex: 1, overflowY: 'auto' }}>
                    {items.length === 0 && (
                        <div style={{ textAlign: 'center', padding: '60px 20px', color: '#9ca3af' }}>
                            <MessageCircle size={40} style={{ marginBottom: 12, opacity: .3 }} />
                            <div style={{ fontWeight: 600, marginBottom: 4 }}>Sin mensajes aún</div>
                            <div style={{ fontSize: 13 }}>Cuando conectes tus redes, los mensajes y comentarios aparecerán aquí.</div>
                        </div>
                    )}
                    {items.map(item => {
                        const info = ICONS[item.red] || {};
                        const activo = seleccionado?.id === item.id;
                        return (
                            <div key={item.id} onClick={() => abrir(item)} style={{ padding: '14px 20px', borderBottom: '1px solid #f3f4f6', cursor: 'pointer', background: activo ? '#ede9fe' : item.leido ? '#fff' : '#fafafa', display: 'flex', gap: 12, alignItems: 'flex-start', transition: 'background .1s' }}>
                                <div style={{ width: 38, height: 38, borderRadius: '50%', background: info.bg || '#9ca3af', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 16, flexShrink: 0 }}>
                                    {info.emoji}
                                </div>
                                <div style={{ flex: 1, minWidth: 0 }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 2 }}>
                                        <span style={{ fontWeight: item.leido ? 500 : 700, fontSize: 13, color: '#111' }}>{item.remitente || 'Desconocido'}</span>
                                        <span style={{ fontSize: 11, color: '#9ca3af', flexShrink: 0 }}>{timeAgo(item.createdAt)}</span>
                                    </div>
                                    <div style={{ fontSize: 11, color: info.bg || '#9ca3af', fontWeight: 600, marginBottom: 3 }}>
                                        {info.label} · {item.tipo}
                                    </div>
                                    <div style={{ fontSize: 12, color: '#6b7280', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                        {item.contenido}
                                    </div>
                                </div>
                                {!item.leido && <div style={{ width: 8, height: 8, borderRadius: '50%', background: '#6366f1', flexShrink: 0, marginTop: 4 }} />}
                            </div>
                        );
                    })}
                </div>
            </div>

            {/* Detalle derecha */}
            {seleccionado && (
                <div style={{ flex: 1, display: 'flex', flexDirection: 'column' }}>
                    {/* Header detalle */}
                    <div style={{ padding: '16px 24px', borderBottom: '1px solid #e5e7eb', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <div style={{ display: 'flex', gap: 12, alignItems: 'center' }}>
                            <div style={{ width: 42, height: 42, borderRadius: '50%', background: ICONS[seleccionado.red]?.bg || '#9ca3af', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 20 }}>
                                {ICONS[seleccionado.red]?.emoji}
                            </div>
                            <div>
                                <div style={{ fontWeight: 700, fontSize: '1rem' }}>{seleccionado.remitente || 'Desconocido'}</div>
                                <div style={{ fontSize: 12, color: '#9ca3af' }}>{ICONS[seleccionado.red]?.label} · {seleccionado.tipo} · {timeAgo(seleccionado.createdAt)}</div>
                            </div>
                        </div>
                        <div style={{ display: 'flex', gap: 8 }}>
                            {!seleccionado.respondido && (
                                <button onClick={() => marcarRespondido(seleccionado.id)} style={{ display: 'flex', alignItems: 'center', gap: 6, padding: '6px 12px', background: '#dcfce7', color: '#16a34a', border: 'none', borderRadius: 7, cursor: 'pointer', fontSize: 12, fontWeight: 600 }}>
                                    <CheckCheck size={14} /> Marcar respondido
                                </button>
                            )}
                            <button onClick={() => eliminar(seleccionado.id)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#ef4444', padding: 4 }}>
                                <X size={18} />
                            </button>
                        </div>
                    </div>

                    {/* Contenido */}
                    <div style={{ flex: 1, padding: 24, overflowY: 'auto' }}>
                        <div style={{ background: '#f9fafb', borderRadius: 12, padding: 20, marginBottom: 16, maxWidth: 600 }}>
                            <div style={{ fontSize: 15, lineHeight: 1.6, color: '#111' }}>{seleccionado.contenido}</div>
                        </div>
                        <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
                            {seleccionado.leido && <span style={{ background: '#f3f4f6', color: '#9ca3af', borderRadius: 20, padding: '3px 10px', fontSize: 11 }}><Check size={11} style={{ verticalAlign: 'middle' }} /> Leído</span>}
                            {seleccionado.respondido && <span style={{ background: '#dcfce7', color: '#16a34a', borderRadius: 20, padding: '3px 10px', fontSize: 11 }}><CheckCheck size={11} style={{ verticalAlign: 'middle' }} /> Respondido</span>}
                            {seleccionado.post_id && <span style={{ background: '#ede9fe', color: '#7c3aed', borderRadius: 20, padding: '3px 10px', fontSize: 11 }}>Post ID: {seleccionado.post_id.slice(-8)}</span>}
                        </div>
                        {seleccionado.fecha_red && (
                            <div style={{ marginTop: 12, fontSize: 12, color: '#9ca3af' }}>
                                Recibido: {new Date(seleccionado.fecha_red).toLocaleString('es-CO', { dateStyle: 'full', timeStyle: 'short' })}
                            </div>
                        )}
                    </div>

                    {/* Caja de respuesta */}
                    {(seleccionado.red === 'facebook' || seleccionado.red === 'instagram' || seleccionado.red === 'whatsapp') && (
                        <div style={{ padding: '16px 24px', borderTop: '1px solid #e5e7eb', background: '#fff' }}>
                            <div style={{ fontSize: 12, fontWeight: 600, color: '#6b7280', marginBottom: 8 }}>
                                Responder via {ICONS[seleccionado.red]?.label}
                            </div>
                            <div style={{ display: 'flex', gap: 8 }}>
                                <textarea
                                    value={respuesta}
                                    onChange={e => setRespuesta(e.target.value)}
                                    onKeyDown={e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); enviarRespuesta(); } }}
                                    placeholder="Escribe tu respuesta… (Enter para enviar)"
                                    rows={2}
                                    style={{ flex: 1, resize: 'none', border: '1px solid #e5e7eb', borderRadius: 8, padding: '8px 12px', fontSize: 13, fontFamily: 'inherit', outline: 'none' }}
                                />
                                <button
                                    onClick={enviarRespuesta}
                                    disabled={enviando || !respuesta.trim()}
                                    style={{ padding: '0 16px', background: enviando || !respuesta.trim() ? '#e5e7eb' : '#6366f1', color: enviando || !respuesta.trim() ? '#9ca3af' : '#fff', border: 'none', borderRadius: 8, cursor: enviando || !respuesta.trim() ? 'default' : 'pointer', display: 'flex', alignItems: 'center', gap: 6, fontSize: 13, fontWeight: 600 }}
                                >
                                    <Send size={14} /> {enviando ? 'Enviando…' : 'Enviar'}
                                </button>
                            </div>
                            {errorRespuesta && <div style={{ marginTop: 6, fontSize: 12, color: '#ef4444' }}>{errorRespuesta}</div>}
                        </div>
                    )}
                </div>
            )}

            {/* Estado: sin conexión */}
            {!seleccionado && items.length === 0 && (
                <div style={{ display: 'none' }} />
            )}
        </div>
    );
}
