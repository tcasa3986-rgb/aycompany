package co.aicompany.plataforma

import android.app.PendingIntent
import android.appwidget.AppWidgetManager
import android.appwidget.AppWidgetProvider
import android.content.Context
import android.content.Intent
import android.net.Uri
import android.view.View
import android.widget.RemoteViews
import org.json.JSONObject
import java.net.HttpURLConnection
import java.net.URL
import java.text.NumberFormat
import java.util.Locale
import java.util.concurrent.Executors

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
        const val ACCION_REFRESCAR = "co.aicompany.plataforma.REFRESCAR"

        private val hilo = Executors.newSingleThreadExecutor()

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

            // Tocar cualquier parte abre la app en Inicio
            val abrir = Intent(Intent.ACTION_VIEW, Uri.parse("$BASE_URL/hoy")).apply {
                setPackage(ctx.packageName)
                addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            }
            val abrirApp = Intent(ctx, MainActivity::class.java).apply { addFlags(Intent.FLAG_ACTIVITY_NEW_TASK) }
            vistas.setOnClickPendingIntent(
                R.id.widget_raiz,
                PendingIntent.getActivity(ctx, id, abrirApp, PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE)
            )

            val clave = claveGuardada(ctx)
            if (clave.isNullOrBlank()) {
                vistas.setTextViewText(R.id.txt_libre, "Falta la clave")
                vistas.setTextViewText(R.id.txt_detalle, "Quita el widget y vuelve a agregarlo para escribirla")
                vistas.setViewVisibility(R.id.txt_acciones, View.GONE)
                mgr.updateAppWidget(id, vistas)
                return
            }

            vistas.setTextViewText(R.id.txt_detalle, "Actualizando…")
            mgr.updateAppWidget(id, vistas)

            hilo.execute {
                try {
                    val con = (URL("$BASE_URL/api/widget?key=$clave").openConnection() as HttpURLConnection).apply {
                        connectTimeout = 10_000; readTimeout = 10_000
                        setRequestProperty("Accept", "application/json")
                    }
                    val cuerpo = con.inputStream.bufferedReader().readText()
                    val d = JSONObject(cuerpo)
                    if (!d.optBoolean("ok")) throw Exception(d.optString("msg", "error"))

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
                        for (i in 0 until acciones.length()) {
                            val a = acciones.getJSONObject(i)
                            val punto = when (a.optString("u")) { "alta" -> "● "; "media" -> "◐ "; else -> "○ " }
                            if (i > 0) sb.append("\n")
                            sb.append(punto).append(a.optString("t"))
                        }
                        if (pend > acciones.length()) sb.append("\n+${pend - acciones.length()} más")
                        vistas.setTextViewText(R.id.txt_acciones, sb.toString())
                        vistas.setTextColor(R.id.txt_acciones, 0xFF334155.toInt())
                    }
                    vistas.setViewVisibility(R.id.txt_acciones, View.VISIBLE)
                } catch (e: Exception) {
                    vistas.setTextViewText(R.id.txt_detalle, "Sin conexión · toca para reintentar")
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
