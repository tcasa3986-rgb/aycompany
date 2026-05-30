/**
 * Buscador de negocios via Google Places API (sin Playwright).
 * Usa $0 del crédito gratuito de $200/mes de Google Cloud.
 * ~240 búsquedas/mes ≈ $7.68 — cubierto completamente por el crédito gratuito.
 */

const https = require('https');

const PLACES_KEY = process.env.GOOGLE_PLACES_API_KEY;

// Places API (New) — más datos, misma key, crédito gratuito de $200/mes
async function textSearch(query, maxResults = 10) {
    if (!PLACES_KEY) throw new Error('GOOGLE_PLACES_API_KEY no configurado');

    const body = JSON.stringify({
        textQuery:      query,
        languageCode:   'es',
        regionCode:     'CO',
        maxResultCount: maxResults,
    });

    const fields = 'places.displayName,places.formattedAddress,places.nationalPhoneNumber,places.websiteUri,places.rating,places.id';

    return new Promise((resolve, reject) => {
        const req = https.request({
            hostname: 'places.googleapis.com',
            path:     '/v1/places:searchText',
            method:   'POST',
            headers: {
                'Content-Type':     'application/json',
                'X-Goog-Api-Key':   PLACES_KEY,
                'X-Goog-FieldMask': fields,
                'Content-Length':   Buffer.byteLength(body),
            },
        }, res => {
            let d = '';
            res.on('data', c => d += c);
            res.on('end', () => {
                try {
                    const json = JSON.parse(d);
                    if (json.error) reject(new Error(`Places API: ${json.error.message}`));
                    else resolve(json.places || []);
                } catch (e) { reject(e); }
            });
        });
        req.on('error', reject);
        req.write(body);
        req.end();
    });
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
    console.log(`🔍 [Places API New] "${categoria}" en ${ciudad}...`);

    if (!PLACES_KEY) {
        console.error('⚠️  GOOGLE_PLACES_API_KEY no configurado — omitiendo prospección');
        return [];
    }

    try {
        const query   = `${categoria} en ${ciudad} Colombia`;
        const places  = await textSearch(query, maxResultados);

        const negocios = [];
        for (const place of places) {
            try {
                const telefono = place.nationalPhoneNumber?.replace(/[\s()-]/g, '') || null;
                const sitioUrl = place.websiteUri || null;
                const emails   = await extraerEmailsWeb(sitioUrl);

                negocios.push({
                    nombre:    place.displayName?.text || 'Sin nombre',
                    ciudad,
                    tipo:      categoria,
                    sitioUrl,
                    telefono,
                    rating:    place.rating || null,
                    direccion: place.formattedAddress || null,
                    tieneMaps: true,
                    web: {
                        emails,
                        url:      sitioUrl,
                        tieneWA:  false,
                        tieneFB:  false,
                        tieneIG:  false,
                    }
                });

                console.log(`   ✓ ${place.displayName?.text} | Tel: ${telefono || 'no'} | Web: ${sitioUrl || 'no'} | Emails: ${emails.length}`);
            } catch (e) {
                console.error(`   ✗ ${place.displayName?.text}: ${e.message}`);
            }
        }

        console.log(`[Places API] ✓ ${negocios.length} negocios para "${categoria}" en ${ciudad}`);
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
