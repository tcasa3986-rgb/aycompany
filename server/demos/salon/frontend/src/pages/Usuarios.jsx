import { useState, useEffect, useContext } from 'react';
import { FaPlus, FaTrash, FaEdit, FaUserTie, FaSearch } from 'react-icons/fa';
import Swal from 'sweetalert2';
import api from '../services/api';
import { AuthContext } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';

function Usuarios() {
    const [usuarios, setUsuarios] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const { user } = useContext(AuthContext);
    const navigate = useNavigate();

    // Estado del formulario
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [formData, setFormData] = useState({
        id: null,
        nombre: '',
        email: '',
        password: '',
        rol: 'recepcionista' // admin, recepcionista, estilista
    });

    useEffect(() => {
        // Validación de seguridad extra: sólo admins ven esta vista
        if (user && user.rol !== 'admin') {
            Swal.fire('Acceso Denegado', 'No tienes permisos para ver el módulo de personal', 'error');
            navigate('/dashboard');
        } else {
            fetchUsuarios();
        }
    }, [user, navigate]);

    const fetchUsuarios = async () => {
        try {
            setLoading(true);
            const response = await api.get('/usuarios');
            setUsuarios(response.data);
        } catch (error) {
            console.error('Error fetching usuarios:', error);
            Swal.fire('Error', 'No se pudieron cargar los usuarios', 'error');
        } finally {
            setLoading(false);
        }
    };

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const openModal = () => {
        setIsEditing(false);
        setFormData({ id: null, nombre: '', email: '', password: '', rol: 'recepcionista' });
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        // Si es creación, exigimos contraseña; si es edición, es opcional
        if (!isEditing && !formData.password) {
            Swal.fire('Atención', 'Debes ingresar una contraseña para el nuevo usuario', 'warning');
            return;
        }

        try {
            if (isEditing) {
                await api.put(`/usuarios/${formData.id}`, formData);
                Swal.fire('¡Actualizado!', 'Usuario actualizado correctamente', 'success');
            } else {
                await api.post('/usuarios', formData);
                Swal.fire('¡Creado!', 'Usuario creado correctamente', 'success');
            }
            fetchUsuarios();
            closeModal();
        } catch (error) {
            console.error('Error guardando usuario:', error);
            const errMsj = error.response?.data?.error || 'No se pudo guardar el usuario';
            Swal.fire('Error', errMsj, 'error');
        }
    };

    const handleEdit = (userSelected) => {
        setIsEditing(true);
        setFormData({
            id: userSelected.id,
            nombre: userSelected.nombre,
            email: userSelected.email,
            password: '', // En edición, mostramos el campo en blanco
            rol: userSelected.rol
        });
        setIsModalOpen(true);
    };

    const handleDelete = (id) => {
        if (user.id === id) {
            Swal.fire('No permitido', 'No puedes eliminar tu propia sesión activa.', 'warning');
            return;
        }

        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    await api.delete(`/usuarios/${id}`);
                    Swal.fire('¡Eliminado!', 'El usuario ha sido eliminado.', 'success');
                    fetchUsuarios();
                } catch (error) {
                    console.error('Error eliminando usuario:', error);
                    const errMsj = error.response?.data?.error || 'No se pudo eliminar el usuario';
                    Swal.fire('Error', errMsj, 'error');
                }
            }
        });
    };

    const getRoleBadge = (rol) => {
        switch (rol) {
            case 'admin': return <span className="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Administrador</span>;
            case 'estilista': return <span className="px-3 py-1 rounded-full text-xs font-semibold bg-[#fce4f9] text-[#a42ca1]">Estilista</span>;
            case 'recepcionista': return <span className="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Recepcionista</span>;
            default: return <span className="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{rol}</span>;
        }
    };

    const filteredUsuarios = usuarios.filter(u =>
        u.nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
        u.email.toLowerCase().includes(searchTerm.toLowerCase())
    );

    return (
        <div className="h-full flex flex-col animation-fade-in relative z-0">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                <div>
                    <h1 className="text-3xl font-bold text-gray-800 flex items-center">
                        <FaUserTie className="text-[#a42ca1] mr-3" /> Personal y Accesos
                    </h1>
                    <p className="text-gray-500 mt-1">Gestiona los empleados del salón y sus niveles de acceso.</p>
                </div>
                <button
                    onClick={openModal}
                    className="bg-gradient-to-r from-[#a42ca1] to-[#651b75] hover:opacity-90 text-white px-6 py-2.5 rounded-xl flex items-center transition-all shadow-md font-medium"
                >
                    <FaPlus className="mr-2" /> Nuevo Empleado
                </button>
            </div>

            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 flex-1 flex flex-col overflow-hidden">
                <div className="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div className="relative w-64">
                        <span className="absolute inset-y-0 left-0 flex items-center pl-3">
                            <FaSearch className="text-gray-400" />
                        </span>
                        <input
                            type="text"
                            placeholder="Buscar por nombre o email..."
                            className="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#a42ca1]/20 focus:border-[#a42ca1] transition-all"
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                        />
                    </div>
                </div>

                <div className="flex-1 overflow-auto">
                    {loading ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#a42ca1]"></div>
                        </div>
                    ) : (
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th scope="col" className="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" className="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" className="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rol</th>
                                    <th scope="col" className="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-100">
                                {filteredUsuarios.length > 0 ? (
                                    filteredUsuarios.map((u) => (
                                        <tr key={u.id} className="hover:bg-gray-50 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="h-10 w-10 flex-shrink-0 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-[#a42ca1] font-bold">
                                                        {u.nombre.charAt(0).toUpperCase()}
                                                    </div>
                                                    <div className="ml-4">
                                                        <div className="text-sm font-medium text-gray-900">{u.nombre}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {u.email}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {getRoleBadge(u.rol)}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button onClick={() => handleEdit(u)} className="text-indigo-600 hover:text-indigo-900 mr-4 transition-colors" title="Editar">
                                                    <FaEdit size={18} />
                                                </button>
                                                {user && user.id !== u.id && (
                                                    <button onClick={() => handleDelete(u.id)} className="text-red-500 hover:text-red-700 transition-colors" title="Eliminar">
                                                        <FaTrash size={18} />
                                                    </button>
                                                )}
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="4" className="px-6 py-12 text-center text-gray-500">
                                            No se encontraron usuarios.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>

            {/* Modal */}
            {isModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 backdrop-blur-sm animation-fade-in">
                    <div className="relative w-full max-w-md p-4 mx-auto">
                        <div className="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                                <h3 className="text-xl font-bold text-gray-800">
                                    {isEditing ? 'Editar Usuario' : 'Nuevo Usuario'}
                                </h3>
                                <button onClick={closeModal} className="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <form onSubmit={handleSubmit} className="p-6">
                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                                        <input
                                            type="text"
                                            name="nombre"
                                            value={formData.nombre}
                                            onChange={handleInputChange}
                                            required
                                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#a42ca1]/50 focus:border-[#a42ca1] transition-all"
                                            placeholder="Ej. Juan Pérez"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Email (Usuario) *</label>
                                        <input
                                            type="email"
                                            name="email"
                                            value={formData.email}
                                            onChange={handleInputChange}
                                            required
                                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#a42ca1]/50 focus:border-[#a42ca1] transition-all"
                                            placeholder="ejemplo@salon.com"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Contraseña {isEditing && <span className="text-gray-400 font-normal text-xs">(Dejar en blanco para no cambiar)</span>} {!isEditing && "*"}
                                        </label>
                                        <input
                                            type="password"
                                            name="password"
                                            value={formData.password}
                                            onChange={handleInputChange}
                                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#a42ca1]/50 focus:border-[#a42ca1] transition-all"
                                            placeholder="••••••••"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Rol en el Sistema *</label>
                                        <select
                                            name="rol"
                                            value={formData.rol}
                                            onChange={handleInputChange}
                                            required
                                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#a42ca1]/50 focus:border-[#a42ca1] transition-all bg-white"
                                        >
                                            <option value="admin">Administrador (Acceso Total)</option>
                                            <option value="recepcionista">Recepcionista (Agendamiento)</option>
                                            <option value="estilista">Estilista (Solo lectura de citas)</option>
                                        </select>
                                    </div>
                                </div>
                                <div className="mt-8 flex justify-end space-x-3">
                                    <button
                                        type="button"
                                        onClick={closeModal}
                                        className="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#a42ca1] transition-all font-medium"
                                    >
                                        Cancelar
                                    </button>
                                    <button
                                        type="submit"
                                        className="px-5 py-2.5 bg-gradient-to-r from-[#a42ca1] to-[#651b75] hover:opacity-90 text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#a42ca1] shadow-md transition-all font-medium"
                                    >
                                        {isEditing ? 'Actualizar' : 'Guardar'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

export default Usuarios;
