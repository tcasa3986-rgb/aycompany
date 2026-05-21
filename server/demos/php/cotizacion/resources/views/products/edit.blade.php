<x-app-layout>
    <x-slot name="title">Editar Producto</x-slot>
    <x-slot name="actions">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">← Volver</a>
    </x-slot>

    <div style="max-width:680px;">
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Editar: {{ Str::limit($product->name, 40) }}</div>
                    <div class="card-sub">Actualice la información del producto</div>
                </div>
            </div>

            <form method="POST" action="{{ route('products.update', $product) }}">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Precio Unitario ({{ $globalSym }}) *</label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ old('price', $product->price) }}" required>
                        @error('price')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unidad de Medida</label>
                        <select name="unit" class="form-control">
                            @php $units = ['und'=>'Unidad (und)','hr'=>'Hora (hr)','día'=>'Día','mes'=>'Mes','año'=>'Año','kg'=>'Kilogramo (kg)','m'=>'Metro (m)','m²'=>'Metro cuadrado (m²)','m³'=>'Metro cúbico (m³)','lt'=>'Litro (lt)','caja'=>'Caja','paq'=>'Paquete','servicio'=>'Servicio','proyecto'=>'Proyecto','sesión'=>'Sesión','global'=>'Global'] @endphp
                            <option value="">— Sin unidad —</option>
                            @foreach($units as $val => $label)
                                <option value="{{ $val }}" {{ old('unit', $product->unit) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border);margin-top:8px;">
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('¿Eliminar este producto?')" style="margin-left:auto">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
