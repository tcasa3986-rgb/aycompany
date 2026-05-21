<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Nueva Asignación</h2>
            <a href="{{ route('asignaciones.index') }}"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            <form action="{{ route('asignaciones.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    {{-- Equipo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Equipo Disponible *</label>
                        <select name="id_equipo" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('id_equipo') border-red-500 @enderror">
                            <option value="">Seleccione un equipo...</option>
                            @foreach($equipos as $equipo)
                                <option value="{{ $equipo->id }}" {{ old('id_equipo') == $equipo->id ? 'selected' : '' }}>
                                    {{ $equipo->codigo_inventario }} - {{ $equipo->marca->nombre ?? '' }}
                                    {{ $equipo->modelo->nombre ?? '' }} ({{ $equipo->tipoEquipo->nombre ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_equipo')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-400 text-xs mt-1">Solo se muestran equipos en estado "Disponible"</p>
                    </div>

                    {{-- Empleado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Empleado *</label>
                        <select name="id_empleado" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('id_empleado') border-red-500 @enderror">
                            <option value="">Seleccione un empleado...</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id }}" {{ old('id_empleado') == $empleado->id ? 'selected' : '' }}>
                                    {{ $empleado->nombreCompleto() }} - {{ $empleado->cargo->nombre ?? 'Sin cargo' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_empleado')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha Entrega --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Fecha de Entrega *</label>
                        <input type="date" name="fecha_entrega" value="{{ old('fecha_entrega', date('Y-m-d')) }}"
                            required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('fecha_entrega') border-red-500 @enderror">
                        @error('fecha_entrega')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Observaciones Entrega --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Observaciones de Entrega</label>
                        <textarea name="observaciones_entrega" rows="4"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('observaciones_entrega') border-red-500 @enderror"
                            placeholder="Ingrese observaciones sobre el estado del equipo al momento de la entrega...">{{ old('observaciones_entrega') }}</textarea>
                        @error('observaciones_entrega')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Info Box --}}
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-blue-300">
                                <p class="font-semibold mb-1">Información importante:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Al crear la asignación, el equipo cambiará automáticamente a estado "Asignado"
                                    </li>
                                    <li>La asignación se creará con estado "Activa"</li>
                                    <li>Puede modificar o finalizar la asignación posteriormente desde el listado</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('asignaciones.index') }}"
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Crear Asignación
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>