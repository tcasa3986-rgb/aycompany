<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Laboratorio Clínico</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top right, var(--bg-card), var(--bg-main) 70%);
        }
        
        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 40px;
            background: rgba(26, 39, 80, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            text-align: center;
        }

        .login-brand {
            font-size: 2rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .login-subtitle {
            color: var(--text-muted);
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .alert-error {
            background: rgba(255, 71, 87, 0.1);
            color: var(--danger);
            padding: 12px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            border: 1px solid rgba(255, 71, 87, 0.3);
            font-size: 0.9rem;
        }

        .form-group {
            text-align: left;
            margin-bottom: 25px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .form-control.with-icon {
            padding-left: 45px;
        }

        .btn-block {
            width: 100%;
            padding: 14px;
            font-size: 1.05rem;
            margin-top: 10px;
        }

        .demo-credentials {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            text-align: left;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="login-brand">
            <div class="brand-icon" style="width: 48px; height: 48px; font-size: 1.5rem;">
                <i class="fa-solid fa-microscope text-white"></i>
            </div>
            <span class="text-gradient" style="font-family: var(--font-heading);">LabSalud</span>
        </div>
        <p class="login-subtitle">Sistema Integral de Gestión Analítica</p>

        @if(session('error'))
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" class="form-control with-icon" value="{{ old('email') }}" required autofocus placeholder="admin@lab.com">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" class="form-control with-icon" required placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Ingresar al Sistema <i class="fa-solid fa-arrow-right"></i></button>
        </form>

        <div class="demo-credentials">
            <strong>Credenciales de prueba:</strong><br>
            Admin: admin@lab.com / password<br>
            Recepción: recepcion@lab.com / password
        </div>
    </div>
</div>

</body>
</html>
