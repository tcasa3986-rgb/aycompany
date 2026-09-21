# Widget en Samsung con KWGT

## Antes de empezar
1. En Railway → mi-plataforma → Variables → crear `WIDGET_KEY` con una cadena
   larga al azar. Sin esto el widget dice "clave inválida".
2. Instalar **KWGT Kustom Widget Maker** y **KWGT Pro Key** desde Play Store
   (Pro se necesita para guardar el widget; ~$5 una vez).

## Poner el widget
1. Mantener presionado en la pantalla de inicio → **Widgets** → buscar **KWGT**
   → arrastrar el tamaño **4×2** al escritorio.
2. Tocar el widget vacío → se abre el editor → abajo, pestaña **Elementos**.

## Armar el contenido
Cada línea de abajo es un elemento de tipo **Texto**. Se agrega con **+ → Texto**,
se toca el elemento, y en la pestaña **Texto** se pega la fórmula.

Reemplazar `CLAVE` por el valor de `WIDGET_KEY`.

| Qué muestra | Fórmula (pegar tal cual) | Tamaño sugerido |
|---|---|---|
| Rótulo | `TE QUEDA LIBRE` | 10 |
| Libre | `$wg("https://mi-plataforma-production.up.railway.app/api/widget?key=CLAVE", json, ".libre")$` | 30, negrita |
| Te deben | `Te deben $$wg("https://mi-plataforma-production.up.railway.app/api/widget?key=CLAVE", json, ".te_deben")$` | 13 |
| Deuda | `Deuda $$wg("https://mi-plataforma-production.up.railway.app/api/widget?key=CLAVE", json, ".deuda")$` | 13 |
| Pendientes | `$wg("https://mi-plataforma-production.up.railway.app/api/widget?key=CLAVE", json, ".pendientes")$ pendientes` | 11 |
| Lo más urgente | `$wg("https://mi-plataforma-production.up.railway.app/api/widget?key=CLAVE", json, ".acciones[0].t")$` | 11 |

Para que los números salgan con puntos de miles (766.000 en vez de 766000),
envolver así: `$nc(fmt, wg(...), "#,##0")$` → se ve como `766.000`.

## Que al tocarlo abra la app
Tocar el fondo del widget → pestaña **Toque** → **Abrir enlace** →
`https://mi-plataforma-production.up.railway.app/hoy`

## Colores del diseño
- Fondo: `#F8FAFC` · Texto principal: `#0F172A` · Gris: `#94A3B8`
- Te deben: `#D97706` (ámbar) · Deuda: `#DC2626` (rojo) · Al día: `#16A34A` (verde)

## Cada cuánto se actualiza
KWGT refresca el `$wg()` cada ~15 minutos por defecto. Se puede bajar en
Ajustes de KWGT → "Intervalo de actualización de red".

## Si algo no sale
- **"clave inválida"** → la `WIDGET_KEY` de Railway no coincide con la pegada.
- **Vacío** → KWGT no tiene permiso de internet o el intervalo aún no corrió;
  tocar el widget → ⋮ → **Actualizar**.
- **Números sin puntos** → usar la fórmula `$nc(...)$` de arriba.
