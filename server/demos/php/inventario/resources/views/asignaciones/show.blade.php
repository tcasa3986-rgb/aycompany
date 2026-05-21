<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Detalle de Asignación</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">
            {{-- Header Actions --}}
            <div class="flex justify-end gap-3">
                @if($asignacione->estado_asignacion === 'Activa')
                    <a href="{{ route('asignaciones.edit', $asignacione) }}"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Editar / Finalizar
                    </a>
                @endif
                <a href="{{ route('asignaciones.index') }}"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                    Volver
                </a>
            </div>
            {{-- Estado de la Asignación --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Estado de la Asignación</h3>
                    @if($asignacione->estado_asignacion === 'Activa')
                        <span
                            class="px-4 py-2 bg-green-500/20 text-green-400 rounded-lg text-sm font-semibold">Activa</span>
                    @else
                        <span
                            class="px-4 py-2 bg-gray-500/20 text-gray-400 rounded-lg text-sm font-semibold">Finalizada</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Fecha de Entrega</p>
                        <p class="text-white font-semibold text-lg">{{ $asignacione->fecha_entrega->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Fecha de Devolución</p>
                        <p class="text-white font-semibold text-lg">
                            {{ $asignacione->fecha_devolucion?->format('d/m/Y') ?: 'Pendiente' }}
                        </p>
                    </div>
                    @if($asignacione->estado_asignacion === 'Activa')
                        <div class="md:col-span-2 bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                            <p class="text-blue-300 text-sm flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Asignación activa desde hace {{ $asignacione->fecha_entrega->diffInDays(now()) }} días
                            </p>
                        </div>
                    @else
                        <div class="md:col-span-2">
                            <p class="text-gray-400 text-sm mb-1">Duración Total</p>
                            <p class="text-white font-medium">
                                {{ $asignacione->fecha_entrega->diffInDays($asignacione->fecha_devolucion) }} días
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Información del Equipo --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-white mb-6">Información del Equipo</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Código de Inventario</p>
                        <p class="text-white font-semibold text-lg">{{ $asignacione->equipo->codigo_inventario }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Tipo</p>
                        <p class="text-white font-medium">{{ $asignacione->equipo->tipoEquipo->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Marca y Modelo</p>
                        <p class="text-white font-medium">
                            {{ $asignacione->equipo->marca->nombre ?? '' }}
                            {{ $asignacione->equipo->modelo->nombre ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Número de Serie</p>
                        <p class="text-white font-medium">{{ $asignacione->equipo->numero_serie ?: 'No registrado' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Estado Actual</p>
                        <p class="text-white font-medium">
                            @if($asignacione->equipo->estado === 'Disponible')
                                <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-sm">Disponible</span>
                            @elseif($asignacione->equipo->estado === 'Asignado')
                                <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-sm">Asignado</span>
                            @else
                                <span
                                    class="px-2 py-1 bg-orange-500/20 text-orange-400 rounded text-sm">{{ $asignacione->equipo->estado }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Sucursal</p>
                        <p class="text-white font-medium">{{ $asignacione->equipo->sucursal->nombre ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($asignacione->equipo->caracteristicas)
                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <p class="text-gray-400 text-sm mb-2">Características del Equipo</p>
                        <p class="text-white">{{ $asignacione->equipo->caracteristicas }}</p>
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('equipos.show', $asignacione->equipo) }}"
                        class="text-blue-400 hover:text-blue-300 text-sm">
                        Ver detalles completos del equipo →
                    </a>
                </div>
            </div>

            {{-- Información del Empleado --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-white mb-6">Información del Empleado</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">DNI</p>
                        <p class="text-white font-semibold text-lg">{{ $asignacione->empleado->dni }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-400 text-sm mb-1">Nombre Completo</p>
                        <p class="text-white font-semibold text-lg">{{ $asignacione->empleado->nombreCompleto() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Cargo</p>
                        <p class="text-white font-medium">{{ $asignacione->empleado->cargo->nombre ?? 'No asignado' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Área</p>
                        <p class="text-white font-medium">{{ $asignacione->empleado->area->nombre ?? 'No asignada' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Sucursal</p>
                        <p class="text-white font-medium">{{ $asignacione->empleado->sucursal->nombre ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('empleados.show', $asignacione->empleado) }}"
                        class="text-blue-400 hover:text-blue-300 text-sm">
                        Ver perfil completo del empleado →
                    </a>
                </div>
            </div>

            {{-- Observaciones --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-white mb-6">Observaciones</h3>

                @if($asignacione->observaciones_entrega)
                    <div class="mb-6">
                        <p class="text-gray-400 text-sm mb-2">Observaciones de Entrega</p>
                        <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                            <p class="text-white leading-relaxed">{{ $asignacione->observaciones_entrega }}</p>
                        </div>
                    </div>
                @endif

                @if($asignacione->observaciones_devolucion)
                    <div>
                        <p class="text-gray-400 text-sm mb-2">Observaciones de Devolución</p>
                        <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                            <p class="text-white leading-relaxed">{{ $asignacione->observaciones_devolucion }}</p>
                        </div>
                    </div>
                @endif

                @if(!$asignacione->observaciones_entrega && !$asignacione->observaciones_devolucion)
                    <p class="text-gray-400 text-center py-4">No hay observaciones registradas</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>