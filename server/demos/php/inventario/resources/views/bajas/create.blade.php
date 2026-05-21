<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Registrar Baja de Equipo</h2>
            <a href="{{ route('bajas.index') }}"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            {{-- Warning Box --}}
            <div class="mb-6 bg-red-500/10 border border-red-500/30 rounded-lg p-4">
                <p class="text-red-300 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    El equipo seleccionado pasará al estado "De Baja" y ya no estará disponible para asignación
                </p>
            </div>

            <form action="{{ route('bajas.store') }}" method="POST">
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

                    {{-- Fecha de Baja --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Fecha de Baja *</label>
                        <input type="date" name="fecha_baja" value="{{ old('fecha_baja', date('Y-m-d')) }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('fecha_baja') border-red-500 @enderror">
                        @error('fecha_baja')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Motivo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Motivo de la Baja *</label>
                        <select name="motivo" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('motivo') border-red-500 @enderror">
                            <option value="">Seleccione...</option>
                            <option value="Obsolescencia" {{ old('motivo') == 'Obsolescencia' ? 'selected' : '' }}>
                                Obsolescencia</option>
                            <option value="Daño Irreparable" {{ old('motivo') == 'Daño Irreparable' ? 'selected' : '' }}>
                                Daño Irreparable</option>
                            <option value="Perdida" {{ old('motivo') == 'Perdida' ? 'selected' : '' }}>Pérdida</option>
                            <option value="Robo" {{ old('motivo') == 'Robo' ? 'selected' : '' }}>Robo</option>
                            <option value="Otro" {{ old('motivo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('motivo')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Descripción *</label>
                        <textarea name="descripcion" rows="4" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('descripcion') border-red-500 @enderror"
                            placeholder="Describa las razones y detalles de la baja...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Autorizado Por --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Autorizado Por</label>
                        <input type="text" name="autorizado_por" value="{{ old('autorizado_por') }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('autorizado_por') border-red-500 @enderror"
                            placeholder="Nombre de quien autoriza la baja">
                        @error('autorizado_por')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('bajas.index') }}"
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-semibold rounded-lg shadow-lg">
                        Registrar Baja
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>