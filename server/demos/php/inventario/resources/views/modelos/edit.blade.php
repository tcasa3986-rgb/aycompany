<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Editar Modelo</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <form action="{{ route('modelos.update', $modelo) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="id_marca" class="block text-sm font-medium text-gray-300 mb-2">Marca</label>
                        <select name="id_marca" id="id_marca"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($marcas as $marca)
                                <option value="{{ $marca->id }}" {{ (old('id_marca', $modelo->id_marca) == $marca->id) ? 'selected' : '' }}>
                                    {{ $marca->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_marca')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-300 mb-2">Nombre del
                            Modelo</label>
                        <input type="text" name="nombre" id="nombre"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('nombre', $modelo->nombre) }}" required>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-300 mb-2">Estado</label>
                        <select name="estado" id="estado"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Activo" {{ old('estado', $modelo->estado) == 'Activo' ? 'selected' : '' }}>
                                Activo
                            </option>
                            <option value="Inactivo" {{ old('estado', $modelo->estado) == 'Inactivo' ? 'selected' : '' }}>
                                Inactivo</option>
                        </select>
                        @error('estado')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <a href="{{ route('modelos.index') }}"
                            class="px-4 py-2 text-gray-400 hover:text-white transition-colors">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200">
                            Actualizar Modelo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>