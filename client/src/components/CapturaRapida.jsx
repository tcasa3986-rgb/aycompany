import { useState, useEffect, useRef } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { X, Check, Fuel, UtensilsCrossed, Home, Zap, Dumbbell, Wrench, Landmark, Banknote,
         Bike, HeartPulse, GraduationCap, Smartphone, Server, Megaphone, Users, Receipt,
         ShoppingBag, CircleDashed, FileText, Globe } from 'lucide-react';

// Propuesta C: si anotar un gasto toma más de tres segundos, uno deja de
// hacerlo y el sistema se vuelve mentira. Por eso esto no es un formulario:
// es un teclado. Monto primero, todo lo demás a un toque, sin scroll.
//
// OJO con la categoría por defecto: antes venía marcada "Gasolina" y todo lo
// que no encajaba en las 7 opciones terminaba ahí. Así aparecieron gastos de
// $1.600.000 "en gasolina". Ahora no viene ninguna marcada y lo que no se
// clasifica cae en "Otro", que al menos no miente y se ve de lejos.

const CATS = {
  egreso: [
    { v: 'arriendo',     l: 'Arriendo',    i: Home,            ambito: 'personal' },
    { v: 'recibos',      l: 'Servicios',   i: Zap,             ambito: 'personal' },
    { v: 'comida',       l: 'Comida',      i: UtensilsCrossed, ambito: 'personal' },
    { v: 'transporte',   l: 'Gasolina',    i: Fuel,            ambito: 'personal' },
    { v: 'cuota_moto',   l: 'Cuota moto',  i: Bike,            ambito: 'personal' },
    { v: 'deuda',        l: 'Deuda',       i: Receipt,         ambito: 'personal' },
    { v: 'gimnasio',     l: 'Gimnasio',    i: Dumbbell,        ambito: 'personal' },
    { v: 'celular',      l: 'Celular',     i: Smartphone,      ambito: 'personal' },
    { v: 'salud',        l: 'Salud',       i: HeartPulse,      ambito: 'personal' },
    { v: 'educacion',    l: 'Estudio',     i: GraduationCap,   ambito: 'personal' },
    { v: 'ropa',         l: 'Compras',     i: ShoppingBag,     ambito: 'personal' },
    { v: 'hosting',      l: 'Servidores',  i: Server,          ambito: 'empresa'  },
    { v: 'apis_ia',      l: 'APIs / IA',   i: Zap,             ambito: 'empresa'  },
    { v: 'herramientas', l: 'Herramientas',i: Wrench,          ambito: 'empresa'  },
    { v: 'publicidad',   l: 'Publicidad',  i: Megaphone,       ambito: 'empresa'  },
    { v: 'sueldo',       l: 'Sueldos',     i: Users,           ambito: 'empresa'  },
    { v: 'impuestos',    l: 'Impuestos',   i: Landmark,        ambito: 'empresa'  },
    { v: 'otro',         l: 'Otro',        i: CircleDashed,    ambito: 'personal' },
  ],
  ingreso: [
    { v: 'mensualidad',       l: 'Mensualidad',  i: Banknote,  ambito: 'empresa'  },
    { v: 'cuota_contrato',    l: 'Cuota',        i: Landmark,  ambito: 'empresa'  },
    { v: 'anticipo_contrato', l: 'Anticipo',     i: FileText,  ambito: 'empresa'  },
    { v: 'marketing',         l: 'Marketing',    i: Megaphone, ambito: 'empresa'  },
    { v: 'pagina_web',        l: 'Página web',   i: Globe,     ambito: 'empresa'  },
    { v: 'alquiler_moto',     l: 'Alquiler moto',i: Bike,      ambito: 'personal' },
    { v: 'otro',              l: 'Otro',         i: CircleDashed, ambito: 'empresa' },
  ],
};

export default function CapturaRapida({ onCerrar, tipoInicial = 'egreso' }) {
  // tipoInicial viene del boton del widget: "Entro" abre en ingreso, "Gaste" en egreso
  const inicial = tipoInicial === 'ingreso' ? 'ingreso' : 'egreso';
  const [tipo, setTipo]     = useState(inicial);
  const [monto, setMonto]   = useState('');
  const [cat, setCat]       = useState(null);           // nada marcado: que elija
  const [concepto, setConcepto] = useState('');
  const [ambito, setAmbito] = useState(inicial === 'ingreso' ? 'empresa' : 'personal');
  const [guardando, setGuardando] = useState(false);
  const montoRef = useRef(null);

  useEffect(() => { montoRef.current?.focus(); }, []);
  useEffect(() => {
    const esc = e => e.key === 'Escape' && onCerrar();
    window.addEventListener('keydown', esc);
    return () => window.removeEventListener('keydown', esc);
  }, [onCerrar]);

  // Al elegir categoría se ajusta el bolsillo solo: gasolina es personal,
  // hosting es de la empresa. Un toque menos, y se puede cambiar abajo.
  function elegirCat(c) { setCat(c); setAmbito(c.ambito); }
  function cambiarTipo(t) {
    setTipo(t); setCat(null);
    setAmbito(t === 'ingreso' ? 'empresa' : 'personal');
  }

  async function guardar() {
    const n = Number(String(monto).replace(/\D/g, ''));
    if (!(n > 0)) { toast.error('Escribe cuánto'); montoRef.current?.focus(); return; }
    setGuardando(true);
    try {
      const r = await api.post('/finanzas/movimientos', {
        tipo, ambito,
        categoria: cat?.v || 'otro',
        // El concepto escrito manda: es lo único que después deja entender
        // qué fue ese movimiento.
        concepto: concepto.trim() || cat?.l || 'Sin clasificar',
        monto: n,
      });
      toast.success(r.data.msg || 'Registrado');
      onCerrar();
    } catch (err) {
      toast.error(err.response?.data?.msg || 'No se pudo guardar');
      setGuardando(false);
    }
  }

  const cats = CATS[tipo];
  const acento = tipo === 'egreso' ? '#DC2626' : '#16A34A';

  return (
    <div style={S.fondo} onClick={onCerrar}>
      <div style={S.hoja} onClick={e => e.stopPropagation()}>
        <div style={S.cabecera}>
          <div style={S.asa} />
          <button onClick={onCerrar} style={S.cerrar} aria-label="Cerrar"><X size={20} /></button>
        </div>

        {/* Monto: lo primero y lo más grande */}
        <div style={S.montoCaja}>
          <input
            ref={montoRef} inputMode="numeric" pattern="[0-9]*"
            value={monto ? Number(String(monto).replace(/\D/g, '')).toLocaleString('es-CO') : ''}
            onChange={e => setMonto(e.target.value.replace(/\D/g, ''))}
            onKeyDown={e => e.key === 'Enter' && guardar()}
            placeholder="$ 0"
            style={{ ...S.montoInput, color: monto ? acento : '#CBD5E1' }}
          />
        </div>

        {/* Sale / Entra */}
        <div style={S.segmento}>
          {[['egreso', 'SALE'], ['ingreso', 'ENTRA']].map(([v, l]) => (
            <button key={v} onClick={() => cambiarTipo(v)}
              style={{ ...S.segBtn, ...(tipo === v ? { background: v === 'egreso' ? '#DC2626' : '#16A34A', color: '#fff' } : {}) }}>
              {l}
            </button>
          ))}
        </div>

        {/* Categorías: dos filas para que quepan todas sin buscar */}
        <div style={S.cats}>
          {cats.map(c => {
            const Icon = c.i, sel = cat?.v === c.v;
            return (
              <button key={c.v} onClick={() => elegirCat(c)}
                style={{ ...S.cat, ...(sel ? S.catSel : {}) }}>
                <Icon size={15} /> {c.l}
              </button>
            );
          })}
        </div>

        {/* Concepto: lo que de verdad deja entender el movimiento después */}
        <input
          value={concepto}
          onChange={e => setConcepto(e.target.value)}
          onKeyDown={e => e.key === 'Enter' && guardar()}
          placeholder={cat ? `${cat.l} — ¿de qué exactamente? (opcional)` : '¿En qué fue? (opcional)'}
          maxLength={120}
          style={S.concepto}
        />

        {/* Bolsillo */}
        <div style={S.bolsillos}>
          {[['empresa', 'Empresa'], ['personal', 'Personal']].map(([v, l]) => (
            <button key={v} onClick={() => setAmbito(v)}
              style={{ ...S.bolsillo, ...(ambito === v ? S.bolsilloSel : {}) }}>{l}</button>
          ))}
        </div>

        <p style={S.telegram}>o dile al bot de Telegram, hablado: <em>gasté 30 mil en gasolina</em></p>

        <button onClick={guardar} disabled={guardando} style={{ ...S.guardar, opacity: guardando ? .6 : 1 }}>
          <Check size={19} /> {guardando ? 'Guardando…' : 'Guardar'}
        </button>
      </div>
    </div>
  );
}

const S = {
  fondo: { position: 'fixed', inset: 0, background: 'rgba(15,23,42,.55)', zIndex: 70,
           display: 'flex', alignItems: 'flex-end', justifyContent: 'center' },
  hoja: { background: '#fff', width: '100%', maxWidth: 520, borderRadius: '20px 20px 0 0',
          padding: '8px 20px 24px', paddingBottom: 'calc(24px + env(safe-area-inset-bottom))',
          maxHeight: '92vh', overflowY: 'auto' },
  cabecera: { position: 'relative', height: 26, display: 'flex', alignItems: 'center', justifyContent: 'center' },
  asa: { width: 38, height: 4, borderRadius: 2, background: '#CBD5E1' },
  cerrar: { position: 'absolute', right: 0, top: 0, background: 'none', border: 'none', color: '#94A3B8', cursor: 'pointer', padding: 4 },

  montoCaja: { padding: '14px 0 8px', textAlign: 'center' },
  montoInput: { width: '100%', border: 'none', outline: 'none', textAlign: 'center',
                fontFamily: "'Space Grotesk', system-ui, sans-serif", fontSize: '2.8rem',
                fontWeight: 800, letterSpacing: '-.02em', background: 'transparent' },

  segmento: { display: 'flex', gap: 8, marginBottom: 14 },
  segBtn: { flex: 1, padding: '13px 0', borderRadius: 12, border: 'none', background: '#F1F5F9',
            color: '#64748B', fontWeight: 750, fontSize: '.85rem', letterSpacing: '.04em', cursor: 'pointer' },

  // Ya no es una tira que se scrollea a ciegas: se ven todas de una.
  cats: { display: 'flex', flexWrap: 'wrap', gap: 7, marginBottom: 12 },
  cat: { display: 'inline-flex', alignItems: 'center', gap: 6, padding: '9px 13px', borderRadius: 999,
         border: 'none', background: '#F1F5F9', color: '#475569', fontSize: '.8rem',
         fontWeight: 600, whiteSpace: 'nowrap', cursor: 'pointer' },
  catSel: { background: '#2563EB', color: '#fff' },

  concepto: { width: '100%', boxSizing: 'border-box', padding: '12px 14px', borderRadius: 12,
              border: '1px solid #E2E8F0', outline: 'none', fontSize: '.9rem', color: '#0F172A',
              marginBottom: 12, background: '#fff' },

  bolsillos: { display: 'flex', gap: 8, marginBottom: 12 },
  bolsillo: { flex: 1, padding: '9px 0', borderRadius: 10, border: 'none', background: '#F1F5F9',
              color: '#64748B', fontSize: '.83rem', fontWeight: 600, cursor: 'pointer' },
  bolsilloSel: { background: '#0F172A', color: '#fff' },

  telegram: { fontSize: '.72rem', color: '#94A3B8', textAlign: 'center', margin: '0 0 12px' },
  guardar: { width: '100%', padding: '15px', borderRadius: 13, border: 'none', background: '#2563EB',
             color: '#fff', fontSize: '.95rem', fontWeight: 700, display: 'flex', alignItems: 'center',
             justifyContent: 'center', gap: 8, cursor: 'pointer' },
};
