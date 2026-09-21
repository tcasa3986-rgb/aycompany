# Widgets de pantalla de inicio

Una página web no puede poner un widget en la pantalla de inicio del celular:
eso lo hace solo una app nativa. Pero hay apps de widgets que **sí** pueden
pintar datos que vengan de una URL. La plataforma expone esa URL, y aquí están
los scripts para cada sistema.

## El endpoint

```
GET https://mi-plataforma-production.up.railway.app/api/widget?key=<WIDGET_KEY>
```

Responde algo así:

```json
{
  "ok": true, "hoy": "2026-09-20",
  "libre": 766000, "te_deben": 11500000, "recurrente": 2050000, "deuda": 6000000,
  "pendientes": 4, "urgentes": 2,
  "acciones": [
    { "t": "Ferre Láser CDA nunca te ha pagado la cuota", "u": "alta" },
    { "t": "JD Metales nunca te ha pagado la cuota",      "u": "alta" },
    { "t": "No tienes medido cuánto te cuesta la infra…",  "u": "media" }
  ]
}
```

**La clave `WIDGET_KEY` es aparte del login a propósito.** Un widget la guarda en el
celular para siempre, así que no puede llevar la contraseña ni un token que vence.
Solo abre esta ruta y solo lee: si se filtra, lo peor que pasa es que alguien vea
el resumen. Se rota cambiando la variable en Railway.

Para crearla: Railway → mi-plataforma → Variables → `WIDGET_KEY` = una cadena larga
al azar (por ejemplo `openssl rand -hex 24`).

## iPhone — Scriptable (gratis)

Archivo: `iphone-scriptable.js`. Las instrucciones están al inicio del archivo.
Widget mediano: libre en grande, te deben y deuda, y las 3 cosas más urgentes.
Al tocarlo abre Inicio. Se refresca solo cada ~15 minutos.

## Android — KWGT (~$5) o Widgets Personalizados

KWGT puede leer JSON de una URL con la fórmula `$wg(URL, json, .libre)$`. Con el
endpoint de arriba se arma un widget con estas fórmulas:

| Texto del widget | Fórmula KWGT |
|---|---|
| Libre | `$wg("https://…/api/widget?key=CLAVE", json, ".libre")$` |
| Te deben | `$wg("…", json, ".te_deben")$` |
| Pendientes | `$wg("…", json, ".pendientes")$` |
| Primera acción | `$wg("…", json, ".acciones[0].t")$` |

## Android — accesos directos (gratis, ya activo)

No es un widget, pero está listo sin instalar nada: con la PWA instalada, **mantén
presionado el ícono** y salen tres atajos:

- **Registrar gasto** → abre el capturador de una
- **Qué hago hoy** → Inicio
- **Finanzas**

## Si algún día se quiere el widget "de verdad"

Una app nativa envolvente (Capacitor) con un widget hecho a la medida y
notificaciones push. Es un proyecto aparte, con su propio despliegue en las
tiendas. Todo lo de arriba sigue sirviendo mientras tanto.
