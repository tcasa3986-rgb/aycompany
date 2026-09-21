import { lazy, Suspense } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuthStore } from './store/authStore';
import Layout    from './components/Layout';
import Login     from './pages/Login';
import Hoy from './pages/Hoy';

// Cada pantalla se descarga solo cuando se abre. Antes las 27 venían en un
// solo archivo de 900 KB que el celular tenía que ejecutar completo antes
// de pintar nada: por eso se congelaba al abrir.
const Dashboard      = lazy(() => import('./pages/Dashboard'));
const Clientes       = lazy(() => import('./pages/Clientes'));
const Productos      = lazy(() => import('./pages/Productos'));
const Licencias      = lazy(() => import('./pages/Licencias'));
const CostosServidor = lazy(() => import('./pages/CostosServidor'));
const Finanzas       = lazy(() => import('./pages/Finanzas'));
const Pagos          = lazy(() => import('./pages/Pagos'));
const PagarLicencia  = lazy(() => import('./pages/PagarLicencia'));
const PortalCliente  = lazy(() => import('./pages/PortalCliente'));
const Facturas       = lazy(() => import('./pages/Facturas'));
const Marketing      = lazy(() => import('./pages/Marketing'));
const Calendario     = lazy(() => import('./pages/Calendario'));
const Contenido      = lazy(() => import('./pages/Contenido'));
const Social         = lazy(() => import('./pages/Social'));
const Leads          = lazy(() => import('./pages/Leads'));
const Tickets        = lazy(() => import('./pages/Tickets'));
const Cartera        = lazy(() => import('./pages/Cartera'));
const Configuracion  = lazy(() => import('./pages/Configuracion'));
const Proyectos      = lazy(() => import('./pages/Proyectos'));
const ClienteDetalle = lazy(() => import('./pages/ClienteDetalle'));
const Pipeline       = lazy(() => import('./pages/Pipeline'));
const Contratos      = lazy(() => import('./pages/Contratos'));
const Usuarios       = lazy(() => import('./pages/Usuarios'));
const Reportes       = lazy(() => import('./pages/Reportes'));
const Analitica      = lazy(() => import('./pages/Analitica'));

// Barra fina arriba mientras baja el código de la pantalla. No cubre nada.
function Cargando() {
  return <div style={{ position: 'fixed', top: 0, left: 0, right: 0, height: 3, background: '#2563EB',
                       animation: 'cargando 1s ease-in-out infinite', zIndex: 100 }}>
    <style>{`@keyframes cargando { 0%{transform:scaleX(0);transform-origin:left} 50%{transform:scaleX(1);transform-origin:left} 51%{transform-origin:right} 100%{transform:scaleX(0);transform-origin:right} }`}</style>
  </div>;
}

function Private({ children }) {
  const token = useAuthStore(s => s.token);
  return token ? children : <Navigate to="/" replace />;
}

export default function App() {
  return (
    <BrowserRouter>
      <Suspense fallback={<Cargando />}>
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/pagar/:license_key"  element={<PagarLicencia />} />
        <Route path="/cliente/:token"     element={<PortalCliente />} />
        <Route path="/" element={<Private><Layout /></Private>}>
          <Route path="hoy"        element={<Hoy />} />
          <Route path="dashboard"  element={<Dashboard />} />
          <Route path="clientes"   element={<Clientes />} />
          <Route path="productos"  element={<Productos />} />
          <Route path="licencias"  element={<Licencias />} />
          <Route path="costos"     element={<CostosServidor />} />
          <Route path="finanzas"   element={<Finanzas />} />
          <Route path="pagos"      element={<Pagos />} />
          <Route path="facturas"   element={<Facturas />} />
          <Route path="marketing"  element={<Marketing />} />
          <Route path="calendario" element={<Calendario />} />
          <Route path="contenido"  element={<Contenido />} />
          <Route path="social"     element={<Social />} />
          <Route path="leads"      element={<Leads />} />
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
        </Route>
      </Routes>
      </Suspense>
    </BrowserRouter>
  );
}
