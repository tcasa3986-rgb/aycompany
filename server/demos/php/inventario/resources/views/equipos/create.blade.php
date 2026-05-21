<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Registrar Nuevo Equipo</h2>
            <a href="{{ route('equipos.index') }}"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            <form action="{{ route('equipos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Sucursal --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Sucursal *</label>
                        <select name="id_sucursal" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('id_sucursal') border-red-500 @enderror">
                            <option value="">Seleccione...</option>
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

                    {{-- Código Inventario --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Código de Inventario *</label>
                        <input type="text" name="codigo_inventario" value="{{ old('codigo_inventario') }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('codigo_inventario') border-red-500 @enderror">
                        @error('codigo_inventario')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tipo Equipo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tipo de Equipo *</label>
                        <select name="id_tipo_equipo" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('id_tipo_equipo') border-red-500 @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($tiposEquipo as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('id_tipo_equipo') == $tipo->id ? 'selected' : '' }}>
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
                                <option value="{{ $marca->id }}" {{ old('id_marca') == $marca->id ? 'selected' : '' }}>
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
                            <option value="">Seleccione primero una marca...</option>
                        </select>
                        @error('id_modelo')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Número de Serie --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Número de Serie *</label>
                        <input type="text" name="numero_serie" value="{{ old('numero_serie') }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('numero_serie') border-red-500 @enderror">
                        @error('numero_serie')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tipo Adquisición --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tipo de Adquisición *</label>
                        <select name="tipo_adquisicion" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('tipo_adquisicion') border-red-500 @enderror">
                            <option value="">Seleccione...</option>
                            <option value="Propio" {{ old('tipo_adquisicion') == 'Propio' ? 'selected' : '' }}>Propio
                            </option>
                            <option value="Arrendado" {{ old('tipo_adquisicion') == 'Arrendado' ? 'selected' : '' }}>
                                Arrendado</option>
                            <option value="Prestamo" {{ old('tipo_adquisicion') == 'Prestamo' ? 'selected' : '' }}>
                                Préstamo</option>
                        </select>
                        @error('tipo_adquisicion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha Adquisición --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Fecha de Adquisición</label>
                        <input type="date" name="fecha_adquisicion" value="{{ old('fecha_adquisicion') }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('fecha_adquisicion') border-red-500 @enderror">
                        @error('fecha_adquisicion')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Costo del Equipo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Costo
                            ({{ $setting->currency_symbol ?? 'S/' }})</label>
                        <input type="number" step="0.01" name="costo" value="{{ old('costo') }}"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('costo') border-red-500 @enderror"
                            placeholder="0.00">
                        @error('costo')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Número de Guía --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Número de Guía (Opcional)</label>
                        <input type="text" name="numero_guia" value="{{ old('numero_guia') }}"
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
                        @error('archivo_guia')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Proveedor --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Proveedor</label>
                        <input type="text" name="proveedor" value="{{ old('proveedor') }}"
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
                            <option value="Disponible" {{ old('estado') == 'Disponible' ? 'selected' : '' }}>Disponible
                            </option>
                            <option value="Asignado" {{ old('estado') == 'Asignado' ? 'selected' : '' }}>Asignado</option>
                            <option value="En Reparacion" {{ old('estado') == 'En Reparacion' ? 'selected' : '' }}>En
                                Reparación</option>
                            <option value="De Baja" {{ old('estado') == 'De Baja' ? 'selected' : '' }}>De Baja</option>
                        </select>
                        @error('estado')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Características --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Características</label>
                        <textarea name="caracteristicas" rows="3"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('caracteristicas') border-red-500 @enderror">{{ old('caracteristicas') }}</textarea>
                        @error('caracteristicas')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Observaciones --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Observaciones</label>
                        <textarea name="observaciones" rows="3"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
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
                        Guardar Equipo
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('marca').addEventListener('change', function () {
                const marcaId = this.value;
                const modeloSelect = document.getElementById('modelo');

                modeloSelect.innerHTML = '<option value="">Cargando...</option>';

                if (marcaId) {
                    fetch(`/api/modelos/${marcaId}`)
                        .then(response => response.json())
                        .then(data => {
                            modeloSelect.innerHTML = '<option value="">Seleccione...</option>';
                            data.forEach(modelo => {
                                const option = document.createElement('option');
                                option.value = modelo.id;
                                option.textContent = modelo.nombre;
                                modeloSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            modeloSelect.innerHTML = '<option value="">Error al cargar modelos</option>';
                        });
                } else {
                    modeloSelect.innerHTML = '<option value="">Seleccione primero una marca...</option>';
                }
            });
        </script>
    @endpush
</x-app-layout>