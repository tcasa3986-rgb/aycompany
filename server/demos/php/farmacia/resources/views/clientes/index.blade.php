@extends('layouts.app')
@section('title', 'Clientes')
@section('section', 'Pacientes')

@section('content')
<div class="card card-pad">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Clientes / Pacientes</h2>
            <p class="text-sm text-gray-500">Ficha de pacientes con historial y datos clínicos básicos.</p>
        </div>
        <div class="flex gap-2">
            <form method="GET" class="relative">
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar cliente..." class="input pl-9 pr-4 w-72">
                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
            </form>
            <a href="{{ route('clientes.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4 mr-1" /> Nuevo cliente
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Alergias</th>
                    <th class="text-right">Puntos</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($clientes as $c)
                    <tr>
                        <td class="font-mono text-xs text-gray-500">{{ $c->documento ?? '—' }}</td>
                        <td class="font-medium text-gray-800">{{ $c->nombre_completo }}</td>
                        <td>{{ $c->telefono ?? '—' }}</td>
                        <td>{{ $c->email ?? '—' }}</td>
                        <td>
                            @if($c->alergias)
                                <span class="badge bg-amber-50 text-amber-700">{{ \Illuminate\Support\Str::limit($c->alergias, 30) }}</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="text-right">{{ $c->puntos_fidelidad }}</td>
                        <td class="text-right">
                            <div class="inline-flex gap-1">
                                <a href="{{ route('clientes.edit', $c) }}" class="p-1.5 rounded hover:bg-gray-100">
                                    <x-icon name="pencil" class="h-4 w-4 text-farmacia-600" />
                                </a>
                                <form method="POST" action="{{ route('clientes.destroy', $c) }}"
                                      onsubmit="return confirm('¿Eliminar este cliente?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded hover:bg-gray-100">
                                        <x-icon name="trash" class="h-4 w-4 text-rose-500" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-400">No hay clientes registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $clientes->links() }}</div>
</div>
@endsection
