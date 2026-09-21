import { Component, lazy } from 'react';

// Dos redes de seguridad para que la app nunca se quede en blanco.
//
// El problema real: cada despliegue le cambia el nombre a los archivos de cada
// pantalla (son hasheados). Si la app ya estaba abierta y entras a una sección
// que no habías abierto, pide un archivo que ya no existe, el import falla, y
// como React no tiene dónde agarrar el error desmonta TODO el árbol. Resultado:
// pantalla blanca y sensación de que la app se cerró. Pasaba "a veces" porque
// solo afectaba a las pantallas no visitadas antes del despliegue.

const MARCA = 'recarga-por-version';

/**
 * Igual que lazy(), pero si el archivo no está (despliegue nuevo) recarga la
 * página una sola vez. La marca vive en sessionStorage para no entrar en un
 * bucle de recargas si el fallo es otro.
 */
export function pantalla(importar) {
  return lazy(() =>
    importar().catch(err => {
      let yaRecargo = false;
      try { yaRecargo = sessionStorage.getItem(MARCA) === '1'; } catch (_) { /* modo privado */ }
      if (!yaRecargo) {
        try { sessionStorage.setItem(MARCA, '1'); } catch (_) { /* da igual */ }
        window.location.reload();
        // Se devuelve algo válido para que React no explote mientras recarga.
        return { default: () => null };
      }
      throw err;
    })
  );
}

/** Al cargar bien una pantalla, se borra la marca: la próxima vez puede recargar. */
export function olvidarRecarga() {
  try { sessionStorage.removeItem(MARCA); } catch (_) { /* da igual */ }
}

/**
 * Si algo más revienta (un dato inesperado, un campo nulo), se muestra el
 * error y dos botones. Cualquier cosa es mejor que una pantalla blanca sin
 * explicación de la que uno no sabe cómo salir.
 */
export class Red extends Component {
  constructor(props) {
    super(props);
    this.state = { error: null };
  }

  static getDerivedStateFromError(error) {
    return { error };
  }

  componentDidCatch(error, info) {
    // Queda en la consola del navegador para poder verlo con el celular conectado
    console.error('Pantalla caída:', error, info?.componentStack);
  }

  render() {
    if (!this.state.error) return this.props.children;
    return (
      <div style={S.caja}>
        <div style={S.icono}>⚠️</div>
        <h2 style={S.titulo}>Esta pantalla se cayó</h2>
        <p style={S.texto}>
          No es tu culpa ni se perdió nada. Vuelve a cargar y sigue.
        </p>
        <pre style={S.detalle}>{String(this.state.error?.message || this.state.error).slice(0, 200)}</pre>
        <div style={S.botones}>
          <button style={S.principal} onClick={() => window.location.reload()}>Recargar</button>
          <button style={S.secundario} onClick={() => { window.location.href = '/hoy'; }}>Ir a Hoy</button>
        </div>
      </div>
    );
  }
}

const S = {
  caja: { minHeight: '60vh', display: 'flex', flexDirection: 'column', alignItems: 'center',
          justifyContent: 'center', padding: '32px 20px', textAlign: 'center' },
  icono: { fontSize: '2.4rem', marginBottom: 10 },
  titulo: { fontSize: '1.15rem', fontWeight: 700, color: '#0F172A', margin: '0 0 6px' },
  texto: { fontSize: '.9rem', color: '#64748B', margin: '0 0 14px', maxWidth: 320 },
  detalle: { fontSize: '.7rem', color: '#94A3B8', background: '#F8FAFC', padding: '8px 10px',
             borderRadius: 8, maxWidth: '100%', overflowX: 'auto', margin: '0 0 18px' },
  botones: { display: 'flex', gap: 10 },
  principal: { padding: '12px 22px', borderRadius: 12, border: 'none', background: '#2563EB',
               color: '#fff', fontWeight: 700, fontSize: '.9rem', cursor: 'pointer' },
  secundario: { padding: '12px 22px', borderRadius: 12, border: '1px solid #E2E8F0',
                background: '#fff', color: '#475569', fontWeight: 600, fontSize: '.9rem', cursor: 'pointer' },
};
