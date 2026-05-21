@extends('layouts.app')

@section('title', 'Detalle de Orden Médica')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Orden {{ $orden->numero_orden }}</h1>
        <p class="text-secondary">Fecha: {{ $orden->fecha_registro ? $orden->fecha_registro->format('d/m/Y H:i') : ($orden->created_at ? $orden->created_at->format('d/m/Y H:i') : 'No registrada') }} | Registrado por: {{ $orden->user->name ?? 'Sistema' }}</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('ordenes.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Listado</a>
        @if($orden->estado == 'Pendiente')
            <a href="{{ route('ordenes.edit', $orden->id) }}" class="btn" style="background: var(--warning); color: black;"><i class="fa-solid fa-edit"></i> Editar</a>
        @endif
        @if(in_array($orden->estado, ['Completado', 'Entregado']))
            <form method="POST" action="{{ route('resultados.email', $orden->id) }}">
                @csrf
                <button type="submit" class="btn" style="background:var(--accent-secondary); color:white;"><i class="fa-solid fa-envelope"></i> Enviar Email</button>
            </form>
            <a href="{{ route('resultados.pdf', $orden->id) }}" target="_blank" class="btn btn-primary"><i class="fa-solid fa-print"></i> PDF</a>
        @else
            <button class="btn btn-primary" disabled style="opacity: 0.5;"><i class="fa-solid fa-print"></i> Validación Pendiente</button>
        @endif
    </div>
</div>

<div class="dashboard-grid">
    <div class="col-4">
        <div class="card" style="height: 100%;">
            <h3 style="margin-bottom: 20px; color: var(--accent-secondary);"><i class="fa-solid fa-user-injured"></i> Datos del Paciente</h3>
            <p style="margin-bottom: 10px;"><strong>Paciente:</strong> {{ $orden->paciente->nombre_completo ?? 'Paciente Eliminado o No Encontrado' }}</p>
            <p style="margin-bottom: 10px;"><strong>Documento:</strong> {{ $orden->paciente->tipo_documento ?? 'N/A' }} {{ $orden->paciente->numero_documento ?? '' }}</p>
            <p style="margin-bottom: 10px;"><strong>Edad:</strong> {{ optional($orden->paciente)->edad ? optional($orden->paciente)->edad . ' años' : 'No reg.' }} | <strong>Sexo:</strong> {{ optional($orden->paciente)->sexo ?? 'No reg.' }}</p>
            <p style="margin-bottom: 10px;"><strong>H. Clínica:</strong> {{ optional($orden->paciente)->historia_clinica ?? 'N/A' }}</p>
        </div>
    </div>
    
    <div class="col-4">
        <div class="card" style="height: 100%;">
            <h3 style="margin-bottom: 20px; color: var(--info);"><i class="fa-solid fa-stethoscope"></i> Datos Clínicos</h3>
            <p style="margin-bottom: 10px;"><strong>Médico:</strong> {{ $orden->medico ? $orden->medico->nombre_completo : 'Particular (Ninguno)' }}</p>
            <p style="margin-bottom: 10px;"><strong>Diagnóstico:</strong> {{ $orden->diagnostico_presuntivo ?? 'No especificado' }}</p>
            <p style="margin-bottom: 10px;">
                <strong>Estado:</strong> 
                <span class="status-badge {{ in_array($orden->estado, ['Completado', 'Entregado']) ? 'status-completed' : (in_array($orden->estado, ['Pendiente', 'En proceso']) ? 'status-pending' : 'status-critical') }}">
                    {{ $orden->estado }}
                </span>
            </p>
            <p style="margin-bottom: 10px;"><strong>Prioridad:</strong> {{ $orden->prioridad }}</p>
        </div>
    </div>

    <div class="col-4">
        <div class="card" style="height: 100%;">
            <h3 style="margin-bottom: 20px; color: var(--success);"><i class="fa-solid fa-file-invoice-dollar"></i> Facturación</h3>
            <p style="margin-bottom: 10px;"><strong>Convenio:</strong> {{ $orden->convenio ? $orden->convenio->nombre : 'Sin convenio' }}</p>
            <p style="margin-bottom: 10px;"><strong>Subtotal:</strong> S/ {{ number_format($orden->subtotal, 2) }}</p>
            <p style="margin-bottom: 10px;"><strong>Descuento:</strong> S/ {{ number_format($orden->descuento, 2) }}</p>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 10px 0;">
            <p style="font-size: 1.2rem; margin-bottom: 10px;"><strong>Total:</strong> <span class="text-success">S/ {{ number_format($orden->total, 2) }}</span></p>
            <p style="margin-bottom: 10px;"><strong>Pago:</strong> {!! $orden->pagado ? '<span class="text-success"><i class="fa-solid fa-check"></i> Pagado</span>' : '<span class="text-danger"><i class="fa-solid fa-xmark"></i> Pendiente</span>' !!}</p>
        </div>
    </div>

    <div class="col-12" style="margin-top: 20px;">
        <div class="card">
            <h3 style="margin-bottom: 20px; color: var(--accent-primary);"><i class="fa-solid fa-flask-vial"></i> Pruebas Solicitadas</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Área</th>
                            <th>Prueba</th>
                            <th>Muestra Tipo</th>
                            <th>Precio Final</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orden->detalles as $detalle)
                        <tr>
                            <td>{{ optional($detalle->prueba)->codigo ?? 'N/A' }}</td>
                            <td>{{ optional(optional($detalle->prueba)->area)->nombre ?? 'N/A' }}</td>
                            <td><strong>{{ optional($detalle->prueba)->nombre ?? 'Prueba Eliminada' }}</strong></td>
                            <td>{{ optional($detalle->prueba)->muestra_tipo ?? 'N/A' }}</td>
                            <td>S/ {{ number_format($detalle->precio_final, 2) }}</td>
                            <td>
                                <span class="status-badge {{ $detalle->estado == 'Completado' ? 'status-completed' : 'status-pending' }}">{{ $detalle->estado }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
