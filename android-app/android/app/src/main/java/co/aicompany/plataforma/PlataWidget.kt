package co.aicompany.plataforma

import android.appwidget.AppWidgetManager
import android.appwidget.AppWidgetProvider
import android.content.ComponentName
import android.content.Context
import android.content.Intent
import android.util.TypedValue
import android.widget.RemoteViews

/**
 * Widget PLATA (4x2): lo que entró y lo que salió este mes, y dos botones para
 * anotar un ingreso o un gasto sin abrir menús.
 *
 * El punto no es mirar la cifra: es que anotar cueste un toque. Si anotar cuesta
 * trabajo uno deja de hacerlo y las finanzas del sistema se vuelven mentira.
 */
class PlataWidget : AppWidgetProvider() {

    companion object {

        fun actualizar(ctx: Context, mgr: AppWidgetManager, id: Int) {
            val v = RemoteViews(ctx.packageName, R.layout.widget_plata)

            // Los botones no dependen de la red: sirven aunque no haya señal.
            v.setOnClickPendingIntent(R.id.plata_btn_entro,
                WidgetComun.abrir(ctx, id * 10 + 1, "/hoy?captura=1&tipo=ingreso"))
            v.setOnClickPendingIntent(R.id.plata_btn_gaste,
                WidgetComun.abrir(ctx, id * 10 + 2, "/hoy?captura=1&tipo=egreso"))
            v.setOnClickPendingIntent(R.id.plata_raiz,
                WidgetComun.abrir(ctx, id * 10 + 3, "/finanzas"))

            v.setTextViewText(R.id.plata_mes, WidgetComun.mesActual())

            val clave = WidgetComun.clave(ctx)
            if (clave.isNullOrBlank()) {
                v.setTextViewText(R.id.plata_libre, "Falta la clave")
                v.setTextViewTextSize(R.id.plata_libre, TypedValue.COMPLEX_UNIT_SP, 18f)
                v.setTextViewText(R.id.plata_estado, "sin clave")
                v.setTextViewText(R.id.plata_entro, "Quita el widget y vuelve a agregarlo")
                v.setTextViewText(R.id.plata_salio, "")
                mgr.updateAppWidget(id, v)
                return
            }

            mgr.updateAppWidget(id, v)

            WidgetComun.pedir(clave) { d ->
                if (d == null) {
                    v.setTextViewText(R.id.plata_estado, "sin datos")
                    v.setTextViewText(R.id.plata_entro, "Sin conexión")
                    v.setTextViewText(R.id.plata_salio, "")
                    v.setTextViewText(R.id.plata_libre, "—")
                } else {
                    val mes = d.optJSONObject("mes")
                    val entro = mes?.optDouble("ingresos", 0.0) ?: 0.0
                    val salio = mes?.optDouble("egresos", 0.0) ?: 0.0
                    val neto = entro - salio

                    val texto = WidgetComun.cop(neto)
                    v.setTextViewText(R.id.plata_libre, texto)
                    v.setTextViewTextSize(R.id.plata_libre, TypedValue.COMPLEX_UNIT_SP, WidgetComun.tamanoCifra(texto))
                    v.setTextViewText(R.id.plata_entro, "↑ " + WidgetComun.cop(entro))
                    v.setTextViewText(R.id.plata_salio, "↓ " + WidgetComun.cop(salio))

                    v.setTextViewText(R.id.plata_estado, when {
                        entro == 0.0 && salio == 0.0 -> "sin anotar"
                        neto < 0                     -> "en rojo"
                        neto < entro * 0.2           -> "ajustado"
                        else                         -> "vas bien"
                    })
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
            onUpdate(ctx, mgr, mgr.getAppWidgetIds(ComponentName(ctx, PlataWidget::class.java)))
        }
    }
}
