<x-app-layout>
    <x-slot name="title">Editar Cliente</x-slot>
    <x-slot name="actions">
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">← Volver</a>
    </x-slot>

    <div style="max-width:700px;">
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Editar: {{ $client->name }}</div>
                    <div class="card-sub">Actualice la información del cliente</div>
                </div>
            </div>

            <form method="POST" action="{{ route('clients.update', $client) }}">
                @csrf @method('PUT')
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Nombre / Razón Social *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $client->name) }}" required>
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">RUC / DNI / NIT</label>
                        <input type="text" name="document_number" class="form-control" value="{{ old('document_number', $client->document_number) }}" maxlength="20">
                        @error('document_number')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}">
                        @error('email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}" maxlength="30">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $client->address) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Notas internas</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $client->notes ?? '') }}</textarea>
                </div>

                <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border);margin-top:8px;">
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancelar</a>
                    <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('¿Eliminar este cliente?')" style="margin-left:auto">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar Cliente</button>
                    </form>
                </div>
            </form>
        </div>

        {{-- Cotizaciones del cliente --}}
        @if($client->quotations && $client->quotations->count() > 0)
        <div class="card" style="margin-top:16px;">
            <div class="card-header">
                <div class="card-title">Cotizaciones de este Cliente</div>
                <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm">+ Nueva</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Número</th><th>Fecha</th><th style="text-align:right">Total</th><th>Estado</th></tr></thead>
                    <tbody>
                        @foreach($client->quotations->take(10) as $q)
                        <tr>
                            <td><a href="{{ route('quotations.show',$q) }}" style="color:#2b6cb0;font-weight:600;text-decoration:none;">{{ $q->quotation_number }}</a></td>
                            <td style="color:var(--text-muted);font-size:12.5px">{{ $q->issue_date->format('d/m/Y') }}</td>
                            <td style="text-align:right;font-weight:600">{{ $q->currency_symbol }} {{ number_format($q->total,2) }}</td>
                            <td><span class="badge {{ $q->status_color }}">{{ $q->status }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
