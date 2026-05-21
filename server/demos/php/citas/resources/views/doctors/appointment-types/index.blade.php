<x-app-layout>
    <x-slot name="header">Servicios y Tipos de Cita - Dr. {{ $doctor->user->name }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('doctors.show', $doctor) }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Volver al Perfil
            </a>
            <a href="{{ route('doctors.appointment-types.create', $doctor) }}"
                class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition">
                + Nuevo Servicio
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-xl text-sm border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($appointmentTypes as $type)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative group">
                    <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition">
                        <a href="{{ route('doctors.appointment-types.edit', [$doctor, $type]) }}"
                            class="p-1.5 text-gray-400 hover:text-blue-500 hover:bg-blue-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('doctors.appointment-types.destroy', [$doctor, $type]) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar servicio?');">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                            <span class="text-indigo-600 font-bold text-lg">{{ substr($type->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $type->name }}</h3>
                            <div class="mt-2 flex flex-col gap-1 text-sm text-gray-500">
                                <p class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $type->duration_minutes }} minutos
                                </p>
                                @if($type->price)
                                    <p class="flex items-center gap-1.5 font-medium text-emerald-600">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ \App\Models\Setting::get('currency_symbol', 'S/') }}
                                        {{ number_format($type->price, 2) }}
                                    </p>
                                @endif
                                <p class="mt-1">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-xs font-medium {{ $type->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $type->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-12 bg-white rounded-2xl border border-gray-100">
                    <p class="text-gray-500">No se han configurado Tipos de Cita / Servicios.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $appointmentTypes->links() }}
        </div>
    </div>
</x-app-layout>