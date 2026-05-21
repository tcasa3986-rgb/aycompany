@extends('layouts.app')
@section('title', 'Recetas médicas')
@section('section', 'Recetas')

@section('content')
<div class="card card-pad">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Recetas médicas</h2>
            <p class="text-sm text-gray-500">Registro y validación de recetas, controlados y retenidas.</p>
        </div>
        <div class="flex gap-2">
            <form method="GET" class="relative">
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por médico/paciente/código..." class="input pl-9 pr-4 w-80">
                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
            </form>
            <a href="{{ route('recetas.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4 mr-1" /> Nueva receta
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th>Código</th>
                <th>Fecha</th>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Especialidad</th>
                <th>Tipo</th>
                <th class="text-right">Acciones</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($recetas as $r)
                    <tr>
                        <td class="font-mono text-xs text-gray-500">{{ $r->codigo }}</td>
                        <td>{{ $r->fecha?->format('d/m/Y') }}</td>
                        <td class="font-medium text-gray-800">{{ $r->cliente?->nombre_completo ?? '—' }}</td>
                        <td>{{ $r->medico }}</td>
                        <td>{{ $r->especialidad ?? '—' }}</td>
                        <td>
                            @if($r->retenida)
                                <span class="badge bg-rose-50 text-rose-700">Retenida</span>
                            @else
                                <span class="badge bg-emerald-50 text-emerald-700">Normal</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('recetas.show', $r) }}" class="text-farmacia-600 text-xs hover:underline">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-400">Aún no hay recetas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $recetas->links() }}</div>
</div>
@endsection
