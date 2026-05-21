<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Registrar Nuevo Empleado</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            <form action="{{ route('empleados.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Sucursal --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Sucursal *</label>
                        <select name="id_sucursal" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('id_sucursal') border-red-500 @enderror">
                            <option value="">Seleccione una sucursal</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" {{ old('id_sucursal') == $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_sucursal')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- DNI --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">DNI *</label>
                        <input type="text" name="dni" value="{{ old('dni') }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('dni') border-red-500 @enderror"
                            placeholder="Ej: 12345678">
                        @error('dni')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Estado *</label>
                        <select name="estado" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('estado') border-red-500 @enderror">
                            <option value="Activo" {{ old('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                            <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estado')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombres --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nombres *</label>
                        <input type="text" name="nombres" value="{{ old('nombres') }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('nombres') border-red-500 @enderror"
                            placeholder="Ej: Juan Antonio">
                        @error('nombres')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Apellidos --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos') }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('apellidos') border-red-500 @enderror"
                            placeholder="Ej: Pérez López">
                        @error('apellidos')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Área --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Área</label>
                        <select name="id_area" id="id_area"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('id_area') border-red-500 @enderror">
                            <option value="">Seleccione un área</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('id_area') == $area->id ? 'selected' : '' }}>
                                    {{ $area->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_area')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cargo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Cargo</label>
                        <select name="id_cargo" id="id_cargo"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('id_cargo') border-red-500 @enderror">
                            <option value="">Seleccione un cargo</option>
                            @foreach($cargos as $cargo)
                                <option value="{{ $cargo->id }}" {{ old('id_cargo') == $cargo->id ? 'selected' : '' }}>
                                    {{ $cargo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_cargo')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-700">
                    <a href="{{ route('empleados.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Guardar Empleado
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const areaSelect = document.getElementById('id_area');
            const cargoSelect = document.getElementById('id_cargo');

            areaSelect.addEventListener('change', function () {
                const areaId = this.value;
                cargoSelect.innerHTML = '<option value="">Cargando cargos...</option>';

                if (areaId) {
                    fetch(`/api/areas/${areaId}/cargos`)
                        .then(response => response.json())
                        .then(data => {
                            cargoSelect.innerHTML = '<option value="">Seleccione un cargo</option>';
                            data.forEach(cargo => {
                                cargoSelect.innerHTML += `<option value="${cargo.id}">${cargo.nombre}</option>`;
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            cargoSelect.innerHTML = '<option value="">Error al cargar cargos</option>';
                        });
                } else {
                    cargoSelect.innerHTML = '<option value="">Seleccione un cargo</option>';
                }
            });
        });
    </script>
</x-app-layout>