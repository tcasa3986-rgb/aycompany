import { useEffect, useState } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import api from '../api/axios';
import CapturaRapida from '../components/CapturaRapida';
import {
  Lock, Clock, AlertTriangle, Banknote, Server, FileText, Calendar,
  Check, Plus, PartyPopper
} from 'lucide-react';

// Propuesta B — "Qué hago hoy". La pantalla no es un tablero: es la lista de
// lo que necesita acción, y cada fila trae el botón que lo resuelve. Los
// números van arriba en una franja delgada, como contexto, no de protagonistas.

const COLORES = {
  rojo:    { barra: '#DC2626', suave: '#FEF2F2', texto: '#DC2626' },
  ambar:   { barra: '#F59E0B', suave: '#FFFBEB', texto: '#D97706' },
  azul:    { barra: '#2563EB', suave: '#EFF6FF', texto: '#2563EB' },
  violeta: { barra: '#7C3AED', suave: '#F5F3FF', texto: '#7C3AED' },
};
const ICONOS = { lock: Lock, clock: Clock, alert: AlertTriangle, money: Banknote,
                 server: Server, doc: FileText, calendar: Calendar };

const fmt = n => '$' + Number(n || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 });

function saludo() {
  const h = new Date().getHours();
  if (h < 12) return 'Buenos días';
  if (h < 19) return 'Buenas tardes';
  return 'Buenas noches';
}

export default function Hoy() {
  const [d, setD] = useState(null);
  const navigate = useNavigate();
  // ?captura=1 abre el capturador de una: es lo que hace el acceso directo
  // "Registrar gasto" al mantener presionado el ícono de la app.
  const [params, setParams] = useSearchParams();
  const [captura, setCaptura] = useState(params.get('captura') === '1');
  const cerrarCaptura = () => { setCaptura(false); if (params.get('captura')) setParams({}); cargar(); };

  const cargar = () => api.get('/hoy').then(r => setD(r.data)).catch(() => setD({ error: true }));
  useEffect(cargar, []);

  if (!d) return <div style={{ padding: 32, color: '#94A3B8' }}>Cargando…</div>;
  if (d.error) return <div style={{ padding: 32, color: '#DC2626' }}>No se pudo cargar. Recarga la página.</div>;

  const { acciones, contexto, hechas_hoy, pendientes } = d;
  const fecha = new Date(d.hoy + 'T12:00:00').toLocaleDateString('es-CO',
    { weekday: 'long', day: 'numeric', month: 'long' });

  return (
    <div style={S.pagina}>
      {/* Encabezado */}
      <div style={S.cabecera}>
        <div>
          <h1 style={S.titulo}>{saludo()}, Cristian</h1>
          <p style={S.subtitulo}>
            {fecha}
            {pendientes > 0
              ? <> · <strong style={{ color: '#0F172A' }}>{pendientes} {pendientes === 1 ? 'cosa necesita' : 'cosas necesitan'} tu atención</strong></>
              : ' · todo al día'}
          </p>
        </div>
        <button onClick={() => setCaptura(true)} style={S.btnPrimario} className="solo-escritorio">
          <Plus size={17} /> Registrar movimiento
        </button>
      </div>

      {/* Franja de contexto: discreta a propósito */}
      <div style={S.franja}>
        <Cifra rotulo="Te queda libre" valor={fmt(contexto.libre)} color={contexto.libre >= 0 ? '#0F172A' : '#DC2626'} />
        <span style={S.divisor} />
        <Cifra rotulo="Te deben" valor={fmt(contexto.por_cobrar)} color="#D97706" />
        <span style={S.divisor} />
        <Cifra rotulo="Recurrente al mes" valor={fmt(contexto.recurrente)} color="#16A34A" />
        <span style={S.divisor} />
        <Cifra rotulo="Deuda" valor={fmt(contexto.deuda)} color="#DC2626" />
      </div>

      {/* La lista */}
      {acciones.length === 0 ? (
        <div style={S.vacio}>
          <PartyPopper size={34} color="#16A34A" />
          <h2 style={{ fontSize: '1.05rem', fontWeight: 700, margin: '12px 0 4px' }}>Nada pendiente hoy</h2>
          <p style={{ color: '#94A3B8', fontSize: '.88rem', margin: 0 }}>
            Sin vencimientos, moras ni pagos de servidor por atender.
          </p>
        </div>
      ) : (
        <>
          <h2 style={S.seccion}>Qué necesita tu atención hoy</h2>
          <div style={S.lista}>
            {acciones.map(a => {
              const c = COLORES[a.color] || COLORES.azul;
              const Icon = ICONOS[a.icono] || AlertTriangle;
              return (
                <article key={a.id} style={S.fila}>
                  <span style={{ ...S.barra, background: c.barra }} />
                  <div style={{ ...S.iconoCirculo, background: c.suave, color: c.texto }}>
                    <Icon size={18} />
                  </div>
                  <div style={S.filaTexto}>
                    <h3 style={S.filaTitulo}>{a.titulo}</h3>
                    <p style={S.filaDetalle}>{a.detalle}</p>
                  </div>
                  <button onClick={() => navigate(a.ruta)} style={{ ...S.filaBtn, background: c.barra }}>
                    {a.accion}
                  </button>
                </article>
              );
            })}
          </div>
        </>
      )}

      {/* Ya resueltas */}
      {hechas_hoy?.length > 0 && (
        <>
          <h2 style={{ ...S.seccion, marginTop: 30 }}>Ya registraste hoy</h2>
          <div style={S.resueltas}>
            {hechas_hoy.map(m => (
              <div key={m.id} style={S.resuelta}>
                <Check size={15} color="#16A34A" />
                <span style={{ flex: 1 }}>{m.concepto}</span>
                <strong style={{ color: m.tipo === 'ingreso' ? '#16A34A' : '#64748B' }}>
                  {m.tipo === 'ingreso' ? '+' : '−'}{fmt(m.monto)}
                </strong>
              </div>
            ))}
          </div>
        </>
      )}

      {captura && <CapturaRapida onCerrar={cerrarCaptura} tipoInicial={params.get('tipo') || 'egreso'} />}
    </div>
  );
}

const Cifra = ({ rotulo, valor, color }) => (
  <div style={{ flex: '1 1 auto', minWidth: 130 }}>
    <div style={S.cifraRotulo}>{rotulo}</div>
    <div style={{ ...S.cifraValor, color }}>{valor}</div>
  </div>
);

const S = {
  pagina: { padding: 'clamp(20px, 4vw, 38px)', maxWidth: 1180, margin: '0 auto' },
  cabecera: { display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: 16, marginBottom: 22 },
  titulo: { fontFamily: "'Space Grotesk', system-ui, sans-serif", fontSize: 'clamp(1.35rem, 4vw, 1.75rem)',
            fontWeight: 700, color: '#0F172A', margin: 0, letterSpacing: '-.02em' },
  subtitulo: { color: '#64748B', fontSize: '.88rem', margin: '5px 0 0', textTransform: 'capitalize' },
  btnPrimario: { display: 'inline-flex', alignItems: 'center', gap: 7, padding: '10px 17px', background: '#2563EB',
                 color: '#fff', border: 'none', borderRadius: 10, fontSize: '.88rem', fontWeight: 650,
                 cursor: 'pointer', flexShrink: 0 },

  franja: { display: 'flex', flexWrap: 'wrap', alignItems: 'center', gap: 'clamp(12px, 3vw, 28px)',
            background: '#fff', border: '1px solid #E2E8F0', borderRadius: 14,
            padding: 'clamp(14px, 2.5vw, 20px) clamp(16px, 3vw, 26px)', marginBottom: 30 },
  divisor: { width: 1, height: 34, background: '#E2E8F0', flexShrink: 0 },
  cifraRotulo: { fontSize: '.68rem', fontWeight: 700, color: '#94A3B8', textTransform: 'uppercase', letterSpacing: '.06em' },
  cifraValor: { fontFamily: "'Space Grotesk', system-ui, sans-serif", fontSize: 'clamp(1.05rem, 3vw, 1.35rem)',
                fontWeight: 750, marginTop: 3, letterSpacing: '-.01em' },

  seccion: { fontSize: '.95rem', fontWeight: 700, color: '#0F172A', margin: '0 0 12px' },
  lista: { display: 'flex', flexDirection: 'column', gap: 11 },
  fila: { position: 'relative', display: 'flex', alignItems: 'center', gap: 13, background: '#fff',
          border: '1px solid #E2E8F0', borderRadius: 14, padding: '16px 18px 16px 22px',
          overflow: 'hidden', flexWrap: 'wrap' },
  barra: { position: 'absolute', left: 0, top: 0, bottom: 0, width: 4 },
  iconoCirculo: { width: 38, height: 38, borderRadius: '50%', display: 'flex', alignItems: 'center',
                  justifyContent: 'center', flexShrink: 0 },
  filaTexto: { flex: '1 1 220px', minWidth: 0 },
  filaTitulo: { fontSize: '.93rem', fontWeight: 700, color: '#0F172A', margin: 0, lineHeight: 1.35 },
  filaDetalle: { fontSize: '.81rem', color: '#64748B', margin: '3px 0 0', lineHeight: 1.4 },
  filaBtn: { padding: '9px 18px', borderRadius: 9, border: 'none', color: '#fff',
             fontSize: '.83rem', fontWeight: 650, cursor: 'pointer', flexShrink: 0, minHeight: 38 },

  resueltas: { display: 'flex', flexDirection: 'column', gap: 1, background: '#fff',
               border: '1px solid #E2E8F0', borderRadius: 14, overflow: 'hidden' },
  resuelta: { display: 'flex', alignItems: 'center', gap: 10, padding: '12px 18px',
              fontSize: '.85rem', color: '#94A3B8', borderBottom: '1px solid #F8FAFC' },

  vacio: { background: '#fff', border: '1px solid #E2E8F0', borderRadius: 14, padding: '44px 24px', textAlign: 'center' },
};
