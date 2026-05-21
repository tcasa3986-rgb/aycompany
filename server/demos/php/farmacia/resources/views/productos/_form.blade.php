@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="label">Código *</label>
        <input type="text" name="codigo" value="{{ old('codigo', $producto->codigo) }}" class="input" required>
    </div>
    <div>
        <label class="label">Nombre *</label>
        <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" class="input" required>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Principio activo</label>
            <input type="text" name="principio_activo" value="{{ old('principio_activo', $producto->principio_activo) }}" class="input">
        </div>
        <div>
            <label class="label">Código ATC</label>
            <input type="text" name="codigo_atc" value="{{ old('codigo_atc', $producto->codigo_atc) }}" class="input" placeholder="Ej: N02BE01">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="label">Presentación</label>
            <input type="text" name="presentacion" value="{{ old('presentacion', $producto->presentacion) }}" class="input" placeholder="Tableta, jarabe...">
        </div>
        <div>
            <label class="label">Concentración</label>
            <input type="text" name="concentracion" value="{{ old('concentracion', $producto->concentracion) }}" class="input" placeholder="500 mg, 250 ml...">
        </div>
    </div>
    <div>
        <label class="label">Categoría</label>
        <select name="categoria_id" class="input">
            <option value="">— Sin categoría —</option>
            @foreach ($categorias as $c)
                <option value="{{ $c->id }}" @selected(old('categoria_id', $producto->categoria_id) == $c->id)>{{ $c->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Proveedor</label>
        <select name="proveedor_id" class="input">
            <option value="">— Sin proveedor —</option>
            @foreach ($proveedores as $p)
                <option value="{{ $p->id }}" @selected(old('proveedor_id', $producto->proveedor_id) == $p->id)>{{ $p->razon_social }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Tipo *</label>
        <select name="tipo" class="input" required>
            @foreach (['generico','marca','controlado','refrigerado','cosmetico','insumo'] as $t)
                <option value="{{ $t }}" @selected(old('tipo', $producto->tipo) == $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Ubicación</label>
        <input type="text" name="ubicacion" value="{{ old('ubicacion', $producto->ubicacion) }}" class="input">
    </div>
    <div>
        <label class="label">Precio de compra *</label>
        <input type="number" step="0.01" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra) }}" class="input" required>
    </div>
    <div>
        <label class="label">Precio de venta *</label>
        <input type="number" step="0.01" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta) }}" class="input" required>
    </div>
    <div class="md:col-span-2 mt-4">
        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3 border-b pb-1">Inventario por Sucursal</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($sucursales as $suc)
                @php
                    $inv = $producto->sucursales->where('id', $suc->id)->first();
                    $stock = $inv ? $inv->pivot->stock : 0;
                    $min = $inv ? $inv->pivot->stock_minimo : 5;
                    $loc = $inv ? $inv->pivot->ubicacion : '';
                @endphp
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="font-bold text-farmacia-700 mb-2 flex items-center gap-2">
                        <x-icon name="box" class="h-4 w-4" />
                        {{ $suc->nombre }}
                    </p>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase">Stock Actual</label>
                            <input type="number" name="sucursales[{{ $suc->id }}][stock]" value="{{ old("sucursales.{$suc->id}.stock", $stock) }}" class="input py-1 text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase">Stock Mínimo</label>
                            <input type="number" name="sucursales[{{ $suc->id }}][stock_minimo]" value="{{ old("sucursales.{$suc->id}.stock_minimo", $min) }}" class="input py-1 text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">Ubicación</label>
                            <input type="text" name="sucursales[{{ $suc->id }}][ubicacion]" value="{{ old("sucursales.{$suc->id}.ubicacion", $loc) }}" class="input py-1 text-sm" placeholder="Estante/Gaveta">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="md:col-span-2 flex flex-wrap gap-6 mt-1">
        <label class="inline-flex items-center text-sm text-gray-700">
            <input type="hidden" name="requiere_receta" value="0">
            <input type="checkbox" name="requiere_receta" value="1" @checked(old('requiere_receta', $producto->requiere_receta)) class="rounded text-farmacia-500 focus:ring-farmacia-400">
            <span class="ml-2">Requiere receta médica</span>
        </label>
        <label class="inline-flex items-center text-sm text-gray-700">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $producto->activo ?? true)) class="rounded text-farmacia-500 focus:ring-farmacia-400">
            <span class="ml-2">Activo</span>
        </label>
    </div>
</div>

<div class="mt-6 flex justify-end gap-2">
    <a href="{{ route('productos.index') }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">Guardar</button>
</div>
