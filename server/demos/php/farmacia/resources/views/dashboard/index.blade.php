@extends('layouts.app')

@section('title', 'Dashboard')
@section('section', 'Panel principal')

@section('content')
    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- KPI 1: Productos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-tr from-blue-100 to-blue-50 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700 ease-out"></div>
            <div class="w-14 h-14 rounded-full bg-blue-500 flex items-center justify-center text-white flex-shrink-0 z-10 shadow-lg shadow-blue-200">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
            </div>
            <div class="z-10">
                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Productos en stock</p>
                <p class="text-2xl font-bold text-gray-700 mt-1">{{ number_format($totalProductos) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Catálogo total</p>
            </div>
        </div>

        {{-- KPI 2: Ventas Hoy --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-tr from-emerald-100 to-emerald-50 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700 ease-out"></div>
            <div class="w-14 h-14 rounded-full bg-emerald-500 flex items-center justify-center text-white flex-shrink-0 z-10 shadow-lg shadow-emerald-200">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="z-10">
                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Ventas hoy</p>
                <p class="text-2xl font-bold text-gray-700 mt-1">S/ {{ number_format($ventasHoy, 2) }}</p>
                <p class="text-xs text-emerald-500/80 font-medium mt-0.5">Total del día</p>
            </div>
        </div>

        {{-- KPI 3: Ventas Mes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-tr from-purple-100 to-purple-50 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700 ease-out"></div>
            <div class="w-14 h-14 rounded-full bg-purple-500 flex items-center justify-center text-white flex-shrink-0 z-10 shadow-lg shadow-purple-200">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
            </div>
            <div class="z-10">
                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Ventas del mes</p>
                <p class="text-2xl font-bold text-gray-700 mt-1">S/ {{ number_format($ventasMes, 2) }}</p>
                <p class="text-xs text-gray-400 mt-0.5 capitalize">{{ now()->locale('es')->isoFormat('MMMM Y') }}</p>
            </div>
        </div>

        {{-- KPI 4: Clientes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-tr from-amber-100 to-amber-50 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700 ease-out"></div>
            <div class="w-14 h-14 rounded-full bg-amber-500 flex items-center justify-center text-white flex-shrink-0 z-10 shadow-lg shadow-amber-200">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
            <div class="z-10">
                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Clientes</p>
                <p class="text-2xl font-bold text-gray-700 mt-1">{{ number_format($totalClientes) }}</p>
                @if($stockBajo > 0)
                <p class="text-xs text-rose-500/80 font-medium mt-0.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    {{ $stockBajo }} producto(s) bajo stock
                </p>
                @else
                <p class="text-xs text-gray-400 mt-0.5">Registrados</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Gráfico 1: Evolución de Ventas --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700">Evolución de Ventas</h3>
                </div>
                <span class="text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-500 rounded-full">12 meses (S/)</span>
            </div>
            <div class="h-72 w-full">
                <canvas id="chartVentas"></canvas>
            </div>
        </div>

        {{-- Gráfico 2: Compras vs Ventas --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700">Ingresos vs Gastos</h3>
                </div>
                <span class="text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-500 rounded-full">6 meses (S/)</span>
            </div>
            <div class="h-72 w-full">
                <canvas id="chartComprasVentas"></canvas>
            </div>
        </div>

        {{-- Gráfico 3: Ventas por Categoría --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" /></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700">Ventas por Categoría</h3>
                </div>
                <span class="text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-500 rounded-full">Top 5</span>
            </div>
            <div class="h-72 w-full relative">
                <canvas id="chartCategorias"></canvas>
            </div>
        </div>

        {{-- Gráfico 4: Top Productos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-sky-50 rounded-lg text-sky-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700">Top Productos</h3>
                </div>
                <span class="text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-500 rounded-full">Unidades</span>
            </div>
            <div class="h-72 w-full">
                <canvas id="chartTop"></canvas>
            </div>
        </div>
    </div>

    {{-- Bottom row: vencimientos + calendario --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-2 flex flex-col transition-shadow hover:shadow-md">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-white">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-rose-50 rounded-lg text-rose-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-700">Próximos a vencer</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Lotes que caducan en los próximos 90 días</p>
                    </div>
                </div>
                <a href="{{ route('productos.index') }}" class="text-sm font-semibold text-farmacia-600 hover:text-farmacia-700 hover:underline px-3 py-1.5 rounded-lg hover:bg-farmacia-50 transition-colors">
                    Ver inventario &rarr;
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Producto</th>
                            <th class="px-6 py-4 font-semibold">Lote</th>
                            <th class="px-6 py-4 font-semibold">Vencimiento</th>
                            <th class="px-6 py-4 font-semibold text-right">Cant.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($proximosVencer as $row)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 mr-3">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                        </div>
                                        <span class="font-semibold text-gray-800">{{ $row->nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-medium">#{{ $row->numero_lote }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-500/20">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                        {{ \Carbon\Carbon::parse($row->fecha_vencimiento)->locale('es')->isoFormat('DD MMM YYYY') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-gray-700">{{ $row->cantidad }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span class="font-medium text-gray-500">Todo en orden</span>
                                        <span class="text-xs mt-1">No hay productos próximos a vencer.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col transition-shadow hover:shadow-md">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-slate-50 rounded-lg text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Calendario</h3>
            </div>
            
            @php
                $hoy   = now();
                $first = $hoy->copy()->startOfMonth();
                $start = $first->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                $end   = $hoy->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
            @endphp
            
            <div class="flex-1 bg-gray-50/50 rounded-xl p-4 border border-gray-100">
                <div class="flex items-center justify-center mb-4 pb-3 border-b border-gray-200">
                    <p class="text-base text-gray-800 font-bold capitalize">{{ $hoy->locale('es')->isoFormat('MMMM YYYY') }}</p>
                </div>
                <div class="grid grid-cols-7 text-center text-xs text-gray-400 font-bold mb-3">
                    @foreach (['DO', 'LU', 'MA', 'MI', 'JU', 'VI', 'SA'] as $d)
                        <div>{{ $d }}</div>
                    @endforeach
                </div>
                <div class="grid grid-cols-7 text-center text-sm gap-y-3 gap-x-1">
                    @php $cur = $start->copy(); @endphp
                    @while ($cur <= $end)
                        @php
                            $isToday = $cur->isSameDay($hoy);
                            $isThisMonth = $cur->month === $hoy->month;
                        @endphp
                        <div class="flex justify-center items-center">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full transition-all duration-300
                                {{ $isToday ? 'bg-gradient-to-br from-farmacia-400 to-farmacia-600 text-white font-bold shadow-md shadow-farmacia-200 scale-110' : '' }}
                                {{ ! $isToday && $isThisMonth ? 'text-gray-700 hover:bg-white hover:shadow-sm hover:text-farmacia-600 cursor-default' : '' }}
                                {{ ! $isThisMonth ? 'text-gray-300' : '' }}">
                                {{ $cur->day }}
                            </div>
                        </div>
                        @php $cur->addDay(); @endphp
                    @endwhile
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Gráfico de Evolución de Ventas (Línea) ---
        const labelsVentas = @json($serieVentas->pluck('mes'));
        const dataVentas   = @json($serieVentas->pluck('monto'));

        const ctxVentas = document.getElementById('chartVentas');
        if (ctxVentas) {
            const grad = ctxVentas.getContext('2d').createLinearGradient(0, 0, 0, 300);
            grad.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); // emerald-500
            grad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            new Chart(ctxVentas, {
                type: 'line',
                data: {
                    labels: labelsVentas.map(l => l.substring(0, 3).toUpperCase()),
                    datasets: [{
                        label: 'Ventas',
                        data: dataVentas,
                        borderColor: '#10b981', // emerald-500
                        borderWidth: 3,
                        backgroundColor: grad,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#059669', // emerald-600
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            titleFont: { size: 13, family: "'Figtree', sans-serif" },
                            bodyFont: { size: 14, weight: 'bold', family: "'Figtree', sans-serif" },
                            displayColors: false,
                            callbacks: {
                                label: function(context) { return 'S/ ' + context.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2}); }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { color: '#9ca3af', font: { family: "'Figtree', sans-serif", size: 11 }, callback: function(value) { return 'S/ ' + value; } }, border: { display: false }, grid: { color: '#f3f4f6', drawTicks: false } },
                        x: { ticks: { color: '#9ca3af', font: { family: "'Figtree', sans-serif", size: 11 } }, border: { display: false }, grid: { display: false } }
                    },
                    interaction: { intersect: false, mode: 'index' },
                }
            });
        }

        // --- 2. Gráfico Compras vs Ventas (Barras Agrupadas) ---
        const cvData = @json($comprasVsVentas);
        const ctxCV = document.getElementById('chartComprasVentas');
        if (ctxCV) {
            new Chart(ctxCV, {
                type: 'bar',
                data: {
                    labels: cvData.map(d => d.mes.substring(0, 3).toUpperCase()),
                    datasets: [
                        {
                            label: 'Ingresos (Ventas)',
                            data: cvData.map(d => d.ventas),
                            backgroundColor: '#10b981', // emerald-500
                            borderRadius: 4,
                        },
                        {
                            label: 'Gastos (Compras)',
                            data: cvData.map(d => d.compras),
                            backgroundColor: '#f43f5e', // rose-500
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, font: { family: "'Figtree', sans-serif", size: 12 } } },
                        tooltip: {
                            backgroundColor: '#1f2937', padding: 12,
                            titleFont: { size: 13, family: "'Figtree', sans-serif" }, bodyFont: { size: 14, weight: 'bold', family: "'Figtree', sans-serif" },
                            callbacks: { label: function(context) { return context.dataset.label + ': S/ ' + context.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2}); } }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { color: '#9ca3af', font: { family: "'Figtree', sans-serif", size: 11 }, callback: function(value) { return 'S/ ' + value; } }, border: { display: false }, grid: { color: '#f3f4f6', drawTicks: false } },
                        x: { ticks: { color: '#9ca3af', font: { family: "'Figtree', sans-serif", size: 11 } }, border: { display: false }, grid: { display: false } }
                    },
                    interaction: { intersect: false, mode: 'index' },
                }
            });
        }

        // --- 3. Gráfico Ventas por Categoría (Doughnut) ---
        const catData = @json($ventasPorCategoria);
        const ctxCat = document.getElementById('chartCategorias');
        if (ctxCat && catData.length > 0) {
            new Chart(ctxCat, {
                type: 'doughnut',
                data: {
                    labels: catData.map(c => c.nombre),
                    datasets: [{
                        data: catData.map(c => c.total),
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'], // blue, emerald, amber, purple, pink
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: { 
                        legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, padding: 20, font: { family: "'Figtree', sans-serif", size: 12 } } },
                        tooltip: {
                            backgroundColor: '#1f2937', padding: 12,
                            titleFont: { size: 13, family: "'Figtree', sans-serif" }, bodyFont: { size: 14, weight: 'bold', family: "'Figtree', sans-serif" },
                            callbacks: { label: function(context) { return ' ' + context.label + ': S/ ' + context.parsed.toLocaleString('es-PE', {minimumFractionDigits: 2}); } }
                        }
                    }
                }
            });
        } else if (ctxCat) {
            // Placeholder si no hay data
            ctxCat.parentElement.innerHTML = '<div class="flex h-full items-center justify-center text-gray-400 text-sm">No hay suficientes datos de categorías</div>';
        }

        // --- 4. Gráfico Top Productos (Barras Horizontales) ---
        const top = @json($topProductos);
        const ctxTop = document.getElementById('chartTop');
        if (ctxTop) {
            new Chart(ctxTop, {
                type: 'bar',
                data: {
                    labels: top.map(t => t.nombre.length > 15 ? t.nombre.substring(0, 15) + '...' : t.nombre),
                    datasets: [{
                        label: 'Unidades Vendidas',
                        data: top.map(t => t.total),
                        backgroundColor: [
                            '#0284c7', // sky-600
                            '#0369a1', // sky-700
                            '#0ea5e9', // sky-500
                            '#38bdf8', // sky-400
                            '#7dd3fc', // sky-300
                            '#bae6fd'  // sky-200
                        ],
                        borderRadius: 6,
                        barThickness: 16
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937', padding: 12, displayColors: false,
                            titleFont: { size: 13, family: "'Figtree', sans-serif" }, bodyFont: { size: 14, weight: 'bold', family: "'Figtree', sans-serif" }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, ticks: { color: '#9ca3af', font: { family: "'Figtree', sans-serif", size: 11 } }, border: { display: false }, grid: { color: '#f3f4f6', drawTicks: false } },
                        y: { ticks: { color: '#6b7280', font: { family: "'Figtree', sans-serif", size: 11, weight: '600' } }, border: { display: false }, grid: { display: false } }
                    }
                }
            });
        }
    });
</script>
@endpush
