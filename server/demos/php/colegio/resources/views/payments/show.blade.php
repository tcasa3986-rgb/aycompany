@extends('layouts.app')
@section('title', 'Recibo de Pago')
@section('page-title', 'Recibo de Pago')

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .page-body { padding: 0 !important; }
    .recibo-card { box-shadow: none !important; border: 1px solid #ccc; }
}
</style>
@endpush

@section('content')

<div class="no-print" style="display:flex;gap:12px;margin-bottom:20px;align-items:center;">
    <a href="{{ route('pagos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    <a href="{{ route('pagos.edit', $pago) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a>
    <button onclick="window.print()" class="btn btn-success" style="margin-left:auto;">
        <i class="fas fa-print"></i> Imprimir Recibo
    </button>
    @if($pago->estado !== 'anulado')
    <form method="POST" action="{{ route('pagos.destroy', $pago) }}" onsubmit="return confirm('¿Anular este pago?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="fas fa-ban"></i> Anular</button>
    </form>
    @endif
</div>

{{-- RECIBO IMPRIMIBLE --}}
<div class="recibo-card" style="max-width:680px;margin:0 auto;background:white;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,0.1);overflow:hidden;">

    {{-- Encabezado institucional --}}
    <div style="background:linear-gradient(135deg,#0f2460,#1e3a8a,#1d4ed8);color:white;padding:28px 36px;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:8px;">
                <div style="width:52px;height:52px;background:rgba(255,255,255,0.2);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;">🎓</div>
                <div>
                    <div style="font-size:20px;font-weight:800;letter-spacing:.5px;">COLEGIO CRM</div>
                    <div style="font-size:12px;opacity:.8;">Sistema de Gestión Escolar</div>
                </div>
            </div>
            <div style="font-size:11px;opacity:.7;">
                <i class="fas fa-map-marker-alt"></i> Av. Principal 123, Lima, Perú &nbsp;|&nbsp;
                <i class="fas fa-phone"></i> (01) 234-5678
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:13px;opacity:.7;text-transform:uppercase;letter-spacing:2px;">Recibo de Pago</div>
            <div style="font-size:26px;font-weight:800;font-family:monospace;letter-spacing:1px;">{{ $pago->numero_recibo }}</div>
            <div style="margin-top:8px;">
                @php $color = match($pago->estado) { 'pagado'=>'#10b981','pendiente'=>'#f59e0b','vencido'=>'#ef4444','anulado'=>'#94a3b8',default=>'#94a3b8' }; @endphp
                <span style="background:{{ $color }};padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:.5px;">
                    {{ strtoupper($pago->estado) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Cuerpo del recibo --}}
    <div style="padding:32px 36px;">

        {{-- Datos del alumno --}}
        <div style="background:#f8fafc;border-radius:12px;padding:18px 22px;margin-bottom:24px;border-left:4px solid #3b82f6;">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;font-weight:700;">Datos del Estudiante</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <div style="font-size:11px;color:#94a3b8;">Nombre Completo</div>
                    <div style="font-weight:700;font-size:15px;">{{ $pago->alumno->nombre_completo ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:#94a3b8;">Código / DNI</div>
                    <div style="font-weight:700;">{{ $pago->alumno->codigo ?? '—' }} / {{ $pago->alumno->dni ?? '—' }}</div>
                </div>
                @php $mat = $pago->alumno?->matriculaActiva(); @endphp
                @if($mat)
                <div>
                    <div style="font-size:11px;color:#94a3b8;">Grado</div>
                    <div style="font-weight:600;">{{ $mat->grado->nombre ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:#94a3b8;">Sección / Año</div>
                    <div style="font-weight:600;">Sec. {{ $mat->seccion->nombre ?? '—' }} · {{ $mat->anio_escolar }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Detalle del pago --}}
        <table style="width:100%;border-collapse:collapse;margin-bottom:24px;">
            <thead>
                <tr style="background:#f1f5f9;">
                    <th style="padding:10px 16px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#64748b;border-radius:8px 0 0 8px;">Concepto</th>
                    <th style="padding:10px 16px;text-align:center;font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#64748b;">Período</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#64748b;border-radius:0 8px 8px 0;">Importe</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:16px;font-size:14px;font-weight:600;border-bottom:1px solid #e2e8f0;">
                        {{ $pago->concepto->nombre ?? '—' }}
                    </td>
                    <td style="padding:16px;text-align:center;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;">
                        {{ $pago->nombre_mes }} {{ $pago->anio_escolar }}
                    </td>
                    <td style="padding:16px;text-align:right;font-size:15px;font-weight:700;border-bottom:1px solid #e2e8f0;">
                        S/. {{ number_format($pago->monto, 2) }}
                    </td>
                </tr>
                @if($pago->descuento > 0)
                <tr>
                    <td colspan="2" style="padding:10px 16px;font-size:13px;color:#10b981;">Descuento aplicado</td>
                    <td style="padding:10px 16px;text-align:right;font-size:13px;color:#10b981;font-weight:600;">
                        - S/. {{ number_format($pago->descuento, 2) }}
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr style="background:linear-gradient(135deg,#0f2460,#1e3a8a);">
                    <td colspan="2" style="padding:14px 16px;color:white;font-size:14px;font-weight:700;border-radius:8px 0 0 8px;">
                        TOTAL PAGADO
                    </td>
                    <td style="padding:14px 16px;text-align:right;color:white;font-size:20px;font-weight:800;border-radius:0 8px 8px 0;">
                        S/. {{ number_format($pago->monto_pagado, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Datos del pago --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:28px;">
            @foreach([
                ['Fecha de Pago', $pago->fecha_pago?->format('d/m/Y')],
                ['Método de Pago', ucfirst($pago->metodo_pago)],
                ['Registrado por', $pago->registradoPor->name ?? 'Sistema'],
            ] as [$label, $val])
            <div style="text-align:center;padding:14px;background:#f8fafc;border-radius:10px;">
                <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">{{ $label }}</div>
                <div style="font-size:13px;font-weight:700;">{{ $val }}</div>
            </div>
            @endforeach
        </div>

        @if($pago->observaciones)
        <div style="background:#fef3c7;border-radius:10px;padding:12px 16px;margin-bottom:24px;font-size:13px;color:#92400e;">
            <i class="fas fa-info-circle"></i> <strong>Observaciones:</strong> {{ $pago->observaciones }}
        </div>
        @endif

        {{-- Firma y sello --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-top:16px;padding-top:24px;border-top:2px dashed #e2e8f0;">
            <div style="text-align:center;">
                <div style="height:60px;border-bottom:1.5px solid #334155;margin-bottom:8px;"></div>
                <div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:1px;">Firma del Cajero</div>
                <div style="font-size:12px;font-weight:600;margin-top:2px;">{{ $pago->registradoPor->name ?? 'Tesorería' }}</div>
            </div>
            <div style="text-align:center;">
                <div style="height:60px;display:flex;align-items:center;justify-content:center;margin-bottom:8px;">
                    <div style="width:70px;height:70px;border:2px solid #1e3a8a;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#1e3a8a;font-size:10px;font-weight:700;text-align:center;padding:8px;line-height:1.3;">
                        SELLO<br>COLEGIO
                    </div>
                </div>
                <div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:1px;">Sello Institucional</div>
            </div>
        </div>
    </div>

    {{-- Pie --}}
    <div style="background:#f8fafc;padding:12px 36px;text-align:center;font-size:11px;color:#94a3b8;border-top:1px solid #e2e8f0;">
        Documento generado el {{ now()->format('d/m/Y H:i') }} — CRM Colegio v1.0 — Este recibo es válido como comprobante de pago.
    </div>
</div>

@endsection
