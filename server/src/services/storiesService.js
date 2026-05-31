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
    greenD:   '#128C7E',
};

const W = 1080;
const H = 1920;

// ── DALL-E ────────────────────────────────────────────────────────────────────
async function generarIlustracion(descripcion) {
    const apiKey = process.env.OPENAI_API_KEY;
    if (!apiKey) return null;
    try {
        const r = await fetch('https://api.openai.com/v1/images/generations', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${apiKey}` },
            body: JSON.stringify({
                model:   'dall-e-3',
                prompt:  `3D cartoon illustration, friendly businessman character, ${descripcion}, transparent or white background, clean minimalist tech company style, no text, no letters, professional high quality, similar to modern SaaS illustration packs`,
                n: 1, size: '1024x1024', quality: 'standard'
            })
        });
        const d = await r.json();
        if (d.error) { console.warn('[Stories DALL-E]', d.error.message); return null; }
        const imgUrl = d.data?.[0]?.url;
        if (!imgUrl) return null;
        const imgR = await fetch(imgUrl);
        return await loadImage(Buffer.from(await imgR.arrayBuffer()));
    } catch (e) {
        console.warn('[Stories DALL-E] Error:', e.message);
        return null;
    }
}

// ── Utilidades ────────────────────────────────────────────────────────────────
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
        if (ctx.measureText(test).width > maxWidth && line) { lines.push(line); line = word; }
        else line = test;
    }
    if (line) lines.push(line);
    lines.forEach((l, i) => ctx.fillText(l, x, y + i * lineHeight));
    return lines.length;
}

// Checkmark dibujado a mano (sin emojis)
function drawCheck(ctx, cx, cy, size) {
    ctx.save();
    ctx.strokeStyle = C.green;
    ctx.lineWidth   = size * 0.18;
    ctx.lineCap     = 'round';
    ctx.lineJoin    = 'round';
    ctx.beginPath();
    ctx.moveTo(cx - size * 0.35, cy);
    ctx.lineTo(cx - size * 0.05, cy + size * 0.35);
    ctx.lineTo(cx + size * 0.45, cy - size * 0.35);
    ctx.stroke();
    ctx.restore();
}

// Indicador "sigue →" con flechas
function drawSwipeHint(ctx, text, y) {
    ctx.textAlign = 'center';
    ctx.font = '26px SpaceGroteskRegular';
    ctx.fillStyle = 'rgba(155, 95, 255, 0.65)';
    ctx.fillText(text, W / 2, y);
    // Línea decorativa debajo
    const gl = ctx.createLinearGradient(W/2 - 120, 0, W/2 + 120, 0);
    gl.addColorStop(0, 'rgba(155,95,255,0)');
    gl.addColorStop(0.5, 'rgba(155,95,255,0.4)');
    gl.addColorStop(1, 'rgba(155,95,255,0)');
    ctx.fillStyle = gl;
    ctx.fillRect(W/2 - 120, y + 10, 240, 1);
    ctx.textAlign = 'left';
}

// ── Fondo 1080×1920 ───────────────────────────────────────────────────────────
function drawStoryBg(ctx) {
    ctx.fillStyle = C.bg;
    ctx.fillRect(0, 0, W, H);

    ctx.fillStyle = 'rgba(90, 0, 184, 0.08)';
    for (let x = 40; x < W; x += 60)
        for (let y = 40; y < H; y += 60) {
            ctx.beginPath(); ctx.arc(x, y, 1.5, 0, Math.PI * 2); ctx.fill();
        }

    const g1 = ctx.createRadialGradient(W/2, 0, 0, W/2, 0, 800);
    g1.addColorStop(0, 'rgba(90, 0, 184, 0.5)');
    g1.addColorStop(1, 'rgba(90, 0, 184, 0)');
    ctx.fillStyle = g1; ctx.fillRect(0, 0, W, H);

    const g2 = ctx.createRadialGradient(W/2, H, 0, W/2, H, 700);
    g2.addColorStop(0, 'rgba(123, 47, 224, 0.45)');
    g2.addColorStop(1, 'rgba(123, 47, 224, 0)');
    ctx.fillStyle = g2; ctx.fillRect(0, 0, W, H);

    const gl = ctx.createLinearGradient(0, 0, W, 0);
    gl.addColorStop(0, 'rgba(90,0,184,0)');
    gl.addColorStop(0.5, 'rgba(155,95,255,1)');
    gl.addColorStop(1, 'rgba(90,0,184,0)');
    ctx.fillStyle = gl; ctx.fillRect(0, 0, W, 3);
}

async function drawStoryHeader(ctx) {
    try {
        const lp = path.join(ASSETS, 'logo_aicompany_icon.png');
        if (fs.existsSync(lp)) {
            const logo = await loadImage(lp);
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

function drawStoryFooter(ctx) {
    const gl = ctx.createLinearGradient(0, 0, W, 0);
    gl.addColorStop(0, 'rgba(90,0,184,0)');
    gl.addColorStop(0.5, 'rgba(155,95,255,0.6)');
    gl.addColorStop(1, 'rgba(90,0,184,0)');
    ctx.fillStyle = gl; ctx.fillRect(0, H - 3, W, 3);
    ctx.font = '24px SpaceGroteskRegular';
    ctx.fillStyle = C.gray;
    ctx.textAlign = 'center';
    ctx.fillText('aicompanyco.com', W/2, H - 48);
    ctx.textAlign = 'left';
}

// ── FRAME 1: HOOK ─────────────────────────────────────────────────────────────
async function renderHook(data, ilustracion) {
    const canvas = createCanvas(W, H);
    const ctx    = canvas.getContext('2d');
    drawStoryBg(ctx);
    await drawStoryHeader(ctx);
    ctx.textAlign = 'center';

    // Badge
    const tag = data.tag || 'DATO IMPACTANTE';
    ctx.font = '28px SpaceGrotesk';
    const tagW = ctx.measureText(tag).width + 56;
    roundRect(ctx, (W - tagW)/2, 195, tagW, 58, 29);
    ctx.fillStyle = 'rgba(90, 0, 184, 0.35)'; ctx.fill();
    roundRect(ctx, (W - tagW)/2, 195, tagW, 58, 29);
    ctx.strokeStyle = C.purpleXL; ctx.lineWidth = 1.5; ctx.stroke();
    ctx.fillStyle = C.purpleXL;
    ctx.fillText(tag, W/2, 237);

    if (ilustracion) {
        ctx.drawImage(ilustracion, W/2 - 260, 290, 520, 520);
        const baseY = 870;
        ctx.font = `bold 76px Orbitron`;
        ctx.fillStyle = C.white;
        const hl = wrapText(ctx, data.hook || '', W/2, baseY, W - 120, 92);
        if (data.acento) {
            const g = ctx.createLinearGradient(160, 0, W-160, 0);
            g.addColorStop(0, C.purpleXL); g.addColorStop(1, C.purpleL);
            ctx.font = `bold 100px Orbitron`; ctx.fillStyle = g;
            ctx.fillText(data.acento, W/2, baseY + hl * 92 + 20);
        }
        ctx.font = '34px SpaceGroteskRegular'; ctx.fillStyle = C.grayL;
        wrapText(ctx, data.subtitulo || '', W/2, baseY + hl * 92 + (data.acento ? 130 : 20), W - 140, 50);
    } else {
        // Sin DALL-E: círculo con stat grande como visual principal
        const cx = W/2, cy = 600, rad = 230;
        // Glow
        const gGlow = ctx.createRadialGradient(cx, cy, 0, cx, cy, rad + 80);
        gGlow.addColorStop(0, 'rgba(90, 0, 184, 0.55)');
        gGlow.addColorStop(0.6, 'rgba(90, 0, 184, 0.15)');
        gGlow.addColorStop(1, 'rgba(90, 0, 184, 0)');
        ctx.fillStyle = gGlow; ctx.fillRect(0, cy - rad - 100, W, (rad + 100) * 2);
        // Anillo exterior
        ctx.beginPath(); ctx.arc(cx, cy, rad + 20, 0, Math.PI * 2);
        ctx.strokeStyle = 'rgba(155, 95, 255, 0.15)'; ctx.lineWidth = 2; ctx.stroke();
        // Círculo principal
        ctx.beginPath(); ctx.arc(cx, cy, rad, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(90, 0, 184, 0.2)'; ctx.fill();
        ctx.strokeStyle = 'rgba(155, 95, 255, 0.6)'; ctx.lineWidth = 2; ctx.stroke();
        // Stat dentro del círculo
        if (data.acento) {
            const gs = ctx.createLinearGradient(cx - rad, 0, cx + rad, 0);
            gs.addColorStop(0, C.purpleXL); gs.addColorStop(1, C.white);
            ctx.font = `bold 130px Orbitron`; ctx.fillStyle = gs;
            ctx.fillText(data.acento, cx, cy + 46);
        } else {
            ctx.font = '120px SpaceGrotesk'; ctx.fillStyle = C.purpleXL;
            ctx.fillText('?', cx, cy + 46);
        }

        // Pregunta debajo del círculo
        ctx.font = `bold 74px Orbitron`; ctx.fillStyle = C.white;
        const hl = wrapText(ctx, data.hook || '', W/2, 900, W - 120, 90);
        ctx.font = '34px SpaceGroteskRegular'; ctx.fillStyle = C.grayL;
        wrapText(ctx, data.subtitulo || '', W/2, 900 + hl * 90 + 30, W - 140, 50);
    }

    // Engagement: "¿Te pasa esto?" + swipe hint
    ctx.font = '30px SpaceGrotesk';
    ctx.fillStyle = 'rgba(255,255,255,0.5)';
    ctx.fillText('¿Te pasa esto?  Responde este mensaje', W/2, H - 160);
    drawSwipeHint(ctx, 'Sigue viendo  >', H - 115);

    ctx.textAlign = 'left';
    drawStoryFooter(ctx);
    return canvas.toBuffer('image/png');
}

// ── FRAME 2: INSIGHT ──────────────────────────────────────────────────────────
async function renderInsight(data) {
    const canvas = createCanvas(W, H);
    const ctx    = canvas.getContext('2d');
    drawStoryBg(ctx);
    await drawStoryHeader(ctx);
    ctx.textAlign = 'center';

    // Número decorativo de fondo — tamaño dinámico según longitud para que no se corte
    if (data.numero) {
        const num = String(data.numero);
        const fs  = num.length <= 2 ? 340 : num.length <= 3 ? 260 : num.length <= 4 ? 200 : 160;
        const gradNum = ctx.createLinearGradient(0, 400, W, 700);
        gradNum.addColorStop(0, 'rgba(90,0,184,0.18)');
        gradNum.addColorStop(0.5, 'rgba(155,95,255,0.18)');
        gradNum.addColorStop(1, 'rgba(90,0,184,0.18)');
        ctx.font = `bold ${fs}px Orbitron`;
        ctx.fillStyle = gradNum;
        // Clip para que no se salga de pantalla
        ctx.save();
        ctx.rect(0, 0, W, H); ctx.clip();
        ctx.fillText(num, W/2, 720);
        ctx.restore();
    }

    // Separador
    const gs = ctx.createLinearGradient(0, 0, W, 0);
    gs.addColorStop(0, 'rgba(90,0,184,0)'); gs.addColorStop(0.5, C.purpleXL); gs.addColorStop(1, 'rgba(90,0,184,0)');
    ctx.fillStyle = gs; ctx.fillRect(W/2 - 180, 760, 360, 3);

    // Título
    ctx.font = `bold 72px Orbitron`; ctx.fillStyle = C.white;
    const tl = wrapText(ctx, data.titulo || '', W/2, 830, W - 120, 88);

    // Descripción
    ctx.font = '38px SpaceGroteskRegular'; ctx.fillStyle = C.grayL;
    const dy  = 830 + tl * 88 + 30;
    const dl  = wrapText(ctx, data.descripcion || '', W/2, dy, W - 140, 54);

    // Card extra
    if (data.punto_extra) {
        const cardY = dy + dl * 54 + 60;
        roundRect(ctx, 60, cardY, W - 120, 140, 22);
        ctx.fillStyle = 'rgba(90, 0, 184, 0.22)'; ctx.fill();
        roundRect(ctx, 60, cardY, W - 120, 140, 22);
        ctx.strokeStyle = 'rgba(155, 95, 255, 0.4)'; ctx.lineWidth = 1; ctx.stroke();
        ctx.font = '30px SpaceGroteskRegular'; ctx.fillStyle = C.text;
        wrapText(ctx, data.punto_extra, W/2, cardY + 52, W - 200, 44);
    }

    // Engagement
    ctx.font = '30px SpaceGrotesk'; ctx.fillStyle = 'rgba(255,255,255,0.5)';
    ctx.fillText('¿Cuanto pierdes tu?  Responde este mensaje', W/2, H - 160);
    drawSwipeHint(ctx, 'Ver la solucion  >', H - 115);

    ctx.textAlign = 'left';
    drawStoryFooter(ctx);
    return canvas.toBuffer('image/png');
}

// ── FRAME 3: CTA ──────────────────────────────────────────────────────────────
async function renderStoryCTA(data) {
    const canvas = createCanvas(W, H);
    const ctx    = canvas.getContext('2d');
    drawStoryBg(ctx);

    // Glow central
    const gc = ctx.createRadialGradient(W/2, H/2 - 100, 0, W/2, H/2 - 100, 700);
    gc.addColorStop(0, 'rgba(90, 0, 184, 0.45)'); gc.addColorStop(1, 'rgba(90, 0, 184, 0)');
    ctx.fillStyle = gc; ctx.fillRect(0, 0, W, H);

    ctx.textAlign = 'center';

    // Badge urgencia
    ctx.font = '28px SpaceGrotesk';
    const urgencia = '  CONSULTA GRATIS  ';
    const urgW = ctx.measureText(urgencia).width + 20;
    roundRect(ctx, (W - urgW)/2, 90, urgW, 58, 29);
    ctx.fillStyle = 'rgba(155, 95, 255, 0.25)'; ctx.fill();
    roundRect(ctx, (W - urgW)/2, 90, urgW, 58, 29);
    ctx.strokeStyle = C.purpleXL; ctx.lineWidth = 1.5; ctx.stroke();
    ctx.fillStyle = C.purpleXL; ctx.fillText(urgencia, W/2, 132);

    // Logo
    try {
        const lp = path.join(ASSETS, 'logo_aicompany_icon.png');
        if (fs.existsSync(lp)) {
            const logo = await loadImage(lp);
            ctx.globalAlpha = 0.95;
            ctx.drawImage(logo, W/2 - 75, 200, 150, 150);
            ctx.globalAlpha = 1;
        }
    } catch {}

    ctx.font = '32px SpaceGrotesk'; ctx.fillStyle = C.purpleXL;
    ctx.fillText('AI COMPANY CO', W/2, 400);
    ctx.fillStyle = C.purpleL; ctx.fillRect(W/2 - 80, 418, 160, 2);

    // Pregunta CTA
    ctx.font = `bold 80px Orbitron`; ctx.fillStyle = C.white;
    const cl = wrapText(ctx, data.cta_titulo || '¿Cuanto tiempo pierdes cada semana?', W/2, 480, W - 120, 96);

    // Subtítulo
    ctx.font = '38px SpaceGroteskRegular'; ctx.fillStyle = C.grayL;
    wrapText(ctx, data.cta_sub || 'Automatizamos tu negocio en menos de 1 semana', W/2, 480 + cl * 96 + 24, W - 140, 54);

    // Beneficios con checkmarks dibujados (sin emojis)
    const bens = data.beneficios || ['Respuesta 24/7 sin contratar', 'Mas ventas con menos esfuerzo', 'Listo en menos de 1 semana'];
    let benY = 480 + cl * 96 + 180;
    for (const ben of bens) {
        const pillW = 700, pillH = 68;
        const pillX = (W - pillW) / 2;
        roundRect(ctx, pillX, benY, pillW, pillH, 34);
        ctx.fillStyle = 'rgba(90, 0, 184, 0.28)'; ctx.fill();
        roundRect(ctx, pillX, benY, pillW, pillH, 34);
        ctx.strokeStyle = 'rgba(155, 95, 255, 0.35)'; ctx.lineWidth = 1; ctx.stroke();
        // Checkmark dibujado
        drawCheck(ctx, pillX + 40, benY + 34, 22);
        ctx.font = '30px SpaceGroteskRegular'; ctx.fillStyle = C.text;
        ctx.textAlign = 'left';
        ctx.fillText(ben, pillX + 80, benY + 44);
        ctx.textAlign = 'center';
        benY += 84;
    }

    // Botón WhatsApp
    const btnY = H - 440, btnW = 740, btnH = 96;
    roundRect(ctx, (W - btnW)/2, btnY, btnW, btnH, 48);
    const gb = ctx.createLinearGradient((W - btnW)/2, 0, (W + btnW)/2, 0);
    gb.addColorStop(0, C.greenD); gb.addColorStop(1, C.green);
    ctx.fillStyle = gb; ctx.fill();
    ctx.font = '36px SpaceGrotesk'; ctx.fillStyle = C.white;
    ctx.fillText('Escribenos por WhatsApp', W/2, btnY + 62);

    // Urgencia bajo el botón
    ctx.font = '26px SpaceGroteskRegular'; ctx.fillStyle = 'rgba(255,255,255,0.55)';
    ctx.fillText('Sin compromiso - Respondemos hoy mismo', W/2, btnY + 118);

    // Número de teléfono visible
    ctx.font = '28px SpaceGrotesk'; ctx.fillStyle = C.purpleXL;
    ctx.fillText('3224807767', W/2, H - 110);

    ctx.textAlign = 'left';
    drawStoryFooter(ctx);
    return canvas.toBuffer('image/png');
}

// ── GENERAR CONTENIDO CON CLAUDE ─────────────────────────────────────────────
async function generarContenidoStory(tipo, contexto = '') {
    const client = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

    const prompts = {
        curiosidad: `Crea contenido para 3 historias de Instagram de AI Company CO (agencia de IA en Colombia).

Tipo: CURIOSIDAD — genera reflexión, hace que el empresario piense en su problema.
Tono: Directo, impactante, genera incomodidad positiva. Habla de TU directamente.
${contexto ? `Tema: ${contexto}` : ''}

Responde SOLO con JSON (sin markdown):
{
  "ilustracion_prompt": "describe in english a 3D character illustration (max 10 words)",
  "frame1": {
    "tag": "DATO DEL DIA",
    "hook": "pregunta corta impactante (max 22 chars)",
    "acento": "cifra impactante (ej: 40%, 3h, 2x)",
    "subtitulo": "contexto del dato (max 65 chars)"
  },
  "frame2": {
    "numero": "stat corto max 5 chars (ej: 40%, 8h)",
    "titulo": "lo que significa ese numero (max 28 chars)",
    "descripcion": "impacto real en el negocio (max 90 chars)",
    "punto_extra": "consecuencia si no lo resuelves (max 70 chars)"
  },
  "frame3": {
    "cta_titulo": "pregunta directa al dolor (max 32 chars)",
    "cta_sub": "promesa de solucion concreta (max 48 chars)",
    "beneficios": ["beneficio 1 (max 28 chars)", "beneficio 2", "beneficio 3"]
  }
}`,

        educativo: `Crea contenido para 3 historias de Instagram de AI Company CO (agencia de IA en Colombia).

Tipo: EDUCATIVO — enseña algo práctico sobre IA/automatización, genera autoridad.
Tono: Experto, claro, genera curiosidad y confianza. Da valor real.
${contexto ? `Tema: ${contexto}` : ''}

Responde SOLO con JSON (sin markdown):
{
  "ilustracion_prompt": "describe in english a 3D character illustration (max 10 words)",
  "frame1": {
    "tag": "TIP DEL DIA",
    "hook": "titulo del tip (max 22 chars)",
    "acento": "numero o stat relevante (o null)",
    "subtitulo": "beneficio concreto del tip (max 65 chars)"
  },
  "frame2": {
    "numero": "dato clave (o null)",
    "titulo": "el insight central (max 28 chars)",
    "descripcion": "explicacion practica de como funciona (max 90 chars)",
    "punto_extra": "ejemplo real o caso de uso en Colombia (max 70 chars)"
  },
  "frame3": {
    "cta_titulo": "oferta de implementacion (max 32 chars)",
    "cta_sub": "te lo construimos en 1 semana (max 48 chars)",
    "beneficios": ["beneficio 1 (max 28 chars)", "beneficio 2", "beneficio 3"]
  }
}`,

        ventas: `Crea contenido para 3 historias de Instagram de AI Company CO vendiendo servicios de IA.

Tipo: VENTAS — genera urgencia, muestra resultados, cierra con accion directa.
Tono: Urgente, orientado a resultados, concreto. Sin rodeos.
${contexto ? `Servicio: ${contexto}` : ''}

Responde SOLO con JSON (sin markdown):
{
  "ilustracion_prompt": "describe in english a 3D character showing success/money/growth (max 10 words)",
  "frame1": {
    "tag": "RESULTADO REAL",
    "hook": "resultado logrado (max 22 chars)",
    "acento": "cifra del resultado (ej: +200%)",
    "subtitulo": "contexto breve del resultado (max 65 chars)"
  },
  "frame2": {
    "numero": "resultado numerico clave",
    "titulo": "que logramos exactamente (max 28 chars)",
    "descripcion": "como lo logramos y en cuanto tiempo (max 90 chars)",
    "punto_extra": "tu negocio puede lograr lo mismo (max 70 chars)"
  },
  "frame3": {
    "cta_titulo": "llamada urgente a actuar (max 32 chars)",
    "cta_sub": "empieza esta semana, sin riesgo (max 48 chars)",
    "beneficios": ["beneficio 1 (max 28 chars)", "beneficio 2", "beneficio 3"]
  }
}`
    };

    const resp = await client.messages.create({
        model:      'claude-haiku-4-5-20251001',
        max_tokens: 1400,
        messages:   [{ role: 'user', content: prompts[tipo] || prompts.curiosidad }]
    });

    const raw = resp.content[0]?.text?.trim() || '';

    // Claude a veces devuelve array [...] o objeto anidado {historia_1:...}
    const matchArr = raw.match(/\[[\s\S]*\]/);
    const matchObj = raw.match(/\{[\s\S]*\}/);

    let parsed;
    if (matchArr) {
        const clean = matchArr[0].replace(/,\s*([}\]])/g, '$1').replace(/\/\/[^\n]*/g, '');
        const arr   = JSON.parse(clean);
        // Usar el primer elemento del array
        parsed = Array.isArray(arr) ? arr[0] : arr;
    } else if (matchObj) {
        const clean  = matchObj[0].replace(/,\s*([}\]])/g, '$1').replace(/\/\/[^\n]*/g, '');
        parsed = JSON.parse(clean);
        // Si devolvio estructura anidada historia_1.frame1...
        if (parsed.historia_1) {
            parsed = {
                ilustracion_prompt: parsed.historia_1.ilustracion_prompt,
                frame1: parsed.historia_1.frame1,
                frame2: parsed.historia_1.frame2,
                frame3: parsed.historia_1.frame3,
            };
        }
    } else {
        throw new Error('Claude no devolvio JSON para story');
    }
    return parsed;
}

// ── RENDERIZAR 3 FRAMES ───────────────────────────────────────────────────────
async function renderizarStories(contenido) {
    const ilustracion = await generarIlustracion(contenido.ilustracion_prompt || 'friendly businessman thinking about automation');
    return [
        await renderHook(contenido.frame1 || {}, ilustracion),
        await renderInsight(contenido.frame2 || {}),
        await renderStoryCTA(contenido.frame3 || {}),
    ];
}

// ── TEMP files ────────────────────────────────────────────────────────────────
function guardarTemp(buffers, prefijo) {
    if (!fs.existsSync(TEMP)) fs.mkdirSync(TEMP, { recursive: true });
    return buffers.map((buf, i) => {
        const nombre = `${prefijo}_story${i+1}_${Date.now()}.png`;
        const ruta   = path.join(TEMP, nombre);
        fs.writeFileSync(ruta, buf);
        return { nombre, ruta };
    });
}

function limpiarTemp(archivos) {
    archivos.forEach(({ ruta }) => { try { fs.unlinkSync(ruta); } catch {} });
}

// ── PUBLICAR EN INSTAGRAM ─────────────────────────────────────────────────────
async function publicarStoriesInstagram(archivos) {
    const token   = process.env.INSTAGRAM_TOKEN || process.env.WHATSAPP_TOKEN;
    const userId  = process.env.INSTAGRAM_USER_ID;
    const baseUrl = (process.env.RAILWAY_PUBLIC_URL || process.env.ALLOWED_ORIGIN || '').replace(/\/$/, '');
    if (!token || !userId) return [];

    const publicados = [];
    const apiBase    = `https://graph.facebook.com/v21.0/${userId}`;

    for (const arch of archivos) {
        try {
            const imageUrl = `${baseUrl}/api/stories/temp/${arch.nombre}`;
            const r = await fetch(`${apiBase}/media`, {
                method: 'POST', headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
                body: JSON.stringify({ image_url: imageUrl, media_type: 'STORIES', access_token: token })
            });
            const d = await r.json();
            if (d.error) throw new Error(d.error.message);
            await new Promise(res => setTimeout(res, 1500));
            const pub = await fetch(`${apiBase}/media_publish`, {
                method: 'POST', headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
                body: JSON.stringify({ creation_id: d.id, access_token: token })
            });
            const pd = await pub.json();
            if (pd.error) throw new Error(pd.error.message);
            publicados.push(pd.id);
        } catch (e) {
            console.warn(`[Stories] Error publicando ${arch.nombre}:`, e.message);
        }
    }
    return publicados;
}

// ── TELEGRAM ──────────────────────────────────────────────────────────────────
async function enviarTelegram(buffers, contenido, tipo, publicadoEnIG) {
    const tok  = process.env.PLATAFORMA_TELEGRAM_TOKEN;
    const chat = process.env.PLATAFORMA_TELEGRAM_CHAT_ID;
    if (!tok || !chat) return;

    const emoji  = { curiosidad: '🤔', educativo: '💡', ventas: '🚀' }[tipo] || '📖';
    const estado = publicadoEnIG
        ? '✅ *Publicado automáticamente en Instagram*'
        : '📲 *Sube estas 3 imágenes como historias en Instagram (en este orden)*';

    await fetch(`https://api.telegram.org/bot${tok}/sendMessage`, {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            chat_id: chat,
            text: `${emoji} *Historias AI Company CO — ${tipo.toUpperCase()}*\n\n📌 *"${contenido.frame1?.hook || ''}"*\n🖼 3 frames listos\n\n${estado}`,
            parse_mode: 'Markdown'
        })
    });

    const labels = ['Frame 1/3 — Hook (primera historia)', 'Frame 2/3 — Insight (segunda)', 'Frame 3/3 — CTA (tercera)'];
    for (let i = 0; i < buffers.length; i++) {
        const fd = new FormData();
        fd.append('chat_id', chat);
        fd.append('photo', new Blob([buffers[i]], { type: 'image/png' }), `historia_${i+1}.png`);
        fd.append('caption', labels[i]);
        await fetch(`https://api.telegram.org/bot${tok}/sendPhoto`, { method: 'POST', body: fd });
        await new Promise(r => setTimeout(r, 400));
    }
    console.log('[Stories] 3 frames enviados a Telegram');
}

// ── PIPELINE COMPLETO ─────────────────────────────────────────────────────────
async function generarYPublicarStory(tipo = 'curiosidad', contexto = '') {
    console.log(`[Stories] Generando historias tipo: ${tipo}`);
    const contenido = await generarContenidoStory(tipo, contexto);
    console.log(`[Stories] Contenido: "${contenido.frame1?.hook}"`);

    const buffers  = await renderizarStories(contenido);
    const archivos = guardarTemp(buffers, `story_${tipo}`);

    let publicados = [];
    try { publicados = await publicarStoriesInstagram(archivos); } catch {}

    try { await enviarTelegram(buffers, contenido, tipo, publicados.length > 0); }
    catch (e) { console.error('[Stories] Telegram error:', e.message); }

    setTimeout(() => limpiarTemp(archivos), 10 * 60 * 1000);
    return { contenido, frames: buffers.length, publicados };
}

function servirImagenTemp(req, res) {
    const nombre = req.params.nombre?.replace(/[^a-zA-Z0-9_.\-]/g, '');
    const ruta   = path.join(TEMP, nombre);
    if (!fs.existsSync(ruta)) return res.status(404).json({ ok: false });
    res.setHeader('Content-Type', 'image/png');
    res.sendFile(ruta);
}

module.exports = { generarYPublicarStory, servirImagenTemp, renderizarStories, generarContenidoStory };
