<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Editar Empleado</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            <form action="{{ route('empleados.update', $empleado) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Sucursal --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Sucursal *</label>
                        <select name="id_sucursal" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('id_sucursal') border-red-500 @enderror">
                            <option value="">Seleccione una sucursal</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" {{ old('id_sucursal', $empleado->id_sucursal) == $sucursal->id ? 'selected' : '' }}>
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
                        <input type="text" name="dni" value="{{ old('dni', $empleado->dni) }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('dni') border-red-500 @enderror">
                        @error('dni')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Estado *</label>
                        <select name="estado" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('estado') border-red-500 @enderror">
                            <option value="Activo" {{ old('estado', $empleado->estado) == 'Activo' ? 'selected' : '' }}>
                                Activo</option>
                            <option value="Inactivo" {{ old('estado', $empleado->estado) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estado')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombres --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nombres *</label>
                        <input type="text" name="nombres" value="{{ old('nombres', $empleado->nombres) }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('nombres') border-red-500 @enderror">
                        @error('nombres')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Apellidos --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $empleado->apellidos) }}"
                            required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('apellidos') border-red-500 @enderror">
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
                                <option value="{{ $area->id }}" {{ old('id_area', $empleado->id_area) == $area->id ? 'selected' : '' }}>
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
                                <option value="{{ $cargo->id }}" {{ old('id_cargo', $empleado->id_cargo) == $cargo->id ? 'selected' : '' }}>
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
                        Actualizar Empleado
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const areaSelect = document.getElementById('id_area');
            const cargoSelect = document.getElementById('id_cargo');
            const currentCargoId = "{{ old('id_cargo', $empleado->id_cargo) }}";

            // Función para cargar cargos
            function loadCargos(areaId, selectId = null) {
                if (!areaId) {
                    cargoSelect.innerHTML = '<option value="">Seleccione un cargo</option>';
                    return;
                }

                cargoSelect.innerHTML = '<option value="">Cargando cargos...</option>';

                fetch(`/api/areas/${areaId}/cargos`)
                    .then(response => response.json())
                    .then(data => {
                        cargoSelect.innerHTML = '<option value="">Seleccione un cargo</option>';
                        data.forEach(cargo => {
                            const isSelected = selectId == cargo.id ? 'selected' : '';
                            cargoSelect.innerHTML += `<option value="${cargo.id}" ${isSelected}>${cargo.nombre}</option>`;
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        cargoSelect.innerHTML = '<option value="">Error al cargar cargos</option>';
                    });
            }

            // Cargar cargos al cambiar área
            areaSelect.addEventListener('change', function () {
                loadCargos(this.value);
            });

            // Si hay un error de validación o estamos editando, asegurar que los cargos del área seleccionada estén cargados
            // Pero como estamos en JS, si recargamos la página por error de validación, el HTML ya trae las opciones seleccionadas si el backend las mandara,
            // pero el backend manda TODAS las opciones. 
            // Para "limpiar" la lista visualmente y solo dejar las del área, podríamos ejecutar loadCargos al inicio.
            // Sin embargo, en blade el foreach ya renderiza todas.
            // Lo ideal es: Si cambiamos de área -> cargar nuevos.
            // Al inicio -> Si queremos filtrar strictamente, podríamos llamar a loadCargos(areaSelect.value, currentCargoId).

            // Opcional: Ejecutar al inicio para filtrar la lista inicial si se desea comportamiento estricto
            // loadCargos(areaSelect.value, currentCargoId);
        });
    </script>
</x-app-layout>