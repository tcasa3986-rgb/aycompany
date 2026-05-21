import { useState, useEffect, useContext } from 'react';
import { FaPlus, FaTrash, FaEdit, FaTools } from 'react-icons/fa';
import Swal from 'sweetalert2';
import api from '../services/api';
import { ConfigContext } from '../context/ConfigContext';

function Mantenimiento() {
    const { config } = useContext(ConfigContext);
    const [mantenimientos, setMantenimientos] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [formData, setFormData] = useState({ 
        id: null, 
        equipo: '', 
        descripcion: '', 
        fecha_mantenimiento: '', 
        proxima_fecha: '', 
        costo: '', 
        estado: 'Pendiente' 
    });

    const fetchMantenimientos = async () => {
        try {
            const response = await api.get('/mantenimiento');
            setMantenimientos(response.data);
        } catch (error) {
            console.error('Error fetching mantenimientos:', error);
        }
    };

    useEffect(() => {
        fetchMantenimientos();
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            // Asegurar que si ciertas fechas o costos están vacíos se envíen de forma apropiada o nula
            const dataToSubmit = {
                ...formData,
                fecha_mantenimiento: formData.fecha_mantenimiento || new Date().toISOString().split('T')[0],
                proxima_fecha: formData.proxima_fecha || null,
                costo: formData.costo ? parseFloat(formData.costo) : 0
            };

            if (isEditing) {
                await api.put(`/mantenimiento/${formData.id}`, dataToSubmit);
                Swal.fire('Actualizado!', 'El registro ha sido actualizado.', 'success');
            } else {
                await api.post('/mantenimiento', dataToSubmit);
                Swal.fire('Guardado!', 'El mantenimiento ha sido registrado.', 'success');
            }
            setShowModal(false);
            setFormData({ id: null, equipo: '', descripcion: '', fecha_mantenimiento: '', proxima_fecha: '', costo: '', estado: 'Pendiente' });
            setIsEditing(false);
            fetchMantenimientos();
        } catch (error) {
            console.error('Error saving mantenimiento:', error);
            Swal.fire('Error', 'Hubo un error al guardar el mantenimiento.', 'error');
        }
    };

    const handleEdit = (mantenimiento) => {
        setFormData({
            ...mantenimiento,
            fecha_mantenimiento: mantenimiento.fecha_mantenimiento ? mantenimiento.fecha_mantenimiento.split('T')[0] : '',
            proxima_fecha: mantenimiento.proxima_fecha ? mantenimiento.proxima_fecha.split('T')[0] : '',
        });
        setIsEditing(true);
        setShowModal(true);
    };

    const handleDelete = async (id) => {
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminará permanentemente este registro.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar!'
        });

        if (result.isConfirmed) {
            try {
                await api.delete(`/mantenimiento/${id}`);
                Swal.fire('Eliminado!', 'El registro ha sido eliminado.', 'success');
                fetchMantenimientos();
            } catch (error) {
                console.error("Error deleting mantenimiento", error);
                Swal.fire('Error', 'No se pudo eliminar el registro.', 'error');
            }
        }
    };

    const getStatusBadge = (estado) => {
        switch (estado) {
            case 'Completado':
                return <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Completado</span>;
            case 'En Proceso':
                return <span className="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">En Proceso</span>;
            default:
                return <span className="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Pendiente</span>;
        }
    };

    return (
        <div className="h-full flex flex-col space-y-6">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-4 sm:space-y-0">
                <div>
                    <h2 className="text-2xl font-bold text-gray-800 flex items-center">
                        <FaTools className="mr-3 text-[#a42ca1]" />
                        Mantenimiento e Infraestructura
                    </h2>
                    <p className="text-gray-500 mt-1 text-sm">Gestiona el mantenimiento de tus equipos y herramientas del salón.</p>
                </div>
                <button
                    onClick={() => {
                        setIsEditing(false);
                        setFormData({ id: null, equipo: '', descripcion: '', fecha_mantenimiento: new Date().toISOString().split('T')[0], proxima_fecha: '', costo: '', estado: 'Pendiente' });
                        setShowModal(true);
                    }}
                    className="flex items-center space-x-2 px-6 py-2.5 rounded-full text-white font-semibold shadow-md transition-all hover:scale-105"
                    style={{ background: 'linear-gradient(90deg, #811e86 0%, #d82e88 100%)' }}
                >
                    <FaPlus /> <span>Nuevo Registro</span>
                </button>
            </div>

            <div className="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex-1 overflow-hidden flex flex-col">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr className="border-b-2 border-gray-100 text-gray-500 text-sm">
                                <th className="pb-4 font-semibold uppercase tracking-wider">Equipo/Activo</th>
                                <th className="pb-4 font-semibold uppercase tracking-wider">Último Mantenimiento</th>
                                <th className="pb-4 font-semibold uppercase tracking-wider">Próximo</th>
                                <th className="pb-4 font-semibold uppercase tracking-wider">Costo</th>
                                <th className="pb-4 font-semibold uppercase tracking-wider">Estado</th>
                                <th className="pb-4 font-semibold uppercase tracking-wider text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {mantenimientos.length === 0 ? (
                                <tr><td colSpan="6" className="py-8 text-center text-gray-400">No hay registros de mantenimiento.</td></tr>
                            ) : (
                                mantenimientos.map((mant) => (
                                    <tr key={mant.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                        <td className="py-4">
                                            <div className="font-medium text-gray-800">{mant.equipo}</div>
                                            <div className="text-xs text-gray-500 truncate max-w-[200px]">{mant.descripcion}</div>
                                        </td>
                                        <td className="py-4 text-gray-600">{new Date(mant.fecha_mantenimiento).toLocaleDateString()}</td>
                                        <td className="py-4 text-gray-600">{mant.proxima_fecha ? new Date(mant.proxima_fecha).toLocaleDateString() : '-'}</td>
                                        <td className="py-4 font-medium text-gray-700">{config?.simbolo_moneda || '$'}{parseFloat(mant.costo).toFixed(2)}</td>
                                        <td className="py-4">{getStatusBadge(mant.estado)}</td>
                                        <td className="py-4 flex justify-end space-x-3">
                                            <button
                                                onClick={() => handleEdit(mant)}
                                                className="p-2 text-gray-400 hover:text-blue-500 transition-colors"
                                                title="Editar"
                                            >
                                                <FaEdit />
                                            </button>
                                            <button
                                                onClick={() => handleDelete(mant.id)}
                                                className="p-2 text-gray-400 hover:text-red-500 transition-colors"
                                                title="Eliminar"
                                            >
                                                <FaTrash />
                                            </button>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                    <div className="bg-white rounded-3xl p-8 w-full max-w-lg shadow-2xl scale-100 transition-transform max-h-[90vh] overflow-y-auto">
                        <h3 className="text-xl font-bold text-gray-800 mb-6">{isEditing ? 'Editar Mantenimiento' : 'Registrar Mantenimiento'}</h3>
                        <form onSubmit={handleSubmit} className="space-y-5">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Nombre del Equipo o Activo *</label>
                                <input
                                    type="text" required
                                    value={formData.equipo}
                                    onChange={e => setFormData({ ...formData, equipo: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] outline-none"
                                    placeholder="Ej: Aire Acondicionado Principal"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Descripción del Trabajo</label>
                                <textarea
                                    value={formData.descripcion}
                                    onChange={e => setFormData({ ...formData, descripcion: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] outline-none h-20 resize-none"
                                    placeholder="Limpieza de filtros, recarga de gas..."
                                />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Fecha Realizada *</label>
                                    <input
                                        type="date" required
                                        value={formData.fecha_mantenimiento}
                                        onChange={e => setFormData({ ...formData, fecha_mantenimiento: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] outline-none"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Próximo Mantenimiento</label>
                                    <input
                                        type="date"
                                        value={formData.proxima_fecha}
                                        onChange={e => setFormData({ ...formData, proxima_fecha: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] outline-none"
                                    />
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Costo Total ({config?.simbolo_moneda || '$'})</label>
                                    <input
                                        type="number" step="0.01" min="0"
                                        value={formData.costo}
                                        onChange={e => setFormData({ ...formData, costo: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] outline-none"
                                        placeholder="0.00"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                                    <select
                                        value={formData.estado} required
                                        onChange={e => setFormData({ ...formData, estado: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] outline-none bg-white"
                                    >
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="En Proceso">En Proceso</option>
                                        <option value="Completado">Completado</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div className="flex space-x-4 mt-8 pt-4 border-t border-gray-100">
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
                                    {isEditing ? 'Guardar Cambios' : 'Registrar'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

export default Mantenimiento;
