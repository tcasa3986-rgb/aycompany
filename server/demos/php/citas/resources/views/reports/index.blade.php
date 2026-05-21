<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Reportes y Estadísticas</h1>
                <p class="text-sm text-gray-500 mt-1">Visión general del rendimiento del sistema</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.appointments') }}"
                    class="inline-flex items-center gap-2 border border-[#4A88F6] text-[#4A88F6] hover:bg-blue-50 font-semibold px-4 py-2 rounded-xl text-sm transition">
                    📋 Citas
                </a>
                <a href="{{ route('reports.revenue') }}"
                    class="inline-flex items-center gap-2 border border-[#4A88F6] text-[#4A88F6] hover:bg-blue-50 font-semibold px-4 py-2 rounded-xl text-sm transition">
                    💰 Ingresos
                </a>
                <a href="{{ route('reports.patients') }}"
                    class="inline-flex items-center gap-2 border border-[#4A88F6] text-[#4A88F6] hover:bg-blue-50 font-semibold px-4 py-2 rounded-xl text-sm transition">
                    👥 Pacientes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">Total Citas</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalAppointments) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">Total Pacientes</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalPatients) }}</p>
            </div>
            <div class="bg-green-50 rounded-2xl border border-green-100 shadow-sm p-5">
                <p class="text-xs text-green-600 uppercase font-semibold tracking-wide">Ingresos Totales</p>
                <p class="text-2xl font-bold text-green-700 mt-1">S/ {{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div
                class="{{ $cancellationRate >= 20 ? 'bg-red-50 border-red-100' : 'bg-blue-50 border-blue-100' }} rounded-2xl border shadow-sm p-5">
                <p
                    class="text-xs {{ $cancellationRate >= 20 ? 'text-red-600' : 'text-blue-600' }} uppercase font-semibold tracking-wide">
                    Tasa Cancelación</p>
                <p class="text-3xl font-bold {{ $cancellationRate >= 20 ? 'text-red-700' : 'text-blue-700' }} mt-1">
                    {{ $cancellationRate }}%</p>
            </div>
        </div>

        {{-- Charts row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Appointments per month chart --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Citas por Mes (últimos 12 meses)</h2>
                <canvas id="apptChart" height="120"></canvas>
            </div>

            {{-- Status donut --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Citas por Estado</h2>
                <canvas id="donutChart"></canvas>
            </div>
        </div>

        {{-- Revenue + Top Doctors --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Revenue per month --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Ingresos por Mes — S/ (últimos 12 meses)</h2>
                <canvas id="revenueChart" height="120"></canvas>
            </div>

            {{-- Top doctors --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Top 5 Médicos</h2>
                <ul class="space-y-3">
                    @forelse($topDoctors as $dr)
                        <li class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 font-medium">Dr. {{ $dr->name }}</span>
                            <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-2.5 py-0.5 rounded-full">
                                {{ $dr->appointments_count }} citas
                            </span>
                        </li>
                    @empty
                        <li class="text-sm text-gray-400 text-center py-4">Sin datos</li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
        <script>
            const monthLabels = @json($monthLabels);
            const monthValues = @json($monthValues);
            const revenueValues = @json($revenueValues);
            const donutLabels = @json($donutLabels);
            const donutValues = @json($donutValues);

            // Appointments bar chart
            new Chart(document.getElementById('apptChart'), {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Citas',
                        data: monthValues,
                        backgroundColor: 'rgba(74,136,246,0.7)',
                        borderRadius: 6,
                    }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });

            // Revenue bar chart
            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Ingresos S/',
                        data: revenueValues,
                        backgroundColor: 'rgba(34,197,94,0.7)',
                        borderRadius: 6,
                    }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });

            // Status donut
            new Chart(document.getElementById('donutChart'), {
                type: 'doughnut',
                data: {
                    labels: donutLabels,
                    datasets: [{
                        data: donutValues,
                        backgroundColor: ['#FBBF24', '#3B82F6', '#8B5CF6', '#22C55E', '#EF4444', '#9CA3AF'],
                        borderWidth: 2,
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
            });
        </script>
    @endpush
</x-app-layout>