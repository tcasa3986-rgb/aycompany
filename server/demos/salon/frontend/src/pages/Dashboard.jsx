import { useState, useEffect, useContext } from 'react';
import api from '../services/api';
import {
    Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend, ArcElement, Filler
} from 'chart.js';
import { Line, Bar, Doughnut } from 'react-chartjs-2';
import { ConfigContext } from '../context/ConfigContext';
import { FaUserTie, FaCut, FaArrowUp, FaWallet } from 'react-icons/fa';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Title, Tooltip, Legend, Filler);

function Dashboard() {
    const { config } = useContext(ConfigContext);
    const [stats, setStats] = useState(null);

    const fetchStats = async () => {
        try {
            const response = await api.get('/dashboard/stats');
            let chartData = response.data.ingresosMensuales || [];
            if (chartData.length === 0) {
                chartData = [{ mes: '1', total: 0 }, { mes: '2', total: 0 }, { mes: '3', total: 0 }];
            }
            setStats({
                ...response.data,
                ingresosMensuales: chartData
            });
        } catch (error) {
            console.error("Error cargando dashboard:", error);
        }
    };

    useEffect(() => {
        fetchStats();
    }, []);

    if (!stats) return (
        <div className="flex justify-center items-center h-full">
            <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-[#a42ca1]"></div>
        </div>
    );

    const monthNames = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
    const labels = stats.ingresosMensuales.map(m => monthNames[parseInt(m.mes) - 1] || m.mes);
    const dataIngresos = stats.ingresosMensuales.map(m => m.total);

    // Area Line (Ingresos Netos)
    const areaLineData = {
        labels: labels.length > 0 ? labels : ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        datasets: [{
            label: 'Ingresos',
            data: dataIngresos.length > 0 ? dataIngresos : [100, 300, 150, 400, 250, 500],
            borderColor: '#ff2a7a',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            backgroundColor: (context) => {
                const ctx = context.chart.ctx;
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(255, 42, 122, 0.4)');
                gradient.addColorStop(1, 'rgba(255, 42, 122, 0.0)');
                return gradient;
            },
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#ff2a7a',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    };
    const areaLineOptions = {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: '#ff2a7a', titleColor: '#fff', bodyColor: '#fff', callbacks: { label: function (c) { return (config?.simbolo_moneda || '$') + ' ' + c.parsed.y; } } } },
        scales: { y: { grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false } }, x: { grid: { display: false } } }
    };

    // Doughnuts (Citas Eficiencia)
    const totalCitas = stats.citas.total || 1;
    const completadasPct = Math.round((stats.citas.completadas / totalCitas) * 100) || 0;
    const canceladasPct = Math.round((stats.citas.canceladas / totalCitas) * 100) || 0;
    const donut1 = { datasets: [{ data: [completadasPct, 100 - completadasPct], backgroundColor: ['#ff2a7a', 'rgba(255,255,255,0.1)'], borderWidth: 0, cutout: '75%' }] };
    const donut2 = { datasets: [{ data: [canceladasPct, 100 - canceladasPct], backgroundColor: ['#00d2ff', 'rgba(255,255,255,0.1)'], borderWidth: 0, cutout: '75%' }] };

    // Bar Chart (Crecimiento Semanal)
    const barData = {
        labels: ['S1', 'S2', 'S3', 'S4'],
        datasets: [
            { label: 'Citas', data: [15, 25, 12, 30], backgroundColor: (context) => { const ctx = context.chart.ctx; const grad = ctx.createLinearGradient(0,0,0,150); grad.addColorStop(0,'#00d2ff'); grad.addColorStop(1,'#3a7bd5'); return grad; }, borderRadius: 6, barPercentage: 0.5 },
            { label: 'Clientes', data: [10, 15, 8, 20], backgroundColor: (context) => { const ctx = context.chart.ctx; const grad = ctx.createLinearGradient(0,0,0,150); grad.addColorStop(0,'#ff2a7a'); grad.addColorStop(1,'#7a28cb'); return grad; }, borderRadius: 6, barPercentage: 0.5 }
        ]
    };
    const barOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { display: false }, x: { grid: { display: false } } } };

    // Dual Spline (Comparativa Tráfico)
    const splineData = {
        labels: ['L', 'M', 'X', 'J', 'V', 'S'],
        datasets: [
            { data: [40, 60, 30, 80, 50, 90], borderColor: '#00d2ff', borderWidth: 3, tension: 0.4, pointRadius: 0 },
            { data: [30, 70, 40, 60, 80, 50], borderColor: '#ff2a7a', borderWidth: 3, tension: 0.4, pointRadius: 0 }
        ]
    };
    const splineOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(0,0,0,0.05)', borderDash: [5,5] }, ticks: { display: false } }, x: { grid: { color: 'rgba(0,0,0,0.05)' } } } };

    return (
        <div className="h-full bg-transparent p-2 sm:p-6 overflow-y-auto w-full text-gray-800" style={{ fontFamily: "'Inter', sans-serif" }}>
            
            <div className="mb-10">
                <h1 className="text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-[#a42ca1] to-[#3a1b75]">Resumen Analítico</h1>
                <p className="text-gray-500 mt-2 font-medium">Monitorea el estatus general y rendimiento comercial del local.</p>
            </div>

            {/* FILA 1: KPIs Elegantes con COLORES FUERTES Y VIBRANTES */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {/* KPI Ingresos */}
                <div className="bg-gradient-to-br from-[#ff2a7a] to-[#d81b60] text-white rounded-3xl p-6 shadow-xl shadow-pink-500/20 border border-[#ff2a7a]/50 flex flex-col justify-between transform transition hover:-translate-y-1 relative overflow-hidden">
                    <div className="absolute -top-6 -right-6 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                    <div className="flex justify-between items-start mb-4 relative z-10">
                        <span className="text-pink-100 text-xs font-bold uppercase tracking-wider">Ingresos Netos</span>
                        <div className="bg-white/20 p-2 rounded-xl text-white shadow-sm backdrop-blur-sm"><FaWallet size={20} /></div>
                    </div>
                    <div className="relative z-10">
                        <div className="text-3xl font-black drop-shadow-md">
                            {config?.simbolo_moneda || '$'}{parseFloat(stats.ingresos).toLocaleString()}
                        </div>
                        <div className="text-[11px] text-pink-50 mt-2 flex items-center font-bold">
                            <FaArrowUp className="mr-1" /> 18% vs mes anterior
                        </div>
                    </div>
                </div>

                {/* KPI Clientes */}
                <div className="bg-gradient-to-br from-[#a42ca1] to-[#7d1b82] text-white rounded-3xl p-6 shadow-xl shadow-purple-500/20 border border-[#a42ca1]/50 flex flex-col justify-between transform transition hover:-translate-y-1 relative overflow-hidden">
                    <div className="absolute -bottom-6 -right-6 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                    <div className="flex justify-between items-start mb-4 relative z-10">
                        <span className="text-purple-200 text-xs font-bold uppercase tracking-wider">Total Clientes</span>
                        <div className="bg-white/20 p-2 rounded-xl text-white shadow-sm backdrop-blur-sm"><FaUserTie size={20} /></div>
                    </div>
                    <div className="relative z-10">
                        <div className="text-3xl font-black drop-shadow-md">{stats.clientes}</div>
                        <div className="text-[11px] text-purple-100 mt-2 flex items-center font-bold">
                            <FaArrowUp className="mr-1" /> 12% nuevos este mes
                        </div>
                    </div>
                </div>

                {/* KPI Citas */}
                <div className="bg-gradient-to-br from-[#00d2ff] to-[#3a7bd5] text-white rounded-3xl p-6 shadow-xl shadow-cyan-500/20 border border-[#00d2ff]/50 flex flex-col justify-between transform transition hover:-translate-y-1 relative overflow-hidden">
                    <div className="absolute -top-6 -left-6 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                    <div className="flex justify-between items-start mb-4 relative z-10">
                        <span className="text-cyan-100 text-xs font-bold uppercase tracking-wider">Citas Activas</span>
                        <div className="bg-white/20 p-2 rounded-xl text-white shadow-sm backdrop-blur-sm"><FaCut size={20} /></div>
                    </div>
                    <div className="relative z-10">
                        <div className="text-3xl font-black drop-shadow-md">{stats.citas.total}</div>
                        <div className="text-[11px] text-cyan-50 mt-2 flex items-center font-bold">
                            <FaArrowUp className="mr-1" /> 8% vs semana pasada
                        </div>
                    </div>
                </div>

                {/* KPI Desempeño */}
                <div className="bg-gradient-to-br from-[#3a1b75] to-[#1e1a3b] text-white rounded-3xl p-6 shadow-xl shadow-indigo-500/20 border border-[#3a1b75]/50 flex flex-col justify-center space-y-4 transform transition hover:-translate-y-1 relative overflow-hidden">
                    <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
                    <div className="relative z-10">
                        <div className="flex justify-between items-end mb-1">
                            <span className="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Retención</span>
                            <span className="text-xs font-black text-white drop-shadow-md">63%</span>
                        </div>
                        <div className="w-full bg-black/30 rounded-full h-1.5 backdrop-blur-sm">
                            <div className="bg-gradient-to-r from-[#ff2a7a] to-[#a42ca1] h-1.5 rounded-full shadow-[0_0_8px_#ff2a7a]" style={{ width: '63%' }}></div>
                        </div>
                    </div>
                    <div className="relative z-10">
                        <div className="flex justify-between items-end mb-1">
                            <span className="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Ocupación</span>
                            <span className="text-xs font-black text-white drop-shadow-md">79%</span>
                        </div>
                        <div className="w-full bg-black/30 rounded-full h-1.5 backdrop-blur-sm">
                            <div className="bg-gradient-to-r from-[#00d2ff] to-[#0052D4] h-1.5 rounded-full shadow-[0_0_8px_#00d2ff]" style={{ width: '79%' }}></div>
                        </div>
                    </div>
                </div>
            </div>

            {/* FILA 2: Bar Chart de Crecimiento + Modulo Doughnuts */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                
                {/* Bar Chart Sencillo */}
                <div className="lg:col-span-2 bg-white rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex flex-col">
                    <div className="flex justify-between items-center mb-6">
                        <h3 className="text-sm font-bold text-gray-600 uppercase tracking-widest">Crecimiento Semanal</h3>
                        <div className="bg-gray-50 text-gray-500 px-4 py-1.5 rounded-lg text-xs font-bold border border-gray-100">Citas vs Clientes</div>
                    </div>
                    <div className="flex-1 w-full min-h-[220px]">
                        <Bar data={barData} options={barOptions} />
                    </div>
                </div>

                {/* Modulo Dual Doughnut y Botones */}
                <div className="lg:col-span-1 flex flex-col space-y-8">
                    {/* Eficiencia de Citas */}
                    <div className="flex-1 bg-gradient-to-br from-[#1e1a3b] to-[#0f0e20] text-white rounded-3xl p-8 shadow-[0_8px_40px_rgba(30,26,59,0.3)] relative overflow-hidden flex flex-col">
                        <div className="absolute -top-10 -right-10 w-40 h-40 bg-pink-500 rounded-full blur-[60px] opacity-20"></div>
                        <div className="absolute -bottom-10 -left-10 w-40 h-40 bg-cyan-500 rounded-full blur-[60px] opacity-20"></div>
                        
                        <h3 className="text-sm font-bold text-gray-300 uppercase tracking-widest relative z-10 text-center mb-6">Eficiencia de Citas</h3>
                        
                        <div className="flex justify-around items-center relative z-10 flex-1">
                            <div className="flex flex-col items-center">
                                <div className="relative w-24 h-24 mb-3">
                                    <Doughnut data={donut1} options={{ cutout: '75%', plugins: { tooltip: { enabled: false } } }} />
                                    <div className="absolute inset-0 flex items-center justify-center">
                                        <span className="text-xl font-black text-white">{completadasPct}%</span>
                                    </div>
                                </div>
                                <span className="text-[10px] uppercase font-bold text-pink-300 tracking-wider">Completadas</span>
                            </div>
                            <div className="flex flex-col items-center">
                                <div className="relative w-24 h-24 mb-3">
                                    <Doughnut data={donut2} options={{ cutout: '75%', plugins: { tooltip: { enabled: false } } }} />
                                    <div className="absolute inset-0 flex items-center justify-center">
                                        <span className="text-xl font-black text-white">{canceladasPct}%</span>
                                    </div>
                                </div>
                                <span className="text-[10px] uppercase font-bold text-cyan-300 tracking-wider">Canceladas</span>
                            </div>
                        </div>
                    </div>

                    {/* Quick actions box */}
                    <div className="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex space-x-4">
                        <button className="flex-1 bg-gradient-to-r from-[#a42ca1] to-[#651b75] hover:opacity-90 transition text-white py-3.5 rounded-2xl font-bold text-sm shadow-[0_4px_15px_rgba(164,44,161,0.2)]">
                            Nuevo Cliente
                        </button>
                        <button className="flex-1 bg-gray-50 border border-gray-200 hover:bg-gray-100 transition text-gray-700 py-3.5 rounded-2xl font-bold text-sm">
                            Generar Reporte
                        </button>
                    </div>
                </div>
            </div>

            {/* FILA 3: Area Line Chart (Ingresos movido abajo) + Spline Comparativo */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                
                {/* Gran Chart de Ingresos (Ahora en posición secundaria/inferior) */}
                <div className="lg:col-span-2 bg-white rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex flex-col">
                    <div className="flex justify-between items-center mb-6">
                        <h3 className="text-sm font-bold text-gray-600 uppercase tracking-widest">Historial de Ingresos Monetarios</h3>
                        <div className="bg-gray-50 text-gray-500 px-4 py-1.5 rounded-lg text-xs font-bold cursor-pointer hover:bg-gray-100">Ver todo el año</div>
                    </div>
                    <div className="flex-1 w-full min-h-[220px]">
                        <Line data={areaLineData} options={areaLineOptions} />
                    </div>
                </div>

                {/* Spline Comparativo */}
                <div className="lg:col-span-1 bg-white rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex flex-col">
                    <div className="flex flex-col mb-6">
                        <h3 className="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Comparativa Tráfico</h3>
                        <div className="flex justify-start space-x-4">
                            <div className="flex items-center text-[10px] font-bold text-gray-500 uppercase">
                                <span className="w-2.5 h-2.5 rounded-full bg-cyan-400 mr-2"></span> Nuevos
                            </div>
                            <div className="flex items-center text-[10px] font-bold text-gray-500 uppercase">
                                <span className="w-2.5 h-2.5 rounded-full bg-pink-500 mr-2"></span> Recurrentes
                            </div>
                        </div>
                    </div>
                    <div className="flex-1 w-full min-h-[160px]">
                        <Line data={splineData} options={splineOptions} />
                    </div>
                </div>

            </div>

        </div>
    );
}

export default Dashboard;
