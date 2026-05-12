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

  useEffect(() => {
    api.get(`/pagos/mp/info/${license_key}`)
      .then(r => setInfo(r.data))
      .catch(() => setError('Licencia no encontrada'))
      .finally(() => setCargando(false));
  }, [license_key]);

  async function activarSuscripcion() {
    setProcesando(true);
    try {
      const { data } = await api.post(`/pagos/mp/suscripcion/${license_key}`);
      window.location.href = data.init_point;
    } catch {
      setError('Error al activar la suscripción. Intente de nuevo.');
      setProcesando(false);
    }
  }

  async function pagarUnaVez() {
    setProcesando(true);
    try {
      const { data } = await api.post(`/pagos/mp/crear/${license_key}`);
      window.location.href = data.init_point;
    } catch {
      setError('Error al iniciar el pago. Intente de nuevo.');
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

  if (cargando) return <div style={s.page}><div style={s.card}><p style={{ color: '#aaa' }}>Cargando...</p></div></div>;
  if (error && !info) return <div style={s.page}><div style={s.card}><h2 style={{ color: '#e74c3c' }}>Error</h2><p style={{ color: '#aaa' }}>{error}</p></div></div>;

  const vencida = !info.activo || info.dias_restantes <= 0;

  return (
    <div style={s.page}>
      <div style={s.card}>
        <img src="https://http2.mlstatic.com/frontend-assets/mp-web-navigation/ui-navigation/5.21.22/mercadopago/logo__large@2x.png"
          alt="Mercado Pago" style={{ width: 140, marginBottom: 20 }} />

        {/* Alertas de estado */}
        {estado === 'ok'       && <Alert color="green">✅ ¡Pago exitoso! Su licencia fue renovada por 1 mes.</Alert>}
        {estado === 'suscrito' && <Alert color="green">✅ ¡Suscripción activada! Se cobrará automáticamente cada mes.</Alert>}
        {estado === 'error'    && <Alert color="red">❌ El pago no pudo procesarse. Intente de nuevo.</Alert>}
        {estado === 'pendiente'&& <Alert color="orange">⏳ Pago pendiente de confirmación.</Alert>}
        {msg && <Alert color="green">{msg}</Alert>}
        {error && <Alert color="red">{error}</Alert>}

        <h2 style={{ color: '#fff', margin: '0 0 4px', fontSize: 20 }}>{info.producto}</h2>
        <p style={{ color: '#666', margin: '0 0 20px', fontSize: 13 }}>Licencia de software — {info.cliente}</p>

        {/* Info licencia */}
        <div style={s.infoBox}>
          <Row label="Estado"      value={vencida ? 'Vencida' : `Activa (${info.dias_restantes} días)`} color={vencida ? '#e74c3c' : '#2ecc71'} />
          <Row label="Vencimiento" value={new Date(info.fecha_vencimiento + 'T00:00:00').toLocaleDateString('es-CO', { year:'numeric', month:'long', day:'numeric' })} />
          <Row label="Suscripción" value={info.suscripcion_activa ? '✅ Activa — cobro automático' : '⭕ No configurada'} color={info.suscripcion_activa ? '#2ecc71' : '#888'} />
        </div>

        {/* Precio */}
        <div style={s.precioBox}>
          <span style={{ color: '#aaa', fontSize: 12, marginBottom: 4 }}>Precio mensual</span>
          <span style={{ color: '#fff', fontSize: 34, fontWeight: 800 }}>${Number(info.precio).toLocaleString('es-CO')}</span>
          <span style={{ color: '#aaa', fontSize: 12 }}>COP / mes</span>
        </div>

        {/* Botones según estado */}
        {!info.suscripcion_activa && (estado !== 'ok' && estado !== 'suscrito') && (
          <>
            <button onClick={activarSuscripcion} disabled={procesando} style={s.btnPrimario}>
              {procesando ? 'Procesando...' : '🔄 Activar suscripción automática'}
            </button>
            <p style={{ color: '#555', fontSize: 12, textAlign: 'center', margin: '8px 0' }}>
              Se cobra cada mes automáticamente. Puede cancelar cuando quiera.
            </p>
            <button onClick={pagarUnaVez} disabled={procesando} style={s.btnSecundario}>
              💳 Pagar solo este mes
            </button>
          </>
        )}

        {info.suscripcion_activa && (
          <button onClick={cancelarSuscripcion} disabled={procesando} style={s.btnCancelar}>
            {procesando ? 'Procesando...' : '✕ Cancelar suscripción'}
          </button>
        )}

        <p style={{ color: '#333', fontSize: 11, marginTop: 20, textAlign: 'center' }}>
          Pagos seguros procesados por Mercado Pago · Su información está protegida
        </p>
      </div>
    </div>
  );
}

function Alert({ color, children }) {
  const colors = { green: ['#0d2e1a','#2ecc71'], red: ['#2e0d0d','#e74c3c'], orange: ['#2e1f0d','#f39c12'] };
  const [bg, fg] = colors[color] || colors.green;
  return <div style={{ width:'100%', padding:'12px 16px', borderRadius:8, marginBottom:16, fontSize:13, background:bg, color:fg, border:`1px solid ${fg}` }}>{children}</div>;
}

function Row({ label, value, color }) {
  return (
    <div style={{ display:'flex', justifyContent:'space-between', padding:'9px 0', borderBottom:'1px solid #1e1e1e' }}>
      <span style={{ color:'#888', fontSize:13 }}>{label}</span>
      <span style={{ color: color || '#fff', fontWeight:600, fontSize:13 }}>{value}</span>
    </div>
  );
}

const s = {
  page:       { minHeight:'100vh', background:'#0a0a0a', display:'flex', alignItems:'center', justifyContent:'center', padding:16 },
  card:       { background:'#141414', borderRadius:18, padding:36, width:'100%', maxWidth:420, display:'flex', flexDirection:'column', alignItems:'center', boxShadow:'0 8px 40px rgba(0,0,0,0.6)' },
  infoBox:    { width:'100%', margin:'0 0 20px' },
  precioBox:  { width:'100%', background:'#0d0d0d', borderRadius:12, padding:'18px 20px', display:'flex', flexDirection:'column', alignItems:'center', marginBottom:24, border:'1px solid #1e1e1e' },
  btnPrimario:{ width:'100%', padding:'15px', background:'#009ee3', color:'#fff', border:'none', borderRadius:12, fontSize:15, fontWeight:700, cursor:'pointer', marginBottom:0 },
  btnSecundario:{ width:'100%', padding:'12px', background:'transparent', color:'#009ee3', border:'1px solid #009ee3', borderRadius:12, fontSize:13, fontWeight:600, cursor:'pointer', marginTop:10 },
  btnCancelar:{ width:'100%', padding:'12px', background:'transparent', color:'#e74c3c', border:'1px solid #e74c3c', borderRadius:12, fontSize:13, fontWeight:600, cursor:'pointer', marginTop:8 },
};
