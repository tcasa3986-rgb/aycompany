@extends('layouts.app')
@section('title', 'Conceptos de Pago')
@section('page-title', 'Conceptos de Pago')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <p style="color:var(--muted);font-size:13px;">{{ $conceptos->count() }} conceptos registrados</p>
    <a href="{{ route('conceptos.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Concepto</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>N° de Pagos</th>
                    <th>Estado</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($conceptos as $c)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $c->nombre }}</div>
                        @if($c->descripcion)
                        <div style="font-size:11px;color:var(--muted);">{{ Str::limit($c->descripcion, 60) }}</div>
                        @endif
                    </td>
                    <td>
                        @php $tipoBadge = match($c->tipo) { 'mensualidad'=>'badge-primary','matricula'=>'badge-info','taller'=>'badge-warning','otros'=>'badge-secondary',default=>'badge-secondary' }; @endphp
                        <span class="badge {{ $tipoBadge }}">{{ ucfirst($c->tipo) }}</span>
                    </td>
                    <td style="font-weight:700;font-size:15px;color:var(--primary);">S/. {{ number_format($c->monto, 2) }}</td>
                    <td>
                        <span style="font-size:13px;">{{ number_format($c->pagos_count) }} pagos</span>
                    </td>
                    <td>
                        <span class="badge {{ $c->activo ? 'badge-success' : 'badge-secondary' }}">
                            {{ $c->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:center;">
                            <a href="{{ route('conceptos.edit', $c) }}" class="btn btn-sm btn-secondary btn-icon" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('conceptos.toggle', $c) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $c->activo ? 'btn-danger' : 'btn-success' }} btn-icon"
                                    title="{{ $c->activo ? 'Desactivar' : 'Activar' }}">
                                    <i class="fas fa-{{ $c->activo ? 'toggle-off' : 'toggle-on' }}"></i>
                                </button>
                            </form>
                            @if($c->pagos_count == 0)
                            <form method="POST" action="{{ route('conceptos.destroy', $c) }}"
                                onsubmit="return confirm('¿Eliminar este concepto?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:var(--muted);">
                        <i class="fas fa-tags" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px;"></i>
                        No hay conceptos de pago registrados.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
