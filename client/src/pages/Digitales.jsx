import { useState, useEffect } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, Check, X, Send, Flame, Package, Pencil } from 'lucide-react';
import { C, fmt, Pagina, Cabecera, Tarjeta, Cifra, Rejilla, Boton, BotonIcono,
         Campo, Dos, Modal, entrada, Pildora, Franja } from '../components/ui';

// Productos digitales: catálogo, embudo y caja.
//
// La pantalla está ordenada por lo que de verdad se hace en el día:
//   1. ¿Hay plata por confirmar?  ← lo primero, porque bloquea el despacho
//   2. ¿Cómo va el embudo?
//   3. El catálogo, que se toca poco.

const PASOS = [
  ['escribio',    'Escribió'],
  ['cotizado',    'Cotizado'],
  ['comprobante', 'Comprobante'],
  ['pagado',      'Pagado'],
  ['despachado',  'Despachado'],
];
const TEMP = { caliente: ['#DC2626', '#FEE2E2'], tibio: ['#F59E0B', '#FEF3C7'], frio: ['#64748B', '#F1F5F9'] };
const VACIO_P = { nombre: '', descripcion: '', precio: '', tipo: 'pdf', origen_url: '', dolor: '', objeciones: '' };
const VACIO_V = { telefono: '', nombre: '', producto_id: '', estado: 'escribio', temperatura: 'tibio', metodo: 'nequi', campana: '' };

export default function Digitales() {
  const [r, setR] = useState(null);
  const [productos, setProductos] = useState([]);
  const [ventas, setVentas] = useState([]);
  const [embudo, setEmbudo] = useState({});
  const [tab, setTab] = useState('ventas');
  const [modalP, setModalP] = useState(false);
  const [modalV, setModalV] = useState(false);
  const [formP, setFormP] = useState(VACIO_P);
  const [formV, setFormV] = useState(VACIO_V);
  const [editP, setEditP] = useState(null);

  const cargar = () => {
    api.get('/digitales/resumen?dias=7').then(x => setR(x.data));
    api.get('/digitales/productos?todos=1').then(x => setProductos(x.data.data));
    api.get('/digitales/ventas').then(x => { setVentas(x.data.data); setEmbudo(x.data.embudo); });
  };
  useEffect(cargar, []);

  async function accion(fn, msgError = 'No se pudo') {
    try { const x = await fn(); toast.success(x.data.msg); cargar(); }
    catch (err) { toast.error(err.response?.data?.msg || msgError); }
  }

  if (!r) return <Pagina><Cabecera titulo="Productos digitales" sub="Cargando…" /></Pagina>;

  const porConfirmar = ventas.filter(v => v.estado === 'comprobante');
  const porDespachar = ventas.filter(v => v.estado === 'pagado');

  return (
    <Pagina>
      <Cabecera titulo="Productos digitales" sub={`Últimos 7 días · ${r.escribieron} escribieron, ${r.compraron} compraron`}>
        <Boton onClick={() => { setFormV(VACIO_V); setModalV(true); }}><Plus size={16} /> Registrar quien escribió</Boton>
      </Cabecera>

      {/* Lo primero: plata esperando que la confirmes */}
      {porConfirmar.length > 0 && (
        <Tarjeta titulo="Comprobantes por verificar" acento={C.ambar}>
          <p style={S.aviso}>
            Nada se despacha hasta que confirmes. Cruza el monto y la hora con tu app del banco:
            los comprobantes falsos son comunes.
          </p>
          <div style={{ display: 'grid', gap: 9 }}>
            {porConfirmar.map(v => (
              <div key={v.id} style={S.confirmar}>
                <div style={{ flex: 1, minWidth: 0 }}>
                  <div style={S.nombre}>{v.nombre || v.telefono}</div>
                  <div style={S.sub}>
                    <strong style={{ color: C.tinta }}>{fmt(v.monto)}</strong> · {v.metodo}
                    {v.producto?.nombre ? ` · ${v.producto.nombre}` : ''}
                  </div>
                  {v.comprobante && <div style={S.comprobante}>{v.comprobante}</div>}
                </div>
                <Boton color={C.verde} onClick={() => accion(() => api.post(`/digitales/ventas/${v.id}/confirmar`))}>
                  <Check size={15} /> Confirmar
                </Boton>
                <Boton color={C.rojo} variante="borde"
                       onClick={() => { const m = prompt('¿Por qué no es válido?'); if (m !== null) accion(() => api.post(`/digitales/ventas/${v.id}/rechazar`, { motivo: m })); }}>
                  <X size={15} />
                </Boton>
              </div>
            ))}
          </div>
        </Tarjeta>
      )}

      {/* Pagados que faltan por entregar */}
      {porDespachar.length > 0 && (
        <Tarjeta titulo="Pagados sin despachar" acento={C.azul} style={{ marginTop: 12 }}>
          <div style={{ display: 'grid', gap: 9 }}>
            {porDespachar.map(v => (
              <div key={v.id} style={S.confirmar}>
                <div style={{ flex: 1 }}>
                  <div style={S.nombre}>{v.nombre || v.telefono}</div>
                  <div style={S.sub}>{v.producto?.nombre} · {fmt(v.monto)}</div>
                </div>
                <Boton onClick={() => { const e = prompt('Enlace de entrega (opcional)') || ''; accion(() => api.post(`/digitales/ventas/${v.id}/despachar`, { enlace: e })); }}>
                  <Send size={15} /> Despachar
                </Boton>
              </div>
            ))}
          </div>
        </Tarjeta>
      )}

      {/* Las cifras que deciden la pauta */}
      <Rejilla min={170}>
        <Cifra rotulo="Vendido (7 días)" valor={fmt(r.vendido)} color={C.verde} pie={`${r.compraron} ventas`} />
        <Cifra rotulo="De cada 100 que escriben" valor={`${r.conversion}`} pie="compran" />
        <Cifra rotulo="Ticket promedio" valor={fmt(r.ticket)} />
        <Cifra rotulo="Hoy" valor={fmt(r.hoy_vendido)} pie={`${r.hoy_ventas} ventas`} />
      </Rejilla>

      {r.compraron > 0 && (
        <Franja color={C.azul}>
          Para saber si deja plata, registra lo que gastas en anuncios en Finanzas, categoría
          <strong> publicidad</strong>. Sin eso, lo vendido no es lo ganado.
        </Franja>
      )}

      {/* El embudo */}
      <div style={S.embudo}>
        {PASOS.map(([k, l]) => (
          <div key={k} style={S.paso}>
            <div style={S.pasoNum}>{embudo[k] ?? 0}</div>
            <div style={S.pasoTxt}>{l}</div>
          </div>
        ))}
        <div style={{ ...S.paso, opacity: .55 }}>
          <div style={{ ...S.pasoNum, color: C.rojo }}>{embudo.perdido ?? 0}</div>
          <div style={S.pasoTxt}>Perdidos</div>
        </div>
      </div>

      <div style={{ display: 'flex', gap: 8, margin: '18px 0 12px' }}>
        <Pildora activa={tab === 'ventas'} onClick={() => setTab('ventas')}>Conversaciones</Pildora>
        <Pildora activa={tab === 'catalogo'} onClick={() => setTab('catalogo')}>Catálogo ({productos.length})</Pildora>
      </div>

      {tab === 'ventas' ? (
        ventas.length === 0
          ? <Tarjeta><p style={S.vacio}>Todavía no hay nadie. Cuando alguien escriba al WhatsApp, regístralo aquí.</p></Tarjeta>
          : <div style={{ display: 'grid', gap: 9 }}>
              {ventas.map(v => {
                const [col, bg] = TEMP[v.temperatura] || TEMP.tibio;
                return (
                  <div key={v.id} style={S.venta}>
                    <span style={{ ...S.punto, background: col }} />
                    <div style={{ flex: 1, minWidth: 0 }}>
                      <div style={S.nombre}>{v.nombre || v.telefono}</div>
                      <div style={S.sub}>
                        {v.producto?.nombre || 'sin producto'} · {fmt(v.monto)}
                        {v.campana ? ` · ${v.campana}` : ''}
                      </div>
                      {v.motivo_perdida && <div style={S.perdida}>Se cayó: {v.motivo_perdida}</div>}
                    </div>
                    <select
                      value={v.estado}
                      onChange={e => {
                        const nuevo = e.target.value;
                        if (nuevo === 'pagado') return toast.error('El pago se confirma arriba, con el comprobante');
                        const extra = {};
                        if (nuevo === 'perdido') { const m = prompt('¿Por qué se cayó?'); if (m === null) return; extra.motivo_perdida = m; }
                        accion(() => api.put(`/digitales/ventas/${v.id}`, { estado: nuevo, ...extra }));
                      }}
                      style={S.selectEstado}>
                      {[...PASOS, ['perdido', 'Perdido']].map(([k, l]) => <option key={k} value={k}>{l}</option>)}
                    </select>
                  </div>
                );
              })}
            </div>
      ) : (
        <>
          <Boton onClick={() => { setEditP(null); setFormP(VACIO_P); setModalP(true); }}><Plus size={16} /> Nuevo producto</Boton>
          <div style={{ display: 'grid', gap: 9, marginTop: 12 }}>
            {productos.map(p => (
              <div key={p.id} style={{ ...S.venta, opacity: p.activo ? 1 : .5 }}>
                <Package size={17} color={C.tenue} />
                <div style={{ flex: 1, minWidth: 0 }}>
                  <div style={S.nombre}>{p.nombre}</div>
                  <div style={S.sub}>{fmt(p.precio)} · {p.tipo}{p.dolor ? ` · ${p.dolor.slice(0, 60)}` : ''}</div>
                </div>
                <BotonIcono color={C.azul} titulo="Editar"
                  onClick={() => { setEditP(p.id); setFormP({ ...VACIO_P, ...p, precio: String(p.precio) }); setModalP(true); }}>
                  <Pencil size={14} />
                </BotonIcono>
              </div>
            ))}
            {productos.length === 0 && <Tarjeta><p style={S.vacio}>Agrega los productos que ya vendías antes.</p></Tarjeta>}
          </div>
        </>
      )}

      {/* Producto */}
      {modalP && (
        <Modal titulo={editP ? 'Editar producto' : 'Nuevo producto'} onCerrar={() => setModalP(false)}>
          <form onSubmit={async e => {
            e.preventDefault();
            await accion(() => editP ? api.put(`/digitales/productos/${editP}`, formP) : api.post('/digitales/productos', formP));
            setModalP(false);
          }}>
            <Campo label="Nombre"><input value={formP.nombre} onChange={e => setFormP(f => ({ ...f, nombre: e.target.value }))} required autoFocus style={entrada} /></Campo>
            <Dos>
              <Campo label="Precio"><input type="number" min="0" value={formP.precio} onChange={e => setFormP(f => ({ ...f, precio: e.target.value }))} required style={entrada} /></Campo>
              <Campo label="Tipo">
                <select value={formP.tipo} onChange={e => setFormP(f => ({ ...f, tipo: e.target.value }))} style={entrada}>
                  {['pdf', 'plantilla', 'acceso', 'curso', 'otro'].map(t => <option key={t} value={t}>{t}</option>)}
                </select>
              </Campo>
            </Dos>
            <Campo label="¿Qué problema resuelve?" pista="En las palabras del cliente. Es con lo que se vende.">
              <input value={formP.dolor} onChange={e => setFormP(f => ({ ...f, dolor: e.target.value }))} style={entrada} placeholder="Ej: No sé cómo organizar las cuentas de mi negocio" />
            </Campo>
            <Campo label="Objeciones típicas y qué responder" pista="Una por línea. Esto es lo que va a usar el bot.">
              <textarea value={formP.objeciones} onChange={e => setFormP(f => ({ ...f, objeciones: e.target.value }))} rows={3} style={{ ...entrada, resize: 'vertical' }} placeholder={'"Está caro" → ...\n"Lo consigo gratis" → ...'} />
            </Campo>
            <Campo label="Dónde está el archivo" pista="Enlace de tu Drive. NUNCA se le manda al cliente: el sistema entrega una copia.">
              <input value={formP.origen_url} onChange={e => setFormP(f => ({ ...f, origen_url: e.target.value }))} style={entrada} />
            </Campo>
            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 12 }}>
              <Boton type="button" variante="borde" onClick={() => setModalP(false)}>Cancelar</Boton>
              <Boton type="submit">Guardar</Boton>
            </div>
          </form>
        </Modal>
      )}

      {/* Venta */}
      {modalV && (
        <Modal titulo="Alguien escribió" onCerrar={() => setModalV(false)}>
          <form onSubmit={async e => {
            e.preventDefault();
            await accion(() => api.post('/digitales/ventas', formV));
            setModalV(false);
          }}>
            <Dos>
              <Campo label="Teléfono"><input value={formV.telefono} onChange={e => setFormV(f => ({ ...f, telefono: e.target.value }))} required autoFocus style={entrada} placeholder="300…" /></Campo>
              <Campo label="Nombre"><input value={formV.nombre} onChange={e => setFormV(f => ({ ...f, nombre: e.target.value }))} style={entrada} /></Campo>
            </Dos>
            <Dos>
              <Campo label="¿Qué quiere?">
                <select value={formV.producto_id} onChange={e => setFormV(f => ({ ...f, producto_id: e.target.value }))} style={entrada}>
                  <option value="">Todavía no sé</option>
                  {productos.filter(p => p.activo).map(p => <option key={p.id} value={p.id}>{p.nombre}</option>)}
                </select>
              </Campo>
              <Campo label="¿De qué anuncio vino?" pista="Para saber qué campaña trae los que pagan">
                <input value={formV.campana} onChange={e => setFormV(f => ({ ...f, campana: e.target.value }))} style={entrada} placeholder="Ej: meta-oct-plantillas" />
              </Campo>
            </Dos>
            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 12 }}>
              <Boton type="button" variante="borde" onClick={() => setModalV(false)}>Cancelar</Boton>
              <Boton type="submit">Registrar</Boton>
            </div>
          </form>
        </Modal>
      )}
    </Pagina>
  );
}

const S = {
  aviso: { fontSize: '.8rem', color: C.tenue, margin: '0 0 12px' },
  confirmar: { display: 'flex', alignItems: 'center', gap: 10, background: '#fff', borderRadius: 12, padding: '11px 13px', flexWrap: 'wrap' },
  venta: { display: 'flex', alignItems: 'center', gap: 11, background: '#fff', borderRadius: 12, padding: '11px 13px', boxShadow: '0 1px 2px rgba(15,23,42,.06)' },
  punto: { width: 9, height: 9, borderRadius: '50%', flexShrink: 0 },
  nombre: { fontWeight: 700, color: C.tinta, fontSize: '.92rem' },
  sub: { fontSize: '.78rem', color: C.tenue, marginTop: 2 },
  comprobante: { fontSize: '.74rem', color: '#92400E', background: '#FEF3C7', padding: '4px 8px', borderRadius: 7, marginTop: 5, display: 'inline-block' },
  perdida: { fontSize: '.74rem', color: C.rojo, marginTop: 3 },
  selectEstado: { ...entrada, width: 'auto', fontSize: '.78rem', padding: '7px 9px' },
  embudo: { display: 'flex', gap: 7, marginTop: 14, overflowX: 'auto', paddingBottom: 4 },
  paso: { flex: '1 0 86px', background: '#fff', borderRadius: 12, padding: '12px 8px', textAlign: 'center', boxShadow: '0 1px 2px rgba(15,23,42,.06)' },
  pasoNum: { fontFamily: "'Space Grotesk', system-ui, sans-serif", fontSize: '1.5rem', fontWeight: 800, color: C.tinta, lineHeight: 1 },
  pasoTxt: { fontSize: '.7rem', color: C.tenue, marginTop: 4 },
  vacio: { textAlign: 'center', color: C.tenue, margin: 0, padding: '18px 0', fontSize: '.88rem' },
};
