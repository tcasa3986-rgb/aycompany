<x-app-layout>
    <x-slot name="header">Paciente: {{ $patient->user->name }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Info Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-5 mb-6">
                <div
                    class="w-16 h-16 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-2xl">
                    {{ strtoupper(substr($patient->user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $patient->user->name }}</h2>
                    <p class="text-gray-500 text-sm">{{ $patient->user->email }}</p>
                </div>
                <div class="ml-auto flex gap-2">
                    <a href="{{ route('patients.export-profile.pdf', $patient) }}" target="_blank"
                        class="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Exportar PDF
                    </a>
                    <a href="{{ route('medical-records.patient-history', $patient) }}"
                        class="px-4 py-2 bg-purple-50 text-purple-600 rounded-lg text-sm font-medium hover:bg-purple-100 transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Historia Clínica
                    </a>
                    <a href="{{ route('patients.edit', $patient) }}"
                        class="px-4 py-2 bg-yellow-50 text-yellow-600 rounded-lg text-sm font-medium hover:bg-yellow-100 transition">Editar</a>
                    <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">+
                        Agendar Cita</a>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs mb-1">Fecha de Nac.</p>
                    <p class="font-medium">
                        {{ $patient->dob ? \Carbon\Carbon::parse($patient->dob)->format('d/m/Y') : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Género</p>
                    <p class="font-medium">
                        {{ ['male' => 'Masculino', 'female' => 'Femenino', 'other' => 'Otro'][$patient->gender] ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Tipo de Sangre</p>
                    <p class="font-bold text-red-500">{{ $patient->blood_type ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Teléfono</p>
                    <p class="font-medium">{{ $patient->phone ?? '—' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-gray-400 text-xs mb-1">Dirección</p>
                    <p class="font-medium">{{ $patient->address ?? '—' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-gray-400 text-xs mb-1">Seguro Médico (ARS)</p>
                    <p class="font-medium text-indigo-700">
                        @if($patient->insurance)
                            {{ $patient->insurance->name }} ({{ $patient->policy_number ?? 'Sin póliza' }})
                        @else
                            <span class="text-gray-500 font-normal">Particular / Sin Seguro</span>
                        @endif
                    </p>
                </div>
                <div class="col-span-2">
                    <p class="text-gray-400 text-xs mb-1">Alergias</p>
                    <p class="font-medium text-orange-600">{{ $patient->allergies ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Primary Doctor Card --}}
        @if($patient->primaryDoctor)
            <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-5">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-3">Médico de Cabecera</p>
                <div class="flex items-center gap-4">
                    <img src="{{ $patient->primaryDoctor->user->avatar_url }}"
                        class="w-14 h-14 rounded-xl object-cover border border-blue-100"
                        alt="{{ $patient->primaryDoctor->user->name }}">
                    <div>
                        <p class="font-semibold text-gray-900">Dr. {{ $patient->primaryDoctor->user->name }}</p>
                        @if($patient->primaryDoctor->specialty)
                            <p class="text-sm text-blue-500">{{ $patient->primaryDoctor->specialty->name }}</p>
                        @endif
                        @if($patient->primaryDoctor->license_number)
                            <p class="text-xs text-gray-400">Colegiatura: {{ $patient->primaryDoctor->license_number }}</p>
                        @endif
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('appointments.create', ['patient_id' => $patient->id, 'doctor_id' => $patient->primaryDoctor->id]) }}"
                            class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-100 transition">
                            + Cita con Dr.
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Appointment History --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Historial de Citas</h3>
            @if($patient->appointments->isEmpty())
                <p class="text-gray-400 text-sm py-6 text-center">No tiene citas registradas.</p>
            @else
                <div class="space-y-3">
                    @foreach($patient->appointments->sortByDesc('date') as $appt)
                        <div
                            class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-4">
                                <div class="text-center">
                                    <p class="text-lg font-bold text-gray-800">
                                        {{ \Carbon\Carbon::parse($appt->date)->format('d') }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($appt->date)->format('M Y') }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">Dr. {{ $appt->doctor->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $appt->specialty->name }} ·
                                        {{ \Carbon\Carbon::parse($appt->date)->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            @php
                                $colors = ['pending' => 'yellow', 'confirmed' => 'blue', 'in_progress' => 'purple', 'completed' => 'green', 'cancelled' => 'red', 'no_show' => 'gray'];
                                $c = $colors[$appt->status] ?? 'gray';
                            @endphp
                            <span
                                class="px-3 py-1 bg-{{ $c }}-50 text-{{ $c }}-600 rounded-full text-xs font-medium">{{ $appt->status_label }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>