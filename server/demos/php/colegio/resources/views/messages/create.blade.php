@extends('layouts.app')
@section('title', 'Nuevo Mensaje')
@section('page-title', 'Redactar Mensaje')

@section('content')
<div style="max-width:700px;">
<div style="margin-bottom:16px;">
    <a href="{{ route('mensajes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<form method="POST" action="{{ route('mensajes.store') }}">
@csrf
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-envelope" style="color:#3b82f6;margin-right:8px;"></i>Nuevo Mensaje</span>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Para *</label>
            <select name="destinatario_id" class="form-control {{ $errors->has('destinatario_id') ? 'is-invalid':'' }}" required>
                <option value="">Seleccionar destinatario...</option>
                @foreach($usuarios as $u)
                    <option value="{{ $u->id }}" {{ old('destinatario_id')==$u->id ? 'selected':'' }}>
                        {{ $u->name }} — {{ ucfirst($u->role) }}
                    </option>
                @endforeach
            </select>
            @error('destinatario_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Asunto *</label>
            <input type="text" name="asunto" class="form-control {{ $errors->has('asunto') ? 'is-invalid':'' }}"
                value="{{ old('asunto') }}" required placeholder="Escribe el asunto...">
            @error('asunto') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Mensaje *</label>
            <textarea name="cuerpo" class="form-control {{ $errors->has('cuerpo') ? 'is-invalid':'' }}"
                rows="8" required placeholder="Escribe tu mensaje aquí...">{{ old('cuerpo') }}</textarea>
            @error('cuerpo') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>
<div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px;">
    <a href="{{ route('mensajes.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar Mensaje</button>
</div>
</form>
</div>
@endsection
