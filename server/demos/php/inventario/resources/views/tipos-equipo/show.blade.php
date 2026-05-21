<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">
            {{-- Header Content --}}
            <div class="flex justify-between items-center bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-white">Detalle del Tipo de Equipo</h2>
                <div class="flex gap-3">
                    <a href="{{ route('tipos-equipo.edit', $tiposEquipo) }}"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </a>
                    <a href="{{ route('tipos-equipo.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </a>
                </div>
            </div>

            {{-- Información Principal --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Información del Tipo</h3>
                    @if($tiposEquipo->estado === 'Activo')
                        <span
                            class="px-4 py-2 bg-green-500/20 text-green-400 rounded-lg text-sm font-semibold">Activo</span>
                    @else
                        <span
                            class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg text-sm font-semibold">Inactivo</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Nombre</p>
                        <p class="text-white font-semibold text-lg">{{ $tiposEquipo->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Total de Equipos Asociados</p>
                        <p class="text-white font-medium">{{ $tiposEquipo->equipos_count ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Lista de Equipos Recientes (Opcional) --}}
             @if($tiposEquipo->equipos && $tiposEquipo->equipos->count() > 0)
                <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-xl font-bold text-white mb-6">Equipos Recientes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr class="text-left">
                                    <th class="px-4 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">
                                        Marca</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Modelo
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Serie
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Estado
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($tiposEquipo->equipos->take(5) as $equipo)
                                    <tr class="hover:bg-gray-750">
                                        <td class="px-4 py-3 text-white">{{ $equipo->marca->nombre ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-gray-300">{{ $equipo->modelo->nombre ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-300">
                                            {{ $equipo->numero_serie ?: 'N/A' }}</td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 {{ $equipo->estado == 'Disponible' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }} rounded text-xs font-semibold">
                                                {{ $equipo->estado }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
