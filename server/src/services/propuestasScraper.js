/**
 * Buscador de negocios via Google Places API (sin Playwright).
 * Usa $0 del crédito gratuito de $200/mes de Google Cloud.
 * ~240 búsquedas/mes ≈ $7.68 — cubierto completamente por el crédito gratuito.
 */

const https = require('https');

const PLACES_KEY = process.env.GOOGLE_PLACES_API_KEY;

function fetchJson(url) {
    return new Promise((resolve, reject) => {
        https.get(url, res => {
            let d = '';
            res.on('data', c => d += c);
            res.on('end', () => {
                try { resolve(JSON.parse(d)); }
                catch (e) { reject(new Error('JSON parse: ' + d.slice(0, 100))); }
            });
        }).on('error', reject);
    });
}

// Buscar negocios por texto usando Places Text Search
async function textSearch(query) {
    if (!PLACES_KEY) throw new Error('GOOGLE_PLACES_API_KEY no configurado');
    const url = `https://maps.googleapis.com/maps/api/place/textsearch/json?query=${encodeURIComponent(query)}&language=es&region=co&key=${PLACES_KEY}`;
    const data = await fetchJson(url);
    if (data.status !== 'OK' && data.status !== 'ZERO_RESULTS') {
        throw new Error(`Places API: ${data.status} — ${data.error_message || ''}`);
    }
    return data.results || [];
}

// Obtener detalles de un place (teléfono, web, emails)
async function placeDetails(placeId) {
    if (!PLACES_KEY) return null;
    const fields = 'name,formatted_phone_number,website,rating,formatted_address,url';
    const url = `https://maps.googleapis.com/maps/api/place/details/json?place_id=${placeId}&fields=${fields}&language=es&key=${PLACES_KEY}`;
    const data = await fetchJson(url);
    if (data.status !== 'OK') return null;
    return data.result;
}

// Extraer emails del sitio web con fetch simple (sin navegador)
async function extraerEmailsWeb(siteUrl) {
    if (!siteUrl) return [];
    try {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 8000);
        const res = await fetch(siteUrl, {
            signal: controller.signal,
            headers: { 'User-Agent': 'Mozilla/5.0 (compatible; LeadBot/1.0)' }
        });
        clearTimeout(timeout);
        if (!res.ok) return [];
        const html = await res.text();
        const emailRx = /[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g;
        const emails = [...new Set([...html.matchAll(emailRx)].map(m => m[0].toLowerCase()))]
            .filter(e => !e.includes('example') && !e.includes('sentry') && !e.includes('pixel'))
            .slice(0, 3);
        return emails;
    } catch {
        return [];
    }
}

// ── Búsqueda automática por categoría ────────────────────────────────────────

async function buscarNegociosCategoria({ categoria, ciudad, maxResultados = 8 }) {
    console.log(`🔍 [Places API] "${categoria}" en ${ciudad}...`);

    if (!PLACES_KEY) {
        console.error('⚠️  GOOGLE_PLACES_API_KEY no configurado — omitiendo prospección');
        return [];
    }

    try {
        const query   = `${categoria} en ${ciudad} Colombia`;
        const results = await textSearch(query);
        const slice   = results.slice(0, maxResultados);

        const negocios = [];
        for (const place of slice) {
            try {
                const det = await placeDetails(place.place_id);

                const telefono  = det?.formatted_phone_number?.replace(/\s|-/g, '') || null;
                const sitioUrl  = det?.website || null;
                const emails    = await extraerEmailsWeb(sitioUrl);

                negocios.push({
                    nombre:    place.name,
                    ciudad,
                    tipo:      categoria,
                    sitioUrl,
                    telefono,
                    rating:    place.rating || null,
                    direccion: place.formatted_address || det?.formatted_address || null,
                    tieneMaps: true,
                    web: {
                        emails,
                        url: sitioUrl,
                        tieneWA:   false,
                        tieneFB:   false,
                        tieneIG:   false,
                    }
                });

                console.log(`   ✓ ${place.name} | Tel: ${telefono || 'no'} | Web: ${sitioUrl || 'no'} | Emails: ${emails.length}`);
            } catch (e) {
                console.error(`   ✗ ${place.name}: ${e.message}`);
            }
        }

        console.log(`[Places API] ✓ ${negocios.length} negocios encontrados para "${categoria}" en ${ciudad}`);
        return negocios;
    } catch (e) {
        console.error('[Places API] Error:', e.message);
        return [];
    }
}

// Investigar negocio específico (compatible con el uso anterior)
async function investigarNegocio({ nombre, ciudad, tipo }) {
    const resultados = await buscarNegociosCategoria({ categoria: `${nombre} ${tipo || ''}`.trim(), ciudad, maxResultados: 1 });
    return resultados[0] || { nombre, ciudad, tipo, telefono: null, web: { emails: [] } };
}

module.exports = { investigarNegocio, buscarNegociosCategoria };
