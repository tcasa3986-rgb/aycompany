<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Reparaciones de Equipos</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-end mb-6">
            <a href="{{ route('reparaciones.create') }}"
                class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                + Nueva Reparación
            </a>
        </div>

        {{-- Filters --}}
        <div class="bg-gray-800 rounded-xl p-6 mb-6 shadow-lg">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por código de equipo..."
                        class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <select name="estado"
                    class="bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="En Proceso" {{ request('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso
                    </option>
                    <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completada
                    </option>
                    <option value="Cancelada" {{ request('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex-1">
                        Buscar
                    </button>
                    <a href="{{ route('reparaciones.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-6 py-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-750">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Equipo</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Problema</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Fecha Ingreso</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Técnico</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Costo</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($reparaciones as $reparacion)
                            <tr class="hover:bg-gray-750 transition">
                                <td class="px-6 py-4">
                                    <div class="text-white font-medium">{{ $reparacion->equipo->codigo_inventario }}</div>
                                    <div class="text-gray-400 text-sm">
                                        {{ $reparacion->equipo->marca->nombre ?? '' }}
                                        {{ $reparacion->equipo->modelo->nombre ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-300 max-w-xs truncate">
                                    {{ Str::limit($reparacion->descripcion_problema, 50) }}
                                </td>
                                <td class="px-6 py-4 text-gray-300">{{ $reparacion->fecha_ingreso->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-gray-300">{{ $reparacion->tecnico_asignado ?: 'No asignado' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($reparacion->estado_reparacion === 'Pendiente')
                                        <span
                                            class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-semibold">Pendiente</span>
                                    @elseif($reparacion->estado_reparacion === 'En Proceso')
                                        <span
                                            class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-semibold">En
                                            Proceso</span>
                                    @elseif($reparacion->estado_reparacion === 'Completada')
                                        <span
                                            class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold">Completada</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-xs font-semibold">Cancelada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-300">
                                    @if($reparacion->costo_real)
                                        S/. {{ number_format($reparacion->costo_real, 2) }}
                                    @elseif($reparacion->costo_estimado)
                                        <span class="text-gray-500">~S/.
                                            {{ number_format($reparacion->costo_estimado, 2) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <div class="flex items-center space-x-3">
                                        {{-- Ver --}}
                                        <a href="{{ route('reparaciones.show', $reparacion) }}"
                                            class="text-blue-400 hover:text-blue-300 transition-colors p-1 hover:bg-blue-500/10 rounded-full"
                                            title="Ver detalles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        {{-- Editar --}}
                                        <a href="{{ route('reparaciones.edit', $reparacion) }}"
                                            class="text-yellow-400 hover:text-yellow-300 transition-colors p-1 hover:bg-yellow-500/10 rounded-full"
                                            title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        {{-- Eliminar --}}
                                        <form action="{{ route('reparaciones.destroy', $reparacion) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('¿Eliminar esta reparación?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-400 hover:text-red-300 transition-colors p-1 hover:bg-red-500/10 rounded-full"
                                                title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    No hay reparaciones registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($reparaciones->hasPages())
                <div class="px-6 py-4 bg-gray-750">
                    {{ $reparaciones->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>