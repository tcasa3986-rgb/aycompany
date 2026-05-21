<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><style>
body{font-family:Arial,sans-serif;background:#f0f2f5;margin:0;padding:0;}
.wrap{max-width:580px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.header{background:#1e2d3d;padding:28px 32px;display:flex;align-items:center;gap:14px;}
.header h1{color:#fff;font-size:18px;margin:0;}
.header span{color:#4ade80;font-size:12px;display:block;margin-top:3px;}
.body{padding:32px;}
.body h2{font-size:16px;color:#1e2d3d;margin-bottom:8px;}
.body p{font-size:13.5px;color:#4a5568;line-height:1.7;margin-bottom:14px;}
.info-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px;margin:20px 0;}
.info-row{display:flex;justify-content:space-between;margin-bottom:8px;font-size:13px;}
.info-row:last-child{margin-bottom:0;border-top:1px solid #e2e8f0;padding-top:8px;font-weight:700;font-size:15px;}
.label{color:#718096;}
.value{color:#1e2d3d;font-weight:600;}
.btn{display:inline-block;background:#4ade80;color:#1e2d3d;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;margin:16px 0;}
.footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:16px 32px;font-size:11px;color:#a0aec0;text-align:center;}
.msg-box{background:#fffbeb;border:1px solid #fef3c7;border-radius:8px;padding:14px;margin:14px 0;font-size:13px;color:#92400e;font-style:italic;}
</style></head>
<body>
<div class="wrap">
    <div class="header">
        <div>
            <h1>{{ $company['company_name'] }}</h1>
            <span>Sistema de Cotizaciones</span>
        </div>
    </div>
    <div class="body">
        <h2>Cotización {{ $quotation->quotation_number }}</h2>
        <p>Estimado/a <strong>{{ $quotation->client->name }}</strong>, adjuntamos la cotización solicitada en formato PDF.</p>

        @if($customMessage)
        <div class="msg-box">{{ $customMessage }}</div>
        @endif

        <div class="info-box">
            <div class="info-row">
                <span class="label">Número</span>
                <span class="value">{{ $quotation->quotation_number }}</span>
            </div>
            <div class="info-row">
                <span class="label">Fecha de emisión</span>
                <span class="value">{{ $quotation->issue_date->format('d/m/Y') }}</span>
            </div>
            @if($quotation->due_date)
            <div class="info-row">
                <span class="label">Válida hasta</span>
                <span class="value">{{ $quotation->due_date->format('d/m/Y') }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="label">Moneda</span>
                <span class="value">{{ $quotation->currency }}</span>
            </div>
            <div class="info-row">
                <span class="label">Total</span>
                <span class="value">{{ $quotation->currency_symbol }} {{ number_format($quotation->total, 2) }}</span>
            </div>
        </div>

        <p>Si tiene alguna consulta o desea proceder con la aprobación, no dude en contactarnos.</p>
        <p>Atentamente,<br><strong>{{ $company['company_name'] }}</strong></p>
        @if($company['company_phone']) <p style="font-size:12px;color:#718096;">📞 {{ $company['company_phone'] }}</p> @endif
    </div>
    <div class="footer">
        {{ $company['company_name'] }} &nbsp;|&nbsp;
        @if($company['company_email']) {{ $company['company_email'] }} @endif
        @if($company['company_website']) &nbsp;|&nbsp; {{ $company['company_website'] }} @endif
    </div>
</div>
</body>
</html>
