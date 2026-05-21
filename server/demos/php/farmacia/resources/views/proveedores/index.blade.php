@extends('layouts.app')
@section('title', 'Proveedores')
@section('section', 'Proveedores')

@section('content')
<div class="card card-pad">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Proveedores</h2>
            <p class="text-sm text-gray-500">Laboratorios y distribuidoras.</p>
        </div>
        <div class="flex gap-2">
            <form method="GET" class="relative">
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar..." class="input pl-9 pr-4 w-72">
                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
            </form>
            <a href="{{ route('proveedores.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4 mr-1" /> Nuevo proveedor
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th>RUC</th>
                <th>Razón social</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th class="text-right">Productos</th>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($proveedores as $p)
                    <tr>
                        <td class="font-mono text-xs text-gray-500">{{ $p->ruc ?? '—' }}</td>
                        <td class="font-medium text-gray-800">{{ $p->razon_social }}</td>
                        <td>{{ $p->contacto ?? '—' }}</td>
                        <td>{{ $p->telefono ?? '—' }}</td>
                        <td class="text-right">{{ $p->productos_count }}</td>
                        <td>
                            @if($p->activo)
                                <span class="badge bg-emerald-50 text-emerald-700">Activo</span>
                            @else
                                <span class="badge bg-gray-100 text-gray-600">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="inline-flex gap-1">
                                <a href="{{ route('proveedores.edit', $p) }}" class="p-1.5 rounded hover:bg-gray-100">
                                    <x-icon name="pencil" class="h-4 w-4 text-farmacia-600" />
                                </a>
                                <form method="POST" action="{{ route('proveedores.destroy', $p) }}"
                                      onsubmit="return confirm('¿Eliminar proveedor?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded hover:bg-gray-100">
                                        <x-icon name="trash" class="h-4 w-4 text-rose-500" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-400">No hay proveedores.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $proveedores->links() }}</div>
</div>
@endsection
