<x-app-layout>
    <x-slot name="title">Nuevo Cliente</x-slot>
    <x-slot name="actions">
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">← Volver</a>
    </x-slot>

    <div style="max-width:700px;">
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Datos del Cliente</div>
                    <div class="card-sub">Complete la información del cliente</div>
                </div>
            </div>

            <form method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Nombre / Razón Social *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Ej: Corporación Andina S.A.C.">
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">RUC / DNI / NIT</label>
                        <input type="text" name="document_number" class="form-control" value="{{ old('document_number') }}" placeholder="20512345678" maxlength="20">
                        @error('document_number')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="contacto@empresa.com">
                        @error('email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+51 01 234-5678" maxlength="30">
                        @error('phone')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="Av. Principal 123, Lima">
                    @error('address')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Notas internas</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Observaciones o notas sobre este cliente...">{{ old('notes') }}</textarea>
                </div>

                <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border);margin-top:8px;">
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Guardar Cliente
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
