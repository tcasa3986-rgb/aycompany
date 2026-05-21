import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Headphones, ChevronDown, ChevronUp, Trash2, Send, Clock } from 'lucide-react';

const ESTADOS = { abierto: { label: 'Abierto', bg: '#fef3c7', color: '#d97706' }, en_proceso: { label: 'En proceso', bg: '#dbeafe', color: '#1d4ed8' }, cerrado: { label: 'Cerrado', bg: '#d1fae5', color: '#065f46' } };

export default function Tickets() {
  const [tickets,    setTickets]    = useState([]);
  const [filtro,     setFiltro]     = useState('');
  const [expandido,  setExpandido]  = useState(null);
  const [respuesta,  setRespuesta]  = useState({});
  const [guardando,  setGuardando]  = useState(false);

  const cargar = (estado = filtro) =>
    api.get('/tickets', { params: estado ? { estado } : {} })
       .then(r => setTickets(r.data.data));

  useEffect(() => { cargar(); }, []);

  function toggle(id) {
    setExpandido(p => p === id ? null : id);
  }

  async function responder(ticket) {
    const texto = respuesta[ticket.id]?.trim();
    if (!texto) return toast.error('Escribe una respuesta');
    setGuardando(true);
    try {
      await api.put(`/tickets/${ticket.id}`, { respuesta: texto, estado: 'cerrado' });
      toast.success('Respuesta enviada');
      setRespuesta(r => ({ ...r, [ticket.id]: '' }));
      cargar();
    } catch { toast.error('Error al responder'); }
    finally { setGuardando(false); }
  }

  async function cambiarEstado(id, estado) {
    try {
      await api.put(`/tickets/${id}`, { estado });
      toast.success('Estado actualizado');
      cargar();
    } catch { toast.error('Error'); }
  }

  async function eliminar(id) {
    if (!confirm('¿Eliminar este ticket?')) return;
    await api.delete(`/tickets/${id}`);
    toast.success('Ticket eliminado');
    cargar();
  }

  function aplicarFiltro(val) {
    setFiltro(val);
    cargar(val);
  }

  const abiertos   = tickets.filter(t => t.estado === 'abierto').length;
  const enProceso  = tickets.filter(t => t.estado === 'en_proceso').length;

  return (
    <div style={{ padding: 32 }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
        <div>
          <h1 style={{ fontSize: '1.4rem', fontWeight: 700, display: 'flex', alignItems: 'center', gap: 10 }}>
            <Headphones size={22} color="#7c3aed" /> Tickets de soporte
          </h1>
          <p style={{ color: '#64748b', fontSize: '.88rem', marginTop: 2 }}>
            {abiertos > 0 && <span style={{ color: '#d97706', fontWeight: 600 }}>{abiertos} abierto{abiertos !== 1 ? 's' : ''} · </span>}
            {enProceso > 0 && <span style={{ color: '#1d4ed8', fontWeight: 600 }}>{enProceso} en proceso · </span>}
            {tickets.length} total
          </p>
        </div>
        <div style={{ display: 'flex', gap: 8 }}>
          {[['', 'Todos'], ['abierto', 'Abiertos'], ['en_proceso', 'En proceso'], ['cerrado', 'Cerrados']].map(([val, label]) => (
            <button key={val} onClick={() => aplicarFiltro(val)}
              style={{ padding: '7px 14px', borderRadius: 8, border: '1px solid', borderColor: filtro === val ? '#7c3aed' : '#e2e8f0', background: filtro === val ? '#ede9fe' : '#fff', color: filtro === val ? '#7c3aed' : '#374151', fontSize: '.82rem', fontWeight: filtro === val ? 700 : 500, cursor: 'pointer' }}>
              {label}
            </button>
          ))}
        </div>
      </div>

      {tickets.length === 0 && (
        <div style={{ textAlign: 'center', padding: 60, color: '#94a3b8' }}>
          <Headphones size={40} style={{ marginBottom: 12, opacity: .4 }} />
          <div>No hay tickets {filtro ? `con estado "${filtro}"` : ''}</div>
        </div>
      )}

      {tickets.map(t => {
        const est = ESTADOS[t.estado] || ESTADOS.abierto;
        const open = expandido === t.id;
        return (
          <div key={t.id} style={{ background: '#fff', borderRadius: 12, marginBottom: 10, boxShadow: '0 1px 4px rgba(0,0,0,.07)', overflow: 'hidden' }}>
            {/* Header del ticket */}
            <div onClick={() => toggle(t.id)} style={{ display: 'flex', alignItems: 'center', gap: 14, padding: '14px 18px', cursor: 'pointer' }}>
              <span style={{ background: est.bg, color: est.color, padding: '3px 10px', borderRadius: 20, fontSize: '.78rem', fontWeight: 700, whiteSpace: 'nowrap' }}>
                {est.label}
              </span>
              <div style={{ flex: 1, minWidth: 0 }}>
                <div style={{ fontWeight: 600, fontSize: '.95rem', color: '#1e293b' }}>{t.asunto}</div>
                <div style={{ fontSize: '.78rem', color: '#64748b', marginTop: 2 }}>
                  {t.cliente?.nombre} · {new Date(t.created_at).toLocaleDateString('es-CO', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                </div>
              </div>
              <div style={{ display: 'flex', gap: 8, flexShrink: 0, alignItems: 'center' }}>
                {t.estado !== 'en_proceso' && t.estado !== 'cerrado' && (
                  <button onClick={e => { e.stopPropagation(); cambiarEstado(t.id, 'en_proceso'); }}
                    style={{ background: '#dbeafe', color: '#1d4ed8', border: 'none', borderRadius: 7, padding: '5px 10px', fontSize: '.78rem', fontWeight: 600, cursor: 'pointer' }}>
                    <Clock size={12} /> En proceso
                  </button>
                )}
                <button onClick={e => { e.stopPropagation(); eliminar(t.id); }}
                  style={{ background: '#fef2f2', color: '#ef4444', border: 'none', borderRadius: 7, padding: '6px 8px', cursor: 'pointer' }}>
                  <Trash2 size={13} />
                </button>
                {open ? <ChevronUp size={16} color="#94a3b8" /> : <ChevronDown size={16} color="#94a3b8" />}
              </div>
            </div>

            {/* Cuerpo expandido */}
            {open && (
              <div style={{ borderTop: '1px solid #f1f5f9', padding: '16px 18px 18px' }}>
                <div style={{ background: '#f8fafc', borderRadius: 8, padding: '12px 14px', marginBottom: 14, fontSize: '.9rem', color: '#374151', lineHeight: 1.6 }}>
                  {t.mensaje}
                </div>
                {t.cliente?.email && (
                  <div style={{ fontSize: '.8rem', color: '#64748b', marginBottom: 14 }}>
                    📧 {t.cliente.email} {t.cliente.telefono && `· 📱 ${t.cliente.telefono}`}
                  </div>
                )}
                {t.respuesta && (
                  <div style={{ background: '#f0fdf4', border: '1px solid #bbf7d0', borderRadius: 8, padding: '12px 14px', marginBottom: 14 }}>
                    <div style={{ fontSize: '.75rem', color: '#065f46', fontWeight: 700, marginBottom: 6 }}>RESPUESTA ENVIADA</div>
                    <div style={{ fontSize: '.9rem', color: '#374151' }}>{t.respuesta}</div>
                  </div>
                )}
                {t.estado !== 'cerrado' && (
                  <div>
                    <textarea
                      rows={3}
                      placeholder="Escribe tu respuesta al cliente..."
                      value={respuesta[t.id] || ''}
                      onChange={e => setRespuesta(r => ({ ...r, [t.id]: e.target.value }))}
                      style={{ width: '100%', padding: '10px 12px', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: '.9rem', resize: 'none', boxSizing: 'border-box', marginBottom: 10 }}
                    />
                    <button onClick={() => responder(t)} disabled={guardando}
                      style={{ display: 'inline-flex', alignItems: 'center', gap: 6, padding: '9px 18px', background: '#7c3aed', color: '#fff', border: 'none', borderRadius: 8, fontSize: '.88rem', fontWeight: 700, cursor: 'pointer' }}>
                      <Send size={14} /> {guardando ? 'Guardando...' : 'Responder y cerrar'}
                    </button>
                  </div>
                )}
              </div>
            )}
          </div>
        );
      })}
    </div>
  );
}
