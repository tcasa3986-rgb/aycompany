<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>


    <div class="space-y-8">

        {{-- ══════════════════════════════════
        ROW 1 – KPI Cards (4 cols)
        ══════════════════════════════════ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Citas Hoy --}}
            <div
                class="relative bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex flex-col gap-4 overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div
                    class="absolute -right-6 -top-6 w-32 h-32 bg-gradient-to-br from-blue-50 to-blue-200 rounded-full opacity-40 group-hover:scale-125 transition-transform duration-700 ease-out z-0">
                </div>
                <div
                    class="absolute -left-6 -bottom-6 w-24 h-24 bg-gradient-to-tr from-blue-50 to-transparent rounded-full opacity-50 z-0">
                </div>
                <div class="flex items-center justify-between relative z-10">
                    <p class="text-xs font-bold text-gray-400 tracking-widest uppercase">Citas Hoy</p>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 transform group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="relative z-10 mt-2">
                    <p class="text-5xl font-black text-gray-900 tracking-tight">
                        {{ $todayAppointments }}
                    </p>
                </div>
            </div>

            {{-- Esta Semana --}}
            <div
                class="relative bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex flex-col gap-4 overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div
                    class="absolute -right-6 -top-6 w-32 h-32 bg-gradient-to-br from-indigo-50 to-indigo-200 rounded-full opacity-40 group-hover:scale-125 transition-transform duration-700 ease-out z-0">
                </div>
                <div
                    class="absolute -left-6 -bottom-6 w-24 h-24 bg-gradient-to-tr from-indigo-50 to-transparent rounded-full opacity-50 z-0">
                </div>
                <div class="flex items-center justify-between relative z-10">
                    <p class="text-xs font-bold text-gray-400 tracking-widest uppercase">Esta Semana</p>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 transform group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="relative z-10 mt-2">
                    <p class="text-5xl font-black text-gray-900 tracking-tight">
                        {{ $weekAppointments }}
                    </p>
                </div>
            </div>

            {{-- Total Pacientes --}}
            <div
                class="relative bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex flex-col gap-4 overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div
                    class="absolute -right-6 -top-6 w-32 h-32 bg-gradient-to-br from-emerald-50 to-emerald-200 rounded-full opacity-40 group-hover:scale-125 transition-transform duration-700 ease-out z-0">
                </div>
                <div
                    class="absolute -left-6 -bottom-6 w-24 h-24 bg-gradient-to-tr from-emerald-50 to-transparent rounded-full opacity-50 z-0">
                </div>
                <div class="flex items-center justify-between relative z-10">
                    <p class="text-xs font-bold text-gray-400 tracking-widest uppercase">Pacientes</p>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 transform group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <div class="relative z-10 mt-2">
                    <p class="text-5xl font-black text-gray-900 tracking-tight">{{ $totalPatients }}
                    </p>
                </div>
            </div>

            {{-- Total Médicos --}}
            <div
                class="relative bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex flex-col gap-4 overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div
                    class="absolute -right-6 -top-6 w-32 h-32 bg-gradient-to-br from-purple-50 to-purple-200 rounded-full opacity-40 group-hover:scale-125 transition-transform duration-700 ease-out z-0">
                </div>
                <div
                    class="absolute -left-6 -bottom-6 w-24 h-24 bg-gradient-to-tr from-purple-50 to-transparent rounded-full opacity-50 z-0">
                </div>
                <div class="flex items-center justify-between relative z-10">
                    <p class="text-xs font-bold text-gray-400 tracking-widest uppercase">Médicos</p>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-purple-500/30 transform group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0M12 2a10 10 0 100 20 10 10 0 000-20z" />
                        </svg>
                    </div>
                </div>
                <div class="relative z-10 mt-2">
                    <p class="text-5xl font-black text-gray-900 tracking-tight">{{ $totalDoctors }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════
        ROW 2 – Estado Badges
        ══════════════════════════════════ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Badges --}}
            <div
                class="bg-white rounded-2xl p-5 flex items-center gap-4 relative overflow-hidden shadow-[0_4px_20px_rgb(0,0,0,0.03)] border-l-4 border-l-yellow-400 border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-2xl font-extrabold text-gray-900 leading-none">
                        {{ $pendingAppointments }}
                    </h4>
                    <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wider">Pendientes</p>
                </div>
            </div>
            <div
                class="bg-white rounded-2xl p-5 flex items-center gap-4 relative overflow-hidden shadow-[0_4px_20px_rgb(0,0,0,0.03)] border-l-4 border-l-blue-400 border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-2xl font-extrabold text-gray-900 leading-none">
                        {{ $confirmedAppointments }}
                    </h4>
                    <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wider">Confirmadas</p>
                </div>
            </div>
            <div
                class="bg-white rounded-2xl p-5 flex items-center gap-4 relative overflow-hidden shadow-[0_4px_20px_rgb(0,0,0,0.03)] border-l-4 border-l-emerald-400 border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-2xl font-extrabold text-gray-900 leading-none">
                        {{ $completedThisMonth }}
                    </h4>
                    <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wider">Atendidas</p>
                </div>
            </div>
            <div
                class="bg-white rounded-2xl p-5 flex items-center gap-4 relative overflow-hidden shadow-[0_4px_20px_rgb(0,0,0,0.03)] border-l-4 border-l-rose-400 border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-2xl font-extrabold text-gray-900 leading-none">{{ $cancelledThisMonth }}
                    </h4>
                    <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wider">Canceladas</p>
                </div>
            </div>
        </div>
        {{-- ══════════════════════════════════
        ROW 3 – Charts (2 cols)
        ══════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- Bar Chart: Citas próximos 7 días --}}
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Actividad de Citas</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Volumen proyectado vs real (7 días)
                        </p>
                    </div>
                </div>
                <div id="chart-bar" class="h-64 w-full"></div>
            </div>

            {{-- Donut Chart: Por Estado --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Estado General</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Distribución de todas las citas</p>
                </div>
                @if(count($donutValues) > 0)
                    <div id="chart-donut" class="h-64 flex items-center justify-center w-full"></div>
                @else
                    <div class="h-64 flex items-center justify-center">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 mx-auto bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3 transition-colors">
                                <svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400 dark:text-gray-500 font-medium">No hay suficientes datos</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════
        ROW 4 – Upcoming & Top Doctors
        ══════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Próximas Citas --}}
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors">
                <div
                    class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50 transition-colors">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Agenda Próxima</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Pacientes agendados próximamente</p>
                    </div>
                    <a href="{{ route('appointments.index') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
                        Ver Calendario
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
                <div class="p-2">
                    @if($upcomingAppointments->isEmpty())
                        <div class="py-12 text-center">
                            <div
                                class="w-16 h-16 mx-auto bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 transition-colors">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-gray-900 dark:text-white font-medium">Agenda libre</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No hay citas programadas para los
                                próximos días.</p>
                        </div>
                    @else
                        <div class="space-y-1">
                            @foreach($upcomingAppointments as $appt)
                                @php
                                    $statusStyles = [
                                        'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/40 dark:text-yellow-400 dark:border-yellow-900/50',
                                        'confirmed' => 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/40 dark:text-blue-400 dark:border-blue-900/50',
                                        'completed' => 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/40 dark:text-green-400 dark:border-green-900/50',
                                        'in_progress' => 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-400 dark:border-emerald-900/50',
                                    ];
                                    $c = $statusStyles[$appt->status] ?? 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700';
                                @endphp
                                <div
                                    class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                    <div
                                        class="w-14 h-14 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-100 dark:border-gray-600 flex flex-col items-center justify-center flex-shrink-0 group-hover:bg-white dark:group-hover:bg-gray-600 group-hover:border-blue-100 dark:group-hover:border-blue-500/30 transition-colors">
                                        <span
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase leading-none">{{ \Carbon\Carbon::parse($appt->date)->translatedFormat('M') }}</span>
                                        <span
                                            class="text-xl font-bold text-gray-900 dark:text-white leading-tight mt-0.5">{{ \Carbon\Carbon::parse($appt->date)->format('d') }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="font-bold text-gray-900 dark:text-white text-base truncate">
                                                {{ $appt->patient->user->name }}
                                            </p>
                                            <span
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ \Carbon\Carbon::parse($appt->date)->format('H:i') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1.5">
                                            <div
                                                class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 truncate">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                                <span class="truncate">Dr. {{ $appt->doctor->user->name }}</span>
                                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                                <span class="truncate">{{ $appt->specialty->name }}</span>
                                            </div>
                                            <span
                                                class="px-2.5 py-1 text-xs font-semibold rounded-md border {{ $c }}">{{ $appt->status_label }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Top Médicos del Mes --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors">
                <div
                    class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 transition-colors">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Rendimiento Médico</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Top 5 médicos con más citas (Mes actual)
                    </p>
                </div>
                <div class="p-6">
                    @if($topDoctors->isEmpty())
                        <div class="py-8 text-center">
                            <div
                                class="w-16 h-16 mx-auto bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 transition-colors">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Sin datos suficientes este mes.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($topDoctors as $i => $doctor)
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <img src="{{ $doctor->user->profile_photo_url }}" alt="{{ $doctor->user->name }}"
                                            class="w-12 h-12 rounded-full object-cover border-2 border-white dark:border-gray-800 shadow-sm transition-colors">
                                        <span
                                            class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold border-2 border-white dark:border-gray-800 transition-colors
                                                                                        {{ $i === 0 ? 'bg-yellow-400 text-yellow-900' : ($i === 1 ? 'bg-gray-300 text-gray-800' : ($i === 2 ? 'bg-orange-300 text-orange-900' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400')) }}">
                                            {{ $i + 1 }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">Dr.
                                                {{ $doctor->user->name }}
                                            </p>
                                            <span
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $doctor->month_count }}
                                                <span
                                                    class="text-xs font-normal text-gray-400 dark:text-gray-500">citas</span></span>
                                        </div>
                                        @php $max = $topDoctors->first()->month_count ?: 1; @endphp
                                        <div
                                            class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden transition-colors">
                                            <div class="h-2 rounded-full {{ $i === 0 ? 'bg-gradient-to-r from-yellow-400 to-yellow-500' : 'bg-gradient-to-r from-blue-400 to-indigo-500' }}"
                                                style="width: {{ ($doctor->month_count / $max) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ApexCharts Scripts --}}
    <script data-navigate-once>
        document.addEventListener('livewire:navigated', () => {
            const renderCharts = () => {
                if (typeof window.ApexCharts === 'undefined') {
                    setTimeout(renderCharts, 50);
                    return;
                }

                const barEl = document.querySelector("#chart-bar");
                if (barEl) {
                    barEl.innerHTML = '';
                    var barOptions = {
                        series: [{
                            name: 'Citas Programadas',
                            data: @json($chartValues)
                        }],
                        chart: {
                            type: 'bar',
                            height: 256,
                            toolbar: { show: false },
                            fontFamily: 'inherit',
                            animations: { enabled: false }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 6,
                                columnWidth: '45%',
                                dataLabels: { position: 'top' },
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function (val) { return val; },
                            offsetY: -20,
                            style: { fontSize: '12px', colors: ['#6b7280'], fontWeight: 600 }
                        },
                        xaxis: {
                            categories: @json($chartDays),
                            position: 'bottom',
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                            labels: { style: { colors: '#9ca3af', fontSize: '13px', fontWeight: 500 } },
                            crosshairs: {
                                fill: { type: 'gradient', gradient: { colorFrom: '#D8E3F0', colorTo: '#BED1E6', stops: [0, 100], opacityFrom: 0.4, opacityTo: 0.5 } }
                            },
                            tooltip: { enabled: true }
                        },
                        yaxis: {
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                            labels: { show: false },
                            min: 0,
                            forceNiceScale: true
                        },
                        grid: {
                            show: true,
                            borderColor: '#f3f4f6',
                            strokeDashArray: 4,
                            xaxis: { lines: { show: false } },
                            yaxis: { lines: { show: true } }
                        },
                        colors: ['#4f46e5'],
                        tooltip: { theme: 'light', y: { formatter: function (val) { return val + ' citas' } } }
                    };
                    new window.ApexCharts(barEl, barOptions).render();
                }

                @if(count($donutValues) > 0)
                    const donutEl = document.querySelector("#chart-donut");
                    if (donutEl) {
                        donutEl.innerHTML = '';
                        var donutOptions = {
                            series: @json($donutValues),
                            labels: @json($donutLabels),
                            chart: { type: 'donut', height: 256, fontFamily: 'inherit', animations: { enabled: false } },
                            colors: ['#f59e0b', '#3b82f6', '#10b981', '#6366f1', '#ef4444', '#9ca3af'],
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '75%',
                                        labels: {
                                            show: true,
                                            name: { show: true, fontSize: '14px', fontFamily: 'inherit', fontWeight: 600, color: '#6b7280', offsetY: -10 },
                                            value: { show: true, fontSize: '32px', fontFamily: 'inherit', fontWeight: 800, color: '#111827', offsetY: 10, formatter: function (val) { return val } },
                                            total: { show: true, showAlways: true, label: 'Total', fontSize: '14px', fontWeight: 600, color: '#6b7280', formatter: function (w) { return w.globals.seriesTotals.reduce((a, b) => { return a + b }, 0) } }
                                        }
                                    }
                                }
                            },
                            dataLabels: { enabled: false },
                            legend: { show: true, position: 'bottom', horizontalAlign: 'center', fontSize: '13px', markers: { width: 10, height: 10, radius: 5 }, itemMargin: { horizontal: 10, vertical: 5 } },
                            stroke: { show: true, colors: ['#fff'], width: 3 },
                            tooltip: { theme: 'light', fillSeriesColor: false }
                        };
                        new window.ApexCharts(donutEl, donutOptions).render();
                    }
                @endif
            };

            renderCharts();
        });
    </script>
</x-app-layout>