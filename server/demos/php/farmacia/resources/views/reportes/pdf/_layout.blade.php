<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Reporte')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 16px; color: #199172; margin: 0 0 4px; }
        h2 { font-size: 12px; color: #4b5563; margin: 0 0 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #ecfdf6; color: #199172; text-align: left; padding: 6px; border-bottom: 1px solid #d1fae9; text-transform: uppercase; font-size: 9px; }
        td { padding: 6px; border-bottom: 1px solid #f3f4f6; }
        .right { text-align: right; }
        .footer { margin-top: 16px; font-size: 9px; color: #6b7280; text-align: right; }
        .totals td { font-weight: bold; background: #f9fafb; }
    </style>
</head>
<body>
    <h1>@yield('title', 'Reporte') · ERP Farmacia</h1>
    <h2>@yield('subtitle', '')</h2>
    @yield('content')
    <div class="footer">Generado el {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
