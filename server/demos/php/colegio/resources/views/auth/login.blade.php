<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Colegio — Iniciar Sesión</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            top: -200px; right: -200px;
        }
        body::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -150px; left: -150px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
            position: relative;
            z-index: 1;
        }
        .logo-area {
            text-align: center;
            margin-bottom: 36px;
        }
        .logo-circle {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(59,130,246,0.4);
        }
        .logo-circle i { color: white; font-size: 32px; }
        .logo-area h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1e3a8a;
        }
        .logo-area p {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 4px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 15px;
        }
        .input-wrap input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #334155;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .input-wrap input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }
        .input-wrap input.is-invalid { border-color: #ef4444; }
        .error-msg {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
        }
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .remember-row label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
        }
        .remember-row input[type="checkbox"] { accent-color: #3b82f6; }
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
            letter-spacing: .5px;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(59,130,246,0.4);
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="logo-area">
        <div class="logo-circle">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <h1>CRM Colegio</h1>
        <p>Sistema de Gestión Escolar</p>
    </div>

    @if ($errors->any())
    <div class="alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label>Correo Electrónico</label>
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="admin@colegio.edu.pe"
                    value="{{ old('email') }}"
                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                    autocomplete="email" required>
            </div>
            @error('email') <div class="error-msg">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>Contraseña</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="••••••••"
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                    autocomplete="current-password" required>
            </div>
            @error('password') <div class="error-msg">{{ $message }}</div> @enderror
        </div>

        <div class="remember-row">
            <label>
                <input type="checkbox" name="remember"> Recordar sesión
            </label>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </button>
    </form>

    <div class="footer-text">
        CRM Colegio &copy; {{ date('Y') }} — Sistema Educativo
    </div>
</div>
</body>
</html>
