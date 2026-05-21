import { useState } from 'react';
import { useAuth } from '../context/AuthContext';

export default function LoginPage() {
  const { login } = useAuth();
  const [email, setEmail] = useState('admin@laspalmas.com');
  const [password, setPassword] = useState('Admin123!');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      await login(email, password);
    } catch (err) {
      setError(err.response?.data?.message || 'Error al iniciar sesión. Verifica tus credenciales.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="login-page">
      <div className="login-decoration" />

      {/* Panel Izquierdo — Información del sistema */}
      <div className="login-left">
        <div className="login-brand">
          <div className="login-brand-icon">🏢</div>
          <div className="login-brand-name">CRM Condominio</div>
          <div className="login-brand-sub">Sistema Integral de Administración</div>
        </div>

        <div className="login-features">
          {[
            { icon: '💰', title: 'Cobranza Inteligente', text: 'Gestiona cuotas, pagos y morosos automáticamente' },
            { icon: '🔧', title: 'Mantenimiento', text: 'Órdenes de trabajo y seguimiento de proveedores' },
            { icon: '🛡️', title: 'Control de Acceso', text: 'Registro de visitantes y seguridad 24/7' },
            { icon: '📢', title: 'Comunicaciones', text: 'Anuncios, asambleas y mensajería interna' },
          ].map((f, i) => (
            <div className="login-feature" key={i}>
              <div className="login-feature-icon">{f.icon}</div>
              <div>
                <div className="login-feature-title">{f.title}</div>
                <div className="login-feature-text">{f.text}</div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Panel Derecho — Formulario */}
      <div className="login-right">
        <div className="login-form-wrapper">
          <div className="login-form-title">Bienvenido 👋</div>
          <div className="login-form-sub">Ingresa a tu panel de administración</div>

          {error && <div className="login-error">⚠️ {error}</div>}

          <form onSubmit={handleSubmit}>
            <div className="login-input-group">
              <label className="login-input-label">Correo electrónico</label>
              <input
                type="email"
                className="login-input"
                value={email}
                onChange={e => setEmail(e.target.value)}
                placeholder="admin@condominio.com"
                required
              />
            </div>

            <div className="login-input-group">
              <label className="login-input-label">Contraseña</label>
              <input
                type="password"
                className="login-input"
                value={password}
                onChange={e => setPassword(e.target.value)}
                placeholder="••••••••"
                required
              />
            </div>

            <div style={{ display: 'flex', justifyContent: 'flex-end', marginBottom: 24 }}>
              <span style={{ fontSize: 13, color: 'var(--primary-light)', cursor: 'pointer' }}>
                ¿Olvidaste tu contraseña?
              </span>
            </div>

            <button type="submit" className="login-btn" disabled={loading}>
              {loading ? (
                <>
                  <div className="spinner" style={{ width: 20, height: 20, borderWidth: 2 }} />
                  Iniciando sesión...
                </>
              ) : (
                <>🚀 Iniciar sesión</>
              )}
            </button>
          </form>

          <div className="login-demo">
            <p>Credenciales de prueba:</p>
            <strong>Email:</strong> admin@laspalmas.com<br />
            <strong>Contraseña:</strong> Admin123!
          </div>
        </div>
      </div>
    </div>
  );
}
