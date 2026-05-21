<x-app-layout>
    <x-slot name="title">Mi Perfil</x-slot>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:900px;">

        {{-- Actualizar datos --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Información Personal</span></div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PATCH')
                <div class="form-group">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Correo Electrónico *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div class="alert alert-error" style="font-size:12px">
                        Tu email no está verificado. <a href="{{ route('verification.send') }}" style="color:inherit;font-weight:700">Reenviar verificación</a>
                    </div>
                @endif
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>

        {{-- Cambiar contraseña --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Cambiar Contraseña</span></div>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Contraseña Actual *</label>
                    <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                    @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nueva Contraseña *</label>
                    <input type="password" name="password" class="form-control" required autocomplete="new-password">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmar Contraseña *</label>
                    <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
            </form>
        </div>

        {{-- Zona peligrosa --}}
        <div class="card" style="border-color:#fed7d7;">
            <div class="card-header"><span class="card-title" style="color:var(--danger)">Zona Peligrosa</span></div>
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:14px;">
                Una vez que tu cuenta sea eliminada, todos sus datos serán eliminados permanentemente.
            </p>
            <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('¿Estás seguro? Esta acción no se puede deshacer.')">
                @csrf @method('DELETE')
                <div class="form-group">
                    <label class="form-label">Confirma tu contraseña</label>
                    <input type="password" name="password" class="form-control" required placeholder="Tu contraseña actual">
                    @error('password', 'userDeletion')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-danger">Eliminar Cuenta</button>
            </form>
        </div>
    </div>
</x-app-layout>
