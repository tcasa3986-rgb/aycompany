import { useState, useEffect } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, Flame, Pencil, Archive, Check, X, Minus } from 'lucide-react';
import { C, Pagina, Cabecera, Tarjeta, Boton, BotonIcono, Campo, Dos, Modal, entrada, Barra, Franja } from '../components/ui';

// La pantalla de hábitos.
//
// Regla de diseño: marcar tiene que costar UN toque y verse al instante. Por
// eso el círculo cambia antes de que responda el servidor y se devuelve solo si
// falla. Si marcar cuesta trabajo, uno deja de marcar y el historial se vuelve
// mentira — el mismo problema que ya tuvimos con los gastos.

const DIAS = [['1','L'], ['2','M'], ['3','X'], ['4','J'], ['5','V'], ['6','S'], ['0','D']];
const ICONOS = ['✅','💪','🏃','📚','💧','🧘','🌅','🛏','🚭','🍎','💻','📞','🧹','💰','🙏','🎯'];
const COLORES = ['#2563EB','#16A34A','#DC2626','#F59E0B','#7C3AED','#0891B2','#DB2777','#0F172A'];

const VACIO = { nombre: '', detalle: '', tipo: 'hacer', icono: '✅', color: '#2563EB',
                dias: '0,1,2,3,4,5,6', meta_diaria: 1, hora: '', recordar: true, ambito: 'personal' };

export default function Habitos() {
  const [r, setR]         = useState(null);
  const [modal, setModal] = useState(false);
  const [form, setForm]   = useState(VACIO);
  const [editId, setEditId] = useState(null);
  const [verArchivados, setVerArchivados] = useState(false);

  const cargar = () => api.get('/habitos' + (verArchivados ? '?todos=1' : '')).then(x => setR(x.data));
  useEffect(() => { cargar(); }, [verArchivados]);

  async function marcar(h, accion = 'alternar') {
    // Se pinta de una, sin esperar al servidor.
    setR(prev => ({
      ...prev,
      data: prev.data.map(x => x.id !== h.id ? x : {
        ...x,
        hecho_hoy: x.tipo === 'evitar' ? !x.cai_hoy : !x.hecho_hoy,
        cai_hoy:   x.tipo === 'evitar' ? !x.cai_hoy : x.cai_hoy,
      }),
    }));
    try {
      await api.post(`/habitos/${h.id}/marcar`, { accion });
      cargar();
    } catch (err) {
      toast.error('No se pudo marcar');
      cargar();   // se devuelve a como estaba de verdad
    }
  }

  async function guardar(e) {
    e.preventDefault();
    try {
      const x = editId ? await api.put(`/habitos/${editId}`, form) : await api.post('/habitos', form);
      toast.success(x.data.msg); cerrar(); cargar();
    } catch (err) { toast.error(err.response?.data?.msg || 'Error al guardar'); }
  }
  async function archivar(h) {
    if (!confirm(`¿Archivar "${h.nombre}"? El historial se guarda.`)) return;
    try { const x = await api.delete(`/habitos/${h.id}`); toast.success(x.data.msg); cargar(); }
    catch (err) { toast.error('Error'); }
  }
  function editar(h) {
    setEditId(h.id);
    setForm({ nombre: h.nombre, detalle: h.detalle || '', tipo: h.tipo, icono: h.icono, color: h.color,
              dias: h.dias, meta_diaria: h.meta_diaria, hora: h.hora || '', recordar: !!h.recordar,
              ambito: h.ambito, activo: h.activo });
    setModal(true);
  }
  function cerrar() { setModal(false); setEditId(null); setForm(VACIO); }

  function alternarDia(d) {
    const lista = String(form.dias).split(',').filter(Boolean);
    const nueva = lista.includes(d) ? lista.filter(x => x !== d) : [...lista, d];
    setForm(f => ({ ...f, dias: nueva.sort().join(',') }));
  }

  if (!r) return <Pagina><Cabecera titulo="Hábitos" sub="Cargando…" /></Pagina>;

  const { resumen, data, hoy } = r;
  const activos = data.filter(h => h.activo);
  const archivados = data.filter(h => !h.activo);
  const deHoy = activos.filter(h => h.aplica_hoy);
  const otrosDias = activos.filter(h => !h.aplica_hoy);

  return (
    <Pagina>
      <Cabecera titulo="Hábitos" sub={fechaLarga(hoy)}>
        <Boton onClick={() => { setEditId(null); setForm(VACIO); setModal(true); }}>
          <Plus size={16} /> Nuevo hábito
        </Boton>
      </Cabecera>

      {activos.length === 0 ? (
        <Tarjeta>
          <div style={S.vacio}>
            <div style={{ fontSize: '2.2rem', marginBottom: 8 }}>🎯</div>
            <p style={{ fontWeight: 700, color: C.tinta, margin: '0 0 4px' }}>Todavía no hay hábitos</p>
            <p style={{ color: C.tenue, fontSize: '.88rem', margin: '0 0 16px' }}>
              Empieza con uno solo. Uno que sostengas vale más que cinco que abandones.
            </p>
            <Boton onClick={() => setModal(true)}><Plus size={16} /> Crear el primero</Boton>
          </div>
        </Tarjeta>
      ) : (
        <>
          {/* Cómo va el día */}
          <Tarjeta>
            <div style={S.cabeceraDia}>
              <div>
                <div style={S.pctNum}>{resumen.pct}%</div>
                <div style={S.pctPie}>{resumen.hechos} de {resumen.de_hoy} hoy</div>
              </div>
              {resumen.mejor_racha > 0 && (
                <div style={S.rachaTop}>
                  <Flame size={15} color="#F59E0B" />
                  <span><strong>{resumen.mejor_racha}</strong> días · tu mejor racha viva</span>
                </div>
              )}
            </div>
            <Barra pct={resumen.pct} color={resumen.pct === 100 ? C.verde : C.azul} alto={9} />
          </Tarjeta>

          {/* Los de hoy */}
          <div style={{ display: 'grid', gap: 10, marginTop: 14 }}>
            {deHoy.map(h => <Fila key={h.id} h={h} onMarcar={marcar} onEditar={editar} onArchivar={archivar} />)}
          </div>

          {otrosDias.length > 0 && (
            <>
              <p style={S.seccion}>Hoy no tocan</p>
              <div style={{ display: 'grid', gap: 10 }}>
                {otrosDias.map(h => <Fila key={h.id} h={h} apagada onMarcar={marcar} onEditar={editar} onArchivar={archivar} />)}
              </div>
            </>
          )}
        </>
      )}

      <p style={S.verMas} onClick={() => setVerArchivados(v => !v)}>
        {verArchivados ? 'Ocultar archivados' : 'Ver archivados'}
      </p>
      {verArchivados && archivados.length > 0 && (
        <div style={{ display: 'grid', gap: 10 }}>
          {archivados.map(h => <Fila key={h.id} h={h} apagada onMarcar={marcar} onEditar={editar} onArchivar={archivar} />)}
        </div>
      )}

      {modal && (
        <Modal titulo={editId ? 'Editar hábito' : 'Nuevo hábito'} onCerrar={cerrar}>
          <form onSubmit={guardar}>
            <Campo label="¿Qué hábito?">
              <input value={form.nombre} onChange={e => setForm(f => ({ ...f, nombre: e.target.value }))}
                     required autoFocus style={entrada} placeholder="Ej: Ir al gimnasio" />
            </Campo>
            <Campo label="Detalle" pista="Opcional. Sale en el recordatorio.">
              <input value={form.detalle} onChange={e => setForm(f => ({ ...f, detalle: e.target.value }))}
                     style={entrada} placeholder="Ej: 45 minutos, pesas" />
            </Campo>

            <Dos>
              <Campo label="¿De qué tipo?">
                <select value={form.tipo} onChange={e => setForm(f => ({ ...f, tipo: e.target.value }))} style={entrada}>
                  <option value="hacer">Hacer algo (sumar)</option>
                  <option value="evitar">Evitar algo (no caer)</option>
                </select>
              </Campo>
              <Campo label="Hora del recordatorio" pista="Vacío = no te avisa">
                <input type="time" value={form.hora || ''} onChange={e => setForm(f => ({ ...f, hora: e.target.value }))} style={entrada} />
              </Campo>
            </Dos>

            <Campo label="¿Qué días?">
              <div style={S.dias}>
                {DIAS.map(([v, l]) => {
                  const activo = String(form.dias).split(',').includes(v);
                  return (
                    <button key={v} type="button" onClick={() => alternarDia(v)}
                      style={{ ...S.dia, ...(activo ? { background: form.color, color: '#fff' } : {}) }}>{l}</button>
                  );
                })}
              </div>
            </Campo>

            {form.tipo === 'hacer' && (
              <Campo label="¿Cuántas veces al día?" pista="1 para la mayoría. 8 para los vasos de agua.">
                <input type="number" min="1" max="20" value={form.meta_diaria}
                       onChange={e => setForm(f => ({ ...f, meta_diaria: Number(e.target.value) || 1 }))} style={entrada} />
              </Campo>
            )}

            <Campo label="Ícono">
              <div style={S.iconos}>
                {ICONOS.map(i => (
                  <button key={i} type="button" onClick={() => setForm(f => ({ ...f, icono: i }))}
                    style={{ ...S.icono, ...(form.icono === i ? { background: '#E0E7FF', borderColor: C.azul } : {}) }}>{i}</button>
                ))}
              </div>
            </Campo>
            <Campo label="Color">
              <div style={S.colores}>
                {COLORES.map(c => (
                  <button key={c} type="button" onClick={() => setForm(f => ({ ...f, color: c }))}
                    style={{ ...S.color, background: c, outline: form.color === c ? `3px solid ${c}55` : 'none' }} />
                ))}
              </div>
            </Campo>

            <label style={S.check}>
              <input type="checkbox" checked={!!form.recordar}
                     onChange={e => setForm(f => ({ ...f, recordar: e.target.checked }))} />
              Avisarme por Telegram
            </label>

            {!form.hora && form.recordar && (
              <Franja color={C.azul}>
                Sin hora no hay recordatorio propio, pero igual sale en el resumen de las
                8:00 pm y en el cierre de las 9:30 pm.
              </Franja>
            )}

            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 14 }}>
              <Boton type="button" variante="borde" onClick={cerrar}>Cancelar</Boton>
              <Boton type="submit">Guardar</Boton>
            </div>
          </form>
        </Modal>
      )}
    </Pagina>
  );
}

// ── Una fila ──────────────────────────────────────────────────────────────────
function Fila({ h, apagada, onMarcar, onEditar, onArchivar }) {
  const evitar = h.tipo === 'evitar';
  const hecho  = evitar ? !h.cai_hoy : h.hecho_hoy;
  const parcial = !evitar && h.meta_diaria > 1 && h.veces_hoy > 0 && !hecho;

  return (
    <div style={{ ...S.fila, opacity: apagada ? .55 : 1, borderLeft: `3px solid ${h.color}` }}>
      {/* El círculo: un toque */}
      <button onClick={() => !apagada && onMarcar(h)} disabled={apagada}
        style={{ ...S.circulo,
                 background: hecho ? h.color : parcial ? h.color + '33' : '#fff',
                 borderColor: hecho ? h.color : '#CBD5E1',
                 cursor: apagada ? 'default' : 'pointer' }}>
        {hecho
          ? (evitar ? <Check size={18} color="#fff" /> : <Check size={18} color="#fff" />)
          : parcial ? <span style={{ fontSize: '.72rem', fontWeight: 800, color: h.color }}>{h.veces_hoy}</span>
          : <span style={{ fontSize: '1rem' }}>{h.icono}</span>}
      </button>

      <div style={{ flex: 1, minWidth: 0 }}>
        <div style={S.nombre}>
          {h.nombre}
          {evitar && <span style={S.tagEvitar}>evitar</span>}
          {h.hora && <span style={S.hora}>{h.hora}</span>}
        </div>
        <div style={S.sub}>
          {h.racha > 0 && (
            <span style={{ color: '#F59E0B', fontWeight: 700 }}>
              <Flame size={12} style={{ verticalAlign: -1 }} /> {h.racha}
            </span>
          )}
          {h.racha > 0 && <span style={S.punto}>·</span>}
          <span>{h.mes.pct}% este mes</span>
          {h.meta_diaria > 1 && <><span style={S.punto}>·</span><span>{h.veces_hoy}/{h.meta_diaria}</span></>}
        </div>
        {/* La semana de un vistazo */}
        <div style={S.semana}>
          {h.semana.map(d => (
            <span key={d.fecha} title={d.fecha}
              style={{ ...S.pip,
                       background: !d.aplica ? '#F1F5F9' : d.hecho ? h.color : '#E2E8F0',
                       outline: d.esHoy ? `2px solid ${h.color}66` : 'none' }} />
          ))}
        </div>
      </div>

      {/* Para los de varias veces al día */}
      {!apagada && !evitar && h.meta_diaria > 1 && (
        <div style={S.masMenos}>
          <BotonIcono color={C.tenue} titulo="Quitar una" onClick={() => onMarcar(h, 'quitar')}><Minus size={14} /></BotonIcono>
          <BotonIcono color={h.color} titulo="Sumar una" onClick={() => onMarcar(h, 'sumar')}><Plus size={14} /></BotonIcono>
        </div>
      )}

      <div style={{ display: 'flex', gap: 2 }}>
        <BotonIcono color={C.azul} titulo="Editar" onClick={() => onEditar(h)}><Pencil size={14} /></BotonIcono>
        {h.activo && <BotonIcono color={C.tenue} titulo="Archivar" onClick={() => onArchivar(h)}><Archive size={14} /></BotonIcono>}
      </div>
    </div>
  );
}

function fechaLarga(iso) {
  if (!iso) return '';
  const [y, m, d] = iso.split('-').map(Number);
  const f = new Date(Date.UTC(y, m - 1, d));
  return f.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', timeZone: 'UTC' });
}

const S = {
  vacio: { textAlign: 'center', padding: '28px 16px' },
  cabeceraDia: { display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 12, marginBottom: 10, flexWrap: 'wrap' },
  pctNum: { fontFamily: "'Space Grotesk', system-ui, sans-serif", fontSize: '2rem', fontWeight: 800, color: C.tinta, lineHeight: 1 },
  pctPie: { fontSize: '.8rem', color: C.tenue, marginTop: 2 },
  rachaTop: { display: 'inline-flex', alignItems: 'center', gap: 6, fontSize: '.82rem', color: '#92400E',
              background: '#FEF3C7', padding: '7px 12px', borderRadius: 999 },

  fila: { display: 'flex', alignItems: 'center', gap: 12, background: '#fff', borderRadius: 14,
          padding: '12px 14px', boxShadow: '0 1px 2px rgba(15,23,42,.06)' },
  circulo: { width: 44, height: 44, borderRadius: '50%', border: '2px solid', flexShrink: 0,
             display: 'flex', alignItems: 'center', justifyContent: 'center', transition: 'all .12s' },
  nombre: { fontWeight: 700, color: C.tinta, fontSize: '.95rem', display: 'flex', alignItems: 'center',
            gap: 7, flexWrap: 'wrap' },
  tagEvitar: { fontSize: '.64rem', fontWeight: 700, color: '#9A3412', background: '#FFEDD5',
               padding: '2px 7px', borderRadius: 999, letterSpacing: '.03em' },
  hora: { fontSize: '.7rem', color: C.tenue, fontWeight: 600 },
  sub: { fontSize: '.78rem', color: C.tenue, display: 'flex', alignItems: 'center', gap: 5, marginTop: 2 },
  punto: { color: '#CBD5E1' },
  semana: { display: 'flex', gap: 4, marginTop: 7 },
  pip: { width: 13, height: 5, borderRadius: 3, display: 'inline-block' },
  masMenos: { display: 'flex', gap: 2 },

  seccion: { fontSize: '.72rem', fontWeight: 700, color: C.tenue, letterSpacing: '.08em',
             textTransform: 'uppercase', margin: '20px 0 8px' },
  verMas: { fontSize: '.8rem', color: C.azul, textAlign: 'center', marginTop: 18, cursor: 'pointer', fontWeight: 600 },

  dias: { display: 'flex', gap: 6 },
  dia: { flex: 1, padding: '10px 0', borderRadius: 10, border: '1px solid #E2E8F0', background: '#fff',
         color: '#64748B', fontWeight: 700, fontSize: '.82rem', cursor: 'pointer' },
  iconos: { display: 'flex', flexWrap: 'wrap', gap: 6 },
  icono: { width: 38, height: 38, borderRadius: 10, border: '1px solid #E2E8F0', background: '#fff',
           fontSize: '1.05rem', cursor: 'pointer' },
  colores: { display: 'flex', gap: 8 },
  color: { width: 30, height: 30, borderRadius: '50%', border: 'none', cursor: 'pointer' },
  check: { display: 'flex', alignItems: 'center', gap: 9, fontSize: '.86rem', margin: '4px 0 12px',
           cursor: 'pointer', color: '#334155' },
};
