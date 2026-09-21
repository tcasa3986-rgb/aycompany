// Casos de lo que el bot debe y NO debe anotar. Correr con:
//   node server/scripts/probar-registro-rapido.js
const { interpretar } = require('../src/services/registroRapido');

const casos = [
  // [frase, tipo esperado, monto esperado, categoria esperada]
  ['gasté 30000 gasolina',              'egreso',  30000,   'transporte'],
  ['gaste 30.000 en gasolina',          'egreso',  30000,   'transporte'],
  ['Gasté 30 mil en gasolina',          'egreso',  30000,   'transporte'],
  ['gasté 30k gasolina',                'egreso',  30000,   'transporte'],
  ['pagué 500000 de arriendo',          'egreso',  500000,  'arriendo'],
  ['pague el arriendo 500.000',         'egreso',  500000,  'arriendo'],
  ['compré comida 25000',               'egreso',  25000,   'comida'],
  ['pagué 100000 del gimnasio',         'egreso',  100000,  'gimnasio'],
  ['pagué 64000 de claude',             'egreso',  64000,   'apis_ia'],
  ['pagué 1 millón de railway',         'egreso',  1000000, 'hosting'],
  ['gasté 1.5 millones en publicidad',  'egreso',  1500000, 'publicidad'],
  ['me pagaron 250000 de mensualidad',  'ingreso', 250000,  'mensualidad'],
  ['entraron 1000000 de maderas',       'ingreso', 1000000, 'otro'],
  ['me entró la cuota de 1000000',      'ingreso', 1000000, 'cuota_contrato'],
  ['recibí 750000 del alquiler moto',   'ingreso', 750000,  'alquiler_moto'],
  ['cobré 100000 de dinasty',           'ingreso', 100000,  'otro'],
  // Dicho en palabras, que es como llega por nota de voz
  ['Pagué un millón de Railway',        'egreso',  1000000, 'hosting'],
  ['Gasté treinta mil en gasolina',     'egreso',  30000,   'transporte'],
  ['Me pagaron doscientos cincuenta mil de la mensualidad', 'ingreso', 250000, 'mensualidad'],
  ['Recibí quinientos mil del alquiler moto', 'ingreso', 500000, 'alquiler_moto'],
  ['Gasté ciento cincuenta mil en servicios', 'egreso', 150000, 'recibos'],
  ['Me pagaron un millón quinientos mil de la cuota', 'ingreso', 1500000, 'cuota_contrato'],
  // Como lo transcribe Whisper, con simbolos y puntuacion
  ['Gasté $30,000 pesos en gasolina.',  'egreso',  30000,   'transporte'],
  ['Me pagaron $250,000 de la mensualidad.', 'ingreso', 250000, 'mensualidad'],
  // Lo que NO debe registrar (pasa al asistente)
  ['¿cómo vamos este mes?',             null],
  ['qué tengo esta semana',             null],
  ['hola',                              null],
  ['gasté mucho',                       null],            // sin monto
  ['250000',                            null],            // sin verbo
  ['crea una reunión mañana a las 3pm', null],
  ['gasté 50 pesos',                    null],            // monto absurdo, bajo el minimo
  ['me pagaron y gasté 30000',          null],
  ['gasté un café',                      null],
  ['¿cómo vamos este mes?',             null],            // ambiguo: entra y sale
];

let ok = 0, fallos = [];
for (const [frase, tipo, monto, cat] of casos) {
  const r = interpretar(frase);
  if (tipo === null) {
    if (r === null) ok++; else fallos.push([frase, 'debia ignorarse', JSON.stringify(r)]);
    continue;
  }
  if (!r) { fallos.push([frase, 'debia registrarse', 'null']); continue; }
  const bien = r.tipo === tipo && r.monto === monto && r.categoria === cat;
  if (bien) { ok++; console.log('  ok  ', frase.padEnd(36), '->', r.tipo, r.monto, r.categoria, '|', r.concepto, '|', r.ambito); }
  else fallos.push([frase, `${tipo} ${monto} ${cat}`, `${r.tipo} ${r.monto} ${r.categoria}`]);
}
console.log(`\n${ok}/${casos.length} bien`);
if (fallos.length) { console.log('\nFALLOS:'); fallos.forEach(f => console.log(' -', f[0], '\n   esperaba:', f[1], '\n   dio     :', f[2])); process.exit(1); }
