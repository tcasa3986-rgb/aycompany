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
 * Pantalla que Android abre al agregar cualquiera de los widgets: pide la
 * WIDGET_KEY. La clave es una sola para los tres, así que al agregar el segundo
 * ya viene escrita y basta con darle al botón.
 *
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

        val existente = ResumenWidget.claveGuardada(this)

        raiz.addView(TextView(this).apply {
            text = "Clave del widget"
            textSize = 22f; setTypeface(null, Typeface.BOLD)
            setTextColor(Color.parseColor("#0F172A"))
        })
        raiz.addView(TextView(this).apply {
            text = if (existente.isNullOrBlank())
                "Es la variable WIDGET_KEY de Railway. Solo sirve para leer el resumen: no da acceso a nada más."
            else
                "Ya la habías guardado. Dale al botón y listo."
            textSize = 14f; setTextColor(Color.parseColor("#64748B"))
            setPadding(0, (8 * dp).toInt(), 0, (20 * dp).toInt())
        })

        val campo = EditText(this).apply {
            hint = "Pega la clave aquí"
            inputType = InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD
            setText(existente ?: "")
            textSize = 16f
        }
        raiz.addView(campo)

        val aviso = TextView(this).apply {
            textSize = 13f; setTextColor(Color.parseColor("#DC2626"))
            setPadding(0, (10 * dp).toInt(), 0, 0)
            visibility = android.view.View.GONE
        }

        val boton = Button(this).apply {
            text = "Poner el widget"
            setBackgroundColor(Color.parseColor("#2563EB")); setTextColor(Color.WHITE)
            val lp = LinearLayout.LayoutParams(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT)
            lp.topMargin = (20 * dp).toInt(); layoutParams = lp
        }
        raiz.addView(boton)
        raiz.addView(aviso)

        boton.setOnClickListener {
            val clave = campo.text.toString().trim()
            if (clave.length < 8) { campo.error = "Muy corta"; return@setOnClickListener }

            // Se prueba contra el servidor ANTES de guardarla. Si no, una clave
            // mal escrita se guarda igual y el widget queda diciendo "sin
            // conexion" para siempre, que es mentira y no se sabe como arreglar.
            boton.isEnabled = false
            boton.text = "Probando la clave…"
            aviso.visibility = android.view.View.GONE

            WidgetComun.pedir(clave) { r ->
                runOnUiThread {
                    // Sin red no se puede comprobar: se guarda y el widget dira
                    // que reintente. Con 401 NO se guarda: esa clave no sirve.
                    if (r.error != null && r.esClave) {
                        boton.isEnabled = true
                        boton.text = "Poner el widget"
                        aviso.text = "Esa clave no es. Cópiala de Railway → mi-plataforma → Variables → WIDGET_KEY."
                        aviso.visibility = android.view.View.VISIBLE
                        return@runOnUiThread
                    }
                    getSharedPreferences(ResumenWidget.PREFS, Context.MODE_PRIVATE)
                        .edit().putString(ResumenWidget.PREF_KEY, clave).apply()
                    pintarWidget()
                    setResult(RESULT_OK, Intent().putExtra(AppWidgetManager.EXTRA_APPWIDGET_ID, widgetId))
                    pedirExencionDeBateria()
                    finish()
                }
            }
        }

        raiz.gravity = Gravity.TOP
        setContentView(raiz)
    }

    /**
     * Con el ahorro de batería encendido, Android bloquea la red de la app en
     * segundo plano: el widget intenta, le cortan el DNS en 0 ms y queda
     * diciendo "sin conexión" — que es mentira y no se puede adivinar desde el
     * teléfono. Se le pide al sistema que exente la app; es un toque.
     */
    private fun pedirExencionDeBateria() {
        if (android.os.Build.VERSION.SDK_INT < 23) return
        try {
            val pm = getSystemService(Context.POWER_SERVICE) as android.os.PowerManager
            if (pm.isIgnoringBatteryOptimizations(packageName)) return
            startActivity(Intent(
                android.provider.Settings.ACTION_REQUEST_IGNORE_BATTERY_OPTIMIZATIONS,
                android.net.Uri.parse("package:$packageName")
            ))
        } catch (e: Exception) {
            // Algunos fabricantes esconden esa pantalla: no vale la pena tumbar
            // la configuración del widget por esto.
        }
    }

    /**
     * El id no dice de qué widget se trata: hay que preguntarle a Android por el
     * proveedor. Si por lo que sea no lo sabe, se refrescan todos — la clave es
     * la misma y sobra trabajo, no falla nada.
     */
    private fun pintarWidget() {
        val mgr = AppWidgetManager.getInstance(this)
        val clase = try { mgr.getAppWidgetInfo(widgetId)?.provider?.className } catch (e: Exception) { null }
        when (clase) {
            PlataWidget::class.java.name   -> PlataWidget.actualizar(this, mgr, widgetId)
            HoyWidget::class.java.name     -> HoyWidget.actualizar(this, mgr, widgetId)
            ResumenWidget::class.java.name -> ResumenWidget.actualizar(this, mgr, widgetId)
            else -> sendBroadcast(Intent(WidgetComun.ACCION_REFRESCAR).setPackage(packageName))
        }
    }
}
