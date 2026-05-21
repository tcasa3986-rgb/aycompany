<x-app-layout>
    <x-slot name="title">Editar Empresa</x-slot>
    <x-slot name="actions">
        <a href="{{ route('companies.index') }}" class="btn btn-secondary">← Volver</a>
    </x-slot>
    <div style="max-width:700px;">
        <div class="card">
            <div class="card-header">
                <div><div class="card-title">Editar: {{ $company->name }}</div></div>
            </div>
            <form method="POST" action="{{ route('companies.update', $company) }}">
                @csrf @method('PUT')
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Nombre / Razón Social *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">RUC / NIT</label>
                        <input type="text" name="document_number" class="form-control" value="{{ old('document_number', $company->document_number) }}" maxlength="20">
                    </div>
                </div>
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}" maxlength="30">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $company->address) }}">
                </div>
                <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border);margin-top:8px;">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <a href="{{ route('companies.index') }}" class="btn btn-secondary">Cancelar</a>
                    <form method="POST" action="{{ route('companies.destroy', $company) }}" onsubmit="return confirm('¿Eliminar esta empresa?')" style="margin-left:auto">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
