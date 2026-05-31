/**
 * Generador automático de historias para Instagram — AI Company CO
 * Formato: 1080×1920px (vertical 9:16)
 * 3 frames por set — tipos: curiosidad, educativo, ventas
 */

const { createCanvas, loadImage, GlobalFonts } = require('@napi-rs/canvas');
const path = require('path');
const fs   = require('fs');
const Anthropic = require('@anthropic-ai/sdk');

const ASSETS = path.join(__dirname, '../assets');
const FONTS  = path.join(ASSETS, 'fonts');
const TEMP   = path.join(ASSETS, 'stories_temp');

try {
    GlobalFonts.registerFromPath(path.join(FONTS, 'Orbitron-Bold.ttf'),        'Orbitron');
    GlobalFonts.registerFromPath(path.join(FONTS, 'Orbitron-Regular.ttf'),     'OrbitronRegular');
    GlobalFonts.registerFromPath(path.join(FONTS, 'SpaceGrotesk-Bold.ttf'),    'SpaceGrotesk');
    GlobalFonts.registerFromPath(path.join(FONTS, 'SpaceGrotesk-Regular.ttf'), 'SpaceGroteskRegular');
} catch {}

const C = {
    bg:       '#0D0D14',
    purple:   '#5A00B8',
    purpleL:  '#7B2FE0',
    purpleXL: '#9B5FFF',
    white:    '#FFFFFF',
    text:     '#e8e9f0',
    gray:     '#8A8D99',
    grayL:    '#b0b3bf',
    green:    '#25D366',
};

const W = 1080;
const H = 1920;

// ── DALL-E: ilustración 3D para frame 1 ──────────────────────────────────────
async function generarIlustracion(descripcion) {
    const apiKey = process.env.OPENAI_API_KEY;
    if (!apiKey) return null;
    try {
        const r = await fetch('https://api.openai.com/v1/images/generations', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${apiKey}` },
            body: JSON.stringify({
                model:   'dall-e-3',
                prompt:  `3D cartoon illustration, friendly businessman character, ${descripcion}, white background, clean minimalist tech company style, no text, no letters, professional high quality, similar to modern SaaS illustration packs`,
                n:       1,
                size:    '1024x1024',
                quality: 'standard'
            })
        });
        const d = await r.json();
        if (d.error) { console.warn('[Stories DALL-E]', d.error.message); return null; }
        const imgUrl = d.data?.[0]?.url;
        if (!imgUrl) return null;
        const imgR = await fetch(imgUrl);
        const buf  = Buffer.from(await imgR.arrayBuffer());
        return await loadImage(buf);
    } catch (e) {
        console.warn('[Stories DALL-E] Error:', e.message);
        return null;
    }
}

// ── Utilidades canvas ─────────────────────────────────────────────────────────
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
    if (!text) return 0;
    const words = text.split(' ');
    let line = '';
    const lines = [];
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

// ── Fondo 1080×1920 ───────────────────────────────────────────────────────────
function drawStoryBg(ctx) {
    ctx.fillStyle = C.bg;
    ctx.fillRect(0, 0, W, H);

    // Dot grid sutil
    ctx.fillStyle = 'rgba(90, 0, 184, 0.08)';
    for (let x = 40; x < W; x += 60) {
        for (let y = 40; y < H; y += 60) {
            ctx.beginPath();
            ctx.arc(x, y, 1.5, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    // Glow superior
    const g1 = ctx.createRadialGradient(W / 2, 0, 0, W / 2, 0, 750);
    g1.addColorStop(0, 'rgba(90, 0, 184, 0.55)');
    g1.addColorStop(1, 'rgba(90, 0, 184, 0)');
    ctx.fillStyle = g1;
    ctx.fillRect(0, 0, W, H);

    // Glow inferior
    const g2 = ctx.createRadialGradient(W / 2, H, 0, W / 2, H, 650);
    g2.addColorStop(0, 'rgba(123, 47, 224, 0.45)');
    g2.addColorStop(1, 'rgba(123, 47, 224, 0)');
    ctx.fillStyle = g2;
    ctx.fillRect(0, 0, W, H);

    // Línea superior
    const gl = ctx.createLinearGradient(0, 0, W, 0);
    gl.addColorStop(0, 'rgba(90,0,184,0)');
    gl.addColorStop(0.5, 'rgba(155,95,255,1)');
    gl.addColorStop(1, 'rgba(90,0,184,0)');
    ctx.fillStyle = gl;
    ctx.fillRect(0, 0, W, 3);
}

// ── Header: logo + nombre empresa ────────────────────────────────────────────
async function drawStoryHeader(ctx) {
    try {
        const logoPath = path.join(ASSETS, 'logo_aicompany_icon.png');
        if (fs.existsSync(logoPath)) {
            const logo = await loadImage(logoPath);
            ctx.globalAlpha = 0.9;
            ctx.drawImage(logo, 60, 60, 70, 70);
            ctx.globalAlpha = 1;
        }
    } catch {}

    ctx.font = '30px SpaceGrotesk';
    ctx.fillStyle = C.purpleXL;
    ctx.textAlign = 'left';
    ctx.fillText('AI COMPANY CO', 148, 106);
}

// ── Footer: URL ───────────────────────────────────────────────────────────────
function drawStoryFooter(ctx) {
    const gl = ctx.createLinearGradient(0, 0, W, 0);
    gl.addColorStop(0, 'rgba(90,0,184,0)');
    gl.addColorStop(0.5, 'rgba(155,95,255,0.6)');
    gl.addColorStop(1, 'rgba(90,0,184,0)');
    ctx.fillStyle = gl;
    ctx.fillRect(0, H - 3, W, 3);

    ctx.font = '26px SpaceGroteskRegular';
    ctx.fillStyle = C.gray;
    ctx.textAlign = 'center';
    ctx.fillText('aicompanyco.com', W / 2, H - 50);
    ctx.textAlign = 'left';
}

// ── FRAME 1: HOOK (pregunta/dato impactante + ilustración) ────────────────────
async function renderHook(data, ilustracion) {
    const canvas = createCanvas(W, H);
    const ctx    = canvas.getContext('2d');
    drawStoryBg(ctx);
    await drawStoryHeader(ctx);

    ctx.textAlign = 'center';

    // Badge/tag
    const tag = data.tag || 'DATO DEL DÍA';
    ctx.font = '28px SpaceGrotesk';
    const tagW = ctx.measureText(tag).width + 52;
    roundRect(ctx, (W - tagW) / 2, 200, tagW, 56, 28);
    ctx.fillStyle = 'rgba(90, 0, 184, 0.35)';
    ctx.fill();
    roundRect(ctx, (W - tagW) / 2, 200, tagW, 56, 28);
    ctx.strokeStyle = C.purpleXL;
    ctx.lineWidth = 1.5;
    ctx.stroke();
    ctx.fillStyle = C.purpleXL;
    ctx.fillText(tag, W / 2, 241);

    // Ilustración 3D o emoji fallback
    if (ilustracion) {
        ctx.drawImage(ilustracion, W / 2 - 260, 290, 520, 520);
    } else {
        ctx.font = '190px serif';
        ctx.fillText(data.emoji || '🤖', W / 2, 680);
    }

    const baseY = ilustracion ? 870 : 920;

    // Hook principal
    ctx.font = `bold 78px Orbitron`;
    ctx.fillStyle = C.white;
    const hookLines = wrapText(ctx, data.hook || '', W / 2, baseY, W - 120, 94);

    // Acento (número/cifra grande en gradiente)
    if (data.acento) {
        const gradTxt = ctx.createLinearGradient(160, 0, W - 160, 0);
        gradTxt.addColorStop(0, C.purpleXL);
        gradTxt.addColorStop(1, C.purpleL);
        ctx.font = `bold 100px Orbitron`;
        ctx.fillStyle = gradTxt;
        ctx.fillText(data.acento, W / 2, baseY + hookLines * 94 + 20);
    }

    // Subtítulo
    const subY = baseY + hookLines * 94 + (data.acento ? 130 : 20);
    ctx.font = '36px SpaceGroteskRegular';
    ctx.fillStyle = C.grayL;
    wrapText(ctx, data.subtitulo || '', W / 2, subY, W - 140, 52);

    ctx.textAlign = 'left';
    drawStoryFooter(ctx);
    return canvas.toBuffer('image/png');
}

// ── FRAME 2: INSIGHT (valor/tip — tipografía grande, sin ilustración) ─────────
async function renderInsight(data) {
    const canvas = createCanvas(W, H);
    const ctx    = canvas.getContext('2d');
    drawStoryBg(ctx);
    await drawStoryHeader(ctx);

    ctx.textAlign = 'center';

    // Número decorativo grande (marca de agua)
    if (data.numero) {
        const gradNum = ctx.createLinearGradient(0, 400, W, 700);
        gradNum.addColorStop(0, 'rgba(90,0,184,0.18)');
        gradNum.addColorStop(0.5, 'rgba(155,95,255,0.18)');
        gradNum.addColorStop(1, 'rgba(90,0,184,0.18)');
        ctx.font = `bold 400px Orbitron`;
        ctx.fillStyle = gradNum;
        ctx.fillText(data.numero, W / 2, 750);
    }

    // Separador
    const gradSep = ctx.createLinearGradient(0, 0, W, 0);
    gradSep.addColorStop(0, 'rgba(90,0,184,0)');
    gradSep.addColorStop(0.5, C.purpleXL);
    gradSep.addColorStop(1, 'rgba(90,0,184,0)');
    ctx.fillStyle = gradSep;
    ctx.fillRect(W / 2 - 180, 780, 360, 3);

    // Título del insight
    ctx.font = `bold 74px Orbitron`;
    ctx.fillStyle = C.white;
    const titleLines = wrapText(ctx, data.titulo || '', W / 2, 860, W - 120, 90);

    // Descripción
    ctx.font = '38px SpaceGroteskRegular';
    ctx.fillStyle = C.grayL;
    const descY = 860 + titleLines * 90 + 30;
    const descLines = wrapText(ctx, data.descripcion || '', W / 2, descY, W - 140, 56);

    // Card extra
    if (data.punto_extra) {
        const cardY = descY + descLines * 56 + 70;
        roundRect(ctx, 60, cardY, W - 120, 140, 22);
        ctx.fillStyle = 'rgba(90, 0, 184, 0.22)';
        ctx.fill();
        roundRect(ctx, 60, cardY, W - 120, 140, 22);
        ctx.strokeStyle = 'rgba(155, 95, 255, 0.4)';
        ctx.lineWidth = 1;
        ctx.stroke();
        ctx.font = '32px SpaceGroteskRegular';
        ctx.fillStyle = C.text;
        wrapText(ctx, data.punto_extra, W / 2, cardY + 56, W - 200, 46);
    }

    ctx.textAlign = 'left';
    drawStoryFooter(ctx);
    return canvas.toBuffer('image/png');
}

// ── FRAME 3: CTA (llamada a acción con botón WhatsApp) ────────────────────────
async function renderStoryCTA(data) {
    const canvas = createCanvas(W, H);
    const ctx    = canvas.getContext('2d');
    drawStoryBg(ctx);

    // Glow central intenso
    const gCenter = ctx.createRadialGradient(W / 2, H / 2, 0, W / 2, H / 2, 650);
    gCenter.addColorStop(0, 'rgba(90, 0, 184, 0.4)');
    gCenter.addColorStop(1, 'rgba(90, 0, 184, 0)');
    ctx.fillStyle = gCenter;
    ctx.fillRect(0, 0, W, H);

    ctx.textAlign = 'center';

    // Logo grande
    try {
        const logoPath = path.join(ASSETS, 'logo_aicompany_icon.png');
        if (fs.existsSync(logoPath)) {
            const logo = await loadImage(logoPath);
            ctx.globalAlpha = 0.95;
            ctx.drawImage(logo, W / 2 - 80, 380, 160, 160);
            ctx.globalAlpha = 1;
        }
    } catch {}

    ctx.font = '34px SpaceGrotesk';
    ctx.fillStyle = C.purpleXL;
    ctx.fillText('AI COMPANY CO', W / 2, 590);

    ctx.fillStyle = C.purpleL;
    ctx.fillRect(W / 2 - 90, 610, 180, 2);

    // Pregunta CTA
    ctx.font = `bold 84px Orbitron`;
    ctx.fillStyle = C.white;
    const ctaLines = wrapText(ctx, data.cta_titulo || '¿Tu negocio puede automatizarse?', W / 2, 700, W - 120, 100);

    // Subtítulo CTA
    ctx.font = '40px SpaceGroteskRegular';
    ctx.fillStyle = C.grayL;
    wrapText(ctx, data.cta_sub || 'Automatiza ventas, atención y procesos con IA', W / 2, 700 + ctaLines * 100 + 30, W - 140, 58);

    // Beneficios rápidos
    const bens = data.beneficios || ['✅ Respuesta 24/7', '✅ Más ventas', '✅ Menos tiempo perdido'];
    let benY = 700 + ctaLines * 100 + 180;
    for (const ben of bens) {
        roundRect(ctx, (W - 600) / 2, benY, 600, 64, 32);
        ctx.fillStyle = 'rgba(90, 0, 184, 0.25)';
        ctx.fill();
        ctx.font = '30px SpaceGroteskRegular';
        ctx.fillStyle = C.text;
        ctx.fillText(ben, W / 2, benY + 42);
        benY += 84;
    }

    // Botón WhatsApp
    const btnY = H - 500;
    const btnW = 720, btnH = 94;
    roundRect(ctx, (W - btnW) / 2, btnY, btnW, btnH, 47);
    const gradBtn = ctx.createLinearGradient((W - btnW) / 2, 0, (W + btnW) / 2, 0);
    gradBtn.addColorStop(0, '#128C7E');
    gradBtn.addColorStop(1, C.green);
    ctx.fillStyle = gradBtn;
    ctx.fill();
    ctx.font = '36px SpaceGrotesk';
    ctx.fillStyle = C.white;
    ctx.fillText('📱 Escríbenos por WhatsApp', W / 2, btnY + 60);

    ctx.font = '28px SpaceGroteskRegular';
    ctx.fillStyle = C.gray;
    ctx.fillText('Consultoría GRATIS — sin compromiso', W / 2, btnY + 122);

    ctx.textAlign = 'left';
    drawStoryFooter(ctx);
    return canvas.toBuffer('image/png');
}

// ── GENERAR CONTENIDO CON CLAUDE HAIKU ───────────────────────────────────────
async function generarContenidoStory(tipo, contexto = '') {
    const client = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

    const prompts = {
        curiosidad: `Crea contenido para un set de 3 historias de Instagram de AI Company CO (agencia de automatización e IA en Colombia).

Tipo: CURIOSIDAD (genera engagement, hace preguntar al usuario algo sobre su negocio)
Tono: Impactante, pregunta que genera reflexión. En español para Colombia.
${contexto ? `Contexto/tema: ${contexto}` : ''}

Responde SOLO con este JSON válido (sin markdown):
{
  "ilustracion_prompt": "describe in english a 3D character illustration (max 12 words, character + action)",
  "frame1": {
    "tag": "DATO IMPACTANTE",
    "emoji": "🤔",
    "hook": "pregunta corta impactante (máx 25 chars)",
    "acento": "cifra o stat (ej: 73%)",
    "subtitulo": "contexto del dato (máx 70 chars)"
  },
  "frame2": {
    "numero": "stat corto (ej: 4h)",
    "titulo": "qué significa ese número (máx 30 chars)",
    "descripcion": "explicación del impacto en el negocio (máx 100 chars)",
    "punto_extra": "tip o reflexión final (máx 80 chars)"
  },
  "frame3": {
    "cta_titulo": "pregunta directa (máx 30 chars)",
    "cta_sub": "propuesta de valor (máx 50 chars)",
    "beneficios": ["✅ beneficio 1 (máx 25 chars)", "✅ beneficio 2", "✅ beneficio 3"]
  }
}`,

        educativo: `Crea contenido para un set de 3 historias de Instagram de AI Company CO (agencia de IA en Colombia).

Tipo: EDUCATIVO (enseña algo sobre automatización/IA que genera valor y confianza)
Tono: Experto pero accesible, genera autoridad. En español para Colombia.
${contexto ? `Tema: ${contexto}` : ''}

Responde SOLO con este JSON válido (sin markdown):
{
  "ilustracion_prompt": "describe in english a 3D character illustration (max 12 words)",
  "frame1": {
    "tag": "TIP DEL DÍA",
    "emoji": "💡",
    "hook": "título del tip (máx 25 chars)",
    "acento": "número impactante si aplica (o null)",
    "subtitulo": "beneficio del tip (máx 70 chars)"
  },
  "frame2": {
    "numero": "stat relevante (o null)",
    "titulo": "el insight clave (máx 30 chars)",
    "descripcion": "explicación práctica (máx 100 chars)",
    "punto_extra": "ejemplo real o caso de uso (máx 80 chars)"
  },
  "frame3": {
    "cta_titulo": "¿Quieres implementarlo? (máx 30 chars)",
    "cta_sub": "te ayudamos en 1 semana (máx 50 chars)",
    "beneficios": ["✅ beneficio 1", "✅ beneficio 2", "✅ beneficio 3"]
  }
}`,

        ventas: `Crea contenido para un set de 3 historias de Instagram de AI Company CO vendiendo servicios de automatización.

Tipo: VENTAS (urgencia, beneficios concretos, cierre directo)
Tono: Urgente, orientado a resultados, con prueba social. En español para Colombia.
${contexto ? `Servicio destacado: ${contexto}` : ''}

Responde SOLO con este JSON válido (sin markdown):
{
  "ilustracion_prompt": "describe in english a 3D character illustration showing success/results (max 12 words)",
  "frame1": {
    "tag": "CASO DE ÉXITO",
    "emoji": "🚀",
    "hook": "resultado logrado (máx 25 chars)",
    "acento": "cifra del resultado (ej: +200%)",
    "subtitulo": "contexto del resultado (máx 70 chars)"
  },
  "frame2": {
    "numero": "resultado numérico clave",
    "titulo": "qué logramos (máx 30 chars)",
    "descripcion": "cómo lo logramos brevemente (máx 100 chars)",
    "punto_extra": "tu negocio también puede lograrlo (máx 80 chars)"
  },
  "frame3": {
    "cta_titulo": "¿Quieres este resultado? (máx 30 chars)",
    "cta_sub": "empieza esta semana con nosotros",
    "beneficios": ["✅ beneficio 1", "✅ beneficio 2", "✅ beneficio 3"]
  }
}`
    };

    const resp = await client.messages.create({
        model:      'claude-haiku-4-5-20251001',
        max_tokens: 900,
        messages:   [{ role: 'user', content: prompts[tipo] || prompts.curiosidad }]
    });

    const raw   = resp.content[0]?.text?.trim() || '';
    const match = raw.match(/\{[\s\S]*\}/);
    if (!match) throw new Error('Claude no devolvió JSON válido para story');
    return JSON.parse(match[0]);
}

// ── RENDERIZAR LOS 3 FRAMES ───────────────────────────────────────────────────
async function renderizarStories(contenido) {
    const buffers = [];

    // Generar 1 ilustración DALL-E para frame 1
    const ilustracion = await generarIlustracion(contenido.ilustracion_prompt || 'friendly AI assistant helping businessman');

    buffers.push(await renderHook(contenido.frame1 || {},    ilustracion));
    buffers.push(await renderInsight(contenido.frame2 || {}));
    buffers.push(await renderStoryCTA(contenido.frame3 || {}));

    return buffers;
}

// ── GUARDAR/LIMPIAR TEMPORALES ────────────────────────────────────────────────
function guardarTemp(buffers, prefijo) {
    if (!fs.existsSync(TEMP)) fs.mkdirSync(TEMP, { recursive: true });
    return buffers.map((buf, i) => {
        const nombre = `${prefijo}_story${i + 1}_${Date.now()}.png`;
        const ruta   = path.join(TEMP, nombre);
        fs.writeFileSync(ruta, buf);
        return { nombre, ruta };
    });
}

function limpiarTemp(archivos) {
    archivos.forEach(({ ruta }) => { try { fs.unlinkSync(ruta); } catch {} });
}

// ── PUBLICAR EN INSTAGRAM COMO STORIES ───────────────────────────────────────
async function publicarStoriesInstagram(archivos) {
    const token   = process.env.INSTAGRAM_TOKEN || process.env.WHATSAPP_TOKEN;
    const userId  = process.env.INSTAGRAM_USER_ID;
    const baseUrl = (process.env.RAILWAY_PUBLIC_URL || process.env.ALLOWED_ORIGIN || '').replace(/\/$/, '');

    if (!token || !userId) return [];

    const publicados = [];
    const apiBase = `https://graph.facebook.com/v21.0/${userId}`;

    for (const arch of archivos) {
        try {
            const imageUrl = `${baseUrl}/api/stories/temp/${arch.nombre}`;
            // Crear contenedor story
            const r = await fetch(`${apiBase}/media`, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
                body:    JSON.stringify({ image_url: imageUrl, media_type: 'STORIES', access_token: token })
            });
            const d = await r.json();
            if (d.error) throw new Error(d.error.message);

            await new Promise(res => setTimeout(res, 1500));

            // Publicar
            const pub = await fetch(`${apiBase}/media_publish`, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
                body:    JSON.stringify({ creation_id: d.id, access_token: token })
            });
            const pubD = await pub.json();
            if (pubD.error) throw new Error(pubD.error.message);
            publicados.push(pubD.id);
            console.log(`[Stories] Frame publicado: ${pubD.id}`);
        } catch (e) {
            console.warn(`[Stories] Error publicando ${arch.nombre}:`, e.message);
        }
    }

    return publicados;
}

// ── ENVIAR POR TELEGRAM ───────────────────────────────────────────────────────
async function enviarTelegram(buffers, archivos, contenido, tipo, publicadoEnIG) {
    const telegramToken  = process.env.PLATAFORMA_TELEGRAM_TOKEN;
    const telegramChatId = process.env.PLATAFORMA_TELEGRAM_CHAT_ID;
    if (!telegramToken || !telegramChatId) return;

    const tipoEmoji = { curiosidad: '🤔', educativo: '💡', ventas: '🚀' }[tipo] || '📖';
    const estado    = publicadoEnIG
        ? '✅ *Publicado en Instagram automáticamente*'
        : '📲 *Listo para publicar — sube las 3 imágenes como historias en Instagram*';

    await fetch(`https://api.telegram.org/bot${telegramToken}/sendMessage`, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({
            chat_id:    telegramChatId,
            text:       `${tipoEmoji} *Historias AI Company CO — ${tipo.toUpperCase()}*\n\n📌 *${contenido.frame1?.hook || ''}*\n🖼 3 frames (sube en este orden)\n\n${estado}`,
            parse_mode: 'Markdown'
        })
    });

    for (let i = 0; i < buffers.length; i++) {
        const formData = new FormData();
        formData.append('chat_id', telegramChatId);
        formData.append('photo', new Blob([buffers[i]], { type: 'image/png' }), `historia_${i + 1}.png`);
        formData.append('caption', `Historia ${i + 1}/3${i === 0 ? ' — Hook' : i === 1 ? ' — Insight' : ' — CTA'}`);
        await fetch(`https://api.telegram.org/bot${telegramToken}/sendPhoto`, {
            method: 'POST',
            body:   formData
        });
        await new Promise(r => setTimeout(r, 400));
    }

    console.log('[Stories] 3 frames enviados a Telegram');
}

// ── PIPELINE COMPLETO ─────────────────────────────────────────────────────────
async function generarYPublicarStory(tipo = 'curiosidad', contexto = '') {
    console.log(`[Stories] Generando historias tipo: ${tipo}`);

    const contenido = await generarContenidoStory(tipo, contexto);
    console.log(`[Stories] Contenido generado: "${contenido.frame1?.hook}"`);

    const buffers  = await renderizarStories(contenido);
    console.log(`[Stories] ${buffers.length} frames renderizados`);

    const prefijo  = `story_${tipo}`;
    const archivos = guardarTemp(buffers, prefijo);

    let publicados = [];
    try {
        await new Promise(r => setTimeout(r, 2000));
        publicados = await publicarStoriesInstagram(archivos);
    } catch (e) {
        console.warn('[Stories] Instagram no disponible:', e.message);
    }

    try {
        await enviarTelegram(buffers, archivos, contenido, tipo, publicados.length > 0);
    } catch (e) {
        console.error('[Stories] Error Telegram:', e.message);
    }

    setTimeout(() => limpiarTemp(archivos), 10 * 60 * 1000);

    return { contenido, frames: buffers.length, publicados };
}

// ── RUTA PARA SERVIR IMÁGENES TEMPORALES ─────────────────────────────────────
function servirImagenTemp(req, res) {
    const nombre = req.params.nombre?.replace(/[^a-zA-Z0-9_.\-]/g, '');
    const ruta   = path.join(TEMP, nombre);
    if (!fs.existsSync(ruta)) return res.status(404).json({ ok: false });
    res.setHeader('Content-Type', 'image/png');
    res.sendFile(ruta);
}

module.exports = { generarYPublicarStory, servirImagenTemp, renderizarStories, generarContenidoStory };
