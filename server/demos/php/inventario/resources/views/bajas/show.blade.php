<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Detalle de Baja</h2>
            <div class="flex gap-3">
                <a href="{{ route('bajas.edit', $baja) }}"
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                    Editar
                </a>
                <a href="{{ route('bajas.index') }}"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">
            {{-- Estado de Baja --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Información de la Baja</h3>
                    @if($baja->motivo === 'Obsolescencia')
                        <span
                            class="px-4 py-2 bg-gray-500/20 text-gray-400 rounded-lg text-sm font-semibold">Obsolescencia</span>
                    @elseif($baja->motivo === 'Daño Irreparable')
                        <span class="px-4 py-2 bg-orange-500/20 text-orange-400 rounded-lg text-sm font-semibold">Daño
                            Irreparable</span>
                    @elseif($baja->motivo === 'Perdida')
                        <span
                            class="px-4 py-2 bg-yellow-500/20 text-yellow-400 rounded-lg text-sm font-semibold">Pérdida</span>
                    @elseif($baja->motivo === 'Robo')
                        <span class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg text-sm font-semibold">Robo</span>
                    @else
                        <span
                            class="px-4 py-2 bg-purple-500/20 text-purple-400 rounded-lg text-sm font-semibold">Otro</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Fecha de Baja</p>
                        <p class="text-white font-semibold text-lg">{{ $baja->fecha_baja->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Autorizado Por</p>
                        <p class="text-white font-semibold text-lg">{{ $baja->autorizado_por ?: 'No especificado' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-400 text-sm mb-1">Tiempo desde la baja</p>
                        <p class="text-white font-medium">{{ $baja->fecha_baja->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Información del Equipo --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-white mb-6">Información del Equipo</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Código de Inventario</p>
                        <p class="text-white font-semibold text-lg">{{ $baja->equipo->codigo_inventario }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Tipo</p>
                        <p class="text-white font-medium">{{ $baja->equipo->tipoEquipo->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Marca y Modelo</p>
                        <p class="text-white font-medium">
                            {{ $baja->equipo->marca->nombre ?? '' }}
                            {{ $baja->equipo->modelo->nombre ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Número de Serie</p>
                        <p class="text-white font-medium">{{ $baja->equipo->numero_serie ?: 'No registrado' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Sucursal</p>
                        <p class="text-white font-medium">{{ $baja->equipo->sucursal->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Estado Actual</p>
                        <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-sm">De Baja</span>
                    </div>
                </div>

                @if($baja->equipo->caracteristicas)
                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <p class="text-gray-400 text-sm mb-2">Características del Equipo</p>
                        <p class="text-white">{{ $baja->equipo->caracteristicas }}</p>
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('equipos.show', $baja->equipo) }}"
                        class="text-blue-400 hover:text-blue-300 text-sm">
                        Ver historial completo del equipo →
                    </a>
                </div>
            </div>

            {{-- Descripción de la Baja --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-white mb-4">Descripción y Justificación</h3>
                <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                    <p class="text-white leading-relaxed">{{ $baja->descripcion }}</p>
                </div>
            </div>

            {{-- Warning para restaurar --}}
            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4">
                <p class="text-yellow-300 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Si elimina este registro de baja, el equipo volverá al estado "Disponible"
                </p>
            </div>
        </div>
    </div>
</x-app-layout>