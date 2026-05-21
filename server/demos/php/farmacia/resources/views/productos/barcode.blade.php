<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Código de Barras - {{ $producto->nombre }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .label { border: 1px dashed #ccc; padding: 20px; text-align: center; width: 300px; }
        .name { font-weight: bold; margin-bottom: 5px; font-size: 14px; }
        .price { font-size: 18px; font-weight: bold; margin-top: 5px; }
        @media print {
            .no-print { display: none; }
            body { height: auto; }
            .label { border: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #22b388; color: white; border: none; border-radius: 5px;">Imprimir Etiqueta</button>
    </div>

    <div class="label">
        <div class="name">{{ $producto->nombre }}</div>
        <div class="text-xs">{{ $producto->presentacion }}</div>
        <svg id="barcode"></svg>
        <div class="price">S/ {{ number_format($producto->precio_venta, 2) }}</div>
    </div>

    <script>
        JsBarcode("#barcode", "{{ $producto->codigo }}", {
            format: "CODE128",
            width: 2,
            height: 60,
            displayValue: true
        });
    </script>
</body>
</html>
