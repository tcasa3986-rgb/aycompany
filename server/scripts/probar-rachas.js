// Pruebas de las cuentas de hábitos. No tocan la base de datos.
//   node server/scripts/probar-rachas.js
const { rachaActual, mejorRacha, cumplimiento, ultimosDias, aplicaEn } = require('../src/utils/rachas');

const HOY = '2026-10-13';   // martes
let ok = 0, fallos = [];

function prueba(nombre, real, esperado) {
    if (JSON.stringify(real) === JSON.stringify(esperado)) { ok++; console.log('  ok  ', nombre); }
    else fallos.push([nombre, JSON.stringify(esperado), JSON.stringify(real)]);
}

/** Arma el mapa de registros a partir de una lista de fechas hechas. */
const mapa = (fechas, campo = 'veces', valor = 1) => {
    const m = new Map();
    for (const f of fechas) m.set(f, { [campo]: valor });
    return m;
};

const diario   = { tipo: 'hacer', dias: '0,1,2,3,4,5,6', meta_diaria: 1 };
const laboral  = { tipo: 'hacer', dias: '1,2,3,4,5',     meta_diaria: 1 };
const evitar   = { tipo: 'evitar', dias: '0,1,2,3,4,5,6', meta_diaria: 1 };
const agua     = { tipo: 'hacer', dias: '0,1,2,3,4,5,6', meta_diaria: 8 };

console.log('— racha de un hábito diario —');
prueba('hoy + 3 días atrás = 4',
    rachaActual(diario, mapa(['2026-10-13','2026-10-12','2026-10-11','2026-10-10']), HOY), 4);
prueba('hoy sin marcar no rompe: cuenta desde ayer',
    rachaActual(diario, mapa(['2026-10-12','2026-10-11','2026-10-10']), HOY), 3);
prueba('falló ayer: racha 0',
    rachaActual(diario, mapa(['2026-10-11','2026-10-10']), HOY), 0);
prueba('solo hoy = 1', rachaActual(diario, mapa(['2026-10-13']), HOY), 1);
prueba('nada = 0', rachaActual(diario, new Map(), HOY), 0);

console.log('— el fin de semana no rompe un hábito de lunes a viernes —');
// 13 mar, 12 lun; 11 dom y 10 sáb NO aplican; 9 vie, 8 jue
prueba('salta sábado y domingo',
    rachaActual(laboral, mapa(['2026-10-13','2026-10-12','2026-10-09','2026-10-08']), HOY), 4);
prueba('sin marcar el finde igual sigue la racha',
    rachaActual(laboral, mapa(['2026-10-12','2026-10-09','2026-10-08']), HOY), 3);
prueba('domingo no aplica al hábito laboral', aplicaEn('1,2,3,4,5', '2026-10-11'), false);
prueba('lunes sí aplica', aplicaEn('1,2,3,4,5', '2026-10-12'), true);
prueba('dias vacío = todos los días', aplicaEn('', '2026-10-11'), true);

console.log('— hábitos de evitar: el día limpio es el que NO tiene registro —');
// Ojo con la asimetría, que es a propósito: en un hábito de HACER, el día de
// hoy no cuenta hasta que se hace (todavía puede cumplirse). En uno de EVITAR,
// hoy cuenta desde que amanece, porque se cumple solo mientras no caigas. Decir
// "llevas 0 días sin caer" el día que volviste a empezar sería desmoralizante y
// además falso: llevas uno.
prueba('sin registros = racha larga (10 días limpios)',
    rachaActual(evitar, new Map(), HOY) >= 10, true);
prueba('caí ayer: hoy ya cuenta = 1',
    rachaActual(evitar, mapa(['2026-10-12'], 'cai', true), HOY), 1);
prueba('caí hace 3 días = 3 limpios (anteayer, ayer y hoy)',
    rachaActual(evitar, mapa(['2026-10-10'], 'cai', true), HOY), 3);
prueba('caí hoy = racha 0 (el día ya se decidió)',
    rachaActual(evitar, mapa(['2026-10-13'], 'cai', true), HOY), 0);
prueba('hábito nuevo de evitar no inventa racha: nació hace 3 días',
    rachaActual({ ...evitar, desde: '2026-10-11' }, new Map(), HOY), 3);
prueba('hábito de hacer tampoco cuenta antes de nacer',
    rachaActual({ ...diario, desde: '2026-10-12' }, mapa(['2026-10-13','2026-10-12','2026-10-11','2026-10-10']), HOY), 2);

console.log('— meta de varias veces al día —');
const parcial = new Map([['2026-10-13', { veces: 5 }], ['2026-10-12', { veces: 8 }]]);
prueba('5 de 8 hoy no cuenta, 8 de ayer sí', rachaActual(agua, parcial, HOY), 1);
prueba('8 de 8 hoy sí cuenta',
    rachaActual(agua, new Map([['2026-10-13', { veces: 8 }], ['2026-10-12', { veces: 8 }]]), HOY), 2);

console.log('— mejor racha —');
prueba('mejor racha de un tramo viejo',
    mejorRacha(diario, mapa(['2026-10-01','2026-10-02','2026-10-03','2026-10-04','2026-10-08','2026-10-09']), HOY), 4);

console.log('— cumplimiento —');
const c = cumplimiento(diario, mapa(['2026-10-13','2026-10-12','2026-10-11','2026-10-10','2026-10-09']), 10, HOY);
prueba('5 de 10 días = 50%', [c.aplicables, c.hechos, c.pct], [10, 5, 50]);
const cl = cumplimiento(laboral, mapa(['2026-10-13','2026-10-12','2026-10-09']), 7, HOY);
prueba('laboral: denominador son solo los días hábiles', [cl.aplicables, cl.hechos], [5, 3]);

console.log('— tira de los últimos días —');
const tira = ultimosDias(laboral, mapa(['2026-10-13','2026-10-12']), 7, HOY);
prueba('7 casillas', tira.length, 7);
prueba('la última es hoy y está hecha', [tira[6].esHoy, tira[6].hecho], [true, true]);
prueba('el domingo sale como "no aplica"', tira.find(d => d.fecha === '2026-10-11').hecho, null);

console.log(`\n${ok}/${ok + fallos.length} bien`);
if (fallos.length) {
    console.log('\nFALLOS:');
    fallos.forEach(f => console.log(' -', f[0], '\n   esperaba:', f[1], '\n   dio     :', f[2]));
    process.exit(1);
}
