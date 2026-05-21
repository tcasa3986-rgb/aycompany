@csrf
<div class="space-y-4">
    <div>
        <label class="label">Nombre *</label>
        <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" class="input" required>
    </div>
    <div>
        <label class="label">Descripción</label>
        <textarea name="descripcion" rows="2" class="input">{{ old('descripcion', $categoria->descripcion) }}</textarea>
    </div>
    <label class="inline-flex items-center text-sm text-gray-700">
        <input type="hidden" name="activo" value="0">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $categoria->activo ?? true)) class="rounded text-farmacia-500 focus:ring-farmacia-400">
        <span class="ml-2">Activa</span>
    </label>
</div>
<div class="mt-6 flex justify-end gap-2">
    <a href="{{ route('categorias.index') }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">Guardar</button>
</div>
