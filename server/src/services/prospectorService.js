const axios = require('axios');
const { Lead } = require('../models');
const { Op } = require('sequelize');

// ── GOOGLE PLACES (New API) ───────────────────────────────────────────────────
async function buscarEnGooglePlaces({ categoria, ciudad, pais = 'Colombia', maxResultados = 20 }) {
    const apiKey = process.env.GOOGLE_PLACES_API_KEY;
    if (!apiKey) throw new Error('GOOGLE_PLACES_API_KEY no configurada');

    const leads = [];
    let pageToken = null;
    let paginas = 0;

    do {
        const body = {
            textQuery: `${categoria} en ${ciudad}, ${pais}`,
            languageCode: 'es',
            maxResultCount: Math.min(maxResultados - leads.length, 20),
        };
        if (pageToken) body.pageToken = pageToken;

        const { data } = await axios.post(
            'https://places.googleapis.com/v1/places:searchText',
            body,
            {
                headers: {
                    'X-Goog-Api-Key': apiKey,
                    'X-Goog-FieldMask': 'places.id,places.displayName,places.formattedAddress,places.nationalPhoneNumber,places.websiteUri,nextPageToken',
                    'Content-Type': 'application/json',
                },
            }
        );

        for (const lugar of (data.places || [])) {
            if (leads.length >= maxResultados) break;
            const nombre   = lugar.displayName?.text || 'Sin nombre';
            const telefono = lugar.nationalPhoneNumber || null;
            const website  = lugar.websiteUri || null;
            leads.push({
                nombre,
                empresa:  nombre,
                telefono: telefono ? telefono.replace(/\s/g, '') : null,
                email:    null,
                fuente:   'google_places',
                notas:    `${categoria} en ${ciudad}. Dirección: ${lugar.formattedAddress || ''}${website ? '. Web: ' + website : ''}`,
            });
        }

        pageToken = data.nextPageToken || null;
        paginas++;
        if (pageToken && leads.length < maxResultados) await new Promise(r => setTimeout(r, 2000));
    } while (pageToken && leads.length < maxResultados && paginas < 3);

    return leads;
}

// ── APOLLO.IO ─────────────────────────────────────────────────────────────────
async function buscarEnApollo({ industria, ciudad, pais = 'CO', maxResultados = 20 }) {
    const apiKey = process.env.APOLLO_API_KEY;
    if (!apiKey) throw new Error('APOLLO_API_KEY no configurada');

    const { data } = await axios.post(
        'https://api.apollo.io/api/v1/mixed_people/search',
        {
            q_organization_locations: [`${ciudad}, Colombia`],
            q_keywords: industria,
            page: 1,
            per_page: Math.min(maxResultados, 25),
        },
        {
            headers: {
                'Content-Type': 'application/json',
                'Cache-Control': 'no-cache',
                'Authorization': `Bearer ${apiKey}`,
            }
        }
    );

    return (data.people || []).map(p => ({
        nombre:   p.name || p.first_name + ' ' + p.last_name,
        empresa:  p.organization?.name || p.organization_name || '',
        telefono: p.phone_numbers?.[0]?.sanitized_number || null,
        email:    p.email || null,
        fuente:   'apollo',
        notas:    `Cargo: ${p.title || '?'}. Industria: ${p.organization?.industry || industria}. Web: ${p.organization?.website_url || ''}`,
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
