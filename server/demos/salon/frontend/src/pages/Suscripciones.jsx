import { useState, useEffect, useContext } from 'react';
import { FaPlus, FaTrash, FaEdit, FaIdCard, FaUserCheck, FaRegCalendarAlt, FaTimes } from 'react-icons/fa';
import Swal from 'sweetalert2';
import * as suscripcionesService from '../services/suscripcionesService';
import api from '../services/api';
import { ConfigContext } from '../context/ConfigContext';

function Suscripciones() {
    const { config } = useContext(ConfigContext);
    const [activeTab, setActiveTab] = useState('planes'); // 'planes' or 'activas'
    
    // Data states
    const [planes, setPlanes] = useState([]);
    const [suscripciones, setSuscripciones] = useState([]);
    const [clientes, setClientes] = useState([]);
    
    // Modals
    const [showPlanModal, setShowPlanModal] = useState(false);
    const [showAsignarModal, setShowAsignarModal] = useState(false);
    
    // Forms
    const [planForm, setPlanForm] = useState({ id: null, nombre: '', descripcion: '', precio: '', duracion_dias: 30, servicios_incluidos: 0 });
    const [asignarForm, setAsignarForm] = useState({ cliente_id: '', plan_id: '', fecha_inicio: new Date().toISOString().split('T')[0] });

    useEffect(() => {
        fetchPlanes();
        fetchSuscripciones();
        fetchClientes();
    }, []);

    const fetchPlanes = async () => {
        try {
            const data = await suscripcionesService.getPlanes();
            setPlanes(data);
        } catch (error) {
            console.error('Error fetching planes:', error);
        }
    };

    const fetchSuscripciones = async () => {
        try {
            const data = await suscripcionesService.getSuscripciones();
            setSuscripciones(data);
        } catch (error) {
            console.error('Error fetching suscripciones:', error);
        }
    };

    const fetchClientes = async () => {
        try {
            const response = await api.get('/clientes');
            setClientes(response.data);
        } catch (error) {
            console.error('Error fetching clientes:', error);
        }
    };

    // --- PLANES HANDLERS ---
    const handlePlanSubmit = async (e) => {
        e.preventDefault();
        try {
            if (planForm.precio < 0 || planForm.duracion_dias <= 0) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'El precio y duración deben ser válidos', confirmButtonColor: '#a42ca1' });
                return;
            }
            if (planForm.id) {
                // Future implementation: await suscripcionesService.updatePlan(planForm.id, planForm);
                Swal.fire('Atención', 'Edición de planes se implementará en la próxima versión.', 'info');
            } else {
                await suscripcionesService.createPlan(planForm);
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'Plan creado.', timer: 1500, showConfirmButton: false });
            }
            setShowPlanModal(false);
            setPlanForm({ id: null, nombre: '', descripcion: '', precio: '', duracion_dias: 30, servicios_incluidos: 0 });
            fetchPlanes();
        } catch (error) {
            console.error('Error saving plan:', error);
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'No se pudo guardar el plan.', confirmButtonColor: '#a42ca1' });
        }
    };

    const handleDeletePlan = async (id) => {
        const result = await Swal.fire({
            title: '¿Eliminar Plan?', text: "Se borrará permanentemente.", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Sí, eliminar!'
        });
        if (result.isConfirmed) {
            try {
                await suscripcionesService.deletePlan(id);
                Swal.fire('Eliminado!', '', 'success');
                fetchPlanes();
            } catch (error) {
                Swal.fire('Error', 'No se pudo eliminar el plan.', 'error');
            }
        }
    };

    // --- ASIGNAR HANDLERS ---
    const handleAsignarSubmit = async (e) => {
        e.preventDefault();
        try {
            await suscripcionesService.assignPlanToClient(asignarForm);
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'Suscripción asignada al cliente.', timer: 2000, showConfirmButton: false });
            setShowAsignarModal(false);
            setAsignarForm({ cliente_id: '', plan_id: '', fecha_inicio: new Date().toISOString().split('T')[0] });
            fetchSuscripciones();
            setActiveTab('activas'); // Switch to view it
        } catch (error) {
            console.error('Error asignando:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: error.response?.data?.error || 'No se pudo asignar.', confirmButtonColor: '#a42ca1' });
        }
    };

    const handleCancelarSuscripcion = async (id) => {
        const result = await Swal.fire({
            title: '¿Cancelar Suscripción?', text: "El cliente perderá los beneficios.", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Sí, cancelar'
        });
        if (result.isConfirmed) {
            try {
                await suscripcionesService.updateSuscripcionEstado(id, 'cancelada');
                Swal.fire('Cancelada!', '', 'success');
                fetchSuscripciones();
            } catch (error) {
                Swal.fire('Error', 'No se pudo actualizar el estado.', 'error');
            }
        }
    };

    return (
        <div className="h-full flex flex-col space-y-6">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-4 sm:space-y-0">
                <div>
                    <h2 className="text-2xl font-bold text-gray-800">Suscripciones y Membresías</h2>
                    <div className="flex space-x-4 mt-4 bg-gray-50 p-1 rounded-xl w-max">
                        <button 
                            onClick={() => setActiveTab('planes')}
                            className={`px-4 py-2 rounded-lg text-sm font-semibold transition-all ${activeTab === 'planes' ? 'bg-white shadow-sm text-[#a42ca1]' : 'text-gray-500 hover:text-gray-700'}`}
                        >
                            Planes
                        </button>
                        <button 
                            onClick={() => setActiveTab('activas')}
                            className={`px-4 py-2 rounded-lg text-sm font-semibold transition-all ${activeTab === 'activas' ? 'bg-white shadow-sm text-[#a42ca1]' : 'text-gray-500 hover:text-gray-700'}`}
                        >
                            Suscripciones Activas
                        </button>
                    </div>
                </div>
                
                <div className="flex space-x-3">
                    {activeTab === 'planes' ? (
                        <button
                            onClick={() => { setPlanForm({ id: null, nombre: '', descripcion: '', precio: '', duracion_dias: 30, servicios_incluidos: 0 }); setShowPlanModal(true); }}
                            className="flex items-center space-x-2 px-5 py-2.5 rounded-full text-white font-semibold shadow-md transition-all hover:scale-105"
                            style={{ background: 'linear-gradient(90deg, #811e86 0%, #d82e88 100%)' }}
                        >
                            <FaPlus /> <span>Nuevo Plan</span>
                        </button>
                    ) : (
                        <button
                            onClick={() => setShowAsignarModal(true)}
                            className="flex items-center space-x-2 px-5 py-2.5 rounded-full text-white font-semibold shadow-md transition-all hover:scale-105"
                            style={{ background: 'linear-gradient(90deg, #2c3da4 0%, #2f8bd8 100%)' }}
                        >
                            <FaUserCheck /> <span>Asignar Suscripción</span>
                        </button>
                    )}
                </div>
            </div>

            {/* TAB: PLANES */}
            {activeTab === 'planes' && (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 overflow-y-auto pb-6">
                    {planes.length === 0 ? (
                        <div className="col-span-full p-8 text-center text-gray-400 bg-white rounded-3xl shadow-sm border border-gray-100">
                            No hay planes de suscripción registrados.
                        </div>
                    ) : (
                        planes.map((plan) => (
                            <div key={plan.id} className="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col transition-all hover:shadow-md hover:-translate-y-1 relative overflow-hidden group">
                                <div className="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-[#a42ca1]/10 rounded-bl-full -z-10"></div>
                                <div className="flex justify-between items-start mb-4">
                                    <div className="w-12 h-12 rounded-full flex items-center justify-center text-white" style={{ background: 'linear-gradient(135deg, #a42ca1 0%, #651b75 100%)' }}>
                                        <FaIdCard size={20} />
                                    </div>
                                    <div className="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onClick={() => handleDeletePlan(plan.id)} className="text-gray-400 hover:text-red-500 transition-colors bg-red-50 p-2 rounded-full"><FaTrash /></button>
                                    </div>
                                </div>
                                <h3 className="text-xl font-bold text-gray-800 mb-1">{plan.nombre}</h3>
                                <div className="text-sm font-semibold text-[#a42ca1] mb-3 bg-[#a42ca1]/10 w-max px-2 py-0.5 rounded">
                                    {plan.duracion_dias} días
                                </div>
                                <p className="text-sm text-gray-500 mb-6 flex-1">{plan.descripcion || 'Sin descripción adicional'}</p>
                                <div className="flex items-end justify-between mt-auto">
                                    <div className="text-3xl font-black text-gray-800">
                                        <span className="text-xl text-gray-400 mr-1">{config?.simbolo_moneda || '$'}</span>
                                        {parseFloat(plan.precio).toLocaleString()}
                                    </div>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            )}

            {/* TAB: SUSCRIPCIONES ACTIVAS */}
            {activeTab === 'activas' && (
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex-1 overflow-y-auto">
                     <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 text-gray-500 text-sm">
                                <th className="p-4 font-semibold">Cliente</th>
                                <th className="p-4 font-semibold">Plan</th>
                                <th className="p-4 font-semibold">Fechas</th>
                                <th className="p-4 font-semibold">Estado</th>
                                <th className="p-4 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {suscripciones.length === 0 ? (
                                <tr>
                                    <td colSpan="5" className="p-8 text-center text-gray-400">No hay suscripciones registradas.</td>
                                </tr>
                            ) : (
                                suscripciones.map((sub) => (
                                    <tr key={sub.id} className="hover:bg-gray-50/50 transition-colors">
                                        <td className="p-4">
                                            <div className="font-bold text-gray-800">{sub.cliente_nombre}</div>
                                        </td>
                                        <td className="p-4">
                                            <div className="font-medium text-[#a42ca1]">{sub.plan_nombre}</div>
                                            <div className="text-xs text-gray-400">{sub.duracion_dias} días</div>
                                        </td>
                                        <td className="p-4">
                                            <div className="text-sm text-gray-600 flex items-center gap-1"><FaRegCalendarAlt className="text-gray-400"/> Inicio: {new Date(sub.fecha_inicio).toLocaleDateString()}</div>
                                            <div className="text-sm text-gray-600 flex items-center gap-1"><FaRegCalendarAlt className="text-gray-400"/> Fin: {new Date(sub.fecha_fin).toLocaleDateString()}</div>
                                        </td>
                                        <td className="p-4">
                                            {sub.estado === 'activa' && <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Activa</span>}
                                            {sub.estado === 'vencida' && <span className="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">Vencida</span>}
                                            {sub.estado === 'cancelada' && <span className="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Cancelada</span>}
                                        </td>
                                        <td className="p-4 text-right">
                                            {sub.estado === 'activa' && (
                                                <button onClick={() => handleCancelarSuscripcion(sub.id)} className="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors" title="Cancelar Suscripción">
                                                    <FaTimes />
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            )}

            {/* Modal Nuevo Plan */}
            {showPlanModal && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                    <div className="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl">
                        <h3 className="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <FaIdCard className="text-[#a42ca1]" /> Nuevo Plan de Suscripción
                        </h3>
                        <form onSubmit={handlePlanSubmit} className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Nombre del Plan</label>
                                <input type="text" required value={planForm.nombre} onChange={e => setPlanForm({ ...planForm, nombre: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] outline-none bg-gray-50" placeholder="Ej: Plan VIP Mensual"/>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                                    <input type="number" required min="0" step="0.01" value={planForm.precio} onChange={e => setPlanForm({ ...planForm, precio: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] outline-none bg-gray-50" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Duración (días)</label>
                                    <input type="number" required min="1" value={planForm.duracion_dias} onChange={e => setPlanForm({ ...planForm, duracion_dias: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] outline-none bg-gray-50" />
                                </div>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <textarea value={planForm.descripcion} onChange={e => setPlanForm({ ...planForm, descripcion: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] outline-none bg-gray-50 resize-none h-20" placeholder="Beneficios del plan..."></textarea>
                            </div>
                            <div className="flex space-x-4 mt-8">
                                <button type="button" onClick={() => setShowPlanModal(false)} className="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-medium transition-colors">Cancelar</button>
                                <button type="submit" className="flex-1 px-4 py-2 text-white rounded-xl font-medium shadow-md transition-transform hover:scale-105" style={{ background: 'linear-gradient(90deg, #811e86 0%, #30176b 100%)' }}>Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Modal Asignar Suscripción */}
            {showAsignarModal && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                    <div className="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl">
                        <h3 className="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <FaUserCheck className="text-[#2f8bd8]" /> Asignar Suscripción
                        </h3>
                        <form onSubmit={handleAsignarSubmit} className="space-y-5">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                                <select required value={asignarForm.cliente_id} onChange={e => setAsignarForm({ ...asignarForm, cliente_id: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#2f8bd8] outline-none bg-gray-50">
                                    <option value="">-- Seleccionar Cliente --</option>
                                    {clientes.map(c => <option key={c.id} value={c.id}>{c.nombre} {c.telefono ? `(${c.telefono})` : ''}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                                <select required value={asignarForm.plan_id} onChange={e => setAsignarForm({ ...asignarForm, plan_id: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#2f8bd8] outline-none bg-gray-50">
                                    <option value="">-- Seleccionar Plan --</option>
                                    {planes.map(p => <option key={p.id} value={p.id}>{p.nombre} ({config?.simbolo_moneda || '$'}{p.precio}) - {p.duracion_dias} días</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                                <input type="date" required value={asignarForm.fecha_inicio} onChange={e => setAsignarForm({ ...asignarForm, fecha_inicio: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#2f8bd8] outline-none bg-gray-50" />
                            </div>
                            <div className="flex space-x-4 mt-8">
                                <button type="button" onClick={() => setShowAsignarModal(false)} className="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-medium transition-colors">Cancelar</button>
                                <button type="submit" className="flex-1 px-4 py-2 text-white rounded-xl font-medium shadow-md transition-transform hover:scale-105" style={{ background: 'linear-gradient(90deg, #2c3da4 0%, #2f8bd8 100%)' }}>Asignar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

export default Suscripciones;
