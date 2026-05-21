<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Asignaciones</title>
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

        .badge-activa {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-finalizada {
            background-color: #e5e7eb;
            color: #374151;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte de Asignaciones</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
        <p>Total de asignaciones: {{ $asignaciones->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Equipo</th>
                <th>Empleado</th>
                <th>F. Entrega</th>
                <th>F. Devolución</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($asignaciones as $asignacion)
                <tr>
                    <td>{{ $asignacion->equipo->codigo_inventario }}</td>
                    <td>{{ $asignacion->empleado->nombreCompleto() }}</td>
                    <td>{{ $asignacion->fecha_entrega->format('d/m/Y') }}</td>
                    <td>{{ $asignacion->fecha_devolucion?->format('d/m/Y') ?: '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $asignacion->fecha_devolucion ? 'finalizada' : 'activa' }}">
                            {{ $asignacion->fecha_devolucion ? 'Finalizada' : 'Activa' }}
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