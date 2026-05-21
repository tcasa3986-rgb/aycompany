import { useState, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';
import { FaLock, FaEnvelope } from 'react-icons/fa';
import Swal from 'sweetalert2';

function Login() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const { login } = useContext(AuthContext);
    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!email || !password) {
            Swal.fire({ icon: 'warning', title: 'Atención', text: 'Por favor, llena todos los campos.' });
            return;
        }

        const result = await login(email, password);

        if (result.success) {
            navigate('/dashboard');
        } else {
            Swal.fire({ icon: 'error', title: 'Acceso Denegado', text: result.message, confirmButtonColor: '#a42ca1' });
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center p-4 relative overflow-hidden" style={{ background: '#f8fafc' }}>
            {/* Elementos decorativos de fondo abstractos parecidos al diseño */}
            <div className="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full blur-[120px] opacity-30 pointer-events-none" style={{ background: '#d82e88' }}></div>
            <div className="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full blur-[140px] opacity-20 pointer-events-none" style={{ background: '#30176b' }}></div>
            <div className="absolute top-[20%] right-[10%] w-[20%] h-[20%] rounded-full blur-[80px] opacity-20 pointer-events-none" style={{ background: '#811e86' }}></div>

            <div className="bg-white/80 p-10 rounded-[2rem] shadow-2xl w-full max-w-md backdrop-blur-xl border border-white/50 relative z-10 transition-transform hover:scale-[1.01] duration-500">
                <div className="text-center mb-8">
                    <div className="w-20 h-20 mx-auto bg-gradient-to-tr from-[#30176b] via-[#811e86] to-[#d82e88] rounded-2xl flex items-center justify-center mb-4 shadow-lg transform rotate-3">
                        <span className="text-white text-3xl font-black italic">M</span>
                    </div>
                    <h2 className="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-[#30176b] to-[#a42ca1]">Bienvenido(a)</h2>
                    <p className="text-gray-500 mt-2 text-sm">Ingresa tus credenciales para administrar el salón.</p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div>
                        <label className="block text-sm font-semibold text-gray-700 mb-2">Correo Electrónico</label>
                        <div className="relative">
                            <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <FaEnvelope />
                            </div>
                            <input
                                type="email" required
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                className="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#811e86]/20 focus:border-[#811e86] outline-none bg-gray-50/50 text-gray-800 transition-all font-medium"
                                placeholder="admin@salon.com"
                            />
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-semibold text-gray-700 mb-2">Contraseña</label>
                        <div className="relative">
                            <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <FaLock />
                            </div>
                            <input
                                type="password" required
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                className="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#811e86]/20 focus:border-[#811e86] outline-none bg-gray-50/50 text-gray-800 transition-all font-medium"
                                placeholder="••••••••"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        className="w-full py-3.5 px-4 text-white rounded-xl font-bold shadow-lg transition-all hover:scale-[1.02] hover:shadow-[#811e86]/30"
                        style={{ background: 'linear-gradient(90deg, #811e86 0%, #30176b 100%)' }}
                    >
                        Ingresar al Sistema
                    </button>

                    <div className="text-center mt-6">
                        <span className="text-xs text-gray-400">Acceso administrativo seguro SSL</span>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default Login;
