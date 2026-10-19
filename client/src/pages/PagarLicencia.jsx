import { useEffect, useState } from 'react';
import { useParams, useSearchParams } from 'react-router-dom';
import api from '../api/axios';

export default function PagarLicencia() {
  const { license_key } = useParams();
  const [params] = useSearchParams();
  const estado = params.get('estado');

  const [info, setInfo]           = useState(null);
  const [cargando, setCargando]   = useState(true);
  const [procesando, setProcesando] = useState(false);
  const [error, setError]         = useState('');
  const [msg, setMsg]             = useState('');
  const [meses, setMeses]         = useState(1);
  const [emailMP, setEmailMP]     = useState('');

  const DESCUENTOS = { 1: 0, 2: 0, 3: 5, 6: 10, 12: 15 };
  function precioFinal(precio, m) {
    const desc = DESCUENTOS[m] || 0;
    return Math.round(precio * m * (1 - desc / 100));
  }
  function ahorroTotal(precio, m) {
    return precio * m - precioFinal(precio, m);
  }

  useEffect(() => {
    api.get(`/pagos/mp/info/${license_key}`)
      .then(r => { setInfo(r.data); setEmailMP(r.data.email || ''); })
      .catch(() => setError('Licencia no encontrada'))
      .finally(() => setCargando(false));
  }, [license_key]);

  async function activarSuscripcion() {
    const email = emailMP.trim();
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      setError('Escriba el correo de su cuenta de MercadoPago para activar el cobro automático.');
      return;
    }
    setError('');
    setProcesando(true);
    try {
      const { data } = await api.post(`/pagos/mp/suscripcion/${license_key}`, { email });
      window.location.href = data.init_point;
    } catch (err) {
      setError(err.response?.data?.msg || 'Error al activar la suscripción. Intente de nuevo o use "Pagar".');
      setProcesando(false);
    }
  }

  async function pagarUnaVez() {
    setProcesando(true);
    try {
      const { data } = await api.post(`/pagos/mp/crear/${license_key}`, { meses });
      window.location.href = data.init_point;
    } catch (err) {
      setError(err.response?.data?.msg || 'Error al iniciar el pago. Intente de nuevo.');
      setProcesando(false);
    }
  }

  async function cancelarSuscripcion() {
    if (!confirm('¿Está seguro que desea cancelar la suscripción automática?')) return;
    setProcesando(true);
    try {
      await api.post(`/pagos/mp/cancelar/${license_key}`);
      setMsg('Suscripción cancelada. Su acceso continúa hasta la fecha de vencimiento.');
      setInfo(i => ({ ...i, suscripcion_activa: false }));
    } catch {
      setError('Error al cancelar. Contáctenos directamente.');
    }
    setProcesando(false);
  }

  if (cargando) return <div style={s.page}><div style={s.card}><p style={{ color: '#7b7b86' }}>Cargando…</p></div></div>;
  if (error && !info) return <div style={s.page}><div style={s.card}><h2 style={{ color: '#A3311F', fontSize: 20 }}>No encontramos esta licencia</h2><p style={{ color: '#55555f', fontSize: 14 }}>{error}</p></div></div>;

  const vencida = !info.activo || info.dias_restantes <= 0;

  return (
    <div style={s.page}>
      <div style={s.card}>
        <img src="/ai-company.webp" alt="AI Company · Ingeniería de software"
          style={{ height: 42, width: 'auto', marginBottom: 22 }} />

        {/* Alertas de estado */}
        {estado === 'ok'       && <Alert color="green">✅ ¡Pago exitoso! Su licencia fue renovada por {params.get('meses') || 1} mes{(params.get('meses') || 1) > 1 ? 'es' : ''}.</Alert>}
        {estado === 'suscrito' && <Alert color="green">✅ ¡Suscripción activada! Se cobrará automáticamente cada mes.</Alert>}
        {estado === 'error'    && <Alert color="red">❌ El pago no pudo procesarse. Intente de nuevo.</Alert>}
        {estado === 'pendiente'&& <Alert color="orange">⏳ Pago pendiente de confirmación.</Alert>}
        {msg && <Alert color="green">{msg}</Alert>}
        {error && <Alert color="red">{error}</Alert>}

        <h2 style={{ color: '#191923', margin: '0 0 4px', fontSize: 21, fontWeight: 700, fontFamily: "'Manrope',system-ui,sans-serif", letterSpacing: '-.015em' }}>{info.producto}</h2>
        <p style={{ color: '#7b7b86', margin: '0 0 20px', fontSize: 13 }}>Licencia de software — {info.cliente}</p>

        {/* Info licencia */}
        <div style={s.infoBox}>
          <Row label="Estado" color={info.estado === 'bloqueada' ? '#e74c3c' : (info.dias_mora > 0 ? '#f59e0b' : '#2ecc71')}
            value={info.estado === 'bloqueada' ? `Bloqueada — ${info.dias_mora} días de mora`
              : info.dias_mora > 0 ? `En mora (${info.dias_mora} días)${info.fecha_bloqueo ? ' — se bloquea el ' + new Date(info.fecha_bloqueo + 'T00:00:00').toLocaleDateString('es-CO', { month: 'long', day: 'numeric' }) : ''}`
              : `Activa (${info.dias_restantes} días)`} />
          <Row label="Vencimiento" value={new Date(info.fecha_vencimiento + 'T00:00:00').toLocaleDateString('es-CO', { year:'numeric', month:'long', day:'numeric' })} />
          <Row label="Suscripción" value={info.suscripcion_activa ? '✅ Activa — cobro automático' : '⭕ No configurada'} color={info.suscripcion_activa ? '#2ecc71' : '#888'} />
        </div>

        {/* Selector de meses */}
        {!info.suscripcion_activa && estado !== 'suscrito' && (
          <div style={s.mesesBox}>
            <p style={{ color: '#55555f', fontSize: 12, marginBottom: 10, textAlign: 'center' }}>
              ¿Cuántos meses desea pagar?
            </p>
            <div style={s.mesesGrid}>
              {[1, 2, 3, 6, 12].map(m => {
                const desc = DESCUENTOS[m] || 0;
                return (
                  <button key={m} onClick={() => setMeses(m)}
                    style={{ ...s.mesBtn, ...(meses === m ? s.mesBtnActive : {}) }}>
                    <span style={{ fontSize: 16, fontWeight: 800 }}>{m}</span>
                    <span style={{ fontSize: 11, color: meses === m ? '#4C168E' : '#7b7b86' }}>
                      {m === 1 ? 'mes' : 'meses'}
                    </span>
                    {desc > 0 && <span style={{ ...s.tag, background: '#10b981' }}>-{desc}%</span>}
                    {m === 3 && desc === 0 && <span style={s.tag}>Popular</span>}
                  </button>
                );
              })}
            </div>
          </div>
        )}

        {/* Precio calculado */}
        <div style={s.precioBox}>
          {!info.suscripcion_activa && estado !== 'suscrito' ? (
            <>
              {meses > 1 && (
                <span style={{ color: '#7b7b86', fontSize: 12, marginBottom: 2, textDecoration: 'line-through' }}>
                  ${(Number(info.precio) * meses).toLocaleString('es-CO')} COP
                </span>
              )}
              <span style={{ color: '#191923', fontSize: 34, fontWeight: 800, fontFamily: "'Manrope',system-ui,sans-serif" }}>
                ${precioFinal(Number(info.precio), meses).toLocaleString('es-CO')}
              </span>
              <span style={{ color: '#55555f', fontSize: 12 }}>{meses === 1 ? 'COP / mes' : 'COP en total'}</span>
              {(DESCUENTOS[meses] || 0) > 0 && (
                <span style={{ color: '#1B7A43', fontSize: 12, marginTop: 4 }}>
                  🎉 Ahorras ${ahorroTotal(Number(info.precio), meses).toLocaleString('es-CO')} COP ({DESCUENTOS[meses]}% off)
                </span>
              )}
              {meses > 1 && (
                <span style={{ color: '#7b7b86', fontSize: 11, marginTop: 2 }}>
                  {meses} mes{meses > 1 ? 'es' : ''} × ${Number(info.precio).toLocaleString('es-CO')}
                </span>
              )}
            </>
          ) : (
            <>
              <span style={{ color: '#55555f', fontSize: 12, marginBottom: 4 }}>Precio mensual</span>
              <span style={{ color: '#191923', fontSize: 34, fontWeight: 800, fontFamily: "'Manrope',system-ui,sans-serif" }}>
                ${Number(info.precio).toLocaleString('es-CO')}
              </span>
              <span style={{ color: '#aaa', fontSize: 12 }}>COP / mes</span>
            </>
          )}
        </div>

        {/* Transferencia directa: sin comisión y es como pagan las empresas
            colombianas. Va ANTES de la tarjeta porque es la vía principal. */}
        {!info.suscripcion_activa && estado !== 'ok' && estado !== 'suscrito' && (
          <Transferencia
            monto={precioFinal(Number(info.precio), meses)}
            meses={meses}
            cliente={info.cliente}
            producto={info.producto}
          />
        )}

        {/* Botones según estado */}
        {!info.suscripcion_activa && (estado !== 'ok' && estado !== 'suscrito') && (
          <>
            <p style={{ width:'100%', textAlign:'center', fontSize:11.5, color:'#7b7b86', margin:'0 0 14px' }}>
              o paga con tarjeta
            </p>
            <input
              type="email" value={emailMP} onChange={e => setEmailMP(e.target.value)}
              placeholder="Correo de su cuenta MercadoPago (para el cobro automático)"
              style={{ width: '100%', padding: '12px 14px', borderRadius: 10, border: '1px solid #E4E2EC', background: '#fff', color: '#191923', fontSize: 14, marginBottom: 10, boxSizing: 'border-box', minHeight: 48 }}
            />
            <button onClick={activarSuscripcion} disabled={procesando} style={s.btnPrimario}>
              {procesando ? 'Procesando...' : '🔄 Activar suscripción automática'}
            </button>
            <p style={{ color: '#7b7b86', fontSize: 12, textAlign: 'center', margin: '8px 0' }}>
              Se cobra ${Number(info.precio).toLocaleString('es-CO')} cada mes automáticamente. Puede cancelar cuando quiera.
            </p>
            <button onClick={pagarUnaVez} disabled={procesando} style={s.btnSecundario}>
              💳 Pagar {meses} {meses === 1 ? 'mes' : 'meses'} — ${precioFinal(Number(info.precio), meses).toLocaleString('es-CO')} COP
            </button>
          </>
        )}

        {info.suscripcion_activa && (
          <button onClick={cancelarSuscripcion} disabled={procesando} style={s.btnCancelar}>
            {procesando ? 'Procesando...' : '✕ Cancelar suscripción'}
          </button>
        )}

        <p style={{ color: '#7b7b86', fontSize: 11, marginTop: 20, textAlign: 'center' }}>
          Pagos seguros procesados por Mercado Pago · Su información está protegida
        </p>
      </div>
    </div>
  );
}

// Datos de cobro autorizados por Cristian. No se inventan cuentas ni montos:
// el monto sale del mismo calculo que usa el pago con tarjeta.
const COBRO = {
  nequi: '322 480 7767',
  nequiPlano: '3224807767',
  breb: 'cg901376@gmail.com',
  wa: '573224807767'
};

function Transferencia({ monto, meses, cliente, producto }) {
  const [copiado, setCopiado] = useState('');

  async function copiar(texto, cual) {
    try {
      await navigator.clipboard.writeText(texto);
      setCopiado(cual);
      setTimeout(() => setCopiado(''), 1800);
    } catch {
      // Algunos navegadores lo niegan; el dato queda visible para copiarlo a mano.
      setCopiado('no');
      setTimeout(() => setCopiado(''), 2600);
    }
  }

  const texto = [
    `Hola, ya transferí la licencia de ${producto}.`,
    `Cliente: ${cliente}`,
    `Meses: ${meses}`,
    `Monto: $${Number(monto).toLocaleString('es-CO')} COP`,
    'Adjunto el comprobante.'
  ].join('\n');

  return (
    <div style={t.caja}>
      <p style={t.titulo}>Pagar por transferencia</p>
      <p style={t.bajada}>Transfiere el monto exacto y avísanos. Reactivamos tu sistema apenas lo confirmemos.</p>

      <div style={t.fila}>
        <div style={{ minWidth: 0 }}>
          <span style={t.etiqueta}>Nequi</span>
          <span style={t.valor}>{COBRO.nequi}</span>
        </div>
        <button type="button" onClick={() => copiar(COBRO.nequiPlano, 'nequi')} style={t.copiar}>
          {copiado === 'nequi' ? 'Copiado' : 'Copiar'}
        </button>
      </div>

      <div style={t.fila}>
        <div style={{ minWidth: 0 }}>
          <span style={t.etiqueta}>Bre-B</span>
          <span style={{ ...t.valor, fontSize: 13.5, wordBreak: 'break-all' }}>{COBRO.breb}</span>
        </div>
        <button type="button" onClick={() => copiar(COBRO.breb, 'breb')} style={t.copiar}>
          {copiado === 'breb' ? 'Copiado' : 'Copiar'}
        </button>
      </div>

      <div style={{ ...t.fila, borderBottom: 'none', paddingBottom: 0 }}>
        <div>
          <span style={t.etiqueta}>Monto exacto</span>
          <span style={{ ...t.valor, fontSize: 18, fontWeight: 800 }}>
            ${Number(monto).toLocaleString('es-CO')} COP
          </span>
        </div>
        <button type="button" onClick={() => copiar(String(monto), 'monto')} style={t.copiar}>
          {copiado === 'monto' ? 'Copiado' : 'Copiar'}
        </button>
      </div>

      {copiado === 'no' && (
        <p style={t.aviso}>Tu navegador no dejó copiar. Selecciona el dato y cópialo a mano.</p>
      )}

      <a href={`https://wa.me/${COBRO.wa}?text=${encodeURIComponent(texto)}`}
        target="_blank" rel="noopener noreferrer" style={t.boton}>
        Ya transferí, enviar comprobante
      </a>
      <p style={t.pie}>Se abre WhatsApp con los datos listos. Adjunta la foto de la transferencia.</p>
    </div>
  );
}

const t = {
  caja:     { width:'100%', background:'#F7F8FA', border:'1px solid #E4E2EC', borderRadius:14, padding:'20px', marginBottom:18, boxSizing:'border-box' },
  titulo:   { margin:'0 0 4px', fontSize:15, fontWeight:700, color:'#191923', fontFamily:"'Manrope',system-ui,sans-serif" },
  bajada:   { margin:'0 0 16px', fontSize:12.5, lineHeight:1.55, color:'#7b7b86' },
  fila:     { display:'flex', alignItems:'center', justifyContent:'space-between', gap:12, padding:'10px 0', borderBottom:'1px solid #E9E8EF' },
  etiqueta: { display:'block', fontSize:11, color:'#7b7b86', marginBottom:2 },
  valor:    { display:'block', fontSize:15, fontWeight:600, color:'#191923', fontVariantNumeric:'tabular-nums' },
  copiar:   { flex:'none', background:'#FFFFFF', border:'1px solid #D9CBEC', color:'#4C168E', borderRadius:8, padding:'8px 12px', fontSize:12, fontWeight:600, cursor:'pointer', minHeight:36 },
  boton:    { display:'flex', alignItems:'center', justifyContent:'center', width:'100%', minHeight:48, marginTop:16, background:'#7024C4', color:'#fff', borderRadius:10, textDecoration:'none', fontSize:14.5, fontWeight:600, boxSizing:'border-box' },
  pie:      { margin:'8px 0 0', fontSize:11.5, lineHeight:1.5, color:'#7b7b86', textAlign:'center' },
  aviso:    { margin:'10px 0 0', fontSize:11.5, color:'#8A5D00' }
};

function Alert({ color, children }) {
  const colors = { green: ['#E8F7EE','#1B7A43'], red: ['#FDECE8','#A3311F'], orange: ['#FFF4DC','#8A5D00'] };
  const [bg, fg] = colors[color] || colors.green;
  return <div style={{ width:'100%', padding:'12px 16px', borderRadius:8, marginBottom:16, fontSize:13, background:bg, color:fg, border:`1px solid ${fg}` }}>{children}</div>;
}

function Row({ label, value, color }) {
  return (
    <div style={{ display:'flex', justifyContent:'space-between', padding:'9px 0', borderBottom:'1px solid #EFEEF3' }}>
      <span style={{ color:'#7b7b86', fontSize:13 }}>{label}</span>
      <span style={{ color: color || '#191923', fontWeight:600, fontSize:13 }}>{value}</span>
    </div>
  );
}

const s = {
  page:       { minHeight:'100vh', background:'#F7F8FA', fontFamily:"'Inter',system-ui,-apple-system,'Segoe UI',sans-serif", display:'flex', alignItems:'center', justifyContent:'center', padding:16 },
  card:       { background:'#FFFFFF', border:'1px solid #E4E2EC', borderRadius:20, padding:36, width:'100%', maxWidth:440, display:'flex', flexDirection:'column', alignItems:'center', boxShadow:'0 18px 44px rgba(25,25,35,.07)' },
  infoBox:    { width:'100%', margin:'0 0 20px' },
  precioBox:  { width:'100%', background:'#F7F8FA', borderRadius:14, padding:'18px 20px', display:'flex', flexDirection:'column', alignItems:'center', marginBottom:24, border:'1px solid #E4E2EC' },
  btnPrimario:  { width:'100%', minHeight:52, padding:'15px', background:'#7024C4', color:'#fff', border:'none', borderRadius:12, fontSize:15, fontWeight:600, cursor:'pointer', marginBottom:0 },
  btnSecundario:{ width:'100%', minHeight:48, padding:'13px', background:'transparent', color:'#4C168E', border:'1px solid #D9CBEC', borderRadius:12, fontSize:14, fontWeight:600, cursor:'pointer', marginTop:10 },
  btnCancelar:  { width:'100%', minHeight:44, padding:'12px', background:'transparent', color:'#A3311F', border:'1px solid #F3C9BF', borderRadius:12, fontSize:13, fontWeight:600, cursor:'pointer', marginTop:8 },
  mesesBox:     { width:'100%', marginBottom:16 },
  mesesGrid:    { display:'grid', gridTemplateColumns:'repeat(5,1fr)', gap:6 },
  mesBtn:       { display:'flex', flexDirection:'column', alignItems:'center', justifyContent:'center', padding:'10px 4px', background:'#FFFFFF', border:'1px solid #E4E2EC', borderRadius:10, cursor:'pointer', position:'relative', gap:2, color:'#191923', minHeight:56 },
  mesBtnActive: { background:'#F0E5FA', border:'2px solid #7024C4', color:'#4C168E' },
  tag:          { position:'absolute', top:-8, left:'50%', transform:'translateX(-50%)', background:'#7024C4', color:'#fff', fontSize:9, fontWeight:700, padding:'2px 6px', borderRadius:8, whiteSpace:'nowrap' },
};
