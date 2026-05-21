<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Página no encontrada | CotizaPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;background:#f0f2f5;min-height:100vh;display:flex;align-items:center;justify-content:center;}
        .wrap{text-align:center;padding:40px 20px;max-width:480px;}
        .code{font-size:120px;font-weight:800;color:#e2e8f0;line-height:1;margin-bottom:0;}
        .code span{color:#4ade80;}
        .title{font-size:22px;font-weight:700;color:#1e2d3d;margin:8px 0 12px;}
        .sub{font-size:14px;color:#718096;line-height:1.7;margin-bottom:32px;}
        .btn{display:inline-flex;align-items:center;gap:8px;background:#4ade80;color:#1e2d3d;padding:12px 24px;border-radius:9px;font-weight:700;font-size:14px;text-decoration:none;transition:.15s;}
        .btn:hover{background:#22c55e;}
        .logo{font-size:15px;font-weight:800;color:#1e2d3d;margin-bottom:24px;}
        .logo span{color:#4ade80;}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo">Cotiza<span>Pro</span></div>
        <div class="code">4<span>0</span>4</div>
        <h1 class="title">Página no encontrada</h1>
        <p class="sub">Lo sentimos, la página que buscas no existe o fue movida. Verifica la URL o regresa al inicio.</p>
        <a href="{{ url('/dashboard') }}" class="btn">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Ir al Dashboard
        </a>
    </div>
</body>
</html>
