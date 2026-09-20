import { useEffect, useState } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Plus, X, Trash2, TrendingUp, TrendingDown, AlertTriangle, Target, Landmark } from 'lucide-react';

const fmt = n => '$' + Number(n || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 });
const fmtFecha = s => s ? new Date(s + 'T12:00:00').toLocaleDateString('es-CO', { day: '2-digit', month: 'short' }) : '—';

const CATEGORIAS = {
  empresa: {
    ingreso: ['mensualidad', 'cuota_contrato', 'anticipo_contrato', 'marketing', 'pagina_web', 'otro'],
    egreso:  ['hosting', 'apis_ia', 'dominios', 'herramientas', 'sueldo', 'impuestos', 'comisiones', 'publicidad', 'otro'],
  },
  personal: {
    ingreso: ['sueldo', 'alquiler_moto', 'otro'],
    egreso:  ['arriendo', 'recibos', 'comida', 'transporte', 'celular', 'gimnasio', 'salud', 'educacion', 'deuda', 'ahorro_meta', 'otro'],
  },
};
const MOV_VACIO = { tipo: 'egreso', ambito: 'empresa', categoria: 'otro', concepto: '', monto: '', fecha: '', metodo_pago: 'transferencia', recurrente: false, notas: '' };

export default function Finanzas() {
  const [resumen, setResumen] = useState(null);
  const [movs, setMovs]       = useState([]);
  const [tab, setTab]         = useState('tablero');
  const [modal, setModal]     = useState(false);
  const [form, setForm]       = useState(MOV_VACIO);
  const [filtro, setFiltro]   = useState({ ambito: '', tipo: '' });

  const cargar = () => {
    api.get('/finanzas/resumen').then(r => setResumen(r.data));
    const q = new URLSearchParams(Object.entries(filtro).filter(([, v]) => v)).toString();
    api.get('/finanzas/movimientos' + (q ? '?' + q : '')).then(r => setMovs(r.data.data));
  };
  useEffect(cargar, [filtro.ambito, filtro.tipo]);

  async function guardar(e) {
    e.preventDefault();
    try {
      const r = await api.post('/finanzas/movimientos', form);
      toast.success(r.data.msg);
      setModal(false); setForm(MOV_VACIO); cargar();
    } catch (err) { toast.error(err.response?.data?.msg || 'Error al guardar'); }
  }

  async function eliminar(m) {
    if (!confirm(`¿Eliminar "${m.concepto}"?`)) return;
    try { const r = await api.delete(`/finanzas/movimientos/${m.id}`); toast.success(r.data.msg); cargar(); }
    catch (err) { toast.error(err.response?.data?.msg || 'Error'); }
  }

  async function abonarDeuda(d) {
    const v = prompt(`Abonar a "${d.acreedor}". Saldo actual: ${fmt(d.saldo)}\n\n¿Cuánto abonas?`);
    if (!v) return;
    try { const r = await api.post(`/finanzas/deudas/${d.id}/abonar`, { monto: Number(v) }); toast.success(r.data.msg); cargar(); }
    catch (err) { toast.error(err.response?.data?.msg || 'Error'); }
  }

  async function aportarMeta(m) {
    const v = prompt(`Ahorrar para "${m.nombre}". Faltan ${fmt(m.falta)}\n\n¿Cuánto aportas?`);
    if (!v) return;
    try { const r = await api.post(`/finanzas/metas/${m.id}/aportar`, { monto: Number(v) }); toast.success(r.data.msg); cargar(); }
    catch (err) { toast.error(err.response?.data?.msg || 'Error'); }
  }

  if (!resumen) return <div style={{ padding: 32, color: '#94a3b8' }}>Cargando…</div>;

  const { recurrente, gasto_fijo, margen, por_cobrar, deudas, metas, concentracion, mes } = resumen;
  const mayor = concentracion[0];
  const mesesDeuda = margen.con_cuotas > 0 ? Math.ceil(deudas.total / margen.con_cuotas) : null;

  return (
    <div style={{ padding: 32 }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 6, flexWrap: 'wrap', gap: 10 }}>
        <h1 style={{ fontSize: '1.4rem', fontWeight: 700 }}>Finanzas</h1>
        <button onClick={() => { setForm({ ...MOV_VACIO, fecha: resumen.hoy }); setModal(true); }} style={btn('#4f46e5')}>
          <Plus size={16} /> Registrar movimiento
        </button>
      </div>
      <p style={{ fontSize: '.85rem', color: '#64748b', marginBottom: 20 }}>
        Mes en curso: entra {fmt(mes.ingresos)} · sale {fmt(mes.egresos)} ·{' '}
        <strong style={{ color: mes.balance >= 0 ? '#16a34a' : '#dc2626' }}>balance {fmt(mes.balance)}</strong>
      </p>

      <div style={{ display: 'flex', gap: 8, marginBottom: 22 }}>
        {[['tablero', 'Tablero'], ['movimientos', 'Movimientos'], ['deudas', 'Deudas'], ['metas', 'Metas']].map(([v, l]) => (
          <button key={v} onClick={() => setTab(v)} style={{ padding: '7px 16px', borderRadius: 20, border: 'none', fontWeight: 600, fontSize: '.85rem', cursor: 'pointer', background: tab === v ? '#4f46e5' : '#e2e8f0', color: tab === v ? '#fff' : '#64748b' }}>{l}</button>
        ))}
      </div>

      {tab === 'tablero' && (
        <>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(230px, 1fr))', gap: 16, marginBottom: 22 }}>
            <Tarjeta icono={<TrendingUp size={18} />} color="#16a34a" titulo="Recurrente real" valor={fmt(recurrente.licencias)}
              pie="Mensualidades que NO se acaban" />
            <Tarjeta icono={<TrendingDown size={18} />} color="#dc2626" titulo="Gasto fijo" valor={fmt(gasto_fijo.total)}
              pie={`Empresa ${fmt(gasto_fijo.empresa)} · Personal ${fmt(gasto_fijo.personal)}`} />
            <Tarjeta icono={<Landmark size={18} />} color={margen.real >= 0 ? '#0ea5e9' : '#dc2626'} titulo="Margen real" valor={fmt(margen.real)}
              pie="Sin contar cuotas de contrato" destacado />
            <Tarjeta icono={<AlertTriangle size={18} />} color="#f59e0b" titulo="Por cobrar" valor={fmt(por_cobrar.total)}
              pie="Capital ya firmado, sin recibir" destacado />
          </div>

          {recurrente.descuadre_mensualidad !== 0 && (
            <Aviso color="#f59e0b">
              Lo pactado en contratos ({fmt(recurrente.pactado_en_contratos)}) no coincide con lo que cobran las licencias
              ({fmt(recurrente.licencias)}). Diferencia: {fmt(recurrente.descuadre_mensualidad)}. Revisa cuál de los dos está mal.
            </Aviso>
          )}

          {mayor && mayor.pct >= 40 && (
            <Aviso color="#dc2626">
              <strong>{mayor.cliente}</strong> es el {mayor.pct}% de tu ingreso recurrente. Si se cae ese cliente,
              pierdes {fmt(mayor.monto)} al mes de golpe.
            </Aviso>
          )}

          <Seccion titulo={`Por cobrar — ${fmt(por_cobrar.total)}`} sub="Capital de contratos ya firmados que todavía no te entregan">
            <Tabla cabeceras={['Cliente', 'Contrato', 'Valor', 'Recibido', 'Pendiente', 'Cuotas']}>
              {por_cobrar.contratos.filter(c => c.pendiente > 0).map(c => (
                <tr key={c.id} style={fila}>
                  <td style={td}><strong>{c.cliente}</strong></td>
                  <td style={td}>{c.titulo}</td>
                  <td style={td}>{fmt(c.total)}</td>
                  <td style={td}>{fmt(c.anticipo + c.abonado_saldo)}</td>
                  <td style={{ ...td, color: '#dc2626', fontWeight: 700 }}>{fmt(c.pendiente)}</td>
                  <td style={td}>{c.cuotas_faltantes} × {fmt(c.cuota_mensual)}{c.dia_cobro ? ` (día ${c.dia_cobro})` : ''}</td>
                </tr>
              ))}
              {por_cobrar.contratos.every(c => c.pendiente <= 0) && <tr><td colSpan={6} style={vacio}>Nada pendiente de cobro</td></tr>}
            </Tabla>
          </Seccion>

          <Seccion titulo="De dónde viene tu ingreso recurrente">
            <Tabla cabeceras={['Cliente', 'Sistema', 'Al mes', 'Peso', 'Estado']}>
              {concentracion.map((c, i) => (
                <tr key={i} style={fila}>
                  <td style={td}><strong>{c.cliente}</strong></td>
                  <td style={td}>{c.sistema}</td>
                  <td style={td}>{fmt(c.monto)}</td>
                  <td style={td}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                      <div style={{ width: 70, height: 6, background: '#f1f5f9', borderRadius: 3, overflow: 'hidden' }}>
                        <div style={{ width: `${c.pct}%`, height: '100%', background: c.pct >= 40 ? '#dc2626' : '#4f46e5' }} />
                      </div>
                      <span style={{ fontWeight: 600, color: c.pct >= 40 ? '#dc2626' : '#475569' }}>{c.pct}%</span>
                    </div>
                  </td>
                  <td style={td}><Estado e={c.estado} /></td>
                </tr>
              ))}
            </Tabla>
          </Seccion>
        </>
      )}

      {tab === 'movimientos' && (
        <>
          <div style={{ display: 'flex', gap: 10, marginBottom: 14 }}>
            <select value={filtro.ambito} onChange={e => setFiltro(f => ({ ...f, ambito: e.target.value }))} style={select}>
              <option value="">Empresa y personal</option><option value="empresa">Solo empresa</option><option value="personal">Solo personal</option>
            </select>
            <select value={filtro.tipo} onChange={e => setFiltro(f => ({ ...f, tipo: e.target.value }))} style={select}>
              <option value="">Entradas y salidas</option><option value="ingreso">Solo entradas</option><option value="egreso">Solo salidas</option>
            </select>
          </div>
          <Tabla cabeceras={['Fecha', 'Concepto', 'Categoría', 'Ámbito', 'Monto', '']}>
            {movs.map(m => (
              <tr key={m.id} style={fila}>
                <td style={td}>{fmtFecha(m.fecha)}</td>
                <td style={td}>
                  <strong>{m.concepto}</strong>
                  {m.recurrente && <span style={chip('#4f46e5')}>fijo</span>}
                  {m.origen !== 'manual' && <span style={chip('#64748b')}>{m.origen}</span>}
                </td>
                <td style={td}>{m.categoria}</td>
                <td style={td}>{m.ambito}</td>
                <td style={{ ...td, fontWeight: 700, color: m.tipo === 'ingreso' ? '#16a34a' : '#dc2626' }}>
                  {m.tipo === 'ingreso' ? '+' : '−'}{fmt(m.monto)}
                </td>
                <td style={td}><button onClick={() => eliminar(m)} style={btnSm('#ef4444')}><Trash2 size={13} /></button></td>
              </tr>
            ))}
            {movs.length === 0 && <tr><td colSpan={6} style={vacio}>Sin movimientos</td></tr>}
          </Tabla>
        </>
      )}

      {tab === 'deudas' && (
        <>
          <div style={{ marginBottom: 16, fontSize: '.9rem', color: '#475569' }}>
            Debes <strong style={{ color: '#dc2626' }}>{fmt(deudas.total)}</strong>
            {mesesDeuda && <> · a tu margen actual la saldas en <strong>{mesesDeuda} {mesesDeuda === 1 ? 'mes' : 'meses'}</strong></>}
          </div>
          {deudas.detalle.map(d => (
            <div key={d.id} style={tarjeta}>
              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start', flexWrap: 'wrap', gap: 10 }}>
                <div>
                  <strong style={{ fontSize: '1rem' }}>{d.acreedor}</strong>
                  <div style={{ fontSize: '.8rem', color: '#94a3b8' }}>{d.concepto}</div>
                  <div style={{ fontSize: '.78rem', color: '#94a3b8', marginTop: 4 }}>
                    {Number(d.tasa_mensual) > 0 ? `${d.tasa_mensual}% mensual — cuesta ${fmt(d.interes_mensual)}/mes` : 'Sin intereses'}
                  </div>
                </div>
                <div style={{ textAlign: 'right' }}>
                  <div style={{ fontSize: '1.3rem', fontWeight: 800, color: '#dc2626' }}>{fmt(d.saldo)}</div>
                  <div style={{ fontSize: '.75rem', color: '#94a3b8' }}>de {fmt(d.monto_original)}</div>
                  <button onClick={() => abonarDeuda(d)} style={{ ...btn('#10b981'), marginTop: 8, padding: '6px 14px' }}>Abonar</button>
                </div>
              </div>
              <Barra pct={d.avance_pct} color="#10b981" />
            </div>
          ))}
          {deudas.detalle.length === 0 && <p style={vacio}>Sin deudas activas</p>}
        </>
      )}

      {tab === 'metas' && (
        <>
          {metas.map(m => (
            <div key={m.id} style={tarjeta}>
              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start', flexWrap: 'wrap', gap: 10 }}>
                <div>
                  <strong style={{ fontSize: '1rem' }}><Target size={14} style={{ verticalAlign: -2 }} /> {m.nombre}</strong>
                  {m.descripcion && <div style={{ fontSize: '.8rem', color: '#94a3b8', maxWidth: 480 }}>{m.descripcion}</div>}
                  {Number(m.retorno_mensual) > 0 && (
                    <div style={{ fontSize: '.78rem', color: '#16a34a', marginTop: 4, fontWeight: 600 }}>
                      Genera {fmt(m.retorno_mensual)}/mes — se paga sola en {Math.ceil(m.monto_objetivo / m.retorno_mensual)} meses
                    </div>
                  )}
                </div>
                <div style={{ textAlign: 'right' }}>
                  <div style={{ fontSize: '1.2rem', fontWeight: 800 }}>{fmt(m.monto_objetivo)}</div>
                  <div style={{ fontSize: '.75rem', color: '#94a3b8' }}>faltan {fmt(m.falta)}</div>
                  <button onClick={() => aportarMeta(m)} style={{ ...btn('#4f46e5'), marginTop: 8, padding: '6px 14px' }}>Ahorrar</button>
                </div>
              </div>
              <Barra pct={m.avance_pct} color="#4f46e5" />
            </div>
          ))}
          {metas.length === 0 && <p style={vacio}>Sin metas activas</p>}
        </>
      )}

      {modal && (
        <div style={overlay}>
          <div style={modalBox}>
            <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 18 }}>
              <h2 style={{ fontSize: '1.1rem', fontWeight: 700 }}>Registrar movimiento</h2>
              <button onClick={() => setModal(false)} style={{ background: 'none', border: 'none', cursor: 'pointer' }}><X size={20} /></button>
            </div>
            <form onSubmit={guardar}>
              <div style={grid2}>
                <Campo label="¿Entra o sale?">
                  <select value={form.tipo} onChange={e => setForm(f => ({ ...f, tipo: e.target.value, categoria: 'otro' }))} style={input}>
                    <option value="egreso">Sale (gasto)</option><option value="ingreso">Entra (ingreso)</option>
                  </select>
                </Campo>
                <Campo label="¿De qué bolsillo?">
                  <select value={form.ambito} onChange={e => setForm(f => ({ ...f, ambito: e.target.value, categoria: 'otro' }))} style={input}>
                    <option value="empresa">Empresa</option><option value="personal">Personal</option>
                  </select>
                </Campo>
              </div>
              <Campo label="Concepto *">
                <input value={form.concepto} onChange={e => setForm(f => ({ ...f, concepto: e.target.value }))} required style={input} placeholder="Ej: Cuota contrato Ferre Láser" />
              </Campo>
              <div style={grid2}>
                <Campo label="Categoría">
                  <select value={form.categoria} onChange={e => setForm(f => ({ ...f, categoria: e.target.value }))} style={input}>
                    {(CATEGORIAS[form.ambito]?.[form.tipo] || ['otro']).map(c => <option key={c} value={c}>{c.replace(/_/g, ' ')}</option>)}
                  </select>
                </Campo>
                <Campo label="Monto *">
                  <input type="number" min="1" value={form.monto} onChange={e => setForm(f => ({ ...f, monto: e.target.value }))} required style={input} />
                </Campo>
              </div>
              <div style={grid2}>
                <Campo label="Fecha"><input type="date" value={form.fecha} onChange={e => setForm(f => ({ ...f, fecha: e.target.value }))} style={input} /></Campo>
                <Campo label="Método">
                  <select value={form.metodo_pago} onChange={e => setForm(f => ({ ...f, metodo_pago: e.target.value }))} style={input}>
                    {['transferencia', 'nequi', 'efectivo', 'tarjeta', 'mercadopago', 'otro'].map(m => <option key={m} value={m}>{m}</option>)}
                  </select>
                </Campo>
              </div>
              <label style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: '.86rem', margin: '4px 0 12px', cursor: 'pointer' }}>
                <input type="checkbox" checked={form.recurrente} onChange={e => setForm(f => ({ ...f, recurrente: e.target.checked }))} />
                Es un gasto fijo que se repite todos los meses
              </label>
              {form.ambito === 'empresa' && form.tipo === 'egreso' && form.categoria === 'sueldo' && (
                <Aviso color="#0ea5e9">
                  Al guardarlo se crea solo el ingreso espejo en tu bolsillo personal. Así se separa la plata
                  del negocio de la tuya sin registrar nada dos veces.
                </Aviso>
              )}
              <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 18 }}>
                <button type="button" onClick={() => setModal(false)} style={btn('#94a3b8')}>Cancelar</button>
                <button type="submit" style={btn('#4f46e5')}>Guardar</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

function Tarjeta({ icono, color, titulo, valor, pie, destacado }) {
  return (
    <div style={{ background: '#fff', borderRadius: 12, padding: 18, boxShadow: '0 1px 4px rgba(0,0,0,.07)', borderLeft: destacado ? `4px solid ${color}` : 'none' }}>
      <div style={{ display: 'flex', alignItems: 'center', gap: 7, color, marginBottom: 6 }}>
        {icono}<span style={{ fontSize: '.8rem', fontWeight: 600, color: '#64748b' }}>{titulo}</span>
      </div>
      <div style={{ fontSize: '1.5rem', fontWeight: 800, color: '#0f172a' }}>{valor}</div>
      <div style={{ fontSize: '.74rem', color: '#94a3b8', marginTop: 3 }}>{pie}</div>
    </div>
  );
}
const Seccion = ({ titulo, sub, children }) => (
  <div style={{ marginBottom: 26 }}>
    <h2 style={{ fontSize: '1rem', fontWeight: 700, marginBottom: 2 }}>{titulo}</h2>
    {sub && <p style={{ fontSize: '.8rem', color: '#94a3b8', marginBottom: 10 }}>{sub}</p>}
    {children}
  </div>
);
const Tabla = ({ cabeceras, children }) => (
  <div style={{ background: '#fff', borderRadius: 12, boxShadow: '0 1px 4px rgba(0,0,0,.07)', overflowX: 'auto' }}>
    <table style={{ width: '100%', borderCollapse: 'collapse', minWidth: 660 }}>
      <thead><tr style={{ background: '#f8fafc' }}>
        {cabeceras.map(h => <th key={h} style={{ padding: '10px 16px', textAlign: 'left', fontSize: '.78rem', color: '#64748b', fontWeight: 600 }}>{h}</th>)}
      </tr></thead>
      <tbody>{children}</tbody>
    </table>
  </div>
);
const Aviso = ({ color, children }) => (
  <div style={{ background: color + '12', border: `1px solid ${color}40`, borderRadius: 10, padding: '12px 16px', marginBottom: 18, fontSize: '.86rem', color: '#334155' }}>{children}</div>
);
const Barra = ({ pct, color }) => (
  <div style={{ marginTop: 12, height: 8, background: '#f1f5f9', borderRadius: 4, overflow: 'hidden' }}>
    <div style={{ width: `${Math.min(100, pct)}%`, height: '100%', background: color, transition: 'width .3s' }} />
  </div>
);
const Estado = ({ e }) => {
  const c = { al_dia: ['#f0fdf4', '#16a34a', 'Al día'], por_vencer: ['#fffbeb', '#d97706', 'Por vencer'],
    en_gracia: ['#fff7ed', '#ea580c', 'En mora'], mora_sin_bloqueo: ['#fff7ed', '#ea580c', 'En mora'],
    bloqueada: ['#fef2f2', '#dc2626', 'Bloqueada'], desactivada: ['#f1f5f9', '#64748b', 'Inactiva'] }[e] || ['#f1f5f9', '#64748b', e];
  return <span style={{ background: c[0], color: c[1], padding: '3px 10px', borderRadius: 20, fontSize: '.76rem', fontWeight: 600 }}>{c[2]}</span>;
};
const Campo = ({ label, children }) => (
  <div style={{ marginBottom: 13 }}>
    <label style={{ display: 'block', fontSize: '.81rem', fontWeight: 600, color: '#374151', marginBottom: 5 }}>{label}</label>
    {children}
  </div>
);

const fila   = { borderTop: '1px solid #f1f5f9' };
const td     = { padding: '12px 16px', fontSize: '.88rem', verticalAlign: 'top' };
const vacio  = { padding: 24, textAlign: 'center', color: '#94a3b8' };
const grid2  = { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 13 };
const input  = { width: '100%', padding: '9px 12px', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: '.88rem', boxSizing: 'border-box' };
const select = { padding: '7px 14px', borderRadius: 8, border: '1px solid #e2e8f0', fontSize: '.85rem', background: '#fff', color: '#374151' };
const tarjeta = { background: '#fff', borderRadius: 12, padding: 20, boxShadow: '0 1px 4px rgba(0,0,0,.07)', marginBottom: 14 };
const overlay  = { position: 'fixed', inset: 0, background: 'rgba(0,0,0,.45)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50 };
const modalBox = { background: '#fff', borderRadius: 14, padding: 26, width: 520, maxWidth: '95vw', maxHeight: '92vh', overflowY: 'auto' };
const btn   = bg => ({ display: 'inline-flex', alignItems: 'center', gap: 6, padding: '9px 16px', background: bg, color: '#fff', border: 'none', borderRadius: 8, fontSize: '.87rem', fontWeight: 600, cursor: 'pointer' });
const btnSm = bg => ({ padding: '5px 8px', background: bg + '18', color: bg, border: 'none', borderRadius: 6, cursor: 'pointer' });
const chip  = bg => ({ marginLeft: 6, background: bg + '18', color: bg, padding: '1px 7px', borderRadius: 10, fontSize: '.68rem', fontWeight: 600 });
