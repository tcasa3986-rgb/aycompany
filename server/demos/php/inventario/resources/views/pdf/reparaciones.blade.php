<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Reparaciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            color: #1e40af;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #1e40af;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }

        td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-pendiente {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-enproceso {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-completada {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-cancelada {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte de Reparaciones</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
        <p>Total de reparaciones: {{ $reparaciones->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Equipo</th>
                <th>Problema</th>
                <th>F. Ingreso</th>
                <th>F. Salida</th>
                <th>Técnico</th>
                <th>Costo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reparaciones as $reparacion)
                <tr>
                    <td>{{ $reparacion->equipo->codigo_inventario }}</td>
                    <td style="max-width: 150px;">{{ Str::limit($reparacion->descripcion_problema, 40) }}</td>
                    <td>{{ $reparacion->fecha_ingreso->format('d/m/Y') }}</td>
                    <td>{{ $reparacion->fecha_salida?->format('d/m/Y') ?: '-' }}</td>
                    <td>{{ $reparacion->tecnico_asignado ?: '-' }}</td>
                    <td>{{ $reparacion->costo_real ? ($setting->currency_symbol ?? 'S/') . ' ' . number_format($reparacion->costo_real, 2) : '-' }}
                    </td>
                    <td>
                        <span class="badge badge-{{ strtolower(str_replace(' ', '', $reparacion->estado_reparacion)) }}">
                            {{ $reparacion->estado_reparacion }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Inventario TI - Página {PAGE_NUM} de {PAGE_COUNT}</p>
    </div>
</body>

</html>