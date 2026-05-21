import { useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

const pageNames = {
  '/': 'Dashboard',
  '/unidades': 'Unidades',
  '/residentes': 'Residentes',
  '/cobranza': 'Cobranza',
  '/contabilidad': 'Contabilidad',
  '/mantenimiento': 'Mantenimiento',
  '/amenidades': 'Amenidades',
  '/acceso': 'Control de Acceso',
  '/comunicaciones': 'Comunicaciones',
  '/proveedores': 'Proveedores',
  '/reportes': 'Reportes',
  '/configuracion': 'Configuración',
  '/sistema': 'Mantenimiento del Sistema',
};

export default function Header({ collapsed, onToggle }) {
  const location = useLocation();
  const { usuario } = useAuth();
  const pageName = pageNames[location.pathname] || 'CRM';
  const initials = usuario ? (usuario.nombre?.[0] || '') + (usuario.apellidos?.[0] || '') : 'A';

  return (
    <header className={`header ${collapsed ? 'sidebar-collapsed' : ''}`}>
      {/* Toggle sidebar */}
      <button
        onClick={onToggle}
        style={{
          width: 36, height: 36,
          borderRadius: 8,
          background: 'var(--bg-main)',
          border: '1.5px solid var(--border)',
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          fontSize: 16, cursor: 'pointer', color: 'var(--text-secondary)',
          flexShrink: 0,
        }}
      >
        {collapsed ? '→' : '←'}
      </button>

      <div>
        <div className="header-title">{pageName}</div>
        <div className="header-subtitle">
          {new Date().toLocaleDateString('es-MX', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
        </div>
      </div>

      {/* Búsqueda */}
      <div className="header-search">
        <span className="header-search-icon">🔍</span>
        <input className="header-search-input" placeholder="Buscar..." />
      </div>

      {/* Acciones */}
      <div className="header-actions">
        <button className="header-btn" title="Notificaciones">
          🔔
          <span className="header-btn-badge">3</span>
        </button>
        <button className="header-btn" title="Mensajes">✉️</button>

        <div className="header-user">
          <div className="header-user-avatar">{initials.toUpperCase()}</div>
          <div>
            <div className="header-user-name">{usuario?.nombre}</div>
            <div className="header-user-role">{usuario?.rol}</div>
          </div>
          <span style={{ color: 'var(--text-muted)', fontSize: 12 }}>▾</span>
        </div>
      </div>
    </header>
  );
}
