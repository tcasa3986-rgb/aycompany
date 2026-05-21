<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Editar Área</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto bg-gray-800 rounded-xl shadow-lg p-6">
            <form action="{{ route('areas.update', $area) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Nombre --}}
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-400 mb-2">Nombre del
                            Área</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $area->nombre) }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-500 @enderror"
                            required>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-400 mb-2">Estado</label>
                        <select name="estado" id="estado"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="Activo" {{ old('estado', $area->estado) == 'Activo' ? 'selected' : '' }}>Activo
                            </option>
                            <option value="Inactivo" {{ old('estado', $area->estado) == 'Inactivo' ? 'selected' : '' }}>
                                Inactivo</option>
                        </select>
                        @error('estado')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('areas.index') }}"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                            Actualizar Área
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>