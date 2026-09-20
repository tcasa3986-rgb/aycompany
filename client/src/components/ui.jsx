// Piezas compartidas del rediseño. Existen para que Finanzas, Licencias y lo
// que venga después se vean como el mismo producto: si cada pantalla define sus
// propios estilos, en tres meses hay cinco diseños distintos otra vez.
import { X } from 'lucide-react';

export const C = {
  azul: '#2563EB', verde: '#16A34A', rojo: '#DC2626', ambar: '#F59E0B',
  violeta: '#7C3AED', tinta: '#0F172A', texto: '#475569', tenue: '#94A3B8',
  borde: '#E2E8F0', lienzo: '#F8FAFC', panel: '#FFFFFF',
};

export const fmt = n => '$' + Number(n || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 });
export const fmtFecha = s => s
  ? new Date(String(s).split('T')[0] + 'T12:00:00').toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' })
  : '—';

// ── Estructura ────────────────────────────────────────────────────────
export const Pagina = ({ children }) => (
  <div style={{ padding: 'clamp(20px, 4vw, 38px)', maxWidth: 1180, margin: '0 auto' }}>{children}</div>
);

export const Cabecera = ({ titulo, sub, children }) => (
  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start',
                gap: 16, marginBottom: 22, flexWrap: 'wrap' }}>
    <div>
      <h1 style={{ fontFamily: "'Space Grotesk', system-ui, sans-serif",
                   fontSize: 'clamp(1.35rem, 4vw, 1.75rem)', fontWeight: 700,
                   color: C.tinta, margin: 0, letterSpacing: '-.02em' }}>{titulo}</h1>
      {sub && <p style={{ color: C.texto, fontSize: '.88rem', margin: '5px 0 0' }}>{sub}</p>}
    </div>
    {children && <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>{children}</div>}
  </div>
);

export const Tarjeta = ({ titulo, sub, acento, children, style }) => (
  <section style={{ background: C.panel, border: `1px solid ${C.borde}`, borderRadius: 14,
                    padding: 'clamp(16px, 2.5vw, 22px)',
                    borderLeft: acento ? `4px solid ${acento}` : undefined, ...style }}>
    {titulo && <h2 style={{ fontSize: '.95rem', fontWeight: 700, color: C.tinta, margin: 0 }}>{titulo}</h2>}
    {sub && <p style={{ fontSize: '.79rem', color: C.tenue, margin: '4px 0 0' }}>{sub}</p>}
    {(titulo || sub) && <div style={{ height: 14 }} />}
    {children}
  </section>
);

// Cifra grande con rótulo. `tono` la pinta sobre color sólido.
export const Cifra = ({ rotulo, valor, pie, color = C.tinta, tono }) => (
  <div style={{ background: tono || C.panel, border: tono ? 'none' : `1px solid ${C.borde}`,
                borderRadius: 14, padding: 'clamp(16px, 2.5vw, 22px)' }}>
    <div style={{ fontSize: '.68rem', fontWeight: 700, letterSpacing: '.06em', textTransform: 'uppercase',
                  color: tono ? 'rgba(255,255,255,.75)' : C.tenue }}>{rotulo}</div>
    <div style={{ fontFamily: "'Space Grotesk', system-ui, sans-serif",
                  fontSize: 'clamp(1.3rem, 3.4vw, 1.75rem)', fontWeight: 750, marginTop: 5,
                  letterSpacing: '-.02em', color: tono ? '#fff' : color }}>{valor}</div>
    {pie && <div style={{ fontSize: '.74rem', marginTop: 5,
                          color: tono ? 'rgba(255,255,255,.72)' : C.tenue }}>{pie}</div>}
  </div>
);

export const Rejilla = ({ min = 230, gap = 14, children }) => (
  <div style={{ display: 'grid', gridTemplateColumns: `repeat(auto-fit, minmax(${min}px, 1fr))`, gap }}>{children}</div>
);

// ── Avisos ────────────────────────────────────────────────────────────
export const Franja = ({ color = C.ambar, icono, children, accion }) => (
  <div style={{ display: 'flex', alignItems: 'center', gap: 12, flexWrap: 'wrap',
                background: color + '12', border: `1px solid ${color}40`, borderRadius: 12,
                padding: '13px 17px', marginBottom: 18 }}>
    {icono && <span style={{ color, display: 'flex', flexShrink: 0 }}>{icono}</span>}
    <div style={{ flex: '1 1 260px', fontSize: '.86rem', color: '#334155', lineHeight: 1.45 }}>{children}</div>
    {accion}
  </div>
);

// Lo que NO está confirmado se ve distinto: borde punteado, nunca sólido.
export const Probable = ({ titulo, children }) => (
  <section style={{ border: `1.5px dashed ${C.ambar}80`, background: C.ambar + '0D',
                    borderRadius: 14, padding: 'clamp(16px, 2.5vw, 22px)' }}>
    {titulo && <h2 style={{ fontSize: '.95rem', fontWeight: 700, color: '#92400E', margin: '0 0 12px' }}>{titulo}</h2>}
    {children}
  </section>
);

// ── Tabla ─────────────────────────────────────────────────────────────
export const Tabla = ({ cabeceras, children, min = 640 }) => (
  <div style={{ overflowX: 'auto', margin: '0 -4px' }}>
    <table style={{ width: '100%', borderCollapse: 'collapse', minWidth: min }}>
      <thead>
        <tr>{cabeceras.map(h => (
          <th key={h} style={{ padding: '0 14px 10px', textAlign: 'left', fontSize: '.68rem',
                               fontWeight: 700, color: C.tenue, textTransform: 'uppercase',
                               letterSpacing: '.06em', whiteSpace: 'nowrap' }}>{h}</th>
        ))}</tr>
      </thead>
      <tbody>{children}</tbody>
    </table>
  </div>
);
export const Fila = ({ children, resaltada }) => (
  <tr style={{ borderTop: `1px solid ${C.lienzo}`, background: resaltada || undefined }}>{children}</tr>
);
export const Td = ({ children, style }) => (
  <td style={{ padding: '14px', fontSize: '.87rem', color: C.texto, verticalAlign: 'middle', ...style }}>{children}</td>
);
export const Vacio = ({ cols, children }) => (
  <tr><td colSpan={cols} style={{ padding: 30, textAlign: 'center', color: C.tenue, fontSize: '.87rem' }}>{children}</td></tr>
);

// ── Controles ─────────────────────────────────────────────────────────
export const Boton = ({ color = C.azul, variante = 'solido', children, ...p }) => {
  const base = { display: 'inline-flex', alignItems: 'center', justifyContent: 'center', gap: 7,
                 padding: '10px 17px', borderRadius: 10, fontSize: '.87rem', fontWeight: 650,
                 cursor: 'pointer', border: 'none', minHeight: 40, whiteSpace: 'nowrap' };
  const v = {
    solido: { background: color, color: '#fff' },
    suave:  { background: color + '18', color },
    borde:  { background: 'transparent', color: C.texto, border: `1px solid ${C.borde}` },
  }[variante];
  return <button {...p} style={{ ...base, ...v, ...p.style }}>{children}</button>;
};

export const BotonIcono = ({ color = C.azul, titulo, children, ...p }) => (
  <button {...p} title={titulo} aria-label={titulo}
    style={{ width: 34, height: 34, borderRadius: 9, border: 'none', background: color + '18',
             color, display: 'inline-flex', alignItems: 'center', justifyContent: 'center',
             cursor: 'pointer', marginRight: 5, ...p.style }}>{children}</button>
);

export const Pildora = ({ activa, children, ...p }) => (
  <button {...p} style={{ padding: '8px 15px', borderRadius: 999, border: 'none', cursor: 'pointer',
                          fontSize: '.82rem', fontWeight: 650,
                          background: activa ? C.azul : '#EEF2F6', color: activa ? '#fff' : C.texto }}>
    {children}
  </button>
);

export const Insignia = ({ color, children }) => (
  <span style={{ background: color + '18', color, padding: '4px 11px', borderRadius: 999,
                 fontSize: '.75rem', fontWeight: 700, whiteSpace: 'nowrap', display: 'inline-block' }}>
    {children}
  </span>
);

export const Barra = ({ pct, color = C.azul, alto = 7 }) => (
  <div style={{ height: alto, background: '#F1F5F9', borderRadius: alto / 2, overflow: 'hidden' }}>
    <div style={{ width: `${Math.max(0, Math.min(100, pct))}%`, height: '100%', background: color,
                  borderRadius: alto / 2, transition: 'width .3s' }} />
  </div>
);

// ── Formulario ────────────────────────────────────────────────────────
export const entrada = {
  width: '100%', padding: '10px 13px', border: `1px solid ${C.borde}`, borderRadius: 9,
  fontSize: '.88rem', boxSizing: 'border-box', fontFamily: 'inherit', color: C.tinta, background: '#fff',
};

export const Campo = ({ label, pista, children }) => (
  <div style={{ marginBottom: 14 }}>
    <label style={{ display: 'block', fontSize: '.8rem', fontWeight: 650, color: '#334155', marginBottom: 6 }}>{label}</label>
    {children}
    {pista && <div style={{ fontSize: '.72rem', color: C.tenue, marginTop: 5 }}>{pista}</div>}
  </div>
);

export const Dos = ({ children }) => (
  <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))', gap: 13 }}>{children}</div>
);

export const Modal = ({ titulo, sub, onCerrar, children, ancho = 540 }) => (
  <div onClick={onCerrar}
       style={{ position: 'fixed', inset: 0, background: 'rgba(15,23,42,.5)', zIndex: 60,
                display: 'flex', alignItems: 'center', justifyContent: 'center', padding: 16 }}>
    <div onClick={e => e.stopPropagation()}
         style={{ background: '#fff', borderRadius: 16, padding: 'clamp(20px, 4vw, 28px)',
                  width: ancho, maxWidth: '100%', maxHeight: '92vh', overflowY: 'auto' }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: 12, marginBottom: 18 }}>
        <div>
          <h2 style={{ fontFamily: "'Space Grotesk', system-ui, sans-serif", fontSize: '1.1rem',
                       fontWeight: 700, color: C.tinta, margin: 0 }}>{titulo}</h2>
          {sub && <p style={{ fontSize: '.83rem', color: C.texto, margin: '4px 0 0' }}>{sub}</p>}
        </div>
        <button onClick={onCerrar} aria-label="Cerrar"
                style={{ background: 'none', border: 'none', color: C.tenue, cursor: 'pointer', padding: 2 }}>
          <X size={20} />
        </button>
      </div>
      {children}
    </div>
  </div>
);
