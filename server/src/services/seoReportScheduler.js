const cron          = require('node-cron');
const https         = require('https');
const telegramService = require('./telegramService');

const GITHUB_RAW = 'https://raw.githubusercontent.com/cesargranados0100-alt/AI-COMPANY/main';
const GITHUB_API = 'https://api.github.com/repos/cesargranados0100-alt/AI-COMPANY/contents/blog';

function fetchJson(url) {
    return new Promise((resolve, reject) => {
        https.get(url, { headers: { 'User-Agent': 'mi-plataforma-seo-bot/1.0' } }, (res) => {
            let data = '';
            res.on('data', c => data += c);
            res.on('end', () => {
                try { resolve(JSON.parse(data)); }
                catch (e) { reject(new Error('JSON parse error: ' + data.slice(0, 100))); }
            });
        }).on('error', reject);
    });
}

async function obtenerDatosBlog() {
    const [topics, dirs] = await Promise.all([
        fetchJson(`${GITHUB_RAW}/scripts/blog-topics.json`),
        fetchJson(GITHUB_API),
    ]);
    const publicados = new Set(
        Array.isArray(dirs) ? dirs.filter(d => d.type === 'dir').map(d => d.name) : []
    );
    return { topics, publicados };
}

function proximoLunesJueves() {
    const hoy = new Date();
    const dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    // Siguiente lunes (1) o jueves (4)
    let d = new Date(hoy);
    for (let i = 1; i <= 7; i++) {
        d = new Date(hoy);
        d.setDate(hoy.getDate() + i);
        if (d.getDay() === 1 || d.getDay() === 4) break;
    }
    return d.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', timeZone: 'America/Bogota' });
}

async function generarReporte() {
    try {
        const { topics, publicados } = await obtenerDatosBlog();
        const total     = topics.length;
        const numPub    = topics.filter(t => publicados.has(t.slug)).length;
        const numPend   = total - numPub;
        const pct       = Math.round((numPub / total) * 100);
        const barFull   = Math.round(pct / 10);
        const bar       = '█'.repeat(barFull) + '░'.repeat(10 - barFull);

        const proximos  = topics.filter(t => !publicados.has(t.slug)).slice(0, 3);
        const proxLinea = proximos.map((t, i) => `   ${i + 1}. ${t.titulo.slice(0, 55)}...`).join('\n');
        const proxPub   = proximoLunesJueves();

        const fechaHoy  = new Date().toLocaleDateString('es-CO', {
            day: 'numeric', month: 'long', year: 'numeric', timeZone: 'America/Bogota'
        });

        const msg =
`📊 *Reporte SEO — AI Company CO*
📅 ${fechaHoy}

*Blog aicompanyco.com*
${bar} ${pct}%
📝 Publicados: *${numPub}* de ${total} temas
⏳ Pendientes: *${numPend}*

*Próxima publicación:* ${proxPub}

*Próximos 3 artículos en cola:*
${proxLinea}

*Páginas de ciudades activas:*
✅ Bogotá · Medellín · Cali
✅ Barranquilla · Bucaramanga

*Checklist quincenal:*
🔲 Revisar Google Search Console → posición promedio
🔲 Solicitar indexación de artículos nuevos
🔲 Verificar Core Web Vitals
🔲 Registrar en un directorio nuevo (Clutch, GBP, etc.)

🔗 [Ver Search Console](https://search.google.com/search-console/performance/search-analytics?resource_id=sc-domain%3Aaicompanyco.com)
🌐 [Ver sitio](https://aicompanyco.com)`;

        await telegramService.enviar(msg);
        console.log('[SEO Report] Reporte enviado a Telegram');
        return { ok: true, publicados: numPub, total, pendientes: numPend };
    } catch (e) {
        console.error('[SEO Report] Error:', e.message);
        return { ok: false, error: e.message };
    }
}

function iniciarSeoReportScheduler() {
    // Corre el día 1 y 15 de cada mes a las 9am hora Colombia (UTC-5 = 14:00 UTC)
    cron.schedule('0 14 1,15 * *', () => {
        console.log('[SEO Report] Enviando reporte quincenal...');
        generarReporte();
    }, { timezone: 'America/Bogota' });

    console.log('📊 SEO Report Scheduler activo (días 1 y 15 de cada mes)');
}

module.exports = { iniciarSeoReportScheduler, generarReporte };
