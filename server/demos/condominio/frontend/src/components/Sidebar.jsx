import { NavLink, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useTheme } from '../context/ThemeContext';

const navItems = [
  { path: '/', icon: '⊞', label: 'Dashboard', group: 'principal', roles: ['all'] },
  { path: '/unidades', icon: '🏠', label: 'Unidades', group: 'administración', roles: ['super_admin','administrador','contador'] },
  { path: '/residentes', icon: '👥', label: 'Residentes', group: 'administración', roles: ['super_admin','administrador'] },
  { path: '/cobranza', icon: '💰', label: 'Cobranza', group: 'administración', roles: ['super_admin','administrador','contador'] },
  { path: '/contabilidad', icon: '📊', label: 'Contabilidad', group: 'administración', roles: ['super_admin','administrador','contador'] },
  { path: '/mantenimiento', icon: '🔧', label: 'Mantenimiento', group: 'operaciones', roles: ['super_admin','administrador','mantenimiento'] },
  { path: '/amenidades', icon: '🏋️', label: 'Amenidades', group: 'operaciones', roles: ['super_admin','administrador','residente'] },
  { path: '/acceso', icon: '🛡️', label: 'Control Acceso', group: 'operaciones', roles: ['super_admin','administrador','guardia'] },
  { path: '/comunicaciones', icon: '📢', label: 'Comunicaciones', group: 'comunicación', roles: ['all'] },
  { path: '/proveedores', icon: '📋', label: 'Proveedores', group: 'comunicación', roles: ['super_admin','administrador','contador'] },
  { path: '/reportes', icon: '📑', label: 'Reportes', group: 'reportes', roles: ['super_admin','administrador','contador'] },
  { path: '/configuracion', icon: '⚙️', label: 'Configuración', group: 'sistema', roles: ['super_admin','administrador'] },
  { path: '/sistema', icon: '🛡️', label: 'Mantenimiento Sist.', group: 'sistema', roles: ['super_admin', 'administrador'] },
];

const groups = ['principal', 'administración', 'operaciones', 'comunicación', 'reportes', 'sistema'];

export default function Sidebar({ collapsed, onToggle }) {
  const { usuario, logout } = useAuth();
  const { darkMode, toggleTheme } = useTheme();
  const location = useLocation();

  const canAccess = (roles) => {
    if (roles.includes('all')) return true;
    return roles.includes(usuario?.rol);
  };

  const initials = usuario ? (usuario.nombre?.[0] || '') + (usuario.apellidos?.[0] || '') : 'A';

  return (
    <aside className={`sidebar ${collapsed ? 'collapsed' : ''}`}>
      {/* Logo */}
      <div className="sidebar-logo">
        <div className="sidebar-logo-icon">C</div>
        {!collapsed && (
          <div className="sidebar-logo-text">
            <div className="sidebar-logo-title">CRM Condominio</div>
            <div className="sidebar-logo-sub">Sistema de Administración</div>
          </div>
        )}
      </div>

      {/* Perfil */}
      {!collapsed && (
        <div className="sidebar-profile">
          <div className="sidebar-avatar">{initials.toUpperCase()}</div>
          <div className="sidebar-profile-info">
            <div className="sidebar-profile-name">{usuario?.nombre} {usuario?.apellidos}</div>
            <div className="sidebar-profile-role">{usuario?.rol}</div>
          </div>
        </div>
      )}

      {/* Navegación */}
      <nav className="sidebar-nav">
        {groups.map(group => {
          const items = navItems.filter(n => n.group === group && canAccess(n.roles));
          if (!items.length) return null;
          return (
            <div className="sidebar-section" key={group}>
              {!collapsed && <div className="sidebar-section-label">{group}</div>}
              {items.map(item => (
                <NavLink
                  key={item.path}
                  to={item.path}
                  end={item.path === '/'}
                  className={({ isActive }) => `nav-item ${isActive ? 'active' : ''}`}
                  title={collapsed ? item.label : undefined}
                >
                  <span className="nav-icon">{item.icon}</span>
                  {!collapsed && <span className="nav-label">{item.label}</span>}
                </NavLink>
              ))}
            </div>
          );
        })}
      </nav>

      {/* Footer */}
      <div className="sidebar-footer">
        {!collapsed && (
          <div className="sidebar-theme-toggle">
            <span style={{ fontSize: '16px' }}>{darkMode ? '🌙' : '☀️'}</span>
            <span style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)' }}>
              Modo {darkMode ? 'Oscuro' : 'Claro'}
            </span>
            <div className={`toggle-switch ${darkMode ? 'on' : ''}`} onClick={toggleTheme} />
          </div>
        )}
        <div
          className="nav-item"
          style={{ marginTop: '4px' }}
          onClick={logout}
          title="Cerrar sesión"
        >
          <span className="nav-icon">🚪</span>
          {!collapsed && <span className="nav-label">Cerrar sesión</span>}
        </div>
      </div>
    </aside>
  );
}
