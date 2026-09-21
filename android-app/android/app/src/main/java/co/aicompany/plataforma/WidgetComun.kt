package co.aicompany.plataforma

import android.app.PendingIntent
import android.appwidget.AppWidgetManager
import android.content.Context
import android.content.Intent
import org.json.JSONObject
import java.net.HttpURLConnection
import java.net.URL
import java.text.NumberFormat
import java.util.Calendar
import java.util.Locale
import java.util.concurrent.Executors

/**
 * Lo que comparten los tres widgets: la clave, la llamada a /api/widget, el
 * formato de plata y los intents que abren la app en una ruta concreta.
 *
 * La clave se guarda una sola vez (ConfigurarWidgetActivity) y la usan todos:
 * es la misma WIDGET_KEY del servidor y solo sirve para leer el resumen.
 */
object WidgetComun {

    const val BASE_URL = "https://mi-plataforma-production.up.railway.app"
    const val ACCION_REFRESCAR = "co.aicompany.plataforma.REFRESCAR"

    private val hilo = Executors.newSingleThreadExecutor()

    fun clave(ctx: Context): String? = ResumenWidget.claveGuardada(ctx)

    /** $1.250.000 — sin decimales, con puntos de miles como se lee en Colombia. */
    fun cop(n: Double): String {
        val f = NumberFormat.getInstance(Locale("es", "CO"))
        f.maximumFractionDigits = 0
        val signo = if (n < 0) "-" else ""
        return signo + "$" + f.format(Math.abs(n))
    }

    /** Corto, para donde no cabe la cifra entera: $1,28M o $250K. */
    fun copCorto(n: Double): String {
        val a = Math.abs(n)
        val signo = if (n < 0) "-" else ""
        return when {
            a >= 1_000_000 -> signo + "$" + String.format(Locale("es", "CO"), "%.2f", a / 1_000_000).replace('.', ',') + "M"
            a >= 1_000     -> signo + "$" + Math.round(a / 1000) + "K"
            else           -> cop(n)
        }
    }

    /** Para la cifra grande: si no cabe en 27sp, se baja el tamaño en vez de cortar. */
    fun tamanoCifra(texto: String): Float = when {
        texto.length <= 10 -> 27f
        texto.length <= 12 -> 23f
        else -> 20f
    }

    private val MESES = arrayOf("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO",
        "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE")
    private val MES_CORTO = arrayOf("ene", "feb", "mar", "abr", "may", "jun",
        "jul", "ago", "sep", "oct", "nov", "dic")
    private val DIA_CORTO = arrayOf("dom", "lun", "mar", "mié", "jue", "vie", "sáb")

    fun mesActual(): String = MESES[Calendar.getInstance().get(Calendar.MONTH)]

    fun saludo(): String {
        val h = Calendar.getInstance().get(Calendar.HOUR_OF_DAY)
        return when { h < 12 -> "BUENOS DÍAS"; h < 19 -> "BUENAS TARDES"; else -> "BUENAS NOCHES" }
    }

    /** "sáb 20 de sep" — el servidor manda la fecha de Bogotá como "2026-09-20". */
    fun fechaBonita(iso: String?): String {
        val c = Calendar.getInstance()
        if (!iso.isNullOrBlank() && iso.length >= 10) {
            try {
                c.set(iso.substring(0, 4).toInt(), iso.substring(5, 7).toInt() - 1, iso.substring(8, 10).toInt())
            } catch (_: Exception) { /* si viene raro, se usa la fecha del celular */ }
        }
        val dia = DIA_CORTO[c.get(Calendar.DAY_OF_WEEK) - 1]
        return "$dia ${c.get(Calendar.DAY_OF_MONTH)} de ${MES_CORTO[c.get(Calendar.MONTH)]}"
    }

    /** Abre la app en una ruta ("/hoy?captura=1&tipo=egreso"). */
    fun abrir(ctx: Context, codigo: Int, ruta: String): PendingIntent {
        val i = Intent(ctx, MainActivity::class.java).apply {
            addFlags(Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_SINGLE_TOP)
            putExtra(MainActivity.EXTRA_RUTA, ruta)
            // Sin datos distintos, Android reusaría el mismo PendingIntent para los
            // dos botones y ambos abrirían la misma ruta.
            data = android.net.Uri.parse("aicompany://widget/$codigo$ruta")
        }
        return PendingIntent.getActivity(ctx, codigo, i,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE)
    }

    /**
     * Resultado de pedir el resumen. Si [error] no es nulo, ese es el texto que
     * se le muestra a Cristian tal cual. [esClave] distingue "no hay red" de
     * "la clave esta mal", que es un problema con arreglo distinto: tocar el
     * widget lo lleva a escribirla otra vez.
     */
    class Resp(val datos: org.json.JSONObject?, val error: String?, val esClave: Boolean = false)

    /** Pide el resumen en segundo plano. El callback llega en ese mismo hilo. */
    fun pedir(clave: String, listo: (Resp) -> Unit) {
        hilo.execute {
            var r: Resp
            var con: HttpURLConnection? = null
            try {
                con = (URL("$BASE_URL/api/widget").openConnection() as HttpURLConnection).apply {
                    connectTimeout = 10_000; readTimeout = 10_000
                    setRequestProperty("Accept", "application/json")
                    // Por cabecera y no en la URL: asi la clave no queda escrita
                    // en registros de acceso ni se daña si trae caracteres raros.
                    setRequestProperty("X-Widget-Key", clave)
                }
                val codigo = con.responseCode
                r = when {
                    codigo == 200 -> {
                        val d = JSONObject(con.inputStream.bufferedReader().readText())
                        if (d.optBoolean("ok")) Resp(d, null)
                        else Resp(null, d.optString("msg", "Error del servidor"))
                    }
                    codigo == 401 -> Resp(null, "Clave incorrecta · toca para escribirla", true)
                    codigo == 503 -> Resp(null, "Falta WIDGET_KEY en el servidor", true)
                    else          -> Resp(null, "El servidor respondio $codigo")
                }
            } catch (e: Exception) {
                r = Resp(null, "Sin conexion · toca para reintentar")
            } finally {
                try { con?.disconnect() } catch (_: Exception) {}
            }
            listo(r)
        }
    }

    /** Vuelve a pedir los datos sin abrir la app. */
    fun reintentar(ctx: Context, codigo: Int): PendingIntent =
        PendingIntent.getBroadcast(ctx, codigo,
            Intent(ACCION_REFRESCAR).setPackage(ctx.packageName),
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE)

    /** Abre la pantalla de la clave para ese widget. */
    fun configurar(ctx: Context, codigo: Int, widgetId: Int): PendingIntent {
        val i = Intent(ctx, ConfigurarWidgetActivity::class.java).apply {
            addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            putExtra(AppWidgetManager.EXTRA_APPWIDGET_ID, widgetId)
        }
        return PendingIntent.getActivity(ctx, codigo, i,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE)
    }
}
