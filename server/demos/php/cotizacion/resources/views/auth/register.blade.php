<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name','CotizaPro') }} — Registrarse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;}
        .auth-left{width:380px;flex-shrink:0;background:#1e2d3d;display:flex;flex-direction:column;padding:48px 36px;position:relative;overflow:hidden;}
        .auth-left::before{content:'';position:absolute;width:300px;height:300px;border-radius:50%;background:rgba(74,222,128,.06);top:-80px;right:-80px;}
        .auth-logo{display:flex;align-items:center;gap:14px;margin-bottom:50px;position:relative;z-index:1;}
        .auth-logo-box{width:44px;height:44px;border-radius:10px;background:#172434;border:2px solid #4ade80;display:flex;align-items:center;justify-content:center;position:relative;}
        .auth-logo-box::before{content:'';position:absolute;width:20px;height:20px;border:3px solid #4ade80;border-radius:3px;}
        .auth-logo-box::after{content:'';position:absolute;width:10px;height:10px;background:#f59e0b;border-radius:2px;top:7px;left:7px;}
        .auth-logo-name{font-size:18px;font-weight:800;color:#fff;}
        .auth-logo-sub{font-size:10px;color:#8fa8c0;}
        .auth-hero{flex:1;display:flex;flex-direction:column;justify-content:center;position:relative;z-index:1;}
        .auth-hero h1{font-size:24px;font-weight:800;color:#fff;margin-bottom:12px;}
        .auth-hero p{font-size:13px;color:#8fa8c0;line-height:1.7;}
        .auth-right{flex:1;background:#f0f2f5;display:flex;align-items:center;justify-content:center;padding:40px;}
        .auth-box{background:#fff;border-radius:16px;padding:36px;width:100%;max-width:460px;box-shadow:0 4px 24px rgba(0,0,0,.08);}
        .auth-box h2{font-size:20px;font-weight:800;color:#1e2d3d;margin-bottom:4px;}
        .auth-sub{font-size:13px;color:#718096;margin-bottom:24px;}
        .form-group{margin-bottom:16px;}
        .form-label{display:block;font-size:11px;font-weight:700;color:#4a5568;margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;}
        .form-control{width:100%;padding:9px 13px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:8px;color:#2d3748;font-size:13px;outline:none;transition:border .15s;font-family:inherit;}
        .form-control:focus{border-color:#4ade80;background:#fff;box-shadow:0 0 0 3px rgba(74,222,128,.1);}
        .form-row{display:grid;gap:12px;grid-template-columns:1fr 1fr;}
        .form-error{font-size:11px;color:#e53e3e;margin-top:4px;}
        .btn-auth{display:flex;align-items:center;justify-content:center;width:100%;padding:11px;border-radius:9px;background:#4ade80;color:#1e2d3d;font-size:13.5px;font-weight:700;border:none;cursor:pointer;transition:background .15s;margin-top:4px;}
        .btn-auth:hover{background:#22c55e;}
        .auth-footer{text-align:center;margin-top:18px;font-size:12.5px;color:#718096;}
        .auth-link{color:#2b6cb0;text-decoration:none;font-weight:500;}
        .auth-link:hover{text-decoration:underline;}
        @media(max-width:700px){.auth-left{display:none;}}
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="auth-logo">
            <div class="auth-logo-box"></div>
            <div><div class="auth-logo-name">CotizaPro</div><div class="auth-logo-sub">Sistema Cotización</div></div>
        </div>
        <div class="auth-hero">
            <h1>Crea tu cuenta en segundos</h1>
            <p>Accede a todas las funcionalidades del sistema: cotizaciones, clientes, reportes y más, sin límites.</p>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-box">
            <h2>Crear Cuenta</h2>
            <div class="auth-sub">Completa el formulario para comenzar</div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Tu nombre">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="tu@empresa.com">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required placeholder="Mín. 8 caracteres">
                        @error('password')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmar</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Repetir contraseña">
                    </div>
                </div>
                <button type="submit" class="btn-auth">Crear Cuenta</button>
            </form>

            <div class="auth-footer">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="auth-link">Iniciar sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
