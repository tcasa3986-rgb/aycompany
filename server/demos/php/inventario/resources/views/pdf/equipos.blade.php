<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Equipos</title>
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

        .badge-disponible {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-asignado {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-reparacion {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-baja {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte de Inventario de Equipos</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
        <p>Total de equipos: {{ $equipos->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Tipo</th>
                <th>Marca/Modelo</th>
                <th>Núm. Serie</th>
                <th>Sucursal</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipos as $equipo)
                <tr>
                    <td>{{ $equipo->codigo_inventario }}</td>
                    <td>{{ $equipo->tipoEquipo->nombre ?? '-' }}</td>
                    <td>{{ $equipo->marca->nombre ?? '' }} {{ $equipo->modelo->nombre ?? '' }}</td>
                    <td>{{ $equipo->numero_serie ?: 'N/A' }}</td>
                    <td>{{ $equipo->sucursal->nombre ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower(str_replace(' ', '', $equipo->estado)) }}">
                            {{ $equipo->estado }}
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