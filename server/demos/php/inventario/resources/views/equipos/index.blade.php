<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Equipos</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        {{-- Header Actions --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-white md:hidden">Equipos</h2> {{-- Visible only on mobile/tablet if header is hidden --}}
            <div class="flex gap-3 ml-auto">
                <a href="{{ route('equipos.export') }}"
                    class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition flex items-center gap-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exportar Excel
                </a>
                <a href="{{ route('equipos.create') }}"
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nuevo Equipo
                </a>
            </div>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="mb-6 bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
            {{-- Total Equipos --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">Total Equipos</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['total'] }}</div>
                </div>
            </div>

            {{-- Disponibles --}}
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">Disponibles</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['disponibles'] }}</div>
                </div>
            </div>

            {{-- Asignados --}}
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">Asignados</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['asignados'] }}</div>
                </div>
            </div>

            {{-- En Reparación --}}
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">En Reparación</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['en_reparacion'] }}</div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-gray-800 rounded-xl p-6 mb-6 shadow-lg">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-3"> {{-- Adjusted span for the new layout --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Buscar por código o número de serie..."
                                class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <select name="estado"
                            class="bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos los estados</option>
                            <option value="Disponible" {{ request('estado') == 'Disponible' ? 'selected' : '' }}>
                                Disponible</option>
                            <option value="Asignado" {{ request('estado') == 'Asignado' ? 'selected' : '' }}>Asignado
                            </option>
                            <option value="En Reparacion" {{ request('estado') == 'En Reparacion' ? 'selected' : '' }}>En
                                Reparación</option>
                            <option value="De Baja" {{ request('estado') == 'De Baja' ? 'selected' : '' }}>De Baja
                            </option>
                        </select>
                    </div>
                </div>
                <div class="md:col-span-1 flex gap-2"> {{-- Adjusted span for the new layout --}}
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex-1">
                        Buscar
                    </button>
                    <a href="{{ route('equipos.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- Equipos Table --}}
        <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-750">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Código</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Tipo</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Marca/Modelo</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Serie</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Sucursal</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($equipos as $equipo)
                            <tr class="hover:bg-gray-750 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-white">{{ $equipo->codigo_inventario }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $equipo->tipoEquipo->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-white">{{ $equipo->marca->nombre ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-400">{{ $equipo->modelo->nombre ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $equipo->numero_serie }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $equipo->sucursal->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $estados = [
                                            'Disponible' => 'bg-purple-500/20 text-purple-400',
                                            'Asignado' => 'bg-green-500/20 text-green-400',
                                            'En Reparacion' => 'bg-orange-500/20 text-orange-400',
                                            'De Baja' => 'bg-red-500/20 text-red-400'
                                        ];
                                        $clase = $estados[$equipo->estado] ?? 'bg-gray-500/20 text-gray-400';
                                    @endphp
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $clase }}">
                                        {{ $equipo->estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                        {{-- Ver --}}
                                        <a href="{{ route('equipos.show', $equipo) }}"
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
                                        <a href="{{ route('equipos.edit', $equipo) }}"
                                            class="text-yellow-400 hover:text-yellow-300 transition-colors p-1 hover:bg-yellow-500/10 rounded-full"
                                            title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        {{-- Activar/Desactivar --}}
                                        {{-- Activar/Desactivar --}}
                                        <form action="{{ route('equipos.toggle', $equipo) }}" method="POST"
                                            class="inline-block form-toggle-{{ $equipo->id }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="button"
                                                onclick="confirmToggle({{ $equipo->id }}, '{{ $equipo->codigo_inventario }}', '{{ $equipo->estado }}')"
                                                class="{{ $equipo->estado == 'De Baja' ? 'text-green-400 hover:text-green-300 hover:bg-green-500/10' : 'text-red-400 hover:text-red-300 hover:bg-red-500/10' }} transition-colors p-1 rounded-full"
                                                title="{{ $equipo->estado == 'De Baja' ? 'Activar' : 'Dar de Baja' }}">
                                                @if($equipo->estado == 'De Baja')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                    No se encontraron equipos
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $equipos->links() }}
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script>
            function confirmToggle(id, codigo, status) {
                const isDeBaja = status === 'De Baja';
                const action = isDeBaja ? 'activar' : 'dar de baja';
                const type = isDeBaja ? 'info' : 'warning';
                const confirmButtonColor = isDeBaja ? '#10B981' : '#EF4444'; // green-500 or red-500

                Swal.fire({
                    title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} Equipo?`,
                    text: `¿Estás seguro de que deseas ${action} el equipo "${codigo}"?`,
                    icon: type,
                    showCancelButton: true,
                    confirmButtonColor: confirmButtonColor,
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: `Sí, ${action}`,
                    cancelButtonText: 'Cancelar',
                    background: '#1F2937', // gray-800
                    color: '#F3F4F6' // gray-100
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.querySelector(`.form-toggle-${id}`).submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>