<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">
            {{-- Header Content --}}
            <div class="flex justify-between items-center bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-white">Detalle del Equipo</h2>
                <div class="flex gap-3">
                    <a href="{{ route('equipos.edit', $equipo) }}"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </a>
                    <a href="{{ route('equipos.index') }}"
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
                    <h3 class="text-xl font-bold text-white">Información del Equipo</h3>
                    @if($equipo->estado === 'Disponible')
                        <span
                            class="px-4 py-2 bg-green-500/20 text-green-400 rounded-lg text-sm font-semibold">Disponible</span>
                    @elseif($equipo->estado === 'Asignado')
                        <span
                            class="px-4 py-2 bg-blue-500/20 text-blue-400 rounded-lg text-sm font-semibold">Asignado</span>
                    @elseif($equipo->estado === 'En Reparacion')
                        <span class="px-4 py-2 bg-orange-500/20 text-orange-400 rounded-lg text-sm font-semibold">En
                            Reparación</span>
                    @else
                        <span class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg text-sm font-semibold">De Baja</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Código de Inventario</p>
                        <p class="text-white font-semibold text-lg">{{ $equipo->codigo_inventario }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Tipo de Equipo</p>
                        <p class="text-white font-medium">{{ $equipo->tipoEquipo->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Marca</p>
                        <p class="text-white font-medium">{{ $equipo->marca->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Modelo</p>
                        <p class="text-white font-medium">{{ $equipo->modelo->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Número de Serie</p>
                        <p class="text-white font-medium">{{ $equipo->numero_serie ?: 'No registrado' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Sucursal</p>
                        <p class="text-white font-medium">{{ $equipo->sucursal->nombre ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($equipo->caracteristicas)
                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <p class="text-gray-400 text-sm mb-2">Características</p>
                        <p class="text-white">{{ $equipo->caracteristicas }}</p>
                    </div>
                @endif
            </div>

            {{-- Información de Adquisición --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-white mb-6">Datos de Adquisición y Costos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Tipo de Adquisición</p>
                        <p class="text-white font-medium">{{ $equipo->tipo_adquisicion ?: 'No especificado' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Fecha de Adquisición</p>
                        <p class="text-white font-medium">
                            {{ $equipo->fecha_adquisicion?->format('d/m/Y') ?: 'No registrada' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Proveedor</p>
                        <p class="text-white font-medium">{{ $equipo->proveedor ?: 'No registrado' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Costo</p>
                        <p class="text-white font-medium">
                            {{ $equipo->costo ? ($setting->currency_symbol ?? 'S/') . ' ' . number_format($equipo->costo, 2) : 'No registrado' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Número de Guía</p>
                        <p class="text-white font-medium">{{ $equipo->numero_guia ?: 'No registrado' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Guía Adjunta</p>
                        @if($equipo->archivo_guia)
                            <a href="{{ asset('storage/' . $equipo->archivo_guia) }}" target="_blank"
                                class="text-blue-400 hover:text-blue-300 flex items-center gap-1 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver Documento
                            </a>
                        @else
                            <p class="text-gray-500 italic text-sm">No adjuntada</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Asignaciones --}}
            @if($equipo->asignaciones->count() > 0)
                <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-xl font-bold text-white mb-6">Historial de Asignaciones</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr class="text-left">
                                    <th class="px-4 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">
                                        Empleado</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Fecha
                                        Entrega</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Fecha
                                        Devolución</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Estado
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($equipo->asignaciones->sortByDesc('fecha_entrega') as $asignacion)
                                    <tr class="hover:bg-gray-750">
                                        <td class="px-4 py-3 text-white">{{ $asignacion->empleado->nombreCompleto() }}</td>
                                        <td class="px-4 py-3 text-gray-300">{{ $asignacion->fecha_entrega->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-300">
                                            {{ $asignacion->fecha_devolucion?->format('d/m/Y') ?: '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($asignacion->estado_asignacion === 'Activa')
                                                <span
                                                    class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Activa</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Finalizada</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Observaciones --}}
            @if($equipo->observaciones)
                <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-xl font-bold text-white mb-4">Observaciones</h3>
                    <p class="text-gray-300 leading-relaxed">{{ $equipo->observaciones }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>