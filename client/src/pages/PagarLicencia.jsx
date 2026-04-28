import { useEffect, useState } from 'react';
import { useParams, useSearchParams } from 'react-router-dom';
import api from '../api/axios';

export default function PagarLicencia() {
  const { license_key } = useParams();
  const [params] = useSearchParams();
  const estado = params.get('estado');

  const [info, setInfo]       = useState(null);
  const [cargando, setCargando] = useState(true);
  const [pagando, setPagando]   = useState(false);
  const [error, setError]       = useState('');

  useEffect(() => {
    api.get(`/pagos/mp/info/${license_key}`)
      .then(r => setInfo(r.data))
      .catch(() => setError('Licencia no encontrada'))
      .finally(() => setCargando(false));
  }, [license_key]);

  async function pagar() {
    setPagando(true);
    try {
      const { data } = await api.post(`/pagos/mp/crear/${license_key}`);
      window.location.href = data.init_point;
    } catch {
      setError('Error al iniciar el pago. Intente de nuevo.');
      setPagando(false);
    }
  }

  if (cargando) return (
    <div style={s.page}>
      <div style={s.card}><p style={{ color: '#aaa' }}>Cargando...</p></div>
    </div>
  );

  if (error) return (
    <div style={s.page}>
      <div style={s.card}>
        <h2 style={{ color: '#e74c3c' }}>Error</h2>
        <p style={{ color: '#aaa' }}>{error}</p>
      </div>
    </div>
  );

  return (
    <div style={s.page}>
      <div style={s.card}>
        <img src="https://http2.mlstatic.com/frontend-assets/mp-web-navigation/ui-navigation/5.21.22/mercadopago/logo__large@2x.png"
          alt="Mercado Pago" style={{ width: 160, marginBottom: 24 }} />

        {estado === 'ok' && (
          <div style={s.alert('green')}>
            ✅ ¡Pago exitoso! Su licencia fue renovada por 1 mes.<br />
            Puede reiniciar el sistema ahora.
          </div>
        )}
        {estado === 'error' && (
          <div style={s.alert('red')}>
            ❌ El pago no pudo procesarse. Intente de nuevo.
          </div>
        )}
        {estado === 'pendiente' && (
          <div style={s.alert('orange')}>
            ⏳ Pago pendiente de confirmación. La licencia se renovará automáticamente.
          </div>
        )}

        <h2 style={{ color: '#fff', margin: '0 0 4px' }}>Renovar Licencia</h2>
        <p style={{ color: '#aaa', margin: '0 0 24px' }}>Sistema: <strong style={{ color: '#fff' }}>{info.producto}</strong></p>

        <div style={s.infoBox}>
          <Row label="Cliente"      value={info.cliente} />
          <Row label="Vencimiento"  value={new Date(info.fecha_vencimiento + 'T00:00:00').toLocaleDateString('es')} />
          <Row label="Estado"       value={info.activo && info.dias_restantes > 0 ? `Activo (${info.dias_restantes} días)` : 'Vencida'} color={info.activo && info.dias_restantes > 0 ? '#2ecc71' : '#e74c3c'} />
        </div>

        <div style={s.precioBox}>
          <span style={{ color: '#aaa', fontSize: 14 }}>Renovación 1 mes</span>
          <span style={{ color: '#fff', fontSize: 32, fontWeight: 700 }}>
            ${Number(info.precio).toLocaleString('es-CO')} COP
          </span>
        </div>

        {estado !== 'ok' && (
          <button onClick={pagar} disabled={pagando} style={s.btn}>
            {pagando ? 'Redirigiendo...' : '💳 Pagar con Mercado Pago'}
          </button>
        )}

        <p style={{ color: '#555', fontSize: 12, marginTop: 16 }}>
          Pagos seguros procesados por Mercado Pago
        </p>
      </div>
    </div>
  );
}

function Row({ label, value, color }) {
  return (
    <div style={{ display: 'flex', justifyContent: 'space-between', padding: '8px 0', borderBottom: '1px solid #2a2a2a' }}>
      <span style={{ color: '#888' }}>{label}</span>
      <span style={{ color: color || '#fff', fontWeight: 600 }}>{value}</span>
    </div>
  );
}

const s = {
  page: { minHeight: '100vh', background: '#0f0f0f', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: 16 },
  card: { background: '#1a1a1a', borderRadius: 16, padding: 40, width: '100%', maxWidth: 440, display: 'flex', flexDirection: 'column', alignItems: 'center', boxShadow: '0 8px 32px rgba(0,0,0,0.5)' },
  infoBox: { width: '100%', margin: '0 0 20px' },
  precioBox: { width: '100%', background: '#111', borderRadius: 12, padding: '16px 20px', display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 4, marginBottom: 24 },
  btn: { width: '100%', padding: '16px', background: '#009ee3', color: '#fff', border: 'none', borderRadius: 12, fontSize: 16, fontWeight: 700, cursor: 'pointer' },
  alert: (color) => ({
    width: '100%', padding: '12px 16px', borderRadius: 8, marginBottom: 20, fontSize: 14,
    background: color === 'green' ? '#0d2e1a' : color === 'red' ? '#2e0d0d' : '#2e1f0d',
    color: color === 'green' ? '#2ecc71' : color === 'red' ? '#e74c3c' : '#f39c12',
    border: `1px solid ${color === 'green' ? '#2ecc71' : color === 'red' ? '#e74c3c' : '#f39c12'}`
  })
};
