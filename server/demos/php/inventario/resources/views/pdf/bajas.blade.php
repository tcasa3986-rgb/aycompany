<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Bajas</title>
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

        .badge-obsolescencia {
            background-color: #e5e7eb;
            color: #374151;
        }

        .badge-dañoirre parable {
            background-color: #fed7aa;
            color: #9a3412;
        }

        .badge-perdida {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-robo {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-otro {
            background-color: #e9d5ff;
            color: #6b21a8;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte de Equipos Dados de Baja</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
        <p>Total de bajas: {{ $bajas->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Equipo</th>
                <th>Sucursal</th>
                <th>Fecha Baja</th>
                <th>Motivo</th>
                <th>Autorizado Por</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bajas as $baja)
                <tr>
                    <td>{{ $baja->equipo->codigo_inventario }}</td>
                    <td>{{ $baja->equipo->marca->nombre ?? '' }} {{ $baja->equipo->modelo->nombre ?? '' }}</td>
                    <td>{{ $baja->equipo->sucursal->nombre ?? '-' }}</td>
                    <td>{{ $baja->fecha_baja->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower(str_replace(' ', '', $baja->motivo)) }}">
                            {{ $baja->motivo }}
                        </span>
                    </td>
                    <td>{{ $baja->autorizado_por ?: '-' }}</td>
                    <td style="max-width: 200px; font-size: 9px;">{{ Str::limit($baja->descripcion, 60) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Inventario TI - Página {PAGE_NUM} de {PAGE_COUNT}</p>
    </div>
</body>

</html>