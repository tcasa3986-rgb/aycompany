<x-app-layout>
    <x-slot name="header">Mis Citas</x-slot>

    <div class="max-w-5xl mx-auto space-y-8 pb-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 rounded-3xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] border border-gray-100">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Historial de Citas</h1>
                <p class="text-[15px] text-gray-500 mt-1 max-w-xl">
                    Revisa en un solo lugar todas tus citas médicas programadas y pasadas.
                </p>
            </div>
            <a href="{{ route('portal.appointments.create') }}"
                class="shrink-0 px-6 py-3 bg-blue-600 text-white rounded-2xl text-[15px] font-bold hover:bg-blue-700 transition-all shadow-[0_8px_20px_rgb(6,81,237,0.2)] hover:shadow-[0_8px_25px_rgb(6,81,237,0.3)] hover:-translate-y-0.5 flex items-center justify-center gap-2 group">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Agendar Cita
            </a>
        </div>

        <div>
            @if($appointments->isEmpty())
                <div class="bg-white rounded-3xl p-16 text-center shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] border border-gray-100">
                    <div
                        class="w-24 h-24 bg-gray-50/80 rounded-[2rem] flex items-center justify-center mx-auto mb-6 border border-gray-100/50">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 tracking-tight">Registro Vacío</h3>
                    <p class="text-[15px] text-gray-500 max-w-sm mx-auto">Aún no tienes citas registradas en tu historial. Cuando agendes una cita, aparecerá aquí.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($appointments as $appt)
                        <div
                            class="bg-white rounded-3xl p-6 sm:p-8 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:border-blue-100 transition-all duration-300 border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-6 group relative overflow-hidden">
                            
                            {{-- Decorative Background element for status --}}
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-gray-50 to-transparent opacity-50 rounded-bl-[100px] pointer-events-none"></div>

                            <div class="flex items-start gap-5 relative z-10">
                                @php
                                    $dateStyles = 'bg-blue-50 text-blue-600 border-blue-100/50';
                                    if ($appt->status === 'cancelled' || $appt->status === 'no_show') {
                                         $dateStyles = 'bg-red-50 text-red-500 border-red-100/50';
                                    } elseif ($appt->status === 'completed') {
                                         $dateStyles = 'bg-green-50 text-green-600 border-green-100/50';
                                    }
                                @endphp
                                <div
                                    class="w-16 h-16 shrink-0 rounded-[1.25rem] {{ $dateStyles }} border shadow-inner flex flex-col items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                    <span
                                        class="text-2xl font-extrabold leading-none tracking-tight">{{ \Carbon\Carbon::parse($appt->date)->format('d') }}</span>
                                    <span
                                        class="text-[11px] uppercase font-bold tracking-wider mt-0.5">{{ \Carbon\Carbon::parse($appt->date)->translatedFormat('M y') }}</span>
                                </div>
                                <div class="pt-1">
                                    <div class="flex flex-wrap items-center gap-3 mb-1.5">
                                        <h3 class="font-bold text-gray-900 text-lg tracking-tight">Dr. {{ $appt->doctor->user->name }}</h3>
                                        @php
                                            $colors = ['pending' => 'yellow', 'confirmed' => 'blue', 'in_progress' => 'purple', 'completed' => 'green', 'cancelled' => 'red', 'no_show' => 'gray'];
                                            $c = $colors[$appt->status] ?? 'gray';
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-{{ $c }}-50 text-{{ $c }}-700 ring-1 ring-inset ring-{{ $c }}-200/50">
                                            {{ $appt->status_label }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[15px] font-medium text-gray-500">
                                        <span class="flex items-center gap-1.5 opacity-90">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                             {{ \Carbon\Carbon::parse($appt->date)->format('H:i') }} hrs
                                        </span>
                                        <span class="text-gray-300 hidden sm:inline">&bull;</span>
                                        <span class="text-blue-600">{{ $appt->specialty->name }}</span>
                                    </div>
                                    
                                    @if($appt->office)
                                        <div class="text-sm font-medium text-gray-400 mt-2.5 flex items-center gap-1.5 bg-gray-50 w-fit px-2.5 py-1 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $appt->office->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3 w-full sm:w-auto mt-2 sm:mt-0 relative z-10">
                                @if(in_array($appt->status, ['pending', 'confirmed']))
                                    <button
                                        onclick="document.getElementById('cancel-modal-{{ $appt->id }}').classList.remove('hidden')"
                                        class="w-full sm:w-auto px-5 py-2.5 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors border border-red-100/50 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        Cancelar Cita
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Modal de Cancelación --}}
                        @if(in_array($appt->status, ['pending', 'confirmed']))
                            <div id="cancel-modal-{{ $appt->id }}"
                                class="fixed inset-0 z-[100] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
                                <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden transform transition-transform scale-100">
                                    <div class="p-8">
                                        <div class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mb-5">
                                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                             </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2 tracking-tight">Cancelar Cita Médica</h3>
                                        <p class="text-[15px] text-gray-500 mb-6 leading-relaxed">
                                            Estás a punto de cancelar tu cita del
                                            <strong class="text-gray-900">{{ \Carbon\Carbon::parse($appt->date)->format('d/m/Y H:i') }}</strong> con el
                                            <strong class="text-gray-900">Dr. {{ $appt->doctor->user->name }}</strong>. Esta acción no se puede deshacer.
                                        </p>

                                        <form method="POST" action="{{ route('portal.appointments.cancel', $appt) }}">
                                            @csrf
                                            <div class="mb-6">
                                                <label class="block text-sm font-bold text-gray-700 mb-2">Motivo de cancelación
                                                    <span class="text-red-500">*</span></label>
                                                <textarea name="cancellation_reason" required rows="3"
                                                    class="w-full rounded-2xl border-gray-200 px-5 py-4 text-[15px] focus:outline-none focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all shadow-sm bg-gray-50/50 hover:bg-gray-50 placeholder-gray-400"
                                                    placeholder="Breve descripción del motivo..."></textarea>
                                            </div>
                                            <div class="flex justify-end gap-3 pt-2">
                                                <button type="button"
                                                    onclick="document.getElementById('cancel-modal-{{ $appt->id }}').classList.add('hidden')"
                                                    class="px-5 py-3 text-[15px] font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors">Atrás</button>
                                                <button type="submit"
                                                    class="px-6 py-3 text-[15px] font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all shadow-[0_4px_10px_rgb(220,38,38,0.2)] hover:shadow-[0_4px_15px_rgb(220,38,38,0.3)] hover:-translate-y-0.5">Confirmar
                                                    Cancelación</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if($appointments->hasPages())
                    <div class="pt-8">
                        {{ $appointments->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>