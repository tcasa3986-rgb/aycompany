@extends('layouts.app')
@section('title', 'Paciente — ' . $paciente->nombre_completo)
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Historial del Paciente</h1>
        <p class="text-secondary">HC: <strong>{{ $paciente->historia_clinica }}</strong></p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-secondary"><i class="fa-solid fa-pen"></i> Editar</a>
        <a href="{{ route('ordenes.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva Orden</a>
    </div>
</div>

<div class="dashboard-grid">
    {{-- Info del Paciente --}}
    <div class="col-4">
        <div class="card">
            <div class="card-header"><span class="card-title">Datos Personales</span></div>
            <div style="padding:0 1rem 1rem;">
                <div style="text-align:center;padding:1.5rem 0;">
                    <div style="width:70px;height:70px;border-radius:50%;background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.8rem;font-weight:700;color:#fff;">
                        {{ substr($paciente->nombres, 0, 1) }}{{ substr($paciente->apellido_paterno, 0, 1) }}
                    </div>
                    <h3 style="margin:0;font-size:1.1rem;">{{ $paciente->nombre_completo }}</h3>
                    <p class="text-secondary" style="margin:4px 0;">{{ $paciente->numero_documento }}</p>
                    @if($paciente->tipo_sangre)
                        <span class="status-badge status-critical">{{ $paciente->tipo_sangre }}</span>
                    @endif
                </div>
                <table style="width:100%;font-size:0.9rem;border-collapse:collapse;">
                    <tr><td class="text-muted" style="padding:6px 0;">Tipo Doc.</td><td><strong>{{ $paciente->tipo_documento }}</strong></td></tr>
                    <tr><td class="text-muted" style="padding:6px 0;">Nacimiento</td><td><strong>{{ $paciente->fecha_nacimiento ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') : '—' }}</strong></td></tr>
                    <tr><td class="text-muted" style="padding:6px 0;">Sexo</td><td><strong>{{ $paciente->sexo === 'M' ? 'Masculino' : ($paciente->sexo === 'F' ? 'Femenino' : '—') }}</strong></td></tr>
                    <tr><td class="text-muted" style="padding:6px 0;">Teléfono</td><td><strong>{{ $paciente->telefono ?? '—' }}</strong></td></tr>
                    <tr><td class="text-muted" style="padding:6px 0;">Email</td><td><strong>{{ $paciente->email ?? '—' }}</strong></td></tr>
                    <tr><td class="text-muted" style="padding:6px 0;">Dirección</td><td><strong>{{ $paciente->direccion ?? '—' }}</strong></td></tr>
                </table>
            </div>
        </div>
        {{-- KPIs --}}
        <div class="card" style="margin-top:1rem;">
            <div class="card-header"><span class="card-title">Estadísticas</span></div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;padding:1rem;text-align:center;">
                <div>
                    <div style="font-size:1.6rem;font-weight:700;color:var(--accent-primary);">{{ $totalOrdenes }}</div>
                    <div class="text-muted" style="font-size:0.8rem;">Órdenes Total</div>
                </div>
                <div>
                    <div style="font-size:1.6rem;font-weight:700;color:var(--warning);">{{ $ordenesActivas }}</div>
                    <div class="text-muted" style="font-size:0.8rem;">En Proceso</div>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:700;color:var(--success);">S/{{ number_format($totalFacturado, 0) }}</div>
                    <div class="text-muted" style="font-size:0.8rem;">Facturado</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial de Órdenes --}}
    <div class="col-8">
        <div class="card">
            <div class="card-header"><span class="card-title">Historial de Órdenes</span></div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nro Orden</th>
                            <th>Fecha</th>
                            <th>Pruebas</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paciente->ordenes as $orden)
                        <tr>
                            <td><a href="{{ route('ordenes.show', $orden) }}" style="color:var(--accent-primary);"><strong>{{ $orden->numero_orden }}</strong></a></td>
                            <td>{{ $orden->fecha_registro->format('d/m/Y') }}</td>
                            <td>{{ $orden->detalles->count() }} prueba(s)</td>
                            <td>S/ {{ number_format($orden->total, 2) }}</td>
                            <td>
                                @php $s=$orden->estado; @endphp
                                <span class="status-badge {{ in_array($s,['Completado','Entregado']) ? 'status-completed' : (in_array($s,['Pendiente','En proceso']) ? 'status-pending' : 'status-critical') }}">{{ $s }}</span>
                            </td>
                            <td>
                                @if(in_array($orden->estado, ['Completado','Entregado']))
                                    <a href="{{ route('resultados.pdf', $orden) }}" target="_blank" class="action-btn text-success" title="Descargar PDF"><i class="fa-solid fa-file-pdf"></i></a>
                                @else
                                    <span class="text-muted" style="font-size:0.8rem;">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted">Sin órdenes registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
