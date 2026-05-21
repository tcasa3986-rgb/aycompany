@extends('layouts.app')

@section('title', 'Facturación y Caja')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Facturación y Caja</h1>
        <p class="text-secondary">Gestión de comprobantes y cobranzas de órdenes médicas</p>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Órdenes Pendientes de Cobro -->
    <div class="col-12">
        <div class="card" style="border-left: 4px solid var(--warning);">
            <div class="card-header">
                <span class="card-title text-warning"><i class="fa-solid fa-clock"></i> Órdenes Pendientes de Cobro (Caja Rápida)</span>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nro Orden</th>
                            <th>Paciente</th>
                            <th>Fecha</th>
                            <th>Total a Cobrar</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenesPendientes as $ordenP)
                        <tr>
                            <td><strong>{{ $ordenP->numero_orden }}</strong></td>
                            <td>{{ $ordenP->paciente->nombre_completo ?? 'N/A' }}</td>
                            <td>{{ $ordenP->fecha_registro->format('d/m/Y H:i') }}</td>
                            <td><strong class="text-warning">S/ {{ number_format($ordenP->total, 2) }}</strong></td>
                            <td>
                                <a href="{{ route('facturas.create', ['orden_id' => $ordenP->id]) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">
                                    <i class="fa-solid fa-cash-register"></i> Procesar Pago
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay órdenes pendientes de cobro directo.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Historial de Facturas -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-file-invoice"></i> Historial de Comprobantes Emitidos</span>
            </div>
            
            @if(session('success'))
                <div style="background: rgba(46, 213, 115, 0.1); color: var(--success); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="background: rgba(255, 71, 87, 0.1); color: var(--danger); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Tipo</th>
                            <th>Fecha Emisión</th>
                            <th>Cajero(a)</th>
                            <th>Subtotal</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturas as $factura)
                        <tr>
                            <td><strong>{{ $factura->numero_factura }}</strong></td>
                            <td>{{ $factura->tipo_comprobante }}</td>
                            <td>{{ $factura->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $factura->user->name ?? 'Sistema' }}</td>
                            <td>S/ {{ number_format($factura->subtotal, 2) }}</td>
                            <td><strong class="text-success">S/ {{ number_format($factura->total, 2) }}</strong></td>
                            <td><span class="status-badge status-completed">{{ $factura->estado }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No se han emitido comprobantes aún.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($facturas->hasPages())
            <div style="margin-top: 20px;">
                {{ $facturas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
