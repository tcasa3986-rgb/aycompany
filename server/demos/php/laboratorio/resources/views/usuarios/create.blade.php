@extends('layouts.app')
@section('title', 'Nuevo Usuario')
@section('content')
<div class="page-header">
    <div><h1 class="page-title text-gradient">Crear Usuario</h1></div>
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>
<div class="card" style="max-width:550px;">
    <div class="card-header"><span class="card-title">Datos del Usuario</span></div>
    <form method="POST" action="{{ route('usuarios.store') }}" style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
        @csrf
        @if($errors->any())
            <div style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;color:var(--danger);">
                <ul style="margin:0;padding-left:1.2rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <div class="form-group">
            <label>Nombre completo <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
            <label>Rol <span class="text-danger">*</span></label>
            <select name="role" class="form-control" required>
                <option value="">— Seleccionar rol —</option>
                @foreach($roles as $rol)
                    <option value="{{ $rol->name }}" {{ old('role') === $rol->name ? 'selected' : '' }}>{{ $rol->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Contraseña <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Confirmar Contraseña <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Crear Usuario</button>
        </div>
    </form>
</div>
@endsection
@push('styles')
<style>
.form-group label { display:block;margin-bottom:6px;font-size:0.9rem;color:var(--text-secondary); }
.form-control { width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;box-sizing:border-box;font-size:0.9rem; }
.form-control:focus { outline:none;border-color:var(--accent-primary); }
</style>
@endpush
