/**
 * La página de muestra para odontologías.
 *
 * Escrita contra `DESIGN-odontologia.md` (skill web-design: spec primero,
 * código después) y contra la investigación del nicho que está en el vault.
 *
 * La versión anterior era una plantilla genérica y Cristian la rechazó con
 * razón. Lo que cambia aquí, y por qué:
 *
 *   · **Cero rectángulos de color** donde van las fotos. Era lo que más
 *     delataba el molde. Ahora el espacio se resuelve con composición.
 *   · **Serif cálida (Fraunces)** en los titulares. Casi ninguna web dental la
 *     usa: es lo que hace que no se parezca a las demás.
 *   · **Su horario de Google, vivo**: la barra dice "Abierto ahora" o "Abrimos
 *     mañana a las 8:30". Es el detalle que hace que el dueño piense "esto lo
 *     hicieron para mí".
 *   · **Tres momentos que impresionan**, no una lista de secciones.
 */

const esc = s => String(s == null ? '' : s)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

/** Paletas distintas por clínica: dos del mismo gremio no pueden verse igual. */
const PALETAS = [
    { tinta: '12,43,47',  prof: '17,78,82',   agua: '46,139,148', menta: '215,237,236', coral: '232,112,91',  cs: '253,237,233' },
    { tinta: '26,34,56',  prof: '38,58,110',  agua: '72,110,196', menta: '222,230,247', coral: '233,142,74',  cs: '253,240,229' },
    { tinta: '38,26,46',  prof: '79,48,92',   agua: '140,97,158', menta: '238,228,244', coral: '219,120,122', cs: '252,235,236' },
    { tinta: '15,42,32',  prof: '22,76,56',   agua: '57,140,106', menta: '215,239,228', coral: '226,145,70',  cs: '253,240,227' },
];
const paletaDe = txt => PALETAS[[...String(txt)].reduce((a, c) => a + c.charCodeAt(0), 0) % PALETAS.length];

/** Iconos propios en SVG. Nada de emojis en una web de salud. */
const ICONOS = {
    ortodoncia: '<path d="M4 9h16M4 15h16M8 9v6M12 9v6M16 9v6"/>',
    implante:   '<path d="M12 3v18M8 7c0-2 1.8-3.5 4-3.5S16 5 16 7c0 3-1.2 4-1.6 7-.2 1.6-.8 3-2.4 3s-2.2-1.4-2.4-3C9.2 11 8 10 8 7z"/>',
    sonrisa:    '<circle cx="12" cy="12" r="9"/><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/>',
    blanquea:   '<path d="M12 3l2.2 5.6L20 10l-4.4 3.6L17 20l-5-3.3L7 20l1.4-6.4L4 10l5.8-1.4z"/>',
    endodoncia: '<path d="M12 2c3.3 0 5.5 2.3 5.5 5.3 0 4.3-2 6-2.6 10-.3 2.2-1.2 4.7-2.9 4.7s-2.6-2.5-2.9-4.7C8.5 13.3 6.5 11.6 6.5 7.3 6.5 4.3 8.7 2 12 2z"/>',
    urgencia:   '<path d="M12 2v20M2 12h20"/>',
    general:    '<path d="M12 21s-7-4.5-7-10a4 4 0 017-2.6A4 4 0 0119 11c0 5.5-7 10-7 10z"/>',
    nino:       '<circle cx="12" cy="8" r="4"/><path d="M5 21c0-4 3.1-7 7-7s7 3 7 7"/>',
};
const iconoDe = t => {
    const s = String(t).toLowerCase();
    if (/ortodon|bracket|aline/.test(s)) return ICONOS.ortodoncia;
    if (/implant/.test(s)) return ICONOS.implante;
    if (/diseñ|estétic|estetic|sonris/.test(s)) return ICONOS.sonrisa;
    if (/blanquea/.test(s)) return ICONOS.blanquea;
    if (/endodon|conducto/.test(s)) return ICONOS.endodoncia;
    if (/urgenc|emergenc|dolor/.test(s)) return ICONOS.urgencia;
    if (/niñ|infant|pediatr/.test(s)) return ICONOS.nino;
    return ICONOS.general;
};

/** El horario de Google → datos que el navegador pueda usar para decir si está abierto. */
function horarioJS(descripciones = []) {
    const DIAS = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    const tabla = {};
    for (const linea of descripciones) {
        const m = String(linea).match(/^([^:]+):\s*(.+)$/);
        if (!m) continue;
        const dia = DIAS.findIndex(d => m[1].toLowerCase().includes(d));
        if (dia < 0) continue;
        const h = m[2].match(/(\d{1,2}):(\d{2}).{1,3}(\d{1,2}):(\d{2})/);
        tabla[dia] = h ? { de: +h[1] * 60 + +h[2], a: +h[3] * 60 + +h[4], txt: m[2] } : null;
    }
    return tabla;
}

function plantillaOdontologia(n, t, det) {
    const p = paletaDe(n.nombre);
    const tel = String(n.telefono || '').replace(/\D/g, '');
    const wa = tel ? `https://wa.me/57${tel.replace(/^57/, '')}` : null;
    const dias = det?.regularOpeningHours?.weekdayDescriptions || [];
    const tabla = horarioJS(dias);
    const barrio = (n.direccion || '').split(',')[0].trim();
    const resenas = Number(n.resenas) || 0;
    const cal = Number(n.calificacion) || 0;

    const servicios = (t.servicios || []).slice(0, 6);
    const cards = servicios.map((s, i) => `
      <article class="serv${i === 0 ? ' serv-gr' : ''} reveal" style="--d:${i * 70}ms">
        <svg class="si" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${iconoDe(s.titulo)}</svg>
        <h3>${esc(s.titulo)}</h3>
        <p>${esc(s.desc)}</p>
        ${wa ? `<a class="sl" href="${wa}" target="_blank" rel="noopener">Agendar <span aria-hidden="true">→</span></a>` : ''}
      </article>`).join('');

    return `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<meta name="description" content="${esc(n.nombre)} — odontología en ${esc(n.ciudad)}.">
<title>${esc(n.nombre)}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<style>
:root{
  --tinta:rgb(${p.tinta});--tinta-rgb:${p.tinta};
  --prof:rgb(${p.prof});--prof-rgb:${p.prof};
  --agua:rgb(${p.agua});--agua-rgb:${p.agua};
  --menta:rgb(${p.menta});--menta-rgb:${p.menta};
  --coral:rgb(${p.coral});--coral-rgb:${p.coral};
  --coral-sua:rgb(${p.cs});
  --crema:#FBFAF7;--blanco:#fff;--gris:#5B6F72;--borde:#E4EBEA;--oro:#E0A82E;
  --serif:'Fraunces',Georgia,serif;--sans:'DM Sans',-apple-system,'Segoe UI',system-ui,sans-serif;
  --s2:0 6px 18px rgba(var(--tinta-rgb),.07);--s3:0 18px 44px rgba(var(--tinta-rgb),.1);
  --s4:0 30px 70px rgba(var(--tinta-rgb),.14);
}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:var(--sans);background:var(--crema);color:var(--tinta);line-height:1.65;overflow-x:hidden}
.w{max-width:1160px;margin:0 auto;padding:0 24px}
a{text-decoration:none;color:var(--agua)}
a:focus-visible,button:focus-visible{outline:3px solid rgba(var(--coral-rgb),.55);outline-offset:3px;border-radius:8px}
h1,h2,.cif b{font-family:var(--serif);letter-spacing:-.03em;line-height:1.08}
.ante{font-size:.78rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--agua)}

.btn{font:700 .95rem/1 var(--sans);padding:16px 30px;border-radius:999px;display:inline-flex;
     align-items:center;gap:9px;min-height:48px;border:1.5px solid transparent;cursor:pointer;
     transition:transform .18s cubic-bezier(.2,.7,.3,1),box-shadow .18s,background .18s}
.btn-pri{background:var(--prof);color:#fff}
.btn-pri:hover{background:var(--tinta);transform:translateY(-2px);box-shadow:0 10px 24px rgba(var(--prof-rgb),.28)}
.btn-pri:active{transform:translateY(0)}
.btn-wa{background:#25D366;color:#06281A}
.btn-wa:hover{filter:brightness(1.06);transform:translateY(-2px);box-shadow:0 10px 24px rgba(37,211,102,.32)}
.btn-gho{background:transparent;color:var(--prof);border-color:rgba(var(--prof-rgb),.3)}
.btn-gho:hover{border-color:var(--prof);background:rgba(var(--prof-rgb),.05)}

/* barra */
header{position:fixed;inset:0 0 auto;z-index:90;transition:background .3s,box-shadow .3s,backdrop-filter .3s}
header.on{background:rgba(251,250,247,.92);backdrop-filter:blur(12px);box-shadow:0 1px 0 var(--borde)}
.nav{display:flex;align-items:center;gap:18px;padding:16px 0}
.marca{font-family:var(--serif);font-weight:700;font-size:1.06rem;color:var(--prof);letter-spacing:-.02em;
       flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.abierto{display:inline-flex;align-items:center;gap:7px;font-size:.78rem;font-weight:700;
         padding:7px 13px;border-radius:999px;background:var(--menta);color:var(--prof);white-space:nowrap}
.pt{width:7px;height:7px;border-radius:50%;background:#25D366;box-shadow:0 0 0 0 rgba(37,211,102,.6);animation:lat 2.4s infinite}
@keyframes lat{70%{box-shadow:0 0 0 9px rgba(37,211,102,0)}100%{box-shadow:0 0 0 0 rgba(37,211,102,0)}}
.cerrado .pt{background:var(--coral);animation:none}

/* 1er momento: la primera pantalla */
.hero{position:relative;padding:150px 0 96px;overflow:hidden}
.mancha{position:absolute;border-radius:50%;filter:blur(70px);opacity:.5;pointer-events:none}
.m1{width:620px;height:620px;background:var(--menta);top:-230px;right:-170px}
.m2{width:460px;height:460px;background:var(--coral-sua);bottom:-200px;left:-130px}
.hero .w{position:relative;z-index:2}
h1{font-size:clamp(2.4rem,6vw,4.2rem);max-width:15ch;margin:18px 0 0}
h1 .ln{display:block;overflow:hidden}
h1 .ln>span{display:block;transform:translateY(105%);animation:sube .9s cubic-bezier(.16,1,.3,1) forwards;animation-delay:var(--d)}
@keyframes sube{to{transform:translateY(0)}}
.lead{font-size:1.12rem;color:var(--gris);max-width:50ch;margin-top:20px;opacity:0;animation:ap .7s .75s forwards}
@keyframes ap{to{opacity:1}}
.hbtn{display:flex;gap:12px;flex-wrap:wrap;margin-top:34px;opacity:0;animation:ap .7s .9s forwards}
.social{display:inline-flex;align-items:center;gap:13px;margin-top:34px;padding:14px 20px;background:var(--blanco);
        border:1px solid var(--borde);border-radius:18px;box-shadow:var(--s2);opacity:0;
        animation:subeap .8s 1.05s forwards;max-width:100%}
@keyframes subeap{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
.est{color:var(--oro);letter-spacing:2px;font-size:1.05rem}
.social b{font-family:var(--serif);font-size:1.3rem}
.social span{font-size:.88rem;color:var(--gris)}

/* 2do momento: la franja de cifras */
.cifras{background:var(--prof);color:#fff;padding:clamp(54px,7vw,84px) 0}
.cg{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:34px;text-align:center}
.cif b{display:block;font-size:clamp(2rem,4vw,3rem);line-height:1}
.cif span{font-size:.86rem;opacity:.74;display:block;margin-top:7px}

/* 3er momento: servicios, rejilla no uniforme + luz que sigue el cursor */
section{padding:clamp(64px,9vw,118px) 0}
.tit{max-width:46ch;margin-bottom:46px}
h2{font-size:clamp(1.7rem,3.6vw,2.6rem);margin-top:10px}
.tit p{color:var(--gris);margin-top:12px}
.sg{display:grid;grid-template-columns:repeat(auto-fit,minmax(268px,1fr));gap:20px}
.serv{position:relative;background:var(--blanco);border:1px solid var(--borde);border-radius:22px;
      padding:30px;overflow:hidden;transition:transform .22s,box-shadow .22s,border-color .22s}
.serv::before{content:'';position:absolute;inset:0;opacity:0;transition:opacity .3s;pointer-events:none;
  background:radial-gradient(420px circle at var(--mx,50%) var(--my,50%),rgba(var(--agua-rgb),.1),transparent 42%)}
.serv:hover{transform:translateY(-5px);box-shadow:var(--s3);border-color:rgba(var(--agua-rgb),.4)}
.serv:hover::before{opacity:1}
.serv-gr{grid-column:span 2}
.si{width:30px;height:30px;color:var(--agua);margin-bottom:16px}
.serv h3{font-size:1.08rem;margin-bottom:7px}
.serv p{color:var(--gris);font-size:.94rem}
.sl{display:inline-block;margin-top:16px;font-weight:700;font-size:.88rem}
.sl span{display:inline-block;transition:transform .2s}
.sl:hover span{transform:translateX(4px)}

.sobre{background:var(--menta)}
.sg2{display:grid;grid-template-columns:1.05fr .95fr;gap:clamp(32px,5vw,64px);align-items:center}
.cita{font-family:var(--serif);font-size:clamp(1.3rem,2.4vw,1.75rem);line-height:1.35;color:var(--prof)}
.cita::before{content:'“';font-size:2.6em;line-height:0;vertical-align:-.35em;opacity:.26;margin-right:.06em}

.contacto .sg2{align-items:start}
.dato{padding:18px 0;border-bottom:1px solid var(--borde)}
.dato b{display:block;font-size:.76rem;letter-spacing:.1em;text-transform:uppercase;color:var(--agua);margin-bottom:5px}
.dato p{color:var(--gris)}
.telg{font-family:var(--serif);font-size:clamp(1.5rem,3vw,2rem);color:var(--prof);display:inline-block;margin-top:4px}
.hor{list-style:none;font-size:.95rem}
.hor li{display:flex;justify-content:space-between;gap:16px;padding:9px 0;border-bottom:1px solid var(--borde);color:var(--gris)}
.hor li.hoy{color:var(--prof);font-weight:700}
.hor li.hoy::after{content:'hoy';font-size:.7rem;background:var(--coral-sua);color:var(--coral);
                   padding:2px 9px;border-radius:999px;margin-left:auto}

.fin{background:var(--tinta);color:#fff;text-align:center;padding:clamp(64px,8vw,104px) 24px}
.fin h2{color:#fff;max-width:18ch;margin:0 auto 14px}
.fin p{opacity:.74;margin-bottom:30px}
footer{background:var(--tinta);color:rgba(255,255,255,.52);padding:26px 0;font-size:.85rem;
       border-top:1px solid rgba(255,255,255,.1);text-align:center}

.reveal{opacity:0;transform:translateY(22px);transition:opacity .66s cubic-bezier(.16,1,.3,1) var(--d,0ms),transform .66s cubic-bezier(.16,1,.3,1) var(--d,0ms)}
.reveal.vis{opacity:1;transform:none}

@media(max-width:980px){
  .sg2{grid-template-columns:1fr}
  .serv-gr{grid-column:span 1}
  .abierto{display:none}
}
@media(max-width:600px){
  .hero{padding:124px 0 74px}
  .mancha{display:none}
  .btn{width:100%;justify-content:center}
  .social{width:100%}
}
@media (prefers-reduced-motion:reduce){
  *,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important}
  .reveal{opacity:1!important;transform:none!important}
  h1 .ln>span{transform:none!important}
  .lead,.hbtn,.social{opacity:1!important}
}
</style>
</head>
<body>

<header id="hd"><div class="w nav">
  <div class="marca">${esc(n.nombre)}</div>
  <div class="abierto" id="ab"><span class="pt"></span><span id="abt">Consultorio</span></div>
  ${wa ? `<a class="btn btn-wa" href="${wa}" target="_blank" rel="noopener" style="padding:12px 22px;min-height:44px">Agendar</a>` : ''}
</div></header>

<div class="hero">
  <div class="mancha m1" data-par="0.16"></div>
  <div class="mancha m2" data-par="-0.1"></div>
  <div class="w">
    <div class="ante">Odontología en ${esc(n.ciudad)}</div>
    <h1>${(t.titular || '').split(' ').reduce((ln, w, i, a) => {
        const porLinea = Math.ceil(a.length / 3);
        const idx = Math.floor(i / porLinea);
        ln[idx] = (ln[idx] ? ln[idx] + ' ' : '') + w;
        return ln;
      }, []).map((l, i) => `<span class="ln"><span style="--d:${i * 90 + 120}ms">${esc(l)}</span></span>`).join('')}</h1>
    <p class="lead">${esc(t.subtitulo)}</p>
    <div class="hbtn">
      ${wa ? `<a class="btn btn-wa" href="${wa}" target="_blank" rel="noopener">Escribir por WhatsApp</a>` : ''}
      ${n.telefono ? `<a class="btn btn-gho" href="tel:+57${tel}">Llamar ${esc(n.telefono)}</a>` : ''}
    </div>
    ${resenas > 0 ? `<div class="social">
      <span class="est">${'★'.repeat(Math.round(cal))}</span>
      <div><b>${esc(cal)}</b> <span>· ${esc(resenas)} reseñas reales en Google</span></div>
    </div>` : ''}
  </div>
</div>

<div class="cifras"><div class="w"><div class="cg">
  ${resenas > 0 ? `<div class="cif"><b class="num" data-n="${resenas}">0</b><span>pacientes nos calificaron</span></div>` : ''}
  ${cal > 0 ? `<div class="cif"><b>${esc(cal)}</b><span>de 5 estrellas</span></div>` : ''}
  ${servicios.length ? `<div class="cif"><b class="num" data-n="${servicios.length}">0</b><span>especialidades</span></div>` : ''}
  ${barrio ? `<div class="cif"><b style="font-size:clamp(1.1rem,2vw,1.5rem)">${esc(barrio.slice(0, 26))}</b><span>donde atendemos</span></div>` : ''}
</div></div></div>

${cards ? `<section id="serv"><div class="w">
  <div class="tit reveal"><div class="ante">Tratamientos</div><h2>Lo que hacemos por tu sonrisa</h2>
  <p>Cada tratamiento empieza con una valoración, sin compromiso.</p></div>
  <div class="sg">${cards}</div>
</div></section>` : ''}

<section class="sobre"><div class="w"><div class="sg2">
  <div class="reveal">
    <div class="ante">Quiénes somos</div>
    <h2 style="margin-bottom:18px">Atención que se siente distinta</h2>
    <p style="color:var(--gris)">${esc(t.sobre)}</p>
  </div>
  <div class="reveal" style="--d:140ms">
    <p class="cita">${resenas > 0
      ? `${esc(resenas)} pacientes nos calificaron con ${esc(cal)} estrellas en Google.`
      : `Atendemos en ${esc(n.ciudad)} con el cuidado que cada paciente merece.`}</p>
    <p style="color:var(--gris);margin-top:16px;font-size:.94rem">Esa es la única reseña que no se puede comprar.</p>
  </div>
</div></div></section>

<section class="contacto"><div class="w"><div class="sg2">
  <div class="reveal">
    <div class="ante">Dónde estamos</div>
    <h2 style="margin-bottom:22px">Ven a conocernos</h2>
    <div class="dato"><b>Dirección</b><p>${esc(n.direccion)}</p></div>
    ${n.telefono ? `<div class="dato"><b>Teléfono</b><a class="telg" href="tel:+57${tel}">${esc(n.telefono)}</a></div>` : ''}
    ${wa ? `<div style="margin-top:26px"><a class="btn btn-wa" href="${wa}" target="_blank" rel="noopener">Escribir por WhatsApp</a></div>` : ''}
  </div>
  ${dias.length ? `<div class="reveal" style="--d:120ms">
    <div class="ante" style="margin-bottom:16px">Horario</div>
    <ul class="hor" id="hl">${dias.map(d => {
      const [dia, ...r] = String(d).split(':');
      return `<li data-dia="${esc(dia)}"><span>${esc(dia)}</span><span>${esc(r.join(':').trim())}</span></li>`;
    }).join('')}</ul>
  </div>` : ''}
</div></div></section>

<div class="fin">
  <h2>¿Agendamos tu valoración?</h2>
  <p>Te respondemos hoy mismo.</p>
  ${wa ? `<a class="btn btn-wa" href="${wa}" target="_blank" rel="noopener">Escribir por WhatsApp</a>` : ''}
</div>

<footer><div class="w">${esc(n.nombre)} · ${esc(n.ciudad)}, Colombia${n.telefono ? ` · ${esc(n.telefono)}` : ''}</div></footer>

<script>
(function(){
  var rm = matchMedia('(prefers-reduced-motion: reduce)').matches;

  // barra
  var hd = document.getElementById('hd');
  function sc(){ hd.classList.toggle('on', scrollY > 40); }
  addEventListener('scroll', sc, {passive:true}); sc();

  // ¿abierto ahora? El detalle que hace que el dueño se reconozca.
  var H = ${JSON.stringify(tabla)};
  (function(){
    var ab = document.getElementById('ab'), t = document.getElementById('abt');
    if(!ab) return;
    var d = new Date(), dia = d.getDay(), min = d.getHours()*60 + d.getMinutes(), h = H[dia];
    if (h && min >= h.de && min < h.a) { t.textContent = 'Abierto ahora'; }
    else {
      ab.classList.add('cerrado');
      for (var i=1;i<=7;i++){ var sig = H[(dia+i)%7]; if (sig){
        var hh = String(Math.floor(sig.de/60)).padStart(2,'0'), mm = String(sig.de%60).padStart(2,'0');
        t.textContent = (i===1?'Abrimos mañana ':'Abrimos ') + hh+':'+mm; return; } }
      t.textContent = 'Cerrado ahora';
    }
  })();

  // marcar el día de hoy en el horario
  (function(){
    var DIAS=['domingo','lunes','martes','miércoles','jueves','viernes','sábado'], hoy=DIAS[new Date().getDay()];
    document.querySelectorAll('#hl li').forEach(function(li){
      if ((li.dataset.dia||'').toLowerCase().indexOf(hoy) > -1) li.classList.add('hoy');
    });
  })();

  // reveal al entrar en pantalla
  var io = new IntersectionObserver(function(es){
    es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('vis'); io.unobserve(e.target); } });
  }, {threshold:.12, rootMargin:'0px 0px -8%'});
  document.querySelectorAll('.reveal').forEach(function(el){ io.observe(el); });

  // las cifras cuentan desde cero
  var ioN = new IntersectionObserver(function(es){
    es.forEach(function(e){
      if(!e.isIntersecting) return; ioN.unobserve(e.target);
      var el=e.target, fin=+el.dataset.n, ini=performance.now(), dur=rm?0:1100;
      (function paso(t){ var k=Math.min(1,(t-ini)/(dur||1));
        el.textContent = Math.round(fin*(1-Math.pow(1-k,3)));
        if(k<1) requestAnimationFrame(paso); })(ini);
    });
  }, {threshold:.5});
  document.querySelectorAll('.num').forEach(function(el){ ioN.observe(el); });

  if (rm) return;

  // luz que sigue al cursor en los servicios (con rAF, sin ahogar el hilo)
  if (matchMedia('(hover: hover)').matches) {
    var pend = false, ev = null;
    document.querySelectorAll('.serv').forEach(function(c){
      c.addEventListener('pointermove', function(e){
        ev = {c:c, e:e};
        if (pend) return; pend = true;
        requestAnimationFrame(function(){
          pend = false; var r = ev.c.getBoundingClientRect();
          ev.c.style.setProperty('--mx', (ev.e.clientX-r.left)+'px');
          ev.c.style.setProperty('--my', (ev.e.clientY-r.top)+'px');
        });
      }, {passive:true});
    });
  }

  // parallax suave de las manchas
  if (innerWidth > 600) {
    var m = [].slice.call(document.querySelectorAll('[data-par]')), tick=false;
    addEventListener('scroll', function(){
      if(tick) return; tick=true;
      requestAnimationFrame(function(){ tick=false;
        m.forEach(function(el){ el.style.transform='translateY('+(scrollY*+el.dataset.par)+'px)'; });
      });
    }, {passive:true});
  }
})();
</script>
</body></html>`;
}

module.exports = { plantillaOdontologia, paletaDe, horarioJS };
