package co.aicompany.plataforma

import android.appwidget.AppWidgetManager
import android.appwidget.AppWidgetProvider
import android.content.Context
import android.content.Intent
import android.view.View
import android.widget.RemoteViews
import java.text.NumberFormat
import java.util.Locale

/**
 * Widget de pantalla de inicio. Lee /api/widget de la plataforma y pinta:
 * lo que queda libre, te deben, deuda, y las 3 cosas más urgentes.
 *
 * La clave WIDGET_KEY la escribe Cristian una vez al agregar el widget
 * (ConfigurarWidgetActivity) y queda en SharedPreferences. Solo abre esa ruta
 * y solo lee: si se filtra, lo peor que pasa es que alguien vea el resumen.
 *
 * Android refresca el widget según updatePeriodMillis (mín. 30 min). Tocar el
 * widget lo refresca al instante y abre la app.
 */
class ResumenWidget : AppWidgetProvider() {

    companion object {
        const val BASE_URL = "https://mi-plataforma-production.up.railway.app"
        const val PREFS = "widget_prefs"
        const val PREF_KEY = "widget_key"
        const val ACCION_REFRESCAR = WidgetComun.ACCION_REFRESCAR

        fun claveGuardada(ctx: Context): String? =
            ctx.getSharedPreferences(PREFS, Context.MODE_PRIVATE).getString(PREF_KEY, null)

        fun cop(n: Double): String {
            val f = NumberFormat.getInstance(Locale("es", "CO"))
            f.maximumFractionDigits = 0
            return "$" + f.format(n)
        }

        /** Pinta el widget y luego lo actualiza con datos de la red. */
        fun actualizar(ctx: Context, mgr: AppWidgetManager, id: Int) {
            val vistas = RemoteViews(ctx.packageName, R.layout.widget_resumen)

            // Tocar cualquier parte abre la app en Hoy
            vistas.setOnClickPendingIntent(R.id.widget_raiz, WidgetComun.abrir(ctx, id * 10 + 7, "/hoy"))

            val clave = claveGuardada(ctx)
            if (clave.isNullOrBlank()) {
                vistas.setTextViewText(R.id.txt_libre, "Falta la clave")
                vistas.setTextViewText(R.id.txt_detalle, "Toca para escribirla")
                vistas.setOnClickPendingIntent(R.id.widget_raiz, WidgetComun.configurar(ctx, id * 10 + 5, id))
                vistas.setViewVisibility(R.id.txt_acciones, View.GONE)
                mgr.updateAppWidget(id, vistas)
                return
            }

            vistas.setTextViewText(R.id.txt_detalle, "Actualizando…")
            mgr.updateAppWidget(id, vistas)

            WidgetComun.pedir(clave) { r ->
                if (r.error != null) {
                    vistas.setOnClickPendingIntent(R.id.widget_raiz,
                        if (r.esClave) WidgetComun.configurar(ctx, id * 10 + 5, id)
                        else WidgetComun.reintentar(ctx, id * 10 + 6))
                    vistas.setTextViewText(R.id.txt_detalle, r.error)
                } else {
                    val d = r.datos!!
                    val libre = d.getDouble("libre")
                    vistas.setTextViewText(R.id.txt_libre, cop(libre))
                    vistas.setTextColor(R.id.txt_libre, if (libre >= 0) 0xFF0F172A.toInt() else 0xFFDC2626.toInt())
                    vistas.setTextViewText(
                        R.id.txt_detalle,
                        "Te deben ${cop(d.getDouble("te_deben"))}   ·   Deuda ${cop(d.getDouble("deuda"))}"
                    )

                    val acciones = d.getJSONArray("acciones")
                    val pend = d.optInt("pendientes", 0)
                    if (pend == 0) {
                        vistas.setTextViewText(R.id.txt_acciones, "✓ Nada pendiente hoy")
                        vistas.setTextColor(R.id.txt_acciones, 0xFF16A34A.toInt())
                    } else {
                        val sb = StringBuilder()
                        // Este widget es de 4x2: mas de tres renglones no caben.
                        val cuantas = minOf(acciones.length(), 3)
                        for (i in 0 until cuantas) {
                            val a = acciones.getJSONObject(i)
                            val punto = when (a.optString("u")) { "alta" -> "● "; "media" -> "◐ "; else -> "○ " }
                            if (i > 0) sb.append("\n")
                            sb.append(punto).append(a.optString("t"))
                        }
                        if (pend > cuantas) sb.append("\n+${pend - cuantas} más")
                        vistas.setTextViewText(R.id.txt_acciones, sb.toString())
                        vistas.setTextColor(R.id.txt_acciones, 0xFF334155.toInt())
                    }
                    vistas.setViewVisibility(R.id.txt_acciones, View.VISIBLE)
                }
                mgr.updateAppWidget(id, vistas)
            }
        }
    }

    override fun onUpdate(ctx: Context, mgr: AppWidgetManager, ids: IntArray) {
        for (id in ids) actualizar(ctx, mgr, id)
    }

    override fun onReceive(ctx: Context, intent: Intent) {
        super.onReceive(ctx, intent)
        if (intent.action == ACCION_REFRESCAR) {
            val mgr = AppWidgetManager.getInstance(ctx)
            val ids = mgr.getAppWidgetIds(android.content.ComponentName(ctx, ResumenWidget::class.java))
            onUpdate(ctx, mgr, ids)
        }
    }
}
