<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">
            {{-- Header Content --}}
            <div class="flex justify-between items-center bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-white">Detalle de la Sucursal</h2>
                <div class="flex gap-3">
                    <a href="{{ route('sucursales.edit', $sucursale) }}"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </a>
                    <a href="{{ route('sucursales.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </a>
                </div>
            </div>

            {{-- Información Principal --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Información de la Sucursal</h3>
                    @if($sucursale->estado === 'Activo')
                        <span
                            class="px-4 py-2 bg-green-500/20 text-green-400 rounded-lg text-sm font-semibold">Activo</span>
                    @else
                        <span class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg text-sm font-semibold">Inactivo</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Nombre</p>
                        <p class="text-white font-semibold text-lg">{{ $sucursale->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Teléfono</p>
                        <p class="text-white font-medium">{{ $sucursale->telefono ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-gray-400 text-sm mb-1">Dirección</p>
                    <p class="text-white font-medium">{{ $sucursale->direccion ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Estadísticas Rápidas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Equipos -->
                <div class="bg-gray-800 rounded-xl shadow-lg p-6 flex items-center">
                    <div class="p-3 rounded-full bg-blue-500/20 text-blue-400 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Total Equipos</p>
                        <p class="text-2xl font-bold text-white">{{ $sucursale->equipos_count ?? 0 }}</p>
                    </div>
                </div>

                <!-- Empleados -->
                <div class="bg-gray-800 rounded-xl shadow-lg p-6 flex items-center">
                    <div class="p-3 rounded-full bg-purple-500/20 text-purple-400 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Total Empleados</p>
                        <p class="text-2xl font-bold text-white">{{ $sucursale->empleados_count ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Lista de Equipos (Opcional, si queremos mostrarlo aquí) --}}
            {{-- Podríamos agregar una tabla con los equipos de esta sucursal --}}

        </div>
    </div>
</x-app-layout>