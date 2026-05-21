<x-app-layout>
    <x-slot name="header">Mi Portal Médico</x-slot>

    <div class="max-w-7xl mx-auto space-y-8 pb-10">
        {{-- Welcome Banner --}}
        <div class="rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700"></div>
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] [background-size:24px_24px] opacity-50 mix-blend-overlay">
            </div>
            <div
                class="absolute -right-32 -top-32 w-96 h-96 bg-white/20 rounded-full blur-[80px] group-hover:bg-white/30 transition-all duration-700 ease-in-out">
            </div>

            <div
                class="relative p-8 md:p-12 lg:p-16 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 z-10">
                <div class="max-w-2xl">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 tracking-tight">
                        ¡Hola, {{ auth()->user()->name }}!
                    </h1>
                    <p class="text-blue-100/90 text-lg mb-8 max-w-xl leading-relaxed font-medium">
                        Tu bienestar es nuestra prioridad. Desde aquí puedes gestionar tus consultas, revisar tu
                        evolución médica y administrar tus pagos con total seguridad.
                    </p>
                    <a href="{{ route('portal.appointments.create') }}"
                        class="inline-flex items-center gap-3 bg-white text-blue-600 px-8 py-4 rounded-2xl font-bold hover:bg-blue-50 transition-all duration-300 shadow-[0_8px_20px_rgb(0,0,0,0.1)] hover:shadow-[0_8px_25px_rgb(0,0,0,0.15)] hover:-translate-y-0.5 group/btn">
                        <svg class="w-5 h-5 text-blue-500 group-hover/btn:scale-110 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Agendar Nueva Cita
                    </a>
                </div>
                <div class="hidden lg:block shrink-0">
                    <div
                        class="w-40 h-40 bg-white/10 backdrop-blur-md rounded-[2.5rem] border border-white/20 flex items-center justify-center shadow-2xl rotate-3 hover:rotate-6 transition-transform duration-500">
                        <svg class="w-20 h-20 text-white/90 drop-shadow-lg" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Próximas Citas (Columna Izquierda 2x) --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Tus Próximas Citas</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Consultas agendadas para los próximos días</p>
                    </div>
                    <a href="{{ route('portal.appointments') }}"
                        class="text-sm font-bold text-blue-600 hover:text-blue-700 bg-blue-50/50 hover:bg-blue-50 px-4 py-2 rounded-xl transition-colors">
                        Ver Calendario &rarr;
                    </a>
                </div>

                @if($upcomingAppointments->isEmpty())
                    <div
                        class="bg-white border border-gray-100 rounded-3xl p-12 text-center shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)]">
                        <div
                            class="w-20 h-20 bg-gray-50/80 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-gray-100/50">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Agenda Libre</h3>
                        <p class="text-gray-500">No tienes citas médicas programadas próximamente.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($upcomingAppointments as $appt)
                            @php
                                $statusStyles = [
                                    'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-200/50',
                                    'confirmed' => 'bg-blue-50 text-blue-700 ring-blue-200/50',
                                    'completed' => 'bg-green-50 text-green-700 ring-green-200/50',
                                    'in_progress' => 'bg-emerald-50 text-emerald-700 ring-emerald-200/50',
                                ];
                                $c = $statusStyles[$appt->status] ?? 'bg-gray-50 text-gray-700 ring-gray-200/50';
                            @endphp
                            <div
                                class="bg-white rounded-[1.5rem] p-6 border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.02)] hover:shadow-[0_8px_25px_-5px_rgba(6,81,237,0.08)] hover:border-blue-100 transition-all duration-300 group">
                                <div class="flex justify-between items-start mb-5">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50/50 text-blue-600 flex flex-col items-center justify-center shadow-inner border border-blue-100/30 group-hover:scale-105 transition-transform">
                                            <span
                                                class="text-xl font-extrabold leading-none tracking-tight">{{ \Carbon\Carbon::parse($appt->date)->format('d') }}</span>
                                            <span
                                                class="text-[11px] font-bold uppercase tracking-wider text-blue-500 mt-0.5">{{ \Carbon\Carbon::parse($appt->date)->translatedFormat('M') }}</span>
                                        </div>
                                        <div>
                                            <p class="text-[15px] font-bold text-gray-900 whitespace-nowrap mb-1">
                                                {{ \Carbon\Carbon::parse($appt->date)->format('H:i') }} hrs
                                            </p>
                                            <span
                                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold ring-1 ring-inset {{ $c }}">
                                                {{ $appt->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                    <button
                                        class="p-2 text-gray-300 hover:bg-red-50 hover:text-red-500 rounded-xl transition-colors shrink-0"
                                        title="Cancelar Cita">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="pt-4 border-t border-gray-50">
                                    <p class="font-bold text-gray-900 text-[15px] mb-0.5">Dr. {{ $appt->doctor->user->name }}
                                    </p>
                                    <p class="text-sm font-medium text-blue-600 mb-2.5">{{ $appt->specialty->name }}</p>
                                    @if($appt->office)
                                        <div class="flex items-center gap-1.5 text-sm text-gray-500">
                                            <div class="w-6 h-6 rounded-md bg-gray-50 flex items-center justify-center shrink-0">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <span class="truncate font-medium">{{ $appt->office->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Historial Resumido (Columna Derecha) --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Historial Reciente</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Últimas consultas médicas</p>
                    </div>
                    <a href="{{ route('portal.medical-history') }}"
                        class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div
                    class="bg-white rounded-3xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] border border-gray-100 overflow-hidden">
                    @if($recentHistory->isEmpty())
                        <div class="p-10 text-center">
                            <div class="w-12 h-12 bg-gray-50 rounded-xl mx-auto flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-[15px] font-medium text-gray-900">Historial Vacío</p>
                            <p class="text-sm text-gray-500 mt-1">Tus consultas finalizadas aparecerán aquí.</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-50">
                            @foreach($recentHistory as $rec)
                                <a href="{{ route('portal.medical-history') }}"
                                    class="block p-5 hover:bg-blue-50/30 transition-colors group">
                                    <div class="flex justify-between items-start mb-2">
                                        <p
                                            class="font-bold text-[15px] text-gray-900 group-hover:text-blue-700 transition-colors line-clamp-1">
                                            {{ $rec->diagnosis ?: 'Consulta de Seguimiento' }}
                                        </p>
                                        <span
                                            class="text-xs font-semibold text-gray-400 shrink-0 bg-gray-50 px-2 py-1 rounded-md">{{ $rec->record_date->format('d/m/y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                            <span
                                                class="text-[10px] font-bold text-blue-700">{{ substr($rec->doctor->user->name, 0, 1) }}</span>
                                        </div>
                                        <p class="text-sm font-medium text-gray-600">Dr. {{ $rec->doctor->user->name }}</p>
                                    </div>
                                    @if($rec->prescriptions)
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1.5 rounded-lg border border-amber-100/50">
                                            <svg class="w-3.5 h-3.5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="tracking-wide">RECETA MÉDICA INCLUIDA</span>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>