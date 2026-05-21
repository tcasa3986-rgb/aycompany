import React from 'react';
import { HashRouter, Routes, Route, Navigate } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import useAuthStore from './store/authStore';

// Layout
import Layout from './components/layout/Layout';

// Páginas
import LoginPage       from './pages/LoginPage';
import DashboardPage   from './pages/DashboardPage';
import ClientesPage    from './pages/ClientesPage';
import PipelinePage    from './pages/PipelinePage';
import TareasPage      from './pages/TareasPage';
import PaquetesPage    from './pages/PaquetesPage';
import DestinosPage    from './pages/DestinosPage';
import ProveedoresPage from './pages/ProveedoresPage';
import ReservasPage    from './pages/ReservasPage';
import PagosPage       from './pages/PagosPage';
import CampanasPage    from './pages/CampanasPage';
import ReportesPage    from './pages/ReportesPage';
import ConfiguracionPage from './pages/ConfiguracionPage';
import ClientePerfilPage from './pages/ClientePerfilPage';
import MantenimientoPage from './pages/MantenimientoPage';

// Páginas stub (en construcción)
const StubPage = ({ titulo }) => (
  <div className="animate-fade-in">
    <div className="page-header">
      <h1 className="page-title">{titulo}</h1>
    </div>
    <div className="card" style={{ textAlign:'center', padding:'60px 20px' }}>
      <div style={{ fontSize:'3rem', marginBottom:16 }}>🚧</div>
      <h2 style={{ marginBottom:8 }}>Módulo en construcción</h2>
      <p className="text-muted">Este módulo estará disponible próximamente.</p>
    </div>
  </div>
);

// Ruta protegida
function PrivateRoute({ children }) {
  const { usuario } = useAuthStore();
  return usuario ? children : <Navigate to="/login" replace />;
}

export default function App() {
  return (
    <HashRouter>
      <Toaster
        position="top-right"
        toastOptions={{
          style: {
            background: '#1F2937',
            color: '#F9FAFB',
            border: '1px solid #374151',
            borderRadius: '10px',
            fontSize: '0.875rem',
          },
          success: { iconTheme: { primary: '#10B981', secondary: '#F9FAFB' } },
          error:   { iconTheme: { primary: '#EF4444', secondary: '#F9FAFB' } },
        }}
      />
      <Routes>
        <Route path="/login" element={<LoginPage />} />
        <Route path="/" element={<PrivateRoute><Layout /></PrivateRoute>}>
          <Route index              element={<DashboardPage />} />
          <Route path="clientes"    element={<ClientesPage />} />
          <Route path="clientes/:id" element={<ClientePerfilPage />} />
          <Route path="pipeline"    element={<PipelinePage />} />
          <Route path="tareas"      element={<TareasPage />} />
          <Route path="paquetes"    element={<PaquetesPage />} />
          <Route path="destinos"    element={<DestinosPage />} />
          <Route path="proveedores" element={<ProveedoresPage />} />
          <Route path="reservas"    element={<ReservasPage />} />
          <Route path="pagos"       element={<PagosPage />} />
          <Route path="campanas"    element={<CampanasPage />} />
          <Route path="reportes"    element={<ReportesPage />} />
          <Route path="configuracion" element={<ConfiguracionPage />} />
          <Route path="mantenimiento" element={<MantenimientoPage />} />
          <Route path="*"           element={<Navigate to="/" replace />} />
        </Route>
      </Routes>
    </HashRouter>
  );
}
