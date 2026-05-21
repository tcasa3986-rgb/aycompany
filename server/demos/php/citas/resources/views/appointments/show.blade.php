<x-app-layout>
    <x-slot name="header">Detalle de Cita #{{ $appointment->id }}</x-slot>

    @php
        $colors = ['pending' => 'yellow', 'confirmed' => 'blue', 'in_progress' => 'purple', 'completed' => 'green', 'cancelled' => 'red', 'no_show' => 'gray'];
        $c = $colors[$appointment->status] ?? 'gray';
        $transitions = [
            'pending' => ['confirmed' => 'Confirmar', 'cancelled' => 'Cancelar'],
            'confirmed' => ['in_progress' => 'Iniciar Atención', 'cancelled' => 'Cancelar', 'no_show' => 'No Asistió'],
            'in_progress' => ['completed' => 'Completar'],
            'completed' => [],
            'cancelled' => [],
            'no_show' => [],
        ];
        $actions = $transitions[$appointment->status] ?? [];
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">

        @if(session('waitlist_alert'))
            <div
                class="bg-indigo-50 border border-indigo-200 text-indigo-800 rounded-2xl p-4 flex items-start gap-3 shadow-sm">
                <div class="mt-0.5" style="min-width: 20px;">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-sm">¡Atención! Pacientes en Espera</h4>
                    <p class="text-sm mt-1">{{ session('waitlist_alert') }}</p>
                    <a href="{{ route('waitlists.index', ['doctor_id' => $appointment->doctor_id, 'status' => 'waiting']) }}"
                        class="inline-block mt-2 text-sm font-medium text-indigo-700 hover:text-indigo-900 hover:underline">
                        Ver Lista de Espera &rarr;
                    </a>
                </div>
            </div>
        @endif

        {{-- Header card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <span
                        class="px-3 py-1 bg-{{ $c }}-50 text-{{ $c }}-600 rounded-full text-xs font-semibold">{{ $appointment->status_label }}</span>
                    <h2 class="text-xl font-bold text-gray-900 mt-2">
                        {{ \Carbon\Carbon::parse($appointment->date)->format('d \d\e F, Y') }}
                    </h2>
                    <p class="text-gray-500 text-sm">{{ \Carbon\Carbon::parse($appointment->date)->format('H:i') }} hrs
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if(in_array($appointment->status, ['pending', 'confirmed']))
                        <a href="{{ route('appointments.reschedule', $appointment) }}"
                            class="px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-xs font-medium hover:bg-amber-100 transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Reagendar
                        </a>
                    @endif
                    <a href="{{ route('appointments.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">←
                        Volver</a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-400 text-xs mb-1">Paciente</p>
                        <a href="{{ route('patients.show', $appointment->patient) }}"
                            class="font-semibold text-blue-600 hover:underline">
                            {{ $appointment->patient->user->name }}
                        </a>
                        <p class="text-gray-400 text-xs">{{ $appointment->patient->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-1">Médico</p>
                        <a href="{{ route('doctors.show', $appointment->doctor) }}"
                            class="font-semibold text-indigo-600 hover:underline">
                            Dr. {{ $appointment->doctor->user->name }}
                        </a>
                        <p class="text-gray-400 text-xs">{{ $appointment->specialty->name }}</p>
                        @if($appointment->office)
                            <div class="mt-2 flex items-start gap-1.5 text-gray-500">
                                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium">{{ $appointment->office->name }}</p>
                                    @if($appointment->office->address)
                                        <p class="text-xs">{{ $appointment->office->address }}
                                            {{ $appointment->office->floor ? '(' . $appointment->office->floor . ')' : '' }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-orange-400 mt-1">Sin consultorio asignado</p>
                        @endif
                    </div>
                </div>
                <div class="space-y-4">
                    @if($appointment->notes)
                        <div>
                            <p class="text-gray-400 text-xs mb-1">Motivo de consulta</p>
                            <p class="text-gray-700">{{ $appointment->notes }}</p>
                        </div>
                    @endif
                    @if($appointment->cancellation_reason)
                        <div>
                            <p class="text-gray-400 text-xs mb-1">Motivo de cancelación</p>
                            <p class="text-red-600">{{ $appointment->cancellation_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Status actions --}}
        @if(count($actions))
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Cambiar Estado</h3>
                <div class="flex flex-wrap items-center gap-3">
                    @foreach($actions as $status => $label)
                            <form method="POST" action="{{ route('appointments.updateStatus', $appointment) }}"
                                id="form-{{ $status }}"
                                onsubmit="{{ in_array($status, ['cancelled', 'no_show']) ? 'return confirmCancel(event, this)' : '' }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $status }}">
                                <input type="hidden" name="cancellation_reason" id="reason-{{ $status }}" value="">
                                <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium transition
                                                                                {{ $status === 'completed' ? 'bg-green-500 text-white hover:bg-green-600' :
                        ($status === 'cancelled' || $status === 'no_show' ? 'bg-red-50 text-red-600 hover:bg-red-100' :
                            'bg-blue-500 text-white hover:bg-blue-600') }}">
                                    {{ $label }}
                                </button>
                            </form>
                    @endforeach

                    @if(in_array($appointment->status, ['in_progress', 'completed']))
                        @php $record = \App\Models\MedicalRecord::where('appointment_id', $appointment->id)->first(); @endphp

                        <div class="h-6 w-px bg-gray-200 mx-2 hidden sm:block"></div>

                        @if($record)
                            <a href="{{ route('medical-records.show', $record) }}"
                                class="px-5 py-2 rounded-lg text-sm font-medium bg-purple-50 text-purple-600 hover:bg-purple-100 transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Ver Historia Clínica
                            </a>
                        @else
                            <a href="{{ route('medical-records.create', ['appointment_id' => $appointment->id, 'patient_id' => $appointment->patient_id]) }}"
                                class="px-5 py-2 rounded-lg text-sm font-medium bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Escribir Historia Clínica
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script>
        function confirmCancel(event, form) {
            event.preventDefault();
            const reason = prompt('Indique el motivo de cancelación:');
            if (reason === null) return false;
            const status = form.querySelector('[name="status"]').value;
            form.querySelector(`[id^="reason-${status}"]`).value = reason;
            form.submit();
        }
    </script>
</x-app-layout>