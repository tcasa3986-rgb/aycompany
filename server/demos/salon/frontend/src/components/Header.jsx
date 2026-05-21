import { useContext } from 'react';
import { ConfigContext } from '../context/ConfigContext';
import { FaBars, FaCrown } from 'react-icons/fa';

function Header({ toggleSidebar }) {
    const { config } = useContext(ConfigContext);

    const renderEmpresaLogo = () => {
        const logoIcon = <FaCrown className="text-[#a42ca1] mr-3" size={26} />;
        
        if (!config?.nombre_empresa) {
            return (
                <div className="flex items-center select-none">
                    {logoIcon}
                    <span className="text-3xl font-serif font-bold text-gray-800 tracking-wide drop-shadow-sm">Belleza</span>
                    <span className="text-3xl font-serif font-black text-transparent bg-clip-text bg-gradient-to-br from-pink-500 to-[#a42ca1] tracking-wide ml-2 drop-shadow-md">Admin</span>
                </div>
            );
        }

        const words = config.nombre_empresa.trim().split(' ');
        if (words.length > 1) {
            const midpoint = Math.ceil(words.length / 2);
            return (
                <div className="flex items-center select-none">
                    {logoIcon}
                    <span className="text-3xl font-serif font-bold text-gray-800 tracking-wide drop-shadow-sm mr-2">
                        {words.slice(0, midpoint).join(' ')}
                    </span>
                    <span className="text-3xl font-serif font-black text-transparent bg-clip-text bg-gradient-to-br from-pink-500 to-[#a42ca1] tracking-wide drop-shadow-md">
                        {words.slice(midpoint).join(' ')}
                    </span>
                </div>
            );
        }
        
        return (
            <div className="flex items-center select-none">
                {logoIcon}
                <div className="text-3xl font-serif font-black text-transparent bg-clip-text bg-gradient-to-br from-pink-500 to-[#a42ca1] tracking-wide drop-shadow-md">
                    {config.nombre_empresa}
                </div>
            </div>
        );
    };

    return (
        <header className="flex justify-between items-center p-6 bg-white/50 backdrop-blur-md border-b border-gray-100 shadow-sm z-10 sticky top-0">
            <div className="flex items-center">
                <button
                    onClick={toggleSidebar}
                    className="md:hidden text-[#a42ca1] p-2 focus:outline-none mr-4 bg-white rounded-lg shadow-sm"
                    title="Menu"
                >
                    <FaBars size={22} />
                </button>
                <div className="hidden sm:flex items-center">
                    {renderEmpresaLogo()}
                </div>
            </div>

            <div className="flex items-center space-x-6">
                {/* Opcional: Perfil de usuario podría ir aquí en el futuro */}
                <div className="sm:hidden flex items-center">
                    {/* Versión móvil del logo si lo desea, o mantener vacío */}
                </div>
            </div>
        </header>
    );
}

export default Header;
