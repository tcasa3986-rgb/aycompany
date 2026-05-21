<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Editar Equipo</h2>
            <a href="{{ route('equipos.index') }}"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            <form action="{{ route('equipos.update', $equipo) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Sucursal --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Sucursal *</label>
                        <select name="id_sucursal" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('id_sucursal') border-red-500 @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" {{ old('id_sucursal', $equipo->id_sucursal) == $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_sucursal')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Código Inventario --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Código de Inventario *</label>
                        <input type="text" name="codigo_inventario"
                            value="{{ old('codigo_inventario', $equipo->codigo_inventario) }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('codigo_inventario') border-red-500 @enderror">
                        @error('codigo_inventario')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tipo de Equipo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tipo de Equipo *</label>
                        <select name="id_tipo_equipo" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('id_tipo_equipo') border-red-500 @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($tiposEquipo as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('id_tipo_equipo', $equipo->id_tipo_equipo) == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_tipo_equipo')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Marca --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Marca *</label>
                        <select name="id_marca" id="marca" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('id_marca') border-red-500 @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($marcas as $marca)
                                <option value="{{ $marca->id }}" {{ old('id_marca', $equipo->id_marca) == $marca->id ? 'selected' : '' }}>
                                    {{ $marca->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_marca')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Modelo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Modelo *</label>
                        <select name="id_modelo" id="modelo" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('id_modelo') border-red-500 @enderror">
                            <option value="">Seleccione marca primero...</option>
                            @foreach($modelos as $modelo)
                                <option value="{{ $modelo->id }}" data-marca="{{ $modelo->id_marca }}" {{ old('id_modelo', $equipo->id_modelo) == $modelo->id ? 'selected' : '' }}>
                                    {{ $modelo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_modelo')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Número de Serie --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Número de Serie</label>
                        <input type="text" name="numero_serie" value="{{ old('numero_serie', $equipo->numero_serie) }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('numero_serie') border-red-500 @enderror">
                        @error('numero_serie')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tipo de Adquisición --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tipo de Adquisición</label>
                        <select name="tipo_adquisicion"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('tipo_adquisicion') border-red-500 @enderror">
                            <option value="">Seleccione...</option>
                            <option value="Propio" {{ old('tipo_adquisicion', $equipo->tipo_adquisicion) == 'Propio' ? 'selected' : '' }}>Propio</option>
                            <option value="Arrendado" {{ old('tipo_adquisicion', $equipo->tipo_adquisicion) == 'Arrendado' ? 'selected' : '' }}>Arrendado</option>
                            <option value="Prestamo" {{ old('tipo_adquisicion', $equipo->tipo_adquisicion) == 'Prestamo' ? 'selected' : '' }}>Préstamo</option>
                        </select>
                        @error('tipo_adquisicion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha de Adquisición --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Fecha de Adquisición</label>
                        <input type="date" name="fecha_adquisicion"
                            value="{{ old('fecha_adquisicion', $equipo->fecha_adquisicion?->format('Y-m-d')) }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('fecha_adquisicion') border-red-500 @enderror">
                        @error('fecha_adquisicion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Costo del Equipo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Costo
                            ({{ $setting->currency_symbol ?? 'S/' }})</label>
                        <input type="number" step="0.01" name="costo" value="{{ old('costo', $equipo->costo) }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('costo') border-red-500 @enderror"
                            placeholder="0.00">
                        @error('costo')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Número de Guía --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Número de Guía (Opcional)</label>
                        <input type="text" name="numero_guia" value="{{ old('numero_guia', $equipo->numero_guia) }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('numero_guia') border-red-500 @enderror"
                            placeholder="Ej: 001-001234">
                        @error('numero_guia')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Archivo de Guía --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Adjuntar Guía (PDF/Imagen)</label>
                        <input type="file" name="archivo_guia" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        @if($equipo->archivo_guia)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $equipo->archivo_guia) }}" target="_blank"
                                    class="text-blue-400 hover:text-blue-300 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver Guía Actual
                                </a>
                            </div>
                        @endif
                        @error('archivo_guia')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Proveedor --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Proveedor</label>
                        <input type="text" name="proveedor" value="{{ old('proveedor', $equipo->proveedor) }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('proveedor') border-red-500 @enderror">
                        @error('proveedor')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Estado *</label>
                        <select name="estado" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('estado') border-red-500 @enderror">
                            <option value="Disponible" {{ old('estado', $equipo->estado) == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="Asignado" {{ old('estado', $equipo->estado) == 'Asignado' ? 'selected' : '' }}>
                                Asignado</option>
                            <option value="En Reparacion" {{ old('estado', $equipo->estado) == 'En Reparacion' ? 'selected' : '' }}>En Reparación</option>
                            <option value="De Baja" {{ old('estado', $equipo->estado) == 'De Baja' ? 'selected' : '' }}>De
                                Baja</option>
                        </select>
                        @error('estado')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Características (Full Width) --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Características</label>
                        <textarea name="caracteristicas" rows="3"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('caracteristicas') border-red-500 @enderror"
                            placeholder="Ej: Intel i5, 8GB RAM, 256GB SSD">{{ old('caracteristicas', $equipo->caracteristicas) }}</textarea>
                        @error('caracteristicas')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Observaciones (Full Width) --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Observaciones</label>
                        <textarea name="observaciones" rows="3"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('observaciones') border-red-500 @enderror">{{ old('observaciones', $equipo->observaciones) }}</textarea>
                        @error('observaciones')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('equipos.index') }}"
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Actualizar Equipo
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Dropdown dependiente Marca -> Modelo (igual que en create)
            const marcaSelect = document.getElementById('marca');
            const modeloSelect = document.getElementById('modelo');
            const allOptions = Array.from(modeloSelect.querySelectorAll('option[data-marca]'));

            function filterModelos() {
                const marcaId = marcaSelect.value;
                modeloSelect.innerHTML = '<option value="">Seleccione...</option>';

                if (marcaId) {
                    const filteredOptions = allOptions.filter(opt => opt.dataset.marca === marcaId);
                    filteredOptions.forEach(opt => {
                        const newOption = opt.cloneNode(true);
                        modeloSelect.appendChild(newOption);
                    });
                }
            }

            marcaSelect.addEventListener('change', filterModelos);

            // Filtrar al cargar si ya hay una marca seleccionada
            if (marcaSelect.value) {
                filterModelos();
                // Restaurar el modelo seleccionado
                const selectedModeloId = '{{ old('id_modelo', $equipo->id_modelo) }}';
                if (selectedModeloId) {
                    modeloSelect.value = selectedModeloId;
                }
            }
        </script>
    @endpush
</x-app-layout>