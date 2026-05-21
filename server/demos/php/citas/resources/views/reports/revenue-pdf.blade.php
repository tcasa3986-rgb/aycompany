<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Ingresos</title>
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
            background: #16a34a;
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

        .totals {
            display: table;
            width: 100%;
            margin-bottom: 14px;
        }

        .totals .col {
            display: table-cell;
            width: 25%;
            padding: 10px 12px;
            text-align: center;
            border-radius: 8px;
        }

        .totals .t1 {
            background: #dcfce7;
            color: #15803d;
        }

        .totals .t2 {
            background: #fef9c3;
            color: #92400e;
        }

        .totals .t3 {
            background: #fee2e2;
            color: #b91c1c;
        }

        .totals .t4 {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .totals .col strong {
            display: block;
            font-size: 16px;
            font-weight: 700;
            margin-top: 4px;
        }

        .totals .col span {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
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

        td.amount {
            text-align: right;
            font-weight: 600;
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

        .badge-yellow {
            background: #fef9c3;
            color: #92400e;
        }

        .badge-red {
            background: #fee2e2;
            color: #b91c1c;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>
            <h1>Reporte de Ingresos</h1>
            <div style="font-size:11px;opacity:.85;margin-top:4px;">CitasMédicas — Sistema de Gestión Médica</div>
        </div>
        <div class="meta">
            Generado: {{ now()->format('d/m/Y H:i') }}<br>
            Total: {{ $invoices->count() }} facturas
        </div>
    </div>

    @if($request->date_from || $request->date_to || $request->payment_method || $request->status)
        <div class="filters">
            Filtros:
            @if($request->date_from) Desde: {{ $request->date_from }} @endif
            @if($request->date_to) Hasta: {{ $request->date_to }} @endif
            @if($request->payment_method) Método: {{ $request->payment_method }} @endif
            @if($request->status) Estado: {{ $request->status }} @endif
        </div>
    @endif

    <div class="body">
        <div class="totals">
            <div class="col t1">
                <span>Total Pagado</span>
                <strong>S/ {{ number_format($totals['paid'], 2) }}</strong>
            </div>
            <div class="col t2">
                <span>Pendiente</span>
                <strong>S/ {{ number_format($totals['pending'], 2) }}</strong>
            </div>
            <div class="col t3">
                <span>Cancelado</span>
                <strong>S/ {{ number_format($totals['cancelled'], 2) }}</strong>
            </div>
            <div class="col t4">
                <span>Total General</span>
                <strong>S/ {{ number_format($totals['total'], 2) }}</strong>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Fecha Cita</th>
                    <th style="text-align:right;">Monto</th>
                    <th>Método</th>
                    <th>Estado</th>
                    <th>Registrado</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $statusMap = ['pending' => 'Pendiente', 'paid' => 'Pagado', 'cancelled' => 'Cancelado'];
                    $methodMap = ['cash' => 'Efectivo', 'card' => 'Tarjeta', 'transfer' => 'Transferencia'];
                    $bClasses = ['pending' => 'badge-yellow', 'paid' => 'badge-green', 'cancelled' => 'badge-red'];
                @endphp
                @foreach($invoices as $inv)
                    <tr>
                        <td>{{ $inv->id }}</td>
                        <td>{{ $inv->appointment->patient->name ?? '—' }}</td>
                        <td>Dr. {{ $inv->appointment->doctor->name ?? '—' }}</td>
                        <td>{{ $inv->appointment?->date?->format('d/m/Y') ?? '—' }}</td>
                        <td class="amount">S/ {{ number_format($inv->amount, 2) }}</td>
                        <td>{{ $methodMap[$inv->payment_method] ?? $inv->payment_method ?? '—' }}</td>
                        <td><span
                                class="badge {{ $bClasses[$inv->status] ?? '' }}">{{ $statusMap[$inv->status] ?? $inv->status }}</span>
                        </td>
                        <td>{{ $inv->created_at->format('d/m/Y') }}</td>
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