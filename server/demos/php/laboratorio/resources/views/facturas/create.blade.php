@extends('layouts.app')

@section('title', 'Procesar Pago')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Procesar Cobranza</h1>
        <p class="text-secondary">Emisión de comprobante para Orden: {{ $orden->numero_orden }}</p>
    </div>
    <div>
        <a href="{{ route('facturas.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: white;"><i class="fa-solid fa-arrow-left"></i> Volver a Caja</a>
    </div>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    @if ($errors->any())
        <div class="alert-error" style="background: rgba(255, 71, 87, 0.1); color: var(--danger); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px;">
            <ul style="margin-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert-error" style="background: rgba(255, 71, 87, 0.1); color: var(--danger); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px;">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div class="dashboard-grid">
        <!-- Detalles de la deuda -->
        <div class="col-5">
            <div style="background: rgba(0,0,0,0.2); padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); height: 100%;">
                <h3 style="color: var(--info); margin-bottom: 20px;"><i class="fa-solid fa-receipt"></i> Resumen del Cliente</h3>
                <p style="margin-bottom: 10px;"><strong>Paciente:</strong> {{ $orden->paciente->nombre_completo }}</p>
                <p style="margin-bottom: 10px;"><strong>Documento:</strong> {{ $orden->paciente->tipo_documento }} {{ $orden->paciente->numero_documento }}</p>
                <p style="margin-bottom: 10px;"><strong>Convenio:</strong> {{ $orden->convenio ? $orden->convenio->nombre : 'Ninguno' }}</p>
                <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
                <h4 style="color: var(--text-muted); margin-bottom: 10px;">Monto a Pagar</h4>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>Subtotal:</span>
                    <span>S/ {{ number_format($orden->subtotal, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: var(--success);">
                    <span>Descuento:</span>
                    <span>- S/ {{ number_format($orden->descuento, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: bold; margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color);">
                    <span>Total:</span>
                    <span class="text-accent" style="color: var(--accent-secondary);">S/ <span id="monto_total_txt">{{ number_format($orden->total, 2) }}</span></span>
                </div>
            </div>
        </div>

        <!-- Formulario de Pago -->
        <div class="col-7">
            <form action="{{ route('facturas.store') }}" method="POST" style="padding-left: 20px;">
                @csrf
                <input type="hidden" name="orden_id" value="{{ $orden->id }}">
                <input type="hidden" id="monto_total_val" value="{{ $orden->total }}">
                
                <h3 style="color: var(--accent-primary); margin-bottom: 20px;"><i class="fa-solid fa-cash-register"></i> Ingreso de Caja</h3>

                <div class="form-group">
                    <label class="form-label">Tipo de Comprobante</label>
                    <select name="tipo_comprobante" class="form-control" required style="font-size: 1.1rem; padding: 15px;">
                        <option value="Boleta">Boleta Electrónica</option>
                        <option value="Factura">Factura Electrónica</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Medio de Pago</label>
                    <select name="medio_pago" class="form-control" required>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Tarjeta de Débito">Tarjeta de Débito</option>
                        <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                        <option value="Transferencia / Yape / Plin">Transferencia / Yape / Plin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Código de Referencia / Operación (Opcional)</label>
                    <input type="text" name="referencia" class="form-control" placeholder="Ej: OP-0019283">
                </div>

                <div class="form-group">
                    <label class="form-label">Monto Recibido (S/)</label>
                    <input type="number" id="monto_recibido" name="monto_recibido" class="form-control" step="0.01" min="{{ $orden->total }}" value="{{ $orden->total }}" required style="font-size: 1.5rem; font-weight: bold; color: var(--success); background: rgba(0,0,0,0.4);">
                </div>

                <div class="form-group">
                    <label class="form-label">Vuelto a entregar</label>
                    <div style="font-size: 1.8rem; font-weight: bold; color: var(--text-primary);">
                        S/ <span id="vuelto_txt">0.00</span>
                    </div>
                </div>

                <div style="margin-top: 30px; text-align: right;">
                    <button type="submit" class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 30px;"><i class="fa-solid fa-check"></i> Registrar Pago y Emitir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const montoTotal = parseFloat(document.getElementById('monto_total_val').value);
        const montoRecibidoInput = document.getElementById('monto_recibido');
        const vueltoTxt = document.getElementById('vuelto_txt');

        montoRecibidoInput.addEventListener('input', function() {
            const recibido = parseFloat(this.value) || 0;
            let vuelto = recibido - montoTotal;
            if (vuelto < 0) vuelto = 0;
            vueltoTxt.textContent = vuelto.toFixed(2);
        });
    });
</script>
@endsection
