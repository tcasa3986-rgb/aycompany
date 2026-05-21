@extends('layouts.app')
@section('title', 'Comprobante ' . $factura->numero_factura)
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Comprobante de Pago</h1>
        <p class="text-secondary">{{ $factura->tipo_comprobante }} N° <strong>{{ $factura->numero_factura }}</strong></p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        <button onclick="window.print()" class="btn btn-primary"><i class="fa-solid fa-print"></i> Imprimir</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="col-8">
        <div class="card" id="printArea">
            {{-- Encabezado --}}
            <div style="padding:2rem;border-bottom:1px solid var(--border);">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;">
                    <div>
                        <h2 style="margin:0;font-family:'Outfit';font-size:1.5rem;" class="text-gradient">LabSalud</h2>
                        <p class="text-muted" style="margin:4px 0;font-size:0.9rem;">Sistema de Laboratorio Clínico</p>
                    </div>
                    <div style="text-align:right;">
                        <div style="background:var(--gradient-primary);padding:8px 16px;border-radius:8px;display:inline-block;">
                            <strong style="font-size:1.1rem;">{{ strtoupper($factura->tipo_comprobante) }}</strong>
                        </div>
                        <p style="margin:8px 0 0;font-size:0.9rem;color:var(--text-secondary);">N° {{ $factura->numero_factura }}</p>
                        <p style="margin:2px 0;font-size:0.85rem;color:var(--text-muted);">{{ $factura->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Datos del Paciente --}}
            <div style="padding:1.5rem 2rem;border-bottom:1px solid var(--border);">
                <h4 style="margin:0 0 1rem;font-size:0.9rem;text-transform:uppercase;letter-spacing:1px;" class="text-muted">Datos del Paciente</h4>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem 2rem;font-size:0.9rem;">
                    <div><span class="text-muted">Paciente:</span> <strong>{{ $factura->orden->paciente->nombre_completo }}</strong></div>
                    <div><span class="text-muted">Documento:</span> <strong>{{ $factura->orden->paciente->numero_documento }}</strong></div>
                    <div><span class="text-muted">Orden N°:</span> <strong>{{ $factura->orden->numero_orden }}</strong></div>
                    @if($factura->convenio_id)
                    <div><span class="text-muted">Convenio:</span> <strong>{{ $factura->orden->convenio->nombre }}</strong></div>
                    @endif
                    <div><span class="text-muted">Atendido por:</span> <strong>{{ $factura->user->name ?? '—' }}</strong></div>
                </div>
            </div>

            {{-- Detalle de Pruebas --}}
            <div style="padding:1.5rem 2rem;">
                <h4 style="margin:0 0 1rem;font-size:0.9rem;text-transform:uppercase;letter-spacing:1px;" class="text-muted">Detalle de Servicios</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Prueba / Servicio</th>
                            <th style="text-align:right;">Precio</th>
                            <th style="text-align:right;">Descuento</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($factura->orden->detalles as $det)
                        <tr>
                            <td>{{ $det->prueba->nombre }}</td>
                            <td style="text-align:right;">S/ {{ number_format($det->precio_unitario, 2) }}</td>
                            <td style="text-align:right;">{{ $det->descuento > 0 ? 'S/ -' . number_format($det->descuento, 2) : '—' }}</td>
                            <td style="text-align:right;"><strong>S/ {{ number_format($det->precio_final, 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totales --}}
                <div style="display:flex;justify-content:flex-end;margin-top:1.5rem;">
                    <div style="min-width:260px;">
                        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border);">
                            <span class="text-muted">Subtotal</span>
                            <strong>S/ {{ number_format($factura->subtotal, 2) }}</strong>
                        </div>
                        @if($factura->descuento > 0)
                        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border);">
                            <span class="text-muted">Descuento</span>
                            <strong class="text-success">- S/ {{ number_format($factura->descuento, 2) }}</strong>
                        </div>
                        @endif
                        @if($factura->igv > 0)
                        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border);">
                            <span class="text-muted">IGV (18%)</span>
                            <strong>S/ {{ number_format($factura->igv, 2) }}</strong>
                        </div>
                        @endif
                        <div style="display:flex;justify-content:space-between;padding:10px 0;font-size:1.2rem;">
                            <strong>TOTAL</strong>
                            <strong class="text-gradient">S/ {{ number_format($factura->total, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pagos --}}
            @foreach($factura->pagos as $pago)
            <div style="padding:1rem 2rem;background:rgba(46,213,115,0.05);border-top:1px solid var(--border);">
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.9rem;">
                    <div><i class="fa-solid fa-circle-check text-success"></i> <strong>PAGO REGISTRADO</strong></div>
                    <div><span class="text-muted">Medio:</span> <strong>{{ $pago->medio_pago }}</strong> &nbsp;|&nbsp; <span class="text-muted">Monto:</span> <strong>S/ {{ number_format($pago->monto, 2) }}</strong> &nbsp;|&nbsp; <span class="text-muted">Fecha:</span> {{ $pago->fecha_pago->format('d/m/Y H:i') }}</div>
                </div>
                @if($pago->referencia)
                <p class="text-muted" style="margin:4px 0 0;font-size:0.85rem;">Referencia: {{ $pago->referencia }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="col-4">
        <div class="card">
            <div class="card-header"><span class="card-title">Estado del Comprobante</span></div>
            <div style="padding:1.5rem;text-align:center;">
                <div style="font-size:3rem;margin-bottom:1rem;">
                    @if($factura->estado === 'Pagada')
                        <i class="fa-solid fa-circle-check text-success"></i>
                    @else
                        <i class="fa-solid fa-clock text-warning"></i>
                    @endif
                </div>
                <span class="status-badge {{ $factura->estado === 'Pagada' ? 'status-completed' : 'status-pending' }}" style="font-size:1rem;padding:8px 20px;">
                    {{ $factura->estado }}
                </span>
                <div style="margin-top:1.5rem;font-size:0.9rem;">
                    <p class="text-muted">Emitido el {{ $factura->created_at->format('d/m/Y') }}</p>
                    <p class="text-muted">Por: {{ $factura->user->name ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .page-header > div:last-child, .col-4 { display: none !important; }
    .col-8 { width: 100% !important; }
    body { background: white !important; color: black !important; }
    .card { background: white !important; border: 1px solid #ddd !important; box-shadow: none !important; }
    th, td { color: black !important; }
}
</style>
@endpush
