<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Dashboard</h2>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- Filtro de Sucursal --}}
            @if(isset($sucursales) && count($sucursales) > 0)
                <form method="GET" action="{{ route('dashboard') }}" class="mb-6 flex justify-end">
                    <div class="relative inline-block text-left w-full sm:w-auto">
                        <select name="sucursal_id" onchange="this.form.submit()"
                            class="block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2">
                            <option value="">Todas las Sucursales</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" {{ $sucursalId == $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif

            {{-- Estadísticas Principales --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
                {{-- Total Equipos --}}
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-5">
                    <div class="text-white">
                        <div class="text-sm opacity-90">Total Equipos</div>
                        <div class="text-4xl font-bold mt-2">{{ $stats['total_equipos'] }}</div>
                    </div>
                </div>

                {{-- Disponibles --}}
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-5">
                    <div class="text-white">
                        <div class="text-sm opacity-90">Disponibles</div>
                        <div class="text-4xl font-bold mt-2">{{ $stats['equipos_disponibles'] }}</div>
                    </div>
                </div>

                {{-- Asignados --}}
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow p-5">
                    <div class="text-white">
                        <div class="text-sm opacity-90">Asignados</div>
                        <div class="text-4xl font-bold mt-2">{{ $stats['equipos_asignados'] }}</div>
                    </div>
                </div>

                {{-- En Reparación --}}
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow p-5">
                    <div class="text-white">
                        <div class="text-sm opacity-90">En Reparación</div>
                        <div class="text-4xl font-bold mt-2">{{ $stats['equipos_reparacion'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Sección Principal: Gráficos y Métricas --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">

                {{-- Distribución de Equipos --}}
                <div class="bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Distribución por Estado</h3>
                    <div class="relative" style="height: 300px;">
                        <canvas id="equiposDonutChart"></canvas>
                    </div>

                    {{-- Leyenda personalizada --}}
                    <div class="grid grid-cols-2 gap-3 mt-6">
                        @foreach($equiposPorEstado as $estado)
                            @php
                                $colorClasses = [
                                    'Disponible' => 'bg-green-500',
                                    'Asignado' => 'bg-purple-500',
                                    'En Reparacion' => 'bg-orange-500',
                                    'De Baja' => 'bg-red-500'
                                ];
                                $color = $colorClasses[$estado->estado] ?? 'bg-blue-500';
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="{{ $color }} w-3 h-3 rounded-full"></div>
                                <span class="text-gray-300 text-sm">{{ $estado->estado }}</span>
                                <span class="text-white font-semibold ml-auto">{{ $estado->total }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Equipos por Tipo (Nuevo) --}}
                <div class="bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Equipos por Tipo</h3>
                    <div class="relative" style="height: 300px;">
                        <canvas id="equiposTipoChart"></canvas>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-2 justify-center">
                        <span class="text-sm text-gray-400">Total de tipos registrados:
                            {{ count($equiposPorTipo) }}</span>
                    </div>
                </div>

                {{-- Métricas Rápidas y Tendencia --}}
                <div class="lg:col-span-2 xl:col-span-1 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-4">
                    <div class="bg-gray-800 rounded-lg shadow p-4 sm:p-5">
                        <div class="text-gray-400 text-sm">Empleados</div>
                        <div class="text-white text-3xl font-bold mt-1">{{ $stats['total_empleados'] }}</div>
                    </div>

                    <div class="bg-gray-800 rounded-lg shadow p-5">
                        <div class="text-gray-400 text-sm">Asignaciones Activas</div>
                        <div class="text-white text-3xl font-bold mt-1">{{ $stats['total_asignaciones_activas'] }}</div>
                    </div>

                    <div class="bg-gray-800 rounded-lg shadow p-5">
                        <div class="text-gray-400 text-sm">Reparaciones Pendientes</div>
                        <div class="text-white text-3xl font-bold mt-1">{{ $stats['total_reparaciones_pendientes'] }}
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-lg shadow p-5">
                        <div class="text-gray-400 text-sm">Bajas</div>
                        <div class="text-white text-3xl font-bold mt-1">{{ $stats['total_bajas'] }}</div>
                    </div>

                    {{-- Gráfico de Tendencia Mensual --}}
                    <div class="col-span-1 sm:col-span-2 xl:col-span-1 bg-gray-800 rounded-lg shadow p-4 sm:p-5">
                        <h4 class="text-sm font-semibold text-gray-300 mb-3">Tendencia de Asignaciones</h4>
                        <div class="relative" style="height: 180px;">
                            <canvas id="tendenciaMensualChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Costos de Reparación del Mes --}}
            <div class="mb-6">
                <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-white text-lg font-semibold">Costos de Reparación del Mes</h3>
                            <p class="text-orange-100 text-sm mt-1">{{ now()->locale('es')->translatedFormat('F Y') }}
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <div class="text-white text-4xl font-bold">
                                {{ $setting->currency_symbol ?? 'S/' }}
                                {{ number_format($costosReparacion['total'], 2) }}
                            </div>
                            <div class="text-orange-100 text-sm mt-2">Total invertido</div>
                        </div>
                        <div>
                            <div class="text-white text-4xl font-bold">{{ $costosReparacion['count'] }}</div>
                            <div class="text-orange-100 text-sm mt-2">Reparaciones realizadas</div>
                        </div>
                    </div>

                    @if($costosReparacion['count'] > 0)
                        <div class="mt-4 pt-4 border-t border-white border-opacity-20">
                            <div class="flex items-center text-orange-100">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                <span class="text-sm">Promedio:
                                    {{ $setting->currency_symbol ?? 'S/' }}
                                    {{ number_format($costosReparacion['total'] / $costosReparacion['count'], 2) }} por
                                    reparación</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Top Empleados y Actividad --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">

                {{-- Top 5 Empleados --}}
                <div class="bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Top 5 Empleados</h3>
                    <div class="space-y-3 mb-6">
                        @forelse($topEmpleados as $empleado)
                            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($empleado->nombres, 0, 1) }}{{ substr($empleado->apellidos, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-white font-medium text-sm">{{ $empleado->nombreCompleto() }}</div>
                                        <div class="text-gray-400 text-xs">{{ $empleado->cargo->nombre ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div
                                    class="px-3 py-1 bg-blue-500 bg-opacity-20 text-blue-400 rounded-full text-xs font-semibold">
                                    {{ $empleado->equipos_asignados }} equipos
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 text-center py-8">No hay datos disponibles</p>
                        @endforelse
                    </div>

                    {{-- Gráfico de barras --}}
                    @if(count($topEmpleados) > 0)
                        <div class="relative" style="height: 250px;">
                            <canvas id="topEmpleadosBarChart"></canvas>
                        </div>
                    @endif
                </div>

                {{-- Actividad Reciente --}}
                <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Actividad Reciente</h3>
                    <div class="space-y-3">
                        @forelse($actividadReciente as $actividad)
                            <div class="flex items-start space-x-3 p-3 bg-gray-700 rounded-lg">
                                <div
                                    class="w-8 h-8 rounded-full bg-{{ $actividad['color'] }}-500 bg-opacity-20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <div class="w-2 h-2 rounded-full bg-{{ $actividad['color'] }}-400"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-white text-sm">{{ $actividad['descripcion'] }}</p>
                                    <p class="text-gray-400 text-xs mt-1">{{ $actividad['fecha']->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 text-center py-8">No hay actividad reciente</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Alertas --}}
            @if(count($alertas) > 0)
                <div class="mt-6">
                    <div class="bg-yellow-900 bg-opacity-30 border border-yellow-600 rounded-lg p-4">
                        <h3 class="text-yellow-400 font-semibold mb-3">⚠️ Alertas Importantes</h3>
                        <div class="space-y-2">
                            @foreach($alertas as $alerta)
                                <div class="flex items-center justify-between p-3 bg-gray-800 bg-opacity-50 rounded">
                                    <span class="text-white text-sm">{{ $alerta['mensaje'] }}</span>
                                    <a href="{{ $alerta['accion'] }}"
                                        class="px-4 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded text-sm transition">
                                        Ver
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // ====== CONFIGURACIÓN GLOBAL DARK THEME ======
                Chart.defaults.color = '#E5E7EB'; // Gray-200
                Chart.defaults.borderColor = '#374151'; // Gray-700
                Chart.defaults.font.family = 'Inter, sans-serif';

                // ====== COLORES DEL TEMA ======
                const colors = {
                    disponible: { bg: 'rgba(34, 197, 94, 0.8)', border: 'rgb(34, 197, 94)' },
                    asignado: { bg: 'rgba(168, 85, 247, 0.8)', border: 'rgb(168, 85, 247)' },
                    reparacion: { bg: 'rgba(249, 115, 22, 0.8)', border: 'rgb(249, 115, 22)' },
                    baja: { bg: 'rgba(239, 68, 68, 0.8)', border: 'rgb(239, 68, 68)' },
                    blue: { bg: 'rgba(59, 130, 246, 0.8)', border: 'rgb(59, 130, 246)' },
                    palette: [
                        'rgba(59, 130, 246, 0.8)',   // Blue
                        'rgba(16, 185, 129, 0.8)',   // Green
                        'rgba(245, 158, 11, 0.8)',   // Amber
                        'rgba(239, 68, 68, 0.8)',    // Red
                        'rgba(139, 92, 246, 0.8)',   // Violet
                        'rgba(236, 72, 153, 0.8)',   // Pink
                        'rgba(14, 165, 233, 0.8)',   // Sky
                        'rgba(20, 184, 166, 0.8)',   // Teal
                    ]
                };

                // ====== GRÁFICO DE DONUT - DISTRIBUCIÓN DE EQUIPOS ======
                const donutCanvas = document.getElementById('equiposDonutChart');
                if (donutCanvas) {
                    const equiposData = @json($equiposPorEstado);
                    const labels = equiposData.map(item => item.estado);
                    const data = equiposData.map(item => item.total);

                    const backgroundColors = equiposData.map(item => {
                        switch (item.estado) {
                            case 'Disponible': return colors.disponible.bg;
                            case 'Asignado': return colors.asignado.bg;
                            case 'En Reparacion': return colors.reparacion.bg;
                            case 'De Baja': return colors.baja.bg;
                            default: return colors.blue.bg;
                        }
                    });

                    const borderColors = equiposData.map(item => {
                        switch (item.estado) {
                            case 'Disponible': return colors.disponible.border;
                            case 'Asignado': return colors.asignado.border;
                            case 'En Reparacion': return colors.reparacion.border;
                            case 'De Baja': return colors.baja.border;
                            default: return colors.blue.border;
                        }
                    });

                    new Chart(donutCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: backgroundColors,
                                borderColor: borderColors,
                                borderWidth: 2,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                                    titleColor: '#F3F4F6',
                                    bodyColor: '#E5E7EB',
                                    padding: 12,
                                    callbacks: {
                                        label: function (context) {
                                            const value = context.parsed || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            return `${context.label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            cutout: '65%'
                        }
                    });
                }

                // ====== GRÁFICO BAR CHART HORIZONTAL - EQUIPOS POR TIPO (NUEVO) ======
                const tiposCanvas = document.getElementById('equiposTipoChart');
                if (tiposCanvas) {
                    const tiposData = @json($equiposPorTipo);
                    const labels = tiposData.map(item => item.tipo);
                    const data = tiposData.map(item => item.total);

                    new Chart(tiposCanvas, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                axis: 'y',
                                label: 'Cantidad',
                                data: data,
                                backgroundColor: colors.palette,
                                borderColor: 'rgba(0,0,0,0)',
                                borderWidth: 0,
                                borderRadius: 4,
                                barThickness: 20
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                                    titleColor: '#F3F4F6',
                                    bodyColor: '#E5E7EB',
                                    padding: 12,
                                    callbacks: {
                                        title: (items) => items[0].label,
                                        label: (context) => `Cantidad: ${context.parsed.x}`
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { color: 'rgba(75, 85, 99, 0.2)', borderDash: [5, 5] },
                                    ticks: { color: '#9CA3AF', stepSize: 1 }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { color: '#E5E7EB', font: { size: 12 } }
                                }
                            },
                            layout: { padding: 0 }
                        }
                    });
                }

                // ====== GRÁFICO DE BARRAS - TOP EMPLEADOS ======
                const barCanvas = document.getElementById('topEmpleadosBarChart');
                if (barCanvas) {
                    const empleadosData = @json($topEmpleados);
                    const labels = empleadosData.map(emp => {
                        const nombres = emp.nombres.split(' ')[0];
                        const apellidos = emp.apellidos.split(' ')[0];
                        return `${nombres} ${apellidos}`;
                    });
                    const data = empleadosData.map(emp => emp.equipos_asignados);

                    new Chart(barCanvas, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Equipos Asignados',
                                data: data,
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 2,
                                borderRadius: 6,
                                barThickness: 30
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                                    padding: 12,
                                }
                            },
                            scales: {
                                x: { ticks: { color: '#9CA3AF', stepSize: 1 }, grid: { color: 'rgba(55, 65, 81, 0.5)', drawBorder: false } },
                                y: { ticks: { color: '#E5E7EB' }, grid: { display: false } }
                            }
                        }
                    });
                }

                // ====== GRÁFICO DE LÍNEAS - TENDENCIA MENSUAL ======
                const lineCanvas = document.getElementById('tendenciaMensualChart');
                if (lineCanvas) {
                    const tendenciaData = @json($tendenciaMensual);
                    const ctx = lineCanvas.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 180);
                    gradient.addColorStop(0, 'rgba(168, 85, 247, 0.4)');
                    gradient.addColorStop(1, 'rgba(168, 85, 247, 0.0)');

                    new Chart(lineCanvas, {
                        type: 'line',
                        data: {
                            labels: tendenciaData.meses,
                            datasets: [{
                                label: 'Asignaciones',
                                data: tendenciaData.datos,
                                borderColor: 'rgb(168, 85, 247)',
                                backgroundColor: gradient,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'rgb(168, 85, 247)',
                                pointBorderColor: '#fff',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } }, // ... same options
                            scales: {
                                x: { ticks: { color: '#9CA3AF' }, grid: { display: false } },
                                y: { beginAtZero: true, ticks: { color: '#9CA3AF', stepSize: 1 }, grid: { color: 'rgba(55, 65, 81, 0.3)' } }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>