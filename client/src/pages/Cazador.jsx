import { useState, useEffect } from 'react';
import api from '../api/axios';
import toast from 'react-hot-toast';
import { Search, Wand2, Copy, Send, X, Flame, ExternalLink, Phone, Star } from 'lucide-react';
import { C, Pagina, Cabecera, Tarjeta, Boton, BotonIcono, Campo, Dos, entrada, Pildora, Franja } from '../components/ui';

// Cazador de negocios sin página web.
//
// El orden de la pantalla es el orden en que se trabaja:
//   1. Quién abrió su demo   ← lo primero, es a quién hay que llamar HOY
//   2. Buscar más
//   3. La lista, ordenada por quién puede pagar mejor
//
// Un negocio que abrió su página tres veces y no ha contestado vale más que
// veinte que nunca la abrieron. Por eso van arriba y aparte.

const PASOS = [
  ['encontrado',    'Encontrados'],
  ['demo_generada', 'Con demo'],
  ['enviado',       'Enviados'],
  ['abrio',         'Abrieron'],
  ['respondio',     'Respondieron'],
  ['cliente',       'Clientes'],
];

const RUBROS = ['odontología', 'abogados', 'veterinaria', 'estética', 'taller mecánico',
                'constructora', 'gimnasio', 'restaurante', 'inmobiliaria', 'óptica'];

export default function Cazador() {
  const [data, setData] = useState([]);
  const [embudo, setEmbudo] = useState({});
  const [calientes, setCalientes] = useState([]);
  const [busq, setBusq] = useState({ rubro: 'odontología', ciudad: 'Ibagué' });
  const [buscando, setBuscando] = useState(false);
  const [generando, setGenerando] = useState(null);
  const [filtro, setFiltro] = useState('');

  const cargar = () => api.get('/cazador' + (filtro ? `?estado=${filtro}` : ''))
    .then(x => { setData(x.data.data); setEmbudo(x.data.embudo); setCalientes(x.data.calientes || []); });
  useEffect(() => { cargar(); }, [filtro]);

  async function buscar(e) {
    e?.preventDefault();
    if (!busq.rubro || !busq.ciudad) return toast.error('Dime el rubro y la ciudad');
    setBuscando(true);
    try {
      const x = await api.post('/cazador/buscar', busq);
      toast.success(`${x.data.msg} · ${x.data.con_web} ya tenían web`);
      cargar();
    } catch (err) { toast.error(err.response?.data?.msg || 'No se pudo buscar'); }
    setBuscando(false);
  }

  async function generar(n) {
    setGenerando(n.id);
    try {
      const x = await api.post(`/cazador/${n.id}/generar`);
      toast.success(x.data.msg);
      cargar();
    } catch (err) { toast.error(err.response?.data?.msg || 'No se pudo generar'); }
    setGenerando(null);
  }

  async function marcar(n, estado) {
    const extra = {};
    if (estado === 'descartado') {
      const m = prompt('¿Por qué lo descartas?');
      if (m === null) return;
      extra.motivo_descarte = m;
    }
    try { await api.put(`/cazador/${n.id}`, { estado, ...extra }); cargar(); }
    catch { toast.error('No se pudo'); }
  }

  const enlaceDe = n => `${window.location.origin}/demo/${n.demo_slug}`;

  // El mensaje que de verdad se manda. Escrito para que no parezca plantilla:
  // nombra el negocio, dice de dónde salió y da salida fácil.
  const mensajeDe = n => `Hola, buen día 👋 Soy Cristian, de AI Company.

Vi que ${n.nombre} tiene ${n.resenas} reseñas en Google con ${n.calificacion} estrellas — muy bueno — pero todavía no tiene página web.

Me tomé el atrevimiento de armarles un boceto para que vean cómo se vería:
${enlaceDe(n)}

Si les gusta, hablamos. Y si no les interesa, me dice y no vuelvo a escribir 🙌`;

  function copiar(texto, que = 'Copiado') {
    navigator.clipboard.writeText(texto).then(() => toast.success(que), () => toast.error('No se pudo copiar'));
  }

  return (
    <Pagina>
      <Cabecera titulo="Negocios sin página web"
        sub={`${data.length} en la lista · ${embudo.abrio || 0} abrieron su demo`} />

      {/* 1. A quién llamar HOY */}
      {calientes.length > 0 && (
        <Tarjeta titulo="Abrieron su demo y no han contestado" acento={C.ambar}>
          <p style={S.nota}>
            Estos ya vieron su página. Es la señal de compra más honesta que hay — llámalos.
          </p>
          <div style={{ display: 'grid', gap: 9 }}>
            {calientes.map(n => (
              <div key={n.id} style={S.caliente}>
                <Flame size={17} color="#F59E0B" />
                <div style={{ flex: 1, minWidth: 0 }}>
                  <div style={S.nombre}>{n.nombre}</div>
                  <div style={S.sub}>
                    La abrió <strong>{n.visitas} {n.visitas === 1 ? 'vez' : 'veces'}</strong>
                    {n.telefono ? ` · ${n.telefono}` : ''}
                  </div>
                </div>
                {n.telefono && (
                  <a href={`https://wa.me/57${n.telefono.replace(/\D/g, '')}`} target="_blank" rel="noopener noreferrer">
                    <Boton color={C.verde}><Phone size={15} /> Escribir</Boton>
                  </a>
                )}
                <Boton variante="borde" onClick={() => marcar(n, 'respondio')}>Contestó</Boton>
              </div>
            ))}
          </div>
        </Tarjeta>
      )}

      {/* 2. Buscar */}
      <Tarjeta titulo="Buscar negocios" style={{ marginTop: 12 }}>
        <form onSubmit={buscar}>
          <Dos>
            <Campo label="¿Qué rubro?">
              <input list="rubros" value={busq.rubro}
                     onChange={e => setBusq(b => ({ ...b, rubro: e.target.value }))} style={entrada} />
              <datalist id="rubros">{RUBROS.map(r => <option key={r} value={r} />)}</datalist>
            </Campo>
            <Campo label="¿En qué ciudad?">
              <input value={busq.ciudad} onChange={e => setBusq(b => ({ ...b, ciudad: e.target.value }))}
                     style={entrada} placeholder="Ibagué, Bogotá, Soacha…" />
            </Campo>
          </Dos>
          <Boton type="submit" disabled={buscando}>
            <Search size={16} /> {buscando ? 'Buscando en Google Maps…' : 'Buscar'}
          </Boton>
        </form>
      </Tarjeta>

      {/* El embudo */}
      <div style={S.embudo}>
        {PASOS.map(([k, l]) => (
          <button key={k} onClick={() => setFiltro(filtro === k ? '' : k)}
            style={{ ...S.paso, ...(filtro === k ? S.pasoActivo : {}) }}>
            <div style={S.pasoNum}>{embudo[k] ?? 0}</div>
            <div style={S.pasoTxt}>{l}</div>
          </button>
        ))}
      </div>

      {filtro && (
        <div style={{ margin: '12px 0' }}>
          <Pildora activa onClick={() => setFiltro('')}>Viendo solo: {filtro} · quitar filtro</Pildora>
        </div>
      )}

      {/* 3. La lista */}
      {data.length === 0 ? (
        <Tarjeta style={{ marginTop: 14 }}>
          <p style={S.vacio}>
            Busca un rubro y una ciudad arriba. Google te dice cuáles no tienen página web.
          </p>
        </Tarjeta>
      ) : (
        <div style={{ display: 'grid', gap: 10, marginTop: 14 }}>
          {data.map(n => (
            <div key={n.id} style={{ ...S.fila, opacity: n.estado === 'descartado' ? .5 : 1 }}>
              <div style={{ ...S.score, background: n.score >= 80 ? '#DCFCE7' : n.score >= 60 ? '#FEF3C7' : '#F1F5F9',
                             color: n.score >= 80 ? '#15803D' : n.score >= 60 ? '#92400E' : '#64748B' }}>
                {n.score}
              </div>

              <div style={{ flex: 1, minWidth: 0 }}>
                <div style={S.nombre}>{n.nombre}</div>
                <div style={S.sub}>
                  {Number(n.resenas) > 0 && (
                    <span><Star size={11} style={{ verticalAlign: -1, color: '#F59E0B' }} /> {n.calificacion} · {n.resenas} reseñas</span>
                  )}
                  {n.telefono && <span style={S.punto}>·</span>}
                  {n.telefono && <span>{n.telefono}</span>}
                  {n.web_parcial && <><span style={S.punto}>·</span><span style={S.tag}>usa redes como web</span></>}
                </div>
                {n.visitas > 0 && (
                  <div style={S.visitas}>🔥 Abrió su demo {n.visitas} {n.visitas === 1 ? 'vez' : 'veces'}</div>
                )}
                {n.motivo_descarte && <div style={S.descarte}>Descartado: {n.motivo_descarte}</div>}
              </div>

              <div style={S.acciones}>
                {!n.demo_slug ? (
                  <Boton onClick={() => generar(n)} disabled={generando === n.id}>
                    <Wand2 size={15} /> {generando === n.id ? 'Creando…' : 'Crear demo'}
                  </Boton>
                ) : (
                  <>
                    <a href={enlaceDe(n)} target="_blank" rel="noopener noreferrer">
                      <BotonIcono color={C.azul} titulo="Ver la demo"><ExternalLink size={15} /></BotonIcono>
                    </a>
                    <BotonIcono color={C.verde} titulo="Copiar el mensaje listo"
                      onClick={() => { copiar(mensajeDe(n), 'Mensaje copiado, pégalo en WhatsApp'); }}>
                      <Copy size={15} />
                    </BotonIcono>
                    {n.telefono && (
                      <a href={`https://wa.me/57${n.telefono.replace(/\D/g, '')}?text=${encodeURIComponent(mensajeDe(n))}`}
                         target="_blank" rel="noopener noreferrer"
                         onClick={() => marcar(n, 'enviado')}>
                        <BotonIcono color={C.verde} titulo="Abrir WhatsApp con el mensaje"><Send size={15} /></BotonIcono>
                      </a>
                    )}
                  </>
                )}
                <BotonIcono color={C.tenue} titulo="Descartar" onClick={() => marcar(n, 'descartado')}>
                  <X size={15} />
                </BotonIcono>
              </div>
            </div>
          ))}
        </div>
      )}

      <Franja color={C.azul}>
        Manda los mensajes <strong>de a pocos y espaciados</strong>. WhatsApp bloquea los
        números que mandan muchos mensajes nuevos seguidos, y ese número es tu negocio.
      </Franja>
    </Pagina>
  );
}

const S = {
  nota: { fontSize: '.82rem', color: C.tenue, margin: '0 0 12px' },
  caliente: { display: 'flex', alignItems: 'center', gap: 11, background: '#fff', borderRadius: 12, padding: '11px 13px', flexWrap: 'wrap' },

  fila: { display: 'flex', alignItems: 'center', gap: 12, background: '#fff', borderRadius: 13,
          padding: '12px 14px', boxShadow: '0 1px 2px rgba(15,23,42,.06)' },
  score: { width: 44, height: 44, borderRadius: 12, display: 'flex', alignItems: 'center',
           justifyContent: 'center', fontWeight: 800, fontSize: '.95rem', flexShrink: 0,
           fontFamily: "'Space Grotesk', system-ui, sans-serif" },
  nombre: { fontWeight: 700, color: C.tinta, fontSize: '.93rem', overflow: 'hidden',
            textOverflow: 'ellipsis', whiteSpace: 'nowrap' },
  sub: { fontSize: '.78rem', color: C.tenue, display: 'flex', alignItems: 'center', gap: 5,
         marginTop: 2, flexWrap: 'wrap' },
  punto: { color: '#CBD5E1' },
  tag: { fontSize: '.68rem', fontWeight: 700, color: '#1D4ED8', background: '#DBEAFE',
         padding: '2px 7px', borderRadius: 999 },
  visitas: { fontSize: '.76rem', color: '#92400E', fontWeight: 700, marginTop: 4 },
  descarte: { fontSize: '.74rem', color: C.rojo, marginTop: 3 },
  acciones: { display: 'flex', alignItems: 'center', gap: 3, flexShrink: 0 },

  embudo: { display: 'flex', gap: 7, marginTop: 16, overflowX: 'auto', paddingBottom: 4 },
  paso: { flex: '1 0 92px', background: '#fff', borderRadius: 12, padding: '12px 8px',
          textAlign: 'center', boxShadow: '0 1px 2px rgba(15,23,42,.06)', border: '1.5px solid transparent',
          cursor: 'pointer' },
  pasoActivo: { borderColor: C.azul, background: '#EFF4FF' },
  pasoNum: { fontFamily: "'Space Grotesk', system-ui, sans-serif", fontSize: '1.45rem',
             fontWeight: 800, color: C.tinta, lineHeight: 1 },
  pasoTxt: { fontSize: '.69rem', color: C.tenue, marginTop: 4 },

  vacio: { textAlign: 'center', color: C.tenue, margin: 0, padding: '22px 0', fontSize: '.88rem' },
};
