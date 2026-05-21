<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Actualizar Reparación</h2>
            <a href="{{ route('reparaciones.index') }}"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            {{-- Info Box --}}
            <div class="mb-6 bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4">
                <p class="text-yellow-300 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Al marcar como "Completada" o "Cancelada", el equipo volverá a estado "Disponible"
                </p>
            </div>

            {{-- Equipo Info (Read-only) --}}
            <div class="mb-6 bg-gray-750 rounded-lg p-4 border border-gray-700">
                <h3 class="text-white font-semibold mb-2">Equipo en Reparación</h3>
                <p class="text-gray-300">
                    {{ $reparacione->equipo->codigo_inventario }} -
                    {{ $reparacione->equipo->marca->nombre ?? '' }} {{ $reparacione->equipo->modelo->nombre ?? '' }}
                </p>
                <p class="text-gray-400 text-sm mt-1">Ingresó: {{ $reparacione->fecha_ingreso->format('d/m/Y') }}</p>
            </div>

            <form action="{{ route('reparaciones.update', $reparacione) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Estado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Estado de la Reparación *</label>
                        <select name="estado_reparacion" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('estado_reparacion') border-red-500 @enderror">
                            <option value="Pendiente" {{ old('estado_reparacion', $reparacione->estado_reparacion) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="En Proceso" {{ old('estado_reparacion', $reparacione->estado_reparacion) == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="Completada" {{ old('estado_reparacion', $reparacione->estado_reparacion) == 'Completada' ? 'selected' : '' }}>Completada</option>
                            <option value="Cancelada" {{ old('estado_reparacion', $reparacione->estado_reparacion) == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                        @error('estado_reparacion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha Salida --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Fecha de Salida</label>
                        <input type="date" name="fecha_salida"
                            value="{{ old('fecha_salida', $reparacione->fecha_salida?->format('Y-m-d')) }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('fecha_salida') border-red-500 @enderror">
                        @error('fecha_salida')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descripción de la Solución --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Descripción de la Solución</label>
                        <textarea name="descripcion_solucion" rows="4"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('descripcion_solucion') border-red-500 @enderror"
                            placeholder="Describa las acciones realizadas y la solución aplicada...">{{ old('descripcion_solucion', $reparacione->descripcion_solucion) }}</textarea>
                        @error('descripcion_solucion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Técnico Asignado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Técnico Asignado</label>
                        <input type="text" name="tecnico_asignado"
                            value="{{ old('tecnico_asignado', $reparacione->tecnico_asignado) }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('tecnico_asignado') border-red-500 @enderror"
                            placeholder="Nombre del técnico">
                        @error('tecnico_asignado')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Costo Real --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Costo Real
                            ({{ $setting->currency_symbol ?? 'S/' }})</label>
                        <input type="number" step="0.01" min="0" name="costo_real"
                            value="{{ old('costo_real', $reparacione->costo_real) }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('costo_real') border-red-500 @enderror"
                            placeholder="0.00">
                        @if($reparacione->costo_estimado)
                            <p class="text-gray-400 text-sm mt-1">Costo estimado: {{ $setting->currency_symbol ?? 'S/' }}
                                {{ number_format($reparacione->costo_estimado, 2) }}
                            </p>
                        @endif
                        @error('costo_real')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Problema Original (Read-only) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Problema Reportado</label>
                        <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                            <p class="text-gray-300">{{ $reparacione->descripcion_problema }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('reparaciones.index') }}"
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Actualizar Reparación
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>