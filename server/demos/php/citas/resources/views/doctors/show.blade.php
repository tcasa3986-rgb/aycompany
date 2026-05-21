<x-app-layout>
    <x-slot name="header">Dr. {{ $doctor->user->name }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-5 mb-6">
                <div class="w-16 h-16 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-2xl">
                    {{ strtoupper(substr($doctor->user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Dr. {{ $doctor->user->name }}</h2>
                    <p class="text-gray-500 text-sm">{{ $doctor->specialty->name }}</p>
                </div>
                <div class="ml-auto flex flex-wrap justify-end items-center gap-2">
                    <a href="{{ route('doctors.offices.index', $doctor) }}"
                       class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-lg text-sm font-medium hover:bg-emerald-100 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1v1H9V7zm5 0h1v1h-1V7zm-5 4h1v1H9v-1zm5 0h1v1h-1v-1zm-3 4H2v1h5.5l2.5 2.5 2.5-2.5H19v-1h-2m-8-3h1v1H9v-1z"/>
                        </svg>
                        Consultorios
                    </a>
                    <a href="{{ route('doctors.schedule.index', $doctor) }}"
                       class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Horarios
                    </a>
                    <a href="{{ route('doctors.appointment-types.index', $doctor) }}"
                       class="px-4 py-2 bg-purple-50 text-purple-600 rounded-lg text-sm font-medium hover:bg-purple-100 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Servicios
                    </a>
                    <a href="{{ route('doctors.edit', $doctor) }}" class="px-4 py-2 bg-yellow-50 text-yellow-600 rounded-lg text-sm font-medium hover:bg-yellow-100 transition">Editar</a>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                <div><p class="text-gray-400 text-xs mb-1">Email</p><p class="font-medium">{{ $doctor->user->email }}</p></div>
                <div><p class="text-gray-400 text-xs mb-1">Colegiatura</p><p class="font-mono font-medium">{{ $doctor->collegiate_number }}</p></div>
                <div><p class="text-gray-400 text-xs mb-1">Citas realizadas</p><p class="font-bold text-blue-600">{{ $doctor->appointments->count() }}</p></div>
                @if($doctor->biography)
                <div class="col-span-3"><p class="text-gray-400 text-xs mb-1">Biografía</p><p class="text-gray-700">{{ $doctor->biography }}</p></div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Próximas Citas</h3>
            @php $upcoming = $doctor->appointments->where('date', '>=', now())->sortBy('date')->take(10); @endphp
            @if($upcoming->isEmpty())
                <p class="text-gray-400 text-sm py-6 text-center">Sin citas próximas.</p>
            @else
                <div class="space-y-3">
                    @foreach($upcoming as $appt)
                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition">
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ $appt->patient->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($appt->date)->format('d/m/Y H:i') }}</p>
                        </div>
                        <a href="{{ route('appointments.show', $appt) }}" class="text-blue-500 text-xs hover:underline">Ver cita →</a>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
