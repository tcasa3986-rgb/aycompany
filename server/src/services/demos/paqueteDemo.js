const fs = require('fs');
const path = require('path');

const VERSION = 'comercial-2026-10-18';
const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]));
const jsonSeguro = value => JSON.stringify(value).replace(/</g, '\\u003c');
const telefonoCO = value => {
    const digits = String(value || '').replace(/\D/g, '');
    if (/^3\d{9}$/.test(digits)) return `57${digits}`;
    if (/^573\d{9}$/.test(digits)) return digits;
    return null; // No inventar un WhatsApp móvil a partir de un teléfono fijo.
};
const alcanceInicial = [
    'Página de presentación adaptada al negocio y a dispositivos móviles.',
    'Secciones de atención, presentación, ubicación y contacto.',
    'Enlaces a WhatsApp, teléfono o canal confirmado por el negocio.',
    'Configuración básica de títulos y descripción para buscadores.',
    'Publicación después de validar contenido, fotografías y condiciones.',
];

function propuestaBase() {
    return { inicial: null, mensual: null, plazo: '', condiciones: '', alcance: [...alcanceInicial],
        aprobada: false, revisado: false, fotos: 'ilustrativas' };
}

function validarPropuesta(datos = {}) {
    if (!datos || typeof datos !== 'object' || Array.isArray(datos)) throw new Error('Propuesta inválida');
    const p = propuestaBase();
    for (const k of ['inicial', 'mensual']) {
        const v = datos[k];
        if (v == null || v === '') continue;
        if (typeof v !== 'number' || !Number.isFinite(v) || v < 0 || v > 999999999 || !Number.isInteger(v))
            throw new Error('Los valores deben ser pesos colombianos enteros, sin negativos');
        p[k] = v;
    }
    for (const k of ['plazo', 'condiciones']) {
        if (datos[k] != null && typeof datos[k] !== 'string') throw new Error('Condiciones inválidas');
        if (String(datos[k] || '').length > (k === 'plazo' ? 200 : 2000)) throw new Error('Texto demasiado largo');
        p[k] = String(datos[k] || '').trim();
    }
    if (datos.alcance !== undefined) {
        if (!Array.isArray(datos.alcance) || !datos.alcance.length || datos.alcance.length > 12 ||
            datos.alcance.some(v => typeof v !== 'string' || !v.trim() || v.length > 350)) throw new Error('Revisa los puntos del alcance');
        p.alcance = datos.alcance.map(v => v.trim());
    }
    p.revisado = datos.revisado === true;
    p.aprobada = datos.aprobada === true;
    if (p.aprobada && !p.revisado)
        throw new Error('Confirma la revisión de contenido antes de aprobar la propuesta');
    return p;
}

function leerPaquete(html = '') {
    const m = String(html).match(/<script id="demo-paquete" type="application\/json">([\s\S]*?)<\/script>/);
    if (!m) return null;
    try {
        const value = JSON.parse(m[1]);
        if (value.version !== VERSION) return null;
        return { version: VERSION, propuesta: validarPropuesta(value.propuesta) };
    } catch { return null; }
}

function contexto(n) {
    const nombre = String(n.nombre || 'Tu negocio');
    const rubro = `${n.rubro || ''} ${n.categoria || ''} ${nombre}`.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    const dental = /odontolog|dentista|ortodon|dental/.test(rubro);
    const ortodoncia = /ortodon/.test(nombre.toLowerCase());
    const estetica = /estetica/.test(nombre.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase());
    const ciudad = String(n.ciudad || 'Colombia');
    let marca = nombre.split(',')[0].split(/\s[-–]\s*/)[0]
        .replace(/cl[ií]nica\s+odontol[oó]gica|odontolog[ií]a|odontol[oó]gic[ao]s?/gi, '').replace(/\s+/g,' ').trim() || nombre;
    if (marca === marca.toUpperCase()) marca = marca.toLowerCase().replace(/(^|\s)(\p{L})/gu, (_,s,c)=>s+c.toUpperCase());
    if (marca.length > 42) marca = marca.slice(0, 39).trim()+'…';
    return { nombre, marca, ciudad, dental,
        titular: dental ? (ortodoncia ? 'Tu próxima sonrisa empieza con una conversación.' : estetica ? 'Una nueva mirada a tu sonrisa. A tu ritmo.' : 'Cuidar tu sonrisa empieza por sentirte en confianza.') : 'Conoce el negocio. Encuentra lo que necesitas.',
        rubro: dental ? 'Odontología' : (n.categoria || n.rubro || 'Atención'),
        subtitulo: dental ? `Conoce ${nombre}, encuentra cómo llegar y consulta por tu atención odontológica en ${ciudad}.` : `Conoce ${nombre} en ${ciudad}. Consulta sus servicios y encuentra cómo contactar al negocio.`,
        cta: dental ? 'Consultar una valoración' : 'Consultar al negocio',
    };
}

function construirDemo(n, propuesta = propuestaBase(), opciones = {}) {
    const p = validarPropuesta(propuesta);
    const c = contexto(n);
    const slug = encodeURIComponent(n.demo_slug || 'muestra-local');
    const mobile = telefonoCO(n.telefono);
    const texto = `Hola, estoy viendo la propuesta de página para ${c.nombre}. Me gustaría consultar por una cita o atención.`;
    const contacto = mobile ? `https://wa.me/${mobile}?text=${encodeURIComponent(texto)}` : null;
    const replacements = {
        DISENO: ['natural','editorial','calida'][[...c.nombre].reduce((sum,v)=>sum+v.charCodeAt(0),0)%3],
        NOMBRE: esc(c.nombre), CIUDAD: esc(c.ciudad), TITULAR: esc(c.titular),
        SUBTITULO: esc(c.subtitulo), RUBRO: esc(c.rubro), CTA: esc(c.cta),
        DIRECCION: esc(n.direccion || 'Dirección por confirmar con el negocio'),
        MARCA: esc(c.marca), INICIAL: esc(c.marca[0].toUpperCase()),
        CLIENTE: c.dental ? 'paciente' : 'cliente',
        PRIMERA_CONSULTA: c.dental ? 'Es mi primera valoración' : 'Quiero conocer sus servicios',
        PROPUESTA: `/propuesta/${slug}`, METADATOS: jsonSeguro({ version: VERSION, propuesta: p }),
        CONTACTO: contacto ? `<a class="button" href="${esc(contacto)}" target="_blank" rel="noopener noreferrer">Abrir WhatsApp del negocio ↗</a>` : '<p class="result">El canal de atención debe ser confirmado por el negocio.</p>',
        HERO_FOTO: c.dental ? '<figure class="photo hero-photo"><img src="/demo-assets/consultorio-ilustrativo.webp" width="1200" height="800" fetchpriority="high" alt="Consultorio ficticio generado con IA, no representa la sede real"><div class="image-card"><span class="eyebrow">Tu primera visita</span><p>Un espacio para tus preguntas.</p></div><figcaption class="photo-label">Referencia con IA · No es la sede real</figcaption></figure>' : '<div class="location-panel"><p class="eyebrow">Una presencia propia</p><h2>Tu negocio,<br>más cerca.</h2><p>Presentación propuesta para '+esc(c.nombre)+'. La galería se completará con fotografías autorizadas.</p></div>',
        FOTO_SECUNDARIA: c.dental ? '<figure class="photo"><img src="/demo-assets/consulta-ilustrativa.webp" width="1000" height="667" loading="lazy" alt="Profesional y paciente ficticios creados con IA; no son el equipo del negocio"><figcaption class="photo-label">Escena con IA · Personas ficticias</figcaption></figure>' : '<div class="location-panel"><p class="eyebrow">'+esc(c.ciudad)+'</p><h2>'+esc(c.nombre)+'</h2><p>Contenido y fotografías finales pendientes de aprobación del negocio.</p></div>',
        MAPA: `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(c.nombre+' '+c.ciudad+' '+(n.direccion || ''))}`,
    };
    const template = fs.readFileSync(path.join(__dirname, 'plantillaComercial.html'), 'utf8');
    return template.replace(/\{\{([A-Z_]+)\}\}/g, (_, key) => replacements[key] ?? '');
}

const dinero = v => v === null ? 'Por acordar' : '$ '+v.toLocaleString('es-CO')+' COP';
function construirPropuesta(n, propuesta = propuestaBase()) {
    const p = validarPropuesta(propuesta);
    const c = contexto(n);
    const contactoAgencia = `https://wa.me/573224807767?text=${encodeURIComponent(`Hola Cristian, vi la demo y la propuesta de página para ${c.nombre}. Quisiera conversar sobre el proyecto.`)}`;
    return `<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive"><title>Propuesta para ${esc(c.nombre)} · AI Company</title><style>
    *{box-sizing:border-box}body{margin:0;background:#f6f5fa;color:#182336;font:15px/1.65 system-ui,sans-serif}.wrap{max-width:1040px;margin:auto;padding:30px 28px}a{color:inherit}header{display:flex;justify-content:space-between;gap:20px;align-items:center;border-bottom:1px solid #ddd5e8;padding-bottom:23px}.brand{font-size:22px;font-weight:800;letter-spacing:-1px}.brand span{color:#7b23c9}.tag{font-size:11px;color:#715b85;text-transform:uppercase;letter-spacing:2px}.hero{padding:55px 0 35px}h1{font-size:clamp(32px,5vw,54px);letter-spacing:-2px;line-height:1.12;max-width:850px;margin:15px 0 23px}h2{font-size:22px;letter-spacing:-.5px}p{max-width:720px;color:#59677b}.grid{display:grid;grid-template-columns:1.15fr 1fr;gap:22px}.card{background:#fff;border:1px solid #e2dbea;border-radius:16px;padding:28px}.prices{display:grid;grid-template-columns:1fr 1fr;gap:15px}.price{background:#f6f2fa;border-radius:10px;padding:18px}.price strong{display:block;font-size:23px;margin:8px 0;line-height:1.25}.price small{font-size:11px;color:#6a587b}.btn{display:inline-block;padding:13px 19px;background:#7427bf;color:white;text-decoration:none;border-radius:8px;font-weight:600;font-size:13px;border:0;cursor:pointer}.secondary{background:#ece6f4;color:#582486}.actions{display:flex;gap:12px;flex-wrap:wrap;margin:28px 0}li{padding:7px 0;color:#46566b}ul{padding-left:20px}.note{font-size:12px;color:#766787;white-space:pre-wrap}.footer{border-top:1px solid #ded6e9;font-size:11px;margin-top:30px;padding-top:20px;color:#737e90}.label{font-size:12px;color:#7427bf}.conditions{white-space:pre-wrap}.card .btn{margin-top:18px}a:focus-visible,button:focus-visible{outline:3px solid #9255d9;outline-offset:4px}@media(max-width:720px){.grid,.prices{grid-template-columns:1fr}.wrap{padding:22px 20px}.card{padding:23px}.hero{padding-top:35px}header{align-items:flex-start}h1{letter-spacing:-1px}}@media print{body{background:white}.wrap{max-width:none;padding:0}.actions,.card .btn{display:none}.card{break-inside:avoid}.hero{padding:25px 0}.grid{grid-template-columns:1fr 1fr}.btn{display:none}}
    </style></head><body><div class="wrap"><header><div class="brand"><span>AI</span> Company</div><a href="/demo/${encodeURIComponent(n.demo_slug)}">Ver la demostración ↗</a></header><main><div class="hero"><div class="tag">Una propuesta para ${esc(c.ciudad)}</div><h1>${esc(c.nombre)},<br>con una presencia propia.</h1><p>Preparamos esta muestra sin ningún compromiso. Si te gusta, podemos implementarla con una opción económica, de forma ágil y con una estructura técnica pensada para posicionamiento.</p><span class="label">${p.aprobada ? 'Propuesta comercial revisada por AI Company' : 'Propuesta sin compromiso · alcance por confirmar contigo'}</span></div><div class="grid"><section class="card"><h2>Lo que proponemos construir</h2><ul>${p.alcance.map(v=>`<li>${esc(v)}</li>`).join('')}</ul><p class="note">El contenido definitivo y las fotografías se aprueban contigo. La demostración no es el sitio oficial del negocio.</p></section><section class="card"><h2>Una opción económica, a tu medida</h2><p>Conversemos sobre lo que necesita tu negocio. Confirmamos el valor antes de empezar, sin cobros ni compromisos por ver esta propuesta.</p>${p.inicial === null && p.mensual === null ? '' : `<div class="prices"><div class="price">Creación de la página<strong>${dinero(p.inicial)}</strong><small>Pago inicial</small></div><div class="price">Mantenimiento<strong>${p.mensual === 0 ? 'Sin mensualidad' : dinero(p.mensual)}</strong><small>${p.mensual === 0 ? 'No contratado en esta propuesta' : 'Valor mensual; alcance según condiciones'}</small></div></div>`}<p><strong>Implementación ágil:</strong> ${esc(p.plazo || 'Acordamos la fecha contigo después de validar el alcance y recibir el material.')}</p><p class="conditions">${esc(p.condiciones || 'La muestra se preparó sin compromiso. Precio, forma de pago y condiciones se acuerdan antes de contratar.')}</p><a class="btn" href="${esc(contactoAgencia)}" target="_blank" rel="noopener noreferrer">Conversar con Cristian sobre la propuesta ↗</a></section></div><section class="card" style="margin-top:22px"><h2>De la idea a tu página</h2><p><strong>1. Revisamos la muestra.</strong> Ajustamos enfoque, contenido y fotos.<br><strong>2. Acordamos el proyecto.</strong> Confirmamos alcance, precio y forma de pago.<br><strong>3. Validamos y publicamos.</strong> Revisamos enlaces, dispositivos y datos contigo.</p><p class="note">No incluye automáticamente dominio, alojamiento, publicidad, tienda, reservas, pagos en línea, asistente de IA ni integraciones. Sólo se incluyen si se detallan expresamente en el alcance y las condiciones. No se garantizan ventas ni posiciones en Google.</p></section><div class="actions"><a class="btn secondary" href="/demo/${encodeURIComponent(n.demo_slug)}">Volver a ver la demo</a><button class="btn secondary" onclick="window.print()">Guardar propuesta como PDF</button></div></main><footer class="footer">AI Company · Propuesta preparada para ${esc(c.nombre)}. La aceptación, fechas y obligaciones se formalizan por separado. Imágenes de la demo: referencias ilustrativas con IA, no fotografías de la sede ni de su equipo.</footer></div></body></html>`;
}

module.exports = { VERSION, esc, telefonoCO, contexto, validarPropuesta, propuestaBase, leerPaquete, construirDemo, construirPropuesta };
