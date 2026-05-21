<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('doctors.show', $doctor) }}" class="text-gray-400 hover:text-blue-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            Consultorios — Dr. {{ $doctor->user->name }}
        </div>
    </x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-gray-500 text-sm">Gestiona las diferentes ubicaciones de atención del médico.</p>
        <a href="{{ route('doctors.offices.create', $doctor) }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition shadow-sm font-medium">
            + Nuevo Consultorio
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($offices as $office)
            <div
                class="bg-white rounded-2xl shadow-sm border {{ $office->is_active ? 'border-gray-100' : 'border-gray-200 bg-gray-50' }} p-6 relative group transition hover:shadow-md">
                @if(!$office->is_active)
                    <span
                        class="absolute top-4 right-4 bg-gray-200 text-gray-500 text-[10px] uppercase tracking-wider font-bold px-2 py-1 rounded-md">Inactivo</span>
                @endif

                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1v1H9V7zm5 0h1v1h-1V7zm-5 4h1v1H9v-1zm5 0h1v1h-1v-1zm-3 4H2v1h5.5l2.5 2.5 2.5-2.5H19v-1h-2m-8-3h1v1H9v-1z" />
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-1 {{ !$office->is_active ? 'text-gray-500' : '' }}">
                    {{ $office->name }}</h3>

                <div class="space-y-2 text-sm text-gray-500 mt-4">
                    @if($office->address)
                        <p class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-gray-400 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $office->address }} {{ $office->floor ? "({$office->floor})" : '' }}</span>
                        </p>
                    @endif
                    @if($office->phone)
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {{ $office->phone }}
                        </p>
                    @endif
                    @if($office->maps_url)
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <a href="{{ $office->maps_url }}" target="_blank"
                                class="text-blue-500 hover:underline inline-block truncate w-full">Google Maps</a>
                        </p>
                    @endif
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex gap-2">
                    <a href="{{ route('doctors.offices.edit', [$doctor, $office]) }}"
                        class="flex-1 text-center py-2 bg-yellow-50 text-yellow-600 rounded-lg text-sm font-medium hover:bg-yellow-100 transition">Editar</a>
                    <form action="{{ route('doctors.offices.destroy', [$doctor, $office]) }}" method="POST" class="flex-1"
                        onsubmit="return confirm('¿Seguro que deseas eliminar este consultorio?');">
                        @csrf @method('DELETE')
                        <button
                            class="w-full text-center py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition">Eliminar</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-dashed border-gray-300">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1v1H9V7zm5 0h1v1h-1V7zm-5 4h1v1H9v-1zm5 0h1v1h-1v-1zm-3 4H2v1h5.5l2.5 2.5 2.5-2.5H19v-1h-2m-8-3h1v1H9v-1z" />
                    </svg>
                </div>
                <p class="text-gray-500">Este médico no tiene consultorios registrados.</p>
                <a href="{{ route('doctors.offices.create', $doctor) }}"
                    class="inline-block mt-4 text-blue-500 font-medium hover:underline">Agregar el primero</a>
            </div>
        @endforelse
    </div>
</x-app-layout>