const axios = require('axios');
const { Lead } = require('../models');
const { Op } = require('sequelize');

// ── GOOGLE PLACES ─────────────────────────────────────────────────────────────
async function buscarEnGooglePlaces({ categoria, ciudad, pais = 'Colombia', maxResultados = 20 }) {
    const apiKey = process.env.GOOGLE_PLACES_API_KEY;
    if (!apiKey) throw new Error('GOOGLE_PLACES_API_KEY no configurada');

    const query = encodeURIComponent(`${categoria} en ${ciudad}, ${pais}`);
    const url = `https://maps.googleapis.com/maps/api/place/textsearch/json?query=${query}&language=es&key=${apiKey}`;

    const leads = [];
    let nextPageToken = null;
    let paginas = 0;

    do {
        const urlPagina = nextPageToken
            ? `https://maps.googleapis.com/maps/api/place/textsearch/json?pagetoken=${nextPageToken}&key=${apiKey}`
            : url;

        const { data } = await axios.get(urlPagina);
        if (data.status !== 'OK' && data.status !== 'ZERO_RESULTS') {
            throw new Error(`Google Places error: ${data.status} — ${data.error_message || ''}`);
        }

        for (const lugar of (data.results || [])) {
            if (leads.length >= maxResultados) break;

            // Obtener detalles para conseguir teléfono y web
            let telefono = null;
            let website  = null;
            try {
                const det = await axios.get(
                    `https://maps.googleapis.com/maps/api/place/details/json?place_id=${lugar.place_id}&fields=formatted_phone_number,website&key=${apiKey}`
                );
                telefono = det.data.result?.formatted_phone_number || null;
                website  = det.data.result?.website || null;
            } catch { /* detalles opcionales */ }

            leads.push({
                nombre:   lugar.name,
                empresa:  lugar.name,
                telefono: telefono ? telefono.replace(/\s/g, '') : null,
                email:    null,
                fuente:   'google_places',
                notas:    `${categoria} en ${ciudad}. Dirección: ${lugar.formatted_address || ''}${website ? '. Web: ' + website : ''}`,
            });
        }

        nextPageToken = data.next_page_token || null;
        paginas++;
        if (nextPageToken && leads.length < maxResultados) await new Promise(r => setTimeout(r, 2000));
    } while (nextPageToken && leads.length < maxResultados && paginas < 3);

    return leads;
}

// ── APOLLO.IO ─────────────────────────────────────────────────────────────────
async function buscarEnApollo({ industria, ciudad, pais = 'CO', maxResultados = 20 }) {
    const apiKey = process.env.APOLLO_API_KEY;
    if (!apiKey) throw new Error('APOLLO_API_KEY no configurada');

    const { data } = await axios.post(
        'https://api.apollo.io/api/v1/mixed_companies/search',
        {
            api_key: apiKey,
            q_organization_locations: [`${ciudad}, Colombia`],
            organization_num_employees_ranges: ['1,200'],
            page: 1,
            per_page: Math.min(maxResultados, 25),
        },
        { headers: { 'Content-Type': 'application/json', 'Cache-Control': 'no-cache' } }
    );

    return (data.organizations || []).map(org => ({
        nombre:   org.primary_domain ? `Contacto de ${org.name}` : org.name,
        empresa:  org.name,
        telefono: org.primary_phone?.number || null,
        email:    org.contact_emails?.[0] || null,
        fuente:   'apollo',
        notas:    `Industria: ${org.industry || industria}. Empleados: ${org.num_employees || '?'}. Web: ${org.website_url || ''}`,
    }));
}

// ── GUARDADO CON DEDUPLICACIÓN ────────────────────────────────────────────────
async function guardarLeads(leadsNuevos) {
    let guardados = 0;
    let duplicados = 0;

    for (const lead of leadsNuevos) {
        if (!lead.nombre) continue;

        // Deduplicar por teléfono o nombre+empresa
        const donde = lead.telefono
            ? { telefono: lead.telefono.replace(/\D/g, '') }
            : { nombre: lead.nombre, empresa: lead.empresa };

        const existe = await Lead.findOne({ where: donde });
        if (existe) { duplicados++; continue; }

        await Lead.create({
            nombre:   lead.nombre,
            empresa:  lead.empresa,
            telefono: lead.telefono ? lead.telefono.replace(/\D/g, '') : null,
            email:    lead.email,
            fuente:   lead.fuente,
            notas:    lead.notas,
            estado:   'nuevo',
            agente_activo: true,
        });
        guardados++;
    }

    return { guardados, duplicados };
}

// ── BÚSQUEDA COMBINADA ────────────────────────────────────────────────────────
async function prospectar({ categorias, ciudades, fuentes = ['google_places'], maxPorBusqueda = 20 }) {
    const resultados = { total: 0, guardados: 0, duplicados: 0, errores: [], detalle: [] };

    for (const ciudad of ciudades) {
        for (const categoria of categorias) {
            for (const fuente of fuentes) {
                try {
                    let leads = [];
                    if (fuente === 'google_places') {
                        leads = await buscarEnGooglePlaces({ categoria, ciudad, maxResultados: maxPorBusqueda });
                    } else if (fuente === 'apollo') {
                        leads = await buscarEnApollo({ industria: categoria, ciudad, maxResultados: maxPorBusqueda });
                    }

                    const { guardados, duplicados } = await guardarLeads(leads);
                    resultados.total     += leads.length;
                    resultados.guardados += guardados;
                    resultados.duplicados += duplicados;
                    resultados.detalle.push({ fuente, ciudad, categoria, encontrados: leads.length, guardados });

                    await new Promise(r => setTimeout(r, 1500)); // pausa entre llamadas
                } catch (e) {
                    resultados.errores.push(`[${fuente}] ${categoria} en ${ciudad}: ${e.message}`);
                }
            }
        }
    }

    return resultados;
}

module.exports = { prospectar, buscarEnGooglePlaces, buscarEnApollo, guardarLeads };
