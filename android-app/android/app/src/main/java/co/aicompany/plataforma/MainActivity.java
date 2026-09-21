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
    private static final String BASE = "https://mi-plataforma-production.up.railway.app";

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        navegarSiHayRuta(getIntent());
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
