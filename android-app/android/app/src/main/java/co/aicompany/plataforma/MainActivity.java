package co.aicompany.plataforma;

import android.content.Intent;
import android.os.Bundle;

import com.getcapacitor.BridgeActivity;

/**
 * Actividad principal: abre la plataforma web en vivo.
 *
 * Los widgets la abren con un extra "ruta" (por ejemplo "/hoy?captura=1&tipo=egreso")
 * para caer directo en el capturador. Capacitor en modo servidor no navega solo
 * a una ruta que venga en el Intent, así que se hace aquí a mano.
 */
public class MainActivity extends BridgeActivity {

    public static final String EXTRA_RUTA = "ruta";
    private static final String ACCION_REFRESCAR = "co.aicompany.plataforma.REFRESCAR";
    private static final String BASE = "https://mi-plataforma-production.up.railway.app";

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        navegarSiHayRuta(getIntent());
    }

    /**
     * Al salir de la app se refrescan los widgets.
     *
     * Android solo los actualiza solo cada 30 minutos. Si uno anota un gasto y
     * vuelve al escritorio, el widget sigue mostrando lo de antes y parece que
     * no se guardó nada. Este es el momento exacto en que hay algo nuevo que
     * mostrar, y no cuesta nada.
     */
    @Override
    public void onPause() {
        super.onPause();
        sendBroadcast(new Intent(ACCION_REFRESCAR).setPackage(getPackageName()));
    }

    @Override
    protected void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        setIntent(intent);
        navegarSiHayRuta(intent);
    }

    private void navegarSiHayRuta(Intent intent) {
        if (intent == null) return;
        String ruta = intent.getStringExtra(EXTRA_RUTA);
        if (ruta == null || ruta.isEmpty()) return;
        if (!ruta.startsWith("/")) ruta = "/" + ruta;
        final String url = BASE + ruta;
        // El WebView puede no estar listo en onCreate: se encola en el hilo de UI.
        if (getBridge() != null && getBridge().getWebView() != null) {
            getBridge().getWebView().post(() -> getBridge().getWebView().loadUrl(url));
        }
        // Se limpia para que rotar la pantalla no vuelva a disparar la navegación
        intent.removeExtra(EXTRA_RUTA);
    }
}
