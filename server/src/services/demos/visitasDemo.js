const crypto = require('crypto');
const TTL = 30 * 60 * 1000;
const firma = (slug, until, secret) => crypto.createHmac('sha256', secret).update(`${slug}:${until}`).digest('hex');
function tokenVisita(slug, secret, now = Date.now()) {
    if (!secret) return null;
    const until = now + TTL;
    return `${until}.${firma(slug, until, secret)}`;
}
function tokenValido(token, slug, secret, now = Date.now()) {
    if (!secret || typeof token !== 'string' || !/^\d{13}\.[a-f0-9]{64}$/.test(token)) return false;
    const [until, sig] = token.split('.');
    if (Number(until) <= now || Number(until) > now + TTL) return false;
    return crypto.timingSafeEqual(Buffer.from(sig), Buffer.from(firma(slug, until, secret)));
}
const nombreCookie = slug => 'demo_visit_'+crypto.createHash('sha256').update(slug).digest('hex').slice(0, 14);
function yaContada(req, slug, secret) {
    const cookie = String(req.headers.cookie || '').split(';').map(c=>c.trim()).find(c=>c.startsWith(nombreCookie(slug)+'='));
    return cookie ? tokenValido(cookie.slice(cookie.indexOf('=')+1), slug, secret) : false;
}
function conMedicion(html, slug, token) {
    if (!token) return html;
    // Sólo una interacción visible: ni HEAD, ni GET, ni la vista interna suman.
    const script = `<script>(()=>{let counted=false;const track=()=>{if(counted||document.visibilityState!=='visible')return;counted=true;fetch('/demo/'+${JSON.stringify(encodeURIComponent(slug))}+'/visita',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({token:${JSON.stringify(token)}}),keepalive:true}).catch(()=>{});};['pointerdown','keydown'].forEach(e=>document.addEventListener(e,track,{once:true}));})();</script>`;
    return html.replace('</body>', script+'</body>');
}
module.exports = { tokenVisita, tokenValido, nombreCookie, yaContada, conMedicion, TTL };
