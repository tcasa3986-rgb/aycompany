import React, { useState } from 'react';
import { Outlet, useLocation } from 'react-router-dom';
import Sidebar from './Sidebar';
import Topbar from './Topbar';
import useConfigStore from '../../store/configStore';

const titulos = {
  '/':              'Dashboard',
  '/clientes':      'Clientes',
  '/pipeline':      'Pipeline de Ventas',
  '/tareas':        'Tareas',
  '/destinos':      'Destinos',
  '/paquetes':      'Paquetes Turísticos',
  '/proveedores':   'Proveedores',
  '/reservas':      'Reservas',
  '/pagos':         'Pagos',
  '/campanas':      'Campañas de Marketing',
  '/reportes':      'Reportes',
  '/configuracion': 'Configuración',
};

export default function Layout() {
  const [collapsed, setCollapsed] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const location = useLocation();
  const { fetchConfig } = useConfigStore();

  React.useEffect(() => {
    fetchConfig();
  }, [fetchConfig]);

  // Cerrar menú móvil al cambiar de ruta
  React.useEffect(() => {
    setMobileOpen(false);
  }, [location.pathname]);

  // Manejar el redimensionamiento de ventana
  React.useEffect(() => {
    const handleResize = () => {
      if (window.innerWidth > 768) {
        setMobileOpen(false);
      }
    };
    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, []);

  const titulo = Object.entries(titulos).find(
    ([key]) => key === '/'
      ? location.pathname === '/'
      : location.pathname.startsWith(key)
  )?.[1] || 'CRM Viaje 360';

  return (
    <div className="layout">
      <Sidebar 
        collapsed={collapsed} 
        onToggle={() => setCollapsed(c => !c)} 
        mobileOpen={mobileOpen}
        onClose={() => setMobileOpen(false)}
      />
      <div className={`main-content ${collapsed ? 'collapsed' : ''}`}>
        <Topbar 
          collapsed={collapsed} 
          onToggle={() => {
            if (window.innerWidth <= 768) {
              setMobileOpen(!mobileOpen);
            } else {
              setCollapsed(!collapsed);
            }
          }} 
          title={titulo} 
        />
        <main className="page-wrapper">
          <Outlet />
        </main>
      </div>
    </div>
  );
}

