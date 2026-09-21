// Interpreta frases de plata SIN llamar a ninguna IA.
//
// "gasté 30000 gasolina" o "me entraron 250000 de asoerc" se resuelven con
// expresiones regulares: cuestan cero y responden al instante. Solo lo que no
// cuadra con este formato pasa al asistente, que sí consume tokens.
//
// Es el mismo lenguaje que promete la app en el capturador rápido.

// Palabras que delatan si la plata entra o sale.
//
// OJO con \b y los acentos: en JavaScript "é" no es caracter de palabra, asi que
// /gast[eé]\b/ NO encuentra "gasté" (entre "é" y el espacio no hay frontera).
// Por eso el limite se escribe a mano con un lookahead de letras.
const LETRA = "a-záéíóúüñ";
const fin = `(?![${LETRA}])`;
const ini = `(?<![${LETRA}])`;

const SALE  = new RegExp(`${ini}(gast[eé]|pagu[eé]|pag[uú]e|compr[eé]|sali[oó]|salieron|me\\s+cost[oó])${fin}`, 'i');
const ENTRA = new RegExp(`${ini}(entr[oó]|entraron|me\\s+pagaron|me\\s+entr[oó]|recib[ií]|cobr[eé]|me\\s+consignaron|me\\s+dieron)${fin}`, 'i');

// Categoria segun lo que se nombre. El orden importa: gana la primera, por eso
// "alquiler moto" va antes que "moto" (uno es ingreso y el otro gasto).
const CATEGORIAS = [
    { re: /alquiler\s+(de\s+)?moto|arriendo\s+(de\s+)?moto/i,          cat: 'alquiler_moto', ambito: 'personal', etiqueta: 'Alquiler de moto' },
    { re: /gasolina|combustible|tanque[ao]|moto/i,                      cat: 'transporte',  ambito: 'personal', etiqueta: 'Gasolina' },
    { re: /comida|almuerzo|desayuno|cena|mercado|restaurante/i,         cat: 'comida',      ambito: 'personal', etiqueta: 'Comida' },
    { re: /arriendo|renta/i,                                            cat: 'arriendo',    ambito: 'personal', etiqueta: 'Arriendo' },
    { re: /servicios|recibos?|luz|agua|gas|internet/i,                cat: 'recibos',     ambito: 'personal', etiqueta: 'Servicios' },
    { re: /gimnasio|gym/i,                                              cat: 'gimnasio',    ambito: 'personal', etiqueta: 'Gimnasio' },
    { re: /celular|plan|minutos|datos/i,                              cat: 'celular',     ambito: 'personal', etiqueta: 'Celular' },
    { re: /salud|eps|medicin|droguer/i,                               cat: 'salud',       ambito: 'personal', etiqueta: 'Salud' },
    { re: /universidad|semestre|curso|estudio/i,                        cat: 'educacion',   ambito: 'personal', etiqueta: 'Educación' },
    { re: /railway|hosting|servidor|dominio/i,                          cat: 'hosting',     ambito: 'empresa',  etiqueta: 'Hosting' },
    { re: /codex|claude|openai|anthropic|gemini|api/i,                cat: 'apis_ia',     ambito: 'empresa',  etiqueta: 'APIs / IA' },
    { re: /publicidad|pauta|anuncios?/i,                                cat: 'publicidad',  ambito: 'empresa',  etiqueta: 'Publicidad' },
    { re: /mensualidad|licencia|mantenimiento/i,                        cat: 'mensualidad', ambito: 'empresa',  etiqueta: 'Mensualidad' },
    { re: /cuota|abono|contrato/i,                                      cat: 'cuota_contrato', ambito: 'empresa', etiqueta: 'Cuota de contrato' },
    { re: /redes|marketing|contenido/i,                                 cat: 'marketing',   ambito: 'empresa',  etiqueta: 'Marketing' },
];

// Numeros dichos en palabras. Hablando nadie dice "1000000": dice "un millon".
// Esto entra sobre todo por las notas de voz.
// Patron que nunca encuentra nada: sirve de "no reemplaces" en un encadenado.
const NUNCA = /(?!)/;

const PALABRA_NUM = {
    cero: 0, un: 1, uno: 1, una: 1, dos: 2, tres: 3, cuatro: 4, cinco: 5, seis: 6,
    siete: 7, ocho: 8, nueve: 9, diez: 10, once: 11, doce: 12, trece: 13, catorce: 14,
    quince: 15, dieciseis: 16, diecisiete: 17, dieciocho: 18, diecinueve: 19,
    veinte: 20, veintiun: 21, veintiuno: 21, veintidos: 22, veintitres: 23,
    veinticuatro: 24, veinticinco: 25, veintiseis: 26, veintisiete: 27,
    veintiocho: 28, veintinueve: 29, treinta: 30, cuarenta: 40, cincuenta: 50,
    sesenta: 60, setenta: 70, ochenta: 80, noventa: 90, cien: 100, ciento: 100,
    doscientos: 200, trescientos: 300, cuatrocientos: 400, quinientos: 500,
    seiscientos: 600, setecientos: 700, ochocientos: 800, novecientos: 900,
    mil: 1000, millon: 1000000, millones: 1000000,
};

/** Quita tildes para poder comparar: "millón" y "millon" son la misma palabra. */
function sinTildes(t) {
    return t.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
}

/**
 * Lee un monto dicho en palabras dentro de una frase.
 * "pagué un millón de Railway" -> { monto: 1000000, frase: 'un millón' }
 * Devuelve null si no hay ninguno.
 */
function leerMontoEnPalabras(texto) {
    const crudas = texto.split(/\s+/);
    const limpias = crudas.map(sinTildes).map(w => w.replace(/[^a-z]/g, ''));

    let mejor = null;
    for (let i = 0; i < limpias.length; i++) {
        if (PALABRA_NUM[limpias[i]] === undefined) continue;
        let total = 0, parcial = 0, j = i, visto = false;
        for (; j < limpias.length; j++) {
            const w = limpias[j];
            if (w === 'y' && visto) continue;          // "ciento y pico" no, pero "treinta y dos" si
            const v = PALABRA_NUM[w];
            if (v === undefined) break;
            visto = true;
            if (v === 1000)            { parcial = (parcial || 1) * 1000; total += parcial; parcial = 0; }
            else if (v === 1000000)    { total = (total + (parcial || 1)) * 1000000; parcial = 0; }
            else                       { parcial += v; }
        }
        const monto = total + parcial;
        // Se queda con el tramo mas largo: "doscientos cincuenta mil" entero,
        // no solo "doscientos".
        if (monto > 0 && (!mejor || j - i > mejor.largo)) {
            mejor = { monto, frase: crudas.slice(i, j).join(' '), largo: j - i };
        }
        i = j - 1;
    }
    return mejor;
}

/**
 * Convierte lo que se escribe de verdad en un número:
 * "30000", "30.000", "30 mil", "30k", "1'200.000", "1,5 millones".
 * Devuelve null si no hay un monto claro.
 */
function leerMonto(texto) {
    // Primero las formas con palabra: 30 mil, 1.5 millones, 250k
    const conPalabra = texto.match(/(\d+(?:[.,]\d+)?)\s*(mill[oó]n(?:es)?|mill|mil|k|m)(?![a-záéíóúüñ])/i);
    if (conPalabra) {
        const n = Number(conPalabra[1].replace(',', '.'));
        const u = conPalabra[2].toLowerCase();
        if (!isFinite(n)) return null;
        const factor = (u === 'mil' || u === 'k') ? 1000 : 1000000;
        return Math.round(n * factor);
    }
    // Y luego los números planos, con puntos o comas de miles: 30.000 / 1'200,000
    const plano = texto.match(/\d[\d.,'\s]*\d|\d/);
    // Sin dígitos, puede venir dicho en palabras: "un millón", "treinta mil".
    if (!plano) {
        const enPalabras = leerMontoEnPalabras(texto);
        return enPalabras ? enPalabras.monto : null;
    }
    const limpio = plano[0].replace(/[.,'\s]/g, '');
    const n = Number(limpio);
    if (!isFinite(n) || n <= 0) return null;
    return n;
}

/**
 * Devuelve { tipo, monto, categoria, concepto, ambito } o null si la frase no
 * es un registro de plata (entonces la atiende el asistente).
 */
function interpretar(texto) {
    if (!texto || typeof texto !== 'string') return null;
    const t = texto.trim();
    if (t.length > 120) return null;          // una parrafada no es un registro

    const sale  = SALE.test(t);
    const entra = ENTRA.test(t);
    if (sale === entra) return null;          // ni ambas ni ninguna: que decida la IA

    const monto = leerMonto(t);
    if (!monto || monto < 100) return null;   // sin monto no hay registro

    const tipo = sale ? 'egreso' : 'ingreso';
    const encontrada = CATEGORIAS.find(c => c.re.test(t));

    // Sin categoría reconocida se guarda igual como "otro": perder el dato es
    // peor que guardarlo sin etiqueta, y se puede corregir en la app.
    const categoria = encontrada ? encontrada.cat : 'otro';
    const ambito    = encontrada ? encontrada.ambito : (tipo === 'ingreso' ? 'empresa' : 'personal');

    // El concepto es lo que escribió, sin el verbo ni el número: así queda
    // "asoerc" o "gasolina moto" y no un texto genérico.
    const dicho = leerMontoEnPalabras(t);
    let concepto = t
        .replace(SALE, ' ').replace(ENTRA, ' ')
        .replace(dicho ? dicho.frase : NUNCA, ' ')
        .replace(/(\d+(?:[.,]\d+)?)\s*(mill[oó]n(?:es)?|mill|mil|k|m)(?![a-záéíóúüñ])/i, ' ')
        .replace(/\d[\d.,'\s]*\d|\d/, ' ')
        .replace(/\b(de|del|en|por|para|a|el|la|los|las|un|una|pesos?|cop|me|se)\b/gi, ' ')
        // El dictado deja simbolos y puntuacion: "$ gasolina." en vez de
        // "gasolina". Se limpian aparte de las palabras vacias.
        .replace(/[$\u20ac.,;:!\u00a1?\u00bf"'\u00ab\u00bb]/g, ' ')
        .replace(/\s+/g, ' ').trim();
    if (concepto.length < 3) concepto = encontrada ? encontrada.etiqueta : (tipo === 'egreso' ? 'Gasto' : 'Ingreso');
    concepto = concepto.charAt(0).toUpperCase() + concepto.slice(1);

    return { tipo, monto, categoria, concepto: concepto.slice(0, 200), ambito };
}

module.exports = { interpretar, leerMonto, leerMontoEnPalabras };
