@extends('layouts.app')
@section('title', 'Validar Resultado — ' . $orden->numero_orden)
@section('content')
<div style="max-width:800px;margin:0 auto;padding:2rem 1rem;">
    <div class="card">
        <div style="padding:2rem;text-align:center;border-bottom:1px solid var(--border);">
            <div style="font-size:3rem;margin-bottom:1rem;"><i class="fa-solid fa-shield-check" style="color:var(--success);"></i></div>
            <h1 style="margin:0 0 8px;font-family:'Outfit';" class="text-gradient">Resultado Verificado</h1>
            <p class="text-muted">Este resultado es auténtico y fue emitido por LabSalud</p>
        </div>
        <div style="padding:2rem;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:2rem;">
                <div><span class="text-muted" style="font-size:0.85rem;">N° Orden</span><br><strong>{{ $orden->numero_orden }}</strong></div>
                <div><span class="text-muted" style="font-size:0.85rem;">Fecha</span><br><strong>{{ $orden->fecha_registro->format('d/m/Y') }}</strong></div>
                <div><span class="text-muted" style="font-size:0.85rem;">Paciente</span><br><strong>{{ $orden->paciente->nombre_completo }}</strong></div>
                <div><span class="text-muted" style="font-size:0.85rem;">Documento</span><br><strong>{{ $orden->paciente->numero_documento }}</strong></div>
            </div>
            <h3 style="margin:0 0 1rem;font-size:1rem;border-bottom:1px solid var(--border);padding-bottom:8px;">Resultados</h3>
            @foreach($orden->detalles as $det)
                @if($det->resultado)
                <div style="padding:12px 0;border-bottom:1px solid var(--border);">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <strong>{{ $det->prueba->nombre }}</strong>
                        <span class="status-badge {{ $det->resultado->valor_critico ? 'status-critical' : 'status-completed' }}">
                            {{ $det->resultado->interpretacion }}
                        </span>
                    </div>
                    <p style="margin:6px 0 0;font-size:1.1rem;"><strong>{{ $det->resultado->valor }}</strong> <span class="text-muted">{{ $det->resultado->unidad }}</span></p>
                    @if($det->resultado->valores_referencia)<p class="text-muted" style="font-size:0.85rem;margin:4px 0 0;">Referencia: {{ $det->resultado->valores_referencia }}</p>@endif
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
