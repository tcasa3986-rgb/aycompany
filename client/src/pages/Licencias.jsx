import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import {
  Plus, Copy, ToggleLeft, ToggleRight, RefreshCw, Pencil, Banknote,
  ExternalLink, ShieldCheck, Lock, Info,
} from 'lucide-react';
import {
  C, fmt, fmtFecha, Pagina, Cabecera, Tarjeta, Franja, Tabla, Fila, Td, Vacio,
  Boton, BotonIcono, Pildora, Insignia, Campo, Dos, Modal, entrada,
} from '../components/ui';

const ESTADOS = {
  al_dia:           { label: 'Al día',      color: C.verde },
  por_vencer:       { label: 'Por vencer',  color: C.ambar },
  en_gracia:        { label: 'En mora',     color: '#EA580C' },
  mora_sin_bloqueo: { label: 'En mora',     color: '#EA580C' },
  bloqueada:        { label: 'Bloqueada',   color: C.rojo },
  desactivada:      { label: 'Desactivada', color: C.tenue },
};
const METODOS = [['nequi', 'Nequi'], ['transferencia', 'Transferencia'], ['efectivo', 'Efectivo'], ['tarjeta', 'Tarjeta'], ['otro', 'Otro']];
const FILTROS = [['todas', 'Todas'], ['activas', 'Al día'], ['por_vencer', 'Por vencer'], ['mora', 'En mora'], ['inactivas', 'Desactivadas']];
const VACIO = { cliente_id: '', producto_id: '', dia_corte: '', dias_gracia: '0', bloquear: true,
                precio_mensual: '', fecha_vencimiento: '', notas: '' };

export default function Licencias() {
  const [licencias, setLicencias] = useState([]);
  const [clientes, setClientes]   = useState([]);
  const [productos, setProductos] = useState([]);
  const [modal, setModal]         = useState(null);   // 'nueva' | licencia
  const [modalPago, setModalPago] = useState(null);
  const [modalRenew, setModalRenew] = useState(null);
  const [form, setForm] = useState(VACIO);
  const [pago, setPago] = useState({ metodo_pago: 'nequi', meses: '1', monto: '', fecha_pago: '', notas: '' });
  const [meses, setMeses] = useState('1');
  const [filtro, setFiltro] = useState('todas');

  const cargar = () => api.get('/licencias').then(r => setLicencias(r.data.data));
  useEffect(() => {
    cargar();
    api.get('/clientes').then(r => setClientes(r.data.data));
    api.get('/productos').then(r => setProductos(r.data.data));
  }, []);

  const vistas = licencias.filter(l => {
    const e = l.ciclo?.estado;
    if (filtro === 'activas')    return l.activo && (e === 'al_dia' || e === 'por_vencer');
    if (filtro === 'por_vencer') return e === 'por_vencer';
    if (filtro === 'mora')       return ['en_gracia', 'mora_sin_bloqueo', 'bloqueada'].includes(e);
    if (filtro === 'inactivas')  return !l.activo;
    return true;
  });

  const mrr    = licencias.filter(l => l.activo).reduce((s, l) => s + Number(l.precio_efectivo || 0), 0);
  const alDia  = licencias.filter(l => l.ciclo?.estado === 'al_dia').length;
  const enMora = licencias.filter(l => ['en_gracia', 'mora_sin_bloqueo', 'bloqueada'].includes(l.ciclo?.estado)).length;

  function abrirEditar(l) {
    setForm({ cliente_id: l.cliente_id, producto_id: l.producto_id, dia_corte: l.dia_corte ?? '',
      dias_gracia: String(l.dias_gracia ?? 0), bloquear: l.bloquear !== false,
      precio_mensual: l.precio_mensual ?? '', fecha_vencimiento: l.fecha_vencimiento || '',
      notas: l.notas || '', vencimiento_visto: l.fecha_vencimiento || '' });
    setModal(l);
  }
  async function guardar(e) {
    e.preventDefault();
    try {
      if (modal === 'nueva') { await api.post('/licencias', form); toast.success('Licencia creada'); }
      else { await api.put(`/licencias/${modal.id}`, form); toast.success('Licencia actualizada'); }
      setModal(null); cargar();
    } catch (err) {
      const msg = err.response?.data?.msg || 'Error al guardar';
      toast.error(msg, { duration: err.response?.status === 409 ? 8000 : 4000 });
      if (err.response?.status === 409) { setModal(null); cargar(); }
    }
  }
  async function registrarPago(e) {
    e.preventDefault();
    try {
      const r = await api.post(`/licencias/${modalPago.id}/pago`, pago);
      toast.success(r.data.msg); setModalPago(null);
      setPago({ metodo_pago: 'nequi', meses: '1', monto: '', fecha_pago: '', notas: '' }); cargar();
    } catch (err) { toast.error(err.response?.data?.msg || 'Error al registrar el pago'); }
  }
  async function toggle(l) {
    if (l.activo && !confirm(`¿Desactivar la licencia de ${l.cliente?.nombre}?\n\nSu sistema dejará de funcionar.`)) return;
    const r = await api.put(`/licencias/${l.id}/toggle`); toast.success(r.data.msg); cargar();
  }
  async function renovar() {
    const r = await api.put(`/licencias/${modalRenew}/renovar`, { meses });
    toast.success(`Renovada hasta ${fmtFecha(r.data.fecha_vencimiento)}`); setModalRenew(null); cargar();
  }
  const copiar = k => { navigator.clipboard.writeText(k); toast.success('Clave copiada'); };

  function ciclo(l) {
    if (l.bloquear === false) return { texto: 'nunca bloquea', icono: <ShieldCheck size={13} />, color: C.violeta };
    if (Number(l.dias_gracia) > 0) return { texto: `${l.dias_gracia} días de tolerancia`, icono: null, color: C.tenue };
    return { texto: 'bloquea el mismo día', icono: <Lock size={13} />, color: C.ambar };
  }
  function detalle(l) {
    const c = l.ciclo || {};
    if (!l.activo) return 'Desactivada a mano';
    if (c.estado === 'bloqueada')        return `${c.dias_mora} días de mora`;
    if (c.estado === 'en_gracia')        return `${c.dias_mora} días de mora · se bloquea el ${fmtFecha(c.fecha_bloqueo)}`;
    if (c.estado === 'mora_sin_bloqueo') return `${c.dias_mora} días de mora`;
    if (c.dias_restantes === 0)          return 'Vence hoy';
    return `en ${c.dias_restantes} días`;
  }

  return (
    <Pagina>
      <Cabecera titulo="Licencias" sub="Cada sistema se bloquea solo si el cliente no paga">
        <Boton onClick={() => { setForm(VACIO); setModal('nueva'); }}><Plus size={16} /> Nueva licencia</Boton>
      </Cabecera>

      <div style={{ display: 'flex', gap: 8, marginBottom: 16, flexWrap: 'wrap' }}>
        {FILTROS.map(([v, l]) => (
          <Pildora key={v} activa={filtro === v} onClick={() => setFiltro(v)}>
            {l}{v === 'mora' && enMora > 0 && <span style={{ marginLeft: 6, display: 'inline-block', width: 6, height: 6, borderRadius: '50%', background: filtro === v ? '#fff' : C.rojo }} />}
          </Pildora>
        ))}
      </div>

      <div style={{ display: 'flex', gap: 'clamp(14px, 3vw, 28px)', alignItems: 'center', marginBottom: 20, flexWrap: 'wrap' }}>
        {[['Recurrente', fmt(mrr), C.verde], ['Al día', alDia, C.tinta], ['En mora', enMora, enMora > 0 ? C.rojo : C.tinta]].map(([r, v, col], i) => (
          <div key={r} style={{ display: 'flex', alignItems: 'center', gap: 'clamp(14px, 3vw, 28px)' }}>
            {i > 0 && <span style={{ width: 1, height: 30, background: C.borde }} />}
            <div>
              <div style={{ fontSize: '.67rem', fontWeight: 700, color: C.tenue, textTransform: 'uppercase', letterSpacing: '.06em' }}>{r}</div>
              <div style={{ fontFamily: "'Space Grotesk', sans-serif", fontSize: '1.15rem', fontWeight: 750, color: col, marginTop: 2 }}>{v}</div>
            </div>
          </div>
        ))}
      </div>

      <Tarjeta style={{ padding: 'clamp(14px, 2vw, 20px) clamp(8px, 1.5vw, 14px)' }}>
        <Tabla cabeceras={['Cliente', 'Sistema', 'Mensualidad', 'Ciclo', 'Vence', 'Estado', 'Acciones']} min={900}>
          {vistas.map(l => {
            const e = ESTADOS[l.ciclo?.estado] || ESTADOS.desactivada;
            const ci = ciclo(l);
            const especial = l.bloquear === false;
            return (
              <Fila key={l.id} resaltada={especial ? '#F5F3FF' : undefined}>
                <Td>
                  <strong style={{ color: C.tinta }}>{l.cliente?.nombre}</strong>
                  <div style={{ fontSize: '.73rem', color: C.tenue }}>{l.cliente?.telefono}</div>
                </Td>
                <Td>{l.producto?.nombre}</Td>
                <Td style={{ fontWeight: 650, color: C.tinta, whiteSpace: 'nowrap' }}>{fmt(l.precio_efectivo)}</Td>
                <Td>
                  {l.dia_corte ? <span style={{ color: C.tinta }}>Día <strong>{l.dia_corte}</strong></span> : <span style={{ color: C.tenue }}>Sin corte</span>}
                  <div style={{ fontSize: '.73rem', color: ci.color, display: 'flex', alignItems: 'center', gap: 4, marginTop: 2 }}>
                    {ci.icono}{ci.texto}
                  </div>
                </Td>
                <Td style={{ whiteSpace: 'nowrap' }}>{fmtFecha(l.fecha_vencimiento)}</Td>
                <Td>
                  <Insignia color={e.color}>{e.label}</Insignia>
                  <div style={{ fontSize: '.72rem', color: C.tenue, marginTop: 4 }}>{detalle(l)}</div>
                </Td>
                <Td style={{ whiteSpace: 'nowrap' }}>
                  <BotonIcono color={C.verde} titulo="Registrar pago" onClick={() => { setModalPago(l); setPago(p => ({ ...p, monto: '' })); }}><Banknote size={15} /></BotonIcono>
                  <BotonIcono color={C.azul} titulo="Editar ciclo" onClick={() => abrirEditar(l)}><Pencil size={14} /></BotonIcono>
                  <BotonIcono color={C.ambar} titulo="Renovar sin pago" onClick={() => setModalRenew(l.id)}><RefreshCw size={14} /></BotonIcono>
                  <BotonIcono color={l.activo ? C.rojo : C.verde} titulo={l.activo ? 'Desactivar' : 'Activar'} onClick={() => toggle(l)}>
                    {l.activo ? <ToggleRight size={15} /> : <ToggleLeft size={15} />}
                  </BotonIcono>
                  <BotonIcono color={C.tenue} titulo="Copiar clave" onClick={() => copiar(l.license_key)}><Copy size={13} /></BotonIcono>
                  <a href={`/pagar/${l.license_key}`} target="_blank" rel="noreferrer"
                     title="Abrir la página de pago del cliente" aria-label="Página de pago"
                     style={{ width: 34, height: 34, borderRadius: 9, background: C.tenue + '18',
                              color: C.tenue, display: 'inline-flex', alignItems: 'center',
                              justifyContent: 'center', verticalAlign: 'middle' }}>
                    <ExternalLink size={13} />
                  </a>
                </Td>
              </Fila>
            );
          })}
          {vistas.length === 0 && <Vacio cols={7}>No hay licencias en este filtro</Vacio>}
        </Tabla>

        {licencias.some(l => l.bloquear === false) && (
          <div style={{ display: 'flex', gap: 8, alignItems: 'flex-start', marginTop: 14, padding: '0 14px',
                        fontSize: '.78rem', color: C.tenue }}>
            <Info size={14} style={{ flexShrink: 0, marginTop: 1 }} />
            <span>Las filas en violeta nunca se bloquean, por decisión tuya. Las demás se apagan solas si el cliente no paga.</span>
          </div>
        )}
      </Tarjeta>

      {/* ── Modales ─────────────────────────────────────────────────── */}
      {modal && (
        <Modal ancho={580} titulo={modal === 'nueva' ? 'Nueva licencia' : `Editar — ${modal.cliente?.nombre}`} onCerrar={() => setModal(null)}>
          <form onSubmit={guardar}>
            <Dos>
              <Campo label="Cliente">
                <select value={form.cliente_id} onChange={e => setForm({ ...form, cliente_id: e.target.value })} required style={entrada}>
                  <option value="">Seleccionar…</option>
                  {clientes.map(c => <option key={c.id} value={c.id}>{c.nombre}</option>)}
                </select>
              </Campo>
              <Campo label="Sistema">
                <select value={form.producto_id} onChange={e => setForm({ ...form, producto_id: e.target.value })} required style={entrada}>
                  <option value="">Seleccionar…</option>
                  {productos.map(p => <option key={p.id} value={p.id}>{p.nombre} — {fmt(p.precio_mensual)}/mes</option>)}
                </select>
              </Campo>
            </Dos>
            <Dos>
              <Campo label="Día de corte" pista="Día del mes en que vence. Vacío = sin ciclo fijo.">
                <input type="number" min="1" max="31" value={form.dia_corte} onChange={e => setForm({ ...form, dia_corte: e.target.value })} placeholder="Ej: 20" style={entrada} />
              </Campo>
              <Campo label="Días de tolerancia" pista="0 = se bloquea el mismo día del corte.">
                <input type="number" min="0" max="60" value={form.dias_gracia} onChange={e => setForm({ ...form, dias_gracia: e.target.value })} disabled={!form.bloquear} style={entrada} />
              </Campo>
            </Dos>
            <Dos>
              <Campo label="Mensualidad propia" pista="Vacío = usa el precio del sistema.">
                <input type="number" min="0" step="1000" value={form.precio_mensual} onChange={e => setForm({ ...form, precio_mensual: e.target.value })} placeholder="250000" style={entrada} />
              </Campo>
              <Campo label={modal === 'nueva' ? 'Vencimiento inicial' : 'Vencimiento actual'} pista={modal === 'nueva' ? 'Vacío = se calcula con el día de corte.' : 'Cámbialo solo para corregir.'}>
                <input type="date" value={form.fecha_vencimiento} onChange={e => setForm({ ...form, fecha_vencimiento: e.target.value })} style={entrada} />
              </Campo>
            </Dos>
            <label style={{ display: 'flex', alignItems: 'center', gap: 9, fontSize: '.87rem', margin: '2px 0 14px', cursor: 'pointer', color: '#334155' }}>
              <input type="checkbox" checked={form.bloquear} onChange={e => setForm({ ...form, bloquear: e.target.checked })} />
              Bloquear el sistema del cliente si no paga
            </label>
            {!form.bloquear && (
              <Franja color={C.violeta} icono={<ShieldCheck size={18} />}>
                Este cliente <strong>nunca</strong> se va a bloquear. Solo te avisará la mora.
              </Franja>
            )}
            <Campo label="Notas">
              <input value={form.notas} onChange={e => setForm({ ...form, notas: e.target.value })} placeholder="Ej: paga por Nequi los 20" style={entrada} />
            </Campo>
            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 6 }}>
              <Boton type="button" variante="borde" onClick={() => setModal(null)}>Cancelar</Boton>
              <Boton type="submit">{modal === 'nueva' ? 'Crear licencia' : 'Guardar cambios'}</Boton>
            </div>
          </form>
        </Modal>
      )}

      {modalPago && (
        <Modal ancho={440} titulo="Registrar pago" onCerrar={() => setModalPago(null)}
          sub={`${modalPago.cliente?.nombre} · ${fmt(modalPago.precio_efectivo)}/mes${modalPago.dia_corte ? ` · corte el día ${modalPago.dia_corte}` : ''}`}>
          <form onSubmit={registrarPago}>
            <Dos>
              <Campo label="Método">
                <select value={pago.metodo_pago} onChange={e => setPago({ ...pago, metodo_pago: e.target.value })} style={entrada}>
                  {METODOS.map(([v, l]) => <option key={v} value={v}>{l}</option>)}
                </select>
              </Campo>
              <Campo label="Meses pagados">
                <select value={pago.meses} onChange={e => setPago({ ...pago, meses: e.target.value })} style={entrada}>
                  {[1, 2, 3, 6, 12].map(m => <option key={m} value={m}>{m} mes{m > 1 ? 'es' : ''}</option>)}
                </select>
              </Campo>
            </Dos>
            <Dos>
              <Campo label="Monto" pista={`Vacío = ${fmt(Number(modalPago.precio_efectivo) * Number(pago.meses))}`}>
                <input type="number" min="0" value={pago.monto} onChange={e => setPago({ ...pago, monto: e.target.value })} style={entrada} />
              </Campo>
              <Campo label="Fecha" pista="Vacío = hoy">
                <input type="date" value={pago.fecha_pago} onChange={e => setPago({ ...pago, fecha_pago: e.target.value })} style={entrada} />
              </Campo>
            </Dos>
            <Campo label="Referencia">
              <input value={pago.notas} onChange={e => setPago({ ...pago, notas: e.target.value })} placeholder="Ej: Nequi #12345" style={entrada} />
            </Campo>
            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 6 }}>
              <Boton type="button" variante="borde" onClick={() => setModalPago(null)}>Cancelar</Boton>
              <Boton type="submit" color={C.verde}>Registrar y renovar</Boton>
            </div>
          </form>
        </Modal>
      )}

      {modalRenew && (
        <Modal ancho={380} titulo="Renovar sin pago" onCerrar={() => setModalRenew(null)}
               sub="Cortesía o ajuste. No registra plata ni genera factura.">
          <Campo label="Agregar meses">
            <select value={meses} onChange={e => setMeses(e.target.value)} style={entrada}>
              {[1, 3, 6, 12].map(m => <option key={m} value={m}>{m} mes{m > 1 ? 'es' : ''}</option>)}
            </select>
          </Campo>
          <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 8 }}>
            <Boton variante="borde" onClick={() => setModalRenew(null)}>Cancelar</Boton>
            <Boton color={C.ambar} onClick={renovar}>Renovar</Boton>
          </div>
        </Modal>
      )}
    </Pagina>
  );
}
