<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Editar Asignación</h2>
            <a href="{{ route('asignaciones.index') }}"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            <form action="{{ route('asignaciones.update', $asignacione) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Info de Equipo y Empleado (readonly) --}}
                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                        <h3 class="text-lg font-semibold text-white mb-3">Información de la Asignación</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-400">Equipo:</span>
                                <p class="text-white font-medium">{{ $asignacione->equipo->codigo_inventario }}</p>
                                <p class="text-gray-300 text-xs">{{ $asignacione->equipo->marca->nombre ?? '' }}
                                    {{ $asignacione->equipo->modelo->nombre ?? '' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Empleado:</span>
                                <p class="text-white font-medium">{{ $asignacione->empleado->nombreCompleto() }}</p>
                                <p class="text-gray-300 text-xs">
                                    {{ $asignacione->empleado->cargo->nombre ?? 'Sin cargo' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Fecha de Entrega:</span>
                                <p class="text-white">{{ $asignacione->fecha_entrega->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Estado Actual:</span>
                                <p>
                                    @if($asignacione->estado_asignacion === 'Activa')
                                        <span
                                            class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Activa</span>
                                    @else
                                        <span
                                            class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Finalizada</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Fecha Devolución --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Fecha de Devolución</label>
                        <input type="date" name="fecha_devolucion"
                            value="{{ old('fecha_devolucion', $asignacione->fecha_devolucion?->format('Y-m-d')) }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('fecha_devolucion') border-red-500 @enderror">
                        @error('fecha_devolucion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-400 text-xs mt-1">Dejar vacío si la asignación sigue activa</p>
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Estado de Asignación *</label>
                        <select name="estado_asignacion" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('estado_asignacion') border-red-500 @enderror">
                            <option value="Activa" {{ old('estado_asignacion', $asignacione->estado_asignacion) == 'Activa' ? 'selected' : '' }}>Activa</option>
                            <option value="Finalizada" {{ old('estado_asignacion', $asignacione->estado_asignacion) == 'Finalizada' ? 'selected' : '' }}>Finalizada</option>
                        </select>
                        @error('estado_asignacion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Observaciones Devolución --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Observaciones de Devolución</label>
                        <textarea name="observaciones_devolucion" rows="4"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('observaciones_devolucion') border-red-500 @enderror"
                            placeholder="Ingrese observaciones sobre el estado del equipo al momento de la devolución...">{{ old('observaciones_devolucion', $asignacione->observaciones_devolucion) }}</textarea>
                        @error('observaciones_devolucion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Warning Box --}}
                    @if($asignacione->estado_asignacion === 'Activa')
                        <div class="bg-orange-500/10 border border-orange-500/30 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-orange-400 mt-0.5 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="text-sm text-orange-300">
                                    <p class="font-semibold mb-1">Atención:</p>
                                    <p class="text-xs">Al cambiar el estado a "Finalizada", el equipo automáticamente
                                        volverá a estado "Disponible" y podrá ser asignado a otro empleado.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('asignaciones.index') }}"
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Actualizar Asignación
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>