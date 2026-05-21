import { useState, useEffect, useContext } from 'react';
import { FaCalendarPlus, FaCheck, FaTimes, FaUser, FaCut, FaClock, FaEdit, FaTrash, FaMoneyBillWave } from 'react-icons/fa';
import Swal from 'sweetalert2';
import api from '../services/api';
import { pagosService } from '../services/pagosService';
import { ConfigContext } from '../context/ConfigContext';
import ExportButtons from '../components/ExportButtons';

function Citas() {
    const { config } = useContext(ConfigContext);
    const [citas, setCitas] = useState([]);
    const [clientes, setClientes] = useState([]);
    const [servicios, setServicios] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [formData, setFormData] = useState({ id: null, cliente_id: '', servicio_id: '', usuario_id: '1', fecha_hora: '', estado: '' }); // usuario_id 1 is the default admin we created

    // Estado para pagos parciales (abonos)
    const [showAbonoModal, setShowAbonoModal] = useState(false);
    const [currentCitaAbono, setCurrentCitaAbono] = useState(null);
    const [historialPagos, setHistorialPagos] = useState([]);
    const [abonoForm, setAbonoForm] = useState({ monto: '', metodo_pago: 'efectivo' });

    const fetchData = async () => {
        try {
            const [citasRes, clientesRes, servsRes] = await Promise.all([
                api.get('/citas'),
                api.get('/clientes'),
                api.get('/servicios')
            ]);
            setCitas(citasRes.data);
            setClientes(clientesRes.data);
            setServicios(servsRes.data);
        } catch (error) {
            console.error('Error fetching citas data:', error);
        }
    };

    useEffect(() => {
        fetchData();
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            if (isEditing) {
                await api.put(`/citas/${formData.id}`, formData);
                Swal.fire({ icon: 'success', title: 'Actualizado', text: 'Cita actualizada correctamente.', timer: 1500, showConfirmButton: false });
            } else {
                await api.post('/citas', formData);
                Swal.fire({ icon: 'success', title: 'Agendado', text: 'Cita reservada correctamente.', timer: 1500, showConfirmButton: false });
            }
            setShowModal(false);
            setFormData({ id: null, cliente_id: '', servicio_id: '', usuario_id: '1', fecha_hora: '', estado: '' });
            setIsEditing(false);
            fetchData();
        } catch (error) {
            console.error('Error saving cita:', error);
            const errorMessage = error.response?.data?.error || 'No se pudo guardar la cita.';
            Swal.fire({ icon: 'error', title: 'No disponible', text: errorMessage, confirmButtonColor: '#a42ca1' });
        }
    };

    const handleEdit = (cita) => {
        // Formatear la fecha para el input datetime-local (YYYY-MM-DDThh:mm)
        let formattedDate = cita.fecha_hora;
        if (formattedDate) {
            const dateObj = new Date(formattedDate);
            // Ajustar al timezone local para el input
            const tzOffset = dateObj.getTimezoneOffset() * 60000;
            formattedDate = (new Date(dateObj - tzOffset)).toISOString().slice(0, 16);
        }

        setFormData({ ...cita, fecha_hora: formattedDate });
        setIsEditing(true);
        setShowModal(true);
    };

    const handleDelete = async (id) => {
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar!'
        });

        if (result.isConfirmed) {
            try {
                await api.delete(`/citas/${id}`);
                Swal.fire('Eliminada!', 'La cita ha sido eliminada.', 'success');
                fetchData();
            } catch (error) {
                console.error("Error deleting cita", error);
                Swal.fire('Error', 'No se pudo eliminar la cita.', 'error');
            }
        }
    };

    const handleUpdateStatus = async (id, estado) => {
        try {
            await api.patch(`/citas/${id}/estado`, { estado });
            fetchData();
            if (estado === 'completada') {
                Swal.fire({ icon: 'success', title: 'Completada', text: 'La cita ha sido marcada como completada y facturada.', timer: 1500, showConfirmButton: false });
            }
        } catch (error) {
            console.error('Error updating cita status:', error);
        }
    };

    const handleOpenAbono = async (cita) => {
        setCurrentCitaAbono(cita);
        setAbonoForm({ monto: '', metodo_pago: 'efectivo' });
        try {
            const pagos = await pagosService.getPagosPorCita(cita.id);
            setHistorialPagos(pagos);
        } catch (error) {
            console.error('Error fetching historial pagos', error);
        }
        setShowAbonoModal(true);
    };

    const handleAbonar = async (e) => {
        e.preventDefault();
        if (!currentCitaAbono) return;
        
        try {
            const result = await pagosService.registrarPago({
                cita_id: currentCitaAbono.id,
                monto: parseFloat(abonoForm.monto),
                metodo_pago: abonoForm.metodo_pago
            });
            
            setShowAbonoModal(false);
            
            if (result.nuevo_estado === 'completada') {
                Swal.fire({ icon: 'success', title: '¡Pagado y Completado!', text: 'El saldo ha sido cubierto y la cita se completó automáticamente.', confirmButtonColor: '#10b981' });
            } else {
                Swal.fire({ icon: 'success', title: 'Abono Registrado', text: 'El pago parcial se ha guardado correctamente.', timer: 1500, showConfirmButton: false });
            }
            
            fetchData();
        } catch (error) {
            console.error('Error al registrar abono', error);
            Swal.fire('Error', 'No se pudo guardar el pago parcial', 'error');
        }
    };

    const getStatusBadge = (estado) => {
        const statusMap = {
            pendiente: { bg: 'bg-amber-100', text: 'text-amber-600', label: 'Pendiente' },
            confirmada: { bg: 'bg-blue-100', text: 'text-blue-600', label: 'Confirmada' },
            completada: { bg: 'bg-green-100', text: 'text-green-600', label: 'Completada' },
            cancelada: { bg: 'bg-red-100', text: 'text-red-600', label: 'Cancelada' },
        };
        const s = statusMap[estado] || statusMap.pendiente;
        return <span className={`px-3 py-1 rounded-full text-xs font-bold ${s.bg} ${s.text}`}>{s.label}</span>;
    };

    const exportColumns = [
        { label: 'Fecha/Hora', key: 'fecha_hora' },
        { label: 'Cliente', key: 'cliente_nombre' },
        { label: 'Servicio', key: 'servicio_nombre' },
        { label: 'Estilista', key: 'estilista_nombre' },
        { label: 'Estado', key: 'estado' },
        { label: 'Costo', key: 'precio' },
        { label: 'Abonado', key: 'total_abonado' }
    ];

    return (
        <div className="h-full flex flex-col space-y-6">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-4 sm:space-y-0">
                <h2 className="text-2xl font-bold text-gray-800">Agenda de Citas</h2>
                <div className="flex items-center space-x-4">
                    <ExportButtons title="Agenda de Citas" columns={exportColumns} data={citas} fileName="reporte_citas" />
                    <button
                        onClick={() => {
                            setIsEditing(false);
                            setFormData({ id: null, cliente_id: '', servicio_id: '', usuario_id: '1', fecha_hora: '', estado: '' });
                            setShowModal(true);
                        }}
                        className="flex items-center space-x-2 px-6 py-2.5 rounded-full text-white font-semibold shadow-md transition-all hover:scale-105"
                        style={{ background: 'linear-gradient(90deg, #811e86 0%, #d82e88 100%)' }}
                    >
                        <FaCalendarPlus /> <span>Agendar Cita</span>
                    </button>
                </div>
            </div>

            <div className="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex-1 overflow-hidden flex flex-col">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="border-b-2 border-gray-100 text-gray-500 text-sm">
                                <th className="pb-4 font-semibold">Fecha y Hora</th>
                                <th className="pb-4 font-semibold">Cliente</th>
                                <th className="pb-4 font-semibold">Servicio</th>
                                <th className="pb-4 font-semibold">Estilista</th>
                                <th className="pb-4 font-semibold">Estado</th>
                                <th className="pb-4 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {citas.length === 0 ? (
                                <tr><td colSpan="6" className="py-8 text-center text-gray-400">No hay citas agendadas.</td></tr>
                            ) : (
                                citas.map((cita) => (
                                    <tr key={cita.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                        <td className="py-4 text-gray-800 font-medium">
                                            {new Date(cita.fecha_hora).toLocaleString('es-ES', { dateStyle: 'short', timeStyle: 'short' })}
                                        </td>
                                        <td className="py-4 text-gray-600 font-medium">{cita.cliente_nombre}</td>
                                        <td className="py-4 text-gray-600">
                                            {cita.servicio_nombre} 
                                            <div className="text-xs">
                                                <span className="text-gray-400">Total: {config?.simbolo_moneda || '$'}{cita.precio}</span>
                                                {parseFloat(cita.total_abonado) > 0 && (
                                                    <span className="ml-2 text-green-600 font-semibold bg-green-50 px-1 rounded">
                                                        Abonado: {config?.simbolo_moneda || '$'}{cita.total_abonado}
                                                    </span>
                                                )}
                                            </div>
                                        </td>
                                        <td className="py-4 text-gray-600">{cita.estilista_nombre}</td>
                                        <td className="py-4 whitespace-nowrap">{getStatusBadge(cita.estado)}</td>
                                        <td className="py-4 flex justify-end space-x-2">
                                            {(cita.estado === 'pendiente' || cita.estado === 'confirmada') && (
                                                <button onClick={() => handleOpenAbono(cita)} className="p-2 text-green-600 hover:bg-green-50 rounded-full transition-colors font-bold flex items-center" title="Registrar Pago / Abono"><FaMoneyBillWave size={16} /> <span className="ml-1 text-xs">Abonar</span></button>
                                            )}
                                            {cita.estado === 'pendiente' && (
                                                <button onClick={() => handleUpdateStatus(cita.id, 'confirmada')} className="p-2 text-blue-500 hover:bg-blue-50 rounded-full transition-colors" title="Confirmar"><FaCheck size={14} /></button>
                                            )}
                                            {(cita.estado === 'pendiente' || cita.estado === 'confirmada') && (
                                                <>
                                                    <button onClick={() => handleUpdateStatus(cita.id, 'completada')} className="p-2 text-green-500 hover:bg-green-50 rounded-full transition-colors" title="Marcar Completada"><FaCheck size={14} /><FaCheck size={14} className="-ml-2" /></button>
                                                    <button onClick={() => handleUpdateStatus(cita.id, 'cancelada')} className="p-2 text-amber-500 hover:bg-amber-50 rounded-full transition-colors" title="Cancelar"><FaTimes size={14} /></button>
                                                </>
                                            )}
                                            <div className="w-px h-6 bg-gray-200 mx-1 self-center hidden sm:block"></div>
                                            <button onClick={() => handleEdit(cita)} className="p-2 text-gray-400 hover:text-blue-500 rounded-full transition-colors" title="Editar"><FaEdit size={14} /></button>
                                            <button onClick={() => handleDelete(cita.id)} className="p-2 text-gray-400 hover:text-red-500 rounded-full transition-colors" title="Eliminar"><FaTrash size={14} /></button>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Modal Nueva Cita */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                    <div className="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl scale-100 transition-transform">
                        <h3 className="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2"><FaCalendarPlus className="text-[#a42ca1]" /> {isEditing ? 'Editar Cita' : 'Nueva Cita'}</h3>
                        <form onSubmit={handleSubmit} className="space-y-4">

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1"><FaUser className="text-gray-400" /> Cliente</label>
                                <select
                                    required
                                    value={formData.cliente_id}
                                    onChange={e => setFormData({ ...formData, cliente_id: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] focus:border-transparent outline-none bg-gray-50 text-gray-800"
                                >
                                    <option value="" disabled>Seleccionar un cliente...</option>
                                    {clientes.map(c => <option key={c.id} value={c.id}>{c.nombre}</option>)}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1"><FaCut className="text-gray-400" /> Servicio</label>
                                <select
                                    required
                                    value={formData.servicio_id}
                                    onChange={e => setFormData({ ...formData, servicio_id: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] focus:border-transparent outline-none bg-gray-50 text-gray-800"
                                >
                                    <option value="" disabled>Seleccionar un servicio...</option>
                                    {servicios.map(s => <option key={s.id} value={s.id}>{s.nombre} - {config?.simbolo_moneda || '$'}{parseFloat(s.precio)} ({s.duracion_minutos}m)</option>)}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1"><FaClock className="text-gray-400" /> Fecha y Hora</label>
                                <input
                                    type="datetime-local"
                                    required
                                    value={formData.fecha_hora}
                                    onChange={e => setFormData({ ...formData, fecha_hora: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] focus:border-transparent outline-none bg-gray-50 text-gray-800"
                                />
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
                                    disabled={clientes.length === 0 || servicios.length === 0}
                                    className="flex-1 px-4 py-2 text-white rounded-xl font-medium shadow-md transition-transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                                    style={{ background: 'linear-gradient(90deg, #811e86 0%, #30176b 100%)' }}
                                >
                                    Agendar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
            
            {/* Modal de Abonos */}
            {showAbonoModal && currentCitaAbono && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                    <div className="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl scale-100 transition-transform">
                        <div className="flex justify-between items-center mb-6">
                            <h3 className="text-xl font-bold text-gray-800 flex items-center gap-2">
                                <FaMoneyBillWave className="text-[#a42ca1]" /> Registrar Abono
                            </h3>
                            <button onClick={() => setShowAbonoModal(false)} className="text-gray-400 hover:text-gray-600"><FaTimes /></button>
                        </div>
                        
                        <div className="bg-gray-50 p-4 rounded-xl mb-6 border border-gray-100">
                            <div className="flex justify-between text-sm mb-1">
                                <span className="text-gray-500">Cliente:</span>
                                <span className="font-medium text-gray-800">{currentCitaAbono.cliente_nombre}</span>
                            </div>
                            <div className="flex justify-between text-sm mb-1">
                                <span className="text-gray-500">Servicio:</span>
                                <span className="font-medium text-gray-800">{currentCitaAbono.servicio_nombre}</span>
                            </div>
                            <div className="flex justify-between text-sm mb-1 mt-3">
                                <span className="text-gray-500">Total a Pagar:</span>
                                <span className="font-bold text-gray-800">{config?.simbolo_moneda || '$'}{currentCitaAbono.precio}</span>
                            </div>
                            <div className="flex justify-between text-sm text-green-600 font-medium">
                                <span>Abonado hasta ahora:</span>
                                <span>{config?.simbolo_moneda || '$'}{currentCitaAbono.total_abonado || 0}</span>
                            </div>
                            <div className="border-t border-gray-200 my-2"></div>
                            <div className="flex justify-between text-sm font-bold text-red-500">
                                <span>Saldo Pendiente:</span>
                                <span>{config?.simbolo_moneda || '$'}{Math.max(0, currentCitaAbono.precio - (currentCitaAbono.total_abonado || 0))}</span>
                            </div>
                        </div>

                        {/* Historial rápido */}
                        {historialPagos.length > 0 && (
                            <div className="mb-6">
                                <h4 className="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Historial de Pagos</h4>
                                <div className="space-y-2 max-h-32 overflow-y-auto pr-1">
                                    {historialPagos.map((pago, idx) => (
                                        <div key={idx} className="text-sm flex justify-between items-center bg-gray-50 border border-gray-100 px-3 py-2 rounded-lg">
                                            <div className="flex flex-col">
                                                <span className="font-bold text-gray-800">{config?.simbolo_moneda || '$'}{pago.monto}</span>
                                                <span className="text-[10px] text-gray-400 font-medium">{new Date(pago.fecha).toLocaleString()}</span>
                                            </div>
                                            <span className="text-[10px] uppercase font-bold bg-white border border-gray-200 text-gray-500 px-2 py-1 rounded-md">{pago.metodo_pago}</span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {Math.max(0, currentCitaAbono.precio - (currentCitaAbono.total_abonado || 0)) > 0 ? (
                            <form onSubmit={handleAbonar} className="space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Monto a abonar ({config?.simbolo_moneda || '$'})</label>
                                    <input
                                        type="number"
                                        required
                                        min="1"
                                        step="0.01"
                                        max={currentCitaAbono.precio - (currentCitaAbono.total_abonado || 0)}
                                        value={abonoForm.monto}
                                        onChange={e => setAbonoForm({ ...abonoForm, monto: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] focus:border-transparent outline-none bg-white text-gray-800"
                                    />
                                    <p className="text-xs text-gray-400 mt-1">El monto máximo es el saldo pendiente.</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                                    <select
                                        required
                                        value={abonoForm.metodo_pago}
                                        onChange={e => setAbonoForm({ ...abonoForm, metodo_pago: e.target.value })}
                                        className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#a42ca1] focus:border-transparent outline-none bg-gray-50 text-gray-800"
                                    >
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tarjeta">Tarjeta</option>
                                        <option value="transferencia">Transferencia</option>
                                    </select>
                                </div>

                                <button
                                    type="submit"
                                    className="w-full mt-6 px-4 py-3 text-white rounded-xl font-bold shadow-md transition-transform hover:scale-105"
                                    style={{ background: 'linear-gradient(90deg, #10b981 0%, #047857 100%)' }}
                                >
                                    Guardar Abono
                                </button>
                            </form>
                        ) : (
                            <div className="text-center p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl font-bold flex flex-col items-center justify-center">
                                <FaCheck size={24} className="mb-2" />
                                Esta cita ya está completamente pagada.
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}

export default Citas;
