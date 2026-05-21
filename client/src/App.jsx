import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuthStore } from './store/authStore';
import Layout    from './components/Layout';
import Login     from './pages/Login';
import Dashboard from './pages/Dashboard';
import Clientes  from './pages/Clientes';
import Productos from './pages/Productos';
import Licencias from './pages/Licencias';
import Pagos          from './pages/Pagos';
import PagarLicencia  from './pages/PagarLicencia';
import PortalCliente  from './pages/PortalCliente';
import Facturas       from './pages/Facturas';
import Marketing      from './pages/Marketing';
import Calendario     from './pages/Calendario';
import Contenido      from './pages/Contenido';
import Social         from './pages/Social';
import Leads          from './pages/Leads';
import Agente         from './pages/Agente';
import Prospector     from './pages/Prospector';
import Tickets        from './pages/Tickets';
import Cartera        from './pages/Cartera';
import Configuracion  from './pages/Configuracion';
import Proyectos      from './pages/Proyectos';
import ClienteDetalle from './pages/ClienteDetalle';
import Pipeline       from './pages/Pipeline';
import Contratos      from './pages/Contratos';
import Usuarios       from './pages/Usuarios';
import Reportes       from './pages/Reportes';
import Analitica      from './pages/Analitica';
import Empresas       from './pages/Empresas';
import PortalVendedor from './pages/PortalVendedor';

function Private({ children }) {
  const token = useAuthStore(s => s.token);
  return token ? children : <Navigate to="/" replace />;
}

function PrivateVendedor() {
  const { token, user } = useAuthStore();
  if (!token) return <Navigate to="/" replace />;
  if (user?.rol === 'vendedor') return <PortalVendedor />;
  return <Navigate to="/dashboard" replace />;
}

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/vendedor" element={<PrivateVendedor />} />
        <Route path="/pagar/:license_key"  element={<PagarLicencia />} />
        <Route path="/cliente/:token"     element={<PortalCliente />} />
        <Route path="/" element={<Private><Layout /></Private>}>
          <Route path="dashboard"  element={<Dashboard />} />
          <Route path="clientes"   element={<Clientes />} />
          <Route path="productos"  element={<Productos />} />
          <Route path="licencias"  element={<Licencias />} />
          <Route path="pagos"      element={<Pagos />} />
          <Route path="facturas"   element={<Facturas />} />
          <Route path="marketing"  element={<Marketing />} />
          <Route path="calendario" element={<Calendario />} />
          <Route path="contenido"  element={<Contenido />} />
          <Route path="social"     element={<Social />} />
          <Route path="leads"      element={<Leads />} />
          <Route path="agente"     element={<Agente />} />
          <Route path="prospector" element={<Prospector />} />
          <Route path="tickets"        element={<Tickets />} />
          <Route path="cartera"        element={<Cartera />} />
          <Route path="configuracion"  element={<Configuracion />} />
          <Route path="proyectos"      element={<Proyectos />} />
          <Route path="clientes/:id"   element={<ClienteDetalle />} />
          <Route path="pipeline"       element={<Pipeline />} />
          <Route path="contratos"      element={<Contratos />} />
          <Route path="usuarios"       element={<Usuarios />} />
          <Route path="reportes"       element={<Reportes />} />
          <Route path="analitica"      element={<Analitica />} />
          <Route path="empresas"       element={<Empresas />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}
