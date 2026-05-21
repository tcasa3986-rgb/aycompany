import { useState } from 'react';
import { HashRouter, Routes, Route, Navigate } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import { AuthProvider, useAuth } from './context/AuthContext';
import { ThemeProvider } from './context/ThemeContext';

import Sidebar from './components/Sidebar';
import Header from './components/Header';
import LoginPage from './pages/LoginPage';
import Dashboard from './pages/Dashboard';
import Unidades from './pages/Unidades';
import Cobranza from './pages/Cobranza';
import Mantenimiento from './pages/Mantenimiento';
import Acceso from './pages/Acceso';
import Comunicaciones from './pages/Comunicaciones';
import Configuracion from './pages/Configuracion';
import Residentes from './pages/Residentes';
import Amenidades from './pages/Amenidades';
import Proveedores from './pages/Proveedores';
import Reportes from './pages/Reportes';
import Contabilidad from './pages/Contabilidad';
import Sistema from './pages/Sistema';
function ProtectedLayout() {
  const { usuario, loading } = useAuth();
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false);

  if (loading) return (
    <div className="flex-center" style={{ minHeight: '100vh', flexDirection: 'column', gap: 16 }}>
      <div className="spinner" style={{ width: 48, height: 48 }} />
      <p style={{ color: 'var(--text-secondary)' }}>Cargando sistema...</p>
    </div>
  );

  if (!usuario) return <Navigate to="/login" replace />;

  return (
    <div className="app-layout">
      <Sidebar collapsed={sidebarCollapsed} onToggle={() => setSidebarCollapsed(p => !p)} />
      <div className={`main-content ${sidebarCollapsed ? 'sidebar-collapsed' : ''}`}>
        <Header collapsed={sidebarCollapsed} onToggle={() => setSidebarCollapsed(p => !p)} />
        <main className="page-content">
          <Routes>
            <Route path="/" element={<Dashboard />} />
            <Route path="/unidades" element={<Unidades />} />
            <Route path="/residentes" element={<Residentes />} />
            <Route path="/cobranza" element={<Cobranza />} />
            <Route path="/contabilidad" element={<Contabilidad />} />
            <Route path="/mantenimiento" element={<Mantenimiento />} />
            <Route path="/amenidades" element={<Amenidades />} />
            <Route path="/acceso" element={<Acceso />} />
            <Route path="/comunicaciones" element={<Comunicaciones />} />
            <Route path="/proveedores" element={<Proveedores />} />
            <Route path="/reportes" element={<Reportes />} />
            <Route path="/configuracion" element={<Configuracion />} />
            <Route path="/sistema" element={<Sistema />} />
            <Route path="*" element={<Navigate to="/" replace />} />
          </Routes>
        </main>
      </div>
    </div>
  );
}

function AuthWrapper() {
  const { usuario, loading } = useAuth();

  if (loading) return null;

  return (
    <Routes>
      <Route path="/login" element={usuario ? <Navigate to="/" replace /> : <LoginPage />} />
      <Route path="/*" element={<ProtectedLayout />} />
    </Routes>
  );
}

function App() {
  return (
    <ThemeProvider>
      <AuthProvider>
        <HashRouter>
          <Toaster
            position="top-right"
            toastOptions={{
              style: { background: 'var(--bg-card)', color: 'var(--text-primary)', border: '1px solid var(--border)' },
              duration: 4000,
            }}
          />
          <AuthWrapper />
        </HashRouter>
      </AuthProvider>
    </ThemeProvider>
  );
}

export default App;
