<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - CRM Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a2035 0%, #2a3150 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo .icon-wrap {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 8px 20px rgba(13,110,253,0.35);
        }
        .login-logo h1 { font-size: 1.6rem; font-weight: 700; color: #1a2035; margin-bottom: 4px; }
        .login-logo p { color: #6c757d; font-size: 0.9rem; }
        .form-control { border-radius: 10px; padding: 0.65rem 1rem; border-color: #dee2e6; }
        .form-control:focus { box-shadow: 0 0 0 3px rgba(13,110,253,0.15); border-color: #0d6efd; }
        .btn-login {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border: none;
            border-radius: 10px;
            padding: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .input-group-text { border-radius: 10px 0 0 10px; background: #f8f9fa; border-color: #dee2e6; }
        .footer-text { text-align: center; font-size: 0.8rem; color: #adb5bd; margin-top: 2rem; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <div class="icon-wrap"><i class="bi bi-truck"></i></div>
        <h1>CRM Delivery</h1>
        <p>Sistema de Gestión de Entregas</p>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Correo Electrónico</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="admin@crm.com" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" id="passwordField"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="••••••••" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePwd()">
                    <i class="bi bi-eye" id="pwdIcon"></i>
                </button>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label text-muted" for="remember">Recordarme</label>
            </div>
            @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-primary text-decoration-none small">¿Olvidaste tu contraseña?</a>
            @endif
        </div>
        <button type="submit" class="btn btn-primary btn-login w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al Sistema
        </button>
    </form>

    <div class="footer-text">
        <i class="bi bi-shield-lock me-1"></i>Acceso restringido al personal autorizado
    </div>
</div>

<script>
function togglePwd() {
    const f = document.getElementById('passwordField');
    const i = document.getElementById('pwdIcon');
    if (f.type === 'password') {
        f.type = 'text';
        i.className = 'bi bi-eye-slash';
    } else {
        f.type = 'password';
        i.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
