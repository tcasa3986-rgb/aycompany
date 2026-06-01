/**
 * Generador automático de carruseles para Instagram — AI Company CO
 * Tipo A: Educativo (engagement/audiencia)
 * Tipo B: Ventas (conversión directa)
 * Alterna automáticamente día a día.
 */

const { createCanvas, loadImage, GlobalFonts } = require('@napi-rs/canvas');
const path      = require('path');
const fs        = require('fs');
const Anthropic = require('@anthropic-ai/sdk');

// ── DALL-E: genera ilustración 3D para cada slide ────────────────────────────
async function generarIlustracion(descripcion) {
    const apiKey = process.env.OPENAI_API_KEY;
    if (!apiKey) return null;
    try {
        const r = await fetch('https://api.openai.com/v1/images/generations', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${apiKey}` },
            body: JSON.stringify({
                model:   'dall-e-3',
                prompt:  `3D cartoon illustration, friendly businessman character, ${descripcion}, white background, clean minimalist tech company style, no text, no letters, professional, high quality, similar to modern SaaS illustration packs`,
                n:       1,
                size:    '1024x1024',
                quality: 'standard'
            })
        });
        const d = await r.json();
        if (d.error) { console.warn('[DALL-E]', d.error.message); return null; }
        const imgUrl = d.data?.[0]?.url;
        if (!imgUrl) return null;
        // Descargar imagen como buffer
        const imgR = await fetch(imgUrl);
        const buf  = Buffer.from(await imgR.arrayBuffer());
        return await loadImage(buf);
    } catch (e) {
        console.warn('[DALL-E] Error:', e.message);
        return null;
    }
}

// ── Rutas de assets ───────────────────────────────────────────────────────────
const ASSETS = path.join(__dirname, '../assets');
const FONTS  = path.join(ASSETS, 'fonts');
const TEMP   = path.join(ASSETS, 'carousel_temp');

// Registrar fuentes
try {
    GlobalFonts.registerFromPath(path.join(FONTS, 'Orbitron-Bold.ttf'),    'Orbitron');
    GlobalFonts.registerFromPath(path.join(FONTS, 'Orbitron-Regular.ttf'), 'OrbitronRegular');
    GlobalFonts.registerFromPath(path.join(FONTS, 'SpaceGrotesk-Bold.ttf'),    'SpaceGrotesk');
    GlobalFonts.registerFromPath(path.join(FONTS, 'SpaceGrotesk-Regular.ttf'), 'SpaceGroteskRegular');
} catch (e) { console.error('[Carousel] Error cargando fuentes:', e.message); }

// ── Colores AI Company CO ─────────────────────────────────────────────────────
const C = {
    bg:         '#0D0D14',
    bgCard:     '#111118',
    purple:     '#5A00B8',
    purpleL:    '#7B2FE0',
    purpleXL:   '#9B5FFF',
    white:      '#FFFFFF',
    text:       '#e8e9f0',
    gray:       '#8A8D99',
    grayL:      '#b0b3bf',
};

const SIZE = 1080;

// ── Utilidades canvas ─────────────────────────────────────────────────────────
function hexToRgb(hex) {
    const r = parseInt(hex.slice(1,3),16);
    const g = parseInt(hex.slice(3,5),16);
    const b = parseInt(hex.slice(5,7),16);
    return { r, g, b };
}

function roundRect(ctx, x, y, w, h, r) {
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.lineTo(x + w - r, y);
    ctx.quadraticCurveTo(x + w, y, x + w, y + r);
    ctx.lineTo(x + w, y + h - r);
    ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
    ctx.lineTo(x + r, y + h);
    ctx.quadraticCurveTo(x, y + h, x, y + h - r);
    ctx.lineTo(x, y + r);
    ctx.quadraticCurveTo(x, y, x + r, y);
    ctx.closePath();
}

function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
    const words = text.split(' ');
    let line = '';
    let lines = [];
    for (const word of words) {
        const test = line ? line + ' ' + word : word;
        if (ctx.measureText(test).width > maxWidth && line) {
            lines.push(line);
            line = word;
        } else {
            line = test;
        }
    }
    if (line) lines.push(line);
    lines.forEach((l, i) => ctx.fillText(l, x, y + i * lineHeight));
    return lines.length;
}

// ── Fondo base con estilo AI Company CO ──────────────────────────────────────
function drawBackground(ctx, tipo = 'default') {
    // Fondo sólido oscuro
    ctx.fillStyle = C.bg;
    ctx.fillRect(0, 0, SIZE, SIZE);

    // Puntos de grid (dot pattern sutil)
    ctx.fillStyle = 'rgba(90, 0, 184, 0.12)';
    for (let x = 30; x < SIZE; x += 45) {
        for (let y = 30; y < SIZE; y += 45) {
            ctx.beginPath();
            ctx.arc(x, y, 1.5, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    // Glow púrpura — esquina superior derecha
    const g1 = ctx.createRadialGradient(SIZE, 0, 0, SIZE, 0, 480);
    g1.addColorStop(0, 'rgba(90, 0, 184, 0.35)');
    g1.addColorStop(1, 'rgba(90, 0, 184, 0)');
    ctx.fillStyle = g1;
    ctx.fillRect(0, 0, SIZE, SIZE);

    // Glow púrpura — esquina inferior izquierda
    const g2 = ctx.createRadialGradient(0, SIZE, 0, 0, SIZE, 350);
    g2.addColorStop(0, 'rgba(123, 47, 224, 0.2)');
    g2.addColorStop(1, 'rgba(123, 47, 224, 0)');
    ctx.fillStyle = g2;
    ctx.fillRect(0, 0, SIZE, SIZE);

    // Línea decorativa superior
    const gradLine = ctx.createLinearGradient(0, 0, SIZE, 0);
    gradLine.addColorStop(0, 'rgba(90,0,184,0)');
    gradLine.addColorStop(0.5, 'rgba(155,95,255,0.8)');
    gradLine.addColorStop(1, 'rgba(90,0,184,0)');
    ctx.fillStyle = gradLine;
    ctx.fillRect(0, 0, SIZE, 2);
}

// ── Logo en esquina superior derecha ─────────────────────────────────────────
async function drawLogo(ctx) {
    try {
        const logoPath = path.join(ASSETS, 'logo_aicompany_icon.png');
        if (fs.existsSync(logoPath)) {
            const logo = await loadImage(logoPath);
            ctx.globalAlpha = 0.9;
            ctx.drawImage(logo, SIZE - 90, 28, 60, 60);
            ctx.globalAlpha = 1;
        }
    } catch (e) { /* logo opcional */ }
}

// ── Número de slide ───────────────────────────────────────────────────────────
function drawSlideNumber(ctx, current, total) {
    ctx.font = '22px SpaceGroteskRegular';
    ctx.fillStyle = C.gray;
    ctx.textAlign = 'right';
    ctx.fillText(`${current}/${total}`, SIZE - 36, SIZE - 36);
    ctx.textAlign = 'left';
}

// ── Pie de página ─────────────────────────────────────────────────────────────
function drawFooter(ctx) {
    // Línea decorativa inferior
    const grad = ctx.createLinearGradient(0, 0, SIZE, 0);
    grad.addColorStop(0, 'rgba(90,0,184,0)');
    grad.addColorStop(0.5, 'rgba(155,95,255,0.5)');
    grad.addColorStop(1, 'rgba(90,0,184,0)');
    ctx.fillStyle = grad;
    ctx.fillRect(0, SIZE - 2, SIZE, 2);

    ctx.font = '22px SpaceGroteskRegular';
    ctx.fillStyle = C.gray;
    ctx.textAlign = 'center';
    ctx.fillText('aicompanyco.com', SIZE / 2, SIZE - 36);
    ctx.textAlign = 'left';
}

// ── SLIDE 1: PORTADA ──────────────────────────────────────────────────────────
async function renderPortada(data) {
    const canvas = createCanvas(SIZE, SIZE);
    const ctx    = canvas.getContext('2d');
    drawBackground(ctx, 'portada');
    await drawLogo(ctx);

    // Ilustración DALL-E o emoji fallback
    const ilus = data._ilustracion || await generarIlustracion(data.ilustracion_prompt || 'thinking about technology and AI');
    if (ilus) {
        ctx.drawImage(ilus, SIZE/2 - 200, 120, 400, 400);
    } else {
        ctx.font = '160px serif';
        ctx.textAlign = 'center';
        ctx.fillText(data.emoji || '🤖', SIZE / 2, 360);
    }

    // Subtítulo arriba del título
    ctx.font = '26px SpaceGroteskRegular';
    ctx.fillStyle = C.purpleXL;
    ctx.fillText(data.subtitulo_top || 'AI Company CO presenta', SIZE / 2, 460);

    // Título principal — línea 1
    ctx.font = `bold 72px Orbitron`;
    ctx.fillStyle = C.white;
    const lines1 = wrapText(ctx, data.titulo_linea1 || '', SIZE / 2, 560, SIZE - 120, 82);

    // Título acento — palabra clave en púrpura
    const gradTxt = ctx.createLinearGradient(0, 0, SIZE, 0);
    gradTxt.addColorStop(0, C.purpleXL);
    gradTxt.addColorStop(1, C.purpleL);
    ctx.font = `bold 80px Orbitron`;
    ctx.fillStyle = gradTxt;
    const linesA = wrapText(ctx, data.titulo_acento || '', SIZE / 2, 560 + lines1 * 82 + 20, SIZE - 120, 88);

    // Descripción corta — Y dinámico según líneas del acento
    ctx.font = '30px SpaceGroteskRegular';
    ctx.fillStyle = C.grayL;
    wrapText(ctx, data.descripcion || '', SIZE / 2, 560 + lines1 * 82 + 20 + linesA * 88 + 24, SIZE - 160, 42);

    ctx.textAlign = 'left';
    drawFooter(ctx);
    return canvas.toBuffer('image/png');
}

// ── SLIDE CONTENIDO (numerado) ────────────────────────────────────────────────
async function renderContenido(data, numero, total) {
    const canvas = createCanvas(SIZE, SIZE);
    const ctx    = canvas.getContext('2d');
    drawBackground(ctx);
    await drawLogo(ctx);

    // Badge numerado
    const badge = data.badge || `#${numero}`;
    ctx.font = '28px SpaceGrotesk';
    const badgeW = ctx.measureText(badge).width + 48;
    roundRect(ctx, (SIZE - badgeW) / 2, 100, badgeW, 52, 26);
    ctx.fillStyle = 'rgba(90, 0, 184, 0.35)';
    ctx.fill();
    // Borde badge
    roundRect(ctx, (SIZE - badgeW) / 2, 100, badgeW, 52, 26);
    ctx.strokeStyle = C.purpleXL;
    ctx.lineWidth = 1.5;
    ctx.stroke();
    ctx.fillStyle = C.purpleXL;
    ctx.textAlign = 'center';
    ctx.fillText(badge, SIZE / 2, 137);

    // Ilustración DALL-E o emoji fallback
    const ilus = data._ilustracion || await generarIlustracion(data.ilustracion_prompt || 'business concept illustration');
    if (ilus) {
        ctx.drawImage(ilus, SIZE/2 - 170, 160, 340, 340);
    } else {
        ctx.font = '130px serif';
        ctx.fillText(data.emoji || '💡', SIZE / 2, 330);
    }

    // Título — palabra clave en púrpura
    ctx.font = `bold 58px Orbitron`;
    ctx.fillStyle = C.white;
    const linesT = wrapText(ctx, data.titulo || '', SIZE / 2, 440, SIZE - 140, 72);

    // Línea acento
    let acentoLines = 0;
    if (data.acento) {
        const gradTxt = ctx.createLinearGradient(200, 0, SIZE - 200, 0);
        gradTxt.addColorStop(0, C.purpleXL);
        gradTxt.addColorStop(1, C.purpleL);
        ctx.font = `bold 62px Orbitron`;
        ctx.fillStyle = gradTxt;
        acentoLines = wrapText(ctx, data.acento, SIZE / 2, 440 + linesT * 72 + 10, SIZE - 160, 72);
    }

    // Descripción — Y dinámico según cuántas líneas ocupó el acento
    const descY = 440 + linesT * 72 + (data.acento ? 10 + acentoLines * 72 + 24 : 20);
    ctx.font = '32px SpaceGroteskRegular';
    ctx.fillStyle = C.grayL;
    wrapText(ctx, data.descripcion || '', SIZE / 2, descY, SIZE - 160, 46);

    ctx.textAlign = 'left';
    drawSlideNumber(ctx, numero, total);
    drawFooter(ctx);
    return canvas.toBuffer('image/png');
}

// ── SLIDE CTA FINAL ───────────────────────────────────────────────────────────
async function renderCTA(data, total) {
    const canvas = createCanvas(SIZE, SIZE);
    const ctx    = canvas.getContext('2d');
    drawBackground(ctx, 'cta');

    // Glow central intenso
    const gCenter = ctx.createRadialGradient(SIZE/2, SIZE/2, 0, SIZE/2, SIZE/2, 400);
    gCenter.addColorStop(0, 'rgba(90, 0, 184, 0.3)');
    gCenter.addColorStop(1, 'rgba(90, 0, 184, 0)');
    ctx.fillStyle = gCenter;
    ctx.fillRect(0, 0, SIZE, SIZE);

    // Logo grande al centro
    try {
        const logoPath = path.join(ASSETS, 'logo_aicompany_icon.png');
        if (fs.existsSync(logoPath)) {
            const logo = await loadImage(logoPath);
            ctx.globalAlpha = 0.95;
            ctx.drawImage(logo, SIZE/2 - 55, 160, 110, 110);
            ctx.globalAlpha = 1;
        }
    } catch {}

    ctx.textAlign = 'center';

    // Nombre empresa
    ctx.font = '30px SpaceGrotesk';
    ctx.fillStyle = C.purpleXL;
    ctx.fillText('AI COMPANY CO', SIZE / 2, 310);

    // Separador línea
    ctx.fillStyle = C.purpleL;
    ctx.fillRect(SIZE/2 - 60, 325, 120, 2);

    // Pregunta principal
    ctx.font = `bold 64px Orbitron`;
    ctx.fillStyle = C.white;
    wrapText(ctx, data.cta_titulo || '¿Listo para automatizar tu negocio?', SIZE/2, 400, SIZE - 120, 76);

    // Subtítulo
    ctx.font = '34px SpaceGroteskRegular';
    ctx.fillStyle = C.grayL;
    wrapText(ctx, data.cta_sub || 'Consultoría GRATIS — sin compromiso', SIZE/2, 590, SIZE - 140, 46);

    // Botón WhatsApp visual
    const btnW = 440, btnH = 68, btnX = (SIZE - btnW) / 2, btnY = 680;
    roundRect(ctx, btnX, btnY, btnW, btnH, 34);
    ctx.fillStyle = '#25D366';
    ctx.fill();
    ctx.font = '30px SpaceGrotesk';
    ctx.fillStyle = '#FFFFFF';
    ctx.fillText('📱 Escríbenos por WhatsApp', SIZE / 2, btnY + 44);

    ctx.textAlign = 'left';
    drawFooter(ctx);
    return canvas.toBuffer('image/png');
}

// ── GENERAR CONTENIDO CON CLAUDE ──────────────────────────────────────────────
async function generarContenidoClaude(tipo, contexto = '') {
    const client = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

    const prompts = {
        educativo: `Crea contenido para un carrusel de Instagram de AI Company CO (agencia de automatización e IA en Colombia).

Tipo: EDUCATIVO (genera audiencia, explica problemas que la IA resuelve)
Tono: Profesional, directo, orientado a resultados. En español para Colombia.
${contexto ? `Tema sugerido: ${contexto}` : ''}

Responde SOLO con este JSON válido (sin markdown, sin explicaciones):
{
  "portada": {
    "emoji": "🤖",
    "ilustracion_prompt": "describe in english what DALL-E should draw (character + action + mood, max 15 words)",
    "subtitulo_top": "texto corto (máx 40 chars)",
    "titulo_linea1": "título principal (máx 30 chars)",
    "titulo_acento": "PALABRA CLAVE (1-3 palabras, mayúsculas)",
    "descripcion": "subtítulo (máx 60 chars)"
  },
  "slides": [
    {
      "badge": "Señal #1",
      "emoji": "💸",
      "ilustracion_prompt": "describe in english what DALL-E should draw (max 12 words)",
      "titulo": "Problema o señal (máx 25 chars)",
      "acento": "frase corta en acento",
      "descripcion": "explicación del problema (máx 80 chars)"
    }
  ],
  "cta": {
    "cta_titulo": "pregunta de cierre (máx 35 chars)",
    "cta_sub": "propuesta de valor (máx 50 chars)"
  }
}

Genera exactamente 5 slides de contenido. Temas buenos: automatización, tiempo perdido en tareas, pérdida de clientes, competencia, crecimiento estancado.`,

        ventas: `Crea contenido para un carrusel de Instagram de AI Company CO vendiendo servicios de IA y automatización.

Tipo: VENTAS (conversión directa, genera leads para agendar reunión gratis)
Tono: Urgente, orientado a beneficios, con prueba social.
${contexto ? `Servicio destacado: ${contexto}` : ''}

Responde SOLO con este JSON válido (sin markdown, sin explicaciones):
{
  "portada": {
    "emoji": "🚀",
    "ilustracion_prompt": "describe in english what DALL-E should draw (character + action + mood, max 15 words)",
    "subtitulo_top": "texto corto (máx 40 chars)",
    "titulo_linea1": "promesa principal (máx 25 chars)",
    "titulo_acento": "RESULTADO CLAVE",
    "descripcion": "prueba social o resultado (máx 60 chars)"
  },
  "slides": [
    {
      "badge": "Beneficio #1",
      "emoji": "⚡",
      "ilustracion_prompt": "describe in english what DALL-E should draw (max 12 words)",
      "titulo": "beneficio concreto (máx 25 chars)",
      "acento": "resultado específico",
      "descripcion": "descripción del beneficio (máx 80 chars)"
    }
  ],
  "cta": {
    "cta_titulo": "oferta irresistible (máx 35 chars)",
    "cta_sub": "consultoría GRATIS — sin compromiso"
  }
}

Genera exactamente 4 slides. Beneficios: respuesta automática 24/7, más ventas, menos tiempo en tareas, clientes satisfechos.`
    };

    const resp = await client.messages.create({
        model: 'claude-haiku-4-5-20251001',
        max_tokens: 1200,
        messages: [{ role: 'user', content: prompts[tipo] || prompts.educativo }]
    });

    const raw = resp.content[0]?.text?.trim() || '';
    // Extraer JSON del response
    const match = raw.match(/\{[\s\S]*\}/);
    if (!match) throw new Error('Claude no devolvió JSON válido');
    return JSON.parse(match[0]);
}

// ── RENDERIZAR TODOS LOS SLIDES ───────────────────────────────────────────────
async function renderizarCarrusel(contenido) {
    const slides  = contenido.slides || [];
    const total   = slides.length + 2; // portada + contenido + CTA
    const buffers = [];

    // Slide 1: Portada
    buffers.push(await renderPortada(contenido.portada || {}));

    // Slides de contenido
    for (let i = 0; i < slides.length; i++) {
        buffers.push(await renderContenido(slides[i], i + 2, total));
    }

    // Último slide: CTA
    buffers.push(await renderCTA(contenido.cta || {}, total));

    return buffers;
}

// ── GUARDAR IMÁGENES TEMPORALES ───────────────────────────────────────────────
function guardarTemp(buffers, prefijo) {
    if (!fs.existsSync(TEMP)) fs.mkdirSync(TEMP, { recursive: true });
    const archivos = [];
    buffers.forEach((buf, i) => {
        const nombre = `${prefijo}_slide${i + 1}_${Date.now()}.png`;
        const ruta   = path.join(TEMP, nombre);
        fs.writeFileSync(ruta, buf);
        archivos.push({ nombre, ruta });
    });
    return archivos;
}

function limpiarTemp(archivos) {
    archivos.forEach(({ ruta }) => {
        try { fs.unlinkSync(ruta); } catch {}
    });
}

// ── PUBLICAR EN INSTAGRAM ─────────────────────────────────────────────────────
async function publicarInstagram(archivos, caption) {
    const token  = process.env.INSTAGRAM_TOKEN  || process.env.WHATSAPP_TOKEN;
    const userId = process.env.INSTAGRAM_USER_ID;
    const baseUrl = (process.env.RAILWAY_PUBLIC_URL || process.env.ALLOWED_ORIGIN || '').replace(/\/$/, '');

    if (!token || !userId) {
        console.warn('[Carousel] INSTAGRAM_TOKEN o INSTAGRAM_USER_ID no configurados — omitiendo publicación');
        return null;
    }

    const apiBase = `https://graph.facebook.com/v21.0/${userId}`;

    // 1. Crear contenedor para cada imagen
    const containerIds = [];
    for (const arch of archivos) {
        const imageUrl = `${baseUrl}/api/carousel/temp/${arch.nombre}`;
        const r = await fetch(`${apiBase}/media`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
            body: JSON.stringify({
                image_url:    imageUrl,
                is_carousel_item: true,
                access_token: token
            })
        });
        const d = await r.json();
        if (d.error) throw new Error(`IG media error: ${d.error.message}`);
        containerIds.push(d.id);
        console.log(`[Carousel] Imagen ${containerIds.length} subida: ${d.id}`);
    }

    // 2. Crear contenedor del carrusel
    const carouselR = await fetch(`${apiBase}/media`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
        body: JSON.stringify({
            media_type:   'CAROUSEL',
            children:     containerIds.join(','),
            caption,
            access_token: token
        })
    });
    const carousel = await carouselR.json();
    if (carousel.error) throw new Error(`IG carousel error: ${carousel.error.message}`);
    console.log(`[Carousel] Contenedor carrusel: ${carousel.id}`);

    // 3. Publicar
    const publishR = await fetch(`${apiBase}/media_publish`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
        body: JSON.stringify({ creation_id: carousel.id, access_token: token })
    });
    const published = await publishR.json();
    if (published.error) throw new Error(`IG publish error: ${published.error.message}`);

    console.log(`✅ [Carousel] Publicado en Instagram: ${published.id}`);
    return published.id;
}

// ── PIPELINE COMPLETO ─────────────────────────────────────────────────────────
async function generarYPublicar(tipo = 'educativo', contexto = '') {
    console.log(`[Carousel] Generando carrusel tipo: ${tipo}`);

    // 1. Generar contenido con Claude
    const contenido = await generarContenidoClaude(tipo, contexto);
    console.log(`[Carousel] Contenido generado: "${contenido.portada?.titulo_linea1}"`);

    // 2. Renderizar slides
    const buffers  = await renderizarCarrusel(contenido);
    console.log(`[Carousel] ${buffers.length} slides renderizados`);

    // 3. Guardar temporalmente
    const archivos = guardarTemp(buffers, tipo);

    // 4. Construir caption
    const hashtags = tipo === 'educativo'
        ? '#InteligenciaArtificial #Automatizacion #IAParaNegocios #Colombia #MarketingDigital #Emprendimiento #Tecnologia #Startups'
        : '#AICompany #Automatizacion #IA #Negocios #Colombia #Crecimiento #TransformacionDigital #Marketing';

    const caption = `${contenido.portada?.titulo_linea1 || ''} ${contenido.portada?.titulo_acento || ''}\n\n${contenido.portada?.descripcion || ''}\n\n👉 Guarda este post y compártelo con quien lo necesite\n💬 Escríbenos para tu consultoría GRATIS\n\n${hashtags}`;

    let postId = null;
    let publicadoEnIG = false;

    // 5. Intentar publicar en Instagram
    try {
        await new Promise(r => setTimeout(r, 2000));
        postId = await publicarInstagram(archivos, caption);
        publicadoEnIG = !!postId;
    } catch (e) {
        console.warn('[Carousel] Instagram no disponible:', e.message);
    }

    // 6. Enviar imágenes por Telegram siempre (para revisar y publicar manualmente si IG falla)
    try {
        const telegramToken  = process.env.PLATAFORMA_TELEGRAM_TOKEN;
        const telegramChatId = process.env.PLATAFORMA_TELEGRAM_CHAT_ID;
        const tipoEmoji = tipo === 'educativo' ? '📚' : '🛒';

        if (telegramToken && telegramChatId) {
            // Enviar mensaje cabecera
            const baseUrl = (process.env.RAILWAY_PUBLIC_URL || '').replace(/\/$/, '');
            const estado  = publicadoEnIG ? '✅ *Publicado en Instagram automáticamente*' : '📲 *Listo para publicar — descarga y sube a Instagram*';
            await fetch(`https://api.telegram.org/bot${telegramToken}/sendMessage`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    chat_id:    telegramChatId,
                    text:       `${tipoEmoji} *Carrusel AI Company CO*\n\n📌 *${contenido.portada?.titulo_linea1} ${contenido.portada?.titulo_acento}*\n🖼 ${buffers.length} slides\n\n${estado}\n\n📝 *Caption para Instagram:*\n${caption}`,
                    parse_mode: 'Markdown'
                })
            });

            // Enviar cada imagen por Telegram
            for (let i = 0; i < archivos.length; i++) {
                const formData = new FormData();
                formData.append('chat_id', telegramChatId);
                formData.append('photo', new Blob([buffers[i]], { type: 'image/png' }), `slide_${i+1}.png`);
                formData.append('caption', `Slide ${i+1}/${archivos.length}`);
                await fetch(`https://api.telegram.org/bot${telegramToken}/sendPhoto`, {
                    method: 'POST',
                    body: formData
                });
                await new Promise(r => setTimeout(r, 300));
            }
            console.log(`[Carousel] ${buffers.length} slides enviados a Telegram`);
        }
    } catch (e) {
        console.error('[Carousel] Error Telegram:', e.message);
    }

    // 7. Limpiar archivos temporales después de 10 minutos
    setTimeout(() => limpiarTemp(archivos), 10 * 60 * 1000);

    return { contenido, slides: buffers.length, postId };
}

// ── RUTA PARA SERVIR IMÁGENES TEMPORALES ─────────────────────────────────────
function servirImagenTemp(req, res) {
    const nombre = req.params.nombre?.replace(/[^a-zA-Z0-9_.\-]/g, '');
    const ruta   = path.join(TEMP, nombre);
    if (!fs.existsSync(ruta)) return res.status(404).json({ ok: false });
    res.setHeader('Content-Type', 'image/png');
    res.sendFile(ruta);
}

module.exports = { generarYPublicar, servirImagenTemp, renderizarCarrusel, generarContenidoClaude };
