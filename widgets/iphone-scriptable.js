// Widget de pantalla de inicio para iPhone — se usa con la app Scriptable (gratis).
//
// CÓMO INSTALARLO (una sola vez):
//   1. Instala "Scriptable" del App Store.
//   2. Abre Scriptable, toca "+", pega este archivo completo, ponle nombre "AI Company".
//   3. Cambia CLAVE por el valor de WIDGET_KEY que está en Railway.
//   4. En la pantalla de inicio: mantén presionado → "+" → busca Scriptable →
//      elige el tamaño mediano → toca el widget → Script: "AI Company".
//
// Se actualiza solo cada ~15 minutos. Al tocarlo abre la app.

const CLAVE = 'PEGA_AQUI_TU_WIDGET_KEY';
const URL   = 'https://mi-plataforma-production.up.railway.app';

const cop = n => '$' + Math.round(n).toLocaleString('es-CO');

async function datos() {
    const r = new Request(`${URL}/api/widget?key=${CLAVE}`);
    r.timeoutInterval = 10;
    return await r.loadJSON();
}

const w = new ListWidget();
w.backgroundColor = new Color('#F8FAFC');
w.setPadding(14, 16, 12, 16);
w.url = `${URL}/hoy`;   // tocar el widget abre Inicio

let d;
try { d = await datos(); } catch (e) { d = null; }

if (!d || !d.ok) {
    const t = w.addText(d && d.msg ? d.msg : 'Sin conexión');
    t.font = Font.mediumSystemFont(13); t.textColor = new Color('#94A3B8');
} else {
    // ── Fila 1: lo que queda libre, grande ──
    const r1 = w.addText('TE QUEDA LIBRE');
    r1.font = Font.boldSystemFont(9); r1.textColor = new Color('#94A3B8');
    const v1 = w.addText(cop(d.libre));
    v1.font = Font.boldSystemFont(26);
    v1.textColor = new Color(d.libre >= 0 ? '#0F172A' : '#DC2626');

    w.addSpacer(6);

    // ── Fila 2: te deben · deuda ──
    const fila = w.addStack(); fila.layoutHorizontally(); fila.centerAlignContent();
    const c1 = fila.addStack(); c1.layoutVertically();
    const l1 = c1.addText('TE DEBEN'); l1.font = Font.boldSystemFont(8); l1.textColor = new Color('#94A3B8');
    const n1 = c1.addText(cop(d.te_deben)); n1.font = Font.semiboldSystemFont(14); n1.textColor = new Color('#D97706');
    fila.addSpacer();
    const c2 = fila.addStack(); c2.layoutVertically();
    const l2 = c2.addText('DEUDA'); l2.font = Font.boldSystemFont(8); l2.textColor = new Color('#94A3B8');
    const n2 = c2.addText(cop(d.deuda)); n2.font = Font.semiboldSystemFont(14); n2.textColor = new Color('#DC2626');

    w.addSpacer(8);

    // ── Fila 3: pendientes ──
    if (d.pendientes === 0) {
        const t = w.addText('✓ Nada pendiente hoy');
        t.font = Font.mediumSystemFont(12); t.textColor = new Color('#16A34A');
    } else {
        const colores = { alta: '#DC2626', media: '#D97706', baja: '#2563EB' };
        for (const a of d.acciones) {
            const s = w.addStack(); s.layoutHorizontally(); s.centerAlignContent();
            const punto = s.addText('●'); punto.font = Font.systemFont(8); punto.textColor = new Color(colores[a.u] || '#94A3B8');
            s.addSpacer(6);
            const t = s.addText(a.t); t.font = Font.systemFont(11.5); t.textColor = new Color('#334155'); t.lineLimit = 1;
        }
        if (d.pendientes > 3) {
            const m = w.addText(`+${d.pendientes - 3} más`);
            m.font = Font.systemFont(10); m.textColor = new Color('#94A3B8');
        }
    }
}

w.refreshAfterDate = new Date(Date.now() + 15 * 60 * 1000);
Script.setWidget(w);
Script.complete();
if (config.runsInApp) w.presentMedium();
