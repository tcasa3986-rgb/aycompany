import { useState } from 'react';
import { HashRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import Sidebar from './components/Sidebar';
import Dashboard from './pages/Dashboard';
import Clientes from './pages/Clientes';
import Servicios from './pages/Servicios';
import Citas from './pages/Citas';
import Login from './pages/Login';
import Ventas from './pages/Ventas';
import Inventario from './pages/Inventario';
import Reportes from './pages/Reportes';
import Usuarios from './pages/Usuarios';
import Gastos from './pages/Gastos';
import Configuracion from './pages/Configuracion';
import Suscripciones from './pages/Suscripciones';
import Mantenimiento from './pages/Mantenimiento';
import { AuthProvider } from './context/AuthContext';
import ProtectedRoute from './components/ProtectedRoute';

import { ConfigProvider } from './context/ConfigContext';

import Header from './components/Header';

function App() {
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);

  return (
    <AuthProvider>
      <ConfigProvider>
        <Router>
          <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/*" element={
              <ProtectedRoute>
                <div className="flex h-screen bg-[#f3f4fa] overflow-hidden text-gray-800 font-sans relative">
                  <Sidebar isOpen={isSidebarOpen} closeSidebar={() => setIsSidebarOpen(false)} />
                  {isSidebarOpen && (
                    <div
                      className="fixed inset-0 bg-black/40 z-40 md:hidden backdrop-blur-sm"
                      onClick={() => setIsSidebarOpen(false)}
                    />
                  )}

                  <div className="flex-1 flex flex-col overflow-y-auto w-full relative">
                    {/* Header area (search bar and logo) */}
                    <Header toggleSidebar={() => setIsSidebarOpen(!isSidebarOpen)} />

                    <main className="flex-1 p-4 md:p-8 pt-4 z-10 w-full mx-auto">
                      <Routes>
                        <Route path="/" element={<Navigate to="/dashboard" replace />} />
                        <Route path="/dashboard" element={<Dashboard />} />
                        <Route path="/clientes" element={<Clientes />} />
                        <Route path="/servicios" element={<Servicios />} />
                        <Route path="/citas" element={<Citas />} />
                        <Route path="/inventario" element={<Inventario />} />
                        <Route path="/usuarios" element={<Usuarios />} />
                        <Route path="/ventas" element={<Ventas />} />
                        <Route path="/gastos" element={<Gastos />} />
                        <Route path="/reportes" element={<Reportes />} />
                        <Route path="/configuracion" element={<Configuracion />} />
                        <Route path="/suscripciones" element={<Suscripciones />} />
                        <Route path="/mantenimiento" element={<Mantenimiento />} />
                      </Routes>
                    </main>
                  </div>
                </div>
              </ProtectedRoute>
            } />
          </Routes>
        </Router>
      </ConfigProvider>
    </AuthProvider>
  );
}

export default App;
