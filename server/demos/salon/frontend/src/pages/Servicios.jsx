import { useState, useEffect, useContext } from 'react';
import { FaPlus, FaTrash, FaEdit, FaCut, FaBox } from 'react-icons/fa';
import Swal from 'sweetalert2';
import api from '../services/api';
import { ConfigContext } from '../context/ConfigContext';

function Servicios() {
    const { config } = useContext(ConfigContext);
    const [servicios, setServicios] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [formData, setFormData] = useState({ id: null, nombre: '', descripcion: '', precio: '', duracion_minutos: '' });

    const fetchServicios = async () => {
        try {
            const response = await api.get('/servicios');
            setServicios(response.data);
        } catch (error) {
            console.error('Error fetching servicios:', error);
        }
    };

    useEffect(() => {
        fetchServicios();
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            if (formData.precio <= 0 || formData.duracion_minutos <= 0) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'El precio y duración deben ser mayores a 0', confirmButtonColor: '#a42ca1' });
                return;
            }
            if (isEditing) {
                await api.put(`/servicios/${formData.id}`, formData);
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'Servicio actualizado.', timer: 1500, showConfirmButton: false });
            } else {
                await api.post('/servicios', formData);
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'Servicio registrado correctamente.', timer: 1500, showConfirmButton: false });
            }
            setShowModal(false);
            setFormData({ id: null, nombre: '', descripcion: '', precio: '', duracion_minutos: '' });
            setIsEditing(false);
            fetchServicios();
        } catch (error) {
            console.error('Error saving servicio:', error);
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'No se pudo guardar el servicio.', confirmButtonColor: '#a42ca1' });
        }
    };

    const handleEdit = (servicio) => {
        setFormData(servicio);
        setIsEditing(true);
        setShowModal(true);
    };

    const handleDelete = async (id) => {
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "Cuidado, se eliminará el servicio.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar!'
        });

        if (result.isConfirmed) {
            try {
                await api.delete(`/servicios/${id}`);
                Swal.fire('Eliminado!', 'El servicio ha sido borrado.', 'success');
                fetchServicios();
            } catch (error) {
                console.error("Error deleting servicio", error);
                Swal.fire('Error', 'No se pudo eliminar el servicio.', 'error');
            }
        }
    };

    return (
        <div className="h-full flex flex-col space-y-6">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-4 sm:space-y-0">
                <h2 className="text-2xl font-bold text-gray-800">Catálogo de Servicios</h2>
                <button
                    onClick={() => {
                        setIsEditing(false);
                        setFormData({ id: null, nombre: '', descripcion: '', precio: '', duracion_minutos: '' });
                        setShowModal(true);
                    }}
                    className="flex items-center space-x-2 px-6 py-2.5 rounded-full text-white font-semibold shadow-md transition-all hover:scale-105"
                    style={{ background: 'linear-gradient(90deg, #811e86 0%, #d82e88 100%)' }}
                >
                    <FaPlus /> <span>Nuevo Servicio</span>
                </button>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 overflow-y-auto pb-6">
                {servicios.length === 0 ? (
                    <div className="col-span-full p-8 text-center text-gray-400 bg-white rounded-3xl shadow-sm border border-gray-100">
                        No hay servicios registrados en el catálogo.
                    </div>
                ) : (
                    servicios.map((servicio) => (
                        <div key={servicio.id} className="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col transition-all hover:shadow-md hover:-translate-y-1">
                            <div className="flex justify-between items-start mb-4">
                                <div className="w-12 h-12 rounded-full flex items-center justify-center text-white" style={{ background: 'linear-gradient(135deg, #a42ca1 0%, #651b75 100%)' }}>
                                    <FaCut size={20} />
                                </div>
                                <div className="flex space-x-2">
                                    <button onClick={() => handleEdit(servicio)} className="text-gray-400 hover:text-blue-500 transition-colors" title="Editar"><FaEdit /></button>
                                    <button onClick={() => handleDelete(servicio.id)} className="text-gray-400 hover:text-red-500 transition-colors" title="Eliminar"><FaTrash /></button>
                                </div>
                            </div>

                            <h3 className="text-xl font-bold text-gray-800 mb-2 truncate">{servicio.nombre}</h3>
                            <p className="text-sm text-gray-500 mb-4 flex-1 line-clamp-2">{servicio.descripcion || 'Sin descripción'}</p>

                            <div className="flex items-end justify-between mt-auto">
                                <div className="text-2xl font-black text-[#7d1b82]">
                                    {config?.simbolo_moneda || '$'}{parseFloat(servicio.precio).toLocaleString()}
                                </div>
                                <div className="text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-600 rounded-full">
                                    ⏱ {servicio.duracion_minutos} min
                                </div>
                            </div>
                        </div>
                    ))
                )}
            </div>

            {/* Modal Nuevo Servicio */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                    <div className="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl scale-100 transition-transform">
                        <h3 className="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2"><FaCut className="text-[#a42ca1]" /> {isEditing ? 'Editar Servicio' : 'Registrar Servicio'}</h3>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Nombre del Servicio</label>
                                <input
                                    type="text" required
                                    value={formData.nombre}
                                    onChange={e => setFormData({ ...formData, nombre: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] focus:border-transparent outline-none bg-gray-50 text-gray-800"
                                    placeholder="Ej: Corte de Cabello Mujer"
                                />
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Precio ({config?.simbolo_moneda || '$'})</label>
                                    <input
                                        type="number" required min="1" step="0.01"
                                        value={formData.precio}
                                        onChange={e => setFormData({ ...formData, precio: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] focus:border-transparent outline-none bg-gray-50 text-gray-800"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Duración (min)</label>
                                    <input
                                        type="number" required min="5" step="5"
                                        value={formData.duracion_minutos}
                                        onChange={e => setFormData({ ...formData, duracion_minutos: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] focus:border-transparent outline-none bg-gray-50 text-gray-800"
                                    />
                                </div>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <textarea
                                    value={formData.descripcion}
                                    onChange={e => setFormData({ ...formData, descripcion: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] focus:border-transparent outline-none bg-gray-50 text-gray-800 resize-none h-24"
                                    placeholder="Detalles del servicio..."
                                ></textarea>
                            </div>
                            <div className="flex space-x-4 mt-8">
                                <button
                                    type="button"
                                    onClick={() => {
                                        setShowModal(false);
                                        setIsEditing(false);
                                    }}
                                    className="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-medium transition-colors"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    className="flex-1 px-4 py-2 text-white rounded-xl font-medium shadow-md transition-transform hover:scale-105"
                                    style={{ background: 'linear-gradient(90deg, #811e86 0%, #30176b 100%)' }}
                                >
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

export default Servicios;
