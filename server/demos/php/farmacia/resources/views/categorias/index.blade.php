@extends('layouts.app')
@section('title', 'Categorías')
@section('section', 'Catálogo')

@section('content')
<div class="card card-pad">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Categorías de productos</h2>
            <p class="text-sm text-gray-500">Organiza tus productos por categoría.</p>
        </div>
        <div class="flex gap-2">
            <form method="GET" class="relative">
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar..." class="input pl-9 pr-4 w-72">
                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
            </form>
            <a href="{{ route('categorias.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4 mr-1" /> Nueva categoría
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th class="text-right">Productos</th>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($categorias as $c)
                    <tr>
                        <td class="font-medium text-gray-800">{{ $c->nombre }}</td>
                        <td class="text-gray-500">{{ $c->descripcion ?? '—' }}</td>
                        <td class="text-right">{{ $c->productos_count }}</td>
                        <td>
                            @if($c->activo)
                                <span class="badge bg-emerald-50 text-emerald-700">Activo</span>
                            @else
                                <span class="badge bg-gray-100 text-gray-600">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="inline-flex gap-1">
                                <a href="{{ route('categorias.edit', $c) }}" class="p-1.5 rounded hover:bg-gray-100">
                                    <x-icon name="pencil" class="h-4 w-4 text-farmacia-600" />
                                </a>
                                <form method="POST" action="{{ route('categorias.destroy', $c) }}"
                                      onsubmit="return confirm('¿Eliminar categoría?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded hover:bg-gray-100">
                                        <x-icon name="trash" class="h-4 w-4 text-rose-500" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-gray-400">No hay categorías.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $categorias->links() }}</div>
</div>
@endsection
