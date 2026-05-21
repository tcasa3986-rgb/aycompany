@extends('layouts.app')
@section('title', 'Receta ' . $receta->codigo)
@section('section', 'Recetas')

@section('content')
<div class="card card-pad max-w-3xl mx-auto">
    <div class="flex justify-between items-start border-b border-gray-100 pb-4 mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Receta médica</h2>
            <p class="text-sm text-gray-500">{{ $receta->codigo }} · {{ $receta->fecha?->format('d/m/Y') }}</p>
        </div>
        @if($receta->retenida)
            <span class="badge bg-rose-50 text-rose-700">Retenida</span>
        @else
            <span class="badge bg-emerald-50 text-emerald-700">Normal</span>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
        <div>
            <p class="text-gray-500">Paciente</p>
            <p class="font-medium">{{ $receta->cliente?->nombre_completo ?? 'No registrado' }}</p>
            @if($receta->cliente?->alergias)
                <p class="text-xs text-amber-600 mt-1">⚠ Alergias: {{ $receta->cliente->alergias }}</p>
            @endif
        </div>
        <div>
            <p class="text-gray-500">Médico</p>
            <p class="font-medium">{{ $receta->medico }}</p>
            <p class="text-xs text-gray-500">{{ $receta->especialidad }} @if($receta->cmp) · CMP {{ $receta->cmp }} @endif</p>
        </div>
    </div>

    @if($receta->diagnostico)
        <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm">
            <p class="text-gray-500 text-xs uppercase font-semibold">Diagnóstico</p>
            <p class="mt-1">{{ $receta->diagnostico }}</p>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th>Medicamento</th>
                <th class="text-right">Cantidad</th>
                <th>Indicaciones</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($receta->detalles as $d)
                    <tr>
                        <td class="font-medium text-gray-800">
                            {{ $d->producto->nombre }}
                            @if($d->producto->concentracion) · <span class="text-gray-500 font-normal">{{ $d->producto->concentracion }}</span> @endif
                            @if($d->producto->requiere_receta)
                                <span class="badge bg-rose-50 text-rose-700 ml-2">Controlado</span>
                            @endif
                        </td>
                        <td class="text-right">{{ $d->cantidad }}</td>
                        <td>{{ $d->indicaciones ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($receta->observaciones)
        <p class="text-sm text-gray-500 mt-4"><strong>Observaciones:</strong> {{ $receta->observaciones }}</p>
    @endif

    <div class="mt-6 flex justify-between">
        <a href="{{ route('recetas.index') }}" class="btn-secondary">← Volver</a>
        <form method="POST" action="{{ route('recetas.destroy', $receta) }}" onsubmit="return confirm('¿Eliminar esta receta?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger">Eliminar</button>
        </form>
    </div>
</div>
@endsection
