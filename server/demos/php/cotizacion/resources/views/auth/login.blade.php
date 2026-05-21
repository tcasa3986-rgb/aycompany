<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name','CotizaPro') }} — Iniciar Sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;}

        /* ── LEFT PANEL ── */
        .auth-left{
            width:420px;flex-shrink:0;
            background:#1e2d3d;
            display:flex;flex-direction:column;
            padding:48px 40px;
            position:relative;overflow:hidden;
        }
        .auth-left::before{
            content:'';position:absolute;
            width:300px;height:300px;border-radius:50%;
            background:rgba(74,222,128,.06);
            top:-80px;right:-80px;
        }
        .auth-left::after{
            content:'';position:absolute;
            width:200px;height:200px;border-radius:50%;
            background:rgba(56,189,248,.05);
            bottom:60px;left:-60px;
        }
        .auth-logo{display:flex;align-items:center;gap:14px;margin-bottom:60px;position:relative;z-index:1;}
        .auth-logo-box{
            width:48px;height:48px;border-radius:10px;
            background:#172434;border:2px solid #4ade80;
            display:flex;align-items:center;justify-content:center;position:relative;
        }
        .auth-logo-box::before{content:'';position:absolute;width:22px;height:22px;border:3px solid #4ade80;border-radius:3px;}
        .auth-logo-box::after{content:'';position:absolute;width:11px;height:11px;background:#f59e0b;border-radius:2px;top:7px;left:7px;}
        .auth-logo-name{font-size:20px;font-weight:800;color:#fff;letter-spacing:-.02em;}
        .auth-logo-sub{font-size:11px;color:#8fa8c0;font-weight:400;}

        .auth-hero{flex:1;display:flex;flex-direction:column;justify-content:center;position:relative;z-index:1;}
        .auth-hero h1{font-size:28px;font-weight:800;color:#fff;line-height:1.2;margin-bottom:14px;}
        .auth-hero p{font-size:13.5px;color:#8fa8c0;line-height:1.7;margin-bottom:32px;}
        .auth-feature{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
        .auth-feature-dot{width:8px;height:8px;border-radius:50%;background:#4ade80;flex-shrink:0;}
        .auth-feature span{font-size:12.5px;color:#a0b8cc;}

        /* ── RIGHT PANEL ── */
        .auth-right{
            flex:1;background:#f0f2f5;
            display:flex;align-items:center;justify-content:center;
            padding:40px;
        }
        .auth-box{
            background:#fff;border-radius:16px;
            padding:40px;width:100%;max-width:420px;
            box-shadow:0 4px 24px rgba(0,0,0,.08);
        }
        .auth-box h2{font-size:22px;font-weight:800;color:#1e2d3d;margin-bottom:6px;}
        .auth-box .auth-sub{font-size:13px;color:#718096;margin-bottom:28px;}

        .form-group{margin-bottom:18px;}
        .form-label{display:block;font-size:11.5px;font-weight:700;color:#4a5568;margin-bottom:7px;text-transform:uppercase;letter-spacing:.05em;}
        .form-control{
            width:100%;padding:10px 14px;
            background:#f8fafc;border:1.5px solid #e2e8f0;
            border-radius:8px;color:#2d3748;font-size:13.5px;
            outline:none;transition:border .15s;font-family:inherit;
        }
        .form-control:focus{border-color:#4ade80;background:#fff;box-shadow:0 0 0 3px rgba(74,222,128,.1);}
        .form-error{font-size:11.5px;color:#e53e3e;margin-top:5px;}

        .btn-auth{
            display:flex;align-items:center;justify-content:center;gap:8px;
            width:100%;padding:12px;border-radius:9px;
            background:#4ade80;color:#1e2d3d;
            font-size:14px;font-weight:700;border:none;cursor:pointer;
            transition:background .15s;letter-spacing:.01em;margin-top:4px;
        }
        .btn-auth:hover{background:#22c55e;}

        .auth-divider{text-align:center;margin:20px 0;color:#a0aec0;font-size:12px;position:relative;}
        .auth-divider::before,.auth-divider::after{content:'';position:absolute;top:50%;width:40%;height:1px;background:#e2e8f0;}
        .auth-divider::before{left:0;}.auth-divider::after{right:0;}

        .auth-link{color:#2b6cb0;text-decoration:none;font-size:13px;font-weight:500;}
        .auth-link:hover{text-decoration:underline;}
        .auth-footer{text-align:center;margin-top:20px;font-size:12.5px;color:#718096;}

        .checkbox-row{display:flex;align-items:center;gap:8px;margin-bottom:20px;}
        .checkbox-row input{width:16px;height:16px;accent-color:#4ade80;}
        .checkbox-row label{font-size:12.5px;color:#718096;}

        .alert-info{background:#ebf8ff;border:1px solid #bee3f8;color:#2b6cb0;padding:10px 14px;border-radius:8px;font-size:12.5px;margin-bottom:16px;}
        .alert-success-sm{background:#f0fff4;border:1px solid #9ae6b4;color:#276749;padding:10px 14px;border-radius:8px;font-size:12.5px;margin-bottom:16px;}

        @media(max-width:700px){.auth-left{display:none;}.auth-right{padding:24px;}}
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="auth-logo">
            <div class="auth-logo-box"></div>
            <div>
                <div class="auth-logo-name">CotizaPro</div>
                <div class="auth-logo-sub">Sistema Cotización</div>
            </div>
        </div>
        <div class="auth-hero">
            <h1>Gestiona tus cotizaciones de forma profesional</h1>
            <p>Emite cotizaciones en segundos, haz seguimiento de clientes y genera reportes en PDF con un solo clic.</p>
            <div class="auth-feature"><div class="auth-feature-dot"></div><span>Numeración automática COT-YYYY-NNNN</span></div>
            <div class="auth-feature"><div class="auth-feature-dot"></div><span>Multi-moneda: PEN, USD, EUR</span></div>
            <div class="auth-feature"><div class="auth-feature-dot"></div><span>PDF profesional con tu logo y datos</span></div>
            <div class="auth-feature"><div class="auth-feature-dot"></div><span>Dashboard con gráficas en tiempo real</span></div>
            <div class="auth-feature"><div class="auth-feature-dot"></div><span>Descuentos por línea y globales</span></div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-box">
            <h2>Bienvenido de vuelta</h2>
            <div class="auth-sub">Ingresa tus credenciales para continuar</div>

            <x-auth-session-status class="alert-success-sm" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="tu@empresa.com">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Recordar mi sesión</label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="auth-link" style="margin-left:auto">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>
                <button type="submit" class="btn-auth">Iniciar Sesión</button>
            </form>

            @if(Route::has('register'))
            <div class="auth-footer">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="auth-link">Registrarse gratis</a>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
