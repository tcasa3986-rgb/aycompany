import React, { useState } from 'react';
import { NavLink, useNavigate, useLocation } from 'react-router-dom';
import {
  LayoutDashboard, Users, MapPin, Package, TrendingUp,
  CalendarCheck, CreditCard, Truck, Megaphone, CheckSquare,
  BarChart2, Settings, ChevronRight, Plane, LogOut, Shield
} from 'lucide-react';
import useAuthStore from '../../store/authStore';

const menus = [
  {
    label: 'Principal',
    items: [
      { icon: LayoutDashboard, label: 'Dashboard', path: '/' },
    ]
  },
  {
    label: 'CRM',
    items: [
      { icon: Users,        label: 'Clientes',   path: '/clientes' },
      { icon: TrendingUp,   label: 'Pipeline',   path: '/pipeline' },
      { icon: CheckSquare,  label: 'Tareas',     path: '/tareas' },
    ]
  },
  {
    label: 'Catálogo',
    items: [
      { icon: MapPin,   label: 'Destinos',  path: '/destinos' },
      { icon: Package,  label: 'Paquetes',  path: '/paquetes' },
      { icon: Truck,    label: 'Proveedores',path: '/proveedores'},
    ]
  },
  {
    label: 'Ventas',
    items: [
      { icon: CalendarCheck, label: 'Reservas',  path: '/reservas' },
      { icon: CreditCard,    label: 'Pagos',     path: '/pagos' },
    ]
  },
  {
    label: 'Marketing & Análisis',
    items: [
      { icon: Megaphone, label: 'Campañas',  path: '/campanas' },
      { icon: BarChart2, label: 'Reportes',  path: '/reportes' },
    ]
  },
  {
    label: 'Sistema',
    items: [
      { icon: Settings, label: 'Configuración',  path: '/configuracion' },
      { icon: Shield,   label: 'Mantenimiento',  path: '/mantenimiento' },
    ]
  },
];

export default function Sidebar({ collapsed, onToggle, mobileOpen, onClose }) {
  const { usuario, logout } = useAuthStore();
  const navigate = useNavigate();
  const location = useLocation();

  const iniciales = usuario
    ? `${usuario.nombre?.[0] || ''}${usuario.apellido?.[0] || ''}`.toUpperCase()
    : 'U';

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <>
      {/* Backdrop para móvil */}
      {mobileOpen && <div className="sidebar-backdrop" onClick={onClose} />}

      <aside className={`sidebar ${collapsed ? 'collapsed' : ''} ${mobileOpen ? 'mobile-open' : ''}`}>
        {/* Header / Logo */}
        <div className="sidebar-header">
          <div className="sidebar-logo">
            <Plane size={18} color="white" />
          </div>
          {(!collapsed || mobileOpen) && (
            <span className="sidebar-brand">Viaje 360</span>
          )}
        </div>

        {/* Navegación */}
        <nav className="sidebar-nav">
          {menus.map((section) => (
            <div className="nav-section" key={section.label}>
              {(!collapsed || mobileOpen) && (
                <div className="nav-section-label">{section.label}</div>
              )}
              {section.items.map((item) => {
                const Icon = item.icon;
                const active = location.pathname === item.path ||
                  (item.path !== '/' && location.pathname.startsWith(item.path));
                return (
                  <NavLink
                    key={item.path}
                    to={item.path}
                    className={`nav-item ${active ? 'active' : ''}`}
                    title={(collapsed && !mobileOpen) ? item.label : ''}
                    onClick={() => {
                      if (window.innerWidth <= 768) onClose();
                    }}
                  >
                    <Icon size={18} className="nav-icon" />
                    {(!collapsed || mobileOpen) && <span className="nav-text">{item.label}</span>}
                  </NavLink>
                );
              })}
            </div>
          ))}
        </nav>

        {/* Footer usuario */}
        <div className="sidebar-footer">
          <div className="sidebar-user" onClick={handleLogout} title="Cerrar sesión">
            <div className="user-avatar">{iniciales}</div>
            {(!collapsed || mobileOpen) && (
              <div className="user-info">
                <div className="user-name">{usuario?.nombre} {usuario?.apellido}</div>
                <div className="user-role">{usuario?.rol}</div>
              </div>
            )}
            {(!collapsed || mobileOpen) && <LogOut size={14} style={{ color: 'var(--text-muted)', flexShrink: 0 }} />}
          </div>
        </div>
      </aside>
    </>
  );
}

