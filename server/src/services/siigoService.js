const fetch = require('node-fetch');

const BASE = 'https://api.siigo.com';

async function getToken() {
    const user     = process.env.SIIGO_USER;
    const key      = process.env.SIIGO_ACCESS_KEY;
    const partner  = process.env.SIIGO_PARTNER_ID;

    if (!user || !key) return null;

    const r = await fetch(`${BASE}/connect/token`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Partner-Id': partner || ''
        },
        body: new URLSearchParams({
            username:   user,
            password:   key,
            grant_type: 'password'
        })
    });

    if (!r.ok) {
        const err = await r.text();
        throw new Error(`SIIGO auth error: ${err}`);
    }

    const data = await r.json();
    return data.access_token;
}

async function buscarOCrearCliente(token, { nombre, email, telefono, nit }) {
    const headers = {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Partner-Id': process.env.SIIGO_PARTNER_ID || ''
    };

    // Buscar cliente por identificación
    if (nit) {
        const r = await fetch(`${BASE}/v1/customers?identification=${nit}`, { headers });
        if (r.ok) {
            const data = await r.json();
            if (data.results?.length > 0) return data.results[0].id;
        }
    }

    // Crear cliente si no existe
    const nombreParts = (nombre || 'Cliente').split(' ');
    const body = {
        type: 'Customer',
        person_type: 'Person',
        id_type: { code: nit ? '13' : '22' }, // 13=CC, 22=Sin identificación
        identification: nit || `TEMP${Date.now()}`,
        name: [nombreParts[0] || 'Cliente', nombreParts.slice(1).join(' ') || ''],
        commercial_name: nombre,
        contacts: [
            { first_name: nombreParts[0], last_name: nombreParts.slice(1).join(' ') || ' ',
              email: email || '', phone: { indicative: '57', number: (telefono || '').replace(/\D/g,'').slice(-10) } }
        ],
        fiscal_responsibilities: [{ code: 'R-99-PN' }],
        address: { address: 'Colombia', city: { country_code: 'Co', state_code: '11', city_code: '11001' } }
    };

    const cr = await fetch(`${BASE}/v1/customers`, { method: 'POST', headers, body: JSON.stringify(body) });
    if (!cr.ok) {
        const err = await cr.text();
        throw new Error(`SIIGO crear cliente: ${err}`);
    }
    const created = await cr.json();
    return created.id;
}

async function crearFactura({ clienteNombre, clienteEmail, clienteTelefono, clienteNit, concepto, monto, fecha }) {
    const token = await getToken();
    if (!token) {
        console.log('⚠️ SIIGO no configurado, omitiendo sincronización');
        return null;
    }

    const headers = {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Partner-Id': process.env.SIIGO_PARTNER_ID || ''
    };

    // Obtener tipos de documentos disponibles
    const tiposRes = await fetch(`${BASE}/v1/document-types?type=FV`, { headers });
    const tiposData = await tiposRes.json();
    const docType = tiposData.results?.[0];
    if (!docType) throw new Error('SIIGO: no se encontró tipo de documento FV');

    const clienteId = await buscarOCrearCliente(token, {
        nombre:    clienteNombre,
        email:     clienteEmail,
        telefono:  clienteTelefono,
        nit:       clienteNit
    });

    // Obtener impuesto sin IVA (régimen simplificado)
    const taxRes = await fetch(`${BASE}/v1/taxes`, { headers });
    const taxes = await taxRes.json();
    const sinIva = taxes.results?.find(t => t.percentage_value === 0) || null;

    const fechaStr = fecha || new Date().toISOString().split('T')[0];

    const body = {
        document: { id: docType.id },
        date:     fechaStr,
        customer: { id: clienteId },
        seller:   0,
        items: [{
            code:     'LICENCIA',
            description: concepto || 'Licencia de software',
            quantity:  1,
            price:     Number(monto),
            taxes:     sinIva ? [{ id: sinIva.id }] : []
        }],
        observations: `Generado automáticamente — ${new Date().toISOString()}`
    };

    const r = await fetch(`${BASE}/v1/invoices`, { method: 'POST', headers, body: JSON.stringify(body) });
    if (!r.ok) {
        const err = await r.text();
        throw new Error(`SIIGO crear factura: ${err}`);
    }

    const data = await r.json();
    console.log(`🧾 SIIGO factura creada: ${data.id} — ${concepto}`);
    return data;
}

module.exports = { crearFactura };
