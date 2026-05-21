<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Empleado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            color: #1e40af;
            margin-bottom: 10px;
        }

        h2 {
            color: #374151;
            font-size: 14px;
            margin-top: 20px;
        }

        .header {
            margin-bottom: 30px;
        }

        .info-box {
            background-color: #f3f4f6;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .info-row {
            display: flex;
            margin: 5px 0;
        }

        .info-label {
            width: 150px;
            font-weight: bold;
            color: #6b7280;
        }

        .info-value {
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte de Empleado</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">DNI:</span>
            <span class="info-value">{{ $empleado->dni }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nombre Completo:</span>
            <span class="info-value">{{ $empleado->nombreCompleto() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Cargo:</span>
            <span class="info-value">{{ $empleado->cargo->nombre ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Sucursal:</span>
            <span class="info-value">{{ $empleado->sucursal->nombre ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $empleado->email ?: 'N/A' }}</span>
        </div>
    </div>

    <h2>Historial de Asignaciones ({{ $empleado->asignaciones->count() }} total)</h2>

    <table>
        <thead>
            <tr>
                <th>Equipo</th>
                <th>Código</th>
                <th>Fecha Entrega</th>
                <th>Fecha Devolución</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($empleado->asignaciones->sortByDesc('fecha_entrega') as $asignacion)
                <tr>
                    <td>{{ $asignacion->equipo->marca->nombre ?? '' }} {{ $asignacion->equipo->modelo->nombre ?? '' }}</td>
                    <td>{{ $asignacion->equipo->codigo_inventario }}</td>
                    <td>{{ $asignacion->fecha_entrega->format('d/m/Y') }}</td>
                    <td>{{ $asignacion->fecha_devolucion?->format('d/m/Y') ?: '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $asignacion->fecha_devolucion ? 'finalizada' : 'activa' }}">
                            {{ $asignacion->fecha_devolucion ? 'Finalizada' : 'Activa' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #6b7280;">Sin asignaciones registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Inventario TI - Página {PAGE_NUM} de {PAGE_COUNT}</p>
    </div>
</body>

</html>