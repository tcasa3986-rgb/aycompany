@extends('layouts.app')
@section('title', 'Registrar Pago')
@section('page-title', 'Registrar Pago')

@section('content')
<div style="max-width:760px;">
<div style="margin-bottom:16px;">
    <a href="{{ route('pagos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<form method="POST" action="{{ route('pagos.store') }}">
@csrf
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-money-bill-wave" style="color:#10b981;margin-right:8px;"></i>Datos del Pago</span>
    </div>
    <div class="card-body">
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Alumno *</label>
                <select name="alumno_id" class="form-control" required>
                    <option value="">Seleccionar alumno...</option>
                    @foreach($alumnos as $a)
                        <option value="{{ $a->id }}" {{ old('alumno_id')==$a->id ? 'selected':'' }}>
                            {{ $a->nombre_completo }} — {{ $a->dni }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Concepto de Pago *</label>
                <select name="concepto_id" class="form-control" id="concepto_select" required>
                    <option value="">Seleccionar concepto...</option>
                    @foreach($conceptos as $c)
                        <option value="{{ $c->id }}" data-monto="{{ $c->monto }}" {{ old('concepto_id')==$c->id ? 'selected':'' }}>
                            {{ $c->nombre }} — S/. {{ number_format($c->monto, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">Año Escolar *</label>
                <select name="anio_escolar" class="form-control" required>
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ old('anio_escolar', date('Y'))==$y ? 'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Mes (si aplica)</label>
                <select name="mes" class="form-control">
                    <option value="">— No aplica —</option>
                    @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $mes)
                        <option value="{{ $i+1 }}" {{ old('mes')==($i+1) ? 'selected':'' }}>{{ $mes }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Método de Pago *</label>
                <select name="metodo_pago" class="form-control" required>
                    <option value="efectivo"      {{ old('metodo_pago','efectivo')=='efectivo'     ? 'selected':'' }}>Efectivo</option>
                    <option value="transferencia" {{ old('metodo_pago')=='transferencia'           ? 'selected':'' }}>Transferencia</option>
                    <option value="tarjeta"       {{ old('metodo_pago')=='tarjeta'                ? 'selected':'' }}>Tarjeta</option>
                    <option value="cheque"        {{ old('metodo_pago')=='cheque'                 ? 'selected':'' }}>Cheque</option>
                </select>
            </div>
        </div>

        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">Monto Total (S/.) *</label>
                <input type="number" name="monto" id="monto" class="form-control" step="0.01" min="0"
                    value="{{ old('monto') }}" required placeholder="0.00">
            </div>
            <div class="form-group">
                <label class="form-label">Descuento (S/.)</label>
                <input type="number" name="descuento" id="descuento" class="form-control" step="0.01" min="0"
                    value="{{ old('descuento', '0.00') }}" oninput="calcularPagado()">
            </div>
            <div class="form-group">
                <label class="form-label">Monto Pagado (S/.) *</label>
                <input type="number" name="monto_pagado" id="monto_pagado" class="form-control" step="0.01" min="0"
                    value="{{ old('monto_pagado') }}" required style="font-weight:700;color:var(--success);">
            </div>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Fecha de Pago *</label>
                <input type="date" name="fecha_pago" class="form-control" value="{{ old('fecha_pago', date('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha de Vencimiento</label>
                <input type="date" name="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento') }}">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
        </div>
    </div>
</div>

<div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px;">
    <a href="{{ route('pagos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Pago</button>
</div>
</form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('concepto_select').addEventListener('change', function() {
    const opt  = this.options[this.selectedIndex];
    const monto = opt.dataset.monto || '';
    document.getElementById('monto').value        = monto;
    document.getElementById('monto_pagado').value = monto;
});
function calcularPagado() {
    const m = parseFloat(document.getElementById('monto').value)       || 0;
    const d = parseFloat(document.getElementById('descuento').value)   || 0;
    document.getElementById('monto_pagado').value = Math.max(0, m - d).toFixed(2);
}
document.getElementById('monto').addEventListener('input', calcularPagado);
</script>
@endpush
