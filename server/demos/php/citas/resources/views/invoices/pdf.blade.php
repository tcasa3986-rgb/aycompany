<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $invoice->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 13px;
            color: #1f2937;
            background: #fff;
        }

        .header {
            background: #4A88F6;
            color: #fff;
            padding: 28px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .header .clinic-name {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .header .clinic-sub {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 4px;
        }

        .header .invoice-info {
            text-align: right;
        }

        .header .invoice-info .inv-number {
            font-size: 20px;
            font-weight: 700;
        }

        .header .invoice-info .inv-date {
            font-size: 12px;
            opacity: 0.85;
            margin-top: 4px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }

        .status-paid {
            background: #dcfce7;
            color: #15803d;
        }

        .status-pending {
            background: #fef9c3;
            color: #92400e;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #b91c1c;
        }

        .body {
            padding: 32px 40px;
        }

        .section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }

        .grid-2 {
            display: table;
            width: 100%;
            margin-bottom: 28px;
        }

        .grid-2 .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .grid-2 .col:last-child {
            padding-left: 20px;
        }

        .field-label {
            font-size: 11px;
            color: #9ca3af;
            margin-bottom: 2px;
        }

        .field-value {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .amount-box {
            background: #f0f6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 20px 28px;
            text-align: center;
            margin: 28px 0;
        }

        .amount-label {
            font-size: 12px;
            color: #3b82f6;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .amount-value {
            font-size: 36px;
            font-weight: 700;
            color: #1d4ed8;
        }

        .notes-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px 18px;
            font-size: 12px;
            color: #374151;
            line-height: 1.6;
        }

        .footer {
            margin-top: 48px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div>
            <div class="clinic-name">🏥 CitasMédicas</div>
            <div class="clinic-sub">Sistema de Gestión Médica</div>
        </div>
        <div class="invoice-info">
            <div class="inv-number">Factura #{{ $invoice->id }}</div>
            <div class="inv-date">Emitida el {{ $invoice->created_at->format('d/m/Y') }}</div>
            @php
                $statusClass = match ($invoice->status) {
                    'paid' => 'status-paid',
                    'cancelled' => 'status-cancelled',
                    default => 'status-pending',
                };
            @endphp
            <div class="status-badge {{ $statusClass }}">{{ $invoice->status_label }}</div>
        </div>
    </div>

    <div class="body">

        {{-- Patient & Doctor --}}
        <div class="section-title">Datos de la Cita</div>
        <div class="grid-2">
            <div class="col">
                <div class="field-label">Paciente</div>
                <div class="field-value">{{ $invoice->appointment->patient->name ?? '—' }}</div>

                <div class="field-label" style="margin-top:14px;">Fecha de Cita</div>
                <div class="field-value">{{ $invoice->appointment->date?->format('d/m/Y H:i') ?? '—' }}</div>
            </div>
            <div class="col">
                <div class="field-label">Médico tratante</div>
                <div class="field-value">Dr. {{ $invoice->appointment->doctor->name ?? '—' }}</div>

                <div class="field-label" style="margin-top:14px;">Especialidad</div>
                <div class="field-value">{{ $invoice->appointment->specialty->name ?? '—' }}</div>
            </div>
        </div>

        {{-- Payment --}}
        <div class="section-title">Detalle del Pago</div>
        <div class="grid-2">
            <div class="col">
                <div class="field-label">Método de Pago</div>
                <div class="field-value">{{ $invoice->payment_method_label }}</div>
            </div>
            <div class="col">
                <div class="field-label">Fecha de Registro</div>
                <div class="field-value">{{ $invoice->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        {{-- Total --}}
        <div class="amount-box" style="margin-bottom: 5px; padding-bottom: 10px;">
            <div class="amount-label" style="font-size: 10px; color:#6b7280; text-transform:uppercase;">Monto Subtotal
            </div>
            <div class="amount-value" style="font-size: 20px; color:#374151;">S/
                {{ number_format($invoice->amount, 2) }}</div>
        </div>

        @if($invoice->insurance_coverage_amount > 0)
            <div class="amount-box"
                style="margin-top: 0; margin-bottom: 5px; padding-top: 10px; padding-bottom: 10px; background: #e0e7ff; border-color: #c7d2fe;">
                <div class="amount-label" style="font-size: 10px; color: #4338ca; text-transform:uppercase;">
                    Aportación Seguro ARS @if($invoice->insurance) ({{ $invoice->insurance->name }}) @endif
                </div>
                <div class="amount-value" style="font-size: 18px; color: #3730a3;">-S/
                    {{ number_format($invoice->insurance_coverage_amount, 2) }}</div>
            </div>
        @endif

        <div class="amount-box" style="margin-top: 0;">
            <div class="amount-label">TOTAL A PAGAR (COPAGO)</div>
            <div class="amount-value">S/ {{ number_format($invoice->patient_copay_amount ?: $invoice->amount, 2) }}
            </div>
        </div>

        {{-- Notes --}}
        @if($invoice->notes)
            <div class="section-title">Observaciones</div>
            <div class="notes-box">{{ $invoice->notes }}</div>
        @endif

        <div class="footer">
            Este documento es un comprobante de pago interno generado por CitasMédicas.
        </div>
    </div>

</body>

</html>