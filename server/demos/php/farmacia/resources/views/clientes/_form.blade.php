@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="label">Documento (DNI/RUC)</label>
        <input type="text" name="documento" value="{{ old('documento', $cliente->documento) }}" class="input">
    </div>
    <div>
        <label class="label">Nombres *</label>
        <input type="text" name="nombres" value="{{ old('nombres', $cliente->nombres) }}" class="input" required>
    </div>
    <div>
        <label class="label">Apellidos</label>
        <input type="text" name="apellidos" value="{{ old('apellidos', $cliente->apellidos) }}" class="input">
    </div>
    <div>
        <label class="label">Teléfono</label>
        <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" class="input">
    </div>
    <div>
        <label class="label">Email</label>
        <input type="email" name="email" value="{{ old('email', $cliente->email) }}" class="input">
    </div>
    <div>
        <label class="label">Dirección</label>
        <input type="text" name="direccion" value="{{ old('direccion', $cliente->direccion) }}" class="input">
    </div>
    <div>
        <label class="label">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento"
               value="{{ old('fecha_nacimiento', optional($cliente->fecha_nacimiento)->format('Y-m-d')) }}" class="input">
    </div>
    <div>
        <label class="label">Género</label>
        <select name="genero" class="input">
            <option value="">— Seleccione —</option>
            @foreach (['M' => 'Masculino', 'F' => 'Femenino', 'O' => 'Otro'] as $k => $v)
                <option value="{{ $k }}" @selected(old('genero', $cliente->genero) === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="label">Alergias</label>
        <textarea name="alergias" rows="2" class="input">{{ old('alergias', $cliente->alergias) }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="label">Enfermedades crónicas</label>
        <textarea name="enfermedades_cronicas" rows="2" class="input">{{ old('enfermedades_cronicas', $cliente->enfermedades_cronicas) }}</textarea>
    </div>
    <div>
        <label class="label">Puntos de fidelidad</label>
        <input type="number" name="puntos_fidelidad" value="{{ old('puntos_fidelidad', $cliente->puntos_fidelidad ?? 0) }}" class="input">
    </div>
    <div class="flex items-end">
        <label class="inline-flex items-center text-sm text-gray-700">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $cliente->activo ?? true)) class="rounded text-farmacia-500 focus:ring-farmacia-400">
            <span class="ml-2">Activo</span>
        </label>
    </div>
</div>

<div class="mt-6 flex justify-end gap-2">
    <a href="{{ route('clientes.index') }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">Guardar</button>
</div>
