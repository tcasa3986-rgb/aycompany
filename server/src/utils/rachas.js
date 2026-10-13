// Cuentas de los hábitos: rachas y cumplimiento.
//
// Va aparte del controlador para poder probarlo sin base de datos. Una racha
// mal contada es peor que no tenerla: si el sistema dice "llevas 12 días" y uno
// sabe que falló el jueves, deja de creerle a todo lo demás.

const { aFecha, aStr, hoyBogota } = require('./licenciaCiclo');

const DIA = 86400000;

/** Resta días a una fecha "2026-10-13" y devuelve otra fecha igual de texto. */
function menosDias(fecha, n) {
    return aStr(new Date(aFecha(fecha).getTime() - n * DIA));
}

/** 0 = domingo … 6 = sábado, en el calendario, sin líos de zona horaria. */
function diaSemana(fecha) {
    return aFecha(fecha).getUTCDay();
}

/** ¿El hábito aplica ese día? `dias` viene como "1,2,3,4,5". */
function aplicaEn(dias, fecha) {
    const lista = String(dias == null ? '' : dias).split(',').map(s => s.trim()).filter(Boolean);
    if (lista.length === 0) return true;
    return lista.includes(String(diaSemana(fecha)));
}

/**
 * ¿Ese día quedó cumplido?
 * - 'hacer'  → se hizo al menos las veces de la meta.
 * - 'evitar' → se cumple si NO se cayó. Un día sin registro es un día limpio.
 */
function cumplido(habito, registro) {
    if (habito.tipo === 'evitar') return !(registro && registro.cai);
    if (!registro) return false;
    return Number(registro.veces || 0) >= Number(habito.meta_diaria || 1);
}

/**
 * Racha actual: días seguidos cumplidos, contando hacia atrás y **saltando los
 * días en que el hábito no aplica** (si es de lunes a viernes, el sábado no la
 * rompe).
 *
 * El día de hoy es un caso especial: si todavía no se ha cumplido, no cuenta
 * como roto — el día no ha terminado. Se empieza a contar desde ayer.
 *
 * @param {object} habito  { tipo, dias, meta_diaria }
 * @param {Map<string,object>} porFecha  fecha "YYYY-MM-DD" -> registro
 * @param {string} hoy
 */
function rachaActual(habito, porFecha, hoy = hoyBogota()) {
    let racha = 0;

    // El día de hoy solo recibe el beneficio de la duda mientras esté SIN
    // DECIDIR. No es lo mismo "todavía no lo he hecho" que "ya caí":
    //   - 'hacer' sin cumplir  → indeciso, el día no ha terminado, no rompe.
    //   - 'evitar' marcado caí → decidido, y la racha se va a cero hoy mismo.
    if (aplicaEn(habito.dias, hoy)) {
        const reg = porFecha.get(hoy);
        if (cumplido(habito, reg)) racha = 1;
        else if (habito.tipo === 'evitar' && reg && reg.cai) return 0;
    }

    // No se cuenta más atrás del día en que nació el hábito: si no, uno de
    // 'evitar' recién creado diría "llevas 730 días sin caer", que es mentira y
    // además quita todo el mérito de empezar.
    const desde = habito.desde ? String(habito.desde).slice(0, 10) : null;

    let fecha = menosDias(hoy, 1);
    for (let i = 0; i < 730; i++) {
        if (desde && fecha < desde) break;
        if (!aplicaEn(habito.dias, fecha)) { fecha = menosDias(fecha, 1); continue; }
        if (!cumplido(habito, porFecha.get(fecha))) break;
        racha++;
        fecha = menosDias(fecha, 1);
    }
    return racha;
}

/** La racha más larga que ha tenido, mirando todo el historial que se le pase. */
function mejorRacha(habito, porFecha, hoy = hoyBogota()) {
    const desde = habito.desde ? String(habito.desde).slice(0, 10) : null;
    const fechas = [...porFecha.keys()].sort();
    if (fechas.length === 0 && !desde) return 0;

    let mejor = 0, actual = 0;
    // Se arranca en el nacimiento del hábito: antes de eso no hay nada que contar,
    // ni siquiera para los de 'evitar', que se cumplen por omisión.
    let fecha = desde || fechas[0];
    if (fechas.length && fechas[0] > fecha && !desde) fecha = fechas[0];
    while (fecha <= hoy) {
        if (aplicaEn(habito.dias, fecha)) {
            if (cumplido(habito, porFecha.get(fecha))) { actual++; if (actual > mejor) mejor = actual; }
            else actual = 0;
        }
        fecha = aStr(new Date(aFecha(fecha).getTime() + DIA));
    }
    return mejor;
}

/**
 * Porcentaje de cumplimiento en los últimos N días, contando SOLO los días en
 * que el hábito aplicaba. Si es de 3 días a la semana, el denominador son esos
 * días, no 30.
 */
function cumplimiento(habito, porFecha, dias = 30, hoy = hoyBogota()) {
    // Igual que en la racha: nada antes del día en que nació el hábito. Si no,
    // uno de 'evitar' creado hoy diría "97% este mes" por días en los que
    // todavía no existía — un número que no se ganó.
    const desde = habito.desde ? String(habito.desde).slice(0, 10) : null;
    let aplicables = 0, hechos = 0;
    for (let i = 0; i < dias; i++) {
        const f = menosDias(hoy, i);
        if (desde && f < desde) break;
        if (!aplicaEn(habito.dias, f)) continue;
        aplicables++;
        if (cumplido(habito, porFecha.get(f))) hechos++;
    }
    return { aplicables, hechos, pct: aplicables ? Math.round((hechos / aplicables) * 100) : 0 };
}

/** Los últimos N días en orden, para pintar la tira de puntos. */
function ultimosDias(habito, porFecha, dias = 7, hoy = hoyBogota()) {
    const out = [];
    for (let i = dias - 1; i >= 0; i--) {
        const f = menosDias(hoy, i);
        const aplica = aplicaEn(habito.dias, f);
        out.push({
            fecha: f,
            aplica,
            hecho: aplica ? cumplido(habito, porFecha.get(f)) : null,
            esHoy: f === hoy,
            veces: Number(porFecha.get(f)?.veces || 0),
        });
    }
    return out;
}

module.exports = { menosDias, diaSemana, aplicaEn, cumplido, rachaActual, mejorRacha, cumplimiento, ultimosDias };
