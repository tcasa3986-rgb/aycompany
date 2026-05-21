<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Citas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
        }

        .header {
            background: #4A88F6;
            color: #fff;
            padding: 18px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
        }

        .header .meta {
            text-align: right;
            font-size: 11px;
            opacity: 0.85;
        }

        .filters {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 28px;
            font-size: 10px;
            color: #6b7280;
        }

        .body {
            padding: 16px 28px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: #f3f4f6;
        }

        th {
            text-align: left;
            padding: 8px 10px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            font-weight: 700;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 7px 10px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }

        tr:nth-child(even) td {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 50px;
            font-size: 9px;
            font-weight: 700;
        }

        .badge-green {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-yellow {
            background: #fef9c3;
            color: #92400e;
        }

        .badge-red {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .badge-gray {
            background: #f3f4f6;
            color: #6b7280;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        .summary {
            margin: 12px 0;
            padding: 10px 14px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            font-size: 10px;
            color: #1d4ed8;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>
            <h1>Reporte de Citas Médicas</h1>
            <div style="font-size:11px;opacity:.85;margin-top:4px;">CitasMédicas — Sistema de Gestión Médica</div>
        </div>
        <div class="meta">
            Generado: {{ now()->format('d/m/Y H:i') }}<br>
            Total: {{ $appointments->count() }} citas
        </div>
    </div>

    @if($request->date_from || $request->date_to || $request->status)
        <div class="filters">
            Filtros aplicados:
            @if($request->date_from) Desde: {{ $request->date_from }} @endif
            @if($request->date_to) Hasta: {{ $request->date_to }} @endif
            @if($request->status) Estado: {{ $request->status }} @endif
        </div>
    @endif

    <div class="body">
        <div class="summary">
            Total de citas en el reporte: <strong>{{ $appointments->count() }}</strong>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Especialidad</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $colors = ['pending' => 'yellow', 'confirmed' => 'blue', 'in_progress' => 'purple', 'completed' => 'green', 'cancelled' => 'red', 'no_show' => 'gray'];
                    $labels = ['pending' => 'Pendiente', 'confirmed' => 'Confirmada', 'in_progress' => 'En Atención', 'completed' => 'Completada', 'cancelled' => 'Cancelada', 'no_show' => 'No Asistió'];
                @endphp
                @foreach($appointments as $appt)
                    @php $color = $colors[$appt->status] ?? 'gray'; @endphp
                    <tr>
                        <td>{{ $appt->id }}</td>
                        <td>{{ $appt->patient->name ?? '—' }}</td>
                        <td>Dr. {{ $appt->doctor->name ?? '—' }}</td>
                        <td>{{ $appt->specialty->name ?? '—' }}</td>
                        <td>{{ $appt->date?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td><span class="badge badge-{{ $color }}">{{ $labels[$appt->status] ?? $appt->status }}</span></td>
                        <td>{{ \Illuminate\Support\Str::limit($appt->reason ?? '—', 40) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Reporte generado automáticamente por CitasMédicas · {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>

</html>