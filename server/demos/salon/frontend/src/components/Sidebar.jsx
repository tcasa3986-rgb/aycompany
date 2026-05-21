import { useContext } from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import { FaHome, FaCog, FaLock, FaSignOutAlt, FaUsers, FaCut, FaCalendarAlt, FaChartPie, FaUserTie, FaMoneyBillWave, FaBoxOpen, FaChartBar, FaWallet, FaIdCard, FaWrench } from 'react-icons/fa';
import { AuthContext } from '../context/AuthContext';
import { ConfigContext } from '../context/ConfigContext';

function Sidebar({ isOpen, closeSidebar }) {
    const { logout, user } = useContext(AuthContext);
    const { config } = useContext(ConfigContext);
    const navigate = useNavigate();

    const handleLogout = () => {
        logout();
        navigate('/login');
    };

    // Calculate Initials from company name
    const getInitials = (name) => {
        if (!name) return 'AD';
        const words = name.split(' ');
        if (words.length > 1) {
            return (words[0][0] + words[1][0]).toUpperCase();
        }
        return name.substring(0, 2).toUpperCase();
    };

    return (
        <aside className={`${isOpen ? 'translate-x-0' : '-translate-x-full'} md:translate-x-0 fixed md:relative w-32 flex flex-col items-center py-8 rounded-r-3xl m-0 shadow-lg h-full flex-shrink-0 transition-transform duration-300 ease-in-out`} style={{ background: 'linear-gradient(180deg, #a42ca1 0%, #31186b 100%)', zIndex: 50 }}>
            {/* Circle active indicator (the white dot on the right edge in mockup) */}
            <div className="absolute right-0 top-[180px] w-3 h-3 bg-white rounded-full translate-x-1.5 shadow-md border-2 border-[#a42ca1]"></div>

            <div className="h-20 w-20 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg mb-10 overflow-hidden bg-cover bg-center" style={{
                background: config?.logo_url ? `url(${config.logo_url}) no-repeat center center / cover` : 'linear-gradient(135deg, #d82e88 0%, #651b75 100%)',
                boxShadow: '0 4px 15px rgba(0,0,0,0.2)'
            }}>
                {!config?.logo_url && (config?.nombre_empresa ? getInitials(config.nombre_empresa) : 'AD')}
            </div>

            <nav className="flex-1 w-full space-y-4 flex flex-col items-center text-white relative overflow-y-auto pb-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" onClick={() => { if (window.innerWidth < 768) closeSidebar(); }}>
                <div className="flex flex-col items-center mb-6">
                    <NavLink to="/dashboard" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                        <FaHome size={22} />
                    </NavLink>
                    <span className="text-xs font-semibold mt-1 opacity-90">Inicio</span>
                </div>

                <div className="flex flex-col items-center mb-6">
                    <NavLink to="/clientes" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                        <FaUsers size={22} />
                    </NavLink>
                    <span className="text-xs font-semibold mt-1 opacity-90">Clientes</span>
                </div>

                <div className="flex flex-col items-center mb-6">
                    <NavLink to="/suscripciones" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                        <FaIdCard size={22} />
                    </NavLink>
                    <span className="text-xs font-semibold mt-1 opacity-90">Membresías</span>
                </div>

                <div className="flex flex-col items-center mb-6">
                    <NavLink to="/servicios" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                        <FaCut size={22} />
                    </NavLink>
                    <span className="text-xs font-semibold mt-1 opacity-90">Catálogo</span>
                </div>

                <div className="flex flex-col items-center mb-6">
                    <NavLink to="/inventario" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                        <FaBoxOpen size={22} />
                    </NavLink>
                    <span className="text-xs font-semibold mt-1 opacity-90">Inventario</span>
                </div>

                <div className="flex flex-col items-center mb-6">
                    <NavLink to="/citas" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                        <FaCalendarAlt size={22} />
                    </NavLink>
                    <span className="text-xs font-semibold mt-1 opacity-90">Citas</span>
                </div>

                <div className="flex flex-col items-center mb-6">
                    <NavLink to="/ventas" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                        <FaMoneyBillWave size={22} />
                    </NavLink>
                    <span className="text-xs font-semibold mt-1 opacity-90">Ventas</span>
                </div>

                <div className="flex flex-col items-center mb-6">
                    <NavLink to="/gastos" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                        <FaWallet size={22} />
                    </NavLink>
                    <span className="text-xs font-semibold mt-1 opacity-90">Egresos</span>
                </div>

                {user && user.rol === 'admin' && (
                    <div className="flex flex-col items-center mb-6">
                        <NavLink to="/reportes" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                            <FaChartBar size={22} />
                        </NavLink>
                        <span className="text-xs font-semibold mt-1 opacity-90">Reportes</span>
                    </div>
                )}

                {user && user.rol === 'admin' && (
                    <div className="flex flex-col items-center mb-6">
                        <NavLink to="/usuarios" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                            <FaUserTie size={22} />
                        </NavLink>
                        <span className="text-xs font-semibold mt-1 opacity-90">Personal</span>
                    </div>
                )}

                {user && user.rol === 'admin' && (
                    <div className="flex flex-col items-center mb-6">
                        <NavLink to="/mantenimiento" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                            <FaWrench size={22} />
                        </NavLink>
                        <span className="text-xs font-semibold mt-1 opacity-90 text-center">Mantenimiento</span>
                    </div>
                )}

                {user && user.rol === 'admin' && (
                    <div className="flex flex-col items-center mb-6">
                        <NavLink to="/configuracion" className={({ isActive }) => `flex items-center justify-center w-14 h-14 rounded-full transition-all ${isActive ? 'bg-white text-[#7d1b82] shadow-md' : 'hover:bg-white/20 text-white'}`}>
                            <FaCog size={22} />
                        </NavLink>
                        <span className="text-xs font-semibold mt-1 opacity-90">Ajustes</span>
                    </div>
                )}
            </nav>

            <div className="mt-auto flex flex-col items-center">
                <button
                    onClick={handleLogout}
                    className="flex items-center justify-center w-14 h-14 rounded-2xl bg-[#4b1670] hover:bg-[#340b54] text-white transition-all shadow-md"
                    title="Cerrar Sesión"
                >
                    <FaSignOutAlt size={22} />
                </button>
                <span className="text-xs font-semibold mt-2 text-[#463261]">Salir</span>
            </div>
        </aside>
    );
}

export default Sidebar;
