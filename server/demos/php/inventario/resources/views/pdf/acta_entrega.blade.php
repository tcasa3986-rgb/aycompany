<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Acta de Entrega de Equipo</title>
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
            border-bottom: 2px solid #1e40af;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #1e40af;
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
            background-color: #f3f4f6;
            padding: 8px;
            font-weight: bold;
            border-left: 4px solid #1e40af;
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
        <h1>Acta de Entrega de Equipo</h1>
        <p>Documento Generado el: {{ date('d/m/Y H:i') }}</p>
        <p>Código de Asignación: #{{ str_pad($asignacion->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="section">
        <div class="section-title">Datos del Colaborador</div>
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
            <tr>
                <th>Sucursal:</th>
                <td>{{ $asignacion->empleado->sucursal->nombre ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalles del Equipo Asignado</div>
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
            <tr>
                <th>Estado del Equipo:</th>
                <td>{{ $asignacion->equipo->estado }}</td>
            </tr>
            @if($asignacion->observaciones_entrega)
                <tr>
                    <th>Observaciones de Entrega:</th>
                    <td>{{ $asignacion->observaciones_entrega }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">Términos y Condiciones de Uso</div>
        <div class="terms">
            <p>Por medio del presente documento, el colaborador declara recibir el equipo informático detallado
                anteriormente en perfectas condiciones de funcionamiento y se compromete a:</p>
            <ol>
                <li>Hacer uso del equipo asignado única y exclusivamente para fines laborales relacionados con sus
                    funciones en la empresa.</li>
                <li>Mantener el equipo en buen estado, siendo responsable de su custodia y cuidado.</li>
                <li>No instalar software no autorizado ni realizar modificaciones en la configuración del sistema sin
                    previa autorización del área de TI.</li>
                <li>Reportar inmediatamente cualquier fallo, pérdida o robo del equipo al departamento de Soporte
                    Técnico.</li>
                <li>Devolver el equipo en las mismas condiciones en que fue recibido (salvo el desgaste natural por uso)
                    al finalizar la relación laboral o cuando la empresa lo requiera.</li>
            </ol>
            <p>La empresa se reserva el derecho de auditar el equipo en cualquier momento para verificar su estado y el
                cumplimiento de las políticas de uso.</p>
        </div>
    </div>

    <div class="signatures clearfix">
        <div class="signature-box">
            <div style="height: 60px;"></div> <!-- Espacio para firma -->
            <div class="signature-line"></div>
            <strong>Entregado por (TI)</strong><br>
            {{ auth()->user()->name }}
        </div>
        <div class="signature-box">
            <div style="height: 60px;"></div> <!-- Espacio para firma -->
            <div class="signature-line"></div>
            <strong>Recibido por (Colaborador)</strong><br>
            {{ $asignacion->empleado->nombreCompleto() }}<br>
            DNI: {{ $asignacion->empleado->dni }}
        </div>
    </div>

    <div class="footer">
        Sistema de Gestión de Inventario TI - {{ date('Y') }}
    </div>
</body>

</html>