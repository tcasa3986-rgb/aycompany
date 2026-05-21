import React, { useEffect, useState, useCallback } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import {
  ArrowLeft, Phone, Mail, MessageCircle, Plus, Edit2, MapPin,
  Calendar, User, Star, TrendingUp, DollarSign, BookOpen,
  CheckSquare, Clock, Briefcase, ChevronRight, X, Send, Target,
  Activity, AlertCircle, FileText, Globe
} from 'lucide-react';
import api from '../services/api';
import { format, formatDistanceToNow } from 'date-fns';
import { es } from 'date-fns/locale';
import toast from 'react-hot-toast';
import useConfigStore from '../store/configStore';


// ─── Helpers ──────────────────────────────────────────────────
const gradients = [
  'linear-gradient(135deg,#7C3AED,#EC4899)',
  'linear-gradient(135deg,#0EA5E9,#06B6D4)',
  'linear-gradient(135deg,#10B981,#06B6D4)',
  'linear-gradient(135deg,#F59E0B,#EF4444)',
  'linear-gradient(135deg,#6366F1,#8B5CF6)',
];
const avatarGradient = (id) => gradients[id % gradients.length];
const initiales = (n, a) => `${n?.[0]||''}${a?.[0]||''}`.toUpperCase();

const catStyle = {
  VIP:        { bg: '#FEF3C7', color: '#92400E', icon: '⭐' },
  Recurrente: { bg: '#D1FAE5', color: '#065F46', icon: '🔄' },
  Nuevo:      { bg: '#EFF6FF', color: '#1E40AF', icon: '🆕' },
  Inactivo:   { bg: '#F3F4F6', color: '#6B7280', icon: '😴' },
};

const tipoIcono = {
  Llamada:     { icon: <Phone size={13}/>,          color: '#3B82F6', bg: '#EFF6FF', label: 'Llamada' },
  Email:       { icon: <Mail size={13}/>,           color: '#8B5CF6', bg: '#F5F3FF', label: 'Email' },
  WhatsApp:    { icon: <MessageCircle size={13}/>,  color: '#10B981', bg: '#ECFDF5', label: 'WhatsApp' },
  Reunion:     { icon: <User size={13}/>,           color: '#F59E0B', bg: '#FFFBEB', label: 'Reunión' },
  Nota:        { icon: <FileText size={13}/>,       color: '#6B7280', bg: '#F9FAFB', label: 'Nota' },
  Cotizacion:  { icon: <DollarSign size={13}/>,     color: '#EC4899', bg: '#FDF2F8', label: 'Cotización' },
  Seguimiento: { icon: <Activity size={13}/>,       color: '#0EA5E9', bg: '#EFF8FF', label: 'Seguimiento' },
};

const estadoRes = {
  Pendiente:  { color: '#F59E0B', bg: '#FFFBEB' },
  Confirmada: { color: '#3B82F6', bg: '#EFF6FF' },
  'En Curso': { color: '#8B5CF6', bg: '#F5F3FF' },
  Completada: { color: '#10B981', bg: '#ECFDF5' },
  Cancelada:  { color: '#EF4444', bg: '#FEF2F2' },
};

const prioridadColor = {
  Urgente: '#EF4444', Alta: '#F59E0B', Media: '#8B5CF6', Baja: '#94A3B8'
};

// ─── Stat mini card ───────────────────────────────────────────
function StatCard({ icon, label, value, sub, accent }) {
  return (
    <div style={{
      background: '#FFFFFF', borderRadius: 14, padding: '16px 20px',
      border: '1px solid #E2E8F0', boxShadow: '0 2px 8px rgba(0,0,0,0.04)',
      display: 'flex', alignItems: 'center', gap: 14,
    }}>
      <div style={{
        width: 44, height: 44, borderRadius: 12,
        background: accent + '18', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0,
      }}>
        {React.cloneElement(icon, { size: 20, color: accent })}
      </div>
      <div>
        <div style={{ fontSize: '1.35rem', fontWeight: 800, color: '#0F172A', lineHeight: 1.1, fontFamily: 'Plus Jakarta Sans' }}>
          {value}
        </div>
        <div style={{ fontSize: '0.75rem', color: '#64748B', fontWeight: 500 }}>{label}</div>
        {sub && <div style={{ fontSize: '0.68rem', color: '#94A3B8', marginTop: 1 }}>{sub}</div>}
      </div>
    </div>
  );
}

// ─── Modal nueva interacción ──────────────────────────────────
function NuevaInteraccionModal({ clienteId, onClose, onSaved }) {
  const [form, setForm] = useState({ tipo: 'Llamada', descripcion: '' });
  const [saving, setSaving] = useState(false);
  const tipos = ['Llamada','Email','WhatsApp','Reunion','Nota','Cotizacion','Seguimiento'];
  const tipLabel = { Llamada:'Llamada', Email:'Email', WhatsApp:'WhatsApp', Reunion:'Reunión', Nota:'Nota', Cotizacion:'Cotización', Seguimiento:'Seguimiento' };

  const submit = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      await api.post(`/clientes/${clienteId}/interacciones`, form);
      toast.success('Interacción registrada');
      onSaved();
    } catch { toast.error('Error al guardar'); }
    finally { setSaving(false); }
  };

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal animate-fade-in-up" style={{ maxWidth: 480 }}>
        <div className="modal-header">
          <h2 className="modal-title">Nueva Interacción</h2>
          <button className="modal-close" onClick={onClose}><X size={18}/></button>
        </div>
        <form onSubmit={submit}>
          <div className="modal-body">
            <div className="form-group">
              <label className="form-label required">Tipo de Contacto</label>
              <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 8 }}>
                {tipos.map(t => {
                  const ti = tipoIcono[t];
                  const sel = form.tipo === t;
                  return (
                    <button key={t} type="button" onClick={() => setForm(f => ({ ...f, tipo: t }))}
                      style={{
                        padding: '10px 6px', borderRadius: 10, cursor: 'pointer', textAlign: 'center',
                        border: sel ? `2px solid ${ti.color}` : '2px solid #E2E8F0',
                        background: sel ? ti.bg : 'transparent', transition: 'all 0.15s',
                        display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 4,
                      }}>
                      <span style={{ color: ti.color }}>{ti.icon}</span>
                      <span style={{ fontSize: '0.65rem', fontWeight: 600, color: sel ? ti.color : '#64748B' }}>
                        {tipLabel[t]}
                      </span>
                    </button>
                  );
                })}
              </div>
            </div>
            <div className="form-group" style={{ marginTop: 16 }}>
              <label className="form-label required">Descripción</label>
              <textarea className="form-control" rows={4} required
                placeholder="Detalla lo que se trató en esta interacción..."
                value={form.descripcion} onChange={e => setForm(f => ({ ...f, descripcion: e.target.value }))} />
            </div>
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
            <button type="submit" className="btn btn-primary" disabled={saving}>
              <Send size={14}/> {saving ? 'Guardando...' : 'Registrar'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

// ─── Modal nueva tarea ────────────────────────────────────────
function NuevaTareaModal({ clienteId, agentes, onClose, onSaved }) {
  const [form, setForm] = useState({
    titulo: '', descripcion: '', prioridad: 'Media',
    asignado_a: '', fecha_vence: '',
  });
  const [saving, setSaving] = useState(false);

  const submit = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      await api.post('/tareas', { ...form, cliente_id: clienteId });
      toast.success('Tarea creada');
      onSaved();
    } catch { toast.error('Error al crear tarea'); }
    finally { setSaving(false); }
  };
  const set = (k, v) => setForm(f => ({ ...f, [k]: v }));

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal animate-fade-in-up" style={{ maxWidth: 480 }}>
        <div className="modal-header">
          <h2 className="modal-title">Nueva Tarea</h2>
          <button className="modal-close" onClick={onClose}><X size={18}/></button>
        </div>
        <form onSubmit={submit}>
          <div className="modal-body">
            <div className="form-group">
              <label className="form-label required">Título</label>
              <input className="form-control" required value={form.titulo} onChange={e => set('titulo', e.target.value)} placeholder="Ej: Llamar para confirmar reserva" />
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Prioridad</label>
                <select className="form-control" value={form.prioridad} onChange={e => set('prioridad', e.target.value)}>
                  {['Urgente','Alta','Media','Baja'].map(p => <option key={p}>{p}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Vence el</label>
                <input type="date" className="form-control" value={form.fecha_vence} onChange={e => set('fecha_vence', e.target.value)} />
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Asignar a</label>
              <select className="form-control" value={form.asignado_a} onChange={e => set('asignado_a', e.target.value)}>
                <option value="">Seleccionar agente</option>
                {agentes.map(a => <option key={a.id} value={a.id}>{a.nombre} {a.apellido}</option>)}
              </select>
            </div>
            <div className="form-group">
              <label className="form-label">Descripción</label>
              <textarea className="form-control" rows={3} value={form.descripcion} onChange={e => set('descripcion', e.target.value)} />
            </div>
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
            <button type="submit" className="btn btn-primary" disabled={saving}>
              <CheckSquare size={14}/> {saving ? 'Guardando...' : 'Crear Tarea'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

// ─── Tab: Historial ───────────────────────────────────────────
function TabHistorial({ interacciones, clienteId, onRefresh }) {
  const [showModal, setShowModal] = useState(false);
  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
        <div>
          <div style={{ fontWeight: 700, fontSize: '0.95rem', color: '#0F172A' }}>Historial de Interacciones</div>
          <div style={{ fontSize: '0.78rem', color: '#94A3B8' }}>{interacciones.length} interacciones registradas</div>
        </div>
        <button className="btn btn-primary" style={{ fontSize: '0.8rem', padding: '8px 16px' }}
          onClick={() => setShowModal(true)}>
          <Plus size={14}/> Nueva Interacción
        </button>
      </div>

      {interacciones.length === 0 ? (
        <div style={{ textAlign: 'center', padding: '40px 20px', color: '#94A3B8' }}>
          <MessageCircle size={40} style={{ margin: '0 auto 12px', opacity: 0.3 }} />
          <p style={{ fontWeight: 500 }}>Sin interacciones aún</p>
          <p style={{ fontSize: '0.8rem' }}>Registra la primera interacción con este cliente</p>
        </div>
      ) : (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
          {interacciones.sort((a, b) => new Date(b.fecha) - new Date(a.fecha)).map(int => {
            const ti = tipoIcono[int.tipo] || tipoIcono.Nota;
            return (
              <div key={int.id} style={{
                display: 'flex', gap: 14, padding: '14px 0',
                borderBottom: '1px solid #F1F5F9',
              }}>
                <div style={{
                  width: 36, height: 36, borderRadius: 10, background: ti.bg,
                  display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0,
                  color: ti.color, border: `1.5px solid ${ti.color}22`,
                }}>
                  {ti.icon}
                </div>
                <div style={{ flex: 1, minWidth: 0 }}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 4 }}>
                    <span style={{
                      fontSize: '0.7rem', fontWeight: 700, color: ti.color,
                      background: ti.bg, padding: '2px 8px', borderRadius: 20,
                    }}>{ti.label}</span>
                    <span style={{ fontSize: '0.7rem', color: '#94A3B8' }}>
                      {int.usuario?.nombre} {int.usuario?.apellido}
                    </span>
                  </div>
                  <p style={{ fontSize: '0.85rem', color: '#334155', margin: 0, lineHeight: 1.5 }}>
                    {int.descripcion}
                  </p>
                  <div style={{ fontSize: '0.7rem', color: '#94A3B8', marginTop: 6 }}>
                    <Clock size={10} style={{ display: 'inline', marginRight: 4 }} />
                    {int.fecha ? formatDistanceToNow(new Date(int.fecha), { addSuffix: true, locale: es }) : ''}
                    {' · '}
                    {int.fecha ? format(new Date(int.fecha), "d MMM yyyy 'a las' HH:mm", { locale: es }) : ''}
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      )}

      {showModal && (
        <NuevaInteraccionModal
          clienteId={clienteId}
          onClose={() => setShowModal(false)}
          onSaved={() => { setShowModal(false); onRefresh(); }}
        />
      )}
    </div>
  );
}

// ─── Tab: Oportunidades ───────────────────────────────────────
function TabOportunidades({ oportunidades }) {
  const estadoOp = {
    Activa:   { color: '#3B82F6', bg: '#EFF6FF' },
    Ganada:   { color: '#10B981', bg: '#ECFDF5' },
    Perdida:  { color: '#EF4444', bg: '#FEF2F2' },
    Cancelada:{ color: '#94A3B8', bg: '#F8FAFC' },
  };
  return (
    <div>
      <div style={{ fontWeight: 700, fontSize: '0.95rem', color: '#0F172A', marginBottom: 16 }}>
        Oportunidades en Pipeline <span style={{ fontSize: '0.78rem', fontWeight: 400, color: '#94A3B8' }}>({oportunidades.length} total)</span>
      </div>
      {oportunidades.length === 0 ? (
        <div style={{ textAlign: 'center', padding: '40px 20px', color: '#94A3B8' }}>
          <Target size={40} style={{ margin: '0 auto 12px', opacity: 0.3 }} />
          <p style={{ fontWeight: 500 }}>Sin oportunidades registradas</p>
        </div>
      ) : (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
          {oportunidades.map(op => {
            const est = estadoOp[op.estado] || estadoOp.Activa;
            return (
              <div key={op.id} style={{
                background: '#FAFAFA', border: '1px solid #E2E8F0',
                borderRadius: 12, padding: '14px 16px',
                display: 'flex', alignItems: 'center', gap: 14,
              }}>
                <div style={{
                  width: 40, height: 40, borderRadius: 10,
                  background: op.etapa?.color + '20' || '#F1F5F9',
                  display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0,
                }}>
                  <Target size={18} color={op.etapa?.color || '#94A3B8'} />
                </div>
                <div style={{ flex: 1 }}>
                  <div style={{ fontWeight: 600, fontSize: '0.88rem', color: '#0F172A' }}>{op.titulo}</div>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginTop: 4, flexWrap: 'wrap' }}>
                    {op.etapa && (
                      <span style={{ fontSize: '0.68rem', fontWeight: 700, color: op.etapa.color, background: op.etapa.color + '18', padding: '2px 8px', borderRadius: 20 }}>
                        {op.etapa.nombre}
                      </span>
                    )}
                    <span style={{ fontSize: '0.68rem', fontWeight: 700, color: est.color, background: est.bg, padding: '2px 8px', borderRadius: 20 }}>
                      {op.estado}
                    </span>
                    <span style={{ fontSize: '0.72rem', color: '#64748B' }}>
                      Probabilidad: {op.probabilidad}%
                    </span>
                  </div>
                </div>
                <div style={{ textAlign: 'right', flexShrink: 0 }}>
                  <div style={{ fontWeight: 800, fontSize: '1rem', color: '#0F172A' }}>
                    {m}{(+op.valor_estimado || 0).toLocaleString('es')}

                  </div>
                  <div style={{ fontSize: '0.68rem', color: '#94A3B8' }}>Valor estimado</div>
                </div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}

// ─── Tab: Reservas ────────────────────────────────────────────
function TabReservas({ reservas }) {
  return (
    <div>
      <div style={{ fontWeight: 700, fontSize: '0.95rem', color: '#0F172A', marginBottom: 16 }}>
        Historial de Reservas <span style={{ fontSize: '0.78rem', fontWeight: 400, color: '#94A3B8' }}>({reservas.length} total)</span>
      </div>
      {reservas.length === 0 ? (
        <div style={{ textAlign: 'center', padding: '40px 20px', color: '#94A3B8' }}>
          <BookOpen size={40} style={{ margin: '0 auto 12px', opacity: 0.3 }} />
          <p style={{ fontWeight: 500 }}>Sin reservas aún</p>
        </div>
      ) : (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
          {reservas.map(r => {
            const st = estadoRes[r.estado] || { color: '#94A3B8', bg: '#F8FAFC' };
            return (
              <div key={r.id} style={{
                background: '#FAFAFA', border: '1px solid #E2E8F0',
                borderRadius: 12, padding: '14px 16px',
                display: 'flex', alignItems: 'center', gap: 14,
              }}>
                <div style={{
                  width: 40, height: 40, borderRadius: 10, background: st.bg,
                  display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0,
                }}>
                  <Globe size={18} color={st.color} />
                </div>
                <div style={{ flex: 1 }}>
                  <div style={{ fontWeight: 600, fontSize: '0.88rem', color: '#0F172A' }}>
                    {r.paquete?.nombre || 'Reserva personalizada'}
                  </div>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginTop: 4, flexWrap: 'wrap' }}>
                    <span style={{ fontSize: '0.68rem', fontWeight: 600, color: '#7C3AED', background: '#F5F3FF', padding: '2px 8px', borderRadius: 20 }}>
                      {r.codigo_reserva}
                    </span>
                    <span style={{ fontSize: '0.68rem', fontWeight: 700, color: st.color, background: st.bg, padding: '2px 8px', borderRadius: 20 }}>
                      {r.estado}
                    </span>
                    {r.fecha_salida && (
                      <span style={{ fontSize: '0.72rem', color: '#64748B' }}>
                        <Calendar size={10} style={{ display: 'inline', marginRight: 3 }} />
                        {format(new Date(r.fecha_salida), 'd MMM yyyy', { locale: es })}
                      </span>
                    )}
                  </div>
                </div>
                <div style={{ textAlign: 'right', flexShrink: 0 }}>
                  <div style={{ fontWeight: 800, fontSize: '1rem', color: '#0F172A' }}>
                    {m}{(+r.total_final || 0).toLocaleString('es')}

                  </div>
                  <div style={{ fontSize: '0.68rem', color: '#94A3B8' }}>
                    {r.num_adultos} adulto{r.num_adultos !== 1 ? 's' : ''}
                    {r.num_ninos > 0 ? `, ${r.num_ninos} niño${r.num_ninos !== 1 ? 's' : ''}` : ''}
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}

// ─── Tab: Tareas ──────────────────────────────────────────────
function TabTareas({ tareas, clienteId, agentes, onRefresh }) {
  const [showModal, setShowModal] = useState(false);

  const completar = async (id) => {
    await api.put(`/tareas/${id}`, { estado: 'Completada' });
    toast.success('Tarea completada');
    onRefresh();
  };

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
        <div>
          <div style={{ fontWeight: 700, fontSize: '0.95rem', color: '#0F172A' }}>Tareas Asociadas</div>
          <div style={{ fontSize: '0.78rem', color: '#94A3B8' }}>{tareas.length} tareas</div>
        </div>
        <button className="btn btn-primary" style={{ fontSize: '0.8rem', padding: '8px 16px' }}
          onClick={() => setShowModal(true)}>
          <Plus size={14}/> Nueva Tarea
        </button>
      </div>

      {tareas.length === 0 ? (
        <div style={{ textAlign: 'center', padding: '40px 20px', color: '#94A3B8' }}>
          <CheckSquare size={40} style={{ margin: '0 auto 12px', opacity: 0.3 }} />
          <p style={{ fontWeight: 500 }}>Sin tareas asignadas</p>
        </div>
      ) : (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
          {tareas.map(t => (
            <div key={t.id} style={{
              background: '#FAFAFA', border: `1.5px solid ${t.estado === 'Completada' ? '#ECFDF5' : '#E2E8F0'}`,
              borderLeft: `4px solid ${prioridadColor[t.prioridad] || '#94A3B8'}`,
              borderRadius: '0 12px 12px 0', padding: '12px 16px',
              display: 'flex', alignItems: 'center', gap: 12,
              opacity: t.estado === 'Cancelada' ? 0.5 : 1,
            }}>
              {t.estado !== 'Completada' && t.estado !== 'Cancelada' && (
                <button onClick={() => completar(t.id)} style={{
                  width: 22, height: 22, borderRadius: '50%', border: '2px solid #CBD5E1',
                  background: 'transparent', cursor: 'pointer', flexShrink: 0,
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                }} title="Marcar como completada">
                  <div style={{ width: 10, height: 10, borderRadius: '50%', background: '#CBD5E1' }} />
                </button>
              )}
              {t.estado === 'Completada' && (
                <div style={{ width: 22, height: 22, borderRadius: '50%', background: '#10B981', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                  <span style={{ color: 'white', fontSize: '0.7rem' }}>✓</span>
                </div>
              )}
              <div style={{ flex: 1 }}>
                <div style={{ fontWeight: 600, fontSize: '0.88rem', color: t.estado === 'Completada' ? '#94A3B8' : '#0F172A', textDecoration: t.estado === 'Completada' ? 'line-through' : 'none' }}>
                  {t.titulo}
                </div>
                <div style={{ display: 'flex', gap: 8, marginTop: 4, alignItems: 'center', flexWrap: 'wrap' }}>
                  <span style={{ fontSize: '0.68rem', fontWeight: 700, color: prioridadColor[t.prioridad], background: prioridadColor[t.prioridad] + '18', padding: '2px 8px', borderRadius: 20 }}>
                    {t.prioridad}
                  </span>
                  {t.fecha_vence && (
                    <span style={{ fontSize: '0.71rem', color: '#64748B' }}>
                      <Clock size={10} style={{ display: 'inline', marginRight: 3 }} />
                      Vence: {format(new Date(t.fecha_vence), 'd MMM yyyy', { locale: es })}
                    </span>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      )}

      {showModal && (
        <NuevaTareaModal
          clienteId={clienteId}
          agentes={agentes}
          onClose={() => setShowModal(false)}
          onSaved={() => { setShowModal(false); onRefresh(); }}
        />
      )}
    </div>
  );
}

// ═══ PÁGINA PRINCIPAL ══════════════════════════════════════════
export default function ClientePerfilPage() {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const { id } = useParams();

  const navigate = useNavigate();
  const [cliente, setCliente] = useState(null);
  const [resumen, setResumen] = useState(null);
  const [agentes, setAgentes] = useState([]);
  const [tareas,  setTareas]  = useState([]);
  const [tab, setTab] = useState('historial');
  const [loading, setLoading] = useState(true);
  const [editModal, setEditModal] = useState(false);

  const cargar = useCallback(async () => {
    try {
      const [c, r, a, t] = await Promise.all([
        api.get(`/clientes/${id}`),
        api.get(`/clientes/${id}/resumen`),
        api.get('/agentes'),
        api.get(`/tareas?cliente_id=${id}`),
      ]);
      setCliente(c.data);
      setResumen(r.data);
      setAgentes(a.data);
      setTareas(t.data);
    } catch (e) {
      toast.error('No se pudo cargar el cliente');
      navigate('/clientes');
    } finally {
      setLoading(false);
    }
  }, [id, navigate]);

  useEffect(() => { cargar(); }, [cargar]);

  if (loading) return (
    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '60vh' }}>
      <div style={{ textAlign: 'center' }}>
        <div className="spinner" style={{ margin: '0 auto 16px' }} />
        <p className="text-muted">Cargando perfil CRM...</p>
      </div>
    </div>
  );

  if (!cliente) return null;

  const cat = catStyle[cliente.categoria] || catStyle.Nuevo;
  const interacciones = cliente.interacciones || [];
  const oportunidades = cliente.oportunidades || [];
  const reservas = cliente.reservas || [];

  const tabs = [
    { key: 'historial',     label: 'Historial',     icon: <Activity size={15}/>,    count: interacciones.length },
    { key: 'oportunidades', label: 'Oportunidades', icon: <Target size={15}/>,      count: oportunidades.length },
    { key: 'reservas',      label: 'Reservas',      icon: <BookOpen size={15}/>,    count: reservas.length },
    { key: 'tareas',        label: 'Tareas',        icon: <CheckSquare size={15}/>, count: tareas.filter(t => t.estado === 'Pendiente' || t.estado === 'En Progreso').length },
  ];

  return (
    <div className="animate-fade-in">
      {/* ── Header ── */}
      <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 24 }}>
        <button onClick={() => navigate('/clientes')} style={{
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          width: 36, height: 36, borderRadius: 10, border: '1.5px solid #E2E8F0',
          background: 'white', cursor: 'pointer', color: '#64748B',
          boxShadow: '0 1px 4px rgba(0,0,0,0.06)',
        }}>
          <ArrowLeft size={16} />
        </button>
        <div style={{ flex: 1 }}>
          <nav style={{ display: 'flex', alignItems: 'center', gap: 6, fontSize: '0.78rem', color: '#94A3B8' }}>
            <Link to="/clientes" style={{ color: '#7C3AED', textDecoration: 'none', fontWeight: 500 }}>Clientes</Link>
            <ChevronRight size={12} />
            <span style={{ color: '#0F172A', fontWeight: 600 }}>{cliente.nombre} {cliente.apellido}</span>
          </nav>
        </div>
        <button className="btn btn-secondary" style={{ fontSize: '0.8rem' }} onClick={() => setEditModal(true)}>
          <Edit2 size={14}/> Editar Perfil
        </button>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '300px 1fr', gap: 22, alignItems: 'start' }}>

        {/* ══ PANEL IZQUIERDO ══════════════════════════════════ */}
        <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>

          {/* Tarjeta de perfil */}
          <div style={{
            background: 'white', borderRadius: 18, border: '1px solid #E2E8F0',
            boxShadow: '0 4px 20px rgba(0,0,0,0.06)', overflow: 'hidden',
          }}>
            {/* Banner degradado */}
            <div style={{ height: 70, background: avatarGradient(cliente.id) }} />

            <div style={{ padding: '0 20px 20px', marginTop: -36 }}>
              {/* Avatar */}
              <div style={{
                width: 72, height: 72, borderRadius: '50%',
                background: avatarGradient(cliente.id),
                display: 'flex', alignItems: 'center', justifyContent: 'center',
                fontSize: '1.5rem', fontWeight: 900, color: 'white',
                border: '3px solid white', boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
                marginBottom: 12,
              }}>
                {initiales(cliente.nombre, cliente.apellido)}
              </div>

              <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', gap: 8 }}>
                <div>
                  <div style={{ fontWeight: 800, fontSize: '1.1rem', color: '#0F172A', fontFamily: 'Plus Jakarta Sans' }}>
                    {cliente.nombre} {cliente.apellido}
                  </div>
                  <div style={{ fontSize: '0.78rem', color: '#64748B' }}>{cliente.email}</div>
                </div>
                <span style={{
                  fontSize: '0.68rem', fontWeight: 700, padding: '3px 10px', borderRadius: 20,
                  background: cat.bg, color: cat.color, whiteSpace: 'nowrap',
                }}>
                  {cat.icon} {cliente.categoria}
                </span>
              </div>

              {/* Acciones rápidas */}
              <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 6, margin: '16px 0' }}>
                {[
                  { label: 'Llamar', icon: <Phone size={14}/>, color: '#3B82F6', href: `tel:${cliente.telefono}` },
                  { label: 'Email', icon: <Mail size={14}/>, color: '#8B5CF6', href: `mailto:${cliente.email}` },
                  { label: 'WhatsApp', icon: <MessageCircle size={14}/>, color: '#10B981', href: `https://wa.me/${cliente.telefono?.replace(/\D/g,'')}` },
                  { label: 'Tarea', icon: <Plus size={14}/>, color: '#F59E0B', onClick: () => setTab('tareas') },
                ].map(a => (
                  <a key={a.label}
                    href={a.href || '#'}
                    onClick={a.onClick ? (e) => { e.preventDefault(); a.onClick(); } : undefined}
                    target={a.href && !a.href.startsWith('tel:') && !a.href.startsWith('mailto:') ? '_blank' : undefined}
                    rel="noreferrer"
                    style={{
                      display: 'flex', flexDirection: 'column', alignItems: 'center',
                      gap: 4, padding: '10px 4px', borderRadius: 10, textDecoration: 'none',
                      background: a.color + '10', border: `1.5px solid ${a.color}22`,
                      cursor: 'pointer', transition: 'all 0.15s',
                    }}>
                    <span style={{ color: a.color }}>{a.icon}</span>
                    <span style={{ fontSize: '0.62rem', fontWeight: 600, color: a.color }}>{a.label}</span>
                  </a>
                ))}
              </div>

              {/* Datos de contacto */}
              <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
                {[
                  { icon: <Phone size={13}/>, value: cliente.telefono || '—' },
                  { icon: <MapPin size={13}/>, value: [cliente.ciudad, cliente.pais].filter(Boolean).join(', ') || '—' },
                  { icon: <Briefcase size={13}/>, value: `${cliente.documento_tipo}: ${cliente.documento_num || 'N/E'}` },
                  { icon: <Globe size={13}/>, value: cliente.fuente?.nombre || 'Sin fuente' },
                  { icon: <Calendar size={13}/>, value: cliente.creado_en ? `Desde ${format(new Date(cliente.creado_en), 'd MMM yyyy', { locale: es })}` : '' },
                ].map((d, i) => d.value && (
                  <div key={i} style={{ display: 'flex', alignItems: 'flex-start', gap: 8 }}>
                    <span style={{ color: '#94A3B8', marginTop: 1, flexShrink: 0 }}>{d.icon}</span>
                    <span style={{ fontSize: '0.8rem', color: '#475569' }}>{d.value}</span>
                  </div>
                ))}
              </div>

              {/* Agente */}
              {cliente.agente && (
                <div style={{
                  marginTop: 16, padding: '10px 14px', borderRadius: 10,
                  background: '#F8FAFC', border: '1px solid #E2E8F0',
                  display: 'flex', alignItems: 'center', gap: 10,
                }}>
                  <div style={{
                    width: 32, height: 32, borderRadius: '50%',
                    background: avatarGradient(cliente.agente.id),
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    fontSize: '0.65rem', fontWeight: 800, color: 'white',
                  }}>
                    {initiales(cliente.agente.nombre, cliente.agente.apellido)}
                  </div>
                  <div>
                    <div style={{ fontSize: '0.75rem', color: '#94A3B8' }}>Agente asignado</div>
                    <div style={{ fontSize: '0.85rem', fontWeight: 600, color: '#0F172A' }}>
                      {cliente.agente.nombre} {cliente.agente.apellido}
                    </div>
                  </div>
                </div>
              )}

              {/* Notas */}
              {cliente.notas && (
                <div style={{ marginTop: 14 }}>
                  <div style={{ fontSize: '0.72rem', fontWeight: 600, color: '#94A3B8', marginBottom: 6, textTransform: 'uppercase', letterSpacing: '0.05em' }}>NOTAS</div>
                  <div style={{ fontSize: '0.8rem', color: '#475569', lineHeight: 1.6, background: '#FFFBEB', padding: '10px 12px', borderRadius: 8, border: '1px solid #FDE68A' }}>
                    {cliente.notas}
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Stats de rentabilidad */}
          {resumen && (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
              <StatCard icon={<DollarSign/>} label="Valor de Vida (LTV)" value={`${m}${(resumen.ltv||0).toLocaleString('es')}`} accent="#7C3AED" />
              <StatCard icon={<TrendingUp/>} label="Utilidad generada" value={`${m}${(resumen.utilidadCliente||0).toLocaleString('es')}`} accent="#10B981" />
              <StatCard icon={<BookOpen/>} label="Reservas" value={resumen.totalReservas} sub={`${resumen.reservasCompletadas} completadas`} accent="#3B82F6" />
              <StatCard icon={<Target/>} label="Valor en pipeline" value={`${m}${(resumen.valorPipeline||0).toLocaleString('es')}`} sub={`${resumen.opActivas} oportunidad${resumen.opActivas !== 1 ? 'es' : ''} activa${resumen.opActivas !== 1 ? 's' : ''}`} accent="#F59E0B" />

            </div>
          )}
        </div>

        {/* ══ PANEL DERECHO — TABS ═════════════════════════════ */}
        <div style={{
          background: 'white', borderRadius: 18, border: '1px solid #E2E8F0',
          boxShadow: '0 4px 20px rgba(0,0,0,0.06)', overflow: 'hidden',
        }}>
          {/* Tabs header */}
          <div style={{
            display: 'flex', borderBottom: '1px solid #E2E8F0',
            padding: '0 6px',
          }}>
            {tabs.map(t => (
              <button key={t.key} onClick={() => setTab(t.key)} style={{
                display: 'flex', alignItems: 'center', gap: 7,
                padding: '16px 18px', border: 'none', background: 'transparent',
                cursor: 'pointer', fontWeight: tab === t.key ? 700 : 500,
                fontSize: '0.83rem', transition: 'all 0.15s',
                color: tab === t.key ? '#7C3AED' : '#64748B',
                borderBottom: tab === t.key ? '2.5px solid #7C3AED' : '2.5px solid transparent',
                marginBottom: -1,
              }}>
                <span style={{ color: tab === t.key ? '#7C3AED' : '#94A3B8' }}>{t.icon}</span>
                {t.label}
                {t.count > 0 && (
                  <span style={{
                    fontSize: '0.65rem', fontWeight: 800, minWidth: 18, height: 18,
                    borderRadius: 20, display: 'flex', alignItems: 'center', justifyContent: 'center',
                    background: tab === t.key ? '#7C3AED' : '#E2E8F0',
                    color: tab === t.key ? 'white' : '#64748B', padding: '0 5px',
                  }}>{t.count}</span>
                )}
              </button>
            ))}
          </div>

          {/* Tab content */}
          <div style={{ padding: '24px' }}>
            {tab === 'historial'     && <TabHistorial interacciones={interacciones} clienteId={id} onRefresh={cargar} />}
            {tab === 'oportunidades' && <TabOportunidades oportunidades={oportunidades} />}
            {tab === 'reservas'      && <TabReservas reservas={reservas} />}
            {tab === 'tareas'        && <TabTareas clienteId={id} agentes={agentes} tareas={tareas} onRefresh={cargar} />}
          </div>
        </div>
      </div>
    </div>
  );
}
