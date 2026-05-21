import { HashRouter, Routes, Route, Navigate } from 'react-router-dom';
import ProtectedRoute from './routes/ProtectedRoute';
import MainLayout from './components/layout/MainLayout';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import POS from './pages/POS';
import Productos from './pages/Productos';
import Categorias from './pages/Categorias';
import Clientes from './pages/Clientes';
import Proveedores from './pages/Proveedores';
import Ventas from './pages/Ventas';
import Caja from './pages/Caja';
import Pedidos from './pages/Pedidos';
import Inventario from './pages/Inventario';
import Reportes from './pages/Reportes';
import Configuracion from './pages/Configuracion';
import Usuarios from './pages/Usuarios';
import Compras from './pages/Compras';
import Logs from './pages/Logs';
import Promociones from './pages/Promociones';
import CRM from './pages/CRM';
import CRMPerfilCliente from './pages/CRMPerfilCliente';

export default function App() {
    return (
        <HashRouter>
            <Routes>
                <Route path="/login" element={<Login />} />
                <Route path="/" element={<ProtectedRoute><MainLayout /></ProtectedRoute>}>
                    <Route index element={<Dashboard />} />
                    <Route path="pos" element={<POS />} />
                    <Route path="ventas" element={<Ventas />} />
                    <Route path="pedidos" element={<Pedidos />} />
                    <Route path="productos" element={<Productos />} />
                    <Route path="categorias" element={<Categorias />} />
                    <Route path="inventario" element={<Inventario />} />
                    <Route path="compras" element={<Compras />} />
                    <Route path="promociones" element={<Promociones />} />
                    <Route path="crm" element={<CRM />} />
                    <Route path="crm/cliente/:id" element={<CRMPerfilCliente />} />
                    <Route path="clientes" element={<Clientes />} />
                    <Route path="proveedores" element={<Proveedores />} />
                    <Route path="caja" element={<Caja />} />
                    <Route path="reportes" element={<Reportes />} />
                    <Route path="configuracion" element={<Configuracion />} />
                    <Route path="usuarios" element={<Usuarios />} />
                    <Route path="logs" element={<Logs />} />
                </Route>
                <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
        </HashRouter>
    );
}
