<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Detalle de Reparación</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">
            {{-- Header Actions --}}
            <div class="flex justify-end gap-3">
                @if(in_array($reparacione->estado_reparacion, ['Pendiente', 'En Proceso']))
                    <a href="{{ route('reparaciones.edit', $reparacione) }}"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Actualizar
                    </a>
                @endif
                <a href="{{ route('reparaciones.index') }}"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                    Volver
                </a>
            </div>

            {{-- Estado y Timeline --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Estado de la Reparación</h3>
                    @if($reparacione->estado_reparacion === 'Pendiente')
                        <span
                            class="px-4 py-2 bg-yellow-500/20 text-yellow-400 rounded-lg text-sm font-semibold">Pendiente</span>
                    @elseif($reparacione->estado_reparacion === 'En Proceso')
                        <span class="px-4 py-2 bg-blue-500/20 text-blue-400 rounded-lg text-sm font-semibold">En
                            Proceso</span>
                    @elseif($reparacione->estado_reparacion === 'Completada')
                        <span
                            class="px-4 py-2 bg-green-500/20 text-green-400 rounded-lg text-sm font-semibold">Completada</span>
                    @else
                        <span class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg text-sm font-semibold">Cancelada</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Fecha de Ingreso</p>
                        <p class="text-white font-semibold text-lg">{{ $reparacione->fecha_ingreso->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Fecha de Salida</p>
                        <p class="text-white font-semibold text-lg">
                            {{ $reparacione->fecha_salida?->format('d/m/Y') ?: 'Pendiente' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Duración</p>
                        <p class="text-white font-semibold text-lg">
                            @if($reparacione->fecha_salida)
                                {{ $reparacione->fecha_ingreso->diffInDays($reparacione->fecha_salida) }} días
                            @else
                                {{ $reparacione->fecha_ingreso->diffInDays(now()) }} días transcurridos
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Información del Equipo --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-white mb-6">Información del Equipo</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Código de Inventario</p>
                        <p class="text-white font-semibold text-lg">{{ $reparacione->equipo->codigo_inventario }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Tipo</p>
                        <p class="text-white font-medium">{{ $reparacione->equipo->tipoEquipo->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Marca y Modelo</p>
                        <p class="text-white font-medium">
                            {{ $reparacione->equipo->marca->nombre ?? '' }}
                            {{ $reparacione->equipo->modelo->nombre ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Número de Serie</p>
                        <p class="text-white font-medium">{{ $reparacione->equipo->numero_serie ?: 'No registrado' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Sucursal</p>
                        <p class="text-white font-medium">{{ $reparacione->equipo->sucursal->nombre ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('equipos.show', $reparacione->equipo) }}"
                        class="text-blue-400 hover:text-blue-300 text-sm">
                        Ver detalles completos del equipo →
                    </a>
                </div>
            </div>

            {{-- Problema y Solución --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-white mb-6">Detalles Técnicos</h3>

                <div class="space-y-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-2">Problema Reportado</p>
                        <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                            <p class="text-white leading-relaxed">{{ $reparacione->descripcion_problema }}</p>
                        </div>
                    </div>

                    @if($reparacione->descripcion_solucion)
                        <div>
                            <p class="text-gray-400 text-sm mb-2">Solución Aplicada</p>
                            <div class="bg-gray-750 rounded-lg p-4 border border-green-500/20">
                                <p class="text-white leading-relaxed">{{ $reparacione->descripcion_solucion }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Técnico Asignado</p>
                            <p class="text-white font-medium">{{ $reparacione->tecnico_asignado ?: 'No asignado' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Costos --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-white mb-6">Información de Costos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-750 rounded-lg p-6 border border-gray-700">
                        <p class="text-gray-400 text-sm mb-2">Costo Estimado</p>
                        <p class="text-white font-bold text-2xl">
                            @if($reparacione->costo_estimado)
                                {{ $setting->currency_symbol ?? 'S/' }} {{ number_format($reparacione->costo_estimado, 2) }}
                            @else
                                <span class="text-gray-500">No estimado</span>
                            @endif
                        </p>
                    </div>
                    <div
                        class="bg-gray-750 rounded-lg p-6 border {{ $reparacione->costo_real ? 'border-green-500/20' : 'border-gray-700' }}">
                        <p class="text-gray-400 text-sm mb-2">Costo Real</p>
                        <p class="text-white font-bold text-2xl">
                            @if($reparacione->costo_real)
                                {{ $setting->currency_symbol ?? 'S/' }} {{ number_format($reparacione->costo_real, 2) }}
                            @else
                                <span class="text-gray-500">Pendiente</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($reparacione->costo_estimado && $reparacione->costo_real)
                    <div class="mt-4 p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
                        <p class="text-blue-300 text-sm">
                            @php
                                $diferencia = $reparacione->costo_real - $reparacione->costo_estimado;
                                $porcentaje = ($diferencia / $reparacione->costo_estimado) * 100;
                            @endphp
                            @if($diferencia > 0)
                                Excedió estimación en {{ $setting->currency_symbol ?? 'S/' }}
                                {{ number_format($diferencia, 2) }}
                                ({{ number_format($porcentaje, 1) }}%)
                            @elseif($diferencia < 0)
                                Por debajo de estimación por {{ $setting->currency_symbol ?? 'S/' }}
                                {{ number_format(abs($diferencia), 2) }}
                                ({{ number_format(abs($porcentaje), 1) }}%)
                            @else
                                Costo igual a la estimación
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>