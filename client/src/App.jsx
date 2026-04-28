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
import Facturas       from './pages/Facturas';
import Marketing      from './pages/Marketing';
import Calendario     from './pages/Calendario';
import Contenido      from './pages/Contenido';

function Private({ children }) {
  const token = useAuthStore(s => s.token);
  return token ? children : <Navigate to="/" replace />;
}

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/pagar/:license_key" element={<PagarLicencia />} />
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
        </Route>
      </Routes>
    </BrowserRouter>
  );
}
