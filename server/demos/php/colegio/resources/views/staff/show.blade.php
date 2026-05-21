@extends('layouts.app')
@section('title', 'Ficha de Personal')
@section('page-title', 'Ficha de Personal')

@section('content')

<div style="display:flex;gap:12px;margin-bottom:20px;">
    <a href="{{ route('personal.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    <a href="{{ route('personal.edit', $personal) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a>
</div>

<div class="grid grid-3" style="gap:20px;">

    {{-- Perfil --}}
    <div class="card" style="text-align:center;padding:32px 24px;">
        <div style="width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,#065f46,#10b981);display:flex;align-items:center;justify-content:center;color:white;font-size:30px;font-weight:800;margin:0 auto 16px;">
            {{ strtoupper(substr($personal->nombres,0,1)) }}{{ strtoupper(substr($personal->apellidos,0,1)) }}
        </div>
        <div style="font-size:19px;font-weight:700;">{{ $personal->nombre_completo }}</div>
        <div style="margin-top:8px;">
            <span class="badge badge-{{ $personal->tipo_badge }}">{{ ucfirst($personal->tipo) }}</span>
        </div>
        @if($personal->especialidad)
        <div style="margin-top:8px;font-size:13px;color:var(--muted);">{{ $personal->especialidad }}</div>
        @endif

        <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--border);">
            @php $estadoClass = $personal->estado==='activo' ? 'badge-success' : ($personal->estado==='licencia' ? 'badge-warning' : 'badge-secondary'); @endphp
            <span class="badge {{ $estadoClass }}">{{ ucfirst($personal->estado) }}</span>
        </div>

        <div style="margin-top:20px;padding:16px;background:#f0fdf4;border-radius:12px;">
            <div style="font-size:12px;color:var(--muted);">Salario mensual</div>
            <div style="font-size:22px;font-weight:800;color:#065f46;">S/. {{ number_format($personal->salario, 2) }}</div>
        </div>
    </div>

    {{-- Datos completos --}}
    <div style="grid-column:span 2;">
        <div class="card">
            <div class="card-header"><span class="card-title">Información Detallada</span></div>
            <div class="card-body" style="padding:0;">
                @foreach([
                    ['fas fa-id-badge','DNI', $personal->dni],
                    ['fas fa-user-tag','Tipo', ucfirst($personal->tipo)],
                    ['fas fa-book','Especialidad', $personal->especialidad ?? '—'],
                    ['fas fa-phone','Teléfono', $personal->telefono ?? '—'],
                    ['fas fa-envelope','Email', $personal->email ?? '—'],
                    ['fas fa-map-marker-alt','Dirección', $personal->direccion ?? '—'],
                    ['fas fa-calendar-plus','Fecha de Ingreso', $personal->fecha_ingreso?->format('d/m/Y').' ('.($personal->fecha_ingreso?->diffForHumans()).')'],
                ] as [$icon, $label, $val])
                <div style="display:flex;align-items:center;gap:14px;padding:14px 22px;border-bottom:1px solid var(--border);">
                    <div style="width:34px;height:34px;border-radius:9px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:var(--muted);flex-shrink:0;">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--muted);">{{ $label }}</div>
                        <div style="font-weight:500;font-size:14px;">{{ $val }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection
