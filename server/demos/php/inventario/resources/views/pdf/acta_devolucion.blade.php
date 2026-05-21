<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Acta de Devolución de Equipo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ef4444;
            /* Rojo para Devolución */
            padding-bottom: 10px;
        }

        .header h1 {
            color: #ef4444;
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 10px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            background-color: #fef2f2;
            color: #991b1b;
            padding: 8px;
            font-weight: bold;
            border-left: 4px solid #ef4444;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            width: 30%;
            color: #4b5563;
            font-weight: bold;
            background-color: #f9fafb;
        }

        .terms {
            font-size: 11px;
            text-align: justify;
            margin-bottom: 40px;
            padding: 15px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .signatures {
            margin-top: 60px;
            width: 100%;
        }

        .signature-box {
            width: 45%;
            float: left;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin: 0 20px 10px;
            padding-top: 5px;
        }

        .signature-box:last-child {
            float: right;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Acta de Devolución de Equipo</h1>
        <p>Documento Generado el: {{ date('d/m/Y H:i') }}</p>
        <p>Código de Asignación: #{{ str_pad($asignacion->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="section">
        <div class="section-title">Datos del Colaborador (Devuelve)</div>
        <table>
            <tr>
                <th>Nombre Completo:</th>
                <td>{{ $asignacion->empleado->nombreCompleto() }}</td>
            </tr>
            <tr>
                <th>DNI:</th>
                <td>{{ $asignacion->empleado->dni }}</td>
            </tr>
            <tr>
                <th>Área / Departamento:</th>
                <td>{{ $asignacion->empleado->area->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Cargo:</th>
                <td>{{ $asignacion->empleado->cargo->nombre ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalles del Equipo Devuelto</div>
        <table>
            <tr>
                <th>Tipo de Equipo:</th>
                <td>{{ $asignacion->equipo->tipoEquipo->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Marca:</th>
                <td>{{ $asignacion->equipo->marca->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Modelo:</th>
                <td>{{ $asignacion->equipo->modelo->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Número de Serie:</th>
                <td>{{ $asignacion->equipo->numero_serie }}</td>
            </tr>
            <tr>
                <th>Código Inventario:</th>
                <td><strong>{{ $asignacion->equipo->codigo_inventario }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalles de la Devolución</div>
        <table>
            <tr>
                <th>Fecha de Entrega Inicial:</th>
                <td>{{ $asignacion->fecha_entrega->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Fecha de Devolución:</th>
                <td>{{ $asignacion->fecha_devolucion ? $asignacion->fecha_devolucion->format('d/m/Y H:i') : 'N/A' }}
                </td>
            </tr>
            <tr>
                <th>Estado Final de Asignación:</th>
                <td>{{ $asignacion->estado_asignacion }}</td>
            </tr>
            @if($asignacion->observaciones_devolucion)
                <tr>
                    <th>Observaciones de Devolución:</th>
                    <td>{{ $asignacion->observaciones_devolucion }}</td>
                </tr>
            @endif
            @if($asignacion->motivo_anulacion)
                <tr>
                    <th>Motivo de Anulación:</th>
                    <td>{{ $asignacion->motivo_anulacion }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">Conformidad</div>
        <div class="terms">
            <p>Por medio del presente documento, el área de TI certifica la recepción del equipo detallado
                anteriormente.</p>
            <p>El equipo ha sido inspeccionado y recibido en las condiciones descritas en las observaciones.</p>
            <p>Con la firma de este documento, el colaborador queda liberado de la responsabilidad sobre el equipo
                devuelto, salvo observaciones pendientes detalladas en este acta.</p>
        </div>
    </div>

    <div class="signatures clearfix">
        <div class="signature-box">
            <div style="height: 60px;"></div> <!-- Espacio para firma -->
            <div class="signature-line"></div>
            <strong>Recibido por (TI)</strong><br>
            {{ auth()->user()->name }}
        </div>
        <div class="signature-box">
            <div style="height: 60px;"></div> <!-- Espacio para firma -->
            <div class="signature-line"></div>
            <strong>Entregado por (Colaborador)</strong><br>
            {{ $asignacion->empleado->nombreCompleto() }}<br>
            DNI: {{ $asignacion->empleado->dni }}
        </div>
    </div>

    <div class="footer">
        Sistema de Gestión de Inventario TI - {{ date('Y') }}
    </div>
</body>

</html>