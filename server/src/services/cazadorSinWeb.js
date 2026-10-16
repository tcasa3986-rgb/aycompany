/**
 * Caza negocios SIN página web, y decide a cuáles vale la pena hacerles una.
 *
 * Google Maps no dice "quizás no tiene web": lo dice con certeza, en un campo
 * (`websiteUri`). Esa lista es el negocio.
 *
 * Pero no todos sirven. El que tiene 180 reseñas y 4.6 estrellas sin página
 * está perdiendo clientes todos los días y ya lo intuye. El que tiene 4 reseñas
 * no tiene con qué pagar. Por eso cada negocio se puntúa ANTES de gastarle un
 * solo token en generarle la demo: veinte bien escogidas cierran más que
 * quinientas genéricas, y cuestan mucho menos.
 */
const https = require('https');

const PLACES_KEY = () => process.env.GOOGLE_PLACES_API_KEY;

// Rubros donde una web se paga sola con uno o dos clientes. Un cliente de
// odontología vale cientos de miles; uno de una tienda de barrio, no.
const RUBROS_TICKET_ALTO = [
    'odontolog', 'dentista', 'ortodon', 'estétic', 'estetic', 'clínic', 'clinic',
    'abogad', 'jurídic', 'juridic', 'contador', 'notaría', 'notaria',
    'veterinar', 'médic', 'medic', 'psicólog', 'psicolog', 'fisioterap',
    'constructor', 'arquitect', 'ingenier', 'inmobiliar', 'finca raíz',
    'hotel', 'hostal', 'spa', 'gimnasio',
    'taller', 'automotriz', 'mecánic', 'mecanic', 'cerrajer',
    'laboratorio', 'óptic', 'optic', 'centro médico',
];

/**
 * El puntaje que decide a quién hacerle la demo. De 0 a 100.
 *
 * Pesa la PRUEBA de que el negocio funciona (reseñas) por encima de todo: es lo
 * único de esta lista que no se puede fingir y que dice que hay plata entrando.
 */
function puntuar(n) {
    let score = 0;
    const razones = [];

    // 1. Reseñas: la señal más fuerte de que el negocio está vivo y vende.
    const r = Number(n.reseñas || 0);
    if (r >= 200)      { score += 40; razones.push(`${r} reseñas`); }
    else if (r >= 100) { score += 32; razones.push(`${r} reseñas`); }
    else if (r >= 50)  { score += 24; razones.push(`${r} reseñas`); }
    else if (r >= 20)  { score += 14; razones.push(`${r} reseñas`); }
    else if (r >= 10)  { score += 6; }
    else               { razones.push(`solo ${r} reseñas`); }

    // 2. Calificación: un negocio bien calificado tiene reputación que cuidar,
    //    y una web es la forma natural de capitalizarla.
    const cal = Number(n.calificacion || 0);
    if (cal >= 4.5)      { score += 20; razones.push(`${cal}★`); }
    else if (cal >= 4.0) { score += 14; razones.push(`${cal}★`); }
    else if (cal >= 3.5) { score += 6; }
    else if (cal > 0)    { razones.push(`${cal}★ bajo`); }

    // 3. Rubro: ¿el ticket aguanta una web?
    const texto = `${n.nombre} ${n.categoria || ''} ${n.consulta || ''}`.toLowerCase();
    if (RUBROS_TICKET_ALTO.some(k => texto.includes(k))) {
        score += 20; razones.push('rubro de ticket alto');
    }

    // 4. Teléfono: sin forma de contactarlo, la mejor demo del mundo no sirve.
    if (n.telefono) score += 15;
    else razones.push('SIN teléfono');

    // 5. Ya tiene algo en Facebook/Instagram como "web": le importa estar en
    //    internet, solo que lo resolvió a medias. Es el mejor cliente posible.
    if (n.web_parcial) { score += 5; razones.push('usa redes como web'); }

    return { score: Math.min(100, score), razones };
}

/** Una búsqueda en Google Places. */
function buscar(consulta, max = 20) {
    const key = PLACES_KEY();
    if (!key) return Promise.reject(new Error('Falta GOOGLE_PLACES_API_KEY'));

    const body = JSON.stringify({
        textQuery: consulta, languageCode: 'es', regionCode: 'CO',
        maxResultCount: Math.min(20, max),
    });
    const fields = [
        'places.id', 'places.displayName', 'places.formattedAddress',
        'places.nationalPhoneNumber', 'places.websiteUri',
        'places.rating', 'places.userRatingCount',
        'places.primaryTypeDisplayName', 'places.businessStatus',
        'places.googleMapsUri',
    ].join(',');

    return new Promise((resolve, reject) => {
        const req = https.request({
            hostname: 'places.googleapis.com', path: '/v1/places:searchText', method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Goog-Api-Key': key,
                'X-Goog-FieldMask': fields,
                'Content-Length': Buffer.byteLength(body),
            },
        }, res => {
            let d = '';
            res.on('data', c => d += c);
            res.on('end', () => {
                try {
                    const j = JSON.parse(d);
                    if (j.error) return reject(new Error(`Places: ${j.error.message}`));
                    resolve(j.places || []);
                } catch (e) { reject(e); }
            });
        });
        req.on('error', reject);
        req.write(body); req.end();
    });
}

/** ¿Lo que Google llama "web" es una web de verdad? */
function clasificarWeb(url) {
    if (!url) return { tiene: false, parcial: false };
    const u = String(url).toLowerCase();
    // Una página de Facebook o un linktree NO es un sitio web: es justamente el
    // cliente ideal — le importa estar en internet pero lo resolvió a medias.
    const esRed = /facebook\.com|instagram\.com|linktr\.ee|wa\.me|api\.whatsapp|business\.site|negocio\.site/.test(u);
    return { tiene: !esRed, parcial: esRed };
}

/**
 * Busca negocios sin web en una ciudad y rubro, ya puntuados y ordenados.
 * @returns {Promise<{candidatos: object[], descartados: number, total: number}>}
 */
async function cazar({ rubro, ciudad, minimo = 40, max = 20 }) {
    const consulta = `${rubro} en ${ciudad}, Colombia`;
    const lugares = await buscar(consulta, max);

    const candidatos = [];
    let conWeb = 0;

    for (const p of lugares) {
        // Los cerrados no compran nada.
        if (p.businessStatus && p.businessStatus !== 'OPERATIONAL') continue;

        const web = clasificarWeb(p.websiteUri);
        if (web.tiene) { conWeb++; continue; }

        const n = {
            place_id:    p.id,
            nombre:      p.displayName?.text || '(sin nombre)',
            direccion:   p.formattedAddress || '',
            telefono:    p.nationalPhoneNumber || null,
            calificacion: p.rating || 0,
            reseñas:     p.userRatingCount || 0,
            categoria:   p.primaryTypeDisplayName?.text || '',
            maps:        p.googleMapsUri || '',
            web_parcial: web.parcial,
            red_social:  web.parcial ? p.websiteUri : null,
            ciudad, rubro, consulta,
        };
        const { score, razones } = puntuar(n);
        candidatos.push({ ...n, score, razones });
    }

    candidatos.sort((a, b) => b.score - a.score);
    return {
        total: lugares.length,
        con_web: conWeb,
        sin_web: candidatos.length,
        candidatos: candidatos.filter(c => c.score >= minimo),
        descartados: candidatos.filter(c => c.score < minimo).length,
    };
}

module.exports = { cazar, puntuar, clasificarWeb, buscar };
