import React from 'react';
import { Menu, Search, Bell, Sun } from 'lucide-react';
import useAuthStore from '../../store/authStore';

export default function Topbar({ collapsed, onToggle, title }) {
  const { usuario } = useAuthStore();

  const iniciales = usuario
    ? `${usuario.nombre?.[0] || ''}${usuario.apellido?.[0] || ''}`.toUpperCase()
    : 'U';

  return (
    <header className={`topbar ${collapsed ? 'collapsed' : ''}`}>
      <button className="topbar-toggle" onClick={onToggle} title="Colapsar menú">
        <Menu size={18} />
      </button>

      {title && <h1 className="topbar-title">{title}</h1>}

      <div className="topbar-spacer" />

      <div className="search-box">
        <Search size={15} className="search-icon" />
        <input placeholder="Buscar clientes, reservas..." />
      </div>

      <div className="topbar-actions">
        <button className="topbar-btn" title="Notificaciones">
          <Bell size={17} />
          <span className="notif-badge">3</span>
        </button>
        <div className="topbar-avatar" title={`${usuario?.nombre} ${usuario?.apellido}`}>
          {iniciales}
        </div>
      </div>
    </header>
  );
}
