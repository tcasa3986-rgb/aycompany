import { useEffect, useState, useRef } from 'react';
import axios from '../api/axios';
import { Lightbulb, Plus, X, Edit2, Bot, Send, Sparkles, Rss, CheckCircle2, Clock, ExternalLink, BarChart2, Send as SendIcon } from 'lucide-react';

const GITHUB_RAW = 'https://raw.githubusercontent.com/tcasa3986-rgb/AI-COMPANY/main';
const GITHUB_API = 'https://api.github.com/repos/tcasa3986-rgb/AI-COMPANY/contents/blog';

const CANALES = ['Instagram', 'Facebook', 'TikTok', 'YouTube', 'LinkedIn', 'Twitter/X', 'Blog', 'Email', 'WhatsApp', 'Otro'];
const FORMATOS = ['Video', 'Reel', 'Imagen', 'Carrusel', 'Historia', 'Texto', 'Podcast', 'Newsletter'];
const ESTADOS = {
    idea:        { label: '💡 Idea',        color: '#f59e0b', bg: '#fef3c7' },
    en_progreso: { label: '⚡ En progreso', color: '#6366f1', bg: '#ede9fe' },
    publicado:   { label: '✅ Publicado',   color: '#22c55e', bg: '#dcfce7' },
    descartado:  { label: '🗑 Descartado',  color: '#9ca3af', bg: '#f3f4f6' }
};

const empty = { titulo: '', descripcion: '', canal: 'Instagram', formato: 'Video', estado: 'idea', fecha_publicacion: '' };

const SUGERENCIAS_RAPIDAS = [
    'Dame 5 ideas de contenido para Instagram',
    '¿Qué tipo de reels funcionan mejor ahora?',
    '¿Cómo puedo crecer más rápido en TikTok?',
    'Genera un calendario de contenido para esta semana',
    '¿Qué estrategia me recomiendas según mis metas?',
];

const CIUDADES = [
    { nombre: 'Bogotá',       slug: 'bogota' },
    { nombre: 'Medellín',     slug: 'medellin' },
    { nombre: 'Cali',         slug: 'cali' },
    { nombre: 'Barranquilla', slug: 'barranquilla' },
    { nombre: 'Bucaramanga',  slug: 'bucaramanga' },
];

const CHECKLIST_SEO = [
    { id: 1, texto: 'Revisar posición promedio en Google Search Console' },
    { id: 2, texto: 'Solicitar indexación de los artículos nuevos publicados' },
    { id: 3, texto: 'Verificar Core Web Vitals (velocidad móvil)' },
    { id: 4, texto: 'Registrar en un directorio nuevo (Clutch, Google Business, etc.)' },
    { id: 5, texto: 'Revisar artículos con posición 11-20 y mejorar su contenido' },
];

export default function Contenido() {
    const [tab, setTab] = useState('ideas'); // 'ideas' | 'blog' | 'rendimiento'
    const [ideas, setIdeas] = useState([]);
    const [modal, setModal] = useState(false);
    const [form, setForm] = useState(empty);
    const [editId, setEditId] = useState(null);
    const [filtro, setFiltro] = useState('todas');
    const [panelIA, setPanelIA] = useState(false);
    const [mensajeIA, setMensajeIA] = useState('');
    const [historial, setHistorial] = useState([]);
    const [cargandoIA, setCargandoIA] = useState(false);
    const chatEndRef = useRef(null);

    // Blog SEO state
    const [blogTopics, setBlogTopics] = useState([]);
    const [publishedSlugs, setPublishedSlugs] = useState(new Set());
    const [blogLoading, setBlogLoading] = useState(false);
    const [blogError, setBlogError] = useState(null);
    const [blogFiltro, setBlogFiltro] = useState('todos');

    // Rendimiento state
    const [enviandoTelegram, setEnviandoTelegram] = useState(false);
    const [telegramOk, setTelegramOk] = useState(null);
    const [checklistDone, setChecklistDone] = useState(new Set());

    useEffect(() => {
        if ((tab === 'blog' || tab === 'rendimiento') && blogTopics.length === 0) cargarBlog();
    }, [tab]);
    useEffect(() => { cargar(); }, []);

    async function cargarBlog() {
        setBlogLoading(true);
        setBlogError(null);
        try {
            const [topicsRes, dirRes] = await Promise.all([
                fetch(`${GITHUB_RAW}/scripts/blog-topics.json`),
                fetch(GITHUB_API),
            ]);
            const topics = await topicsRes.json();
            const dirs = await dirRes.json();
            const slugs = new Set(Array.isArray(dirs) ? dirs.filter(d => d.type === 'dir').map(d => d.name) : []);
            setBlogTopics(topics);
            setPublishedSlugs(slugs);
        } catch {
            setBlogError('No se pudo cargar el estado del blog desde GitHub.');
        }
        setBlogLoading(false);
    }

    useEffect(() => { chatEndRef.current?.scrollIntoView({ behavior: 'smooth' }); }, [historial]);
    async function cargar() { const { data } = await axios.get('/contenido'); setIdeas(data); }

    async function guardar(e) {
        e.preventDefault();
        if (editId) await axios.put(`/contenido/${editId}`, form);
        else await axios.post('/contenido', form);
        setModal(false); setForm(empty); setEditId(null); cargar();
    }

    function editar(i) {
        setForm({ titulo: i.titulo, descripcion: i.descripcion || '', canal: i.canal, formato: i.formato, estado: i.estado, fecha_publicacion: i.fecha_publicacion || '' });
        setEditId(i.id); setModal(true);
    }
    async function eliminar(id) { if (!confirm('¿Eliminar?')) return; await axios.delete(`/contenido/${id}`); cargar(); }
    async function avanzar(id, estado) { await axios.put(`/contenido/${id}`, { estado }); cargar(); }

    async function enviarIA(texto) {
        const msg = texto || mensajeIA;
        if (!msg.trim()) return;
        const nuevoHistorial = [...historial, { role: 'user', content: msg }];
        setHistorial(nuevoHistorial);
        setMensajeIA('');
        setCargandoIA(true);
        try {
            const { data } = await axios.post('/asistente/chat', { mensaje: msg, historial: historial.slice(-8) });
            setHistorial(h => [...h, { role: 'assistant', content: data.respuesta }]);
        } catch {
            setHistorial(h => [...h, { role: 'assistant', content: '⚠️ Error al conectar con el asistente.' }]);
        }
        setCargandoIA(false);
    }

    async function enviarReporteTelegram() {
        setEnviandoTelegram(true);
        setTelegramOk(null);
        try {
            await axios.post('/seo/reporte-telegram');
            setTelegramOk('ok');
        } catch {
            setTelegramOk('error');
        }
        setEnviandoTelegram(false);
        setTimeout(() => setTelegramOk(null), 4000);
    }

    const filtradas = filtro === 'todas' ? ideas : ideas.filter(i => i.estado === filtro);
    const conteo = Object.keys(ESTADOS).reduce((acc, e) => { acc[e] = ideas.filter(i => i.estado === e).length; return acc; }, {});

    const blogFiltrados = blogTopics.filter(t => {
        if (blogFiltro === 'publicado') return publishedSlugs.has(t.slug);
        if (blogFiltro === 'pendiente') return !publishedSlugs.has(t.slug);
        return true;
    });
    const publicados = blogTopics.filter(t => publishedSlugs.has(t.slug)).length;
    const pendientes = blogTopics.length - publicados;
    const pct = blogTopics.length > 0 ? Math.round((publicados / blogTopics.length) * 100) : 0;
    const proximos3 = blogTopics.filter(t => !publishedSlugs.has(t.slug)).slice(0, 3);

    return (
        <div style={{ display: 'flex', height: '100%' }}>
            <div style={{ flex: 1, padding: 28, overflowY: 'auto', transition: 'all .3s' }}>

                {/* Tabs */}
                <div style={{ display: 'flex', gap: 4, marginBottom: 24, borderBottom: '2px solid #e5e7eb', paddingBottom: 0 }}>
                    {[
                        { id: 'ideas', icon: <Lightbulb size={15} />, label: 'Ideas de Contenido' },
                        { id: 'blog', icon: <Rss size={15} />, label: 'Blog SEO', badge: pendientes > 0 ? pendientes : null },
                        { id: 'rendimiento', icon: <BarChart2 size={15} />, label: 'Rendimiento SEO' },
                    ].map(t => (
                        <button key={t.id} onClick={() => setTab(t.id)} style={{ display: 'flex', alignItems: 'center', gap: 6, padding: '8px 18px', border: 'none', background: 'none', cursor: 'pointer', fontWeight: 600, fontSize: 14, color: tab === t.id ? '#6366f1' : '#6b7280', borderBottom: tab === t.id ? '2px solid #6366f1' : '2px solid transparent', marginBottom: -2 }}>
                            {t.icon} {t.label}
                            {t.badge && <span style={{ background: '#f59e0b', color: '#fff', borderRadius: 10, padding: '1px 7px', fontSize: 11, fontWeight: 700 }}>{t.badge}</span>}
                        </button>
                    ))}
                </div>

                {/* ═══ TAB RENDIMIENTO SEO ═══ */}
                {tab === 'rendimiento' && (
                    <div>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
                            <div>
                                <h2 style={{ margin: 0, fontSize: '1.2rem', fontWeight: 700 }}>Rendimiento SEO — aicompanyco.com</h2>
                                <div style={{ fontSize: 12, color: '#9ca3af', marginTop: 3 }}>Reporte automático el 1 y 15 de cada mes por Telegram</div>
                            </div>
                            <div style={{ display: 'flex', gap: 8 }}>
                                <button onClick={cargarBlog} disabled={blogLoading} style={{ fontSize: 12, padding: '7px 14px', background: '#f3f4f6', border: '1px solid #e5e7eb', borderRadius: 7, cursor: 'pointer', color: '#374151' }}>
                                    {blogLoading ? 'Cargando...' : '↻ Actualizar'}
                                </button>
                                <button
                                    onClick={enviarReporteTelegram}
                                    disabled={enviandoTelegram}
                                    style={{ display: 'flex', alignItems: 'center', gap: 6, fontSize: 12, padding: '7px 14px', background: telegramOk === 'ok' ? '#16a34a' : telegramOk === 'error' ? '#ef4444' : '#6366f1', color: '#fff', border: 'none', borderRadius: 7, cursor: 'pointer', fontWeight: 600, opacity: enviandoTelegram ? 0.7 : 1 }}>
                                    <SendIcon size={13} />
                                    {enviandoTelegram ? 'Enviando...' : telegramOk === 'ok' ? '✓ Enviado' : telegramOk === 'error' ? 'Error' : 'Enviar a Telegram'}
                                </button>
                            </div>
                        </div>

                        {/* Progreso blog */}
                        <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 20, marginBottom: 16 }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 10 }}>
                                <div style={{ fontWeight: 700, fontSize: 14 }}>Progreso del Blog</div>
                                <div style={{ fontWeight: 800, fontSize: 22, color: '#6366f1' }}>{pct}%</div>
                            </div>
                            <div style={{ background: '#f3f4f6', borderRadius: 99, height: 10, overflow: 'hidden', marginBottom: 12 }}>
                                <div style={{ width: `${pct}%`, height: '100%', background: 'linear-gradient(90deg, #6366f1, #8b5cf6)', borderRadius: 99, transition: 'width .6s ease' }} />
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 10 }}>
                                {[
                                    { label: 'Total temas', value: blogTopics.length, color: '#6366f1', bg: '#ede9fe' },
                                    { label: 'Publicados', value: publicados, color: '#16a34a', bg: '#dcfce7' },
                                    { label: 'Pendientes', value: pendientes, color: '#d97706', bg: '#fef3c7' },
                                ].map(c => (
                                    <div key={c.label} style={{ background: c.bg, borderRadius: 8, padding: '10px 14px', textAlign: 'center' }}>
                                        <div style={{ fontSize: 11, fontWeight: 600, color: c.color }}>{c.label}</div>
                                        <div style={{ fontSize: 22, fontWeight: 800, color: c.color }}>{c.value}</div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Próximos artículos */}
                        <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 20, marginBottom: 16 }}>
                            <div style={{ fontWeight: 700, fontSize: 14, marginBottom: 12 }}>📅 Próximos a publicar</div>
                            <div style={{ fontSize: 12, color: '#9ca3af', marginBottom: 10 }}>El agente publica automáticamente cada lunes y jueves</div>
                            {proximos3.length === 0
                                ? <div style={{ color: '#9ca3af', fontSize: 13 }}>¡Todo publicado! 🎉</div>
                                : proximos3.map((t, i) => (
                                    <div key={t.slug} style={{ display: 'flex', gap: 12, padding: '10px 0', borderBottom: i < proximos3.length - 1 ? '1px solid #f3f4f6' : 'none', alignItems: 'center' }}>
                                        <span style={{ background: '#ede9fe', color: '#7c3aed', borderRadius: '50%', width: 26, height: 26, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 12, fontWeight: 700, flexShrink: 0 }}>{i + 1}</span>
                                        <div style={{ flex: 1 }}>
                                            <div style={{ fontWeight: 600, fontSize: 13, color: '#111' }}>{t.titulo}</div>
                                            <div style={{ fontSize: 11, color: '#9ca3af', marginTop: 2 }}>{t.categoria} · {t.keyword}</div>
                                        </div>
                                    </div>
                                ))
                            }
                        </div>

                        {/* Páginas de ciudades */}
                        <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 20, marginBottom: 16 }}>
                            <div style={{ fontWeight: 700, fontSize: 14, marginBottom: 12 }}>📍 Páginas de ciudades activas</div>
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill,minmax(140px,1fr))', gap: 8 }}>
                                {CIUDADES.map(c => (
                                    <a key={c.slug} href={`https://aicompanyco.com/${c.slug}/`} target="_blank" rel="noopener noreferrer"
                                        style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '8px 12px', background: '#f0fdf4', border: '1px solid #bbf7d0', borderRadius: 8, color: '#15803d', fontWeight: 600, fontSize: 12, textDecoration: 'none' }}>
                                        <CheckCircle2 size={14} /> {c.nombre}
                                    </a>
                                ))}
                            </div>
                        </div>

                        {/* Links importantes */}
                        <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 20, marginBottom: 16 }}>
                            <div style={{ fontWeight: 700, fontSize: 14, marginBottom: 12 }}>🔗 Acceso rápido</div>
                            <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8 }}>
                                {[
                                    { label: '📊 Google Search Console', url: 'https://search.google.com/search-console/performance/search-analytics?resource_id=sc-domain%3Aaicompanyco.com' },
                                    { label: '🌐 Ver sitio web', url: 'https://aicompanyco.com' },
                                    { label: '📝 Ver blog', url: 'https://aicompanyco.com/blog/' },
                                    { label: '🗺️ Ver sitemap', url: 'https://aicompanyco.com/sitemap-main.xml' },
                                    { label: '⚡ PageSpeed', url: 'https://pagespeed.web.dev/analysis?url=https%3A%2F%2Faicompanyco.com' },
                                ].map(l => (
                                    <a key={l.url} href={l.url} target="_blank" rel="noopener noreferrer"
                                        style={{ padding: '7px 13px', background: '#f3f4f6', border: '1px solid #e5e7eb', borderRadius: 7, fontSize: 12, color: '#374151', textDecoration: 'none', fontWeight: 500 }}>
                                        {l.label} <ExternalLink size={11} style={{ marginLeft: 3 }} />
                                    </a>
                                ))}
                            </div>
                        </div>

                        {/* Checklist quincenal */}
                        <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 20 }}>
                            <div style={{ fontWeight: 700, fontSize: 14, marginBottom: 12 }}>✅ Checklist quincenal SEO</div>
                            {CHECKLIST_SEO.map(item => (
                                <div key={item.id} onClick={() => setChecklistDone(s => { const n = new Set(s); n.has(item.id) ? n.delete(item.id) : n.add(item.id); return n; })}
                                    style={{ display: 'flex', gap: 10, alignItems: 'center', padding: '9px 0', borderBottom: item.id < CHECKLIST_SEO.length ? '1px solid #f3f4f6' : 'none', cursor: 'pointer' }}>
                                    <div style={{ width: 20, height: 20, borderRadius: 4, border: checklistDone.has(item.id) ? 'none' : '2px solid #d1d5db', background: checklistDone.has(item.id) ? '#6366f1' : 'transparent', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                                        {checklistDone.has(item.id) && <span style={{ color: '#fff', fontSize: 13 }}>✓</span>}
                                    </div>
                                    <span style={{ fontSize: 13, color: checklistDone.has(item.id) ? '#9ca3af' : '#374151', textDecoration: checklistDone.has(item.id) ? 'line-through' : 'none' }}>{item.texto}</span>
                                </div>
                            ))}
                            <div style={{ marginTop: 12, fontSize: 12, color: '#9ca3af' }}>
                                {checklistDone.size}/{CHECKLIST_SEO.length} completados
                            </div>
                        </div>
                    </div>
                )}

                {/* ═══ TAB BLOG SEO ═══ */}
                {tab === 'blog' && (
                    <div>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
                            <div>
                                <h2 style={{ margin: 0, fontSize: '1.2rem', fontWeight: 700 }}>Blog SEO — aicompanyco.com</h2>
                                <div style={{ fontSize: 12, color: '#9ca3af', marginTop: 3 }}>Se publica automáticamente cada lunes y jueves vía GitHub Actions</div>
                            </div>
                            <button onClick={cargarBlog} disabled={blogLoading} style={{ fontSize: 12, padding: '7px 14px', background: '#f3f4f6', border: '1px solid #e5e7eb', borderRadius: 7, cursor: 'pointer', color: '#374151' }}>
                                {blogLoading ? 'Cargando...' : '↻ Actualizar'}
                            </button>
                        </div>

                        {/* Barra de progreso */}
                        <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 10, padding: '14px 18px', marginBottom: 16 }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 12, color: '#6b7280', marginBottom: 6, fontWeight: 600 }}>
                                <span>Progreso del blog</span>
                                <span style={{ color: '#6366f1', fontWeight: 700 }}>{publicados}/{blogTopics.length} publicados ({pct}%)</span>
                            </div>
                            <div style={{ background: '#f3f4f6', borderRadius: 99, height: 8, overflow: 'hidden' }}>
                                <div style={{ width: `${pct}%`, height: '100%', background: 'linear-gradient(90deg,#6366f1,#8b5cf6)', borderRadius: 99, transition: 'width .5s' }} />
                            </div>
                        </div>

                        {/* Contadores */}
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 12, marginBottom: 20 }}>
                            {[
                                { label: 'Total temas', value: blogTopics.length, color: '#6366f1', bg: '#ede9fe' },
                                { label: 'Publicados', value: publicados, color: '#16a34a', bg: '#dcfce7' },
                                { label: 'Pendientes', value: pendientes, color: '#d97706', bg: '#fef3c7' },
                            ].map(c => (
                                <div key={c.label} style={{ background: c.bg, borderRadius: 10, padding: '14px 16px' }}>
                                    <div style={{ fontSize: 12, fontWeight: 600, color: c.color }}>{c.label}</div>
                                    <div style={{ fontSize: 26, fontWeight: 800, color: c.color, marginTop: 4 }}>{c.value}</div>
                                </div>
                            ))}
                        </div>

                        {/* Filtro */}
                        <div style={{ display: 'flex', gap: 6, marginBottom: 16 }}>
                            {['todos', 'publicado', 'pendiente'].map(f => (
                                <button key={f} onClick={() => setBlogFiltro(f)} style={{ padding: '5px 14px', borderRadius: 20, border: '1px solid #e5e7eb', background: blogFiltro === f ? '#6366f1' : '#fff', color: blogFiltro === f ? '#fff' : '#374151', fontSize: 12, fontWeight: 600, cursor: 'pointer', textTransform: 'capitalize' }}>{f}</button>
                            ))}
                        </div>

                        {blogError && <div style={{ color: '#ef4444', background: '#fef2f2', border: '1px solid #fecaca', borderRadius: 8, padding: '12px 16px', marginBottom: 16, fontSize: 13 }}>{blogError}</div>}
                        {blogLoading && <div style={{ textAlign: 'center', color: '#9ca3af', padding: '40px 0' }}>Cargando desde GitHub...</div>}

                        {!blogLoading && !blogError && (
                            <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
                                {blogFiltrados.map(t => {
                                    const pub = publishedSlugs.has(t.slug);
                                    return (
                                        <div key={t.slug} style={{ display: 'flex', alignItems: 'center', gap: 14, background: '#fff', border: '1px solid #e5e7eb', borderRadius: 10, padding: '12px 16px' }}>
                                            <div style={{ flexShrink: 0 }}>
                                                {pub ? <CheckCircle2 size={20} color="#16a34a" /> : <Clock size={20} color="#d97706" />}
                                            </div>
                                            <div style={{ flex: 1, minWidth: 0 }}>
                                                <div style={{ fontWeight: 600, fontSize: 13, color: '#111', lineHeight: 1.3, marginBottom: 3 }}>{t.titulo}</div>
                                                <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap' }}>
                                                    <span style={{ background: '#ede9fe', color: '#7c3aed', borderRadius: 20, padding: '1px 8px', fontSize: 11, fontWeight: 600 }}>{t.categoria}</span>
                                                    {t.industria && <span style={{ background: '#f0fdf4', color: '#15803d', borderRadius: 20, padding: '1px 8px', fontSize: 11, fontWeight: 600 }}>{t.industria}</span>}
                                                    <span style={{ background: '#f9fafb', color: '#6b7280', borderRadius: 20, padding: '1px 8px', fontSize: 11 }}>{t.keyword}</span>
                                                </div>
                                            </div>
                                            <div style={{ flexShrink: 0, display: 'flex', alignItems: 'center', gap: 8 }}>
                                                <span style={{ fontSize: 11, fontWeight: 700, padding: '3px 10px', borderRadius: 20, background: pub ? '#dcfce7' : '#fef3c7', color: pub ? '#16a34a' : '#d97706' }}>
                                                    {pub ? 'Publicado' : 'Pendiente'}
                                                </span>
                                                {pub && (
                                                    <a href={`https://aicompanyco.com/blog/${t.slug}/`} target="_blank" rel="noopener noreferrer" style={{ color: '#9ca3af', display: 'flex' }}>
                                                        <ExternalLink size={14} />
                                                    </a>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                                {blogFiltrados.length === 0 && <div style={{ textAlign: 'center', color: '#9ca3af', padding: '30px 0', fontSize: 13 }}>Sin artículos en esta categoría</div>}
                            </div>
                        )}
                    </div>
                )}

                {/* ═══ TAB IDEAS ═══ */}
                {tab === 'ideas' && <>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                        <Lightbulb size={22} color="#6366f1" />
                        <h2 style={{ margin: 0, fontSize: '1.3rem', fontWeight: 700 }}>Ideas de Contenido</h2>
                    </div>
                    <div style={{ display: 'flex', gap: 8 }}>
                        <button onClick={() => setPanelIA(p => !p)} style={{ display: 'flex', alignItems: 'center', gap: 6, background: panelIA ? '#6366f1' : '#ede9fe', color: panelIA ? '#fff' : '#6366f1', border: 'none', borderRadius: 8, padding: '9px 16px', cursor: 'pointer', fontWeight: 600, fontSize: 13 }}>
                            <Sparkles size={15} /> Asistente IA
                        </button>
                        <button onClick={() => { setForm(empty); setEditId(null); setModal(true); }} style={{ display: 'flex', alignItems: 'center', gap: 6, background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 16px', cursor: 'pointer', fontWeight: 600, fontSize: 13 }}>
                            <Plus size={15} /> Nueva idea
                        </button>
                    </div>
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4,1fr)', gap: 12, marginBottom: 24 }}>
                    {Object.entries(ESTADOS).map(([e, { label, color, bg }]) => (
                        <div key={e} style={{ background: bg, borderRadius: 10, padding: '14px 16px', cursor: 'pointer', border: filtro === e ? `2px solid ${color}` : '2px solid transparent' }} onClick={() => setFiltro(filtro === e ? 'todas' : e)}>
                            <div style={{ fontSize: 13, fontWeight: 600, color }}>{label}</div>
                            <div style={{ fontSize: 24, fontWeight: 800, color, marginTop: 4 }}>{conteo[e]}</div>
                        </div>
                    ))}
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(260px,1fr))', gap: 14 }}>
                    {filtradas.length === 0 && <div style={{ gridColumn: '1/-1', textAlign: 'center', color: '#9ca3af', padding: '40px 0' }}>Sin ideas registradas</div>}
                    {filtradas.map(idea => {
                        const est = ESTADOS[idea.estado];
                        return (
                            <div key={idea.id} style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 18, display: 'flex', flexDirection: 'column', gap: 10 }}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                    <div style={{ fontWeight: 600, fontSize: '.95rem', flex: 1, lineHeight: 1.3 }}>{idea.titulo}</div>
                                    <div style={{ display: 'flex', gap: 4, flexShrink: 0 }}>
                                        <button onClick={() => editar(idea)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#9ca3af' }}><Edit2 size={13} /></button>
                                        <button onClick={() => eliminar(idea.id)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#ef4444' }}><X size={13} /></button>
                                    </div>
                                </div>
                                <div style={{ display: 'flex', gap: 5, flexWrap: 'wrap' }}>
                                    <span style={{ background: '#ede9fe', color: '#7c3aed', borderRadius: 20, padding: '2px 9px', fontSize: 11, fontWeight: 600 }}>{idea.canal}</span>
                                    <span style={{ background: '#f0fdf4', color: '#15803d', borderRadius: 20, padding: '2px 9px', fontSize: 11, fontWeight: 600 }}>{idea.formato}</span>
                                    <span style={{ background: est.bg, color: est.color, borderRadius: 20, padding: '2px 9px', fontSize: 11, fontWeight: 600 }}>{est.label}</span>
                                </div>
                                {idea.descripcion && <div style={{ fontSize: 12, color: '#6b7280', lineHeight: 1.5 }}>{idea.descripcion}</div>}
                                {idea.fecha_publicacion && <div style={{ fontSize: 11, color: '#9ca3af' }}>📅 {idea.fecha_publicacion}</div>}
                                <div style={{ display: 'flex', gap: 5, marginTop: 4 }}>
                                    {idea.estado === 'idea' && <button onClick={() => avanzar(idea.id, 'en_progreso')} style={{ flex: 1, fontSize: 12, padding: '5px', background: '#ede9fe', color: '#6366f1', border: 'none', borderRadius: 6, cursor: 'pointer', fontWeight: 600 }}>→ En progreso</button>}
                                    {idea.estado === 'en_progreso' && <button onClick={() => avanzar(idea.id, 'publicado')} style={{ flex: 1, fontSize: 12, padding: '5px', background: '#dcfce7', color: '#16a34a', border: 'none', borderRadius: 6, cursor: 'pointer', fontWeight: 600 }}>✓ Publicar</button>}
                                    {idea.estado !== 'descartado' && idea.estado !== 'publicado' && <button onClick={() => avanzar(idea.id, 'descartado')} style={{ fontSize: 12, padding: '5px 10px', background: '#f3f4f6', color: '#9ca3af', border: 'none', borderRadius: 6, cursor: 'pointer' }}>Descartar</button>}
                                </div>
                            </div>
                        );
                    })}
                </div>
                </>}
            </div>

            {/* Panel IA — solo en tab ideas */}
            {tab === 'ideas' && panelIA && (
                <div style={{ width: 360, borderLeft: '1px solid #e5e7eb', display: 'flex', flexDirection: 'column', background: '#fff', flexShrink: 0 }}>
                    <div style={{ padding: '16px 20px', borderBottom: '1px solid #e5e7eb', background: 'linear-gradient(135deg,#6366f1,#8b5cf6)' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 8, color: '#fff' }}>
                            <Bot size={18} />
                            <div>
                                <div style={{ fontWeight: 700, fontSize: 14 }}>Asistente de Contenido</div>
                                <div style={{ fontSize: 11, opacity: .8 }}>Conoce tus metas y estrategias</div>
                            </div>
                        </div>
                    </div>
                    <div style={{ flex: 1, overflowY: 'auto', padding: 16, display: 'flex', flexDirection: 'column', gap: 12 }}>
                        {historial.length === 0 && (
                            <div>
                                <div style={{ textAlign: 'center', color: '#9ca3af', fontSize: 13, marginBottom: 16 }}>
                                    <Sparkles size={28} color="#6366f1" style={{ marginBottom: 8 }} />
                                    <div style={{ fontWeight: 600, color: '#374151' }}>¡Hola! Soy tu asistente de marketing.</div>
                                    <div style={{ marginTop: 4 }}>¿En qué te ayudo?</div>
                                </div>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
                                    {SUGERENCIAS_RAPIDAS.map((s, i) => (
                                        <button key={i} onClick={() => enviarIA(s)} style={{ textAlign: 'left', padding: '8px 12px', background: '#f9fafb', border: '1px solid #e5e7eb', borderRadius: 8, cursor: 'pointer', fontSize: 12, color: '#374151', lineHeight: 1.4 }}>{s}</button>
                                    ))}
                                </div>
                            </div>
                        )}
                        {historial.map((msg, i) => (
                            <div key={i} style={{ display: 'flex', justifyContent: msg.role === 'user' ? 'flex-end' : 'flex-start' }}>
                                <div style={{ maxWidth: '85%', padding: '10px 13px', borderRadius: msg.role === 'user' ? '12px 12px 2px 12px' : '12px 12px 12px 2px', background: msg.role === 'user' ? '#6366f1' : '#f3f4f6', color: msg.role === 'user' ? '#fff' : '#111', fontSize: 13, lineHeight: 1.5, whiteSpace: 'pre-wrap' }}>
                                    {msg.content}
                                </div>
                            </div>
                        ))}
                        {cargandoIA && (
                            <div style={{ display: 'flex', gap: 4, padding: '10px 13px', background: '#f3f4f6', borderRadius: '12px 12px 12px 2px', width: 'fit-content' }}>
                                {[0, 1, 2].map(i => <div key={i} style={{ width: 7, height: 7, borderRadius: '50%', background: '#9ca3af', animation: `bounce .8s ${i * .15}s infinite` }} />)}
                            </div>
                        )}
                        <div ref={chatEndRef} />
                    </div>
                    <div style={{ padding: 12, borderTop: '1px solid #e5e7eb' }}>
                        <div style={{ display: 'flex', gap: 8 }}>
                            <input type="text" value={mensajeIA} onChange={e => setMensajeIA(e.target.value)} onKeyDown={e => e.key === 'Enter' && !e.shiftKey && enviarIA()} placeholder="Escribe tu pregunta..." disabled={cargandoIA} style={{ flex: 1, padding: '9px 12px', border: '1px solid #d1d5db', borderRadius: 8, fontSize: 13, outline: 'none' }} />
                            <button onClick={() => enviarIA()} disabled={cargandoIA || !mensajeIA.trim()} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '0 14px', cursor: 'pointer', opacity: (!mensajeIA.trim() || cargandoIA) ? 0.5 : 1 }}>
                                <Send size={16} />
                            </button>
                        </div>
                        {historial.length > 0 && (
                            <button onClick={() => setHistorial([])} style={{ marginTop: 6, fontSize: 11, color: '#9ca3af', background: 'none', border: 'none', cursor: 'pointer', width: '100%', textAlign: 'center' }}>
                                Limpiar conversación
                            </button>
                        )}
                    </div>
                </div>
            )}

            {/* Modal */}
            {modal && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,.4)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100 }} onClick={() => setModal(false)}>
                    <div style={{ background: '#fff', borderRadius: 12, padding: 28, width: 480, maxHeight: '90vh', overflowY: 'auto' }} onClick={e => e.stopPropagation()}>
                        <h3 style={{ margin: '0 0 20px', fontSize: '1.1rem' }}>{editId ? 'Editar' : 'Nueva'} idea</h3>
                        <form onSubmit={guardar}>
                            <div style={{ marginBottom: 14 }}>
                                <label style={labelStyle}>Título *</label>
                                <input type="text" value={form.titulo} onChange={e => setForm(f => ({ ...f, titulo: e.target.value }))} required style={inputStyle} />
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 14 }}>
                                <div>
                                    <label style={labelStyle}>Canal</label>
                                    <select value={form.canal} onChange={e => setForm(f => ({ ...f, canal: e.target.value }))} style={selectStyle}>
                                        {CANALES.map(c => <option key={c}>{c}</option>)}
                                    </select>
                                </div>
                                <div>
                                    <label style={labelStyle}>Formato</label>
                                    <select value={form.formato} onChange={e => setForm(f => ({ ...f, formato: e.target.value }))} style={selectStyle}>
                                        {FORMATOS.map(f => <option key={f}>{f}</option>)}
                                    </select>
                                </div>
                            </div>
                            <div style={{ marginBottom: 14 }}>
                                <label style={labelStyle}>Descripción / Guión</label>
                                <textarea value={form.descripcion} onChange={e => setForm(f => ({ ...f, descripcion: e.target.value }))} rows={4} placeholder="Describe la idea, el guión, el mensaje clave..." style={{ ...inputStyle, resize: 'vertical' }} />
                            </div>
                            <div style={{ marginBottom: 20 }}>
                                <label style={labelStyle}>Fecha de publicación</label>
                                <input type="date" value={form.fecha_publicacion} onChange={e => setForm(f => ({ ...f, fecha_publicacion: e.target.value }))} style={inputStyle} />
                            </div>
                            <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
                                <button type="button" onClick={() => setModal(false)} style={{ padding: '9px 18px', border: '1px solid #d1d5db', borderRadius: 8, background: '#fff', cursor: 'pointer' }}>Cancelar</button>
                                <button type="submit" style={{ padding: '9px 18px', background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, cursor: 'pointer', fontWeight: 600 }}>Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            <style>{`@keyframes bounce { 0%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-6px)} }`}</style>
        </div>
    );
}

const labelStyle = { display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5, color: '#374151' };
const inputStyle = { width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' };
const selectStyle = { width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14 };
