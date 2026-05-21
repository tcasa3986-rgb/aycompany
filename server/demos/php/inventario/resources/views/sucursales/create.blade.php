<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Nueva Sucursal</h2>
            <a href="{{ route('sucursales.index') }}"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            <form action="{{ route('sucursales.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nombre *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-500 @enderror">
                        @error('nombre')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Dirección</label>
                        <textarea name="direccion" rows="3"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('direccion') border-red-500 @enderror">{{ old('direccion') }}</textarea>
                        @error('direccion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('telefono') border-red-500 @enderror">
                        @error('telefono')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Estado *</label>
                        <select name="estado" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('estado') border-red-500 @enderror">
                            <option value="Activo" {{ old('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                            <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estado')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('sucursales.index') }}"
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Guardar Sucursal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>