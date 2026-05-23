const axios = require('axios');

const BASE = 'https://aicompanyco.com/demos';

const DEMOS = [
    { id: 'restaurante', tipos: ['restaurante','comida','cocina','bar','cafeteria','pizzeria','sushi','grill','asadero','parrilla'],  label: 'Sistema para Restaurante',  url: `${BASE}/restaurante` },
    { id: 'farmacia',    tipos: ['farmacia','drogueria','medicamentos','salud','drogas'],                                             label: 'Sistema para Farmacia',     url: `${BASE}/farmacia` },
    { id: 'colegio',     tipos: ['colegio','escuela','academia','instituto','educacion','universidad','jardin','preescolar'],         label: 'Sistema para Colegio',      url: `${BASE}/colegio` },
    { id: 'odontologia', tipos: ['odontologia','dentista','clinica','consultorio','medico','estetica','optometria','laboratorio'],    label: 'Sistema para Clínica',      url: `${BASE}/odontologia` },
    { id: 'salon',       tipos: ['salon','peluqueria','barberia','spa','belleza','estetica','nails','unas'],                         label: 'Sistema para Salón',        url: `${BASE}/salon` },
    { id: 'ferreteria',  tipos: ['ferreteria','construccion','materiales','herramientas','electricidad','plomeria','pintura'],       label: 'Sistema para Ferretería',   url: `${BASE}/ferreteria` },
    { id: 'hospedaje',   tipos: ['hotel','hostal','motel','posada','hospedaje','airbnb','apartamentos','glamping'],                  label: 'Sistema para Hospedaje',    url: `${BASE}/hospedaje` },
    { id: 'parqueo',     tipos: ['parqueo','parqueadero','estacionamiento','lavado','vehiculos'],                                    label: 'Sistema para Parqueadero',  url: `${BASE}/parqueo` },
    { id: 'delivery',    tipos: ['delivery','domicilios','mensajeria','envios','logistica','transporte'],                            label: 'Sistema para Delivery',     url: `${BASE}/delivery` },
    { id: 'ventas',      tipos: ['tienda','comercio','almacen','distribuidora','ventas','repuestos','electrodomesticos','ropa'],     label: 'Sistema de Ventas',         url: `${BASE}/ventas` },
];

function detectarDemosRelevantes(info) {
    const texto = [info.tipo, info.nombre, info.web?.titulo, info.web?.descripcion, info.web?.textoResumen]
        .filter(Boolean).join(' ').toLowerCase();

    const scores = DEMOS.map(demo => ({
        demo,
        score: demo.tipos.filter(t => texto.includes(t)).length
    })).sort((a, b) => b.score - a.score);

    const seleccionados = scores.filter(s => s.score > 0).slice(0, 3).map(s => s.demo);
    if (seleccionados.length < 2) {
        const extras = scores.filter(s => !seleccionados.includes(s.demo)).slice(0, 3 - seleccionados.length);
        seleccionados.push(...extras.map(s => s.demo));
    }
    return seleccionados.slice(0, 4);
}

function analizarPresencia(info) {
    const tiene = [];
    const falta = [];

    if (info.tieneMaps) tiene.push('Google Maps');   else falta.push('Google Maps');
    if (info.sitioUrl)  tiene.push('Sitio web');     else falta.push('Sitio web');

    if (info.web) {
        if (info.web.tieneWA)      tiene.push('WhatsApp Business');  else falta.push('WhatsApp Business');
        if (info.web.tieneFB)      tiene.push('Facebook');           else falta.push('Facebook');
        if (info.web.tieneIG)      tiene.push('Instagram');          else falta.push('Instagram');
        if (info.web.tieneEcomm)   tiene.push('Tienda online');      else falta.push('Tienda online');
        if (info.web.tienePedido)  tiene.push('Sistema de pedidos'); else falta.push('Sistema de pedidos');
        if (info.web.tieneChatbot) tiene.push('Chat automatizado');  else falta.push('Chat automatizado');
    } else {
        falta.push('WhatsApp Business', 'Tienda online', 'Sistema de pedidos', 'Chat automatizado');
    }

    return { tiene, falta };
}

async function generarIntroIA(info) {
    const apiKey = process.env.GEMINI_API_KEY;
    if (!apiKey) return null;

    try {
        const prompt = `Eres un asesor comercial de AI Company CO, empresa colombiana de transformación digital.
Escribe UN párrafo corto (máximo 3 oraciones) dirigido a ${info.nombre} en ${info.ciudad}.
Menciona algo específico de su negocio. Sé directo, profesional y amigable. No uses emojis.
Contexto del negocio:
- Tiene sitio web: ${info.sitioUrl ? 'Sí (' + info.sitioUrl + ')' : 'No'}
- Tiene Google Maps: ${info.tieneMaps ? 'Sí' : 'No'}
- Rating Google: ${info.rating || 'No disponible'}
- Tipo de negocio: ${info.tipo || 'General'}
${info.web?.textoResumen ? '- Descripción: ' + info.web.textoResumen.slice(0, 300) : ''}`;

        const response = await axios.post(
            `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${apiKey}`,
            { contents: [{ parts: [{ text: prompt }] }] },
            { timeout: 10000 }
        );
        return response.data?.candidates?.[0]?.content?.parts?.[0]?.text?.trim() || null;
    } catch (e) {
        console.error('[Gemini]', e.message);
        return null;
    }
}

async function generarPropuesta(info) {
    const demos    = detectarDemosRelevantes(info);
    const analisis = analizarPresencia(info);
    const introIA  = await generarIntroIA(info);

    const intro = introIA ||
        `Hemos analizado la presencia digital de <strong>${info.nombre}</strong> en ${info.ciudad} y encontramos oportunidades concretas para potenciar sus ventas y automatizar su operación. Nuestras soluciones están diseñadas específicamente para negocios como el suyo.`;

    const screenshotHTML = info.web?.screenshot
        ? `<div style="margin:24px 0;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.3)">
             <img src="data:image/jpeg;base64,${info.web.screenshot}" style="width:100%;display:block" alt="Sitio web actual">
             <div style="background:#1a1a2e;padding:8px 16px;font-size:12px;color:#888">Sitio web actual: ${info.sitioUrl}</div>
           </div>`
        : '';

    const tieneItems = analisis.tiene.map(t =>
        `<span style="background:#0d4f3c;color:#4ade80;padding:4px 12px;border-radius:20px;font-size:13px;display:inline-block;margin:3px">✓ ${t}</span>`
    ).join('');

    const faltaItems = analisis.falta.map(f =>
        `<span style="background:#4f1d1d;color:#f87171;padding:4px 12px;border-radius:20px;font-size:13px;display:inline-block;margin:3px">✗ ${f}</span>`
    ).join('');

    const demosHTML = demos.map(d => `
        <div style="background:#1a1a2e;border:1px solid #2a2a4a;border-radius:12px;padding:20px;margin-bottom:12px">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
                <strong style="color:#e0e0ff;font-size:16px">${d.label}</strong>
                <a href="${d.url}" target="_blank" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;padding:8px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600">Ver demo en vivo →</a>
            </div>
            <p style="color:#9090b0;margin:10px 0 0;font-size:14px">Vea cómo funcionaría el sistema para su negocio — demo completamente funcional.</p>
        </div>`
    ).join('');

    const beneficiosHTML = [
        ['⚡', 'Implementación en 72h',   'Su sistema funcionando en menos de 3 días.'],
        ['🎯', 'Personalizado para usted', 'Adaptado exactamente a su tipo de negocio.'],
        ['📱', 'Funciona en celular',      'Sus clientes y equipo acceden desde cualquier dispositivo.'],
        ['🔒', 'Datos seguros',            'Sus datos almacenados con respaldo diario.'],
        ['💬', 'Soporte incluido',         'Acompañamiento durante los primeros 30 días.'],
        ['💰', 'Desde $150.000/mes',       'Sin costo inicial. Cancela cuando quiera.'],
    ].map(([icon, titulo, desc]) => `
        <div style="background:#1a1a2e;border:1px solid #2a2a4a;border-radius:12px;padding:20px;text-align:center">
            <div style="font-size:32px;margin-bottom:8px">${icon}</div>
            <strong style="color:#e0e0ff;display:block;margin-bottom:6px">${titulo}</strong>
            <p style="color:#9090b0;font-size:13px;margin:0">${desc}</p>
        </div>`
    ).join('');

    const ratingBadge = info.rating
        ? `<span style="background:#2d2d00;color:#fbbf24;padding:3px 10px;border-radius:12px;font-size:13px">⭐ ${info.rating}</span>`
        : '';

    const html = `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Propuesta Digital — ${info.nombre}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f0e1a; color: #c0c0e0; line-height: 1.6; }
  .container { max-width: 800px; margin: 0 auto; padding: 20px; }
  h2 { font-size: 20px; font-weight: 600; color: #a0a0d0; margin-bottom: 16px; }
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
  @media (max-width: 600px) { .grid-2, .grid-3 { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<div style="background:linear-gradient(135deg,#1a1a3e,#2d1b69);padding:40px 20px;text-align:center">
  <div style="max-width:800px;margin:0 auto">
    <p style="color:#8b8bc0;font-size:14px;margin-bottom:8px">PROPUESTA PERSONALIZADA — AI COMPANY CO</p>
    <h1 style="color:#fff;font-size:28px;font-weight:700;margin-bottom:12px">Transformación Digital para<br><span style="color:#a78bfa">${info.nombre}</span></h1>
    <p style="color:#b0b0d0">${info.ciudad} ${ratingBadge}</p>
  </div>
</div>

<div class="container" style="padding-top:40px">

  <div style="background:#1a1a2e;border-left:4px solid #6366f1;border-radius:0 12px 12px 0;padding:20px 24px;margin-bottom:40px">
    <p style="color:#c0c0e0;font-size:15px">${intro}</p>
  </div>

  ${screenshotHTML}

  <div style="margin-bottom:40px">
    <h2>Análisis de presencia digital</h2>
    <div class="grid-2">
      <div style="background:#0d1a14;border:1px solid #1a3a28;border-radius:12px;padding:16px">
        <p style="color:#4ade80;font-weight:600;margin-bottom:10px">✓ Lo que ya tiene</p>
        ${tieneItems || '<span style="color:#666;font-size:13px">Ninguno detectado</span>'}
      </div>
      <div style="background:#1a0d0d;border:1px solid #3a1a1a;border-radius:12px;padding:16px">
        <p style="color:#f87171;font-weight:600;margin-bottom:10px">✗ Oportunidades de mejora</p>
        ${faltaItems}
      </div>
    </div>
  </div>

  <div style="margin-bottom:40px">
    <h2>Demos en vivo — personalizadas para su sector</h2>
    ${demosHTML}
  </div>

  <div style="margin-bottom:40px">
    <h2>¿Por qué AI Company CO?</h2>
    <div class="grid-3">${beneficiosHTML}</div>
  </div>

  <div style="background:linear-gradient(135deg,#2d1b69,#1a1a3e);border-radius:16px;padding:40px;text-align:center;margin-bottom:40px">
    <h2 style="color:#fff;margin-bottom:12px">¿Listo para dar el salto digital?</h2>
    <p style="color:#b0b0d0;margin-bottom:24px">Agenda una llamada de 30 minutos sin costo. Le mostramos el sistema funcionando en vivo.</p>
    ${info.telefono ? `<a href="https://wa.me/${info.telefono.replace(/\D/g,'')}" style="background:#25D366;color:#fff;padding:14px 32px;border-radius:10px;text-decoration:none;font-weight:700;display:inline-block;margin:6px">💬 Responder por WhatsApp</a>` : ''}
    <a href="mailto:ventas@aicompanyco.com" style="background:#6366f1;color:#fff;padding:14px 32px;border-radius:10px;text-decoration:none;font-weight:700;display:inline-block;margin:6px">📧 Escribirnos por Email</a>
  </div>

</div>
</body>
</html>`;

    return { html, demos, analisis };
}

module.exports = { generarPropuesta, detectarDemosRelevantes, analizarPresencia };
