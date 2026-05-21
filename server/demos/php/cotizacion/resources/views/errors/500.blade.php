<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Error del servidor | CotizaPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;background:#f0f2f5;min-height:100vh;display:flex;align-items:center;justify-content:center;}
        .wrap{text-align:center;padding:40px 20px;max-width:480px;}
        .code{font-size:120px;font-weight:800;color:#e2e8f0;line-height:1;}
        .code span{color:#f87171;}
        .title{font-size:22px;font-weight:700;color:#1e2d3d;margin:8px 0 12px;}
        .sub{font-size:14px;color:#718096;line-height:1.7;margin-bottom:32px;}
        .btn{display:inline-flex;align-items:center;gap:8px;background:#4ade80;color:#1e2d3d;padding:12px 24px;border-radius:9px;font-weight:700;font-size:14px;text-decoration:none;}
        .btn:hover{background:#22c55e;}
        .logo{font-size:15px;font-weight:800;color:#1e2d3d;margin-bottom:24px;}
        .logo span{color:#4ade80;}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo">Cotiza<span>Pro</span></div>
        <div class="code">5<span>0</span>0</div>
        <h1 class="title">Error interno del servidor</h1>
        <p class="sub">Algo salió mal en nuestro servidor. Por favor, intente de nuevo más tarde o contacte al administrador del sistema.</p>
        <a href="{{ url('/dashboard') }}" class="btn">Regresar al inicio</a>
    </div>
</body>
</html>
