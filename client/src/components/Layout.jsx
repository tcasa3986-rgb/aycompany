import { Outlet, NavLink, useNavigate } from 'react-router-dom';
import { useAuthStore } from '../store/authStore';
import { LayoutDashboard, Users, Package, Key, CreditCard, FileText, LogOut, TrendingUp, Calendar, Lightbulb, MessageCircle } from 'lucide-react';

const nav = [
  { to: '/dashboard',  icon: LayoutDashboard, label: 'Dashboard' },
  { to: '/clientes',   icon: Users,           label: 'Clientes' },
  { to: '/productos',  icon: Package,         label: 'Productos' },
  { to: '/licencias',  icon: Key,             label: 'Licencias' },
  { to: '/pagos',      icon: CreditCard,      label: 'Pagos' },
  { to: '/facturas',   icon: FileText,        label: 'Facturas' },
  { to: '/marketing',  icon: TrendingUp,      label: 'Marketing' },
  { to: '/calendario', icon: Calendar,        label: 'Calendario' },
  { to: '/contenido',  icon: Lightbulb,       label: 'Contenido' },
  { to: '/social',     icon: MessageCircle,   label: 'Bandeja Social' },
];

export default function Layout() {
  const { user, logout } = useAuthStore();
  const navigate = useNavigate();

  function handleLogout() {
    logout();
    navigate('/');
  }

  return (
    <div style={{ display: 'flex', height: '100vh', overflow: 'hidden' }}>
      {/* Sidebar */}
      <aside style={{ width: 220, background: '#1e1b4b', color: '#fff', display: 'flex', flexDirection: 'column', padding: '24px 0', flexShrink: 0, overflowY: 'auto' }}>
        <div style={{ padding: '0 20px 24px', borderBottom: '1px solid rgba(255,255,255,.1)' }}>
          <div style={{ fontSize: '1.1rem', fontWeight: 700, color: '#a5b4fc' }}>⚡ Mi Plataforma</div>
          <div style={{ fontSize: '.78rem', color: '#94a3b8', marginTop: 4 }}>{user?.nombre}</div>
        </div>
        <nav style={{ flex: 1, padding: '12px 0' }}>
          {nav.map(({ to, icon: Icon, label }) => (
            <NavLink key={to} to={to} style={({ isActive }) => ({
              display: 'flex', alignItems: 'center', gap: 10, padding: '10px 20px',
              color: isActive ? '#fff' : '#94a3b8',
              background: isActive ? 'rgba(99,102,241,.3)' : 'transparent',
              borderLeft: isActive ? '3px solid #818cf8' : '3px solid transparent',
              fontSize: '.9rem', transition: 'all .15s'
            })}>
              <Icon size={17} />
              {label}
            </NavLink>
          ))}
        </nav>
        <button onClick={handleLogout} style={{ display: 'flex', alignItems: 'center', gap: 10, margin: '0 12px', padding: '10px 12px', background: 'rgba(239,68,68,.15)', color: '#fca5a5', border: 'none', borderRadius: 8, fontSize: '.88rem' }}>
          <LogOut size={16} /> Cerrar sesión
        </button>
      </aside>

      {/* Main */}
      <main style={{ flex: 1, overflow: 'auto', position: 'relative', minHeight: 0 }}>
        <Outlet />
      </main>
    </div>
  );
}
