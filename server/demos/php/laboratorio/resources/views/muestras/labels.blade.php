<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Etiquetas de Muestras - {{ $orden->numero_orden }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 0;
            background: white;
            color: black;
        }
        .label-container {
            width: 50mm;
            height: 25mm;
            padding: 2mm;
            border: 1px dashed #ccc;
            margin-bottom: 5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .label-header {
            font-size: 10pt;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }
        .patient-name {
            font-size: 9pt;
            margin: 1mm 0;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sample-info {
            font-size: 8pt;
            display: flex;
            justify-content: space-between;
        }
        .barcode-placeholder {
            height: 8mm;
            background: repeating-linear-gradient(90deg, #000, #000 1px, #fff 1px, #fff 3px);
            margin-top: 1mm;
        }
        .sample-code {
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            margin-top: 1mm;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
            .label-container { border: none; margin-bottom: 0; page-break-after: always; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #ddd; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 5px;">
            🖨️ Imprimir Etiquetas
        </button>
        <p style="font-size: 12px; color: #666; margin-top: 10px;">Asegúrese de configurar su impresora térmica (50mm x 25mm)</p>
    </div>

    @foreach($orden->muestras as $muestra)
    <div class="label-container">
        <div class="label-header">
            <span>LAB-SALUD</span>
            <span>{{ date('d/m/y') }}</span>
        </div>
        <div class="patient-name">
            {{ $orden->paciente->apellido_paterno }} {{ substr($orden->paciente->nombres, 0, 1) }}.
        </div>
        <div class="sample-info">
            <span><strong>{{ $muestra->tipo_muestra }}</strong></span>
            <span>#{{ $orden->numero_orden }}</span>
        </div>
        <div class="barcode-placeholder"></div>
        <div class="sample-code">{{ $muestra->codigo_muestra }}</div>
    </div>
    @endforeach

    <script>
        // Auto print after load if desired
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
