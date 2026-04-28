import { useEffect, useState } from 'react';
import axios from '../api/axios';
import { Calendar, Plus, X, ChevronLeft, ChevronRight, Clock, Users, ExternalLink, Bell } from 'lucide-react';

const DIAS = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
const MESES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
const COLORES = ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#ec4899'];

const emptyEvento = { titulo: '', descripcion: '', fecha_inicio: '', fecha_fin: '', todo_el_dia: false, color: '#6366f1', recordatorio: false, participantes: '', link: '' };

function formatHora(fecha) {
    if (!fecha) return '';
    return new Date(fecha).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
}

function isSameDay(d1, d2) {
    return d1.getFullYear() === d2.getFullYear() && d1.getMonth() === d2.getMonth() && d1.getDate() === d2.getDate();
}

export default function Calendario() {
    const [hoy] = useState(new Date());
    const [vistaFecha, setVistaFecha] = useState(new Date());
    const [vista, setVista] = useState('mes'); // mes | semana | agenda
    const [eventos, setEventos] = useState([]);
    const [modal, setModal] = useState(false);
    const [form, setForm] = useState(emptyEvento);
    const [editId, setEditId] = useState(null);
    const [diaSeleccionado, setDiaSeleccionado] = useState(null);
    const [eventoDetalle, setEventoDetalle] = useState(null);

    useEffect(() => { cargar(); }, [vistaFecha]);

    async function cargar() {
        const year = vistaFecha.getFullYear();
        const month = vistaFecha.getMonth();
        const desde = new Date(year, month - 1, 1).toISOString();
        const hasta = new Date(year, month + 2, 0).toISOString();
        const { data } = await axios.get(`/eventos?desde=${desde}&hasta=${hasta}`);
        setEventos(data);
    }

    async function guardar(e) {
        e.preventDefault();
        const payload = { ...form };
        if (form.todo_el_dia) {
            payload.fecha_inicio = form.fecha_inicio + 'T00:00:00';
            payload.fecha_fin = form.fecha_inicio + 'T23:59:59';
        }
        if (editId) await axios.put(`/eventos/${editId}`, payload);
        else await axios.post('/eventos', payload);
        setModal(false); setForm(emptyEvento); setEditId(null); cargar();
    }

    async function eliminar(id) {
        if (!confirm('¿Eliminar evento?')) return;
        await axios.delete(`/eventos/${id}`);
        setEventoDetalle(null); cargar();
    }

    function abrirModal(fecha = null) {
        const f = fecha ? fecha.toISOString().slice(0, 10) : new Date().toISOString().slice(0, 10);
        setForm({ ...emptyEvento, fecha_inicio: f + 'T09:00', fecha_fin: f + 'T10:00' });
        setEditId(null); setModal(true);
    }

    function editarEvento(ev) {
        setForm({
            titulo: ev.titulo, descripcion: ev.descripcion || '',
            fecha_inicio: ev.fecha_inicio?.slice(0, 16) || '',
            fecha_fin: ev.fecha_fin?.slice(0, 16) || '',
            todo_el_dia: ev.todo_el_dia || false, color: ev.color || '#6366f1',
            recordatorio: ev.recordatorio || false,
            participantes: ev.participantes || '', link: ev.link || ''
        });
        setEditId(ev.id); setEventoDetalle(null); setModal(true);
    }

    // Calcular días del mes para la vista
    const año = vistaFecha.getFullYear();
    const mes = vistaFecha.getMonth();
    const primerDia = new Date(año, mes, 1).getDay();
    const diasEnMes = new Date(año, mes + 1, 0).getDate();
    const celdasCalendario = [];
    for (let i = 0; i < primerDia; i++) celdasCalendario.push(null);
    for (let d = 1; d <= diasEnMes; d++) celdasCalendario.push(new Date(año, mes, d));

    function eventosDelDia(fecha) {
        if (!fecha) return [];
        return eventos.filter(ev => {
            const fi = new Date(ev.fecha_inicio);
            return isSameDay(fi, fecha);
        });
    }

    // Vista semana
    const inicioSemana = (() => {
        const d = new Date(vistaFecha);
        d.setDate(d.getDate() - d.getDay());
        return d;
    })();
    const diasSemana = Array.from({ length: 7 }, (_, i) => {
        const d = new Date(inicioSemana);
        d.setDate(inicioSemana.getDate() + i);
        return d;
    });

    function navMes(dir) {
        setVistaFecha(v => new Date(v.getFullYear(), v.getMonth() + dir, 1));
    }
    function navSemana(dir) {
        setVistaFecha(v => { const d = new Date(v); d.setDate(d.getDate() + dir * 7); return d; });
    }
    function irHoy() { setVistaFecha(new Date()); }

    const proximosEventos = eventos
        .filter(ev => new Date(ev.fecha_inicio) >= hoy)
        .sort((a, b) => new Date(a.fecha_inicio) - new Date(b.fecha_inicio))
        .slice(0, 5);

    return (
        <div style={{ padding: 28, maxWidth: 1200, margin: '0 auto' }}>
            {/* Header */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <Calendar size={22} color="#6366f1" />
                    <h2 style={{ margin: 0, fontSize: '1.3rem', fontWeight: 700 }}>Calendario</h2>
                </div>
                <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
                    <div style={{ display: 'flex', border: '1px solid #e5e7eb', borderRadius: 8, overflow: 'hidden' }}>
                        {[['mes', 'Mes'], ['semana', 'Semana'], ['agenda', 'Agenda']].map(([v, l]) => (
                            <button key={v} onClick={() => setVista(v)} style={{ padding: '6px 14px', border: 'none', background: vista === v ? '#6366f1' : '#fff', color: vista === v ? '#fff' : '#374151', cursor: 'pointer', fontSize: 13, fontWeight: vista === v ? 600 : 400 }}>{l}</button>
                        ))}
                    </div>
                    <button onClick={() => abrirModal()} style={{ display: 'flex', alignItems: 'center', gap: 6, background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, padding: '8px 16px', cursor: 'pointer', fontWeight: 600, fontSize: 13 }}>
                        <Plus size={15} /> Nuevo evento
                    </button>
                </div>
            </div>

            <div style={{ display: 'flex', gap: 20 }}>
                {/* Calendario principal */}
                <div style={{ flex: 1 }}>
                    {/* Navegación */}
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16, background: '#fff', border: '1px solid #e5e7eb', borderRadius: 10, padding: '10px 16px' }}>
                        <button onClick={() => vista === 'semana' ? navSemana(-1) : navMes(-1)} style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 4 }}><ChevronLeft size={18} /></button>
                        <div style={{ display: 'flex', gap: 12, alignItems: 'center' }}>
                            <span style={{ fontWeight: 700, fontSize: '1rem' }}>
                                {vista === 'semana'
                                    ? `${DIAS[inicioSemana.getDay()]} ${inicioSemana.getDate()} — ${DIAS[diasSemana[6].getDay()]} ${diasSemana[6].getDate()} ${MESES[diasSemana[6].getMonth()]} ${diasSemana[6].getFullYear()}`
                                    : `${MESES[mes]} ${año}`}
                            </span>
                            <button onClick={irHoy} style={{ padding: '3px 10px', border: '1px solid #e5e7eb', borderRadius: 6, background: '#f9fafb', cursor: 'pointer', fontSize: 12 }}>Hoy</button>
                        </div>
                        <button onClick={() => vista === 'semana' ? navSemana(1) : navMes(1)} style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 4 }}><ChevronRight size={18} /></button>
                    </div>

                    {/* Vista Mes */}
                    {vista === 'mes' && (
                        <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, overflow: 'hidden' }}>
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(7,1fr)', background: '#f9fafb', borderBottom: '1px solid #e5e7eb' }}>
                                {DIAS.map(d => <div key={d} style={{ textAlign: 'center', padding: '8px 0', fontSize: 12, fontWeight: 700, color: '#6b7280' }}>{d}</div>)}
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(7,1fr)' }}>
                                {celdasCalendario.map((fecha, i) => {
                                    const evs = eventosDelDia(fecha);
                                    const esHoy = fecha && isSameDay(fecha, hoy);
                                    const esSel = fecha && diaSeleccionado && isSameDay(fecha, diaSeleccionado);
                                    return (
                                        <div key={i} onClick={() => { if (fecha) { setDiaSeleccionado(fecha); abrirModal(fecha); } }} style={{ minHeight: 90, padding: '6px 8px', borderRight: '1px solid #f3f4f6', borderBottom: '1px solid #f3f4f6', cursor: fecha ? 'pointer' : 'default', background: esSel ? '#ede9fe' : fecha ? '#fff' : '#fafafa', transition: 'background .1s' }}>
                                            {fecha && (
                                                <>
                                                    <div style={{ width: 26, height: 26, borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', background: esHoy ? '#6366f1' : 'transparent', color: esHoy ? '#fff' : '#111', fontWeight: esHoy ? 700 : 400, fontSize: 13, marginBottom: 4 }}>
                                                        {fecha.getDate()}
                                                    </div>
                                                    {evs.slice(0, 3).map(ev => (
                                                        <div key={ev.id} onClick={e => { e.stopPropagation(); setEventoDetalle(ev); }} style={{ background: ev.color || '#6366f1', color: '#fff', borderRadius: 4, padding: '1px 6px', fontSize: 11, marginBottom: 2, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis', fontWeight: 500 }}>
                                                            {!ev.todo_el_dia && formatHora(ev.fecha_inicio) + ' '}{ev.titulo}
                                                        </div>
                                                    ))}
                                                    {evs.length > 3 && <div style={{ fontSize: 10, color: '#9ca3af' }}>+{evs.length - 3} más</div>}
                                                </>
                                            )}
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    {/* Vista Semana */}
                    {vista === 'semana' && (
                        <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, overflow: 'hidden' }}>
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(7,1fr)', background: '#f9fafb', borderBottom: '1px solid #e5e7eb' }}>
                                {diasSemana.map((d, i) => {
                                    const esHoy = isSameDay(d, hoy);
                                    return (
                                        <div key={i} style={{ textAlign: 'center', padding: '10px 0', cursor: 'pointer' }} onClick={() => abrirModal(d)}>
                                            <div style={{ fontSize: 11, color: '#9ca3af', fontWeight: 600 }}>{DIAS[d.getDay()]}</div>
                                            <div style={{ width: 32, height: 32, borderRadius: '50%', background: esHoy ? '#6366f1' : 'transparent', color: esHoy ? '#fff' : '#111', fontWeight: esHoy ? 700 : 500, fontSize: 15, display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '2px auto 0' }}>{d.getDate()}</div>
                                        </div>
                                    );
                                })}
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(7,1fr)', minHeight: 300 }}>
                                {diasSemana.map((d, i) => {
                                    const evs = eventosDelDia(d);
                                    return (
                                        <div key={i} style={{ padding: 6, borderRight: i < 6 ? '1px solid #f3f4f6' : 'none', minHeight: 200, cursor: 'pointer' }} onClick={() => abrirModal(d)}>
                                            {evs.map(ev => (
                                                <div key={ev.id} onClick={e => { e.stopPropagation(); setEventoDetalle(ev); }} style={{ background: ev.color || '#6366f1', color: '#fff', borderRadius: 6, padding: '4px 8px', fontSize: 12, marginBottom: 4, cursor: 'pointer' }}>
                                                    <div style={{ fontWeight: 600, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{ev.titulo}</div>
                                                    {!ev.todo_el_dia && <div style={{ fontSize: 10, opacity: .8 }}>{formatHora(ev.fecha_inicio)}</div>}
                                                </div>
                                            ))}
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    {/* Vista Agenda */}
                    {vista === 'agenda' && (
                        <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
                            {eventos.length === 0 && <div style={{ textAlign: 'center', color: '#9ca3af', padding: '40px 0' }}>Sin eventos este mes</div>}
                            {eventos.sort((a, b) => new Date(a.fecha_inicio) - new Date(b.fecha_inicio)).map(ev => (
                                <div key={ev.id} style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 10, padding: 16, display: 'flex', gap: 14, alignItems: 'flex-start', borderLeft: `4px solid ${ev.color || '#6366f1'}` }}>
                                    <div style={{ flex: 1 }}>
                                        <div style={{ fontWeight: 600, fontSize: '.95rem', marginBottom: 4 }}>{ev.titulo}</div>
                                        <div style={{ display: 'flex', gap: 12, fontSize: 13, color: '#6b7280', flexWrap: 'wrap' }}>
                                            <span><Clock size={13} style={{ verticalAlign: 'middle' }} /> {new Date(ev.fecha_inicio).toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long' })}{!ev.todo_el_dia && ' — ' + formatHora(ev.fecha_inicio)}</span>
                                            {ev.participantes && <span><Users size={13} style={{ verticalAlign: 'middle' }} /> {ev.participantes}</span>}
                                        </div>
                                        {ev.descripcion && <div style={{ fontSize: 12, color: '#9ca3af', marginTop: 4 }}>{ev.descripcion}</div>}
                                        {ev.link && <a href={ev.link} target="_blank" rel="noreferrer" style={{ fontSize: 12, color: '#6366f1', display: 'flex', alignItems: 'center', gap: 4, marginTop: 4 }}><ExternalLink size={12} /> Unirse</a>}
                                        {ev.recordatorio && <div style={{ fontSize: 11, color: '#f59e0b', marginTop: 4 }}><Bell size={11} style={{ verticalAlign: 'middle' }} /> Recordatorio por Telegram activado</div>}
                                    </div>
                                    <div style={{ display: 'flex', gap: 6 }}>
                                        <button onClick={() => editarEvento(ev)} style={{ fontSize: 12, padding: '4px 10px', background: '#f3f4f6', border: 'none', borderRadius: 6, cursor: 'pointer' }}>Editar</button>
                                        <button onClick={() => eliminar(ev.id)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#ef4444' }}><X size={15} /></button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                {/* Panel lateral */}
                <div style={{ width: 260, flexShrink: 0 }}>
                    <div style={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: 12, padding: 16, marginBottom: 16 }}>
                        <div style={{ fontWeight: 700, fontSize: 13, marginBottom: 12, color: '#374151' }}>📅 Próximos eventos</div>
                        {proximosEventos.length === 0 && <div style={{ fontSize: 13, color: '#9ca3af' }}>Sin eventos próximos</div>}
                        {proximosEventos.map(ev => (
                            <div key={ev.id} onClick={() => setEventoDetalle(ev)} style={{ display: 'flex', gap: 10, alignItems: 'flex-start', padding: '8px 0', borderBottom: '1px solid #f3f4f6', cursor: 'pointer' }}>
                                <div style={{ width: 3, alignSelf: 'stretch', borderRadius: 2, background: ev.color || '#6366f1', flexShrink: 0 }} />
                                <div>
                                    <div style={{ fontSize: 13, fontWeight: 600 }}>{ev.titulo}</div>
                                    <div style={{ fontSize: 11, color: '#9ca3af' }}>{new Date(ev.fecha_inicio).toLocaleDateString('es-CO', { day: 'numeric', month: 'short' })}{!ev.todo_el_dia && ' ' + formatHora(ev.fecha_inicio)}</div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Detalle de evento */}
            {eventoDetalle && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,.3)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100 }} onClick={() => setEventoDetalle(null)}>
                    <div style={{ background: '#fff', borderRadius: 12, padding: 24, width: 400, boxShadow: '0 20px 60px rgba(0,0,0,.2)' }} onClick={e => e.stopPropagation()}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 16 }}>
                            <div style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                                <div style={{ width: 14, height: 14, borderRadius: '50%', background: eventoDetalle.color || '#6366f1' }} />
                                <div style={{ fontWeight: 700, fontSize: '1rem' }}>{eventoDetalle.titulo}</div>
                            </div>
                            <button onClick={() => setEventoDetalle(null)} style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#9ca3af' }}><X size={18} /></button>
                        </div>
                        <div style={{ fontSize: 13, color: '#6b7280', marginBottom: 8 }}>
                            <Clock size={13} style={{ verticalAlign: 'middle', marginRight: 6 }} />
                            {new Date(eventoDetalle.fecha_inicio).toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}
                            {!eventoDetalle.todo_el_dia && ` — ${formatHora(eventoDetalle.fecha_inicio)}`}
                        </div>
                        {eventoDetalle.participantes && <div style={{ fontSize: 13, color: '#6b7280', marginBottom: 8 }}><Users size={13} style={{ verticalAlign: 'middle', marginRight: 6 }} />{eventoDetalle.participantes}</div>}
                        {eventoDetalle.descripcion && <div style={{ fontSize: 13, color: '#374151', marginBottom: 12, lineHeight: 1.5 }}>{eventoDetalle.descripcion}</div>}
                        {eventoDetalle.link && <a href={eventoDetalle.link} target="_blank" rel="noreferrer" style={{ display: 'flex', alignItems: 'center', gap: 6, fontSize: 13, color: '#6366f1', fontWeight: 600, marginBottom: 12 }}><ExternalLink size={14} /> Unirse al enlace</a>}
                        {eventoDetalle.recordatorio && <div style={{ fontSize: 12, color: '#f59e0b', marginBottom: 12 }}><Bell size={12} style={{ verticalAlign: 'middle' }} /> Recordatorio por Telegram activo</div>}
                        <div style={{ display: 'flex', gap: 8, marginTop: 4 }}>
                            <button onClick={() => editarEvento(eventoDetalle)} style={{ flex: 1, padding: '8px', background: '#f3f4f6', border: 'none', borderRadius: 8, cursor: 'pointer', fontWeight: 600, fontSize: 13 }}>Editar</button>
                            <button onClick={() => eliminar(eventoDetalle.id)} style={{ flex: 1, padding: '8px', background: '#fee2e2', color: '#ef4444', border: 'none', borderRadius: 8, cursor: 'pointer', fontWeight: 600, fontSize: 13 }}>Eliminar</button>
                        </div>
                    </div>
                </div>
            )}

            {/* Modal nuevo/editar evento */}
            {modal && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,.4)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 200 }} onClick={() => setModal(false)}>
                    <div style={{ background: '#fff', borderRadius: 12, padding: 28, width: 500, maxHeight: '90vh', overflowY: 'auto' }} onClick={e => e.stopPropagation()}>
                        <h3 style={{ margin: '0 0 20px', fontSize: '1.1rem' }}>{editId ? 'Editar' : 'Nuevo'} evento</h3>
                        <form onSubmit={guardar}>
                            <div style={{ marginBottom: 14 }}>
                                <label style={labelStyle}>Título *</label>
                                <input type="text" required value={form.titulo} onChange={e => setForm(f => ({ ...f, titulo: e.target.value }))} style={inputStyle} />
                            </div>
                            <div style={{ marginBottom: 14 }}>
                                <label style={{ display: 'flex', gap: 8, alignItems: 'center', fontSize: 13, cursor: 'pointer' }}>
                                    <input type="checkbox" checked={form.todo_el_dia} onChange={e => setForm(f => ({ ...f, todo_el_dia: e.target.checked }))} />
                                    Todo el día
                                </label>
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 14 }}>
                                <div>
                                    <label style={labelStyle}>{form.todo_el_dia ? 'Fecha *' : 'Inicio *'}</label>
                                    <input type={form.todo_el_dia ? 'date' : 'datetime-local'} required value={form.todo_el_dia ? form.fecha_inicio?.slice(0,10) : form.fecha_inicio} onChange={e => setForm(f => ({ ...f, fecha_inicio: e.target.value }))} style={inputStyle} />
                                </div>
                                {!form.todo_el_dia && (
                                    <div>
                                        <label style={labelStyle}>Fin</label>
                                        <input type="datetime-local" value={form.fecha_fin} onChange={e => setForm(f => ({ ...f, fecha_fin: e.target.value }))} style={inputStyle} />
                                    </div>
                                )}
                            </div>
                            <div style={{ marginBottom: 14 }}>
                                <label style={labelStyle}>Participantes</label>
                                <input type="text" value={form.participantes} onChange={e => setForm(f => ({ ...f, participantes: e.target.value }))} placeholder="Ej: Juan, María" style={inputStyle} />
                            </div>
                            <div style={{ marginBottom: 14 }}>
                                <label style={labelStyle}>Link (Meet, Zoom…)</label>
                                <input type="url" value={form.link} onChange={e => setForm(f => ({ ...f, link: e.target.value }))} placeholder="https://..." style={inputStyle} />
                            </div>
                            <div style={{ marginBottom: 14 }}>
                                <label style={labelStyle}>Descripción</label>
                                <textarea value={form.descripcion} onChange={e => setForm(f => ({ ...f, descripcion: e.target.value }))} rows={2} style={{ ...inputStyle, resize: 'vertical' }} />
                            </div>
                            <div style={{ marginBottom: 14 }}>
                                <label style={labelStyle}>Color</label>
                                <div style={{ display: 'flex', gap: 8 }}>
                                    {COLORES.map(c => (
                                        <div key={c} onClick={() => setForm(f => ({ ...f, color: c }))} style={{ width: 24, height: 24, borderRadius: '50%', background: c, cursor: 'pointer', border: form.color === c ? '3px solid #111' : '2px solid transparent' }} />
                                    ))}
                                </div>
                            </div>
                            <div style={{ marginBottom: 20 }}>
                                <label style={{ display: 'flex', gap: 8, alignItems: 'center', fontSize: 13, cursor: 'pointer', background: '#fef3c7', padding: '8px 12px', borderRadius: 8 }}>
                                    <input type="checkbox" checked={form.recordatorio} onChange={e => setForm(f => ({ ...f, recordatorio: e.target.checked }))} />
                                    <Bell size={14} color="#f59e0b" /> <span>Enviar recordatorio por Telegram</span>
                                </label>
                            </div>
                            <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
                                <button type="button" onClick={() => setModal(false)} style={{ padding: '9px 18px', border: '1px solid #d1d5db', borderRadius: 8, background: '#fff', cursor: 'pointer' }}>Cancelar</button>
                                <button type="submit" style={{ padding: '9px 18px', background: '#6366f1', color: '#fff', border: 'none', borderRadius: 8, cursor: 'pointer', fontWeight: 600 }}>Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

const labelStyle = { display: 'block', fontSize: 13, fontWeight: 600, marginBottom: 5, color: '#374151' };
const inputStyle = { width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 7, fontSize: 14, boxSizing: 'border-box' };
