<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('reports.index') }}" class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Reporte de Pacientes</h1>
                <p class="text-sm text-gray-500 mt-1">Distribución y evolución del padrón de pacientes</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- KPIs --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">Total Pacientes</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalPatients) }}</p>
            </div>
            <div class="bg-blue-50 rounded-2xl border border-blue-100 shadow-sm p-5">
                <p class="text-xs text-blue-600 uppercase font-semibold tracking-wide">Nuevos este Mes</p>
                <p class="text-3xl font-bold text-blue-700 mt-1">{{ $newThisMonth }}</p>
            </div>
            <div class="bg-gray-50 rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Nuevos Mes Anterior</p>
                <p class="text-3xl font-bold text-gray-700 mt-1">{{ $newLastMonth }}</p>
            </div>
        </div>

        {{-- Charts row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- New patients per month --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Pacientes Nuevos por Mes (últimos 12 meses)</h2>
                <canvas id="newPatientsChart" height="110"></canvas>
            </div>

            {{-- Gender donut --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Distribución por Género</h2>
                <canvas id="genderChart"></canvas>
            </div>
        </div>

        {{-- Blood type --}}
        @if($byBloodType->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Distribución por Tipo de Sangre</h2>
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3">
                    @foreach($byBloodType as $type => $count)
                        <div class="text-center bg-red-50 border border-red-100 rounded-xl py-3 px-2">
                            <p class="text-lg font-bold text-red-700">{{ $type }}</p>
                            <p class="text-xs text-red-500 mt-0.5">{{ $count }} pax</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
        <script>
            const monthLabels = @json($monthLabels);
            const monthValues = @json($monthValues);
            const genderData = @json($genderData);

            new Chart(document.getElementById('newPatientsChart'), {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Nuevos pacientes',
                        data: monthValues,
                        backgroundColor: 'rgba(74,136,246,0.7)',
                        borderRadius: 6,
                    }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
            });

            new Chart(document.getElementById('genderChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Masculino', 'Femenino', 'Otro'],
                    datasets: [{
                        data: genderData,
                        backgroundColor: ['#3B82F6', '#EC4899', '#9CA3AF'],
                        borderWidth: 2,
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
            });
        </script>
    @endpush
</x-app-layout>