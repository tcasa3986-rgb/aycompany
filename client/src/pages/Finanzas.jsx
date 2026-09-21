import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, Trash2, Pencil, AlertTriangle, Target, Server } from 'lucide-react';
import {
  C, fmt, fmtFecha, Pagina, Cabecera, Tarjeta, Cifra, Rejilla, Franja, Probable,
  Tabla, Fila, Td, Vacio, Boton, BotonIcono, Pildora, Insignia, Barra,
  Campo, Dos, Modal, entrada,
} from '../components/ui';

const CATEGORIAS = {
  empresa: {
    ingreso: ['mensualidad', 'cuota_contrato', 'anticipo_contrato', 'marketing', 'pagina_web', 'otro'],
    egreso:  ['hosting', 'apis_ia', 'dominios', 'herramientas', 'sueldo', 'impuestos', 'comisiones', 'publicidad', 'otro'],
  },
  personal: {
    ingreso: ['sueldo', 'alquiler_moto', 'otro'],
    egreso:  ['arriendo', 'recibos', 'comida', 'transporte', 'cuota_moto', 'celular', 'gimnasio', 'salud', 'educacion', 'ropa', 'deuda', 'ahorro_meta', 'otro'],
  },
};
const VACIO = { tipo: 'egreso', ambito: 'empresa', categoria: 'otro', concepto: '', monto: '',
                fecha: '', metodo_pago: 'transferencia', recurrente: false, notas: '' };
const PESTANAS = [['tablero', 'Tablero'], ['movimientos', 'Movimientos'], ['deudas', 'Deudas'], ['metas', 'Metas']];

export default function Finanzas() {
  const [params, setParams] = useSearchParams();
  const tab = PESTANAS.some(([v]) => v === params.get('tab')) ? params.get('tab') : 'tablero';
  const [r, setR]       = useState(null);
  const [movs, setMovs] = useState([]);
  const [modal, setModal] = useState(false);
  const [form, setForm]   = useState(VACIO);
  const [editId, setEditId] = useState(null);
  const [filtro, setFiltro] = useState({ ambito: '', tipo: '' });

  const cargar = () => {
    api.get('/finanzas/resumen').then(x => setR(x.data));
    const q = new URLSearchParams(Object.entries(filtro).filter(([, v]) => v)).toString();
    api.get('/finanzas/movimientos' + (q ? '?' + q : '')).then(x => setMovs(x.data.data));
  };
  useEffect(cargar, [filtro.ambito, filtro.tipo]);

  const irA = t => setParams(t === 'tablero' ? {} : { tab: t });

  async function guardar(e) {
    e.preventDefault();
    try {
      const x = editId
        ? await api.put(`/finanzas/movimientos/${editId}`, form)
        : await api.post('/finanzas/movimientos', form);
      toast.success(x.data.msg); cerrarModal(); cargar();
    } catch (err) { toast.error(err.response?.data?.msg || 'Error al guardar'); }
  }
  function abrirNuevo() { setEditId(null); setForm(VACIO); setModal(true); }
  function abrirEdicion(m) {
    setEditId(m.id);
    setForm({
      tipo: m.tipo, ambito: m.ambito, categoria: m.categoria, concepto: m.concepto,
      monto: String(m.monto), fecha: String(m.fecha).slice(0, 10),
      metodo_pago: m.metodo_pago || 'transferencia', recurrente: !!m.recurrente, notas: m.notas || '',
    });
    setModal(true);
  }
  function cerrarModal() { setModal(false); setEditId(null); setForm(VACIO); }
  async function eliminar(m) {
    if (!confirm(`¿Eliminar "${m.concepto}"?`)) return;
    try { const x = await api.delete(`/finanzas/movimientos/${m.id}`); toast.success(x.data.msg); cargar(); }
    catch (err) { toast.error(err.response?.data?.msg || 'Error'); }
  }
  async function abonar(d) {
    const v = prompt(`Abonar a "${d.acreedor}".\nSaldo actual: ${fmt(d.saldo)}\n\n¿Cuánto abonas?`);
    if (!v) return;
    try { const x = await api.post(`/finanzas/deudas/${d.id}/abonar`, { monto: Number(v) }); toast.success(x.data.msg); cargar(); }
    catch (err) { toast.error(err.response?.data?.msg || 'Error'); }
  }
  async function aportar(m) {
    const v = prompt(`Ahorrar para "${m.nombre}".\nFaltan ${fmt(m.falta)}\n\n¿Cuánto aportas?`);
    if (!v) return;
    try { const x = await api.post(`/finanzas/metas/${m.id}/aportar`, { monto: Number(v) }); toast.success(x.data.msg); cargar(); }
    catch (err) { toast.error(err.response?.data?.msg || 'Error'); }
  }

  if (!r) return <Pagina><p style={{ color: C.tenue }}>Cargando…</p></Pagina>;

  const { recurrente, gasto_fijo, margen, por_cobrar, deudas, metas, concentracion, mes } = r;
  const tratos = r.tratos_por_cerrar || { cantidad: 0, detalle: [] };
  const mayor = concentracion[0];
  const mesesDeuda = margen.con_cuotas > 0 ? Math.ceil(deudas.total / margen.con_cuotas) : null;
  const pctLibre = recurrente.licencias > 0 ? Math.round(margen.real / recurrente.licencias * 100) : 0;

  return (
    <Pagina>
      <Cabecera titulo="Finanzas"
        sub={`Este mes entra ${fmt(mes.ingresos)} y sale ${fmt(mes.egresos)} · ${mes.movimientos} movimientos`}>
        <Boton onClick={() => { setEditId(null); setForm({ ...VACIO, fecha: r.hoy }); setModal(true); }}>
          <Plus size={16} /> Registrar movimiento
        </Boton>
      </Cabecera>

      <div style={{ display: 'flex', gap: 8, marginBottom: 22, flexWrap: 'wrap' }}>
        {PESTANAS.map(([v, l]) => (
          <Pildora key={v} activa={tab === v} onClick={() => irA(v)}>{l}</Pildora>
        ))}
      </div>

      {tab === 'tablero' && (
        <>
          <Rejilla min={240}>
            <Cifra rotulo="Entra al mes" valor={fmt(recurrente.licencias)} color={C.verde}
                   pie="Mensualidades que no se acaban" />
            <Cifra rotulo="Sale al mes" valor={fmt(gasto_fijo.total)} color={C.rojo}
                   pie={`Empresa ${fmt(gasto_fijo.empresa)} · Personal ${fmt(gasto_fijo.personal)}`} />
            <Cifra rotulo="Te queda libre" valor={fmt(margen.real)} tono={C.azul}
                   pie={`${pctLibre}% de lo que entra · sin contar cuotas de contrato`} />
          </Rejilla>

          <div style={{ height: 18 }} />

          {r.infraestructura?.items === 0 && (
            <Franja color={C.ambar} icono={<AlertTriangle size={19} />}
              accion={<Boton color={C.ambar} onClick={() => (window.location.href = '/costos')} style={{ padding: '8px 14px', minHeight: 36 }}>Medir ahora</Boton>}>
              <strong>Falta medir Railway, dominios y APIs.</strong> Tu margen real es menor al que ves arriba.
            </Franja>
          )}

          {recurrente.descuadre_mensualidad !== 0 && (
            <Franja color={C.ambar} icono={<AlertTriangle size={19} />}>
              Lo pactado en contratos ({fmt(recurrente.pactado_en_contratos)}) no coincide con lo que cobran las
              licencias ({fmt(recurrente.licencias)}). Diferencia de {fmt(Math.abs(recurrente.descuadre_mensualidad))}.
            </Franja>
          )}

          {mayor && mayor.pct >= 40 && (
            <Franja color={C.rojo} icono={<AlertTriangle size={19} />}>
              <strong>{mayor.cliente}</strong> es el {mayor.pct}% de tu ingreso recurrente.
              Si se cae ese cliente pierdes {fmt(mayor.monto)} al mes de golpe.
            </Franja>
          )}

          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: 16, marginTop: 4 }}>
            <Tarjeta titulo={`Te deben ${fmt(por_cobrar.total)}`} sub="Capital de contratos firmados que todavía no te entregan" acento={C.ambar}>
              <Tabla cabeceras={['Cliente', 'Recibido', 'Pendiente', 'Cuotas']} min={420}>
                {por_cobrar.contratos.filter(c => c.pendiente > 0).map(c => (
                  <Fila key={c.id}>
                    <Td><strong style={{ color: C.tinta }}>{c.cliente}</strong>
                        <div style={{ fontSize: '.74rem', color: C.tenue }}>{c.titulo}</div></Td>
                    <Td>{fmt(c.anticipo + c.abonado_saldo)}</Td>
                    <Td style={{ color: C.rojo, fontWeight: 750 }}>{fmt(c.pendiente)}</Td>
                    <Td style={{ fontSize: '.78rem' }}>{c.cuotas_faltantes} × {fmt(c.cuota_mensual)}
                      {c.dia_cobro ? <div style={{ color: C.tenue }}>día {c.dia_cobro}</div> : null}</Td>
                  </Fila>
                ))}
                {por_cobrar.contratos.every(c => c.pendiente <= 0) && <Vacio cols={4}>Nada pendiente de cobro</Vacio>}
              </Tabla>
            </Tarjeta>

            <Tarjeta titulo="De dónde viene tu plata" sub="Peso de cada cliente en el recurrente">
              {concentracion.map((c, i) => (
                <div key={i} style={{ marginBottom: 13 }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '.85rem', marginBottom: 5 }}>
                    <span style={{ color: C.tinta, fontWeight: 600 }}>{c.cliente}</span>
                    <span style={{ color: C.texto }}>{fmt(c.monto)} · <strong style={{ color: c.pct >= 40 ? C.rojo : C.texto }}>{c.pct}%</strong></span>
                  </div>
                  <Barra pct={c.pct} color={c.pct >= 40 ? C.rojo : C.azul} alto={6} />
                </div>
              ))}
            </Tarjeta>
          </div>

          {tratos.cantidad > 0 && (
            <div style={{ marginTop: 16 }}>
              <Probable titulo={`Sin cerrar todavía — ${fmt(tratos.valor_unico)}${tratos.valor_mensual > 0 ? ` + ${fmt(tratos.valor_mensual)}/mes` : ''}`}>
                <p style={{ fontSize: '.83rem', color: '#78350F', margin: '0 0 14px' }}>
                  Plata probable. <strong>No está sumada en ninguna cifra de arriba.</strong> Ponderado por
                  probabilidad: {fmt(tratos.ponderado_unico)}{tratos.ponderado_mensual > 0 ? ` + ${fmt(tratos.ponderado_mensual)}/mes` : ''}.
                </p>
                <Tabla cabeceras={['Trato', 'Pago único', 'Mensual', 'Probabilidad']} min={460}>
                  {tratos.detalle.map(t => (
                    <Fila key={t.id}>
                      <Td><strong style={{ color: C.tinta }}>{t.nombre}</strong>
                          {t.empresa && <div style={{ fontSize: '.74rem', color: C.tenue }}>{t.empresa}</div>}</Td>
                      <Td>{t.valor_unico > 0 ? fmt(t.valor_unico) : '—'}</Td>
                      <Td>{t.valor_mensual > 0 ? fmt(t.valor_mensual) : '—'}</Td>
                      <Td><div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                        <div style={{ width: 54 }}><Barra pct={t.probabilidad} alto={6}
                          color={t.probabilidad >= 70 ? C.verde : t.probabilidad >= 40 ? C.ambar : C.tenue} /></div>
                        <strong>{t.probabilidad}%</strong>
                      </div></Td>
                    </Fila>
                  ))}
                </Tabla>
              </Probable>
            </div>
          )}
        </>
      )}

      {tab === 'movimientos' && (
        <>
          <div style={{ display: 'flex', gap: 9, marginBottom: 14, flexWrap: 'wrap' }}>
            <select value={filtro.ambito} onChange={e => setFiltro(f => ({ ...f, ambito: e.target.value }))} style={{ ...entrada, width: 'auto' }}>
              <option value="">Empresa y personal</option><option value="empresa">Solo empresa</option><option value="personal">Solo personal</option>
            </select>
            <select value={filtro.tipo} onChange={e => setFiltro(f => ({ ...f, tipo: e.target.value }))} style={{ ...entrada, width: 'auto' }}>
              <option value="">Entradas y salidas</option><option value="ingreso">Solo entradas</option><option value="egreso">Solo salidas</option>
            </select>
          </div>
          <Tarjeta>
            <Tabla cabeceras={['Fecha', 'Concepto', 'Categoría', 'Bolsillo', 'Monto', '']}>
              {movs.map(m => (
                <Fila key={m.id}>
                  <Td style={{ color: C.tenue, fontSize: '.8rem', whiteSpace: 'nowrap' }}>{fmtFecha(m.fecha)}</Td>
                  <Td>
                    <strong style={{ color: C.tinta }}>{m.concepto}</strong>
                    {m.recurrente && <Insignia color={C.azul}> fijo </Insignia>}
                    {m.origen !== 'manual' && <span style={{ marginLeft: 6 }}><Insignia color={C.tenue}>{m.origen}</Insignia></span>}
                  </Td>
                  <Td style={{ fontSize: '.8rem' }}>{String(m.categoria).replace(/_/g, ' ')}</Td>
                  <Td style={{ fontSize: '.8rem' }}>{m.ambito}</Td>
                  <Td style={{ fontWeight: 750, whiteSpace: 'nowrap', color: m.tipo === 'ingreso' ? C.verde : C.rojo }}>
                    {m.tipo === 'ingreso' ? '+' : '−'}{fmt(m.monto)}
                  </Td>
                  <Td style={{ whiteSpace: 'nowrap' }}>
                    <BotonIcono color={C.azul} titulo="Corregir" onClick={() => abrirEdicion(m)}><Pencil size={14} /></BotonIcono>
                    <BotonIcono color={C.rojo} titulo="Eliminar" onClick={() => eliminar(m)}><Trash2 size={14} /></BotonIcono>
                  </Td>
                </Fila>
              ))}
              {movs.length === 0 && <Vacio cols={6}>Sin movimientos todavía</Vacio>}
            </Tabla>
          </Tarjeta>
        </>
      )}

      {tab === 'deudas' && (
        <>
          <p style={{ fontSize: '.9rem', color: C.texto, marginTop: 0, marginBottom: 16 }}>
            Debes <strong style={{ color: C.rojo }}>{fmt(deudas.total)}</strong>
            {mesesDeuda && <> · a tu margen actual la saldas en <strong>{mesesDeuda} {mesesDeuda === 1 ? 'mes' : 'meses'}</strong></>}
          </p>
          {deudas.detalle.map(d => (
            <div key={d.id} style={{ marginBottom: 14 }}>
              <Tarjeta acento={C.rojo}>
                <div style={{ display: 'flex', justifyContent: 'space-between', gap: 14, flexWrap: 'wrap' }}>
                  <div>
                    <strong style={{ fontSize: '1rem', color: C.tinta }}>{d.acreedor}</strong>
                    <div style={{ fontSize: '.82rem', color: C.texto }}>{d.concepto}</div>
                    <div style={{ fontSize: '.77rem', color: C.tenue, marginTop: 4 }}>
                      {Number(d.tasa_mensual) > 0
                        ? `${d.tasa_mensual}% mensual — te cuesta ${fmt(d.interes_mensual)} al mes`
                        : 'Sin intereses'}
                    </div>
                  </div>
                  <div style={{ textAlign: 'right' }}>
                    <div style={{ fontFamily: "'Space Grotesk', sans-serif", fontSize: '1.4rem', fontWeight: 800, color: C.rojo }}>{fmt(d.saldo)}</div>
                    <div style={{ fontSize: '.75rem', color: C.tenue }}>de {fmt(d.monto_original)}</div>
                    <Boton color={C.verde} onClick={() => abonar(d)} style={{ marginTop: 9, padding: '8px 16px', minHeight: 36 }}>Abonar</Boton>
                  </div>
                </div>
                <div style={{ marginTop: 14 }}>
                  <Barra pct={d.avance_pct} color={C.verde} />
                  <div style={{ fontSize: '.74rem', color: C.tenue, marginTop: 5 }}>{d.avance_pct}% pagado</div>
                </div>
              </Tarjeta>
            </div>
          ))}
          {deudas.detalle.length === 0 && <Tarjeta><p style={{ textAlign: 'center', color: C.tenue, margin: 0 }}>Sin deudas activas</p></Tarjeta>}
        </>
      )}

      {tab === 'metas' && (
        <>
          {metas.map(m => (
            <div key={m.id} style={{ marginBottom: 14 }}>
              <Tarjeta acento={C.violeta}>
                <div style={{ display: 'flex', justifyContent: 'space-between', gap: 14, flexWrap: 'wrap' }}>
                  <div style={{ flex: '1 1 260px' }}>
                    <strong style={{ fontSize: '1rem', color: C.tinta, display: 'flex', alignItems: 'center', gap: 7 }}>
                      <Target size={16} color={C.violeta} /> {m.nombre}
                    </strong>
                    {m.descripcion && <div style={{ fontSize: '.82rem', color: C.texto, marginTop: 4 }}>{m.descripcion}</div>}
                    {Number(m.retorno_mensual) > 0 && (
                      <div style={{ fontSize: '.79rem', color: C.verde, marginTop: 6, fontWeight: 650 }}>
                        Genera {fmt(m.retorno_mensual)} al mes · se paga sola en {Math.ceil(m.monto_objetivo / m.retorno_mensual)} meses
                      </div>
                    )}
                  </div>
                  <div style={{ textAlign: 'right' }}>
                    <div style={{ fontFamily: "'Space Grotesk', sans-serif", fontSize: '1.25rem', fontWeight: 800, color: C.tinta }}>{fmt(m.monto_objetivo)}</div>
                    <div style={{ fontSize: '.75rem', color: C.tenue }}>faltan {fmt(m.falta)}</div>
                    <Boton onClick={() => aportar(m)} style={{ marginTop: 9, padding: '8px 16px', minHeight: 36 }}>Ahorrar</Boton>
                  </div>
                </div>
                <div style={{ marginTop: 14 }}>
                  <Barra pct={m.avance_pct} color={C.violeta} />
                  <div style={{ fontSize: '.74rem', color: C.tenue, marginTop: 5 }}>{m.avance_pct}% · {fmt(m.ahorrado)} ahorrado</div>
                </div>
              </Tarjeta>
            </div>
          ))}
          {metas.length === 0 && <Tarjeta><p style={{ textAlign: 'center', color: C.tenue, margin: 0 }}>Sin metas activas</p></Tarjeta>}
        </>
      )}

      {modal && (
        <Modal titulo={editId ? 'Corregir movimiento' : 'Registrar movimiento'} onCerrar={cerrarModal}>
          <form onSubmit={guardar}>
            <Dos>
              <Campo label="¿Entra o sale?">
                <select value={form.tipo} onChange={e => setForm(f => ({ ...f, tipo: e.target.value, categoria: 'otro' }))} style={entrada}>
                  <option value="egreso">Sale (gasto)</option><option value="ingreso">Entra (ingreso)</option>
                </select>
              </Campo>
              <Campo label="¿De qué bolsillo?">
                <select value={form.ambito} onChange={e => setForm(f => ({ ...f, ambito: e.target.value, categoria: 'otro' }))} style={entrada}>
                  <option value="empresa">Empresa</option><option value="personal">Personal</option>
                </select>
              </Campo>
            </Dos>
            <Campo label="Concepto">
              <input value={form.concepto} onChange={e => setForm(f => ({ ...f, concepto: e.target.value }))}
                     required style={entrada} placeholder="Ej: Cuota contrato Ferre Láser" />
            </Campo>
            <Dos>
              <Campo label="Categoría">
                <select value={form.categoria} onChange={e => setForm(f => ({ ...f, categoria: e.target.value }))} style={entrada}>
                  {(CATEGORIAS[form.ambito]?.[form.tipo] || ['otro']).map(c => <option key={c} value={c}>{c.replace(/_/g, ' ')}</option>)}
                </select>
              </Campo>
              <Campo label="Monto">
                <input type="number" min="1" value={form.monto} onChange={e => setForm(f => ({ ...f, monto: e.target.value }))} required style={entrada} />
              </Campo>
            </Dos>
            <Dos>
              <Campo label="Fecha"><input type="date" value={form.fecha} onChange={e => setForm(f => ({ ...f, fecha: e.target.value }))} style={entrada} /></Campo>
              <Campo label="Método">
                <select value={form.metodo_pago} onChange={e => setForm(f => ({ ...f, metodo_pago: e.target.value }))} style={entrada}>
                  {['transferencia', 'nequi', 'efectivo', 'tarjeta', 'mercadopago', 'otro'].map(m => <option key={m} value={m}>{m}</option>)}
                </select>
              </Campo>
            </Dos>
            <label style={{ display: 'flex', alignItems: 'center', gap: 9, fontSize: '.86rem', margin: '2px 0 14px', cursor: 'pointer', color: '#334155' }}>
              <input type="checkbox" checked={form.recurrente} onChange={e => setForm(f => ({ ...f, recurrente: e.target.checked }))} />
              Es un gasto fijo que se repite todos los meses
            </label>
            {form.ambito === 'empresa' && form.tipo === 'egreso' && form.categoria === 'sueldo' && (
              <Franja color={C.azul}>
                Al guardarlo se crea solo el ingreso espejo en tu bolsillo personal. Así separas la plata del
                negocio de la tuya sin registrar nada dos veces.
              </Franja>
            )}
            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 6 }}>
              <Boton type="button" variante="borde" onClick={cerrarModal}>Cancelar</Boton>
              <Boton type="submit">Guardar</Boton>
            </div>
          </form>
        </Modal>
      )}
    </Pagina>
  );
}
