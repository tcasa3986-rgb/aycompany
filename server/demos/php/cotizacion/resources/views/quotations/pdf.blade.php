<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cotización {{ $quotation->quotation_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11.5px; color: #1e293b; }

        /* ── HEADER ──────────────────────────── */
        .page-header {
            background: #0f172a;
            color: #fff;
            padding: 0;
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            padding: 28px 32px;
            vertical-align: top;
            width: 60%;
        }
        .header-right {
            display: table-cell;
            padding: 28px 32px;
            vertical-align: top;
            text-align: right;
            background: #1e3a5f;
            width: 40%;
        }
        .company-name { font-size: 20px; font-weight: 700; color: #60a5fa; }
        .company-sub  { font-size: 10px; color: #94a3b8; margin-top: 2px; }
        .company-detail { font-size: 10px; color: #cbd5e1; margin-top: 14px; line-height: 1.7; }
        .doc-label { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; }
        .doc-number { font-size: 20px; font-weight: 700; color: #fff; margin-top: 2px; }
        .doc-status {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10px; font-weight: 700;
            margin-top: 8px;
            background: #3b82f6; color: #fff;
        }
        .doc-currency {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10px; font-weight: 700;
            margin-top: 4px; margin-left: 4px;
            background: #f59e0b; color: #1e293b;
        }

        /* ── CONTENT ─────────────────────────── */
        .content { padding: 24px 32px; }

        /* ── INFO GRID ───────────────────────── */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        .info-table td { padding: 14px 16px; vertical-align: top; }
        .info-table td:first-child { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px 0 0 6px; width: 40%; }
        .info-table td:nth-child(2) { background: #f1f5f9; border: 1px solid #e2e8f0; border-left: none; width: 30%; }
        .info-table td:nth-child(3) { background: #f8fafc; border: 1px solid #e2e8f0; border-left: none; border-radius: 0 6px 6px 0; width: 30%; }
        .info-label { font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 4px; }
        .info-value { font-size: 12.5px; font-weight: 600; }
        .info-sub   { font-size: 10px; color: #94a3b8; margin-top: 1px; }

        /* ── ITEMS TABLE ─────────────────────── */
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.items thead th {
            background: #0f172a; color: #94a3b8;
            font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
            padding: 9px 11px; text-align: left;
        }
        table.items thead th.r { text-align: right; }
        table.items tbody td { padding: 9px 11px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        table.items tbody td.r { text-align: right; }
        table.items tbody tr:nth-child(even) td { background: #f8fafc; }
        .badge-disc { background: #fef3c7; color: #92400e; padding: 1px 7px; border-radius: 10px; font-size: 9px; font-weight: 700; }

        /* ── TOTALS ──────────────────────────── */
        .totals-wrap { display: table; width: 100%; margin-bottom: 22px; }
        .totals-left  { display: table-cell; width: 55%; vertical-align: top; padding-right: 20px; }
        .totals-right { display: table-cell; width: 45%; vertical-align: top; }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td { padding: 5px 10px; font-size: 11.5px; }
        .totals-table td:last-child { text-align: right; font-weight: 600; }
        .totals-table .total-row td {
            border-top: 2px solid #0f172a;
            font-size: 14px; font-weight: 800;
            padding-top: 8px;
        }
        .totals-table .total-row td:last-child { color: #2563eb; font-size: 16px; }
        .totals-table .discount-row td { color: #dc2626; }

        /* ── NOTES / TERMS ───────────────────── */
        .section-box {
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 6px; padding: 12px 14px; margin-bottom: 12px;
        }
        .section-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 6px; }
        .section-text  { font-size: 10.5px; color: #475569; line-height: 1.6; white-space: pre-line; }

        /* ── FOOTER ──────────────────────────── */
        .page-footer {
            border-top: 2px solid #e2e8f0;
            padding: 12px 32px;
            display: table; width: 100%;
            font-size: 9px; color: #94a3b8;
        }
        .footer-left  { display: table-cell; text-align: left; }
        .footer-right { display: table-cell; text-align: right; }
    </style>
</head>
<body>
    @php
        $sym = $quotation->currency_symbol;
        $grossSub = $quotation->subtotal;
        $discount = $quotation->discount_amount;
        $tax      = $quotation->tax_amount;
        $total    = $quotation->total;
    @endphp

    {{-- ══ HEADER ══ --}}
    <div class="page-header">
        <div class="header-left">
            <div class="company-name">{{ $company['company_name'] }}</div>
            @if($company['company_ruc'])
                <div class="company-sub">RUC / NIT: {{ $company['company_ruc'] }}</div>
            @endif
            <div class="company-detail">
                @if($company['company_address']) {{ $company['company_address'] }}<br> @endif
                @if($company['company_phone'])  Tel: {{ $company['company_phone'] }}<br> @endif
                @if($company['company_email'])  {{ $company['company_email'] }}<br> @endif
                @if($company['company_website']) {{ $company['company_website'] }} @endif
            </div>
        </div>
        <div class="header-right">
            <div class="doc-label">Cotización</div>
            <div class="doc-number">{{ $quotation->quotation_number }}</div>
            <br>
            <span class="doc-status">{{ $quotation->status }}</span>
            <span class="doc-currency">{{ $quotation->currency }}</span>
        </div>
    </div>

    {{-- ══ CONTENT ══ --}}
    <div class="content">

        {{-- Info --}}
        <table class="info-table">
            <tr>
                <td>
                    <div class="info-label">Cliente</div>
                    <div class="info-value">{{ $quotation->client->name }}</div>
                    @if($quotation->client->document_number)
                        <div class="info-sub">{{ $quotation->client->document_number }}</div>
                    @endif
                    @if($quotation->client->address)
                        <div class="info-sub">{{ $quotation->client->address }}</div>
                    @endif
                    @if($quotation->client->email)
                        <div class="info-sub">{{ $quotation->client->email }}</div>
                    @endif
                </td>
                <td>
                    <div class="info-label">Fecha de Emisión</div>
                    <div class="info-value">{{ $quotation->issue_date->format('d/m/Y') }}</div>
                </td>
                <td>
                    <div class="info-label">Fecha de Vencimiento</div>
                    <div class="info-value">{{ $quotation->due_date ? $quotation->due_date->format('d/m/Y') : '—' }}</div>
                </td>
            </tr>
        </table>

        {{-- Items --}}
        <table class="items">
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:38%">Descripción</th>
                    <th style="width:10%">Unidad</th>
                    <th class="r" style="width:10%">Cant.</th>
                    <th class="r" style="width:14%">P. Unitario</th>
                    <th class="r" style="width:8%">Dto.</th>
                    <th class="r" style="width:16%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->details as $i => $d)
                <tr>
                    <td style="color:#94a3b8">{{ $i + 1 }}</td>
                    <td style="font-weight:500">{{ $d->product_name }}</td>
                    <td style="color:#64748b">{{ $d->unit ?: '—' }}</td>
                    <td class="r">{{ rtrim(rtrim(number_format($d->quantity, 3), '0'), '.') }}</td>
                    <td class="r">{{ $sym }} {{ number_format($d->unit_price, 2) }}</td>
                    <td class="r">
                        @if($d->discount_pct > 0)
                            <span class="badge-disc">{{ $d->discount_pct }}%</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="r" style="font-weight:700">{{ $sym }} {{ number_format($d->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-wrap">
            <div class="totals-left">
                @if($quotation->notes)
                <div class="section-box">
                    <div class="section-title">Notas</div>
                    <div class="section-text">{{ $quotation->notes }}</div>
                </div>
                @endif
            </div>
            <div class="totals-right">
                <table class="totals-table">
                    <tr>
                        <td style="color:#64748b">Subtotal bruto</td>
                        <td>{{ $sym }} {{ number_format($grossSub, 2) }}</td>
                    </tr>
                    @if($discount > 0)
                    <tr class="discount-row">
                        <td>Descuento global</td>
                        <td>- {{ $sym }} {{ number_format($discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="color:#64748b">IGV</td>
                        <td>{{ $sym }} {{ number_format($tax, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total</td>
                        <td>{{ $sym }} {{ number_format($total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Terms --}}
        @if($quotation->terms)
        <div class="section-box">
            <div class="section-title">Términos y Condiciones</div>
            <div class="section-text">{{ $quotation->terms }}</div>
        </div>
        @endif
    </div>

    {{-- ══ FOOTER ══ --}}
    <div class="page-footer">
        <div class="footer-left">{{ $company['company_name'] }} — {{ $company['company_email'] }}</div>
        <div class="footer-right">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</body>
</html>
