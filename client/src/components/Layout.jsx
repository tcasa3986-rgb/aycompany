import { Outlet, NavLink, useNavigate } from 'react-router-dom';
import { useAuthStore } from '../store/authStore';
import { LayoutDashboard, Users, Package, Key, CreditCard, FileText, LogOut, TrendingUp, Calendar, Lightbulb, MessageCircle, UserPlus, Bot, Radar, Headphones, AlertCircle, FolderOpen, GitBranch, ScrollText, Settings, UserCog, BarChart2 } from 'lucide-react';

const nav = [
  { group: 'Principal' },
  { to: '/dashboard',      icon: LayoutDashboard, label: 'Dashboard' },
  { to: '/clientes',       icon: Users,            label: 'Clientes' },
  { to: '/pipeline',       icon: GitBranch,        label: 'Pipeline CRM' },
  { to: '/leads',          icon: UserPlus,         label: 'Leads' },

  { group: 'Operaciones' },
  { to: '/licencias',      icon: Key,              label: 'Licencias' },
  { to: '/pagos',          icon: CreditCard,       label: 'Pagos' },
  { to: '/facturas',       icon: FileText,         label: 'Facturas' },
  { to: '/cartera',        icon: AlertCircle,      label: 'Cartera' },
  { to: '/contratos',      icon: ScrollText,       label: 'Contratos' },
  { to: '/proyectos',      icon: FolderOpen,       label: 'Proyectos' },
  { to: '/tickets',        icon: Headphones,       label: 'Tickets' },

  { group: 'Marketing' },
  { to: '/marketing',      icon: TrendingUp,       label: 'Marketing' },
  { to: '/calendario',     icon: Calendar,         label: 'Calendario' },
  { to: '/contenido',      icon: Lightbulb,        label: 'Contenido' },
  { to: '/social',         icon: MessageCircle,    label: 'Bandeja Social' },
  { to: '/prospector',     icon: Radar,            label: 'Prospector' },
  { to: '/agente',         icon: Bot,              label: 'Agente IA' },

  { group: 'Sistema' },
  { to: '/productos',      icon: Package,          label: 'Productos' },
  { to: '/usuarios',       icon: UserCog,          label: 'Usuarios' },
  { to: '/reportes',       icon: BarChart2,        label: 'Reportes' },
  { to: '/configuracion',  icon: Settings,         label: 'Configuración' },
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
          <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
            {/* Logo AI Company */}
            <img src="/logo-ai-company.png" alt="AI Company" style={{ width: 32, height: 32, objectFit: 'contain' }} onError={e => { e.target.style.display = 'none'; }} />
            <div style={{ fontSize: '1.1rem', fontWeight: 700, color: '#a5b4fc' }}>AI Company</div>
          </div>
          <div style={{ fontSize: '.78rem', color: '#94a3b8', marginTop: 4 }}>{user?.nombre}</div>
        </div>
        <nav style={{ flex: 1, padding: '12px 0' }}>
          {nav.map((item, i) => {
            if (item.group) return (
              <div key={i} style={{ padding: '10px 20px 4px', fontSize: '.68rem', fontWeight: 700, color: 'rgba(255,255,255,.3)', textTransform: 'uppercase', letterSpacing: '.08em' }}>
                {item.group}
              </div>
            );
            const { to, icon: Icon, label } = item;
            return (
              <NavLink key={to} to={to} style={({ isActive }) => ({
                display: 'flex', alignItems: 'center', gap: 10, padding: '9px 20px',
                color: isActive ? '#fff' : '#94a3b8',
                background: isActive ? 'rgba(99,102,241,.3)' : 'transparent',
                borderLeft: isActive ? '3px solid #818cf8' : '3px solid transparent',
                fontSize: '.88rem', transition: 'all .15s', textDecoration: 'none'
              })}>
                <Icon size={16} />
                {label}
              </NavLink>
            );
          })}
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
