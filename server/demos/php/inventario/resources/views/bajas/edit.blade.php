<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Editar Baja</h2>
            <a href="{{ route('bajas.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            {{-- Equipo Info (Read-only) --}}
            <div class="mb-6 bg-gray-750 rounded-lg p-4 border border-gray-700">
                <h3 class="text-white font-semibold mb-2">Equipo</h3>
                <p class="text-gray-300">
                    {{ $baja->equipo->codigo_inventario }} -
                    {{ $baja->equipo->marca->nombre ?? '' }} {{ $baja->equipo->modelo->nombre ?? '' }}
                </p>
            </div>

            <form action="{{ route('bajas.update', $baja) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    {{-- Fecha de Baja --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Fecha de Baja *</label>
                        <input type="date" name="fecha_baja" value="{{ old('fecha_baja', $baja->fecha_baja->format('Y-m-d')) }}" required class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('fecha_baja') border-red-500 @enderror">
                        @error('fecha_baja')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Motivo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Motivo de la Baja *</label>
                        <select name="motivo" required class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('motivo') border-red-500 @enderror">
                            <option value="">Seleccione...</option>
                            <option value="Obsolescencia" {{ old('motivo', $baja->motivo) == 'Obsolescencia' ? 'selected' : '' }}>Obsolescencia</option>
                            <option value="Daño Irreparable" {{ old('motivo', $baja->motivo) == 'Daño Irreparable' ? 'selected' : '' }}>Daño Irreparable</option>
                            <option value="Perdida" {{ old('motivo', $baja->motivo) == 'Perdida' ? 'selected' : '' }}>Pérdida</option>
                            <option value="Robo" {{ old('motivo', $baja->motivo) == 'Robo' ? 'selected' : '' }}>Robo</option>
                            <option value="Otro" {{ old('motivo', $baja->motivo) == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('motivo')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Descripción *</label>
                        <textarea name="descripcion" rows="4" required class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('descripcion') border-red-500 @enderror" placeholder="Describa las razones y detalles de la baja...">{{ old('descripcion', $baja->descripcion) }}</textarea>
                        @error('descripcion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Autorizado Por --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Autorizado Por</label>
                        <input type="text" name="autorizado_por" value="{{ old('autorizado_por', $baja->autorizado_por) }}" class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('autorizado_por') border-red-500 @enderror" placeholder="Nombre de quien autoriza la baja">
                        @error('autorizado_por')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('bajas.index') }}" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Cancelar</a>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Actualizar Baja
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>