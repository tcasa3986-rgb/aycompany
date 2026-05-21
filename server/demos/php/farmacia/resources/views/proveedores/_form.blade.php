@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="label">RUC</label>
        <input type="text" name="ruc" value="{{ old('ruc', $proveedor->ruc) }}" class="input">
    </div>
    <div>
        <label class="label">Razón social *</label>
        <input type="text" name="razon_social" value="{{ old('razon_social', $proveedor->razon_social) }}" class="input" required>
    </div>
    <div>
        <label class="label">Contacto</label>
        <input type="text" name="contacto" value="{{ old('contacto', $proveedor->contacto) }}" class="input">
    </div>
    <div>
        <label class="label">Teléfono</label>
        <input type="text" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}" class="input">
    </div>
    <div>
        <label class="label">Email</label>
        <input type="email" name="email" value="{{ old('email', $proveedor->email) }}" class="input">
    </div>
    <div>
        <label class="label">Dirección</label>
        <input type="text" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}" class="input">
    </div>
    <div class="md:col-span-2">
        <label class="inline-flex items-center text-sm text-gray-700">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $proveedor->activo ?? true)) class="rounded text-farmacia-500 focus:ring-farmacia-400">
            <span class="ml-2">Activo</span>
        </label>
    </div>
</div>
<div class="mt-6 flex justify-end gap-2">
    <a href="{{ route('proveedores.index') }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">Guardar</button>
</div>
