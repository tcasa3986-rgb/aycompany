@extends('layouts.app')
@section('title', 'Inventario')
@section('section', 'Productos')

@section('content')
<div class="card card-pad">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Inventario de productos</h2>
            <p class="text-sm text-gray-500">Gestión de medicamentos, insumos y cosméticos.</p>
        </div>
        <div class="flex gap-2">
            <form method="GET" class="relative">
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar producto..." class="input pl-9 pr-4 w-72">
                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
            </form>
            <a href="{{ route('productos.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4 mr-1" /> Nuevo producto
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Tipo</th>
                    <th class="text-right">P. Venta</th>
                    <th class="text-right">Stock</th>
                    <th>Estado</th>
                    <th>ATC</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($productos as $p)
                    <tr>
                        <td class="font-mono text-xs text-gray-500">{{ $p->codigo }}</td>
                        <td>
                            <div class="font-medium text-gray-800">{{ $p->nombre }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $p->principio_activo }}
                                @if($p->concentracion) · {{ $p->concentracion }} @endif
                            </div>
                        </td>
                        <td>{{ $p->categoria?->nombre ?? '—' }}</td>
                        <td>
                            <span class="badge bg-farmacia-50 text-farmacia-700 capitalize">{{ $p->tipo }}</span>
                            @if($p->requiere_receta)
                                <span class="badge bg-amber-50 text-amber-700">Receta</span>
                            @endif
                        </td>
                        <td class="text-right font-semibold">S/ {{ number_format($p->precio_venta, 2) }}</td>
                        <td class="text-right">{{ $p->stock_actual }}</td>
                        <td>
                            @if ($p->es_bajo_stock)
                                <span class="badge bg-rose-50 text-rose-600">Stock bajo</span>
                            @else
                                <span class="badge bg-emerald-50 text-emerald-700">OK</span>
                            @endif
                        </td>
                        <td class="font-mono text-xs">{{ $p->codigo_atc ?? '—' }}</td>
                        <td class="text-right">
                            <div class="inline-flex gap-1">
                                <a href="{{ route('productos.barcode', $p) }}" class="p-1.5 rounded hover:bg-gray-100" title="Código de Barras" target="_blank">
                                    <x-icon name="clipboard" class="h-4 w-4 text-farmacia-600" />
                                </a>
                                <a href="{{ route('productos.lotes.index', $p) }}" class="p-1.5 rounded hover:bg-gray-100" title="Lotes">
                                    <x-icon name="box" class="h-4 w-4 text-farmacia-600" />
                                </a>
                                <a href="{{ route('productos.edit', $p) }}" class="p-1.5 rounded hover:bg-gray-100" title="Editar">
                                    <x-icon name="pencil" class="h-4 w-4 text-farmacia-600" />
                                </a>
                                <form method="POST" action="{{ route('productos.destroy', $p) }}"
                                      onsubmit="return confirm('¿Eliminar este producto?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded hover:bg-gray-100" title="Eliminar">
                                        <x-icon name="trash" class="h-4 w-4 text-rose-500" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-8 text-center text-gray-400">No hay productos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $productos->links() }}</div>
</div>
@endsection
