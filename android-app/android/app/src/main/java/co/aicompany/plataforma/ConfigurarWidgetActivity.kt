package co.aicompany.plataforma

import android.app.Activity
import android.appwidget.AppWidgetManager
import android.content.Context
import android.content.Intent
import android.graphics.Color
import android.graphics.Typeface
import android.os.Bundle
import android.text.InputType
import android.view.Gravity
import android.widget.Button
import android.widget.EditText
import android.widget.LinearLayout
import android.widget.TextView

/**
 * Pantalla que Android abre al agregar el widget: pide la WIDGET_KEY una vez.
 * Se construye en código (sin XML) para no arrastrar dependencias de diseño.
 */
class ConfigurarWidgetActivity : Activity() {

    private var widgetId = AppWidgetManager.INVALID_APPWIDGET_ID

    override fun onCreate(saved: Bundle?) {
        super.onCreate(saved)
        // Si el usuario cancela, Android no debe dejar un widget a medias
        setResult(RESULT_CANCELED)

        widgetId = intent?.extras?.getInt(AppWidgetManager.EXTRA_APPWIDGET_ID, AppWidgetManager.INVALID_APPWIDGET_ID)
            ?: AppWidgetManager.INVALID_APPWIDGET_ID
        if (widgetId == AppWidgetManager.INVALID_APPWIDGET_ID) { finish(); return }

        val dp = resources.displayMetrics.density
        val raiz = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            setPadding((24 * dp).toInt(), (32 * dp).toInt(), (24 * dp).toInt(), (24 * dp).toInt())
            setBackgroundColor(Color.parseColor("#F8FAFC"))
        }

        raiz.addView(TextView(this).apply {
            text = "Clave del widget"
            textSize = 22f; setTypeface(null, Typeface.BOLD)
            setTextColor(Color.parseColor("#0F172A"))
        })
        raiz.addView(TextView(this).apply {
            text = "Es la variable WIDGET_KEY de Railway. Solo sirve para leer el resumen: no da acceso a nada más."
            textSize = 14f; setTextColor(Color.parseColor("#64748B"))
            setPadding(0, (8 * dp).toInt(), 0, (20 * dp).toInt())
        })

        val existente = ResumenWidget.claveGuardada(this)
        val campo = EditText(this).apply {
            hint = "Pega la clave aquí"
            inputType = InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD
            setText(existente ?: "")
            textSize = 16f
        }
        raiz.addView(campo)

        raiz.addView(Button(this).apply {
            text = "Guardar y poner el widget"
            setBackgroundColor(Color.parseColor("#2563EB")); setTextColor(Color.WHITE)
            val lp = LinearLayout.LayoutParams(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT)
            lp.topMargin = (20 * dp).toInt(); layoutParams = lp
            setOnClickListener {
                val clave = campo.text.toString().trim()
                if (clave.length < 8) { campo.error = "Muy corta"; return@setOnClickListener }
                getSharedPreferences(ResumenWidget.PREFS, Context.MODE_PRIVATE)
                    .edit().putString(ResumenWidget.PREF_KEY, clave).apply()

                val mgr = AppWidgetManager.getInstance(this@ConfigurarWidgetActivity)
                ResumenWidget.actualizar(this@ConfigurarWidgetActivity, mgr, widgetId)

                setResult(RESULT_OK, Intent().putExtra(AppWidgetManager.EXTRA_APPWIDGET_ID, widgetId))
                finish()
            }
        })

        raiz.gravity = Gravity.TOP
        setContentView(raiz)
    }
}
