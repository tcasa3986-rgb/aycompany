/**
 * Genera la página de muestra de un negocio que no tiene web.
 *
 * Dos decisiones que mandan sobre todo lo demás:
 *
 * 1. **No usa fotos del negocio.** Google no las entrega en este plan, y aunque
 *    las entregara, publicar las fotos de alguien en una página que no pidió es
 *    buscarse un problema. Además el hueco de la foto es el mejor gancho de
 *    conversación que hay: "es un boceto, mándame tus fotos y queda mejor".
 *
 * 2. **Una sola llamada a la IA por negocio, y barata.** Lo que cambia entre un
 *    negocio y otro son los textos; la estructura es la misma. Generar la página
 *    entera con IA costaría diez veces más y saldría peor.
 */
const https = require('https');

const PLACES_KEY = () => process.env.GOOGLE_PLACES_API_KEY;

/** Pide a Google el horario y el resumen, que son los detalles que dan credibilidad. */
function detalle(placeId) {
    const key = PLACES_KEY();
    if (!key || !placeId) return Promise.resolve({});
    const campos = 'regularOpeningHours,editorialSummary,primaryTypeDisplayName,formattedAddress';
    return new Promise(resolve => {
        const req = https.request({
            hostname: 'places.googleapis.com',
            path: `/v1/places/${placeId}?languageCode=es`,
            method: 'GET',
            headers: { 'X-Goog-Api-Key': key, 'X-Goog-FieldMask': campos },
        }, res => {
            let d = '';
            res.on('data', c => d += c);
            res.on('end', () => { try { resolve(JSON.parse(d)); } catch (_) { resolve({}); } });
        });
        req.on('error', () => resolve({}));
        req.end();
    });
}

/** Paleta por rubro: que no todas las demos se vean iguales. */
function paleta(rubro = '') {
    const r = rubro.toLowerCase();
    if (/odontolog|dentista|ortodon|clínic|clinic|médic|medic|salud|laboratorio|óptic/.test(r))
        return { a: '#0B4F6C', b: '#01BAEF', c: '#E8F6FA', nombre: 'salud' };
    if (/abogad|jurídic|juridic|contador|notar|asesor/.test(r))
        return { a: '#1A2238', b: '#9DAAF2', c: '#EEF0FB', nombre: 'legal' };
    if (/taller|mecánic|mecanic|automotriz|cerrajer|construc|ingenier/.test(r))
        return { a: '#172A3A', b: '#FF8C42', c: '#FFF1E6', nombre: 'tecnico' };
    if (/estétic|estetic|spa|belleza|peluquer|gimnasio/.test(r))
        return { a: '#3D2C4A', b: '#E0A3C4', c: '#FBF0F6', nombre: 'belleza' };
    if (/veterinar|mascota/.test(r))
        return { a: '#1E4034', b: '#6BBF59', c: '#EDF7EB', nombre: 'veterinaria' };
    return { a: '#0F172A', b: '#2563EB', c: '#EFF4FF', nombre: 'general' };
}

/** Los textos: lo único que de verdad cambia entre un negocio y otro. */
async function generarTextos(n, det) {
    const vacio = {
        titular: `${n.nombre}`,
        subtitulo: `Atención profesional en ${n.ciudad}. Pide tu cita hoy mismo.`,
        sobre: `Somos un equipo con años de experiencia en ${n.ciudad}, reconocido por nuestros clientes.`,
        servicios: [],
    };

    const key = process.env.OPENAI_API_KEY;
    if (!key) return vacio;

    const prompt = `Escribe los textos de una página web para este negocio colombiano real.

Negocio: ${n.nombre}
Rubro: ${n.rubro || n.categoria}
Ciudad: ${n.ciudad}
Calificación en Google: ${n.calificacion} estrellas con ${n.resenas} reseñas
${det?.editorialSummary?.text ? `Lo que dice Google: ${det.editorialSummary.text}` : ''}

Responde SOLO un JSON con esta forma exacta:
{
  "titular": "titular de 6 a 9 palabras, humano, que hable del resultado que busca el cliente, NO del negocio",
  "subtitulo": "una frase de 15 a 25 palabras que invite a pedir cita, mencionando la ciudad",
  "sobre": "párrafo de 40 a 60 palabras sobre el negocio, cálido y creíble, que use las reseñas como prueba sin exagerar",
  "servicios": [{"titulo":"nombre corto del servicio","desc":"una línea de 8 a 14 palabras"}]
}

Reglas:
- 6 servicios, los típicos y reales de ese rubro en Colombia.
- Español de Colombia, natural, sin palabras rimbombantes.
- NO inventes años de experiencia, premios, certificaciones ni cifras que no te di.
- NO uses "líderes del mercado" ni "excelencia" ni frases de folleto.`;

    try {
        const OpenAI = require('openai');
        const openai = new OpenAI({ apiKey: key });
        const r = await openai.chat.completions.create({
            model: 'gpt-4o-mini',
            messages: [{ role: 'user', content: prompt }],
            response_format: { type: 'json_object' },
            max_tokens: 900,
            temperature: 0.7,
        });
        const j = JSON.parse(r.choices[0].message.content);
        return {
            titular:   j.titular   || vacio.titular,
            subtitulo: j.subtitulo || vacio.subtitulo,
            sobre:     j.sobre     || vacio.sobre,
            servicios: Array.isArray(j.servicios) ? j.servicios.slice(0, 6) : [],
        };
    } catch (e) {
        console.warn('Demo: no pude generar los textos —', e.message);
        return vacio;
    }
}

const esc = s => String(s == null ? '' : s)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');

const ICONOS = ['◆', '✚', '✦', '❖', '✧', '◈'];

/** La plantilla. Autocontenida: sin fuentes ni imágenes de fuera, carga al instante
 *  aunque la abra con datos móviles, que es como la va a abrir el dueño. */
function plantilla(n, t, det) {
    const p = paleta(n.rubro || n.categoria);
    const tel = (n.telefono || '').replace(/\D/g, '');
    const wa = tel ? `https://wa.me/57${tel.replace(/^57/, '')}` : null;
    const horario = (det?.regularOpeningHours?.weekdayDescriptions || []);
    const estrellas = '★'.repeat(Math.round(Number(n.calificacion) || 0)).padEnd(5, '☆');

    const servicios = (t.servicios || []).map((s, i) => `
      <article class="serv">
        <div class="ic">${ICONOS[i % ICONOS.length]}</div>
        <h3>${esc(s.titulo)}</h3>
        <p>${esc(s.desc)}</p>
      </article>`).join('');

    return `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>${esc(n.nombre)}</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  :root{--a:${p.a};--b:${p.b};--c:${p.c}}
  body{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;color:#0f172a;line-height:1.6;background:#fff}
  .wrap{max-width:1140px;margin:0 auto;padding:0 24px}
  a{text-decoration:none}

  header{position:sticky;top:0;z-index:50;background:rgba(255,255,255,.94);backdrop-filter:blur(8px);border-bottom:1px solid #e9eef5}
  .nav{display:flex;align-items:center;justify-content:space-between;padding:16px 0;gap:16px}
  .logo{font-weight:800;font-size:1.05rem;color:var(--a);letter-spacing:-.02em}
  .menu{display:flex;gap:26px;font-size:.9rem;color:#475569}
  .menu a:hover{color:var(--b)}
  .cta{background:var(--b);color:#fff;padding:11px 20px;border-radius:999px;font-weight:700;font-size:.88rem;white-space:nowrap}

  .hero{background:linear-gradient(135deg,var(--a) 0%,color-mix(in srgb,var(--a) 70%,var(--b)) 100%);color:#fff;padding:84px 0 92px;position:relative;overflow:hidden}
  .hero::after{content:'';position:absolute;right:-120px;top:-120px;width:460px;height:460px;border-radius:50%;background:rgba(255,255,255,.07)}
  .hero h1{font-size:clamp(2rem,5vw,3.3rem);line-height:1.12;font-weight:800;letter-spacing:-.03em;max-width:17ch}
  .hero p{margin-top:18px;font-size:1.08rem;opacity:.92;max-width:52ch}
  .botones{margin-top:32px;display:flex;gap:12px;flex-wrap:wrap}
  .b1{background:#fff;color:var(--a);padding:15px 28px;border-radius:999px;font-weight:700}
  .b2{border:1.5px solid rgba(255,255,255,.55);color:#fff;padding:15px 28px;border-radius:999px;font-weight:600}
  .prueba{margin-top:28px;display:inline-flex;align-items:center;gap:10px;background:rgba(255,255,255,.12);padding:9px 16px;border-radius:999px;font-size:.9rem}
  .est{color:#FBBF24;letter-spacing:2px}

  .cifras{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1px;background:#e9eef5;margin:-38px auto 0;max-width:1000px;border-radius:16px;overflow:hidden;box-shadow:0 12px 32px rgba(15,23,42,.1);position:relative;z-index:10}
  .cif{background:#fff;padding:24px 16px;text-align:center}
  .cif b{display:block;font-size:1.7rem;color:var(--a);font-weight:800;letter-spacing:-.02em}
  .cif span{font-size:.8rem;color:#64748b}

  section{padding:72px 0}
  .tit{text-align:center;max-width:44ch;margin:0 auto 44px}
  .tit h2{font-size:clamp(1.5rem,3.4vw,2.1rem);color:var(--a);font-weight:800;letter-spacing:-.02em}
  .tit p{color:#64748b;margin-top:10px}

  .servs{display:grid;grid-template-columns:repeat(auto-fit,minmax(265px,1fr));gap:18px}
  .serv{background:#fff;border:1px solid #e9eef5;border-radius:18px;padding:26px;transition:.2s}
  .serv:hover{box-shadow:0 10px 30px rgba(15,23,42,.08);transform:translateY(-3px)}
  .ic{width:46px;height:46px;border-radius:13px;background:var(--c);color:var(--b);display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:14px}
  .serv h3{font-size:1.02rem;color:var(--a);margin-bottom:6px}
  .serv p{color:#64748b;font-size:.9rem}

  .sobre{background:var(--c)}
  .sobre .wrap{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center}
  .sobre h2{font-size:clamp(1.4rem,3vw,2rem);color:var(--a);margin-bottom:14px;letter-spacing:-.02em}
  .sobre p{color:#475569}
  .foto{aspect-ratio:4/3;border-radius:20px;background:
      repeating-linear-gradient(45deg,rgba(255,255,255,.5) 0 12px,transparent 12px 24px),var(--b);
      display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;text-align:center;padding:24px;font-size:.92rem;opacity:.9}

  .contacto .wrap{display:grid;grid-template-columns:1fr 1fr;gap:44px}
  .dato{display:flex;gap:14px;padding:16px 0;border-bottom:1px solid #e9eef5}
  .dato b{display:block;color:var(--a);font-size:.82rem;text-transform:uppercase;letter-spacing:.06em}
  .dato span{color:#475569}
  .tel{font-size:1.5rem;font-weight:800;color:var(--b)}
  .hor{font-size:.9rem;color:#475569;line-height:2}

  .final{background:var(--a);color:#fff;text-align:center;padding:72px 24px}
  .final h2{font-size:clamp(1.5rem,3.6vw,2.3rem);margin-bottom:14px;letter-spacing:-.02em}
  .final p{opacity:.85;margin-bottom:28px}

  footer{background:#0b1220;color:#8ea0b5;padding:36px 0;font-size:.86rem;text-align:center}

  @media(max-width:860px){
    .menu{display:none}
    .sobre .wrap,.contacto .wrap{grid-template-columns:1fr}
    .hero{padding:60px 0 72px}
  }
</style>
</head>
<body>

<header><div class="wrap nav">
  <div class="logo">${esc(n.nombre)}</div>
  <nav class="menu">
    <a href="#servicios">Servicios</a><a href="#nosotros">Nosotros</a><a href="#contacto">Contacto</a>
  </nav>
  ${wa ? `<a class="cta" href="${wa}" target="_blank" rel="noopener">Pedir cita</a>` : ''}
</div></header>

<div class="hero"><div class="wrap">
  <h1>${esc(t.titular)}</h1>
  <p>${esc(t.subtitulo)}</p>
  <div class="botones">
    ${wa ? `<a class="b1" href="${wa}" target="_blank" rel="noopener">Escribir por WhatsApp</a>` : ''}
    ${n.telefono ? `<a class="b2" href="tel:${esc(n.telefono)}">Llamar ${esc(n.telefono)}</a>` : ''}
  </div>
  ${Number(n.resenas) > 0 ? `<div class="prueba"><span class="est">${estrellas}</span> <b>${esc(n.calificacion)}</b> · ${esc(n.resenas)} reseñas en Google</div>` : ''}
</div></div>

<div class="wrap"><div class="cifras">
  ${Number(n.resenas) > 0 ? `<div class="cif"><b>${esc(n.resenas)}</b><span>reseñas reales</span></div>` : ''}
  ${Number(n.calificacion) > 0 ? `<div class="cif"><b>${esc(n.calificacion)}</b><span>de calificación</span></div>` : ''}
  <div class="cif"><b>${esc(n.ciudad)}</b><span>donde atendemos</span></div>
  ${horario.length ? `<div class="cif"><b>Lun a Sáb</b><span>horario de atención</span></div>` : ''}
</div></div>

${servicios ? `<section id="servicios"><div class="wrap">
  <div class="tit"><h2>Lo que hacemos</h2><p>Todo lo que necesitas, en un solo lugar.</p></div>
  <div class="servs">${servicios}</div>
</div></section>` : ''}

<section class="sobre" id="nosotros"><div class="wrap">
  <div>
    <h2>Sobre nosotros</h2>
    <p>${esc(t.sobre)}</p>
  </div>
  <div class="foto">Aquí van las fotos de tu local<br>y de tu equipo</div>
</div></section>

<section class="contacto" id="contacto"><div class="wrap">
  <div>
    <div class="tit" style="text-align:left;margin:0 0 20px"><h2>Dónde estamos</h2></div>
    <div class="dato"><div><b>Dirección</b><span>${esc(n.direccion)}</span></div></div>
    ${n.telefono ? `<div class="dato"><div><b>Teléfono</b><span class="tel">${esc(n.telefono)}</span></div></div>` : ''}
  </div>
  <div>
    ${horario.length ? `<div class="tit" style="text-align:left;margin:0 0 14px"><h2 style="font-size:1.1rem">Horario</h2></div>
    <div class="hor">${horario.map(h => esc(h)).join('<br>')}</div>` : ''}
  </div>
</div></section>

<div class="final">
  <h2>¿Agendamos tu cita?</h2>
  <p>Escríbenos y te respondemos hoy mismo.</p>
  ${wa ? `<a class="b1" href="${wa}" target="_blank" rel="noopener">Escribir por WhatsApp</a>` : ''}
</div>

<footer><div class="wrap">
  ${esc(n.nombre)} · ${esc(n.ciudad)}, Colombia${n.telefono ? ` · ${esc(n.telefono)}` : ''}
</div></footer>

</body></html>`;
}

/**
 * Elige la plantilla del rubro. Una página de odontología y una de taller no
 * se parecen en nada, y una plantilla para todos es justo lo que delata el
 * molde: la primera tanda se rechazó por eso.
 */
function plantillaDe(rubro = '') {
    const r = String(rubro).toLowerCase();
    if (/odontolog|dentista|ortodon|dental/.test(r)) {
        return require('./demos/plantillaOdontologia').plantillaOdontologia;
    }
    return plantilla;   // la base, mientras se escriben las demás
}

/** Genera la demo completa de un negocio. */
async function generar(negocio) {
    const det = await detalle(negocio.place_id);
    const textos = await generarTextos(negocio, det);
    const hacer = plantillaDe(negocio.rubro || negocio.categoria);
    return { html: hacer(negocio, textos, det), textos, detalle: det };
}

module.exports = { generar, plantilla, plantillaDe, generarTextos, paleta, detalle };
