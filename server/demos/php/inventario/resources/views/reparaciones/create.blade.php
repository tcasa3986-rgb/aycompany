<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Registrar Reparación</h2>
            <a href="{{ route('reparaciones.index') }}"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            {{-- Info Box --}}
            <div class="mb-6 bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                <p class="text-blue-300 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    El equipo seleccionado pasará automáticamente al estado "En Reparación"
                </p>
            </div>

            <form action="{{ route('reparaciones.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    {{-- Equipo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Equipo *</label>
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
                    </div>

                    {{-- Fecha Ingreso --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Fecha de Ingreso *</label>
                        <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', date('Y-m-d')) }}"
                            required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('fecha_ingreso') border-red-500 @enderror">
                        @error('fecha_ingreso')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descripción del Problema --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Descripción del Problema *</label>
                        <textarea name="descripcion_problema" rows="4" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('descripcion_problema') border-red-500 @enderror"
                            placeholder="Describa detalladamente el problema reportado...">{{ old('descripcion_problema') }}</textarea>
                        @error('descripcion_problema')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Técnico Asignado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Técnico Asignado</label>
                        <input type="text" name="tecnico_asignado" value="{{ old('tecnico_asignado') }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('tecnico_asignado') border-red-500 @enderror"
                            placeholder="Nombre del técnico">
                        @error('tecnico_asignado')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Costo Estimado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Costo Estimado
                            ({{ $setting->currency_symbol ?? 'S/' }})</label>
                        <input type="number" step="0.01" min="0" name="costo_estimado"
                            value="{{ old('costo_estimado') }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('costo_estimado') border-red-500 @enderror"
                            placeholder="0.00">
                        @error('costo_estimado')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('reparaciones.index') }}"
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Registrar Reparación
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>