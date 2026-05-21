import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  DndContext, closestCorners, PointerSensor, useSensor, useSensors, DragOverlay
} from '@dnd-kit/core';
import {
  SortableContext, verticalListSortingStrategy, useSortable, arrayMove
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { Plus, DollarSign, Calendar, User2, GripVertical, Target } from 'lucide-react';
import api from '../services/api';
import useConfigStore from '../store/configStore';

import { format } from 'date-fns';
import { es } from 'date-fns/locale';

// ─── Kanban Card arrastrable ──────────────────────────────────
function KanbanCard({ op, etapaColor, etapaNombre }) {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const { attributes, listeners, setNodeRef, transform, transition, isDragging } =
    useSortable({ id: op.id });

  const navigate = useNavigate();

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
    opacity: isDragging ? 0.4 : 1,
  };

  return (
    <div ref={setNodeRef} style={style} className="kanban-card" {...attributes}>
      {/* Color borde izquierdo */}
      <div style={{
        position:'absolute', left:0, top:0, bottom:0, width:3,
        background: etapaColor, borderRadius:'8px 0 0 8px'
      }} />

      <div style={{ paddingLeft: 8 }}>
        <div className="kanban-card-top">
          <div className="kanban-card-title">{op.titulo}</div>
          <div className="kanban-card-value">
            {m}{(+op.valor_estimado || 0).toLocaleString('es')}
          </div>

        </div>

        {op.cliente && (
          <div className="kanban-card-client">
            <div style={{
              width: 20, height: 20, borderRadius: '50%',
              background: 'linear-gradient(135deg,#0EA5E9,#8B5CF6)',
              display:'flex', alignItems:'center', justifyContent:'center',
              fontSize: '0.55rem', fontWeight: 700, color:'white', flexShrink:0
            }}>
              {op.cliente.nombre?.[0]}{op.cliente.apellido?.[0]}
            </div>
            {op.cliente.nombre} {op.cliente.apellido}
          </div>
        )}

        {op.paquete && (
          <div className="text-xs text-muted" style={{ marginBottom: 10 }}>
            📦 {op.paquete.nombre}
          </div>
        )}

        <div className="kanban-card-bottom">
          <div className="kanban-card-date">
            <Calendar size={11} />
            {op.fecha_cierre
              ? format(new Date(op.fecha_cierre), 'd MMM', { locale: es })
              : 'Sin fecha'}
          </div>
          <div style={{ display:'flex', alignItems:'center', gap: 6 }}>
            <div className="kanban-progress">
              <div className="kanban-progress-fill" style={{ width: `${op.probabilidad || 50}%` }} />
            </div>
            <span className="text-xs text-muted">{op.probabilidad || 50}%</span>
          </div>
          <div style={{ display: 'flex', gap: 6, alignItems: 'center' }}>
            {(op.probabilidad === 100 || (etapaNombre && etapaNombre.includes('Ganado'))) && (
              <button
                className="btn btn-ghost btn-icon btn-sm"
                title="Convertir a Reserva"
                style={{ color: 'var(--color-success)', cursor: 'pointer' }}
                onClick={(e) => {
                  e.stopPropagation();
                  navigate('/reservas', { state: { prefillOportunidad: op } });
                }}
              >
                <Target size={14} />
              </button>
            )}
            <div {...listeners} style={{ cursor:'grab', color:'var(--text-muted)', display:'flex', alignItems:'center' }}>
              <GripVertical size={14} />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

// ─── Columna Kanban ───────────────────────────────────────────
function KanbanColumn({ etapa, oportunidades, onAddClick }) {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const total = oportunidades.reduce((s, o) => s + (+o.valor_estimado || 0), 0);


  return (
    <div className="kanban-column">
      <div className="kanban-col-header">
        <div className="kanban-col-title">
          <div className="kanban-col-dot" style={{ background: etapa.color }} />
          {etapa.nombre}
          <span className="kanban-col-count">{oportunidades.length}</span>
        </div>
        <div className="kanban-col-total">{m}{total.toLocaleString('es')}</div>

      </div>

      <SortableContext
        items={oportunidades.map(o => o.id)}
        strategy={verticalListSortingStrategy}
      >
        <div className="kanban-cards">
          {oportunidades.map(op => (
            <KanbanCard key={op.id} op={op} etapaColor={etapa.color} etapaNombre={etapa.nombre} />
          ))}
          {oportunidades.length === 0 && (
            <div style={{ textAlign:'center', padding:'20px 0', color:'var(--text-muted)', fontSize:'0.75rem' }}>
              Sin oportunidades
            </div>
          )}
        </div>
      </SortableContext>

      <div style={{ padding: '0 12px 12px' }}>
        <button className="btn btn-ghost btn-sm w-full" style={{ justifyContent:'center', borderStyle:'dashed' }}
          onClick={() => onAddClick(etapa.id)}>
          <Plus size={14} /> Agregar
        </button>
      </div>
    </div>
  );
}

// ─── Modal nueva oportunidad ──────────────────────────────────
function OportunidadModal({ etapaId, etapas, clientes, agentes, paquetes, onClose, onSaved }) {
  const [form, setForm] = useState({
    titulo:'', cliente_id:'', agente_id:'', paquete_id:'',
    etapa_id: etapaId || '', valor_estimado:'', probabilidad: 50,
    fecha_cierre:'', notas:'', estado:'Activa'
  });
  const [saving, setSaving] = useState(false);
  const set = (k, v) => setForm(f => ({ ...f, [k]: v }));

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      await api.post('/oportunidades', form);
      onSaved();
    } catch(e) { console.error(e); }
    finally { setSaving(false); }
  };

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal animate-fade-in-up">
        <div className="modal-header">
          <h2 className="modal-title">Nueva Oportunidad</h2>
          <button className="modal-close" onClick={onClose}>✕</button>
        </div>
        <form onSubmit={handleSubmit}>
          <div className="modal-body">
            <div className="form-group">
              <label className="form-label required">Título</label>
              <input className="form-control" value={form.titulo} required
                onChange={e=>set('titulo',e.target.value)}
                placeholder="Ej: Europa 10 días - Familia Pérez" />
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label required">Cliente</label>
                <select className="form-control" value={form.cliente_id} required onChange={e=>set('cliente_id',e.target.value)}>
                  <option value="">Seleccionar</option>
                  {clientes.map(c=><option key={c.id} value={c.id}>{c.nombre} {c.apellido}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Agente</label>
                <select className="form-control" value={form.agente_id} onChange={e=>set('agente_id',e.target.value)}>
                  <option value="">Seleccionar</option>
                  {agentes.map(a=><option key={a.id} value={a.id}>{a.nombre} {a.apellido}</option>)}
                </select>
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Paquete</label>
                <select className="form-control" value={form.paquete_id} onChange={e=>set('paquete_id',e.target.value)}>
                  <option value="">Seleccionar</option>
                  {paquetes.map(p=><option key={p.id} value={p.id}>{p.nombre}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label required">Etapa</label>
                <select className="form-control" value={form.etapa_id} required onChange={e=>set('etapa_id',e.target.value)}>
                  <option value="">Seleccionar</option>
                  {etapas.map(e=><option key={e.id} value={e.id}>{e.nombre}</option>)}
                </select>
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Valor Estimado ({m})</label>

                <input type="number" className="form-control" value={form.valor_estimado}
                  onChange={e=>set('valor_estimado',e.target.value)} placeholder="0.00" />
              </div>
              <div className="form-group">
                <label className="form-label">Fecha de Cierre</label>
                <input type="date" className="form-control" value={form.fecha_cierre}
                  onChange={e=>set('fecha_cierre',e.target.value)} />
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Probabilidad: {form.probabilidad}%</label>
              <input type="range" min={0} max={100} value={form.probabilidad}
                onChange={e=>set('probabilidad', +e.target.value)}
                style={{ width:'100%', accentColor:'var(--color-primary)' }} />
            </div>
            <div className="form-group">
              <label className="form-label">Notas</label>
              <textarea className="form-control" rows={3} value={form.notas} onChange={e=>set('notas',e.target.value)} />
            </div>
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
            <button type="submit" className="btn btn-primary" disabled={saving}>
              {saving ? 'Creando...' : 'Crear Oportunidad'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

// ─── PIPELINE PAGE ────────────────────────────────────────────
export default function PipelinePage() {
  const m = useConfigStore(state => state.config?.moneda_simbolo) || '$';
  const [columnas,  setColumnas]  = useState([]);

  const [clientes,  setClientes]  = useState([]);
  const [agentes,   setAgentes]   = useState([]);
  const [paquetes,  setPaquetes]  = useState([]);
  const [etapas,    setEtapas]    = useState([]);
  const [loading,   setLoading]   = useState(true);
  const [modal,     setModal]     = useState(null); // etapaId
  const [activeId,  setActiveId]  = useState(null);

  const sensors = useSensors(useSensor(PointerSensor, { activationConstraint: { distance: 8 } }));

  const fetchKanban = async () => {
    setLoading(true);
    try {
      const res = await api.get('/oportunidades/kanban');
      setColumnas(res.data);
      setEtapas(res.data.map(c => ({ id: c.id, nombre: c.nombre, color: c.color, orden: c.orden })));
    } catch(e) { console.error(e); }
    finally { setLoading(false); }
  };

  useEffect(() => {
    fetchKanban();
    Promise.all([
      api.get('/clientes?limit=200'),
      api.get('/agentes'),
      api.get('/paquetes'),
    ]).then(([c, a, p]) => {
      setClientes(c.data);
      setAgentes(a.data);
      setPaquetes(p.data);
    });
  }, []);

  // Encuentra la oportunidad activa para overlay
  const activeOp = columnas.flatMap(c => c.oportunidades).find(o => o.id === activeId);
  const activeEtapa = columnas.find(c => c.oportunidades.some(o => o.id === activeId));

  const findColumn = (opId) => columnas.find(c => c.oportunidades.some(o => o.id === opId));

  const handleDragStart = ({ active }) => setActiveId(active.id);

  const handleDragEnd = async ({ active, over }) => {
    setActiveId(null);
    if (!over) return;
    const fromCol = findColumn(active.id);
    const toCol = columnas.find(c => c.id === over.id) || findColumn(over.id);
    if (!fromCol || !toCol) return;

    if (fromCol.id !== toCol.id) {
      // Mover a otra etapa
      try {
        await api.patch(`/oportunidades/${active.id}/etapa`, { etapa_id: toCol.id });
        fetchKanban();
      } catch(e) { console.error(e); }
    }
  };

  const totalPipeline = columnas
    .flatMap(c => c.oportunidades)
    .reduce((s, o) => s + (+o.valor_estimado || 0), 0);

  if (loading) return (
    <div style={{ display:'flex', alignItems:'center', justifyContent:'center', height:'60vh' }}>
      <div className="spinner" />
    </div>
  );

  return (
    <div className="animate-fade-in" style={{ height: '100%' }}>
      <div className="page-header">
        <div className="page-header-left">
          <h1 className="page-title">Pipeline de Ventas</h1>
          <p className="page-subtitle">
            {columnas.flatMap(c => c.oportunidades).length} oportunidades · 
            Total: {m}{totalPipeline.toLocaleString('es')}

          </p>
        </div>
        <button className="btn btn-primary" onClick={() => setModal('default')}>
          <Plus size={16} /> Nueva Oportunidad
        </button>
      </div>

      <DndContext
        sensors={sensors}
        collisionDetection={closestCorners}
        onDragStart={handleDragStart}
        onDragEnd={handleDragEnd}
      >
        <div className="kanban-board">
          {columnas.map(col => (
            <KanbanColumn
              key={col.id}
              etapa={{ id: col.id, nombre: col.nombre, color: col.color, orden: col.orden }}
              oportunidades={col.oportunidades}
              onAddClick={(etapaId) => setModal(etapaId)}
            />
          ))}
        </div>

        <DragOverlay>
          {activeId && activeOp ? (
            <div className="kanban-card" style={{ boxShadow: '0 16px 40px rgba(0,0,0,0.5)', cursor:'grabbing', width: 260 }}>
              <div className="kanban-card-title">{activeOp.titulo}</div>
              <div className="kanban-card-value">{m}{(+activeOp.valor_estimado || 0).toLocaleString('es')}</div>

            </div>
          ) : null}
        </DragOverlay>
      </DndContext>

      {modal && (
        <OportunidadModal
          etapaId={modal !== 'default' ? modal : etapas[0]?.id}
          etapas={etapas}
          clientes={clientes}
          agentes={agentes}
          paquetes={paquetes}
          onClose={() => setModal(null)}
          onSaved={() => { setModal(null); fetchKanban(); }}
        />
      )}
    </div>
  );
}
