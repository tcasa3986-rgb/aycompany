package co.aicompany.plataforma

import android.appwidget.AppWidgetManager
import android.appwidget.AppWidgetProvider
import android.content.ComponentName
import android.content.Context
import android.content.Intent
import android.view.View
import android.widget.RemoteViews

/**
 * Widget HOY (4x3): la lista de lo que te toca, ordenada por urgencia, tal cual
 * la calcula el servidor en /api/widget (la misma de la pantalla Hoy).
 *
 * RemoteViews no arma listas solo: las filas están fijas en el XML y aquí se
 * muestran o se esconden. Cinco es lo que cabe sin que se vea apretado; si hay
 * más, la última fila dice cuántas faltan.
 */
class HoyWidget : AppWidgetProvider() {

    companion object {

        private val FILAS = intArrayOf(R.id.hoy_fila1, R.id.hoy_fila2, R.id.hoy_fila3, R.id.hoy_fila4, R.id.hoy_fila5)
        private val TEXTOS = intArrayOf(R.id.hoy_txt1, R.id.hoy_txt2, R.id.hoy_txt3, R.id.hoy_txt4, R.id.hoy_txt5)
        private val PUNTOS = intArrayOf(R.id.hoy_punto1, R.id.hoy_punto2, R.id.hoy_punto3, R.id.hoy_punto4, R.id.hoy_punto5)

        private fun punto(urgencia: String) = when (urgencia) {
            "alta"  -> R.drawable.w_punto_rojo
            "media" -> R.drawable.w_punto_ambar
            else    -> R.drawable.w_punto_azul
        }

        fun actualizar(ctx: Context, mgr: AppWidgetManager, id: Int) {
            val v = RemoteViews(ctx.packageName, R.layout.widget_hoy)

            v.setOnClickPendingIntent(R.id.hoy_raiz, WidgetComun.abrir(ctx, id * 10 + 4, "/hoy"))
            v.setTextViewText(R.id.hoy_saludo, WidgetComun.saludo())
            v.setTextViewText(R.id.hoy_fecha, WidgetComun.fechaBonita(null))

            fun soloMensaje(texto: String, color: Int) {
                for (f in FILAS) v.setViewVisibility(f, View.GONE)
                v.setTextViewText(R.id.hoy_vacio, texto)
                v.setTextColor(R.id.hoy_vacio, color)
                v.setViewVisibility(R.id.hoy_vacio, View.VISIBLE)
            }

            val clave = WidgetComun.clave(ctx)
            if (clave.isNullOrBlank()) {
                v.setTextViewText(R.id.hoy_contador, "sin clave")
                soloMensaje("Quita el widget y vuelve a agregarlo para escribir la clave", 0xFF64748B.toInt())
                mgr.updateAppWidget(id, v)
                return
            }

            v.setTextViewText(R.id.hoy_contador, "…")
            mgr.updateAppWidget(id, v)

            WidgetComun.pedir(clave) { d ->
                if (d == null) {
                    v.setTextViewText(R.id.hoy_contador, "sin red")
                    soloMensaje("Sin conexión · toca para reintentar", 0xFF64748B.toInt())
                } else {
                    v.setTextViewText(R.id.hoy_fecha, WidgetComun.fechaBonita(d.optString("hoy", null)))

                    val acciones = d.optJSONArray("acciones")
                    val total = d.optInt("pendientes", 0)
                    val urgentes = d.optInt("urgentes", 0)

                    v.setTextViewText(R.id.hoy_contador, when {
                        total == 0    -> "al día"
                        urgentes > 0  -> "$urgentes urgente" + (if (urgentes > 1) "s" else "")
                        total == 1    -> "1 cosa"
                        else          -> "$total cosas"
                    })

                    if (acciones == null || acciones.length() == 0) {
                        soloMensaje("✓  Nada pendiente. Buen día.", 0xFF16A34A.toInt())
                    } else {
                        v.setViewVisibility(R.id.hoy_vacio, View.GONE)
                        // La quinta fila se sacrifica para el "+N más" cuando sobra trabajo.
                        val sobran = total - FILAS.size
                        val aPintar = if (sobran > 0) FILAS.size - 1 else minOf(acciones.length(), FILAS.size)

                        for (i in FILAS.indices) {
                            if (i < aPintar) {
                                val a = acciones.getJSONObject(i)
                                v.setViewVisibility(FILAS[i], View.VISIBLE)
                                v.setTextViewText(TEXTOS[i], a.optString("t"))
                                v.setTextColor(TEXTOS[i], 0xFF0F172A.toInt())
                                v.setImageViewResource(PUNTOS[i], punto(a.optString("u")))
                            } else if (i == aPintar && sobran > 0) {
                                v.setViewVisibility(FILAS[i], View.VISIBLE)
                                v.setTextViewText(TEXTOS[i], "y ${sobran + 1} cosas más")
                                v.setTextColor(TEXTOS[i], 0xFF64748B.toInt())
                                v.setImageViewResource(PUNTOS[i], R.drawable.w_punto_gris)
                            } else {
                                v.setViewVisibility(FILAS[i], View.GONE)
                            }
                        }
                    }
                }
                mgr.updateAppWidget(id, v)
            }
        }
    }

    override fun onUpdate(ctx: Context, mgr: AppWidgetManager, ids: IntArray) {
        for (id in ids) actualizar(ctx, mgr, id)
    }

    override fun onReceive(ctx: Context, intent: Intent) {
        super.onReceive(ctx, intent)
        if (intent.action == WidgetComun.ACCION_REFRESCAR) {
            val mgr = AppWidgetManager.getInstance(ctx)
            onUpdate(ctx, mgr, mgr.getAppWidgetIds(ComponentName(ctx, HoyWidget::class.java)))
        }
    }
}
