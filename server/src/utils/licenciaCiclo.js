// Ciclo de cobro de una licencia: día de corte, tolerancia y bloqueo.
// Toda la lógica de "¿está válida?" y "¿hasta cuándo renueva?" vive aquí
// para que /api/licencias/validar, /api/pagos/mp/validar, el webhook de
// MercadoPago, el pago manual y la UI respondan exactamente lo mismo.
const DIA_MS = 86400000;

// Fecha de hoy en Bogotá como 'YYYY-MM-DD' (Railway corre en UTC).
function hoyBogota(ahora = new Date()) {
    return new Date(ahora.getTime() - 5 * 3600000).toISOString().split('T')[0];
}

// 'YYYY-MM-DD' (o un Date de Sequelize) -> Date a medianoche UTC, para comparar
// solo días. Sequelize devuelve DATEONLY como string, pero un modelo recién
// actualizado en memoria puede traer un Date: hay que soportar ambos o
// estadoLicencia() revienta con "Invalid time value".
function aFecha(valor) {
    if (valor instanceof Date) return new Date(Date.UTC(valor.getUTCFullYear(), valor.getUTCMonth(), valor.getUTCDate()));
    const [y, m, d] = String(valor).split('T')[0].split('-').map(Number);
    if (!y || !m || !d) throw new Error('Fecha invalida: ' + valor);
    const f = new Date(Date.UTC(y, m - 1, d));
    // JS normaliza 2026-02-31 a 2026-03-03 en silencio; eso corre el corte de un
    // cliente sin avisar. Se rechaza en vez de aceptar una fecha que no existe.
    if (f.getUTCFullYear() !== y || f.getUTCMonth() !== m - 1 || f.getUTCDate() !== d) {
        throw new Error('Fecha inexistente en el calendario: ' + valor);
    }
    return f;
}
function aStr(date) { return date.toISOString().split('T')[0]; }

function sumarDias(date, dias) { return new Date(date.getTime() + dias * DIA_MS); }

// Último día del mes (para día de corte 29-31 en meses cortos).
function ultimoDiaMes(y, m0) { return new Date(Date.UTC(y, m0 + 1, 0)).getUTCDate(); }

/**
 * Estado de una licencia para un día dado.
 * Regla de bloqueo: bloqueada si hoy >= vencimiento + dias_gracia (y bloquear = true).
 *   - ASOERC: corte 20, gracia 0  -> se bloquea el mismo 20.
 *   - Láser:  corte 1,  gracia 15 -> vence el 1, se bloquea el 16.
 * Si bloquear = false la licencia nunca deja de validar; solo se avisa la mora.
 */
function estadoLicencia(lic, hoyStr = hoyBogota()) {
    const hoy      = aFecha(hoyStr);
    const venc     = aFecha(lic.fecha_vencimiento);
    const gracia   = Number(lic.dias_gracia) || 0;
    const bloquear = lic.bloquear !== false && lic.bloquear !== 0;
    const bloqueo  = sumarDias(venc, gracia);

    const diasParaVencer = Math.round((venc - hoy) / DIA_MS);   // negativo = ya venció
    const diasMora       = diasParaVencer < 0 ? -diasParaVencer : 0;

    const base = {
        fecha_vencimiento: aStr(venc),
        fecha_bloqueo:     bloquear ? aStr(bloqueo) : null,
        dias_restantes:    diasParaVencer,
        dias_mora:         diasMora,
        bloquear
    };

    if (lic.activo === false || lic.activo === 0) return { ...base, valida: false, estado: 'desactivada' };
    if (hoy < venc) return { ...base, valida: true, estado: diasParaVencer <= 7 ? 'por_vencer' : 'al_dia' };
    if (!bloquear)  return { ...base, valida: true, estado: 'mora_sin_bloqueo' };
    if (hoy < bloqueo) return { ...base, valida: true, estado: 'en_gracia' };
    return { ...base, valida: false, estado: 'bloqueada' };
}

/**
 * Nueva fecha de vencimiento al registrar un pago de `meses` meses.
 * Con día de corte: se salta al siguiente día de corte estrictamente posterior a
 * `desde` (el vencimiento actual si sigue vigente, o hoy si ya venció) y luego se
 * suman meses-1. Sin día de corte: comportamiento antiguo (desde + meses).
 */
function siguienteVencimiento(lic, meses = 1, hoyStr = hoyBogota()) {
    const n     = Math.min(Math.max(parseInt(meses) || 1, 1), 24);
    const hoy   = aFecha(hoyStr);
    const venc  = aFecha(lic.fecha_vencimiento);
    const desde = venc > hoy ? venc : hoy;
    const corte = parseInt(lic.dia_corte) || 0;

    if (!corte) {
        // Sin día de corte: sumar meses conservando el día, pero recortando al
        // último día del mes destino (31 ene + 1 mes = 28 feb, no 3 mar).
        const destino = new Date(Date.UTC(desde.getUTCFullYear(), desde.getUTCMonth() + n, 1));
        const dia = Math.min(desde.getUTCDate(), ultimoDiaMes(destino.getUTCFullYear(), destino.getUTCMonth()));
        return aStr(new Date(Date.UTC(destino.getUTCFullYear(), destino.getUTCMonth(), dia)));
    }

    // Con día de corte: se calcula el aniversario natural (desde + n meses) y se
    // elige el día de corte MÁS CERCANO a esa fecha. Empate -> el anterior.
    //
    // Es importante que sea "el más cercano" y no "el siguiente": si el
    // vencimiento no está alineado al ciclo (pasa al migrar una licencia vieja),
    // cualquiera de los dos atajos le cobra un mes completo por unos pocos días.
    // Ejemplos: vence 16 sep + corte 20 -> 20 oct (no 20 sep, que serían 4 días);
    // vence 31 ene + corte 1 -> 1 mar (no 1 feb, que sería 1 día);
    // paga tarde el 25 oct con corte 20 -> 20 nov (se mantiene el ciclo).
    const destino = new Date(Date.UTC(desde.getUTCFullYear(), desde.getUTCMonth() + n, 1));
    const diaNat  = Math.min(desde.getUTCDate(), ultimoDiaMes(destino.getUTCFullYear(), destino.getUTCMonth()));
    const natural = new Date(Date.UTC(destino.getUTCFullYear(), destino.getUTCMonth(), diaNat));

    const enMes = (offset) => {
        const base = new Date(Date.UTC(natural.getUTCFullYear(), natural.getUTCMonth() + offset, 1));
        const y = base.getUTCFullYear(), m = base.getUTCMonth();
        return new Date(Date.UTC(y, m, Math.min(corte, ultimoDiaMes(y, m))));
    };

    let mejor = null;
    for (const off of [-1, 0, 1, 2]) {
        const c = enMes(off);
        if (c <= desde) continue;                       // nunca hacia atrás
        if (!mejor || Math.abs(c - natural) < Math.abs(mejor - natural)) mejor = c;
    }
    return aStr(mejor || enMes(1));
}

// Precio efectivo: el de la licencia si tiene override, si no el del producto.
function precioLicencia(lic) {
    const p = lic.precio_mensual != null && lic.precio_mensual !== '' ? Number(lic.precio_mensual) : Number(lic.producto?.precio_mensual);
    return isNaN(p) ? 0 : p;
}

module.exports = { hoyBogota, aFecha, aStr, estadoLicencia, siguienteVencimiento, precioLicencia };
