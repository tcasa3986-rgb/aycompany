import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuthStore } from './store/authStore';
import Layout    from './components/Layout';
import Login     from './pages/Login';
import Dashboard from './pages/Dashboard';
import Clientes  from './pages/Clientes';
import Productos from './pages/Productos';
import Licencias from './pages/Licencias';
import Pagos     from './pages/Pagos';

function Private({ children }) {
  const token = useAuthStore(s => s.token);
  return token ? children : <Navigate to="/" replace />;
}

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/" element={<Private><Layout /></Private>}>
          <Route path="dashboard"  element={<Dashboard />} />
          <Route path="clientes"   element={<Clientes />} />
          <Route path="productos"  element={<Productos />} />
          <Route path="licencias"  element={<Licencias />} />
          <Route path="pagos"      element={<Pagos />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}
